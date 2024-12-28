<?php

date_default_timezone_set('America/Bogota');

$host = "localhost";
$user = "root";
$pass = "fabio0824";
$db = "peticiones_db";

$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
