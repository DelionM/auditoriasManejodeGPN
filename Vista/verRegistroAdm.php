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
$sql = "SELECT * FROM auditorias WHERE id_auditoria = ?";
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
            <h3 class="text-center me-3 text-justify">AUDITORÍA DE PROCESO POR CAPAS</h3>
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
                        <th>Operación auditada</th>
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
                                value="<?php echo $row['operacion_auditada']; ?>" placeholder="Operación Auditada"></td>
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
                            1. IDENTIFICACION DE MATERIAL - MANEJO DE MATERIAL EN PROCESO Y NO CONFORME -
                        </td>
                    </tr>
                    <tr data-id="1">
                        <td>1.1</td>
                        <td class="text-justify">¿Se han anotado todas las materias primas en el control de trazabilidad
                            correspondiente?</td>
                        <td class="text-justify">Solicitar al supervisor el registro y verificar si todos los materiales
                            que se encuentren en el área están anotados.</td>
                        <td><textarea class="form-control observaciones" id="observaciones"
                                disabled><?php echo htmlspecialchars($row['observaciones']); ?></textarea></td>
                        <td><textarea class="form-control acciones" id="acciones"
                                disabled><?php echo htmlspecialchars($row['acciones']); ?></textarea></td>
                        <td><textarea class="form-control problemas" id="idProblemasUnoUno"
                                disabled><?php echo htmlspecialchars($row['idProblemasUnoUno']); ?></textarea></td>
                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivo']) ? $row['ruta_archivo'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo ($row['estatus'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatus'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                                <option value="N/A" <?php echo ($row['estatus'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatus'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila1.1" class="form-control fecha"
                                value="<?php echo $row['fecha_fila']; ?>" disabled></td>
                        <th>

                            <button id="btnEditarUnoUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarUnoUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <BR></BR>
                            <button id="btnSeguimientoUnoUno" class="btn btn-info" style="display:none;">Generar Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoUnoUno" class="btn btn-success" style="">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoUnoUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>
                    <tr>
                        <td>1.2</td>
                        <td class="text-justify">¿Todos los materiales, empaques, dispositivos en el área de producción
                            están en la ubicación correcta como lo indica el lay-out para evitar "contaminación"?</td>
                        <td class="text-justify">Los materiales deben de encontrarse dentro de las delimitaciones
                            establecidas y de acuerdo al documento de lay out</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesUnoDos"><?php echo htmlspecialchars($row['observacionesUnoDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesUnoDos"><?php echo htmlspecialchars($row['accionesUnoDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasUnoDos"><?php echo htmlspecialchars($row['idProblemasUnoDos']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoUnoDos']) ? $row['ruta_archivoUnoDos'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo ($row['estatusUnoDos'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusUnoDos'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusUnoDos'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusUnoDos'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="date" id="idFechaFila1.2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaUnoDos']; ?>">
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
                            <br>
                            <div>
                            <button id="btnEnviarcorreoUnoDos" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td>1.3</td>
                        <td class="text-justify">¿Todos los materiales en el área de producción están correctamente
                            identificados de acuerdo a la hoja de proceso?</td>
                        <td class="text-justify">Verificar que todo el material del proceso se encuentre correctamente
                            identificado: Materia prima con etiqueta de SAP, Producto en Proceso, Material rechazado con
                            etiqueta roja, producto terminado, sin etiquetas obsoleta. Asegurar que los materiales
                            utilizados estén en la hoja de proceso e identificada la norma de empaque</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesUnoTres"><?php echo htmlspecialchars($row['observacionesUnoTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesUnoTres"><?php echo htmlspecialchars($row['accionesUnoTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasUnoTres"><?php echo htmlspecialchars($row['idProblemasUnoTres']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoUnoTres']) ? $row['ruta_archivoUnoTres'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo ($row['estatusUnoTres'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusUnoTres'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusUnoTres'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusUnoTres'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td>
                            <input type="date" id="idFechaFila1.3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaUnoTres']; ?>">
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
                            <button id="btnEnviarcorreoUnoTres" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>


                        </th>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-start small fw-bold text-justify">
                            2. TRABAJO ESTANDARIZADO, COMPETENCIAS Y TOMA DE CONCIENCIA
                        </td>
                    </tr>
                    <tr data-id="2.1">
                        <td>2.1</td>
                        <td class="text-justify">¿El operador está certificado para realizar la operación de acuerdo a
                            la matriz de habilidades?</td>
                        <td class="text-justify">¿El operador está certificado para realizar la operación de acuerdo a
                            la matriz de habilidades?</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosUno"><?php echo htmlspecialchars($row['observacionesDosUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosUno"><?php echo htmlspecialchars($row['accionesDosUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosUno"><?php echo htmlspecialchars($row['idProblemasDosUno']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDosUno']) ? $row['ruta_archivoDosUno'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo ($row['estatusDosUno'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDosUno'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDosUno'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusDosUno'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td>
                            <input type="date" id="idFechaFila2.1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDosUno']; ?>">
                        </td>

                        <th>
                            <button id="btnEditarDosUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDosUno" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDosUno" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDosUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr data-id="2.2">
                        <td>2.2</td>
                        <td class="text-justify">¿Se están llenando correctamente los reportes de control de producción
                            en las frecuencias establecidas?</td>
                        <td class="text-justify">Verificar el formato de producción por hora que se encuentra en el
                            tablero del proceso.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosDos"><?php echo htmlspecialchars($row['observacionesDosDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosDos"><?php echo htmlspecialchars($row['accionesDosDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosDos"><?php echo htmlspecialchars($row['idProblemasDosDos']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDosDos']) ? $row['ruta_archivoDosDos'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo ($row['estatusDosDos'] == '') ? 'selected' : ''; ?>>
                                    Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDosDos'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDosDos'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusDosDos'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td>
                            <input type="date" id="idFechaFila2.2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDosDos']; ?>">
                        </td>

                        <th>
                            <button id="btnEditarDosDos" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDosDos" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDosDos" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDosDos" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr data-id="2.3">
                        <td>2.3</td>
                        <td class="text-justify">Verificar que el registro de Chequeo de maquinaria y equipo se
                            encuentre con los registros al día</td>
                        <td class="text-justify">Verificar que al arranque de la línea se haya realizado la liberación
                            del proceso mediante el registro de chequeo de maquinaria y equipo y en caso de desviaciones
                            se hayan tomado acciones.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosTres"><?php echo htmlspecialchars($row['observacionesDosTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosTres"><?php echo htmlspecialchars($row['accionesDosTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosTres"><?php echo htmlspecialchars($row['idProblemasDosTres']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDosTres']) ? $row['ruta_archivoDosTres'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusDosTres']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDosTres'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDosTres'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusDosTres'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila2.3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDosTres']; ?>"></td>
                        <th>
                            <button id="btnEditarDosTres" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDosTres" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDosTres" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDosTres" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>
                    <tr id="2.4">
                        <td>2.4</td>
                        <td class="text-justify">La documentación técnica se encuentra disponible en el área de trabajo
                            y es trazable con el diagrama de flujo (hoja de proceso y plan de control) y el operador
                            registra parámetros como lo indica esta documentación</td>
                        <td class="text-justify">Verificar que se encuentre en tablero de información el diagrama de
                            flujo, hoja de proceso, plan de control y que estos documentos cuenten con la misma
                            revisión. La hoja de proceso y plan de control deben tener los mismos procesos declarados en
                            el diagrama de flujo. Revisar que los registros que indica el plan de control se encuentren
                            correctamente llenados.</td>
                        <td>
                            <textarea class="form-control"
                                id="observacionesDosCuatro"><?php echo htmlspecialchars($row['observacionesDosCuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosCuatro"><?php echo htmlspecialchars($row['accionesDosCuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosCuatro"><?php echo htmlspecialchars($row['idProblemasDosCuatro']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDosCuatro']) ? $row['ruta_archivoDosCuatro'] : null;
                            $directorioUploads = '../uploads/';
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
                            <select id="idResultado2.4" class="form-control resultado" name="estatusDosCuatro">
                                <option value="" disabled <?php echo empty($row['estatusDosCuatro']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDosCuatro'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDosCuatro'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusDosCuatro'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila2.4" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDosCuatro']; ?>"></td>

                        <th>
                            <button id="btnEditarDosCuatro" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosCuatro" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDosCuatro" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDosCuatro" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDosCuatro" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="2.5">
                        <td>2.5</td>
                        <td class="text-justify">Si la estación auditada cuenta con un sistema de poka yokes, verificar
                            que al arranque del proceso se realizó su revisión y están funcionando.</td>
                        <td class="text-justify">Se solicita al operador el check list de verificación del poka yoke y
                            se corrobora nuevamente su funcionamiento.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosCinco"><?php echo htmlspecialchars($row['observacionesDosCinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosCinco"><?php echo htmlspecialchars($row['accionesDosCinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosCinco"><?php echo htmlspecialchars($row['idProblemasDosCinco']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDosCinco']) ? $row['ruta_archivoDosCinco'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusDosCinco']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDosCinco'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDosCinco'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusDosCinco'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila2.5" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDosCinco']; ?>"></td>

                        <th>
                            <button id="btnEditarDosCinco" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosCinco" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDosCinco" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDosCinco" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDosCinco" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="2.6">
                        <td>2.6</td>
                        <td class="text-justify">¿El personal conoce y usa el sistema de escalación en caso de fallas?
                        </td>
                        <td class="text-justify">Se pregunta al operador si sabe a quién o quiénes dirigirse en caso de
                            fallas.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesDosSeis"><?php echo htmlspecialchars($row['observacionesDosSeis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesDosSeis"><?php echo htmlspecialchars($row['accionesDosSeis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasDosSeis"><?php echo htmlspecialchars($row['idProblemasDosSeis']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoDosSeis']) ? $row['ruta_archivoDosSeis'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusDosSeis']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusDosSeis'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusDosSeis'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusDosSeis'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila2.6" class="form-control fecha"
                                value="<?php echo $row['fecha_filaDosSeis']; ?>"></td>

                        <th>
                            <button id="btnEditarDosSeis" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarDosSeis" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoDosSeis" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoDosSeis" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoDosSeis" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-start small fw-bold text-justify">
                            3. LIBERACIÓN DE PROCESO
                        </td>
                    </tr>
                    <tr id="3.1">
                        <td>3.1</td>
                        <td class="text-justify">Se cuenta con la liberación de proceso al inicio de turno / arranque de
                            la línea por el operador y es validada por el líder de celda?</td>
                        <td class="text-justify">Verificar que en el dispositivo de control se encuentre el registro de
                            la liberación de la primera pieza y este debidamente llenado y firmado por el operador y el
                            líder de grupo</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesTresUno"><?php echo htmlspecialchars($row['observacionesTresUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesTresUno"><?php echo htmlspecialchars($row['accionesTresUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasTresUno"><?php echo htmlspecialchars($row['idProblemasTresUno']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoTresUno']) ? $row['ruta_archivoTresUno'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusTresUno']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusTresUno'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusTresUno'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusTresUno'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila3.1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaTresUno']; ?>"></td>

                        <th>
                            <button id="btnEditarTresUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarTresUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoTresUno" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoTresUno" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoTresUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-start small fw-bold text-justify">
                            4. CONTROLES DE PROCESO
                        </td>
                    </tr>
                    <tr id="4">
                        <td>4.1</td>
                        <td class="text-justify">¿Se encuentran en estado correcto de calibración y/o verificación los
                            equipos de control necesarios para la operación?</td>
                        <td class="text-justify">Verificar que el escantillón y los equipos donde se verifican
                            parámetros no indiquen fecha de calibración y/o verificación vencida en su etiqueta de
                            identificación.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCuatroUno"><?php echo htmlspecialchars($row['observacionesCuatroUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCuatroUno"><?php echo htmlspecialchars($row['accionesCuatroUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCuatroUno"><?php echo htmlspecialchars($row['idProblemasCuatroUno']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCuatroUno']) ? $row['ruta_archivoCuatroUno'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCuatroUno']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCuatroUno'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCuatroUno'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCuatroUno'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila4.1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCuatroUno']; ?>"></td>

                        <th>
                            <button id="btnEditarCuatroUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCuatroUno" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCuatroUno" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCuatroUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="4.2">
                        <td>4.2</td>
                        <td class="text-justify">¿Si hay no conformidades en alguno de los controles de los tableros
                            están documentadas y siendo tomadas las contramedidas?</td>
                        <td class="text-justify">Si se encuentran parámetros fuera de especificación deben de existir
                            anotaciones en los registros de acciones correctivas / bitácora de proceso</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCuatroDos"><?php echo htmlspecialchars($row['observacionesCuatroDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCuatroDos"><?php echo htmlspecialchars($row['accionesCuatroDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCuatroDos"><?php echo htmlspecialchars($row['idProblemasCuatroDos']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCuatroDos']) ? $row['ruta_archivoCuatroDos'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCuatroDos']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCuatroDos'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCuatroDos'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCuatroDos'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila4.2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCuatroDos']; ?>"></td>

                        <th>
                            <button id="btnEditarCuatroDos" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCuatroDos" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCuatroDos" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCuatroDos" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="4.3">
                        <td>4.3</td>
                        <td class="text-justify">¿Los materiales se encuentran estibados de manera que la calidad de la
                            pieza no se vea afectada?</td>
                        <td class="text-justify">Verificar si están estibadas de acuerdo al máximo indicado en hojas de
                            proceso.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCuatroTres"><?php echo htmlspecialchars($row['observacionesCuatroTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCuatroTres"><?php echo htmlspecialchars($row['accionesCuatroTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCuatroTres"><?php echo htmlspecialchars($row['idProblemasCuatroTres']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCuatroTres']) ? $row['ruta_archivoCuatroTres'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCuatroTres']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCuatroTres'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCuatroTres'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCuatroTres'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila4.3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCuatroTres']; ?>"></td>

                        <th>
                            <button id="btnEditarCuatroTres" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCuatroTres" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCuatroTres" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoCuatroTres" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCuatroTres" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-start small fw-bold text-justify">
                            5. 5S & AMBIENTAL / SEGURIDAD
                        </td>
                    </tr>
                    <tr id="5.1">
                        <td>5.1</td>
                        <td class="text-justify">¿Se está utilizando el Equipo de Protección Personal indicado en la
                            matriz de EPP?</td>
                        <td class="text-justify">Solicitar al supervisor su matriz de EPP y verificar físicamente el uso
                            del equipo en el operador.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoUno"><?php echo htmlspecialchars($row['observacionesCincoUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoUno"><?php echo htmlspecialchars($row['accionesCincoUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoUno"><?php echo htmlspecialchars($row['idProblemasCincoUno']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoUno']) ? $row['ruta_archivoCincoUno'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoUno']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoUno'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCincoUno'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoUno'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5.1" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCincoUno']; ?>"></td>

                        <th>
                            <button id="btnEditarCincoUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoUno" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCincoUno" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="5.2">
                        <td>5.2</td>
                        <td class="text-justify">Los medios de seguridad incluyen equipos para el control de incendios,
                            control de derrames de productos químicos, solventes, etc; Tales como: Hidrantes,
                            extintores, lava ojos, regaderas, arena / acerrín para control de derrames, etc.</td>
                        <td class="text-justify">En las áreas en donde se manejan materiales peligrosos se encuentran
                            equipos que ayuden a mitigar un impacto causado por un incendio o derrame</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoDos"><?php echo htmlspecialchars($row['observacionesCincoDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoDos"><?php echo htmlspecialchars($row['accionesCincoDos']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoDos"><?php echo htmlspecialchars($row['idProblemasCincoDos']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoDos']) ? $row['ruta_archivoCincoDos'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoDos']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoDos'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCincoDos'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoDos'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5.2" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCincoDos']; ?>"></td>

                        <th>
                            <button id="btnEditarCincoDos" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoDos" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoDos" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCincoDos" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoDos" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="5.3">
                        <td>5.3</td>
                        <td class="text-justify">¿El área está libre de riesgos de accidente (actos y condiciones
                            inseguras)?</td>
                        <td class="text-justify">Actos inseguros: actividades que hacen las personas que pueden ponerlas
                            en riesgo de sufrir un accidente; Condición insegura: instalaciones, equipos y herramientas
                            que no están en condiciones de ser usadas; los moldes en prensas y troqueles cuentan con
                            toda la tornillería instalada en la partes superior e inferior y que pueden causar un
                            accidente en su uso)</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoTres"><?php echo htmlspecialchars($row['observacionesCincoTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoTres"><?php echo htmlspecialchars($row['accionesCincoTres']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoTres"><?php echo htmlspecialchars($row['idProblemasCincoTres']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoTres']) ? $row['ruta_archivoCincoTres'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoTres']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoTres'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCincoTres'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoTres'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5.3" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCincoTres']; ?>"></td>

                        <th>
                            <button id="btnEditarCincoTres" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoTres" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoTres" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCincoTres" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoTres" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="5.4">
                        <td>5.4</td>
                        <td class="text-justify">¿Existe en el área auditada un equipo contra incendio?</td>
                        <td class="text-justify">Asegurar que estos equipos no deben encontrarse obstruidos</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoCuatro"><?php echo htmlspecialchars($row['observacionesCincoCuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoCuatro"><?php echo htmlspecialchars($row['accionesCincoCuatro']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoCuatro"><?php echo htmlspecialchars($row['idProblemasCincoCuatro']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoCuatro']) ? $row['ruta_archivoCincoCuatro'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoCuatro']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoCuatro'] == 'OK') ? 'selected' : ''; ?>>
                                    OK</option>
                                <option value="N/A" <?php echo ($row['estatusCincoCuatro'] == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoCuatro'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5.4" class="form-control fecha"
                                value="<?php echo $row['fecha_filaCincoCuatro']; ?>"></td>

                        <th>
                            <button id="btnEditarCincoCuatro" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoCuatro" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoCuatro" class="btn btn-info" style="display:none;">Seguimiento</button>
                            <button id="btnVerSeguimientoCincoCuatro" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoCuatro" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="5.5">
                        <td>5.5</td>
                        <td class="text-justify">Los controles de la maquinaria de producción operan adecuadamente
                            (incluyendo paro de emergencia, guardas, y controles que protejan la integridad del
                            operador) y el área se encuentra iluminada?</td>
                        <td class="text-justify">Las condiciones de los controles o tableros de la maquinaria se
                            encuentra en condiciones adecuadas de uso. Los controles de seguridad se encuentran operando
                            adecuadamente (guardas sin ser bloqueadas, paro de emergencia, Sensores, etc;), la luz es
                            adecuada para la operación</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoCinco"><?php echo htmlspecialchars($row['observacionesCincoCinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoCinco"><?php echo htmlspecialchars($row['accionesCincoCinco']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoCinco"><?php echo htmlspecialchars($row['idProblemasCincoCinco']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoCinco']) ? $row['ruta_archivoCincoCinco'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoCinco']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo (isset($row['estatusCincoCinco']) && $row['estatusCincoCinco'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                                <option value="N/A" <?php echo (isset($row['estatusCincoCinco']) && $row['estatusCincoCinco'] == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                                <option value="NOK" <?php echo (isset($row['estatusCincoCinco']) && $row['estatusCincoCinco'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_5" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaCincoCinco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarCincoCinco" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoCinco" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoCinco" class="btn btn-info" style="display:none;">Nuevo Seguimiento</button>
                            <button id="btnVerSeguimientoCincoCinco" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoCinco" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="5.6">
                        <td>5.6</td>
                        <td class="text-justify">¿El lugar de trabajo cumple con el estándar 5S
                            (Eliminar-Ordenar-Limpiar-Estandarizar-Disciplina)?</td>
                        <td class="text-justify">Verificar por ejemplo: que el área se encuentre limpia (sin derrames ni
                            sobrantes en piso y maquinaria), ordenada (cada cosa de acuerdo a lay out e
                            identificaciones) y estandarizada.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoSeis"><?php echo htmlspecialchars($row['observacionesCincoSeis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoSeis"><?php echo htmlspecialchars($row['accionesCincoSeis']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoSeis"><?php echo htmlspecialchars($row['idProblemasCincoSeis']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoSeis']) ? $row['ruta_archivoCincoSeis'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoSeis']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoSeis'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCincoSeis'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoSeis'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_6" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaCincoSeis'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarCincoSeis" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoSeis" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoSeis" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCincoSeis" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoSeis" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>

                    <tr id="5.7">
                        <td>5.7</td>
                        <td class="text-justify">En caso de que aplique, ¿los químicos usados en el proceso están en el
                            contenedor adecuado y correctamente identificados?</td>
                        <td class="text-justify">El recipiente que contenga químicos debe de tener el pictograma de
                            seguridad y el nombre del químico que almacena, verificar que no se utilizan recipientes de
                            refrescos o similares para almacenar materiales químicos.</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoSiete"><?php echo htmlspecialchars($row['observacionesCincoSiete']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoSiete"><?php echo htmlspecialchars($row['accionesCincoSiete']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoSiete"><?php echo htmlspecialchars($row['idProblemasCincoSiete']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoSiete']) ? $row['ruta_archivoCincoSiete'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoSiete']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoSiete'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCincoSiete'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoSiete'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_7" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaCincoSiete'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarCincoSiete" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoSiete" class="btn bg-primary text-white"
                                style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoSiete" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCincoSiete" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoSiete" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>

                        </th>
                    </tr>

                    <tr id="5.8">
                        <td>5.8</td>
                        <td class="text-justify">En caso de que aplique, ¿los residuos peligrosos son almacenados e
                            identificados adecuadamente?</td>
                        <td class="text-justify">La identificación de los contenedores de residuos es visible dentro de
                            ellos no existe una mezcla de residuos (metales en contenedores de cartón o residuos
                            peligrosos, residuos peligrosos en contenedores de cartón o metales, cartón en contenedores
                            de cartón o residuos peligrosos).</td>

                        <td>
                            <textarea class="form-control"
                                id="observacionesCincoOcho"><?php echo htmlspecialchars($row['observacionesCincoOcho']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="accionesCincoOcho"><?php echo htmlspecialchars($row['accionesCincoOcho']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemasCincoOcho"><?php echo htmlspecialchars($row['idProblemasCincoOcho']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoCincoOcho']) ? $row['ruta_archivoCincoOcho'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusCincoOcho']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusCincoOcho'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusCincoOcho'] == 'N/A') ? 'selected' : ''; ?>>
                                    N/A</option>
                                <option value="NOK" <?php echo ($row['estatusCincoOcho'] == 'NOK') ? 'selected' : ''; ?>>
                                    NOK</option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila5_8" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaCincoOcho'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <th>
                            <button id="btnEditarCincoOcho" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarCincoOcho" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoCincoOcho" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoCincoOcho" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoCincoOcho" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="8" class="text-start small fw-bold text-justify">
                            6. EMPAQUE Y TRAZABILIDAD
                        </td>
                    </tr>
                    <tr id="6.1">
                        <td>6.1</td>
                        <td class="text-justify">¿El producto terminado es empacado de acuerdo a la hoja de empaque
                            correspondiente con las etiquetas de liberación y SAP correctas? Si no, ¿se encuentra
                            identificado con etiqueta de material en proceso?</td>
                        <td class="text-justify">Solicitar al supervisor la hoja de empaque y verificar físicamente si
                            el producto terminado está de acuerdo al documento.</td>

                        <td>
                            <textarea class="form-control"
                                id="idObservaciones6.1"><?php echo htmlspecialchars($row['observacionesSeisUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idAcciones6.1"><?php echo htmlspecialchars($row['accionesSeisUno']); ?></textarea>
                        </td>
                        <td>
                            <textarea class="form-control"
                                id="idProblemas6.1"><?php echo htmlspecialchars($row['idProblemasSeisUno']); ?></textarea>
                        </td>

                        <td>
                            <?php
                            $rutaArchivo = isset($row['ruta_archivoSeisUno']) ? $row['ruta_archivoSeisUno'] : null;
                            $directorioUploads = '../uploads/';
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
                                <option value="" disabled <?php echo empty($row['estatusSeisUno']) ? 'selected' : ''; ?>>Selecciona una opción</option>
                                <option value="OK" <?php echo ($row['estatusSeisUno'] == 'OK') ? 'selected' : ''; ?>>OK
                                </option>
                                <option value="N/A" <?php echo ($row['estatusSeisUno'] == 'N/A') ? 'selected' : ''; ?>>N/A
                                </option>
                                <option value="NOK" <?php echo ($row['estatusSeisUno'] == 'NOK') ? 'selected' : ''; ?>>NOK
                                </option>
                            </select>
                        </td>

                        <td><input type="date" id="idFechaFila6.1" class="form-control fecha"
                                value="<?php echo htmlspecialchars($row['fecha_filaSeisUno'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>

                        <th>
                            <button id="btnEditarSeisUno" class="btn bg-warning text-white me-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button id="btnActualizarSeisUno" class="btn bg-primary text-white" style="display:none;">
                                <i class="fas fa-sync"></i>
                            </button>
                            <button id="btnSeguimientoSeisUno" class="btn btn-info" style="display:none;">Nuevo seguimiento</button>
                            <button id="btnVerSeguimientoSeisUno" class="btn btn-success" style="display:none;">Ver Seguimiento</button>
                            <button id="btnEnviarcorreoSeisUno" class="btn btn-dark text-white" style="display:none;">Enviar Correo</button>
                        </th>
                    </tr>
                    </thead>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Nombre y firma de operador</th>
                                    <th>Nombre y firma de superviso|r</th>
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
                        <button id="cerrarAuditoria" class="btn btn-dark btn-lg fw-bold px-4 py-2">CERRAR AUDITORÍA</button>
                    </div>
                </tbody>
            </table>
        </div>
    </div>
    <br><br><br><br><br>
<!-- Modal para Seguimiento de UnoUno ❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌❌-->
<div class="modal fade" id="seguimientoModalUnoUno" tabindex="-1" aria-labelledby="seguimientoModalLabelUnoUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelUnoUno">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoUnoUno">
                    <div class="mb-3">
                        <label for="accionModalUnoUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalUnoUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalUnoUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalUnoUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalUnoUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalUnoUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalUnoUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalUnoUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoUnoUno">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalUnoDos" tabindex="-1" aria-labelledby="seguimientoModalLabelUnoDos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelUnoDos">Seguimiento - Fila 1.1</h5>
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
                <h5 class="modal-title" id="seguimientoModalLabelUnoTres">Seguimiento - Fila 1.1</h5>
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
<div class="modal fade" id="seguimientoModalDosUno" tabindex="-1" aria-labelledby="seguimientoModalLabelDosUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDosUno">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDosUno">
                    <div class="mb-3">
                        <label for="accionModalDosUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDosUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDosUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDosUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDosUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDosUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDosUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDosUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDosUno">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalDosDos" tabindex="-1" aria-labelledby="seguimientoModalLabelDosDos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDosDos">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDosDos">
                    <div class="mb-3">
                        <label for="accionModalDosDos" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDosDos" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDosDos" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDosDos" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDosDos" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDosDos">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDosDos" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDosDos" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDosDos">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalDosTres" tabindex="-1" aria-labelledby="seguimientoModalLabelDosTres" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDosTres">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDosTres">
                    <div class="mb-3">
                        <label for="accionModalDosTres" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDosTres" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDosTres" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDosTres" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDosTres" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDosTres">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDosTres" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDosTres" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDosTres">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalDosCuatro" tabindex="-1" aria-labelledby="seguimientoModalLabelDosCuatro" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDosCuatro">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDosCuatro">
                    <div class="mb-3">
                        <label for="accionModalDosCuatro" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDosCuatro" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDosCuatro" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDosCuatro" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDosCuatro" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDosCuatro">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDosCuatro" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDosCuatro" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDosCuatro">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalDosCinco" tabindex="-1" aria-labelledby="seguimientoModalLabelDosCinco" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDosCinco">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDosCinco">
                    <div class="mb-3">
                        <label for="accionModalDosCinco" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDosCinco" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDosCinco" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDosCinco" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDosCinco" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDosCinco">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDosCinco" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDosCinco" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDosCinco">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalDosSeis" tabindex="-1" aria-labelledby="seguimientoModalLabelDosSeis" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelDosSeis">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoDosSeis">
                    <div class="mb-3">
                        <label for="accionModalDosSeis" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalDosSeis" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalDosSeis" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalDosSeis" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalDosSeis" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalDosSeis">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalDosSeis" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalDosSeis" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoDosSeis">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalTresUno" tabindex="-1" aria-labelledby="seguimientoModalLabelTresUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelTresUno">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoTresUno">
                    <div class="mb-3">
                        <label for="accionModalTresUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalTresUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalTresUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalTresUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalTresUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalTresUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalTresUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalTresUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoTresUno">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalCuatroUno" tabindex="-1" aria-labelledby="seguimientoModalLabelCuatroUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCuatroUno">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCuatroUno">
                    <div class="mb-3">
                        <label for="accionModalCuatroUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCuatroUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCuatroUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCuatroUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCuatroUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCuatroUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCuatroUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCuatroUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCuatroUno">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCuatroDos" tabindex="-1" aria-labelledby="seguimientoModalLabelCuatroDos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCuatroDos">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCuatroDos">
                    <div class="mb-3">
                        <label for="accionModalCuatroDos" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCuatroDos" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCuatroDos" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCuatroDos" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCuatroDos" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCuatroDos">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCuatroDos" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCuatroDos" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCuatroDos">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCuatroTres" tabindex="-1" aria-labelledby="seguimientoModalLabelCuatroTres" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCuatroTres">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCuatroTres">
                    <div class="mb-3">
                        <label for="accionModalCuatroTres" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCuatroTres" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCuatroTres" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCuatroTres" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCuatroTres" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCuatroTres">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCuatroTres" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCuatroTres" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCuatroTres">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalCincoUno" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoUno">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoUno">
                    <div class="mb-3">
                        <label for="accionModalCincoUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoUno">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="seguimientoModalCincoDos" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoDos" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoDos">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoDos">
                    <div class="mb-3">
                        <label for="accionModalCincoDos" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoDos" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoDos" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoDos" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoDos" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoDos">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoDos" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoDos" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoDos">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCincoTres" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoTres" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoTres">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoTres">
                    <div class="mb-3">
                        <label for="accionModalCincoTres" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoTres" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoTres" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoTres" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoTres" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoTres">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoTres" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoTres" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoTres">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCincoCuatro" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoCuatro" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoCuatro">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoCuatro">
                    <div class="mb-3">
                        <label for="accionModalCincoCuatro" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoCuatro" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoCuatro" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoCuatro" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoCuatro" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoCuatro">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoCuatro" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoCuatro" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoCuatro">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCincoCinco" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoCinco" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoCinco">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoCinco">
                    <div class="mb-3">
                        <label for="accionModalCincoCinco" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoCinco" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoCinco" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoCinco" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoCinco" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoCinco">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoCinco" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoCinco" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoCinco">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCincoSeis" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoSeis" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoSeis">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoSeis">
                    <div class="mb-3">
                        <label for="accionModalCincoSeis" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoSeis" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoSeis" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoSeis" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoSeis" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoSeis">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoSeis" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoSeis" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoSeis">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCincoSiete" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoSiete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoSiete">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoSiete">
                    <div class="mb-3">
                        <label for="accionModalCincoSiete" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoSiete" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoSiete" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoSiete" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoSiete" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoSiete">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoSiete" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoSiete" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoSiete">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalCincoOcho" tabindex="-1" aria-labelledby="seguimientoModalLabelCincoOcho" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelCincoOcho">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoCincoOcho">
                    <div class="mb-3">
                        <label for="accionModalCincoOcho" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalCincoOcho" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalCincoOcho" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalCincoOcho" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalCincoOcho" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalCincoOcho">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalCincoOcho" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalCincoOcho" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoCincoOcho">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="seguimientoModalSeisUno" tabindex="-1" aria-labelledby="seguimientoModalLabelSeisUno" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seguimientoModalLabelSeisUno">Seguimiento - Fila 1.1</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSeguimientoSeisUno">
                    <div class="mb-3">
                        <label for="accionModalSeisUno" class="form-label">Acción</label>
                        <textarea class="form-control" id="accionModalSeisUno" rows="3" placeholder="Describe la acción a tomar"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="observacionModalSeisUno" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacionModalSeisUno" rows="3" placeholder="Ingresa observaciones"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="fechaModalSeisUno" class="form-label">Fecha Compromiso</label>
                        <input type="date" class="form-control" id="fechaModalSeisUno">
                    </div>
                    <div class="mb-3">
                        <label for="archivoModalSeisUno" class="form-label">Subir Archivo</label>
                        <input type="file" class="form-control" id="archivoModalSeisUno" accept=".jpg,.png,.pdf">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="guardarSeguimientoSeisUno">Guardar</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para Seguimiento de UnoUno✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅✅-->

<!-- Modal para ver los seguimientos de UnoUno -->
<div class="modal fade" id="verSeguimientoModalUnoUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelUnoUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelUnoUno">Seguimientos - Fila 1.1</h5>
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
                    <tbody id="tablaSeguimientosUnoUno">
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
                <h5 class="modal-title" id="verSeguimientoModalLabelUnoDos">Seguimientos - Fila 1.2</h5>
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
                <h5 class="modal-title" id="verSeguimientoModalLabelUnoTres">Seguimientos - Fila 1.3</h5>
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

<div class="modal fade" id="verSeguimientoModalDosUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDosUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosUno">Seguimientos - Fila 2.1</h5>
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
                    <tbody id="tablaSeguimientosDosUno">
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

<div class="modal fade" id="verSeguimientoModalDosDos" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDosDos" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosDos">Seguimientos - Fila 2.2</h5>
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
                    <tbody id="tablaSeguimientosDosDos">
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

<div class="modal fade" id="verSeguimientoModalDosTres" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDosTres" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosTres">Seguimientos - Fila 2.3</h5>
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
                    <tbody id="tablaSeguimientosDosTres">
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

<div class="modal fade" id="verSeguimientoModalDosCuatro" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDosCuatro" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosCuatro">Seguimientos - Fila 2.4</h5>
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
                    <tbody id="tablaSeguimientosDosCuatro">
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

<div class="modal fade" id="verSeguimientoModalDosCinco" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDosCinco" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosCinco">Seguimientos - Fila 2.5</h5>
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
                    <tbody id="tablaSeguimientosDosCinco">
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

<div class="modal fade" id="verSeguimientoModalDosSeis" tabindex="-1" aria-labelledby="verSeguimientoModalLabelDosSeis" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelDosSeis">Seguimientos - Fila 2.6</h5>
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
                    <tbody id="tablaSeguimientosDosSeis">
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

<div class="modal fade" id="verSeguimientoModalTresUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelTresUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelTresUno">Seguimientos - Fila 3.1</h5>
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
                    <tbody id="tablaSeguimientosTresUno">
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

<div class="modal fade" id="verSeguimientoModalCuatroUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCuatroUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCuatroUno">Seguimientos - Fila 4.1</h5>
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
                    <tbody id="tablaSeguimientosCuatroUno">
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

<div class="modal fade" id="verSeguimientoModalCuatroDos" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCuatroDos" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCuatroDos">Seguimientos - Fila 4.2</h5>
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
                    <tbody id="tablaSeguimientosCuatroDos">
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

<div class="modal fade" id="verSeguimientoModalCuatroTres" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCuatroTres" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCuatroTres">Seguimientos - Fila 4.3</h5>
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
                    <tbody id="tablaSeguimientosCuatroTres">
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

<div class="modal fade" id="verSeguimientoModalCincoUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoUno">Seguimientos - Fila 5.1</h5>
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
                    <tbody id="tablaSeguimientosCincoUno">
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

<div class="modal fade" id="verSeguimientoModalCincoDos" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoDos" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoDos">Seguimientos - Fila 5.2</h5>
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
                    <tbody id="tablaSeguimientosCincoDos">
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

<div class="modal fade" id="verSeguimientoModalCincoTres" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoTres" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoTres">Seguimientos - Fila 5.3</h5>
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
                    <tbody id="tablaSeguimientosCincoTres">
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

<div class="modal fade" id="verSeguimientoModalCincoCuatro" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoCuatro" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoCuatro">Seguimientos - Fila 5.4</h5>
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
                    <tbody id="tablaSeguimientosCincoCuatro">
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

<div class="modal fade" id="verSeguimientoModalCincoCinco" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoCinco" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoCinco">Seguimientos - Fila 5.5</h5>
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
                    <tbody id="tablaSeguimientosCincoCinco">
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

<div class="modal fade" id="verSeguimientoModalCincoSeis" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoSeis" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoSeis">Seguimientos - Fila 5.6</h5>
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
                    <tbody id="tablaSeguimientosCincoSeis">
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

<div class="modal fade" id="verSeguimientoModalCincoSiete" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoSiete" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoSiete">Seguimientos - Fila 5.7</h5>
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
                    <tbody id="tablaSeguimientosCincoSiete">
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

<div class="modal fade" id="verSeguimientoModalCincoOcho" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoOcho" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelCincoOcho">Seguimientos - Fila 5.8</h5>
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
                    <tbody id="tablaSeguimientosCincoOcho">
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


<div class="modal fade" id="verSeguimientoModalSeisUno" tabindex="-1" aria-labelledby="verSeguimientoModalLabelSeisUno" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verSeguimientoModalLabelSeisUno">Seguimientos - Fila 6.1</h5>
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
                    <tbody id="tablaSeguimientosSeisUno">
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
<!-- <div class="modal fade" id="verSeguimientoModalCincoOcho" tabindex="-1" aria-labelledby="verSeguimientoModalLabelCincoOcho" aria-hidden="true"> -->


    <?php include('pie.php'); ?>
    <!-- <script src="../js/actualizacionesRegistros.js" ></script> -->
    <script src="../js/verRegistroAdminFUNCIONES.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>