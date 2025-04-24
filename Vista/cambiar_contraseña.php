<?php
// cambiar_contrasena.php
session_start(); // Iniciar sesión al principio

$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha solicitado el cierre de sesión
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php"); // Redirigir al login
    exit();
}

// Obtener el número de empleado del usuario logueado
$numero_empleado = $_SESSION['numero_empleado'];

// Obtener datos del usuario y el nombre
$sql = "SELECT numero_empleado, nombre FROM empleados WHERE numero_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $numero_empleado);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Definir $nombre_empleado (como en mis_auditorias.php)
$nombre_empleado = $user['nombre'] ?? "Usuario desconocido";

// Variable para mostrar mensaje de éxito
$success = false;
$error = "";

// Actualizar contraseña
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['logout'])) { // Solo procesar cambio de contraseña si no es logout
    $nueva_contrasena = $_POST['contrasena'] ?? '';

    if (!empty($nueva_contrasena)) {
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $update_sql = "UPDATE empleados SET contraseña = ? WHERE numero_empleado = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ss", $hashed_password, $numero_empleado);

        if ($stmt->execute()) {
            $success = true; // Establecemos que el cambio fue exitoso
        } else {
            $error = "Error al actualizar la contraseña: " . $stmt->error;
        }
    } else {
        $error = "La nueva contraseña no puede estar vacía.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Cambiar Contraseña</title>
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

        /* Estilo del formulario */
        .container-form {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .container-form:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(67, 12, 131, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        h3 {
            color: #2A3184;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-label {
            color: #2A3184;
            font-weight: 500;
        }

        .form-control {
            border-radius: 10px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn-modern {
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .container-form {
                padding: 20px;
                margin: 20px;
            }

            .btn-modern {
                padding: 6px 12px;
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
                <a class="nav-link " href="mis_auditorias.php"><i class="fas fa-calendar me-2"></i>Mis Auditorías Programadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="ver_registro_por_usuario.php"><i class="fas fa-check-circle me-2"></i>Mis Registros Terminados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="cambiar_contraseña.php"><i class="fas fa-check-circle me-2"></i>Cambiar Contraseña</a>
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


    <!-- Contenido principal -->
    <div class="content" id="content">
        <div class="container-form">
            <h3>Cambiar Contraseña</h3>

            <!-- Mostrar mensaje de éxito o error -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Contraseña actualizada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label class="form-label">Nombre de usuario</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['nombre']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nueva contraseña</label>
                    <input type="password" class="form-control" name="contrasena" required>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success btn-modern">Guardar cambios</button>
                    <a href="ver_usuarios_registrados.php" class="btn btn-danger btn-modern">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    <?php include('pie.php'); ?>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }
    </script>

    <?php $conn->close(); ?>
</body>
</html>