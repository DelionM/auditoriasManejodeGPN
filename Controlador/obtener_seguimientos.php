<?php
include('../conexion.php');

header('Content-Type: application/json');

$response = ['success' => false, 'seguimientos' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_auditoria = $_GET['id_auditoria'] ?? '';
    $fila = $_GET['fila'] ?? '';

    if (empty($id_auditoria) || empty($fila)) {
        $response['message'] = 'ID de auditoría y fila son requeridos.';
        echo json_encode($response);
        exit;
    }

    $sql = "SELECT observaciones, acciones, fecha_seguimiento, nombre_archivo, ruta_archivo, fecha_subida, estatus_seguimiento 
            FROM seguimientos 
            WHERE id_auditoria = ? AND fila = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("is", $id_auditoria, $fila);
    $stmt->execute();
    $result = $stmt->get_result();

    $seguimientos = [];
    while ($row = $result->fetch_assoc()) {
        $seguimientos[] = $row;
    }

    $stmt->close();

    $response['success'] = true;
    $response['seguimientos'] = $seguimientos;
} else {
    $response['message'] = 'Método no permitido.';
}

$conexion->close();
echo json_encode($response);
?>