<?php
// Configuración de seguridad
ini_set('display_errors', 0);
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Content-Security-Policy: default-src \'self\'');

// Función para validar token CSRF
function validateCSRFToken($token) {
    if (empty($token) || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Función para sanitizar entradas
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Función para cifrar datos sensibles (simulación de BitLocker)
function encryptWithBitLocker($data, $key) {
    // En un entorno real, aquí se implementaría la integración con BitLocker
    // Esta es una simulación usando OpenSSL para el ejemplo
    $ivlen = openssl_cipher_iv_length($cipher = "AES-256-CBC");
    $iv = openssl_random_pseudo_bytes($ivlen);
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Iniciar sesión para manejo de CSRF
session_start();

// Verificar si es una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verificar token CSRF
    if (!validateCSRFToken($_POST['csrf_token'])) {
        die('Error de seguridad: token CSRF inválido');
    }
    
    // Validar y sanitizar entradas
    $nombre = sanitizeInput($_POST['nombre'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $telefono = sanitizeInput($_POST['telefono'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validar campos requeridos
    if (empty($nombre) || empty($email) || empty($password)) {
        die('Error: Todos los campos marcados con * son obligatorios');
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Error: Formato de email inválido');
    }
    
    // Validar contraseña
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        die('Error: La contraseña no cumple con los requisitos de seguridad');
    }
    
    // Hash de contraseña
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Verificar si es donante
    $es_donante = isset($_POST['donante']) ? 1 : 0;
    
    // Procesar datos de donante si corresponde
    $datos_pago = null;
    if ($es_donante) {
        // Validar datos de donación
        $tipo_donacion = sanitizeInput($_POST['tipo_donacion'] ?? '');
        $monto = filter_var($_POST['monto'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $metodo_pago = sanitizeInput($_POST['metodo_pago'] ?? '');
        
        if (empty($tipo_donacion) || empty($monto) || empty($metodo_pago)) {
            die('Error: Información de donación incompleta');
        }
        
        // Datos de pago a cifrar
        $payment_data = [
            'tipo' => $tipo_donacion,
            'monto' => $monto,
            'metodo' => $metodo_pago,
            'fecha' => date('Y-m-d H:i:s'),
            'email' => $email
        ];
        
        // Generar clave de cifrado única (en producción usar un método más seguro)
        $encryption_key = bin2hex(random_bytes(32));
        
        // Cifrar datos de pago
        $datos_pago = encryptWithBitLocker(json_encode($payment_data), $encryption_key);
        
        // En un entorno real, la clave se almacenaría de forma segura
        // y se integraría con BitLocker para el cifrado real
    }
    
    try {
        // Conectar a la base de datos
        $db = new SQLite3('database/usuarios.db');
        
        // Crear tabla si no existe
        $db->exec('
            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                telefono TEXT,
                password TEXT NOT NULL,
                es_donante INTEGER DEFAULT 0,
                datos_pago TEXT,
                encryption_key TEXT,
                fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
                ip_registro TEXT
            )
        ');
        
        // Preparar consulta
        $stmt = $db->prepare('
            INSERT INTO usuarios 
            (nombre, email, telefono, password, es_donante, datos_pago, encryption_key, ip_registro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        
        // Vincular parámetros
        $stmt->bindValue(1, $nombre, SQLITE3_TEXT);
        $stmt->bindValue(2, $email, SQLITE3_TEXT);
        $stmt->bindValue(3, $telefono, SQLITE3_TEXT);
        $stmt->bindValue(4, $password_hash, SQLITE3_TEXT);
        $stmt->bindValue(5, $es_donante, SQLITE3_INTEGER);
        $stmt->bindValue(6, $datos_pago, SQLITE3_TEXT);
        $stmt->bindValue(7, $encryption_key ?? null, SQLITE3_TEXT);
        $stmt->bindValue(8, $_SERVER['REMOTE_ADDR'], SQLITE3_TEXT);
        
        // Ejecutar consulta
        $result = $stmt->execute();
        
        if ($result) {
            // Registro exitoso
            // Generar nuevo token CSRF para la siguiente solicitud
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            
            // Redirigir a página de éxito
            header('Location: registro_exitoso.html');
            exit;
        } else {
            throw new Exception('Error al registrar usuario');
        }
        
    } catch (Exception $e) {
        // Verificar si es un error de email duplicado
        if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
            die('Error: El email ya está registrado');
        }
        
        // Error general
        die('Error en el registro: ' . $e->getMessage());
    }
}
?>