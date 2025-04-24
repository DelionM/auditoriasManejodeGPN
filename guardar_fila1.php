<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Conexión a la base de datos
require "conexion.php";
if ($conexion->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conexion->connect_error]);
    exit;
}

// Obtener datos POST
$id_auditoria = $_POST['id'] ?? null;
$numero_empleado = $_POST['numero_empleado'] ?? null;
$nombre_auditor = $_POST['nombre_auditor'] ?? null;
$cliente = $_POST['cliente'] ?? null;
$proceso_auditado = $_POST['proceso_auditado'] ?? null;
$parte_auditada = $_POST['parte_auditada'] ?? null;
$operacion_auditada = $_POST['operacion_auditada'] ?? null;
$nave = $_POST['nave'] ?? null;
$unidad = $_POST['unidad'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;
$acciones = $_POST['acciones'] ?? null;
$idProblemasUnoUno = $_POST['idProblemasUnoUno'] ?? null;
$estatus = $_POST['estatus'] ?? null;
$fecha_fila = $_POST['fecha_fila'] ?? null;

// Validar campos requeridos
$required_fields = [
    'id_auditoria' => $id_auditoria,
    'numero_empleado' => $numero_empleado,
    'proceso_auditado' => $proceso_auditado,
    'parte_auditada' => $parte_auditada,
    'operacion_auditada' => $operacion_auditada,
    'nave' => $nave,
    'unidad' => $unidad,
    'fecha' => $fecha
];

$missing_fields = [];
foreach ($required_fields as $field_name => $field_value) {
    if (empty($field_value)) {
        $missing_fields[] = $field_name;
    }
}

if (!empty($missing_fields)) {
    echo json_encode(['success' => false, 'error' => 'Campos requeridos vacíos: ' . implode(', ', $missing_fields)]);
    exit;
}

// Manejo de archivo
$nombre_archivo = null;
$ruta_archivo = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['archivo'];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombreLimpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombre_archivo = $nombreLimpio . "_" . time() . "." . $extension;
    $directorioDestino = "uploads/";

    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $ruta_archivo = $directorioDestino . $nombre_archivo;

    if (!move_uploaded_file($archivo["tmp_name"], $ruta_archivo)) {
        echo json_encode(["success" => false, "error" => "❌ Error al mover el archivo."]);
        exit;
    }
}

// Verificar si ya existe el registro
$checkQuery = "SELECT id_auditoria FROM auditorias WHERE id_auditoria = ?";
$checkStmt = $conexion->prepare($checkQuery);
$checkStmt->bind_param("s", $id_auditoria);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    // Actualizar registro existente
    $query = "UPDATE auditorias SET 
        numero_empleado = ?, 
        nombre_auditor = ?, 
        cliente = ?, 
        proceso_auditado = ?, 
        parte_auditada = ?, 
        operacion_auditada = ?, 
        nave = ?, 
        unidad = ?, 
        fecha = ?, 
        observaciones = ?, 
        acciones = ?, 
        idProblemasUnoUno = ?, 
        estatus = ?, 
        fecha_fila = ?, 
        nombre_archivo = ?, 
        ruta_archivo = ?, 
        fecha_subida = NOW()
        WHERE id_auditoria = ?";

    $stmt = $conexion->prepare($query);
    $stmt->bind_param(
        "sssssssssssssssss",
        $numero_empleado,
        $nombre_auditor,
        $cliente,
        $proceso_auditado,
        $parte_auditada,
        $operacion_auditada,
        $nave,
        $unidad,
        $fecha,
        $observaciones,
        $acciones,
        $idProblemasUnoUno,
        $estatus,
        $fecha_fila,
        $nombre_archivo,
        $ruta_archivo,
        $id_auditoria
    );

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "✅ Datos actualizados correctamente.",
            "id" => $id_auditoria
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "❌ Error al actualizar: " . $stmt->error]);
    }
    $stmt->close();
} else {
    // Insertar nuevo registro
    $insertQuery = "INSERT INTO auditorias (
        id_auditoria, 
        numero_empleado, 
        nombre_auditor, 
        cliente, 
        proceso_auditado, 
        parte_auditada, 
        operacion_auditada, 
        nave, 
        unidad, 
        fecha, 
        observaciones, 
        acciones, 
        idProblemasUnoUno, 
        estatus, 
        fecha_fila, 
        nombre_archivo, 
        ruta_archivo, 
        fecha_inicio_proceso
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $insertStmt = $conexion->prepare($insertQuery);
    $insertStmt->bind_param(
        "sssssssssssssssss",
        $id_auditoria,
        $numero_empleado,
        $nombre_auditor,
        $cliente,
        $proceso_auditado,
        $parte_auditada,
        $operacion_auditada,
        $nave,
        $unidad,
        $fecha,
        $observaciones,
        $acciones,
        $idProblemasUnoUno,
        $estatus,
        $fecha_fila,
        $nombre_archivo,
        $ruta_archivo
    );

    if ($insertStmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "✅ Datos guardados correctamente.",
            "id" => $id_auditoria
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "❌ Error al insertar: " . $insertStmt->error]);
    }
    $insertStmt->close();
}

$checkStmt->close();
$conexion->close();
?>