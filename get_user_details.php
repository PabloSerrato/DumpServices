<?php
include 'conexion.php';

if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    $sql = "SELECT id, nombre, correo, telefono, contrasena, rol FROM usuarios WHERE id = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
}
?>
