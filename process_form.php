<?php
// Configuración de seguridad
session_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Función para sanitizar entradas
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Verificar si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Error de validación de seguridad']);
        exit;
    }
    
    // Verificar captcha
    if (!isset($_POST['captcha']) || !isset($_SESSION['captcha_answer']) || 
        (int)$_POST['captcha'] !== $_SESSION['captcha_answer']) {
        echo json_encode(['success' => false, 'message' => 'Verificación de captcha incorrecta']);
        exit;
    }
    
    // Validar y sanitizar entradas
    $name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
    $subject = isset($_POST['subject']) ? sanitize_input($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';
    
    // Validaciones adicionales
    if (empty($name) || strlen($name) < 2 || strlen($name) > 100) {
        echo json_encode(['success' => false, 'message' => 'Por favor ingresa un nombre válido']);
        exit;
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Por favor ingresa un correo electrónico válido']);
        exit;
    }
    
    if (empty($subject) || strlen($subject) < 3 || strlen($subject) > 100) {
        echo json_encode(['success' => false, 'message' => 'Por favor ingresa un asunto válido']);
        exit;
    }
    
    if (empty($message) || strlen($message) < 10 || strlen($message) > 1000) {
        echo json_encode(['success' => false, 'message' => 'Por favor ingresa un mensaje válido (entre 10 y 1000 caracteres)']);
        exit;
    }
    
    // Configurar conexión a la base de datos SQLite
    $db_path = __DIR__ . '/database/contact_form.db';
    $db_dir = dirname($db_path);
    
    // Crear directorio de base de datos si no existe
    if (!is_dir($db_dir)) {
        mkdir($db_dir, 0755, true);
    }
    
    try {
        // Conectar a la base de datos
        $db = new PDO('sqlite:' . $db_path);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Crear tabla si no existe
        $db->exec('CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NOT NULL,
            message TEXT NOT NULL,
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');
        
        // Preparar y ejecutar la inserción
        $stmt = $db->prepare('INSERT INTO contacts (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt->execute([$name, $email, $subject, $message, $ip]);
        
        // Responder con éxito
        echo json_encode(['success' => true, 'message' => '¡Gracias por tu mensaje! Te responderemos a la brevedad.']);
        
    } catch (PDOException $e) {
        // Log del error (no mostrar detalles al usuario)
        error_log('Error en la base de datos: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Ha ocurrido un error al procesar tu solicitud. Por favor, intenta nuevamente.']);
    }
    
} else {
    // Si no es una solicitud POST, redirigir a la página de contacto
    header('Location: contacto.html');
    exit;
}
?>