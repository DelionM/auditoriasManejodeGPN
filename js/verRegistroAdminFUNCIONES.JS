// verRegistroAdminFUNCIONES.js

// Función para bloquear todos los campos y botones
function bloquearFormulario() {
    document.querySelectorAll('input, textarea, select').forEach(element => {
        element.disabled = true;
    });
    document.querySelectorAll('.btn').forEach(button => {
        if (!button.id.includes('Seguimiento') && !button.id.includes('VerSeguimiento')) {
            button.style.display = 'none';
        }
    });
}

// Función para cerrar la auditoría
function cerrarAuditoria() {
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

    // Proceder a cerrar la auditoría si no hay estatus NOK
    if (confirm("¿Está seguro de que desea cerrar la auditoría? Esta acción no se puede deshacer.")) {
        const idAuditoria = document.getElementById('numeroDocumento').textContent;
        fetch('../Controlador/cerrar_auditoriaPorCompleto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_auditoria=' + encodeURIComponent(idAuditoria)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Auditoría cerrada exitosamente.');
                bloquearFormulario();
            } else {
                alert('Error al cerrar la auditoría: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al cerrar la auditoría.');
        });
    }
}
document.getElementById('cerrarAuditoria').addEventListener('click', cerrarAuditoria);

// Array con todos los identificadores de las filas
const filas = [
    'UnoUno', 'UnoDos', 'UnoTres',
    'DosUno', 'DosDos', 'DosTres', 'DosCuatro', 'DosCinco', 'DosSeis',
    'TresUno',
    'CuatroUno', 'CuatroDos', 'CuatroTres',
    'CincoUno', 'CincoDos', 'CincoTres', 'CincoCuatro', 'CincoCinco', 'CincoSeis', 'CincoSiete', 'CincoOcho',
    'SeisUno'
];

// Mapeo de filas a archivos PHP
const filaToPhpMap = {
    'unouno': './Controlador/actualizar_fila1_1.php',
    'unodos': './Controlador/actualizar_fila1_2.php',
    'unotres': './Controlador/actualizar_fila1_3.php',
    'dosuno': './Controlador/actualizar_fila2_1.php',
    'dosdos': './Controlador/actualizar_fila2_2.php',
    'dostres': './Controlador/actualizar_fila2_3.php',
    'doscuatro': './Controlador/actualizar_fila2_4.php',
    'doscinco': './Controlador/actualizar_fila2_5.php',
    'dosseis': './Controlador/actualizar_fila2_6.php',
    'tresuno': './Controlador/actualizar_fila3_1.php',
    'cuatrouno': './Controlador/actualizar_fila4_1.php',
    'cuatrodos': './Controlador/actualizar_fila4_2.php',
    'cuatrotres': './Controlador/actualizar_fila4_3.php',
    'cincouno': './Controlador/actualizar_fila5_1.php',
    'cincodos': './Controlador/actualizar_fila5_2.php',
    'cincotres': './Controlador/actualizar_fila5_3.php',
    'cincocuatro': './Controlador/actualizar_fila5_4.php',
    'cincocinco': './Controlador/actualizar_fila5_5.php',
    'cincoseis': './Controlador/actualizar_fila5_6.php',
    'cincosiete': './Controlador/actualizar_fila5_7.php',
    'cincoocho': './Controlador/actualizar_fila5_8.php',
    'seisuno': './Controlador/actualizar_fila6_1.php'
};

// Mapeo de nombres de filas a IDs
const filaToIdMap = {
    'UnoUno': '1.1', 'UnoDos': '1.2', 'UnoTres': '1.3',
    'DosUno': '2.1', 'DosDos': '2.2', 'DosTres': '2.3', 'DosCuatro': '2.4', 'DosCinco': '2.5', 'DosSeis': '2.6',
    'TresUno': '3_1',
    'CuatroUno': '4_1', 'CuatroDos': '4_2', 'CuatroTres': '4_3',
    'CincoUno': '5_1', 'CincoDos': '5_2', 'CincoTres': '5_3', 'CincoCuatro': '5_4', 'CincoCinco': '5_5', 'CincoSeis': '5_6', 'CincoSiete': '5_7', 'CincoOcho': '5_8',
    'SeisUno': '6_1'
};

