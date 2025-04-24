<?php
include_once '../Controlador/check_access.php';  // Aquí ya se inicia la sesión

require '../conexion.php';
check_permission(['superadmin', 'admin', 'auditor']);

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Consulta modificada para incluir tipo_auditoria, gpn, numero_parte, nave y ordenar columnas
$sql = "SELECT 
    pa.id_auditoria, 
    pa.numero_empleado, 
    pa.nombre, 
    pa.gpn, 
    pa.numero_parte, 
    pa.descripcion, 
    pa.proyecto, 
    pa.cliente, 
    pa.nave,
    pa.semana, 
    pa.fecha_programada, 
    pa.correo, 
    pa.estatus,
    pa.nave,
    pa.tipo_auditoria,
    COALESCE(a.fecha, ap.fecha) as fecha_realizada,
    COALESCE(a.fecha_inicio_proceso, ap.fecha_inicio_proceso) as fecha_inicio_proceso,
    COALESCE(a.fecha_cierre, ap.fecha_cierre) as fecha_cierre,
    COALESCE(a.tuvo_nok, ap.tuvo_nok) as tuvo_nok,
    SUM(
        CASE WHEN COALESCE(a.estatus, ap.estatusUno) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusUnoDos, ap.estatusDos) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusUnoTres, ap.estatusTres) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusDosUno, ap.estatusCuatro) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusDosDos, ap.estatusCinco) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusDosTres, ap.estatusSeis) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusDosCuatro, ap.estatusSiete) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusDosCinco, ap.estatusOcho) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusDosSeis, ap.estatusNueve) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusTresUno, ap.estatusDiez) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCuatroUno, ap.estatusOnce) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCuatroDos, ap.estatusDoce) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCuatroTres, ap.estatusTrece) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoUno, ap.estatusCatorce) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoDos, ap.estatusQuince) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoTres, ap.estatusDieciseis) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoCuatro, ap.estatusDiecisiete) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoCinco, ap.estatusDieciocho) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoSeis, ap.estatusDiecinueve) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoSiete, ap.estatusVeinte) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusCincoOcho, ap.estatusVeintiuno) = 'NOK' THEN 1 ELSE 0 END +
        CASE WHEN COALESCE(a.estatusSeisUno, ap.estatusVeintidos) = 'NOK' THEN 1 ELSE 0 END
    ) as nok_count
FROM programar_auditoria pa
LEFT JOIN auditorias a ON pa.id_auditoria = a.id_auditoria AND pa.tipo_auditoria = 'auditoria por capas'
LEFT JOIN auditoria_proceso ap ON pa.id_auditoria = ap.id_auditoria AND pa.tipo_auditoria = 'auditoria por procesos'
GROUP BY 
    pa.id_auditoria, 
    pa.numero_empleado, 
    pa.nombre, 
    pa.gpn, 
    pa.numero_parte, 
    pa.descripcion, 
    pa.proyecto, 
    pa.cliente, 
    pa.nave,
    pa.semana, 
    pa.fecha_programada, 
    pa.correo, 
    pa.estatus,
    pa.nave,
    pa.tipo_auditoria,
    a.fecha, 
    a.fecha_inicio_proceso, 
    a.fecha_cierre, 
    ap.fecha, 
    ap.fecha_inicio_proceso, 
    ap.fecha_cierre, 
    a.tuvo_nok, 
    ap.tuvo_nok
ORDER BY pa.id_auditoria DESC";
$result = $conexion->query($sql);
if (!$result) {
    die("Query failed: " . $conexion->error);
}

// Separar datos por tipo de auditoría (use case-insensitive comparison)
$auditorias_capas = [];
$auditorias_procesos = [];
$unclassified_audits = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (isset($row["tipo_auditoria"]) && $row["tipo_auditoria"] !== null) {
            $tipo_auditoria_lower = strtolower($row["tipo_auditoria"]);
            if ($tipo_auditoria_lower === "auditoria por capas") {
                $auditorias_capas[] = $row;
            } elseif ($tipo_auditoria_lower === "auditoria por procesos") {
                $auditorias_procesos[] = $row;
            } else {
                $unclassified_audits[] = $row;
            }
        } else {
            $unclassified_audits[] = $row;
        }
    }
}

