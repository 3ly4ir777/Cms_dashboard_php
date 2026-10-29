<?php
// CORS PRIMERO
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../controllers/AuthController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    (new AuthController())->login();
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}