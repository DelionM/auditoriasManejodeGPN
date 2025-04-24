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
$observacionesCuatroTres = $_POST["observacionesCuatroTres"] ?? null;
$accionesCuatroTres = $_POST["accionesCuatroTres"] ?? null;
$problema = $_POST["idProblemasCuatroTres"] ?? null;
$estatusCuatroTres = $_POST["estatusCuatroTres"] ?? null;
$fechaFilaCuatroTres = $_POST["fecha_filaCuatroTres"] ?? null;

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

// Verificar que se recibió un ID válido
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// Verificar si el id_auditoria existe en programar_auditoria
$checkQuery = "SELECT id_auditoria FROM programar_auditoria WHERE id_auditoria = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("i", $id_auditoria);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows == 0) {
    echo json_encode(["success" => false, "error" => "❌ El ID de auditoría no existe en programar_auditoria."]);
    $checkStmt->close();
    $conn->close();
    exit;
}
$checkStmt->close();

// Intentar actualizar primero
$query = "UPDATE auditorias SET 
    observacionesCuatroTres = ?, 
    accionesCuatroTres = ?, 
    idProblemasCuatroTres = ?,
    estatusCuatroTres = ?, 
    fecha_filaCuatroTres = ?, 
    nombre_archivoCuatroTres = ?, 
    ruta_archivoCuatroTres = ?
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param("sssssssi", 
    $observacionesCuatroTres, 
    $accionesCuatroTres, 
    $problema, 
    $estatusCuatroTres, 
    $fechaFilaCuatroTres, 
    $nombreArchivo, 
    $rutaArchivo, 
    $id_auditoria
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Fila 4.3 actualizada correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no se actualizó nada, insertar un nuevo registro
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria, 
            observacionesCuatroTres, 
            accionesCuatroTres, 
            idProblemasCuatroTres, 
            estatusCuatroTres, 
            fecha_filaCuatroTres, 
            nombre_archivoCuatroTres, 
            ruta_archivoCuatroTres
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param("isssssss", 
            $id_auditoria, 
            $observacionesCuatroTres, 
            $accionesCuatroTres,
            $problema, 
            $estatusCuatroTres, 
            $fechaFilaCuatroTres, 
            $nombreArchivo, 
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Fila 4.3 guardada correctamente.", "id" => $id_auditoria]);
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
