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

$id_auditoria = $datos['id_auditoria'] ?? '';
$fila = $datos['fila'] ?? '';
$observaciones = $datos['observaciones'] ?? '';
$acciones = $datos['acciones'] ?? '';
$problemas = $datos['problemas'] ?? '';
$estatus = $datos['estatus'] ?? '';
$fecha = $datos['fecha'] ?? '';
$correo_destinatario = $datos['correo_destinatario'] ?? '';
$ruta_archivo = $datos['ruta_archivo'] ?? '';

if (empty($correo_destinatario)) {
    echo json_encode(['success' => false, 'message' => 'Correo del destinatario no proporcionado']);
    exit;
}

if (!filter_var($correo_destinatario, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo del destinatario inválido']);
    exit;
}

$audit_items = [
    'UnoUno' => [
        'id' => '1.1',
        'requirement' => '¿Se han anotado todas las materias primas en el control de trazabilidad correspondiente?',
        'instruction' => 'Solicitar al supervisor el registro y verificar si todos los materiales que se encuentren en el área están anotados.'
    ],
    'UnoDos' => [
        'id' => '1.2',
        'requirement' => '¿Todos los materiales, empaques, dispositivos en el área de producción están en la ubicación correcta como lo indica el lay-out para evitar "contaminación"?',
        'instruction' => 'Los materiales deben de encontrarse dentro de las delimitaciones establecidas y de acuerdo al documento de lay out.'
    ],

    'UnoTres' => [
        'id' => '1.3',
        'requirement' => '¿Todos los materiales en el área de producción están correctamente
                            identificados de acuerdo a la hoja de proceso?',
        'instruction' => 'Verificar que todo el material del proceso se encuentre correctamente
                            identificado: Materia prima con etiqueta de SAP, Producto en Proceso, Material rechazado con
                            etiqueta roja, producto terminado, sin etiquetas obsoleta. Asegurar que los materiales
                            utilizados estén en la hoja de proceso e identificada la norma de empaque'
    ],

    'DosUno' => [
        'id' => '2.1',
        'requirement' => '¿El operador está certificado para realizar la operación de acuerdo a
                            la matriz de habilidades?',
        'instruction' => '¿El operador está certificado para realizar la operación de acuerdo a
                            la matriz de habilidades?'
    ],

    'DosDos' => [
        'id' => '2.2',
        'requirement' => '¿Se están llenando correctamente los reportes de control de producción en las frecuencias establecidas?',
        'instruction' => 'Verificar el formato de producción por hora que se encuentra en el tablero del proceso.	'
    ],
    'DosTres' => [
        'id' => '2.3',
        'requirement' => 'Verificar que el registro de Chequeo de maquinaria y equipo se encuentre con los registros al día	',
        'instruction' => 'Verificar que al arranque de la línea se haya realizado la liberación del proceso mediante el registro de chequeo de maquinaria y equipo y en caso de desviaciones se hayan tomado acciones.	'
    ],
    'DosCuatro' => [
        'id' => '2.4',
        'requirement' => 'La documentación técnica se encuentra disponible en el área de trabajo y es trazable con el diagrama de flujo (hoja de proceso y plan de control) y el operador registra parámetros como lo indica esta documentación	',
        'instruction' => 'Verificar que se encuentre en tablero de información el diagrama de flujo, hoja de proceso, plan de control y que estos documentos cuenten con la misma revisión. La hoja de proceso y plan de control deben tener los mismos procesos declarados en el diagrama de flujo. Revisar que los registros que indica el plan de control se encuentren correctamente llenados.	'
    ],
    'DosCinco' => [
        'id' => '2.5',
        'requirement' => '¿Si la estación auditada cuenta con un sistema de poka yokes, verificar que al arranque del proceso se realizó su revisión y están funcionando.	',
        'instruction' => 'Se solicita al operador el check list de verificación del poka yoke y se corrobora nuevamente su funcionamiento.	'
    ],
    'DosSeis' => [
        'id' => '2.6',
        'requirement' => '¿El personal conoce y usa el sistema de escalación en caso de fallas?	',
        'instruction' => 'Se pregunta al operador si sabe a quién o quiénes dirigirse en caso de fallas.	'
    ],
    'TresUno' => [
        'id' => '3.1',
        'requirement' => 'Se cuenta con la liberación de proceso al inicio de turno / arranque de la línea por el operador y es validada por el líder de celda?	',
        'instruction' => 'Verificar que en el dispositivo de control se encuentre el registro de la liberación de la primera pieza y este debidamente llenado y firmado por el operador y el líder de grupo	'
    ],
    'CuatroUno' => [
        'id' => '4.1',
        'requirement' => '¿Se encuentran en estado correcto de calibración y/o verificación los equipos de control necesarios para la operación?	',
        'instruction' => 'Verificar que el escantillón y los equipos donde se verifican parámetros no indiquen fecha de calibración y/o verificación vencida en su etiqueta de identificación.		'
    ],
    'CuatroDos' => [
        'id' => '4.2',
        'requirement' => '¿Si hay no conformidades en alguno de los controles de los tableros están documentadas y siendo tomadas las contramedidas?	',
        'instruction' => 'Si se encuentran parámetros fuera de especificación deben de existir anotaciones en los registros de acciones correctivas / bitácora de proceso	'
    ],
    'CuatroTres' => [
        'id' => '4.3',
        'requirement' => '¿Los materiales se encuentran estibados de manera que la calidad de la pieza no se vea afectada?		',
        'instruction' => 'Verificar si están estibadas de acuerdo al máximo indicado en hojas de proceso.	'
    ],
    'CincoUno' => [
        'id' => '5.1',
        'requirement' => '¿Se está utilizando el Equipo de Protección Personal indicado en la matriz de EPP?	',
        'instruction' => 'Solicitar al supervisor su matriz de EPP y verificar físicamente el uso del equipo en el operador.	'
    ],
    'CincoDos' => [
        'id' => '5.2',
        'requirement' => 'Los medios de seguridad incluyen equipos para el control de incendios, control de derrames de productos químicos, solventes, etc; Tales como: Hidrantes, extintores, lava ojos, regaderas, arena / acerrín para control de derrames, etc.		',
        'instruction' => 'En las áreas en donde se manejan materiales peligrosos se encuentran equipos que ayuden a mitigar un impacto causado por un incendio o derrame	'
    ],
    'CincoTres' => [
        'id' => '5.3',
        'requirement' => '¿El área está libre de riesgos de accidente (actos y condiciones inseguras)?	',
        'instruction' => 'Actos inseguros: actividades que hacen las personas que pueden ponerlas en riesgo de sufrir un accidente; Condición insegura: instalaciones, equipos y herramientas que no están en condiciones de ser usadas; los moldes en prensas y troqueles cuentan con toda la tornillería instalada en la partes superior e inferior y que pueden causar un accidente en su uso)	'
    ],
    'CincoCuatro' => [
        'id' => '5.4',
        'requirement' => '¿Existe en el área auditada un equipo contra incendio?	',
        'instruction' => 'Asegurar que estos equipos no deben encontrarse obstruidos	'
    ],  
    'CincoCinco' => [
        'id' => '5.5',
        'requirement' => 'Los controles de la maquinaria de producción operan adecuadamente (incluyendo paro de emergencia, guardas, y controles que protejan la integridad del operador) y el área se encuentra iluminada?	',
        'instruction' => 'Las condiciones de los controles o tableros de la maquinaria se encuentra en condiciones adecuadas de uso. Los controles de seguridad se encuentran operando adecuadamente (guardas sin ser bloqueadas, paro de emergencia, Sensores, etc;), la luz es adecuada para la operación		'
    ],
    'CincoSeis' => [
        'id' => '5.6',
        'requirement' => '¿El lugar de trabajo cumple con el estándar 5S (Eliminar-Ordenar-Limpiar-Estandarizar-Disciplina)?	',
        'instruction' => 'Verificar por ejemplo: que el área se encuentre limpia (sin derrames ni sobrantes en piso y maquinaria), ordenada (cada cosa de acuerdo a lay out e identificaciones) y estandarizada.	'
    ],
    'CincoSiete' => [
        'id' => '5.7',
        'requirement' => 'En caso de que aplique, ¿los químicos usados en el proceso están en el contenedor adecuado y correctamente identificados?	',
        'instruction' => 'El recipiente que contenga químicos debe de tener el pictograma de seguridad y el nombre del químico que almacena, verificar que no se utilizan recipientes de refrescos o similares para almacenar materiales químicos.	'
    ],
    'CincoOcho' => [
        'id' => '5.8',
        'requirement' => 'En caso de que aplique, ¿los residuos peligrosos son almacenados e identificados adecuadamente?	',
        'instruction' => 'La identificación de los contenedores de residuos es visible dentro de ellos no existe una mezcla de residuos (metales en contenedores de cartón o residuos peligrosos, residuos peligrosos en contenedores de cartón o metales, cartón en contenedores de cartón o residuos peligrosos).	'
    ],
    'SeisUno' => [
        'id' => '6.1',
        'requirement' => '¿El producto terminado es empacado de acuerdo a la hoja de empaque correspondiente con las etiquetas de liberación y SAP correctas? Si no, ¿se encuentra identificado con etiqueta de material en proceso?	',
        'instruction' => 'Solicitar al supervisor la hoja de empaque y verificar físicamente si el producto terminado está de acuerdo al documento.	'
    ],
];

$audit_item = $audit_items[$fila] ?? ['id' => $fila, 'requirement' => 'Desconocido', 'instruction' => 'No disponible'];

$status_message = ($estatus === 'NOK') ? '<p><strong>Estado:</strong> <span style="color: red;">NOK - Requiere corrección</span></p>' : "<p><strong>Estado:</strong> $estatus</p>";

$image_message = !empty($ruta_archivo) ? '<p><strong>Imagen adjunta:</strong> Se ha incluido una imagen para referencia.</p>' : '<p><strong>Imagen:</strong> No se adjuntó ninguna imagen.</p>';

$cuerpo = "
    <h2>Notificación de Auditoría</h2>
    <p>Le informamos que se ha identificado un punto en la auditoría de proceso por capas que requiere atención. A continuación, los detalles:</p>
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
    $image_message
    <p>Por favor, tome las medidas necesarias para corregir este punto si aplica. 
    <p>Saludos,</p>
";

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

    // Attach the image if it exists
    if (!empty($ruta_archivo)) {
        $base_dir = realpath(dirname(__FILE__) . '/..'); // Parent directory of Controlador/
        $file_path = str_replace('../', '', $ruta_archivo); // Remove '../' prefix
        $absolute_path = $base_dir . '/' . $file_path;

        error_log("Base dir: $base_dir"); // Log the base directory
        error_log("Ruta archivo recibida: $ruta_archivo"); // Log the received path
        error_log("File path: $file_path"); // Log the cleaned path
        error_log("Absolute path: $absolute_path"); // Log the full path
        error_log("File exists: " . (file_exists($absolute_path) ? 'Yes' : 'No')); // Log if file exists

        if (file_exists($absolute_path)) {
            $mail->addAttachment($absolute_path);
        } else {
            error_log("Image file not found: $absolute_path");
        }
    }

    $mail->send();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
}
?>