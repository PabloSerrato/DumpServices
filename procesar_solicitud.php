<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Obtener los datos del formulario
$id_operario = $_POST['id_operario'];
$direccion_acarreo = $_POST['direccion_acarreo'];
$detalles_acarreo = $_POST['detalles_acarreo'];

// Verificar si el ID del solicitante de transporte está disponible en la sesión o en la cookie
if (isset($_SESSION['usuario_id'])) {
    $id_solicitante = $_SESSION['usuario_id'];
} elseif (isset($_COOKIE['usuario_id'])) {
    $id_solicitante = $_COOKIE['usuario_id'];
} else {
    // Si el ID del solicitante de transporte no está disponible, mostrar un mensaje de error
    die("Error: ID de solicitante de transporte no disponible.");
}

// Definir el valor predeterminado para el estado
$estado_predeterminado = 'Espera';

// Aquí puedes agregar lógica adicional, como guardar la solicitud en la base de datos
$sql_solicitud = "INSERT INTO solicitudes (id_operario, id_solicitante, direccion_acarreo, detalles_acarreo, estado) 
                    VALUES ('$id_operario', '$id_solicitante', '$direccion_acarreo', '$detalles_acarreo', '$estado_predeterminado')";

if (mysqli_query($conn, $sql_solicitud)) {
    // Si la solicitud se envía correctamente, redirigir a solicitante.php
    header("Location: solicitante.php");
    exit(); // Asegúrate de detener la ejecución del script después de la redirección
} else {
    echo "Error al enviar la solicitud de acarreo: " . mysqli_error($conn);
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
