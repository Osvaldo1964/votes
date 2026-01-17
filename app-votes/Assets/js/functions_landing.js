let dataUbicaciones = null;

document.addEventListener('DOMContentLoaded', function () {

    // CARGAR UBICACIONES
    cargarUbicaciones();

    // EVENTO CAMBIO DEPARTAMENTO
    const selDpto = document.getElementById('dpto_elector');
    if (selDpto) {
        selDpto.addEventListener('change', function () {
            filtrarMunicipios(this.value);
        });
    }

    // CONSULTA PUESTO - Logica Original
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

    // CONTACTO - Logica Original
    const formContacto = document.querySelector("#formContacto");
    if (formContacto) {
        formContacto.addEventListener('submit', function (e) {
            e.preventDefault();
            enviarMensaje();
        });
    }

    // NUEVA LÓGICA: REGISTRO PÚBLICO
    const formRegistro = document.getElementById('formRegistroPublico');
    if (formRegistro) {
        formRegistro.addEventListener('submit', function (e) {
            e.preventDefault();

            let btn = document.getElementById('btnRegistrar');
            let alert = document.getElementById('divAlertRegistro');

            // Guardar texto original
            let txtOriginal = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
            alert.classList.add('d-none');

            // Ejecutar reCAPTCHA
            grecaptcha.ready(function () {
                grecaptcha.execute('6Le2ikQsAAAAAJVqo_KpOrqhzdwpmLbM-vSTwjVh', { action: 'submit' }).then(function (token) {

                    let formData = new FormData(formRegistro);
                    let object = {};
                    formData.forEach((value, key) => object[key] = value);
                    object['recaptcha_token'] = token;

                    // URL API REGISTRO
                    let urlAPI = `${BASE_URL_API}/RegistroPublico/registrar`;

                    fetch(urlAPI, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(object)
                    })
                        .then(response => response.json())
                        .then(data => {
                            alert.classList.remove('d-none');
                            if (data.status) {
                                // Éxito: Mostrar alerta bonita o simple
                                swal("¡Bienvenido!", data.msg, "success");
                                formRegistro.reset();
                                // Cerrar modal opcionalmente
                                $('#modalRegistro').modal('hide');
                            } else {
                                alert.classList.remove('alert-success');
                                alert.classList.add('alert-danger');
                                alert.innerHTML = data.msg;
                            }
                            btn.disabled = false;
                            btn.innerHTML = txtOriginal;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert.classList.remove('d-none');
                            alert.classList.add('alert-danger');
                            alert.innerHTML = "Error de conexión. Intente nuevamente.";
                            btn.disabled = false;
                            btn.innerHTML = txtOriginal;
                        });
                });
            });
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

// FUNCIONES DE UBICACIÓN
function cargarUbicaciones() {
    // Usamos BASE_URL_API definida en la vista
    fetch(`${BASE_URL_API}/RegistroPublico/getUbicaciones`)
        .then(res => res.json())
        .then(data => {
            dataUbicaciones = data; // Guardamos globalmente
            if (data.dptos) {
                const selDpto = document.getElementById('dpto_elector');
                if (selDpto) {
                    // Limpiar y resetear
                    selDpto.innerHTML = '<option value="0">Seleccione...</option>';

                    data.dptos.forEach(d => {
                        let opt = document.createElement('option');
                        opt.value = d.iddpto;
                        opt.innerHTML = d.namedpto;
                        selDpto.appendChild(opt);
                    });
                }
            }
        })
        .catch(err => console.error("Error cargando ubicaciones", err));
}

function filtrarMunicipios(idDpto) {
    const selMuni = document.getElementById('muni_elector');
    if (selMuni) {
        selMuni.innerHTML = '<option value="0">Seleccione...</option>'; // Limpiar

        if (dataUbicaciones && dataUbicaciones.munis && idDpto != "0") {
            // Filtramos por dptomuni == idDpto
            const filtrados = dataUbicaciones.munis.filter(m => m.dptomuni == idDpto);
            filtrados.forEach(m => {
                let opt = document.createElement('option');
                opt.value = m.idmuni;
                opt.innerHTML = m.namemuni;
                selMuni.appendChild(opt);
            });
        }
    }
}


