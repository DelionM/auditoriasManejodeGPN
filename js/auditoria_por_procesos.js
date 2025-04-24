// Evita inicializaciones automáticas de Dropzone
Dropzone.autoDiscover = false;

let idGenerado = null; // Variable global para almacenar el ID generado por PHP

// Cuando el DOM esté cargado
document.addEventListener("DOMContentLoaded", function () {
    console.log("El DOM está cargado");

    // Obtener el ID desde el backend
    fetch("../Controlador/get_auditoria.php")
        .then(response => response.json())
        .then(data => {
            console.log("Datos obtenidos:", data);
            idGenerado = data.id; // Guardar el ID globalmente
            document.getElementById("numeroDocumento").textContent = idGenerado;

            // Inicializar Dropzone después de obtener el ID
            inicializarDropzone();
        })
        .catch(error => console.error("Error al obtener el ID:", error));

    // Cambiar color de los selects automáticamente
    document.querySelectorAll(".resultado").forEach(select => {
        select.addEventListener("change", cambiarColorSelect);
    });
});

// Inicializar Dropzone para cada formulario
function inicializarDropzone() {
    document.querySelectorAll(".archivo-dropzone").forEach(function (form) {
        let dropzoneId = form.id;

        if (Dropzone.instances.some(dz => dz.element.id === dropzoneId)) {
            console.warn(`⚠️ Dropzone ya inicializado en ${dropzoneId}, evitando duplicación.`);
            return;
        }

        
    });
}

// Cambiar color de los selects según la selección
function cambiarColorSelect(event) {
    const select = event.target;
    select.style.backgroundColor = select.value === "OK" ? "#4caf50" : select.value === "Pendiente" ? "#ffeb3b" : "#f44336";
    select.style.color = select.value === "Pendiente" ? "black" : "white";
}

// Funcionalidad para modales genérica
function configurarModal(selectId, modalId, overlayId, closeId, saveId) {
    const select = document.getElementById(selectId);
    const modal = document.getElementById(modalId);
    const modalOverlay = document.getElementById(overlayId);
    const closeModal = document.getElementById(closeId);
    const btnGuardar = document.getElementById(saveId);

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

    select.addEventListener("change", function () {
        if (this.value === "NOK" || this.value === "Pendiente") {
            abrirModal();
        }
    });

    closeModal.addEventListener("click", cerrarModal);
    btnGuardar.addEventListener("click", cerrarModal);
}

// Configurar todos los modales
const modales = [
    ["idResultado1.1", "modalObservaciones", "modalOverlay", "closeModal", "btnGuardarDatos"],
    ["idResultado1.2", "modalObservaciones1_2", "modalOverlay1_2", "closeModal1_2", "btnGuardarDatos1_2"],
    ["idResultado1.3", "modalObservaciones1_3", "modalOverlay1_3", "closeModal1_3", "btnGuardarDatos1_3"],
    ["idResultado2.1", "modalObservaciones2_1", "modalOverlay2_1", "closeModal2_1", "btnGuardarDatos2_1"],
    ["idResultado2.2", "modalObservaciones2_2", "modalOverlay2_2", "closeModal2_2", "btnGuardarDatos2_2"],
    ["idResultado2.3", "modalObservaciones2_3", "modalOverlay2_3", "closeModal2_3", "btnGuardarDatos2_3"],
    ["idResultado2.4", "modalObservaciones2_4", "modalOverlay2_4", "closeModal2_4", "btnGuardarDatos2_4"],
    ["idResultado2.5", "modalObservaciones2_5", "modalOverlay2_5", "closeModal2_5", "btnGuardarDatos2_5"],
    ["idResultado2.6", "modalObservaciones2_6", "modalOverlay2_6", "closeModal2_6", "btnGuardarDatos2_6"],
    ["idResultado3.1", "modalObservaciones3_1", "modalOverlay3_1", "closeModal3_1", "btnGuardarDatos3_1"],
    ["idResultado4.1", "modalObservaciones4_1", "modalOverlay4_1", "closeModal4_1", "btnGuardarDatos4_1"],
    ["idResultado4.3", "modalObservaciones4_3", "modalOverlay4_3", "closeModal4_3", "btnGuardarDatos4_3"],
    ["idResultado5.1", "modalObservaciones5_1", "modalOverlay5_1", "closeModal5_1", "btnGuardarDatos5_1"],
    ["idResultado5.2", "modalObservaciones5_2", "modalOverlay5_2", "closeModal5_2", "btnGuardarDatos5_2"],
    ["idResultado5.3", "modalObservaciones5_3", "modalOverlay5_3", "closeModal5_3", "btnGuardarDatos5_3"],
    ["idResultado5.4", "modalObservaciones5_4", "modalOverlay5_4", "closeModal5_4", "btnGuardarDatos5_4"],
    ["idResultado5.5", "modalObservaciones5_5", "modalOverlay5_5", "closeModal5_5", "btnGuardarDatos5_5"],
    ["idResultado5.6", "modalObservaciones5_6", "modalOverlay5_6", "closeModal5_6", "btnGuardarDatos5_6"],
    ["idResultado5.7", "modalObservaciones5_7", "modalOverlay5_7", "closeModal5_7", "btnGuardarDatos5_7"],
    ["idResultado5.8", "modalObservaciones5_8", "modalOverlay5_8", "closeModal5_8", "btnGuardarDatos5_8"],
    ["idResultado6.1", "modalObservaciones6_1", "modalOverlay6_1", "closeModal6_1", "btnGuardarDatos6_1"]
];

