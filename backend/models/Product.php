<?php
class Product {
    private $conn;
    public $id, $nombre, $descripcion, $precio, $stock, $categoria, $imagen_url;

    public function __construct($db) { $this->conn = $db; }

    public function all() {
        return $this->conn->query(
            "SELECT * FROM productos ORDER BY id DESC"
        )->fetchAll();
    }

    public function find($id) {
        $s = $this->conn->prepare("SELECT * FROM productos WHERE id = :id");
        $s->execute([':id' => $id]);
        return $s->fetch();
    }

    public function create() {
        $s = $this->conn->prepare(
            "INSERT INTO productos (nombre, descripcion, precio, stock, categoria, imagen_url)
             VALUES (:n, :d, :p, :s, :c, :i)"
        );
        return $s->execute([
            ':n' => $this->nombre,  ':d' => $this->descripcion,
            ':p' => $this->precio,  ':s' => $this->stock,
            ':c' => $this->categoria, ':i' => $this->imagen_url,
        ]);
    }

    public function update() {
        $s = $this->conn->prepare(
            "UPDATE productos SET nombre=:n, descripcion=:d, precio=:p,
             stock=:s, categoria=:c, imagen_url=:i WHERE id=:id"
        );
        return $s->execute([
            ':n' => $this->nombre,  ':d' => $this->descripcion,
            ':p' => $this->precio,  ':s' => $this->stock,
            ':c' => $this->categoria, ':i' => $this->imagen_url,
            ':id'=> $this->id,
        ]);
    }

    public function delete($id) {
        $s = $this->conn->prepare("DELETE FROM productos WHERE id = :id");
        return $s->execute([':id' => $id]);
    }
}