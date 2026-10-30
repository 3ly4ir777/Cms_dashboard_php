<?php
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';
require_once __DIR__ . '/../models/Order.php';

$user = require_cliente_auth();
$db   = (new DB())->getConnection();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $s = $db->prepare("SELECT * FROM ordenes WHERE cliente_id = :cid ORDER BY id DESC");
        $s->execute([':cid' => $user['id']]);
        json_response(["status"=>"success","data"=>$s->fetchAll()]);
        break;

    case 'POST':
        $b = json_decode(file_get_contents("php://input"), true) ?: [];
        $items = $b['items'] ?? [];
        if (!is_array($items) || empty($items))
            json_response(["status"=>"error","message"=>"Carrito vacío"], 400);
        foreach ($items as $it) {
            if (empty($it['producto_id']) || empty($it['cantidad']) || (int)$it['cantidad'] <= 0)
                json_response(["status"=>"error","message"=>"Item inválido en el carrito"], 400);
        }
        try {
            $id = (new Order($db))->createConItems($user['id'], $items);
            json_response(["status"=>"success","message"=>"Orden creada","id"=>$id], 201);
        } catch (Exception $e) {
            json_response(["status"=>"error","message"=>$e->getMessage()], 400);
        }
        break;

    default:
        json_response(["status"=>"error","message"=>"Método no permitido"], 405);
}