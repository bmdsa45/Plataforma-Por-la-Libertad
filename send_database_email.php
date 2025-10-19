<?php
/**
 * Script para envío automático de la base de datos por email
 * Este script debe ejecutarse mediante una tarea programada cada 24 horas
 */

// Configuración de correo electrónico
$config = [
    'admin_email' => 'admin@ppluy.org', // Cambiar por el email del administrador
    'from_email' => 'sistema@ppluy.org',
    'subject' => 'Backup diario de base de datos - Plataforma Por la Libertad',
    'message' => 'Adjunto encontrará el backup diario de las bases de datos del sitio web.',
    'timezone' => 'America/Montevideo'
];

// Establecer zona horaria
date_default_timezone_set($config['timezone']);

// Función para registrar logs
function logMessage($message) {
    $date = date('Y-m-d H:i:s');
    $logFile = __DIR__ . '/logs/database_backup_' . date('Y-m') . '.log';
    
    // Crear directorio de logs si no existe
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    file_put_contents($logFile, "[$date] $message" . PHP_EOL, FILE_APPEND);
}

// Función para crear backup de la base de datos
function createDatabaseBackup($dbPath) {
    if (!file_exists($dbPath)) {
        logMessage("Error: Base de datos no encontrada en $dbPath");
        return false;
    }
    
    $backupDir = __DIR__ . '/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $backupFile = $backupDir . '/' . basename($dbPath) . '_' . date('Y-m-d') . '.sqlite';
    
    try {
        // Copiar archivo de base de datos
        if (copy($dbPath, $backupFile)) {
            logMessage("Backup creado exitosamente: $backupFile");
            return $backupFile;
        } else {
            logMessage("Error al crear backup de $dbPath");
            return false;
        }
    } catch (Exception $e) {
        logMessage("Excepción al crear backup: " . $e->getMessage());
        return false;
    }
}

// Función para enviar email con archivos adjuntos
function sendEmailWithAttachments($to, $from, $subject, $message, $attachments = []) {
    $boundary = md5(time());
    
    $headers = [
        'From' => $from,
        'MIME-Version' => '1.0',
        'Content-Type' => "multipart/mixed; boundary=\"$boundary\""
    ];
    
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($message)) . "\r\n";
    
    // Adjuntar archivos
    foreach ($attachments as $file) {
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $filename = basename($file);
            
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n";
            $body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
            $body .= chunk_split(base64_encode($content)) . "\r\n";
        }
    }
    
    $body .= "--$boundary--";
    
    // Enviar email
    $success = mail($to, $subject, $body, $headers);
    
    if ($success) {
        logMessage("Email enviado exitosamente a $to");
        return true;
    } else {
        logMessage("Error al enviar email a $to");
        return false;
    }
}

// Inicio del proceso
logMessage("Iniciando proceso de backup y envío de base de datos");

// Bases de datos a respaldar
$databases = [
    __DIR__ . '/database/contact_form.db',
    __DIR__ . '/database/user_registry.db'
];

$backupFiles = [];

// Crear backups de cada base de datos
foreach ($databases as $db) {
    $backupFile = createDatabaseBackup($db);
    if ($backupFile) {
        $backupFiles[] = $backupFile;
    }
}

// Enviar email con los backups
if (!empty($backupFiles)) {
    $date = date('Y-m-d');
    $config['subject'] .= " ($date)";
    
    $result = sendEmailWithAttachments(
        $config['admin_email'],
        $config['from_email'],
        $config['subject'],
        $config['message'] . "\n\nFecha: $date\n\nEste es un mensaje automático, por favor no responda.",
        $backupFiles
    );
    
    if ($result) {
        logMessage("Proceso completado exitosamente");
    } else {
        logMessage("El proceso falló al enviar el email");
    }
} else {
    logMessage("No se pudieron crear backups de las bases de datos");
}

// Eliminar backups antiguos (más de 7 días)
$backupDir = __DIR__ . '/backups';
if (is_dir($backupDir)) {
    $files = glob($backupDir . '/*.sqlite');
    $now = time();
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 7 * 24 * 60 * 60) { // 7 días
                unlink($file);
                logMessage("Backup antiguo eliminado: $file");
            }
        }
    }
}

logMessage("Proceso finalizado");
?>