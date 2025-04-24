<?php
include_once '../Controlador/check_access.php';  // Incluye check_access.php, que ya inicia la sesión
require '../conexion.php';  // Asegúrate de que este archivo defina $conexion correctamente

// todos los usuarios podemos accceder
check_permission(['superadmin', 'admin', 'auditor', 'usuario']);

// Obtener el número de empleado del usuario logueado (ya validado por check_permission)
$numero_empleado = $_SESSION['numero_empleado'];

// Consulta para obtener las auditorías asignadas al empleado con estatus 'Asignada' o 'Proceso'
$sql = "SELECT id_auditoria, numero_empleado, nombre, nave, descripcion, proyecto, cliente, responsable, tipo_auditoria, semana, fecha_programada, correo, estatus 
        FROM programar_auditoria 
        WHERE numero_empleado = ? AND estatus IN ('Asignada', 'Proceso')";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conexion->error);
}

$stmt->bind_param("s", $numero_empleado);
$stmt->execute();
$result = $stmt->get_result();

$auditorias = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $auditorias[] = $row;
    }
}

// Verificar si se ha solicitado el cierre de sesión
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Obtener el nombre del empleado para la sección user-info
$nombre_empleado = "Usuario desconocido";
$query_nombre = "SELECT nombre FROM empleados WHERE numero_empleado = ?";
if ($stmt_nombre = $conexion->prepare($query_nombre)) {
    $stmt_nombre->bind_param("s", $numero_empleado);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    if ($row_nombre = $result_nombre->fetch_assoc()) {
        $nombre_empleado = $row_nombre['nombre'];
    }
    $stmt_nombre->close();
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Mis Auditorías Programadas</title>
    <link rel="icon" type="image/png" href="img/images.ico">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
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
            color: #ffd700;
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
            /* background-color: #ffc107; */
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

        /* Existing Table Styles */
        .container {
            max-width: 95%;
            margin: 0 auto;
            padding: 20px;
        }
        .table-modern {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: white;
            font-size: 14px;
        }
        .table-modern thead th {
            background-color: #2A3184;
            color: white;
            text-align: center;
            padding: 10px;
        }
        .table-modern tbody td {
            padding: 10px;
            vertical-align: middle;
        }
        .table-modern tbody tr:hover {
            background-color: #f1f1f1;
        }
        .status-asignada {
            background-color: #ffeb3b;
            color: black;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
        }
        .status-proceso {
            background-color: rgb(250, 37, 0);
            color: black;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            display: inline-block;
            min-width: 80px;
        }
        h2 {
            color: #2A3184;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-iniciar {
            background-color: rgb(0, 47, 255);
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-iniciar:hover {
            background-color: rgb(8, 58, 61);
            color: white;
        }
        .btn-logout {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 500;
            background-color: #dc3545;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-logout:hover {
            background-color: #c82333;
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        /* Responsive Design */
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
                font-size: 12px;
            }
            .btn-iniciar {
                padding: 5px 10px;
                font-size: 12px;
            }
            .btn-logout {
                padding: 6px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Adler Pelzer Group</h4>
        </div>
        <ul class="nav flex-column">
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"  || $_SESSION["tipo"] === 'auditor'): ?>
                <li class="nav-item">
                    <a class="nav-link " href="../index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"  || $_SESSION["tipo"] === 'auditor'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="programar_auditoria.php"><i class="fas fa-calendar-plus me-2"></i>Programar Auditoría</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"  || $_SESSION["tipo"] === 'auditor'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="ver_auditorias_programadas.php"><i class="fas fa-calendar-check me-2"></i>Ver Auditorías Programadas</a>
                </li>
            <?php endif; ?>
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
                <a class="nav-link active" href="mis_auditorias.php"><i class="fas fa-calendar me-2"></i>Mis Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="ver_registro_por_usuario.php"><i class="fas fa-check-circle me-2"></i>Mis Registros Terminados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cambiar_contraseña.php"><i class="fas fa-check-circle me-2"></i>Cambiar Contraseña</a>
            </li>
        </ul>
        <div class="user-info">
            <span><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre_empleado); ?></span>
            <form action="../Controlador/logout.php" method="POST" class="mt-2">
                <button type="submit" class="btn btn-danger w-100">Cerrar Sesión</button>
            </form>
        </div>
        <div class="toggle-bar" onclick="toggleSidebar()">
            <i class="fas fa-arrow-right"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" id="content">
        <div class="container mt-4">
            <h2>Mis Auditorías Programadas</h2>
            <div class="table-responsive">
                <table class="table table-modern table-striped text-center">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Núm. Emp.</th>
                            <th>Nombre</th>
                            <th>Nave</th>
                            <th>Descripción</th>
                            <th>Proyecto</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Semana</th>
                            <th>Estatus</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($auditorias)) {
                            foreach ($auditorias as $row) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["id_auditoria"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["numero_empleado"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["nave"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["proyecto"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["tipo_auditoria"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["semana"]) . "</td>";
                                $estatus = htmlspecialchars($row["estatus"]);
                                $estatus_class = ($estatus === 'Asignada') ? 'status-asignada' : 'status-proceso';
                                echo "<td><span class='$estatus_class'>$estatus</span></td>";

                                // Determinar la URL según el tipo de auditoría
                                $audit_url = ($row["tipo_auditoria"] === 'auditoria por Procesos') 
                                    ? "../por_procesos.php?id_auditoria=" . htmlspecialchars($row["id_auditoria"])
                                    : "../nuevo_index.php?id_auditoria=" . htmlspecialchars($row["id_auditoria"]);
                                
                                // Botón para iniciar auditoría con URL dinámica
                                echo "<td><a href='$audit_url' class='btn-iniciar'>Iniciar</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No tienes auditorías asignadas pendientes</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br><br><br><br><br>
    </div>
    <?php include('pie.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }
    </script>
</body>
</html>