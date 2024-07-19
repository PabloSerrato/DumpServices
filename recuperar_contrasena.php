<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';
require 'vendor/autoload.php'; // Asegúrate de que el autoload de Composer esté incluido

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);

    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // Verificar si el correo existe en la base de datos
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultado_verificar = $stmt->get_result();

        if ($resultado_verificar->num_rows > 0) {
            // Generar un código único de 6 dígitos
            $codigo = rand(100000, 999999);
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Insertar el código en la tabla de restablecimiento de contraseñas
            $stmt_insertar = $conn->prepare("INSERT INTO restablecer_password (correo, codigo, expira) VALUES (?, ?, ?)");
            $stmt_insertar->bind_param("sis", $correo, $codigo, $expira);
            if ($stmt_insertar->execute()) {
                // Enviar el correo electrónico con el código usando PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Configuración del servidor
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de tu proveedor de correo
                    $mail->SMTPAuth = true;
                    $mail->Username = 'soportedumpservices@gmail.com'; // Tu correo electrónico
                    $mail->Password = 'nerx pjbw qmdp gobr'; // Tu contraseña de aplicación
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Destinatarios
                    $mail->setFrom('soportedumpservices@gmail.com', 'Dump Services');
                    $mail->addAddress($correo);

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Restablecer contraseña';
                    $mail->Body = "Hola,<br><br>Hemos recibido una solicitud para restablecer tu contraseña. Usa el siguiente código para restablecer tu contraseña:<br><br>Código: <strong>$codigo</strong><br><br>Si no solicitaste un restablecimiento de contraseña, ignora este correo electrónico.<br><br>Saludos,<br>Equipo de Soporte";

                    $mail->send();
                    echo "Se ha enviado un código para restablecer la contraseña a tu correo electrónico.";
                    header('Location: restablecer_password.php');
                    exit();
                } catch (Exception $e) {
                    echo "Error al enviar el correo: {$mail->ErrorInfo}";
                }
            } else {
                echo "Error al generar el código para restablecer la contraseña: " . $conn->error;
            }
        } else {
            echo "El correo electrónico no está registrado.";
        }
        $stmt->close();
    } else {
        echo "Correo electrónico no válido.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Dump Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='css/solicitante.css'>
</head>
<body>
    <div class="container-contraseña">
        <div class="container">
            <h2>Recuperar Contraseña</h2>
            <form action="recuperar_contrasena.php" method="post">
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="form-group">
                    <button type="submit">Solicitar Código</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
