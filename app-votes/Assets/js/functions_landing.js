document.addEventListener('DOMContentLoaded', function () {

    // CONSULTA PUESTO
    const btnConsultar = document.querySelector("#btnConsultar");
    const txtCedula = document.querySelector("#txtCedula");

    if (txtCedula) {
        txtCedula.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                consultarPuesto();
            }
        });
    }

    if (btnConsultar) {
        btnConsultar.addEventListener('click', function () {
            consultarPuesto();
        });
    }

    // CONTACTO
    const formContacto = document.querySelector("#formContacto");
    if (formContacto) {
        formContacto.addEventListener('submit', function (e) {
            e.preventDefault();
            enviarMensaje();
        });
    }
});

async function consultarPuesto() {
    const cedula = document.querySelector("#txtCedula").value.trim();
    const divLoading = document.querySelector("#loading");
    const divResultado = document.querySelector("#resultado");

    if (cedula === "") {
        swal("Atención", "Por favor ingresa un número de cédula.", "warning");
        return;
    }

    divLoading.style.display = "block";
    divResultado.style.display = "none";
    divResultado.innerHTML = "";

    try {
        const url = `${BASE_URL_API}/Place/getValidaPlace/${cedula}`;
        const response = await fetch(url);

        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("Respuesta invalida JSON:", text);
            throw new Error("Respuesta servidor invalida");
        }

        divLoading.style.display = "none";

        if (data.status) {
            const info = data.data;
            const html = `
                <div class="alert alert-success text-left">
                    <h4 class="alert-heading"><i class="fa fa-check-circle"></i> ¡Habilitado!</h4>
                    <p>Tu puesto de votación es:</p>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6"><strong>Departamento:</strong><br> ${info.name_department}</div>
                        <div class="col-sm-6"><strong>Municipio:</strong><br> ${info.name_municipality}</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6"><strong>Zona:</strong><br> ${info.name_zone}</div>
                        <div class="col-sm-6"><strong>Puesto:</strong><br> ${info.nameplace_place}</div>
                    </div>
                    <div class="row mt-3">
                         <div class="col-12 text-center bg-white border rounded py-2">
                            <span class="text-muted small">Tu mesa asignada es:</span>
                            <h2 class="text-primary m-0">MESA ${info.mesa_place}</h2>
                         </div>
                    </div>
                </div>
            `;
            divResultado.innerHTML = html;
            divResultado.style.display = "block";
        } else {
            divResultado.innerHTML = `
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Atención:</strong><br>
                    ${data.msg || "La cédula ingresada no se encuentra habilitada en el censo disponible."}
                </div>
            `;
            divResultado.style.display = "block";
        }

    } catch (error) {
        console.error(error);
        divLoading.style.display = "none";
        divResultado.innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-times-circle"></i> Error de conexión. Intente nuevamente.
            </div>
        `;
        divResultado.style.display = "block";
    }
}

async function enviarMensaje() {
    const btnEnviar = document.querySelector("#btnEnviarMensaje"); // Si le pusimos ID

    // Recoger datos
    const formData = new FormData(document.querySelector("#formContacto"));

    // Feedback visual
    const txtOriginal = btnEnviar ? btnEnviar.innerHTML : "Enviar";
    if (btnEnviar) {
        btnEnviar.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Enviando...';
        btnEnviar.disabled = true;
    }

    try {
        const url = `${BASE_URL}/Home/enviarContacto`;
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.status) {
            swal("Mensaje Enviado", data.msg, "success");
            document.querySelector("#formContacto").reset();
        } else {
            swal("Error", data.msg, "error");
        }

    } catch (error) {
        console.error(error);
        swal("Error", "Error de conexión al enviar mensaje.", "error");
    } finally {
        if (btnEnviar) {
            btnEnviar.innerHTML = txtOriginal;
            btnEnviar.disabled = false;
        }
    }
}
