<?php
require_once __DIR__ . '/config/db.php';

try {
    $db = (new DB())->getConnection();
    echo "🌱 Migrando...\n";

    // --- administradores (ya existía) ---
    $db->exec("CREATE TABLE IF NOT EXISTS administradores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        codigo_especial INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ administradores OK\n";

    // --- productos ---
    $db->exec("CREATE TABLE IF NOT EXISTS productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(120) NOT NULL,
        descripcion TEXT NULL,
        precio DECIMAL(10,2) NOT NULL DEFAULT 0,
        stock INT NOT NULL DEFAULT 0,
        categoria VARCHAR(60) NULL,
        imagen_url VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ productos OK\n";

    // --- ordenes ---
    $db->exec("CREATE TABLE IF NOT EXISTS ordenes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente_nombre VARCHAR(120) NOT NULL,
        cliente_email VARCHAR(120) NULL,
        total DECIMAL(10,2) NOT NULL DEFAULT 0,
        estado ENUM('pendiente','pagada','enviada','entregada','cancelada') NOT NULL DEFAULT 'pendiente',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ ordenes OK\n";

    // --- items de orden ---
    $db->exec("CREATE TABLE IF NOT EXISTS orden_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        orden_id INT NOT NULL,
        producto_id INT NOT NULL,
        cantidad INT NOT NULL,
        precio_unitario DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (orden_id) REFERENCES ordenes(id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES productos(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ orden_items OK\n";

    // --- admin de prueba ---
    $stmt = $db->prepare("SELECT COUNT(*) FROM administradores WHERE usuario = 'amiguito'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $db->prepare("INSERT INTO administradores (usuario, password_hash, codigo_especial) VALUES (?, ?, ?)")
           ->execute(['amiguito', password_hash('secreto123', PASSWORD_ARGON2ID), 777]);
        echo "👤 admin 'amiguito' creado (pass: secreto123, código: 777)\n";
    }

    // --- productos de prueba ---
    $stmt = $db->prepare("SELECT COUNT(*) FROM productos");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $ins = $db->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES (?,?,?,?,?)");
        $ins->execute(['Teclado mecánico', 'Switch azul, retroiluminado', 45.90, 12, 'Periféricos']);
        $ins->execute(['Mouse gamer', 'RGB 12800 DPI', 29.50, 30, 'Periféricos']);
        $ins->execute(['Monitor 24"', 'Full HD 75Hz', 149.99, 8, 'Monitores']);
        echo "📦 3 productos de ejemplo insertados\n";
    }

        // --- clientes (NUEVO) ---
    $db->exec("CREATE TABLE IF NOT EXISTS clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(120) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        usuario VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        telefono VARCHAR(30) NULL,
        direccion VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "✅ clientes OK\n";

    // Añadir cliente_id a ordenes si no existe (para no romper las viejas)
    $col = $db->query("SHOW COLUMNS FROM ordenes LIKE 'cliente_id'")->fetch();
    if (!$col) {
        $db->exec("ALTER TABLE ordenes ADD COLUMN cliente_id INT NULL AFTER id");
        $db->exec("ALTER TABLE ordenes ADD CONSTRAINT fk_orden_cliente
                   FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL");
        echo "➕ columna cliente_id agregada a ordenes\n";
    }

    // --- cliente de prueba ---
    $stmt = $db->prepare("SELECT COUNT(*) FROM clientes WHERE usuario = 'cliente1'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $db->prepare("INSERT INTO clientes (nombre, email, usuario, password_hash)
                      VALUES (?,?,?,?)")
           ->execute(['Cliente Demo', 'cliente1@demo.com', 'cliente1',
                      password_hash('demo123', PASSWORD_ARGON2ID)]);
        echo "👤 cliente 'cliente1' creado (pass: demo123)\n";
    }

    echo "🚀 ¡Migración completa, amiguito!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}


//en podman correr para crear 
//podman exec -it mi_php_backend php /var/www/migrar.php

