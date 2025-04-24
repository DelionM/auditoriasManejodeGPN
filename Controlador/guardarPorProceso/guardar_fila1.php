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
file_put_contents('debug.log', "Datos POST: " . print_r($_POST, true) . "\nArchivos: " . print_r($_FILES, true) . "\n", FILE_APPEND);

// Obtener los datos enviados desde el frontend
$id_auditoria = isset($_POST['id']) ? $_POST['id'] : null;
$numero_empleado = isset($_POST['numero_empleado']) ? $_POST['numero_empleado'] : null;
$nombre_auditor = isset($_POST['nombre_auditor']) ? $_POST['nombre_auditor'] : null;
$cliente = isset($_POST['cliente']) ? trim($_POST['cliente']) : null;
$proceso_auditado = isset($_POST['proceso_auditado']) ? trim($_POST['proceso_auditado']) : null;
$parte_auditada = isset($_POST['parte_auditada']) ? trim($_POST['parte_auditada']) : null;
$nivelIngenieria = isset($_POST['nivelIngenieria']) ? trim($_POST['nivelIngenieria']) : null;
$nave = isset($_POST['nave']) ? trim($_POST['nave']) : null;
$unidad = isset($_POST['unidad']) ? trim($_POST['unidad']) : null;
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : null;
$nombre_supervisor = isset($_POST['nombre_supervisor']) ? trim($_POST['nombre_supervisor']) : null;
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : null;
$acciones = isset($_POST['acciones']) ? $_POST['acciones'] : null;
$idProblemasUno = isset($_POST['idProblemasUno']) ? $_POST['idProblemasUno'] : null;
$estatusUno = isset($_POST['estatusUno']) ? $_POST['estatusUno'] : null;
$fecha_filaUno = isset($_POST['fecha_filaUno']) ? $_POST['fecha_filaUno'] : null;

// Validar que el id_auditoria esté presente
if (!$id_auditoria) {
    echo json_encode(['success' => false, 'error' => 'ID de auditoría no proporcionado']);
    exit;
}

// Validar campos obligatorios
$campos_obligatorios = [
    'cliente' => $cliente,
    'proceso_auditado' => $proceso_auditado,
    'parte_auditada' => $parte_auditada,
    'nivelIngenieria' => $nivelIngenieria,
    'nave' => $nave,
    'unidad' => $unidad,
    'fecha' => $fecha,
    'nombre_supervisor' => $nombre_supervisor
];

foreach ($campos_obligatorios as $campo => $valor) {
    if (is_null($valor) || $valor === '') {
        echo json_encode(['success' => false, 'error' => "El campo '$campo' es obligatorio y no puede estar vacío"]);
        exit;
    }
}

// Manejo de archivo subido
$nombre_archivoUno = null;
$ruta_archivoUno = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Crear el directorio si no existe
    }
    $nombre_archivoUno = basename($_FILES['archivo']['name']);
    $ruta_archivoUno = $uploadDir . time() . '_' . $nombre_archivoUno;

    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta_archivoUno)) {
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
            numero_empleado = ?, 
            nombre_auditor = ?, 
            cliente = ?, 
            proceso_auditado = ?, 
            parte_auditada = ?, 
            nivelIngenieria = ?, 
            nave = ?, 
            unidad = ?, 
            fecha = ?, 
            supervisor = ?, 
            observaciones = ?, 
            acciones = ?, 
            idProblemasUno = ?, 
            estatusUno = ?, 
            fecha_filaUno = ?,
            nombre_archivoUno = ?,
            ruta_archivoUno = ?,
            fecha_subidaUno = CURRENT_TIMESTAMP
            WHERE id_auditoria = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error en la preparación de la actualización: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param(
        "sssssssssssssssssi",
        $numero_empleado,
        $nombre_auditor,
        $cliente,
        $proceso_auditado,
        $parte_auditada,
        $nivelIngenieria,
        $nave,
        $unidad,
        $fecha,
        $nombre_supervisor,
        $observaciones,
        $acciones,
        $idProblemasUno,
        $estatusUno,
        $fecha_filaUno,
        $nombre_archivoUno,
        $ruta_archivoUno,
        $id_auditoria
    );
} else {
    // Insertar un nuevo registro
    $sql = "INSERT INTO auditoria_proceso (
            id_auditoria, 
            numero_empleado, 
            nombre_auditor, 
            cliente, 
            proceso_auditado, 
            parte_auditada, 
            nivelIngenieria, 
            nave, 
            unidad, 
            fecha, 
            supervisor, 
            observaciones, 
            acciones, 
            idProblemasUno, 
            estatusUno, 
            fecha_filaUno,
            nombre_archivoUno,
            ruta_archivoUno,
            fecha_inicio_proceso
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Error en la preparación de la inserción: ' . $conexion->error]);
        exit;
    }
    $stmt->bind_param(
        "isssssssssssssssss",
        $id_auditoria,
        $numero_empleado,
        $nombre_auditor,
        $cliente,
        $proceso_auditado,
        $parte_auditada,
        $nivelIngenieria,
        $nave,
        $unidad,
        $fecha,
        $nombre_supervisor,
        $observaciones,
        $acciones,
        $idProblemasUno,
        $estatusUno,
        $fecha_filaUno,
        $nombre_archivoUno,
        $ruta_archivoUno
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