<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;

// Incluir la conexión a la base de datos
include 'conexion.php';

// Consultas SQL
$sql1 = "SELECT usuarios.nombre, COUNT(mensajes.id) AS num_mensajes 
         FROM usuarios 
         LEFT JOIN mensajes ON usuarios.id = mensajes.id_usuario 
         GROUP BY usuarios.id";
$result1 = $conn->query($sql1);

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

// Crear el contenido HTML para el PDF con estilos CSS
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, h2 {
            color: #2C3E50;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 20px;
            margin-top: 40px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            color: #2C3E50;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Reporte Completo del Sistema</h1>';

$html .= '<h2>Mensajes por Usuario</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Usuario</th><th>Número de Mensajes</th></tr></thead>';
$html .= '<tbody>';
while ($row1 = $result1->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row1['nombre'] . '</td>';
    $html .= '<td>' . $row1['num_mensajes'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<h2>Solicitudes por Operario</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Operario</th><th>Número de Solicitudes</th></tr></thead>';
$html .= '<tbody>';
while ($row2 = $result2->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row2['id_operario'] . '</td>';
    $html .= '<td>' . $row2['num_solicitudes'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<h2>Mensajes por Fecha</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Fecha</th><th>Número de Mensajes</th></tr></thead>';
$html .= '<tbody>';
while ($row3 = $result3->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row3['fecha'] . '</td>';
    $html .= '<td>' . $row3['num_mensajes'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<h2>Estado de las Solicitudes</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Estado</th><th>Número de Solicitudes</th></tr></thead>';
$html .= '<tbody>';
while ($row4 = $result4->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row4['estado'] . '</td>';
    $html .= '<td>' . $row4['num_solicitudes'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<h2>Usuarios Activos (Última Semana)</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Usuario</th><th>Número de Mensajes</th></tr></thead>';
$html .= '<tbody>';
while ($row5 = $result5->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row5['nombre'] . '</td>';
    $html .= '<td>' . $row5['num_mensajes'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<h2>Calificación Promedio por Operario</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Operario</th><th>Calificación Promedio</th></tr></thead>';
$html .= '<tbody>';
while ($row6 = $result6->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row6['id_operario'] . '</td>';
    $html .= '<td>' . $row6['calificacion_promedio'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '<h2>Usuarios por Rol</h2>';
$html .= '<table>';
$html .= '<thead><tr><th>Rol</th><th>Número de Usuarios</th></tr></thead>';
$html .= '<tbody>';
while ($row7 = $result7->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row7['rol'] . '</td>';
    $html .= '<td>' . $row7['num_usuarios'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

$html .= '</body></html>';

// Inicializar Dompdf y cargar el HTML
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// (Opcional) Configurar el tamaño y la orientación del papel
$dompdf->setPaper('A4', 'landscape');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador para su descarga
$dompdf->stream("reporte_completo.pdf", array("Attachment" => 1));
?>
