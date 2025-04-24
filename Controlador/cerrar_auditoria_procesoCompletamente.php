<?php
session_start();
require '../conexion.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar conexión a la base de datos
if ($conexion->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos: ' . $conexion->connect_error]);
    exit;
}

// Leer el cuerpo de la solicitud (JSON)
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Depuración
$debug_msg = "=== Nueva solicitud ===\n" .
             "Datos recibidos en el cuerpo: " . $input . "\n" .
             "Datos decodificados: " . print_r($data, true) . "\n";
file_put_contents('debug.log', $debug_msg, FILE_APPEND);

// Capturar datos
$id_auditoria = isset($data['id_auditoria']) ? trim($data['id_auditoria']) : '';
$nombreAuditado = isset($data['nombreAuditado']) ? trim($data['nombreAuditado']) : '';
$nombreSupervisor = isset($data['nombreSupervisor']) ? trim($data['nombreSupervisor']) : '';
$nombreAuditor = isset($data['nombreAuditor']) ? trim($data['nombreAuditor']) : '';

// Validar ID
if ($id_auditoria === '') {
    file_put_contents('debug.log', "Error: ID de auditoría no recibido\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID de auditoría no recibido']);
    exit;
}

// Validar campos de nombres
if ($nombreAuditado === '' || $nombreSupervisor === '' || $nombreAuditor === '') {
    $error_msg = "Error: Campos vacíos detectados:\n" .
                 "nombreAuditado: '$nombreAuditado'\n" .
                 "nombreSupervisor: '$nombreSupervisor'\n" .
                 "nombreAuditor: '$nombreAuditor'\n";
    file_put_contents('debug.log', $error_msg, FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Todos los campos de nombres deben estar llenos']);
    exit;
}   

// Consultar auditoria_proceso
$sql = "SELECT * FROM auditoria_proceso WHERE id_auditoria = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    file_put_contents('debug.log', "Error preparando consulta: " . $conexion->error . "\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error preparando consulta: ' . $conexion->error]);
    exit;
}
$stmt->bind_param("i", $id_auditoria);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    file_put_contents('debug.log', "Auditoría no encontrada para ID: $id_auditoria\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Auditoría no encontrada']);
    exit;
}

// if ($row['estatus_cierre'] === 'Cerrado') {
//     file_put_contents('debug.log', "La auditoría ya está cerrada para ID: $id_auditoria\n", FILE_APPEND);
//     header('Content-Type: application/json');
//     echo json_encode(['success' => false, 'message' => 'La auditoría ya está cerrada']);
//     exit;
// }

// Actualizar auditoria_proceso
$sql = "UPDATE auditoria_proceso SET 
        idNombreOperador = ?, 
        idNombreSupervisor = ?, 
        idNombreAuditor2 = ?, 
        estatus_cierre = 'Cerrado', 
        fecha_cierre = NOW()
        WHERE id_auditoria = ?";
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    file_put_contents('debug.log', "Error preparando actualización: " . $conexion->error . "\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error preparando actualización: ' . $conexion->error]);
    exit;
}
$stmt->bind_param("sssi", $nombreAuditado, $nombreSupervisor, $nombreAuditor, $id_auditoria);

if ($stmt->execute()) {
    // Actualizar programar_auditoria a 'Cerrada'
    $sql_update = "UPDATE programar_auditoria SET estatus = 'Cerrada' WHERE id_auditoria = ?";
    $stmt_update = $conexion->prepare($sql_update);
    if (!$stmt_update) {
        file_put_contents('debug.log', "Error preparando actualización de programar_auditoria: " . $conexion->error . "\n", FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error preparando actualización secundaria: ' . $conexion->error]);
        exit;
    }
    $stmt_update->bind_param("i", $id_auditoria);
    $stmt_update->execute();
    $stmt_update->close();

    file_put_contents('debug.log', "Auditoría cerrada correctamente para ID: $id_auditoria\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Auditoría cerrada correctamente']);
} else {
    file_put_contents('debug.log', "Error al cerrar la auditoría: " . $stmt->error . "\n", FILE_APPEND);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error al cerrar la auditoría: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>