<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "../conexion.php";

if (!$conexion) {
    echo json_encode(["success" => false, "error" => "❌ No se pudo conectar a la base de datos."]);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data === null) {
    echo json_encode(["success" => false, "error" => "❌ Error al decodificar los datos JSON."]);
    exit;
}

$id_auditoria = $data["id_auditoria"] ?? null;
$observaciones = $data["observaciones"] ?? null;
$acciones = $data["acciones"] ?? null;
$problemas = $data["problemas"] ?? null;

$estatus = $data["estatus"] ?? null;
$fechaFila = $data["fecha"] ?? null;

if (!$id_auditoria) {
    echo json_encode(["success" => false, "error" => "❌ Error: El ID de auditoría es obligatorio."]);
    exit;
}

$query = "UPDATE auditorias SET 
            observacionesCincoCinco = ?, 
            accionesCincoCinco = ?, 
                        idProblemasCincoCinco = ?, 

            estatusCincoCinco = ?, 
            fecha_filaCincoCinco = ? 
          WHERE id_auditoria = ?";
$stmt = $conexion->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conexion->error]);
    exit;
}

$stmt->bind_param("sssssi", 
    $observaciones, 
    $acciones, 
    $problemas, 

    $estatus, 
    $fechaFila, 
    $id_auditoria
);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "mensaje" => "✅ Fila 5.5 actualizada correctamente."]);
} else {
    echo json_encode(["success" => false, "error" => "❌ Error al actualizar: " . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>