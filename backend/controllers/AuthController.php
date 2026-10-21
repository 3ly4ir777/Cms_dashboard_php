<?php
    session_start();
    require_once __DIR__ . '/../config/db.php';

    class AuthController{
        private $db;

        public function __construct(){
            $database = new DB();
            $this->db = $database->getConnection();
        }

        // iniciar sesion como administrador 

        // tarea que se inicie sesion no con correo sino usuario y contraseña

        public function login(){
            header('Content-Type: application/json; charset=utf-8');

            $data = json_decode(file_get_contents("php://input"), true);

// tarea aqui deberia ir codigo de la negacion aqui va a ser el codigo especial que se le da al usuario para que pueda iniciar sesion como administrador

            $CodeEncode = filter_var($data['codigo_especial'] ?? '', FILTER_VALIDATE_INT); 

        }


        //Cerrar sesion 
        public function logout(){
            header('Content-Type: application/json; charset=utf-8');
            unset($_SESSION['admin_user']);
            session_destroy();

            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Sesión cerrada correctamente"]);
        
        }
    }

