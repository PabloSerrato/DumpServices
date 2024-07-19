<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $id_usuario = $_COOKIE['usuario_id']; // Obtener el id_usuario de la cookie

    $marca_motocarro = $_POST['marca'];
    $modelo_motocarro = $_POST['modelo'];
    $año_motocarro = $_POST['año'];
    $placa_motocarro = $_POST['placa'];
    $direccion_domicilio = $_POST['direccion'];
    $otros_detalles = $_POST['detalles'];

    // Manejar la carga de la foto del motocarro
    $ruta_foto_motocarro = '';
    if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombre_temporal = $_FILES['foto']['tmp_name'];
        $nombre_archivo = $_FILES['foto']['name'];
        $ruta_destino = 'media/fotos/' . $nombre_archivo;
        move_uploaded_file($nombre_temporal, $ruta_destino);
        $ruta_foto_motocarro = $ruta_destino;
    }

    // Manejar la carga de las fotos adicionales
    $ruta_fotos_adicionales = array();
    for ($i = 1; $i <= 10; $i++) {
        if (isset($_FILES["foto_$i"]) && $_FILES["foto_$i"]["error"] === UPLOAD_ERR_OK) {
            $nombre_temporal = $_FILES["foto_$i"]["tmp_name"];
            $nombre_archivo = $_FILES["foto_$i"]["name"];
            $ruta_destino = 'media/fotos/' . $nombre_archivo;
            move_uploaded_file($nombre_temporal, $ruta_destino);
            $ruta_fotos_adicionales[$i] = $ruta_destino;
        } else {
            $ruta_fotos_adicionales[$i] = null; // Si no hay foto adicional, poner null
        }
    }

    // Manejar la carga de los certificados
    $ruta_certificado_antecedentes = '';
    if ($_FILES['certificado_antecedentes']['error'] === UPLOAD_ERR_OK) {
        $nombre_temporal = $_FILES['certificado_antecedentes']['tmp_name'];
        $nombre_archivo = $_FILES['certificado_antecedentes']['name'];
        $ruta_destino = 'media/certificados/antecedentes/' . $nombre_archivo;
        move_uploaded_file($nombre_temporal, $ruta_destino);
        $ruta_certificado_antecedentes = $ruta_destino;
    }

    $ruta_certificado_seguridad = '';
    if ($_FILES['certificado_seguridad']['error'] === UPLOAD_ERR_OK) {
        $nombre_temporal = $_FILES['certificado_seguridad']['tmp_name'];
        $nombre_archivo = $_FILES['certificado_seguridad']['name'];
        $ruta_destino = 'media/certificados/seguridad/' . $nombre_archivo;
        move_uploaded_file($nombre_temporal, $ruta_destino);
        $ruta_certificado_seguridad = $ruta_destino;
    }

    $ruta_licencia_conduccion = '';
    if ($_FILES['licencia_conduccion']['error'] === UPLOAD_ERR_OK) {
        $nombre_temporal = $_FILES['licencia_conduccion']['tmp_name'];
        $nombre_archivo = $_FILES['licencia_conduccion']['name'];
        $ruta_destino = 'media/certificados/licencia/' . $nombre_archivo;
        move_uploaded_file($nombre_temporal, $ruta_destino);
        $ruta_licencia_conduccion = $ruta_destino;
    }

    $ruta_seguro_vehiculo = '';
    if ($_FILES['seguro_vehiculo']['error'] === UPLOAD_ERR_OK) {
        $nombre_temporal = $_FILES['seguro_vehiculo']['tmp_name'];
        $nombre_archivo = $_FILES['seguro_vehiculo']['name'];
        $ruta_destino = 'media/certificados/seguro/' . $nombre_archivo;
        move_uploaded_file($nombre_temporal, $ruta_destino);
        $ruta_seguro_vehiculo = $ruta_destino;
    }

    $sql = "INSERT INTO operarios (id_usuario, marca_motocarro, modelo_motocarro, año_motocarro, placa_motocarro, foto_motocarro, direccion_domicilio, otros_detalles, certificado_antecedentes_judiciales, certificado_seguridad_social, licencia_conduccion, seguro_vehiculo";
    for ($i = 2; $i <= 10; $i++) {
        $sql .= ", foto_$i"; // Utilizar las columnas foto_2, foto_3, ..., foto_10
    }
    $sql .= ") VALUES ('$id_usuario', '$marca_motocarro', '$modelo_motocarro', '$año_motocarro', '$placa_motocarro', '$ruta_foto_motocarro', '$direccion_domicilio', '$otros_detalles', '$ruta_certificado_antecedentes', '$ruta_certificado_seguridad', '$ruta_licencia_conduccion', '$ruta_seguro_vehiculo'";
    for ($i = 2; $i <= 10; $i++) {
        $sql .= ", " . ($ruta_fotos_adicionales[$i] ? "'" . $ruta_fotos_adicionales[$i] . "'" : "NULL");
    }
    $sql .= ")";

    if (mysqli_query($conn, $sql)) {
        // Redireccionar a la misma página para evitar reenvío del formulario
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error al registrar operario: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro de Operario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
        }

        .d-flex-center {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .btn-primary, .btn-success {
            width: 100%;
            margin-top: 20px;
        }

        .btn-next, .btn-prev {
            margin-top: 20px;
            width: 48%;
        }

        .btn-prev {
            background-color: #6c757d;
        }

        .progress {
            height: 25px;
        }
    </style>
</head>
<body>
    <div class="d-flex-center">
        <div class="container">
            <h2>Registro de Operario</h2>
            <div class="progress mb-4">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
            </div>
            <form id="registroForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <!-- Input oculto para el id_usuario -->
                <input type="hidden" name="id_usuario" value="<?php echo $_COOKIE['usuario_id']; ?>">

                <!-- Sección 1 -->
                <div class="form-section">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="marca" class="form-label">Marca del Motocarro</label>
                            <input type="text" class="form-control" id="marca" name="marca" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modelo" class="form-label">Modelo del Motocarro</label>
                            <input type="text" class="form-control" id="modelo" name="modelo" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="año" class="form-label">Año de Fabricación</label>
                            <input type="number" class="form-control" id="año" name="año" required>
                        </div>
                        <div class="col-md-6">
                            <label for="placa" class="form-label">Placa del Motocarro</label>
                            <input type="text" class="form-control" id="placa" name="placa" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-primary btn-next" onclick="showNextSection()">Siguiente</button>
                    </div>
                </div>

                <!-- Sección 2 -->
                <div class="form-section d-none">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto del Motocarro</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección del Domicilio</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-prev" onclick="showPrevSection()">Atrás</button>
                        <button type="button" class="btn btn-primary btn-next" onclick="showNextSection()">Siguiente</button>
                    </div>
                </div>

                <!-- Sección 3 -->
                <div class="form-section d-none">
                    <div class="mb-3">
                        <label for="certificado_antecedentes" class="form-label">Certificado de Antecedentes Judiciales</label>
                        <input type="file" class="form-control" id="certificado_antecedentes" name="certificado_antecedentes" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="certificado_seguridad" class="form-label">Certificado de Seguridad Social</label>
                        <input type="file" class="form-control" id="certificado_seguridad" name="certificado_seguridad" accept="image/*">
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-prev" onclick="showPrevSection()">Atrás</button>
                        <button type="button" class="btn btn-primary btn-next" onclick="showNextSection()">Siguiente</button>
                    </div>
                </div>

                <!-- Sección 4 -->
                <div class="form-section d-none">
                    <div class="mb-3">
                        <label for="licencia_conduccion" class="form-label">Licencia de Conducción</label>
                        <input type="file" class="form-control" id="licencia_conduccion" name="licencia_conduccion" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="seguro_vehiculo" class="form-label">Seguro del Vehículo</label>
                        <input type="file" class="form-control" id="seguro_vehiculo" name="seguro_vehiculo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="detalles" class="form-label">Otros Detalles</label>
                        <textarea class="form-control" id="detalles" name="detalles" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fotos Adicionales</label>
                        <div class="row">
                            <?php for ($i = 1; $i <= 10; $i++) : ?>
                                <div class="col-md-4 mb-3">
                                    <label for="foto_<?php echo $i; ?>" class="form-label">Foto adicional <?php echo $i; ?></label>
                                    <input type="file" class="form-control" id="foto_<?php echo $i; ?>" name="foto_<?php echo $i; ?>" accept="image/*">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-prev" onclick="showPrevSection()">Atrás</button>
                        <button type="submit" class="btn btn-success">Registrar Operario</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        let currentSection = 0;
        const sections = document.querySelectorAll('.form-section');
        const progressBar = document.getElementById('progressBar');

        function showNextSection() {
            sections[currentSection].classList.add('d-none');
            currentSection++;
            sections[currentSection].classList.remove('d-none');
            updateProgressBar();
        }

        function showPrevSection() {
            sections[currentSection].classList.add('d-none');
            currentSection--;
            sections[currentSection].classList.remove('d-none');
            updateProgressBar();
        }

        function updateProgressBar() {
            const progress = ((currentSection + 1) / sections.length) * 100;
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
            progressBar.innerHTML = `${progress}%`;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
