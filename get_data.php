<?php
require 'conexion.php';

$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';
$filtro_fechas = '';

if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $filtro_fechas = " WHERE fecha_programada BETWEEN ? AND ?";
} elseif (!empty($fecha_inicio)) {
    $filtro_fechas = " WHERE fecha_programada >= ?";
} elseif (!empty($fecha_fin)) {
    $filtro_fechas = " WHERE fecha_programada <= ?";
}

// Función para ejecutar consultas seguras
function ejecutarConsulta($conexion, $sql, $params = []) {
    if (empty($params)) {
        $result = $conexion->query($sql);
    } else {
        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            $types = str_repeat('s', count($params)); // Asumimos que todos los parámetros son strings
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            return false;
        }
    }
    return $result ? $result : false;
}

// Consultas para nuevas gráficas
$sql_total = "SELECT COUNT(*) as total FROM programar_auditoria" . $filtro_fechas;
$params = [];
if (!empty($fecha_inicio)) $params[] = $fecha_inicio;
if (!empty($fecha_fin)) $params[] = $fecha_fin;

$result = ejecutarConsulta($conexion, $sql_total, $params);
$total_nuevas = $result ? $result->fetch_assoc()['total'] : 0;

$sql_asignadas = "SELECT COUNT(*) as asignadas_nuevas FROM programar_auditoria" . $filtro_fechas . (!empty($filtro_fechas) ? " AND" : " WHERE") . " estatus = 'Asignada'";
$result = ejecutarConsulta($conexion, $sql_asignadas, $params);
$asignadas_nuevas = $result ? $result->fetch_assoc()['asignadas_nuevas'] : 0;

$sql_en_proceso = "SELECT COUNT(*) as en_proceso_nuevas FROM programar_auditoria" . $filtro_fechas . (!empty($filtro_fechas) ? " AND" : " WHERE") . " estatus = 'Proceso'";
$result = ejecutarConsulta($conexion, $sql_en_proceso, $params);
$en_proceso_nuevas = $result ? $result->fetch_assoc()['en_proceso_nuevas'] : 0;

$sql_realizadas = "SELECT COUNT(*) as realizadas_nuevas FROM programar_auditoria" . $filtro_fechas . (!empty($filtro_fechas) ? " AND" : " WHERE") . " estatus = 'Realizada'";
$result = ejecutarConsulta($conexion, $sql_realizadas, $params);
$realizadas_nuevas = $result ? $result->fetch_assoc()['realizadas_nuevas'] : 0;

$sql_cerradas = "SELECT COUNT(*) as cerradas_nuevas FROM programar_auditoria" . $filtro_fechas . (!empty($filtro_fechas) ? " AND" : " WHERE") . " estatus = 'Cerrada'";
$result = ejecutarConsulta($conexion, $sql_cerradas, $params);
$cerradas_nuevas = $result ? $result->fetch_assoc()['cerradas_nuevas'] : 0;

$sql_capas = "SELECT COUNT(*) as capas_nuevas FROM programar_auditoria" . $filtro_fechas . (!empty($filtro_fechas) ? " AND" : " WHERE") . " LOWER(tipo_auditoria) = 'auditoria por capas'";
$result = ejecutarConsulta($conexion, $sql_capas, $params);
$capas_nuevas = $result ? $result->fetch_assoc()['capas_nuevas'] : 0;

$sql_procesos = "SELECT COUNT(*) as procesos_nuevas FROM programar_auditoria" . $filtro_fechas . (!empty($filtro_fechas) ? " AND" : " WHERE") . " LOWER(tipo_auditoria) = 'auditoria por procesos'";
$result = ejecutarConsulta($conexion, $sql_procesos, $params);
$procesos_nuevas = $result ? $result->fetch_assoc()['procesos_nuevas'] : 0;

// Auditorías por semana (nuevas)
$sql_total_semanas_nuevas = "SELECT semana, 
                             SUM(CASE WHEN estatus = 'Realizada' THEN 1 ELSE 0 END) as realizadas, 
                             SUM(CASE WHEN estatus = 'Cerrada' THEN 1 ELSE 0 END) as cerradas, 
                             COUNT(*) as programadas 
                             FROM programar_auditoria 
                             " . (!empty($filtro_fechas) ? $filtro_fechas . " AND" : "WHERE") . " semana IS NOT NULL 
                             GROUP BY semana 
                             ORDER BY semana";
$result_total_semanas_nuevas = ejecutarConsulta($conexion, $sql_total_semanas_nuevas, $params);
$total_semanas_labels_nuevas = [];
$total_semanas_programadas_nuevas = [];
$total_semanas_realizadas_nuevas = [];
$total_semanas_cerradas_nuevas = [];
if ($result_total_semanas_nuevas) {
    while ($row = $result_total_semanas_nuevas->fetch_assoc()) {
        $total_semanas_labels_nuevas[] = "Semana " . $row['semana'];
        $total_semanas_programadas_nuevas[] = $row['programadas'];
        $total_semanas_realizadas_nuevas[] = $row['realizadas'];
        $total_semanas_cerradas_nuevas[] = $row['cerradas'];
    }
}

