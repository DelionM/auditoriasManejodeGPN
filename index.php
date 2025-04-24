<?php
include_once 'Controlador/check_access.php';
require 'conexion.php';

// Solo superadmin, admin y auditor pueden acceder a esta página
check_permission(['superadmin', 'admin', 'auditor']);

// Habilitar reporte de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener el mes seleccionado del formulario (si existe) para las gráficas originales y la tabla de problemas
$mes_filtro = isset($_GET['mes']) ? $_GET['mes'] : 'total';
$filtro_mes = ($mes_filtro !== 'total') ? " WHERE MONTH(fecha_programada) = " . intval($mes_filtro) : "";
$filtro_mes_problemas = ($mes_filtro !== 'total') ? " WHERE MONTH(fecha) = " . intval($mes_filtro) : "";
// Obtener filtros de fechas iniciales para las nuevas gráficas (si existen)
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

// Obtener el nombre del empleado que inició sesión
$numero_empleado_sesion = $_SESSION['numero_empleado'] ?? null;
$nombre_empleado = "Usuario desconocido";
if ($numero_empleado_sesion) {
    $query_nombre = "SELECT nombre FROM empleados WHERE numero_empleado = ?";
    if ($stmt = $conexion->prepare($query_nombre)) {
        $stmt->bind_param("s", $numero_empleado_sesion);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nombre_empleado = $row['nombre'];
        }
        $stmt->close();
    }
}

// Datos para las gráficas originales (con filtro por mes)
$result_original = $conexion->query("SELECT COUNT(*) as total FROM programar_auditoria $filtro_mes");
if ($result_original === false) die("Error en total_auditorias: " . $conexion->error);
$total_auditorias = $result_original->fetch_assoc()['total'];

$result_original = $conexion->query("SELECT COUNT(*) as asignadas FROM programar_auditoria $filtro_mes " . ($filtro_mes ? "AND" : "WHERE") . " estatus = 'Asignada'");
if ($result_original === false) die("Error en asignadas: " . $conexion->error);
$asignadas = $result_original->fetch_assoc()['asignadas'];

$result_original = $conexion->query("SELECT COUNT(*) as en_proceso FROM programar_auditoria $filtro_mes " . ($filtro_mes ? "AND" : "WHERE") . " estatus = 'Proceso'");
if ($result_original === false) die("Error en en_proceso: " . $conexion->error);
$en_proceso = $result_original->fetch_assoc()['en_proceso'];

$result_original = $conexion->query("SELECT COUNT(*) as realizadas FROM programar_auditoria $filtro_mes " . ($filtro_mes ? "AND" : "WHERE") . " estatus = 'Realizada'");
if ($result_original === false) die("Error en realizadas: " . $conexion->error);
$realizadas = $result_original->fetch_assoc()['realizadas'];

$result_original = $conexion->query("SELECT COUNT(*) as cerradas FROM programar_auditoria $filtro_mes " . ($filtro_mes ? "AND" : "WHERE") . " estatus = 'Cerrada'");
if ($result_original === false) die("Error en cerradas: " . $conexion->error);
$cerradas = $result_original->fetch_assoc()['cerradas'];

$result_original = $conexion->query("SELECT COUNT(*) as capas FROM programar_auditoria $filtro_mes " . ($filtro_mes ? "AND" : "WHERE") . " LOWER(tipo_auditoria) = 'auditoria por capas'");
if ($result_original === false) die("Error en capas: " . $conexion->error);
$capas = $result_original->fetch_assoc()['capas'];

$result_original = $conexion->query("SELECT COUNT(*) as procesos FROM programar_auditoria $filtro_mes " . ($filtro_mes ? "AND" : "WHERE") . " LOWER(tipo_auditoria) = 'auditoria por procesos'");
if ($result_original === false) die("Error en procesos: " . $conexion->error);
$procesos = $result_original->fetch_assoc()['procesos'];

// Auditorías por semana (originales)
$sql_total_semanas = "SELECT semana, 
                      SUM(CASE WHEN estatus = 'Realizada' THEN 1 ELSE 0 END) as realizadas, 
                      SUM(CASE WHEN estatus = 'Cerrada' THEN 1 ELSE 0 END) as cerradas, 
                      COUNT(*) as programadas 
                      FROM programar_auditoria 
                      " . ($filtro_mes !== '' ? $filtro_mes . " AND" : "WHERE") . " semana IS NOT NULL 
                      GROUP BY semana 
                      ORDER BY semana"; 
$result_total_semanas = $conexion->query($sql_total_semanas);
if ($result_total_semanas === false) die("Error en total_semanas: " . $conexion->error);
$total_semanas_labels = [];
$total_semanas_programadas = [];
$total_semanas_realizadas = [];
$total_semanas_cerradas = [];
while ($row = $result_total_semanas->fetch_assoc()) {
    $total_semanas_labels[] = "Semana " . $row['semana'];
    $total_semanas_programadas[] = $row['programadas'];
    $total_semanas_realizadas[] = $row['realizadas'];
    $total_semanas_cerradas[] = $row['cerradas'];
}

// Auditorías por semana (Capas) - originales
$sql_capas_semanas = "SELECT semana, 
                      SUM(CASE WHEN estatus = 'Realizada' THEN 1 ELSE 0 END) as realizadas, 
                      SUM(CASE WHEN estatus = 'Cerrada' THEN 1 ELSE 0 END) as cerradas, 
                      COUNT(*) as programadas 
                      FROM programar_auditoria 
                      WHERE LOWER(tipo_auditoria) = 'auditoria por capas' 
                      " . ($filtro_mes !== '' ? "AND " . substr($filtro_mes, 7) . " AND" : "AND") . " semana IS NOT NULL 
                      GROUP BY semana 
                      ORDER BY semana";
$result_capas_semanas = $conexion->query($sql_capas_semanas);
if ($result_capas_semanas === false) die("Error en capas_semanas: " . $conexion->error);
$capas_semanas_labels = [];
$capas_semanas_programadas = [];
$capas_semanas_realizadas = [];
$capas_semanas_cerradas = [];
while ($row = $result_capas_semanas->fetch_assoc()) {
    $capas_semanas_labels[] = "Semana " . $row['semana'];
    $capas_semanas_programadas[] = $row['programadas'];
    $capas_semanas_realizadas[] = $row['realizadas'];
    $capas_semanas_cerradas[] = $row['cerradas'];
}

