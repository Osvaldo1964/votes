function controlTag(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8) return true;
    else if (tecla == 0 || tecla == 9) return true;
    patron = /[0-9\s]/;
    n = String.fromCharCode(tecla);
    return patron.test(n);
}

function testText(txtString) {
    var stringText = new RegExp(/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/);
    if (stringText.test(txtString)) {
        return true;
    } else {
        return false;
    }
}

function testAddress(address) {
    var stringAddress = new RegExp(/^[a-zA-Z0-9ÑñÁáÉéÍíÓóÚúÜü\s#.,-\/]+$/);
    return stringAddress.test(address);
}

function testEntero(intCant) {
    var intCantidad = new RegExp(/^([0-9])*$/);
    if (intCantidad.test(intCant)) {
        return true;
    } else {
        return false;
    }
}

function fntEmailValidate(email) {
    var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);
    if (stringEmail.test(email) == false) {
        return false;
    } else {
        return true;
    }
}

function fntValidText() {
    let validText = document.querySelectorAll(".validText");
    validText.forEach(function (validText) {
        validText.addEventListener('keyup', function () {
            let inputValue = this.value;
            if (!testText(inputValue)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

function fntValidNumber() {
    let validNumber = document.querySelectorAll(".validNumber");
    validNumber.forEach(function (validNumber) {
        validNumber.addEventListener('keyup', function () {
            let inputValue = this.value;
            if (!testEntero(inputValue)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

function fntValidEmail() {
    let validEmail = document.querySelectorAll(".validEmail");
    validEmail.forEach(function (validEmail) {
        validEmail.addEventListener('keyup', function () {
            let inputValue = this.value;
            if (!fntEmailValidate(inputValue)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

function fntValidAddress() {
    let validAddress = document.querySelectorAll(".validAddress");
    validAddress.forEach(function (input) {
        input.addEventListener('keyup', function () {
            let inputValue = this.value;
            // Si el campo está vacío, podrías decidir si es válido o no. 
            // Aquí validamos si cumple el patrón.
            if (!testAddress(inputValue) && inputValue !== "") {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    });
}

// ----------------------------------------------------------------------------------
// GLOBAL HELPERS (Optimización y Estandarización)
// ----------------------------------------------------------------------------------

const lenguajeEspanol = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
};

/**
 * Helper global para peticiones fetch con manejo de errores y token
 * @param {string} url - Endpoint completo
 * @param {string} method - GET, POST, PUT, DELETE
 * @param {object|FormData} body - Datos a enviar
 * @returns {Promise<object|null>} - Respuesta JSON o null
 */
async function fetchData(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('userToken')}`
        }
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

        // Manejo básico de 401 (No autorizado)
        if (response.status === 401) {
            console.warn("Token expirado o inválido (401).");
            // Opcional: Redirigir al login o limpiar sesión aquí
            // localStorage.clear();
            // window.location = BASE_URL + '/login';
            return { status: false, msg: "Sesión expirada o no autorizada." };
        }

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

/**
 * Devuelve la configuración AJAX estándar para DataTables con Auth Headers
 * @param {string} urlEndpoint - Endpoint de la API (ej: /usuario/getUsers)
 * @param {object} additionalData - Datos extra para enviar (opcional)
 */
function getDataTableFetchConfig(urlEndpoint, additionalData = {}) {
    return {
        "url": BASE_URL_API + urlEndpoint,
        "type": "GET",
        "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
        "data": function (d) {
            d.rolUser = localStorage.getItem('userRol');
            // Merge con datos adicionales si existen
            Object.assign(d, additionalData);
        },
        "dataSrc": function (json) {
            if (json && json.status && Array.isArray(json.data)) {
                return json.data;
            } else {
                // Si el token expiró o hay error, manejamos silenciosamente o logueamos
                if (json && json.status === false && json.msg) {
                    console.warn("DataTable Error/Auth:", json.msg);
                }
                return [];
            }
        },
        "error": function (xhr, error, thrown) {
            console.error("Error en DataTable AJAX:", xhr);
            if (xhr.status === 401) {
                swal("Sesión Expirada", "Su sesión ha terminado", "warning");
                // localStorage.removeItem('userToken'); 
                // window.location = BASE_URL + '/login';
            }
        }
    };
}

// ----------------------------------------------------------------------------------

function checkAuth() {
    const token = localStorage.getItem('userToken');

    if (!token) {
        window.location.href = BASE_URL + "/login";
    }
}

function verificarExpiracionToken() {
    const token = localStorage.getItem('userToken');

    // Si no hay token, no hacemos nada (el middleware de PHP se encargará)
    if (!token) return;

    try {
        // Decodificamos el Payload del JWT
        const base64Url = token.split('.')[1];
        const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        const payload = JSON.parse(window.atob(base64));

        const tiempoActual = Math.floor(Date.now() / 1000);

        // Si el tiempo actual es mayor a la expiración
        if (payload.exp < tiempoActual) {
            swal({
                title: "Sesión Expirada",
                text: "Tu tiempo de acceso ha terminado. Por seguridad, debes ingresar nuevamente.",
                type: "warning",
                confirmButtonText: "Aceptar",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    // 1. Limpiamos el token localmente de inmediato
                    localStorage.removeItem('userToken');

                    // 2. Redirigimos al logout del servidor
                    window.location.href = BASE_URL + '/logout/logout';
                }
            });
        }
    } catch (e) {
        console.error("Error al decodificar el token:", e);
    }
}

window.addEventListener('load', function () {
    fntValidText();
    fntValidEmail();
    fntValidNumber();

    // Validar Rol para botón de inicialización
    if (localStorage.getItem('userRol') == 1 && document.querySelector('#liInitEscrutinio')) {
        document.querySelector('#liInitEscrutinio').classList.remove('d-none');
    }
}, false);

function fntInicializarMesas(e) {
    if (e) e.preventDefault();
    swal({
        title: "Inicializar Escrutinio",
        text: "¿Desea ejecutar la carga de mesas faltantes? Esto verificará y creará los registros maestros de mesas. No afectará datos existentes.",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, Ejecutar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, async function () {
        // Obtenemos Base URL API correctamente (asumiendo que variable global BASE_URL_API existe, si no, usar BASE_URL + '/api')
        // Si no existe BASE_URL_API global, usar BASE_URL o construirla.
        // En functions_resultados vi BASE_URL_API. Asumo que está disponible.
        // Si no, fallback:
        const apiUrl = (typeof BASE_URL_API !== 'undefined') ? BASE_URL_API : BASE_URL + '/api-votes';

        const url = apiUrl + '/resultados/inicializar';
        try {
            const response = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('userToken')}`
                }
            });
            const data = await response.json();
            if (data.status) {
                swal("Éxito", data.msg, "success");
            } else {
                swal("Error", data.msg, "error");
            }
        } catch (error) {
            swal("Error", "Error de comunicación con el servidor.", "error");
        }
    });
}