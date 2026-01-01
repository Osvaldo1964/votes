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
            let responseInfo = await fetch(`${BASE_URL_API}/Electores/getValidaElector/${strIdentificacion}`, {
                method: 'GET',
                headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
            });
            let info = await responseInfo.json();

            // CASO 1: No en Censo
            if (!info.status) {
                swal("Error", "El número de identificación no se encuentra en el Censo.", "error");
                resetUI();
                document.querySelector('#txtIdentificacion').value = "";
                return;
            }

            // CASO 2: No registrado en Electores
            if (!info.is_registered) {
                swal("Atención", "Este elector NO está registrado en la base de datos de Electores. Debe registrarlo primero.", "warning");
                resetUI();
                return;
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
                    // 3. REGISTRAR EL VOTO
                    performVote(new FormData(formVoto));
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
                let request = await fetch(`${BASE_URL_API}/Electores/setVoto`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
                    body: formData
                });
                let response = await request.json();

                if (response.status) {
                    swal("¡Voto Registrado!", "El voto ha sido contabilizado.", "success");
                    formVoto.reset();
                    resetUI();
                    document.querySelector('#txtIdentificacion').focus();
                } else {
                    let tipo = response.msg.includes("YA") ? "warning" : "error";
                    swal("Atención", response.msg, tipo);
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
