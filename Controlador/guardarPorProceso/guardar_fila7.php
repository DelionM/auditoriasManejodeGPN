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

file_put_contents('debug.log', "Datos POST (fila 7): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesSiete = isset($_POST['observacionesSiete']) ? $_POST['observacionesSiete'] : null;
$accionesSiete = isset($_POST['accionesSiete']) ? $_POST['accionesSiete'] : null;
$idProblemasSiete = isset($_POST['idProblemasSiete']) ? $_POST['idProblemasSiete'] : null;
$estatusSiete = isset($_POST['estatusSiete']) ? $_POST['estatusSiete'] : null;
$fecha_filaSiete = isset($_POST['fecha_filaSiete']) ? $_POST['fecha_filaSiete'] : null;

if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

$nombre_archivoSiete = null;
$ruta_archivoSiete = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $nombre_archivoSiete = basename($_FILES['archivo']['name']);
    $ruta_archivoSiete = $uploadDir . time() . '_' . $nombre_archivoSiete;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoSiete)) {
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
            observacionesSiete = ?, 
            accionesSiete = ?, 
            idProblemasSiete = ?, 
            estatusSiete = ?, 
            fecha_filaSiete = ?,
            nombre_archivoSiete = ?,
            ruta_archivoSiete = ?,
            fecha_subidaSiete = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "sssssssi",
        $observacionesSiete,
        $accionesSiete,
        $idProblemasSiete,
        $estatusSiete,
        $fecha_filaSiete,
        $nombre_archivoSiete,
        $ruta_archivoSiete,
        $id_auditoria
    );
} else {
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesSiete, 
            accionesSiete, 
            idProblemasSiete, 
            estatusSiete, 
            fecha_filaSiete,
            nombre_archivoSiete,
            ruta_archivoSiete,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesSiete,
        $accionesSiete,
        $idProblemasSiete,
        $estatusSiete,
        $fecha_filaSiete,
        $nombre_archivoSiete,
        $ruta_archivoSiete
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