<?php
// modificar_usuario.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Get user data
$numero_empleado = $_GET['numero_empleado'];
$sql = "SELECT numero_empleado, nombre, tipo, correo, estado FROM empleados WHERE numero_empleado = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $numero_empleado);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update user data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $tipo = $_POST['tipo'];
    $estado = $_POST['estado'];
    $nueva_contrasena = $_POST['contrasena']; // Obtener la contraseña sin procesar aún

    if (!empty($nueva_contrasena)) {
        // Caso 1: Se proporcionó una nueva contraseña
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
        $update_sql = "UPDATE empleados SET nombre = ?, correo = ?, tipo = ?, estado = ?, contraseña = ? WHERE numero_empleado = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssss", $nombre, $correo, $tipo, $estado, $hashed_password, $numero_empleado);
    } else {
        // Caso 2: No se proporcionó nueva contraseña, no actualizar ese campo
        $update_sql = "UPDATE empleados SET nombre = ?, correo = ?, tipo = ?, estado = ? WHERE numero_empleado = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssss", $nombre, $correo, $tipo, $estado, $numero_empleado);
    }

    if ($stmt->execute()) {
        header("Location: ver_usuarios_registrados.php?success=Usuario actualizado correctamente");
        exit();
    } else {
        $error = "Error al actualizar el usuario: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Modificar Usuario</title>
    <link rel="icon" type="image/png" href="img/images.ico">
    <style>
        body {
            background: linear-gradient(135deg, rgb(225, 237, 255) 0%, rgb(227, 238, 255) 100%);
            min-height: 100vh;
        }
        .container {
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            margin-top: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        
        .container:hover {
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
        .form-label {
            color: #2A3184;
            font-weight: 500;
        }
        .form-control, .form-select {
            border-radius: 10px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }
            .btn-modern {
                padding: 6px 12px;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg" style="background-color:#2A3184;">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="../index.php">Adler Pelzer Group</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active text-white" href="../index.php">Dashboard</a>
                </li>    
                <li class="nav-item">
                    <a class="nav-link active text-white" href="programar_auditoria.php">Programar Auditoria</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" href="ver_auditorias_programadas.php">Ver Auditorias Programadas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" href="registrar_nuevo_usuario.php">Registrar nuevo usuario</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-warning" href="ver_usuarios_registrados.php">Ver Usuario Registrado</a>
                </li> 
            </ul>
            <form action="" method="POST" class="d-flex ms-auto">
                <button type="submit" name="logout" class="btn btn-danger">Cerrar sesión</button>
            </form>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h3>Modificar Usuario</h3>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label class="form-label">Número de empleado</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['numero_empleado']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" class="form-control" name="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de usuario</label>
            <select class="form-select" name="tipo" required>
                <option value="superadmin" <?php echo $user['tipo'] == 'superadmin' ? 'selected' : ''; ?>>Superadmin</option>
                <option value="admin" <?php echo $user['tipo'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="auditor" <?php echo $user['tipo'] == 'auditor' ? 'selected' : ''; ?>>Auditor</option>
                <option value="usuario" <?php echo $user['tipo'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado" required>
                <option value="1" <?php echo $user['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo $user['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nueva contraseña (dejar en blanco si no desea cambiarla)</label>
            <input type="password" class="form-control" name="contrasena">
        </div>
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success btn-modern">Guardar cambios</button>
            <a href="ver_usuarios_registrados.php" class="btn btn-danger btn-modern">Cancelar</a>
        </div>
    </form>
</div>
<br><br><br><br>
<?php $conn->close(); ?>
<?php include('pie.php'); ?>

</body>
</html>