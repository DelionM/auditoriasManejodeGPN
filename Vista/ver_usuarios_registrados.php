<?php 
// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
include_once '../Controlador/check_access.php';  // Aquí ya se inicia la sesión
check_permission(['superadmin']);

// Handle search query
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sql = "SELECT numero_empleado, nombre, tipo, correo, estado FROM empleados WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (nombre LIKE '%$search%' OR numero_empleado LIKE '%$search%')";
}
$sql .= " ORDER BY nombre ASC"; // Ordenar alfabéticamente por nombre
$result = $conn->query($sql);

// Verificar si se ha solicitado el cierre de sesión
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}
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
    <style>
        body {
            background: linear-gradient(120deg, rgb(241, 247, 246) 0%, rgb(226, 248, 255) 100%);
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: -220px; /* Partially visible (30px showing) */
            background-color: #2A3184;
            transition: 0.3s;
            padding-top: 60px;
            z-index: 1000;
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.2);
        }

        .sidebar.active {
            left: 0; /* Fully visible when active */
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
            /* background-color: #ffeb3b; */
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

        .container {
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h3 {
            color: #2A3184;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .table-modern {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            background: white;
        }

        .table-modern thead {
            background-color: #2A3184;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table-modern th, .table-modern td {
            padding: 15px;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
        }

        .table-modern tbody tr:hover {
            background-color: #e6f0ff;
            transform: translateX(-20px);
            transition-duration: 1s;
        }

        .btn-modern {
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: #2A3184;
            color: white;
            border: none;
        }

        .btn-modern:hover {
            background-color: #1e2561;
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .search-form {
            max-width: 400px;
            margin-bottom: 20px;
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
            .table-modern {
                font-size: 14px;
            }
            .btn-modern {
                padding: 6px 10px;
                font-size: 12px;
            }
            .status-badge {
                font-size: 0.8em;
            }
            .search-form {
                max-width: 100%;
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
            <li class="nav-item">
                <a class="nav-link text-white" href="../index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="programar_auditoria.php"><i class="fas fa-calendar-plus me-2"></i>Programar Auditoría</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ver_auditorias_programadas.php"><i class="fas fa-calendar-check me-2"></i>Ver Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-warning" href="ver_usuarios_registrados.php"><i class="fas fa-users me-2"></i>Ver Usuarios Registrados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="proyectos.php"><i class="fas fa-folder-open me-2"></i>Proyectos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="mis_auditorias.php"><i class="fas fa-calendar me-2"></i>Mis Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ver_registro_por_usuario.php"><i class="fas fa-check-circle me-2"></i>Mis Registros Terminados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="cambiar_contraseña.php"><i class="fas fa-check-circle me-2"></i>Cambiar Contraseña</a>
            </li>
        </ul>
        <div class="user-info">
            <span><i class="fas fa-user-circle me-2"></i>Superadmin</span>
            <form action="" method="POST" class="mt-2">
                <button type="submit" name="logout" class="btn btn-danger w-100">Cerrar Sesión</button>
            </form>
        </div>
        <!-- Toggle Bar with Arrow (Inside Sidebar) -->
        <div class="toggle-bar" onclick="toggleSidebar()">
            <i class="fas fa-arrow-right"></i>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content" id="content">
        <div class="container">
            <h3 class="text-center">Usuarios Registrados ✅</h3>
            
            <!-- Search Form -->
            <form method="GET" action="" class="search-form">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o número de empleado..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary btn-modern">Buscar</button>
                    <?php if (!empty($search)): ?>
                        <a href="ver_usuarios_registrados.php" class="btn btn-secondary btn-modern ms-2">Limpiar</a>
                    <?php endif; ?>
                </div>
            </form>

            <?php
            if (isset($_GET['success'])) {
                echo "<div class='alert alert-success mt-3'>" . htmlspecialchars($_GET['success']) . "</div>";
            } elseif (isset($_GET['error'])) {
                echo "<div class='alert alert-danger mt-3'>" . htmlspecialchars($_GET['error']) . "</div>";
            }
            ?>
            <div class="table-responsive">
                <table class="table table-modern table-striped align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">Número de colaborador</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Estado</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["numero_empleado"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["correo"]) . "</td>";
                                echo "<td>";
                                if ($row["estado"] == 1) {
                                    echo "<span class='status-badge status-active'>Activo</span>";
                                } else {
                                    echo "<span class='status-badge status-inactive'>Inactivo</span>";
                                }
                                echo "</td>";
                                echo "<td>";
                                echo "<a href='modificar_usuario.php?numero_empleado=" . htmlspecialchars($row["numero_empleado"]) . "' class='btn btn-primary btn-modern me-2'>Modificar</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hay registros encontrados</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <br><br><br><br>
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

<?php
$conn->close();
?>