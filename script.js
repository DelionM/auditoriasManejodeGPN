Dropzone.autoDiscover = false;

let idGenerado = null;

// Consolidar todos los eventos DOMContentLoaded en uno solo
document.addEventListener("DOMContentLoaded", function () {
    idGenerado = document.getElementById("numeroDocumento")?.textContent.trim() || null;
    console.log("ID obtenido desde #numeroDocumento:", idGenerado);

    if (idGenerado) {
        document.getElementById("numeroDocumento").textContent = idGenerado;
        cargarDatosPrevios(idGenerado);
    } else {
        console.error("No se encontró el ID de auditoría en el elemento #numeroDocumento");
    }

    inicializarDropzone();
    configurarModales();
    configurarBotonesGuardar();
    configurarCierreAuditoria();
});

// Cargar datos previos desde la base de datos
function cargarDatosPrevios(id) {
    console.log("Solicitando datos para id_auditoria:", id);
    fetch(`get_audit_dataProcesos.php?id_auditoria=${encodeURIComponent(id)}`, { method: "GET" })
        .then(response => {
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log("Respuesta del servidor:", data);
            if (data.success && data.data) {
                llenarFormulario(data.data);
            } else {
                console.log("No hay datos previos o error:", data.error || "Respuesta vacía");
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
        { id: "idNivelIngenieria", value: data.nivelIngenieria },
        { id: "idNave", value: data.nave },
        { id: "idNombreSupervisor", value: data.supervisor },
        { id: "idUnidad", value: data.unidad },
        { id: "idFecha", value: data.fecha },
        { id: "idObservaciones1", value: data.observaciones },
        { id: "idAcciones1", value: data.acciones },
        { id: "idProblemas1", value: data.idProblemasUno },
        { id: "idResultado1", value: data.estatusUno },
        { id: "idFechaFila1", value: data.fecha_filaUno },
        { id: "idObservaciones2", value: data.observacionesDos },
        { id: "idAcciones2", value: data.accionesDos },
        { id: "idProblemas2", value: data.idProblemasDos },
        { id: "idResultado2", value: data.estatusDos },
        { id: "idFechaFila2", value: data.fecha_filaDos },
        { id: "idObservaciones3", value: data.observacionesTres },
        { id: "idAcciones3", value: data.accionesTres },
        { id: "idProblemas3", value: data.idProblemasTres },
        { id: "idResultado3", value: data.estatusTres },
        { id: "idFechaFila3", value: data.fecha_filaTres },
        { id: "idObservaciones4", value: data.observacionesCuatro },
        { id: "idAcciones4", value: data.accionesCuatro },
        { id: "idProblemas4", value: data.idProblemasCuatro },
        { id: "idResultado4", value: data.estatusCuatro },
        { id: "idFechaFila4", value: data.fecha_filaCuatro },
        { id: "idObservaciones5", value: data.observacionesCinco },
        { id: "idAcciones5", value: data.accionesCinco },
        { id: "idProblemas5", value: data.idProblemasCinco },
        { id: "idResultado5", value: data.estatusCinco },
        { id: "idFechaFila5", value: data.fecha_filaCinco },
        { id: "idObservaciones6", value: data.observacionesSeis },
        { id: "idAcciones6", value: data.accionesSeis },
        { id: "idProblemas6", value: data.idProblemasSeis },
        { id: "idResultado6", value: data.estatusSeis },
        { id: "idFechaFila6", value: data.fecha_filaSeis },
        { id: "idObservaciones7", value: data.observacionesSiete },
        { id: "idAcciones7", value: data.accionesSiete },
        { id: "idProblemas7", value: data.idProblemasSiete },
        { id: "idResultado7", value: data.estatusSiete },
        { id: "idFechaFila7", value: data.fecha_filaSiete },
        { id: "idObservaciones8", value: data.observacionesOcho },
        { id: "idAcciones8", value: data.accionesOcho },
        { id: "idProblemas8", value: data.idProblemasOcho },
        { id: "idResultado8", value: data.estatusOcho },
        { id: "idFechaFila8", value: data.fecha_filaOcho },
        { id: "idObservaciones9", value: data.observacionesNueve },
        { id: "idAcciones9", value: data.accionesNueve },
        { id: "idProblemas9", value: data.idProblemasNueve },
        { id: "idResultado9", value: data.estatusNueve },
        { id: "idFechaFila9", value: data.fecha_filaNueve },
        { id: "idObservaciones10", value: data.observacionesDiez },
        { id: "idAcciones10", value: data.accionesDiez },
        { id: "idProblemas10", value: data.idProblemasDiez },
        { id: "idResultado10", value: data.estatusDiez },
        { id: "idFechaFila10", value: data.fecha_filaDiez },
        { id: "idObservaciones11", value: data.observacionesOnce },
        { id: "idAcciones11", value: data.accionesOnce },
        { id: "idProblemas11", value: data.idProblemasOnce },
        { id: "idResultado11", value: data.estatusOnce },
        { id: "idFechaFila11", value: data.fecha_filaOnce },
        { id: "idObservaciones12", value: data.observacionesDoce },
        { id: "idAcciones12", value: data.accionesDoce },
        { id: "idProblemas12", value: data.idProblemasDoce },
        { id: "idResultado12", value: data.estatusDoce },
        { id: "idFechaFila12", value: data.fecha_filaDoce },
        { id: "idObservaciones13", value: data.observacionesTrece },
        { id: "idAcciones13", value: data.accionesTrece },
        { id: "idProblemas13", value: data.idProblemasTrece },
        { id: "idResultado13", value: data.estatusTrece },
        { id: "idFechaFila13", value: data.fecha_filaTrece },
        { id: "idObservaciones14", value: data.observacionesCatorce },
        { id: "idAcciones14", value: data.accionesCatorce },
        { id: "idProblemas14", value: data.idProblemasCatorce },
        { id: "idResultado14", value: data.estatusCatorce },
        { id: "idFechaFila14", value: data.fecha_filaCatorce },
        { id: "idObservaciones15", value: data.observacionesQuince },
        { id: "idAcciones15", value: data.accionesQuince },
        { id: "idProblemas15", value: data.idProblemasQuince },
        { id: "idResultado15", value: data.estatusQuince },
        { id: "idFechaFila15", value: data.fecha_filaQuince },
        { id: "idObservaciones16", value: data.observacionesDieciseis },
        { id: "idAcciones16", value: data.accionesDieciseis },
        { id: "idProblemas16", value: data.idProblemasDieciseis },
        { id: "idResultado16", value: data.estatusDieciseis },
        { id: "idFechaFila16", value: data.fecha_filaDieciseis },
        { id: "idObservaciones17", value: data.observacionesDiecisiete },
        { id: "idAcciones17", value: data.accionesDiecisiete },
        { id: "idProblemas17", value: data.idProblemasDiecisiete },
        { id: "idResultado17", value: data.estatusDiecisiete },
        { id: "idFechaFila17", value: data.fecha_filaDiecisiete },
        { id: "idObservaciones18", value: data.observacionesDieciocho },
        { id: "idAcciones18", value: data.accionesDieciocho },
        { id: "idProblemas18", value: data.idProblemasDieciocho },
        { id: "idResultado18", value: data.estatusDieciocho },
        { id: "idFechaFila18", value: data.fecha_filaDieciocho },
        { id: "idObservaciones19", value: data.observacionesDiecinueve },
        { id: "idAcciones19", value: data.accionesDiecinueve },
        { id: "idProblemas19", value: data.idProblemasDiecinueve },
        { id: "idResultado19", value: data.estatusDiecinueve },
        { id: "idFechaFila19", value: data.fecha_filaDiecinueve },
        { id: "idObservaciones20", value: data.observacionesVeinte },
        { id: "idAcciones20", value: data.accionesVeinte },
        { id: "idProblemas20", value: data.idProblemasVeinte },
        { id: "idResultado20", value: data.estatusVeinte },
        { id: "idFechaFila20", value: data.fecha_filaVeinte },
        { id: "idObservaciones21", value: data.observacionesVeintiuno },
        { id: "idAcciones21", value: data.accionesVeintiuno },
        { id: "idProblemas21", value: data.idProblemasVeintiuno },
        { id: "idResultado21", value: data.estatusVeintiuno },
        { id: "idFechaFila21", value: data.fecha_filaVeintiuno },
        { id: "idObservaciones22", value: data.observacionesVeintidos },
        { id: "idAcciones22", value: data.accionesVeintidos },
        { id: "idProblemas22", value: data.idProblemasVeintidos },
        { id: "idResultado22", value: data.estatusVeintidos },
        { id: "idFechaFila22", value: data.fecha_filaVeintidos },
        { id: "idObservaciones23", value: data.observacionesVeintitres },
        { id: "idAcciones23", value: data.accionesVeintitres },
        { id: "idProblemas23", value: data.idProblemasVeintitres },
        { id: "idResultado23", value: data.estatusVeintitres },
        { id: "idFechaFila23", value: data.fecha_filaVeintitres },
        { id: "idObservaciones24", value: data.observacionesVeinticuatro },
        { id: "idAcciones24", value: data.accionesVeinticuatro },
        { id: "idProblemas24", value: data.idProblemasVeinticuatro },
        { id: "idResultado24", value: data.estatusVeinticuatro },
        { id: "idFechaFila24", value: data.fecha_filaVeinticuatro },
        { id: "idObservaciones25", value: data.observacionesVeinticinco },
        { id: "idAcciones25", value: data.accionesVeinticinco },
        { id: "idProblemas25", value: data.idProblemasVeinticinco },
        { id: "idResultado25", value: data.estatusVeinticinco },
        { id: "idFechaFila25", value: data.fecha_filaVeinticinco },
        { id: "idNombreAuditor2", value: data.idNombreAuditor2 },
        { id: "idNombreSupervisor", value: data.idNombreSupervisor },
        { id: "idNombreOperador", value: data.idNombreOperador }
    ];

    campos.forEach(campo => {
        const elemento = document.getElementById(campo.id);
        if (elemento && campo.value) {
            elemento.value = campo.value;
            cambiarColorSelect({ target: elemento });
        }
    });

    if (data.estatus_cierre === "Cerrado") {
        bloquearElementos();
    }
}

// Inicializar Dropzone
function inicializarDropzone() {
    document.querySelectorAll(".archivo-dropzone").forEach(function (form) {
        let dropzoneId = form.id;
        if (Dropzone.instances.some(d => d.element.id === dropzoneId)) {
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
        { selectId: "idResultado1", modalId: "modalObservaciones1", overlayId: "modalOverlay1", closeId: "closeModal1", guardarId: "btnGuardarDatos1" },
        { selectId: "idResultado2", modalId: "modalObservaciones2", overlayId: "modalOverlay2", closeId: "closeModal2", guardarId: "btnGuardarDatos2" },
        { selectId: "idResultado3", modalId: "modalObservaciones3", overlayId: "modalOverlay3", closeId: "closeModal3", guardarId: "btnGuardarDatos3" },
        { selectId: "idResultado4", modalId: "modalObservaciones4", overlayId: "modalOverlay4", closeId: "closeModal4", guardarId: "btnGuardarDatos4" },
        { selectId: "idResultado5", modalId: "modalObservaciones5", overlayId: "modalOverlay5", closeId: "closeModal5", guardarId: "btnGuardarDatos5" },
        { selectId: "idResultado6", modalId: "modalObservaciones6", overlayId: "modalOverlay6", closeId: "closeModal6", guardarId: "btnGuardarDatos6" },
        { selectId: "idResultado7", modalId: "modalObservaciones7", overlayId: "modalOverlay7", closeId: "closeModal7", guardarId: "btnGuardarDatos7" },
        { selectId: "idResultado8", modalId: "modalObservaciones8", overlayId: "modalOverlay8", closeId: "closeModal8", guardarId: "btnGuardarDatos8" },
        { selectId: "idResultado9", modalId: "modalObservaciones9", overlayId: "modalOverlay9", closeId: "closeModal9", guardarId: "btnGuardarDatos9" },
        { selectId: "idResultado10", modalId: "modalObservaciones10", overlayId: "modalOverlay10", closeId: "closeModal10", guardarId: "btnGuardarDatos10" },
        { selectId: "idResultado11", modalId: "modalObservaciones11", overlayId: "modalOverlay11", closeId: "closeModal11", guardarId: "btnGuardarDatos11" },
        { selectId: "idResultado12", modalId: "modalObservaciones12", overlayId: "modalOverlay12", closeId: "closeModal12", guardarId: "btnGuardarDatos12" },
        { selectId: "idResultado13", modalId: "modalObservaciones13", overlayId: "modalOverlay13", closeId: "closeModal13", guardarId: "btnGuardarDatos13" },
        { selectId: "idResultado14", modalId: "modalObservaciones14", overlayId: "modalOverlay14", closeId: "closeModal14", guardarId: "btnGuardarDatos14" },
        { selectId: "idResultado15", modalId: "modalObservaciones15", overlayId: "modalOverlay15", closeId: "closeModal15", guardarId: "btnGuardarDatos15" },
        { selectId: "idResultado16", modalId: "modalObservaciones16", overlayId: "modalOverlay16", closeId: "closeModal16", guardarId: "btnGuardarDatos16" },
        { selectId: "idResultado17", modalId: "modalObservaciones17", overlayId: "modalOverlay17", closeId: "closeModal17", guardarId: "btnGuardarDatos17" },
        { selectId: "idResultado18", modalId: "modalObservaciones18", overlayId: "modalOverlay18", closeId: "closeModal18", guardarId: "btnGuardarDatos18" },
        { selectId: "idResultado19", modalId: "modalObservaciones19", overlayId: "modalOverlay19", closeId: "closeModal19", guardarId: "btnGuardarDatos19" },
        { selectId: "idResultado20", modalId: "modalObservaciones20", overlayId: "modalOverlay20", closeId: "closeModal20", guardarId: "btnGuardarDatos20" },
        { selectId: "idResultado21", modalId: "modalObservaciones21", overlayId: "modalOverlay21", closeId: "closeModal21", guardarId: "btnGuardarDatos21" },
        { selectId: "idResultado22", modalId: "modalObservaciones22", overlayId: "modalOverlay22", closeId: "closeModal22", guardarId: "btnGuardarDatos22" },
        { selectId: "idResultado23", modalId: "modalObservaciones23", overlayId: "modalOverlay23", closeId: "closeModal23", guardarId: "btnGuardarDatos23" },
        { selectId: "idResultado24", modalId: "modalObservaciones24", overlayId: "modalOverlay24", closeId: "closeModal24", guardarId: "btnGuardarDatos24" },
        { selectId: "idResultado25", modalId: "modalObservaciones25", overlayId: "modalOverlay25", closeId: "closeModal25", guardarId: "btnGuardarDatos25" }
    ];

    modalesConfig.forEach(config => {
        const selectResultado = document.getElementById(config.selectId);
        const modal = document.getElementById(config.modalId);
        const modalOverlay = document.getElementById(config.overlayId);
        const closeModal = document.getElementById(config.closeId);
        const btnGuardar = document.getElementById(config.guardarId);

        if (!selectResultado || !modal || !modalOverlay || !closeModal || !btnGuardar) {
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
            if (this.value === "NOK" || this.value === "Pendiente") {
                abrirModal();
            }
        });

        closeModal.addEventListener("click", cerrarModal);
        modalOverlay.addEventListener("click", cerrarModal);
        btnGuardar.addEventListener("click", cerrarModal);
    });
}

// Cambiar estatus a "Proceso" de forma asíncrona
async function cambiarEstatusEnProceso() {
    if (!idGenerado) {
        console.error("No hay ID de auditoría para cambiar el estatus");
        alert("Error: No se encontró el ID de auditoría");
        return false;
    }

    console.log('Intentando cambiar estatus a "Proceso" para ID:', idGenerado);
    try {
        const response = await fetch('./Controlador/update_statusProceso.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_auditoria=${encodeURIComponent(idGenerado)}&estatus=Proceso`
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`Error HTTP: ${response.status} - ${errorText}`);
        }

        const data = await response.json();
        console.log('Respuesta del servidor:', data);

        if (data.success) {
            console.log('Estatus cambiado a "Proceso"');
            return true;
        } else {
            // console.error('Error al cambiar el estatus:', data.message);
            // alert('Error: ' + data.message);
            return false;
        }
    } catch (error) {
        console.error('Error en la solicitud AJAX:', error);
        alert('Error en la comunicación con el servidor: ' + error.message);
        return false;
    }
}

// Configurar botones de guardar
function configurarBotonesGuardar() {
    const filas = [
        {
            seccion: "1",
            campos: [
                { id: "idNumeroEmpleado", nombre: "numero_empleado" },
                { id: "idNombreAuditor", nombre: "nombre_auditor" },
                { id: "idCliente", nombre: "cliente" },
                { id: "idProcesoAuditado", nombre: "proceso_auditado" },
                { id: "idParteAuditada", nombre: "parte_auditada" },
                { id: "idNivelIngenieria", nombre: "nivelIngenieria" },
                { id: "idNave", nombre: "nave" },
                { id: "idNombreSupervisor", nombre: "nombre_supervisor" },
                { id: "idUnidad", nombre: "unidad" },
                { id: "idFecha", nombre: "fecha" },
                { id: "idObservaciones1", nombre: "observaciones" },
                { id: "idAcciones1", nombre: "acciones" },
                { id: "idProblemas1", nombre: "idProblemasUno" },
                { id: "idResultado1", nombre: "estatusUno" },
                { id: "idFechaFila1", nombre: "fecha_filaUno" }
            ],
            guardarBtn: document.getElementById("btnGuardaUno"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila1.php",
        },
        {
            seccion: "2",
            campos: [
                { id: "idObservaciones2", nombre: "observacionesDos" },
                { id: "idAcciones2", nombre: "accionesDos" },
                { id: "idProblemas2", nombre: "idProblemasDos" },
                { id: "idResultado2", nombre: "estatusDos" },
                { id: "idFechaFila2", nombre: "fecha_filaDos" }
            ],
            guardarBtn: document.getElementById("btnGuardaDos"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila2.php",
        },
        {
            seccion: "3",
            campos: [
                { id: "idObservaciones3", nombre: "observacionesTres" },
                { id: "idAcciones3", nombre: "accionesTres" },
                { id: "idProblemas3", nombre: "idProblemasTres" },
                { id: "idResultado3", nombre: "estatusTres" },
                { id: "idFechaFila3", nombre: "fecha_filaTres" }
            ],
            guardarBtn: document.getElementById("btnGuardaTres"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila3.php",
        },
        {
            seccion: "4",
            campos: [
                { id: "idObservaciones4", nombre: "observacionesCuatro" },
                { id: "idAcciones4", nombre: "accionesCuatro" },
                { id: "idProblemas4", nombre: "idProblemasCuatro" },
                { id: "idResultado4", nombre: "estatusCuatro" },
                { id: "idFechaFila4", nombre: "fecha_filaCuatro" }
            ],
            guardarBtn: document.getElementById("btnGuardaCuatro"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila4.php",
        },
        {
            seccion: "5",
            campos: [
                { id: "idObservaciones5", nombre: "observacionesCinco" },
                { id: "idAcciones5", nombre: "accionesCinco" },
                { id: "idProblemas5", nombre: "idProblemasCinco" },
                { id: "idResultado5", nombre: "estatusCinco" },
                { id: "idFechaFila5", nombre: "fecha_filaCinco" }
            ],
            guardarBtn: document.getElementById("btnGuardaCinco"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila5.php",
        },
        {
            seccion: "6",
            campos: [
                { id: "idObservaciones6", nombre: "observacionesSeis" },
                { id: "idAcciones6", nombre: "accionesSeis" },
                { id: "idProblemas6", nombre: "idProblemasSeis" },
                { id: "idResultado6", nombre: "estatusSeis" },
                { id: "idFechaFila6", nombre: "fecha_filaSeis" }
            ],
            guardarBtn: document.getElementById("btnGuardaSeis"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila6.php",
        },
        {
            seccion: "7",
            campos: [
                { id: "idObservaciones7", nombre: "observacionesSiete" },
                { id: "idAcciones7", nombre: "accionesSiete" },
                { id: "idProblemas7", nombre: "idProblemasSiete" },
                { id: "idResultado7", nombre: "estatusSiete" },
                { id: "idFechaFila7", nombre: "fecha_filaSiete" }
            ],
            guardarBtn: document.getElementById("btnGuardaSiete"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila7.php",
        },
        {
            seccion: "8",
            campos: [
                { id: "idObservaciones8", nombre: "observacionesOcho" },
                { id: "idAcciones8", nombre: "accionesOcho" },
                { id: "idProblemas8", nombre: "idProblemasOcho" },
                { id: "idResultado8", nombre: "estatusOcho" },
                { id: "idFechaFila8", nombre: "fecha_filaOcho" }
            ],
            guardarBtn: document.getElementById("btnGuardaOcho"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila8.php",
        },
        {
            seccion: "9",
            campos: [
                { id: "idObservaciones9", nombre: "observacionesNueve" },
                { id: "idAcciones9", nombre: "accionesNueve" },
                { id: "idProblemas9", nombre: "idProblemasNueve" },
                { id: "idResultado9", nombre: "estatusNueve" },
                { id: "idFechaFila9", nombre: "fecha_filaNueve" }
            ],
            guardarBtn: document.getElementById("btnGuardaNueve"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila9.php",
        },
        {
            seccion: "10",
            campos: [
                { id: "idObservaciones10", nombre: "observacionesDiez" },
                { id: "idAcciones10", nombre: "accionesDiez" },
                { id: "idProblemas10", nombre: "idProblemasDiez" },
                { id: "idResultado10", nombre: "estatusDiez" },
                { id: "idFechaFila10", nombre: "fecha_filaDiez" }
            ],
            guardarBtn: document.getElementById("btnGuardaDiez"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila10.php",
        },
        {
            seccion: "11",
            campos: [
                { id: "idObservaciones11", nombre: "observacionesOnce" },
                { id: "idAcciones11", nombre: "accionesOnce" },
                { id: "idProblemas11", nombre: "idProblemasOnce" },
                { id: "idResultado11", nombre: "estatusOnce" },
                { id: "idFechaFila11", nombre: "fecha_filaOnce" }
            ],
            guardarBtn: document.getElementById("btnGuardaOnce"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila11.php",
        },
        {
            seccion: "12",
            campos: [
                { id: "idObservaciones12", nombre: "observacionesDoce" },
                { id: "idAcciones12", nombre: "accionesDoce" },
                { id: "idProblemas12", nombre: "idProblemasDoce" },
                { id: "idResultado12", nombre: "estatusDoce" },
                { id: "idFechaFila12", nombre: "fecha_filaDoce" }
            ],
            guardarBtn: document.getElementById("btnGuardaDoce"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila12.php",
        },
        {
            seccion: "13",
            campos: [
                { id: "idObservaciones13", nombre: "observacionesTrece" },
                { id: "idAcciones13", nombre: "accionesTrece" },
                { id: "idProblemas13", nombre: "idProblemasTrece" },
                { id: "idResultado13", nombre: "estatusTrece" },
                { id: "idFechaFila13", nombre: "fecha_filaTrece" }
            ],
            guardarBtn: document.getElementById("btnGuardaTrece"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila13.php",
        },
        {
            seccion: "14",
            campos: [
                { id: "idObservaciones14", nombre: "observacionesCatorce" },
                { id: "idAcciones14", nombre: "accionesCatorce" },
                { id: "idProblemas14", nombre: "idProblemasCatorce" },
                { id: "idResultado14", nombre: "estatusCatorce" },
                { id: "idFechaFila14", nombre: "fecha_filaCatorce" }
            ],
            guardarBtn: document.getElementById("btnGuardaCatorce"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila14.php",
        },
        {
            seccion: "15",
            campos: [
                { id: "idObservaciones15", nombre: "observacionesQuince" },
                { id: "idAcciones15", nombre: "accionesQuince" },
                { id: "idProblemas15", nombre: "idProblemasQuince" },
                { id: "idResultado15", nombre: "estatusQuince" },
                { id: "idFechaFila15", nombre: "fecha_filaQuince" }
            ],
            guardarBtn: document.getElementById("btnGuardaQuince"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila15.php",
        },
        {
            seccion: "16",
            campos: [
                { id: "idObservaciones16", nombre: "observacionesDieciseis" },
                { id: "idAcciones16", nombre: "accionesDieciseis" },
                { id: "idProblemas16", nombre: "idProblemasDieciseis" },
                { id: "idResultado16", nombre: "estatusDieciseis" },
                { id: "idFechaFila16", nombre: "fecha_filaDieciseis" }
            ],
            guardarBtn: document.getElementById("btnGuardaDieciseis"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila16.php",
        },
        {
            seccion: "17",
            campos: [
                { id: "idObservaciones17", nombre: "observacionesDiecisiete" },
                { id: "idAcciones17", nombre: "accionesDiecisiete" },
                { id: "idProblemas17", nombre: "idProblemasDiecisiete" },
                { id: "idResultado17", nombre: "estatusDiecisiete" },
                { id: "idFechaFila17", nombre: "fecha_filaDiecisiete" }
            ],
            guardarBtn: document.getElementById("btnGuardaDiecisiete"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila17.php",
        },
        {
            seccion: "18",
            campos: [
                { id: "idObservaciones18", nombre: "observacionesDieciocho" },
                { id: "idAcciones18", nombre: "accionesDieciocho" },
                { id: "idProblemas18", nombre: "idProblemasDieciocho" },
                { id: "idResultado18", nombre: "estatusDieciocho" },
                { id: "idFechaFila18", nombre: "fecha_filaDieciocho" }
            ],
            guardarBtn: document.getElementById("btnGuardaDieciocho"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila18.php",
        },
        {
            seccion: "19",
            campos: [
                { id: "idObservaciones19", nombre: "observacionesDiecinueve" },
                { id: "idAcciones19", nombre: "accionesDiecinueve" },
                { id: "idProblemas19", nombre: "idProblemasDiecinueve" },
                { id: "idResultado19", nombre: "estatusDiecinueve" },
                { id: "idFechaFila19", nombre: "fecha_filaDiecinueve" }
            ],
            guardarBtn: document.getElementById("btnGuardaDiecinueve"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila19.php",
        },
        {
            seccion: "20",
            campos: [
                { id: "idObservaciones20", nombre: "observacionesVeinte" },
                { id: "idAcciones20", nombre: "accionesVeinte" },
                { id: "idProblemas20", nombre: "idProblemasVeinte" },
                { id: "idResultado20", nombre: "estatusVeinte" },
                { id: "idFechaFila20", nombre: "fecha_filaVeinte" }
            ],
            guardarBtn: document.getElementById("btnGuardaVeinte"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila20.php",
        },
        {
            seccion: "21",
            campos: [
                { id: "idObservaciones21", nombre: "observacionesVeintiuno" },
                { id: "idAcciones21", nombre: "accionesVeintiuno" },
                { id: "idProblemas21", nombre: "idProblemasVeintiuno" },
                { id: "idResultado21", nombre: "estatusVeintiuno" },
                { id: "idFechaFila21", nombre: "fecha_filaVeintiuno" }
            ],
            guardarBtn: document.getElementById("btnGuardaVeintiuno"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila21.php",
        },
        {
            seccion: "22",
            campos: [
                { id: "idObservaciones22", nombre: "observacionesVeintidos" },
                { id: "idAcciones22", nombre: "accionesVeintidos" },
                { id: "idProblemas22", nombre: "idProblemasVeintidos" },
                { id: "idResultado22", nombre: "estatusVeintidos" },
                { id: "idFechaFila22", nombre: "fecha_filaVeintidos" }
            ],
            guardarBtn: document.getElementById("btnGuardaVeintidos"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila22.php",
        },
        {
            seccion: "23",
            campos: [
                { id: "idObservaciones23", nombre: "observacionesVeintitres" },
                { id: "idAcciones23", nombre: "accionesVeintitres" },
                { id: "idProblemas23", nombre: "idProblemasVeintitres" },
                { id: "idResultado23", nombre: "estatusVeintitres" },
                { id: "idFechaFila23", nombre: "fecha_filaVeintitres" }
            ],
            guardarBtn: document.getElementById("btnGuardaVeintitres"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila23.php",
        },
        {
            seccion: "24",
            campos: [
                { id: "idObservaciones24", nombre: "observacionesVeinticuatro" },
                { id: "idAcciones24", nombre: "accionesVeinticuatro" },
                { id: "idProblemas24", nombre: "idProblemasVeinticuatro" },
                { id: "idResultado24", nombre: "estatusVeinticuatro" },
                { id: "idFechaFila24", nombre: "fecha_filaVeinticuatro" }
            ],
            guardarBtn: document.getElementById("btnGuardaVeinticuatro"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila24.php",
        },
        {
            seccion: "25",
            campos: [
                { id: "idObservaciones25", nombre: "observacionesVeinticinco" },
                { id: "idAcciones25", nombre: "accionesVeinticinco" },
                { id: "idProblemas25", nombre: "idProblemasVeinticinco" },
                { id: "idResultado25", nombre: "estatusVeinticinco" },
                { id: "idFechaFila25", nombre: "fecha_filaVeinticinco" }
            ],
            guardarBtn: document.getElementById("btnGuardaVeinticinco"),
            urlGuardar: "Controlador/guardarPorProceso/guardar_fila25.php",
        }
    ];

    filas.forEach(fila => {
        if (fila.guardarBtn) {
            fila.guardarBtn.addEventListener('click', async () => {
                // console.log(`Botón de guardar para sección ${fila.seccion} cliqueado`);
                const formData = new FormData();
                formData.append("id", idGenerado);

                fila.campos.forEach(campo => {
                    const elemento = document.getElementById(campo.id);
                    formData.append(campo.nombre, elemento ? elemento.value : '');
                });

                const archivoInput = document.getElementById(`archivo_${fila.seccion}`);
                if (archivoInput && archivoInput.files && archivoInput.files.length > 0) {
                    formData.append("archivo", archivoInput.files[0]);
                }

                // Esperar a que el estatus se actualice antes de guardar
                const estatusActualizado = await cambiarEstatusEnProceso();
                // if (!estatusActualizado) {
                //     console.error("No se pudo actualizar el estatus, abortando guardado.");
                //     return;
                // }

                // Proceder con el guardado y pasar el botón para modificarlo después
                enviarDatos(fila.urlGuardar, formData, fila.guardarBtn);
            });
        }
    });

    async function enviarDatos(url, formData, botonGuardar) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();

            if (data.success) {
                // console.log('Datos guardados con éxito para la sección');
                // Deshabilitar el botón y cambiar su estilo
                botonGuardar.disabled = true;
                botonGuardar.style.opacity = "0.5"; // Opacidad reducida para indicar que está bloqueado
                botonGuardar.style.cursor = "not-allowed"; // Cambiar el cursor para mayor claridad
                // Opcional: Cambiar el texto del botón
                botonGuardar.textContent = "Guardado ✅";
                // alert('Datos guardados con éxito');
            } else {
                console.error('Error al guardar los datos:', data.error);
                alert('Error al guardar los datos: ' + data.error);
            }
        } catch (error) {
            console.error('Error al guardar los datos:', error);
            alert('Error al guardar los datos: ' + error.message);
        }
    }
}

// Configuración para cerrar auditoría
function configurarCierreAuditoria() {
    const cerrarBtn = document.getElementById("cerrarDocumento");
    if (cerrarBtn) {
        cerrarBtn.addEventListener("click", async function () {
            let nombreAuditado = document.getElementById("idNombreAuditado")?.value.trim() || "";
            let nombreSupervisor = document.getElementById("idNombreSupervisor")?.value.trim() || "";
            let nombreAuditor = document.getElementById("idNombreAuditor2")?.value.trim() || "";
            let idAuditoria = document.getElementById("idAuditoria")?.value.trim() || 
                             document.getElementById("numeroDocumento")?.textContent.trim() || "";

            if (!idAuditoria) {
                alert("❌ Error: No se encontró el ID de auditoría.");
                console.error("ID de auditoría no encontrado.");
                return;
            }

            if (!nombreAuditado || !nombreSupervisor || !nombreAuditor) {
                alert("❌ Error: Todos los campos de nombres deben estar llenos.");
                return;
            }

            const formData = new FormData();
            formData.append("id", idAuditoria);
            formData.append("nombreAuditado", nombreAuditado);
            formData.append("nombreSupervisor", nombreSupervisor);
            formData.append("nombreAuditor", nombreAuditor);

            try {
                const response = await fetch("Controlador/cerrar_auditoria_procesos.php", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();

                if (data.success) {
                    alert("✅ Auditoría cerrada correctamente.");
                    bloquearElementosYEliminarBotones();
                } else {
                    alert("❌ Error: " + (data.error || "No se pudo cerrar la auditoría"));
                }
            } catch (error) {
                console.error("Error en la solicitud:", error);
                alert("❌ Error en la comunicación con el servidor: " + error.message);
            }
        });
    }
}

function bloquearElementosYEliminarBotones() {
    document.querySelectorAll("input, select, textarea").forEach(element => {
        element.disabled = true;
    });
    document.querySelectorAll("button").forEach(button => {
        button.style.display = "none";
    });
    const botonCerrar = document.querySelector("#cerrarDocumento button");
    if (botonCerrar) {
        botonCerrar.style.display = "block";
        botonCerrar.classList.remove("btn-warning");
        botonCerrar.classList.add("btn-success");
        botonCerrar.textContent = "Auditoría Cerrada ✅";
        botonCerrar.disabled = true;
    }
}

function bloquearElementos() {
    document.querySelectorAll("input, select, textarea").forEach(element => {
        element.disabled = true;
    });
}