// Auditorías por semana (Procesos) - originales
$sql_procesos_semanas = "SELECT semana, 
                         SUM(CASE WHEN estatus = 'Realizada' THEN 1 ELSE 0 END) as realizadas, 
                         SUM(CASE WHEN estatus = 'Cerrada' THEN 1 ELSE 0 END) as cerradas, 
                         COUNT(*) as programadas 
                         FROM programar_auditoria 
                         WHERE LOWER(tipo_auditoria) = 'auditoria por procesos' 
                         " . ($filtro_mes !== '' ? "AND " . substr($filtro_mes, 7) . " AND" : "AND") . " semana IS NOT NULL 
                         GROUP BY semana 
                         ORDER BY semana";
$result_procesos_semanas = $conexion->query($sql_procesos_semanas);
if ($result_procesos_semanas === false) die("Error en procesos_semanas: " . $conexion->error);
$procesos_semanas_labels = [];
$procesos_semanas_programadas = [];
$procesos_semanas_realizadas = [];
$procesos_semanas_cerradas = [];
while ($row = $result_procesos_semanas->fetch_assoc()) {
    $procesos_semanas_labels[] = "Semana " . $row['semana'];
    $procesos_semanas_programadas[] = $row['programadas'];
    $procesos_semanas_realizadas[] = $row['realizadas'];
    $procesos_semanas_cerradas[] = $row['cerradas'];
}

// Fecha actual dinámica
$current_date = new DateTime();
$current_year = $current_date->format('Y');
$current_week = $current_date->format('W');
$current_week_display = "{$current_year}-W" . str_pad($current_week, 2, "0", STR_PAD_LEFT);

// Calcular fechas de inicio y fin de la semana actual
$start_date = clone $current_date;
$start_date->setISODate($current_year, $current_week, 1); // Lunes
$end_date = clone $start_date;
$end_date->modify('+6 days'); // Domingo

$start_date_str = $start_date->format('Y-m-d');
$end_date_str = $end_date->format('Y-m-d');

// Calcular las próximas 2 semanas
$week_number = (int) $current_week;
$start_week = $current_week_display;
$end_week = "{$current_year}-W" . str_pad($week_number + 2, 2, "0", STR_PAD_LEFT);

// Query para Auditorías Próximas y Vencidas (originales)
$sql_upcoming = "
    SELECT pa.id_auditoria, pa.numero_empleado, e.nombre, pa.nave, pa.proyecto, pa.cliente, pa.semana, 
           pa.fecha_programada, pa.estatus, pa.tipo_auditoria
    FROM programar_auditoria pa
    JOIN empleados e ON pa.numero_empleado = e.numero_empleado
    WHERE pa.estatus = 'Asignada'
    AND pa.semana BETWEEN '$start_week' AND '$end_week'
    " . ($filtro_mes ? "AND " . substr($filtro_mes, 7) : "") . "
    ORDER BY pa.semana ASC, pa.fecha_programada ASC";
$result_upcoming = $conexion->query($sql_upcoming);
if ($result_upcoming === false) die("Error en upcoming audits: " . $conexion->error);

$sql_overdue = "
    SELECT pa.id_auditoria, pa.numero_empleado, e.nombre, pa.nave, pa.proyecto, pa.cliente, pa.semana, 
           pa.fecha_programada, pa.estatus, pa.tipo_auditoria
    FROM programar_auditoria pa
    JOIN empleados e ON pa.numero_empleado = e.numero_empleado
    WHERE pa.estatus = 'Asignada'
    AND pa.semana < '$start_week'
    " . ($filtro_mes ? "AND " . substr($filtro_mes, 7) : "") . "
    ORDER BY pa.semana ASC";
$result_overdue = $conexion->query($sql_overdue);
if ($result_overdue === false) die("Error en overdue audits: " . $conexion->error);

