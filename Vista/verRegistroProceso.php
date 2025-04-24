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
    <title>Auditoría de Proceso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
</head>
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
            <h3 class="text-center me-3 text-justify">AUDITORÍA DE PROCESO</h3>
        </div>
        <h5 class="text-center me-3 text-justify">
            <span id="numeroDocumento">Folio: <?php echo htmlspecialchars($row['id_auditoria']); ?></span>
        </h5>

        <div class="table-responsive" style="max-width: 95%; margin: 0 auto;">
            <table class="table table-bordered table-striped text-center small-text">
                <thead class="table-info">
                    <tr>
                        <th>Número de colaborador:</th>
                        <th>Nombre del Auditor:</th>
                        <th>Cliente:</th>
                        <th>Proceso auditado:</th>
                        <th>No. De parte auditada</th>
                        <th>Nivel Ingeniería</th>
                        <th>Nave:</th>
                        <th>Supervisor:</th>
                        <th>Unidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" id="idNumeroEmpleado" class="form-control" value="<?php echo htmlspecialchars($row['numero_empleado']); ?>" readonly></td>
                        <td><input type="text" id="idNombreAuditor" class="form-control" value="<?php echo htmlspecialchars($row['nombre_auditor']); ?>" readonly></td>
                        <td><input type="text" id="idCliente" class="form-control" value="<?php echo htmlspecialchars($row['cliente']); ?>" readonly></td>
                        <td><input type="text" id="idProcesoAuditado" class="form-control" value="<?php echo htmlspecialchars($row['proceso_auditado']); ?>" readonly></td>
                        <td><input type="text" id="idParteAuditada" class="form-control" value="<?php echo htmlspecialchars($row['parte_auditada']); ?>" readonly></td>
                        <td><input type="text" id="idNivelIngenieria" class="form-control" value="<?php echo htmlspecialchars($row['nivelIngenieria']); ?>" readonly></td>
                        <td>
                            <select id="idNave" class="form-control" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="Nave 1" <?php if($row['nave'] == 'Nave 1') echo 'selected'; ?>>Nave 1</option>
                                <option value="Nave 2" <?php if($row['nave'] == 'Nave 2') echo 'selected'; ?>>Nave 2</option>
                                <option value="Nave 3" <?php if($row['nave'] == 'Nave 3') echo 'selected'; ?>>Nave 3</option>
                                <option value="Nave 4" <?php if($row['nave'] == 'Nave 4') echo 'selected'; ?>>Nave 4</option>
                                <option value="Nave 5" <?php if($row['nave'] == 'Nave 5') echo 'selected'; ?>>Nave 5</option>
                                <option value="Nave 6" <?php if($row['nave'] == 'Nave 6') echo 'selected'; ?>>Nave 6</option>
                                <option value="Nave 7" <?php if($row['nave'] == 'Nave 7') echo 'selected'; ?>>Nave 7</option>
                                <option value="Nave 7A" <?php if($row['nave'] == 'Nave 7A') echo 'selected'; ?>>Nave 7A</option>
                                <option value="Nave 8" <?php if($row['nave'] == 'Nave 8') echo 'selected'; ?>>Nave 8</option>
                                <option value="Nave 9" <?php if($row['nave'] == 'Nave 9') echo 'selected'; ?>>Nave 9</option>
                                <option value="Nave 14" <?php if($row['nave'] == 'Nave 14') echo 'selected'; ?>>Nave 14</option>
                            </select>
                        </td>
                        <td><input type="text" id="idSupervisor" class="form-control" value="<?php echo htmlspecialchars($row['supervisor']); ?>" readonly></td>
                        <td>
                            <select id="idUnidad" class="form-control" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="Unidad 1" <?php if($row['unidad'] == 'Unidad 1') echo 'selected'; ?>>Unidad 1</option>
                                <option value="Unidad 2" <?php if($row['unidad'] == 'Unidad 2') echo 'selected'; ?>>Unidad 2</option>
                                <option value="Unidad 3" <?php if($row['unidad'] == 'Unidad 3') echo 'selected'; ?>>Unidad 3</option>
                                <option value="Unidad 4" <?php if($row['unidad'] == 'Unidad 4') echo 'selected'; ?>>Unidad 4</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFecha" class="form-control" value="<?php echo htmlspecialchars($row['fecha']); ?>" readonly></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h6 class="d-flex justify-content-center align-items-center mb-4"> OK=Conforme  NOK=No Conforme   NA=No Aplica</h6>
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
                        Proceso
                        </td>
                    </tr>
                    <tr data-id="1">
                        <td>1</td>
                        <td class="text-justify">Se encuentra la documentación técnica en la línea de Proceso 
                        ( caratula, diagrama de flujo, hoja de proceso, norma de empaque, plan de control)</td>
                        <td class="text-justify">FIN <br>  04,05,06,09 <br> FIN 08</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observaciones']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['acciones']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasUno']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoUno'];
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
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusUno'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusUno'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusUno'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila1.1" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaUno']); ?>" readonly></td>
                    </tr>
                    <tr data-id="2">
                        <td>2</td>
                        <td class="text-justify">Los parámetros se encuentran de acuerdo a la hoja de proceso (deben a su vez coincidir con los anotados en el formato "hoja de control de parámetros") </td>
                        <td class="text-justify">FIN 30</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDos']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDos']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDos']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDos'];
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
                            <select id="idResultado1.2" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDos'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDos'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDos'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila1.2" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDos']); ?>" readonly></td>
                    </tr>

              
                    <tr data-id="3">
                        <td>3</td>
                        <td class="text-justify">Se llevo a cabo la liberación del proceso y de primera pieza de manera correcta y validada por líder de celda</td>
                        <td class="text-justify">FPR 23,24</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesTres']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesTres']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasTres']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoTres'];
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
                            <select id="idResultado2.1" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusTres'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusTres'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusTres'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila2.1" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaTres']); ?>" readonly></td>
                    </tr>

                    <tr data-id="4">
                        <td>4</td>
                        <td class="text-justify">Se identifican correctamente los materiales (producto en proceso y  producto no conforme)</td>
                        <td class="text-justify">FAC 11,12</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCuatro']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCuatro']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCuatro']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoCuatro'];
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
                            <select id="idResultado3.1" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusCuatro'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusCuatro'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusCuatro'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila3.1" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaCuatro']); ?>" readonly></td>
                    </tr>

                    <tr data-id="5">
                        <td>5</td>
                        <td class="text-justify">Se tiene delimitada el área de acuerdo al Lay Out y  el Lay Out esta actualizado </td>
                        <td class="text-justify">FIN 44</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCinco']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCinco']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCinco']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoCinco'];
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
                            <select id="idResultado4.1" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusCinco'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusCinco'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusCinco'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila4.1" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaCinco']); ?>" readonly></td>
                    </tr>

                
                    <tr data-id="6">
                        <td>6</td>
                        <td class="text-justify">Los herramentales e indicadores (manómetros,timer,display,termómetros, etc.)de la línea están identificados, en buenas condiciones, verificados y son funcionales</td>
                        <td class="text-justify">FIN 34  <br>  FAC 43</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesSeis']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesSeis']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasSeis']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoSeis'];
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
                            <select id="idResultado5.1" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusSeis'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusSeis'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusSeis'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila5.1" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaSeis']); ?>" readonly></td>
                    </tr>
                    <tr data-id="7">
                        <td>7</td>
                        <td class="text-justify">Existen ayudas visuales de defectos de la pieza (catalogo de no conformidades)</td>
                        <td class="text-justify">FPR 14</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesSiete']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesSiete']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasSiete']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoSiete'];
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
                            <select id="idResultado6.1" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusSiete'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusSiete'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusSiete'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.1" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaSiete']); ?>" readonly></td>
                    </tr>
                    <tr data-id="8">
                        <td>8</td>
                        <td class="text-justify">El área auditada esta limpia y ordenada (se cuenta con un plan de limpieza y esta documentado)</td>
                        <td class="text-justify">FSH 32</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesOcho']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesOcho']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasOcho']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoOcho'];
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
                            <select id="idResultado6.2" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusOcho'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusOcho'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusOcho'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.2" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaOcho']); ?>" readonly></td>
                    </tr>
                    <tr data-id="9">
                        <td>9</td>
                        <td class="text-justify">Se encuentra el plan de mantenimiento preventivo y se realiza de acuerdo a lo programado </td>
                        <td class="text-justify">FMT 03</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesNueve']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesNueve']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasNueve']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoNueve'];
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
                            <select id="idResultado6.3" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusNueve'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusNueve'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusNueve'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.3" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaNueve']); ?>" readonly></td>
                    </tr>
                    <tr data-id="10">
                        <td>10</td>
                        <td class="text-justify">Se encuentra la ultima auditoria de capas y cuenta con sus acciones correctivas</td>
                        <td class="text-justify">FAC 25</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDiez']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDiez']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDiez']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDiez'];
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
                            <select id="idResultado6.4" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDiez'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDiez'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDiez'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.4" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDiez']); ?>" readonly></td>
                    </tr>

                    <tr>
                        <td colspan="9" class="text-start small fw-bold text-justify">
                        Empleados
                        </td>
                    </tr>



                    <tr data-id="11">
                        <td>11</td>
                        <td class="text-justify">Los  operadores realizan la operación como lo indica su HOJA DE PROCESO</td>
                        <td class="text-justify">FIN 06</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesOnce']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesOnce']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasOnce']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoOnce'];
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
                            <select id="idResultado6.5" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusOnce'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusOnce'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusOnce'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.5" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaOnce']); ?>" readonly></td>
                    </tr>
                    <tr data-id="12">
                        <td>12</td>
                        <td class="text-justify">Los operadores están informados sobre las reclamaciones y saben como manejar las piezas NOK</td>
                        <td class="text-justify">FAC 52</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDoce']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDoce']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDoce']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDoce'];
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
                            <select id="idResultado6.6" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDoce'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDoce'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDoce'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.6" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDoce']); ?>" readonly></td>
                    </tr>
                    <tr data-id="13">
                        <td>13</td>
                        <td class="text-justify">Los operadores conocen el plan de reacción en caso de falla conforme lo indicado el PLAN DE CONTROL</td>
                        <td class="text-justify">FIN 08</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesTrece']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesTrece']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasTrece']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoTrece'];
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
                            <select id="idResultado6.7" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusTrece'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusTrece'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusTrece'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.7" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaTrece']); ?>" readonly></td>
                    </tr>
                    <tr data-id="14">
                        <td>14</td>
                        <td class="text-justify">El operador revisa sus piezas visualmente conforme a lo indicado en el PLAN DE CONTROL</td>
                        <td class="text-justify">FIN 08</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesCatorce']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesCatorce']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasCatorce']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoCatorce'];
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
                            <select id="idResultado6.8" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusCatorce'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusCatorce'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusCatorce'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.8" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaCatorce']); ?>" readonly></td>
                    </tr>
                    <tr data-id="15">
                        <td>15</td>
                        <td class="text-justify">Los empleados cuentan con su EPP completo contra la matriz de EPP</td>
                        <td class="text-justify">FSH22</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesQuince']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesQuince']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasQuince']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoQuince'];
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
                            <select id="idResultado6.9" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusQuince'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusQuince'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusQuince'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.9" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaQuince']); ?>" readonly></td>
                    </tr>
                    <tr data-id="16">
                        <td>16</td>
                        <td class="text-justify">Esta actualizada la matriz de habilidades</td>
                        <td class="text-justify">FAD 14</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDieciseis']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDieciseis']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDieciseis']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDieciseis'];
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
                            <select id="idResultado6.10" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDieciseis'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDieciseis'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDieciseis'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.10" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDieciseis']); ?>" readonly></td>
                    </tr>


                    <tr>
                        <td colspan="9" class="text-start small fw-bold text-justify">
                        Características a evaluar en CHECKING FIXTURE & PLANILLA
                        </td>
                    </tr>



                    <tr data-id="17">
                        <td>17</td>
                        <td class="text-justify">El dispositivo cuenta con todos sus componentes, se encuentra limpio y en buen estado</td>
                        <td class="text-justify">FAC 93</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDiecisiete']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDiecisiete']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDiecisiete']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDiecisiete'];
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
                            <select id="idResultado6.11" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDiecisiete'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDiecisiete'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDiecisiete'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.11" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDiecisiete']); ?>" readonly></td>
                    </tr>
                    <tr data-id="18">
                        <td>18</td>
                        <td class="text-justify">El dispositivo esta verificado y cuenta con el nivel de ingeniería correspondiente</td>
                        <td class="text-justify">FAC 93,  <br> FIN 04</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDieciocho']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDieciocho']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDieciocho']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDieciocho'];
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
                            <select id="idResultado6.12" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDieciocho'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDieciocho'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDieciocho'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.12" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDieciocho']); ?>" readonly></td>
                    </tr>
                    <tr data-id="19">
                        <td>19</td>
                        <td class="text-justify">El dispositivo cuenta con el instructivo de uso del mismo</td>
                        <td class="text-justify">FAC 101</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesDiecinueve']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesDiecinueve']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasDiecinueve']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoDiecinueve'];
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
                            <select id="idResultado6.13" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusDiecinueve'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusDiecinueve'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusDiecinueve'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.13" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaDiecinueve']); ?>" readonly></td>
                    </tr>

                    <tr>
                        <td colspan="9" class="text-start small fw-bold text-justify">
                        Materia prima
                        </td>
                    </tr>


                    <tr data-id="20">
                        <td>20</td>
                        <td class="text-justify">Esta identificada la materia prima correctamente  (etiqueta de proveedor)</td>
                        <td class="text-justify">VISUAL</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesVeinte']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesVeinte']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasVeinte']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoVeinte'];
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
                            <select id="idResultado6.14" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusVeinte'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusVeinte'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusVeinte'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.14" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaVeinte']); ?>" readonly></td>
                    </tr>


                    <tr data-id="21">
                        <td>21</td>
                        <td class="text-justify">Se han anotado las materias primas en el control de carga de materias primas </td>
                        <td class="text-justify">FPR 02</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesVeintiuno']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesVeintiuno']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasVeintiuno']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoVeintiuno'];
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
                            <select id="idResultado6.15" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusVeintiuno'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusVeintiuno'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusVeintiuno'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.15" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaVeintiuno']); ?>" readonly></td>
                    </tr>
                    
                    
                    
                    <tr data-id="22">
                        <td>22</td>
                        <td class="text-justify">La identificación del producto final para envío a cliente es legible.  (Verificar las impresiones de etiqueta individual y SAP)</td>
                        <td class="text-justify">VISUAL</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesVeintidos']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesVeintidos']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasVeintidos']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoVeintidos'];
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
                            <select id="idResultado6.15" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusVeintidos'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusVeintidos'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusVeintidos'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.15" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaVeintidos']); ?>" readonly></td>
                    </tr>
                    <tr data-id="23">
                        <td>23</td>
                        <td class="text-justify">Los materiales son  colocados como lo indica la norma empaque liberada</td>
                        <td class="text-justify">FIN 09</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesVeintitres']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesVeintitres']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasVeintitres']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoVeintitres'];
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
                            <select id="idResultado6.15" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusVeintitres'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusVeintitres'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusVeintitres'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.15" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaVeintitres']); ?>" readonly></td>
                    </tr>
                    <tr data-id="24">
                        <td>24</td>
                        <td class="text-justify">Los contenedores se encuentran en buen estado (limpios, secos y sin roturas) y están libre de etiquetas obsoletas como lo indica la norma de empaque</td>
                        <td class="text-justify">FIN 09</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesVeinticuatro']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesVeinticuatro']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasVeinticuatro']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoVeinticuatro'];
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
                            <select id="idResultado6.15" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusVeinticuatro'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusVeinticuatro'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusVeinticuatro'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.15" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaVeinticuatro']); ?>" readonly></td>
                    </tr>

                    <tr>
                        <td colspan="9" class="text-start small fw-bold text-justify">
                        Gestión de reclamaciones
                        </td>
                    </tr>
                    <tr data-id="25">
                        <td>25</td>
                        <td class="text-justify">Se encuentra la ultima alerta de calidad disponible en producción (solo si aplica)</td>
                        <td class="text-justify">FAC 52</td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['observacionesVeinticinco']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['accionesVeinticinco']); ?></p></td>
                        <td><p class="form-control-static"><?php echo htmlspecialchars($row['idProblemasVeinticinco']); ?></p></td>
                        <td>
                            <?php
                            $rutaArchivo = $row['ruta_archivoVeinticinco'];
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
                            <select id="idResultado6.15" class="form-control resultado" disabled>
                                <option value="">Selecciona una opción</option>
                                <option value="OK" <?php if($row['estatusVeinticinco'] == 'OK') echo 'selected'; ?>>OK</option>
                                <option value="Pendiente" <?php if($row['estatusVeinticinco'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                <option value="NOK" <?php if($row['estatusVeinticinco'] == 'NOK') echo 'selected'; ?>>NOK</option>
                            </select>
                        </td>
                        <td><input type="date" id="idFechaFila6.15" class="form-control fecha" value="<?php echo htmlspecialchars($row['fecha_filaVeinticinco']); ?>" readonly></td>
                    </tr>

                   <!-- Signatures section -->
                    <tr>
                        <td colspan="9">
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
                                            <td><input type="text" id="idNombreOperador" class="form-control" value="<?php echo htmlspecialchars($row['idNombreOperador'] ?? ''); ?>" readonly></td>
                                            <td><input type="text" id="idNombreSupervisor" class="form-control" value="<?php echo htmlspecialchars($row['idNombreSupervisor'] ?? ''); ?>" readonly></td>
                                            <td><input type="text" id="idNombreAuditor2" class="form-control" value="<?php echo htmlspecialchars($row['idNombreAuditor2'] ?? ''); ?>" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<br><br><br><br><br><br>
  
    <script src="../js/verRegistroProceso.js"></script>

</body>
</html>
<?php include('pie.php'); ?>

<?php
// Función auxiliar para convertir números a texto (hasta 25)
function num2text($num) {
    $numeros = [
        1 => 'Uno', 2 => 'Dos', 3 => 'Tres', 4 => 'Cuatro', 5 => 'Cinco',
        6 => 'Seis', 7 => 'Siete', 8 => 'Ocho', 9 => 'Nueve', 10 => 'Diez',
        11 => 'Once', 12 => 'Doce', 13 => 'Trece', 14 => 'Catorce', 15 => 'Quince',
        16 => 'Dieciseis', 17 => 'Diecisiete', 18 => 'Dieciocho', 19 => 'Diecinueve',
        20 => 'Veinte', 21 => 'Veintiuno', 22 => 'Veintidos', 23 => 'Veintitres',
        24 => 'Veinticuatro', 25 => 'Veinticinco'
    ];
    return $numeros[$num] ?? '';
}
?>