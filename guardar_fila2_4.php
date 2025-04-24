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
$id_auditoria = $_POST["id"] ?? null; // Renombramos a id_auditoria para claridad
$observacionesDosCuatro = $_POST["observacionesDosCuatro"] ?? null;
$accionesDosCuatro = $_POST["accionesDosCuatro"] ?? null;
$problema = $_POST["problemas"] ?? null;
$estatusDosCuatro = $_POST["estatusDosCuatro"] ?? null;
$fechaFilaDosCuatro = $_POST["fechaFilaDosCuatro"] ?? null;

// Validar si el estatus está vacío
if (empty($estatusDosCuatro)) {
    echo json_encode(['success' => false, 'error' => '❌ Error: Debes seleccionar un estado (OK, NOK o Pendiente) antes de guardar.']);
    exit;
}

// Manejo del archivo
$nombreArchivo = null;
$rutaArchivo = null;

if (isset($_FILES["archivo"]) && $_FILES["archivo"]["error"] === UPLOAD_ERR_OK) {
    $archivo = $_FILES["archivo"];
    
    // Sanitizar el nombre del archivo para evitar problemas con caracteres especiales
    $extension = pathinfo($archivo["name"], PATHINFO_EXTENSION);
    $nombreLimpio = preg_replace("/[^A-Za-z0-9_-]/", "", pathinfo($archivo["name"], PATHINFO_FILENAME));
    $nombreArchivo = $nombreLimpio . "_" . time() . "." . $extension;

    $directorioDestino = "uploads/";

    // Asegurar que el directorio exista
    if (!is_dir($directorioDestino)) {
        mkdir($directorioDestino, 0777, true);
    }

    $rutaArchivo = $directorioDestino . $nombreArchivo;

    // Mover el archivo al directorio de destino
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

// if ($estatusDosCuatro === "OK") {
//     if (empty($fechaFilaDosCuatro)) {
//         echo json_encode(["success" => false, "error" => "❌ Falta agregar la fecha en la fila 2.4."]);
//         exit;
//     }
// } elseif ($estatusDosCuatro === "Pendiente" || $estatusDosCuatro === "NOK") {
//     if (empty($observacionesDosCuatro) || empty($accionesDosCuatro) || empty($estatusDosCuatro) || empty($fechaFilaDosCuatro)) {
//         echo json_encode(["success" => false, "error" => "❌ Faltan datos obligatorios para Pendiente o NOK."]);
//         exit;
//     }
// }

// Primero intentamos actualizar un registro existente
$query = "UPDATE auditorias SET 
    observacionesDosCuatro = ?, 
    accionesDosCuatro = ?, 
    idProblemasDosCuatro = ?,
    estatusDosCuatro = ?, 
    fecha_filaDosCuatro = ?, 
    nombre_archivoDosCuatro = ?, 
    ruta_archivoDosCuatro = ?, 
    fecha_subidaDosCuatro = NOW()
    WHERE id_auditoria = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la consulta: " . $conn->error]);
    exit;
}

// Vincular los parámetros
$stmt->bind_param(
    "sssssssi",
    $observacionesDosCuatro,
    $accionesDosCuatro,
    $problema, 
    $estatusDosCuatro,
    $fechaFilaDosCuatro,
    $nombreArchivo,
    $rutaArchivo,
    $id_auditoria
);

// Ejecutar la consulta
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.4 actualizados correctamente.", "id" => $id_auditoria]);
    } else {
        // Si no hay filas afectadas, insertamos un nuevo registro
        $insertQuery = "INSERT INTO auditorias (
            id_auditoria,
            observacionesDosCuatro,
            accionesDosCuatro,
            idProblemasDosCuatro, 
            estatusDosCuatro,
            fecha_filaDosCuatro,
            nombre_archivoDosCuatro,
            ruta_archivoDosCuatro,
            fecha_subidaDosCuatro
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $insertStmt = $conn->prepare($insertQuery);
        if (!$insertStmt) {
            echo json_encode(["success" => false, "error" => "❌ Error en la preparación de la inserción: " . $conn->error]);
            exit;
        }

        $insertStmt->bind_param(
            "ssssssss",
            $id_auditoria,
            $observacionesDosCuatro,
            $accionesDosCuatro,
            $problema, 
            $estatusDosCuatro,
            $fechaFilaDosCuatro,
            $nombreArchivo,
            $rutaArchivo
        );

        if ($insertStmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "✅ Datos de fila 2.4 guardados correctamente.", "id" => $id_auditoria]);
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