// Consulta para problemas más comunes (por mes)
$sql_total_problemas = "
    SELECT COUNT(*) as total 
    FROM (
        SELECT idProblemasUnoUno AS id_problema FROM auditorias $filtro_mes_problemas AND idProblemasUnoUno IS NOT NULL AND idProblemasUnoUno != ''
        UNION ALL
        SELECT idProblemasUnoDos FROM auditorias $filtro_mes_problemas AND idProblemasUnoDos IS NOT NULL AND idProblemasUnoDos != ''
        UNION ALL
        SELECT idProblemasUnoTres FROM auditorias $filtro_mes_problemas AND idProblemasUnoTres IS NOT NULL AND idProblemasUnoTres != ''
        UNION ALL
        SELECT idProblemasDosUno FROM auditorias $filtro_mes_problemas AND idProblemasDosUno IS NOT NULL AND idProblemasDosUno != ''
        UNION ALL
        SELECT idProblemasDosDos FROM auditorias $filtro_mes_problemas AND idProblemasDosDos IS NOT NULL AND idProblemasDosDos != ''
        UNION ALL
        SELECT idProblemasDosTres FROM auditorias $filtro_mes_problemas AND idProblemasDosTres IS NOT NULL AND idProblemasDosTres != ''
        UNION ALL
        SELECT idProblemasDosCuatro FROM auditorias $filtro_mes_problemas AND idProblemasDosCuatro IS NOT NULL AND idProblemasDosCuatro != ''
        UNION ALL
        SELECT idProblemasDosCinco FROM auditorias $filtro_mes_problemas AND idProblemasDosCinco IS NOT NULL AND idProblemasDosCinco != ''
        UNION ALL
        SELECT idProblemasDosSeis FROM auditorias $filtro_mes_problemas AND idProblemasDosSeis IS NOT NULL AND idProblemasDosSeis != ''
        UNION ALL
        SELECT idProblemasTresUno FROM auditorias $filtro_mes_problemas AND idProblemasTresUno IS NOT NULL AND idProblemasTresUno != ''
        UNION ALL
        SELECT idProblemasCuatroUno FROM auditorias $filtro_mes_problemas AND idProblemasCuatroUno IS NOT NULL AND idProblemasCuatroUno != ''
        UNION ALL
        SELECT idProblemasCuatroDos FROM auditorias $filtro_mes_problemas AND idProblemasCuatroDos IS NOT NULL AND idProblemasCuatroDos != ''
        UNION ALL
        SELECT idProblemasCuatroTres FROM auditorias $filtro_mes_problemas AND idProblemasCuatroTres IS NOT NULL AND idProblemasCuatroTres != ''
        UNION ALL
        SELECT idProblemasCincoUno FROM auditorias $filtro_mes_problemas AND idProblemasCincoUno IS NOT NULL AND idProblemasCincoUno != ''
        UNION ALL
        SELECT idProblemasCincoDos FROM auditorias $filtro_mes_problemas AND idProblemasCincoDos IS NOT NULL AND idProblemasCincoDos != ''
        UNION ALL
        SELECT idProblemasCincoTres FROM auditorias $filtro_mes_problemas AND idProblemasCincoTres IS NOT NULL AND idProblemasCincoTres != ''
        UNION ALL
        SELECT idProblemasCincoCuatro FROM auditorias $filtro_mes_problemas AND idProblemasCincoCuatro IS NOT NULL AND idProblemasCincoCuatro != ''
        UNION ALL
        SELECT idProblemasCincoCinco FROM auditorias $filtro_mes_problemas AND idProblemasCincoCinco IS NOT NULL AND idProblemasCincoCinco != ''
        UNION ALL
        SELECT idProblemasCincoSeis FROM auditorias $filtro_mes_problemas AND idProblemasCincoSeis IS NOT NULL AND idProblemasCincoSeis != ''
        UNION ALL
        SELECT idProblemasCincoSiete FROM auditorias $filtro_mes_problemas AND idProblemasCincoSiete IS NOT NULL AND idProblemasCincoSiete != ''
        UNION ALL
        SELECT idProblemasCincoOcho FROM auditorias $filtro_mes_problemas AND idProblemasCincoOcho IS NOT NULL AND idProblemasCincoOcho != ''
        UNION ALL
        SELECT idProblemasSeisUno FROM auditorias $filtro_mes_problemas AND idProblemasSeisUno IS NOT NULL AND idProblemasSeisUno != ''
        UNION ALL
        SELECT idProblemasUno FROM auditoria_proceso $filtro_mes_problemas AND idProblemasUno IS NOT NULL AND idProblemasUno != ''
        UNION ALL
        SELECT idProblemasDos FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDos IS NOT NULL AND idProblemasDos != ''
        UNION ALL
        SELECT idProblemasTres FROM auditoria_proceso $filtro_mes_problemas AND idProblemasTres IS NOT NULL AND idProblemasTres != ''
        UNION ALL
        SELECT idProblemasCuatro FROM auditoria_proceso $filtro_mes_problemas AND idProblemasCuatro IS NOT NULL AND idProblemasCuatro != ''
        UNION ALL
        SELECT idProblemasCinco FROM auditoria_proceso $filtro_mes_problemas AND idProblemasCinco IS NOT NULL AND idProblemasCinco != ''
        UNION ALL
        SELECT idProblemasSeis FROM auditoria_proceso $filtro_mes_problemas AND idProblemasSeis IS NOT NULL AND idProblemasSeis != ''
        UNION ALL
        SELECT idProblemasSiete FROM auditoria_proceso $filtro_mes_problemas AND idProblemasSiete IS NOT NULL AND idProblemasSiete != ''
        UNION ALL
        SELECT idProblemasOcho FROM auditoria_proceso $filtro_mes_problemas AND idProblemasOcho IS NOT NULL AND idProblemasOcho != ''
        UNION ALL
        SELECT idProblemasNueve FROM auditoria_proceso $filtro_mes_problemas AND idProblemasNueve IS NOT NULL AND idProblemasNueve != ''
        UNION ALL
        SELECT idProblemasDiez FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDiez IS NOT NULL AND idProblemasDiez != ''
        UNION ALL
        SELECT idProblemasOnce FROM auditoria_proceso $filtro_mes_problemas AND idProblemasOnce IS NOT NULL AND idProblemasOnce != ''
        UNION ALL
        SELECT idProblemasDoce FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDoce IS NOT NULL AND idProblemasDoce != ''
        UNION ALL
        SELECT idProblemasTrece FROM auditoria_proceso $filtro_mes_problemas AND idProblemasTrece IS NOT NULL AND idProblemasTrece != ''
        UNION ALL
        SELECT idProblemasCatorce FROM auditoria_proceso $filtro_mes_problemas AND idProblemasCatorce IS NOT NULL AND idProblemasCatorce != ''
        UNION ALL
        SELECT idProblemasQuince FROM auditoria_proceso $filtro_mes_problemas AND idProblemasQuince IS NOT NULL AND idProblemasQuince != ''
        UNION ALL
        SELECT idProblemasDieciseis FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDieciseis IS NOT NULL AND idProblemasDieciseis != ''
        UNION ALL
        SELECT idProblemasDiecisiete FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDiecisiete IS NOT NULL AND idProblemasDiecisiete != ''
        UNION ALL
        SELECT idProblemasDieciocho FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDieciocho IS NOT NULL AND idProblemasDieciocho != ''
        UNION ALL
        SELECT idProblemasDiecinueve FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDiecinueve IS NOT NULL AND idProblemasDiecinueve != ''
        UNION ALL
        SELECT idProblemasVeinte FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeinte IS NOT NULL AND idProblemasVeinte != ''
        UNION ALL
        SELECT idProblemasVeintiuno FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeintiuno IS NOT NULL AND idProblemasVeintiuno != ''
        UNION ALL
        SELECT idProblemasVeintidos FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeintidos IS NOT NULL AND idProblemasVeintidos != ''
        UNION ALL
        SELECT idProblemasVeintitres FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeintitres IS NOT NULL AND idProblemasVeintitres != ''
        UNION ALL
        SELECT idProblemasVeinticuatro FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeinticuatro IS NOT NULL AND idProblemasVeinticuatro != ''
        UNION ALL
        SELECT idProblemasVeinticinco FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeinticinco IS NOT NULL AND idProblemasVeinticinco != ''
    ) AS combined_problemas";
$result_total_problemas = $conexion->query($sql_total_problemas);
$total_problemas = $result_total_problemas ? $result_total_problemas->fetch_assoc()['total'] : 0;

