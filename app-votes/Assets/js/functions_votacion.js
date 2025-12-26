document.addEventListener('DOMContentLoaded', function () {

    let formVoto = document.querySelector("#formVoto");
    formVoto.onsubmit = async function (e) {
        e.preventDefault();

        let strIdentificacion = document.querySelector('#txtIdentificacion').value;
        if (strIdentificacion == '') {
            swal("Atención", "Escriba la identificación", "warning");
            return false;
        }

        // 1. PRIMERO: Consultar quién es el elector
        try {
            const btnSubmit = formVoto.querySelector('button[type="submit"]');
            btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Verificando...';
            btnSubmit.disabled = true;

            // Usamos getValidaElector del controlador API de Electores
            let responseInfo = await fetch(BASE_URL_API + '/Electores/getValidaElector/' + strIdentificacion, {
                method: 'GET',
                headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
            });
            let info = await responseInfo.json();

            if (!info.status) {
                // No existe en el Censo
                swal("Error", "El número de identificación no se encuentra en el Censo.", "error");
                btnSubmit.innerHTML = '<i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO';
                btnSubmit.disabled = false;
                document.querySelector('#txtIdentificacion').value = "";
                document.querySelector('#txtIdentificacion').focus();
                return;
            }

            // Validar si está registrado en el sistema local (electores)
            if (!info.is_registered) {
                swal("Atención", "Este elector NO está registrado en la base de datos de Electores. Debe registrarlo primero antes de votar.", "warning");
                btnSubmit.innerHTML = '<i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO';
                btnSubmit.disabled = false;
                return;
            }

            let data = info.data;
            let nombreCompleto = `${data.nom1_place} ${data.nom2_place || ""} ${data.ape1_place} ${data.ape2_place || ""}`;
            let lugar = `${data.name_zone} - Publico: ${data.nameplace_place} - Mesa: ${data.mesa_place}`;

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
            }, async function (isConfirm) {
                if (isConfirm) {
                    // 3. REGISTRAR EL VOTO
                    let formData = new FormData(formVoto);
                    let request = await fetch(BASE_URL_API + '/Electores/setVoto', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('userToken')}`
                        },
                        body: formData
                    });
                    let response = await request.json();

                    if (response.status) {
                        swal("¡Voto Registrado!", "El voto ha sido contabilizado.", "success");
                        formVoto.reset();
                        // Devolver foco y restaurar botón
                        btnSubmit.innerHTML = '<i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO';
                        btnSubmit.disabled = false;
                        document.querySelector('#txtIdentificacion').focus();
                    } else {
                        let tipo = response.msg.includes("YA") ? "warning" : "error";
                        swal("Atención", response.msg, tipo);
                        btnSubmit.innerHTML = '<i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO';
                        btnSubmit.disabled = false;
                    }
                } else {
                    // Cancelado
                    btnSubmit.innerHTML = '<i class="fa fa-check-circle fa-lg"></i> REGISTRAR VOTO';
                    btnSubmit.disabled = false;
                    document.querySelector('#txtIdentificacion').focus();
                }
            });

        } catch (error) {
            console.error(error);
            swal("Error", "Error de conexión", "error");
            formVoto.querySelector('button[type="submit"]').disabled = false;
        }
    }
});
