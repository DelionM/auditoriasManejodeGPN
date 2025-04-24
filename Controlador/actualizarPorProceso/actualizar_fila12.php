<?php
header("Content-Type: application/json"); // Establecer el tipo de respuesta como JSON
require "../../conexion.php"; // Incluir la conexión a la base de datos

// Verificar si la conexión es válida
if (!$conexion) {
    echo json_encode(["success" => false, "error" => "❌ No se pudo conectar a la base de datos."]);
    exit;
}

// Obtener datos enviados en formato JSON desde el JavaScript
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Verificar si se decodificaron los datos correctamente
if ($data === null) {
    echo json_encode(["success" => false, "error" => "❌ Error al decodificar los datos JSON."]);
    exit;
}

// Extraer los valores enviados desde el JS
$id_auditoria = $data["id_auditoria"] ?? null;
$observaciones = $data["observaciones"] ?? null;
$acciones = $data["acciones"] ?? null;
$problemas = $data["problemas"] ?? null;
$estatus = $data["estatus"] ?? null;
$fecha = $data["fecha"] ?? null;

// Validar campo obligatorio
if (!$id_auditoria) {
    echo json_encode(["success" => false, "error" => "❌ Error: El ID de auditoría es obligatorio."]);
    exit;
}

// Query para actualizar la fila 12 (Doce) en la tabla auditoria_proceso
$query = "UPDATE auditoria_proceso SET 
            observacionesDoce = ?, 
            accionesDoce = ?, 
            idProblemasDoce = ?, 
            estatusDoce = ?, 
            fecha_filaDoce = ? 
          WHERE id_auditoria = ?";
$stmt = $conexion->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conexion->error]);
    exit;
}

// Vincular parámetros (sssssi: 5 strings y 1 integer)
$stmt->bind_param("sssssi", 
    $observaciones, 
    $acciones, 
    $problemas, 
    $estatus, 
    $fecha, 
    $id_auditoria
);

// Ejecutar consulta
if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "✅ Fila Doce actualizada correctamente."]);
} else {
    echo json_encode(["success" => false, "error" => "❌ Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>