$sql_top_problemas = "
    SELECT id_problema, COUNT(*) AS frecuencia 
    FROM (
        SELECT idProblemasUnoUno AS id_problema FROM auditorias $filtro_mes_problemas AND idProblemasUnoUno IS NOT NULL AND idProblemasUnoUno != ''
        UNION ALL
        SELECT idProblemasUnoDos FROM auditorias $filtro_mes_problemas AND idProblemasUnoDos IS NOT NULL AND idProblemasUnoDos != ''
        UNION ALL
        SELECT idProblemasUnoTres FROM auditorias $filtro_mes_problemas AND idProblemasUnoTres IS NOT NULL AND idProblemasUnoTres != ''
        UNION ALL
        SELECT idProblemasDosUno FROM auditorias $filtro_mes_problemas AND idProblemasDosUno IS NOT NULL AND idProblemasDosUno != ''
        UNION ALL
        SELECT idProblemasDosDos FROM auditorias $filtro_mes_problemas AND idProblemasDosDos IS NOT NULL AND idProblemasDosDos != ''
        UNION ALL
        SELECT idProblemasDosTres FROM auditorias $filtro_mes_problemas AND idProblemasDosTres IS NOT NULL AND idProblemasDosTres != ''
        UNION ALL
        SELECT idProblemasDosCuatro FROM auditorias $filtro_mes_problemas AND idProblemasDosCuatro IS NOT NULL AND idProblemasDosCuatro != ''
        UNION ALL
        SELECT idProblemasDosCinco FROM auditorias $filtro_mes_problemas AND idProblemasDosCinco IS NOT NULL AND idProblemasDosCinco != ''
        UNION ALL
        SELECT idProblemasDosSeis FROM auditorias $filtro_mes_problemas AND idProblemasDosSeis IS NOT NULL AND idProblemasDosSeis != ''
        UNION ALL
        SELECT idProblemasTresUno FROM auditorias $filtro_mes_problemas AND idProblemasTresUno IS NOT NULL AND idProblemasTresUno != ''
        UNION ALL
        SELECT idProblemasCuatroUno FROM auditorias $filtro_mes_problemas AND idProblemasCuatroUno IS NOT NULL AND idProblemasCuatroUno != ''
        UNION ALL
        SELECT idProblemasCuatroDos FROM auditorias $filtro_mes_problemas AND idProblemasCuatroDos IS NOT NULL AND idProblemasCuatroDos != ''
        UNION ALL
        SELECT idProblemasCuatroTres FROM auditorias $filtro_mes_problemas AND idProblemasCuatroTres IS NOT NULL AND idProblemasCuatroTres != ''
        UNION ALL
        SELECT idProblemasCincoUno FROM auditorias $filtro_mes_problemas AND idProblemasCincoUno IS NOT NULL AND idProblemasCincoUno != ''
        UNION ALL
        SELECT idProblemasCincoDos FROM auditorias $filtro_mes_problemas AND idProblemasCincoDos IS NOT NULL AND idProblemasCincoDos != ''
        UNION ALL
        SELECT idProblemasCincoTres FROM auditorias $filtro_mes_problemas AND idProblemasCincoTres IS NOT NULL AND idProblemasCincoTres != ''
        UNION ALL
        SELECT idProblemasCincoCuatro FROM auditorias $filtro_mes_problemas AND idProblemasCincoCuatro IS NOT NULL AND idProblemasCincoCuatro != ''
        UNION ALL
        SELECT idProblemasCincoCinco FROM auditorias $filtro_mes_problemas AND idProblemasCincoCinco IS NOT NULL AND idProblemasCincoCinco != ''
        UNION ALL
        SELECT idProblemasCincoSeis FROM auditorias $filtro_mes_problemas AND idProblemasCincoSeis IS NOT NULL AND idProblemasCincoSeis != ''
        UNION ALL
        SELECT idProblemasCincoSiete FROM auditorias $filtro_mes_problemas AND idProblemasCincoSiete IS NOT NULL AND idProblemasCincoSiete != ''
        UNION ALL
        SELECT idProblemasCincoOcho FROM auditorias $filtro_mes_problemas AND idProblemasCincoOcho IS NOT NULL AND idProblemasCincoOcho != ''
        UNION ALL
        SELECT idProblemasSeisUno FROM auditorias $filtro_mes_problemas AND idProblemasSeisUno IS NOT NULL AND idProblemasSeisUno != ''
        UNION ALL
        SELECT idProblemasUno FROM auditoria_proceso $filtro_mes_problemas AND idProblemasUno IS NOT NULL AND idProblemasUno != ''
        UNION ALL
        SELECT idProblemasDos FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDos IS NOT NULL AND idProblemasDos != ''
        UNION ALL
        SELECT idProblemasTres FROM auditoria_proceso $filtro_mes_problemas AND idProblemasTres IS NOT NULL AND idProblemasTres != ''
        UNION ALL
        SELECT idProblemasCuatro FROM auditoria_proceso $filtro_mes_problemas AND idProblemasCuatro IS NOT NULL AND idProblemasCuatro != ''
        UNION ALL
        SELECT idProblemasCinco FROM auditoria_proceso $filtro_mes_problemas AND idProblemasCinco IS NOT NULL AND idProblemasCinco != ''
        UNION ALL
        SELECT idProblemasSeis FROM auditoria_proceso $filtro_mes_problemas AND idProblemasSeis IS NOT NULL AND idProblemasSeis != ''
        UNION ALL
        SELECT idProblemasSiete FROM auditoria_proceso $filtro_mes_problemas AND idProblemasSiete IS NOT NULL AND idProblemasSiete != ''
        UNION ALL
        SELECT idProblemasOcho FROM auditoria_proceso $filtro_mes_problemas AND idProblemasOcho IS NOT NULL AND idProblemasOcho != ''
        UNION ALL
        SELECT idProblemasNueve FROM auditoria_proceso $filtro_mes_problemas AND idProblemasNueve IS NOT NULL AND idProblemasNueve != ''
        UNION ALL
        SELECT idProblemasDiez FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDiez IS NOT NULL AND idProblemasDiez != ''
        UNION ALL
        SELECT idProblemasOnce FROM auditoria_proceso $filtro_mes_problemas AND idProblemasOnce IS NOT NULL AND idProblemasOnce != ''
        UNION ALL
        SELECT idProblemasDoce FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDoce IS NOT NULL AND idProblemasDoce != ''
        UNION ALL
        SELECT idProblemasTrece FROM auditoria_proceso $filtro_mes_problemas AND idProblemasTrece IS NOT NULL AND idProblemasTrece != ''
        UNION ALL
        SELECT idProblemasCatorce FROM auditoria_proceso $filtro_mes_problemas AND idProblemasCatorce IS NOT NULL AND idProblemasCatorce != ''
        UNION ALL
        SELECT idProblemasQuince FROM auditoria_proceso $filtro_mes_problemas AND idProblemasQuince IS NOT NULL AND idProblemasQuince != ''
        UNION ALL
        SELECT idProblemasDieciseis FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDieciseis IS NOT NULL AND idProblemasDieciseis != ''
        UNION ALL
        SELECT idProblemasDiecisiete FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDiecisiete IS NOT NULL AND idProblemasDiecisiete != ''
        UNION ALL
        SELECT idProblemasDieciocho FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDieciocho IS NOT NULL AND idProblemasDieciocho != ''
        UNION ALL
        SELECT idProblemasDiecinueve FROM auditoria_proceso $filtro_mes_problemas AND idProblemasDiecinueve IS NOT NULL AND idProblemasDiecinueve != ''
        UNION ALL
        SELECT idProblemasVeinte FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeinte IS NOT NULL AND idProblemasVeinte != ''
        UNION ALL
        SELECT idProblemasVeintiuno FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeintiuno IS NOT NULL AND idProblemasVeintiuno != ''
        UNION ALL
        SELECT idProblemasVeintidos FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeintidos IS NOT NULL AND idProblemasVeintidos != ''
        UNION ALL
        SELECT idProblemasVeintitres FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeintitres IS NOT NULL AND idProblemasVeintitres != ''
        UNION ALL
        SELECT idProblemasVeinticuatro FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeinticuatro IS NOT NULL AND idProblemasVeinticuatro != ''
        UNION ALL
        SELECT idProblemasVeinticinco FROM auditoria_proceso $filtro_mes_problemas AND idProblemasVeinticinco IS NOT NULL AND idProblemasVeinticinco != ''
    ) AS combined_problemas
    GROUP BY id_problema 
    ORDER BY frecuencia DESC 
    LIMIT 5";
