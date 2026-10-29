<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    private $model;

    public function __construct() {
        $db = (new DB())->getConnection();
        $this->model = new Order($db);
    }

    public function handle($method) {
        require_auth();

        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $o = $this->model->find((int)$_GET['id']);
                    $o ? json_response(["status"=>"success","data"=>$o])
                       : json_response(["status"=>"error","message"=>"No encontrada"], 404);
                }
                json_response(["status"=>"success","data"=>$this->model->all()]);
                break;

            case 'POST':
                $b = json_decode(file_get_contents("php://input"), true) ?: [];
                $this->model->cliente_nombre = $b['cliente_nombre'] ?? '';
                $this->model->cliente_email  = $b['cliente_email']  ?? '';
                $this->model->total          = $b['total']          ?? 0;
                $this->model->estado         = $b['estado']         ?? 'pendiente';
                if ($this->model->cliente_nombre === '') {
                    json_response(["status"=>"error","message"=>"Falta nombre del cliente"],400);
                }
                $id = $this->model->create();
                json_response(["status"=>"success","message"=>"Orden creada","id"=>$id], 201);
                break;

            case 'PUT':
                $b = json_decode(file_get_contents("php://input"), true) ?: [];
                if (empty($b['id']) || empty($b['estado'])) {
                    json_response(["status"=>"error","message"=>"Falta id o estado"],400);
                }
                $this->model->updateEstado((int)$b['id'], $b['estado'])
                    ? json_response(["status"=>"success","message"=>"Estado actualizado"])
                    : json_response(["status"=>"error","message"=>"Estado inválido"],400);
                break;

            case 'DELETE':
                $id = (int)($_GET['id'] ?? 0);
                if ($id <= 0) json_response(["status"=>"error","message"=>"Falta id"],400);
                $this->model->delete($id);
                json_response(["status"=>"success","message"=>"Orden eliminada"]);
                break;

            default:
                json_response(["status"=>"error","message"=>"Método no permitido"],405);
        }
    }
}