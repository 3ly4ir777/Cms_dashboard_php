<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_user'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "No autenticado"]);
    exit;
}
echo json_encode(["status" => "success", "user" => $_SESSION['admin_user']]);