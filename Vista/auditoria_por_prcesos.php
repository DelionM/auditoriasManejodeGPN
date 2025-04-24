

                <!-- <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                        <div>
                            <h4 class="text-center">Punto 5</h4>
                            <h6 class="text-center">5S & AMBIENTAL / SEGURIDAD</h6>

                            <div class="d-flex justify-content-center">
                                <div class="w-25">
                                    <canvas id="evaluationChart5"></canvas>
                                </div>
                            </div>

                            <div class="text-center mt-3">
                                <button id="update-chart5" class="btn btn-primary">Actualizar Gráfico 5</button>
                            </div>
                        </div>
                    </td>
                </tr> -->

<?php
// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
//if (!isset($_SESSION['usuario'])) {
  //  die("Acceso denegado. Debes iniciar sesión.");
//}

// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "auditoria";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el usuario que inició sesión
$numero_empleado = $_SESSION['numero_empleado']; // Asegúrate de que este valor se guarda correctamente en la sesión
// Preparar la consulta para obtener solo los registros del usuario actual
$sql = "SELECT numero_empleado, nombre FROM empleados WHERE numero_empleado = ?";
$stmt = $conn->prepare($sql);

// Verificar si la consulta se preparó correctamente
if (!$stmt) {
    die("Error en la consultc: " . $conn->error);
}

$stmt->bind_param("s", $numero_empleado);  // Usar $numero_empleado en lugar de $usuario_actual
$stmt->execute();
$result = $stmt->get_result();

// Comprobar si hay resultados y asignar el nombre_auditor a la sesión
if ($row = $result->fetch_assoc()) {
    $_SESSION['nombre'] = $row['nombre'];  // Guardamos el nombre del auditor en la sesión
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDITORIAS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="styles.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <link rel="icon" type="image/png" href="img/images.ico">

</head>
<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
    z-index: 999;
}

.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    width: 50%;
    max-width: 500px;
    max-height: 500px;
}

.modal-content {
    text-align: center;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
}

footer {
            position: fixed;  /* Fija el pie de página al fondo */
            bottom: 0;
            width: 100%;
            background-color: #2A3184; /* Color de fondo personalizado */
}
</style>

