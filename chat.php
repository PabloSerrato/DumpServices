<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';
include 'header.php';

// Obtener el ID de solicitud de la URL
$id_solicitud = isset($_GET['id_solicitud']) ? $_GET['id_solicitud'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            justify-content: center;
            height: 100vh;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .chat-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .mensaje {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #e1f5fe;
            border-left: 4px solid #039be5;
        }
        .mensaje:nth-child(even) {
            background-color: #fff9c4;
            border-left-color: #fbc02d;
        }
        .fecha-envio {
            font-size: 0.8em;
            color: #666;
            text-align: right;
            margin-top: 5px;
        }
        form {
            width: 80%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
            font-size: 1em;
            margin-bottom: 10px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #039be5;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0277bd;
        }
    </style>
</head>
<body>
    <h2>Chat</h2>
    <div class="chat-box" id="chat-box">
        <!-- Los mensajes se cargarán aquí -->
    </div>

    <form action="guardar_mensaje.php" method="post">
        <input type="hidden" name="id_solicitud" value="<?php echo $id_solicitud; ?>">
        <textarea name="mensaje" rows="4" cols="50" required></textarea><br>
        <input type="submit" value="Enviar">
    </form>

    <script>
        function fetchMessages() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'obtener_mensajes.php?id_solicitud=<?php echo $id_solicitud; ?>', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('chat-box').innerHTML = xhr.responseText;
                    var chatBox = document.getElementById('chat-box');
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            };
            xhr.send();
        }

        setInterval(fetchMessages, 5000); // Actualizar cada 5 segundos
        window.onload = fetchMessages; // Cargar mensajes al inicio
    </script>
</body>
</html>

<?php
include 'footer.php';
?>