function inicializarBotones() {
    filas.forEach(fila => {
        const filaId = filaToIdMap[fila];
        const selectEstatus = document.getElementById(`idResultado${filaId}`);
        const btnEditar = document.getElementById(`btnEditar${fila}`);
        const btnActualizar = document.getElementById(`btnActualizar${fila}`);
        const observaciones = document.getElementById(`observaciones${fila}`);
        const acciones = document.getElementById(`acciones${fila}`);
        const problemas = document.getElementById(`idProblemas${fila}`);
        const fecha = document.getElementById(`idFechaFila${filaId}`);
        const btnSeguimiento = document.getElementById(`btnSeguimiento${fila}`);
        const btnVerSeguimiento = document.getElementById(`btnVerSeguimiento${fila}`);
        const btnEnviarCorreo = document.getElementById(`btnEnviarcorreo${fila}`);

        if (selectEstatus && btnEditar && btnActualizar) {
            if (observaciones && !observaciones.id.includes('Modal')) observaciones.disabled = true;
            if (acciones && !acciones.id.includes('Modal')) acciones.disabled = true;
            if (problemas && !problemas.id.includes('Modal')) problemas.disabled = true;
            if (selectEstatus && !selectEstatus.id.includes('Modal')) selectEstatus.disabled = true;
            if (fecha && !fecha.id.includes('Modal')) fecha.disabled = true;

            btnEditar.addEventListener('click', () => {
                observaciones.disabled = false;
                acciones.disabled = false;
                problemas.disabled = false;
                selectEstatus.disabled = false;
                fecha.disabled = false;
                btnEditar.style.display = 'none';
                btnActualizar.style.display = 'inline-block';
            });

            btnActualizar.addEventListener('click', () => {
                observaciones.disabled = true;
                acciones.disabled = true;
                problemas.disabled = true;
                selectEstatus.disabled = true;
                fecha.disabled = true;
                btnEditar.style.display = 'inline-block';
                btnActualizar.style.display = 'none';
            });

            if (btnSeguimiento && btnVerSeguimiento && btnEnviarCorreo) {
                if (selectEstatus.value === 'NOK') {
                    btnSeguimiento.style.display = 'inline-block';
                    btnEnviarCorreo.style.display = 'inline-block';
                    btnVerSeguimiento.style.display = 'inline-block';
                } else {
                    btnSeguimiento.style.display = 'none';
                    btnEnviarCorreo.style.display = 'none';
                    btnVerSeguimiento.style.display = 'inline-block';
                }

                selectEstatus.addEventListener('change', () => {
                    if (selectEstatus.value === 'NOK') {
                        btnSeguimiento.style.display = 'inline-block';
                        btnEnviarCorreo.style.display = 'inline-block';
                        btnVerSeguimiento.style.display = 'inline-block';
                    } else {
                        btnSeguimiento.style.display = 'none';
                        btnEnviarCorreo.style.display = 'none';
                        btnVerSeguimiento.style.display = 'inline-block';
                    }
                });
            }
        } else {
            console.warn(`No se encontraron todos los elementos necesarios para la fila ${fila} (${filaId}). Verifica los IDs en el HTML.`);
        }
    });
}

// Configurar los eventos de enviar correo
function configurarEnvioCorreo() {
    filas.forEach(fila => {
        const btnEnviarCorreo = document.getElementById(`btnEnviarcorreo${fila}`);
        if (btnEnviarCorreo) {
            btnEnviarCorreo.addEventListener('click', () => {
                abrirModalCorreo(fila);
            });
        } else {
            console.warn(`No se encontró btnEnviarcorreo${fila} en el DOM. Verifica el HTML.`);
        }
    });

    // Configurar el botón de confirmar en el modal
    const btnConfirmar = document.getElementById('enviarCorreoConfirmar');
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', () => {
            const fila = document.getElementById('filaSeleccionada').value;
            const correoDestinatario = document.getElementById('correoDestinatario').value;
            if (!correoDestinatario) {
                alert('Por favor, selecciona un destinatario.');
                return;
            }
            enviarCorreo(fila, correoDestinatario);
        });
    }
}

// Abrir el modal y cargar empleados
function abrirModalCorreo(fila) {
    const modal = new bootstrap.Modal(document.getElementById('seleccionarCorreoModal'));
    const selectCorreo = document.getElementById('correoDestinatario');
    const filaSeleccionada = document.getElementById('filaSeleccionada');

    // Guardar la fila seleccionada
    filaSeleccionada.value = fila;

    // Limpiar el select y asegurarse de que esté habilitado
    selectCorreo.innerHTML = '<option value="" disabled selected>Cargando empleados...</option>';
    selectCorreo.disabled = true; // Bloquear mientras carga

    // Cargar empleados desde el servidor
    fetch('../Controlador/obtener_empleados.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.empleados.length > 0) {
                selectCorreo.innerHTML = '<option value="" disabled selected>Selecciona un empleado</option>';
                data.empleados.forEach(empleado => {
                    const option = document.createElement('option');
                    option.value = empleado.correo;
                    option.textContent = `${empleado.nombre} (${empleado.correo})`;
                    selectCorreo.appendChild(option);
                });
                selectCorreo.disabled = false; // Habilitar después de cargar opciones
            } else {
                selectCorreo.innerHTML = '<option value="" disabled selected>No hay empleados disponibles</option>';
                selectCorreo.disabled = false; // Habilitar incluso si no hay empleados
            }
        })
        .catch(error => {
            console.error('Error al cargar empleados:', error);
            selectCorreo.innerHTML = '<option value="" disabled selected>Error al cargar empleados</option>';
            selectCorreo.disabled = false; // Habilitar en caso de error
        });

    modal.show();
}