// Obtener empleados para los selects del modal
$query_empleados = "SELECT numero_empleado, nombre, correo FROM empleados WHERE estado = 1";
$result_empleados = $conexion->query($query_empleados);
$empleados = [];
while ($row = $result_empleados->fetch_assoc()) {
    $empleados[] = $row;
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Obtener el nombre del empleado que inició sesión para la sección user-info
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

// Verificar si el usuario es superadmin
$is_superadmin = ($_SESSION["tipo"] === 'superadmin');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Sistema de Auditorías</title>
    <link rel="icon" type="image/png" href="img/images.ico">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/intro.js@7.0.0/introjs.min.css" rel="stylesheet">
</head>
<style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }

        /* Sidebar Styles */
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
            margin-right: 30px;
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
            width: calc(100% - 45px);
            padding: 15px;
            color: #fff;
            border-top: 1px solid #3b47b8;
        }

        /* Existing Table and Filter Styles */
        .container-fluid {
            max-width: 98%;
            margin: 0 auto;
            padding: 0 15px;
        }

        .table-modern {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: white;
            font-size: 12px;
        }

        .table-modern thead th {
            background-color: rgb(48, 155, 83);
            color: white;
            text-align: center;
            padding: 6px 4px;
            font-size: 12px;
        }

        .table-modern tbody td {
            padding: 4px 3px;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background-color: #f1f1f1;
        }

        .status-asignada {
            background-color: #ffeb3b;
            color: black;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .status-realizada {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .status-cerrada {
            background-color: rgb(0, 0, 0);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .status-proceso {
            background-color: rgb(0, 89, 255);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        .status-nok {
            background-color: #dc3545;
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
            display: inline-block;
            min-width: 60px;
            text-align: center;
        }

        h2 {
            color: #2A3184;
            font-weight: bold;
            font-size: 1.5rem;
        }

        h4 {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .hidden {
            display: none;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section .form-select,
        .filter-section .form-control {
            border: 1px solid #ced4da;
            border-radius: 25px;
            padding: 8px 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background-color: #fff;
        }

        .filter-section .form-select:focus,
        .filter-section .form-control:focus {
            border-color: #2A3184;
            box-shadow: 0 0 8px rgba(42, 49, 132, 0.3);
            outline: none;
        }

        .filter-section .form-select:hover,
        .filter-section .form-control:hover {
            border-color: #2A3184;
        }

        .filter-section .form-control::placeholder {
            color: #6c757d;
            opacity: 0.7;
        }

        .filter-section label {
            font-weight: 500;
            color: #2A3184;
            margin-bottom: 5px;
        }

        .audit-type-btn {
            cursor: pointer;
            padding: 8px 15px;
            border-radius: 25px;
            margin-right: 10px;
            background-color: #e9ecef;
            transition: background-color 0.3s ease;
        }

        .audit-type-btn.active {
            background-color: #2A3184;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 16px;
            border-radius: 25px;
            background-color: #2A3184;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .pagination button:hover {
            background-color: #1e2363;
        }

        .pagination button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        .modal-body select,
        .modal-body input {
            border-radius: 25px;
        }

        .table-modern thead th:nth-child(10),
        .table-modern tbody td:nth-child(10) {
            min-width: 140px;
        }

        .table-modern thead th:nth-child(11),
        .table-modern tbody td:nth-child(11) {
            min-width: 140px;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #2A3184;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
            display: none;
        }

        .table-modern tbody tr.tuvo-nok {
            background-color: rgb(10, 70, 199) !important; /*  NOK histórico */
            font-weight: bold;
        }

        .table-modern tbody tr.tuvo-nok td {
            color:rgb(4, 92, 255) !important;
        }

        .table-modern tbody td.nok-current {
            colorTENrgb(231, 11, 11) !important; /* Rojo para NOK actuales */
            font-weight: bold;
            background-color: #d1f2eb;
            width: 10px;
            height: 10px;
            border-radius: 100%;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
                width: calc(100% - 45px);
            }

            .sidebar .nav-link {
                margin-right: 30px;
            }

            .table-modern {
                font-size: 10px;
            }

            .table-modern thead th,
            .table-modern tbody td {
                padding: 3px 2px;
            }

            .filter-section .col-md-3 {
                margin-bottom: 10px;
            }

            .filter-section .form-select,
            .filter-section .form-control {
                font-size: 14px;
                padding: 6px 12px;
            }
        }
    </style>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Adler Pelzer Group</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="programar_auditoria.php"><i class="fas fa-calendar-plus me-2"></i>Programar Auditoría</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link active" href="ver_auditorias_programadas.php"><i class="fas fa-calendar-check me-2"></i>Ver Auditorías Programadas</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="ver_usuarios_registrados.php"><i class="fas fa-users me-2"></i>Ver Usuarios Registrados</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="proyectos.php"><i class="fas fa-folder-open me-2"></i>Proyectos</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="mis_auditorias.php"><i class="fas fa-calendar me-2"></i>Mis Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="ver_registro_por_usuario.php"><i class="fas fa-check-circle me-2"></i>Mis Registros Terminados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cambiar_contraseña.php"><i class="fas fa-lock me-2"></i>Cambiar Contraseña</a>
            </li>
        </ul>
        <div class="user-info">
            <span><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre_empleado); ?></span>
            <form action="" method="POST" class="mt-2">
                <button type="submit" name="logout" class="btn btn-danger w-100">Cerrar Sesión</button>
            </form>
        </div>
        <div class="toggle-bar" onclick="toggleSidebar()">
            <i class="fas fa-arrow-right"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" id="content">
        <div class="container-fluid mt-3">
            <h2 class="text-center mb-3">Auditorías Programadas</h2>

            <!-- Botón para iniciar la guía -->
            <button id="startTour" class="btn btn-info mb-3">Iniciar Guía</button>

            <!-- Select and Filter Section -->
            <div class="filter-section row" data-step="2" data-intro="Usa estos filtros para buscar auditorías por nombre, número de empleado, semana o estatus.">
                <div class="col-md-3" data-step="1" data-intro="Selecciona el tipo de auditoría que deseas ver: 'Por Capas' o 'Por Procesos'.">
                    <label>Tipo de Auditoría</label>
                    <div>
                        <span class="audit-type-btn active" id="capasBtn">Por Capas</span>
                        <span class="audit-type-btn" id="procesosBtn">Por Procesos</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="searchName">Nombre</label>
                    <input type="text" id="searchName" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col-md-3">
                    <label for="searchEmployee">Número de Empleado</label>
                    <input type="text" id="searchEmployee" class="form-control" placeholder="Buscar por número de empleado">
                </div>
                <div class="col-md-3">
                    <label for="weekFilter">Semana</label>
                    <input type="week" id="weekFilter" class="form-control" placeholder="Filtrar por semana">
                </div>
                <div class="col-md-3 mt-2">
                    <label for="statusFilter">Estatus</label>
                    <select id="statusFilter" class="form-select">
                        <option value="">Todos los estatus</option>
                        <option value="Asignada">Asignada</option>
                        <option value="Proceso">Proceso</option>
                        <option value="Realizada">Realizada</option>
                        <option value="Cerrada">Cerrada</option>
                        <option value="NOK">NOK</option>
                    </select>
                </div>
                <div class="col-md-3 mt-2">
                    <label for="recentFilter">Ordenar por Fecha</label>
                    <select id="recentFilter" class="form-select">
                        <option value="recent">Más recientes primero</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <!-- Tabla Auditorías por Capas -->
                <div class="col-12" id="capasSection" data-step="3" data-intro="Aquí puedes ver las auditorías por capas programadas, con detalles como folio, nombre, estatus y más.">
                    <h4 class="text-center">Por Capas</h4>
                    <table class="table table-modern table-striped text-center" id="capasTable">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Núm. Emp.</th>
                                <th>Nombre</th>
                                <th>GPN</th>
                                <th>Número de Parte</th>
                                <th>Descripción</th>
                                <th>Proyecto</th>
                                <th>Cliente</th>
                                <th>Nave</th>
                                <th>Semana</th>
                                <th>Fecha Programada</th>
                                <th>Inicio Auditoría</th>
                                <th>Término Auditoría</th>
                                <th>NOK Presentes</th>
                                <th>Correo</th>
                                <th>Estatus</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($auditorias_capas)) {
                                foreach ($auditorias_capas as $row) {
                                    $rowClass = ($row["tuvo_nok"] == 1) ? 'tuvo-nok' : '';
                                    echo "<tr class='$rowClass'>";
                                    echo "<td>" . htmlspecialchars($row["id_auditoria"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["numero_empleado"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["gpn"] ?? '-') . "</td>";
                                    echo "<td>" . htmlspecialchars($row["numero_parte"] ?? '-') . "</td>";
                                    echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["proyecto"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nave"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["semana"]) . "</td>";
                                    $fecha_mostrar = ($row["estatus"] === 'Realizada' && !empty($row["fecha_realizada"]))
                                        ? htmlspecialchars($row["fecha_realizada"])
                                        : htmlspecialchars($row["fecha_programada"]);
                                    echo "<td>" . $fecha_mostrar . "</td>";
                                    $fecha_inicio_proceso = !empty($row["fecha_inicio_proceso"])
                                        ? htmlspecialchars(date("Y-m-d H:i", strtotime($row["fecha_inicio_proceso"])))
                                        : '-';
                                    echo "<td>" . $fecha_inicio_proceso . "</td>";
                                    $fecha_cierre = !empty($row["fecha_cierre"])
                                        ? htmlspecialchars(date("Y-m-d H:i", strtotime($row["fecha_cierre"])))
                                        : '-';
                                    echo "<td>" . $fecha_cierre . "</td>";
                                    $nokClass = ($row["nok_count"] > 0) ? 'nok-current' : '';
                                    echo "<td class='$nokClass'>" . htmlspecialchars($row["nok_count"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["correo"]) . "</td>";
                                    $estatus = htmlspecialchars($row["estatus"]);
                                    $estatusClass = '';
                                    switch ($estatus) {
                                        case 'Asignada':
                                            $estatusClass = 'status-asignada';
                                            break;
                                        case 'Proceso':
                                            $estatusClass = 'status-proceso';
                                            break;
                                        case 'Realizada':
                                            $estatusClass = 'status-realizada';
                                            break;
                                        case 'Cerrada':
                                            $estatusClass = 'status-cerrada';
                                            break;
                                        case 'NOK':
                                            $estatusClass = 'status-nok';
                                            break;
                                    }
                                    echo "<td><span class='$estatusClass'>$estatus</span></td>";
                                    echo "<td>";
                                    if ($estatus === 'Proceso' || $estatus === 'Realizada' || $estatus === 'Cerrada' || $estatus === 'NOK') {
                                        echo "<a href='verRegistroAdm.php?id=" . htmlspecialchars($row["id_auditoria"]) . "' class='btn btn-success btn-sm me-1'>Ver</a>";
                                    }
                                    if ($estatus === 'Asignada') {
                                        echo "<button class='btn btn-primary btn-sm edit-btn me-1' data-bs-toggle='modal' data-bs-target='#editModal' 
                                            data-id='" . htmlspecialchars($row["id_auditoria"]) . "' 
                                            data-numero='" . htmlspecialchars($row["numero_empleado"]) . "'
                                            data-nombre='" . htmlspecialchars($row["nombre"]) . "'
                                            data-gpn='" . htmlspecialchars($row["gpn"] ?? '') . "'
                                            data-numeroparte='" . htmlspecialchars($row["numero_parte"] ?? '') . "'
                                            data-descripcion='" . htmlspecialchars($row["descripcion"]) . "'
                                            data-proyecto='" . htmlspecialchars($row["proyecto"]) . "'
                                            data-cliente='" . htmlspecialchars($row["cliente"]) . "'
                                            data-tipoauditoria='" . htmlspecialchars($row["tipo_auditoria"]) . "'
                                            data-semana='" . htmlspecialchars($row["semana"]) . "'
                                            data-fecha='" . htmlspecialchars($row["fecha_programada"]) . "'
                                            data-correo='" . htmlspecialchars($row["correo"]) . "'
                                            data-nave='" . htmlspecialchars($row["nave"] ?? '') . "'>Editar</button>";
                                        if ($is_superadmin) {
                                            echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . htmlspecialchars($row["id_auditoria"]) . "' onclick='confirmDelete(" . htmlspecialchars($row["id_auditoria"]) . ")'>Eliminar</button>";
                                        }
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='17'>No hay auditorías</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination" id="capasPagination" data-step="6" data-intro="Usa estos botones para navegar entre las páginas si hay muchas auditorías."></div>
                </div>

                <!-- Tabla Auditorías por Procesos -->
                <div class="col-12 hidden" id="procesosSection" data-step="4" data-intro="Cambia a esta pestaña para ver las auditorías por procesos.">
                    <h4 class="text-center">Por Procesos</h4>
                    <table class="table table-modern table-striped text-center" id="procesosTable">
                        <thead>
                            <tr>
                                <th>Folio</th>
                                <th>Núm. Emp.</th>
                                <th>Nombre</th>
                                <th>GPN</th>
                                <th>Número de Parte</th>
                                <th>Descripción</th>
                                <th>Proyecto</th>
                                <th>Cliente</th>
                                <th>Nave</th>
                                <th>Semana</th>
                                <th>Fecha Programada</th>
                                <th>Inicio Auditoría</th>
                                <th>Término Auditoría</th>
                                <th>NOK Presentes</th>
                                <th>Correo</th>
                                <th>Estatus</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($auditorias_procesos)) {
                                foreach ($auditorias_procesos as $row) {
                                    $rowClass = ($row["tuvo_nok"] == 1) ? 'tuvo-nok' : '';
                                    echo "<tr class='$rowClass'>";
                                    echo "<td>" . htmlspecialchars($row["id_auditoria"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["numero_empleado"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["gpn"] ?? '-') . "</td>";
                                    echo "<td>" . htmlspecialchars($row["numero_parte"] ?? '-') . "</td>";
                                    echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["proyecto"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nave"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["semana"]) . "</td>";
                                    $fecha_mostrar = ($row["estatus"] === 'Realizada' && !empty($row["fecha_realizada"]))
                                        ? htmlspecialchars($row["fecha_realizada"])
                                        : htmlspecialchars($row["fecha_programada"]);
                                    echo "<td>" . $fecha_mostrar . "</td>";
                                    $fecha_inicio_proceso = !empty($row["fecha_inicio_proceso"])
                                        ? htmlspecialchars(date("Y-m-d H:i", strtotime($row["fecha_inicio_proceso"])))
                                        : '-';
                                    echo "<td>" . $fecha_inicio_proceso . "</td>";
                                    $fecha_cierre = !empty($row["fecha_cierre"])
                                        ? htmlspecialchars(date("Y-m-d H:i", strtotime($row["fecha_cierre"])))
                                        : '-';
                                    echo "<td>" . $fecha_cierre . "</td>";
                                    $nokClass = ($row["nok_count"] > 0) ? 'nok-current' : '';
                                    echo "<td class='$nokClass'>" . htmlspecialchars($row["nok_count"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["correo"]) . "</td>";
                                    $estatus = htmlspecialchars($row["estatus"]);
                                    $estatusClass = '';
                                    switch ($estatus) {
                                        case 'Asignada':
                                            $estatusClass = 'status-asignada';
                                            break;
                                        case 'Proceso':
                                            $estatusClass = 'status-proceso';
                                            break;
                                        case 'Realizada':
                                            $estatusClass = 'status-realizada';
                                            break;
                                        case 'Cerrada':
                                            $estatusClass = 'status-cerrada';
                                            break;
                                        case 'NOK':
                                            $estatusClass = 'status-nok';
                                            break;
                                    }
                                    echo "<td><span class='$estatusClass'>$estatus</span></td>";
                                    echo "<td>";
                                    if ($estatus === 'Proceso' || $estatus === 'Realizada' || $estatus === 'Cerrada' || $estatus === 'NOK') {
                                        echo "<a href='verRegistroAdmPorProceso.php?id=" . htmlspecialchars($row["id_auditoria"]) . "' class='btn btn-success btn-sm me-1'>Ver</a>";
                                    }
                                    if ($estatus === 'Asignada') {
                                        echo "<button class='btn btn-primary btn-sm edit-btn me-1' data-bs-toggle='modal' data-bs-target='#editModal' 
                                            data-id='" . htmlspecialchars($row["id_auditoria"]) . "' 
                                            data-numero='" . htmlspecialchars($row["numero_empleado"]) . "'
                                            data-nombre='" . htmlspecialchars($row["nombre"]) . "'
                                            data-gpn='" . htmlspecialchars($row["gpn"] ?? '') . "'
                                            data-numeroparte='" . htmlspecialchars($row["numero_parte"] ?? '') . "'
                                            data-descripcion='" . htmlspecialchars($row["descripcion"]) . "'
                                            data-proyecto='" . htmlspecialchars($row["proyecto"]) . "'
                                            data-cliente='" . htmlspecialchars($row["cliente"]) . "'
                                            data-tipoauditoria='" . htmlspecialchars($row["tipo_auditoria"]) . "'
                                            data-semana='" . htmlspecialchars($row["semana"]) . "'
                                            data-fecha='" . htmlspecialchars($row["fecha_programada"]) . "'
                                            data-correo='" . htmlspecialchars($row["correo"]) . "'
                                            data-nave='" . htmlspecialchars($row["nave"] ?? '') . "'>Editar</button>";
                                        if ($is_superadmin) {
                                            echo "<button class='btn btn-danger btn-sm delete-btn' data-id='" . htmlspecialchars($row["id_auditoria"]) . "' onclick='confirmDelete(" . htmlspecialchars($row["id_auditoria"]) . ")'>Eliminar</button>";
                                        }
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='17'>No hay auditorías</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="pagination" id="procesosPagination"></div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" data-step="5" data-intro="Edita una auditoría asignada desde este formulario. Cambia empleado, nave, cliente, proyecto, etc., y guarda los cambios.">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Editar Auditoría</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editForm" method="POST" action="../Controlador/update_auditoria.php">
                            <div class="modal-body">
                                <input type="hidden" name="id_auditoria" id="editId">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="editNumColaborador" class="form-label">Número de Colaborador</label>
                                        <select class="form-control" name="num_colaborador" id="editNumColaborador" required>
                                            <option value="" disabled>Selecciona un número</option>
                                            <?php foreach ($empleados as $empleado): ?>
                                                <option value="<?php echo htmlspecialchars($empleado['numero_empleado']); ?>">
                                                    <?php echo htmlspecialchars($empleado['numero_empleado']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editNombreSelect" class="form-label">Nombre del Colaborador</label>
                                        <select class="form-control" id="editNombreSelect">
                                            <option value="" disabled>Selecciona un nombre</option>
                                            <?php foreach ($empleados as $empleado): ?>
                                                <option value="<?php echo htmlspecialchars($empleado['nombre']); ?>"
                                                        data-numero="<?php echo htmlspecialchars($empleado['numero_empleado']); ?>"
                                                        data-correo="<?php echo htmlspecialchars($empleado['correo']); ?>">
                                                    <?php echo htmlspecialchars($empleado['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="nombre" id="editNombre" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="editGpn" class="form-label">GPN</label>
                                        <input type="text" class="form-control" name="gpn" id="editGpn">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editNumeroParte" class="form-label">Número de Parte</label>
                                        <input type="text" class="form-control" name="numero_parte" id="editNumeroParte">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="editNave" class="form-label">Nave</label>
                                        <input type="text" class="form-control" name="nave" id="editNave">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editCliente" class="form-label">Cliente</label>
                                        <input type="text" class="form-control" name="cliente" id="editCliente" notice>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="editProyecto" class="form-label">Proyecto</label>
                                        <input type="text" class="form-control" name="proyecto" id="editProyecto" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editSemana" class="form-label">Semana</label>
                                        <input type="week" class="form-control" name="semana" id="editSemana" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="editCorreo" class="form-label">Correo</label>
                                        <input type="email" class="form-control" name="correo" id="editCorreo" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editTipoAuditoria" class="form-label">Tipo de Auditoría</label>
                                        <select class="form-control" name="tipo_auditoria" id="editTipoAuditoria" required>
                                            <option value="auditoria por capas">Auditoría por Capas</option>
                                            <option value="auditoria por procesos">Auditoría por Procesos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="editDescripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" name="descripcion" id="editDescripcion" rows="4" required></textarea>
                                </div>
                                <div id="editLoader" class="loader"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br>
    <?php include('pie.php'); ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.0.0/introjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }

        // Variables para paginación
        const rowsPerPage = 20;
        let currentPageCapas = 1;
        let currentPageProcesos = 1;
        let currentAuditType = 'capas';

        // Toggle Audit Type Function
        function toggleAuditType(type) {
            const capasSection = document.getElementById('capasSection');
            const procesosSection = document.getElementById('procesosSection');
            const buttons = document.querySelectorAll('.audit-type-btn');

            buttons.forEach(btn => btn.classList.remove('active'));
            if (type === 'capas') {
                document.getElementById('capasBtn').classList.add('active');
                capasSection.classList.remove('hidden');
                procesosSection.classList.add('hidden');
                currentAuditType = 'capas';
                paginateTable('capasTable', currentPageCapas);
            } else {
                document.getElementById('procesosBtn').classList.add('active');
                capasSection.classList.add('hidden');
                procesosSection.classList.remove('hidden');
                currentAuditType = 'procesos';
                paginateTable('procesosTable', currentPageProcesos);
            }
            filterAndPaginate();
        }

        // Función para paginar la tabla
        function paginateTable(tableId, page) {
            const table = document.getElementById(tableId);
            const rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));
            const totalRows = rows.filter(row => row.cells.length > 1).length; // Exclude "No hay auditorías" row
            const totalPages = Math.ceil(totalRows / rowsPerPage);
            const start = (page - 1) * rowsPerPage;
            const end = start + rowsPerPage;
            let visibleCount = 0;

            rows.forEach((row, index) => {
                if (row.cells.length > 1) { // Only process data rows
                    if (visibleCount >= start && visibleCount < end && row.style.display !== 'none') {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                    visibleCount++;
                }
            });

            updatePagination(tableId, page, totalPages);
        }

        // Actualizar botones de paginación
        function updatePagination(tableId, page, totalPages) {
            const paginationDiv = document.getElementById(tableId === 'capasTable' ? 'capasPagination' : 'procesosPagination');
            paginationDiv.innerHTML = `
                <button onclick="changePage('${tableId}', ${page - 1})" ${page === 1 ? 'disabled' : ''}>← Anterior</button>
                <span>Página ${page} de ${totalPages}</span>
                <button onclick="changePage('${tableId}', ${page + 1})" ${page === totalPages ? 'disabled' : ''}>Siguiente →</button>
            `;
        }

        // Cambiar página
        function changePage(tableId, newPage) {
            if (tableId === 'capasTable') {
                currentPageCapas = newPage;
            } else {
                currentPageProcesos = newPage;
            }
            filterAndPaginate();
        }

        // Filtrar y paginar
        function filterAndPaginate() {
            const tableId = currentAuditType === 'capas' ? 'capasTable' : 'procesosTable';
            const page = currentAuditType === 'capas' ? currentPageCapas : currentPageProcesos;

            filterTable(tableId);
            paginateTable(tableId, page);
        }

        // Función para filtrar la tabla
        function filterTable(tableId) {
            const searchName = document.getElementById('searchName').value.toLowerCase();
            const searchEmployee = document.getElementById('searchEmployee').value.toLowerCase();
            const weekFilter = document.getElementById('weekFilter').value; // Format: YYYY-Www
            const statusFilter = document.getElementById('statusFilter').value;
            const recentFilter = document.getElementById('recentFilter').value;

            const table = document.getElementById(tableId);
            let rows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));

            if (recentFilter === 'recent') {
                rows.sort((a, b) => {
                    // Only sort rows with sufficient cells
                    if (a.cells.length > 1 && b.cells.length > 1) {
                        const dateA = new Date(a.cells[10].textContent); // Fecha Programada
                        const dateB = new Date(b.cells[10].textContent);
                        return dateB - dateA;
                    }
                    return 0;
                });
                const tbody = table.getElementsByTagName('tbody')[0];
                rows.forEach(row => tbody.appendChild(row));
            }

            rows.forEach(row => {
                // Skip rows with insufficient cells (e.g., "No hay auditorías")
                if (row.cells.length <= 1) {
                    row.style.display = ''; // Always show placeholder row
                    return;
                }

                const name = row.cells[2].textContent.toLowerCase();
                const employee = row.cells[1].textContent.toLowerCase();
                const week = row.cells[9].textContent.trim(); // Semana in format YYYY-Www
                const status = row.cells[15].textContent.trim(); // Estatus text inside span

                const nameMatch = name.includes(searchName);
                const employeeMatch = employee.includes(searchEmployee);

                // Week filter: Normalize comparison
                let weekMatch = true;
                if (weekFilter) {
                    // Ensure week is in format YYYY-Www
                    const normalizedWeekFilter = weekFilter.replace(/(\d{4})-W(\d{1,2})/, (match, year, weekNum) => {
                        return `${year}-W${weekNum.padStart(2, '0')}`;
                    });
                    weekMatch = week === normalizedWeekFilter;
                }

                // Status filter: Compare status text
                const statusMatch = !statusFilter || status === statusFilter;

                row.style.display = (nameMatch && employeeMatch && weekMatch && statusMatch) ? '' : 'none';
            });
        }

        // Event listeners para filtros
        function updateFilters() {
            filterAndPaginate();
        }

        document.getElementById('searchName').addEventListener('input', updateFilters);
        document.getElementById('searchEmployee').addEventListener('input', updateFilters);
        document.getElementById('weekFilter').addEventListener('change', updateFilters);
        document.getElementById('statusFilter').addEventListener('change', updateFilters);
        document.getElementById('recentFilter').addEventListener('change', updateFilters);

        // Datos para los selects del modal
        const empleados = <?php echo json_encode($empleados); ?>;

        // Modal population script
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('editId').value = this.dataset.id;
                document.getElementById('editNumColaborador').value = this.dataset.numero;
                document.getElementById('editNombre').value = this.dataset.nombre;
                document.getElementById('editNombreSelect').value = this.dataset.nombre;
                document.getElementById('editGpn').value = this.dataset.gpn;
                document.getElementById('editNumeroParte').value = this.dataset.numeroparte;
                document.getElementById('editNave').value = this.dataset.nave || '';
                document.getElementById('editCliente').value = this.dataset.cliente;
                document.getElementById('editProyecto').value = this.dataset.proyecto;
                document.getElementById('editDescripcion').value = this.dataset.descripcion;
                document.getElementById('editSemana').value = this.dataset.semana;
                document.getElementById('editCorreo').value = this.dataset.correo;
                document.getElementById('editTipoAuditoria').value = this.dataset.tipoauditoria || '';
            });
        });

        document.getElementById('editNumColaborador').addEventListener('change', function () {
            const selectedNum = this.value;
            const empleado = empleados.find(emp => emp.numero_empleado === selectedNum);
            if (empleado) {
                document.getElementById('editNombre').value = empleado.nombre || '';
                document.getElementById('editCorreo').value = empleado.correo || '';
                document.getElementById('editNombreSelect').value = empleado.nombre || '';
            }
        });

        document.getElementById('editNombreSelect').addEventListener('change', function () {
            const selectedNombre = this.value;
            const empleado = empleados.find(emp => emp.nombre === selectedNombre);
            if (empleado) {
                document.getElementById('editNumColaborador').value = empleado.numero_empleado || '';
                document.getElementById('editNombre').value = empleado.nombre || '';
                document.getElementById('editCorreo').value = empleado.correo || '';
            }
        });

        // Show loader on form submission
        document.getElementById('editForm').addEventListener('submit', function () {
            document.getElementById('editLoader').style.display = 'block';
        });

        // Función para confirmar eliminación
        function confirmDelete(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../Controlador/delete_auditoria.php?id=' + id;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            paginateTable('capasTable', currentPageCapas);
            paginateTable('procesosTable', currentPageProcesos);

            // Add event listeners for audit type buttons
            document.getElementById('capasBtn').addEventListener('click', () => toggleAuditType('capas'));
            document.getElementById('procesosBtn').addEventListener('click', () => toggleAuditType('procesos'));
        });

        // Configuración de la guía interactiva con Intro.js
        document.getElementById('startTour').addEventListener('click', function () {
            introJs().setOptions({
                nextLabel: 'Siguiente',
                prevLabel: 'Anterior',
                doneLabel: 'Finalizar',
                showProgress: true,
                exitOnOverlayClick: false,
                steps: [
                    {
                        intro: "¡Bienvenido a la sección de Auditorías Programadas! Esta guía te mostrará cómo usar esta página."
                    },
                    {
                        element: document.querySelector('.audit-type-btn'),
                        intro: "Selecciona el tipo de auditoría que deseas ver: 'Por Capas' o 'Por Procesos'."
                    },
                    {
                        element: document.querySelector('.filter-section'),
                        intro: "Usa estos filtros para buscar auditorías por nombre, número de empleado, semana o estatus."
                    },
                    {
                        element: document.getElementById('capasSection'),
                        intro: "Aquí puedes ver las auditorías por capas programadas, con detalles como folio, nombre, estatus y más."
                    },
                    {
                        element: document.getElementById('procesosSection'),
                        intro: "Cambia a esta pestaña para ver las auditorías por procesos."
                    },
                    {
                        element: document.getElementById('editModal'),
                        intro: "Edita una auditoría asignada desde este formulario. Cambia empleado, nave, cliente, proyecto, etc., y guarda los cambios."
                    },
                    {
                        element: document.getElementById('capasPagination'),
                        intro: "Usa estos botones para navegar entre las páginas si hay muchas auditorías."
                    }
                    <?php if ($is_superadmin): ?>
                        , {
                            element: document.querySelector('.delete-btn') || document.querySelector('tbody tr td:last-child'),
                            intro: "Si eres superadmin, puedes eliminar auditorías asignadas con este botón."
                        }
                    <?php endif; ?>
                ]
            }).start();
        });
    </script>
</body>
</html>