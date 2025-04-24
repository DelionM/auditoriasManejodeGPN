<?php
header("Content-Type: application/json");
require "conexion.php";

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "auditoria");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conn->connect_error]);
    exit;
}

// Obtener los datos enviados
$id_auditoria = $_POST["id"] ?? null;
$observacionesUnoDos = $_POST["observacionesUnoDos"] ?? null;
$accionesUnoDos = $_POST["accionesUnoDos"] ?? null;
$idProblemasUnoDos = $_POST["idProblemasUnoDos"] ?? null;
$estatusUnoDos = $_POST["estatusUnoDos"] ?? null;
$fechaFilaUnoDos = $_POST["fecha_filaUnoDos"] ?? null; // Ensure this matches the JS campo.nombre

// Debugging: Log received POST data
file_put_contents('debug.log', "POST recibidos: " . print_r($_POST, true) . "\n", FILE_APPEND);

$nombreArchivo = null;
$rutaArchivo = null;

// Verificación de archivo
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
        echo json_encode(["success" => false, "error" => "❌ Error al mover el archivo al directorio destino."]);
        exit;
    }
}

// Verificar que se recibieron todos los datos necesarios
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// Primero intentamos actualizar un registro existente
$query = "UPDATE auditorias SET 
    observacionesUnoDos = ?, 
    accionesUnoDos = ?,
    idProblemasUnoDos = ?,
    estatusUnoDos = ?, 
    fecha_filaUnoDos = ?, 
    nombre_archivoUnoDos = ?, 
    ruta_archivoUnoDos = ?, 
    fecha_subidaUnoDos = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

// Vincular los parámetros
$stmt->bind_param(
    "ssssssss",
    $observacionesUnoDos,
    $accionesUnoDos,
    $idProblemasUnoDos,
    $estatusUnoDos,
    $fechaFilaUnoDos,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

// Ejecutar la consulta
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 1.2 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no hay filas afectadas, insertamos un nuevo registro
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria,
            observacionesUnoDos,
            accionesUnoDos,
            idProblemasUnoDos,
            estatusUnoDos,
            fecha_filaUnoDos,
            nombre_archivoUnoDos,
            ruta_archivoUnoDos,
            fecha_subidaUnoDos
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param(
            "ssssssss",
            $id_auditoria,
            $observacionesUnoDos,
            $accionesUnoDos,
            $idProblemasUnoDos,
            $estatusUnoDos,
            $fechaFilaUnoDos,
            $nombreArchivo,
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 1.2 guardados correctamente.", "id" => $id_auditoria]);
        } else {
            echo json_encode(["success" => false, "error" => "❌ Error al insertar: " . $insertStmt->error]);
        }
        $insertStmt->close();
    }
} else {
    echo json_encode(["success" => false, "error" => "❌ Error en la ejecución de la consulta: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>