$result_top_problemas = $conexion->query($sql_top_problemas);
$top_problemas = [];
if ($result_top_problemas) {
    while ($row = $result_top_problemas->fetch_assoc()) {
        $top_problemas[] = $row;
    }
}
// Verificar logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Auditorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="img/images.ico">
    <style>
        body {
            background: linear-gradient(120deg, rgb(241, 247, 246) 0%, rgb(226, 248, 255) 100%);
            min-height: 100vh;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: -220px;
            background-color: #2A3184;
            transition: 0.3s;
            padding-top: 60px;
            z-index: 1000;
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 15px 25px;
            transition: 0.3s;
        }

        .sidebar .nav-link:hover {
            background-color: #3b47b8;
            color: #ffc107;
        }

        .sidebar .nav-link.active {
            background-color: #3b47b8;
            color: #ffc107;
        }

        .sidebar-header {
            padding: 20px;
            background-color: #1e2561;
            color: #fff;
            text-align: center;
            position: absolute;
            top: 0;
            width: 100%;
            border-top-right-radius: 15px;
        }

        .toggle-bar {
            position: absolute;
            right: 0;
            top: 0;
            width: 30px;
            height: 100%;
            /* background-color: #f9c10f; */
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: 0.3s;
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .toggle-bar:hover {
            background-color: #f9c10f;
        }

        .toggle-bar i {
            color: #fff;
            font-size: 20px;
            transition: 0.3s;
        }

        .sidebar.active .toggle-bar i {
            transform: rotate(180deg);
        }

        .content {
            margin-left: 30px;
            transition: 0.3s;
            padding: 20px;
        }

        .content.active {
            margin-left: 250px;
        }

        .user-info {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 30px);
            padding: 15px;
            color: #fff;
            border-top: 1px solid #3b47b8;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(67, 12, 131, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-header {
            background-color: #2A3184;
            color: white;
        }

        .stat-number, .stat-number-nuevo {
            color: #2A3184;
        }

        h2, h3 {
            color: #2A3184;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
                left: -170px;
            }
            .sidebar.active {
                left: 0;
            }
            .content {
                margin-left: 30px;
            }
            .content.active {
                margin-left: 200px;
            }
            .user-info {
                width: calc(100% - 30px);
            }
        }

        .problemas-table {
            margin-top: 20px;
        }

        .problemas-table th, .problemas-table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Adler Pelzer Group</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-warning" href="#"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="Vista/programar_auditoria.php"><i class="fas fa-calendar-plus me-2"></i>Programar Auditoría</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="Vista/registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="Vista/ver_auditorias_programadas.php"><i class="fas fa-calendar-check me-2"></i>Ver Auditorías Programadas</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="Vista/ver_usuarios_registrados.php"><i class="fas fa-users me-2"></i>Ver Usuarios Registrados</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="Vista/proyectos.php"><i class="fas fa-folder-open me-2"></i>Proyectos</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="Vista/mis_auditorias.php"><i class="fas fa-calendar me-2"></i>Mis Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Vista/ver_registro_por_usuario.php"><i class="fas fa-check-circle me-2"></i>Mis Registros Terminados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Vista/cambiar_contraseña.php"><i class="fas fa-check-circle me-2"></i>Cambiar Contraseña</a>
            </li>
        </ul>
        <div class="user-info">
            <span><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre_empleado); ?></span>
            <form action="Controlador/logout.php" method="POST" class="mt-2">
                <button type="submit" name="logout" class="btn btn-danger w-100">Cerrar Sesión</button>
            </form>
        </div>
        <div class="toggle-bar" onclick="toggleSidebar()">
            <i class="fas fa-arrow-right"></i>
        </div>
    </div>

    <div class="content" id="content">
        <h2 class="text-center mb-4">Dashboard de Auditorías</h2>
        <div class="row mb-3">
            <div class="col-md-12 text-center">
                <div class="alert alert-info" role="alert">
                    <strong>Semana Actual:</strong> <?php echo $current_week_display; ?> (Del <?php echo $start_date_str; ?> al <?php echo $end_date_str; ?>)
                </div>
            </div>
        </div>

        <!-- Filtro por mes (para gráficas originales y tabla de problemas) -->
        <div class="row mb-4">
            <div class="col-md-4 offset-md-4">
                <form id="filtroMesForm" class="input-group">
                    <label for="mesFiltro" class="input-group-text">Filtrar por Mes:</label>
                    <select name="mes" id="mesFiltro" class="form-select">
                        <option value="total" <?php echo $mes_filtro === 'total' ? 'selected' : ''; ?>>Total de Registros</option>
                        <option value="1" <?php echo $mes_filtro === '1' ? 'selected' : ''; ?>>Enero</option>
                        <option value="2" <?php echo $mes_filtro === '2' ? 'selected' : ''; ?>>Febrero</option>
                        <option value="3" <?php echo $mes_filtro === '3' ? 'selected' : ''; ?>>Marzo</option>
                        <option value="4" <?php echo $mes_filtro === '4' ? 'selected' : ''; ?>>Abril</option>
                        <option value="5" <?php echo $mes_filtro === '5' ? 'selected' : ''; ?>>Mayo</option>
                        <option value="6" <?php echo $mes_filtro === '6' ? 'selected' : ''; ?>>Junio</option>
                        <option value="7" <?php echo $mes_filtro === '7' ? 'selected' : ''; ?>>Julio</option>
                        <option value="8" <?php echo $mes_filtro === '8' ? 'selected' : ''; ?>>Agosto</option>
                        <option value="9" <?php echo $mes_filtro === '9' ? 'selected' : ''; ?>>Septiembre</option>
                        <option value="10" <?php echo $mes_filtro === '10' ? 'selected' : ''; ?>>Octubre</option>
                        <option value="11" <?php echo $mes_filtro === '11' ? 'selected' : ''; ?>>Noviembre</option>
                        <option value="12" <?php echo $mes_filtro === '12' ? 'selected' : ''; ?>>Diciembre</option>
                    </select>
                </form>
            </div>
        </div>
        <!-- Sección original: Top Section: Stats Cards -->
        <div class="row row-cols-1 row-cols-md-5 g-4 mb-4" id="originalStatsCards">
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-info text-center">Total Auditorías</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number" id="total_auditorias"><?php echo $total_auditorias; ?></h3>
                        <p class="text-muted">Programadas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-warning text-center">Asignadas</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number" id="asignadas"><?php echo $asignadas; ?></h3>
                        <p class="text-muted">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white text-center">En Proceso</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number" id="en_proceso"><?php echo $en_proceso; ?></h3>
                        <p class="text-muted">En Ejecución</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-success text-white text-center">Realizadas pendientes por cerrar</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number" id="realizadas"><?php echo $realizadas; ?></h3>
                        <p class="text-muted">Completadas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white text-center">Cerradas</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number" id="cerradas"><?php echo $cerradas; ?></h3>
                        <p class="text-muted">Finalizadas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección original: Gráficas Generales -->
        <div class="row mb-4">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header text-center">Total Auditorías por Semana</div>
                    <div class="card-body">
                        <canvas id="totalChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-center">Estado de Auditorías</div>
                    <div class="card-body">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header text-center">Auditorías por Capas</div>
                    <div class="card-body">
                        <canvas id="capasChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header text-center">Auditorías por Procesos</div>
                    <div class="card-body">
                        <canvas id="procesosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Top 5 Problemas Más Comunes (por mes) -->
        <div class="row mb-4 justify-content-center">
            <div class="col-md-6">
                <div class="card problemas-table">
                    <div class="card-header text-center bg-secondary text-white">Top 5 Problemas Más Comunes (Mes)</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Problema</th>
                                        <th>Frecuencia</th>
                                        <th>Porcentaje</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($top_problemas)): ?>
                                        <?php foreach ($top_problemas as $problema): ?>
                                            <?php $porcentaje = $total_problemas > 0 ? ($problema['frecuencia'] / $total_problemas) * 100 : 0; ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($problema['id_problema']); ?></td>
                                                <td><?php echo $problema['frecuencia']; ?></td>
                                                <td><?php echo number_format($porcentaje, 1); ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No hay problemas registrados en este mes.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección original: Tablas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-center">Auditorías Próximas (Próximas 2 Semanas)</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Auditoría</th>
                                        <th>Número Empleado</th>
                                        <th>Nombre</th>
                                        <th>Nave</th>
                                        <th>Proyecto</th>
                                        <th>Cliente</th>
                                        <th>Semana</th>
                                        <th>Fecha Programada</th>
                                        <th>Tipo de Auditoría</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_upcoming->num_rows > 0): ?>
                                        <?php while ($row = $result_upcoming->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['id_auditoria']; ?></td>
                                                <td><?php echo $row['numero_empleado']; ?></td>
                                                <td><?php echo $row['nombre']; ?></td>
                                                <td><?php echo $row['nave']; ?></td>
                                                <td><?php echo $row['proyecto']; ?></td>
                                                <td><?php echo $row['cliente']; ?></td>
                                                <td><?php echo $row['semana']; ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($row['fecha_programada'])); ?></td>
                                                <td><?php echo $row['tipo_auditoria']; ?></td>
                                                <td><span class="badge bg-warning"><?php echo $row['estatus']; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No hay auditorías próximas en las próximas 2 semanas.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-center text-white">Auditorías Vencidas</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Auditoría</th>
                                        <th>Número Empleado</th>
                                        <th>Nombre</th>
                                        <th>Nave</th>
                                        <th>Proyecto</th>
                                        <th>Cliente</th>
                                        <th>Semana</th>
                                        <th>Fecha Programada</th>
                                        <th>Tipo de Auditoría</th>
                                        <th>Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_overdue->num_rows > 0): ?>
                                        <?php while ($row = $result_overdue->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $row['id_auditoria']; ?></td>
                                                <td><?php echo $row['numero_empleado']; ?></td>
                                                <td><?php echo $row['nombre']; ?></td>
                                                <td><?php echo $row['nave']; ?></td>
                                                <td><?php echo $row['proyecto']; ?></td>
                                                <td><?php echo $row['cliente']; ?></td>
                                                <td><?php echo $row['semana']; ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($row['fecha_programada'])); ?></td>
                                                <td><?php echo $row['tipo_auditoria']; ?></td>
                                                <td><span class="badge bg-danger"><?php echo $row['estatus']; ?></span></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No hay auditorías vencidas.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br><br>
        <h3 class="text-center mb-4 bg-dark text-white">Análisis Detallado por Rango de Fechas</h3>
        <div class="row mb-4">
            <div class="col-md-6 offset-md-3">
                <form id="fechaForm" class="input-group">
                    <label class="input-group-text">Filtrar por Fechas:</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
                    <input type="date" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
                    <button type="button" class="btn btn-primary" id="filtrarFechas">Filtrar</button>
                </form>
            </div>
        </div>

        <!-- Nuevas Cards -->
        <div class="row row-cols-1 row-cols-md-5 g-4 mb-4" id="newStatsCards">
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-info text-center">Total Auditorías (Fechas)</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number-nuevo" id="total_nuevas">0</h3>
                        <p class="text-muted">Programadas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-warning text-center">Asignadas (Fechas)</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number-nuevo" id="asignadas_nuevas">0</h3>
                        <p class="text-muted">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white text-center">En Proceso (Fechas)</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number-nuevo" id="en_proceso_nuevas">0</h3>
                        <p class="text-muted">En Ejecución</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-success text-white text-center">Realizadas pendientes por cerrar</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number-nuevo" id="realizadas_nuevas">0</h3>
                        <p class="text-muted">Completadas</p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <div class="card-header bg-danger text-white text-center">Cerradas (Fechas)</div>
                    <div class="card-body text-center">
                        <h3 class="stat-number-nuevo" id="cerradas_nuevas">0</h3>
                        <p class="text-muted">Finalizadas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nuevas Gráficas -->
        <div class="row mb-4" id="newCharts">
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header text-center">Realizadas pendientes por cerrar (Fechas)</div>
                    <div class="card-body">
                        <canvas id="totalChartNew"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">Estado de Auditorías (Fechas)</div>
                    <div class="card-body">
                        <canvas id="statusChartNew"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header text-center">Auditorías por Capas (Fechas)</div>
                    <div class="card-body">
                        <canvas id="capasChartNew"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header text-center">Auditorías por Procesos (Fechas)</div>
                    <div class="card-body">
                        <canvas id="procesosChartNew"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header text-center">Porcentaje de Auditorías Realizadas  (Fechas)</div>
                    <div class="card-body">
                        <canvas id="percentageChartNew"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <br><br>

            <?php include('Vista/pie.php'); ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }

        // Gráficas originales
        let totalChart, statusChart, capasChart, procesosChart;

        function actualizarGraficasOriginales(data) {
            if (totalChart) totalChart.destroy();
            if (statusChart) statusChart.destroy();
            if (capasChart) capasChart.destroy();
            if (procesosChart) procesosChart.destroy();

            totalChart = new Chart(document.getElementById('totalChart'), {
                type: 'line',
                data: {
                    labels: data.total_semanas_labels,
                    datasets: [
                        { label: 'Programadas', data: data.total_semanas_programadas, borderColor: '#007bff', backgroundColor: 'rgba(0, 123, 255, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Realizadas pendientes por cerrar', data: data.total_semanas_realizadas, borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Cerradas', data: data.total_semanas_cerradas, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.2)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' }, title: { display: true, text: 'Total Auditorías por Semana' } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            const totalStatus = parseInt(data.asignadas) + parseInt(data.en_proceso) + parseInt(data.realizadas) + parseInt(data.cerradas);
            statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Asignadas', 'En Proceso', 'Realizadas pendientes por cerrar', 'Cerradas'],
                    datasets: [{
                        data: [data.asignadas, data.en_proceso, data.realizadas, data.cerradas],
                        backgroundColor: ['#ffeb3b', '#007bff', '#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Distribución por Estado' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    let percentage = totalStatus > 0 ? ((value / totalStatus) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${percentage}%`; // Mostrar solo el porcentaje
                            },
                            color: '#fff',
                            font: { weight: 'bold', size: 14 },
                            anchor: 'center',
                            align: 'center'
                        }
                    }
                },
                plugins: [ChartDataLabels] // Restaurar el plugin para mostrar porcentajes
            });

            capasChart = new Chart(document.getElementById('capasChart'), {
                type: 'line',
                data: {
                    labels: data.capas_semanas_labels,
                    datasets: [
                        { label: 'Programadas', data: data.capas_semanas_programadas, borderColor: '#007bff', backgroundColor: 'rgba(0, 123, 255, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Realizadas pendientes por cerrar', data: data.capas_semanas_realizadas, borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Cerradas', data: data.capas_semanas_cerradas, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.2)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' }, title: { display: true, text: 'Auditorías por Capas' } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            procesosChart = new Chart(document.getElementById('procesosChart'), {
                type: 'line',
                data: {
                    labels: data.procesos_semanas_labels,
                    datasets: [
                        { label: 'Programadas', data: data.procesos_semanas_programadas, borderColor: '#007bff', backgroundColor: 'rgba(0, 123, 255, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Realizadas pendientes por cerrar', data: data.procesos_semanas_realizadas, borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Cerradas', data: data.procesos_semanas_cerradas, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.2)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' }, title: { display: true, text: 'Auditorías por Procesos' } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Actualizar las tarjetas originales usando IDs específicos
            document.getElementById('total_auditorias').textContent = data.total_auditorias;
            document.getElementById('asignadas').textContent = data.asignadas;
            document.getElementById('en_proceso').textContent = data.en_proceso;
            document.getElementById('realizadas').textContent = data.realizadas;
            document.getElementById('cerradas').textContent = data.cerradas;
        }

        const datosOriginales = {
            total_auditorias: <?php echo $total_auditorias; ?>,
            asignadas: <?php echo $asignadas; ?>,
            en_proceso: <?php echo $en_proceso; ?>,
            realizadas: <?php echo $realizadas; ?>,
            cerradas: <?php echo $cerradas; ?>,
            total_semanas_labels: <?php echo json_encode($total_semanas_labels); ?>,
            total_semanas_programadas: <?php echo json_encode($total_semanas_programadas); ?>,
            total_semanas_realizadas: <?php echo json_encode($total_semanas_realizadas); ?>,
            total_semanas_cerradas: <?php echo json_encode($total_semanas_cerradas); ?>,
            capas_semanas_labels: <?php echo json_encode($capas_semanas_labels); ?>,
            capas_semanas_programadas: <?php echo json_encode($capas_semanas_programadas); ?>,
            capas_semanas_realizadas: <?php echo json_encode($capas_semanas_realizadas); ?>,
            capas_semanas_cerradas: <?php echo json_encode($capas_semanas_cerradas); ?>,
            procesos_semanas_labels: <?php echo json_encode($procesos_semanas_labels); ?>,
            procesos_semanas_programadas: <?php echo json_encode($procesos_semanas_programadas); ?>,
            procesos_semanas_realizadas: <?php echo json_encode($procesos_semanas_realizadas); ?>,
            procesos_semanas_cerradas: <?php echo json_encode($procesos_semanas_cerradas); ?>
        };
        actualizarGraficasOriginales(datosOriginales);

        // Gráficas nuevas (con filtro por fechas, sin recarga)
        let totalChartNew, statusChartNew, capasChartNew, procesosChartNew, percentageChartNew;

        function actualizarGraficasNuevas(data) {
            if (totalChartNew) totalChartNew.destroy();
            if (statusChartNew) statusChartNew.destroy();
            if (capasChartNew) capasChartNew.destroy();
            if (procesosChartNew) procesosChartNew.destroy();
            if (percentageChartNew) percentageChartNew.destroy();

            // Total Auditorías por Semana (Línea)
            totalChartNew = new Chart(document.getElementById('totalChartNew'), {
                type: 'line',
                data: {
                    labels: data.total_semanas_labels_nuevas,
                    datasets: [
                        { label: 'Programadas', data: data.total_semanas_programadas_nuevas, borderColor: '#007bff', backgroundColor: 'rgba(0, 123, 255, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Realizadas pendientes por cerrar', data: data.total_semanas_realizadas_nuevas, borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.2)', fill: true, tension: 0.4 },
                        { label: 'Cerradas', data: data.total_semanas_cerradas_nuevas, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.2)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' }, title: { display: true, text: 'Total Auditorías por Semana (Fechas)' } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Estado de Auditorías (Dona)
            const totalStatusNuevas = parseInt(data.asignadas_nuevas) + parseInt(data.en_proceso_nuevas) + parseInt(data.realizadas_nuevas) + parseInt(data.cerradas_nuevas);
            statusChartNew = new Chart(document.getElementById('statusChartNew'), {
                type: 'doughnut',
                data: {
                    labels: ['Asignadas', 'En Proceso', 'Realizadas pendientes por cerrar', 'Cerradas'],
                    datasets: [{
                        data: [data.asignadas_nuevas, data.en_proceso_nuevas, data.realizadas_nuevas, data.cerradas_nuevas],
                        backgroundColor: ['#ffeb3b', '#007bff', '#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Distribución por Estado (Fechas)' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    let percentage = totalStatusNuevas > 0 ? ((value / totalStatusNuevas) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${percentage}%`; // Mostrar solo el porcentaje
                            },
                            color: '#fff',
                            font: { weight: 'bold', size: 14 },
                            anchor: 'center',
                            align: 'center'
                        }
                    }
                },
                plugins: [ChartDataLabels] // Restaurar el plugin para mostrar porcentajes
            });

            // Auditorías por Capas (Línea con porcentaje de realizadas)
            const totalCapas = data.capas_semanas_programadas_nuevas.reduce((a, b) => a + b, 0) || 1; // Evitar división por cero
            capasChartNew = new Chart(document.getElementById('capasChartNew'), {
                type: 'line',
                data: {
                    labels: data.capas_semanas_labels_nuevas || [],
                    datasets: [
                        { 
                            label: 'Programadas', 
                            data: data.capas_semanas_programadas_nuevas || [], 
                            borderColor: '#007bff', 
                            backgroundColor: 'rgba(0, 123, 255, 0.2)', 
                            fill: true, 
                            tension: 0.4 
                        },
                        { 
                            label: 'Realizadas pendientes por cerrar (% de Programadas)', 
                            data: data.capas_semanas_realizadas_nuevas.map(val => totalCapas > 0 ? (val / totalCapas) * 100 : 0), 
                            borderColor: '#28a745', 
                            backgroundColor: 'rgba(40, 167, 69, 0.2)', 
                            fill: true, 
                            tension: 0.4,
                            yAxisID: 'y1'
                        },
                        { 
                            label: 'Cerradas', 
                            data: data.capas_semanas_cerradas_nuevas || [], 
                            borderColor: '#dc3545', 
                            backgroundColor: 'rgba(220, 53, 69, 0.2)', 
                            fill: true, 
                            tension: 0.4 
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { 
                        legend: { position: 'top' }, 
                        title: { display: true, text: 'Auditorías por Capas (Fechas)' } 
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } },
                        y1: { 
                            beginAtZero: true, 
                            position: 'right', 
                            title: { display: true, text: 'Porcentaje Realizadas pendientes por cerrar (%)' }, 
                            ticks: { callback: value => value + '%' } 
                        }
                    }
                }
            });

            // Auditorías por Procesos (Línea con porcentaje de realizadas)
            const totalProcesos = data.procesos_semanas_programadas_nuevas.reduce((a, b) => a + b, 0) || 1; // Evitar división por cero
            procesosChartNew = new Chart(document.getElementById('procesosChartNew'), {
                type: 'line',
                data: {
                    labels: data.procesos_semanas_labels_nuevas,
                    datasets: [
                        { label: 'Programadas', data: data.procesos_semanas_programadas_nuevas, borderColor: '#007bff', backgroundColor: 'rgba(0, 123, 255, 0.2)', fill: true, tension: 0.4 },
                        { 
                            label: 'Realizadas pendientes por cerrar (% de Programadas)', 
                            data: data.procesos_semanas_realizadas_nuevas.map(val => totalProcesos > 0 ? (val / totalProcesos) * 100 : 0), 
                            borderColor: '#28a745', 
                            backgroundColor: 'rgba(40, 167, 69, 0.2)', 
                            fill: true, 
                            tension: 0.4,
                            yAxisID: 'y1' 
                        },
                        { label: 'Cerradas', data: data.procesos_semanas_cerradas_nuevas, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.2)', fill: true, tension: 0.4 }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' }, title: { display: true, text: 'Auditorías por Procesos (Fechas)' } },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Cantidad' } },
                        y1: { beginAtZero: true, position: 'right', title: { display: true, text: 'Porcentaje Realizadas pendientes por cerrar (%)' }, ticks: { callback: value => value + '%' } }
                    }
                }
            });

            // Porcentaje de Auditorías Realizadas (Dona)
            const totalRealizadas = parseInt(data.realizadas_nuevas);
            const totalProgramadas = parseInt(data.total_nuevas);
            percentageChartNew = new Chart(document.getElementById('percentageChartNew'), {
                type: 'doughnut',
                data: {
                    labels: ['Realizadas', 'No Realizadas'],
                    datasets: [{
                        data: [totalRealizadas, totalProgramadas - totalRealizadas],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Porcentaje de Auditorías Realizadas  (Fechas)' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    let percentage = totalProgramadas > 0 ? ((value / totalProgramadas) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${percentage}%`; // Mostrar solo el porcentaje
                            },
                            color: '#fff',
                            font: { weight: 'bold', size: 14 },
                            anchor: 'center',
                            align: 'center'
                        }
                    }
                },
                plugins: [ChartDataLabels] // Restaurar el plugin para mostrar porcentajes
            });

            // Actualizar cards usando IDs específicos
            document.getElementById('total_nuevas').textContent = data.total_nuevas;
            document.getElementById('asignadas_nuevas').textContent = data.asignadas_nuevas;
            document.getElementById('en_proceso_nuevas').textContent = data.en_proceso_nuevas;
            document.getElementById('realizadas_nuevas').textContent = data.realizadas_nuevas;
            document.getElementById('cerradas_nuevas').textContent = data.cerradas_nuevas;
        }

        // Cargar datos iniciales para nuevas gráficas
        $.ajax({
            url: 'get_data.php',
            method: 'POST',
            data: { fecha_inicio: '<?php echo $fecha_inicio; ?>', fecha_fin: '<?php echo $fecha_fin; ?>' },
            success: function(response) {
                const data = JSON.parse(response);
                actualizarGraficasNuevas(data);
            },
            error: function(error) {
                console.error('Error al cargar datos:', error);
            }
        });

        // Evento para filtro por mes (originales y tabla de problemas)
        document.getElementById('mesFiltro').addEventListener('change', function() {
            const mesSeleccionado = this.value;
            window.location.href = `?mes=${mesSeleccionado}`;
        });

        // Evento para filtro por fechas (nuevas) sin recarga
        document.getElementById('filtrarFechas').addEventListener('click', function() {
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
            const fechaFin = document.querySelector('input[name="fecha_fin"]').value;

            $.ajax({
                url: 'get_data.php',
                method: 'POST',
                data: { fecha_inicio: fechaInicio, fecha_fin: fechaFin },
                success: function(response) {
                    const data = JSON.parse(response);
                    actualizarGraficasNuevas(data);
                },
                error: function(error) {
                    console.error('Error al filtrar datos:', error);
                }
            });
        });
        </script>
</body>
</html>