<?php
require_once __DIR__ . '/config/db.php';

try {
    $database = new DB();
    $db = $database->getConnection();

    echo "🌱 Iniciando migración desde el backend...\n";

    // 1. El código SQL para crear la tabla si no existe
    $sqlTabla = "CREATE TABLE IF NOT EXISTS administradores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        codigo_especial INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sqlTabla);
    echo "✅ Tabla 'administradores' creada o verificada con éxito.\n";

    // 2. Verificar si ya existe nuestro usuario de prueba para no duplicarlo
    $stmt = $db->prepare("SELECT COUNT(*) FROM administradores WHERE usuario = 'amiguito'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        // Encriptamos con Argon2id directamente desde el backend
        $password_hash = password_hash('secreto123', PASSWORD_ARGON2ID);
        $codigo_especial = 777;

        $sqlInsert = "INSERT INTO administradores (usuario, password_hash, codigo_especial) VALUES (:usuario, :password, :codigo)";
        $insertStmt = $db->prepare($sqlInsert);
        $insertStmt->execute([
            ':usuario' => 'amiguito',
            ':password' => $password_hash,
            ':codigo' => $codigo_especial
        ]);
        echo "👤 Usuario administrador de prueba ('amiguito') creado con éxito.\n";
    } else {
        echo "ℹ️ El usuario de prueba ya existía, no se duplicó.\n";
    }

    echo "🚀 ¡Migración completada con éxito, amiguito!\n";

} catch (PDOException $e) {
    echo "❌ Error en la migración: " . $e->getMessage() . "\n";
}


//en podman correr para crear 
//podman exec -it mi_php_backend php /var/www/migrar.php

