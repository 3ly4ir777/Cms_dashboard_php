<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';

class AuthController {
    private $db;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new DB();
        $this->db = $database->getConnection();
    }

    public function login() {
        header('Content-Type: application/json; charset=utf-8');
        $data = json_decode(file_get_contents("php://input"), true) ?: [];

        $usuario  = trim($data['usuario'] ?? '');
        $password = $data['password'] ?? '';
        $codigo   = filter_var($data['codigo_especial'] ?? null, FILTER_VALIDATE_INT);

        if ($usuario === '' || $password === '' || $codigo === false || $codigo === null) {
            json_response(["status" => "error", "message" => "Todos los campos son obligatorios y el código debe ser numérico"], 400);
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT id, usuario, password_hash, codigo_especial
                 FROM administradores WHERE usuario = :usuario LIMIT 1"
            );
            $stmt->execute([':usuario' => $usuario]);
            $admin = $stmt->fetch();

            // Mismo mensaje para "no existe" y "password mal" → no filtrar info
            if (!$admin || !password_verify($password, $admin['password_hash'])) {
                json_response(["status" => "error", "message" => "Credenciales incorrectas"], 401);
            }

            //  Casteo a int para comparar seguro
            if ((int)$admin['codigo_especial'] !== (int)$codigo) {
                json_response(["status" => "error", "message" => "Código especial inválido"], 401);
            }

            // Regenerar ID de sesión al loguear (evita session fixation)
            session_regenerate_id(true);
            $_SESSION['admin_user'] = [
                "id"      => (int)$admin['id'],
                "usuario" => $admin['usuario'],
            ];

            json_response(["status" => "success", "message" => "¡Bienvenido al panel, amiguito!", "usuario" => $admin['usuario']]);
        } catch (PDOException $e) {
            json_response(["status" => "error", "message" => "Error interno del servidor"], 500);
        }
    }

    //Cerrar sesion 
    public function logout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
        }
        session_destroy();
        json_response(["status" => "success", "message" => "Sesión cerrada"]);
    }
}

