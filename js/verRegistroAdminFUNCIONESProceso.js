// verRegistroPorProcesoFUNCIONES.js

// Función para bloquear todos los campos de las filas (excluyendo modales)
function bloquearCamposFilas() {
    document.querySelectorAll('tr input, tr textarea, tr select').forEach(element => {
        element.disabled = true;
    });
    document.querySelectorAll('.btn-editar').forEach(button => {
        button.style.display = 'inline-block';
    });
    document.querySelectorAll('.btn-actualizar').forEach(button => {
        button.style.display = 'none';
    });
}

// Función para mantener modales desbloqueados
function desbloquearCamposModales() {
    document.querySelectorAll('.modal input, .modal textarea, .modal select').forEach(element => {
        element.disabled = false;
    });
}

// Array con todos los identificadores de las filas
const filas = [
    'Uno', 'UnoDos', 'UnoTres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve', 'Diez',
    'Once', 'Doce', 'Trece', 'Catorce', 'Quince', 'Dieciseis', 'Diecisiete', 'Dieciocho', 
    'Diecinueve', 'Veinte', 'Veintiuno', 'Veintidos', 'Veintitres', 'Veinticuatro', 'Veinticinco'
];

// Mapeo de filas a archivos PHP
const filaToPhpMap = {
    'uno': '../Controlador/actualizarPorProceso/actualizar_fila1.php',
    'unodos': '../Controlador/actualizarPorProceso/actualizar_fila2.php',
    'unotres': '../Controlador/actualizarPorProceso/actualizar_fila3.php',
    'cuatro': '../Controlador/actualizarPorProceso/actualizar_fila4.php',
    'cinco': '../Controlador/actualizarPorProceso/actualizar_fila5.php',
    'seis': '../Controlador/actualizarPorProceso/actualizar_fila6.php',
    'siete': '../Controlador/actualizarPorProceso/actualizar_fila7.php',
    'ocho': '../Controlador/actualizarPorProceso/actualizar_fila8.php',
    'nueve': '../Controlador/actualizarPorProceso/actualizar_fila9.php',
    'diez': '../Controlador/actualizarPorProceso/actualizar_fila10.php',
    'once': '../Controlador/actualizarPorProceso/actualizar_fila11.php',
    'doce': '../Controlador/actualizarPorProceso/actualizar_fila12.php',
    'trece': '../Controlador/actualizarPorProceso/actualizar_fila13.php',
    'catorce': '../Controlador/actualizarPorProceso/actualizar_fila14.php',
    'quince': '../Controlador/actualizarPorProceso/actualizar_fila15.php',
    'dieciseis': '../Controlador/actualizarPorProceso/actualizar_fila16.php',
    'diecisiete': '../Controlador/actualizarPorProceso/actualizar_fila17.php',
    'dieciocho': '../Controlador/actualizarPorProceso/actualizar_fila18.php',
    'diecinueve': '../Controlador/actualizarPorProceso/actualizar_fila19.php',
    'veinte': '../Controlador/actualizarPorProceso/actualizar_fila20.php',
    'veintiuno': '../Controlador/actualizarPorProceso/actualizar_fila21.php',
    'veintidos': '../Controlador/actualizarPorProceso/actualizar_fila22.php',
    'veintitres': '../Controlador/actualizarPorProceso/actualizar_fila23.php',
    'veinticuatro': '../Controlador/actualizarPorProceso/actualizar_fila24.php',
    'veinticinco': '../Controlador/actualizarPorProceso/actualizar_fila25.php'
};

// Mapeo de nombres de filas a IDs de resultado
const filaToIdMap = {
    'Uno': '1.1', 'UnoDos': '1.2', 'UnoTres': '1.3',
    'Cuatro': '2.1', 'Cinco': '2.2', 'Seis': '2.3', 'Siete': '2.4', 'Ocho': '2.5', 'Nueve': '2.6',
    'Diez': '3_1', 'Once': '4_1', 'Doce': '4_2', 'Trece': '4_3',
    'Catorce': '5_1', 'Quince': '5_2', 'Dieciseis': '5_3', 'Diecisiete': '5_4', 
    'Dieciocho': '5_5', 'Diecinueve': '5_6', 'Veinte': '5_7', 'Veintiuno': '5_8', 
    'Veintidos': '6_1', 'Veintitres': '23', 'Veinticuatro': '24', 'Veinticinco': '25'
};

