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
    if ($audit_data['tipo_auditoria'] !== 'auditoria por Capas') {
        die("Error: Este ID corresponde a una Auditoría por Procesos. Accede a por_procesos.php.");
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
    .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: 999;
    display: none;
    color: #000000 !important;
}

.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    width: 70%;
    max-width: 500px;
    display: none;
    color: #000000 !important;
    height: 60%;
}

.modal-content {
    position: relative;
    color: #000000 !important;
}

.table-responsive .form-control {
    color: #000000 !important;
    font-size: 14px;
}

.table-responsive .form-control::placeholder {
    color: #000000 !important;
    opacity: 1;
}

.close {
    position: absolute;
    top: 5px;
    right: 5px;
    font-size: 38px;
    color: #000000 !important;
    cursor: pointer;
    transition: color 0.3s;
}

.close:hover {
    color:rgb(55, 243, 8) !important;
}

.modal-content h2 {
    margin-bottom: 10px;
    font-size: 28px;
    color: #000000 !important;
    text-align: center;
}

.modal-content label {
    font-weight: bold;
    margin-top: 10px;
    display: block;
    font-size: 14px;
    color: #000000 !important;
}

.modal-content textarea,
.modal-content select,
.modal-content input[type="date"] {
    width: 100%;
    margin-top: 3px;
    margin-bottom: 8px;
    font-size: 14px;
    color: #000000 !important;
}

.modal-content .btn {
    margin-top: 10px;
    width: 100%;
    padding: 6px;
    font-size: 14px;
    color: #000000 !important;
}
    /* Ajuste para el Dropzone */
  
</style>

