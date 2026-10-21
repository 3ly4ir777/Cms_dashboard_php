<?php
// Script de prueba para conectar Podman PHP con MariaDB/MySQL

// 1. Validar e instalar la extensión de MySQL si no está activa
if (!extension_loaded('mysqli')) {
    echo "¡Ups! Falta activar la extensión MySQL. Ejecuta este comando en tu terminal de Fedora:<br>";
    echo "<b>podman exec -it mi_php_backend docker-php-ext-install mysqli</b><br>";
    echo "Y luego reinicia el contenedor desde Podman Desktop. 🐥<br><br>";
    exit();
}

// 2. Intentar la conexión
$host = 'db'; // El nombre del servicio en tu compose.yaml
$user = 'root';
$pass = '';
$dbname = 'ecommerce_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
} 
echo "¡Conexión exitosa a la base de datos desde Fedora con Podman! 🐧🚀";
?>
