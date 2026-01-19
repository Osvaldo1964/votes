document.addEventListener('DOMContentLoaded', function () {

    let formVoto = document.querySelector("#formVoto");
    let isProcessing = false; // Bandera para evitar doble envío

    formVoto.onsubmit = async function (e) {
        e.preventDefault();

        if (isProcessing) return; // Si ya está procesando, no hacer nada

        let strIdentificacion = document.querySelector('#txtIdentificacion').value;
        if (strIdentificacion == '') {
            swal("Atención", "Escriba la identificación", "warning");
            return;
        }

        const btnSubmit = formVoto.querySelector('button[type="submit"]');

        try {
            isProcessing = true;
            btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Verificando...';
            btnSubmit.disabled = true;

            // 1. Consultar estado del elector
            // fetchData hace el json parse
            let info = await fetchData(`${BASE_URL_API}/Electores/getValidaElector/${strIdentificacion}`);

            // CASO 1: No en Censo
            if (!info || !info.status) {
                swal("Error", "El número de identificación no se encuentra en el Censo o hubo un error.", "error");
                resetUI();
                document.querySelector('#txtIdentificacion').value = "";
                return;
            }

            // CASO 2: No registrado en Electores -> Se permite AUTO-REGISTRO
            // if (!info.is_registered) { ... } -> ELIMINADO BLOQUEO
            if (!info.is_registered) {
                // Opcional: Mostrar un aviso no bloqueante o cambiar el texto de confirmación
                // Por ahora permitimos pasar calladamente, el backend lo creará.
            }

            // CASO 3: YA VOTÓ (Validación Previa)
            if (info.elector_data && parseInt(info.elector_data.poll_elector) >= 1) {
                swal({
                    title: "¡Atención!",
                    text: "Este elector YA registró su voto anteriormente.",
                    type: "warning",
                    confirmButtonText: "Aceptar"
                }, function () {
                    resetUI();
                    document.querySelector('#txtIdentificacion').focus();
                });
                return; // Importante detener aquí
            }

            // CASO 4: Todo OK, perdimos confirmación
            let data = info.data;
            let nombreCompleto = `${data.nom1_place} ${data.nom2_place || ""} ${data.ape1_place} ${data.ape2_place || ""}`;
            let lugar = `${data.name_zone} - Puesto: ${data.nameplace_place} - Mesa: ${data.mesa_place}`;

            // 2. CONFIRMACIÓN VISUAL
            swal({
                title: "Confirmar Votante",
                text: `<div style='text-align: left'>
                        <br><b>Nombre:</b> ${nombreCompleto}<br>
                        <b>Lugar:</b> ${lugar}<br><br>
                        ¿CONFIRMA EL VOTO?</div>`,
                html: true,
                type: "info",
                showCancelButton: true,
                confirmButtonText: "SÍ, REGISTRAR",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
            }, function (isConfirm) {
                if (isConfirm) {
                    // 3. CAPTURAR GEOLOCALIZACIÓN Y REGISTRAR
                    if (navigator.geolocation) {
                        // Mensaje de carga para UX
                        btnSubmit.innerHTML = '<i class="fa fa-map-marker fa-spin"></i> Ubicando...';
                        
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                // Éxito Geo
                                let formData = new FormData(formVoto);
                                formData.append('lat', position.coords.latitude);
                                formData.append('lon', position.coords.longitude);
                                performVote(formData);
                            },
                            (error) => {
                                // Error Geo (Permiso denegado o timeout) -> Enviamos SIN coordenadas
                                console.warn("Geo error:", error);
                                swal("Aviso", "No se pudo obtener la ubicación. Se registrará sin georeferencia.", "info");
                                performVote(new FormData(formVoto));
                            },
                            { timeout: 5000, enableHighAccuracy: true }
                        );
                    } else {
                        // No soporta Geo
                        performVote(new FormData(formVoto));
                    }

                } else {
                    // Cancelado
                    swal.close();
                    resetUI();
                    // Opcional: enfocar de nuevo, pero sin timeout agresivo
                    setTimeout(() => document.querySelector('#txtIdentificacion').focus(), 300);
                }
            });

        } catch (error) {
            console.error(error);
            swal("Error", "Error de servidor o conexión.", "error");
            resetUI();
        }

        function resetUI() {
            btnSubmit.innerHTML = '<i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO';
            btnSubmit.disabled = false;
            isProcessing = false;
        }

        async function performVote(formData) {
            try {
                let response = await fetchData(`${BASE_URL_API}/Electores/setVoto`, 'POST', formData);

                if (response && response.status) {
                    swal("¡Voto Registrado!", "El voto ha sido contabilizado.", "success");
                    formVoto.reset();
                    resetUI();
                    document.querySelector('#txtIdentificacion').focus();
                } else {
                    let msg = response ? response.msg : "Error desconocido";
                    let tipo = msg.includes("YA") ? "warning" : "error";
                    swal("Atención", msg, tipo);
                    resetUI(); // Asegurar reset en error también
                }
            } catch (err) {
                console.error(err);
                swal("Error", "Error al procesar el voto.", "error");
                resetUI();
            }
        }
    }
});
