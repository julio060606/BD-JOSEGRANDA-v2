<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, borra también la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión.
session_destroy();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrar Sesión - I.E.E Jóse Granda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c2c74, #44245b, #4c1c4c);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .logout-modal {
            background: #ffffff;
            border-radius: 15px;
            padding: 40px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            border: 3px solid #001f3f;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: slideIn 0.5s ease-out;
        }
        
        .logout-icon {
            font-size: 64px;
            color: #001f3f;
            margin-bottom: 20px;
            animation: bounce 1s infinite;
        }
        
        .logout-title {
            color: #001f3f;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .logout-message {
            color: #333333;
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .logout-btn {
            background: #001f3f;
            color: #ffffff;
            border: 2px solid #001f3f;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .logout-btn:hover {
            background: #8B0000;
            border-color: #8B0000;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 0, 0, 0.3);
            color: #ffffff;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .loading-bar {
            width: 100%;
            height: 4px;
            background: #f0f0f0;
            border-radius: 2px;
            margin-top: 20px;
            overflow: hidden;
        }
        
        .loading-progress {
            width: 0%;
            height: 100%;
            background: #001f3f;
            border-radius: 2px;
            animation: loading 3s ease-in-out forwards;
        }
        
        @keyframes loading {
            to {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="logout-modal">
        <div class="logout-icon">
            <i class="bi bi-check-circle"></i>
        </div>
        <h1 class="logout-title">¡Sesión Cerrada!</h1>
        <p class="logout-message">
            Has cerrado sesión exitosamente.<br>
            Serás redirigido al inicio automáticamente.
        </p>
        
        <div class="loading-bar">
            <div class="loading-progress"></div>
        </div>
        
        <div class="mt-4">
            <a href="index.html" class="logout-btn">
                <i class="bi bi-house me-2"></i>Ir al Inicio Ahora
            </a>
        </div>
    </div>

    <script>
        // Redirigir automáticamente después de 3 segundos
        setTimeout(function() {
            window.location.href = 'index.html';
        }, 3000);
        
        // También redirigir al hacer click en cualquier parte del modal
        document.querySelector('.logout-modal').addEventListener('click', function() {
            window.location.href = 'index.html';
        });
        
        // Redirigir con la tecla ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.location.href = 'index.html';
            }
        });
    </script>
</body>
</html>