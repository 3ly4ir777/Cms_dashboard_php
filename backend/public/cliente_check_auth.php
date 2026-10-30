<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['cliente_user'])) {
    http_response_code(401);
    echo json_encode(["status"=>"error","message"=>"No autenticado como cliente"]);
    exit;
}
echo json_encode(["status"=>"success","cliente"=>$_SESSION['cliente_user']]);