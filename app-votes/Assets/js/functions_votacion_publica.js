// functions_votacion_publica.js
// Lógica idéntica a functions_votacion se adapta para uso público sin auth headers

// Definición local de fetchData para uso público (sin token requerido)
async function fetchData(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {}
    };

    // Si no es FormData, asumimos JSON y añadimos Content-Type
    if (!(body instanceof FormData)) {
        options.headers['Content-Type'] = 'application/json';
        if (body && method !== 'GET') options.body = JSON.stringify(body);
    } else {
        // Si es FormData, fetch pone el Content-Type automáticamente (multipart/form-data)
        if (body && method !== 'GET') options.body = body;
    }

    try {
        const response = await fetch(url, options);
        // No validamos 401 porque es público
        
        const text = await response.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Respuesta válida no es JSON:", text);
            return { status: false, msg: "Error de servidor (respuesta no válida)" };
        }
    } catch (error) {
        console.error("Error de conexión:", error);
        return { status: false, msg: "Error de conexión con la API" };
    }
}

document.addEventListener('DOMContentLoaded', function () {

    let formVoto = document.querySelector("#formVotoPublico");
    let isProcessing = false; 

    if(formVoto){
        formVoto.onsubmit = async function (e) {
            e.preventDefault();
    
            if (isProcessing) return; 
    
            let strIdentificacion = document.querySelector('#txtIdentificacionPublico').value;
            if (strIdentificacion == '') {
                swal("Atención", "Escriba su identificación", "warning");
                return;
            }
    
            const btnSubmit = formVoto.querySelector('button[type="submit"]');
            const originalBtnText = btnSubmit.innerHTML;
    
            try {
                isProcessing = true;
                btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Verificando...';
                btnSubmit.disabled = true;
    
                // 1. Consultar estado (API PUBLICA)
                let info = await fetchData(`${BASE_URL_API}/VotacionPublica/getValidaElector/${strIdentificacion}`);
    
                // CASO 1: No en Censo
                if (!info || !info.status) {
                    swal("Error", "El número de identificación no se encuentra en el Censo.", "error");
                    resetUI();
                    document.querySelector('#txtIdentificacionPublico').value = "";
                    return;
                }
    
                // CASO 3: YA VOTÓ
                if (info.elector_data && parseInt(info.elector_data.poll_elector) >= 1) {
                    swal("¡Atención!", "Este elector YA registró su voto anteriormente.", "warning");
                    resetUI();
                    return;
                }
    
                // CASO 4: Confirmación
                let data = info.data;
                let nombreCompleto = `${data.nom1_place} ${data.nom2_place || ""} ${data.ape1_place} ${data.ape2_place || ""}`;
                let lugar = `${data.name_zone} - Mesa: ${data.mesa_place}`;
    
                swal({
                    title: "Confirmar Voto",
                    text: `<div style='text-align: left'>
                            <br><b>Nombre:</b> ${nombreCompleto}<br>
                            <b>Lugar:</b> ${lugar}<br><br>
                            ¿CONFIRMA SU VOTO?</div>`,
                    html: true,
                    type: "info",
                    showCancelButton: true,
                    confirmButtonText: "SÍ, REGISTRAR",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        performVote(new FormData(formVoto));
                    } else {
                        swal.close();
                        resetUI();
                    }
                });
    
            } catch (error) {
                console.error(error);
                swal("Error", "Error de conexión.", "error");
                resetUI();
            }
    
            function resetUI() {
                btnSubmit.innerHTML = originalBtnText;
                btnSubmit.disabled = false;
                isProcessing = false;
            }
    
            async function performVote(formData) {
                try {
                    // API PUBLICA
                    // Necesitamos asegurar que el formData tenga 'identificacion' (el input name="identificacion")
                    let response = await fetchData(`${BASE_URL_API}/VotacionPublica/setVoto`, 'POST', formData);
    
                    if (response && response.status) {
                        swal("¡Voto Registrado!", "Su participación ha sido registrada. Gracias.", "success");
                        formVoto.reset();
                        resetUI();
                    } else {
                        let msg = response ? response.msg : "Error desconocido";
                        swal("Atención", msg, "warning");
                        resetUI(); 
                    }
                } catch (err) {
                    console.error(err);
                    swal("Error", "Error al procesar el voto.", "error");
                    resetUI();
                }
            }
        }
    }
});
