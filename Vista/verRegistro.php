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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-white" href="mis_auditorias.php">Mis auditorías programadas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="mis_auditorias.php">Mis registros terminados</a>
                </li>
            </ul>
            <form action="../Controlador/logout.php" method="POST" class="d-flex ms-auto">
                <button type="submit" class="btn btn-danger">Cerrar sesión</button>
            </form>
        </div>
    </div>
</nav>
<div class="container-fluid mt-5">
    <div class="d-flex justify-content-center align-items-center mb-4">
        <h3 class="text-center me-3 text-justify">AUDITORÍA DE PROCESO POR CAPAS</h3>
    </div>
    <h5 class="text-center me-3 text-justify">
        <span id="numeroDocumento">Folio: <?php echo $row['id_auditoria']; ?></span>
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
                    <td><input type="text" id="idNumeroEmpleado" class="form-control" value="<?php echo $row['numero_empleado']; ?>" readonly></td>
                    <td><input type="text" id="idNombreAuditor" class="form-control" value="<?php echo $row['nombre_auditor']; ?>" placeholder="Nombre del Auditor"></td>
                    <td><input type="text" id="idCliente" class="form-control" value="<?php echo $row['cliente']; ?>" placeholder="Cliente"></td>
                    <td><input type="text" id="idProcesoAuditado" class="form-control" value="<?php echo $row['proceso_auditado']; ?>" placeholder="Proceso Auditado"></td>
                    <td><input type="text" id="idParteAuditada" class="form-control" value="<?php echo $row['parte_auditada']; ?>" placeholder="Parte Auditada"></td>
                    <td><input type="text" id="idOperacionAuditada" class="form-control" value="<?php echo $row['operacion_auditada']; ?>" placeholder="Operación Auditada"></td>
                    <td>
                        <select id="idNave" class="form-control">
                            <option value="" disabled <?php echo ($row['nave'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="Nave 1" <?php echo ($row['nave'] == 'Nave 1') ? 'selected' : ''; ?>>1</option>
                            <option value="Nave 2" <?php echo ($row['nave'] == 'Nave 2') ? 'selected' : ''; ?>>2</option>
                            <option value="Nave 3" <?php echo ($row['nave'] == 'Nave 3') ? 'selected' : ''; ?>>3</option>
                            <option value="Nave 4" <?php echo ($row['nave'] == 'Nave 4') ? 'selected' : ''; ?>>4</option>
                            <option value="Nave 5" <?php echo ($row['nave'] == 'Nave 5') ? 'selected' : ''; ?>>5</option>
                            <option value="Nave 6" <?php echo ($row['nave'] == 'Nave 6') ? 'selected' : ''; ?>>6</option>
                            <option value="Nave 7" <?php echo ($row['nave'] == 'Nave 7') ? 'selected' : ''; ?>>7</option>
                            <option value="Nave 7A" <?php echo ($row['nave'] == 'Nave 7A') ? 'selected' : ''; ?>>7A</option>
                            <option value="Nave 8" <?php echo ($row['nave'] == 'Nave 8') ? 'selected' : ''; ?>>8</option>
                            <option value="Nave 9" <?php echo ($row['nave'] == 'Nave 9') ? 'selected' : ''; ?>>9</option>
                            <option value="Nave 14" <?php echo ($row['nave'] == 'Nave 14') ? 'selected' : ''; ?>>14</option>
                        </select>
                    </td>
                    <td>
                        <select id="idUnidad" class="form-control">
                            <option value="" disabled <?php echo ($row['unidad'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="Unidad 1" <?php echo ($row['unidad'] == 'Unidad 1') ? 'selected' : ''; ?>>1</option>
                            <option value="Unidad 2" <?php echo ($row['unidad'] == 'Unidad 2') ? 'selected' : ''; ?>>2</option>
                            <option value="Unidad 3" <?php echo ($row['unidad'] == 'Unidad 3') ? 'selected' : ''; ?>>3</option>
                            <option value="Unidad 4" <?php echo ($row['unidad'] == 'Unidad 4') ? 'selected' : ''; ?>>4</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFecha" class="form-control" value="<?php echo $row['fecha']; ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <h6 class="d-flex justify-content-center align-items-center mb-4"> OK=Conforme  NOK=No Conforme   NA=No Aplica</h6>
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
                    <th class="col-1">Fecha Compromiso</th>
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
                    <td class="text-justify">¿Se han anotado todas las materias primas en el control de trazabilidad correspondiente?</td>
                    <td class="text-justify">Solicitar al supervisor el registro y verificar si todos los materiales que se encuentren en el área están anotados.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observaciones']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['acciones']); ?></p></td>

                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasUnoUno']); ?></p></td>

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
                        <select id="idResultado1.1" class="form-control resultado">
                            <option value="" disabled <?php echo ($row['estatus'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="OK" <?php echo ($row['estatus'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatus'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatus'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila1.1" class="form-control fecha" value="<?php echo $row['fecha_fila']; ?>" /></td>
                </tr>
                <tr>
                    <td>1.2</td>
                    <td class="text-justify">¿Todos los materiales, empaques, dispositivos en el área de producción están en la ubicación correcta como lo indica el lay-out para evitar "contaminación"?</td>
                    <td class="text-justify">Los materiales deben de encontrarse dentro de las delimitaciones establecidas y de acuerdo al documento de lay out</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesUnoDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesUnoDos']); ?></p></td>
                    

                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasUnoDos']); ?></p></td>
                    
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
                            <option value="" disabled <?php echo ($row['estatusUnoDos'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="OK" <?php echo ($row['estatusUnoDos'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusUnoDos'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusUnoDos'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila1.2" class="form-control fecha" value="<?php echo $row['fecha_filaUnoDos']; ?>"></td>
                </tr>
                <tr>
                    <td>1.3</td>
                    <td class="text-justify">¿Todos los materiales en el área de producción están correctamente identificados de acuerdo a la hoja de proceso?</td>
                    <td class="text-justify">Verificar que todo el material del proceso se encuentre correctamente identificado: Materia prima con etiqueta de SAP, Producto en Proceso, Material rechazado con etiqueta roja, producto terminado, sin etiquetas obsoleta. Asegurar que los materiales utilizados estén en la hoja de proceso e identificada la norma de empaque</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesUnoTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesUnoTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasUnoTres']); ?></p></td>
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
                            <option value="" disabled <?php echo ($row['estatusUnoTres'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="OK" <?php echo ($row['estatusUnoTres'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusUnoTres'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusUnoTres'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila1.3" class="form-control fecha" value="<?php echo $row['fecha_filaUnoTres']; ?>"></td>
                </tr>
           
                <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        2. TRABAJO ESTANDARIZADO, COMPETENCIAS Y TOMA DE CONCIENCIA
                    </td>
                </tr>
                <tr data-id="2.1">
                    <td>2.1</td>
                    <td class="text-justify">¿El operador está certificado para realizar la operación de acuerdo a la matriz de habilidades?</td>
                    <td class="text-justify">¿El operador está certificado para realizar la operación de acuerdo a la matriz de habilidades?</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDosUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDosUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDosUno']); ?></p></td>

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
                            <option value="" disabled <?php echo ($row['estatusDosUno'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="OK" <?php echo ($row['estatusDosUno'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusDosUno'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusDosUno'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila2.1" class="form-control fecha" value="<?php echo $row['fecha_filaDosUno']; ?>"></td>
                </tr>
                <tr data-id="2.2">
                    <td>2.2</td>
                    <td class="text-justify">¿Se están llenando correctamente los reportes de control de producción en las frecuencias establecidas?</td>
                    <td class="text-justify">Verificar el formato de producción por hora que se encuentra en el tablero del proceso.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDosDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDosDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDosDos']); ?></p></td>

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
                            <option value="" disabled <?php echo ($row['estatusDosDos'] == '') ? 'selected' : ''; ?>>Selecciona una opción</option>
                            <option value="OK" <?php echo ($row['estatusDosDos'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusDosDos'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusDosDos'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila2.2" class="form-control fecha" value="<?php echo $row['fecha_filaDosDos']; ?>"></td>
                </tr>
                <tr data-id="2.3">
                    <td>2.3</td>
                    <td class="text-justify">Verificar que el registros de Chequeo de maquinaria y equipo, se encuentre con los registros al día</td>
                    <td class="text-justify">Verificar que al arranque de la línea se haya realizado la liberación del proceso mediante el registro de chequeo de maquinaria y equipo y en caso de desviaciones se hayan tomado acciones.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDosTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDosTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDosTres']); ?></p></td>

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
                            <option value="OK" <?php echo ($row['estatusDosTres'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusDosTres'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusDosTres'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila2.3" class="form-control fecha" value="<?php echo $row['fecha_filaDosTres']; ?>"></td>
                </tr>
                <tr id="2.4">
                    <td>2.4</td>
                    <td class="text-justify">La documentación técnica se encuentra disponible en el área de trabajo y es trazable con el diagrama de flujo (hoja de proceso y plan de control) y el operador registra parámetros como lo indica esta documentación</td>
                    <td class="text-justify">Verificar que se encuentre en tablero de información el diagrama de flujo, hoja de proceso, plan de control y que estos documentos cuenten con la misma revisión. La hoja de proceso y plan de control deben tener los mismos procesos declarados en el diagrama de flujo. Revisar que los registros que indica el plan de control se encuentren correctamente llenados.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDosCuatro']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDosCuatro']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDosCuatro']); ?></p></td>

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
                            <option value="OK" <?php echo ($row['estatusDosCuatro'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusDosCuatro'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusDosCuatro'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila2.4" class="form-control fecha" value="<?php echo $row['fecha_filaDosCuatro']; ?>"></td>
                </tr>
                <tr id="2.5">
                    <td>2.5</td>
                    <td class="text-justify">Si la estación auditada cuenta con un sistema de poka yokes, verificar que al arranque del proceso se realizó su revisión y están funcionando.</td>
                    <td class="text-justify">Se solicita al operador el check list de verificación del poka yoke y se corrobora nuevamente su funcionamiento.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDosCinco']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDosCinco']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDosCinco']); ?></p></td>

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
                            <option value="OK" <?php echo ($row['estatusDosCinco'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusDosCinco'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusDosCinco'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila2.5" class="form-control fecha" value="<?php echo $row['fecha_filaDosCinco']; ?>"></td>
                </tr>
                <tr>
                    <td>2.6</td>
                    <td class="text-justify">¿El personal conoce y usa el sistema de escalación en caso de fallas?</td>
                    <td class="text-justify">Se pregunta al operador si sabe a quién o quiénes dirigirse en caso de fallas.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDosSeis']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDosSeis']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDosSeis']); ?></p></td>

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
                            <option value="OK" <?php echo ($row['estatusDosSeis'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusDosSeis'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusDosSeis'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila2.6" class="form-control fecha" value="<?php echo $row['fecha_filaDosSeis']; ?>"></td>
                </tr>
               
                <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        3. LIBERACIÓN DE PROCESO
                    </td>
                </tr>
                <tr id="3.1">
                    <td>3.1</td>
                    <td class="text-justify">Se cuenta con la liberación de proceso al inicio de turno / arranque de la línea por el operador y es validada por el líder de celda?</td>
                    <td class="text-justify">Verificar que en el dispositivo de control se encuentre el registro de la liberación de la primera pieza y este debidamente llenado y firmado por el operador y el líder de grupo</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesTresUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesTresUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasTresUno']); ?></p></td>

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
                            <option value="OK" <?php echo ($row['estatusTresUno'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusTresUno'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusTresUno'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila3.1" class="form-control fecha" value="<?php echo $row['fecha_filaTresUno']; ?>" ></td>
                </tr>
               
                <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        4. CONTROLES DE PROCESO
                    </td>
                </tr>
                <tr id="4">
                    <td>4.1</td>
                    <td class="text-justify">¿Se encuentran en estado correcto de calibración y/o verificación los equipos de control necesarios para la operación?</td>
                    <td class="text-justify">Verificar que el escantillón y los equipos donde se verifican parámetros no indiquen fecha de calibración y/o verificación vencida en su etiqueta de identificación.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCuatroUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCuatroUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCuatroUno']); ?></p></td>

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
                            <option value="OK" <?php echo ($row['estatusCuatroUno'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCuatroUno'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCuatroUno'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila4.1" class="form-control fecha" value="<?php echo $row['fecha_filaCuatroUno']; ?>" ></td>
                </tr>
                <tr id="4.2">
                    <td>4.2</td>
                    <td class="text-justify">¿Si hay no conformidades en alguno de los controles de los tableros están documentadas y siendo tomadas las contramedidas?</td>
                    <td class="text-justify">Si se encuentran parámetros fuera de especificación deben de existir anotaciones en los registros de acciones correctivas / bitácora de proceso</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCuatroDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCuatroDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCuatroDos']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCuatroDos'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCuatroDos'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCuatroDos'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila4.2" class="form-control fecha" value="<?php echo $row['fecha_filaCuatroDos']; ?>"></td>
                </tr>
                <tr id="4.3">
                    <td>4.3</td>
                    <td class="text-justify">¿Los materiales se encuentran estibados de manera que la calidad de la pieza no se vea afectada?</td>
                    <td class="text-justify">Verificar si están estibadas de acuerdo al máximo indicado en hojas de proceso.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCuatroTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCuatroTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCuatroTres']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCuatroTres'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCuatroTres'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCuatroTres'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila4.3" class="form-control fecha" value="<?php echo $row['fecha_filaCuatroTres']; ?>"></td>
                </tr>
                
                <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        5. 5S & AMBIENTAL / SEGURIDAD
                    </td>
                </tr>
                <tr id="5.1">
                    <td>5.1</td>
                    <td class="text-justify">¿Se está utilizando el Equipo de Protección Personal indicado en la matriz de EPP?</td>
                    <td class="text-justify">Solicitar al supervisor su matriz de EPP y verificar físicamente el uso del equipo en el operador.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoUno']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoUno']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoUno'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoUno'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoUno'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5.1" class="form-control fecha" value="<?php echo $row['fecha_filaCincoUno']; ?>"></td>
                </tr>
                <tr id="5.2">
                    <td>5.2</td>
                    <td class="text-justify">Los medios de seguridad incluyen equipos para el control de incendios, control de derrames de productos químicos, solventes, etc; Tales como: Hidrantes, extintores, lava ojos, regaderas, arena / acerrín para control de derrames, etc.</td>
                    <td class="text-justify">En las áreas en donde se manejan materiales peligrosos se encuentran equipos que ayuden a mitigar un impacto causado por un incendio o derrame</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoDos']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoDos']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoDos'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoDos'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoDos'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5.2" class="form-control fecha" value="<?php echo $row['fecha_filaCincoDos']; ?>"></td>
                </tr>
                <tr id="5.3">
                    <td>5.3</td>
                    <td class="text-justify">¿El área está libre de riesgos de accidente (actos y condiciones inseguras)?</td>
                    <td class="text-justify">Actos inseguros: actividades que hacen las personas que pueden ponerlas en riesgo de sufrir un accidente; Condición insegura: instalaciones, equipos y herramientas que no están en condiciones de ser usadas; los moldes en prensas y troqueles cuentan con toda la tornillería instalada en la partes superior e inferior y que pueden causar un accidente en su uso)</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoTres']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoTres']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoTres'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoTres'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoTres'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5.3" class="form-control fecha" value="<?php echo $row['fecha_filaCincoTres']; ?>"></td>
                </tr>
                <tr id="5.4">
                    <td>5.4</td>
                    <td class="text-justify">¿Existe en el área auditada un equipo contra incendio?</td>
                    <td class="text-justify">Asegurar que estos equipos no deben encontrarse obstruidos</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoCuatro']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoCuatro']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoCuatro']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoCuatro'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoCuatro'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoCuatro'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5.4" class="form-control fecha" value="<?php echo $row['fecha_filaCincoCuatro']; ?>"></td>
                </tr>
                <tr id="5.5">
                    <td>5.5</td>
                    <td class="text-justify">Los controles de la maquinaria de producción operan adecuadamente (incluyendo paro de emergencia, guardas, y controles que protejan la integridad del operador) y el área se encuentra iluminada?</td>
                    <td class="text-justify">Las condiciones de los controles o tableros de la maquinaria se encuentra en condiciones adecuadas de uso. Los controles de seguridad se encuentran operando adecuadamente (guardas sin ser bloqueadas, paro de emergencia, Sensores, etc;), la luz es adecuada para la operación</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoCinco']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoCinco']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoCinco']); ?></p></td>
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
                            <option value="Pendiente" <?php echo (isset($row['estatusCincoCinco']) && $row['estatusCincoCinco'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo (isset($row['estatusCincoCinco']) && $row['estatusCincoCinco'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5_5" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaCincoCinco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                </tr>
                <tr id="5.6">
                    <td>5.6</td>
                    <td class="text-justify">¿El lugar de trabajo cumple con el estándar 5S (Eliminar-Ordenar-Limpiar-Estandarizar-Disciplina)?</td>
                    <td class="text-justify">Verificar por ejemplo: que el área se encuentre limpia (sin derrames ni sobrantes en piso y maquinaria), ordenada (cada cosa de acuerdo a lay out e identificaciones) y estandarizada.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoSeis']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoSeis']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoSeis']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoSeis'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoSeis'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoSeis'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5_6" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaCincoSeis'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></td>
                </tr>
                <tr id="5.7">
                    <td>5.7</td>
                    <td class="text-justify">En caso de que aplique, ¿los químicos usados en el proceso están en el contenedor adecuado y correctamente identificados?</td>
                    <td class="text-justify">El recipiente que contenga químicos debe de tener el pictograma de seguridad y el nombre del químico que almacena, verificar que no se utilizan recipientes de refrescos o similares para almacenar materiales químicos.</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoSiete']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoSiete']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoSiete']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoSiete'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoSiete'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoSiete'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5.7" class="form-control fecha" value="<?php echo $row['fecha_filaCincoSiete']; ?>"></td>
                </tr>
                <tr id="5.8">
                    <td>5.8</td>
                    <td class="text-justify">En caso de que aplique, ¿los residuos peligrosos son almacenados e identificados adecuadamente?</td>
                    <td class="text-justify">La identificación de los contenedores de residuos es visible dentro de ellos no existe una mezcla de residuos (metales en contenedores de cartón o residuos peligrosos, residuos peligrosos en contenedores de cartón o metales, cartón en contenedores de cartón o residuos peligrosos).</td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCincoOcho']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCincoOcho']); ?></p></td>
                    <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCincoOcho']); ?></p></td>
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
                            <option value="OK" <?php echo ($row['estatusCincoOcho'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                            <option value="Pendiente" <?php echo ($row['estatusCincoOcho'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="NOK" <?php echo ($row['estatusCincoOcho'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                        </select>
                    </td>
                    <td><input type="date" id="idFechaFila5.8" class="form-control fecha" value="<?php echo $row['fecha_filaCincoOcho']; ?>"></td>
                </tr>
           
                <tr>
                    <td colspan="8" class="text-start small fw-bold text-justify">
                        6. EMPAQUE Y TRAZABILIDAD
                    </td>
                </tr>
                <tr id="6.1">
                <td>6.1</td>
                <td class="text-justify">¿El producto terminado es empacado de acuerdo a la hoja de empaque correspondiente con las etiquetas de liberación y SAP correctas? Si no, ¿se encuentra identificado con etiqueta de material en proceso?</td>
                <td class="text-justify">Solicitar al supervisor la hoja de empaque y verificar físicamente si el producto terminado está de acuerdo al documento.</td>
                <td><p class="form-control-static" id="idObservaciones6.1"><?php echo htmlspecialchars($row['observacionesSeisUno']); ?></p></td>
                <td><p class="form-control-static" id="idAcciones6.1"><?php echo htmlspecialchars($row['accionesSeisUno']); ?></p></td>
                <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasSeisUno']); ?></p></td>
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
                        <option value="OK" <?php echo ($row['estatusSeisUno'] == 'OK') ? 'selected' : ''; ?>>OK</option>
                        <option value="Pendiente" <?php echo ($row['estatusSeisUno'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="NOK" <?php echo ($row['estatusSeisUno'] == 'NOK') ? 'selected' : ''; ?>>NOK</option>
                    </select>
                </td>
                <td><input type="date" id="idFechaFila6.1" class="form-control fecha" value="<?php echo $row['fecha_filaSeisUno']; ?>"></td>
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
                                <input type="text" id="idNombreOperador" class="form-control" placeholder="Nombre-Operador" value="<?php echo $row['idNombreOperador']; ?>">
                            </td>
                            <td>
                                <input type="text" id="idNombreSupervisor" class="form-control" placeholder="Nombre-Supervisor" value="<?php echo $row['idNombreSupervisor']; ?>">
                            </td>
                            <td>
                                <input type="text" id="idNombreAuditor2" class="form-control" placeholder="Nombre-Auditor" value="<?php echo $row['idNombreAuditor2']; ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
                </tbody>
            </table>
        </div>
    </div>
 <br><br><br><br><br>
    <br>
    <?php include('pie.php'); ?>
    <!-- <script src="../js/actualizacionesRegistros.js" ></script> -->
     <script src="../js/graficos.js"></script>
    <!-- Bootstrap JS -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>