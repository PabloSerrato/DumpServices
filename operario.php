<?php
// Incluir el archivo de conexión a la base de datos
include 'conexion.php';

// Función para obtener un avatar aleatorio
function obtenerAvatarAleatorio() {
    $avatares = [
        'https://i.pravatar.cc/150?img=1',
        'https://i.pravatar.cc/150?img=2',
        'https://i.pravatar.cc/150?img=3',
        'https://i.pravatar.cc/150?img=4',
        'https://i.pravatar.cc/150?img=5'
    ];
    return $avatares[array_rand($avatares)];
}

// Obtener el ID de usuario de la cookie
$id_usuario = isset($_COOKIE['usuario_id']) ? $_COOKIE['usuario_id'] : null;

if ($id_usuario) {
    // Consulta para verificar si el usuario ya ha llenado su perfil de operario
    $sql_verificar_perfil = "SELECT COUNT(*) AS total FROM operarios WHERE id_usuario = $id_usuario";
    $resultado_verificar_perfil = mysqli_query($conn, $sql_verificar_perfil);

    if ($resultado_verificar_perfil) {
        $fila_verificar_perfil = mysqli_fetch_assoc($resultado_verificar_perfil);
        $total_perfiles = $fila_verificar_perfil['total'];

        // Verificar si el usuario tiene un perfil de operario
        if ($total_perfiles > 0) {
            // El usuario tiene un perfil de operario, continuar con la consulta del perfil
            $sql_perfil = "SELECT usuarios.*, operarios.*, usuarios.correo AS email
                FROM usuarios 
                LEFT JOIN operarios ON usuarios.id = operarios.id_usuario 
                WHERE usuarios.id = $id_usuario";

            $resultado_perfil = mysqli_query($conn, $sql_perfil);

            if ($resultado_perfil) {
                // Verificar si hay datos
                if (mysqli_num_rows($resultado_perfil) > 0) {
                    $perfil = mysqli_fetch_assoc($resultado_perfil);

                    // Incluir el archivo de encabezado con el botón de "Tus Solicitudes"
                    include 'header.php';

                    // Mostrar los datos del perfil de usuario
                    echo '<style>
                        .profile-section {
                            font-family: Arial, sans-serif;
                            margin: 20px auto;
                            max-width: 900px;
                            padding: 30px;
                            background-color: #fff;
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                            border-radius: 10px;
                        }
                        .profile-section h2 {
                            color: #007bff;
                            border-bottom: 2px solid #007bff;
                            padding-bottom: 10px;
                            margin-bottom: 20px;
                        }
                        .profile-section p {
                            font-size: 1em;
                            margin: 10px 0;
                            line-height: 1.6;
                        }
                        .profile-section a, .profile-section button {
                            display: inline-block;
                            padding: 10px 20px;
                            background-color: #007bff;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 5px;
                            margin-top: 20px;
                            transition: background-color 0.3s ease;
                        }
                        .profile-section a:hover, .profile-section button:hover {
                            background-color: #0056b3;
                        }
                        .avatar {
                            width: 150px;
                            height: 150px;
                            border-radius: 50%;
                            margin-bottom: 20px;
                            object-fit: cover;
                        }
                        .avatar-container {
                            text-align: center;
                        }
                        .form-section {
                            display: none;
                        }
                        .document-section, .photo-section {
                            display: flex;
                            flex-wrap: wrap;
                            gap: 15px;
                        }
                        .document, .photo {
                            width: calc(33.333% - 10px);
                            background-color: #f8f9fa;
                            padding: 10px;
                            border: 1px solid #ddd;
                            border-radius: 5px;
                            text-align: center;
                            cursor: pointer;
                            transition: transform 0.3s ease;
                        }
                        .document img, .photo img {
                            max-width: 100%;
                            height: auto;
                            border-radius: 5px;
                        }
                        .document:hover, .photo:hover {
                            transform: scale(1.05);
                        }
                        .form-control {
                            margin-bottom: 10px;
                        }
                        .form-label {
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                    </style>';

                    // Generar y guardar avatar aleatorio si no existe
                    if (empty($perfil['avatar'])) {
                        $avatar = obtenerAvatarAleatorio();
                        $sql_guardar_avatar = "UPDATE usuarios SET avatar = '$avatar' WHERE id = $id_usuario";
                        mysqli_query($conn, $sql_guardar_avatar);
                        $perfil['avatar'] = $avatar;
                    }

                    echo "<div class='profile-section'>";
                    echo "<div class='avatar-container'><img src='" . $perfil['avatar'] . "' class='avatar' alt='Avatar'></div>";
                    echo "<h2>Perfil de Usuario</h2>";
                    echo "<p>Nombre: <span id='nombre_display'>" . $perfil['nombre'] . "</span></p>";
                    echo "<p>Correo electrónico: <span id='email_display'>" . $perfil['email'] . "</span></p>";
                    echo "<p>Teléfono: <span id='telefono_display'>" . $perfil['telefono'] . "</span></p>";

                    if (!empty($perfil['id_operario'])) {
                        echo "<h2>Perfil de Operario</h2>";
                        echo "<p>Marca del Motocarro: <span id='marca_display'>" . $perfil['marca_motocarro'] . "</span></p>";
                        echo "<p>Modelo del Motocarro: <span id='modelo_display'>" . $perfil['modelo_motocarro'] . "</span></p>";
                        echo "<p>Año del Motocarro: <span id='año_display'>" . $perfil['año_motocarro'] . "</span></p>";
                        echo "<p>Placa del Motocarro: <span id='placa_display'>" . $perfil['placa_motocarro'] . "</span></p>";

                        // Mostrar fotos adicionales
                        echo "<h2>Fotos Adicionales</h2>";
                        echo "<div class='photo-section'>";
                        for ($i = 1; $i <= 10; $i++) {
                            if (isset($perfil["foto_$i"]) && !empty($perfil["foto_$i"])) {
                                $foto = $perfil["foto_$i"];
                                echo "<div class='photo' data-bs-toggle='modal' data-bs-target='#imageModal' data-img-src='$foto'><img src='$foto' alt='Foto adicional $i'></div>";
                            }
                        }
                        echo "</div>";

                        // Mostrar documentos
                        echo "<h2>Documentos</h2>";
                        echo "<div class='document-section'>";
                        if ($perfil['certificado_antecedentes_judiciales']) {
                            echo "<div class='document' data-bs-toggle='modal' data-bs-target='#imageModal' data-img-src='" . $perfil['certificado_antecedentes_judiciales'] . "'><p>Certificado de Antecedentes Judiciales</p><img src='" . $perfil['certificado_antecedentes_judiciales'] . "' alt='Certificado de Antecedentes Judiciales'></div>";
                        }
                        if ($perfil['certificado_seguridad_social']) {
                            echo "<div class='document' data-bs-toggle='modal' data-bs-target='#imageModal' data-img-src='" . $perfil['certificado_seguridad_social'] . "'><p>Certificado de Seguridad Social</p><img src='" . $perfil['certificado_seguridad_social'] . "' alt='Certificado de Seguridad Social'></div>";
                        }
                        if ($perfil['licencia_conduccion']) {
                            echo "<div class='document' data-bs-toggle='modal' data-bs-target='#imageModal' data-img-src='" . $perfil['licencia_conduccion'] . "'><p>Licencia de Conducción</p><img src='" . $perfil['licencia_conduccion'] . "' alt='Licencia de Conducción'></div>";
                        }
                        if ($perfil['seguro_vehiculo']) {
                            echo "<div class='document' data-bs-toggle='modal' data-bs-target='#imageModal' data-img-src='" . $perfil['seguro_vehiculo'] . "'><p>Seguro del Vehículo</p><img src='" . $perfil['seguro_vehiculo'] . "' alt='Seguro del Vehículo'></div>";
                        }
                        echo "</div>";
                    }

                    echo "<a href='solicitud_operario.php'>Tus Solicitudes</a>";
                    echo "<button class='btn btn-secondary' onclick='mostrarEdicion()'>Editar Perfil</button>";
                    echo "</div>";

                    // Formulario de edición
                    echo "<div class='profile-section form-section' id='form_section'>";
                    echo "<form action='editar_perfil.php' method='POST' enctype='multipart/form-data'>";
                    echo "<div class='mb-3'><label for='nombre' class='form-label'>Nombre</label><input type='text' class='form-control' id='nombre' name='nombre' value='" . $perfil['nombre'] . "' required></div>";
                    echo "<div class='mb-3'><label for='email' class='form-label'>Correo electrónico</label><input type='email' class='form-control' id='email' name='email' value='" . $perfil['email'] . "' required></div>";
                    echo "<div class='mb-3'><label for='telefono' class='form-label'>Teléfono</label><input type='text' class='form-control' id='telefono' name='telefono' value='" . $perfil['telefono'] . "' required></div>";

                    if (!empty($perfil['id_operario'])) {
                        echo "<div class='mb-3'><label for='marca' class='form-label'>Marca del Motocarro</label><input type='text' class='form-control' id='marca' name='marca' value='" . $perfil['marca_motocarro'] . "'></div>";
                        echo "<div class='mb-3'><label for='modelo' class='form-label'>Modelo del Motocarro</label><input type='text' class='form-control' id='modelo' name='modelo' value='" . $perfil['modelo_motocarro'] . "'></div>";
                        echo "<div class='mb-3'><label for='año' class='form-label'>Año del Motocarro</label><input type='number' class='form-control' id='año' name='año' value='" . $perfil['año_motocarro'] . "'></div>";
                        echo "<div class='mb-3'><label for='placa' class='form-label'>Placa del Motocarro</label><input type='text' class='form-control' id='placa' name='placa' value='" . $perfil['placa_motocarro'] . "'></div>";

                        // Campos para subir fotos adicionales
                        echo "<h2>Fotos Adicionales</h2>";
                        echo "<div class='photo-section'>";
                        for ($i = 1; $i <= 10; $i++) {
                            echo "<div class='mb-3'><label for='foto_$i' class='form-label'>Foto adicional $i</label><input type='file' class='form-control' id='foto_$i' name='foto_$i' accept='image/*'></div>";
                        }
                        echo "</div>";

                        // Campos para subir documentos
                        echo "<h2>Documentos</h2>";
                        echo "<div class='document-section'>";
                        echo "<div class='mb-3'><label for='certificado_antecedentes' class='form-label'>Certificado de Antecedentes Judiciales</label><input type='file' class='form-control' id='certificado_antecedentes' name='certificado_antecedentes' accept='image/*'></div>";
                        echo "<div class='mb-3'><label for='certificado_seguridad' class='form-label'>Certificado de Seguridad Social</label><input type='file' class='form-control' id='certificado_seguridad' name='certificado_seguridad' accept='image/*'></div>";
                        echo "<div class='mb-3'><label for='licencia_conduccion' class='form-label'>Licencia de Conducción</label><input type='file' class='form-control' id='licencia_conduccion' name='licencia_conduccion' accept='image/*'></div>";
                        echo "<div class='mb-3'><label for='seguro_vehiculo' class='form-label'>Seguro del Vehículo</label><input type='file' class='form-control' id='seguro_vehiculo' name='seguro_vehiculo' accept='image/*'></div>";
                        echo "</div>";
                    }

                    echo "<button type='submit' class='btn btn-primary'>Guardar Cambios</button>";
                    echo "<button type='button' class='btn btn-secondary' onclick='mostrarVisualizacion()'>Cancelar</button>";
                    echo "</form>";
                    echo "</div>";

                    // Modal para visualizar imágenes y documentos
                    echo '<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imageModalLabel">Visualización</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img id="modalImage" src="" alt="Imagen" class="img-fluid rounded">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>';
                } else {
                    // No se encontraron registros para este usuario
                    echo "No se encontraron registros para este usuario.";
                }
            } else {
                // Error al ejecutar la consulta del perfil
                echo "Error al obtener el perfil del usuario: " . mysqli_error($conn);
            }
        } else {
            // El usuario no tiene un perfil de operario, mostrar el formulario para llenar el perfil
            include 'header.php';
            include 'perfil_operario.php';
        }
    } else {
        // Error al ejecutar la consulta para verificar el perfil
        echo "Error al verificar el perfil del usuario: " . mysqli_error($conn);
    }

    include 'footer.php';
} else {
    // El usuario no está autenticado
    echo "Error: ID de usuario no disponible.";
}

// Cerrar la conexión a la base de datos
mysqli_close($conn);
?>

<script>
    function mostrarEdicion() {
        document.querySelector('.profile-section').style.display = 'none';
        document.getElementById('form_section').style.display = 'block';
    }

    function mostrarVisualizacion() {
        document.querySelector('.profile-section').style.display = 'block';
        document.getElementById('form_section').style.display = 'none';
    }

    // Mostrar imagen en el modal
    var imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var imgSrc = button.getAttribute('data-img-src');
        var modalImage = imageModal.querySelector('#modalImage');
        modalImage.src = imgSrc;
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
