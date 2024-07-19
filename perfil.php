<?php
session_start();
require 'conexion.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar la información del usuario
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = $_POST['numero_documento'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    $sql = "UPDATE usuarios SET nombre = ?, apellidos = ?, fecha_nacimiento = ?, tipo_documento = ?, numero_documento = ?, correo = ?, telefono = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $nombre, $apellidos, $fecha_nacimiento, $tipo_documento, $numero_documento, $correo, $telefono, $usuario_id);

    if ($stmt->execute()) {
        $mensaje = "Perfil actualizado exitosamente.";
        $usuario['nombre'] = $nombre;
        $usuario['apellidos'] = $apellidos;
        $usuario['fecha_nacimiento'] = $fecha_nacimiento;
        $usuario['tipo_documento'] = $tipo_documento;
        $usuario['numero_documento'] = $numero_documento;
        $usuario['correo'] = $correo;
        $usuario['telefono'] = $telefono;
    } else {
        $mensaje = "Error al actualizar el perfil.";
    }
}

// Generar un avatar aleatorio usando RoboHash
$avatar_url = "https://robohash.org/" . md5(strtolower(trim($usuario['correo']))) . ".png";

include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-header img {
            border-radius: 50%;
            margin-bottom: 20px;
            border: 3px solid #17a2b8;
        }
        .profile-header h2 {
            margin-bottom: 10px;
            color: #17a2b8;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .btn-primary:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="profile-header">
            <img src="<?php echo $avatar_url; ?>" alt="Foto de Perfil" width="150">
            <h2><?php echo isset($usuario['nombre']) ? $usuario['nombre'] : 'Usuario'; ?></h2>
            <p><?php echo isset($usuario['correo']) ? $usuario['correo'] : 'No disponible'; ?></p>
        </div>
        <?php if (isset($mensaje)) : ?>
            <div class="alert alert-info"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="post" action="perfil.php">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($usuario['nombre']) ? $usuario['nombre'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo isset($usuario['apellidos']) ? $usuario['apellidos'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo isset($usuario['fecha_nacimiento']) ? $usuario['fecha_nacimiento'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                    <option value="cc" <?php echo (isset($usuario['tipo_documento']) && $usuario['tipo_documento'] == 'cc') ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                    <option value="ce" <?php echo (isset($usuario['tipo_documento']) && $usuario['tipo_documento'] == 'ce') ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="numero_documento" class="form-label">Número de Documento</label>
                <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?php echo isset($usuario['numero_documento']) ? $usuario['numero_documento'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo isset($usuario['correo']) ? $usuario['correo'] : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo isset($usuario['telefono']) ? $usuario['telefono'] : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
