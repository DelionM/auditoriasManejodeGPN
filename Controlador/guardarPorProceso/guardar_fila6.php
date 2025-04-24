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

file_put_contents('debug.log', "Datos POST (fila 6): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesSeis = isset($_POST['observacionesSeis']) ? $_POST['observacionesSeis'] : null;
$accionesSeis = isset($_POST['accionesSeis']) ? $_POST['accionesSeis'] : null;
$idProblemasSeis = isset($_POST['idProblemasSeis']) ? $_POST['idProblemasSeis'] : null;
$estatusSeis = isset($_POST['estatusSeis']) ? $_POST['estatusSeis'] : null;
$fecha_filaSeis = isset($_POST['fecha_filaSeis']) ? $_POST['fecha_filaSeis'] : null;

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

$nombre_archivoSeis = null;
$ruta_archivoSeis = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $nombre_archivoSeis = basename($_FILES['archivo']['name']);
    $ruta_archivoSeis = $uploadDir . time() . '_' . $nombre_archivoSeis;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoSeis)) {
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
            observacionesSeis = ?, 
            accionesSeis = ?, 
            idProblemasSeis = ?, 
            estatusSeis = ?, 
            fecha_filaSeis = ?,
            nombre_archivoSeis = ?,
            ruta_archivoSeis = ?,
            fecha_subidaSeis = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $observacionesSeis,
        $accionesSeis,
        $idProblemasSeis,
        $estatusSeis,
        $fecha_filaSeis,
        $nombre_archivoSeis,
        $ruta_archivoSeis,
        $id_auditoria
    );
} else {
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesSeis, 
            accionesSeis, 
            idProblemasSeis, 
            estatusSeis, 
            fecha_filaSeis,
            nombre_archivoSeis,
            ruta_archivoSeis,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesSeis,
        $accionesSeis,
        $idProblemasSeis,
        $estatusSeis,
        $fecha_filaSeis,
        $nombre_archivoSeis,
        $ruta_archivoSeis
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