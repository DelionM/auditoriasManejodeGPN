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

file_put_contents('debug.log', "Datos POST (fila 17): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesDiecisiete = isset($_POST['observacionesDiecisiete']) ? $_POST['observacionesDiecisiete'] : null;
$accionesDiecisiete = isset($_POST['accionesDiecisiete']) ? $_POST['accionesDiecisiete'] : null;
$idProblemasDiecisiete = isset($_POST['idProblemasDiecisiete']) ? $_POST['idProblemasDiecisiete'] : null;
$estatusDiecisiete = isset($_POST['estatusDiecisiete']) ? $_POST['estatusDiecisiete'] : null;
$fecha_filaDiecisiete = isset($_POST['fecha_filaDiecisiete']) ? $_POST['fecha_filaDiecisiete'] : null;

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

$nombre_archivoDiecisiete = null;
$ruta_archivoDiecisiete = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $nombre_archivoDiecisiete = basename($_FILES['archivo']['name']);
    $ruta_archivoDiecisiete = $uploadDir . time() . '_' . $nombre_archivoDiecisiete;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoDiecisiete)) {
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
            observacionesDiecisiete = ?, 
            accionesDiecisiete = ?, 
            idProblemasDiecisiete = ?, 
            estatusDiecisiete = ?, 
            fecha_filaDiecisiete = ?,
            nombre_archivoDiecisiete = ?,
            ruta_archivoDiecisiete = ?,
            fecha_subidaDiecisiete = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $observacionesDiecisiete,
        $accionesDiecisiete,
        $idProblemasDiecisiete,
        $estatusDiecisiete,
        $fecha_filaDiecisiete,
        $nombre_archivoDiecisiete,
        $ruta_archivoDiecisiete,
        $id_auditoria
    );
} else {
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesDiecisiete, 
            accionesDiecisiete, 
            idProblemasDiecisiete, 
            estatusDiecisiete, 
            fecha_filaDiecisiete,
            nombre_archivoDiecisiete,
            ruta_archivoDiecisiete,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesDiecisiete,
        $accionesDiecisiete,
        $idProblemasDiecisiete,
        $estatusDiecisiete,
        $fecha_filaDiecisiete,
        $nombre_archivoDiecisiete,
        $ruta_archivoDiecisiete
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