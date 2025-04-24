<?php
session_start();
include_once 'conexion.php';  // Verifica que la conexión sea correcta
$mensaje = "";
$tipo_mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero_empleado = $_POST['numero_empleado'];
    $password = $_POST['password'];

    // Preparar la consulta para buscar por número de empleado
    $stmt = $conexion->prepare("SELECT numero_empleado, contraseña, tipo FROM empleados WHERE numero_empleado = ?");
    if ($stmt === false) {
        die('Error en la consulta: ' . $conexion->error);
    }

    // Dado que el número de empleado es un número, se utiliza "i" para indicar entero
    $stmt->bind_param("i", $numero_empleado);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Asignar las variables con el resultado de la consulta
        $stmt->bind_result($numero_empleado_bd, $hashed_password, $tipo);
        $stmt->fetch();

        // Verificar si la contraseña es correcta
        if (password_verify($password, $hashed_password)) {
            $_SESSION["numero_empleado"] = $numero_empleado_bd;
            $_SESSION["tipo"] = $tipo;

            // Redirigir según el tipo de usuario
            switch ($tipo) {
                case "superadmin":
                    header("Location: index.php");  // Vista para superadmin
                    break;
                case "admin":
                    header("Location: index.php");  // Vista para administradores
                    break;
                case "auditor":
                    header("Location: index.php");  // Vista para auditores
                    break;
                case "usuario":
                    header("Location: Vista/mis_auditorias.php");  // Vista para usuarios normales
                    break;
                default:
                    $mensaje = "Tipo de usuario no válido.";
                    $tipo_mensaje = "danger";
                    break;
            }
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
            $tipo_mensaje = "danger";
        }
    } else {
        $mensaje = "Número de empleado no encontrado.";
        $tipo_mensaje = "warning";
    }
    $stmt->close();
}
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Auditorias</title>
    <meta name="author" content="Delion">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" type="image/png" href="img/images.ico">
    <style>
        .background-radial-gradient {
            background-color: hsl(218, 41%, 15%);
            background-image: radial-gradient(650px circle at 0% 0%,
                    hsl(218, 41%, 35%) 15%,
                    hsl(218, 41%, 30%) 35%,
                    hsl(218, 41%, 20%) 75%,
                    hsl(218, 41%, 19%) 80%,
                    transparent 100%),
                radial-gradient(1250px circle at 100% 100%,
                    hsl(218, 41%, 45%) 15%,
                    hsl(218, 41%, 30%) 35%,
                    hsl(218, 41%, 20%) 75%,
                    hsl(218, 41%, 19%) 80%,
                    transparent 100%);
        }

        #radius-shape-1 {
            height: 220px;
            width: 220px;
            top: -60px;
            left: -130px;
            background: radial-gradient(#44006b, #ad1fff);
            overflow: hidden;
            position: absolute;
            animation: float1 6s ease-in-out infinite;
        }

        #radius-shape-2 {
            border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
            bottom: -60px;
            right: -110px;
            width: 300px;
            height: 300px;
            background: radial-gradient(#44006b, #ad1fff);
            overflow: hidden;
            position: absolute;
            animation: float2 8s ease-in-out infinite;
        }

        .bg-glass {
            background-color: hsla(0, 0.00%, 100.00%, 0.82) !important;
            backdrop-filter: saturate(200%) blur(25px);
        }

        /* Animaciones suaves */
        @keyframes float1 {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(-20px) translateX(15px);
            }
        }

        @keyframes float2 {

            0%,
            100% {
                transform: translateY(0) translateX(0);
            }

            50% {
                transform: translateY(25px) translateX(-20px);
            }
        }
    </style>
</head>

<body class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(9, 84.00%, 95.10%)">
                    Adler Pelzer Group <br />
                    <span style="color: hsl(218, 81%, 75%)">Plataforma Digital de Auditorías</span>
                </h1>
                <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                    Bienvenido a la Plataforma Digital de Auditorías, Adler Pelzer Group Pachuca.
                    Por favor, ingrese sus credenciales para acceder a la plataforma.
                </p>
            </div>
            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <h3 class="text-center mb-4">Iniciar Sesión</h3>
                        <?php if ($mensaje): ?>
                            <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                                <?php echo $mensaje; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-4">
                                <label for="numero_empleado" class="form-label">Número de Empleado</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control" id="numero_empleado" name="numero_empleado"
                                        placeholder="Ingrese su número de empleado" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                        placeholder="Ingrese su contraseña" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-4">Ingresar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include("Vista/pie.php") ?>
</body>
</html>