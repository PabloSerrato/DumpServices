<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Obtener el ID de solicitud de la URL
$id_solicitud = isset($_GET['id_solicitud']) ? $_GET['id_solicitud'] : null;
$id_usuario_actual = 1; // Asume que el ID del usuario actual es 1. Ajusta según tu lógica.

if ($id_solicitud) {
    // Consulta para obtener los mensajes de la solicitud actual
    $sql_mensajes = "SELECT mensajes.*, usuarios.nombre AS nombre_usuario 
                     FROM mensajes 
                     JOIN usuarios ON mensajes.id_usuario = usuarios.id 
                     WHERE mensajes.id_solicitud = $id_solicitud 
                     ORDER BY mensajes.fecha_envio ASC";
    $resultado_mensajes = mysqli_query($conn, $sql_mensajes);

    if ($resultado_mensajes) {
        // Recorrer y mostrar los datos de cada mensaje
        while ($mensaje = mysqli_fetch_assoc($resultado_mensajes)) {
            $clase = ($mensaje['id_usuario'] == $id_usuario_actual) ? 'enviado' : 'recibido';
            echo "<div class='mensaje $clase'>";
            echo "<p><strong>" . $mensaje['nombre_usuario'] . ":</strong> " . $mensaje['mensaje'] . "</p>";
            echo "<span class='fecha-envio'>" . $mensaje['fecha_envio'] . "</span>";
            echo "</div>";
        }
    } else {
        // Error al ejecutar la consulta
        echo "Error al obtener los mensajes: " . mysqli_error($conn);
    }
} else {
    // ID de solicitud no disponible
    echo "Error: ID de solicitud no disponible.";
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