<body>
    <nav class="navbar" style="background-color:#2A3184">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="index.php">Adler Pelzer Group</a>
                <a class="navbar-brand text-white" href="Vista/ver_registro_por_usuario.php">Ver mis registros</a>
            <!-- Botón de cerrar sesión que aparecerá en el lado derecho -->
            <form action="Controlador/logout.php" method="POST" class="d-flex ms-auto">
                <button type="submit" class="btn btn-danger">Cerrar sesión</button>
            </form>            
        </div>
    </nav>
    <div class="container-fluid mt-5">
        <!-- Título de la tabla -->
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h3 class="text-center me-3  text-justify">NUEVA  AUDITORÍA DE PROCESO</h3>
        </div>
       
        <h5 class="text-center me-3 text-justify">Número de documento: 
            <span id="numeroDocumento"></span>
        </h5> <br>
        <!-- Tabla principal responsiva -->
        <div class="table-responsive" style="max-width: 95%; margin: 0 auto;">
        <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-primary">
                    <tr>
                        <th>Proceso</th>
                        <th>Cliente</th>
                        <th>No. Parte</th>
                        <th>Nave</th>
                        <th>Nivel de Ingeniería</th>
                        <th>Revisión y fecha</th>
                        <th>Auditor</th>
                        <th>Supervisor</th>
                        <th>Fecha</th>
                        <th>Turno</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <input type="text" id="idProcesoAuditado" class="form-control" placeholder="Proceso Auditado">
                        </td>
                        <td>
                            <input type="text" id="idCliente" class="form-control" placeholder="Cliente">
                        </td>
                        <td>
                            <input type="text" id="idParteAuditada" class="form-control" placeholder="No. Parte">
                        </td>
                        <td>
                            <select id="idNave" class="form-control">
                                <option value="" disabled selected>Selecciona una opción</option>
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
                            <input type="text" id="idNivelIngenieria" class="form-control" placeholder="Nivel de Ingeniería">
                        </td>
                        <td>
                            <input type="text" id="idRevisionFecha" class="form-control" placeholder="Revisión y Fecha">
                        </td>
                        <td>
                            <input type="text" id="idNombreAuditor" class="form-control" value="<?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : ''; ?>" readonly>
                        </td>
                        <td>
                            <input type="text" id="idSupervisor" class="form-control" placeholder="Supervisor">
                        </td>
                        <td>
                            <input type="date" id="idFecha" class="form-control">
                        </td>
                        <td>
                            <select id="idTurno" class="form-control">
                                <option value="" disabled selected>Selecciona un turno</option>
                                <option value="Matutino">Matutino</option>
                                <option value="Vespertino">Vespertino</option>
                                <option value="Nocturno">Nocturno</option>
                            </select>
                        </td>
                        <td>
                            <input type="time" id="idHora" class="form-control">
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
        <h6  class="d-flex justify-content-center align-items-center mb-4"> OK=Conforme  NOK=No Conforme   NA=No Aplica</h6>
        <!-- Tabla adicional para los encabezados de las 8 columnas -->
        <div id="modalOverlay" class="modal-overlay"></div>
            <div id="modalObservaciones" class="modal">
                <div class="modal-content">
                    <span id="closeModal" class="close">&times;</span>
                    <h2>Observaciones y Acciones</h2>
                    <label for="idObservaciones1.1">Observaciones:</label>
                    <textarea id="idObservaciones1.1" class="form-control observaciones" placeholder="Descripción"></textarea>

                    <label for="idAcciones1.1">Acciones:</label>
                    <textarea id="idAcciones1.1" class="form-control acciones" placeholder="Descripción"></textarea>

                    <div class="input-group justify-content-center">
                        <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_1_1">
                            <input type="file" id="archivo_1_1_input" class="archivo-input">
                            <input type="hidden" name="numeroEmpleado" value="12345">
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


            <!-- MODALES _^ -->
        <div class="table-responsive" style="max-width: 95%; margin: 0 auto; margin-top: 20px;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-primary">
                    <tr>
                        <th class="col-1">ITEM</th>
                        <th class="col-1">Pregunta</th>
                        <th class="col-1"># de referencia</th>
                        <th class="col-1">Estatus</th>
                        <th class="col-1">Responsable</th>
                        <th class="col-1">Fecha</th>
                        <th class="col-1">Btn</th>

                    </tr>
                </thead>
                <tr>
                    <td colspan="9" class="text-start small fw-bold text-justify">
                        Proceso
                    </td>
                </tr>
                <tbody>
                    <tr data-id="1">
                        <td>1.1</td>
                        <td class="text-justify">Se encuentra la documentación técnica en la línea de Proceso 
                        ( caratula, diagrama de flujo, hoja de proceso, norma de empaque, plan de control)</td>
                        <td class="text-justify">FIN 04,05,06,09 <br>  FIN 08</td>
                        <td>
                            <select id="idResultado1.1" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable1.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila1.1" class="form-control fecha"></td>
                        <td>
                            <button id="btnGuardaUnoUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarUnoUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUnoUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </td>
                    </tr>
                    <!-- </tr> -->
                    <tr>
                        <td>1.2</td>
                        <td class="text-justify">Los parámetros se encuentran de acuerdo a la hoja de proceso (deben a su vez coincidir con los anotados en el formato "hoja de control de parámetros") </td>
                        <td class="text-justify">FIN 30</td>
                        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado1.2" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                         <!-- responsable -->
                        <td>
                            <textarea id="idResponsable1.2" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila1.2" class="form-control fecha"></td>
                <td>
                    <button id="btnGuardaUnoDos" class="btn bg-success text-white me-2">
                        <i class="fas fa-save"></i>
                    </button>
                    <button id="btnEditarUnoDos" class="btn bg-warning text-white me-2" style="display:none;">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button id="btnActualizarUnoDos" class="btn bg-primary text-white" style="display:none;">
                        <i class="fas fa-sync"></i>
                    </button>
                </td>
                
                    </tr>
                    <tr>
                        <td>1.3</td>
                        <td class="text-justify">Se llevo a cabo la liberación del proceso y de primera pieza de manera correcta y validada por líder de celda</td>
                        <td class="text-justify">FPR 23,24</td>
                       
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado1.3" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <textarea id="idResponsable1.3" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        
                        <td><input type="date" id="idFechaFila1.3" class="form-control fecha"></td>

                        <th>  
                            <button id="btnGuardaUnoTres" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarUnoTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUnoTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                    </tr>
                    <tr>
                        <!-- <td colspan=9 class="text-start small fw-bold text-justify">
                            <div>
                                <h4 class="text-center">Punto 1</h4>
                                <h6 class="text-center">IDENTIFICACION DE MATERIAL - MANEJO DE MATERIAL EN PROCESO Y NO CONFORME -</h6>
                                
                                <div class="d-flex justify-content-center">
                                    <div class="w-20">
                                        <canvas id="evaluationChart1"></canvas>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <button id="update-chart1" class="btn btn-primary btn-sm">Actualizar Gráfico 1</button>
                                </div>
                            </div>
                        </td> -->
                    </tr>

                  
                </tr>


                <tr data-id="2.1" >
                    <td>2.1</td>
                    <td class="text-justify">Se identifican correctamente los materiales (producto en proceso y  producto no conforme)</td>
                    <td class="text-justify">FAC 11,12</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.1" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                             <!-- responsable -->
                        <td>
                            <textarea id="idResponsable2.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila2.1" class="form-control fecha"></td>

                        <th>  
                            <button id="btnGuardaDosUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarDosUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                <tr data-id="2.2">
                    <td>2.2</td>
                    <td class="text-justify">Se tiene delimitada el área de acuerdo al Lay Out y  el Lay Out esta actualizado </td>
                    <td class="text-justify">FIN 44</td>
                  
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.2" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <textarea id="idResponsable2.2" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila2.2" class="form-control fecha"></td>
                        <th>  
                                <button id="btnGuardaDosDos" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button id="btnEditarDosDos" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosDos" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button>
                        </th>
                </tr>
                <tr data-id="2.3">
                    <td>2.3</td>
                    <td class="text-justify">Los herramentales e indicadores (manómetros,timer,display,termómetros, etc.)de la línea están identificados, en buenas condiciones, verificados y son funcionales</td>
                    <td class="text-justify">FIN 34  <br>  FAC 43</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.3" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                       
                        <td>
                            <textarea id="idResponsable2.3" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila2.3" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaDosTres" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarDosTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                <tr id="2.4">
                    <td>2.4</td>
                    <td class="text-justify">Existen ayudas visuales de defectos de la pieza (catalogo de no conformidades)</td>
                    <td class="text-justify">FPR 14</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.4" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <textarea id="idResponsable2.4" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila2.4" class="form-control fecha"></td>
                            <th>  
                                <button id="btnGuardaDosCuatro" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button id="btnEditarDosCuatro" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosCuatro" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </th>
                </tr>
                <tr id="2.5">
                    <td>2.5</td>
                    <td class="text-justify">El área auditada esta limpia y ordenada (se cuenta con un plan de limpieza y esta documentado)
                        </td>
                    <td class="text-justify">FSH 32</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.5" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                            <td>
                            <textarea id="idResponsable2.5" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        </td>
                        <td><input type="date" id="idFechaFila2.5" class="form-control fecha"></td>
                            <th>  
                                <button id="btnGuardaDosCinco" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button id="btnEditarDosCinco" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosCinco" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button>
                            </th>
                </tr>
                <tr>
                    <td>2.6</td>
                    <td class="text-justify">Se encuentra el plan de mantenimiento preventivo y se realiza de acuerdo a lo programado </td>
                    <td class="text-justify">FMT 03</td>
            
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado2.6" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <textarea id="idResponsable2.6" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        
                        <td><input type="date" id="idFechaFila2.6" class="form-control fecha"></td>
                        <th>  
                                <button id="btnGuardaDosSeis" class="btn bg-success text-white me-2">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button id="btnEditarDosSeis" class="btn bg-warning text-white me-2" style="display:none;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button id="btnActualizarDosSeis" class="btn bg-primary text-white" style="display:none;">
                                    <i class="fas fa-sync"></i>
                                </button>
                        </th>
                        <tr>
                            <!-- <td colspan="9" class="text-start small fw-bold text-justify">
                                <div>
                                    <h4 class="text-center">Punto 2</h4>
                                    <h6 class="text-center">TRABAJO ESTANDARIZADO, COMPETENCIAS Y TOMA DE CONCIENCIA</h6>
                                    
                                    <div class="d-flex justify-content-center">
                                        <div class="w-25">
                                            <canvas id="evaluationChart2"></canvas>
                                        </div>
                                    </div>      
                                    <div class="text-center mt-3">
                                        <button id="update-chart2" class="btn btn-primary">Actualizar Gráfico 2</button>
                                    </div>
                                </div>
                            </td> -->
                        </tr>
                </tr>
                  
                <tr id="3.1">
                    <td>3.1</td>
                    <td class="text-justify">Se encuentra la ultima auditoria de capas y cuenta con sus acciones correctivas</td>
                    <td class="text-justify">FAC 25</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado3.1" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                            <textarea id="idResponsable3.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila3.1" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaTresUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarTresUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarTresUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                        <!-- <tr>
                            <td colspan="9" class="text-start small fw-bold text-justify">
                                <div>
                                    <h4 class="text-center">Punto 3</h4>
                                    <h6 class="text-center">LIBERACIÓN DE PROCESO</h6>

                                    <div class="d-flex justify-content-center">
                                        <div class="w-25">
                                            <canvas id="evaluationChart3"></canvas>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button id="update-chart3" class="btn btn-primary">Actualizar Gráfico 3</button>
                                    </div>
                                </div>
                            </td>
                        </tr> -->
                </tr>
                <!-- Fila para la descripción -->
                <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        Empleados
                    </td>
                </tr>
                   <!-- Fila para la descripción -->
                  
                <tr id="4">
                    <td>4.1</td>
                    <td class="text-justify">Los  operadores realizan la operación como lo indica su HOJA DE PROCESO</td>
                    <td class="text-justify">FIN 06</td>
                        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado4.1" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable4.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        
                        <td><input type="date" id="idFechaFila4.1" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCuatroUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCuatroUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
             
                <tr id="4.3">
                    <td>4.3</td>
                    <td class="text-justify">Los operadores están informados sobre las reclamaciones y saben como manejar las piezas NOK</td>
                    <td class="text-justify">FAC 52</td> 
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado4.3" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable4.3" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        
                        <td><input type="date" id="idFechaFila4.3" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCuatroTres" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCuatroTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                        <tr>
                            <!-- <td colspan="9" class="text-start small fw-bold text-justify">
                                <div>
                                    <h4 class="text-center">Punto 4</h4>
                                    <h6 class="text-center">CONTROLES DE PROCESO</h6>

                                    <div class="d-flex justify-content-center">
                                        <div class="w-25">
                                            <canvas id="evaluationChart4"></canvas>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button id="update-chart4" class="btn btn-primary">Actualizar Gráfico 4</button>
                                    </div>
                                </div>
                            </td>
                        </tr> -->
                </tr>
                   <!-- Fila para la descripción -->
                
                <tr id="5.1">
                    <td>5.1</td>
                    <td class="text-justify">Los operadores conocen el plan de reacción en caso de falla conforme lo indicado el PLAN DE CONTROL</td>
                    <td class="text-justify">FIN 08</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.1" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        
                        <td><input type="date" id="idFechaFila5.1" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>


                </tr>
                <tr id="5.2">
                    <td>5.2</td>
                    <td class="text-justify">El operador revisa sus piezas visualmente conforme a lo indicado en el PLAN DE CONTROL </td>
                    <td class="text-justify">FIN 08</td>
        
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.2" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.2" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        
                        <td><input type="date" id="idFechaFila5.2" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoDos" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoDos" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                <tr id="5.3">
                    <td>5.3</td>
                    <td class="text-justify">Los empleados cuentan con su EPP completo contra la matriz de EPP</td>
                    <td class="text-justify">FSH22</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.3" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.3" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.3" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoTres" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoTres" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                <tr id="5.4">
                    <td>5.4</td>
                    <td class="text-justify">Esta actualizada la matriz de habilidades</td>
                    <td class="text-justify">FAD 14</td>

                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.4" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.4" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.4" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoCuatro" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoCuatro" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoCuatro" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>

                <td colspan="9" class="text-start small fw-bold text-justify">
                Características a evaluar en CHECKING FIXTURE & PLANILLA
                    </td>


                <tr id="5.5">
                    <td>5.5</td>
                    <td class="text-justify">El dispositivo cuenta con todos sus componentes, se encuentra limpio y en buen estado</td>
                    <td class="text-justify">FAC 93</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.5" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.5" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.5" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoCinco" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoCinco" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoCinco" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>               
                <tr id="5.6">
                    <td>5.6</td>
                    <td class="text-justify">El dispositivo esta verificado y cuenta con el nivel de ingeniería correspondiente
                    </td>
                    <td class="text-justify">FAC 93,  <br> FIN 04</td>
                  <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.6" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.6" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.6" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoSeis" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoSeis" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoSeis" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                <tr id="5.7">
                    <td>5.7</td>
                    <td class="text-justify">El dispositivo cuenta con el instructivo de uso del mismo
                    </td>
                    <td class="text-justify">FAC 101</td>
                   
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.7" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.7" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.7" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoSiete" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoSiete" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoSiete" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                <td colspan="9" class="text-start small fw-bold text-justify">
                Materia prima
                    </td>                <tr id="5.8">
                    <td>5.8</td>
                    <td class="text-justify">Esta identificada la materia prima correctamente  (etiqueta de proveedor)
                    </td>
                    <td class="text-justify">VISUAL</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.8" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable5.8" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.8" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoOcho" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                </tr>
                  <!-- Fila para la descripción -->
                  
                <tr id="6.1">
                    <td>6.1</td>
                    <td class="text-justify">Se han anotado las materias primas en el control de carga de materias primas 
                    </td>
                    <td class="text-justify">FPR 02</td>
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado6.1" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable6.1" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila6.1" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaSeisUno" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarSeisUno" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarSeisUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>

                        <tr >
                            <td colspan="9" class="text-start small fw-bold text-justify">
                                Materiales salientes
                              </td>
                        </tr>

                    <tr id="6.2">
                    <td>6.2</td>
                    <td class="text-justify">La identificación del producto final para envío a cliente es legible.  (Verificar las impresiones de etiqueta individual y SAP)
                    </td>
                    <td class="text-justify">VISUAL</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.8" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable6.2" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.8" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoOcho" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                        </tr>

                    <tr id="6.3">
                    <td>6.3</td>
                    <td class="text-justify">Los materiales son  colocados como lo indica la norma empaque liberada
                    </td>
                    <td class="text-justify">FIN 09</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.8" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable6.3" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.8" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoOcho" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                        </tr>


                        <tr id="6.4">
                    <td>6.4</td>
                    <td class="text-justify">Los contenedores se encuentran en buen
                            estado (limpios, secos y sin roturas) y están
                            libre de etiquetas obsoletas como 10 indica la
                            norma de empaque
                    </td>
                    <td class="text-justify">FIN 09</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.8" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable6.4" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.8" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoOcho" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                        </tr>
                    <tr id="6.4">
                    <td>6.4</td>
                    <td class="text-justify">Se encuentra la ultima alerta de calidad disponible en producción (solo si aplica)
                    </td>
                    <td class="text-justify">FAC 52</td>
                    
                        <!-- Select de colores diferentes -->
                        <td>
                            <select id="idResultado5.8" class="form-control resultado">
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="OK">OK</option>
                                <option value="Pendiente">Pendiente</option>

                                <option value="NOK">NOK</option>
                            </select>
                        </td>
                        <td>
                        <!-- responsable -->
                            <textarea id="idResponsable6.4" class="form-control observaciones" placeholder="Descripción"></textarea>
                        </td>
                        <td><input type="date" id="idFechaFila5.8" class="form-control fecha"></td>
                        <th>  
                            <button id="btnGuardaCincoOcho" class="btn bg-success text-white me-2">
                                <i class="fas fa-save"></i>
                            </button>
                            <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2" style="display:none;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                        </th>
                        </tr>
                        <!-- <tr>
                            <td colspan="9" class="text-start small fw-bold text-justify">
                                <div>
                                    <h4 class="text-center">Punto 6</h4>
                                    <h6 class="text-center">EMPAQUE Y TRAZABILIDAD</h6>

                                    <div class="d-flex justify-content-center">
                                        <div class="w-25">
                                            <canvas id="evaluationChart6"></canvas>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <button id="update-chart6" class="btn btn-primary">Actualizar Gráfico 6</button>
                                    </div>
                                </div>
                            </td>
                        </tr> -->

                </tr>            
            </thead>
            <!-- <div class="table-responsive">
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
                                <input type="text" id="idNombreOperador" class="form-control" placeholder="Nombre-Operador">
                            </td>
                            <td>
                                <input type="text" id="idNombreSupervisor" class="form-control" placeholder="Nombre-Supervisor">
                            </td>
                            <td>
                                <input type="text" id="idNombreAuditor2" class="form-control" placeholder="Nombre-Auditor">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex justify-content-center" id="cerrarDocumento">
                    <button class="btn btn-warning w-80">Guardar cambios completos</button>
                </div>

            </div>         -->

                </tbody>
            </table>
        </div> 


        <h5>Y- No se encontró ninguna desviación.</h5>
<h5>N- Se encontró una desviación.
</h5>
<h5>NC- Desviación corregida durante la auditoria.</h5>
<h5>NA - No aplica</h5>


        <div class="container mt-4">
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>Firma del Auditado</th>
                    <th>Firma del Auditor</th>
                    <th>Firma de Supervisor</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <input type="text" class="form-control" id="firmaAuditado" placeholder="Firma Auditado">
                    </td>
                    <td>
                        <input type="text" class="form-control" id="firmaAuditor" placeholder="Firma Auditor">
                    </td>
                    <td>
                        <input type="text" class="form-control" id="firmaSupervisor" placeholder="Firma Supervisor">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>    
            <button class="btn btn-success text-center ">Cerrar auditoria </button>
</div>
<br><br>
    </div>

    <br><br>

    
    <footer class="text-white text-center py-3">
        <p>&copy; 2025 Adler Pelzer Group. Todos los derechos reservados. IT planta Pachuca.  </p>
    </footer>

    <script src="../js/auditoria_por_procesos.js"></script>
    <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>