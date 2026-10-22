<?php
    session_start();
    require_once __DIR__ . '/../config/db.php';

    class AuthController{
        private $db;

        public function __construct(){
            $database = new DB();
            $this->db = $database->getConnection();
        }

        // Iniciar sesión como administrador con usuario, contraseña y código especial

        // tarea que se inicie sesion no con correo sino usuario y contraseña

        public function login(){
            header('Content-Type: application/json; charset=utf-8');

            // Leer los datos JSON que llegan del formulario
            $data = json_decode(file_get_contents("php://input"), true);

            $usuario = filter_var($data['usuario'] ?? '', FILTER_DEFAULT);
            $password = $data['password'] ?? '';

            $CodeEncode = filter_var($data['codigo_especial'] ?? '', FILTER_VALIDATE_INT); 

// tarea aqui deberia ir codigo de la negacion aqui va a ser el codigo especial que se le da al usuario para que pueda iniciar sesion como administrador

// 1. Validación básica: que no vengan vacíos
            if (empty($usuario) || empty($password) || !$CodeEncode) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Todos los campos son obligatorios y el código debe ser un número"]);
                return;
            }

            try {
                // 2. Buscar al administrador por su nombre de usuario
                $query = "SELECT id, usuario, password_hash, codigo_especial FROM administradores WHERE usuario = :usuario LIMIT 1";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->execute();

                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                // 3. Verificar si el usuario existe
                if (!$admin) {
                    http_response_code(401);
                    echo json_encode(["status" => "error", "message" => "Credenciales incorrectas"]);
                    return;
                }

                // 4. Validar el Código Especial (Tu tarea de negación/validación)
                if ($admin['codigo_especial'] !== $CodeEncode) {
                    http_response_code(401);
                    echo json_encode(["status" => "error", "message" => "El código especial de administrador no es válido"]);
                    return;
                }

                // 5. Validar la contraseña (Soporta Argon2id automáticamente)
                if (password_verify($password, $admin['password_hash'])) {
                    // ¡Login exitoso! Guardamos al usuario en la sesión
                    $_SESSION['admin_user'] = [
                        "id" => $admin['id'],
                        "usuario" => $admin['usuario']
                    ];

                    http_response_code(200);
                    echo json_encode(["status" => "success", "message" => "¡Bienvenido al Backend, amiguito!"]);
                } else {
                    http_response_code(401);
                    echo json_encode(["status" => "error", "message" => "Credenciales incorrectas"]);
                }

            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Error en el servidor: " . $e->getMessage()]);
            }
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

