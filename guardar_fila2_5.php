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
$observacionesDosCinco = $_POST["observacionesDosCinco"] ?? null;
$accionesDosCinco = $_POST["accionesDosCinco"] ?? null;
$problema = $_POST["idProblemasDosCinco"] ?? null; // Corregido para coincidir con JS
$estatusDosCinco = $_POST["estatusDosCinco"] ?? null;
$fecha_filaDosCinco = $_POST["fecha_filaDosCinco"] ?? null; // Corregido para coincidir con JS

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

// Verificar datos obligatorios
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

if (empty($estatusDosCinco)) {
    echo json_encode(["success" => false, "error" => "❌ Debe elegir un estatus."]);
    exit;
}

// Validaciones basadas en el estatus
// if ($estatusDosCinco === "OK") {
//     if (empty($fecha_filaDosCinco)) {
//         echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 2.5."]);
//         exit;
//     }
// } elseif ($estatusDosCinco === "Pendiente" || $estatusDosCinco === "NOK") {
//     if (empty($observacionesDosCinco) || empty($accionesDosCinco) || empty($estatusDosCinco) || empty($fecha_filaDosCinco)) {
//         echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para Pendiente o NOK en la fila 2.5."]);
//         exit;
//     }
// }

// Intentar actualizar primero
$query = "UPDATE auditorias SET 
    observacionesDosCinco = ?, 
    accionesDosCinco = ?, 
    idProblemasDosCinco = ?, 
    estatusDosCinco = ?, 
    fecha_filaDosCinco = ?, 
    nombre_archivoDosCinco = COALESCE(?, nombre_archivoDosCinco), 
    ruta_archivoDosCinco = COALESCE(?, ruta_archivoDosCinco), 
    fecha_subidaDosCinco = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssssss",
    $observacionesDosCinco,
    $accionesDosCinco,
    $problema,
    $estatusDosCinco,
    $fecha_filaDosCinco,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.5 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no se actualizó nada, insertar un nuevo registro
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria,
            observacionesDosCinco,
            accionesDosCinco,
            idProblemasDosCinco,
            estatusDosCinco,
            fecha_filaDosCinco,
            nombre_archivoDosCinco,
            ruta_archivoDosCinco,
            fecha_subidaDosCinco
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param(
            "ssssssss",
            $id_auditoria,
            $observacionesDosCinco,
            $accionesDosCinco,
            $problema,
            $estatusDosCinco,
            $fecha_filaDosCinco,
            $nombreArchivo,
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.5 guardados correctamente.", "id" => $id_auditoria]);
        } else {
            echo json_encode(["success" => false, "error" => "❌ Error al insertar: " . $insertStmt->error]);
        }
        $insertStmt->close();
    }
} else {
    echo json_encode(["success" => false, "error" => "❌ Error al ejecutar la consulta: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>