<?php
// Iniciar sesión para verificar autenticación
session_start();

// Verificar si el usuario está autenticado como administrador
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirigir a la página de login
    header('Location: login.php');
    exit;
}

// Función para obtener estadísticas de usuarios
function getUserStats() {
    try {
        $db = new SQLite3('../database/user_registry.db');
        
        // Total de usuarios
        $totalUsers = $db->querySingle('SELECT COUNT(*) FROM users');
        
        // Total de donantes
        $totalDonors = $db->querySingle('SELECT COUNT(*) FROM users WHERE es_donante = 1');
        
        // Usuarios registrados hoy
        $today = date('Y-m-d');
        $newUsers = $db->querySingle("SELECT COUNT(*) FROM users WHERE date(fecha_registro) = '$today'");
        
        // Monto total de donaciones
        $totalAmount = $db->querySingle('SELECT SUM(monto) FROM donantes');
        
        return [
            'total_users' => $totalUsers,
            'total_donors' => $totalDonors,
            'new_users' => $newUsers,
            'total_amount' => $totalAmount ?: 0
        ];
    } catch (Exception $e) {
        return [
            'error' => $e->getMessage(),
            'total_users' => 0,
            'total_donors' => 0,
            'new_users' => 0,
            'total_amount' => 0
        ];
    }
}

// Función para obtener estadísticas de contacto
function getContactStats() {
    try {
        $db = new SQLite3('../database/contact_form.db');
        
        // Total de mensajes
        $totalMessages = $db->querySingle('SELECT COUNT(*) FROM contact_messages');
        
        // Mensajes no leídos
        $unreadMessages = $db->querySingle('SELECT COUNT(*) FROM contact_messages WHERE leido = 0');
        
        // Mensajes de hoy
        $today = date('Y-m-d');
        $newMessages = $db->querySingle("SELECT COUNT(*) FROM contact_messages WHERE date(fecha) = '$today'");
        
        return [
            'total_messages' => $totalMessages,
            'unread_messages' => $unreadMessages,
            'new_messages' => $newMessages
        ];
    } catch (Exception $e) {
        return [
            'error' => $e->getMessage(),
            'total_messages' => 0,
            'unread_messages' => 0,
            'new_messages' => 0
        ];
    }
}

// Función para obtener los últimos usuarios registrados
function getLatestUsers($limit = 5) {
    try {
        $db = new SQLite3('../database/user_registry.db');
        $query = $db->query('SELECT id, nombre, email, fecha_registro, es_donante FROM users ORDER BY fecha_registro DESC LIMIT ' . $limit);
        
        $users = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $users[] = $row;
        }
        
        return $users;
    } catch (Exception $e) {
        return [];
    }
}

// Función para obtener los últimos mensajes de contacto
function getLatestMessages($limit = 5) {
    try {
        $db = new SQLite3('../database/contact_form.db');
        $query = $db->query('SELECT id, nombre, email, asunto, fecha, leido FROM contact_messages ORDER BY fecha DESC LIMIT ' . $limit);
        
        $messages = [];
        while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
            $messages[] = $row;
        }
        
        return $messages;
    } catch (Exception $e) {
        return [];
    }
}

// Obtener estadísticas
$userStats = getUserStats();
$contactStats = getContactStats();
$latestUsers = getLatestUsers();
$latestMessages = getLatestMessages();

