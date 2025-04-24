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

file_put_contents('debug.log', "Datos POST (fila 18): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesDieciocho = isset($_POST['observacionesDieciocho']) ? $_POST['observacionesDieciocho'] : null;
$accionesDieciocho = isset($_POST['accionesDieciocho']) ? $_POST['accionesDieciocho'] : null;
$idProblemasDieciocho = isset($_POST['idProblemasDieciocho']) ? $_POST['idProblemasDieciocho'] : null;
$estatusDieciocho = isset($_POST['estatusDieciocho']) ? $_POST['estatusDieciocho'] : null;
$fecha_filaDieciocho = isset($_POST['fecha_filaDieciocho']) ? $_POST['fecha_filaDieciocho'] : null;

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

$nombre_archivoDieciocho = null;
$ruta_archivoDieciocho = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $nombre_archivoDieciocho = basename($_FILES['archivo']['name']);
    $ruta_archivoDieciocho = $uploadDir . time() . '_' . $nombre_archivoDieciocho;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoDieciocho)) {
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
            observacionesDieciocho = ?, 
            accionesDieciocho = ?, 
            idProblemasDieciocho = ?, 
            estatusDieciocho = ?, 
            fecha_filaDieciocho = ?,
            nombre_archivoDieciocho = ?,
            ruta_archivoDieciocho = ?,
            fecha_subidaDieciocho = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $observacionesDieciocho,
        $accionesDieciocho,
        $idProblemasDieciocho,
        $estatusDieciocho,
        $fecha_filaDieciocho,
        $nombre_archivoDieciocho,
        $ruta_archivoDieciocho,
        $id_auditoria
    );
} else {
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesDieciocho, 
            accionesDieciocho, 
            idProblemasDieciocho, 
            estatusDieciocho, 
            fecha_filaDieciocho,
            nombre_archivoDieciocho,
            ruta_archivoDieciocho,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesDieciocho,
        $accionesDieciocho,
        $idProblemasDieciocho,
        $estatusDieciocho,
        $fecha_filaDieciocho,
        $nombre_archivoDieciocho,
        $ruta_archivoDieciocho
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