// Mapeo de nombres de filas a IDs de campos específicos
const filaToFieldMap = {
    'Uno': { observaciones: 'observaciones', acciones: 'acciones', problemas: 'idProblemasUno' },
    'UnoDos': { observaciones: 'observacionesUnoDos', acciones: 'accionesUnoDos', problemas: 'idProblemasUno' },
    'UnoTres': { observaciones: 'observacionesUnoTres', acciones: 'accionesUnoTres', problemas: 'idProblemasUnoTres' },
    'Cuatro': { observaciones: 'observacionesDosUno', acciones: 'accionesDosUno', problemas: 'idProblemasDosUno' },
    'Cinco': { observaciones: 'observacionesDosDos', acciones: 'accionesDosDos', problemas: 'idProblemasDosDos' },
    'Seis': { observaciones: 'observacionesDosTres', acciones: 'accionesDosTres', problemas: 'idProblemasDosTres' },
    'Siete': { observaciones: 'observacionesSiete', acciones: 'accionesSiete', problemas: 'idProblemasSiete' },
    'Ocho': { observaciones: 'observacionesOcho', acciones: 'accionesOcho', problemas: 'idProblemasOcho' },
    'Nueve': { observaciones: 'observacionesNueve', acciones: 'accionesNueve', problemas: 'idProblemasNueve' },
    'Diez': { observaciones: 'observacionesDiez', acciones: 'accionesDiez', problemas: 'idProblemasDiez' },
    'Once': { observaciones: 'observacionesOnce', acciones: 'accionesOnce', problemas: 'idProblemasOnce' },
    'Doce': { observaciones: 'observacionesDoce', acciones: 'accionesDoce', problemas: 'idProblemasDoce' },
    'Trece': { observaciones: 'observacionesTrece', acciones: 'accionesTrece', problemas: 'idProblemasTrece' },
    'Catorce': { observaciones: 'observacionesCatorce', acciones: 'accionesCatorce', problemas: 'idProblemasCatorce' },
    'Quince': { observaciones: 'observacionesQuince', acciones: 'accionesQuince', problemas: 'idProblemasQuince' },
    'Dieciseis': { observaciones: 'observacionesDieciseis', acciones: 'accionesDieciseis', problemas: 'idProblemasDieciseis' },
    'Diecisiete': { observaciones: 'observacionesDiecisiete', acciones: 'accionesDiecisiete', problemas: 'idProblemasDiecisiete' },
    'Dieciocho': { observaciones: 'observacionesDieciocho', acciones: 'accionesDieciocho', problemas: 'idProblemasDieciocho' },
    'Diecinueve': { observaciones: 'observacionesDiecinueve', acciones: 'accionesDiecinueve', problemas: 'idProblemasDiecinueve' },
    'Veinte': { observaciones: 'observacionesVeinte', acciones: 'accionesVeinte', problemas: 'idProblemasVeinte' },
    'Veintiuno': { observaciones: 'observacionesVeintiuno', acciones: 'accionesVeintiuno', problemas: 'idProblemasVeintiuno' },
    'Veintidos': { observaciones: 'idObservaciones6.1', acciones: 'idAcciones6.1', problemas: 'idProblemas6.1' },
    'Veintitres': { observaciones: 'idObservaciones23', acciones: 'idAcciones23', problemas: 'idProblemas23' },
    'Veinticuatro': { observaciones: 'idObservaciones24', acciones: 'idAcciones24', problemas: 'idProblemas24' },
    'Veinticinco': { observaciones: 'idObservaciones25', acciones: 'idAcciones25', problemas: 'idProblemas25' }
};


