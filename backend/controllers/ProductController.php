<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $model;

    public function __construct() {
        $db = (new DB())->getConnection();
        $this->model = new Product($db);
    }

    public function handle($method) {
        switch ($method) {
            case 'GET':
                // PÚBLICO — cualquier visitante ve el catálogo
                if (isset($_GET['id'])) {
                    $p = $this->model->find((int)$_GET['id']);
                    $p ? json_response(["status"=>"success","data"=>$p])
                       : json_response(["status"=>"error","message"=>"No encontrado"], 404);
                }
                json_response(["status"=>"success","data"=>$this->model->all()]);
                break;

            case 'POST':
            case 'PUT':
            case 'DELETE':

                require_auth(); // 🔒 solo admin puede modificar productos

        switch ($method) {

            case 'POST':
                $b = $this->body();
                $this->fill($b);
                if (!$this->model->create()) json_response(["status"=>"error","message"=>"No se pudo crear"],500);
                json_response(["status"=>"success","message"=>"Producto creado"], 201);
                break;

            case 'PUT':
                $b = $this->body();
                if (empty($b['id'])) json_response(["status"=>"error","message"=>"Falta id"],400);
                $this->fill($b);
                $this->model->update();
                json_response(["status"=>"success","message"=>"Producto actualizado"]);
                break;

            case 'DELETE':
                $id = (int)($_GET['id'] ?? 0);
                if ($id <= 0) json_response(["status"=>"error","message"=>"Falta id"],400);
                $this->model->delete($id);
                json_response(["status"=>"success","message"=>"Producto eliminado"]);
                break;

            default:
                json_response(["status"=>"error","message"=>"Método no permitido"],405);
            }
        }
    }

    private function body() {
        return json_decode(file_get_contents("php://input"), true) ?: [];
    }

    private function fill($b) {
        $this->model->id          = $b['id']          ?? null;
        $this->model->nombre      = $b['nombre']      ?? '';
        $this->model->descripcion = $b['descripcion'] ?? '';
        $this->model->precio      = $b['precio']      ?? 0;
        $this->model->stock       = $b['stock']       ?? 0;
        $this->model->categoria   = $b['categoria']   ?? '';
        $this->model->imagen_url  = $b['imagen_url']  ?? null;
    }
}