<?php

class DB {
    private $host = "localhost";
    private $db_name = "ecommerce_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . 
                ";dbname=" . $this->db_name .
                ";charset=utf8mb4",
                $this->username,
                $this->password,

                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );

        } catch (PDOException $exception) {
            echo json_encode(["error" => "Errror de conexion" . $exception->getMessage()]);
            exit();
        }
        return $this->conn;

    }

}

// en el contenedor limpio de podman la IA me dijo que para instalar la extension pdo_mysql de PHP en el contenedor de Podman, debo ejecutar el siguiente comando: 
//podman exec -it mi_php_backend docker-php-ext-install pdo_mysql

// y este es para dar permisos a la carpeta del proyecto en Fedora con Podman, para que el servidor web pueda acceder a ella correctamente:
//chmod -R 755 ~/Descargas/"Proyectos Programacion"/EliasCms
