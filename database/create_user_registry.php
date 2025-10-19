<?php
/**
 * Script para crear la base de datos de registro de usuarios
 */

// Configuración
$dbPath = __DIR__ . '/user_registry.db';

// Crear la base de datos si no existe
try {
    $db = new SQLite3($dbPath);
    
    // Crear tabla de usuarios
    $db->exec('
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            telefono TEXT,
            password TEXT NOT NULL,
            fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
            es_donante INTEGER DEFAULT 0
        )
    ');
    
    // Crear tabla de donantes
    $db->exec('
        CREATE TABLE IF NOT EXISTS donantes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            tipo_donacion TEXT NOT NULL,
            monto INTEGER NOT NULL,
            metodo_pago TEXT NOT NULL,
            datos_pago TEXT NOT NULL,
            fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ');
    
    // Crear tabla de logs
    $db->exec('
        CREATE TABLE IF NOT EXISTS logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            accion TEXT NOT NULL,
            detalles TEXT,
            ip_address TEXT,
            fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )
    ');
    
    echo "Base de datos creada exitosamente en: $dbPath";
    
} catch (Exception $e) {
    echo "Error al crear la base de datos: " . $e->getMessage();
}
?>