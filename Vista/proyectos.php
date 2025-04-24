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

include_once '../Controlador/check_access.php';
check_permission(['superadmin', 'admin']);

// --- Configuración de paginación ---
$records_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;
if (!in_array($records_per_page, [20, 50, 100])) {
    $records_per_page = 20;
}
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($current_page < 1) {
    $current_page = 1;
}
$offset = ($current_page - 1) * $records_per_page;

// --- Filtros de búsqueda y selección ---
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_cliente = isset($_GET['cliente_filter']) ? trim($_GET['cliente_filter']) : '';

// Construir la consulta SQL con filtros
$sql_total = "SELECT COUNT(*) as total FROM proyectos WHERE 1=1";
$sql_proyectos = "SELECT * FROM proyectos WHERE 1=1";
$params = [];
$types = "";

if (!empty($search_query)) {
    $sql_total .= " AND (gpn LIKE ? OR numero_parte LIKE ?)";
    $sql_proyectos .= " AND (gpn LIKE ? OR numero_parte LIKE ?)";
    $search_param = "%$search_query%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($selected_cliente)) {
    $sql_total .= " AND cliente = ?";
    $sql_proyectos .= " AND cliente = ?";
    $params[] = $selected_cliente;
    $types .= "s";
}

// Obtener el total de registros para calcular el número de páginas
$result_total = $conn->query($sql_total);
if (!empty($params)) {
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param($types, ...$params);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
}
$total_records = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

// --- Manejo de Proyectos ---
if (isset($_POST['submit_proyecto'])) {
    $gpn = trim($_POST['gpn']) ?: null;
    $numero_parte = trim($_POST['numero_parte']) ?: null;
    $cliente = trim($_POST['cliente']);
    $proyecto = trim($_POST['proyecto']);
    $descripcion = trim($_POST['descripcion']);
    $nave = trim($_POST['nave']) ?: null;

    if (!empty($cliente) && !empty($proyecto) && !empty($descripcion)) {
        $sql_insert = "INSERT INTO proyectos (gpn, numero_parte, cliente, proyecto, descripcion, nave) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssssss", $gpn, $numero_parte, $cliente, $proyecto, $descripcion, $nave);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=Proyecto agregado exitosamente&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
            exit();
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=Error al agregar proyecto: " . $conn->error . "&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
            exit();
        }
        $stmt->close();
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=Los campos Cliente, Proyecto y Descripción son obligatorios&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
        exit();
    }
}

if (isset($_POST['update_proyecto'])) {
    $id = $_POST['id'];
    $gpn = trim($_POST['gpn']) ?: null;
    $numero_parte = trim($_POST['numero_parte']) ?: null;
    $cliente = trim($_POST['cliente']);
    $proyecto = trim($_POST['proyecto']);
    $descripcion = trim($_POST['descripcion']);
    $nave = trim($_POST['nave']) ?: null;

    if (!empty($cliente) && !empty($proyecto) && !empty($descripcion)) {
        $sql_update = "UPDATE proyectos SET gpn = ?, numero_parte = ?, cliente = ?, proyecto = ?, descripcion = ?, nave = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssssi", $gpn, $numero_parte, $cliente, $proyecto, $descripcion, $nave, $id);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=Proyecto actualizado exitosamente&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
            exit();
        } else {
            header("Location: " . $_SERVER['PHP_SELF'] . "?error=Error al actualizar proyecto: " . $conn->error . "&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
            exit();
        }
        $stmt->close();
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=Los campos Cliente, Proyecto y Descripción son obligatorios&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
        exit();
    }
}

if (isset($_GET['delete_proyecto'])) {
    $id = $_GET['delete_proyecto'];
    $sql_delete = "DELETE FROM proyectos WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=Proyecto eliminado exitosamente&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
        exit();
    } else {
        header("Location: " . $_SERVER['PHP_SELF'] . "?error=Error al eliminar proyecto: " . $conn->error . "&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""));
        exit();
    }
    $stmt->close();
}