const filaToEmailButtonMap = {
    'Uno': 'btnEnviarcorreoUno', // Adjust if needed
    'UnoDos': 'btnEnviarcorreoDos', // Matches HTML
    'UnoTres': 'btnEnviarcorreoTres', // Matches HTML
    'Cuatro': 'btnEnviarcorreoCuatro',
    'Cinco': 'btnEnviarcorreoCinco',
    'Seis': 'btnEnviarcorreoSeis',
    'Siete': 'btnEnviarcorreoSiete',
    'Ocho': 'btnEnviarcorreoOcho',
    'Nueve': 'btnEnviarcorreoNueve',
    'Diez': 'btnEnviarcorreoDiez',
    'Once': 'btnEnviarcorreoOnce',
    'Doce': 'btnEnviarcorreoDoce',
    'Trece': 'btnEnviarcorreoTrece',
    'Catorce': 'btnEnviarcorreoCatorce',
    'Quince': 'btnEnviarcorreoQuince',
    'Dieciseis': 'btnEnviarcorreoDieciseis',
    'Diecisiete': 'btnEnviarcorreoDiecisiete',
    'Dieciocho': 'btnEnviarcorreoDieciocho',
    'Diecinueve': 'btnEnviarcorreoDiecinueve',
    'Veinte': 'btnEnviarcorreoVeinte',
    'Veintiuno': 'btnEnviarcorreoVeintiuno',
    'Veintidos': 'btnEnviarcorreoVeintidos',
    'Veintitres': 'btnEnviarcorreoVeintitres',
    'Veinticuatro': 'btnEnviarcorreoVeinticuatro',
    'Veinticinco': 'btnEnviarcorreoVeinticinco'
};

// Inicializar botones de edición, actualización y correo
function inicializarBotones() {
    filas.forEach(fila => {
        const filaId = filaToIdMap[fila];
        const fieldIds = filaToFieldMap[fila];
        const selectEstatus = document.getElementById(`idResultado${filaId}`);
        const btnEditar = document.getElementById(`btnEditar${fila}`);
        const btnActualizar = document.getElementById(`btnActualizar${fila}`);
        const observaciones = document.getElementById(fieldIds.observaciones);
        const acciones = document.getElementById(fieldIds.acciones);
        const problemas = document.getElementById(fieldIds.problemas);
        const fecha = document.getElementById(`idFechaFila${filaId}`);
        const btnSeguimiento = document.getElementById(`btnSeguimiento${fila}`);
        const btnVerSeguimiento = document.getElementById(`btnVerSeguimiento${fila}`);
        const btnEnviarCorreo = document.getElementById(filaToEmailButtonMap[fila]); // Use mapping

        if (!selectEstatus || !btnEditar || !btnActualizar) {
            console.warn(`No se encontraron elementos básicos para la fila ${fila} (${filaId})`);
            return;
        }

        if (!observaciones || !acciones || !problemas || !fecha) {
            console.warn(`Faltan campos específicos para la fila ${fila} (${filaId})`);
        }

        btnEditar.classList.add('btn-editar');
        btnActualizar.classList.add('btn-actualizar');

        btnEditar.addEventListener('click', () => {
            if (observaciones) observaciones.disabled = false;
            if (acciones) acciones.disabled = false;
            if (problemas) problemas.disabled = false;
            if (selectEstatus) selectEstatus.disabled = false;
            if (fecha) fecha.disabled = false;
            btnEditar.style.display = 'none';
            btnActualizar.style.display = 'inline-block';
        });

        btnActualizar.addEventListener('click', () => {
            if (observaciones) observaciones.disabled = true;
            if (acciones) acciones.disabled = true;
            if (problemas) problemas.disabled = true;
            if (selectEstatus) selectEstatus.disabled = true;
            if (fecha) fecha.disabled = true;
            btnEditar.style.display = 'inline-block';
            btnActualizar.style.display = 'none';
            enviarActualizacion(fila);
        });

        if (btnSeguimiento && btnVerSeguimiento) {
            updateSeguimientoButtons(selectEstatus, btnSeguimiento, btnVerSeguimiento, btnEnviarCorreo);
            selectEstatus.addEventListener('change', () => {
                updateSeguimientoButtons(selectEstatus, btnSeguimiento, btnVerSeguimiento, btnEnviarCorreo);
            });
        }
        // Configurar el botón de enviar correo
        if (btnEnviarCorreo) {
            btnEnviarCorreo.addEventListener('click', () => {
                document.getElementById('filaSeleccionada').value = fila;
                cargarEmpleados();
                const modal = new bootstrap.Modal(document.getElementById('seleccionarCorreoModal'));
                modal.show();
            });
        } else {
            console.warn(`Botón de correo no encontrado para la fila ${fila}: btnEnviarcorreo${fila}`);
        }
    });
}