// Verificar si se solicitó enviar la base de datos por correo
if (isset($_POST['send_database'])) {
    // Incluir el script de envío de base de datos
    include_once('../send_database_email.php');
    $emailSent = true;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Plataforma Por la Libertad</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .admin-actions {
            display: flex;
            gap: 10px;
        }
        
        .admin-button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        .admin-button.secondary {
            background-color: #6c757d;
        }
        
        .admin-button.danger {
            background-color: #dc3545;
        }
        
        .admin-button:hover {
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 10px 0;
            color: #0056b3;
        }
        
        .stat-label {
            font-size: 16px;
            color: #6c757d;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .data-table tr:hover {
            background-color: #f1f3f5;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .admin-actions {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../index.html">
                    <img src="../ppluy.svg" alt="Logo Plataforma Por la Libertad">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.html">Inicio</a></li>
                    <li><a href="../quienes-somos.html">Quiénes Somos</a></li>
                    <li><a href="../propuestas.html">Propuestas</a></li>
                    <li><a href="../noticias.html">Noticias</a></li>
                    <li><a href="../documentos.html">Documentos</a></li>
                    <li><a href="../contacto.html">Contacto</a></li>
                    <li><a href="../registro.html">Registro</a></li>
                    <li><a href="../health-monitor.html">Monitor</a></li>
                    <li><a href="monitor.php" class="active">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="admin-container">
            <div class="admin-header">
                <h1>Panel de Administración</h1>
                <div class="admin-actions">
                    <form method="post" action="">
                        <button type="submit" name="send_database" class="admin-button">Enviar BBDD por Email</button>
                    </form>
                    <a href="users.php" class="admin-button">Gestionar Usuarios</a>
                    <a href="messages.php" class="admin-button">Ver Mensajes</a>
                    <a href="logout.php" class="admin-button danger">Cerrar Sesión</a>
                </div>
            </div>
            
            <?php if (isset($emailSent)): ?>
            <div class="alert alert-success">
                Base de datos enviada por correo electrónico correctamente.
            </div>
            <?php endif; ?>
            
            <?php if (isset($userStats['error']) || isset($contactStats['error'])): ?>
            <div class="alert alert-danger">
                Error al conectar con la base de datos. Por favor, verifique que exista.
            </div>
            <?php endif; ?>
            
            <h2>Estadísticas Generales</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $userStats['total_users']; ?></div>
                    <div class="stat-label">Usuarios Registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $userStats['total_donors']; ?></div>
                    <div class="stat-label">Donantes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $userStats['new_users']; ?></div>
                    <div class="stat-label">Nuevos Usuarios Hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">$<?php echo number_format($userStats['total_amount'], 0, ',', '.'); ?></div>
                    <div class="stat-label">Total Donaciones</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $contactStats['total_messages']; ?></div>
                    <div class="stat-label">Mensajes Recibidos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $contactStats['unread_messages']; ?></div>
                    <div class="stat-label">Mensajes No Leídos</div>
                </div>
            </div>
            
            <div class="section-header">
                <h2>Últimos Usuarios Registrados</h2>
                <a href="users.php" class="admin-button secondary">Ver Todos</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestUsers)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay usuarios registrados</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($latestUsers as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['fecha_registro'])); ?></td>
                            <td>
                                <?php if ($user['es_donante']): ?>
                                <span class="badge badge-success">Donante</span>
                                <?php else: ?>
                                <span class="badge badge-warning">Usuario</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="section-header">
                <h2>Últimos Mensajes de Contacto</h2>
                <a href="messages.php" class="admin-button secondary">Ver Todos</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($latestMessages)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No hay mensajes recibidos</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($latestMessages as $message): ?>
                        <tr>
                            <td><?php echo $message['id']; ?></td>
                            <td><?php echo htmlspecialchars($message['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo htmlspecialchars($message['asunto']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($message['fecha'])); ?></td>
                            <td>
                                <?php if ($message['leido']): ?>
                                <span class="badge badge-success">Leído</span>
                                <?php else: ?>
                                <span class="badge badge-danger">No Leído</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <h2>Acciones Rápidas</h2>
            <div class="admin-actions" style="margin-bottom: 30px;">
                <a href="backup.php" class="admin-button">Crear Backup Manual</a>
                <a href="logs.php" class="admin-button">Ver Logs del Sistema</a>
                <a href="../health-monitor.html" class="admin-button">Monitor de Salud</a>
                <a href="settings.php" class="admin-button secondary">Configuración</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Plataforma Por la Libertad</h3>
                <p>Trabajando por un Uruguay más libre y próspero.</p>
            </div>
            <div class="footer-section">
                <h3>Enlaces</h3>
                <ul>
                    <li><a href="../index.html">Inicio</a></li>
                    <li><a href="../quienes-somos.html">Quiénes Somos</a></li>
                    <li><a href="../propuestas.html">Propuestas</a></li>
                    <li><a href="../noticias.html">Noticias</a></li>
                    <li><a href="../documentos.html">Documentos</a></li>
                    <li><a href="../contacto.html">Contacto</a></li>
                    <li><a href="../registro.html">Registro</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contacto</h3>
                <p>Email: info@ppluy.org</p>
                <p>Teléfono: +598 99 123 456</p>
                <p>Dirección: Av. 18 de Julio 1234, Montevideo, Uruguay</p>
            </div>
        </div>
        <p>&copy; 2025 Plataforma Por la Libertad. Todos los derechos reservados.</p>
    </footer>

    <script>
        // Actualizar datos automáticamente cada 60 segundos
        setInterval(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>