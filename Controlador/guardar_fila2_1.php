<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0); // Evita mostrar errores en pantalla
ini_set('log_errors', 1); // Registra errores en un archivo
ini_set('error_log', 'php_errors.log'); // Archivo de log

$conn = new mysqli("localhost", "root", "", "auditoria");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conn->connect_error]);
    exit;
}

// Obtener datos del POST (solo id_auditoria y campos de la fila 2.1)
$id_auditoria = $_POST["id"] ?? null;
$estatus_21 = $_POST["estatus21"] ?? "";
$responsable_21 = $_POST["responsable21"] ?? "";
$fecha_fila_21 = $_POST["fechaFila21"] ?? "";
$observaciones_21 = $_POST["observaciones21"] ?? "";
$acciones_21 = $_POST["acciones21"] ?? "";

// Validar id_auditoria
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ Falta el id_auditoria."]);
    exit;
}

// Subida de archivos
$nombre_archivo_21 = null;
$ruta_archivo_21 = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombre_limpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombre_archivo_21 = $nombre_limpio . "_" . time() . "." . $extension;

    $directorio_destino = "uploads/";
    if (!is_dir($directorio_destino)) {
        mkdir($directorio_destino, 0777, true);
    }

    $ruta_archivo_21 = $directorio_destino . $nombre_archivo_21;
    if (!move_uploaded_file($archivo["tmp_name"], $ruta_archivo_21)) {
        echo json_encode(["success" => false, "error" => "❌ Error al subir el archivo."]);
        exit;
    }
}

// Validaciones adicionales para la fila 2.1
if ($estatus_21 === "OK") {
    if (empty($fecha_fila_21)) {
        echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 2.1."]);
        exit;
    }
} elseif ($estatus_21 === "Pendiente" || $estatus_21 === "NOK") {
    if (empty($observaciones_21) || empty($acciones_21) || empty($estatus_21) || empty($fecha_fila_21)) {
        echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para la fila 2.1."]);
        exit;
    }
}

// Verificar si ya existe un registro con ese id_auditoria
$check_query = "SELECT id FROM auditoria_por_proceso WHERE id_auditoria = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("i", $id_auditoria);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Actualizar el registro existente
    $query = "UPDATE auditoria_por_proceso 
              SET estatus_21 = ?, responsable_21 = ?, fecha_fila_21 = ?, observaciones_21 = ?, acciones_21 = ?, archivo_21 = ?
              WHERE id_auditoria = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "ssssssi",
        $estatus_21, $responsable_21, $fecha_fila_21, $observaciones_21, $acciones_21, $ruta_archivo_21, $id_auditoria
    );
} else {
    // Insertar un nuevo registro con solo los campos de la fila 2.1
    $query = "INSERT INTO auditoria_por_proceso (
        id_auditoria, estatus_21, responsable_21, fecha_fila_21, observaciones_21, acciones_21, archivo_21
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "issssss",
        $id_auditoria, $estatus_21, $responsable_21, $fecha_fila_21, $observaciones_21, $acciones_21, $ruta_archivo_21
    );
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "✅ Datos de la fila 2.1 guardados correctamente.", "id" => $id_auditoria]);
} else {
    echo json_encode(["success" => false, "error" => "❌ Error en la ejecución: " . $stmt->error]);
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>