// Actualizar visibilidad de botones de seguimiento
function updateSeguimientoButtons(selectEstatus, btnSeguimiento, btnVerSeguimiento, btnEnviarCorreo) {
    if (selectEstatus.value === 'NOK') {
        btnSeguimiento.style.display = 'inline-block';
        btnVerSeguimiento.style.display = 'inline-block';
        if (btnEnviarCorreo) btnEnviarCorreo.style.display = 'inline-block';
    } else {
        btnSeguimiento.style.display = 'none';
        btnVerSeguimiento.style.display = 'inline-block';
        if (btnEnviarCorreo) btnEnviarCorreo.style.display = 'none';
    }
}

function cargarEmpleados() {
    const selectCorreo = document.getElementById('correoDestinatario');
    selectCorreo.innerHTML = '<option value="">Selecciona un empleado</option>';

    // Hacer una solicitud al servidor para obtener la lista de empleados
    fetch('../Controlador/obtener_empleados.php') // Ajusta la URL según tu estructura
        .then(response => response.json())
        .then(data => {
            if (data.success && data.empleados) {
                data.empleados.forEach(empleado => {
                    const option = document.createElement('option');
                    option.value = empleado.correo;
                    option.textContent = `${empleado.nombre} (${empleado.correo})`;
                    selectCorreo.appendChild(option);
                });
            } else {
                alert('No se pudieron cargar los empleados.');
            }
        })
        .catch(error => {
            console.error('Error al cargar empleados:', error);
            alert('Ocurrió un error al cargar los empleados.');
        });
}





// Configurar seguimiento (modals)
function configurarSeguimiento() {
    filas.forEach(fila => {
        const btnSeguimiento = document.getElementById(`btnSeguimiento${fila}`);
        const btnVerSeguimiento = document.getElementById(`btnVerSeguimiento${fila}`);

        if (btnSeguimiento) {
            btnSeguimiento.addEventListener('click', function() {
                limpiarCamposModal(fila);
                const modal = new bootstrap.Modal(document.getElementById(`seguimientoModal${fila}`));
                modal.show();
            });
        }

        if (btnVerSeguimiento) {
            btnVerSeguimiento.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById(`verSeguimientoModal${fila}`));
                cargarSeguimientos(fila, document.getElementById('numeroDocumento').textContent);
                modal.show();
            });
        }

        const btnGuardar = document.getElementById(`guardarSeguimiento${fila}`);
        if (btnGuardar) {
            btnGuardar.addEventListener('click', function() {
                const accion = document.getElementById(`accionModal${fila}`).value;
                const observacion = document.getElementById(`observacionModal${fila}`).value;
                const fecha = document.getElementById(`fechaModal${fila}`).value;
                const archivo = document.getElementById(`archivoModal${fila}`).files[0];

                if (!accion || !observacion || !fecha) {
                    alert('Por favor, completa todos los campos obligatorios (Acción, Observación, Fecha).');
                    return;
                }

                const formData = new FormData();
                formData.append('id_auditoria', document.getElementById('numeroDocumento').textContent);
                formData.append('fila', fila);
                formData.append('accion', accion);
                formData.append('observacion', observacion);
                formData.append('fecha', fecha);
                if (archivo) {
                    formData.append('archivo', archivo);
                }

                fetch('../Controlador/guardar_seguimientoProcesos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById(`seguimientoModal${fila}`));
                        modal.hide();
                        limpiarCamposModal(fila);
                    } else {
                        alert('Error al guardar el seguimiento: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error al guardar seguimiento:', error);
                    alert('Ocurrió un error al guardar el seguimiento.');
                });
            });
        }
    });
}

