<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['numero_empleado'])) {
    header("Location: ../login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$numero_empleado = $_SESSION['numero_empleado'];
$id_auditoria = isset($_GET['id_auditoria']) ? $_GET['id_auditoria'] : null;

if ($id_auditoria === null) {
    die("No valid audit ID provided.");
}

// Obtener datos de la auditoría, incluyendo el tipo y el estatus
$sql = "SELECT 
    pa.id_auditoria, 
    pa.numero_empleado, 
    pa.nombre, 
    pa.nave, 
    pa.cliente, 
    pa.descripcion,
    pa.tipo_auditoria,
    pa.numero_parte,
    pa.estatus
FROM programar_auditoria pa 
WHERE pa.id_auditoria = ? AND pa.numero_empleado = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation error: " . $conn->error);
}

$stmt->bind_param("ss", $id_auditoria, $numero_empleado);
$stmt->execute();
$result = $stmt->get_result();
$audit_data = [];
if ($result->num_rows > 0) {
    $audit_data = $result->fetch_assoc();
    $_SESSION['nombre'] = $audit_data['nombre'];
    $_SESSION['id_auditoria'] = $audit_data['id_auditoria'];

    // Validar que el tipo de auditoría sea correcto
    if ($audit_data['tipo_auditoria'] !== 'auditoria por Procesos') {
        die("Error: Este ID corresponde a una Auditoría por Capas. Accede a nuevo_index.php.");
    }
} else {
    die("No audit found for the provided ID and employee.");
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDITORIAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <link rel="icon" type="image/png" href="img/images.ico">
    <style>
    /* Estilo para el overlay del modal */
   /* Estilo para el overlay del modal */
/* Forzar color negro a absolutamente todas las letras en todo el body */
body * {
    color: #000000 !important; /* Negro obligatorio para todos los elementos en el body */
}

/* Estilo para el overlay del modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6); /* Fondo oscuro semi-transparente */
    z-index: 999;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Estilo para el modal */
.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 10px; /* Reducido el padding */
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    width: 70%;
    max-width: 500px;
    display: none;
    height: 60%;
}

/* Contenido del modal */
.modal-content {
    position: relative;
}

/* Ajuste para formularios dentro del modal */
.table-responsive .form-control {
    font-size: 14px;
}

/* Asegurar que los placeholders también sean negros */
.table-responsive .form-control::placeholder {
    color: #000000 !important; /* Placeholders en negro */
    opacity: 1;
}

/* Botón de cerrar */
.close {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 38px;
    cursor: pointer;
    transition: color 0.3s;
}

.close:hover {
    color: #000000 !important; /* Negro incluso al pasar el ratón */
}

/* Estilos para los elementos del formulario */
.modal-content h2 {
    margin-bottom: 10px;
    font-size: 28px;
    text-align: center;
}

.modal-content label {
    font-weight: bold;
    margin-top: 10px;
    display: block;
    font-size: 14px;
}

.modal-content textarea,
.modal-content select,
.modal-content input[type="date"] {
    width: 100%;
    margin-top: 3px;
    margin-bottom: 8px;
    font-size: 14px;
}

.modal-content .btn {
    margin-top: 10px;
    width: 100%;
    padding: 6px;
    font-size: 14px;
}

    /* Ajuste para el Dropzone */
  
</style>

</head>
<body>
    <nav class="navbar" style="background-color:#2A3184">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="index.php">Adler Pelzer Group</a>
            <a class="navbar-brand text-white" href="Vista/ver_registro_por_usuario.php">Ver mis registros</a>
            <a class="navbar-brand text-white" href="Vista/mis_auditorias.php">Mis auditorias programadas</a>
            <form action="Controlador/logout.php" method="POST" class="d-flex ms-auto">
                <button type="submit" class="btn btn-danger">Cerrar sesión</button>
            </form>
        </div>
    </nav>
    <div class="container-fluid mt-5">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h3 class="text-center me-3 text-justify">AUDITORIA DE PROCESO </h3>
        </div>
        <h5 class="text-center me-3 text-justify">Folio: 
            <span id="numeroDocumento"><?php echo htmlspecialchars($id_auditoria); ?></span>
        </h5> <br>
        <!-- <input type="hidden" id="idAuditoria" name="id_auditoria" value="<?php echo htmlspecialchars($id_auditoria); ?>"> -->

        <div class="table-responsive" style="max-width: 95%; margin: 0 auto;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-info">
                    <tr>
                        <th>Número de colaborador:</th>
                        <th>Nombre del Auditor:</th>
                        <th>Cliente:</th>
                        <th>Proceso auditado:</th>
                        <th>No. De parte auditada</th>
                        <th>Nivel de Ingeniería:</th>
                        <th>Nave:</th>
                        <th>Supervisor</th>
                        <th>Unidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" id="idNumeroEmpleado" class="form-control" value="<?php echo htmlspecialchars($audit_data['numero_empleado']); ?>" readonly>
                        </td>
                        <td>
                            <input type="text" id="idNombreAuditor" class="form-control" value="<?php echo htmlspecialchars($audit_data['nombre']); ?>" readonly>
                        </td>
                        <td>
                            <input type="text" id="idCliente" class="form-control" value="<?php echo htmlspecialchars($audit_data['cliente']); ?>" required>
                        </td>
                        <td>
                            <input type="text" id="idProcesoAuditado" class="form-control" value="<?php echo htmlspecialchars($audit_data['descripcion']); ?>" required>
                        </td>
                        <td>
                            <input type="text" id="idParteAuditada" class="form-control" value="<?php echo htmlspecialchars($audit_data['numero_parte']); ?>" required>
                        </td>
                        <td>
                            <input type="text" id="idNivelIngenieria" class="form-control" placeholder=""required>
                        </td>
                        <td>
                            <select id="idNave" class="form-control" required>
                                <option value="<?php echo htmlspecialchars($audit_data['nave']); ?>" selected><?php echo htmlspecialchars($audit_data['nave']); ?></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="7A">7A</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="14">14</option>
                            </select>
                        </td>
                        <td>
                        <input type="text" id="idNombreSupervisor" class="form-control" placeholder="" required>

                        </td>
                        <td>
                            <select id="idUnidad" class="form-control" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="Unidad 1">Unidad 1</option>
                                <option value="Unidad 2">Unidad 2</option>
                                <option value="Unidad 3">Unidad 3</option>
                                <option value="Unidad 4">Unidad 4</option>
                            </select>
                        </td>
                        <td>
                            <input type="date" id="idFecha" class="form-control" required>
                        </td>
                    </tr>
                    <!-- Campo oculto para id_auditoria -->
                    <tr>
                        <td colspan="9">
                            <input type="hidden" id="idAuditoria" name="id_auditoria" value="<?php echo htmlspecialchars($id_auditoria); ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h6  class="d-flex justify-content-center align-items-center mb-4"> OK=Conforme  NOK=No Conforme   NA=No Aplica</h6>
        <!-- Tabla adicional para los encabezados de las 8 columnas -->
        <!-- <div id="modalOverlay" class="modal-overlay"></div>
            <div id="modalObservaciones" class="modal">
                <div class="modal-content">
                <span id="closeModal" class="close"><i class="fas fa-times"></i></span>          
                    <h2>Observaciones y Acciones</h2>
                    <label for="idObservaciones1.1">Observaciones:</label>
                    <textarea id="idObservaciones1.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <label for="idAcciones1.1">Acciones:</label>
                    <textarea id="idAcciones1.1" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas1.1">Problemas Comunes:</label>
                    <select id="idProblemas1.1" class="form-control" required>

                    </select>
                    <br>
                    <label for="">Fecha Compromiso</label>
                    <td><input type="date" id="idFechaFila1.1" class="form-control fecha"></td>

                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_1_1">
                            <input type="file" id="archivo_1_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <button id="btnGuardarDatos" class="btn bg-success text-white">Guardar</button>
                </div>
            </div> -->
        <!-- Modal 1.1 (Ejemplo, replicar para otras secciones) -->
                <div id="modalOverlay1" class="modal-overlay"></div>
                    <div id="modalObservaciones1" class="modal">
                        <div class="modal-content">
                            <span id="closeModal1" class="close"><i class="fas fa-times"></i></span>
                            <h2>Observaciones y Acciones</h2>
                            <label for="idObservaciones1">Observaciones:</label>
                            <textarea id="idObservaciones1" class="form-control observaciones" placeholder="Descripción"></textarea>
                            <label for="idAcciones1">Acciones:</label>
                            <textarea id="idAcciones1" class="form-control acciones" placeholder="Descripción"></textarea>
                            <label for="idProblemas1">Problemas Comunes:</label>
                            <select id="idProblemas1" class="form-control" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
                            </select>
                            <label for="idFechaFila1">Fecha Compromiso</label>
                            <input type="date" id="idFechaFila1" class="form-control fecha">
                            <div class="input-group justify-content-center">
                                <form id="formArchivo1" class="dropzone archivo-dropzone">
                                    <div class="dz-message">Arrastra y suelta tu archivo aquí o haz clic para seleccionar</div>
                                    <input type="file" id="archivo_1" name="archivo" class="archivo-input">
                                    <input type="hidden" name="numeroEmpleado" value="<?php echo htmlspecialchars($numero_empleado); ?>">
                                </form>
                            </div>
                            <button id="btnGuardarDatos1" class="btn bg-success text-white">Guardar</button>
                        </div>
                    </div>            
            <!-- MODAL DEL 2 -->
            <div id="modalOverlay2" class="modal-overlay"></div>
                <div id="modalObservaciones2" class="modal">
                    <div class="modal-content">
                        <span id="closeModal2" class="close">×</span>
                        <h2>Observaciones y Acciones</h2>

                        <!-- Observaciones -->
                        <label for="idObservaciones2">Observaciones:</label>
                        <textarea id="idObservaciones2" class="form-control observaciones" placeholder="Descripción"></textarea>

                        <!-- Acciones -->
                        <label for="idAcciones2">Acciones:</label>
                        <textarea id="idAcciones2" class="form-control acciones" placeholder="Descripción"></textarea>

                        <!-- Problemas comunes -->
                        <label for="idProblemas2">Problemas Comunes:</label>
                        <select id="idProblemas2" class="form-control" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
                        </select>

                        <!-- Fecha -->
                        <label for="idFechaFila2">Fecha</label>
                        <input type="date" id="idFechaFila2" class="form-control fecha">

                        <!-- Selección de archivo -->
                        <div class="input-group justify-content-center">
                            <input type="file" id="archivo_2" name="archivo" class="form-control archivo-input">
                        </div>

                        <!-- Botón para guardar datos -->
                        <button id="btnGuardarDatos2" class="btn bg-success text-white">Guardar</button>
                    </div>
                </div>
            <!-- MODAL DEL 3 -->
            <div id="modalOverlay3" class="modal-overlay"></div>
                <div id="modalObservaciones3" class="modal">
                    <div class="modal-content">
                        <span id="closeModal3" class="close">×</span>
                        <h2>Observaciones y Acciones</h2>

                        <!-- Observaciones -->
                        <label for="idObservaciones3">Observaciones:</label>
                        <textarea id="idObservaciones3" class="form-control observaciones" placeholder="Descripción"></textarea>

                        <!-- Acciones -->
                        <label for="idAcciones3">Acciones:</label>
                        <textarea id="idAcciones3" class="form-control acciones" placeholder="Descripción"></textarea>

                        <!-- Problemas comunes -->
                        <label for="idProblemas3">Problemas Comunes:</label>
                        <select id="idProblemas3" class="form-control" required>
                        <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
                            <!-- Otras opciones -->
                        </select>

                        <!-- Fecha -->
                        <label for="idFechaFila3">Fecha</label>
                        <input type="date" id="idFechaFila3" class="form-control fecha">

                        <!-- Selección de archivo -->
                        <div class="input-group justify-content-center">
                            <input type="file" id="archivo_3" name="archivo" class="form-control archivo-input">
                        </div>

                        <!-- Botón para guardar datos -->
                        <button id="btnGuardarDatos3" class="btn bg-success text-white">Guardar</button>
                    </div>
                </div>
              <!-- MODAL DEL 4-->
              <div id="modalOverlay4" class="modal-overlay"></div>
                    <div id="modalObservaciones4" class="modal">
                        <div class="modal-content">
                            <span id="closeModal4" class="close">×</span>
                            <h2>Observaciones y Acciones</h2>

                            <!-- Observaciones -->
                            <label for="idObservaciones4">Observaciones:</label>
                            <textarea id="idObservaciones4" class="form-control observaciones" placeholder="Descripción"></textarea>

                            <!-- Acciones -->
                            <label for="idAcciones4">Acciones:</label>
                            <textarea id="idAcciones4" class="form-control acciones" placeholder="Descripción"></textarea>

                            <!-- Problemas comunes -->
                            <label for="idProblemas4">Problemas Comunes:</label>
                            <select id="idProblemas4" class="form-control" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
                            </select>

                            <!-- Fecha -->
                            <label for="idFechaFila4">Fecha</label>
                            <input type="date" id="idFechaFila4" class="form-control fecha">

                            <!-- Selección de archivo -->
                            <div class="input-group justify-content-center">
                                <input type="file" id="archivo_4" name="archivo" class="form-control archivo-input">
                            </div>

                            <!-- Botón para guardar datos -->
                            <button id="btnGuardarDatos4" class="btn bg-success text-white">Guardar</button>
                        </div>
                    </div>

            <!-- MODAL DEL5 -->
            <div id="modalOverlay5" class="modal-overlay"></div>
                    <div id="modalObservaciones5" class="modal">
                        <div class="modal-content">
                            <span id="closeModal5" class="close">×</span>
                            <h2>Observaciones y Acciones</h2>

                            <!-- Observaciones -->
                            <label for="idObservaciones5">Observaciones:</label>
                            <textarea id="idObservaciones5" class="form-control observaciones" placeholder="Descripción"></textarea>

                            <!-- Acciones -->
                            <label for="idAcciones5">Acciones:</label>
                            <textarea id="idAcciones5" class="form-control acciones" placeholder="Descripción"></textarea>

                            <!-- Problemas comunes -->
                            <label for="idProblemas5">Problemas Comunes:</label>
                            <select id="idProblemas5" class="form-control" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
                            </select>

                            <!-- Fecha -->
                            <label for="idFechaFila5">Fecha</label>
                            <input type="date" id="idFechaFila5" class="form-control fecha">

                            <!-- Selección de archivo -->
                            <div class="input-group justify-content-center">
                                <input type="file" id="archivo_5" name="archivo" class="form-control archivo-input">
                            </div>

                            <!-- Botón para guardar datos -->
                            <button id="btnGuardarDatos5" class="btn bg-success text-white">Guardar</button>
                        </div>
                    </div>

            <!-- MODAL DEL 6 -->
            <div id="modalOverlay6" class="modal-overlay"></div>
<div id="modalObservaciones6" class="modal">
    <div class="modal-content">
        <span id="closeModal6" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones6">Observaciones:</label>
        <textarea id="idObservaciones6" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones6">Acciones:</label>
        <textarea id="idAcciones6" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas6">Problemas Comunes:</label>
        <select id="idProblemas6" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila6">Fecha</label>
        <input type="date" id="idFechaFila6" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_6" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos6" class="btn bg-success text-white">Guardar</button>
    </div>
</div>
            <!-- MODAL DEL 7 -->
    <!-- MODAL DEL 7 -->
                <div id="modalOverlay7" class="modal-overlay"></div>
                <div id="modalObservaciones7" class="modal">
                    <div class="modal-content">
                        <span id="closeModal7" class="close">×</span>
                        <h2>Observaciones y Acciones</h2>

                        <!-- Observaciones -->
                        <label for="idObservaciones7">Observaciones:</label>
                        <textarea id="idObservaciones7" class="form-control observaciones" placeholder="Descripción"></textarea>

                        <!-- Acciones -->
                        <label for="idAcciones7">Acciones:</label>
                        <textarea id="idAcciones7" class="form-control acciones" placeholder="Descripción"></textarea>

                        <!-- Problemas comunes -->
                        <label for="idProblemas7">Problemas Comunes:</label>
                        <select id="idProblemas7" class="form-control" required>
                            <option value="" disabled selected>Selecciona una opción</option>
                            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
                        </select>

                        <!-- Fecha -->
                        <label for="idFechaFila7">Fecha</label>
                        <input type="date" id="idFechaFila7" class="form-control fecha">

                        <!-- Selección de archivo -->
                        <div class="input-group justify-content-center">
                            <input type="file" id="archivo_7" name="archivo" class="form-control archivo-input">
                        </div>

                        <!-- Botón para guardar datos -->
                        <button id="btnGuardarDatos7" class="btn bg-success text-white">Guardar</button>
                    </div>
                </div>

              <!-- MODAL DEL 8 -->
  <!-- MODAL DEL 8 -->
<div id="modalOverlay8" class="modal-overlay"></div>
<div id="modalObservaciones8" class="modal">
    <div class="modal-content">
        <span id="closeModal8" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones8">Observaciones:</label>
        <textarea id="idObservaciones8" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones8">Acciones:</label>
        <textarea id="idAcciones8" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas8">Problemas Comunes:</label>
        <select id="idProblemas8" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila8">Fecha</label>
        <input type="date" id="idFechaFila8" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_8" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos8" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

          <!-- MODAL DEL 9 -->
<div id="modalOverlay9" class="modal-overlay"></div>
<div id="modalObservaciones9" class="modal">
    <div class="modal-content">
        <span id="closeModal9" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones9">Observaciones:</label>
        <textarea id="idObservaciones9" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones9">Acciones:</label>
        <textarea id="idAcciones9" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas9">Problemas Comunes:</label>
        <select id="idProblemas9" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila9">Fecha</label>
        <input type="date" id="idFechaFila9" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_9" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos9" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

<!-- MODAL DEL 10 -->
<div id="modalOverlay10" class="modal-overlay"></div>
<div id="modalObservaciones10" class="modal">
    <div class="modal-content">
        <span id="closeModal10" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones10">Observaciones:</label>
        <textarea id="idObservaciones10" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones10">Acciones:</label>
        <textarea id="idAcciones10" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas10">Problemas Comunes:</label>
        <select id="idProblemas10" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila10">Fecha</label>
        <input type="date" id="idFechaFila10" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_10" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos10" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

           <!-- MODAL DEL 11 -->
<div id="modalOverlay11" class="modal-overlay"></div>
<div id="modalObservaciones11" class="modal">
    <div class="modal-content">
        <span id="closeModal11" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones11">Observaciones:</label>
        <textarea id="idObservaciones11" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones11">Acciones:</label>
        <textarea id="idAcciones11" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas11">Problemas Comunes:</label>
        <select id="idProblemas11" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila11">Fecha</label>
        <input type="date" id="idFechaFila11" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_11" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos11" class="btn bg-success text-white">Guardar</button>
    </div>
</div>
        <!-- MODAL DEL 12 -->
<div id="modalOverlay12" class="modal-overlay"></div>
<div id="modalObservaciones12" class="modal">
    <div class="modal-content">
        <span id="closeModal12" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones12">Observaciones:</label>
        <textarea id="idObservaciones12" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones12">Acciones:</label>
        <textarea id="idAcciones12" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas12">Problemas Comunes:</label>
        <select id="idProblemas12" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila12">Fecha</label>
        <input type="date" id="idFechaFila12" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_12" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos12" class="btn bg-success text-white">Guardar</button>
    </div>
</div>
<!-- MODAL DEL 13 -->
<div id="modalOverlay13" class="modal-overlay"></div>
<div id="modalObservaciones13" class="modal">
    <div class="modal-content">
        <span id="closeModal13" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones13">Observaciones:</label>
        <textarea id="idObservaciones13" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones13">Acciones:</label>
        <textarea id="idAcciones13" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas13">Problemas Comunes:</label>
        <select id="idProblemas13" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila13">Fecha</label>
        <input type="date" id="idFechaFila13" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_13" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos13" class="btn bg-success text-white">Guardar</button>
    </div>
</div>
            <!-- MODAL DEL 14 -->
<div id="modalOverlay14" class="modal-overlay"></div>
<div id="modalObservaciones14" class="modal">
    <div class="modal-content">
        <span id="closeModal14" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones14">Observaciones:</label>
        <textarea id="idObservaciones14" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones14">Acciones:</label>
        <textarea id="idAcciones14" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas14">Problemas Comunes:</label>
        <select id="idProblemas14" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila14">Fecha</label>
        <input type="date" id="idFechaFila14" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_14" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos14" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

          <!-- MODAL DEL 15 -->
<div id="modalOverlay15" class="modal-overlay"></div>
<div id="modalObservaciones15" class="modal">
    <div class="modal-content">
        <span id="closeModal15" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones15">Observaciones:</label>
        <textarea id="idObservaciones15" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones15">Acciones:</label>
        <textarea id="idAcciones15" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas15">Problemas Comunes:</label>
        <select id="idProblemas15" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila15">Fecha</label>
        <input type="date" id="idFechaFila15" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_15" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos15" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

           <!-- MODAL DEL 16 -->
<div id="modalOverlay16" class="modal-overlay"></div>
<div id="modalObservaciones16" class="modal">
    <div class="modal-content">
        <span id="closeModal16" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones16">Observaciones:</label>
        <textarea id="idObservaciones16" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones16">Acciones:</label>
        <textarea id="idAcciones16" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas16">Problemas Comunes:</label>
        <select id="idProblemas16" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila16">Fecha</label>
        <input type="date" id="idFechaFila16" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_16" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos16" class="btn bg-success text-white">Guardar</button>
    </div>
</div>
          <!-- MODAL DEL 17 -->
<div id="modalOverlay17" class="modal-overlay"></div>
<div id="modalObservaciones17" class="modal">
    <div class="modal-content">
        <span id="closeModal17" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones17">Observaciones:</label>
        <textarea id="idObservaciones17" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones17">Acciones:</label>
        <textarea id="idAcciones17" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas17">Problemas Comunes:</label>
        <select id="idProblemas17" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila17">Fecha</label>
        <input type="date" id="idFechaFila17" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_17" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos17" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

       <!-- MODAL DEL 18 -->
<div id="modalOverlay18" class="modal-overlay"></div>
<div id="modalObservaciones18" class="modal">
    <div class="modal-content">
        <span id="closeModal18" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones18">Observaciones:</label>
        <textarea id="idObservaciones18" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones18">Acciones:</label>
        <textarea id="idAcciones18" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas18">Problemas Comunes:</label>
        <select id="idProblemas18" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila18">Fecha</label>
        <input type="date" id="idFechaFila18" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_18" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos18" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

<!-- MODAL DEL 19 -->
<div id="modalOverlay19" class="modal-overlay"></div>
<div id="modalObservaciones19" class="modal">
    <div class="modal-content">
        <span id="closeModal19" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones19">Observaciones:</label>
        <textarea id="idObservaciones19" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones19">Acciones:</label>
        <textarea id="idAcciones19" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas19">Problemas Comunes:</label>
        <select id="idProblemas19" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila19">Fecha</label>
        <input type="date" id="idFechaFila19" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_19" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos19" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

        <!-- MODAL DEL 20 -->
<div id="modalOverlay20" class="modal-overlay"></div>
<div id="modalObservaciones20" class="modal">
    <div class="modal-content">
        <span id="closeModal20" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones20">Observaciones:</label>
        <textarea id="idObservaciones20" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones20">Acciones:</label>
        <textarea id="idAcciones20" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas20">Problemas Comunes:</label>
        <select id="idProblemas20" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila20">Fecha</label>
        <input type="date" id="idFechaFila20" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_20" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos20" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

      <!-- MODAL DEL 21 -->
<div id="modalOverlay21" class="modal-overlay"></div>
<div id="modalObservaciones21" class="modal">
    <div class="modal-content">
        <span id="closeModal21" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones21">Observaciones:</label>
        <textarea id="idObservaciones21" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones21">Acciones:</label>
        <textarea id="idAcciones21" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas21">Problemas Comunes:</label>
        <select id="idProblemas21" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila21">Fecha</label>
        <input type="date" id="idFechaFila21" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_21" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos21" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

                <!-- MODAL DEL 22 -->
<div id="modalOverlay22" class="modal-overlay"></div>
<div id="modalObservaciones22" class="modal">
    <div class="modal-content">
        <span id="closeModal22" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones22">Observaciones:</label>
        <textarea id="idObservaciones22" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones22">Acciones:</label>
        <textarea id="idAcciones22" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas22">Problemas Comunes:</label>
        <select id="idProblemas22" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila22">Fecha</label>
        <input type="date" id="idFechaFila22" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_22" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos22" class="btn bg-success text-white">Guardar</button>
    </div>
</div>


<!-- MODAL DEL 23 -->
<div id="modalOverlay23" class="modal-overlay"></div>
<div id="modalObservaciones23" class="modal">
    <div class="modal-content">
        <span id="closeModal23" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones23">Observaciones:</label>
        <textarea id="idObservaciones23" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones23">Acciones:</label>
        <textarea id="idAcciones23" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas23">Problemas Comunes:</label>
        <select id="idProblemas23" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila23">Fecha</label>
        <input type="date" id="idFechaFila23" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_23" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos23" class="btn bg-success text-white">Guardar</button>
    </div>
</div>






            <!-- MODAL DEL 24 -->
<div id="modalOverlay24" class="modal-overlay"></div>
<div id="modalObservaciones24" class="modal">
    <div class="modal-content">
        <span id="closeModal24" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones24">Observaciones:</label>
        <textarea id="idObservaciones24" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones24">Acciones:</label>
        <textarea id="idAcciones24" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas24">Problemas Comunes:</label>
        <select id="idProblemas24" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila24">Fecha</label>
        <input type="date" id="idFechaFila24" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_24" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos24" class="btn bg-success text-white">Guardar</button>
    </div>
</div>

                <!-- MODAL DEL 25 -->
<div id="modalOverlay25" class="modal-overlay"></div>
<div id="modalObservaciones25" class="modal">
    <div class="modal-content">
        <span id="closeModal25" class="close">×</span>
        <h2>Observaciones y Acciones</h2>

        <!-- Observaciones -->
        <label for="idObservaciones25">Observaciones:</label>
        <textarea id="idObservaciones25" class="form-control observaciones" placeholder="Descripción"></textarea>

        <!-- Acciones -->
        <label for="idAcciones25">Acciones:</label>
        <textarea id="idAcciones25" class="form-control acciones" placeholder="Descripción"></textarea>

        <!-- Problemas comunes -->
        <label for="idProblemas25">Problemas Comunes:</label>
        <select id="idProblemas25" class="form-control" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="Hoja de procesos">Hoja de procesos</option>
                                <option value="Plan de Control">Plan de Control</option>
                                <option value="Lay-Out">Lay-Out</option>
                                <option value="Norma de empaque">Norma de empaque</option>
                                <option value="Dispositivo de control / Instructivo /etiqueta de verificacion">Dispositivo de control / Instructivo /etiqueta de verificacion</option>
                                <option value="Registros de documentacion obligatoria">Registros de documentacion obligatoria</option>
                                <option value="Hoja Hora por Hora">Hoja Hora por Hora</option>
                                <option value="Registro de parametros">Registro de parametros</option>
                                <option value="Plan de mantenimiento">Plan de mantenimiento</option>
                                <option value="Plan de limpieza">Plan de limpieza</option>
                                <option value="Catalogo de NO conformidades">Catalogo de NO conformidades</option>
                                <option value="Alerta de calidad">Alerta de calidad</option>
                                <option value="Auditoria de capas">Auditoria de capas</option>
                                <option value="Uso de EPP">Uso de EPP</option>
                                <option value="Identificación de materiales">Identificación de materiales</option>
                                <option value="Matriz de Habilidades">Matriz de Habilidades</option>
                                <option value="Matriz de EPP">Matriz de EPP</option>
                                <option value="Calibración/Verificación de equipos vigentes">Calibración/Verificación de equipos vigentes</option>
                                <option value="Liberación de primera pieza">Liberación de primera pieza</option>
                                <option value="Identificación de producto terminado">Identificación de producto terminado</option>
                                <option value="Ejecución de 5´s">Ejecución de 5´s</option>
                                <option value="Check list de maquinaria">Check list de maquinaria</option>
                                <option value="Check list de verificacion de pokayoke">Check list de verificacion de pokayoke</option>
                                <option value="Conocimiento al plan de reaccion">Conocimiento al plan de reaccion</option>
                                <option value="Área de trabajo segura">Área de trabajo segura</option>
                                <option value="Medios de seguridad para contención">Medios de seguridad para contención</option>
                                <option value="Manejo de materiales peligrosos">Manejo de materiales peligrosos</option>
        </select>

        <!-- Fecha -->
        <label for="idFechaFila25">Fecha</label>
        <input type="date" id="idFechaFila25" class="form-control fecha">

        <!-- Selección de archivo -->
        <div class="input-group justify-content-center">
            <input type="file" id="archivo_25" name="archivo" class="form-control archivo-input">
        </div>

        <!-- Botón para guardar datos -->
        <button id="btnGuardarDatos25" class="btn bg-success text-white">Guardar</button>
    </div>
</div>
            <!-- MODALES _^ ARRIBA -->
        <div class="table-responsive" style="max-width: 95%; margin: 0 auto; margin-top: 20px;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-info">
                    <tr>
                        <th class="col-1">ITEM</th>
                        <th class="col-2">Pregunta</th>
                        <th class="col-2">Puntos de referencia</th>
                        <th class="col-2">Estatus</th>
                        <th class="col-1">Acciones</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="9" class="text-start medium fw-bold text-justify">
                                         Proceso
                </td>
                </tr>
                <tbody>
                    <tr data-id="1">
                        <td>1</td>
                        <td class="text-justify">Se encuentra la documentación técnica <br> en la línea de Proceso 
                        ( caratula, diagrama de flujo, <br> hoja de proceso, norma de empaque, plan de control)</td>
                        <td class="text-justify">FIN  <br>  04,05,06,09 <br> FIN 08</td>
                       
                        <td>
                            <select id="idResultado1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <button id="btnGuardaUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarUnoUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUnoUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i> -->
                            </button>
                        </td>
                    </tr>
                    <!-- </tr> -->
                    <tr>
                        <td>2</td>
                        <td class="text-justify">Los parámetros se encuentran de acuerdo a la hoja de proceso (deben a su vez coincidir con los anotados en el formato "hoja de control de parámetros") </td>
                        <td class="text-justify">FIN 30</td>
                        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                <td>
                    <button id="btnGuardaDos" class="btn bg-success text-white me-2">
                        <i class="fas fa-save"></i>
                    </button>
                    <!-- <button id="btnEditarUnoDos" class="btn bg-warning text-white me-2" style="display:none;">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button id="btnActualizarUnoDos" class="btn bg-primary text-white" style="display:none;">
                        <i class="fas fa-sync"></i>
                    </button> -->
                </td>
                
                    </tr>
                    <tr>
                        <td>3</td>
                        <td class="text-justify">Se llevo a cabo la liberación del proceso y de primera pieza de manera correcta y validada por líder de celda</td>
                        <td class="text-justify">FPR 23,24</td>
                       
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado3" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaTres" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarUnoTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUnoTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                    </tr>
                </tr>


                <tr data-id="4" >
                    <td>4</td>
                    <td class="text-justify">Se identifican correctamente los materiales (producto en proceso y  producto no conforme)</td>
                    <td class="text-justify">FAC 11,12</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado4" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaCuatro" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarDosUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr data-id="5">
                    <td>5</td>
                    <td class="text-justify">Se tiene delimitada el área de acuerdo al Lay Out y  el Lay Out esta actualizado </td>
                    <td class="text-justify">FIN 44</td>
                  
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                                <button id="btnGuardaCinco" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <!-- <button id="btnEditarDosDos" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosDos" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button> -->
                        </th>
                </tr>
                <tr data-id="6">
                    <td>6</td>
                    <td class="text-justify">Los herramentales e indicadores (manómetros,timer,display,termómetros, etc.)de la línea están identificados, en buenas condiciones, verificados y son funcionales</td>
                    <td class="text-justify">FIN 34  <br>  FAC 43</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado6" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaSeis" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarDosTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="7">
                    <td>7</td>
                    <td class="text-justify">Existen ayudas visuales de defectos de la pieza (catalogo de no conformidades)</td>
                    <td class="text-justify">FPR 14</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado7" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                            <th>  
                                <button id="btnGuardaSiete" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <!-- <button id="btnEditarDosCuatro" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosCuatro" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button> -->
                            </th>
                </tr>
                <tr id="8">
                    <td>8</td>
                    <td class="text-justify">El área auditada esta limpia y ordenada (se cuenta con un plan de limpieza y esta documentado)</td>
                    <td class="text-justify">FSH 32</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado8" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                            <th>  
                                <button id="btnGuardaOcho" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <!-- <button id="btnEditarDosCinco" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosCinco" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button> -->
                            </th>
                </tr>
                <tr id="9"></tr>>
                    <td>9</td>
                    <td class="text-justify">Se encuentra el plan de mantenimiento preventivo y se realiza de acuerdo a lo programado </td>
                    <td class="text-justify">FMT 03</td>
            
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado9" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                                <button id="btnGuardaNueve" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <!-- <button id="btnEditarDosSeis" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosSeis" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button> -->
                        </th>
                   
                </tr>
                 
                <tr id="10">
                    <td>10</td>
                    <td class="text-justify">Se encuentra la ultima auditoria de capas y cuenta con sus acciones correctivas</td>
                    <td class="text-justify">FAC 25</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado10" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaDiez" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarTresUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarTresUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>

                </tr>
                   <!-- Fila para la descripción -->
                   <tr>
                    <td colspan="8" class="text-start medium fw-bold text-justify">
                            Empleados
                    </td>
                </tr>
                <tr id="11">
                    <td>11</td>
                    <td class="text-justify">Los  operadores realizan la operación como lo indica su HOJA DE PROCESO</td>
                    <td class="text-justify">FIN 06</td>
                        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado11" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaOnce" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCuatroUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="12">
                    <td>12</td>
                    <td class="text-justify">Los operadores están informados sobre las reclamaciones y saben como manejar las piezas NOK</td>
                    <td class="text-justify">FAC 52</td>
                  
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado12" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaDoce" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCuatroDos" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="13">
                    <td>13</td>
                    <td class="text-justify">Los operadores conocen el plan de reacción en caso de falla conforme lo indicado el PLAN DE CONTROL</td>
                    <td class="text-justify">FIN 08</td> 
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado13" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaTrece" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCuatroTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                      </tr>
                 
                <tr id="14">
                    <td>14</td>
                    <td class="text-justify">El operador revisa sus piezas visualmente conforme a lo indicado en el PLAN DE CONTROL</td>
                    <td class="text-justify">FIN 08</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado14" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCatorce" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>


                </tr>
                <tr id="15">
                    <td>15</td>
                    <td class="text-justify">Los empleados cuentan con su EPP completo contra la matriz de EPP</td>
                    <td class="text-justify">FSH22</td>
        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado15" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaQuince" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoDos" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="16">
                    <td>16</td>
                    <td class="text-justify">Esta actualizada la matriz de habilidades</td>
                    <td class="text-justify">FAD 14</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado16" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaDieciseis" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <!-- Fila para la descripción -->
                <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                    Características a evaluar en CHECKING FIXTURE & PLANILLA
                    </td>
                </tr>
                <tr id="17">
                    <td>17</td>
                    <td class="text-justify">El dispositivo cuenta con todos sus componentes, se encuentra limpio y en buen estado</td>
                    <td class="text-justify">FAC 93</td>

                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado17" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                <button id="btnGuardaDiecisiete" class="btn bg-success text-white me-2">
                    <i class="fas fa-save"></i>
                </button>
                </th>           
                <tr id="18">
                    <td>18</td>
                    <td class="text-justify">El dispositivo esta verificado y cuenta con el nivel de ingeniería correspondiente</td>
                    <td class="text-justify">FAC 93,  <br>  FIN 04</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado18" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaDieciocho" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoCinco" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoCinco" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>               
                <tr id="19">
                    <td>19</td>
                    <td class="text-justify">El dispositivo cuenta con el instructivo de uso del mismo
                    </td>
                    <td class="text-justify">FAC 101</td>
                  <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado19" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaDiecinueve" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoSeis" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoSeis" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
 <!-- Fila para la descripción -->
                <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                    Materia prima
                    </td>
                </tr>



                <tr id="20">
                    <td>20</td>
                    <td class="text-justify">Esta identificada la materia prima correctamente  (etiqueta de proveedor)
                    </td>
                    <td class="text-justify">VISUAL</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado20" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaVeinte" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoSiete" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoSiete" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="21">
                    <td>21</td>
                    <td class="text-justify">Se han anotado las materias primas en el control de carga de materias primas 
                    </td>
                    <td class="text-justify">FPR 02</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado21" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaVeintiuno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>

                  <!-- Fila para la descripción -->
                  <tr >
                    <td colspan="9" class="text-start small fw-bold text-justify">
                    Materiales salientes
                    </td>
                </tr>
                <tr id="22">
                    <td>22</td>
                    <td class="text-justify">La identificación del producto final para envío a cliente es legible.  (Verificar las impresiones de etiqueta individual y SAP)
                    </td>
                    <td class="text-justify">VISUAL</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado22" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaVeintidos" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarSeisUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarSeisUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>    
                
                <tr id="23">
                    <td>23</td>
                    <td class="text-justify">Los materiales son  colocados como lo indica la norma empaque liberada
                    </td>
                    <td class="text-justify">FIN 09</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado23" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaVeintitres" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="24">
                    <td>24</td>
                    <td class="text-justify">Los contenedores se encuentran en buen estado (limpios, secos y sin roturas) y están libre de etiquetas obsoletas como lo indica la norma de empaque
                    </td>
                    <td class="text-justify">FIN 09</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado24" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaVeinticuatro" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                    Materia prima
                    </td>
                </tr>
                <tr id="25">
                    <td>25</td>
                    <td class="text-justify">Gestión de reclamaciones
                    </td>
                    <td class="text-justify">FAC 52</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado25" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaVeinticinco" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarSeisUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarSeisUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>    
            </thead>
            <div class="table-responsive">
                <form id="formAuditoria"> <!-- Agregado para buena práctica -->
                    <table class="table table-bordered table-striped w-100">
                        <thead>
                            <tr>
                                <th>Nombre de Auditado</th>
                                <th>Nombre de Supervisor</th>
                                <th>Nombre de Auditor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input type="text" 
                                        id="idNombreAuditado" 
                                        name="nombreAuditado" 
                                        class="form-control" 
                                        placeholder="Nombre-Auditado" 
                                        value="" 
                                        required>
                                </td>
                                <td>
                                    <input type="text" 
                                        id="idNombreSupervisor" 
                                        name="nombreSupervisor" 
                                        class="form-control" 
                                        placeholder="Nombre-Supervisor" 
                                        value="" 
                                        required>
                                </td>
                                <td>
                                    <input type="text" 
                                        id="idNombreAuditor2" 
                                        name="nombreAuditor" 
                                        class="form-control" 
                                        value="<?php echo isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : ''; ?>" 
                                        readonly>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- Campo oculto para el ID de la auditoría -->
                    <input type="hidden" 
                        id="idAuditoria" 
                        name="id" 
                        value=""> <!-- Cambia "87" por el valor dinámico real si aplica -->

                    <div class="d-flex justify-content-center" id="cerrarDocumento">
                        <button type="button" class="btn btn-warning btn-lg px-5 py-3 fw-bold">Cerrar Auditoría ✅</button>
                    </div>
                </form>
            </div>       
                </tbody>
                </table>
                </div> 
                <br><br>
            </div>
            <br><br>

    <footer class="text-white text-center py-3">
        <p>&copy; 2025 Adler Pelzer Group. Todos los derechos reservados. IT planta Pachuca.  </p>
    </footer>

    <script src="script.js"></script>
    <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>