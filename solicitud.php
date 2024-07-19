<?php
include 'conexion.php';
include 'header.php';

// Verificar si una sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Función para ejecutar una consulta segura
function ejecutarConsultaSegura($conn, $query, $params, $types) {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    return $stmt;
}

// Función para cancelar la solicitud
if (isset($_POST['cancelar'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $query = "UPDATE solicitudes SET estado = 'Cancelado' WHERE id = ?";
    $stmt = ejecutarConsultaSegura($conn, $query, [$id_solicitud], 'i');
    if ($stmt) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                Swal.fire({
                    title: "Solicitud cancelada correctamente.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(function() {
                    window.location.href = window.location.href;
                });
              </script>';
    } else {
        echo "Error al cancelar la solicitud: " . mysqli_error($conn);
    }
}

// Función para marcar como entregado
if (isset($_POST['entregado'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $query = "UPDATE solicitudes SET estado = 'Entregado' WHERE id = ?";
    $stmt = ejecutarConsultaSegura($conn, $query, [$id_solicitud], 'i');
    if ($stmt) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                Swal.fire({
                    title: "Solicitud marcada como entregada correctamente.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(function() {
                    window.location.href = window.location.href;
                });
              </script>';
    } else {
        echo "Error al marcar la solicitud como entregada: " . mysqli_error($conn);
    }
}

// Función para guardar la calificación
if (isset($_POST['submit_nueva_calificacion'])) {
    $id_solicitud = $_POST['id_solicitud'];
    $calificacion = $_POST['nueva_calificacion'];

    // Obtener el ID del operario asociado a la solicitud
    $query = "SELECT id_operario FROM solicitudes WHERE id = ?";
    $stmt = ejecutarConsultaSegura($conn, $query, [$id_solicitud], 'i');
    $resultado = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($resultado);
    $id_operario = $row['id_operario'];

    // Comprobar si ya existe una calificación para esta solicitud y operario
    $query = "SELECT * FROM calificaciones WHERE id_operario = ? AND id_solicitud = ?";
    $stmt = ejecutarConsultaSegura($conn, $query, [$id_operario, $id_solicitud], 'ii');
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        // Actualizar la calificación existente
        $query = "UPDATE calificaciones SET calificacion = ? WHERE id_operario = ? AND id_solicitud = ?";
        $stmt = ejecutarConsultaSegura($conn, $query, [$calificacion, $id_operario, $id_solicitud], 'iii');
        if ($stmt) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    Swal.fire({
                        title: "Calificación actualizada correctamente.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(function() {
                        window.location.href = window.location.href;
                    });
                  </script>';
        } else {
            echo "Error al actualizar la calificación: " . mysqli_error($conn);
        }
    } else {
        // Insertar una nueva calificación
        $query = "INSERT INTO calificaciones (id_operario, id_solicitud, calificacion) VALUES (?, ?, ?)";
        $stmt = ejecutarConsultaSegura($conn, $query, [$id_operario, $id_solicitud, $calificacion], 'iii');
        if ($stmt) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>
                    Swal.fire({
                        title: "¡Gracias por tu calificación!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(function() {
                        window.location.href = window.location.href;
                    });
                  </script>';
        } else {
            echo "Error al guardar la calificación: " . mysqli_error($conn);
        }
    }

    // Obtener el nuevo promedio de calificaciones para el operario
    $query = "SELECT AVG(calificacion) as promedio_calificacion FROM calificaciones WHERE id_operario = ?";
    $stmt = ejecutarConsultaSegura($conn, $query, [$id_operario], 'i');
    $resultado = mysqli_stmt_get_result($stmt);
    $promedio_calificacion = mysqli_fetch_assoc($resultado)['promedio_calificacion'];

    // Actualizar el promedio de calificaciones en la tabla de operarios
    $query = "UPDATE operarios SET calificacion = ? WHERE id_operario = ?";
    $stmt = ejecutarConsultaSegura($conn, $query, [$promedio_calificacion, $id_operario], 'di');
    if ($stmt) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script>
                Swal.fire({
                    title: "Promedio de calificación actualizado correctamente.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then(function() {
                    window.location.href = window.location.href;
                });
            </script>';
    } else {
        echo "Error al actualizar el promedio de calificación: " . mysqli_error($conn);
    }
}

