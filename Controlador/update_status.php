<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_auditoria = isset($_POST['id_auditoria']) ? $_POST['id_auditoria'] : null;
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : null;

    if ($id_auditoria && $estatus) {
        // Verificar el estatus actual en programar_auditoria
        $sql = "SELECT estatus FROM programar_auditoria WHERE id_auditoria = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error preparando SELECT: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param("i", $id_auditoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'No se encontró la auditoría con ID: ' . $id_auditoria]);
            exit();
        }

        $current_status = $row['estatus'];
        error_log("Estatus actual de $id_auditoria: $current_status"); // Depuración

        if ($current_status === 'Asignada') {
            // Iniciar transacción para asegurar consistencia entre las tablas
            $conn->begin_transaction();

            try {
                // Actualizar el estatus en programar_auditoria
                $sql_update_status = "UPDATE programar_auditoria SET estatus = ? WHERE id_auditoria = ?";
                $stmt_status = $conn->prepare($sql_update_status);
                if (!$stmt_status) {
                    throw new Exception('Error preparando UPDATE de estatus: ' . $conn->error);
                }
                $stmt_status->bind_param("si", $estatus, $id_auditoria);
                $stmt_status->execute();

                // Si el estatus es "Proceso", actualizar fecha_inicio_proceso en auditorias
                if ($estatus === 'Proceso') {
                    // Verificar si existe un registro en auditorias, si no, crearlo
                    $sql_check_auditoria = "SELECT id_auditoria FROM auditorias WHERE id_auditoria = ?";
                    $stmt_check = $conn->prepare($sql_check_auditoria);
                    $stmt_check->bind_param("i", $id_auditoria);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows == 0) {
                        // Insertar un nuevo registro en auditorias si no existe
                        $sql_insert_auditoria = "INSERT INTO auditorias (id_auditoria, fecha_inicio_proceso) VALUES (?, CURRENT_TIMESTAMP)";
                        $stmt_insert = $conn->prepare($sql_insert_auditoria);
                        if (!$stmt_insert) {
                            throw new Exception('Error preparando INSERT en auditorias: ' . $conn->error);
                        }
                        $stmt_insert->bind_param("i", $id_auditoria);
                        $stmt_insert->execute();
                        $stmt_insert->close();
                    } else {
                        // Actualizar fecha_inicio_proceso si el registro ya existe
                        $sql_update_fecha = "UPDATE auditorias SET fecha_inicio_proceso = CURRENT_TIMESTAMP WHERE id_auditoria = ?";
                        $stmt_fecha = $conn->prepare($sql_update_fecha);
                        if (!$stmt_fecha) {
                            throw new Exception('Error preparando UPDATE de fecha: ' . $conn->error);
                        }
                        $stmt_fecha->bind_param("i", $id_auditoria);
                        $stmt_fecha->execute();
                        $stmt_fecha->close();
                    }
                    $stmt_check->close();
                }

                // Confirmar transacción
                $conn->commit();
                error_log("Estatus actualizado a $estatus para $id_auditoria"); // Depuración
                echo json_encode(['success' => true, 'message' => "Estatus actualizado a '$estatus'"]);

            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
            }

            // Cerrar statements
            $stmt_status->close();
        } else {
            echo json_encode(['success' => false, 'message' => "El estatus actual es '$current_status', no se actualizó a '$estatus'"]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos: id_auditoria o estatus no recibidos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido, se esperaba POST']);
}

$conn->close();
?>