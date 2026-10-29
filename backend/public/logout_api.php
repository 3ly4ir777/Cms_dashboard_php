<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
require_once __DIR__ . '/../controllers/AuthController.php';
(new AuthController())->logout();