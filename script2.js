Dropzone.autoDiscover = false;

let idGenerado = null;
let statusChanged = false; // Variable global para rastrear si el estatus ya cambió

// Consolidar todos los eventos DOMContentLoaded en uno solo
document.addEventListener("DOMContentLoaded", function () {
    // console.log("El DOM está cargado"); 

    // Obtener el ID de auditoría
    idGenerado = document.getElementById("idAuditoria")?.value || null;
    console.log("", idGenerado);

    if (idGenerado) {
        document.getElementById("numeroDocumento").textContent = idGenerado;
        cargarDatosPrevios(idGenerado);
    } else {
        console.error("No se encontró el ID de auditoría en el elemento #idAuditoria");
    }

    inicializarDropzone();
    configurarModales();
    configurarBotonesGuardar();
    configurarCierreAuditoria();
});

// Cargar datos previos desde la base de datos
function cargarDatosPrevios(id) {
    fetch(`get_audit_data.php?id_auditoria=${encodeURIComponent(id)}`, { method: "GET" })
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success && data.data) {
                console.log("Datos cargados:", data.data);
                llenarFormulario(data.data);
            } else {
                console.log("", data.error || "Respuesta vacía");
            }
        })
        .catch(error => console.error("Error al cargar datos:", error));
}

// Llenar el formulario con los datos obtenidos
function llenarFormulario(data) {
    const campos = [
        { id: "idNumeroEmpleado", value: data.numero_empleado },
        { id: "idNombreAuditor", value: data.nombre_auditor },
        { id: "idCliente", value: data.cliente },
        { id: "idProcesoAuditado", value: data.proceso_auditado },
        { id: "idParteAuditada", value: data.parte_auditada },
        { id: "idOperacionAuditada", value: data.operacion_auditada },
        { id: "idNave", value: data.nave },
        { id: "idUnidad", value: data.unidad },
        { id: "idFecha", value: data.fecha },
        { id: "idObservaciones1.1", value: data.observaciones },
        { id: "idAcciones1.1", value: data.acciones },
        { id: "idProblemas1.1", value: data.idProblemasUnoUno },
        { id: "idResultado1.1", value: data.estatus },
        { id: "idFechaFila1.1", value: data.fecha_fila },
        { id: "idObservaciones1.2", value: data.observacionesUnoDos },
        { id: "idAcciones1.2", value: data.accionesUnoDos },
        { id: "idProblemas1.2", value: data.idProblemasUnoDos },
        { id: "idResultado1.2", value: data.estatusUnoDos },
        { id: "idFechaFila1.2", value: data.fecha_filaUnoDos },
        { id: "idObservaciones1.3", value: data.observacionesUnoTres },
        { id: "idAcciones1.3", value: data.accionesUnoTres },
        { id: "idProblemas1.3", value: data.idProblemasUnoTres },
        { id: "idResultado1.3", value: data.estatusUnoTres },
        { id: "idFechaFila1.3", value: data.fecha_filaUnoTres },
        { id: "idObservaciones2.1", value: data.observacionesDosUno },
        { id: "idAcciones2.1", value: data.accionesDosUno },
        { id: "idProblemas2.1", value: data.idProblemasDosUno },
        { id: "idResultado2.1", value: data.estatusDosUno },
        { id: "idFechaFila2.1", value: data.fecha_filaDosUno },
        { id: "idObservaciones2.2", value: data.observacionesDosDos },
        { id: "idAcciones2.2", value: data.accionesDosDos },
        { id: "idProblemas2.2", value: data.idProblemasDosDos },
        { id: "idResultado2.2", value: data.estatusDosDos },
        { id: "idFechaFila2.2", value: data.fecha_filaDosDos },
        { id: "idObservaciones2.3", value: data.observacionesDosTres },
        { id: "idAcciones2.3", value: data.accionesDosTres },
        { id: "idProblemas2.3", value: data.idProblemasDosTres },
        { id: "idResultado2.3", value: data.estatusDosTres },
        { id: "idFechaFila2.3", value: data.fecha_filaDosTres },
        { id: "idObservaciones2.4", value: data.observacionesDosCuatro },
        { id: "idAcciones2.4", value: data.accionesDosCuatro },
        { id: "idProblemas2.4", value: data.idProblemasDosCuatro },
        { id: "idResultado2.4", value: data.estatusDosCuatro },
        { id: "idFechaFila2.4", value: data.fecha_filaDosCuatro },
        { id: "idObservaciones2.5", value: data.observacionesDosCinco },
        { id: "idAcciones2.5", value: data.accionesDosCinco },
        { id: "idProblemas2.5", value: data.idProblemasDosCinco },
        { id: "idResultado2.5", value: data.estatusDosCinco },
        { id: "idFechaFila2.5", value: data.fecha_filaDosCinco },
        { id: "idObservaciones2.6", value: data.observacionesDosSeis },
        { id: "idAcciones2.6", value: data.accionesDosSeis },
        { id: "idProblemas2.6", value: data.idProblemasDosSeis },
        { id: "idResultado2.6", value: data.estatusDosSeis },
        { id: "idFechaFila2.6", value: data.fecha_filaDosSeis },
        { id: "idObservaciones3.1", value: data.observacionesTresUno },
        { id: "idAcciones3.1", value: data.accionesTresUno },
        { id: "idProblemas3.1", value: data.idProblemasTresUno },
        { id: "idResultado3.1", value: data.estatusTresUno },
        { id: "idFechaFila3.1", value: data.fecha_filaTresUno },
        { id: "idObservaciones4.1", value: data.observacionesCuatroUno },
        { id: "idAcciones4.1", value: data.accionesCuatroUno },
        { id: "idProblemas4.1", value: data.idProblemasCuatroUno },
        { id: "idResultado4.1", value: data.estatusCuatroUno },
        { id: "idFechaFila4.1", value: data.fecha_filaCuatroUno },
        { id: "idObservaciones4.2", value: data.observacionesCuatroDos },
        { id: "idAcciones4.2", value: data.accionesCuatroDos },
        { id: "idProblemas4.2", value: data.idProblemasCuatroDos },
        { id: "idResultado4.2", value: data.estatusCuatroDos },
        { id: "idFechaFila4.2", value: data.fecha_filaCuatroDos },
        { id: "idObservaciones4.3", value: data.observacionesCuatroTres },
        { id: "idAcciones4.3", value: data.accionesCuatroTres },
        { id: "idProblemas4.3", value: data.idProblemasCuatroTres },
        { id: "idResultado4.3", value: data.estatusCuatroTres },
        { id: "idFechaFila4.3", value: data.fecha_filaCuatroTres },
        { id: "idObservaciones5.1", value: data.observacionesCincoUno },
        { id: "idAcciones5.1", value: data.accionesCincoUno },
        { id: "idProblemas5.1", value: data.idProblemasCincoUno },
        { id: "idResultado5.1", value: data.estatusCincoUno },
        { id: "idFechaFila5.1", value: data.fecha_filaCincoUno },
        { id: "idObservaciones5.2", value: data.observacionesCincoDos },
        { id: "idAcciones5.2", value: data.accionesCincoDos },
        { id: "idProblemas5.2", value: data.idProblemasCincoDos },
        { id: "idResultado5.2", value: data.estatusCincoDos },
        { id: "idFechaFila5.2", value: data.fecha_filaCincoDos },
        { id: "idObservaciones5.3", value: data.observacionesCincoTres },
        { id: "idAcciones5.3", value: data.accionesCincoTres },
        { id: "idProblemas5.3", value: data.idProblemasCincoTres },
        { id: "idResultado5.3", value: data.estatusCincoTres },
        { id: "idFechaFila5.3", value: data.fecha_filaCincoTres },
        { id: "idObservaciones5.4", value: data.observacionesCincoCuatro },
        { id: "idAcciones5.4", value: data.accionesCincoCuatro },
        { id: "idProblemas5.4", value: data.idProblemasCincoCuatro },
        { id: "idResultado5.4", value: data.estatusCincoCuatro },
        { id: "idFechaFila5.4", value: data.fecha_filaCincoCuatro },
        { id: "idObservaciones5.5", value: data.observacionesCincoCinco },
        { id: "idAcciones5.5", value: data.accionesCincoCinco },
        { id: "idProblemas5.5", value: data.idProblemasCincoCinco },
        { id: "idResultado5.5", value: data.estatusCincoCinco },
        { id: "idFechaFila5.5", value: data.fecha_filaCincoCinco },
        { id: "idObservaciones5.6", value: data.observacionesCincoSeis },
        { id: "idAcciones5.6", value: data.accionesCincoSeis },
        { id: "idProblemas5.6", value: data.idProblemasCincoSeis },
        { id: "idResultado5.6", value: data.estatusCincoSeis },
        { id: "idFechaFila5.6", value: data.fecha_filaCincoSeis },
        { id: "idObservaciones5.7", value: data.observacionesCincoSiete },
        { id: "idAcciones5.7", value: data.accionesCincoSiete },
        { id: "idProblemas5.7", value: data.idProblemasCincoSiete },
        { id: "idResultado5.7", value: data.estatusCincoSiete },
        { id: "idFechaFila5.7", value: data.fecha_filaCincoSiete },
        { id: "idObservaciones5.8", value: data.observacionesCincoOcho },
        { id: "idAcciones5.8", value: data.accionesCincoOcho },
        { id: "idProblemas5.8", value: data.idProblemasCincoOcho },
        { id: "idResultado5.8", value: data.estatusCincoOcho },
        { id: "idFechaFila5.8", value: data.fecha_filaCincoOcho },
        { id: "idObservaciones6.1", value: data.observacionesSeisUno },
        { id: "idAcciones6.1", value: data.accionesSeisUno },
        { id: "idProblemas6.1", value: data.idProblemasSeisUno },
        { id: "idResultado6.1", value: data.estatusSeisUno },
        { id: "idFechaFila6.1", value: data.fecha_filaSeisUno },
        { id: "idNombreAuditor2", value: data.idNombreAuditor2 },
        { id: "idNombreSupervisor", value: data.idNombreSupervisor },
        { id: "idNombreOperador", value: data.idNombreOperador }
    ];

    campos.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        if (elemento && campo.value) {
            elemento.value = campo.value;
            cambiarColorSelect({ target: elemento }); // Aplica color si es un select
        }
    });

    if (data.estatus_cierre === "Cerrado") {
        bloquearElementos();
    }
}

