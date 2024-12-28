<?php
require 'db_config.php';

$tipo_pqr = $_POST['tipo_pqr'];
$nombre = $_POST['nombre'];
$documento = $_POST['documento'];
$correo = $_POST['correo'];

// Lógica de días restantes según el tipo de petición
switch ($tipo_pqr) {
    case 'denuncia':
    case 'derecho_peticion':
    case 'felicitacion':
    case 'queja':
    case 'reclamo':
    case 'sugerencia':
        $dias_habiles = 15;
        break;
    case 'peticion_consulta':
        $dias_habiles = 30;
        break;
    case 'peticion_documentos':
    case 'peticion_informacion':
    case 'peticion_entre_autoridades':
        $dias_habiles = 10;
        break;
    case 'peticion_por_congresistas':
        $dias_habiles = 5;
        break;
    default:
        $dias_habiles = 5;
        break;
}

// Calcular tiempo restante en segundos
$tiempo_restante_segundos = $dias_habiles * 24 * 60 * 60; // Días a segundos

// Insertar la nueva petición en la base de datos
$sql = "INSERT INTO peticiones (tipo, nombre, documento, correo, fecha_inicio, dias_restantes, tiempo_restante_segundos, estado, historial)
        VALUES ('$tipo_pqr', '$nombre', '$documento', '$correo', NOW(), $dias_habiles, $tiempo_restante_segundos, 'activa', '')";

if ($conn->query($sql) === TRUE) {
    header('Location: index.php');
} else {
    echo "Error: " . $conn->error;
}
?>
