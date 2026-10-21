<?php
// ¡Hola! Este es nuestro index de prueba temporal 🐣

echo "<h1>¡Servidor web activo en Fedora con Podman! 🐧🚀</h1>";
echo "<p>La carpeta 'public' está funcionando como raíz de manera segura.</p>";

// Intentemos cargar tu clase de base de datos para ver si conecta
require_once __DIR__ . '/../config/db.php';

try {
    $database = new DB();
    $db = $database->getConnection();
    echo "<p style='color: green; font-weight: bold;'>¡Yujuuu! La base de datos también se conectó exitosamente. 🎉</p>";
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>El servidor web sirve, pero la base de datos dice: " . $e->getMessage() . "</p>";
}
