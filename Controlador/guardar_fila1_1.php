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
$proceso_auditado = $_POST["proceso_auditado"] ?? null;
$cliente = $_POST["cliente"] ?? null;
$no_parte = $_POST["no_parte"] ?? null;
$nave = $_POST["nave"] ?? null;
$nivel_ingenieria = $_POST["nivel_ingenieria"] ?? null;
$revision_fecha = $_POST["revision_fecha"] ?? null;
$nombre_auditor = $_POST["nombre_auditor"] ?? null;
$supervisor = $_POST["supervisor"] ?? null;
$fecha = $_POST["fecha"] ?? null;
$turno = $_POST["turno"] ?? null;
$hora = $_POST["hora"] ?? null;
$estatus_11 = $_POST["estatus11"] ?? null;
$responsable_11 = $_POST["responsable11"] ?? null;
$fecha_fila_11 = $_POST["fechaFila11"] ?? null;
$observaciones_11 = $_POST["observaciones11"] ?? null;
$acciones_11 = $_POST["acciones11"] ?? null;

// Subida de archivos
$nombre_archivo_11 = null;
$ruta_archivo_11 = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombre_limpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombre_archivo_11 = $nombre_limpio . "_" . time() . "." . $extension;

    $directorio_destino = "uploads/";
    if (!is_dir($directorio_destino)) {
        mkdir($directorio_destino, 0777, true);
    }

    $ruta_archivo_11 = $directorio_destino . $nombre_archivo_11;
    if (!move_uploaded_file($archivo["tmp_name"], $ruta_archivo_11)) {
        echo json_encode(["success" => false, "error" => "❌ Error al subir el archivo."]);
        exit;
    }
}

// Validaciones
if ($estatus_11 === "OK") {
    if (empty($fecha_fila_11)) {
        echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 1.1."]);
        exit;
    }
} elseif ($estatus_11 === "Pendiente" || $estatus_11 === "NOK") {
    if (empty($observaciones_11) || empty($acciones_11) || empty($estatus_11) || empty($fecha_fila_11)) {
        echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para la fila 1.1."]);
        exit;
    }
}

// Preparar e insertar los datos en la base de datos
$query = "INSERT INTO auditoria_por_proceso (
    id_auditoria, proceso_auditado, cliente, no_parte, nave, nivel_ingenieria, revision_fecha, 
    nombre_auditor, supervisor, fecha, turno, hora, estatus_11, responsable_11, fecha_fila_11, 
    observaciones_11, acciones_11, archivo_11, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param(
    "isssssssssssssssss",
    $id_auditoria, $proceso_auditado, $cliente, $no_parte, $nave, $nivel_ingenieria, $revision_fecha,
    $nombre_auditor, $supervisor, $fecha, $turno, $hora, $estatus_11, $responsable_11, $fecha_fila_11,
    $observaciones_11, $acciones_11, $ruta_archivo_11
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "✅ Datos guardados correctamente.", "id" => $id_auditoria]);
} else {
    echo json_encode(["success" => false, "error" => "❌ Error en la ejecución: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>