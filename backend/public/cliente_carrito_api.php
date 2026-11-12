<?php
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_helper.php';

$user = require_cliente_auth();
$db = (new DB())->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Devuelve el carrito actual con info del producto
        $s = $db->prepare(
            "SELECT c.producto_id, c.cantidad, p.nombre, p.precio, p.stock, p.categoria
             FROM carrito_items c
             JOIN productos p ON p.id = c.producto_id
             WHERE c.cliente_id = :cid"
        );
        $s->execute([':cid' => $user['id']]);
        json_response(["status" => "success", "data" => $s->fetchAll()]);
        break;

    case 'POST':
        // Agrega o actualiza cantidad
        $b = json_decode(file_get_contents("php://input"), true) ?: [];
        $prod_id = (int)($b['producto_id'] ?? 0);
        $cant    = (int)($b['cantidad'] ?? 1);

        if ($prod_id <= 0 || $cant <= 0) json_response(["status"=>"error","message"=>"Datos inválidos"], 400);

        // Verificar stock
        $ps = $db->prepare("SELECT stock FROM productos WHERE id = :id");
        $ps->execute([':id' => $prod_id]);
        $prod = $ps->fetch();
        if (!$prod) json_response(["status"=>"error","message"=>"Producto no existe"], 404);
        if ($prod['stock'] < $cant) json_response(["status"=>"error","message"=>"Stock insuficiente"], 400);

        // Insertar o actualizar (ON DUPLICATE KEY)
        $s = $db->prepare(
            "INSERT INTO carrito_items (cliente_id, producto_id, cantidad)
             VALUES (:cid, :pid, :cant)
             ON DUPLICATE KEY UPDATE cantidad = :cant2"
        );
        $s->execute([':cid' => $user['id'], ':pid' => $prod_id, ':cant' => $cant, ':cant2' => $cant]);
        json_response(["status" => "success", "message" => "Carrito actualizado"]);
        break;

    case 'DELETE':
        // Quita un producto
        $prod_id = (int)($_GET['producto_id'] ?? 0);
        if ($prod_id <= 0) json_response(["status"=>"error","message"=>"Falta producto_id"], 400);
        $s = $db->prepare("DELETE FROM carrito_items WHERE cliente_id = :cid AND producto_id = :pid");
        $s->execute([':cid' => $user['id'], ':pid' => $prod_id]);
        json_response(["status" => "success", "message" => "Producto eliminado"]);
        break;
}