// Inicializar Dropzone (comentado, descomentar si lo necesitas)
/*
function inicializarDropzone() {
    document.querySelectorAll(".archivo-dropzone").forEach(function (form) {
        let dropzoneId = form.id;
        if (Dropzone.instances.some(d => d.element.id === dropzoneId)) {
            console.warn(`⚠️ Dropzone ya inicializado en ${dropzoneId}, evitando duplicación.`);
            return;
        }
        new Dropzone(`#${dropzoneId}`, {
            url: "subir_archivo.php",
            autoProcessQueue: false,
            init: function () {
                this.on("addedfile", function () {
                    this.options.autoProcessQueue = true;
                });
            }
        });
    });
}
*/

// Cambiar color de los selects
function cambiarColorSelect(event) {
    const select = event.target;
    select.style.backgroundColor = select.value === "OK" ? "green" : select.value === "NOK" ? "red" : select.value === "Pendiente" ? "yellow" : "";
    select.style.color = select.value === "Pendiente" ? "black" : "white";
}

document.querySelectorAll(".resultado").forEach(select => {
    select.addEventListener("change", cambiarColorSelect);
});

// Configuración de modales
function configurarModales() {
    const modalesConfig = [
        { selectId: "idResultado1.1", modalId: "modalObservaciones", overlayId: "modalOverlay", closeId: "closeModal", guardarId: "btnGuardarDatos" },
        { selectId: "idResultado1.2", modalId: "modalObservaciones1_2", overlayId: "modalOverlay1_2", closeId: "closeModal1_2", guardarId: "btnGuardarDatos1_2" },
        { selectId: "idResultado1.3", modalId: "modalObservaciones1_3", overlayId: "modalOverlay1_3", closeId: "closeModal1_3", guardarId: "btnGuardarDatos1_3" },
        { selectId: "idResultado2.1", modalId: "modalObservaciones2_1", overlayId: "modalOverlay2_1", closeId: "closeModal2_1", guardarId: "btnGuardarDatos2_1" },
        { selectId: "idResultado2.2", modalId: "modalObservaciones2_2", overlayId: "modalOverlay2_2", closeId: "closeModal2_2", guardarId: "btnGuardarDatos2_2" },
        { selectId: "idResultado2.3", modalId: "modalObservaciones2_3", overlayId: "modalOverlay2_3", closeId: "closeModal2_3", guardarId: "btnGuardarDatos2_3" },
        { selectId: "idResultado2.4", modalId: "modalObservaciones2_4", overlayId: "modalOverlay2_4", closeId: "closeModal2_4", guardarId: "btnGuardarDatos2_4" },
        { selectId: "idResultado2.5", modalId: "modalObservaciones2_5", overlayId: "modalOverlay2_5", closeId: "closeModal2_5", guardarId: "btnGuardarDatos2_5" },
        { selectId: "idResultado2.6", modalId: "modalObservaciones2_6", overlayId: "modalOverlay2_6", closeId: "closeModal2_6", guardarId: "btnGuardarDatos2_6" },
        { selectId: "idResultado3.1", modalId: "modalObservaciones3_1", overlayId: "modalOverlay3_1", closeId: "closeModal3_1", guardarId: "btnGuardarDatos3_1" },
        { selectId: "idResultado4.1", modalId: "modalObservaciones4_1", overlayId: "modalOverlay4_1", closeId: "closeModal4_1", guardarId: "btnGuardarDatos4_1" },
        { selectId: "idResultado4.2", modalId: "modalObservaciones4_2", overlayId: "modalOverlay4_2", closeId: "closeModal4_2", guardarId: "btnGuardarDatos4_2" },
        { selectId: "idResultado4.3", modalId: "modalObservaciones4_3", overlayId: "modalOverlay4_3", closeId: "closeModal4_3", guardarId: "btnGuardarDatos4_3" },
        { selectId: "idResultado5.1", modalId: "modalObservaciones5_1", overlayId: "modalOverlay5_1", closeId: "closeModal5_1", guardarId: "btnGuardarDatos5_1" },
        { selectId: "idResultado5.2", modalId: "modalObservaciones5_2", overlayId: "modalOverlay5_2", closeId: "closeModal5_2", guardarId: "btnGuardarDatos5_2" },
        { selectId: "idResultado5.3", modalId: "modalObservaciones5_3", overlayId: "modalOverlay5_3", closeId: "closeModal5_3", guardarId: "btnGuardarDatos5_3" },
        { selectId: "idResultado5.4", modalId: "modalObservaciones5_4", overlayId: "modalOverlay5_4", closeId: "closeModal5_4", guardarId: "btnGuardarDatos5_4" },
        { selectId: "idResultado5.5", modalId: "modalObservaciones5_5", overlayId: "modalOverlay5_5", closeId: "closeModal5_5", guardarId: "btnGuardarDatos5_5" }, 
        { selectId: "idResultado5.6", modalId: "modalObservaciones5_6", overlayId: "modalOverlay5_6", closeId: "closeModal5_6", guardarId: "btnGuardarDatos5_6" },
        { selectId: "idResultado5.7", modalId: "modalObservaciones5_7", overlayId: "modalOverlay5_7", closeId: "closeModal5_7", guardarId: "btnGuardarDatos5_7" },
        { selectId: "idResultado5.8", modalId: "modalObservaciones5_8", overlayId: "modalOverlay5_8", closeId: "closeModal5_8", guardarId: "btnGuardarDatos5_8" },
        { selectId: "idResultado6.1", modalId: "modalObservaciones6_1", overlayId: "modalOverlay6_1", closeId: "closeModal6_1", guardarId: "btnGuardarDatos6_1" }
    ];

    modalesConfig.forEach(config => {
        const selectResultado = document.getElementById(config.selectId);
        const modal = document.getElementById(config.modalId);
        const modalOverlay = document.getElementById(config.overlayId);
        const closeModal = document.getElementById(config.closeId);
        const btnGuardar = document.getElementById(config.guardarId);

        if (!selectResultado || !modal || !modalOverlay || !closeModal || !btnGuardar) {
            console.error(`Faltan elementos para el modal de ${config.selectId}`);
            return;
        }

        function abrirModal() {
            modalOverlay.style.display = "block";
            modal.style.display = "block";
            setTimeout(() => modalOverlay.style.opacity = "1", 10);
        }

        function cerrarModal() {
            modalOverlay.style.opacity = "0";
            setTimeout(() => {
                modalOverlay.style.display = "none";
                modal.style.display = "none";
            }, 300);
        }

        selectResultado.addEventListener("change", function () {
            if (this.value === "NOK" || this.value === "Pendiente") abrirModal();
        });

        closeModal.addEventListener("click", cerrarModal);
        btnGuardar.addEventListener("click", cerrarModal);
    });
}

