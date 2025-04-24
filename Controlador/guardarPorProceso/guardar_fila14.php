<?php
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require '../../conexion.php';
    if (!$conexion) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

file_put_contents('debug.log', "Datos POST (fila 14): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesCatorce = isset($_POST['observacionesCatorce']) ? $_POST['observacionesCatorce'] : null;
$accionesCatorce = isset($_POST['accionesCatorce']) ? $_POST['accionesCatorce'] : null;
$idProblemasCatorce = isset($_POST['idProblemasCatorce']) ? $_POST['idProblemasCatorce'] : null;
$estatusCatorce = isset($_POST['estatusCatorce']) ? $_POST['estatusCatorce'] : null;
$fecha_filaCatorce = isset($_POST['fecha_filaCatorce']) ? $_POST['fecha_filaCatorce'] : null;

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

$nombre_archivoCatorce = null;
$ruta_archivoCatorce = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $nombre_archivoCatorce = basename($_FILES['archivo']['name']);
    $ruta_archivoCatorce = $uploadDir . time() . '_' . $nombre_archivoCatorce;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoCatorce)) {
        echo json_encode(['success' => false, 'error' => 'Error al subir el archivo']);
        exit;
    }
}

$check_sql = "SELECT id FROM auditoria_proceso WHERE id_auditoria = ?";
$stmt_check = $conexion->prepare($check_sql);
$stmt_check->bind_param("i", $id_auditoria);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $sql = "UPDATE auditoria_proceso SET 
            observacionesCatorce = ?, 
            accionesCatorce = ?, 
            idProblemasCatorce = ?, 
            estatusCatorce = ?, 
            fecha_filaCatorce = ?,
            nombre_archivoCatorce = ?,
            ruta_archivoCatorce = ?,
            fecha_subidaCatorce = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $observacionesCatorce,
        $accionesCatorce,
        $idProblemasCatorce,
        $estatusCatorce,
        $fecha_filaCatorce,
        $nombre_archivoCatorce,
        $ruta_archivoCatorce,
        $id_auditoria
    );
} else {
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesCatorce, 
            accionesCatorce, 
            idProblemasCatorce, 
            estatusCatorce, 
            fecha_filaCatorce,
            nombre_archivoCatorce,
            ruta_archivoCatorce,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesCatorce,
        $accionesCatorce,
        $idProblemasCatorce,
        $estatusCatorce,
        $fecha_filaCatorce,
        $nombre_archivoCatorce,
        $ruta_archivoCatorce
    );
}

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $stmt->error]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Datos guardados correctamente']);

$stmt->close();
$stmt_check->close();
$conexion->close();
?>