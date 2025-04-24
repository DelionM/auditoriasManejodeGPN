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
$observacionesUnoTres = $_POST["observacionesUnoTres"] ?? null;
$accionesUnoTres = $_POST["accionesUnoTres"] ?? null;
$idProblemasUnoTres = $_POST["idProblemasUnoTres"] ?? null; // Corrected to match JS
$estatusUnoTres = $_POST["estatusUnoTres"] ?? null;
$fecha_filaUnoTres = $_POST["fecha_filaUnoTres"] ?? null; // Corrected to match JS (case-sensitive)

// Validar si el estatus está vacío
if (empty($estatusUnoTres)) {
    echo json_encode(['success' => false, 'error' => '❌ Error: Debes seleccionar un estado (OK, NOK o Pendiente) antes de guardar.']);
    exit;
}

// Debugging: Log received POST data
// file_put_contents('debug.log', "POST recibidos: " . print_r($_POST, true) . "\n", FILE_APPEND);

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

// Verificar que se recibieron todos los datos necesarios
if (empty($id_auditoria)) {
    echo json_encode(["success" => false, "error" => "❌ No se proporcionó un ID de auditoría válido."]);
    exit;
}

// Opcional: Validar datos según estatus (descomentado para pruebas, ajusta según necesidad)
// if ($estatusUnoTres === "OK") {
//     if (empty($fecha_filaUnoTres)) {
//         echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 1.3."]);
//         exit;
//     }
// } elseif ($estatusUnoTres === "Pendiente" || $estatusUnoTres === "NOK") {
//     if (empty($observacionesUnoTres) || empty($accionesUnoTres) || empty($estatusUnoTres) || empty($fecha_filaUnoTres)) {
//         echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para Pendiente o NOK."]);
//         exit;
//     }
// }

// Primero intentamos actualizar un registro existente
$query = "UPDATE auditorias SET 
    observacionesUnoTres = ?, 
    accionesUnoTres = ?, 
    idProblemasUnoTres = ?, 
    estatusUnoTres = ?, 
    fecha_filaUnoTres = ?, 
    nombre_archivoUnoTres = ?, 
    ruta_archivoUnoTres = ?, 
    fecha_subidaUnoTres = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

// Vincular los parámetros
$stmt->bind_param(
    "ssssssss",
    $observacionesUnoTres,
    $accionesUnoTres,
    $idProblemasUnoTres,
    $estatusUnoTres,
    $fecha_filaUnoTres,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

// Ejecutar la consulta
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 1.3 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no hay filas afectadas, insertamos un nuevo registro
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria,
            observacionesUnoTres,
            accionesUnoTres,
            idProblemasUnoTres,
            estatusUnoTres,
            fecha_filaUnoTres,
            nombre_archivoUnoTres,
            ruta_archivoUnoTres,
            fecha_subidaUnoTres
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param(
            "ssssssss", // Fixed: Added one 's' for all 8 parameters
            $id_auditoria,
            $observacionesUnoTres,
            $accionesUnoTres,
            $idProblemasUnoTres,
            $estatusUnoTres,
            $fecha_filaUnoTres,
            $nombreArchivo,
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 1.3 guardados correctamente.", "id" => $id_auditoria]);
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