// Función para cambiar el estatus a "Proceso"
function cambiarEstatusEnProceso() {
    if (!idGenerado) {
        console.error("No hay ID de auditoría para cambiar el estatus");
        return;
    }

    if (!statusChanged) {
        console.log('Intentando cambiar estatus a "Proceso" para ID:', idGenerado);
        fetch('Controlador/update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_auditoria=${encodeURIComponent(idGenerado)}&estatus=Proceso` // Cambiado a "Proceso"
        })
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data);
            if (data.success) {
                console.log('Estatus cambiado a "Proceso"');
                statusChanged = true; // Evitar que se repita
            } else {
                // console.error('Error al cambiar el estatus:', data.message);
            }
        })
        .catch(error => console.error('Error en la solicitud AJAX:', error));
    } else {
        // console.log('Estatus ya cambiado previamente, no se realiza otra solicitud');
    }
}
-
// Función genérica para guardar y actualizar filas
function crearFuncionGuardarFila(seccion, campos, guardarBtn, editarBtn, actualizarBtn, urlGuardar, urlActualizar) {
    function guardarFila() {
        let formData = new FormData();
        formData.append("id", idGenerado);
        campos.forEach(campo => {
            const elemento = document.getElementById(campo.id);
            if (elemento) formData.append(campo.nombre, elemento.value);
        });

        let archivoInput = document.getElementById(`archivo_${seccion}_input`);
        if (archivoInput && archivoInput.files.length > 0) formData.append("archivo", archivoInput.files[0]);

        // Cambiar estatus a "En Proceso" antes de guardar
        cambiarEstatusEnProceso();

        fetch(urlGuardar, {
            method: "POST",
            body: formData,
        })
            .then(response => {
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    console.log(`Fila ${seccion} guardada correctamente`);
                    campos.forEach(campo => {
                        const elemento = document.getElementById(campo.id);
                        if (elemento) elemento.disabled = true;
                    });
                    if (archivoInput) archivoInput.disabled = true;
                    guardarBtn.disabled = true;
                    if (editarBtn) editarBtn.style.display = "inline";
                } else {
                    alert("❌ Error: " + result.error);
                }
            })
            .catch(error => {
                console.error(`Error al guardar fila ${seccion}:`, error);
                // alert("❌ Error en la comunicación con el servidor: " + error.message);
            });
    }

    if (editarBtn) {
        editarBtn.addEventListener("click", function () {
            campos.forEach(campo => {
                const elemento = document.getElementById(campo.id);
                if (elemento) elemento.disabled = false;
            });
            if (archivoInput) archivoInput.disabled = false;
            editarBtn.style.display = "none";
            if (actualizarBtn) actualizarBtn.style.display = "inline";
        });
    }

    if (actualizarBtn) {
        actualizarBtn.addEventListener("click", function () {
            let formData = new FormData();
            formData.append("id", idGenerado);
            campos.forEach(campo => {
                const elemento = document.getElementById(campo.id);
                if (elemento) formData.append(campo.nombre, elemento.value);
            });

            if (archivoInput && archivoInput.files.length > 0) formData.append("archivo", archivoInput.files[0]);

            fetch(urlActualizar, {
                method: "POST",
                body: formData,
            })
                .then(response => {
                    if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        alert(`✅ Fila ${seccion} actualizada correctamente.`);
                        campos.forEach(campo => {
                            const elemento = document.getElementById(campo.id);
                            if (elemento) elemento.disabled = true;
                        });
                        if (archivoInput) archivoInput.disabled = true;
                        actualizarBtn.style.display = "none";
                        if (editarBtn) editarBtn.style.display = "inline";
                    } else {
                        alert("❌ Error: " + result.error);
                    }
                })
                .catch(error => {
                    console.error(`Error al actualizar fila ${seccion}:`, error);
                    alert("❌ Error en la comunicación con el servidor: " + error.message);
                });
        });
    }

    if (guardarBtn) guardarBtn.addEventListener("click", guardarFila);
}

