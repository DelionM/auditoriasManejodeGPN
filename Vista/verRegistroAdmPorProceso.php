<?php
session_start();
include('../conexion.php');


if (!isset($_SESSION['numero_empleado'])) {
    header("Location: ../login.php");
    exit();
}

$id_auditoria = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id_auditoria) {
    die("No se proporcionó un ID de auditoría válido.");
}

// Consulta segura para obtener los datos de la auditoría
$sql = "SELECT * FROM auditoria_proceso WHERE id_auditoria = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_auditoria);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "No se encontraron datos para el ID de auditoría: " . htmlspecialchars($id_auditoria);
    exit;
}
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AUDITORIAS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/images.ico">
    <meta name="author" content="Delion">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
</head>
<style>
    .archivo-dropzone {
        border: 2px dashed #007bff;
        border-radius: 10px;
        background-color: #f8f9fa;
        text-align: center;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        min-width: 150px;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .archivo-dropzone:hover {
        background-color: #e9ecef;
        border-color: #0056b3;
    }

    .archivo-dropzone::before {
        color: #6c757d;
        font-size: 14px;
        font-weight: bold;
    }

    .archivo-dropzone input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .archivo-dropzone.dz-drag-hover {
        background-color: #d1ecf1;
        border-color: #17a2b8;
    }

    .alert {
        padding: 10px;
        margin-top: 10px;
        border-radius: 5px;
        font-size: 14px;
        text-align: center;
        width: 100%;
    }

    .alert-success {
        background-color: #28a745;
        color: white;
    }

    .alert-danger {
        background-color: #dc3545;
        color: white;
    }
    
    
</style>

<body>
    <nav class="navbar navbar-expand-lg" style="background-color:#2A3184;">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">Adler Pelzer Group</a>

    </nav>
    <div class="container-fluid mt-5">
        <div class="d-flex justify-content-center align-items-center mb-4">
            <h3 class="text-center me-3 text-justify">AUDITORÍA DE PROCESO</h3>
        </div>
        <h5 class="text-center me-3 text-justify">
            <span id="numeroDocumento"><?php echo $row['id_auditoria']; ?></span>
        </h5>
        <!-- Tabla principal responsiva -->
        <div class="table-responsive" style="max-width: 95%; margin: 0 auto;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-info">
                    <tr>
                        <th>Número de colaborador:</th>
                        <th>Nombre del Auditor:</th>
                        <th>Cliente:</th>
                        <th>Proceso auditado:</th>
                        <th>No. De parte auditada</th>
                        <th>Nivel de Ingeneria</th>
                        <th>Nave:</th>
                        <th>Unidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" id="idNumeroEmpleado" class="form-control"
                                value="<?php echo $row['numero_empleado']; ?>" readonly></td>
                        <td><input type="text" id="idNombreAuditor" class="form-control"
                                value="<?php echo $row['nombre_auditor']; ?>" placeholder="Nombre del Auditor"></td>
                        <td><input type="text" id="idCliente" class="form-control"
                                value="<?php echo $row['cliente']; ?>" placeholder="Cliente"></td>
                        <td><input type="text" id="idProcesoAuditado" class="form-control"
                                value="<?php echo $row['proceso_auditado']; ?>" placeholder="Proceso Auditado"></td>
                        <td><input type="text" id="idParteAuditada" class="form-control"
                                value="<?php echo $row['parte_auditada']; ?>" placeholder="Parte Auditada"></td>
                        <td><input type="text" id="idOperacionAuditada" class="form-control"
                                value="<?php echo $row['nivelIngenieria']; ?>" placeholder=""></td>
                        <td>
                            <select id="idNave" class="form-control">
                                <option value="" disabled <?php echo ($row['nave'] == '') ? 'selected' : ''; ?>>Selecciona
                                    una opción</option>
                                <option value="Nave 1" <?php echo ($row['nave'] == 'Nave 1') ? 'selected' : ''; ?>>1
                                </option>
                                <option value="Nave 2" <?php echo ($row['nave'] == 'Nave 2') ? 'selected' : ''; ?>>2
                                </option>
                                <option value="Nave 3" <?php echo ($row['nave'] == 'Nave 3') ? 'selected' : ''; ?>>3
                                </option>
                                <option value="Nave 4" <?php echo ($row['nave'] == 'Nave 4') ? 'selected' : ''; ?>>4
                                </option>
                                <option value="Nave 5" <?php echo ($row['nave'] == 'Nave 5') ? 'selected' : ''; ?>>5
                                </option>
                                <option value="Nave 6" <?php echo ($row['nave'] == 'Nave 6') ? 'selected' : ''; ?>>6
                                </option>
                                <option value="Nave 7" <?php echo ($row['nave'] == 'Nave 7') ? 'selected' : ''; ?>>7
                                </option>
                                <option value="Nave 7A" <?php echo ($row['nave'] == 'Nave 7A') ? 'selected' : ''; ?>>7A
                                </option>
                                <option value="Nave 8" <?php echo ($row['nave'] == 'Nave 8') ? 'selected' : ''; ?>>8
                                </option>
                                <option value="Nave 9" <?php echo ($row['nave'] == 'Nave 9') ? 'selected' : ''; ?>>9
                                </option>
                                <option value="Nave 14" <?php echo ($row['nave'] == 'Nave 14') ? 'selected' : ''; ?>>14
                                </option>
                            </select>
                        </td>
                        <td>
                            <select id="idUnidad" class="form-control">
                                <option value="" disabled <?php echo ($row['unidad'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="Unidad 1" <?php echo ($row['unidad'] == 'Unidad 1') ? 'selected' : ''; ?>>1
                                </option>
                                <option value="Unidad 2" <?php echo ($row['unidad'] == 'Unidad 2') ? 'selected' : ''; ?>>2
                                </option>
                                <option value="Unidad 3" <?php echo ($row['unidad'] == 'Unidad 3') ? 'selected' : ''; ?>>3
                                </option>
                                <option value="Unidad 4" <?php echo ($row['unidad'] == 'Unidad 4') ? 'selected' : ''; ?>>4
                                </option>
                            </select>
                        </td>
                        <td><input type="date" id="idFecha" class="form-control" value="<?php echo $row['fecha']; ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h6 class="d-flex justify-content-center align-items-center mb-4"> OK=Conforme NOK=No Conforme NA=N/A</h6>
        <!-- Tabla adicional para los encabezados de las 8 columnas -->
        <div class="table-responsive" style="max-width: 95%; margin: 0 auto; margin-top: 20px;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-info">
                    <tr>
                        <th class="col-1">No.</th>
                        <th class="col-2">Pregunta</th>
                        <th class="col-2">Puntos de referencia</th>
                        <th class="col-3">Observaciones</th>
                        <th class="col-3">Acciones</th>
                        <th class="col1">Problema Frecuente</th>
                        <th class="col-1">Evidencias</th>
                        <th class="col-2">Estatus</th>
                        <th class="col-1">Fecha</th>
                        <th class="col-1">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="9" class="text-start small fw-bold text-justify">
                        Proceso
                        </td>
                    </tr>
                    <tr data-id="1">
                        <td>1</td>
                        <td class="text-justify">Se encuentra la documentación técnica en la línea de Proceso 
                        ( caratula, diagrama de flujo, hoja de proceso, norma de empaque, plan de control)</td>
                        <td class="text-justify">FIN <br> 04,05,06,09 <br>  FIN 08</td>
                        <td><textarea  id="observaciones" class="form-control observaciones"
                                disabled><?php echo htmlspecialchars($row['observaciones']); ?></textarea></td>
                        <td><textarea id="acciones" class="form-control acciones"
                                disabled><?php echo htmlspecialchars($row['acciones']); ?></textarea></td>
                        <td><textarea id="idProblemasUno" class="form-control problemas"
                                disabled><?php echo htmlspecialchars($row['idProblemasUno']); ?></textarea></td>
                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoUno']) ? $row['ruta_archivoUno'] : null;
                            $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>
                        <td>
                            <select id="idResultado1.1" class="form-control resultado" disabled>
                                <option value="" disabled <?php echo ($row['estatusUno'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusUno'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                                <option value="N/A" <?php echo ($row['estatusUno'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusUno'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila1.1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaUno']; ?>" disabled></td>
                        <th>

                            <button id="btnEditarUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <BR></BR>
                            <button id="btnSeguimientoUno" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoUno" class="btn btn-success" style="">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    <tr data-id="2">
                    <td>2</td>
                        <td class="text-justify">Los parámetros se encuentran de acuerdo a la hoja de proceso (deben a su vez coincidir con los anotados en el formato "hoja de control de parámetros") </td>
                        <td class="text-justify">FIN 30</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesUnoDos"><?php echo htmlspecialchars($row['observacionesDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesUnoDos"><?php echo htmlspecialchars($row['accionesDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasUnoDos"><?php echo htmlspecialchars($row['idProblemasDos']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDos']) ? $row['ruta_archivoDos'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>
                        <td>
                            <select id="idResultado1.2" class="form-control resultado">
                                <option value="" disabled <?php echo ($row['estatusDos'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDos'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDos'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusDos'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="date" id="idFechaFila1.2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDos']; ?>">
                        </td>
                        <th>
                            <button id="btnEditarUnoDos" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUnoDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <br>
                            <button id="btnSeguimientoUnoDos" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <br>
                            <button id="btnVerSeguimientoUnoDos" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDos" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr data-id="3">
                        <td>3</td>
                        <td class="text-justify">Se llevó a cabo la liberación del proceso y de primera pieza de manera correcta y validada por líder de celda</td>
                        <td class="text-justify">FPR 23,24</td>
                        <td>
                            <textarea class="form-control" id="observacionesUnoTres"><?php echo htmlspecialchars($row['observacionesTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control" id="accionesUnoTres"><?php echo htmlspecialchars($row['accionesTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control" id="idProblemasUnoTres"><?php echo htmlspecialchars($row['idProblemasTres']); ?></textarea>
                        </td>
                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoTres']) ? $row['ruta_archivoTres'] : null;
                            $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>
                        <td>
                            <select id="idResultado1.3" class="form-control resultado">
                                <option value="" disabled <?php echo ($row['estatusTres'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusTres'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                                <option value="N/A" <?php echo ($row['estatusTres'] == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                                <option value="NOK" <?php echo ($row['estatusTres'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                            </select>
                        </td>
                        <td>
                            <input type="date" id="idFechaFila1.3" class="form-control fecha" value="<?php echo $row['fecha_filaTres']; ?>">
                        </td>
                        <th>
                            <button id="btnEditarUnoTres" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <br>
                            <button id="btnActualizarUnoTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <br>
                            <button id="btnSeguimientoUnoTres" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoUnoTres" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoTres" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>


                    <tr data-id="4">
                        <td>4</td>
                        <td class="text-justify">Se identifican correctamente los materiales (producto en proceso y  producto no conforme)</td>
                        <td class="text-justify">FAC 11,12</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosUno"><?php echo htmlspecialchars($row['observacionesCuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosUno"><?php echo htmlspecialchars($row['accionesCuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosUno"><?php echo htmlspecialchars($row['idProblemasCuatro']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCuatro']) ? $row['ruta_archivoCuatro'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado2.1" class="form-control resultado">
                                <option value="" disabled <?php echo ($row['estatusCuatro'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCuatro'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCuatro'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusCuatro'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td>
                            <input type="date" id="idFechaFila2.1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCuatro']; ?>">
                        </td>

                        <th>
                            <button id="btnEditarCuatro" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatro" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCuatro" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCuatro" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCuatro" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr data-id="5">
                        <td>5</td>
                        <td class="text-justify">Se tiene delimitada el área de acuerdo al Lay Out y  el Lay Out esta actualizado </td>
                        <td class="text-justify">FIN 44</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosDos"><?php echo htmlspecialchars($row['observacionesCinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosDos"><?php echo htmlspecialchars($row['accionesCinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosDos"><?php echo htmlspecialchars($row['idProblemasCinco']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCinco']) ? $row['ruta_archivoCinco'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado2.2" class="form-control resultado">
                                <option value="" disabled <?php echo ($row['estatusCinco'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCinco'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCinco'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusCinco'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td>
                            <input type="date" id="idFechaFila2.2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCinco']; ?>">
                        </td>

                        <th>
                            <button id="btnEditarCinco" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCinco" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCinco" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCinco" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCinco" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr data-id="6">
                        <td>6</td>
                        <td class="text-justify">Los herramentales e indicadores (manómetros,timer,display,termómetros, etc.)de la línea están identificados, en buenas condiciones, verificados y son funcionales</td>
                        <td class="text-justify">FIN 34  <br>  FAC 43</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosTres"><?php echo htmlspecialchars($row['observacionesSeis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosTres"><?php echo htmlspecialchars($row['accionesSeis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosTres"><?php echo htmlspecialchars($row['idProblemasSeis']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoSeis']) ? $row['ruta_archivoSeis'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>
                        <td>
                            <select id="idResultado2.3" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusSeis']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusSeis'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusSeis'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusSeis'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila2.3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaSeis']; ?>"></td>
                        <th>
                            <button id="btnEditarSeis" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarSeis" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoSeis" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoSeis" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoSeis" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>
                    <tr id="7">
                        <td>7</td>
                        <td class="text-justify">Existen ayudas visuales de defectos de la pieza (catalogo de no conformidades)</td>

                        <td class="text-justify">FPR 14</td>
                        <td>
                            <textarea class="form-control"
                                id="observacionesSiete"><?php echo htmlspecialchars($row['observacionesSiete']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesSiete"><?php echo htmlspecialchars($row['accionesSiete']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasSiete"><?php echo htmlspecialchars($row['idProblemasSiete']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoSiete']) ? $row['ruta_archivoSiete'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado2.4" class="form-control resultado" name="estatusSiete">
                                <option value="" disabled <?php echo empty($row['estatusSiete']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusSiete'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusSiete'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusSiete'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila2.4" class="form-control fecha"
                                value="<?php echo $row['fecha_filaSiete']; ?>"></td>

                        <th>
                            <button id="btnEditarSiete" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarSiete" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoSiete" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoSiete" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoSiete" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="8">
                        <td>8</td>
                        <td class="text-justify">El área auditada esta limpia y ordenada (se cuenta con un plan de limpieza y esta documentado)</td>
                        <td class="text-justify">FSH 32</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesOcho"><?php echo htmlspecialchars($row['observacionesOcho']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesOcho"><?php echo htmlspecialchars($row['accionesOcho']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasOcho"><?php echo htmlspecialchars($row['idProblemasOcho']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoOcho']) ? $row['ruta_archivoOcho'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado2.5" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusOcho']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusOcho'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusOcho'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusOcho'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila2.5" class="form-control fecha"
                                value="<?php echo $row['fecha_filaOcho']; ?>"></td>

                        <th>
                            <button id="btnEditarOcho" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoOcho" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoOcho" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoOcho" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="9">
                        <td>9</td>
                        <td class="text-justify">Se encuentra el plan de mantenimiento preventivo y se realiza de acuerdo a lo programado 
                        </td>
                        <td class="text-justify">FMT 03</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesNueve"><?php echo htmlspecialchars($row['observacionesNueve']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesNueve"><?php echo htmlspecialchars($row['accionesNueve']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasNueve"><?php echo htmlspecialchars($row['idProblemasNueve']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoNueve']) ? $row['ruta_archivoNueve'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado2.6" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusNueve']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusNueve'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusNueve'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusNueve'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila2.6" class="form-control fecha"
                                value="<?php echo $row['fecha_filaNueve']; ?>"></td>

                        <th>
                            <button id="btnEditarNueve" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarNueve" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoNueve" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoNueve" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoNueve" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>
    
                    <tr id="10">
                        <td>10</td>
                        <td class="text-justify">Se encuentra la ultima auditoria de capas y cuenta con sus acciones correctivas</td>
                        <td class="text-justify">FAC 25</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDiez"><?php echo htmlspecialchars($row['observacionesDiez']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDiez"><?php echo htmlspecialchars($row['accionesDiez']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDiez"><?php echo htmlspecialchars($row['idProblemasDiez']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDiez']) ? $row['ruta_archivoDiez'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado3_1" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusDiez']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDiez'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDiez'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusDiez'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila3_1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDiez']; ?>"></td>

                        <th>
                            <button id="btnEditarDiez" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDiez" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDiez" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoDiez" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDiez" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
           
                     <tr>
                        <td colspan="9" class="text-start small fw-bold text-justify">
                        Empleados
                        </td>
                    </tr>
                    <tr id="11">
                        <td>11</td>
                        <td class="text-justify">Los  operadores realizan la operación como lo indica su HOJA DE PROCESO</td>
                        <td class="text-justify">FIN 06</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesOnce"><?php echo htmlspecialchars($row['observacionesOnce']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesOnce"><?php echo htmlspecialchars($row['accionesOnce']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasOnce"><?php echo htmlspecialchars($row['idProblemasOnce']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoOnce']) ? $row['ruta_archivoOnce'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado4_1" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusOnce']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusOnce'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusOnce'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusOnce'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila4_1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaOnce']; ?>"></td>

                        <th>
                            <button id="btnEditarOnce" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarOnce" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoOnce" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoOnce" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoOnce" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="12">
                        <td>12</td>
                        <td class="text-justify">Los operadores están informados sobre las reclamaciones y saben como manejar las piezas NOK</td>
                        <td class="text-justify">FAC 52</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDoce"><?php echo htmlspecialchars($row['observacionesDoce']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDoce"><?php echo htmlspecialchars($row['accionesDoce']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDoce"><?php echo htmlspecialchars($row['idProblemasDoce']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDoce']) ? $row['ruta_archivoDoce'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado4_2" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusDoce']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDoce'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDoce'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusDoce'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila4_2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDoce']; ?>"></td>

                        <th>
                            <button id="btnEditarDoce" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDoce" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDoce" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDoce" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDoce" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="13">
                        <td>13</td>
                        <td class="text-justify">Los operadores conocen el plan de reacción en caso de falla conforme lo indicado el PLAN DE CONTROL</td>
                        <td class="text-justify">FIN 08</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesTrece"><?php echo htmlspecialchars($row['observacionesTrece']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesTrece"><?php echo htmlspecialchars($row['accionesTrece']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasTrece"><?php echo htmlspecialchars($row['idProblemasTrece']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoTrece']) ? $row['ruta_archivoTrece'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado4_3" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusTrece']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusTrece'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusTrece'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusTrece'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila4_3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaTrece']; ?>"></td>

                        <th>
                            <button id="btnEditarTrece" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarTrece" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoTrece" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoTrece" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoTrece" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
               
                    <tr id="14">
                        <td>14</td>
                        <td class="text-justify">El operador revisa sus piezas visualmente conforme a lo indicado en el PLAN DE CONTROL</td>
                        <td class="text-justify">FIN 08</td>
                        <td>
                            <textarea class="form-control"
                                id="observacionesCatorce"><?php echo htmlspecialchars($row['observacionesCatorce']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCatorce"><?php echo htmlspecialchars($row['accionesCatorce']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCatorce"><?php echo htmlspecialchars($row['idProblemasCatorce']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCatorce']) ? $row['ruta_archivoCatorce'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_1" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusCatorce']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCatorce'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCatorce'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCatorce'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCatorce']; ?>"></td>

                        <th>
                            <button id="btnEditarCatorce" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCatorce" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCatorce" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCatorce" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCatorce" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="15">
                        <td>15</td>
                        <td class="text-justify">Los empleados cuentan con su EPP completo contra la matriz de EPP</td>
                        <td class="text-justify">FSH22</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesQuince"><?php echo htmlspecialchars($row['observacionesQuince']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesQuince"><?php echo htmlspecialchars($row['accionesQuince']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasQuince"><?php echo htmlspecialchars($row['idProblemasQuince']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoQuince']) ? $row['ruta_archivoQuince'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_2" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusQuince']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusQuince'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusQuince'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusQuince'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaQuince']; ?>"></td>

                        <th>
                            <button id="btnEditarQuince" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarQuince" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoQuince" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoQuince" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoQuince" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="16">
                        <td>16</td>
                        <td class="text-justify">Esta actualizada la matriz de habilidades</td>
                        <td class="text-justify">FAD 14</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDieciseis"><?php echo htmlspecialchars($row['observacionesDieciseis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDieciseis"><?php echo htmlspecialchars($row['accionesDieciseis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDieciseis"><?php echo htmlspecialchars($row['idProblemasDieciseis']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDieciseis']) ? $row['ruta_archivoDieciseis'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_3" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusDieciseis']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDieciseis'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDieciseis'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusDieciseis'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDieciseis']; ?>"></td>

                        <th>
                            <button id="btnEditarDieciseis" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDieciseis" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDieciseis" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDieciseis" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDieciseis" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
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

                        <td>
                            <textarea class="form-control"
                                id="observacionesDiecisiete"><?php echo htmlspecialchars($row['observacionesDiecisiete']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDiecisiete"><?php echo htmlspecialchars($row['accionesDiecisiete']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDiecisiete"><?php echo htmlspecialchars($row['idProblemasDiecisiete']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDiecisiete']) ? $row['ruta_archivoDiecisiete'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_4" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusDiecisiete']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDiecisiete'] == 'OK') ? 'selected' : ''; ?>>
                                    OK</option>
                                <option value="N/A" <?php echo ($row['estatusDiecisiete'] == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                                <option value="NOK" <?php echo ($row['estatusDiecisiete'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_4" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDiecisiete']; ?>"></td>

                        <th>
                            <button id="btnEditarDiecisiete" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDiecisiete" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDiecisiete" class="btn btn-info" style="display:none;">Seguimiento</button>
                            <button id="btnVerSeguimientoDiecisiete" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDiecisiete" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="18">
                        <td>18</td>
                        <td class="text-justify">El dispositivo esta verificado y cuenta con el nivel de ingeniería correspondiente</td>
                        <td class="text-justify">FAC 93, <br>  FIN 04</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDieciocho"><?php echo htmlspecialchars($row['observacionesDieciocho']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDieciocho"><?php echo htmlspecialchars($row['accionesDieciocho']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDieciocho"><?php echo htmlspecialchars($row['idProblemasDieciocho']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDieciocho']) ? $row['ruta_archivoDieciocho'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_5" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusDieciocho']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo (isset($row['estatusDieciocho']) && $row['estatusDieciocho'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                                <option value="N/A" <?php echo (isset($row['estatusDieciocho']) && $row['estatusDieciocho'] == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                                <option value="NOK" <?php echo (isset($row['estatusDieciocho']) && $row['estatusDieciocho'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_5" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaDieciocho'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarDieciocho" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDieciocho" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDieciocho" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoDieciocho" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDieciocho" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="19">
                        <td>19</td>
                        <td class="text-justify">El dispositivo cuenta con el instructivo de uso del mismo</td>
                        <td class="text-justify">FAC 101</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDiecinueve"><?php echo htmlspecialchars($row['observacionesDiecinueve']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDiecinueve"><?php echo htmlspecialchars($row['accionesDiecinueve']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDiecinueve"><?php echo htmlspecialchars($row['idProblemasDiecinueve']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDiecinueve']) ? $row['ruta_archivoDiecinueve'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_6" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusDiecinueve']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDiecinueve'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDiecinueve'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusDiecinueve'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_6" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaDiecinueve'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarDiecinueve" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDiecinueve" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDiecinueve" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDiecinueve" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDiecinueve" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="20">
                        <td>20</td>
                        <td class="text-justify">Esta identificada la materia prima correctamente  (etiqueta de proveedor)</td>
                        <td class="text-justify">VISUAL</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesVeinte"><?php echo htmlspecialchars($row['observacionesVeinte']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesVeinte"><?php echo htmlspecialchars($row['accionesVeinte']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasVeinte"><?php echo htmlspecialchars($row['idProblemasVeinte']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoVeinte']) ? $row['ruta_archivoVeinte'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_7" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusVeinte']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusVeinte'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusVeinte'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusVeinte'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_7" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaVeinte'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarVeinte" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarVeinte" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoVeinte" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoVeinte" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoVeinte" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="21">
                        <td>21</td>
                        <td class="text-justify">Se han anotado las materias primas en el control de carga de materias primas </td>
                        <td class="text-justify">FPR 02</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesVeintiuno"><?php echo htmlspecialchars($row['observacionesVeintiuno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesVeintiuno"><?php echo htmlspecialchars($row['accionesVeintiuno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasVeintiuno"><?php echo htmlspecialchars($row['idProblemasVeintiuno']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoVeintiuno']) ? $row['ruta_archivoVeintiuno'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado5_8" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusVeintiuno']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusVeintiuno'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusVeintiuno'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusVeintiuno'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_8" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaVeintiuno'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarVeintiuno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarVeintiuno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoVeintiuno" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoVeintiuno" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoVeintiuno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>                           
                        </th>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-start small fw-bold text-justify">
                        Materiales salientes
                        </td>
                    </tr>
                    <tr id="22">
                        <td>22</td>
                        <td class="text-justify">La identificación del producto final para envío a cliente es legible.  (Verificar las impresiones de etiqueta individual y SAP)</td>
                        <td class="text-justify">VISUAL</td>

                        <td>
                            <textarea class="form-control"
                                id="idObservaciones6.1"><?php echo htmlspecialchars($row['observacionesVeintidos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idAcciones6.1"><?php echo htmlspecialchars($row['accionesVeintidos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemas6.1"><?php echo htmlspecialchars($row['idProblemasVeintidos']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoVeintidos']) ? $row['ruta_archivoVeintidos'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado6_1" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusVeintidos']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusVeintidos'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusVeintidos'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusVeintidos'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila6_1" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaVeintidos'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarVeintidos" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarVeintidos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoVeintidos" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoVeintidos" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoVeintidos" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    <tr id="23">
                        <td>23</td>
                        <td class="text-justify">Los materiales son  colocados como lo indica la norma empaque liberada</td>
                        <td class="text-justify">FIN 09</td>

                        <td>
                            <textarea class="form-control"
                                id="idObservaciones23"><?php echo htmlspecialchars($row['observacionesVeintitres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idAcciones23"><?php echo htmlspecialchars($row['accionesVeintitres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemas23"><?php echo htmlspecialchars($row['idProblemasVeintitres']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoVeintitres']) ? $row['ruta_archivoVeintitres'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado23" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusVeintitres']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusVeintitres'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusVeintitres'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusVeintitres'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila23" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaVeintitres'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarVeintitres" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarVeintitres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoVeintitres" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoVeintitres" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoVeintitres" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    <tr id="24">
                        <td>24</td>
                        <td class="text-justify">Los contenedores se encuentran en buen estado (limpios, secos y sin roturas) y están libre de etiquetas obsoletas como lo indica la norma de empaque</td>
                        <td class="text-justify">FIN 09</td>
                        <td>
                            <textarea class="form-control"
                                id="idObservaciones24"><?php echo htmlspecialchars($row['observacionesVeinticuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idAcciones24"><?php echo htmlspecialchars($row['accionesVeinticuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemas24"><?php echo htmlspecialchars($row['idProblemasVeinticuatro']); ?></textarea>
                        </td>
                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoVeinticuatro']) ? $row['ruta_archivoVeinticuatro'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>

                        <td>
                            <select id="idResultado24" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusVeinticuatro']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusVeinticuatro'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusVeinticuatro'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusVeinticuatro'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila24" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaVeinticuatro'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>
                            <button id="btnEditarVeinticuatro" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarVeinticuatro" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoVeinticuatro" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoVeinticuatro" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoVeinticuatro" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    <tr id="25">
                        <td>25</td>
                        <td class="text-justify">La identificación del producto final para envío a cliente es legible.  (Verificar las impresiones de etiqueta individual y SAP)</td>
                        <td class="text-justify">VISUAL</td>

                        <td>
                            <textarea class="form-control"
                                id="idObservaciones25"><?php echo htmlspecialchars($row['observacionesVeinticinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idAcciones25"><?php echo htmlspecialchars($row['accionesVeinticinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemas25"><?php echo htmlspecialchars($row['idProblemasVeinticinco']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoVeinticinco']) ? $row['ruta_archivoVeinticinco'] : null;
                             $directorioUploads = '../Controlador/uploads/';
                            if (!empty($rutaArchivo)) {
                                if (strpos($rutaArchivo, 'uploads/') === 0) {
                                    $rutaArchivo = substr($rutaArchivo, strlen('uploads/'));
                                }
                                $rutaArchivoCompleta = $directorioUploads . $rutaArchivo;
                                echo "<a href='$rutaArchivoCompleta' target='_blank'>Ver imagen</a>";
                            } else {
                                echo "No hay archivo disponible.";
                            }
                            ?>
                        </td>
                        <td>
                            <select id="idResultado25" class="form-control resultado">
                                <option value="" disabled <?php echo empty($row['estatusVeinticinco']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusVeinticinco'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusVeinticinco'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusVeinticinco'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila25" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaVeinticinco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>
                            <button id="btnEditarVeinticinco" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarVeinticinco" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoVeinticinco" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoVeinticinco" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoVeinticinco" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    </thead>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Nombre y firma de operador</th>
                                    <th>Nombre y firma de supervisor</th>
                                    <th>Nombre y firma de Auditor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="text" id="idNombreOperador" class="form-control"
                                            placeholder="Nombre-Operador"
                                            value="<?php echo $row['idNombreOperador']; ?>">
                                    </td>
                                    <td>
                                        <input type="text" id="idNombreSupervisor" class="form-control"
                                            placeholder="Nombre-Supervisor"
                                            value="<?php echo $row['idNombreSupervisor']; ?>">
                                    </td>
                                    <td>
                                        <input type="text" id="idNombreAuditor2" class="form-control"
                                            placeholder="Nombre-Auditor"
                                            value="<?php echo $row['idNombreAuditor2']; ?>">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-dark btn-lg fw-bold px-4 py-2" id="cerrarAuditoriaProcesos" >CERRAR AUDITORÍA</button>
                    </div>
                </tbody>
            </table>
        </div>
    </div>
    <br><br><br><br><br>
    <br>
    <!-- Modal para seleccionar correo -->
<div class="modal fade" id="seleccionarCorreoModal" tabindex="-1" aria-labelledby="seleccionarCorreoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seleccionarCorreoModalLabel">Seleccionar Destinatario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="correoDestinatario" class="form-label">Seleccionar Empleado</label>
                    <select class="form-control" id="correoDestinatario" required>
                        <option value="">Selecciona un empleado</option>
                        <!-- Opciones se llenarán dinámicamente -->
                    </select>
                </div>
                <input type="hidden" id="filaSeleccionada" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="enviarCorreoConfirmar">Enviar Correo</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para Seguimiento de UnoUno ❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌-->
<div class="modal fade" id="seguimientoModalUno" tabindex="-1" aria-labelledby="seguimientoModalLabelUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelUno">Nuevo Seguimiento fila 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoUno">
                    <div class="mb-3">
                        <label for="accionModalUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoUno">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalUnoDos" tabindex="-1" aria-labelledby="seguimientoModalLabelUnoDos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelUnoDos">Nuevo Seguimiento fila 2</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoUnoDos">
                    <div class="mb-3">
                        <label for="accionModalUnoDos" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalUnoDos" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalUnoDos" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalUnoDos" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalUnoDos" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalUnoDos">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalUnoDos" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalUnoDos" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoUnoDos">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalUnoTres" tabindex="-1" aria-labelledby="seguimientoModalLabelUnoTres" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelUnoTres">Nuevo Seguimiento fila 3</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoUnoTres">
                    <div class="mb-3">
                        <label for="accionModalUnoTres" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalUnoTres" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalUnoTres" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalUnoTres" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalUnoDos" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalUnoTres">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalUnoTres" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalUnoTres" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoUnoTres">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalCuatro" tabindex="-1" aria-labelledby="seguimientoModalLabelCuatro" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCuatro">Nuevo Seguimiento fila 4</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCuatro">
                    <div class="mb-3">
                        <label for="accionModalCuatro" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCuatro" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCuatro" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCuatro" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCuatro" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCuatro">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCuatro" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCuatro" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCuatro">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalCinco" tabindex="-1" aria-labelledby="seguimientoModalLabelCinco" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCinco">Nuevo Seguimiento fila 5</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCinco">
                    <div class="mb-3">
                        <label for="accionModalCinco" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCinco" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCinco" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCinco" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCinco" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCinco">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCinco" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCinco" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCinco">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalSeis" tabindex="-1" aria-labelledby="seguimientoModalLabelSeis" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelSeis">Nuevo Seguimiento fila 6</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoSeis">
                    <div class="mb-3">
                        <label for="accionModalSeis" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalSeis" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalSeis" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalSeis" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalSeis" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalSeis">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalSeis" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalSeis" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoSeis">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalSiete" tabindex="-1" aria-labelledby="seguimientoModalLabelSiete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelSiete">Nuevo Seguimiento fila 7 </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoSiete">
                    <div class="mb-3">
                        <label for="accionModalSiete" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalSiete" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalSiete" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalSiete" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalSiete" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalSiete">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalSiete" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalSiete" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoSiete">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalOcho" tabindex="-1" aria-labelledby="seguimientoModalLabelOcho" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelOcho">Nuevo Seguimiento fila 8</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoOcho">
                    <div class="mb-3">
                        <label for="accionModalOcho" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalOcho" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalOcho" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalOcho" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalOcho" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalOcho">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalOcho" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalOcho" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoOcho">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalNueve" tabindex="-1" aria-labelledby="seguimientoModalLabelNueve" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelNueve">Nuevo Seguimiento fila 9</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoNueve">
                    <div class="mb-3">
                        <label for="accionModalNueve" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalNueve" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalNueve" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalNueve" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalNueve" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalNueve">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalNueve" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalNueve" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoNueve">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalDiez" tabindex="-1" aria-labelledby="seguimientoModalLabelDiez" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDiez">Nuevo Seguimiento fila 10</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDiez">
                    <div class="mb-3">
                        <label for="accionModalDiez" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDiez" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDiez" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDiez" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDiez" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDiez">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDiez" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDiez" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDiez">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalOnce" tabindex="-1" aria-labelledby="seguimientoModalLabelOnce" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelOnce">Nuevo Seguimiento fila 11</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoOnce">
                    <div class="mb-3">
                        <label for="accionModalOnce" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalOnce" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalOnce" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalOnce" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalOnce" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalOnce">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalOnce" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalOnce" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoOnce">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalDoce" tabindex="-1" aria-labelledby="seguimientoModalLabelDoce" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDoce">Nuevo Seguimiento fila 12</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDoce">
                    <div class="mb-3">
                        <label for="accionModalDoce" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDoce" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDoce" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDoce" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDoce" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDoce">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDoce" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDoce" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDoce">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalTrece" tabindex="-1" aria-labelledby="seguimientoModalLabelTrece" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelTrece">Nuevo Seguimiento fila 13</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoTrece">
                    <div class="mb-3">
                        <label for="accionModalTrece" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalTrece" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalTrece" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalTrece" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalTrece" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalTrece">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalTrece" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalTrece" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoTrece">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalCatorce" tabindex="-1" aria-labelledby="seguimientoModalLabelCatorce" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCatorce">Nuevo Seguimiento fila 14</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCatorce">
                    <div class="mb-3">
                        <label for="accionModalCatorce" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCatorce" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCatorce" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCatorce" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCatorce" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCatorce">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCatorce" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCatorce" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCatorce">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalQuince" tabindex="-1" aria-labelledby="seguimientoModalLabelQuince" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelQuince">Nuevo Seguimiento fila 15 </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoQuince">
                    <div class="mb-3">
                        <label for="accionModalQuince" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalQuince" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalQuince" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalQuince" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalQuince" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalQuince">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalQuince" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalQuince" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoQuince">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalDieciseis" tabindex="-1" aria-labelledby="seguimientoModalLabelDieciseis" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDieciseis">Nuevo Seguimiento fila 16</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDieciseis">
                    <div class="mb-3">
                        <label for="accionModalDieciseis" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDieciseis" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDieciseis" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDieciseis" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDieciseis" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDieciseis">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDieciseis" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDieciseis" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDieciseis">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalDiecisiete" tabindex="-1" aria-labelledby="seguimientoModalLabelDiecisiete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDiecisiete">Nuevo Seguimiento fila 17</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDiecisiete">
                    <div class="mb-3">
                        <label for="accionModalDiecisiete" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDiecisiete" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDiecisiete" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDiecisiete" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDiecisiete" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDiecisiete">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDiecisiete" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDiecisiete" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDiecisiete">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalDieciocho" tabindex="-1" aria-labelledby="seguimientoModalLabelDieciocho" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDieciocho">Nuevo Seguimiento fila 18</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDieciocho">
                    <div class="mb-3">
                        <label for="accionModalDieciocho" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDieciocho" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDieciocho" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDieciocho" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDieciocho" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDieciocho">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDieciocho" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDieciocho" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDieciocho">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalDiecinueve" tabindex="-1" aria-labelledby="seguimientoModalLabelDiecinueve" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDiecinueve">Nuevo Seguimiento fila 19</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDiecinueve">
                    <div class="mb-3">
                        <label for="accionModalDiecinueve" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDiecinueve" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDiecinueve" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDiecinueve" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDiecinueve" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDiecinueve">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDiecinueve" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDiecinueve" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDiecinueve">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalVeinte" tabindex="-1" aria-labelledby="seguimientoModalLabelVeinte" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelVeinte">Nuevo Seguimiento fila 20</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoVeinte">
                    <div class="mb-3">
                        <label for="accionModalVeinte" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalVeinte" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalVeinte" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalVeinte" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalVeinte" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalVeinte">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalVeinte" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalVeinte" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoVeinte">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalVeintiuno" tabindex="-1" aria-labelledby="seguimientoModalLabelVeintiuno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelVeintiuno">Nuevo Seguimiento fila 21</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoVeintiuno">
                    <div class="mb-3">
                        <label for="accionModalVeintiuno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalVeintiuno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalVeintiuno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalVeintiuno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalVeintiuno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalVeintiuno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalVeintiuno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalVeintiuno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoVeintiuno">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalVeintidos" tabindex="-1" aria-labelledby="seguimientoModalLabelVeintidos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelVeintidos">Nuevo Seguimiento fila 22</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoVeintidos">
                    <div class="mb-3">
                        <label for="accionModalVeintidos" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalVeintidos" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalVeintidos" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalVeintidos" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalVeintidos" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalVeintidos">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalVeintidos" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalVeintidos" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoVeintidos">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalVeintitres" tabindex="-1" aria-labelledby="seguimientoModalLabelVeintitres" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelVeintitres">Nuevo Seguimiento fila 23</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoVeintitres">
                    <div class="mb-3">
                        <label for="accionModalVeintitres" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalVeintitres" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalVeintitres" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalVeintitres" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalVeintitres" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalVeintitres">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalVeintitres" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalVeintitres" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoVeintitres">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalVeinticuatro" tabindex="-1" aria-labelledby="seguimientoModalLabelVeinticuatro" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelVeinticuatro">Nuevo Seguimiento fila 24</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoVeinticuatro">
                    <div class="mb-3">
                        <label for="accionModalVeinticuatro" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalVeinticuatro" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalVeinticuatro" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalVeinticuatro" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalVeinticuatro" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalVeinticuatro">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalVeinticuatro" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalVeinticuatro" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoVeinticuatro">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalVeinticinco" tabindex="-1" aria-labelledby="seguimientoModalLabelVeinticinco" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelVeinticinco">Nuevo Seguimiento fila 25</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoVeinticinco">
                    <div class="mb-3">
                        <label for="accionModalVeinticinco" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalVeinticinco" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalVeinticinco" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalVeinticinco" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalVeinticinco" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalVeinticinco">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalVeinticinco" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalVeinticinco" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoVeinticinco">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para Seguimiento de UnoUno✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅-->
<div class="modal fade" id="verSeguimientoModalUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelUno">Seguimientos Fila 1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosUno">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalUnoDos" tabindex="-1" aria-labelledby="verSeguimientoModalLabelUnoDos" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelUnoDos">Seguimientos Fila 2</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosUnoDos">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalUnoTres" tabindex="-1" aria-labelledby="verSeguimientoModalLabelUnoTres" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelUnoTres">Seguimientos Fila 3</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosUnoTres">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalCuatro" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCuatro" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCuatro">Seguimientos Fila 4</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosCuatro">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalCinco" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCinco" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCinco">Seguimientos Fila 5</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosCinco">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalSeis" tabindex="-1" aria-labelledby="verSeguimientoModalLabelSeis" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosSeis">Seguimientos Fila 6</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosSeis">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalSiete" tabindex="-1" aria-labelledby="verSeguimientoModalLabelSiete" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelSiete">Seguimientos Fila 7</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosSiete">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalOcho" tabindex="-1" aria-labelledby="verSeguimientoModalLabelOcho" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelOcho">Seguimientos Fila 8 </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosOcho">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalNueve" tabindex="-1" aria-labelledby="verSeguimientoModalLabelNueve" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelNueve">Seguimientos Fila 9</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosNueve">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalDiez" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDiez" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDiez">Seguimientos Fila 10</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosDiez">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalOnce" tabindex="-1" aria-labelledby="verSeguimientoModalLabelOnce" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelOnce">Seguimientos Fila 11</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosOnce">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalDoce" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDoce" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDoce">Seguimientos Fila 12</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosDoce">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalTrece" tabindex="-1" aria-labelledby="verSeguimientoModalLabelTrece" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelTrece">Seguimientos Fila 13</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosTrece">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalCatorce" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCatorce" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCatorce">Seguimientos Fila 14</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosCatorce">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalQuince" tabindex="-1" aria-labelledby="verSeguimientoModalLabelQuince" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelQuince">Seguimientos Fila 15</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosQuince">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalDieciseis" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDieciseis" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDieciseis">Seguimientos Fila 16</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                            <!-- <th>Estatus</th> -->
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosDieciseis">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalDiecisiete" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDiecisiete" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDiecisiete">Seguimientos Fila 17</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosDiecisiete">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalDieciocho" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDieciocho" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDieciocho">Seguimientos Fila 18 </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosDieciocho">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalDiecinueve" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDiecinueve" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDiecinueve">Seguimientos Fila 19</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosDiecinueve">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="verSeguimientoModalVeinte" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeinte" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelVeinte">Seguimientos Fila 20</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosVeinte">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalVeintiuno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeintiuno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelVeintiuno">Seguimientos Fila 21</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosVeintiuno">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalVeintidos" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeintidos" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelVeintidos">Seguimientos Fila 22</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosVeintidos">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- <div class="modal fade" id="verSeguimientoModalVeintiuno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeintiuno" aria-hidden="true"> -->
<div class="modal fade" id="verSeguimientoModalVeintitres" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeintitres" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelVeintitres">Seguimientos Fila 23</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosVeintitres">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalVeinticuatro" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeinticuatro" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelVeinticuatro">Seguimientos Fila 24</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosVeinticuatro">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="verSeguimientoModalVeinticinco" tabindex="-1" aria-labelledby="verSeguimientoModalLabelVeinticinco" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelVeinticinco">Seguimientos Fila 25</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                            <th>Fecha Compromiso</th>
                            <th>Archivo</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSeguimientosVeinticinco">
                        <!-- Los datos se cargarán dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
    <?php include('pie.php'); ?>
    <!-- <script src="../js/actualizacionesRegistros.js" ></script> -->
    <script src="../js/verRegistroAdminFUNCIONESProceso.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>