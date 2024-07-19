<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';
include 'header.php';

// Obtener el ID de usuario de la cookie
$id_usuario = isset($_COOKIE['usuario_id']) ? $_COOKIE['usuario_id'] : null;

// Verificar si el ID de usuario está disponible
if ($id_usuario) {
    // Consulta para verificar si el usuario es un operario
    $sql_verificar_operario = "SELECT id_operario FROM operarios WHERE id_usuario = $id_usuario";
    $resultado_verificar_operario = mysqli_query($conn, $sql_verificar_operario);

    if ($resultado_verificar_operario) {
        // Verificar si se encontró un operario con el ID de usuario actual
        if (mysqli_num_rows($resultado_verificar_operario) > 0) {
            // El usuario es un operario, obtener su ID de operario
            $fila_operario = mysqli_fetch_assoc($resultado_verificar_operario);
            $id_operario = $fila_operario['id_operario'];

            // Función para aceptar la solicitud
            if (isset($_POST['aceptar'])) {
                $id_solicitud = $_POST['id_solicitud'];
                $sql_aceptar = "UPDATE solicitudes SET estado = 'Aceptado' WHERE id = $id_solicitud AND id_operario = $id_operario";
                if (mysqli_query($conn, $sql_aceptar)) {
                    echo "Solicitud aceptada correctamente.";
                } else {
                    echo "Error al aceptar la solicitud: " . mysqli_error($conn);
                }
            }

            // Función para rechazar la solicitud
            if (isset($_POST['rechazar'])) {
                $id_solicitud = $_POST['id_solicitud'];
                $sql_rechazar = "UPDATE solicitudes SET estado = 'Rechazado' WHERE id = $id_solicitud AND id_operario = $id_operario";
                if (mysqli_query($conn, $sql_rechazar)) {
                    echo "Solicitud rechazada correctamente.";
                } else {
                    echo "Error al rechazar la solicitud: " . mysqli_error($conn);
                }
            }

            // Consulta para obtener las solicitudes pendientes y aceptadas del operario actual
            $sql_solicitudes = "SELECT * FROM solicitudes WHERE id_operario = $id_operario AND (estado = 'Espera' OR estado = 'Aceptado') AND NOT estado = 'Cancelado'";
            $resultado_solicitudes = mysqli_query($conn, $sql_solicitudes);

            if ($resultado_solicitudes) {
                // Verificar si hay datos
                if (mysqli_num_rows($resultado_solicitudes) > 0) {
                    echo "<h2>Solicitudes Pendientes</h2>";
                    echo "<div class='solicitudes-list'>";

                    // Recorrer y mostrar los datos de cada solicitud
                    while ($solicitud = mysqli_fetch_assoc($resultado_solicitudes)) {
                        echo "<div class='solicitud'>";
                        echo "<p><strong>Dirección de acarreo:</strong> " . $solicitud['direccion_acarreo'] . "</p>";
                        echo "<p><strong>Detalles de acarreo:</strong> " . $solicitud['detalles_acarreo'] . "</p>";
                        if ($solicitud['estado'] == 'Espera') {
                            echo "<form action='' method='post'>";
                            echo "<input type='hidden' name='id_solicitud' value='" . $solicitud['id'] . "'>";
                            echo "<input type='submit' name='aceptar' value='Aceptar'>";
                            echo "<input type='submit' name='rechazar' value='Rechazar'>";
                            echo "</form>";
                        } elseif ($solicitud['estado'] == 'Aceptado') {
                            echo "<a href='chat.php?id_solicitud=".$solicitud['id']."'>Activar Chat</a>";
                        }
                        echo "</div><hr>";
                    }

                    echo "</div>";
                } else {
                    // No se encontraron solicitudes
                    echo "No se encontraron solicitudes pendientes.";
                }
            } else {
                // Error al ejecutar la consulta
                echo "Error al obtener la lista de solicitudes: " . mysqli_error($conn);
            }
        } else {
            // El usuario no es un operario
            echo "Error: El usuario no es un operario.";
        }
    } else {
        // Error al ejecutar la consulta
        echo "Error al verificar si el usuario es un operario: " . mysqli_error($conn);
    }
} else {
    // ID de usuario no disponible
    echo "Error: ID de usuario no disponible.";
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operario - Lista de Solicitudes Pendientes</title>
    <style>
        .solicitudes-list {
            margin-top: 20px;
        }
        .solicitud {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

</body>
</html>
<?php
include 'footer.php';
?>
