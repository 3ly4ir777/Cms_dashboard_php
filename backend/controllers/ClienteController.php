<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';
require_once __DIR__ . '/../models/Cliente.php';

class ClienteController {
    private $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->model = new Cliente((new DB())->getConnection());
    }

    // POST {nombre, email, usuario, password}
    public function register() {
        $b = json_decode(file_get_contents("php://input"), true) ?: [];
        $nombre   = trim($b['nombre']   ?? '');
        $email    = trim($b['email']    ?? '');
        $usuario  = trim($b['usuario']  ?? '');
        $password = $b['password'] ?? '';

        if ($nombre === '' || $email === '' || $usuario === '' || $password === '')
            json_response(["status"=>"error","message"=>"Todos los campos son obligatorios"], 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            json_response(["status"=>"error","message"=>"Email inválido"], 400);
        if (strlen($password) < 6)
            json_response(["status"=>"error","message"=>"La contraseña debe tener al menos 6 caracteres"], 400);
        if ($this->model->usuarioExists($usuario))
            json_response(["status"=>"error","message"=>"Ese usuario ya existe"], 409);
        if ($this->model->emailExists($email))
            json_response(["status"=>"error","message"=>"Ese email ya está registrado"], 409);

        $this->model->nombre   = $nombre;
        $this->model->email    = $email;
        $this->model->usuario  = $usuario;
        $this->model->password = $password;
        $this->model->create();

        json_response(["status"=>"success","message"=>"Cuenta creada. ¡Ya puedes iniciar sesión!"], 201);
    }

    // POST {usuario, password}
    public function login() {
        $b = json_decode(file_get_contents("php://input"), true) ?: [];
        $usuario  = trim($b['usuario']  ?? '');
        $password = $b['password'] ?? '';

        if ($usuario === '' || $password === '')
            json_response(["status"=>"error","message"=>"Usuario y contraseña obligatorios"], 400);

        $c = $this->model->findByUsuario($usuario);
        if (!$c || !password_verify($password, $c['password_hash']))
            json_response(["status"=>"error","message"=>"Credenciales incorrectas"], 401);

        session_regenerate_id(true);
        $_SESSION['cliente_user'] = [
            "id"      => (int)$c['id'],
            "usuario" => $c['usuario'],
            "nombre"  => $c['nombre'],
        ];
        json_response([
            "status"  => "success",
            "message" => "¡Bienvenido a la tienda!",
            "nombre"  => $c['nombre'],
        ]);
    }
}