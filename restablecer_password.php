<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel='stylesheet' type='text/css' media='screen' href='css/solicitante.css'>

    <style>
        
    </style>
</head>
<body>
    <div class="container-contraseña">
    <div class="container">
        <h2>Restablecer Contraseña</h2>
        <form action="verificar_codigo.php" method="post">
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="nueva_contrasena">Nueva Contraseña:</label>
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
            </div>
            <div class="form-group">
                <button type="submit">Restablecer Contraseña</button>
            </div>
        </form>
    </div>
    </div>
    
</body>
</html>
