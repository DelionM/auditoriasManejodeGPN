<?php
require '../conexion.php';

header('Content-Type: application/json');

$query_empleados = "SELECT numero_empleado, nombre, correo FROM empleados WHERE estado = 1";
$result_empleados = $conexion->query($query_empleados);

$empleados = [];
while ($row = $result_empleados->fetch_assoc()) {
    $empleados[] = $row;
}

echo json_encode(['success' => true, 'empleados' => $empleados]);

$conexion->close();
?>