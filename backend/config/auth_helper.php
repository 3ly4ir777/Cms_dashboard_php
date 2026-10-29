<?php
// Helper para endpoints protegidos
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_auth() {
    if (!isset($_SESSION['admin_user'])) {
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["status" => "error", "message" => "No autenticado"]);
        exit;
    }
    return $_SESSION['admin_user'];
}

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}