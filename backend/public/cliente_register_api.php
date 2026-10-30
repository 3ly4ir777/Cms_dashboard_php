<?php
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
require_once __DIR__ . '/../controllers/ClienteController.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') (new ClienteController())->register();
else json_response(["status"=>"error","message"=>"Método no permitido"], 405);