<?php
class Order {
    private $conn;
    public $id, $cliente_nombre, $cliente_email, $total, $estado;

    public function __construct($db) { $this->conn = $db; }

    public function all() {
        return $this->conn->query("SELECT * FROM ordenes ORDER BY id DESC")->fetchAll();
    }

    public function find($id) {
        $s = $this->conn->prepare("SELECT * FROM ordenes WHERE id = :id");
        $s->execute([':id' => $id]);
        $orden = $s->fetch();
        if (!$orden) return null;

        $s2 = $this->conn->prepare(
            "SELECT oi.*, p.nombre AS producto_nombre
             FROM orden_items oi
             JOIN productos p ON p.id = oi.producto_id
             WHERE oi.orden_id = :id"
        );
        $s2->execute([':id' => $id]);
        $orden['items'] = $s2->fetchAll();
        return $orden;
    }

    public function create() {
        $s = $this->conn->prepare(
            "INSERT INTO ordenes (cliente_nombre, cliente_email, total, estado)
             VALUES (:n, :e, :t, :s)"
        );
        $s->execute([
            ':n' => $this->cliente_nombre,
            ':e' => $this->cliente_email,
            ':t' => $this->total,
            ':s' => $this->estado ?: 'pendiente',
        ]);
        return (int)$this->conn->lastInsertId();
    }

    public function updateEstado($id, $estado) {
        $validos = ['pendiente','pagada','enviada','entregada','cancelada'];
        if (!in_array($estado, $validos, true)) return false;
        $s = $this->conn->prepare("UPDATE ordenes SET estado = :e WHERE id = :id");
        return $s->execute([':e' => $estado, ':id' => $id]);
    }

    public function delete($id) {
        $s = $this->conn->prepare("DELETE FROM ordenes WHERE id = :id");
        return $s->execute([':id' => $id]);
    }

        /**
     * Crea una orden con items en una transacción.
     * @param int   $cliente_id
     * @param array $items  [['producto_id'=>1,'cantidad'=>2], ...]
     * @return int  ID de la orden creada
     * @throws Exception si hay stock insuficiente o producto inexistente
     */
    public function createConItems($cliente_id, $items) {
        $this->conn->beginTransaction();
        try {
            $total = 0;
            foreach ($items as &$it) {
                $s = $this->conn->prepare(
                    "SELECT precio, stock FROM productos WHERE id = :id FOR UPDATE"
                );
                $s->execute([':id' => (int)$it['producto_id']]);
                $p = $s->fetch();
                if (!$p) throw new Exception("Producto {$it['producto_id']} no existe");
                if ((int)$p['stock'] < (int)$it['cantidad'])
                    throw new Exception("Stock insuficiente del producto {$it['producto_id']}");
                $it['precio_unitario'] = (float)$p['precio'];
                $total += (float)$p['precio'] * (int)$it['cantidad'];
            }
            unset($it);

            $cs = $this->conn->prepare("SELECT nombre, email FROM clientes WHERE id = :id");
            $cs->execute([':id' => $cliente_id]);
            $cli = $cs->fetch();
            if (!$cli) throw new Exception("Cliente inexistente");

            $os = $this->conn->prepare(
                "INSERT INTO ordenes (cliente_id, cliente_nombre, cliente_email, total, estado)
                 VALUES (:cid, :nom, :em, :t, 'pendiente')"
            );
            $os->execute([
                ':cid' => $cliente_id,
                ':nom' => $cli['nombre'],
                ':em'  => $cli['email'],
                ':t'   => $total,
            ]);
            $ordenId = (int)$this->conn->lastInsertId();

            $is = $this->conn->prepare(
                "INSERT INTO orden_items (orden_id, producto_id, cantidad, precio_unitario)
                 VALUES (:o, :p, :c, :pu)"
            );
            $us = $this->conn->prepare("UPDATE productos SET stock = stock - :c WHERE id = :id");
            foreach ($items as $it) {
                $is->execute([
                    ':o'  => $ordenId,
                    ':p'  => (int)$it['producto_id'],
                    ':c'  => (int)$it['cantidad'],
                    ':pu' => $it['precio_unitario'],
                ]);
                $us->execute([':c' => (int)$it['cantidad'], ':id' => (int)$it['producto_id']]);
            }

            $this->conn->commit();
            return $ordenId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}