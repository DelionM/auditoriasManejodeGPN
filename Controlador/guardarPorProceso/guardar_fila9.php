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

file_put_contents('debug.log', "Datos POST (fila 9): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesNueve = isset($_POST['observacionesNueve']) ? $_POST['observacionesNueve'] : null;
$accionesNueve = isset($_POST['accionesNueve']) ? $_POST['accionesNueve'] : null;
$idProblemasNueve = isset($_POST['idProblemasNueve']) ? $_POST['idProblemasNueve'] : null;
$estatusNueve = isset($_POST['estatusNueve']) ? $_POST['estatusNueve'] : null;
$fecha_filaNueve = isset($_POST['fecha_filaNueve']) ? $_POST['fecha_filaNueve'] : null;

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

$nombre_archivoNueve = null;
$ruta_archivoNueve = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $nombre_archivoNueve = basename($_FILES['archivo']['name']);
    $ruta_archivoNueve = $uploadDir . time() . '_' . $nombre_archivoNueve;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoNueve)) {
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
            observacionesNueve = ?, 
            accionesNueve = ?, 
            idProblemasNueve = ?, 
            estatusNueve = ?, 
            fecha_filaNueve = ?,
            nombre_archivoNueve = ?,
            ruta_archivoNueve = ?,
            fecha_subidaNueve = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $observacionesNueve,
        $accionesNueve,
        $idProblemasNueve,
        $estatusNueve,
        $fecha_filaNueve,
        $nombre_archivoNueve,
        $ruta_archivoNueve,
        $id_auditoria
    );
} else {
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesNueve, 
            accionesNueve, 
            idProblemasNueve, 
            estatusNueve, 
            fecha_filaNueve,
            nombre_archivoNueve,
            ruta_archivoNueve,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesNueve,
        $accionesNueve,
        $idProblemasNueve,
        $estatusNueve,
        $fecha_filaNueve,
        $nombre_archivoNueve,
        $ruta_archivoNueve
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