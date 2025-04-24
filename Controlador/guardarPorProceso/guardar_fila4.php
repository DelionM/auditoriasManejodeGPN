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
file_put_contents('debug.log', "Datos POST (fila 4): " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

// Obtener los datos enviados desde el frontend
$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$observacionesCuatro = isset($_POST['observacionesCuatro']) ? $_POST['observacionesCuatro'] : null;
$accionesCuatro = isset($_POST['accionesCuatro']) ? $_POST['accionesCuatro'] : null;
$idProblemasCuatro = isset($_POST['idProblemasCuatro']) ? $_POST['idProblemasCuatro'] : null;
$estatusCuatro = isset($_POST['estatusCuatro']) ? $_POST['estatusCuatro'] : null;
$fecha_filaCuatro = isset($_POST['fecha_filaCuatro']) ? $_POST['fecha_filaCuatro'] : null;

// Validar que el id_auditoria esté presente
if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

// Manejo de archivo subido
$nombre_archivoCuatro = null;
$ruta_archivoCuatro = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Crear el directorio si no existe
    }
    $nombre_archivoCuatro = basename($_FILES['archivo']['name']);
    $ruta_archivoCuatro = $uploadDir . time() . '_' . $nombre_archivoCuatro;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoCuatro)) {
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
            observacionesCuatro = ?, 
            accionesCuatro = ?, 
            idProblemasCuatro = ?, 
            estatusCuatro = ?, 
            fecha_filaCuatro = ?,
            nombre_archivoCuatro = ?,
            ruta_archivoCuatro = ?,
            fecha_subidaCuatro = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error en la preparación de la actualización: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param(
        "sssssssi",
        $observacionesCuatro,
        $accionesCuatro,
        $idProblemasCuatro,
        $estatusCuatro,
        $fecha_filaCuatro,
        $nombre_archivoCuatro,
        $ruta_archivoCuatro,
        $id_auditoria
    );
} else {
    // Insertar un nuevo registro
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            observacionesCuatro, 
            accionesCuatro, 
            idProblemasCuatro, 
            estatusCuatro, 
            fecha_filaCuatro,
            nombre_archivoCuatro,
            ruta_archivoCuatro,
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
        $observacionesCuatro,
        $accionesCuatro,
        $idProblemasCuatro,
        $estatusCuatro,
        $fecha_filaCuatro,
        $nombre_archivoCuatro,
        $ruta_archivoCuatro
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