</head>
<body>
    <nav class="navbar" style="background-color:#2A3184">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="">Adler Pelzer Group</a>
            <a class="navbar-brand text-white" href="Vista/ver_registro_por_usuario.php">Ver mis registros</a>
            <a class="navbar-brand text-white" href="Vista/mis_auditorias.php">Mis auditorias programadas</a>
            <form action="Controlador/logout.php" method="POST" class="d-flex ms-auto">
                <button type="submit" class="btn btn-danger">Cerrar sesión</button>
            </form>
        </div>
    </nav>
    <div class="container-fluid mt-5">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h3 class="text-center me-3 text-justify">NUEVA AUDITORÍA DE PROCESO POR CAPAS</h3>
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
                        <th>Operación auditada</th>
                        <th>Nave:</th>
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
                            <input type="text" id="idParteAuditada" class="form-control" value=" <?php echo htmlspecialchars($audit_data['numero_parte']); ?>" required>
                        </td>
                        <td>
                            <input type="text" id="idOperacionAuditada" class="form-control" value="<?php echo htmlspecialchars($audit_data['descripcion']); ?>" required>
                        </td>
                        <td>
                            <select id="idNave" class="form-control" required>
                                <option value="<?php echo htmlspecialchars($audit_data['nave']); ?>" selected><?php echo htmlspecialchars($audit_data['nave']); ?></option>
                                <option value="Nave 1">Nave 1</option>
                                <option value="Nave 2">Nave 2</option>
                                <option value="Nave 3">Nave 3</option>
                                <option value="Nave 4">Nave 4</option>
                                <option value="Nave 5">Nave 5</option>
                                <option value="Nave 6">Nave 6</option>
                                <option value="Nave 7">Nave 7</option>
                                <option value="Nave 7A">Nave 7A</option>
                                <option value="Nave 8">Nave 8</option>
                                <option value="Nave 9">Nave 9</option>
                                <option value="Nave 14">Nave 14</option>
                            </select>
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
        <div id="modalOverlay" class="modal-overlay"></div>
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
                <label for="idFechaFila1.1">Fecha Compromiso</label>
                <input type="date" id="idFechaFila1.1" class="form-control fecha text-dark  ">
                <div class="input-group justify-content-center">
                    <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_1_1">
                        <input type="file" id="archivo_1_1_input" class="archivo-input">
                        <input type="hidden" name="numeroEmpleado" value="<?php echo htmlspecialchars($numero_empleado); ?>">
                    </form>
                </div>
                <button id="btnGuardarDatos" class="btn bg-success text-white">Guardar</button>
            </div>
        </div>
                   
            <!-- MODAL DEL 1.2 -->
            <div id="modalOverlay1_2" class="modal-overlay"></div>
            <div id="modalObservaciones1_2" class="modal">
                <div class="modal-content">
                    <span id="closeModal1_2" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones1.2">Observaciones:</label>
                    <textarea id="idObservaciones1.2" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones1.2">Acciones:</label>
                    <textarea id="idAcciones1.2" class="form-control acciones" placeholder="Descripción"></textarea>


                    <label for="idProblemas1.2">Problemas Comunes:</label>
                    <select id="idProblemas1.2" class="form-control" required>
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
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila1.2" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_1_2">
                            <input type="file" id="archivo_1_2_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos1_2" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            
           
            <!-- MODAL DEL 1.3 -->
            <div id="modalOverlay1_3" class="modal-overlay"></div>
            <div id="modalObservaciones1_3" class="modal">
                <div class="modal-content">
                    <span id="closeModal1_3" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones1.3">Observaciones:</label>
                    <textarea id="idObservaciones1.3" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones1.3">Acciones:</label>
                    <textarea id="idAcciones1.3" class="form-control acciones" placeholder="Descripción"></textarea>


                    <label for="idProblemas1.3">Problemas Comunes:</label>
                    <select id="idProblemas1.3" class="form-control" required>
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
                    <br>
                    <label for="Fecha">Fecha</label>
                    <td><input type="date" id="idFechaFila1.3" class="form-control fecha"></td>
                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_1_3">
                            <input type="file" id="archivo_1_3_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos1_3" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>
              <!-- MODAL DEL 2.1 -->
              <div id="modalOverlay2_1" class="modal-overlay"></div>
            <div id="modalObservaciones2_1" class="modal">
                <div class="modal-content">
                    <span id="closeModal2_1" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones2.1">Observaciones:</label>
                    <textarea id="idObservaciones2.1" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones2.1">Acciones:</label>
                    <textarea id="idAcciones2.1" class="form-control acciones" placeholder="Descripción"></textarea>

                    <label for="idProblemas2.1">Problemas Comunes:</label>
                    <select id="idProblemas2.1" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila2.1" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_2_1">
                            <input type="file" id="archivo_2_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos2_1" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODAL DEL 2.2 -->
            <div id="modalOverlay2_2" class="modal-overlay"></div>
            <div id="modalObservaciones2_2" class="modal">
                <div class="modal-content">
                    <span id="closeModal2_2" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones2.2">Observaciones:</label>
                    <textarea id="idObservaciones2.2" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones2.2">Acciones:</label>
                    <textarea id="idAcciones2.2" class="form-control acciones" placeholder="Descripción"></textarea>

                    <label for="idProblemas2.2">Problemas Comunes:</label>
                    <select id="idProblemas2.2" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila2.2" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_2_2">
                            <input type="file" id="archivo_2_2_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos2_2" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODAL DEL 2.3 -->
            <div id="modalOverlay2_3" class="modal-overlay"></div>
            <div id="modalObservaciones2_3" class="modal">
                <div class="modal-content">
                    <span id="closeModal2_3" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones2.3">Observaciones:</label>
                    <textarea id="idObservaciones2.3" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones2.3">Acciones:</label>
                    <textarea id="idAcciones2.3" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas2.3">Problemas Comunes:</label>
                    <select id="idProblemas2.3" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila2.3" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_2_3">
                            <input type="file" id="archivo_2_3_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos2_3" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODAL DEL 2.4 -->
            <div id="modalOverlay2_4" class="modal-overlay"></div>
            <div id="modalObservaciones2_4" class="modal">
                <div class="modal-content">
                    <span id="closeModal2_4" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones2.4">Observaciones:</label>
                    <textarea id="idObservaciones2.4" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones2.4">Acciones:</label>
                    <textarea id="idAcciones2.4" class="form-control acciones" placeholder="Descripción"></textarea>



                    <label for="idProblemas2.4">Problemas Comunes:</label>
                    <select id="idProblemas2.4" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila2.4" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_2_4">
                            <input type="file" id="archivo_2_4_input" class="archivo-input">
                         <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos2_4" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

              <!-- MODAL DEL 2.5 -->
              <div id="modalOverlay2_5" class="modal-overlay"></div>
            <div id="modalObservaciones2_5" class="modal">
                <div class="modal-content">
                    <span id="closeModal2_5" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones2.5">Observaciones:</label>
                    <textarea id="idObservaciones2.5" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones2.5">Acciones:</label>
                    <textarea id="idAcciones2.5" class="form-control acciones" placeholder="Descripción"></textarea>

                    <label for="idProblemas2.5">Problemas Comunes:</label>
                    <select id="idProblemas2.5" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila2.5" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_2_5">
                            <input type="file" id="archivo_2_5_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos2_5" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODAL DEL 2.6 -->
            <div id="modalOverlay2_6" class="modal-overlay"></div>
            <div id="modalObservaciones2_6" class="modal">
                <div class="modal-content">
                    <span id="closeModal2_6" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones2.6">Observaciones:</label>
                    <textarea id="idObservaciones2.6" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones2.6">Acciones:</label>
                    <textarea id="idAcciones2.6" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas2.6">Problemas Comunes:</label>
                    <select id="idProblemas2.6" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila2.6" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_2_6">
                            <input type="file" id="archivo_2_6_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos2_6" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>


            <!-- MODAL DEL 3.1 -->
            <div id="modalOverlay3_1" class="modal-overlay"></div>
            <div id="modalObservaciones3_1" class="modal">
                <div class="modal-content">
                    <span id="closeModal3_1" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones3.1">Observaciones:</label>
                    <textarea id="idObservaciones3.1" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones3.1">Acciones:</label>
                    <textarea id="idAcciones3.1" class="form-control acciones" placeholder="Descripción"></textarea>

                    <label for="idProblemas3.1">Problemas Comunes:</label>
                    <select id="idProblemas3.1" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila3.1" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_3_1">
                            <input type="file" id="archivo_3_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos3_1" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

             <!-- MODAL DEL 4.1 -->
            <div id="modalOverlay4_1" class="modal-overlay"></div>
            <div id="modalObservaciones4_1" class="modal">
                <div class="modal-content">
                    <span id="closeModal4_1" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones4.1">Observaciones:</label>
                    <textarea id="idObservaciones4.1" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones4.1">Acciones:</label>
                    <textarea id="idAcciones4.1" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas4.1">Problemas Comunes:</label>
                    <select id="idProblemas4.1" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila4.1" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_4_1">
                            <input type="file" id="archivo_4_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos4_1" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

             <!-- MODAL DEL 4.2 -->
             <div id="modalOverlay4_2" class="modal-overlay"></div>
            <div id="modalObservaciones4_2" class="modal">
                <div class="modal-content">
                    <span id="closeModal4_2" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones4.2">Observaciones:</label>
                    <textarea id="idObservaciones4.2" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones4.2">Acciones:</label>
                    <textarea id="idAcciones4.2" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas4.2">Problemas Comunes:</label>
                    <select id="idProblemas4.2" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila4.2" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_4_2">
                            <input type="file" id="archivo_4_2_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos4_2" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

               <!-- MODAL DEL 4.3 -->
               <div id="modalOverlay4_3" class="modal-overlay"></div>
            <div id="modalObservaciones4_3" class="modal">
                <div class="modal-content">
                    <span id="closeModal4_3" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones4.3">Observaciones:</label>
                    <textarea id="idObservaciones4.3" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones4.3">Acciones:</label>
                    <textarea id="idAcciones4.3" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas4.3">Problemas Comunes:</label>
                    <select id="idProblemas4.3" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila4.3" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_4_3">
                            <input type="file" id="archivo_4_3_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos4_3" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

               <!-- MODAL DEL 5.1 -->
               <div id="modalOverlay5_1" class="modal-overlay"></div>
            <div id="modalObservaciones5_1" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_1" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones5.1">Observaciones:</label>
                    <textarea id="idObservaciones5.1" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones5.1">Acciones:</label>
                    <textarea id="idAcciones5.1" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas5.1">Problemas Comunes:</label>
                    <select id="idProblemas5.1" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.1" class="form-control fecha"></td>
                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_1">
                            <input type="file" id="archivo_5_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_1" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>


            <!-- MODAL DEL 5.2 -->
            <div id="modalOverlay5_2" class="modal-overlay"></div>
            <div id="modalObservaciones5_2" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_2" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones5.2">Observaciones:</label>
                    <textarea id="idObservaciones5.2" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones5.2">Acciones:</label>
                    <textarea id="idAcciones5.2" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas5.2">Problemas Comunes:</label>
                    <select id="idProblemas5.2" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.2" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_2">
                            <input type="file" id="archivo_5_2_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_2" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODAL DEL 5.3 -->
            <div id="modalOverlay5_3" class="modal-overlay"></div>
            <div id="modalObservaciones5_3" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_3" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>

                    <!-- Observaciones -->
                    <label for="idObservaciones5.3">Observaciones:</label>
                    <textarea id="idObservaciones5.3" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <!-- Acciones -->
                    <label for="idAcciones5.3">Acciones:</label>
                    <textarea id="idAcciones5.3" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas5.3">Problemas Comunes:</label>
                    <select id="idProblemas5.3" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.3" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_3">
                            <input type="file" id="archivo_5_3_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>

                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_3" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>
             <!-- MODAL DEL 5.4 -->
             <div id="modalOverlay5_4" class="modal-overlay"></div>
            <div id="modalObservaciones5_4" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_4" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <!-- Observaciones -->
                    <label for="idObservaciones5.4">Observaciones:</label>
                    <textarea id="idObservaciones5.4" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <!-- Acciones -->
                    <label for="idAcciones5.4">Acciones:</label>
                    <textarea id="idAcciones5.4" class="form-control acciones" placeholder="Descripción"></textarea>
                    
                    <label for="idProblemas5.4">Problemas Comunes:</label>
                    <select id="idProblemas5.4" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.4" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_4">
                            <input type="file" id="archivo_5_4_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>
                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_4" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

             <!-- MODAL DEL 5.5 -->
             <div id="modalOverlay5_5" class="modal-overlay"></div>
            <div id="modalObservaciones5_5" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_5" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <!-- Observaciones -->
                    <label for="idObservaciones5.5">Observaciones:</label>
                    <textarea id="idObservaciones5.5" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <!-- Acciones -->
                    <label for="idAcciones5.5">Acciones:</label>
                    <textarea id="idAcciones5.5" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas5.5">Problemas Comunes:</label>
                    <select id="idProblemas5.5" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.5" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_5">
                            <input type="file" id="archivo_5_5_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>
                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_5" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>  
            <!-- MODAL DEL 5.6 -->
            <div id="modalOverlay5_6" class="modal-overlay"></div>
            <div id="modalObservaciones5_6" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_6" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <!-- Observaciones -->
                    <label for="idObservaciones5.6">Observaciones:</label>
                    <textarea id="idObservaciones5.6" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <!-- Acciones -->
                    <label for="idAcciones5.6">Acciones:</label>
                    <textarea id="idAcciones5.6" class="form-control acciones" placeholder="Descripción"></textarea>
                    
                    <label for="idProblemas5.6">Problemas Comunes:</label>
                    <select id="idProblemas5.6" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.6" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_6">
                            <input type="file" id="archivo_5_6_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>
                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_6" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODAL DEL 5.7 -->
            <div id="modalOverlay5_7" class="modal-overlay"></div>
            <div id="modalObservaciones5_7" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_7" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <!-- Observaciones -->
                    <label for="idObservaciones5.7">Observaciones:</label>
                    <textarea id="idObservaciones5.7" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <!-- Acciones -->
                    <label for="idAcciones5.7">Acciones:</label>
                    <textarea id="idAcciones5.7" class="form-control acciones" placeholder="Descripción"></textarea>
                    <label for="idProblemas5.7">Problemas Comunes:</label>
                    <select id="idProblemas5.7" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.7" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_7">
                            <input type="file" id="archivo_5_7_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>
                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_7" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>
             <!-- MODAL DEL 5.8 -->
             <div id="modalOverlay5_8" class="modal-overlay"></div>
            <div id="modalObservaciones5_8" class="modal">
                <div class="modal-content">
                    <span id="closeModal5_8" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <!-- Observaciones -->
                    <label for="idObservaciones5.8">Observaciones:</label>
                    <textarea id="idObservaciones5.8" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <!-- Acciones -->
                    <label for="idAcciones5.8">Acciones:</label>
                    <textarea id="idAcciones5.8" class="form-control acciones" placeholder="Descripción"></textarea>
                    
                    <label for="idProblemas5.8">Problemas Comunes:</label>
                    <select id="idProblemas5.8" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila5.8" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_5_8">
                            <input type="file" id="archivo_5_8_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>
                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos5_8" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

                  <!-- MODAL DEL 6.1-->
            <div id="modalOverlay6_1" class="modal-overlay"></div>
            <div id="modalObservaciones6_1" class="modal">
                <div class="modal-content">
                    <span id="closeModal6_1" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <!-- Observaciones -->
                    <label for="idObservaciones6.1">Observaciones:</label>
                    <textarea id="idObservaciones6.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                    <!-- Acciones -->
                    <label for="idAcciones6.1">Acciones:</label>
                    <textarea id="idAcciones6.1" class="form-control acciones" placeholder="Descripción"></textarea>
                   
                    <label for="idProblemas6.1">Problemas Comunes:</label>
                    <select id="idProblemas6.1" class="form-control" required>
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
                    <br>
                    <label for="">Fecha</label>
                    <td><input type="date" id="idFechaFila6.1" class="form-control fecha"></td>

                    <!-- Selección de archivo -->
                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_6_1">
                            <input type="file" id="archivo_6_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
                        </form>
                    </div>
                    <!-- Botón para guardar datos -->
                    <button id="btnGuardarDatos6_1" class="btn bg-success text-white">Guardar</button>
                </div>
            </div>

            <!-- MODALES _^ ARRIBA -->
        <div class="table-responsive" style="max-width: 95%; margin: 0 auto; margin-top: 20px;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-info">
                    <tr>
                        <th class="col-1">No.</th>
                        <th class="col-2">Pregunta</th>
                        <th class="col-2">Puntos de referencia</th>
                        <th class="col-2">Estatus</th>
                        <th class="col-1">Acciones</th>
                    </tr>
                </thead>
                <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                        1. IDENTIFICACION DE MATERIAL - MANEJO DE MATERIAL EN PROCESO Y NO CONFORME -
                    </td>
                </tr>
                <tbody>
                    <tr data-id="1">
                        <td>1.1</td>
                        <td class="text-justify">¿Se han anotado todas las materias primas en el control de trazabilidad correspondiente?</td>
                        <td class="text-justify">Solicitar al supervisor el registro y verificar si todos los materiales que se encuentren en el área están anotados.</td>
                       
                        <td>
                            <select id="idResultado1.1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <button id="btnGuardaUnoUno" class="btn bg-success text-white me-2">
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
                        <td>1.2</td>
                        <td class="text-justify">¿Todos los materiales, empaques, dispositivos en el área de producción están en la ubicación correcta como lo indica el lay-out para evitar "contaminación"?</td>
                        <td class="text-justify">Los materiales deben de encontrarse dentro de las delimitaciones establecidas y de acuerdo al documento de lay out</td>
                        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado1.2" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                <td>
                    <button id="btnGuardaUnoDos" class="btn bg-success text-white me-2">
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
                        <td>1.3</td>
                        <td class="text-justify">¿Todos los materiales en el área de producción están correctamente
                             identificados de acuerdo a la hoja de proceso?</td>
                        <td class="text-justify">Verificar que todo el material del proceso se encuentre correctamente identificado: Materia prima con etiqueta de SAP, Producto en Proceso, Material rechazado con etiqueta roja, producto terminado, sin etiquetas obsoleta. Asegurar que los materiales utilizados estén en la hoja de proceso e identificada la norma de empaque</td>
                       
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado1.3" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaUnoTres" class="btn bg-success text-white me-2">
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
                 

                    <td colspan="9" class="text-start small fw-bold text-justify">
                        2. TRABAJO ESTANDARIZADO, COMPETENCIAS Y TOMA DE CONCIENCIA
                    </td>
                </tr>


                <tr data-id="2.1" >
                    <td>2.1</td>
                    <td class="text-justify">¿El operador está certificado para realizar la operación de acuerdo a la matriz de habilidades?</td>
                    <td class="text-justify">¿El operador está certificado para realizar la operación de acuerdo a la matriz de habilidades?</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->

                                <option value="NOK">NOK</option>
                            </select>
                        </td>


                        <th>  
                            <button id="btnGuardaDosUno" class="btn bg-success text-white me-2">
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
                <tr data-id="2.2">
                    <td>2.2</td>
                    <td class="text-justify">¿Se están llenando correctamente los reportes de control de producción en las frecuencias establecidas?</td>
                    <td class="text-justify">Verificar el formato de produción por hora que se encuentra en el tablero del proceso.</td>
                  
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.2" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                                <button id="btnGuardaDosDos" class="btn bg-success text-white me-2">
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
                <tr data-id="2.3">
                    <td>2.3</td>
                    <td class="text-justify">Verificar  que el registros de Chequeo de máquinaria y equipo, se encuentre con los registros al día</td>
                    <td class="text-justify">Verificar que al arranque de la línea se halla realizado la liberación del proceso mediante el registro de  chequeo de máquinaria y equipo y en caso de desviaciones se hayan tomado acciones.</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.3" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaDosTres" class="btn bg-success text-white me-2">
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
                <tr id="2.4">
                    <td>2.4</td>
                    <td class="text-justify">La documentación técnica se encuentra disponible en el área de trabajo y es trazable con el diagrama de flujo (hoja de proceso y plan de control) y el operador registra parámetros como lo indica esta documentación</td>
                    <td class="text-justify">Verificar que se encuentre en tablero de información el diagrama de flujo, hoja de proceso, plan de control y que estos documentos cuenten con la misma revisión.
                        La hoja de proceso y plan de control deben tener los mismos procesos declarados en el diagrama de flujo.
                        Revisar que los registros que indica el plan de control se encuentren correctamente llenados.</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.4" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                            <th>  
                                <button id="btnGuardaDosCuatro" class="btn bg-success text-white me-2">
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
                <tr id="2.5">
                    <td>2.5</td>
                    <td class="text-justify">Si la estación auditada cuenta con un sistema de poka yokes, verificar que al arranque del proceso se realizará su revisión y estan funcionando.</td>
                    <td class="text-justify">Se solicita al operador el check list de verificación del poka yoke y se corrobora nuevamente su funcionamiento.</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.5" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                            <th>  
                                <button id="btnGuardaDosCinco" class="btn bg-success text-white me-2">
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
                <tr>
                    <td>2.6</td>
                    <td class="text-justify">¿El personal conoce y usa el sistema de escalación en caso de fallas?</td>
                    <td class="text-justify">Se pregunta al operador si sabe a quién ó quiénes dirijirse en caso de fallas.</td>
            
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.6" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                                <button id="btnGuardaDosSeis" class="btn bg-success text-white me-2">
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
                  <!-- Fila para la descripción -->
                  <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        3. LIBERACIÓN DE PROCESO
                    </td>
                </tr>
                <tr id="3.1">
                    <td>3.1</td>
                    <td class="text-justify">Se cuenta con la liberación de proceso al inicio de turno / arranque de la linea por el operador y es validada por el líder de celda?</td>
                    <td class="text-justify">Verificar que en el dispositivo de control se encuentre el registro de la liberación de la primera pieza y este debidamente llenado y firmado por el operdor y el líder de grupo</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado3.1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="N/A">No Aplica</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaTresUno" class="btn bg-success text-white me-2">
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
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        4. CONTROLES DE PROCESO
                    </td>
                </tr>
                <tr id="4">
                    <td>4.1</td>
                    <td class="text-justify">¿ Se encuentran en estado correcto de calibración y/o verificación los equipos de control necesarios para la operación?</td>
                    <td class="text-justify">Verificar que el escantillón y los equipos donde se verifican parámetros no indiquen fecha de calibración y/o verificación vencida en su etiqueta de identificación.</td>
                        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado4.1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCuatroUno" class="btn bg-success text-white me-2">
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
                <tr id="4.2">
                    <td>4.2</td>
                    <td class="text-justify">¿Si hay no conformidades en alguno de los controles de los tableros  están documentadas y siendo tomadas las contramedidas?</td>
                    <td class="text-justify">Si se encuentran parámetros fuera de especificación deben de existir anotaciones en los registros de acciones correctivas / bitácora de proceso</td>
                  
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado4.2" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCuatroDos" class="btn bg-success text-white me-2">
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
                <tr id="4.3">
                    <td>4.3</td>
                    <td class="text-justify">¿Los materiales se encuentran estibados de manera que la calidad de la pieza no se vea afectada?</td>
                    <td class="text-justify">Verificar si están estibadas de acuerdo al máximo indicado en hojas de proceso.</td> 
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado4.3" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCuatroTres" class="btn bg-success text-white me-2">
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
                   <!-- Fila para la descripción -->
                <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                        5. 5S & AMBIENTAL / SEGURIDAD
                    </td>
                </tr>
                <tr id="5.1">
                    <td>5.1</td>
                    <td class="text-justify">¿ Se esta utilizando el Equipo de Protección Personal indicado en la matriz de EPP?</td>
                    <td class="text-justify">Solicitar al supervisor su matriz de EPP y verificar físicamente el uso del equipo en el operador.</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCincoUno" class="btn bg-success text-white me-2">
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
                <tr id="5.2">
                    <td>5.2</td>
                    <td class="text-justify">Los medios de seguridad incluyen equipos para el control de incendios, control de derrames de productos químicos, solventes, etc; Tales como: Hidrantes, extintores, lava ojos, regaderas, arena / acerrín para control de derrames, etc. </td>
                    <td class="text-justify">En las áreas en donde se manejan materiales peligrosos se encuentran equipos que ayuden a mitigar un impacto causado por un incendio o derrame</td>
        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.2" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCincoDos" class="btn bg-success text-white me-2">
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
                <tr id="5.3">
                    <td>5.3</td>
                    <td class="text-justify">¿El área está libre de riesgos de accidente (actos y condiciones inseguras)?</td>
                    <td class="text-justify">Actos inseguros: actividades que hacen las personas que pueden ponerlas en riesgo de sufrir un accidente; 
                        Condición insegura: instalaciones, equipos y herramientas que no están en condiciones de ser usadas; los moldes en prensas y troqueles cuentan con toda la tornillería instalada en la partes superior e inferior y que pueden causar un accidente en su uso)</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.3" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaCincoTres" class="btn bg-success text-white me-2">
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
                <tr id="5.4">
                    <td>5.4</td>
                    <td class="text-justify">¿Existe en el área auditada un equipo contra incendio?</td>
                    <td class="text-justify">Asegurar que estos equipos no deben encontrarse obstruidos</td>

                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.4" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaCincoCuatro" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <!-- <button id="btnEditarCincoCuatro" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoCuatro" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button> -->
                        </th>
                </tr>
                <tr id="5.5">
                    <td>5.5</td>
                    <td class="text-justify">Los controles de la máquinaria de producción operan adecuadamente (incluyendo paro de emergencia, guardas, y controles que protejan la integridad del operador) y el área se encuentra iluminada?</td>
                    <td class="text-justify">Las condiciones de los controles o tableros de la maquinaria se encuentra en condiciones adecuadas de uso.
                        Los controles de seguridad se encuentran operando adecuadamente (guardas sin ser bloqueadas, paro de emergencia, Sensores, etc;), la luz es adecuada para la operación</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.5" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaCincoCinco" class="btn bg-success text-white me-2">
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
                <tr id="5.6">
                    <td>5.6</td>
                    <td class="text-justify">¿El lugar de trabajo cumple con el estandar 5S(Eliminar-Ordenar-Limpiar-Estandarizar-Disciplina)?
                    </td>
                    <td class="text-justify">Verificar por ejemplo: que el área se encuentre limpia(sin derrames ni sobrantes en piso y maquinaria), ordenada(cada cosa de acuerdo a lay out e identificaciones) y estandarizada.</td>
                  <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.6" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaCincoSeis" class="btn bg-success text-white me-2">
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
                <tr id="5.7">
                    <td>5.7</td>
                    <td class="text-justify">En caso de que aplique, ¿los químicos usados en el proceso están en el contenedor adecuado y correctamente identificados?
                    </td>
                    <td class="text-justify">El recipiente que contenga químicos debe de tener el pictograma de seguridad y el nombre del químico que almacena, verificar que no se utilizan recipientes de refrescos o similares para almacenar materiales químicos.</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.7" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaCincoSiete" class="btn bg-success text-white me-2">
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
                <tr id="5.8">
                    <td>5.8</td>
                    <td class="text-justify">En caso de que aplique, ¿los residuos peligrosos son almacenados e identificados adecuadamente?
                    </td>
                    <td class="text-justify">La identificación de los contenedores de residuos es visible dentro de ellos no existe una mezcla de residuos (metales en contenedores de cartón o residuos peligrosos, residuos peligrosos en contenedores de cartón o metales, cartón en contenedores de cartón o residuos peligrosos).</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.8" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        
                        <th>  
                            <button id="btnGuardaCincoOcho" class="btn bg-success text-white me-2">
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
                        6.  EMPAQUE Y TRAZABILIDAD
                    </td>
                </tr>
                <tr id="6.1">
                    <td>6.1</td>
                    <td class="text-justify">¿ El producto terminado es empacado de acuerdo a la hoja de empaque correspondiente con las etiquetas de liberación y SAP correctas? Si no, ¿se encuentra identificado con etiqueta de material en proceso?
                    </td>
                    <td class="text-justify">Solicitar al supervisor la hoja de empaque y verificar físicamente si el producto terminado esta de acuerdo al documento.</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado6.1" class="form-control resultado" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <!-- <option value="Pendiente">Pendiente</option> -->
                                <option value="N/A">No Aplica</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <th>  
                            <button id="btnGuardaSeisUno" class="btn bg-success text-white me-2">
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
                <table class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th>Nombre de operador</th>
                            <th>Nombre de supervisor</th>
                            <th>Nombre de Auditor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" id="idNombreOperador" class="form-control" placeholder="Nombre-Operador" required>
                            </td>
                            <td>
                                <input type="text" id="idNombreSupervisor" class="form-control" placeholder="Nombre-Supervisor" required>
                            </td>
                            <td>
                                <input type="text" id="idNombreAuditor2" class="form-control" value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>" readonly>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-center" id="cerrarDocumento">
                    <button class="btn btn-warning btn-lg px-5 py-3 fw-bold">Cerrar Auditoría ✅</button>
                </div>
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

    <script src="script2.js"></script>
    <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>