modales.forEach(modal => configurarModal(...modal));

// Guardar datos de cada fila (ejemplo para 1.1, repetir para otras filas)
function configurarGuardarFila(btnGuardarId, btnEditarId, btnActualizarId, filaId, guardarUrl, actualizarUrl) {
    const guardarBtn = document.getElementById(btnGuardarId);
    const editarBtn = document.getElementById(btnEditarId);
    const actualizarBtn = document.getElementById(btnActualizarId);

    function guardarFila() {
        if (!idGenerado) {
            alert("No se ha generado un ID de auditoría aún.");
            return;
        }
    
        let formData = new FormData();
        // Campos del encabezado
        formData.append("id", idGenerado);
        formData.append("proceso_auditado", document.getElementById("idProcesoAuditado").value || "");
        formData.append("cliente", document.getElementById("idCliente").value || "");
        formData.append("no_parte", document.getElementById("idParteAuditada").value || "");
        formData.append(`responsable${filaId.replace(".", "")}`, document.getElementById(`idResponsable${filaId}`).value || ""); // Añadido aquí       
        formData.append("nave", document.getElementById("idNave").value || "");
        formData.append("nivel_ingenieria", document.getElementById("idNivelIngenieria").value || "");
        formData.append("revision_fecha", document.getElementById("idRevisionFecha").value || "");
        formData.append("nombre_auditor", document.getElementById("idNombreAuditor").value || "");
        formData.append("supervisor", document.getElementById("idSupervisor").value || "");
        formData.append("fecha", document.getElementById("idFecha").value || "");
        formData.append("turno", document.getElementById("idTurno").value || "");
        formData.append("hora", document.getElementById("idHora").value || "");
    
        // Campos específicos de la fila
        formData.append(`observaciones${filaId.replace(".", "")}`, document.getElementById(`idObservaciones${filaId}`).value || "");
        formData.append(`acciones${filaId.replace(".", "")}`, document.getElementById(`idAcciones${filaId}`).value || "");
        formData.append(`estatus${filaId.replace(".", "")}`, document.getElementById(`idResultado${filaId}`).value || "");
        formData.append(`fechaFila${filaId.replace(".", "")}`, document.getElementById(`idFechaFila${filaId}`).value || "");
    
        let archivoInput = document.getElementById(`archivo_${filaId.replace(".", "_")}_input`);
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }
    
        // Depuración: Ver qué datos se envían
        console.log("Datos enviados:", Object.fromEntries(formData));
    
        fetch(guardarUrl, {
            method: "POST",
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // Usamos text() para depurar primero
        })
        .then(text => {
            console.log("Respuesta cruda:", text);
            try {
                const result = JSON.parse(text);
                if (result.success) {
                    alert(`✅ Fila ${filaId} guardada correctamente.`);
                    bloquearCampos(filaId);
                    guardarBtn.style.display = 'none';
                    editarBtn.style.display = 'inline';
                } else {
                    alert("❌ Error: " + result.error);
                }
            } catch (e) {
                console.error("Error al parsear JSON:", e);
                alert("❌ Error del servidor: respuesta no válida");
            }
        })
        .catch(error => {
            console.error("Error al guardar:", error);
            alert("❌ Error al conectar con el servidor");
        });
    }

    function bloquearCampos(filaId) {
        document.querySelectorAll(`#idObservaciones${filaId}, #idAcciones${filaId}, #idResultado${filaId}, #idFechaFila${filaId}, #archivo_${filaId.replace(".", "_")}_input`)
            .forEach(element => element.disabled = true);
    }

    function habilitarCampos(filaId) {
        document.querySelectorAll(`#idObservaciones${filaId}, #idAcciones${filaId}, #idResultado${filaId}, #idFechaFila${filaId}, #archivo_${filaId.replace(".", "_")}_input`)
            .forEach(element => element.disabled = false);
    }

    guardarBtn.addEventListener("click", guardarFila);

    editarBtn.addEventListener("click", function () {
        habilitarCampos(filaId);
        editarBtn.style.display = 'none';
        actualizarBtn.style.display = 'inline';
    });

    actualizarBtn.addEventListener("click", function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append(`observaciones${filaId.replace(".", "")}`, document.getElementById(`idObservaciones${filaId}`).value);
        formData.append(`acciones${filaId.replace(".", "")}`, document.getElementById(`idAcciones${filaId}`).value);
        formData.append(`estatus${filaId.replace(".", "")}`, document.getElementById(`idResultado${filaId}`).value);
        formData.append(`fechaFila${filaId.replace(".", "")}`, document.getElementById(`idFechaFila${filaId}`).value);

        let archivoInput = document.getElementById(`archivo_${filaId.replace(".", "_")}_input`);
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch(actualizarUrl, {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(`✅ Fila ${filaId} actualizada correctamente.`);
                bloquearCampos(filaId);
                actualizarBtn.style.display = 'none';
                editarBtn.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => console.error("Error al actualizar:", error));
    });
}

