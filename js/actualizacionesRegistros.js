//Bloque de campos obligatorios dejar
document.getElementById("idNombreAuditor").disabled = true;
document.getElementById("idCliente").disabled = true;
document.getElementById("idProcesoAuditado").disabled = true;
document.getElementById("idParteAuditada").disabled = true;
document.getElementById("idOperacionAuditada").disabled = true;
document.getElementById("idNave").disabled = true;
document.getElementById("idUnidad").disabled = true;
document.getElementById("idFecha").disabled = true;

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener("DOMContentLoaded", function () {
    const selectResultado = document.getElementById("idResultado1.1");
    const btnGuardar = document.getElementById("btnGuardaUnoUno");
    const btnEditar = document.getElementById("btnEditarUnoUno");
    const btnActualizar = document.getElementById("btnActualizarUnoUno");
    const observaciones = document.getElementById("idObservaciones1.1");
    const acciones = document.getElementById("idAcciones1.1");
    const fecha = document.getElementById("idFechaFila1.1");
    const dropzoneContainerTd = document.getElementById("dropzoneContainer");
    const mensajeNoArchivo = Array.from(document.querySelectorAll("td")).find(td => td.textContent.includes("No hay archivo disponible."));

    function verificarResultado() {
        const camposLlenos = observaciones.value.trim() !== "" || acciones.value.trim() !== "" || fecha.value.trim() !== "";
        
        if (selectResultado.value === "OK" && !camposLlenos) {
            btnGuardar.style.display = "none";
            observaciones.disabled = true;
            acciones.disabled = true;
            fecha.disabled = true;
            selectResultado.disabled = true;
        } else if ((selectResultado.value === "NOK" || selectResultado.value === "Pendiente") && camposLlenos) {
            btnGuardar.style.display = "none";
            observaciones.disabled = true;
            acciones.disabled = true;
            fecha.disabled = true;
            selectResultado.disabled = false;
        } else if (selectResultado.value === "Selecciona una opción" && !camposLlenos) {
            observaciones.disabled = false;
            acciones.disabled = false;
            fecha.disabled = false;
            selectResultado.disabled = false;
            if (mensajeNoArchivo && !document.getElementById("archivo_1_1")) {
                mensajeNoArchivo.innerHTML = `<div class="input-group justify-content-center">
                    <form action="subir_archivo.php" class="dropzone archivo-dropzone" id="archivo_1_1">
                        <input type="file" id="archivo_1_1_input" class="archivo-input">
                        <input type="hidden" name="numeroEmpleado" value="12345">
                    </form>
                </div>`;
            }
        } else {
            btnGuardar.style.display = "none";
            if (dropzoneContainerTd) dropzoneContainerTd.innerHTML = "";
        }
    }

    selectResultado.addEventListener("change", function () {
        if (selectResultado.value === "OK") {
            btnGuardar.style.display = "block";
        } else {
            btnGuardar.style.display = "none";
        }
    });
    
    btnGuardar.addEventListener("click", function () {
        let formData = new FormData();
        formData.append("estatus", selectResultado.value);
        
        if (selectResultado.value === "OK") {
            formData.append("fechaFila", fecha.value);
        } else {
            formData.append("observaciones", observaciones.value);
            formData.append("acciones", acciones.value);
            formData.append("fechaFila", fecha.value);
        }
        
        fetch("../guardar_fila1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Datos guardados correctamente.");
                btnGuardar.style.display = "none";
                observaciones.disabled = true;
                acciones.disabled = true;
                fecha.disabled = true;
                selectResultado.disabled = true;
            } else {
                alert("❌ Error: " + result.error);
            }
        });
    });
    
    verificarResultado();
});





// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener("DOMContentLoaded", function () {
    const selectResultado = document.getElementById("idResultado1.2");
    const btnGuardar = document.getElementById("btnGuardaUnoDos");
    const btnEditar = document.getElementById("btnEditarUnoDos");
    const btnActualizar = document.getElementById("btnActualizarUnoDos");

    const observaciones = document.getElementById("idObservaciones1.2");
    const acciones = document.getElementById("idAcciones1.2");
    const fecha = document.getElementById("idFechaFila1.2");

    // Función para verificar el valor del select y ajustar la visibilidad de los botones
    function verificarResultado() {
        if (selectResultado.value === "OK") {
            btnGuardar.style.display = "none";
            btnEditar.style.display = "none";
            btnActualizar.style.display = "none";

            observaciones.disabled = true;
            acciones.disabled = true;
            fecha.disabled = true;

        } else if (selectResultado.value === "NOK" || selectResultado.value === "Pendiente") {
            btnGuardar.style.display = "none";
            btnEditar.style.display = "block";
            btnActualizar.style.display = "none";

            observaciones.disabled = true;
            acciones.disabled = true;
            fecha.disabled = true;

        } else {
            btnGuardar.style.display = "block";
            btnEditar.style.display = "none";
            btnActualizar.style.display = "none";

            observaciones.disabled = false;
            acciones.disabled = false;
            fecha.disabled = false;
        }
    }

    // Al cambiar el valor del select
    selectResultado.addEventListener("change", verificarResultado);

    // Función para guardar la fila
    function guardarFilaUnoDos() {
        let formData = new FormData();
        formData.append("id", "1.2");
        formData.append("observaciones", observaciones.value);
        formData.append("acciones", acciones.value);
        formData.append("resultado", selectResultado.value);
        formData.append("fechaFila", fecha.value);

        fetch("../guardar_fila1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Fila guardada correctamente.");
                btnGuardar.style.display = "none";
                btnEditar.style.display = "block";

                observaciones.disabled = true;
                acciones.disabled = true;
                fecha.disabled = true;
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Función para habilitar la edición de la fila
    btnEditar.addEventListener('click', function() {
        observaciones.disabled = false;
        acciones.disabled = false;
        fecha.disabled = false;

        btnEditar.style.display = 'none';
        btnActualizar.style.display = 'block';
    });

    // Función para actualizar la fila
    btnActualizar.addEventListener('click', function() {
        const datos = {
            id: "1.2",
            observaciones: observaciones.value,
            acciones: acciones.value,
            resultado: selectResultado.value,
            fechaFila: fecha.value
        };

        fetch("../Controlador/actualizar_fila1_1.php", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Fila actualizada correctamente.");
                btnActualizar.style.display = "none";
                btnEditar.style.display = "block";

                observaciones.disabled = true;
                acciones.disabled = true;
                fecha.disabled = true;
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Al cargar la página, verificar el valor del select
    verificarResultado();

    // Evento para el botón de guardar
    btnGuardar.addEventListener("click", guardarFilaUnoDos);
});




// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function() {
    const guardarBtnUnoTres = document.getElementById('btnGuardaUnoTres');
    const editarBtnUnoTres = document.getElementById('btnEditarUnoTres');
    const actualizarBtnUnoTres = document.getElementById('btnActualizarUnoTres');
    
    function guardarFilaUnoTres() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 1.");
            return;
        }
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesUnoTres", document.getElementById("idObservaciones1.3").value);
        formData.append("accionesUnoTres", document.getElementById("idAcciones1.3").value);
        formData.append("estatusUnoTres", document.getElementById("idResultado1.3").value);
        formData.append("fechaFilaUnoTres", document.getElementById("idFechaFila1.3").value);

        let archivoInput = document.getElementById("archivo_1_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila1_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Fila 3 guardada correctamente.");
                bloquearCamposUnoTres();
                guardarBtnUnoTres.disabled = true;
                editarBtnUnoTres.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }
    function bloquearCamposUnoTres() {
        document.querySelectorAll("#idObservaciones1\\.3, #idAcciones1\\.3, #idResultado1\\.3, #idFechaFila1\\.3, #archivo_1_3_input")
        .forEach(element => {
            element.disabled = true;
        });
    }
    function habilitarCamposUnoTres() {
        document.querySelectorAll("#idObservaciones1\\.3, #idAcciones1\\.3, #idResultado1\\.3, #idFechaFila1\\.3, #archivo_1_3_input")
        .forEach(element => {
            element.disabled = false;
        });
    }
    
    editarBtnUnoTres.addEventListener('click', function() {
        habilitarCamposUnoTres();
        editarBtnUnoTres.style.display = 'none';
        actualizarBtnUnoTres.style.display = 'inline';
    });
    
    actualizarBtnUnoTres.addEventListener('click', function() {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesUnoTres", document.getElementById("idObservaciones1.3").value);
        formData.append("accionesUnoTres", document.getElementById("idAcciones1.3").value);
        formData.append("estatusUnoTres", document.getElementById("idResultado1.3").value);
        formData.append("fechaFilaUnoTres", document.getElementById("idFechaFila1.3").value);

        let archivoInput = document.getElementById("archivo_1_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila1_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 3 actualizada correctamente.");
                    bloquearCamposUnoTres();
                    actualizarBtnUnoTres.style.display = 'none';
                    editarBtnUnoTres.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });
    guardarBtnUnoTres.addEventListener("click", guardarFilaUnoTres);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnDosUno = document.getElementById('btnGuardaDosUno');
    const editarBtnDosUno = document.getElementById('btnEditarDosUno');
    const actualizarBtnDosUno = document.getElementById('btnActualizarDosUno');

    function guardarFilaDosUno() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 1.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosUno", document.getElementById("idObservaciones2.1").value);
        formData.append("accionesDosUno", document.getElementById("idAcciones2.1").value);
        formData.append("estatusDosUno", document.getElementById("idResultado2.1").value);
        formData.append("fechaFilaDosUno", document.getElementById("idFechaFila2.1").value);

        let archivoInput = document.getElementById("archivo_2_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila2_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Fila 2.1 guardada correctamente.");
                bloquearCampos();
                guardarBtnDosUno.style.display = 'none';
                editarBtnDosUno.style.display = 'inline';
                editarBtnDosUno.disabled = false; // Habilita el botón de editar
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCampos() {
        document.querySelectorAll("#idObservaciones2\\.1, #idAcciones2\\.1, #idResultado2\\.1, #idFechaFila2\\.1, #archivo_2_1_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposDosUno() {
        document.querySelectorAll("#idObservaciones2\\.1, #idAcciones2\\.1, #idResultado2\\.1, #idFechaFila2\\.1, #archivo_2_1_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnDosUno.addEventListener('click', function () {
        habilitarCamposDosUno();
        editarBtnDosUno.style.display = 'none';
        actualizarBtnDosUno.style.display = 'inline';
    });

    actualizarBtnDosUno.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosUno", document.getElementById("idObservaciones2.1").value);
        formData.append("accionesDosUno", document.getElementById("idAcciones2.1").value);
        formData.append("estatusDosUno", document.getElementById("idResultado2.1").value);
        formData.append("fechaFilaDosUno", document.getElementById("idFechaFila2.1").value);

        let archivoInput = document.getElementById("archivo_2_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila2_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 2.1 actualizada correctamente.");
                    bloquearCampos();
                    actualizarBtnDosUno.style.display = 'none';
                    editarBtnDosUno.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnDosUno.addEventListener("click", guardarFilaDosUno);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnDosDos = document.getElementById('btnGuardaDosDos');
    const editarBtnDosDos = document.getElementById('btnEditarDosDos');
    const actualizarBtnDosDos = document.getElementById('btnActualizarDosDos');

    function guardarFilaDosDos() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 1.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosDos", document.getElementById("idObservaciones2.2").value);
        formData.append("accionesDosDos", document.getElementById("idAcciones2.2").value);
        formData.append("estatusDosDos", document.getElementById("idResultado2.2").value);
        formData.append("fechaFilaDosDos", document.getElementById("idFechaFila2.2").value);

        let archivoInput = document.getElementById("archivo_2_2_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila2_2.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Fila 2.2 guardada correctamente.");
                bloquearCamposDosDos();
                guardarBtnDosDos.style.display = 'none';
                editarBtnDosDos.style.display = 'inline';
                editarBtnDosDos.disabled = false; // Habilita el botón de editar
            } else {
                // alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposDosDos() {
        document.querySelectorAll("#idObservaciones2\\.2, #idAcciones2\\.2, #idResultado2\\.2, #idFechaFila2\\.2, #archivo_2_2_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposDosDos() {
        document.querySelectorAll("#idObservaciones2\\.2, #idAcciones2\\.2, #idResultado2\\.2, #idFechaFila2\\.2, #archivo_2_2_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnDosDos.addEventListener('click', function () {
        habilitarCamposDosDos();
        editarBtnDosDos.style.display = 'none';
        actualizarBtnDosDos.style.display = 'inline';
    });

    actualizarBtnDosDos.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosDos", document.getElementById("idObservaciones2.2").value);
        formData.append("accionesDosDos", document.getElementById("idAcciones2.2").value);
        formData.append("estatusDosDos", document.getElementById("idResultado2.2").value);
        formData.append("fechaFilaDosDos", document.getElementById("idFechaFila2.2").value);

        let archivoInput = document.getElementById("archivo_2_2_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila2_2.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 2.2 actualizada correctamente.");
                    bloquearCamposDosDos();
                    actualizarBtnDosDos.style.display = 'none';
                    editarBtnDosDos.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnDosDos.addEventListener("click", guardarFilaDosDos);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnDosTres = document.getElementById('btnGuardaDosTres');
    const editarBtnDosTres = document.getElementById('btnEditarDosTres');
    const actualizarBtnDosTres = document.getElementById('btnActualizarDosTres');

    function guardarFilaDosTres() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 2.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosTres", document.getElementById("idObservaciones2.3").value);
        formData.append("accionesDosTres", document.getElementById("idAcciones2.3").value);
        formData.append("estatusDosTres", document.getElementById("idResultado2.3").value);
        formData.append("fechaFilaDosTres", document.getElementById("idFechaFila2.3").value);

        let archivoInput = document.getElementById("archivo_2_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila2_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("✅ Fila 2.3 guardada correctamente.");
                bloquearCampos();
                guardarBtnDosTres.style.display = 'none';
                editarBtnDosTres.style.display = 'inline';
                editarBtnDosTres.disabled = false; // Habilita el botón de editar
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCampos() {
        document.querySelectorAll("#idObservaciones2\\.3, #idAcciones2\\.3, #idResultado2\\.3, #idFechaFila2\\.3, #archivo_2_3_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposDosTres() {
        document.querySelectorAll("#idObservaciones2\\.3, #idAcciones2\\.3, #idResultado2\\.3, #idFechaFila2\\.3, #archivo_2_3_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnDosTres.addEventListener('click', function () {
        habilitarCamposDosTres();
        editarBtnDosTres.style.display = 'none';
        actualizarBtnDosTres.style.display = 'inline';
    });

    actualizarBtnDosTres.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosTres", document.getElementById("idObservaciones2.3").value);
        formData.append("accionesDosTres", document.getElementById("idAcciones2.3").value);
        formData.append("estatusDosTres", document.getElementById("idResultado2.3").value);
        formData.append("fechaFilaDosTres", document.getElementById("idFechaFila2.3").value);

        let archivoInput = document.getElementById("archivo_2_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila2_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 2.3 actualizada correctamente.");
                    bloquearCampos();
                    actualizarBtnDosTres.style.display = 'none';
                    editarBtnDosTres.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnDosTres.addEventListener("click", guardarFilaDosTres);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnDosCuatro = document.getElementById('btnGuardaDosCuatro');
    const editarBtnDosCuatro = document.getElementById('btnEditarDosCuatro');
    const actualizarBtnDosCuatro = document.getElementById('btnActualizarDosCuatro');

    function guardarFilaDosCuatro() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 1.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);  // Agregamos la verificación de idGenerado
        formData.append("observacionesDosCuatro", document.getElementById("idObservaciones2.4").value);
        formData.append("accionesDosCuatro", document.getElementById("idAcciones2.4").value);
        formData.append("estatusDosCuatro", document.getElementById("idResultado2.4").value);
        formData.append("fechaFilaDosCuatro", document.getElementById("idFechaFila2.4").value);

        let archivoInput = document.getElementById("archivo_2_4_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila2_4.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);  // Muestra la respuesta completa del servidor
            if (result.success) {
                alert("✅ Fila 2.4 guardada correctamente.");
                bloquearCamposDosCuatro();
                guardarBtnDosCuatro.style.display = 'none';
                editarBtnDosCuatro.style.display = 'inline';
                editarBtnDosCuatro.disabled = false; // Habilita el botón de editar
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposDosCuatro() {
        document.querySelectorAll("#idObservaciones2\\.4, #idAcciones2\\.4, #idResultado2\\.4, #idFechaFila2\\.4, #archivo_2_4_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposDosCuatro() {
        document.querySelectorAll("#idObservaciones2\\.4, #idAcciones2\\.4, #idResultado2\\.4, #idFechaFila2\\.4, #archivo_2_4_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnDosCuatro.addEventListener('click', function () {
        habilitarCamposDosCuatro();
        editarBtnDosCuatro.style.display = 'none';
        actualizarBtnDosCuatro.style.display = 'inline';
    });

    actualizarBtnDosCuatro.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);  // Aseguramos que el ID esté presente también al actualizar
        formData.append("observacionesDosCuatro", document.getElementById("idObservaciones2.4").value);
        formData.append("accionesDosCuatro", document.getElementById("idAcciones2.4").value);
        formData.append("estatusDosCuatro", document.getElementById("idResultado2.4").value);
        formData.append("fechaFilaDosCuatro", document.getElementById("idFechaFila2.4").value);

        let archivoInput = document.getElementById("archivo_2_4_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila2_4.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 2.4 actualizada correctamente.");
                    bloquearCamposDosCuatro();
                    actualizarBtnDosCuatro.style.display = 'none';
                    editarBtnDosCuatro.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnDosCuatro.addEventListener("click", guardarFilaDosCuatro);
});
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnDosCinco = document.getElementById('btnGuardaDosCinco');
    const editarBtnDosCinco = document.getElementById('btnEditarDosCinco');
    const actualizarBtnDosCinco = document.getElementById('btnActualizarDosCinco');

    function guardarFilaDosCinco() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 1.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosCinco", document.getElementById("idObservaciones2.5").value);
        formData.append("accionesDosCinco", document.getElementById("idAcciones2.5").value);
        formData.append("estatusDosCinco", document.getElementById("idResultado2.5").value);
        formData.append("fechaFilaDosCinco", document.getElementById("idFechaFila2.5").value);

        let archivoInput = document.getElementById("archivo_2_5_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila2_5.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 2.5 guardada correctamente.");
                bloquearCamposDosCinco();
                guardarBtnDosCinco.style.display = 'none';
                editarBtnDosCinco.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposDosCinco() {
        document.querySelectorAll("#idObservaciones2\\.5, #idAcciones2\\.5, #idResultado2\\.5, #idFechaFila2\\.5, #archivo_2_5_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposDosCinco() {
        document.querySelectorAll("#idObservaciones2\\.5, #idAcciones2\\.5, #idResultado2\\.5, #idFechaFila2\\.5, #archivo_2_5_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnDosCinco.addEventListener('click', function () {
        habilitarCamposDosCinco();
        editarBtnDosCinco.style.display = 'none';
        actualizarBtnDosCinco.style.display = 'inline';
    });

    actualizarBtnDosCinco.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosCinco", document.getElementById("idObservaciones2.5").value);
        formData.append("accionesDosCinco", document.getElementById("idAcciones2.5").value);
        formData.append("estatusDosCinco", document.getElementById("idResultado2.5").value);
        formData.append("fechaFilaDosCinco", document.getElementById("idFechaFila2.5").value);

        let archivoInput = document.getElementById("archivo_2_5_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila2_5.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 2.5 actualizada correctamente.");
                    bloquearCamposDosCinco();
                    actualizarBtnDosCinco.style.display = 'none';
                    editarBtnDosCinco.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnDosCinco.addEventListener("click", guardarFilaDosCinco);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnDosSeis = document.getElementById('btnGuardaDosSeis');
    const editarBtnDosSeis = document.getElementById('btnEditarDosSeis');
    const actualizarBtnDosSeis = document.getElementById('btnActualizarDosSeis');

    function guardarFilaDosSeis() {
        if (!idGenerado) {
            alert("Debes guardar primero la Fila 1.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosSeis", document.getElementById("idObservaciones2.6").value);
        formData.append("accionesDosSeis", document.getElementById("idAcciones2.6").value);
        formData.append("estatusDosSeis", document.getElementById("idResultado2.6").value);
        formData.append("fechaFilaDosSeis", document.getElementById("idFechaFila2.6").value);

        let archivoInput = document.getElementById("archivo_2_6_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila2_6.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 2.6 guardada correctamente.");
                bloquearCamposDosSeis();
                guardarBtnDosSeis.style.display = 'none';
                editarBtnDosSeis.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposDosSeis() {
        document.querySelectorAll("#idObservaciones2\\.6, #idAcciones2\\.6, #idResultado2\\.6, #idFechaFila2\\.6, #archivo_2_6_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposDosSeis() {
        document.querySelectorAll("#idObservaciones2\\.6, #idAcciones2\\.6, #idResultado2\\.6, #idFechaFila2\\.6, #archivo_2_6_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnDosSeis.addEventListener('click', function () {
        habilitarCamposDosSeis();
        editarBtnDosSeis.style.display = 'none';
        actualizarBtnDosSeis.style.display = 'inline';
    });

    actualizarBtnDosSeis.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesDosSeis", document.getElementById("idObservaciones2.6").value);
        formData.append("accionesDosSeis", document.getElementById("idAcciones2.6").value);
        formData.append("estatusDosSeis", document.getElementById("idResultado2.6").value);
        formData.append("fechaFilaDosSeis", document.getElementById("idFechaFila2.6").value);

        let archivoInput = document.getElementById("archivo_2_6_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila2_6.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 2.6 actualizada correctamente.");
                    bloquearCamposDosSeis();
                    actualizarBtnDosSeis.style.display = 'none';
                    editarBtnDosSeis.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnDosSeis.addEventListener("click", guardarFilaDosSeis);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnTresUno = document.getElementById('btnGuardaTresUno');
    const editarBtnTresUno = document.getElementById('btnEditarTresUno');
    const actualizarBtnTresUno = document.getElementById('btnActualizarTresUno');

    function guardarFilaTresUno() {
        if (!idGenerado) {
            alert("Debes guardar primero la fila anterior.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesTresUno", document.getElementById("idObservaciones3.1").value);
        formData.append("accionesTresUno", document.getElementById("idAcciones3.1").value);
        formData.append("estatusTresUno", document.getElementById("idResultado3.1").value);
        formData.append("fechaFilaTresUno", document.getElementById("idFechaFila3.1").value);

        let archivoInput = document.getElementById("archivo_3_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila3_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 3.1 guardada correctamente.");
                bloquearCamposTresUno();
                guardarBtnTresUno.style.display = 'none';
                editarBtnTresUno.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposTresUno() {
        document.querySelectorAll("#idObservaciones3\\.1, #idAcciones3\\.1, #idResultado3\\.1, #idFechaFila3\\.1, #archivo_3_1_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposTresUno() {
        document.querySelectorAll("#idObservaciones3\\.1, #idAcciones3\\.1, #idResultado3\\.1, #idFechaFila3\\.1, #archivo_3_1_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnTresUno.addEventListener('click', function () {
        habilitarCamposTresUno();
        editarBtnTresUno.style.display = 'none';
        actualizarBtnTresUno.style.display = 'inline';
    });

    actualizarBtnTresUno.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesTresUno", document.getElementById("idObservaciones3.1").value);
        formData.append("accionesTresUno", document.getElementById("idAcciones3.1").value);
        formData.append("estatusTresUno", document.getElementById("idResultado3.1").value);
        formData.append("fechaFilaTresUno", document.getElementById("idFechaFila3.1").value);

        let archivoInput = document.getElementById("archivo_3_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila3_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 3.1 actualizada correctamente.");
                    bloquearCamposTresUno();
                    actualizarBtnTresUno.style.display = 'none';
                    editarBtnTresUno.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnTresUno.addEventListener("click", guardarFilaTresUno);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCuatroUno = document.getElementById('btnGuardaCuatroUno');
    const editarBtnCuatroUno = document.getElementById('btnEditarCuatroUno');
    const actualizarBtnCuatroUno = document.getElementById('btnActualizarCuatroUno');


    function guardarFilaCuatroUno() {
        if (!idGenerado) {
            alert("Debes guardar primero la fila anterior.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCuatroUno", document.getElementById("idObservaciones4.1").value);
        formData.append("accionesCuatroUno", document.getElementById("idAcciones4.1").value);
        formData.append("estatusCuatroUno", document.getElementById("idResultado4.1").value);
        formData.append("fechaFilaCuatroUno", document.getElementById("idFechaFila4.1").value);

        let archivoInput = document.getElementById("archivo_4_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila4_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 4.1 guardada correctamente.");
                bloquearCamposCuatroUno();
                guardarBtnCuatroUno.style.display = 'none';
                editarBtnCuatroUno.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCuatroUno() {
        document.querySelectorAll("#idObservaciones4\\.1, #idAcciones4\\.1, #idResultado4\\.1, #idFechaFila4\\.1, #archivo_4_1_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCuatroUno() {
        document.querySelectorAll("#idObservaciones4\\.1, #idAcciones4\\.1, #idResultado4\\.1, #idFechaFila4\\.1, #archivo_4_1_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCuatroUno.addEventListener('click', function () {
        habilitarCamposCuatroUno();
        editarBtnCuatroUno.style.display = 'none';
        actualizarBtnCuatroUno.style.display = 'inline';
    });

    actualizarBtnCuatroUno.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCuatroUno", document.getElementById("idObservaciones4.1").value);
        formData.append("accionesCuatroUno", document.getElementById("idAcciones4.1").value);
        formData.append("estatusCuatroUno", document.getElementById("idResultado4.1").value);
        formData.append("fechaFilaCuatroUno", document.getElementById("idFechaFila4.1").value);

        let archivoInput = document.getElementById("archivo_4_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila4_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 4.1 actualizada correctamente.");
                    bloquearCamposCuatroUno();
                    actualizarBtnCuatroUno.style.display = 'none';
                    editarBtnCuatroUno.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnCuatroUno.addEventListener("click", guardarFilaCuatroUno);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCuatroDos = document.getElementById('btnGuardaCuatroDos');
    const editarBtnCuatroDos = document.getElementById('btnEditarCuatroDos');
    const actualizarBtnCuatroDos = document.getElementById('btnActualizarCuatroDos');

    function guardarFilaCuatroDos() {
        if (!idGenerado) {
            alert("Debes guardar primero la fila anterior.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCuatroDos", document.getElementById("idObservaciones4.2").value);
        formData.append("accionesCuatroDos", document.getElementById("idAcciones4.2").value);
        formData.append("estatusCuatroDos", document.getElementById("idResultado4.2").value);
        formData.append("fechaFilaCuatroDos", document.getElementById("idFechaFila4.2").value);

        let archivoInput = document.getElementById("archivo_4_2_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila4_2.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 4.2 guardada correctamente.");
                bloquearCamposCuatroDos();
                guardarBtnCuatroDos.style.display = 'none';
                editarBtnCuatroDos.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCuatroDos() {
        document.querySelectorAll("#idObservaciones4\\.2, #idAcciones4\\.2, #idResultado4\\.2, #idFechaFila4\\.2, #archivo_4_2_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCuatroDos() {
        document.querySelectorAll("#idObservaciones4\\.2, #idAcciones4\\.2, #idResultado4\\.2, #idFechaFila4\\.2, #archivo_4_2_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCuatroDos.addEventListener('click', function () {
        habilitarCamposCuatroDos();
        editarBtnCuatroDos.style.display = 'none';
        actualizarBtnCuatroDos.style.display = 'inline';
    });

    actualizarBtnCuatroDos.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCuatroDos", document.getElementById("idObservaciones4.2").value);
        formData.append("accionesCuatroDos", document.getElementById("idAcciones4.2").value);
        formData.append("estatusCuatroDos", document.getElementById("idResultado4.2").value);
        formData.append("fechaFilaCuatroDos", document.getElementById("idFechaFila4.2").value);

        let archivoInput = document.getElementById("archivo_4_2_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila4_2.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 4.2 actualizada correctamente.");
                    bloquearCamposCuatroDos();
                    actualizarBtnCuatroDos.style.display = 'none';
                    editarBtnCuatroDos.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnCuatroDos.addEventListener("click", guardarFilaCuatroDos);
});
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCuatroTres = document.getElementById('btnGuardaCuatroTres');
    const editarBtnCuatroTres = document.getElementById('btnEditarCuatroTres');
    const actualizarBtnCuatroTres = document.getElementById('btnActualizarCuatroTres');

    function guardarFilaCuatroTres() {
        if (!idGenerado) {
            alert("Debes guardar primero la fila anterior.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCuatroTres", document.getElementById("idObservaciones4.3").value);
        formData.append("accionesCuatroTres", document.getElementById("idAcciones4.3").value);
        formData.append("estatusCuatroTres", document.getElementById("idResultado4.3").value);
        formData.append("fechaFilaCuatroTres", document.getElementById("idFechaFila4.3").value);

        let archivoInput = document.getElementById("archivo_4_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila4_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 4.3 guardada correctamente.");
                bloquearCamposCuatroTres();
                guardarBtnCuatroTres.style.display = 'none';
                editarBtnCuatroTres.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCuatroTres() {
        document.querySelectorAll("#idObservaciones4\\.3, #idAcciones4\\.3, #idResultado4\\.3, #idFechaFila4\\.3, #archivo_4_3_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCuatroTres() {
        document.querySelectorAll("#idObservaciones4\\.3, #idAcciones4\\.3, #idResultado4\\.3, #idFechaFila4\\.3, #archivo_4_3_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCuatroTres.addEventListener('click', function () {
        habilitarCamposCuatroTres();
        editarBtnCuatroTres.style.display = 'none';
        actualizarBtnCuatroTres.style.display = 'inline';
    });

    actualizarBtnCuatroTres.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCuatroTres", document.getElementById("idObservaciones4.3").value);
        formData.append("accionesCuatroTres", document.getElementById("idAcciones4.3").value);
        formData.append("estatusCuatroTres", document.getElementById("idResultado4.3").value);
        formData.append("fechaFilaCuatroTres", document.getElementById("idFechaFila4.3").value);

        let archivoInput = document.getElementById("archivo_4_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila4_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 4.3 actualizada correctamente.");
                    bloquearCamposCuatroTres();
                    actualizarBtnCuatroTres.style.display = 'none';
                    editarBtnCuatroTres.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnCuatroTres.addEventListener("click", guardarFilaCuatroTres);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoUno = document.getElementById('btnGuardaCincoUno');
    const editarBtnCincoUno = document.getElementById('btnEditarCincoUno');
    const actualizarBtnCincoUno = document.getElementById('btnActualizarCincoUno');

    function guardarFilaCincoUno() {
        if (!idGenerado) {
            alert("Debes guardar primero la fila anterior.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);  // Asegúrate de agregar el ID si es necesario
        formData.append("observacionesCincoUno", document.getElementById("idObservaciones5.1").value);
        formData.append("accionesCincoUno", document.getElementById("idAcciones5.1").value);
        formData.append("estatusCincoUno", document.getElementById("idResultado5.1").value);
        formData.append("fechaFilaCincoUno", document.getElementById("idFechaFila5.1").value);

        let archivoInput = document.getElementById("archivo_5_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.1 guardada correctamente.");
                bloquearCamposCincoUno();
                guardarBtnCincoUno.style.display = 'none';
                editarBtnCincoUno.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoUno() {
        document.querySelectorAll("#idObservaciones5\\.1, #idAcciones5\\.1, #idResultado5\\.1, #idFechaFila5\\.1, #archivo_5_1_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoUno() {
        document.querySelectorAll("#idObservaciones5\\.1, #idAcciones5\\.1, #idResultado5\\.1, #idFechaFila5\\.1, #archivo_5_1_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoUno.addEventListener('click', function () {
        habilitarCamposCincoUno();
        editarBtnCincoUno.style.display = 'none';
        actualizarBtnCincoUno.style.display = 'inline';
    });

    actualizarBtnCincoUno.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado); // Asegúrate de enviar el ID si es necesario
        formData.append("observacionesCincoUno", document.getElementById("idObservaciones5.1").value);
        formData.append("accionesCincoUno", document.getElementById("idAcciones5.1").value);
        formData.append("estatusCincoUno", document.getElementById("idResultado5.1").value);
        formData.append("fechaFilaCincoUno", document.getElementById("idFechaFila5.1").value);

        let archivoInput = document.getElementById("archivo_5_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.1 actualizada correctamente.");
                    bloquearCamposCincoUno();
                    actualizarBtnCincoUno.style.display = 'none';
                    editarBtnCincoUno.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoUno.addEventListener("click", guardarFilaCincoUno);
});
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoDos = document.getElementById('btnGuardaCincoDos');
    const editarBtnCincoDos = document.getElementById('btnEditarCincoDos');
    const actualizarBtnCincoDos = document.getElementById('btnActualizarCincoDos');

    function guardarFilaCincoDos() {
        if (!idGenerado) {
            alert("Debes guardar primero la fila anterior.");
            return;
        }

        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCincoDos", document.getElementById("idObservaciones5.2").value);
        formData.append("accionesCincoDos", document.getElementById("idAcciones5.2").value);
        formData.append("estatusCincoDos", document.getElementById("idResultado5.2").value);
        formData.append("fechaFilaCincoDos", document.getElementById("idFechaFila5.2").value);

        let archivoInput = document.getElementById("archivo_5_2_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_2.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.2 guardada correctamente.");
                bloquearCamposCincoDos();
                guardarBtnCincoDos.style.display = 'none';
                editarBtnCincoDos.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoDos() {
        document.querySelectorAll("#idObservaciones5\\.2, #idAcciones5\\.2, #idResultado5\\.2, #idFechaFila5\\.2, #archivo_5_2_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoDos() {
        document.querySelectorAll("#idObservaciones5\\.2, #idAcciones5\\.2, #idResultado5\\.2, #idFechaFila5\\.2, #archivo_5_2_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoDos.addEventListener('click', function () {
        habilitarCamposCincoDos();
        editarBtnCincoDos.style.display = 'none';
        actualizarBtnCincoDos.style.display = 'inline';
    });

    actualizarBtnCincoDos.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCincoDos", document.getElementById("idObservaciones5.2").value);
        formData.append("accionesCincoDos", document.getElementById("idAcciones5.2").value);
        formData.append("estatusCincoDos", document.getElementById("idResultado5.2").value);
        formData.append("fechaFilaCincoDos", document.getElementById("idFechaFila5.2").value);

        let archivoInput = document.getElementById("archivo_5_2_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_2.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.2 actualizada correctamente.");
                    bloquearCamposCincoDos();
                    actualizarBtnCincoDos.style.display = 'none';
                    editarBtnCincoDos.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        });
    });

    guardarBtnCincoDos.addEventListener("click", guardarFilaCincoDos);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoTres = document.getElementById('btnGuardaCincoTres');
    const editarBtnCincoTres = document.getElementById('btnEditarCincoTres');
    const actualizarBtnCincoTres = document.getElementById('btnActualizarCincoTres');

    function guardarFilaCincoTres() {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCincoTres", document.getElementById("idObservaciones5.3").value);
        formData.append("accionesCincoTres", document.getElementById("idAcciones5.3").value);
        formData.append("estatusCincoTres", document.getElementById("idResultado5.3").value);
        formData.append("fechaFilaCincoTres", document.getElementById("idFechaFila5.3").value);

        let archivoInput = document.getElementById("archivo_5_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.3 guardada correctamente.");
                bloquearCamposCincoTres();
                guardarBtnCincoTres.style.display = 'none';
                editarBtnCincoTres.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoTres() {
        document.querySelectorAll("#idObservaciones5\\.3, #idAcciones5\\.3, #idResultado5\\.3, #idFechaFila5\\.3, #archivo_5_3_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoTres() {
        document.querySelectorAll("#idObservaciones5\\.3, #idAcciones5\\.3, #idResultado5\\.3, #idFechaFila5\\.3, #archivo_5_3_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoTres.addEventListener('click', function () {
        habilitarCamposCincoTres();
        editarBtnCincoTres.style.display = 'none';
        actualizarBtnCincoTres.style.display = 'inline';
    });

    actualizarBtnCincoTres.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCincoTres", document.getElementById("idObservaciones5.3").value);
        formData.append("accionesCincoTres", document.getElementById("idAcciones5.3").value);
        formData.append("estatusCincoTres", document.getElementById("idResultado5.3").value);
        formData.append("fechaFilaCincoTres", document.getElementById("idFechaFila5.3").value);

        let archivoInput = document.getElementById("archivo_5_3_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_3.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.3 actualizada correctamente.");
                    bloquearCamposCincoTres();
                    actualizarBtnCincoTres.style.display = 'none';
                    editarBtnCincoTres.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoTres.addEventListener("click", guardarFilaCincoTres);
});





// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoCuatro = document.getElementById('btnGuardaCincoCuatro');
    const editarBtnCincoCuatro = document.getElementById('btnEditarCincoCuatro');
    const actualizarBtnCincoCuatro = document.getElementById('btnActualizarCincoCuatro');

    function guardarFilaCincoCuatro() {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCincoCuatro", document.getElementById("idObservaciones5.4").value);
        formData.append("accionesCincoCuatro", document.getElementById("idAcciones5.4").value);
        formData.append("estatusCincoCuatro", document.getElementById("idResultado5.4").value);
        formData.append("fechaFilaCincoCuatro", document.getElementById("idFechaFila5.4").value);

        let archivoInput = document.getElementById("archivo_5_4_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_4.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.4 guardada correctamente.");
                bloquearCamposCincoCuatro();
                guardarBtnCincoCuatro.style.display = 'none';
                editarBtnCincoCuatro.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoCuatro() {
        document.querySelectorAll("#idObservaciones5\\.4, #idAcciones5\\.4, #idResultado5\\.4, #idFechaFila5\\.4, #archivo_5_4_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoCuatro() {
        document.querySelectorAll("#idObservaciones5\\.4, #idAcciones5\\.4, #idResultado5\\.4, #idFechaFila5\\.4, #archivo_5_4_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoCuatro.addEventListener('click', function () {
        habilitarCamposCincoCuatro();
        editarBtnCincoCuatro.style.display = 'none';
        actualizarBtnCincoCuatro.style.display = 'inline';
    });

    actualizarBtnCincoCuatro.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);
        formData.append("observacionesCincoCuatro", document.getElementById("idObservaciones5.4").value);
        formData.append("accionesCincoCuatro", document.getElementById("idAcciones5.4").value);
        formData.append("estatusCincoCuatro", document.getElementById("idResultado5.4").value);
        formData.append("fechaFilaCincoCuatro", document.getElementById("idFechaFila5.4").value);

        let archivoInput = document.getElementById("archivo_5_4_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_4.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.4 actualizada correctamente.");
                    bloquearCamposCincoCuatro();
                    actualizarBtnCincoCuatro.style.display = 'none';
                    editarBtnCincoCuatro.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoCuatro.addEventListener("click", guardarFilaCincoCuatro);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoCinco = document.getElementById('btnGuardaCincoCinco');
    const editarBtnCincoCinco = document.getElementById('btnEditarCincoCinco');
    const actualizarBtnCincoCinco = document.getElementById('btnActualizarCincoCinco');

    function guardarFilaCincoCinco() {
        let formData = new FormData();
        formData.append("id", idGenerado); // Asegúrate de definir 'idGenerado' previamente
        formData.append("observacionesCincoCinco", document.getElementById("idObservaciones5.5").value);
        formData.append("accionesCincoCinco", document.getElementById("idAcciones5.5").value);
        formData.append("estatusCincoCinco", document.getElementById("idResultado5.5").value);
        formData.append("fechaFilaCincoCinco", document.getElementById("idFechaFila5.5").value);

        let archivoInput = document.getElementById("archivo_5_5_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_5.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.5 guardada correctamente.");
                bloquearCamposCincoCinco();
                guardarBtnCincoCinco.style.display = 'none';
                editarBtnCincoCinco.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoCinco() {
        document.querySelectorAll("#idObservaciones5\\.5, #idAcciones5\\.5, #idResultado5\\.5, #idFechaFila5\\.5, #archivo_5_5_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoCinco() {
        document.querySelectorAll("#idObservaciones5\\.5, #idAcciones5\\.5, #idResultado5\\.5, #idFechaFila5\\.5, #archivo_5_5_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoCinco.addEventListener('click', function () {
        habilitarCamposCincoCinco();
        editarBtnCincoCinco.style.display = 'none';
        actualizarBtnCincoCinco.style.display = 'inline';
    });

    actualizarBtnCincoCinco.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado); // Asegúrate de definir 'idGenerado' previamente
        formData.append("observacionesCincoCinco", document.getElementById("idObservaciones5.5").value);
        formData.append("accionesCincoCinco", document.getElementById("idAcciones5.5").value);
        formData.append("estatusCincoCinco", document.getElementById("idResultado5.5").value);
        formData.append("fechaFilaCincoCinco", document.getElementById("idFechaFila5.5").value);

        let archivoInput = document.getElementById("archivo_5_5_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_5.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.5 actualizada correctamente.");
                    bloquearCamposCincoCinco();
                    actualizarBtnCincoCinco.style.display = 'none';
                    editarBtnCincoCinco.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoCinco.addEventListener("click", guardarFilaCincoCinco);
});

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoSeis = document.getElementById('btnGuardaCincoSeis');
    const editarBtnCincoSeis = document.getElementById('btnEditarCincoSeis');
    const actualizarBtnCincoSeis = document.getElementById('btnActualizarCincoSeis');

    function guardarFilaCincoSeis() {
        let formData = new FormData();
        formData.append("id", idGenerado); // Asegúrate de definir 'idGenerado' previamente
        formData.append("observacionesCincoSeis", document.getElementById("idObservaciones5.6").value);
        formData.append("accionesCincoSeis", document.getElementById("idAcciones5.6").value);
        formData.append("estatusCincoSeis", document.getElementById("idResultado5.6").value);
        formData.append("fechaFilaCincoSeis", document.getElementById("idFechaFila5.6").value);

        let archivoInput = document.getElementById("archivo_5_6_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_6.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.6 guardada correctamente.");
                bloquearCamposCincoSeis();
                guardarBtnCincoSeis.style.display = 'none';
                editarBtnCincoSeis.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoSeis() {
        document.querySelectorAll("#idObservaciones5\\.6, #idAcciones5\\.6, #idResultado5\\.6, #idFechaFila5\\.6, #archivo_5_6_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoSeis() {
        document.querySelectorAll("#idObservaciones5\\.6, #idAcciones5\\.6, #idResultado5\\.6, #idFechaFila5\\.6, #archivo_5_6_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoSeis.addEventListener('click', function () {
        habilitarCamposCincoSeis();
        editarBtnCincoSeis.style.display = 'none';
        actualizarBtnCincoSeis.style.display = 'inline';
    });

    actualizarBtnCincoSeis.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado); // Asegúrate de definir 'idGenerado' previamente
        formData.append("observacionesCincoSeis", document.getElementById("idObservaciones5.6").value);
        formData.append("accionesCincoSeis", document.getElementById("idAcciones5.6").value);
        formData.append("estatusCincoSeis", document.getElementById("idResultado5.6").value);
        formData.append("fechaFilaCincoSeis", document.getElementById("idFechaFila5.6").value);

        let archivoInput = document.getElementById("archivo_5_6_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_6.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.6 actualizada correctamente.");
                    bloquearCamposCincoSeis();
                    actualizarBtnCincoSeis.style.display = 'none';
                    editarBtnCincoSeis.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoSeis.addEventListener("click", guardarFilaCincoSeis);
});





// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoSiete = document.getElementById('btnGuardaCincoSiete');
    const editarBtnCincoSiete = document.getElementById('btnEditarCincoSiete');
    const actualizarBtnCincoSiete = document.getElementById('btnActualizarCincoSiete');

    function guardarFilaCincoSiete() {
        let formData = new FormData();
        formData.append("id", idGenerado);

        formData.append("observacionesCincoSiete", document.getElementById("idObservaciones5.7").value);
        formData.append("accionesCincoSiete", document.getElementById("idAcciones5.7").value);
        formData.append("estatusCincoSiete", document.getElementById("idResultado5.7").value);
        formData.append("fechaFilaCincoSiete", document.getElementById("idFechaFila5.7").value);

        let archivoInput = document.getElementById("archivo_5_7_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_7.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.7 guardada correctamente.");
                bloquearCamposCincoSiete();
                guardarBtnCincoSiete.style.display = 'none';
                editarBtnCincoSiete.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoSiete() {
        document.querySelectorAll("#idObservaciones5\\.7, #idAcciones5\\.7, #idResultado5\\.7, #idFechaFila5\\.7, #archivo_5_7_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoSiete() {
        document.querySelectorAll("#idObservaciones5\\.7, #idAcciones5\\.7, #idResultado5\\.7, #idFechaFila5\\.7, #archivo_5_7_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoSiete.addEventListener('click', function () {
        habilitarCamposCincoSiete();
        editarBtnCincoSiete.style.display = 'none';
        actualizarBtnCincoSiete.style.display = 'inline';
    });

    actualizarBtnCincoSiete.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);

        formData.append("observacionesCincoSiete", document.getElementById("idObservaciones5.7").value);
        formData.append("accionesCincoSiete", document.getElementById("idAcciones5.7").value);
        formData.append("estatusCincoSiete", document.getElementById("idResultado5.7").value);
        formData.append("fechaFilaCincoSiete", document.getElementById("idFechaFila5.7").value);

        let archivoInput = document.getElementById("archivo_5_7_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_7.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.7 actualizada correctamente.");
                    bloquearCamposCincoSiete();
                    actualizarBtnCincoSiete.style.display = 'none';
                    editarBtnCincoSiete.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoSiete.addEventListener("click", guardarFilaCincoSiete);
});
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnCincoOcho = document.getElementById('btnGuardaCincoOcho');
    const editarBtnCincoOcho = document.getElementById('btnEditarCincoOcho');
    const actualizarBtnCincoOcho = document.getElementById('btnActualizarCincoOcho');

    function guardarFilaCincoOcho() {
        let formData = new FormData();
        formData.append("id", idGenerado);

        formData.append("observacionesCincoOcho", document.getElementById("idObservaciones5.8").value);
        formData.append("accionesCincoOcho", document.getElementById("idAcciones5.8").value);
        formData.append("estatusCincoOcho", document.getElementById("idResultado5.8").value);
        formData.append("fechaFilaCincoOcho", document.getElementById("idFechaFila5.8").value);

        let archivoInput = document.getElementById("archivo_5_8_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila5_8.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 5.8 guardada correctamente.");
                bloquearCamposCincoOcho();
                guardarBtnCincoOcho.style.display = 'none';
                editarBtnCincoOcho.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposCincoOcho() {
        document.querySelectorAll("#idObservaciones5\\.8, #idAcciones5\\.8, #idResultado5\\.8, #idFechaFila5\\.8, #archivo_5_8_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposCincoOcho() {
        document.querySelectorAll("#idObservaciones5\\.8, #idAcciones5\\.8, #idResultado5\\.8, #idFechaFila5\\.8, #archivo_5_8_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnCincoOcho.addEventListener('click', function () {
        habilitarCamposCincoOcho();
        editarBtnCincoOcho.style.display = 'none';
        actualizarBtnCincoOcho.style.display = 'inline';
    });

    actualizarBtnCincoOcho.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);

        formData.append("observacionesCincoOcho", document.getElementById("idObservaciones5.8").value);
        formData.append("accionesCincoOcho", document.getElementById("idAcciones5.8").value);
        formData.append("estatusCincoOcho", document.getElementById("idResultado5.8").value);
        formData.append("fechaFilaCincoOcho", document.getElementById("idFechaFila5.8").value);

        let archivoInput = document.getElementById("archivo_5_8_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila5_8.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 5.8 actualizada correctamente.");
                    bloquearCamposCincoOcho();
                    actualizarBtnCincoOcho.style.display = 'none';
                    editarBtnCincoOcho.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnCincoOcho.addEventListener("click", guardarFilaCincoOcho);
});




// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
document.addEventListener('DOMContentLoaded', function () {
    const guardarBtnSeisUno = document.getElementById('btnGuardaSeisUno');
    const editarBtnSeisUno = document.getElementById('btnEditarSeisUno');
    const actualizarBtnSeisUno = document.getElementById('btnActualizarSeisUno');

    function guardarFilaSeisUno() {
        let formData = new FormData();
        formData.append("id", idGenerado); // Suponiendo que el id está generado en otro lugar

        formData.append("observacionesSeisUno", document.getElementById("idObservaciones6.1").value);
        formData.append("accionesSeisUno", document.getElementById("idAcciones6.1").value);
        formData.append("estatusSeisUno", document.getElementById("idResultado6.1").value);
        formData.append("fechaFilaSeisUno", document.getElementById("idFechaFila6.1").value);

        let archivoInput = document.getElementById("archivo_6_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("guardar_fila6_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(result => {
            console.log(result);
            if (result.success) {
                alert("✅ Fila 6.1 guardada correctamente.");
                bloquearCamposSeisUno();
                guardarBtnSeisUno.style.display = 'none';
                editarBtnSeisUno.style.display = 'inline';
            } else {
                alert("❌ Error: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
        });
    }

    function bloquearCamposSeisUno() {
        document.querySelectorAll("#idObservaciones6\\.1, #idAcciones6\\.1, #idResultado6\\.1, #idFechaFila6\\.1, #archivo_6_1_input")
        .forEach(element => {
            element.disabled = true;
        });
    }

    function habilitarCamposSeisUno() {
        document.querySelectorAll("#idObservaciones6\\.1, #idAcciones6\\.1, #idResultado6\\.1, #idFechaFila6\\.1, #archivo_6_1_input")
        .forEach(element => {
            element.disabled = false;
        });
    }

    editarBtnSeisUno.addEventListener('click', function () {
        habilitarCamposSeisUno();
        editarBtnSeisUno.style.display = 'none';
        actualizarBtnSeisUno.style.display = 'inline';
    });

    actualizarBtnSeisUno.addEventListener('click', function () {
        let formData = new FormData();
        formData.append("id", idGenerado);

        formData.append("observacionesSeisUno", document.getElementById("idObservaciones6.1").value);
        formData.append("accionesSeisUno", document.getElementById("idAcciones6.1").value);
        formData.append("estatusSeisUno", document.getElementById("idResultado6.1").value);
        formData.append("fechaFilaSeisUno", document.getElementById("idFechaFila6.1").value);

        let archivoInput = document.getElementById("archivo_6_1_input");
        if (archivoInput && archivoInput.files.length > 0) {
            formData.append("archivo", archivoInput.files[0]);
        }

        fetch("Controlador/actualizar_fila6_1.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            try {
                const json = JSON.parse(result);
                if (json.success) {
                    alert("✅ Fila 6.1 actualizada correctamente.");
                    bloquearCamposSeisUno();
                    actualizarBtnSeisUno.style.display = 'none';
                    editarBtnSeisUno.style.display = 'inline';
                } else {
                    alert("❌ Error: " + json.error);
                }
            } catch (error) {
                console.error("La respuesta no es JSON válido:", result);
                alert("Error en el servidor. Ver la consola para más detalles.");
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error en la comunicación con el servidor.");
        });
    });

    guardarBtnSeisUno.addEventListener("click", guardarFilaSeisUno);
});

// Asignar la función al botón
