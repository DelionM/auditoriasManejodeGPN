<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/php_errors.log'); // Adjust path as needed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$datos = json_decode($input, true);

if (!$datos || !is_array($datos)) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

// Extract data from JSON (matching JavaScript payload)
$id_auditoria = $datos['id_auditoria'] ?? '';
$fila = $datos['fila'] ?? '';
$correo_destinatario = $datos['correo'] ?? ''; // Changed from 'correo_destinatario' to 'correo'
$observaciones = $datos['observaciones'] ?? '';
$acciones = $datos['acciones'] ?? '';
$problemas = $datos['problemas'] ?? '';
$estatus = $datos['estatus'] ?? '';
$fecha = $datos['fecha'] ?? '';

// Validate required fields
if (empty($correo_destinatario)) {
    echo json_encode(['success' => false, 'message' => 'Correo del destinatario no proporcionado']);
    exit;
}

if (!filter_var($correo_destinatario, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo del destinatario inválido']);
    exit;
}

if (empty($id_auditoria) || empty($fila)) {
    echo json_encode(['success' => false, 'message' => 'ID de auditoría o fila no proporcionados']);
    exit;
}

// Map filas to audit items (aligned with JavaScript's filas array)
$audit_items = [
    'Uno' => [
        'id' => '1',
        'requirement' => 'Se encuentra la documentación técnica en la línea de Proceso ( caratula, diagrama de flujo, hoja de proceso, norma de empaque, plan de control)	',
        'instruction' => 'FIN 04,05,06,09 FIN 08'
    ],
    'UnoDos' => [
        'id' => '2',
        'requirement' => 'Los parámetros se encuentran de acuerdo a la hoja de proceso (deben a su vez coincidir con los anotados en el formato "hoja de control de parámetros")',
        'instruction' => 'FIN 30	'
    ],
    'UnoTres' => [
        'id' => '3',
        'requirement' => 'Se llevó a cabo la liberación del proceso y de primera pieza de manera correcta y validada por líder de celda	',
        'instruction' => 'FPR 23,24	'
    ],
    'Cuatro' => [
        'id' => '4',
        'requirement' => 'Se identifican correctamente los materiales (producto en proceso y producto no conforme)	',
        'instruction' => 'FAC 11,12	'
    ],
    'Cinco' => [
        'id' => '5',
        'requirement' => 'Se tiene delimitada el área de acuerdo al Lay Out y el Lay Out esta actualizado	',
        'instruction' => 'FIN 44	'
    ],
    'Seis' => [
        'id' => '6',
        'requirement' => 'Los herramentales e indicadores (manómetros,timer,display,termómetros, etc.)de la línea están identificados, en buenas condiciones, verificados y son funcionales	',
        'instruction' => 'FIN 34 FAC 43'
    ],
    'Siete' => [
        'id' => '7',
        'requirement' => 'Existen ayudas visuales de defectos de la pieza (catalogo de no conformidades)	',
        'instruction' => 'FPR 14	'
    ],
    'Ocho' => [
        'id' => '8',
        'requirement' => 'El área auditada esta limpia y ordenada (se cuenta con un plan de limpieza y esta documentado)	',
        'instruction' => 'FSH 32	'
    ],
    'Nueve' => [
        'id' => '9',
        'requirement' => 'Se encuentra el plan de mantenimiento preventivo y se realiza de acuerdo a lo programado	',
        'instruction' => 'FMT 03	'
    ],
    'Diez' => [
        'id' => '10',
        'requirement' => 'Se encuentra la ultima auditoria de capas y cuenta con sus acciones correctivas	',
        'instruction' => 'FAC 25	'
    ],
    'Once' => [
        'id' => '11',
        'requirement' => 'Los operadores realizan la operación como lo indica su HOJA DE PROCESO	',
        'instruction' => 'FIN 06	'
    ],
    'Doce' => [
        'id' => '12',
        'requirement' => 'Los operadores están informados sobre las reclamaciones y saben como manejar las piezas NOK	',
        'instruction' => 'FAC 52	'
    ],
    'Trece' => [
        'id' => '13',
        'requirement' => 'Los operadores conocen el plan de reacción en caso de falla conforme lo indicado el PLAN DE CONTROL	',
        'instruction' => 'FIN 08	'
    ],
    'Catorce' => [
        'id' => '14',
        'requirement' => 'El operador revisa sus piezas visualmente conforme a lo indicado en el PLAN DE CONTROL	',
        'instruction' => 'FIN 08	'
    ],
    'Quince' => [
        'id' => '15',
        'requirement' => 'Los empleados cuentan con su EPP completo contra la matriz de EPP	',
        'instruction' => 'FSH22'
    ],
    'Dieciseis' => [
        'id' => '16',
        'requirement' => 'Esta actualizada la matriz de habilidades	',
        'instruction' => 'FAD 14	'
    ],
    'Diecisiete' => [
        'id' => '17',
        'requirement' => 'El dispositivo cuenta con todos sus componentes, se encuentra limpio y en buen estado	',
        'instruction' => 'FAC 93	'
    ],
    'Dieciocho' => [
        'id' => '18',
        'requirement' => 'El dispositivo esta verificado y cuenta con el nivel de ingeniería correspondiente	',
        'instruction' => 'FAC 93, FIN 04'
    ],
    'Diecinueve' => [
        'id' => '19',
        'requirement' => 'El dispositivo cuenta con el instructivo de uso del mismo	',
        'instruction' => 'FAC 101	'
    ],
    'Veinte' => [
        'id' => '20',
        'requirement' => 'Esta identificada la materia prima correctamente (etiqueta de proveedor)	',
        'instruction' => 'VISUAL'
    ],
    'Veintiuno' => [
        'id' => '21',
        'requirement' => 'Se han anotado las materias primas en el control de carga de materias primas	',
        'instruction' => 'FPR 02	'
    ],
    'Veintidos' => [
        'id' => '22',
        'requirement' => 'La identificación del producto final para envío a cliente es legible. (Verificar las impresiones de etiqueta individual y SAP)	',
        'instruction' => 'VISUAL'
    ],
    'Veintitres' => [
        'id' => '23',
        'requirement' => 'Los materiales son colocados como lo indica la norma empaque liberada	',
        'instruction' => 'FIN 09	'
    ],
    'Veinticuatro' => [
        'id' => '24',
        'requirement' => 'Los contenedores se encuentran en buen estado (limpios, secos y sin roturas) y están libre de etiquetas obsoletas como lo indica la norma de empaque	',
        'instruction' => 'FIN 09	'
    ],
    'Veinticinco' => [
        'id' => '25',
        'requirement' => 'La identificación del producto final para envío a cliente es legible. (Verificar las impresiones de etiqueta individual y SAP)',
        'instruction' => 'VISUAL'
    ]
];

// Get audit item details
$audit_item = $audit_items[$fila] ?? [
    'id' => $fila,
    'requirement' => 'Desconocido',
    'instruction' => 'No disponible'
];

// Prepare email content
$status_message = ($estatus === 'NOK')
    ? '<p><strong>Estado:</strong> <span style="color: red;">NOK - Requiere corrección</span></p>'
    : "<p><strong>Estado:</strong> $estatus</p>";

$cuerpo = "
    <h2>Notificación de Auditoría</h2>
    <p>Le informamos que se ha identificado un punto en la auditoría por procesos que requiere atención. A continuación, los detalles:</p>
    <h3>Auditoría ID: $id_auditoria - Fila: {$audit_item['id']}</h3>
    <ul>
        <li><strong>Requerimiento:</strong> {$audit_item['requirement']}</li>
        <li><strong>Instrucción para corrección:</strong> {$audit_item['instruction']}</li>
        <li><strong>Observaciones:</strong> " . htmlspecialchars($observaciones) . "</li>
        <li><strong>Acciones:</strong> " . htmlspecialchars($acciones) . "</li>
        <li><strong>Problemas:</strong> " . htmlspecialchars($problemas) . "</li>
        $status_message
        <li><strong>Fecha compromiso de entrega:</strong> $fecha</li>
    </ul>
    <p>Por favor, tome las medidas necesarias para corregir este punto si aplica.</p>
    <p>Saludos,</p>
";

// Initialize PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'APG-NotReplayMX@adlerpelzer.com';
    $mail->Password = 'DXfem413';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('APG-NotReplayMX@adlerpelzer.com', 'Sistema de Auditorías');
    $mail->addAddress($correo_destinatario);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "Notificación de Auditoría - Fila {$audit_item['id']} (ID: $id_auditoria)";
    $mail->Body = $cuerpo;
    $mail->AltBody = strip_tags($cuerpo);

    // No image attachment since JavaScript doesn't send ruta_archivo
    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
}
?>