// Auditorías por semana (Capas) - nuevas
$sql_capas_semanas_nuevas = "SELECT semana, 
                             SUM(CASE WHEN estatus = 'Realizada' THEN 1 ELSE 0 END) as realizadas, 
                             SUM(CASE WHEN estatus = 'Cerrada' THEN 1 ELSE 0 END) as cerradas, 
                             COUNT(*) as programadas 
                             FROM programar_auditoria 
                             WHERE LOWER(tipo_auditoria) = 'auditoria por capas' 
                             " . (!empty($filtro_fechas) ? "AND " . substr($filtro_fechas, 7) . " AND" : "AND") . " semana IS NOT NULL 
                             GROUP BY semana 
                             ORDER BY semana";
$result_capas_semanas_nuevas = ejecutarConsulta($conexion, $sql_capas_semanas_nuevas, $params);
$capas_semanas_labels_nuevas = [];
$capas_semanas_programadas_nuevas = [];
$capas_semanas_realizadas_nuevas = [];
$capas_semanas_cerradas_nuevas = [];
if ($result_capas_semanas_nuevas) {
    while ($row = $result_capas_semanas_nuevas->fetch_assoc()) {
        $capas_semanas_labels_nuevas[] = "Semana " . $row['semana'];
        $capas_semanas_programadas_nuevas[] = $row['programadas'];
        $capas_semanas_realizadas_nuevas[] = $row['realizadas'];
        $capas_semanas_cerradas_nuevas[] = $row['cerradas'];
    }
}

// Auditorías por semana (Procesos) - nuevas
$sql_procesos_semanas_nuevas = "SELECT semana, 
                                SUM(CASE WHEN estatus = 'Realizada' THEN 1 ELSE 0 END) as realizadas, 
                                SUM(CASE WHEN estatus = 'Cerrada' THEN 1 ELSE 0 END) as cerradas, 
                                COUNT(*) as programadas 
                                FROM programar_auditoria 
                                WHERE LOWER(tipo_auditoria) = 'auditoria por procesos' 
                                " . (!empty($filtro_fechas) ? "AND " . substr($filtro_fechas, 7) . " AND" : "AND") . " semana IS NOT NULL 
                                GROUP BY semana 
                                ORDER BY semana";
$result_procesos_semanas_nuevas = ejecutarConsulta($conexion, $sql_procesos_semanas_nuevas, $params);
$procesos_semanas_labels_nuevas = [];
$procesos_semanas_programadas_nuevas = [];
$procesos_semanas_realizadas_nuevas = [];
$procesos_semanas_cerradas_nuevas = [];
if ($result_procesos_semanas_nuevas) {
    while ($row = $result_procesos_semanas_nuevas->fetch_assoc()) {
        $procesos_semanas_labels_nuevas[] = "Semana " . $row['semana'];
        $procesos_semanas_programadas_nuevas[] = $row['programadas'];
        $procesos_semanas_realizadas_nuevas[] = $row['realizadas'];
        $procesos_semanas_cerradas_nuevas[] = $row['cerradas'];
    }
}

// Top 5 problemas más comunes
$sql_top_problemas = "SELECT idProblemas AS id_problema, COUNT(*) AS frecuencia 
                      FROM (SELECT idProblemas FROM auditorias 
                            UNION ALL 
                            SELECT idProblemas FROM auditoria_proceso) AS combined 
                      " . $filtro_fechas . " 
                      GROUP BY idProblemas 
                      ORDER BY frecuencia DESC 
                      LIMIT 5";
$result_top_problemas = ejecutarConsulta($conexion, $sql_top_problemas, $params);
$top_problemas = [];
if ($result_top_problemas) {
    while ($row = $result_top_problemas->fetch_assoc()) {
        $top_problemas[] = $row;
    }
}

// Responder con JSON
echo json_encode([
    'total_nuevas' => $total_nuevas,
    'asignadas_nuevas' => $asignadas_nuevas,
    'en_proceso_nuevas' => $en_proceso_nuevas,
    'realizadas_nuevas' => $realizadas_nuevas,
    'cerradas_nuevas' => $cerradas_nuevas,
    'total_semanas_labels_nuevas' => $total_semanas_labels_nuevas,
    'total_semanas_programadas_nuevas' => $total_semanas_programadas_nuevas,
    'total_semanas_realizadas_nuevas' => $total_semanas_realizadas_nuevas,
    'total_semanas_cerradas_nuevas' => $total_semanas_cerradas_nuevas,
    'capas_semanas_labels_nuevas' => $capas_semanas_labels_nuevas,
    'capas_semanas_programadas_nuevas' => $capas_semanas_programadas_nuevas,
    'capas_semanas_realizadas_nuevas' => $capas_semanas_realizadas_nuevas,
    'capas_semanas_cerradas_nuevas' => $capas_semanas_cerradas_nuevas,
    'procesos_semanas_labels_nuevas' => $procesos_semanas_labels_nuevas,
    'procesos_semanas_programadas_nuevas' => $procesos_semanas_programadas_nuevas,
    'procesos_semanas_realizadas_nuevas' => $procesos_semanas_realizadas_nuevas,
    'procesos_semanas_cerradas_nuevas' => $procesos_semanas_cerradas_nuevas,
    'top_problemas' => $top_problemas,
    'capas_nuevas' => $capas_nuevas,
    'procesos_nuevas' => $procesos_nuevas
]);
?>