// Función para enviar el correo (corregida para el HTML proporcionado)
function enviarCorreo(fila, correoDestinatario) {
    const filaId = filaToIdMap[fila];
    const idAuditoria = document.getElementById('numeroDocumento').textContent;

    // IDs ajustados para observaciones, acciones y problemas según el HTML
    const observacionesId = fila === 'UnoUno' ? 'observaciones' : `observaciones${fila}`;
    const accionesId = fila === 'UnoUno' ? 'acciones' : `acciones${fila}`;
    const problemasId = fila === 'UnoUno' ? 'idProblemasUnoUno' : `idProblemas${fila}`;

    const observaciones = document.getElementById(observacionesId)?.value || '';
    const acciones = document.getElementById(accionesId)?.value || '';
    const problemas = document.getElementById(problemasId)?.value || '';
    const estatus = document.getElementById(`idResultado${filaId}`)?.value || '';

    // Ajustar la búsqueda de la fecha para manejar tanto "_" como "."
    let fecha = '';
    const fechaIdVariants = [
        `idFechaFila${filaId}`,           // Ejemplo: idFechaFila1.1
        `idFechaFila${filaId.replace('_', '.')}`, // Ejemplo: idFechaFila3.1
        `idFechaFila${filaId.replace('.', '_')}`  // Ejemplo: idFechaFila3_1
    ];
    for (const fechaId of fechaIdVariants) {
        const fechaElement = document.getElementById(fechaId);
        if (fechaElement) {
            fecha = fechaElement.value;
            break;
        }
    }
    if (!fecha) {
        console.warn(`No se encontró el campo de fecha para ${fila} (${filaId}). IDs buscados: ${fechaIdVariants}`);
    }

    // Extraer la URL de la imagen ajustada al HTML
    let rutaArchivo = '';
    const sectionId = filaId.split('.')[0] || filaId.split('_')[0]; // Número de sección (1, 2, 3, etc.)
    
    // Buscar la fila por data-id o id
    let row = document.querySelector(`tr[data-id="${sectionId}"]`) || document.querySelector(`tr[id="${filaId.replace('_', '.')}"]`);
    if (!row) {
        // Si no se encuentra por data-id o id exacto, buscar por el número en el primer <td>
        const rows = document.querySelectorAll('tr');
        rows.forEach(r => {
            const firstTd = r.querySelector('td:first-child');
            if (firstTd && firstTd.textContent.trim() === filaId.replace('_', '.')) {
                row = r;
            }
        });
    }

    if (row) {
        const imageLink = row.querySelector('td a[href*="../uploads/"]');
        if (imageLink) {
            rutaArchivo = imageLink.getAttribute('href');
            console.log(`Imagen encontrada para ${fila}: ${rutaArchivo}`);
        } else {
            console.warn(`No se encontró enlace de imagen en la fila ${fila} (${filaId}).`);
        }
    } else {
        console.warn(`No se encontró la fila para ${fila} (sectionId: ${sectionId}, filaId: ${filaId}).`);
    }

    const datos = {
        id_auditoria: idAuditoria,
        fila: fila,
        observaciones: observaciones,
        acciones: acciones,
        problemas: problemas,
        estatus: estatus,
        fecha: fecha,
        correo_destinatario: correoDestinatario,
        ruta_archivo: rutaArchivo
    };

    console.log('Datos enviados:', datos);

    fetch('../Controlador/enviar_correo.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Correo enviado exitosamente.');
            const modal = bootstrap.Modal.getInstance(document.getElementById('seleccionarCorreoModal'));
            modal.hide();
        } else {
            alert('Error al enviar el correo: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error al enviar el correo:', error);
        alert('Ocurrió un error al enviar el correo: ' + error.message);
    });
}

function configurarEdicion() {
    filas.forEach(fila => {
        const btnEditar = document.getElementById(`btnEditar${fila}`);
        const btnActualizar = document.getElementById(`btnActualizar${fila}`);

        if (btnEditar) {
            btnEditar.addEventListener('click', function() {
                const row = btnEditar.closest('tr');
                const observaciones = row.querySelector('.observaciones') || row.querySelector('textarea[id^="observaciones"]');
                const acciones = row.querySelector('.acciones') || row.querySelector('textarea[id^="acciones"]');
                const problemas = row.querySelector('.problemas') || row.querySelector('textarea[id^="idProblemas"]');
                const estatus = row.querySelector('.resultado');
                const fecha = row.querySelector('.fecha');

                if (observaciones) observaciones.disabled = false;
                if (acciones) acciones.disabled = false;
                if (problemas) problemas.disabled = false;
                if (estatus) estatus.disabled = false;
                if (fecha) fecha.disabled = false;

                btnEditar.style.display = 'none';
                btnActualizar.style.display = 'inline-block';
            });
        }

        if (btnActualizar) {
            btnActualizar.addEventListener('click', function() {
                enviarActualizacion(fila);
            });
        }
    });
}

