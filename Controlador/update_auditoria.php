<?php
require '../vendor/autoload.php';
include_once '../check_access.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_auditoria = $_POST['id_auditoria'];
    $num_colaborador = $_POST['num_colaborador'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $nave = $_POST['nave'];
    $descripcion = $_POST['descripcion'];
    $proyecto = $_POST['proyecto'];
    $cliente = $_POST['cliente'];
    $tipo_auditoria = $_POST['tipo_auditoria'];
    $semana = $_POST['semana'];

    // Verificar si el colaborador existe
    $check_employee_sql = "SELECT COUNT(*) as count FROM empleados WHERE numero_empleado = ?";
    if ($stmt = $conexion->prepare($check_employee_sql)) {
        $stmt->bind_param("s", $num_colaborador);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee_data = $result->fetch_assoc();

        if ($employee_data['count'] == 0) {
            echo "<script>
                    alert('El colaborador con número $num_colaborador no existe. Debes registrarlo primero.');
                    window.location.href = '../registrar_nuevo_usuario.php';
                  </script>";
            $stmt->close();
            $conexion->close();
            exit();
        }
        $stmt->close();
    }

    // Actualizar en la base de datos
    $query = "UPDATE programar_auditoria 
              SET numero_empleado = ?, nombre = ?, nave = ?, descripcion = ?, proyecto = ?, cliente = ?, 
                  tipo_auditoria = ?, semana = ?, correo = ?
              WHERE id_auditoria = ?";
    
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param('sssssssssi', $num_colaborador, $nombre, $nave, $descripcion, $proyecto, $cliente, $tipo_auditoria, $semana, $correo, $id_auditoria);
        if ($stmt->execute()) {
            // Enviar correo
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'brayan.delion@adlerpelzer.com';
                $mail->Password = 'qnpwjtnwfddwswkd';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->setFrom('brayan.delion@adlerpelzer.com', 'Sistema de Auditorías');
                $mail->addAddress($correo, $nombre);
                $mail->Subject = 'Actualización de Auditoría Programada';
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Body = "
                    <h2>Notificación de Actualización de Auditoría</h2>
                    <p>Estimado(a) <b>$nombre</b>,</p>
                    <p>Le informamos que la auditoría con folio <b>$id_auditoria</b> ha sido actualizada con los siguientes detalles:</p>
                    <ul>
                        <li><b>Número de Colaborador:</b> $num_colaborador</li>
                        <li><b>Nave:</b> $nave</li>
                        <li><b>Descripción:</b> $descripcion</li>
                        <li><b>Proyecto:</b> $proyecto</li>
                        <li><b>Cliente:</b> $cliente</li>
                        <li><b>Tipo de Auditoría:</b> $tipo_auditoria</li>
                        <li><b>Número de Semana:</b> $semana</li>
                        <li><b>Estatus:</b> Asignada</li>
                    </ul>
                    <p>Para realizar la auditoría, por favor ingrese al siguiente enlace:</p>
                    <p><a href='http://192.168.61.21/TablaChecklistCasi/TablaChecklist/login.php' target='_blank'>Acceder al Sistema</a></p>
                    <p>Saludos.</p>
                ";
                $mail->send();
            } catch (Exception $e) {
                // Manejo de error silencioso, pero podrías agregar un log aquí
            }
            echo "<script>
                    alert('Auditoría actualizada exitosamente.');
                    window.location.href = '../Vista/ver_auditorias_programadas.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Error al actualizar los datos: " . addslashes($stmt->error) . "');




                  </script>";
        }
        $stmt->close();
    }
    $conexion->close();
} else {
    header("Location: ../ver_auditorias_programadas.php");
    exit();
}
?>



<!--                      window.location.href = '../ver_auditorias_programadas.php';
 -->