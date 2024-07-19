<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';
include 'header.php';

// Obtener el ID del operario desde la URL
$id_operario = $_GET['id_operario'];

// Verificar si la cookie del usuario está configurada
if (isset($_COOKIE['usuario_id'])) {
    // Obtener el ID del solicitante de transporte de la cookie
    $id_solicitante = $_COOKIE['usuario_id'];
} else {
    // Si la cookie no está configurada, redirigir al usuario a la página de inicio de sesión
    header("Location: iniciar_sesion.php");
    exit();
}

// Consulta para obtener los detalles del operario
$sql_operario = "SELECT 
    usuarios.nombre, 
    usuarios.apellidos, 
    usuarios.correo, 
    usuarios.telefono, 
    operarios.direccion_domicilio, 
    operarios.marca_motocarro, 
    operarios.modelo_motocarro, 
    operarios.año_motocarro, 
    operarios.placa_motocarro, 
    operarios.foto_motocarro, 
    operarios.foto_2,
    operarios.foto_3,
    operarios.foto_4,
    operarios.foto_5,
    operarios.foto_6,
    operarios.foto_7,
    operarios.foto_8,
    operarios.foto_9,
    operarios.foto_10,
    operarios.otros_detalles
FROM operarios 
JOIN usuarios ON operarios.id_usuario = usuarios.id 
WHERE operarios.id_operario = $id_operario";

$resultado_operario = mysqli_query($conn, $sql_operario);

if ($resultado_operario) {
    if (mysqli_num_rows($resultado_operario) > 0) {
        $operario = mysqli_fetch_assoc($resultado_operario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' type='text/css' media='screen' href='css/solicitante.css'>

    <title>Solicitud de Acarreo</title>

</head>
<body>
<div class="container">
    <div class="details">
        <h2>Detalles del Operario</h2>
        <table class="details-table">
            <tr>
                <th>Nombre:</th>
                <td><?php echo $operario['nombre'] . " " . $operario['apellidos']; ?></td>
            </tr>
            <tr>
                <th>Correo electrónico:</th>
                <td><?php echo $operario['correo']; ?></td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td><?php echo $operario['telefono']; ?></td>
            </tr>
            <tr>
                <th>Dirección:</th>
                <td><?php echo $operario['direccion_domicilio']; ?></td>
            </tr>
            <tr>
                <th>Marca del Motocarro:</th>
                <td><?php echo $operario['marca_motocarro']; ?></td>
            </tr>
            <tr>
                <th>Modelo del Motocarro:</th>
                <td><?php echo $operario['modelo_motocarro']; ?></td>
            </tr>
            <tr>
                <th>Año del Motocarro:</th>
                <td><?php echo $operario['año_motocarro']; ?></td>
            </tr>
            <tr>
                <th>Placa del Motocarro:</th>
                <td><?php echo $operario['placa_motocarro']; ?></td>
            </tr>
            <tr>
                <th>Otros detalles:</th>
                <td><?php echo nl2br($operario['otros_detalles']); ?></td>
            </tr>
        </table>
        <img src="<?php echo $operario['foto_motocarro']; ?>" alt="Foto del Motocarro">

        <?php if ($operario['foto_2'] || $operario['foto_3'] || $operario['foto_4'] || $operario['foto_5'] || $operario['foto_6'] || $operario['foto_7'] || $operario['foto_8'] || $operario['foto_9'] || $operario['foto_10']) { ?>
        <h3>Fotos Adicionales</h3>
        <div class="additional-photos">
            <?php
            for ($i = 2; $i <= 10; $i++) {
                $foto = $operario['foto_' . $i];
                if ($foto) {
                    echo "<img src='" . $foto . "' alt='Foto adicional $i'>";
                }
            }
            ?>
        </div>
        <?php } ?>
    </div>

    <div class="form-container">
        <h2>Solicitud de Acarreo</h2>
        <form action="procesar_solicitud.php" method="post">
            <input type="hidden" name="id_operario" value="<?php echo $id_operario; ?>">
            <input type="hidden" name="id_solicitante" value="<?php echo $id_solicitante; ?>">
            <label for="direccion_acarreo">Dirección del Acarreo:</label>
            <input type="text" id="direccion_acarreo" name="direccion_acarreo" required>
            <label for="detalles_acarreo">Detalles del Acarreo:</label>
            <textarea id="detalles_acarreo" name="detalles_acarreo" required></textarea>
            <button type="submit">Solicitar Acarreo</button>
        </form>
    </div>
</div>
</body>
</html>
<?php
    } else {
        echo "No se encontraron detalles para este operario.";
    }
} else {
    echo "Error al obtener los detalles del operario: " . mysqli_error($conn);
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>
