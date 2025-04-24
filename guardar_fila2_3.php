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
$observacionesDosTres = $_POST["observacionesDosTres"] ?? null;
$accionesDosTres = $_POST["accionesDosTres"] ?? null;
$problema = $_POST["idProblemasDosTres"] ?? null; // Ajustado para coincidir con JS
$estatusDosTres = $_POST["estatusDosTres"] ?? null;
$fecha_filaDosTres = $_POST["fecha_filaDosTres"] ?? null; // Corregido para coincidir con JS

// Validar si el estatus está vacío
if (empty($estatusDosTres)) {
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

// Verificar datos obligatorios
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// // Validaciones basadas en el estatus
// if ($estatusDosTres === "OK") {
//     if (empty($fecha_filaDosTres)) {
//         echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 2.3."]);
//         exit;
//     }
// } elseif ($estatusDosTres === "Pendiente" || $estatusDosTres === "NOK") {
//     if (empty($observacionesDosTres) || empty($accionesDosTres) || empty($estatusDosTres) || empty($fecha_filaDosTres)) {
//         echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para Pendiente o NOK en la fila 2.3."]);
//         exit;
//     }
// }

// Intentar actualizar primero
$query = "UPDATE auditorias SET 
    observacionesDosTres = ?, 
    accionesDosTres = ?, 
    idProblemasDosTres = ?, 
    estatusDosTres = ?, 
    fecha_filaDosTres = ?, 
    nombre_archivoDosTres = ?, 
    ruta_archivoDosTres = ?, 
    fecha_subidaDosTres = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssssss",
    $observacionesDosTres,
    $accionesDosTres,
    $problema,
    $estatusDosTres,
    $fecha_filaDosTres,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.3 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no se actualizó nada, insertar un nuevo registro
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria,
            observacionesDosTres,
            accionesDosTres,
            idProblemasDosTres,
            estatusDosTres,
            fecha_filaDosTres,
            nombre_archivoDosTres,
            ruta_archivoDosTres,
            fecha_subidaDosTres
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param(
            "ssssssss",
            $id_auditoria,
            $observacionesDosTres,
            $accionesDosTres,
            $problema,
            $estatusDosTres,
            $fecha_filaDosTres,
            $nombreArchivo,
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.3 guardados correctamente.", "id" => $id_auditoria]);
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

<!-- 965277619
5515806436 j 
-->