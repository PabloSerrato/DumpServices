<?php
include 'conexion.php';
include 'header.php';

$user = [];
$operario = [];

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Consulta para obtener los datos del usuario
    $sql = "SELECT * FROM usuarios WHERE id = $userId";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Si el usuario es operador_logistico, obtener datos de operarios
    if ($user['rol'] == 'operador_logistico') {
        $sql_operario = "SELECT * FROM operarios WHERE id_usuario = $userId";
        $result_operario = $conn->query($sql_operario);
        $operario = $result_operario->fetch_assoc();
    }
}

if (isset($_POST['edit_user'])) {
    $userId = $_POST['id'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];
    $telefono = $_POST['telefono'];

    // Actualizar datos del usuario
    $sql = "UPDATE usuarios SET nombre='$nombre', apellidos='$apellidos', fecha_nacimiento='$fecha_nacimiento', tipo_documento='$tipo_documento', numero_documento='$numero_documento', correo='$correo', rol='$rol', telefono='$telefono' WHERE id = $userId";
    if ($conn->query($sql) === TRUE) {
        // Si el usuario es operador_logistico, actualizar datos del operario
        if ($rol == 'operador_logistico') {
            $marca_motocarro = $_POST['marca_motocarro'];
            $modelo_motocarro = $_POST['modelo_motocarro'];
            $ano_motocarro = $_POST['ano_motocarro'];
            $placa_motocarro = $_POST['placa_motocarro'];
            $direccion_domicilio = $_POST['direccion_domicilio'];

            if (isset($operario['id_operario'])) {
                // Actualizar el registro existente
                $sql_operario = "UPDATE operarios SET marca_motocarro='$marca_motocarro', modelo_motocarro='$modelo_motocarro', año_motocarro='$ano_motocarro', placa_motocarro='$placa_motocarro', direccion_domicilio='$direccion_domicilio' WHERE id_operario = " . $operario['id_operario'];
            } else {
                // Crear un nuevo registro
                $sql_operario = "INSERT INTO operarios (id_usuario, marca_motocarro, modelo_motocarro, año_motocarro, placa_motocarro, direccion_domicilio) VALUES ($userId, '$marca_motocarro', '$modelo_motocarro', '$ano_motocarro', '$placa_motocarro', '$direccion_domicilio')";
            }
            if ($conn->query($sql_operario) !== TRUE) {
                echo "Error al actualizar operario: " . $conn->error;
            }
        }
        header("Location: admin_usuarios.php");
    } else {
        echo "Error al actualizar usuario: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #0056b3;
            margin-bottom: 20px;
        }
        .documentacion img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Editar Usuario</h2>
        <form action="edit_usuario.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $user['nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos:</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo $user['apellidos']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo $user['fecha_nacimiento']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="tipo_documento" class="form-label">Tipo de Documento:</label>
                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                    <option value="cc" <?php echo ($user['tipo_documento'] == 'cc') ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                    <option value="ce" <?php echo ($user['tipo_documento'] == 'ce') ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="numero_documento" class="form-label">Número de Documento:</label>
                <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?php echo $user['numero_documento']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo:</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $user['correo']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol:</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="administrador" <?php echo ($user['rol'] == 'administrador') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="operador_logistico" <?php echo ($user['rol'] == 'operador_logistico') ? 'selected' : ''; ?>>Operador Logístico</option>
                    <option value="solicitante" <?php echo ($user['rol'] == 'solicitante') ? 'selected' : ''; ?>>Solicitante</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono:</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $user['telefono']; ?>">
            </div>

            <!-- Mostrar campos adicionales si el rol es operador_logistico -->
            <div id="operario-fields" style="display: <?php echo ($user['rol'] == 'operador_logistico') ? 'block' : 'none'; ?>;">
                <h4>Información del Motocarro</h4>
                <div class="mb-3">
                    <label for="marca_motocarro" class="form-label">Marca del Motocarro:</label>
                    <input type="text" class="form-control" id="marca_motocarro" name="marca_motocarro" value="<?php echo isset($operario['marca_motocarro']) ? $operario['marca_motocarro'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="modelo_motocarro" class="form-label">Modelo del Motocarro:</label>
                    <input type="text" class="form-control" id="modelo_motocarro" name="modelo_motocarro" value="<?php echo isset($operario['modelo_motocarro']) ? $operario['modelo_motocarro'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="ano_motocarro" class="form-label">Año del Motocarro:</label>
                    <input type="number" class="form-control" id="ano_motocarro" name="ano_motocarro" value="<?php echo isset($operario['año_motocarro']) ? $operario['año_motocarro'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="placa_motocarro" class="form-label">Placa del Motocarro:</label>
                    <input type="text" class="form-control" id="placa_motocarro" name="placa_motocarro" value="<?php echo isset($operario['placa_motocarro']) ? $operario['placa_motocarro'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="direccion_domicilio" class="form-label">Dirección de Domicilio:</label>
                    <input type="text" class="form-control" id="direccion_domicilio" name="direccion_domicilio" value="<?php echo isset($operario['direccion_domicilio']) ? $operario['direccion_domicilio'] : ''; ?>">
                </div>

                <!-- Documentación -->
                <h4>Documentación</h4>
                <div class="documentacion">
                    <div class="mb-3">
                        <label for="certificado_antecedentes_judiciales" class="form-label">Certificado de Antecedentes Judiciales:</label>
                        <input type="file" class="form-control" id="certificado_antecedentes_judiciales" name="certificado_antecedentes_judiciales">
                        <?php if (isset($operario['certificado_antecedentes_judiciales'])): ?>
                            <a href="<?php echo $operario['certificado_antecedentes_judiciales']; ?>" target="_blank">Ver documento actual</a>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="certificado_seguridad_social" class="form-label">Certificado de Seguridad Social:</label>
                        <input type="file" class="form-control" id="certificado_seguridad_social" name="certificado_seguridad_social">
                        <?php if (isset($operario['certificado_seguridad_social'])): ?>
                            <a href="<?php echo $operario['certificado_seguridad_social']; ?>" target="_blank">Ver documento actual</a>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="licencia_conduccion" class="form-label">Licencia de Conducción:</label>
                        <input type="file" class="form-control" id="licencia_conduccion" name="licencia_conduccion">
                        <?php if (isset($operario['licencia_conduccion'])): ?>
                            <a href="<?php echo $operario['licencia_conduccion']; ?>" target="_blank">Ver documento actual</a>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="seguro_vehiculo" class="form-label">Seguro del Vehículo:</label>
                        <input type="file" class="form-control" id="seguro_vehiculo" name="seguro_vehiculo">
                        <?php if (isset($operario['seguro_vehiculo'])): ?>
                            <a href="<?php echo $operario['seguro_vehiculo']; ?>" target="_blank">Ver documento actual</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <button type="submit" name="edit_user" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('rol').addEventListener('change', function() {
            var operarioFields = document.getElementById('operario-fields');
            if (this.value == 'operador_logistico') {
                operarioFields.style.display = 'block';
            } else {
                operarioFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
