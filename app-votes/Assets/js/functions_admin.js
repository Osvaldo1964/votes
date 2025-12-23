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

function checkAuth() {
    const token = localStorage.getItem('userToken');

    if (!token) {
        window.location.href = "http://app-vote.com/login";
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
                    window.location.href = 'http://app-vote.com/logout/logout';
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
}, false);