// Enviar actualización al servidor
function enviarActualizacion(fila) {
    const filaId = filaToIdMap[fila];
    const phpFile = filaToPhpMap[fila.toLowerCase()];
    const fieldIds = filaToFieldMap[fila];
    const observaciones = document.getElementById(fieldIds.observaciones);
    const acciones = document.getElementById(fieldIds.acciones);
    const problemas = document.getElementById(fieldIds.problemas);
    const estatus = document.getElementById(`idResultado${filaId}`);
    const fecha = document.getElementById(`idFechaFila${filaId}`);

    const data = {
        id_auditoria: new URLSearchParams(window.location.search).get('id'),
        observaciones: observaciones ? observaciones.value : '',
        acciones: acciones ? acciones.value : '',
        problemas: problemas ? problemas.value : '',
        estatus: estatus ? estatus.value : '',
        fecha: fecha ? fecha.value : ''
    };

    fetch(phpFile, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Estado de la respuesta:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Respuesta cruda del servidor:', text);
        try {
            const result = JSON.parse(text);
            if (result.success) {
                alert(`Fila ${fila} actualizada con éxito`);
            } else {
                alert(`Error al actualizar la fila ${fila}: ${result.message || result.error || 'Error desconocido'}`);
            }
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            alert(`Error al actualizar la fila ${fila}: La respuesta no es JSON válido - ${text}`);
        }
    })
    .catch(error => console.error(`Error al actualizar la fila ${fila}:`, error));
}

// Limpiar campos del modal de seguimiento
function limpiarCamposModal(fila) {
    const accion = document.getElementById(`accionModal${fila}`);
    const observacion = document.getElementById(`observacionModal${fila}`);
    const fecha = document.getElementById(`fechaModal${fila}`);
    const archivo = document.getElementById(`archivoModal${fila}`);

    if (accion) accion.value = '';
    if (observacion) observacion.value = '';
    if (fecha) fecha.value = '';
    if (archivo) archivo.value = '';
}

// Cargar seguimientos en el modal
function cargarSeguimientos(fila, idAuditoria) {
    fetch(`../Controlador/obtener_seguimientosPorProcesos.php?id_auditoria=${idAuditoria}&fila=${fila}`)
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById(`tablaSeguimientos${fila}`);
        tbody.innerHTML = '';

        if (data.success && data.seguimientos.length > 0) {
            data.seguimientos.forEach(seg => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${seg.observaciones || '-'}</td>
                    <td>${seg.acciones || '-'}</td>
                    <td>${seg.fecha_seguimiento || '-'}</td>
                    <td>${seg.nombre_archivo ? `<a href="${seg.ruta_archivo}" target="_blank">${seg.nombre_archivo}</a>` : '-'}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4">No hay seguimientos registrados.</td></tr>';
        }
    })
    .catch(error => {
        console.error('Error al cargar seguimientos:', error);
        const tbody = document.getElementById(`tablaSeguimientos${fila}`);
        tbody.innerHTML = '<tr><td colspan="4">Error al cargar los datos.</td></tr>';
    });
}

// Actualizar colores de los select según su valor
function updateSelectColors() {
    const selects = document.querySelectorAll('.resultado');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            updateColor(this);
        });
        updateColor(select);
    });
}

function updateColor(select) {
    switch (select.value) {
        case 'OK':
            select.style.backgroundColor = '#28a745';
            select.style.color = 'white';
            break;
        case 'NOK':
            select.style.backgroundColor = '#dc3545';
            select.style.color = 'white';
            break;
        case 'N/A':
            select.style.backgroundColor = '#ffc107';
            select.style.color = 'black';
            break;
        default:
            select.style.backgroundColor = 'white';
            select.style.color = 'black';
    }
}

