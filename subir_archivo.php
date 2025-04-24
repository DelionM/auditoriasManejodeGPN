<?php
header("Content-Type: application/json");
require "conexion.php"; // Asegúrate de que este archivo esté en la misma carpeta

// Verificar si se ha enviado un archivo y un número de empleado
if (!isset($_FILES["archivo"]) || !isset($_POST["numeroEmpleado"])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos."]);
    exit;
}

$numeroEmpleado = $_POST["numeroEmpleado"];
$archivo = $_FILES["archivo"];
$nombreArchivo = basename($archivo["name"]);
$directorioDestino = "uploads/";  // Carpeta donde se guardarán los archivos

// Crear la carpeta si no existe
if (!is_dir($directorioDestino)) {
    mkdir($directorioDestino, 0777, true);
}

// Evitar archivos duplicados
$rutaArchivo = $directorioDestino . time() . "_" . $nombreArchivo;

// Mover el archivo al servidor
if (move_uploaded_file($archivo["tmp_name"], $rutaArchivo)) {
    // 🚀 Guardar la ruta en la base de datos
    $query = "UPDATE auditorias SET nombre_archivo = ?, ruta_archivo = ?, fecha_subida = NOW() WHERE numero_empleado = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("sss", $nombreArchivo, $rutaArchivo, $numeroEmpleado);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Archivo subido y guardado correctamente."]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al guardar en la BD: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "Error al mover el archivo al servidor."]);
}

$conexion->close();
?>
