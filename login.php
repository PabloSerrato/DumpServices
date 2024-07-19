<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include 'conexion.php';
include 'header.php';

// Verificar si el usuario ya está autenticado
if (isset($_SESSION['usuario_id'])) {
    // Si el usuario ya está autenticado, redirigirlo a la página correspondiente según su rol
    switch ($_SESSION['rol']) {
        case 'solicitante_transporte':
            header("Location: solicitante.php");
            exit();
        case 'operador_logistico':
            header("Location: operario.php");
            exit();
        case 'administrador':
            header("Location: admin/admin.php");
            exit();
        default:
            // En caso de que el rol no esté definido o sea incorrecto, mostrar mensaje de error
            $mensaje = "Rol de usuario no válido.";
    }
}

// Verificar si se ha enviado el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se han enviado los datos de usuario y contraseña
    if (isset($_POST['usuario']) && isset($_POST['contrasena'])) {
        // Obtener los valores de usuario y contraseña del formulario
        $usuario = $_POST['usuario'];
        $contrasena = $_POST['contrasena'];

        // Consulta para verificar las credenciales del usuario en la base de datos
        $sql = "SELECT * FROM usuarios WHERE correo = '$usuario' AND contrasena = '$contrasena'";
        $resultado = $conn->query($sql);

        // Verificar si se encontraron registros que coincidan con las credenciales
        if ($resultado->num_rows > 0) {
            // Obtener información del usuario
            $usuario_info = $resultado->fetch_assoc();

            // Guardar información del usuario en la sesión
            $_SESSION['usuario_id'] = $usuario_info['id'];
            $_SESSION['rol'] = $usuario_info['rol'];

            // Crear cookie para recordar la sesión
            setcookie("usuario_id", $usuario_info['id'], time() + (86400 * 30), "/"); // 86400 segundos = 1 día

            // Redirigir según el rol del usuario
            switch ($usuario_info['rol']) {
                case 'solicitante_transporte':
                    header("Location: solicitante.php");
                    exit();
                case 'operador_logistico':
                    header("Location: operario.php");
                    exit();
                case 'administrador':
                    header("Location: admin.php");
                    exit();
                default:
                    // En caso de que el rol no esté definido o sea incorrecto, mostrar mensaje de error
                    $mensaje = "Rol de usuario no válido.";
            }
        } else {
            // Las credenciales no son válidas, mostrar mensaje de error
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Usuario o contraseña incorrectos",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                  </script>';
        }        
    } else {
        // Los datos de usuario y contraseña no se enviaron correctamente, mostrar mensaje de error
        $mensaje = "Por favor, ingresa usuario y contraseña";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dump Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS */
        :root {
            --primary-color: #032b53;
            --secondary-color: #064575;
            --background-color: #f2f2f2;
            --text-color: #333;
            --input-background: #fff;
            --link-color: #064575;
            --font-family: 'Roboto', sans-serif;
            --transition-speed: 0.3s;
        }

        /* Estilos generales */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--background-color);
            color: var(--text-color);
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            max-width: 1200px;
            margin: 10vh auto;
        }

        .login-form-container {
            width: 45%;
            animation: fadeInLeft var(--transition-speed) ease-out;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: var(--input-background);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            animation: slideIn var(--transition-speed) ease-out;
        }

        .login-label {
            font-weight: bold;
            text-align: left;
        }

        .login-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .login-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .login-button {
            width: 100%;
            padding: 10px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .login-button:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        .login-link {
            display: block;
            margin-top: 20px;
            color: var(--link-color);
            text-decoration: none;
            text-align: center;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .login-link:hover {
            color: var(--primary-color);
            transform: scale(1.05);
        }

        .side-image {
            width: 45%;
            height: auto;
            border-radius: 10px;
            animation: fadeInRight var(--transition-speed) ease-out;
        }

        /* Animaciones */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-form-container">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="login-form">
                <label for="usuario" class="login-label">Usuario (Correo Electrónico):</label>
                <input type="email" id="usuario" name="usuario" required class="login-input">
                
                <label for="contrasena" class="login-label">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required class="login-input">
                
                <button type="submit" class="login-button">Ingresar</button>        
                <a href="createAcount.php" class="login-link">Crea tu cuenta</a>
                <a href="recuperar_contrasena.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
        </form>
            </form>
        </div>
        
        <img src="fonts/imagenes/logo.png" alt="Descripción de la imagen" class="side-image">
    </div>
</body>
</html>
