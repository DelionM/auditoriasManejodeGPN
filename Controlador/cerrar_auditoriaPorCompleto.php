<?php
header('Content-Type: application/json');
require '../conexion.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_auditoria'])) {
    $id_auditoria = $_POST['id_auditoria'];

    // Update the status to 'Cerrada' in the programar_auditoria table
    $sql = "UPDATE programar_auditoria SET estatus = 'Cerrada' WHERE id_auditoria = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id_auditoria);

    if ($stmt->execute()) {
        // Optionally update the auditorias table if needed
        $sql_auditorias = "UPDATE auditorias SET estatus_cierre = 'Cerrado' WHERE id_auditoria = ?";
        $stmt_auditorias = $conexion->prepare($sql_auditorias);
        $stmt_auditorias->bind_param('i', $id_auditoria);
        $stmt_auditorias->execute();

        $response['success'] = true;
        $response['message'] = 'Auditoría cerrada exitosamente';
    } else {
        $response['message'] = 'Error al actualizar el estatus en la base de datos';
    }

    $stmt->close();
    if (isset($stmt_auditorias)) $stmt_auditorias->close();
} else {
    $response['message'] = 'Solicitud inválida';
}

$conexion->close();
echo json_encode($response);
?>