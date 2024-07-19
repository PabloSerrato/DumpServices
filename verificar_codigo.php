<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = filter_var($_POST['codigo'], FILTER_SANITIZE_STRING);
    $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
    $nueva_contrasena = $_POST['nueva_contrasena'];  // Contraseña sin hashear

    if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        // Verificar si el código es válido y no ha expirado
        $stmt = $conn->prepare("SELECT * FROM restablecer_password WHERE correo = ? AND codigo = ? AND expira > NOW()");
        $stmt->bind_param("ss", $correo, $codigo);
        $stmt->execute();
        $resultado_verificar_codigo = $stmt->get_result();

        if ($resultado_verificar_codigo->num_rows > 0) {
            // Actualizar la contraseña del usuario
            $stmt_actualizar = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE correo = ?");
            $stmt_actualizar->bind_param("ss", $nueva_contrasena, $correo);
            if ($stmt_actualizar->execute()) {
                // Eliminar el código usado de la tabla de restablecimiento
                $stmt_eliminar_codigo = $conn->prepare("DELETE FROM restablecer_password WHERE correo = ? AND codigo = ?");
                $stmt_eliminar_codigo->bind_param("ss", $correo, $codigo);
                $stmt_eliminar_codigo->execute();

                // Redirigir al usuario a la página de inicio de sesión
                header('Location: login.php');
                exit();
            } else {
                echo "Error al actualizar la contraseña: " . $conn->error;
            }
            $stmt_actualizar->close();
        } else {
            echo "Código inválido o expirado.";
        }
        $stmt->close();
    } else {
        echo "Correo electrónico no válido.";
    }
    $conn->close();
}
?>
