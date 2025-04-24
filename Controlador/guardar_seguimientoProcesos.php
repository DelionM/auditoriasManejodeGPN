<?php
include('../conexion.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_auditoria = $_POST['id_auditoria'] ?? '';
    $fila = $_POST['fila'] ?? ''; // 'UnoUno' para el modal de la fila 1.1
    $accion = $_POST['accion'] ?? '';
    $observacion = $_POST['observacion'] ?? '';
    $fecha = $_POST['fecha'] ?? '';

    // Validar campos obligatorios
    if (empty($id_auditoria) || empty($fila) || empty($accion) || empty($observacion) || empty($fecha)) {
        $response['message'] = 'Todos los campos obligatorios deben estar completos.';
        echo json_encode($response);
        exit;
    }

    // Manejo del archivo
    $rutaArchivo = '';
    $nombreArchivo = '';
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/seguimiento/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['archivo']['name']);
        $rutaArchivo = $uploadDir . $fileName;
        $nombreArchivo = $_FILES['archivo']['name'];
        if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaArchivo)) {
            $response['message'] = 'Error al subir el archivo.';
            echo json_encode($response);
            exit;
        }
    }

    // Insertar en la tabla seguimientos
    $sql = "INSERT INTO seguimiento_proceso (id_auditoria, fila, observaciones, acciones, fecha_seguimiento, nombre_archivo, ruta_archivo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("issssss", $id_auditoria, $fila, $observacion, $accion, $fecha, $nombreArchivo, $rutaArchivo);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Seguimiento guardado correctamente.';
    } else {
        $response['message'] = 'Error al guardar en la base de datos: ' . $stmt->error;
    }

    $stmt->close();
} else {
    $response['message'] = 'Método no permitido.';
}

$conexion->close();
echo json_encode($response);
?>