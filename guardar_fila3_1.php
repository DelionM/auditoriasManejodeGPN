<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
require "conexion.php";

$conn = new mysqli("localhost", "root", "", "auditoria");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "❌ Error de conexión: " . $conn->connect_error]);
    exit;
}

$id_auditoria = $_POST["id"] ?? null;
$observacionesTresUno = $_POST["observacionesTresUno"] ?? null;
$accionesTresUno = $_POST["accionesTresUno"] ?? null;
$problema = $_POST["idProblemasTresUno"] ?? null;
$estatusTresUno = $_POST["estatusTresUno"] ?? null;
$fecha_filaTresUno = $_POST["fecha_filaTresUno"] ?? null;

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

if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// if ($estatusTresUno === "OK") {
//     if (empty($fecha_filaTresUno)) {
//         echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 3.1."]);
//         exit;
//     }
// } elseif ($estatusTresUno === "Pendiente" || $estatusTresUno === "NOK") {
//     if (empty($observacionesTresUno) || empty($accionesTresUno) || empty($estatusTresUno) || empty($fecha_filaTresUno)) {
//         echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para Pendiente o NOK en la fila 3.1."]);
//         exit;
//     }
// }

$query = "UPDATE auditorias SET 
    observacionesTresUno = ?, 
    accionesTresUno = ?, 
    idProblemasTresUno = ?, 
    estatusTresUno = ?, 
    fecha_filaTresUno = ?, 
    nombre_archivoTresUno = ?, 
    ruta_archivoTresUno = ?, 
    fecha_subidaTresUno = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssssss",
    $observacionesTresUno,
    $accionesTresUno,
    $problema,
    $estatusTresUno,
    $fecha_filaTresUno,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 3.1 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria,
            observacionesTresUno,
            accionesTresUno,
            idProblemasTresUno,
            estatusTresUno,
            fecha_filaTresUno,
            nombre_archivoTresUno,
            ruta_archivoTresUno,
            fecha_subidaTresUno
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param(
            "ssssssss",
            $id_auditoria,
            $observacionesTresUno,
            $accionesTresUno,
            $problema,
            $estatusTresUno,
            $fecha_filaTresUno,
            $nombreArchivo,
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 3.1 guardados correctamente.", "id" => $id_auditoria]);
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