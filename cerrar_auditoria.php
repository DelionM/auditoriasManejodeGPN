<?php
session_start();
require 'conexion.php';

error_reporting(E_ALL); // Mostrar todos los errores
ini_set('display_errors', 1); // Mostrar errores en pantalla (solo para desarrollo)

$nombreOperador = $_POST['nombreOperador'] ?? '';
$nombreSupervisor = $_POST['nombreSupervisor'] ?? '';
$nombreAuditor = $_POST['nombreAuditor'] ?? '';
$id_auditoria = $_SESSION['id_auditoria'] ?? null;

// Depuración
// file_put_contents('debug.log', "POST: " . print_r($_POST, true) . "\nSESSION: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no encontrado']);
    exit;
}

if (empty($nombreOperador) || empty($nombreSupervisor) || empty($nombreAuditor)) {
    echo json_encode(['success' => false, 'error' => 'Todos los campos de nombres deben estar llenos']);
    exit;
}

$sql = "SELECT * FROM auditorias WHERE id_auditoria = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta: ' . $conexion->error]);
    exit;
}
$stmt->bind_param("i", $id_auditoria);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'Auditoría no encontrada']);
    exit;
}

$sql = "UPDATE auditorias SET 
        idNombreOperador = ?, 
        idNombreSupervisor = ?, 
        idNombreAuditor2 = ?, 
        estatus_cierre = 'Cerrado', 
        fecha_cierre = NOW()  -- Aquí se agrega la fecha y hora del cierre
        WHERE id_auditoria = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la actualización: ' . $conexion->error]);
    exit;
}
$stmt->bind_param("sssi", $nombreOperador, $nombreSupervisor, $nombreAuditor, $id_auditoria);

if ($stmt->execute()) {
    $sql_update = "UPDATE programar_auditoria SET estatus = 'Realizada' WHERE id_auditoria = ?";
    $stmt_update = $conexion->prepare($sql_update);
    $stmt_update->bind_param("i", $id_auditoria);
    $stmt_update->execute();

    echo json_encode(['success' => true, 'message' => 'Auditoría cerrada correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al cerrar la auditoría: ' . $stmt->error]);
}

$stmt->close();
mysqli_close($conexion);
?>