// Configuración de todas las filas
function configurarBotonesGuardar() {
    const filas = [
        {
            seccion: "1_1",
            campos: [
                { id: "idNumeroEmpleado", nombre: "numero_empleado" },
                { id: "idNombreAuditor", nombre: "nombre_auditor" },
                { id: "idCliente", nombre: "cliente" },
                { id: "idProcesoAuditado", nombre: "proceso_auditado" },
                { id: "idParteAuditada", nombre: "parte_auditada" },
                { id: "idOperacionAuditada", nombre: "operacion_auditada" },
                { id: "idNave", nombre: "nave" },
                { id: "idUnidad", nombre: "unidad" },
                { id: "idFecha", nombre: "fecha" },
                { id: "idObservaciones1.1", nombre: "observaciones" },
                { id: "idAcciones1.1", nombre: "acciones" },
                { id: "idProblemas1.1", nombre: "idProblemasUnoUno" },
                { id: "idResultado1.1", nombre: "estatus" },
                { id: "idFechaFila1.1", nombre: "fecha_fila" }
            ],
            guardarBtn: document.getElementById("btnGuardaUnoUno"),
            editarBtn: document.getElementById("btnEditarUnoUno"),
            actualizarBtn: document.getElementById("btnActualizarUnoUno"),
            urlGuardar: "guardar_fila1.php",
            urlActualizar: "Controlador/actualizar_fila1_1.php"
        },
        {
            seccion: "1_2",
            campos: [
                { id: "idObservaciones1.2", nombre: "observacionesUnoDos" },
                { id: "idAcciones1.2", nombre: "accionesUnoDos" },
                { id: "idProblemas1.2", nombre: "idProblemasUnoDos" },
                { id: "idResultado1.2", nombre: "estatusUnoDos" },
                { id: "idFechaFila1.2", nombre: "fecha_filaUnoDos" }
            ],
            guardarBtn: document.getElementById("btnGuardaUnoDos"),
            editarBtn: document.getElementById("btnEditarUnoDos"),
            actualizarBtn: document.getElementById("btnActualizarUnoDos"),
            urlGuardar: "guardar_fila2.php",
            urlActualizar: "Controlador/actualizar_fila2.php"
        },
        {
            seccion: "1_3",
            campos: [
                { id: "idObservaciones1.3", nombre: "observacionesUnoTres" },
                { id: "idAcciones1.3", nombre: "accionesUnoTres" },
                { id: "idProblemas1.3", nombre: "idProblemasUnoTres" },
                { id: "idResultado1.3", nombre: "estatusUnoTres" },
                { id: "idFechaFila1.3", nombre: "fecha_filaUnoTres" }
            ],
            guardarBtn: document.getElementById("btnGuardaUnoTres"),
            editarBtn: document.getElementById("btnEditarUnoTres"),
            actualizarBtn: document.getElementById("btnActualizarUnoTres"),
            urlGuardar: "guardar_fila1_3.php",
            urlActualizar: "Controlador/actualizar_fila1_3.php"
        },
        {
            seccion: "2_1",
            campos: [
                { id: "idObservaciones2.1", nombre: "observacionesDosUno" },
                { id: "idAcciones2.1", nombre: "accionesDosUno" },
                { id: "idProblemas2.1", nombre: "idProblemasDosUno" },
                { id: "idResultado2.1", nombre: "estatusDosUno" },
                { id: "idFechaFila2.1", nombre: "fecha_filaDosUno" }
            ],
            guardarBtn: document.getElementById("btnGuardaDosUno"),
            editarBtn: document.getElementById("btnEditarDosUno"),
            actualizarBtn: document.getElementById("btnActualizarDosUno"),
            urlGuardar: "guardar_fila2_1.php",
            urlActualizar: "Controlador/actualizar_fila2_1.php"
        },
        {
            seccion: "2_2",
            campos: [
                { id: "idObservaciones2.2", nombre: "observacionesDosDos" },
                { id: "idAcciones2.2", nombre: "accionesDosDos" },
                { id: "idProblemas2.2", nombre: "idProblemasDosDos" },
                { id: "idResultado2.2", nombre: "estatusDosDos" },
                { id: "idFechaFila2.2", nombre: "fecha_filaDosDos" }
            ],
            guardarBtn: document.getElementById("btnGuardaDosDos"),
            editarBtn: document.getElementById("btnEditarDosDos"),
            actualizarBtn: document.getElementById("btnActualizarDosDos"),
            urlGuardar: "guardar_fila2_2.php",
            urlActualizar: "Controlador/actualizar_fila2_2.php"
        },
        {
            seccion: "2_3",
            campos: [
                { id: "idObservaciones2.3", nombre: "observacionesDosTres" },
                { id: "idAcciones2.3", nombre: "accionesDosTres" },
                { id: "idProblemas2.3", nombre: "idProblemasDosTres" },
                { id: "idResultado2.3", nombre: "estatusDosTres" },
                { id: "idFechaFila2.3", nombre: "fecha_filaDosTres" }
            ],
            guardarBtn: document.getElementById("btnGuardaDosTres"),
            editarBtn: document.getElementById("btnEditarDosTres"),
            actualizarBtn: document.getElementById("btnActualizarDosTres"),
            urlGuardar: "guardar_fila2_3.php",
            urlActualizar: "Controlador/actualizar_fila2_3.php"
        },
        {
            seccion: "2_4",
            campos: [
                { id: "idObservaciones2.4", nombre: "observacionesDosCuatro" },
                { id: "idAcciones2.4", nombre: "accionesDosCuatro" },
                { id: "idProblemas2.4", nombre: "idProblemasDosCuatro" },
                { id: "idResultado2.4", nombre: "estatusDosCuatro" },
                { id: "idFechaFila2.4", nombre: "fecha_filaDosCuatro" }
            ],
            guardarBtn: document.getElementById("btnGuardaDosCuatro"),
            editarBtn: document.getElementById("btnEditarDosCuatro"),
            actualizarBtn: document.getElementById("btnActualizarDosCuatro"),
            urlGuardar: "guardar_fila2_4.php",
            urlActualizar: "Controlador/actualizar_fila2_4.php"
        },
        {
            seccion: "2_5",
            campos: [
                { id: "idObservaciones2.5", nombre: "observacionesDosCinco" },
                { id: "idAcciones2.5", nombre: "accionesDosCinco" },
                { id: "idProblemas2.5", nombre: "idProblemasDosCinco" },
                { id: "idResultado2.5", nombre: "estatusDosCinco" },
                { id: "idFechaFila2.5", nombre: "fecha_filaDosCinco" }
            ],
            guardarBtn: document.getElementById("btnGuardaDosCinco"),
            editarBtn: document.getElementById("btnEditarDosCinco"),
            actualizarBtn: document.getElementById("btnActualizarDosCinco"),
            urlGuardar: "guardar_fila2_5.php",
            urlActualizar: "Controlador/actualizar_fila2_5.php"
        },
        {
            seccion: "2_6",
            campos: [
                { id: "idObservaciones2.6", nombre: "observacionesDosSeis" },
                { id: "idAcciones2.6", nombre: "accionesDosSeis" },
                { id: "idProblemas2.6", nombre: "idProblemasDosSeis" },
                { id: "idResultado2.6", nombre: "estatusDosSeis" },
                { id: "idFechaFila2.6", nombre: "fecha_filaDosSeis" }
            ],
            guardarBtn: document.getElementById("btnGuardaDosSeis"),
            editarBtn: document.getElementById("btnEditarDosSeis"),
            actualizarBtn: document.getElementById("btnActualizarDosSeis"),
            urlGuardar: "guardar_fila2_6.php",
            urlActualizar: "Controlador/actualizar_fila2_6.php"
        },
        {
            seccion: "3_1",
            campos: [
                { id: "idObservaciones3.1", nombre: "observacionesTresUno" },
                { id: "idAcciones3.1", nombre: "accionesTresUno" },
                { id: "idProblemas3.1", nombre: "idProblemasTresUno" },
                { id: "idResultado3.1", nombre: "estatusTresUno" },
                { id: "idFechaFila3.1", nombre: "fecha_filaTresUno" }
            ],
            guardarBtn: document.getElementById("btnGuardaTresUno"),
            editarBtn: document.getElementById("btnEditarTresUno"),
            actualizarBtn: document.getElementById("btnActualizarTresUno"),
            urlGuardar: "guardar_fila3_1.php",
            urlActualizar: "Controlador/actualizar_fila3_1.php"
        },
        {
            seccion: "4_1",
            campos: [
                { id: "idObservaciones4.1", nombre: "observacionesCuatroUno" },
                { id: "idAcciones4.1", nombre: "accionesCuatroUno" },
                { id: "idProblemas4.1", nombre: "idProblemasCuatroUno" },
                { id: "idResultado4.1", nombre: "estatusCuatroUno" },
                { id: "idFechaFila4.1", nombre: "fecha_filaCuatroUno" }
            ],
            guardarBtn: document.getElementById("btnGuardaCuatroUno"),
            editarBtn: document.getElementById("btnEditarCuatroUno"),
            actualizarBtn: document.getElementById("btnActualizarCuatroUno"),
            urlGuardar: "guardar_fila4_1.php",
            urlActualizar: "Controlador/actualizar_fila4_1.php"
        },
        {
            seccion: "4_2",
            campos: [
                { id: "idObservaciones4.2", nombre: "observacionesCuatroDos" },
                { id: "idAcciones4.2", nombre: "accionesCuatroDos" },
                { id: "idProblemas4.2", nombre: "idProblemasCuatroDos" },
                { id: "idResultado4.2", nombre: "estatusCuatroDos" },
                { id: "idFechaFila4.2", nombre: "fecha_filaCuatroDos" }
            ],
            guardarBtn: document.getElementById("btnGuardaCuatroDos"),
            editarBtn: document.getElementById("btnEditarCuatroDos"),
            actualizarBtn: document.getElementById("btnActualizarCuatroDos"),
            urlGuardar: "guardar_fila4_2.php",
            urlActualizar: "Controlador/actualizar_fila4_2.php"
        },
        {
            seccion: "4_3",
            campos: [
                { id: "idObservaciones4.3", nombre: "observacionesCuatroTres" },
                { id: "idAcciones4.3", nombre: "accionesCuatroTres" },
                { id: "idProblemas4.3", nombre: "idProblemasCuatroTres" },
                { id: "idResultado4.3", nombre: "estatusCuatroTres" },
                { id: "idFechaFila4.3", nombre: "fecha_filaCuatroTres" }
            ],
            guardarBtn: document.getElementById("btnGuardaCuatroTres"),
            editarBtn: document.getElementById("btnEditarCuatroTres"),
            actualizarBtn: document.getElementById("btnActualizarCuatroTres"),
            urlGuardar: "guardar_fila4_3.php",
            urlActualizar: "Controlador/actualizar_fila4_3.php"
        },
        {
            seccion: "5_1",
            campos: [
                { id: "idObservaciones5.1", nombre: "observacionesCincoUno" },
                { id: "idAcciones5.1", nombre: "accionesCincoUno" },
                { id: "idProblemas5.1", nombre: "idProblemasCincoUno" },
                { id: "idResultado5.1", nombre: "estatusCincoUno" },
                { id: "idFechaFila5.1", nombre: "fecha_filaCincoUno" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoUno"),
            editarBtn: document.getElementById("btnEditarCincoUno"),
            actualizarBtn: document.getElementById("btnActualizarCincoUno"),
            urlGuardar: "guardar_fila5_1.php",
            urlActualizar: "Controlador/actualizar_fila5_1.php"
        },
        {
            seccion: "5_2",
            campos: [
                { id: "idObservaciones5.2", nombre: "observacionesCincoDos" },
                { id: "idAcciones5.2", nombre: "accionesCincoDos" },
                { id: "idProblemas5.2", nombre: "idProblemasCincoDos" },
                { id: "idResultado5.2", nombre: "estatusCincoDos" },
                { id: "idFechaFila5.2", nombre: "fecha_filaCincoDos" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoDos"),
            editarBtn: document.getElementById("btnEditarCincoDos"),
            actualizarBtn: document.getElementById("btnActualizarCincoDos"),
            urlGuardar: "guardar_fila5_2.php",
            urlActualizar: "Controlador/actualizar_fila5_2.php"
        },
        {
            seccion: "5_3",
            campos: [
                { id: "idObservaciones5.3", nombre: "observacionesCincoTres" },
                { id: "idAcciones5.3", nombre: "accionesCincoTres" },
                { id: "idProblemas5.3", nombre: "idProblemasCincoTres" },
                { id: "idResultado5.3", nombre: "estatusCincoTres" },
                { id: "idFechaFila5.3", nombre: "fecha_filaCincoTres" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoTres"),
            editarBtn: document.getElementById("btnEditarCincoTres"),
            actualizarBtn: document.getElementById("btnActualizarCincoTres"),
            urlGuardar: "guardar_fila5_3.php",
            urlActualizar: "Controlador/actualizar_fila5_3.php"
        },
        {
            seccion: "5_4",
            campos: [
                { id: "idObservaciones5.4", nombre: "observacionesCincoCuatro" },
                { id: "idAcciones5.4", nombre: "accionesCincoCuatro" },
                { id: "idProblemas5.4", nombre: "idProblemasCincoCuatro" },
                { id: "idResultado5.4", nombre: "estatusCincoCuatro" },
                { id: "idFechaFila5.4", nombre: "fecha_filaCincoCuatro" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoCuatro"),
            editarBtn: document.getElementById("btnEditarCincoCuatro"),
            actualizarBtn: document.getElementById("btnActualizarCincoCuatro"),
            urlGuardar: "guardar_fila5_4.php",
            urlActualizar: "Controlador/actualizar_fila5_4.php"
        },
        {
            seccion: "5_5",
            campos: [
                { id: "idObservaciones5.5", nombre: "observacionesCincoCinco" },
                { id: "idAcciones5.5", nombre: "accionesCincoCinco" },
                { id: "idProblemas5.5", nombre: "idProblemasCincoCinco" },
                { id: "idResultado5.5", nombre: "estatusCincoCinco" },
                { id: "idFechaFila5.5", nombre: "fecha_filaCincoCinco" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoCinco"),
            editarBtn: document.getElementById("btnEditarCincoCinco"),
            actualizarBtn: document.getElementById("btnActualizarCincoCinco"),
            urlGuardar: "guardar_fila5_5.php",
            urlActualizar: "Controlador/actualizar_fila5_5.php"
        },
        {
            seccion: "5_6",
            campos: [
                { id: "idObservaciones5.6", nombre: "observacionesCincoSeis" },
                { id: "idAcciones5.6", nombre: "accionesCincoSeis" },
                { id: "idProblemas5.6", nombre: "idProblemasCincoSeis" },
                { id: "idResultado5.6", nombre: "estatusCincoSeis" },
                { id: "idFechaFila5.6", nombre: "fecha_filaCincoSeis" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoSeis"),
            editarBtn: document.getElementById("btnEditarCincoSeis"),
            actualizarBtn: document.getElementById("btnActualizarCincoSeis"),
            urlGuardar: "guardar_fila5_6.php",
            urlActualizar: "Controlador/actualizar_fila5_6.php"
        },
        {
            seccion: "5_7",
            campos: [
                { id: "idObservaciones5.7", nombre: "observacionesCincoSiete" },
                { id: "idAcciones5.7", nombre: "accionesCincoSiete" },
                { id: "idProblemas5.7", nombre: "idProblemasCincoSiete" },
                { id: "idResultado5.7", nombre: "estatusCincoSiete" },
                { id: "idFechaFila5.7", nombre: "fecha_filaCincoSiete" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoSiete"),
            editarBtn: document.getElementById("btnEditarCincoSiete"),
            actualizarBtn: document.getElementById("btnActualizarCincoSiete"),
            urlGuardar: "guardar_fila5_7.php",
            urlActualizar: "Controlador/actualizar_fila5_7.php"
        },
        {
            seccion: "5_8",
            campos: [
                { id: "idObservaciones5.8", nombre: "observacionesCincoOcho" },
                { id: "idAcciones5.8", nombre: "accionesCincoOcho" },
                { id: "idProblemas5.8", nombre: "idProblemasCincoOcho" },
                { id: "idResultado5.8", nombre: "estatusCincoOcho" },
                { id: "idFechaFila5.8", nombre: "fecha_filaCincoOcho" }
            ],
            guardarBtn: document.getElementById("btnGuardaCincoOcho"),
            editarBtn: document.getElementById("btnEditarCincoOcho"),
            actualizarBtn: document.getElementById("btnActualizarCincoOcho"),
            urlGuardar: "guardar_fila5_8.php",
            urlActualizar: "Controlador/actualizar_fila5_8.php"
        },
        {
            seccion: "6_1",
            campos: [
                { id: "idObservaciones6.1", nombre: "observacionesSeisUno" },
                { id: "idAcciones6.1", nombre: "accionesSeisUno" },
                { id: "idProblemas6.1", nombre: "idProblemasSeisUno" },
                { id: "idResultado6.1", nombre: "estatusSeisUno" },
                { id: "idFechaFila6.1", nombre: "fecha_filaSeisUno" }
            ],
            guardarBtn: document.getElementById("btnGuardaSeisUno"),
            editarBtn: document.getElementById("btnEditarSeisUno"),
            actualizarBtn: document.getElementById("btnActualizarSeisUno"),
            urlGuardar: "guardar_fila6_1.php",
            urlActualizar: "Controlador/actualizar_fila6_1.php"
        }
    ];

    filas.forEach(fila => {
        crearFuncionGuardarFila(
            fila.seccion,
            fila.campos,
            fila.guardarBtn,
            fila.editarBtn,
            fila.actualizarBtn,
            fila.urlGuardar,
            fila.urlActualizar
        );
    });
}

// Configuración para cerrar auditoría
function configurarCierreAuditoria() {
    const cerrarBtn = document.getElementById("cerrarDocumento");
    if (cerrarBtn) {
        cerrarBtn.addEventListener("click", function () {
            let nombreOperador = document.getElementById("idNombreOperador")?.value.trim() || "";
            let nombreSupervisor = document.getElementById("idNombreSupervisor")?.value.trim() || "";
            let nombreAuditor = document.getElementById("idNombreAuditor2")?.value.trim() || "";
            let idAuditoria = document.getElementById("idAuditoria")?.value.trim() || 
                             document.getElementById("numeroDocumento")?.textContent.trim() || "";

            console.log("", idAuditoria);
            console.log("", document.getElementById("idAuditoria")?.value, 
                        "numeroDocumento:", document.getElementById("numeroDocumento")?.textContent);

            if (!idAuditoria) {
                alert("❌ Error: No se encontró el ID de auditoría en la página.");
                console.error("No se pudo obtener idAuditoria de ninguna fuente.");
                return;
            }

            if (!nombreOperador || !nombreSupervisor || !nombreAuditor) {
                alert("❌ Error: Todos los campos de nombres deben estar llenos.");
                return;
            }

            let formData = new FormData();
            formData.append("id", idAuditoria);
            formData.append("nombreOperador", nombreOperador);
            formData.append("nombreSupervisor", nombreSupervisor);
            formData.append("nombreAuditor", nombreAuditor);

            console.log("Datos enviados a cerrar_auditoria.php:", { id: idAuditoria, nombreOperador, nombreSupervisor, nombreAuditor });

            fetch("cerrar_auditoria.php", {
                method: "POST",
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    console.log("Respuesta del servidor:", data);
                    if (data.success) {
                        alert("✅ Auditoría cerrada correctamente.");
                        bloquearElementosYEliminarBotones();
                    } else {
                        alert("❌ Error: " + (data.error || "No se pudo cerrar la auditoría"));
                    }
                })
                .catch(error => {
                    console.error("Error en la solicitud:", error);
                    alert("❌ Error en la comunicación con el servidor: " + error.message);
                });
        });
    } else {
        console.error("No se encontró el botón #cerrarDocumento");
    }
}

function bloquearElementosYEliminarBotones() {
    document.querySelectorAll("input, select, textarea").forEach(element => {
        element.disabled = true;
    });
    document.querySelectorAll("button").forEach(button => {
        button.style.display = "none";
    });
    const botonCerrar = document.querySelector("#cerrarDocumento");
    if (botonCerrar) {
        botonCerrar.style.display = "block";
        botonCerrar.classList.remove("btn-warning");
        botonCerrar.classList.add("btn-success");
        botonCerrar.textContent = "Auditoría Cerrada ✅";
        botonCerrar.disabled = true;
    } else {
        console.error("No se encontró el botón #cerrarDocumento para modificar");
    }
}

// Función vacía para Dropzone si no se usa
function inicializarDropzone() {
}