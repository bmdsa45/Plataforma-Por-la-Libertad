<?php
// Iniciar sesión
session_start();

// Verificar si ya está autenticado
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: monitor.php');
    exit;
}

// Credenciales de administrador (en un entorno real, esto estaría en la base de datos)
$admin_credentials = [
    'admin@ppluy.org' => password_hash('admin123', PASSWORD_BCRYPT)
];

// Procesar formulario de login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validar credenciales
    if (isset($admin_credentials[$email]) && password_verify($password, $admin_credentials[$email])) {
        // Autenticar usuario
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        
        // Redirigir al panel de administración
        header('Location: monitor.php');
        exit;
    } else {
        $error = 'Credenciales inválidas. Por favor, intente nuevamente.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador - Plataforma Por la Libertad</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .login-logo img {
            max-width: 150px;
        }
        
        .login-form {
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }
        
        .login-button:hover {
            background-color: #004494;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="../ppluy.svg" alt="Logo Plataforma Por la Libertad">
        </div>
        
        <h1 style="text-align: center;">Acceso Administrador</h1>
        
        <?php if ($error): ?>
        <div class="error-message">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form class="login-form" method="post" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-button">Iniciar Sesión</button>
        </form>
        
        <a href="../index.html" class="back-link">Volver al sitio web</a>
    </div>
</body>
</html>