// Obtener datos para mostrar con paginación
$sql_proyectos .= " ORDER BY cliente ASC, proyecto ASC, descripcion ASC LIMIT ? OFFSET ?";
$params[] = $records_per_page;
$params[] = $offset;
$types .= "ii";

$stmt_proyectos = $conn->prepare($sql_proyectos);
$stmt_proyectos->bind_param($types, ...$params);
$stmt_proyectos->execute();
$result_proyectos = $stmt_proyectos->get_result();

// Obtener datos para Select2 y Autocomplete (valores únicos)
$sql_gpn = "SELECT DISTINCT gpn FROM proyectos WHERE gpn IS NOT NULL ORDER BY gpn ASC";
$result_gpn = $conn->query($sql_gpn);

$sql_numero_parte = "SELECT DISTINCT numero_parte FROM proyectos WHERE numero_parte IS NOT NULL ORDER BY numero_parte ASC";
$result_numero_parte = $conn->query($sql_numero_parte);

$sql_cliente = "SELECT DISTINCT cliente FROM proyectos WHERE cliente IS NOT NULL ORDER BY cliente ASC";
$result_cliente = $conn->query($sql_cliente);

$sql_proyecto = "SELECT DISTINCT proyecto FROM proyectos WHERE proyecto IS NOT NULL ORDER BY proyecto ASC";
$result_proyecto = $conn->query($sql_proyecto);

$sql_descripcion = "SELECT DISTINCT descripcion FROM proyectos WHERE descripcion IS NOT NULL ORDER BY descripcion ASC";
$result_descripcion = $conn->query($sql_descripcion);

$sql_nave = "SELECT DISTINCT nave FROM proyectos WHERE nave IS NOT NULL ORDER BY nave ASC";
$result_nave = $conn->query($sql_nave);

// Preparar datos para Autocomplete
$gpn_data = [];
while ($row = $result_gpn->fetch_assoc()) {
    $gpn_data[] = ['id' => $row['gpn'], 'label' => $row['gpn']];
}

$numero_parte_data = [];
while ($row = $result_numero_parte->fetch_assoc()) {
    $numero_parte_data[] = ['id' => $row['numero_parte'], 'label' => $row['numero_parte']];
}

$cliente_data = [];
while ($row = $result_cliente->fetch_assoc()) {
    $cliente_data[] = ['id' => $row['cliente'], 'label' => $row['cliente']];
}

$proyecto_data = [];
while ($row = $result_proyecto->fetch_assoc()) {
    $proyecto_data[] = ['id' => $row['proyecto'], 'label' => $row['proyecto']];
}

$descripcion_data = [];
while ($row = $result_descripcion->fetch_assoc()) {
    $descripcion_data[] = ['id' => $row['descripcion'], 'label' => $row['descripcion']];
}

$nave_data = [];
while ($row = $result_nave->fetch_assoc()) {
    $nave_data[] = ['id' => $row['nave'], 'label' => $row['nave']];
}

// Verificar cierre de sesión
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Obtener nombre del empleado
$numero_empleado_sesion = $_SESSION['numero_empleado'] ?? null;
$nombre_empleado = "Usuario desconocido";
if ($numero_empleado_sesion) {
    $query_nombre = "SELECT nombre FROM empleados WHERE numero_empleado = ?";
    if ($stmt = $conn->prepare($query_nombre)) {
        $stmt->bind_param("s", $numero_empleado_sesion);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nombre_empleado = $row['nombre'];
        }
        $stmt->close();
    }
}

