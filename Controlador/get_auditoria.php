<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "auditoria";

// Crear la conexión
$conexion = new mysqli($host, $usuario, $password, $baseDatos);

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error en la conexión: " . $conexion->connect_error]));
}

// Iniciar sesión para obtener el número de empleado
session_start();
$numero_empleado = isset($_SESSION['numero_empleado']) ? $_SESSION['numero_empleado'] : null;

if (!$numero_empleado) {
    echo json_encode(["error" => "No se encontró el número de empleado en la sesión"]);
    exit;
}

// Obtener el último id_auditoria asociado al usuario logueado desde programar_auditoria
$sql = "SELECT id_auditoria FROM programar_auditoria WHERE numero_empleado = ? ORDER BY id_auditoria DESC LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $numero_empleado);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_auditoria = $row["id_auditoria"];
    echo json_encode(["id" => $id_auditoria]);
} else {
    echo json_encode(["error" => "No se encontró una auditoría programada para el empleado $numero_empleado"]);
}

// Cerrar conexión
$stmt->close();
$conexion->close();
?>