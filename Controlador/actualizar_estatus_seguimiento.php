<?php
// actualizar_estatus_seguimiento.php

// Forzar tipo de contenido JSON
header('Content-Type: application/json');

// Habilitar errores para depuración (temporalmente)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Registro en archivo para depuración
file_put_contents('../Controlador/debug.log', "Script iniciado: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// Conexión a la base de datos (usa credenciales correctas)
include('../conexion.php');


// Obtener datos del POST
$id_seguimiento = isset($_POST['id_seguimiento']) ? $_POST['id_seguimiento'] : '';
$estatus = isset($_POST['estatus']) ? $_POST['estatus'] : '';

file_put_contents('../Controlador/debug.log', "Datos recibidos - id_seguimiento: $id_seguimiento, estatus: $estatus\n", FILE_APPEND);

if (empty($id_seguimiento) || empty($estatus)) {
    file_put_contents('../Controlador/debug.log', "Faltan parámetros\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros (id_seguimiento o estatus).']);
    exit;
}

if (!is_numeric($id_seguimiento)) {
    file_put_contents('../Controlador/debug.log', "id_seguimiento no numérico: $id_seguimiento\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'El id_seguimiento debe ser un número válido.']);
    exit;
}

// Actualizar el estatus en la base de datos
$sql = "UPDATE seguimientos SET estatus_seguimiento = ? WHERE id_seguimiento = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    file_put_contents('../Controlador/debug.log', "Error al preparar consulta: " . $conn->error . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

$stmt->bind_param('si', $estatus, $id_seguimiento);

if ($stmt->execute()) {
    file_put_contents('../Controlador/debug.log', "Actualización exitosa\n", FILE_APPEND);
    echo json_encode(['success' => true]);
} else {
    file_put_contents('../Controlador/debug.log', "Error al actualizar: " . $stmt->error . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>