// Función para cerrar la auditoría
// Función para cerrar la auditoría
function cerrarAuditoria() {
    const idAuditoria = new URLSearchParams(window.location.search).get('id');
    if (!idAuditoria) {
        alert('No se encontró el ID de la auditoría en la URL.');
        return;
    }

    // Verificar si algún estatus está en "NOK"
    let hasNOK = false;
    let nokRows = [];

    filas.forEach(fila => {
        const filaId = filaToIdMap[fila];
        const selectEstatus = document.getElementById(`idResultado${filaId}`);
        if (selectEstatus && selectEstatus.value === 'NOK') {
            hasNOK = true;
            nokRows.push(filaId.replace('_', '.')); // Convertir formato para mostrar, ej. 3_1 a 3.1
        }
    });

    if (hasNOK) {
        alert(`No se puede cerrar la auditoría porque las siguientes filas tienen estatus NOK: ${nokRows.join(', ')}. Resuelva estos problemas antes de cerrar la auditoría.`);
        return;
    }

    // Verificar los campos de nombres
    const nombreAuditado = document.getElementById('idNombreOperador')?.value.trim() || '';
    const nombreSupervisor = document.getElementById('idNombreSupervisor')?.value.trim() || '';
    const nombreAuditor = document.getElementById('idNombreAuditor2')?.value.trim() || '';

    if (!nombreAuditado || !nombreSupervisor || !nombreAuditor) {
        alert('Por favor, completa todos los campos de nombres antes de cerrar la auditoría.');
        return;
    }

    // Proceder a cerrar la auditoría si no hay NOK
    if (confirm('¿Estás seguro de que deseas cerrar esta auditoría? Esta acción no se puede deshacer.')) {
        fetch('../Controlador/cerrar_auditoria_procesoCompletamente.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id_auditoria: idAuditoria,
                nombreAuditado: nombreAuditado,
                nombreSupervisor: nombreSupervisor,
                nombreAuditor: nombreAuditor
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Auditoría cerrada con éxito.');
                document.getElementById('cerrarAuditoriaProcesos').disabled = true;
                document.getElementById('cerrarAuditoriaProcesos').textContent = 'AUDITORÍA CERRADA';
                bloquearCamposFilas();
            } else {
                alert('Error al cerrar la auditoría: ' + (data.message || 'Respuesta inesperada del servidor'));
            }
        })
        .catch(error => {
            console.error('Error al cerrar la auditoría:', error);
            alert('Ocurrió un error al intentar cerrar la auditoría.');
        });
    }
}

// Inicializar todo al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    bloquearCamposFilas();
    desbloquearCamposModales();
    inicializarBotones();
    configurarSeguimiento();
    updateSelectColors();
    document.getElementById('cerrarAuditoriaProcesos').addEventListener('click', cerrarAuditoria);

    // Configurar el botón de enviar correo en el modal
    document.getElementById('enviarCorreoConfirmar').addEventListener('click', function() {
        const correoDestinatario = document.getElementById('correoDestinatario').value;
        const fila = document.getElementById('filaSeleccionada').value;

        if (!correoDestinatario) {
            alert('Por favor, selecciona un destinatario.');
            return;
        }

        const filaId = filaToIdMap[fila];
        const fieldIds = filaToFieldMap[fila];
        const observaciones = document.getElementById(fieldIds.observaciones);
        const acciones = document.getElementById(fieldIds.acciones);
        const problemas = document.getElementById(fieldIds.problemas);
        const estatus = document.getElementById(`idResultado${filaId}`);
        const fecha = document.getElementById(`idFechaFila${filaId}`);

        const datosCorreo = {
            id_auditoria: new URLSearchParams(window.location.search).get('id'),
            fila: fila,
            correo: correoDestinatario,
            observaciones: observaciones ? observaciones.value : '',
            acciones: acciones ? acciones.value : '',
            problemas: problemas ? problemas.value : '',
            estatus: estatus ? estatus.value : '',
            fecha: fecha ? fecha.value : ''
        };

        fetch('../Controlador/enviar_correo_procesos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosCorreo)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Correo enviado con éxito.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('seleccionarCorreoModal'));
                modal.hide();
            } else {
                alert('Error al enviar el correo: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error al enviar el correo:', error);
            alert('Ocurrió un error al enviar el correo.');
        });
    });
});