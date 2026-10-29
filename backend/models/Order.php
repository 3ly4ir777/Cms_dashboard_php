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
}