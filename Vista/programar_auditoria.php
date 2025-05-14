<?php
require '../vendor/autoload.php';
include_once '../Controlador/check_access.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../conexion.php';
check_permission(['superadmin', 'admin']);

// Manejo del cierre de sesión
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Obtener empleados para los selects
$query_empleados = "SELECT numero_empleado, nombre, correo FROM empleados WHERE estado = 1";
$result_empleados = $conexion->query($query_empleados);
$empleados = [];
while ($row = $result_empleados->fetch_assoc()) {
    $empleados[] = $row;
}

// Obtener proyectos para el autocomplete
$query_proyectos = "SELECT id, gpn, numero_parte, cliente, proyecto, descripcion, nave FROM proyectos WHERE proyecto IS NOT NULL AND cliente IS NOT NULL";
$result_proyectos = $conexion->query($query_proyectos);
$proyectos = [];
while ($row = $result_proyectos->fetch_assoc()) {
    $proyectos[] = $row;
}

// Procesar el formulario de auditoría
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['logout'])) {
    $num_colaborador = $_POST['num_colaborador'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $nave = $_POST['nave'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $proyecto = $_POST['proyecto'] ?? '';
    $cliente = $_POST['cliente'] ?? '';
    $tipo_auditoria = $_POST['tipo_auditoria'] ?? '';
    $semana = $_POST['semana'] ?? '';
    $gpn = $_POST['gpn'] ?? '';
    $numero_parte = $_POST['numero_parte'] ?? '';

    // Verificar si el colaborador existe
    $check_employee_sql = "SELECT COUNT(*) as count FROM empleados WHERE numero_empleado = ?";
    if ($stmt = $conexion->prepare($check_employee_sql)) {
        $stmt->bind_param("s", $num_colaborador);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee_data = $result->fetch_assoc();

        if ($employee_data['count'] == 0) {
            echo "<script>
                    showCustomAlert('error', 'Colaborador no encontrado', 'El colaborador con número $num_colaborador no existe. Debes registrarlo primero.', () => {
                        window.location.href = 'registrar_nuevo_usuario.php';
                    });
                  </script>";
            $stmt->close();
            $conexion->close();
            exit();
        }
        $stmt->close();
    }

    // Calcular los días hábiles de la semana seleccionada
    $semana_parts = explode('-W', $semana);
    $year = $semana_parts[0];
    $week = ltrim($semana_parts[1], '0');
    $lunes = new DateTime();
    $lunes->setISODate($year, $week, 1); // Lunes de la semana
    $viernes = clone $lunes;
    $viernes->modify('+4 days'); // Viernes de la misma semana

    // Formatear las fechas como "día de Mes"
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    $lunes_str = $lunes->format('j') . ' de ' . $meses[(int)$lunes->format('n')];
    $viernes_str = $viernes->format('j') . ' de ' . $meses[(int)$viernes->format('n')];

    // Guardar en la base de datos
    $query = "INSERT INTO programar_auditoria 
    (numero_empleado, nombre, nave, descripcion, proyecto, cliente, tipo_auditoria, semana, correo, estatus, gpn, numero_parte)     
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Asignada', ?, ?)";
    
    if ($stmt = $conexion->prepare($query)) {
        $stmt->bind_param('sssssssssss', $num_colaborador, $nombre, $nave, $descripcion, $proyecto, $cliente, $tipo_auditoria, $semana, $correo, $gpn, $numero_parte);
        if ($stmt->execute()) {
            $id_auditoria = $stmt->insert_id;

            // Enviar correo
            $mail = new PHPMailer(true);
            try {
                // Habilitar depuración para PHPMailer
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function($str, $level) {
                    // file_put_contents('phpmailer.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND);
                };

                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.office365.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'APG-NotReplayMX@adlerpelzer.com';
                $mail->Password = 'DXfem413';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Configuración del correo
                $mail->setFrom('APG-NotReplayMX@adlerpelzer.com', 'Sistema de Auditorías');
                $mail->addAddress($correo, $nombre);
                $mail->Subject = 'Notificación de Auditoría';
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Body = "
                    <h2>Notificación de Auditoría</h2>
                    <p>Estimado(a) <b>$nombre</b>,</p>
                    <p>Le informamos que tiene una auditoría programada con los siguientes detalles:</p>
                    <ul>
                        <li><b>Número de Colaborador:</b> $num_colaborador</li>
                        <li><b>GPN:</b> " . htmlspecialchars($gpn) . "</li>
                        <li><b>Número de Parte:</b> " . htmlspecialchars($numero_parte) . "</li>
                        <li><b>Nave:</b> $nave</li>
                        <li><b>Descripción:</b> $descripcion</li>
                        <li><b>Proyecto:</b> $proyecto</li>
                        <li><b>Cliente:</b> $cliente</li>
                        <li><b>Tipo de Auditoría:</b> $tipo_auditoria</li>
                        <li><b>Fecha:</b> Del $lunes_str al $viernes_str</li>
                        <li><b>Estatus:</b> Asignada</li>
                    </ul>
                    <p>Para realizar la auditoría, por favor ingrese al siguiente enlace:</p>
                    <p><a href='http://192.168.60.48/auditoria/login.php' target='_blank'>Acceder al Sistema</a></p>
                    <p>Saludos.</p>
                ";

                // Enviar el correo
                $mail->send();
                echo "<script>
                        showCustomAlert('success', 'Éxito', 'Auditoría programada y correo enviado exitosamente.', () => {
                            window.location.href = '../index.php';
                        });
                      </script>";
            } catch (Exception $e) {
                echo "<script>
                        showCustomAlert('error', 'Error al enviar correo', 'No se pudo enviar el correo. Error: " . addslashes($mail->ErrorInfo) . "');
                      </script>";
            }
        } else {
            echo "<script>
                    showCustomAlert('error', 'Error', 'Error al guardar los datos: " . addslashes($stmt->error) . "');
                  </script>";
        }
        $stmt->close();
    }
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
    <title>Programar Auditoría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/intro.js@7.0.0/introjs.min.css" rel="stylesheet">
    <style>
        body { 
            background-color: rgb(255, 255, 255); 
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
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
            padding: 10px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
        .form-container { 
            padding: 20px; 
            border-radius: 15px; 
            background: #fff; 
            box-shadow: 0 8px 16px rgba(80, 139, 228, 0.1); 
            max-width: 500px; 
            margin: 0 auto; 
            font-size: 0.9rem;
        }
        .form-title { 
            color: #2A3184; 
            font-weight: bold; 
            font-size: 1.5rem; 
            margin-bottom: 15px; 
        }
        .btn-primary { 
            background-color: #2A3184; 
            border: none; 
            font-size: 0.9rem; 
            padding: 8px 20px; 
        }
        .btn-primary:hover { 
            background-color: #1e2466; 
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2A3184;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 10px auto;
            display: none;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .autocomplete-container { 
            position: relative; 
        }
        .autocomplete-list {
            position: absolute;
            z-index: 1000;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 0.25rem;
            background-color: #fff;
            display: none;
            font-size: 0.9rem;
        }
        .autocomplete-item {
            padding: 6px;
            cursor: pointer;
        }
        .autocomplete-item:hover {
            background-color: #f8f9fa;
        }
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 4px;
            color: #333;
        }
        .form-control, .form-select {
            font-size: 0.9rem;
            padding: 6px 12px;
        }
        .readonly-field {
            border: none;
            background: transparent;
            font-size: 0.9rem;
            color: #333;
            padding: 6px 0;
            width: 100%;
            pointer-events: none;
        }
        .custom-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            z-index: 2000;
            animation: fadeIn 0.3s ease-in;
            display: none;
        }
        .custom-alert.success {
            border-left: 5px solid #28a745;
        }
        .custom-alert.error {
            border-left: 5px solid #dc3545;
        }
        .custom-alert h3 {
            margin: 0 0 10px;
            font-size: 1.2rem;
            color: #2A3184;
        }
        .custom-alert p {
            margin: 0 0 15px;
            font-size: 0.9rem;
            color: #333;
        }
        .custom-alert button {
            background: #2A3184;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .custom-alert button:hover {
            background: #1e2466;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
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
                margin-left: 20px; 
                padding: 5px; 
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
            .form-container { 
                padding: 15px; 
                max-width: 100%; 
            }
            .form-title {
                font-size: 1.3rem;
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
                <a class="nav-link" href="../index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
            </li>
            <?php if ($_SESSION["tipo"] === 'superadmin' || $_SESSION["tipo"] === "admin"): ?>
                <li class="nav-item">
                    <a class="nav-link active" href="programar_auditoria.php"><i class="fas fa-calendar-plus me-2"></i>Programar Auditoría</a>
                </li>
            <?php endif; ?>
            <?php if ($_SESSION["tipo"] === 'superadmin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="registrar_nuevo_usuario.php"><i class="fas fa-user-plus me-2"></i>Registrar Nuevo Usuario</a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="ver_auditorias_programadas.php"><i class="fas fa-calendar-check me-2"></i>Ver Auditorías Programadas</a>
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
                <a class="nav-link" href="cambiar_contraseña.php"><i class="fas fa-key me-2"></i>Cambiar Contraseña</a>
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
        <div class="container my-3">
            <!-- <button id="startTour" class="btn btn-info mb-2">Iniciar Guía</button> -->
            <h1 class="text-center form-title">Programar Auditoría</h1>
            <div class="form-container">
                <form method="POST" id="auditoriaForm">
                    <div class="mb-2" data-step="1" data-intro="Ingresa el número del colaborador aquí para comenzar.">
                        <label for="num_colaborador" class="form-label"> <strong> Número de Colaborador</strong></label>
                        <div class="autocomplete-container">
                            <input type="text" class="form-control rounded-pill" name="num_colaborador" id="num_colaborador" placeholder="Escribe un número" required>
                            <div class="autocomplete-list" id="num_colaborador_list"></div>
                        </div>
                    </div>
                    <div class="mb-2" data-step="2" data-intro="El nombre se autocompletará al seleccionar el colaborador.">
                        <label for="nombre" class="form-label"> <strong>  Nombre del Colaborador </strong></label>
                        <div class="autocomplete-container">
                            <input type="text" class="form-control rounded-pill" name="nombre" id="nombre" placeholder="Escribe un nombre" required>
                            <div class="autocomplete-list" id="nombre_list"></div>
                        </div>
                    </div>
                    <div class="mb-2" data-step="3" data-intro="El correo se autocompletará al seleccionar el colaborador.">
                        <label for="correo" class="form-label"> <strong> Correo Electrónico </strong></label>
                        <input type="text" class="readonly-field" name="correo" id="correo" readonly required>
                    </div>
                    <div class="mb-2" data-step="4" data-intro="Escribe el GPN para autocompletar los campos relacionados.">
                        <label for="gpn" class="form-label"> <strong> GPN</strong></label>
                        <div class="autocomplete-container">
                            <input type="text" class="form-control rounded-pill" name="gpn" id="gpn" placeholder="Escribe el GPN">
                            <div class="autocomplete-list" id="gpn_list"></div>
                        </div>
                    </div>
                    <div class="mb-2" data-step="5" data-intro="Escribe el número de parte para autocompletar los campos relacionados.">
                        <label for="numero_parte" class="form-label"><strong> Número de Parte</strong></label>
                        <div class="autocomplete-container">
                            <input type="text" class="form-control rounded-pill" name="numero_parte" id="numero_parte" placeholder="Escribe el número de parte">
                            <div class="autocomplete-list" id="numero_parte_list"></div>
                            <input type="hidden" name="proyecto_id" id="proyecto_id">
                        </div>
                    </div>
                    <div class="mb-2" data-step="6" data-intro="El cliente se autocompletará basado en el GPN o número de parte seleccionado.">
                        <label for="cliente" class="form-label"> <strong>Cliente</strong></label>
                        <input type="text" class="readonly-field" name="cliente" id="cliente" readonly required>
                    </div>
                    <div class="mb-2" data-step="7" data-intro="El proyecto se autocompletará basado en el GPN o número de parte seleccionado.">
                        <label for="proyecto" class="form-label">  <strong> Proyecto</strong></label>
                        <input type="text" class="readonly-field" name="proyecto" id="proyecto" readonly required>
                    </div>
                    <div class="mb-2" data-step="8" data-intro="La descripción se autocompletará basado en el GPN o número de parte seleccionado.">
                        <label for="descripcion" class="form-label"> <strong> Descripción</strong></label>
                        <input type="text" class="readonly-field" name="descripcion" id="descripcion" readonly required>
                    </div>
                  <div class="mb-2" data-step="9" data-intro="La nave se autocompletará basado en el GPN o número de parte seleccionado.">
                        <label for="nave" class="form-label"> <strong> Nave</strong></label>
                        <input type="text" class="readonly-field" name="nave" id="nave" readonly required>
                    </div>
                    <div class="mb-2" data-step="10" data-intro="Define el tipo de auditoría a realizar.">
                        <label for="tipo_auditoria" class="form-label"> <strong> Tipo de Auditoría</strong></label>
                        <select class="form-control rounded-pill" name="tipo_auditoria" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="auditoria por Capas">Auditoría por Capas</option>
                            <option value="auditoria por Procesos">Auditoría por Procesos</option>
                        </select>
                    </div>
                    <div class="mb-2" data-step="11" data-intro="Selecciona la semana en la que se programará la auditoría.">
                        <label for="semana" class="form-label"> <strong> Número de Semana</strong></label>
                        <input type="week" class="form-control rounded-pill" name="semana" required>
                    </div>
                    <div class="text-center mt-3" data-step="12" data-intro="Haz clic aquí para enviar la notificación una vez completes el formulario.">
                        <button type="submit" class="btn btn-primary rounded-pill">Enviar Notificación</button>
                    </div>
                    <div class="loader" id="loader"></div>
                </form>
            </div>
        </div>
    </div>
    <?php include('pie.php'); ?>

    <!-- Custom Alert HTML -->
    <div id="customAlert" class="custom-alert">
        <h3 id="alertTitle"></h3>
        <p id="alertMessage"></p>
        <button id="alertButton">Aceptar</button>
    </div>
<br><br><br><br>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.0.0/introjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('content').classList.toggle('active');
        }

        // Iniciar el tour
        document.getElementById('startTour').addEventListener('click', function() {
            introJs().setOptions({
                nextLabel: 'Siguiente',
                prevLabel: 'Anterior',
                doneLabel: 'Finalizar',
                showProgress: true,
                exitOnOverlayClick: false
            }).start();
        });

        // Custom Alert Function
        function showCustomAlert(type, title, message, callback) {
            const alert = document.getElementById('customAlert');
            const alertTitle = document.getElementById('alertTitle');
            const alertMessage = document.getElementById('alertMessage');
            const alertButton = document.getElementById('alertButton');

            alert.className = `custom-alert ${type}`;
            alertTitle.textContent = title;
            alertMessage.textContent = message;
            alert.style.display = 'block';

            alertButton.onclick = () => {
                alert.style.display = 'none';
                if (callback) callback();
            };
        }

        const empleados = <?php echo json_encode($empleados); ?>;
        const proyectos = <?php echo json_encode($proyectos); ?>;

        // Función para mostrar la lista de autocompletado
        function showAutocomplete(inputId, listId, data, key, callback) {
            const input = document.getElementById(inputId);
            const list = document.getElementById(listId);

            input.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                list.innerHTML = '';
                if (!value) {
                    list.style.display = 'none';
                    return;
                }

                const filtered = data.filter(item => 
                    item[key] && item[key].toLowerCase().includes(value)
                );
                if (filtered.length > 0) {
                    filtered.forEach(item => {
                        const div = document.createElement('div');
                        div.className = 'autocomplete-item';
                        div.textContent = item[key];
                        div.addEventListener('click', () => {
                            input.value = item[key];
                            list.style.display = 'none';
                            callback(item);
                        });
                        list.appendChild(div);
                    });
                    list.style.display = 'block';
                } else {
                    list.style.display = 'none';
                }
            });

            document.addEventListener('click', (e) => {
                if (!input.contains(e.target) && !list.contains(e.target)) {
                    list.style.display = 'none';
                }
            });
        }

        // Autocompletado para número de colaborador
        showAutocomplete('num_colaborador', 'num_colaborador_list', empleados, 'numero_empleado', (empleado) => {
            document.getElementById('num_colaborador').value = empleado.numero_empleado;
            document.getElementById('nombre').value = empleado.nombre;
            document.getElementById('correo').value = empleado.correo;
        });

        // Autocompletado para nombre
        showAutocomplete('nombre', 'nombre_list', empleados, 'nombre', (empleado) => {
            document.getElementById('num_colaborador').value = empleado.numero_empleado;
            document.getElementById('correo').value = empleado.correo;
        });

        // Autocompletado para GPN
        showAutocomplete('gpn', 'gpn_list', proyectos, 'gpn', (proyecto) => {
            document.getElementById('proyecto_id').value = proyecto.id;
            document.getElementById('numero_parte').value = proyecto.numero_parte || '';
            document.getElementById('cliente').value = proyecto.cliente;
            document.getElementById('proyecto').value = proyecto.proyecto;
            document.getElementById('descripcion').value = proyecto.descripcion;
            document.getElementById('nave').value = proyecto.nave || ''; // Agregar esta línea
        });

        // Autocompletado para número de parte
        showAutocomplete('numero_parte', 'numero_parte_list', proyectos, 'numero_parte', (proyecto) => {
            document.getElementById('proyecto_id').value = proyecto.id;
            document.getElementById('gpn').value = proyecto.gpn || '';
            document.getElementById('cliente').value = proyecto.cliente;
            document.getElementById('proyecto').value = proyecto.proyecto;
            document.getElementById('descripcion').value = proyecto.descripcion;
            document.getElementById('nave').value = proyecto.nave || ''; // Agregar esta línea
        });

        // Validar que al menos GPN o número de parte esté lleno
        document.getElementById('auditoriaForm').addEventListener('submit', function(e) {
            const gpn = document.getElementById('gpn').value;
            const numero_parte = document.getElementById('numero_parte').value;
            if (!gpn && !numero_parte) {
                e.preventDefault();
                showCustomAlert('error', 'Error', 'Debes ingresar al menos un GPN o número de parte.');
                document.getElementById('loader').style.display = 'none';
            } else {
                document.getElementById('loader').style.display = 'block';
            }
        });
    </script>
</body>
</html>