// Consulta para obtener la información relevante de todas las solicitudes del solicitante actual
$id_solicitante = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : (isset($_COOKIE['usuario_id']) ? $_COOKIE['usuario_id'] : null);

if ($id_solicitante) {
    // Consulta modificada para ordenar las solicitudes por estado
    $query = "SELECT * FROM solicitudes WHERE id_solicitante = ? ORDER BY FIELD(estado, 'Espera', 'Aceptado', 'Entregado', 'Cancelado')";
    $stmt = ejecutarConsultaSegura($conn, $query, [$id_solicitante], 'i');
    $resultado_solicitudes = mysqli_stmt_get_result($stmt);

    if ($resultado_solicitudes) {
        // Verificar si hay datos
        if (mysqli_num_rows($resultado_solicitudes) > 0) {
            echo "<h2>Lista de Solicitudes</h2>";
            echo "<div class='solicitudes-list'>";
            
            // Recorrer y mostrar los datos de cada solicitud
            while ($solicitud = mysqli_fetch_assoc($resultado_solicitudes)) {
                $estado_clase = strtolower($solicitud['estado']); // Convertir el estado a minúsculas para la clase CSS
                echo "<div class='solicitud $estado_clase'>";
                echo "<p><strong><i class='fas fa-map-marker-alt'></i> Dirección de acarreo:</strong> " . $solicitud['direccion_acarreo'] . "</p>";
                echo "<p><strong><i class='fas fa-info-circle'></i> Detalles de acarreo:</strong> " . $solicitud['detalles_acarreo'] . "</p>";
                echo "<p><strong><i class='fas fa-flag'></i> Estado:</strong> " . $solicitud['estado'] . "</p>";
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='id_solicitud' value='" . $solicitud['id'] . "'>";
                if ($solicitud['estado'] == 'Espera') {
                    echo "<button type='submit' name='cancelar' class='btn cancelar'><i class='fas fa-times-circle'></i> Cancelar</button>";
                } elseif ($solicitud['estado'] == 'Aceptado') {
                    echo "<button type='submit' name='entregado' class='btn entregado'><i class='fas fa-check-circle'></i> Pedido Recibido</button>";
                }

                // Aquí se mostrará el formulario de calificación si el estado es "Entregado"
                if ($solicitud['estado'] == 'Entregado') {
                    // Obtener la calificación actual del operario para la solicitud específica
                    $id_operario = $solicitud['id_operario'];
                    $query = "SELECT calificacion FROM calificaciones WHERE id_operario = ? AND id_solicitud = ?";
                    $stmt = ejecutarConsultaSegura($conn, $query, [$id_operario, $solicitud['id']], 'ii');
                    $resultado = mysqli_stmt_get_result($stmt);
                    $calificacion_actual = mysqli_fetch_assoc($resultado)['calificacion'] ?? null;

                    echo "<div>";
                    echo "<label for='nueva_calificacion'><i class='fas fa-star'></i> Calificación (1-5):</label>";
                    for ($i = 1; $i <= 5; $i++) {
                        $checked = ($calificacion_actual == $i) ? "checked" : "";
                        echo "<input type='radio' name='nueva_calificacion' value='$i' $checked>$i";
                    }
                    echo "</div>";
                    echo "<button type='submit' name='submit_nueva_calificacion' class='btn calificar'><i class='fas fa-paper-plane'></i> Calificar</button>";
                }

                echo "</form>";

                // Mostrar el enlace del chat solo si la solicitud no está en espera
                if ($solicitud['estado'] != 'Espera') {
                    echo "<a href='chat.php?id_solicitud=" . $solicitud['id'] . "&id_operario=" . $solicitud['id_operario'] . "' class='btn chatear'><i class='fas fa-comments'></i> Chatear con el operador</a>";
                }
                echo "</div><hr>";
            }
            
            echo "</div>";
        } else {
            // No se encontraron solicitudes
            echo "No se encontraron solicitudes.";
        }
    } else {
        // Error al ejecutar la consulta
        echo "Error al obtener la lista de solicitudes: " . mysqli_error($conn);
    }
} else {
    // ID de solicitante de transporte no disponible
    echo "Error: ID de solicitante de transporte no disponible.";
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' media='screen' href='css/solicitante.css'>
    <title>Solicitante de Transporte - Lista de Solicitudes</title>
</head>
<body>
    <?php include 'footer.php'; ?>
</body>
</html>