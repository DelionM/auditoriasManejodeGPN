<?php
include_once '../Controlador/check_access.php';
include_once '../conexion.php';

check_permission(['superadmin']);
// Solo superadmin puede acceder a esta página

// Comprobar si se hizo clic en el botón de cerrar sesión
if (isset($_POST['logout'])) {
    // Iniciar la sesión si no se ha iniciado
    session_start();
    
    // Destruir todas las variables de sesión
    session_unset();
    
    // Destruir la sesión
    session_destroy();
    
    // Redirigir al login
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="icon" type="image/png" href="../img/images.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .modern-card {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 850px;
            transition: transform 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-5px);
        }

        .modern-card h3 {
            color: #2A3184;
            font-weight: 600;
            text-align: center;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .input-group-text {
            background: #2A3184;
            color: white;
            border: none;
            border-radius: 10px 0 0 10px;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 10px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2A3184;
            box-shadow: 0 0 5px rgba(42, 49, 132, 0.3);
        }

        .btn-primary {
            background: linear-gradient(45deg, #2A3184, #4a5db5);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #0e9652, #2bc48a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(14, 150, 82, 0.4);
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
                <a class="nav-link text-warning" href="registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="ver_usuarios_registrados.php"><i class="fas fa-users me-2"></i>Ver Usuarios Registrados</a>
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
        <span><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($nombre_empleado); ?></span>
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
        <div class="form-container">
            <div class="modern-card">
                <h3>Registrar Nuevo Usuario</h3>
                <form action="../controlador/nuevo_empleado.php" method="POST">
                    <div class="mb-3">
                        <label for="numero_empleado" class="form-label">Número de colaborador</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                            <input type="number" class="form-control" name="numero_empleado" id="numero_empleado" placeholder="Número" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese su nombre" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" name="correo" id="correo" placeholder="Correo electrónico" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese una contraseña" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tipoUsuario" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" name="tipoUsuario" id="tipoUsuario" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="superadmin">Superadministrador</option>
                            <option value="admin">Administrador</option>
                            <option value="auditor">Auditor</option>
                            <option value="usuario">Usuario</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrar</button>
                </form>
            </div>
        </div>
        <br><br><br>
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