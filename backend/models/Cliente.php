<?php
class Cliente {
    private $conn;
    public $id, $nombre, $email, $usuario, $password, $telefono, $direccion;

    public function __construct($db) { $this->conn = $db; }

    public function all() {
        return $this->conn->query(
            "SELECT id, nombre, email, usuario, telefono, direccion, created_at
             FROM clientes ORDER BY id DESC"
        )->fetchAll();
    }

    public function find($id) {
        $s = $this->conn->prepare(
            "SELECT id, nombre, email, usuario, telefono, direccion, created_at
             FROM clientes WHERE id = :id"
        );
        $s->execute([':id' => $id]);
        return $s->fetch();
    }

    public function findByUsuario($usuario) {
        $s = $this->conn->prepare("SELECT * FROM clientes WHERE usuario = :u LIMIT 1");
        $s->execute([':u' => $usuario]);
        return $s->fetch();
    }

    public function emailExists($email) {
        $s = $this->conn->prepare("SELECT COUNT(*) FROM clientes WHERE email = :e");
        $s->execute([':e' => $email]);
        return $s->fetchColumn() > 0;
    }

    public function usuarioExists($usuario) {
        $s = $this->conn->prepare("SELECT COUNT(*) FROM clientes WHERE usuario = :u");
        $s->execute([':u' => $usuario]);
        return $s->fetchColumn() > 0;
    }

    public function create() {
        $s = $this->conn->prepare(
            "INSERT INTO clientes (nombre, email, usuario, password_hash)
             VALUES (:n, :e, :u, :p)"
        );
        return $s->execute([
            ':n' => $this->nombre,
            ':e' => $this->email,
            ':u' => $this->usuario,
            ':p' => password_hash($this->password, PASSWORD_ARGON2ID),
        ]);
    }

    public function delete($id) {
        $s = $this->conn->prepare("DELETE FROM clientes WHERE id = :id");
        return $s->execute([':id' => $id]);
    }
}