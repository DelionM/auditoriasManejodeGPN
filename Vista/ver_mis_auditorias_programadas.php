<?php
session_start(); 

$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_SESSION['numero_empleado'])) {
    header("Location: login.php");
    exit();
}


// Consultar auditorías de tipo "Capas"
$sql_capas = "SELECT id_auditoria, numero_empleado, nombre, nave, descripcion, proyecto, cliente, 
    responsable, tipo_auditoria, semana, fecha_programada, estatus, correo 
    FROM programar_auditoria 
    WHERE numero_empleado = ? AND tipo_auditoria = 'Capas'";

$stmt_capas = $conn->prepare($sql_capas);
$stmt_capas->bind_param("s", $numero_empleado_sesion);
$stmt_capas->execute();
$result_capas = $stmt_capas->get_result();

// Consultar auditorías de tipo "Procesos"
$sql_procesos = "SELECT id_auditoria, numero_empleado, nombre, nave, descripcion, proyecto, cliente, 
    responsable, tipo_auditoria, semana, fecha_programada, estatus, correo 
    FROM programar_auditoria 
    WHERE numero_empleado = ? AND tipo_auditoria = 'Procesos'";

$stmt_procesos = $conn->prepare($sql_procesos);
$stmt_procesos->bind_param("s", $numero_empleado_sesion);
$stmt_procesos->execute();
$result_procesos = $stmt_procesos->get_result();



$numero_empleado_sesion = $_SESSION['numero_empleado'];

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    session_regenerate_id(true); // Esto ayuda a prevenir ataques de sesión
    header("Location: ../login.php");
    exit();
}

$numero_empleado = isset($_GET['numero_empleado']) ? trim($_GET['numero_empleado']) : '';
$id_auditoria = isset($_GET['id_auditoria']) ? trim($_GET['id_auditoria']) : '';

$sql = "SELECT id_auditoria, numero_empleado, nombre, nave, descripcion, proyecto, cliente, 
        responsable, tipo_auditoria, semana, fecha_programada, estatus, correo 
        FROM programar_auditoria 
        WHERE numero_empleado = ?";

$params = [$numero_empleado_sesion];
$types = "s";

if (!empty($numero_empleado)) {
    $sql .= " AND numero_empleado LIKE ?";
    $params[] = "%$numero_empleado%";
    $types .= "s";
}

if (!empty($id_auditoria)) {
    $sql .= " AND id_auditoria LIKE ?";
    $params[] = "%$id_auditoria%";
    $types .= "s";
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error al preparar la consulta: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Sistema de Auditorías</title>
    <style>
        body {
            background-color: #f4f6f9;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #4e73df;
            color: white;
            font-size: 0.875rem;
        }
        .table td {
            font-size: 0.75rem;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #e9ecef;
        }
        .btn-primary {
            background-color: #4e73df;
            border: none;
        }
        .btn-primary:hover {
            background-color: #3757c4;
        }
        .btn-danger {
            background-color: #e74a3b;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Adler Pelzer Group</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active text-white" href="nuevo_index.php">Nueva Auditoría</a>
                </li>
            </ul>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="d-flex ms-auto">
    <button type="submit" name="logout" class="btn btn-danger btn-sm">Cerrar sesión</button>
</form>



        </div>
    </div>
</nav>
<br><br><br>

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card">
                <div class="card-header text-center bg-primary text-white">
                    <h2 class="h4">Auditorías Programadas</h2>
                </div>
                <div class="card-body">
                 

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Empleado</th>
                                    <th>Auditor</th>
                                    <th>Nave</th>
                                    <th>Descripción</th>
                                    <th>Proyecto</th>
                                    <th>Cliente</th>
                                    <th>Responsable</th>
                                    <th>Tipo</th>
                                    <th>Semana</th>
                                    <th>Fecha</th>
                                    <th>Estatus</th>
                                    <th>Correo</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row["id_auditoria"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["numero_empleado"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["nave"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["descripcion"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["proyecto"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["cliente"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["responsable"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["tipo_auditoria"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["semana"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["fecha_programada"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["estatus"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["correo"]) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13'>No hay registros</td></tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






<!-- 

echo "<td>
                                            <a href='vista/verRegistro.php?id=" . htmlspecialchars($row["id_auditoria"]) . "' 
                                               class='btn btn-success btn-sm'>Ver</a>
                                            <a href='controlador/eliminarRegistro.php?id=" . htmlspecialchars($row["id_auditoria"]) . "' 
                                               class='btn btn-danger btn-sm' 
                                               onclick='return confirm(\"¿Estás seguro de eliminar?\")'>Eliminar</a>
                                          </td>";
                                    echo "</tr>"; -->