// Configurar guardar para todas las filas
const filas = [
    ["btnGuardaUnoUno", "btnEditarUnoUno", "btnActualizarUnoUno", "1.1", "../Controlador/guardar_fila1_1.php", "Controlador/actualizar_fila1_1.php"],
    ["btnGuardaUnoDos", "btnEditarUnoDos", "btnActualizarUnoDos", "1.2", "../Controlador/guardar_fila1_2.php", "Controlador/actualizar_fila1_2.php"],
    ["btnGuardaUnoTres", "btnEditarUnoTres", "btnActualizarUnoTres", "1.3", "../Controlador/guardar_fila1_3.php", "Controlador/actualizar_fila1_3.php"],
    ["btnGuardaDosUno", "btnEditarDosUno", "btnActualizarDosUno", "2.1", "../Controlador/guardar_fila2_1.php", "Controlador/actualizar_fila2_1.php"],
    ["btnGuardaDosDos", "btnEditarDosDos", "btnActualizarDosDos", "2.2", "../Controlador/guardar_fila2_2.php", "Controlador/actualizar_fila2_2.php"],
    ["btnGuardaDosTres", "btnEditarDosTres", "btnActualizarDosTres", "2.3", "../Controlador/guardar_fila2_3.php", "Controlador/actualizar_fila2_3.php"],
    ["btnGuardaDosCuatro", "btnEditarDosCuatro", "btnActualizarDosCuatro", "2.4", "../Controlador/guardar_fila2_4.php", "Controlador/actualizar_fila2_4.php"],
    ["btnGuardaDosCinco", "btnEditarDosCinco", "btnActualizarDosCinco", "2.5", "../Controlador/guardar_fila2_5.php", "Controlador/actualizar_fila2_5.php"],
    ["btnGuardaDosSeis", "btnEditarDosSeis", "btnActualizarDosSeis", "2.6", "../Controlador/guardar_fila2_6.php", "Controlador/actualizar_fila2_6.php"],
    ["btnGuardaTresUno", "btnEditarTresUno", "btnActualizarTresUno", "3.1", "../Controlador/guardar_fila3_1.php", "Controlador/actualizar_fila3_1.php"],
    ["btnGuardaCuatroUno", "btnEditarCuatroUno", "btnActualizarCuatroUno", "4.1", "../Controlador/guardar_fila4_1.php", "Controlador/actualizar_fila4_1.php"],
    ["btnGuardaCuatroTres", "btnEditarCuatroTres", "btnActualizarCuatroTres", "4.3", "../Controlador/guardar_fila4_3.php", "Controlador/actualizar_fila4_3.php"],
    ["btnGuardaCincoUno", "btnEditarCincoUno", "btnActualizarCincoUno", "5.1", "../Controlador/guardar_fila5_1.php", "Controlador/actualizar_fila5_1.php"],
    ["btnGuardaCincoDos", "btnEditarCincoDos", "btnActualizarCincoDos", "5.2", "../Controlador/guardar_fila5_2.php", "Controlador/actualizar_fila5_2.php"],
    ["btnGuardaCincoTres", "btnEditarCincoTres", "btnActualizarCincoTres", "5.3", "../Controlador/guardar_fila5_3.php", "Controlador/actualizar_fila5_3.php"],
    ["btnGuardaCincoCuatro", "btnEditarCincoCuatro", "btnActualizarCincoCuatro", "5.4", "../Controlador/guardar_fila5_4.php", "Controlador/actualizar_fila5_4.php"],
    ["btnGuardaCincoCinco", "btnEditarCincoCinco", "btnActualizarCincoCinco", "5.5", "../Controlador/guardar_fila5_5.php", "Controlador/actualizar_fila5_5.php"],
    ["btnGuardaCincoSeis", "btnEditarCincoSeis", "btnActualizarCincoSeis", "5.6", "../Controlador/guardar_fila5_6.php", "Controlador/actualizar_fila5_6.php"],
    ["btnGuardaCincoSiete", "btnEditarCincoSiete", "btnActualizarCincoSiete", "5.7", "../Controlador/guardar_fila5_7.php", "Controlador/actualizar_fila5_7.php"],
    ["btnGuardaCincoOcho", "btnEditarCincoOcho", "btnActualizarCincoOcho", "5.8", "../Controlador/guardar_fila5_8.php", "Controlador/actualizar_fila5_8.php"],
    ["btnGuardaSeisUno", "btnEditarSeisUno", "btnActualizarSeisUno", "6.1", "../Controlador/guardar_fila6_1.php", "Controlador/actualizar_fila6_1.php"]
];

filas.forEach(fila => configurarGuardarFila(...fila));