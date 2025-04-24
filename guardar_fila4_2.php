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

// Obtener los datos enviados desde el JavaScript
$id_auditoria = $_POST["id"] ?? null;
$observacionesCuatroDos = $_POST["observacionesCuatroDos"] ?? null;
$accionesCuatroDos = $_POST["accionesCuatroDos"] ?? null;
$idProblemasCuatroDos = $_POST["idProblemasCuatroDos"] ?? null;
$estatusCuatroDos = $_POST["estatusCuatroDos"] ?? null;
$fecha_filaCuatroDos = $_POST["fecha_filaCuatroDos"] ?? null;

// Manejo del archivo
$nombreArchivo = null;
$rutaArchivo = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    
    // Sanitizar el nombre del archivo
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

// Validar que se haya proporcionado el ID de auditoría
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// Preparar la consulta de actualización
$query = "UPDATE auditorias SET 
    observacionesCuatroDos = ?, 
    accionesCuatroDos = ?, 
    idProblemasCuatroDos = ?, 
    estatusCuatroDos = ?, 
    fecha_filaCuatroDos = ?, 
    nombre_archivoCuatroDos = ?, 
    ruta_archivoCuatroDos = ?, 
    fecha_subidaCuatroDos = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

// Vincular parámetros
$stmt->bind_param(
    "sssssssi",
    $observacionesCuatroDos,
    $accionesCuatroDos,
    $idProblemasCuatroDos,
    $estatusCuatroDos,
    $fecha_filaCuatroDos,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

// Ejecutar la consulta
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 4.2 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no hay filas afectadas, podría significar que no existe el registro, pero no insertamos aquí
        // porque este archivo es para actualizar, no para guardar nuevos datos
        echo json_encode(["success" => false, "error" => "❌ No se encontró el registro para actualizar con el ID: " . $id_auditoria]);
    }
} else {
    echo json_encode(["success" => false, "error" => "❌ Error al ejecutar la consulta: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>