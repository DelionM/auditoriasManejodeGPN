<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desactiva la salida de errores en pantalla
ini_set('log_errors', 1); // Activa el registro de errores
ini_set('error_log', 'php_errors.log'); // Define un archivo de log

$conn = new mysqli("localhost", "root", "", "auditoria");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conn->connect_error]);
    exit;
}

// Obtener datos del POST
$id_auditoria = $_POST["id"] ?? null;
$estatus_13 = $_POST["estatus13"] ?? ""; // Corregido de estatus_12 a estatus_13
$responsable_13 = $_POST["responsable13"] ?? ""; // Corregido de responsable_12 a responsable_13
$fecha_fila_13 = $_POST["fechaFila13"] ?? ""; // Corregido de fechaFila12 a fechaFila13
$observaciones_13 = $_POST["observaciones13"] ?? ""; // Corregido de observaciones_12 a observaciones_13
$acciones_13 = $_POST["acciones13"] ?? ""; // Corregido de acciones_12 a acciones_13

// Validar id_auditoria
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ Falta el id_auditoria."]);
    exit;
}

// Subida de archivos
$nombre_archivo_13 = null;
$ruta_archivo_13 = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombre_limpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombre_archivo_13 = $nombre_limpio . "_" . time() . "." . $extension; // Corregido de _12 a _13

    $directorio_destino = "uploads/";
    if (!is_dir($directorio_destino)) {
        mkdir($directorio_destino, 0777, true);
    }

    $ruta_archivo_13 = $directorio_destino . $nombre_archivo_13; // Usa el nombre correcto
    if (!move_uploaded_file($archivo["tmp_name"], $ruta_archivo_13)) {
        echo json_encode(["success" => false, "error" => "❌ Error al subir el archivo."]);
        exit;
    }
}

// Validaciones adicionales para la fila 1.3
if ($estatus_13 === "OK") {
    if (empty($fecha_fila_13)) {
        echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 1.3."]);
        exit;
    }
} elseif ($estatus_13 === "Pendiente" || $estatus_13 === "NOK") {
    if (empty($observaciones_13) || empty($acciones_13) || empty($estatus_13) || empty($fecha_fila_13)) {
        echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para la fila 1.3."]);
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
              SET estatus_13 = ?, responsable_13 = ?, fecha_fila_13 = ?, observaciones_13 = ?, acciones_13 = ?, archivo_13 = ?
              WHERE id_auditoria = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "ssssssi",
        $estatus_13, $responsable_13, $fecha_fila_13, $observaciones_13, $acciones_13, $ruta_archivo_13, $id_auditoria
    );
} else {
    // Insertar un nuevo registro con solo los campos de la fila 1.3
    $query = "INSERT INTO auditoria_por_proceso (
        id_auditoria, estatus_13, responsable_13, fecha_fila_13, observaciones_13, acciones_13, archivo_13
    ) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
        exit;
    }

    $stmt->bind_param(
        "issssss",
        $id_auditoria, $estatus_13, $responsable_13, $fecha_fila_13, $observaciones_13, $acciones_13, $ruta_archivo_13
    );
}

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "✅ Datos de la fila 1.3 guardados correctamente.", "id" => $id_auditoria]);
} else {
    echo json_encode(["success" => false, "error" => "❌ Error en la ejecución: " . $stmt->error]);
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>