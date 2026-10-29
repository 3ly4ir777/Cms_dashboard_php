<?php
class DB {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Local (Podman) por defecto, pero se puede sobreescribir con variables de entorno
        // Esto sirve para InfinityFree: defines DB_HOST, DB_NAME, etc. en su panel
        $this->host     = getenv('DB_HOST')     ?: "db";
        $this->db_name  = getenv('DB_NAME')     ?: "ecommerce_db";
        $this->username = getenv('DB_USER')     ?: "root";
        $this->password = getenv('DB_PASSWORD') ?: "tuchave_secreta";
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // 👈 importante: tipos reales
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["error" => "Error de conexión a la base de datos"]);
            exit();
        }
        return $this->conn;
    }
}