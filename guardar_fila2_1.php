<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "conexion.php";

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "auditoria");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conn->connect_error]);
    exit;
}

// Obtener los datos enviados
$id_auditoria = $_POST["id"] ?? null;
$observacionesDosUno = $_POST["observacionesDosUno"] ?? null;
$accionesDosUno = $_POST["accionesDosUno"] ?? null;
$idProblemasDosUno = $_POST["idProblemasDosUno"] ?? null; // Corregido para "Problemas Comunes"
$estatusDosUno = $_POST["estatusDosUno"] ?? null;
$fecha_filaDosUno = $_POST["fecha_filaDosUno"] ?? null; // Corregido para la fecha

// Validar si el estatus está vacío
if (empty($estatusDosUno)) {
    echo json_encode(['success' => false, 'error' => '❌ Error: Debes seleccionar un estado (OK, NOK o Pendiente) antes de guardar.']);
    exit;
}

// Manejo del archivo
$nombreArchivo = null;
$rutaArchivo = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombreLimpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombreArchivo = $nombreLimpio . "_" . time() . "." . $extension;
    $directorioDestino = "uploads/";

    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $rutaArchivo = $directorioDestino . $nombreArchivo;
    if (!move_uploaded_file($archivo["tmp_name"], $rutaArchivo)) {
        echo json_encode(["success" => false, "error" => "❌ Error al mover el archivo."]);
        exit;
    }
}

// Verificación de datos
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// Intentar actualizar el registro existente
$query = "UPDATE auditorias SET 
    observacionesDosUno = ?, 
    accionesDosUno = ?, 
    idProblemasDosUno = ?, 
    estatusDosUno = ?, 
    fecha_filaDosUno = ?, 
    nombre_archivoDosUno = ?, 
    ruta_archivoDosUno = ?, 
    fecha_subidaDosUno = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("ssssssss", 
    $observacionesDosUno, 
    $accionesDosUno, 
    $idProblemasDosUno, 
    $estatusDosUno, 
    $fecha_filaDosUno, 
    $nombreArchivo, 
    $rutaArchivo, 
    $id_auditoria
);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.1 actualizados correctamente.", "id" => $id_auditoria]);
} else {
    // Insertar nuevo registro si no se encontró uno existente
    $insertQuery = "INSERT INTO auditorias (
        id_auditoria, 
        observacionesDosUno, 
        accionesDosUno, 
        idProblemasDosUno, 
        estatusDosUno, 
        fecha_filaDosUno, 
        nombre_archivoDosUno, 
        ruta_archivoDosUno, 
        fecha_subidaDosUno
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $insertStmt = $conn->prepare($insertQuery);
    if (!$insertStmt) {
        echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
        exit;
    }

    $insertStmt->bind_param("ssssssss", 
        $id_auditoria, 
        $observacionesDosUno, 
        $accionesDosUno, 
        $idProblemasDosUno, 
        $estatusDosUno, 
        $fecha_filaDosUno, 
        $nombreArchivo, 
        $rutaArchivo
    );

    if ($insertStmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.1 guardados correctamente.", "id" => $id_auditoria]);
    } else {
        echo json_encode(["success" => false, "error" => "❌ Error al insertar: " . $insertStmt->error]);
    }
    $insertStmt->close();
}

$stmt->close();
$conn->close();
?>