function configurarSeguimiento() {
    filas.forEach(fila => {
        const btnSeguimiento = document.getElementById(`btnSeguimiento${fila}`);
        if (btnSeguimiento) {
            btnSeguimiento.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById(`seguimientoModal${fila}`));
                document.getElementById(`accionModal${fila}`).disabled = false;
                document.getElementById(`observacionModal${fila}`).disabled = false;
                document.getElementById(`fechaModal${fila}`).disabled = false;
                document.getElementById(`archivoModal${fila}`).disabled = false;
                modal.show();
            });
        } else {
            console.warn(`No se encontró btnSeguimiento${fila} en el DOM. Verifica el HTML.`);
        }

        const btnVerSeguimiento = document.getElementById(`btnVerSeguimiento${fila}`);
        if (btnVerSeguimiento) {
            btnVerSeguimiento.addEventListener('click', function() {
                const modal = new bootstrap.Modal(document.getElementById(`verSeguimientoModal${fila}`));
                cargarSeguimientos(fila, document.getElementById('numeroDocumento').textContent);
                modal.show();
            });
        } else {
            console.warn(`No se encontró btnVerSeguimiento${fila} en el DOM. Verifica el HTML.`);
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

                fetch('../Controlador/guardar_seguimiento.php', {
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
        } else {
            console.warn(`No se encontró guardarSeguimiento${fila} en el DOM. Verifica el HTML del modal.`);
        }
    });
}

function enviarActualizacion(fila) {
    const row = document.getElementById(`btnActualizar${fila}`).closest('tr');
    const observaciones = row.querySelector('.observaciones') || row.querySelector('textarea[id^="observaciones"]');
    const acciones = row.querySelector('.acciones') || row.querySelector('textarea[id^="acciones"]');
    const problemas = row.querySelector('.problemas') || row.querySelector('textarea[id^="idProblemas"]');
    const estatus = row.querySelector('.resultado');
    const fecha = row.querySelector('.fecha');
    const idAuditoria = document.getElementById('numeroDocumento').textContent;
    const btnEditar = document.getElementById(`btnEditar${fila}`);
    const btnActualizar = document.getElementById(`btnActualizar${fila}`);
    const btnSeguimiento = document.getElementById(`btnSeguimiento${fila}`);

    const datos = {
        id_auditoria: idAuditoria,
        fila: fila.toLowerCase(),
        observaciones: observaciones ? observaciones.value : '',
        acciones: acciones ? acciones.value : '',
        problemas: problemas ? problemas.value : '',
        estatus: estatus.value,
        fecha: fecha.value
    };

    const filaLower = fila.toLowerCase();
    const phpFile = filaToPhpMap[filaLower] || 'actualizar_registro.php';

    fetch(`../${phpFile}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Registro actualizado con éxito');
            if (observaciones) observaciones.disabled = true;
            if (acciones) acciones.disabled = true;
            if (problemas) problemas.disabled = true;
            if (estatus) estatus.disabled = true;
            if (fecha) fecha.disabled = true;

            btnActualizar.style.display = 'none';
            if (estatus.value === 'NOK') {
                btnEditar.style.display = 'inline-block';
                if (btnSeguimiento) btnSeguimiento.style.display = 'inline-block';
            } else {
                btnEditar.style.display = 'none';
                if (btnSeguimiento) btnSeguimiento.style.display = 'none';
            }

            updateColor(estatus);
        } else {
            alert('Error al actualizar el registro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud: ' + error.message);
    });
}

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

function cargarSeguimientos(fila, idAuditoria) {
    fetch(`../Controlador/obtener_seguimientos.php?id_auditoria=${idAuditoria}&fila=${fila}`)
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
        case 'Pendiente':
            select.style.backgroundColor = '#ffc107';
            select.style.color = 'black';
            break;
        case 'NOK':
            select.style.backgroundColor = '#dc3545';
            select.style.color = 'white';
            break;
        default:
            select.style.backgroundColor = 'white';
            select.style.color = 'black';
    }
}

function lockFields() {
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.disabled = true;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    inicializarBotones();
    configurarEdicion();
    configurarSeguimiento();
    configurarEnvioCorreo();
    updateSelectColors();
    lockFields();
});