// Cerrar conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="../img/images.ico">
    <link href="https://cdn.jsdelivr.net/npm/intro.js@7.0.0/introjs.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <title>Gestión de Proyectos</title>
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
            padding: 10px 20px;
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
            padding: 15px;
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
            font-size: 18px;
            transition: 0.3s;
        }

        .sidebar.active .toggle-bar i {
            transform: rotate(180deg);
        }

        .content {
            margin-left: 30px;
            transition: 0.3s;
            padding: 15px;
        }

        .content.active {
            margin-left: 250px;
        }

        .user-info {
            position: absolute;
            bottom: 15px;
            width: calc(100% - 30px);
            padding: 10px;
            color: #fff;
            border-top: 1px solid #3b47b8;
        }

        .container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: #2A3184;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        .table-modern {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            background: white;
            max-height: 400px;
            overflow-y: auto;
        }

        .table-modern thead {
            background-color: #2A3184;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .table-modern th, .table-modern td {
            padding: 10px;
            vertical-align: middle;
            font-size: 14px;
        }

        .table-modern tbody tr:hover {
            background-color: #e6f0ff;
        }

        .btn-modern {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: #2A3184;
            color: white;
            border: none;
            font-size: 14px;
        }

        .btn-modern:hover {
            background-color: #1e2561;
            transform: translateY(-2px);
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #2A3184;
            box-shadow: 0 0 4px rgba(42, 49, 132, 0.3);
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #ddd;
            border-radius: 8px;
            height: 38px;
            padding: 4px;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #2A3184;
            box-shadow: 0 0 4px rgba(42, 49, 132, 0.3);
            outline: none;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #2A3184;
            color: white;
        }

        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1050;
        }

        .ui-menu-item {
            padding: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .ui-menu-item:hover {
            background-color: #2A3184;
            color: white;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination .page-link {
            color: #2A3184;
            border-radius: 8px;
            margin: 0 5px;
        }

        .pagination .page-link:hover {
            background-color: #2A3184;
            color: white;
        }

        .pagination .active .page-link {
            background-color: #2A3184;
            color: white;
            border-color: #2A3184;
        }

        @media (max-width: 768px) {
            .sidebar { width: 200px; left: -170px; }
            .sidebar.active { left: 0; }
            .content { margin-left: 20px; }
            .content.active { margin-left: 200px; }
            .user-info { width: calc(100% - 30px); }
            .table-modern { font-size: 12px; }
            .btn-modern { padding: 5px 8px; font-size: 12px; }
            .form-control, .form-select, .select2-container--default .select2-selection--single { font-size: 12px; }
            .ui-menu-item { font-size: 12px; }
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
                <a class="nav-link" href="../index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link" href="programar_auditoria.php"><i class="fas fa-calendar-plus me-2"></i>Programar Auditoría</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="ver_auditorias_programadas.php"><i class="fas fa-calendar-check me-2"></i>Ver Auditorías Programadas</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ver_usuarios_registrados.php"><i class="fas fa-users me-2"></i>Ver Usuarios Registrados</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fas fa-folder-open me-2"></i>Proyectos</a>
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
        <h2>Gestión de Proyectos</h2>

        <div class="container">
            <button id="startTour" class="btn btn-secondary mb-12 text-white">Iniciar Guía</button>

            <!-- Mensajes de éxito o error -->
            <?php
            if (isset($_GET['success'])) {
                echo "<script>Swal.fire('Éxito', '" . htmlspecialchars($_GET['success']) . "', 'success');</script>";
            } elseif (isset($_GET['error'])) {
                echo "<script>Swal.fire('Error', '" . htmlspecialchars($_GET['error']) . "', 'error');</script>";
            }
            ?>

            <!-- Formulario para agregar nuevo proyecto -->
            <h3 data-step="1" data-intro="Aquí puedes agregar un nuevo proyecto al sistema.">Agregar Nuevo Proyecto</h3>
            <form action="" method="POST" class="mb-3" data-step="2" data-intro="Ingresa los detalles del proyecto y haz clic en 'Agregar Proyecto'.">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select name="gpn" class="form-select select2">
                            <option value="">Selecciona un GPN</option>
                            <?php 
                            $result_gpn->data_seek(0);
                            while ($row = $result_gpn->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row["gpn"]) . "'>" . htmlspecialchars($row["gpn"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="numero_parte" class="form-select select2">
                            <option value="">Selecciona un No. Parte</option>
                            <?php 
                            $result_numero_parte->data_seek(0);
                            while ($row = $result_numero_parte->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row["numero_parte"]) . "'>" . htmlspecialchars($row["numero_parte"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="cliente" class="form-select select2" required>
                            <option value="">Selecciona un Cliente</option>
                            <?php 
                            $result_cliente->data_seek(0);
                            while ($row = $result_cliente->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row["cliente"]) . "'>" . htmlspecialchars($row["cliente"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="proyecto" class="form-select select2" required>
                            <option value="">Selecciona un Proyecto</option>
                            <?php 
                            $result_proyecto->data_seek(0);
                            while ($row = $result_proyecto->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row["proyecto"]) . "'>" . htmlspecialchars($row["proyecto"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="descripcion" class="form-select select2" required>
                            <option value="">Selecciona una Descripción</option>
                            <?php 
                            $result_descripcion->data_seek(0);
                            while ($row = $result_descripcion->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row["descripcion"]) . "'>" . htmlspecialchars($row["descripcion"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select name="nave" class="form-select select2">
                            <option value="">Selecciona una Nave</option>
                            <?php 
                            $result_nave->data_seek(0);
                            while ($row = $result_nave->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row["nave"]) . "'>" . htmlspecialchars($row["nave"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 offset-md-4">
                        <button type="submit" name="submit_proyecto" class="btn btn-modern w-100 bg-success text-white">Agregar Proyecto</button>
                    </div>
                </div>
            </form>

            <!-- Formulario de búsqueda y filtro -->
            <h3>Filtrar Proyectos</h3>
            <form method="GET" action="" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por GPN o No. Parte" value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="cliente_filter" class="form-select select2" onchange="this.form.submit()">
                            <option value="">Todos los Clientes</option>
                            <?php 
                            $result_cliente->data_seek(0);
                            while ($row = $result_cliente->fetch_assoc()) {
                                $selected = ($row["cliente"] === $selected_cliente) ? "selected" : "";
                                echo "<option value='" . htmlspecialchars($row["cliente"]) . "' $selected>" . htmlspecialchars($row["cliente"]) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-modern bg-success text-white">Filtrar</button>
                    </div>
                </div>
                <input type="hidden" name="per_page" value="<?php echo $records_per_page; ?>">
                <input type="hidden" name="page" value="1">
            </form>

            <!-- Selección de registros por página -->
            <div class="mb-3">
                <form method="GET" action="">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label for="per_page" class="form-label">Mostrar:</label>
                        </div>
                        <div class="col-auto">
                            <select name="per_page" id="per_page" class="form-select" onchange="this.form.submit()">
                                <option value="20" <?php echo $records_per_page == 20 ? 'selected' : ''; ?>>20</option>
                                <option value="50" <?php echo $records_per_page == 50 ? 'selected' : ''; ?>>50</option>
                                <option value="100" <?php echo $records_per_page == 100 ? 'selected' : ''; ?>>100</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <span>registros por página</span>
                        </div>
                    </div>
                    <?php if (!empty($search_query)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                    <?php endif; ?>
                    <?php if (!empty($selected_cliente)): ?>
                        <input type="hidden" name="cliente_filter" value="<?php echo htmlspecialchars($selected_cliente); ?>">
                    <?php endif; ?>
                </form>
            </div>

            <!-- Tabla de proyectos registrados -->
            <h3 data-step="3" data-intro="Consulta la lista de proyectos registrados y edítalos o elimínalos si es necesario.">Proyectos Registrados</h3>
            <div class="table-responsive">
                <table class="table table-modern table-striped align-middle text-center">
                    <thead>
                        <tr>
                            <th scope="col">GPN</th>
                            <th scope="col">No. Parte</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Proyecto</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Nave</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result_proyectos->num_rows > 0) {
                            while ($row = $result_proyectos->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . (empty($row["gpn"]) ? "-" : htmlspecialchars($row["gpn"])) . "</td>";
                                echo "<td>" . (empty($row["numero_parte"]) ? "-" : htmlspecialchars($row["numero_parte"])) . "</td>";
                                echo "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["proyecto"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";
                                echo "<td>" . (empty($row["nave"]) ? "-" : htmlspecialchars($row["nave"])) . "</td>";
                                echo "<td>";
                                echo "<button type='button' class='btn btn-modern bg-warning me-1' data-bs-toggle='modal' data-bs-target='#editProyectoModal" . $row["id"] . "'>Editar</button>";
                                echo "<a href='?delete_proyecto=" . $row["id"] . "&per_page=$records_per_page&page=$current_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : "") . "' class='btn btn-danger btn-modern' onclick='return confirm(\"¿Estás seguro de eliminar este proyecto?\")'>Eliminar</a>";
                                echo "</td>";
                                echo "</tr>";
                                // Modal para editar proyecto
                                echo "<div class='modal fade' id='editProyectoModal" . $row["id"] . "' tabindex='-1' aria-hidden='true'>";
                                echo "<div class='modal-dialog'>";
                                echo "<div class='modal-content'>";
                                echo "<div class='modal-header'>";
                                echo "<h5 class='modal-title'>Editar Proyecto</h5>";
                                echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>";
                                echo "</div>";
                                echo "<div class='modal-body'>";
                                echo "<form action='' method='POST'>";
                                echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
                                echo "<div class='mb-3'>";
                                echo "<label for='gpn_" . $row["id"] . "' class='form-label'>GPN</label>";
                                echo "<input type='text' name='gpn' id='gpn_" . $row["id"] . "' class='form-control autocomplete-gpn' value='" . (empty($row["gpn"]) ? "" : htmlspecialchars($row["gpn"])) . "'>";
                                echo "</div>";
                                echo "<div class='mb-3'>";
                                echo "<label for='numero_parte_" . $row["id"] . "' class='form-label'>No. Parte</label>";
                                echo "<input type='text' name='numero_parte' id='numero_parte_" . $row["id"] . "' class='form-control autocomplete-numero-parte' value='" . (empty($row["numero_parte"]) ? "" : htmlspecialchars($row["numero_parte"])) . "'>";
                                echo "</div>";
                                echo "<div class='mb-3'>";
                                echo "<label for='cliente_" . $row["id"] . "' class='form-label'>Cliente</label>";
                                echo "<input type='text' name='cliente' id='cliente_" . $row["id"] . "' class='form-control autocomplete-cliente' value='" . htmlspecialchars($row["cliente"]) . "' required>";
                                echo "</div>";
                                echo "<div class='mb-3'>";
                                echo "<label for='proyecto_" . $row["id"] . "' class='form-label'>Proyecto</label>";
                                echo "<input type='text' name='proyecto' id='proyecto_" . $row["id"] . "' class='form-control autocomplete-proyecto' value='" . htmlspecialchars($row["proyecto"]) . "' required>";
                                echo "</div>";
                                echo "<div class='mb-3'>";
                                echo "<label for='descripcion_" . $row["id"] . "' class='form-label'>Descripción</label>";
                                echo "<input type='text' name='descripcion' id='descripcion_" . $row["id"] . "' class='form-control autocomplete-descripcion' value='" . htmlspecialchars($row["descripcion"]) . "' required>";
                                echo "</div>";
                                echo "<div class='mb-3'>";
                                echo "<label for='nave_" . $row["id"] . "' class='form-label'>Nave</label>";
                                echo "<input type='text' name='nave' id='nave_" . $row["id"] . "' class='form-control autocomplete-nave' value='" . (empty($row["nave"]) ? "" : htmlspecialchars($row["nave"])) . "'>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='modal-footer'>";
                                echo "<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>";
                                echo "<button type='submit' name='update_proyecto' class='btn btn-modern'>Guardar</button>";
                                echo "</form>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No hay proyectos registrados</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Paginación">
                    <ul class="pagination">
                        <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>&per_page=<?php echo $records_per_page; ?><?php echo (!empty($search_query) ? "&search=" . urlencode($search_query) : ""); ?><?php echo (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""); ?>" aria-label="Anterior">
                                <span aria-hidden="true">« Anterior</span>
                            </a>
                        </li>
                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        if ($start_page > 1) {
                            echo "<li class='page-item'><a class='page-link' href='?page=1&per_page=$records_per_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : "") . "'>1</a></li>";
                            if ($start_page > 2) {
                                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                            }
                        }
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            echo "<li class='page-item " . ($i == $current_page ? 'active' : '') . "'>";
                            echo "<a class='page-link' href='?page=$i&per_page=$records_per_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : "") . "'>$i</a>";
                            echo "</li>";
                        }
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                            }
                            echo "<li class='page-item'><a class='page-link' href='?page=$total_pages&per_page=$records_per_page" . (!empty($search_query) ? "&search=" . urlencode($search_query) : "") . (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : "") . "'>$total_pages</a></li>";
                        }
                        ?>
                        <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>&per_page=<?php echo $records_per_page; ?><?php echo (!empty($search_query) ? "&search=" . urlencode($search_query) : ""); ?><?php echo (!empty($selected_cliente) ? "&cliente_filter=" . urlencode($selected_cliente) : ""); ?>" aria-label="Siguiente">
                                <span aria-hidden="true">Siguiente »</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <br><br><br>
    <?php include('pie.php'); ?>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }

        document.getElementById('startTour').addEventListener('click', function() {
            introJs().setOptions({
                nextLabel: 'Siguiente',
                prevLabel: 'Anterior',
                doneLabel: 'Finalizar',
                showProgress: true,
                exitOnOverlayClick: false
            }).start();
        });

        // Inicializar Select2
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: function() {
                    return $(this).find('option:first-child').text();
                },
                allowClear: true,
                width: '100%',
                minimumInputLength: 0,
                tags: true,
                matcher: function(params, data) {
                    if (!params.term) {
                        return data;
                    }
                    function normalize(str) {
                        return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                    }
                    var term = normalize(params.term);
                    var text = normalize(data.text);
                    if (text.indexOf(term) > -1) {
                        return data;
                    }
                    return null;
                }
            });
        });

        // Inicializar jQuery UI Autocomplete para formularios y modales
        $(document).ready(function() {
            var gpnData = <?php echo json_encode($gpn_data); ?>;
            var numeroParteData = <?php echo json_encode($numero_parte_data); ?>;
            var clienteData = <?php echo json_encode($cliente_data); ?>;
            var proyectoData = <?php echo json_encode($proyecto_data); ?>;
            var descripcionData = <?php echo json_encode($descripcion_data); ?>;
            var naveData = <?php echo json_encode($nave_data); ?>;

            function setupAutocomplete(selector, data) {
                $(selector).autocomplete({
                    source: function(request, response) {
                        var term = request.term.toLowerCase();
                        var matches = $.grep(data, function(item) {
                            return item.label.toLowerCase().indexOf(term) > -1;
                        });
                        response(matches);
                    },
                    select: function(event, ui) {
                        $(selector).val(ui.item.label);
                        return false;
                    }
                });
            }

            // Inicializar autocomplete para el formulario principal
            setupAutocomplete("#gpn-input", gpnData);
            setupAutocomplete("#numero-parte-input", numeroParteData);
            setupAutocomplete("#cliente-input", clienteData);
            setupAutocomplete("#proyecto-input", proyectoData);
            setupAutocomplete("#descripcion-input", descripcionData);
            setupAutocomplete("#nave-input", naveData);

            // Inicializar autocomplete en los modales cuando se abren
            $('.modal').on('shown.bs.modal', function() {
                var modal = $(this);
                setupAutocomplete(modal.find('.autocomplete-gpn'), gpnData);
                setupAutocomplete(modal.find('.autocomplete-numero-parte'), numeroParteData);
                setupAutocomplete(modal.find('.autocomplete-cliente'), clienteData);
                setupAutocomplete(modal.find('.autocomplete-proyecto'), proyectoData);
                setupAutocomplete(modal.find('.autocomplete-descripcion'), descripcionData);
                setupAutocomplete(modal.find('.autocomplete-nave'), naveData);
            });
        });
    </script>
</body>
</html>