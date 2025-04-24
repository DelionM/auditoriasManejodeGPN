<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Conexión fallida: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_auditoria = isset($_POST['id_auditoria']) ? $_POST['id_auditoria'] : null;
    $estatus = isset($_POST['estatus']) ? $_POST['estatus'] : null;

    error_log("Datos recibidos: id_auditoria=$id_auditoria, estatus=$estatus");

    if ($id_auditoria && $estatus) {
        $sql = "SELECT estatus FROM programar_auditoria WHERE id_auditoria = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando SELECT: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Error preparando SELECT: ' . $conn->error]);
            exit();
        }
        $stmt->bind_param("i", $id_auditoria);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            error_log("No se encontró auditoría con ID: $id_auditoria");
            echo json_encode(['success' => false, 'message' => 'No se encontró la auditoría con ID: ' . $id_auditoria]);
            exit();
        }

        $current_status = $row['estatus'];
        error_log("Estatus actual de $id_auditoria: $current_status");

        if ($current_status === 'Asignada') {
            $conn->begin_transaction();

            try {
                // Actualizar el estatus en programar_auditoria
                $sql_update_status = "UPDATE programar_auditoria SET estatus = ? WHERE id_auditoria = ?";
                $stmt_status = $conn->prepare($sql_update_status);
                if (!$stmt_status) {
                    throw new Exception('Error preparando UPDATE: ' . $conn->error);
                }
                $stmt_status->bind_param("si", $estatus, $id_auditoria);
                if (!$stmt_status->execute()) {
                    throw new Exception('Error ejecutando UPDATE: ' . $stmt_status->error);
                }
                error_log("UPDATE ejecutado para id_auditoria=$id_auditoria, estatus=$estatus");

                if ($estatus === 'Proceso') {
                    $sql_check = "SELECT id_auditoria FROM auditoria_proceso WHERE id_auditoria = ?";
                    $stmt_check = $conn->prepare($sql_check);
                    $stmt_check->bind_param("i", $id_auditoria);
                    $stmt_check->execute();
                    $result_check = $stmt_check->get_result();

                    if ($result_check->num_rows == 0) {
                        // Insertar en auditoria_proceso con tuvo_nok inicializado en FALSE
                        $sql_insert = "INSERT INTO auditoria_proceso (id_auditoria, fecha_inicio_proceso, tuvo_nok) VALUES (?, CURRENT_TIMESTAMP, FALSE)";
                        $stmt_insert = $conn->prepare($sql_insert);
                        if (!$stmt_insert) {
                            throw new Exception('Error preparando INSERT: ' . $conn->error);
                        }
                        $stmt_insert->bind_param("i", $id_auditoria);
                        $stmt_insert->execute();
                        error_log("INSERT ejecutado en auditoria_proceso para id_auditoria=$id_auditoria con tuvo_nok=FALSE");
                        $stmt_insert->close();
                    } else {
                        // Actualizar fecha_inicio_proceso, pero mantener tuvo_nok como está
                        $sql_update = "UPDATE auditoria_proceso SET fecha_inicio_proceso = CURRENT_TIMESTAMP WHERE id_auditoria = ?";
                        $stmt_update = $conn->prepare($sql_update);
                        if (!$stmt_update) {
                            throw new Exception('Error preparando UPDATE fecha: ' . $conn->error);
                        }
                        $stmt_update->bind_param("i", $id_auditoria);
                        $stmt_update->execute();
                        error_log("UPDATE fecha ejecutado en auditoria_proceso para id_auditoria=$id_auditoria");
                        $stmt_update->close();
                    }
                    $stmt_check->close();

                    // Verificar si ya hay NOKs y actualizar tuvo_nok si es necesario
                    $sql_nok_check = "SELECT 
                        SUM(
                            CASE WHEN estatusUno = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDos = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusTres = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusCuatro = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusCinco = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusSeis = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusSiete = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusOcho = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusNueve = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDiez = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusOnce = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDoce = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusTrece = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusCatorce = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusQuince = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDieciseis = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDiecisiete = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDieciocho = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusDiecinueve = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusVeinte = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusVeintiuno = 'NOK' THEN 1 ELSE 0 END +
                            CASE WHEN estatusVeintidos = 'NOK' THEN 1 ELSE 0 END
                        ) as nok_count
                        FROM auditoria_proceso 
                        WHERE id_auditoria = ?";
                    $stmt_nok = $conn->prepare($sql_nok_check);
                    $stmt_nok->bind_param("i", $id_auditoria);
                    $stmt_nok->execute();
                    $result_nok = $stmt_nok->get_result();
                    $nok_row = $result_nok->fetch_assoc();
                    $nok_count = $nok_row['nok_count'] ?? 0;

                    if ($nok_count > 0) {
                        $sql_update_nok = "UPDATE auditoria_proceso SET tuvo_nok = TRUE WHERE id_auditoria = ?";
                        $stmt_update_nok = $conn->prepare($sql_update_nok);
                        $stmt_update_nok->bind_param("i", $id_auditoria);
                        $stmt_update_nok->execute();
                        error_log("tuvo_nok actualizado a TRUE para id_auditoria=$id_auditoria (nok_count=$nok_count)");
                        $stmt_update_nok->close();
                    }
                    $stmt_nok->close();
                }

                $conn->commit();
                error_log("Estatus actualizado a $estatus para $id_auditoria");
                echo json_encode(['success' => true, 'message' => "Estatus actualizado a '$estatus'"]);
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error en transacción: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()]);
            }
            $stmt_status->close();
        } else {
            error_log("No se actualizó: estatus actual es $current_status");
            echo json_encode(['success' => false, 'message' => "El estatus actual es '$current_status', no se actualizó a '$estatus'"]);
        }
        $stmt->close();
    } else {
        error_log("Datos incompletos: id_auditoria=$id_auditoria, estatus=$estatus");
        echo json_encode(['success' => false, 'message' => 'Datos incompletos: id_auditoria o estatus no recibidos']);
    }
} else {
    error_log("Método no permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Método no permitido, se esperaba POST']);
}

$conn->close();
?>