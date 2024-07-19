<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dump Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="fonts\imagenes\logo.png" type="image/x-icon">

    <style>
        /* Variables CSS */
        :root {
            --primary-color: #032b53;
            --secondary-color: #064575;
            --background-color: #f2f2f2;
            --header-background: #021027;
            --header-shadow: rgba(0, 0, 0, 0.1);
            --header-border: #032b53;
            --text-color: #333;
            --button-border-radius: 50px;
            --transition-speed: 0.3s;
        }

        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        /* Estilos del encabezado */
        a{
            text-decoration: none;
        }
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--header-background);
            color: #fff;
            padding: 20px;
            box-shadow: 0 2px 8px var(--header-shadow);
            border-bottom: 4px solid var(--header-border);
        }

        .main-header .logo h1 {
            font-size: 2rem;
            color: #fff;
            margin: 0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            transition: color var(--transition-speed) ease;
        }

        .main-header .logo h1:hover {
            color: #ddd;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        .auth-buttons button {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            border-radius: var(--button-border-radius);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background-color var(--transition-speed) ease, transform var(--transition-speed) ease;
        }

        .auth-buttons button i {
            font-size: 1.2rem;
        }

        .auth-buttons button:hover {
            background-color: var(--secondary-color);
            transform: scale(1.05);
        }

        .auth-buttons button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(3, 43, 83, 0.5);
        }

        /* Ajustes responsivos */
        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                text-align: center;
            }

            .auth-buttons {
                justify-content: center;
                margin-top: 10px;
            }

            .auth-buttons button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <a href="index.php  "><h1>Dump Services</h1>
            </a>
        </div>
        <div class="auth-buttons">
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            if (isset($_SESSION['usuario_id'])) {
                // Mostrar botón de cerrar sesión
                echo '<button onclick="window.location.href = \'logout.php\';"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</button>';

                // Mostrar botón de redirección según el rol
                $rol = $_SESSION['rol'];
                switch ($rol) {
                    case 'solicitante_transporte':
                        $rol_url = 'solicitante.php';
                        break;
                    case 'operador_logistico':
                        $rol_url = 'operario.php';
                        break;
                    case 'administrador':
                        $rol_url = 'admin.php';
                        break;
                    default:
                        $rol_url = '#';
                        break;
                }
                echo '<button onclick="window.location.href = \'' . $rol_url . '\';"><i class="fas fa-user"></i> Mi Cuenta</button>';
            } else {
                // Mostrar botón de ingresar
                echo '<button onclick="window.location.href = \'login.php\';"><i class="fas fa-sign-in-alt"></i> Ingresar</button>';
            }
            ?>
        </div>
    </header>
</body>
</html>
