<?php
include 'conexion.php';

// Consultas SQL
$sql = "SELECT usuarios.nombre, COUNT(mensajes.id) AS num_mensajes 
        FROM usuarios 
        LEFT JOIN mensajes ON usuarios.id = mensajes.id_usuario 
        GROUP BY usuarios.id";
$result = $conn->query($sql);

$sql2 = "SELECT operarios.id_operario, COUNT(solicitudes.id) AS num_solicitudes 
         FROM operarios 
         LEFT JOIN solicitudes ON operarios.id_operario = solicitudes.id_operario 
         GROUP BY operarios.id_operario";
$result2 = $conn->query($sql2);

$sql3 = "SELECT DATE(fecha_envio) AS fecha, COUNT(id) AS num_mensajes 
         FROM mensajes 
         GROUP BY DATE(fecha_envio)";
$result3 = $conn->query($sql3);

$sql4 = "SELECT estado, COUNT(id) AS num_solicitudes 
         FROM solicitudes 
         GROUP BY estado";
$result4 = $conn->query($sql4);

$sql5 = "SELECT usuarios.nombre, COUNT(mensajes.id) AS num_mensajes 
         FROM usuarios 
         LEFT JOIN mensajes ON usuarios.id = mensajes.id_usuario 
         WHERE mensajes.fecha_envio >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
         GROUP BY usuarios.id";
$result5 = $conn->query($sql5);

$sql6 = "SELECT operarios.id_operario, AVG(calificacion) AS calificacion_promedio 
         FROM operarios 
         GROUP BY operarios.id_operario";
$result6 = $conn->query($sql6);

$sql7 = "SELECT rol, COUNT(id) AS num_usuarios 
         FROM usuarios 
         GROUP BY rol";
$result7 = $conn->query($sql7);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Roboto', sans-serif;
        }

        .navbar-custom {
            background-color: #343a40;
        }

        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: #ffffff;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            color: #495057;
        }

        .chart-container {
            padding: 20px;
        }

        .table-container {
            margin-top: 20px;
        }

        .btn-custom {
            background-color: #17a2b8;
            color: white;
        }

        .btn-custom:hover {
            background-color: #138496;
        }

        .navbar-nav .nav-item:not(:last-child) {
            margin-right: 15px;
        }

        .navbar-nav .nav-link {
            font-size: 1.1rem;
            padding: 10px 15px;
        }
    </style>
</head>

<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Panel de Control</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_usuarios.php">Gestión de usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="text-center text-custom mb-4">Panel de Control</h2>

        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <div class="chart-container card">
                    <div class="card-body">
                        <h5 class="card-title">Número de mensajes por usuario</h5>
                        <canvas id="mensajesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="chart-container card">
                    <div class="card-body">
                        <h5 class="card-title">Número de solicitudes por operario</h5>
                        <canvas id="solicitudesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="chart-container card">
                    <div class="card-body">
                        <h5 class="card-title">Número de mensajes por fecha</h5>
                        <canvas id="mensajesPorFechaChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="chart-container card">
                    <div class="card-body">
                        <h5 class="card-title">Distribución de estados de las solicitudes</h5>
                        <canvas id="estadosSolicitudesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="chart-container card">
                    <div class="card-body">
                        <h5 class="card-title">Distribución de usuarios por rol</h5>
                        <canvas id="usuariosPorRolChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container card mt-4">
            <div class="card-body">
                <h5 class="card-title">Usuarios activos (última semana)</h5>
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Usuario</th>
                            <th>Número de Mensajes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row5 = $result5->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row5["nombre"]; ?></td>
                                <td><?php echo $row5["num_mensajes"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-container card mt-4 mb-5">
            <div class="card-body">
                <h5 class="card-title">Operarios con calificación promedio</h5>
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Operario</th>
                            <th>Calificación Promedio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row6 = $result6->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row6["id_operario"]; ?></td>
                                <td><?php echo $row6["calificacion_promedio"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-custom my-3" onclick="window.location.href='reporte.php'">Descargar Reporte</button>
    </div>

    <script>
        const createChart = (ctx, type, labels, data, label, backgroundColor, borderColor) => {
            return new Chart(ctx, {
                type: type,
                plugins: [ChartDataLabels],
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'white',
                            display: function(context) {
                                return context.dataset.data[context.dataIndex] > 0;
                            },
                            font: {
                                weight: 'bold'
                            },
                            formatter: Math.round
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutBounce'
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        };

        <?php
        // Datos para los gráficos
        function prepararDatos($resultado, $labelCampo, $dataCampo)
        {
            $labels = [];
            $data = [];
            while ($row = $resultado->fetch_assoc()) {
                $labels[] = "'" . $row[$labelCampo] . "'";
                $data[] = $row[$dataCampo];
            }
            return [implode(',', $labels), implode(',', $data)];
        }

        list($labels1, $data1) = prepararDatos($result, 'nombre', 'num_mensajes');
        list($labels2, $data2) = prepararDatos($result2, 'id_operario', 'num_solicitudes');
        list($labels3, $data3) = prepararDatos($result3, 'fecha', 'num_mensajes');
        list($labels4, $data4) = prepararDatos($result4, 'estado', 'num_solicitudes');
        list($labels5, $data5) = prepararDatos($result7, 'rol', 'num_usuarios');
        ?>

        createChart(
            document.getElementById('mensajesChart').getContext('2d'),
            'bar',
            [<?php echo $labels1; ?>],
            [<?php echo $data1; ?>],
            'Número de Mensajes',
            'rgba(54, 162, 235, 0.2)',
            'rgba(54, 162, 235, 1)'
        );

        createChart(
            document.getElementById('solicitudesChart').getContext('2d'),
            'bar',
            [<?php echo $labels2; ?>],
            [<?php echo $data2; ?>],
            'Número de Solicitudes',
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 99, 132, 1)'
        );

        createChart(
            document.getElementById('mensajesPorFechaChart').getContext('2d'),
            'line',
            [<?php echo $labels3; ?>],
            [<?php echo $data3; ?>],
            'Número de Mensajes',
            'rgba(75, 192, 192, 0.2)',
            'rgba(75, 192, 192, 1)'
        );

        createChart(
            document.getElementById('estadosSolicitudesChart').getContext('2d'),
            'pie',
            [<?php echo $labels4; ?>],
            [<?php echo $data4; ?>],
            'Distribución de Estados de las Solicitudes',
            [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ]
        );

        createChart(
            document.getElementById('usuariosPorRolChart').getContext('2d'),
            'bar',
            [<?php echo $labels5; ?>],
            [<?php echo $data5; ?>],
            'Número de Usuarios',
            'rgba(255, 206, 86, 0.2)',
            'rgba(255, 206, 86, 1)'
        );
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
