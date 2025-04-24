<?php
header('Content-Type: application/json');

// Habilitar errores en pantalla para desarrollo (desactiva en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir archivo de conexión a la base de datos
try {
    require '../../conexion.php';
    if (!$conexion) {
        throw new Exception('No se pudo conectar a la base de datos');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido']);
    exit;
}

// Depuración: guardar los datos recibidos en un archivo de log
file_put_contents('debug.log', "Datos POST (fila 3): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

// Obtener los datos enviados desde el frontend
$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesTres = isset($_POST['observacionesTres']) ? $_POST['observacionesTres'] : null;
$accionesTres = isset($_POST['accionesTres']) ? $_POST['accionesTres'] : null;
$idProblemasTres = isset($_POST['idProblemasTres']) ? $_POST['idProblemasTres'] : null;
$estatusTres = isset($_POST['estatusTres']) ? $_POST['estatusTres'] : null;
$fecha_filaTres = isset($_POST['fecha_filaTres']) ? $_POST['fecha_filaTres'] : null;

// Validar que el id_auditoria esté presente
if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

// Manejo de archivo subido
$nombre_archivoTres = null;
$ruta_archivoTres = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Crear el directorio si no existe
    }
    $nombre_archivoTres = basename($_FILES['archivo']['name']);
    $ruta_archivoTres = $uploadDir . time() . '_' . $nombre_archivoTres;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoTres)) {
        echo json_encode(['success' => false, 'error' => 'Error al subir el archivo']);
        exit;
    }
}

// Preparar la consulta para verificar si ya existe un registro con ese id_auditoria
$check_sql = "SELECT id FROM auditoria_proceso WHERE id_auditoria = ?";
$stmt_check = $conexion->prepare($check_sql);
if (!$stmt_check) {
    echo json_encode(['success' => false, 'error' => 'Error en la preparación de la consulta de verificación: ' . $conexion->error]);
    exit;
}
$stmt_check->bind_param("i", $id_auditoria);
if (!$stmt_check->execute()) {
    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta de verificación: ' . $stmt_check->error]);
    exit;
}
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Actualizar el registro existente
    $sql = "UPDATE auditoria_proceso SET 
            observacionesTres = ?, 
            accionesTres = ?, 
            idProblemasTres = ?, 
            estatusTres = ?, 
            fecha_filaTres = ?,
            nombre_archivoTres = ?,
            ruta_archivoTres = ?,
            fecha_subidaTres = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error en la preparación de la actualización: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param(
        "sssssssi",
        $observacionesTres,
        $accionesTres,
        $idProblemasTres,
        $estatusTres,
        $fecha_filaTres,
        $nombre_archivoTres,
        $ruta_archivoTres,
        $id_auditoria
    );
} else {
    // Insertar un nuevo registro
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesTres, 
            accionesTres, 
            idProblemasTres, 
            estatusTres, 
            fecha_filaTres,
            nombre_archivoTres,
            ruta_archivoTres,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error en la preparación de la inserción: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param(
        "isssssss",
        $id_auditoria,
        $observacionesTres,
        $accionesTres,
        $idProblemasTres,
        $estatusTres,
        $fecha_filaTres,
        $nombre_archivoTres,
        $ruta_archivoTres
    );
}

// Ejecutar la consulta
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Error al ejecutar la consulta: ' . $stmt->error]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Datos guardados correctamente']);

// Cerrar las conexiones
$stmt->close();
$stmt_check->close();
$conexion->close();
?>