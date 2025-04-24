<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "auditoria");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conn->connect_error]);
    exit;
}

// Obtener datos del POST
$id_auditoria = $_POST["id"] ?? null;
$estatus_12 = $_POST["estatus12"] ?? "";
$responsable_12 = $_POST["responsable12"] ?? "";
$fecha_fila_12 = $_POST["fechaFila12"] ?? "";
$observaciones_12 = $_POST["observaciones12"] ?? "";
$acciones_12 = $_POST["acciones12"] ?? "";

// Validar id_auditoria
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ Falta el id_auditoria."]);
    exit;
}

// Subida de archivos
$nombre_archivo_12 = null;
$ruta_archivo_12 = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombre_limpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombre_archivo_12 = $nombre_limpio . "_" . time() . "." . $extension;

    $directorio_destino = "uploads/";
    if (!is_dir($directorio_destino)) {
        mkdir($directorio_destino, 0777, true);
    }

    $ruta_archivo_12 = $directorio_destino . $nombre_archivo_12;
    if (!move_uploaded_file($archivo["tmp_name"], $ruta_archivo_12)) {
        echo json_encode(["success" => false, "error" => "❌ Error al subir el archivo."]);
        exit;
    }
}

// Validaciones adicionales para la fila 1.2
if ($estatus_12 === "OK") {
    if (empty($fecha_fila_12)) {
        echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 1.2."]);
        exit;
    }
} elseif ($estatus_12 === "Pendiente" || $estatus_12 === "NOK") {
    if (empty($observaciones_12) || empty($acciones_12) || empty($estatus_12) || empty($fecha_fila_12)) {
        echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para la fila 1.2."]);
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
              SET estatus_12 = ?, responsable_12 = ?, fecha_fila_12 = ?, observaciones_12 = ?, acciones_12 = ?, archivo_12 = ?
              WHERE id_auditoria = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "ssssssi",
        $estatus_12, $responsable_12, $fecha_fila_12, $observaciones_12, $acciones_12, $ruta_archivo_12, $id_auditoria
    );
} else {
    // Insertar un nuevo registro con solo los campos de la fila 1.2 (y encabezado mínimo)
    $query = "INSERT INTO auditoria_por_proceso (
        id_auditoria, estatus_12, responsable_12, fecha_fila_12, observaciones_12, acciones_12, archivo_12
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "issssss",
        $id_auditoria, $estatus_12, $responsable_12, $fecha_fila_12, $observaciones_12, $acciones_12, $ruta_archivo_12
    );
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "✅ Datos de la fila 1.2 guardados correctamente.", "id" => $id_auditoria]);
} else {
    echo json_encode(["success" => false, "error" => "❌ Error en la ejecución: " . $stmt->error]);
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>