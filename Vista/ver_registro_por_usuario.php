<?php
session_start();
include('../conexion.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['numero_empleado'])) {
    header("Location: ../login.php");
    exit();
}

$numero_empleado = $_SESSION['numero_empleado'];

// Consulta para obtener el nombre del empleado
$sql_nombre = "SELECT nombre FROM empleados WHERE numero_empleado = ?";
$stmt_nombre = $conexion->prepare($sql_nombre);
$stmt_nombre->bind_param("s", $numero_empleado);
$stmt_nombre->execute();
$result_nombre = $stmt_nombre->get_result();
$nombre = $result_nombre->num_rows > 0 ? $result_nombre->fetch_assoc()['nombre'] : "Usuario";
$stmt_nombre->close();

// Consulta combinada para obtener auditorías cerradas de ambas tablas, incluyendo fecha_cierre
$sql = "
    SELECT id_auditoria, numero_empleado, nombre_auditor, cliente, proceso_auditado, fecha, estatus_cierre, fecha_cierre, 'Auditoría por Capas' AS tipo_auditoria
    FROM auditorias 
    WHERE numero_empleado = ? AND estatus_cierre = 'Cerrado'
    UNION
    SELECT id_auditoria, numero_empleado, nombre_auditor, cliente, proceso_auditado, fecha, estatus_cierre, fecha_cierre, 'Auditoría por Procesos' AS tipo_auditoria
    FROM auditoria_proceso 
    WHERE numero_empleado = ? AND estatus_cierre = 'Cerrado'
    ORDER BY fecha DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $numero_empleado, $numero_empleado);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Registros Terminados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/images.ico">
    <style>
        body {
            background: linear-gradient(120deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
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
            padding: 12px 20px; /* Reducido para un diseño más compacto */
            transition: 0.3s;
            display: flex;
            align-items: center;
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

        .user-info-top {
            padding: 15px;
            color: #fff;
            border-bottom: 1px solid #3b47b8;
            margin-top: 60px;
        }

        .user-info-top span {
            display: block;
            margin-bottom: 10px;
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
            /* background-color: #2A3184; */
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

        .user-info-bottom {
            position: absolute;
            bottom: 20px;
            width: calc(100% - 45px);
            padding: 15px;
            color: #fff;
            border-top: 1px solid #3b47b8;
        }

        /* Table Styles */
        .table-modern {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: #fff;
        }

        .table-modern thead th {
            background-color: #2A3184;
            color: #fff;
            text-align: center;
            padding: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-modern tbody td {
            vertical-align: middle;
            padding: 15px;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #e6f0ff;
            transform: translateX(-5px);
        }

        .btn-primary.btn-sm {
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #2A3184;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary.btn-sm:hover {
            background-color: #1e2466;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
            /* .user-info-top, .user-info-bottom {
                width: calc(100% - 45px);
            } */
            .sidebar .nav-link {
                padding: 10px 15px;
            }
            .table-modern {
                font-size: 14px;
            }
            .btn-primary.btn-sm {
                padding: 4px 8px;
                font-size: 12px;
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
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin" ): ?>
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
                <a class="nav-link " href="mis_auditorias.php"><i class="fas fa-calendar me-2"></i>Mis Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="ver_registro_por_usuario.php"><i class="fas fa-check-circle me-2"></i>Mis Registros Terminados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cambiar_contraseña.php"><i class="fas fa-check-circle me-2"></i>Cambiar Contraseña</a>
            </li>
        </ul>
        <div class="user-info">
            <span><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre); ?></span>
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
        <div class="container-fluid mt-5">
            <h2 class="text-center mb-4" style="color: #2A3184;">Mis Registros Terminados</h2>
            <div class="table-responsive">
                <table class="table table-modern table-striped text-center">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Número de Empleado</th>
                            <th>Nombre del Auditor</th>
                            <th>Cliente</th>
                            <th>Proceso Auditado</th>
                            <th>Fecha de Asignación</th>
                            <th>Tipo de Auditoría</th>
                            <th>Estatus</th>
                            <th>Fecha de Cierre</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id_auditoria']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['numero_empleado']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['nombre_auditor']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['cliente']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['proceso_auditado']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['tipo_auditoria']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['estatus_cierre']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['fecha_cierre'] ?? 'No especificada') . "</td>";
                                
                                // Conditional link based on audit type
                                $link = ($row['tipo_auditoria'] === 'Auditoría por Capas') 
                                    ? 'verRegistro.php' 
                                    : 'verRegistroProceso.php';
                                echo "<td><a href='" . $link . "?id=" . htmlspecialchars($row['id_auditoria']) . "&tipo=" . urlencode($row['tipo_auditoria']) . "' class='btn btn-primary btn-sm'>Ver</a></td>";
                                
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No hay registros terminados disponibles.</td></tr>";
                        }
                        $stmt->close();
                        $conexion->close();
                        ?>
                    </tbody>
                </table>
                <br><br><br><br>
            </div>
            </div>

    </div>
    <?php include('../Vista/pie.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }
    </script>
</body>
</html>