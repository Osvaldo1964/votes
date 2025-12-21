const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

$('.login-content [data-toggle="flip"]').click(function () {
	$('.login-box').toggleClass('flipped');
	return false;
});

//var divLoading = document.querySelector("#divLoading");
document.addEventListener('DOMContentLoaded', function () {
	if (document.querySelector("#formLogin")) {
		let formLogin = document.querySelector("#formLogin");
		formLogin.onsubmit = function (e) {
			e.preventDefault();

			let strEmail = document.querySelector('#txtEmail').value;
			let strPassword = document.querySelector('#txtPassword').value;

			if (strEmail == "" || strPassword == "") {
				swal("Por favor", "Escribe usuario y contraseñaa.", "error");
				return false;
			} else {
				divLoading.style.display = "flex";
				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = BASE_URL_API + '/login/loginUser';
				var formData = new FormData(formLogin);
				request.open("POST", ajaxUrl, true);
				//request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
				request.send(formData);
				request.onreadystatechange = function () {
					if (request.readyState != 4) return;
					if (request.status == 200) {
						var objData = JSON.parse(request.responseText);
						if (objData.status) {
							//console.log(objData); return;
							localStorage.setItem('idUser', objData.auth.id_usuario);
							localStorage.setItem('userEmail', objData.auth.email_usuario);
							localStorage.setItem('userToken', objData.auth.access_token);
							localStorage.setItem('login', true);
							// 2. PASO NUEVO: Crear la sesión en el servidor local (app-vote)
							// Usaremos otro AJAX pero hacia BASE_URL (tu app)
							var requestSession = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
							var sessionUrl = BASE_URL + '/login/crearSesion'; // Apunta a tu controlador local

							requestSession.open("POST", sessionUrl, true);
							requestSession.setRequestHeader("Content-Type", "application/json");

							// Enviamos los datos necesarios para la sesión
							requestSession.send(JSON.stringify(objData.auth));

							requestSession.onreadystatechange = function () {
								if (requestSession.readyState == 4 && requestSession.status == 200) {
									// 3. Solo cuando el servidor local confirme que creó la sesión, redireccionamos
									window.location = BASE_URL + '/dashboard';
								}
							}
						} else {
							swal("Atención", objData.msg, "error");
							document.querySelector('#txtPassword').value = "";
						}
					} else {
						swal("Atención", "Error en el proceso", "error");
					}
					divLoading.style.display = "none";
					return false;
				}
			}
		}

		// Verificamos si en la URL viene el parámetro ?logout=true
		const urlParams = new URLSearchParams(window.location.search);

		if (urlParams.get('logout') === 'true') {
			// Limpiamos todo el LocalStorage del navegador
			localStorage.clear();

			// Opcional: Limpiar la URL para que no se quede el ?logout=true
			window.history.replaceState({}, document.title, window.location.pathname);

			console.log("Sesión y LocalStorage limpiados correctamente.");
		}
	}

	if (document.querySelector("#formRecetPass")) {
		let formRecetPass = document.querySelector("#formRecetPass");
		formRecetPass.onsubmit = function (e) {
			e.preventDefault();

			let strEmail = document.querySelector('#txtEmailReset').value;
			if (strEmail == "") {
				swal("Por favor", "Escribe tu correo electrónico.", "error");
				return false;
			} else {
				divLoading.style.display = "flex";
				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = BASE_URL_API + '/login/resetPass';
				var formData = new FormData(formRecetPass);
				request.open("POST", ajaxUrl, true);
				request.send(formData);
				request.onreadystatechange = function () {
					if (request.readyState != 4) return;

					if (request.status == 200) {
						var objData = JSON.parse(request.responseText);
						if (objData.status) {
							swal({
								title: "",
								text: objData.msg,
								type: "success",
								confirmButtonText: "Aceptar",
								closeOnConfirm: false,
							}, function (isConfirm) {
								if (isConfirm) {
									window.location = base_url;
								}
							});
						} else {
							swal("Atención", objData.msg, "error");
						}
					} else {
						swal("Atención", "Error en el proceso", "error");
					}
					divLoading.style.display = "none";
					return false;
				}
			}
		}
	}

	if (document.querySelector("#formCambiarPass")) {
		let formCambiarPass = document.querySelector("#formCambiarPass");
		formCambiarPass.onsubmit = function (e) {
			e.preventDefault();

			let strPassword = document.querySelector('#txtPassword').value;
			let strPasswordConfirm = document.querySelector('#txtPasswordConfirm').value;
			let idUsuario = document.querySelector('#idUsuario').value;

			if (strPassword == "" || strPasswordConfirm == "") {
				swal("Por favor", "Escribe la nueva contraseña.", "error");
				return false;
			} else {
				if (strPassword.length < 5) {
					swal("Atención", "La contraseña debe tener un mínimo de 5 caracteres.", "info");
					return false;
				}
				if (strPassword != strPasswordConfirm) {
					swal("Atención", "Las contraseñas no son iguales.", "error");
					return false;
				}
				divLoading.style.display = "flex";
				var request = (window.XMLHttpRequest) ?
					new XMLHttpRequest() :
					new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = base_url + '/Login/setPassword';
				var formData = new FormData(formCambiarPass);
				request.open("POST", ajaxUrl, true);
				request.send(formData);
				request.onreadystatechange = function () {
					if (request.readyState != 4) return;
					if (request.status == 200) {
						var objData = JSON.parse(request.responseText);
						if (objData.status) {
							swal({
								title: "",
								text: objData.msg,
								type: "success",
								confirmButtonText: "Iniciar sessión",
								closeOnConfirm: false,
							}, function (isConfirm) {
								if (isConfirm) {
									window.location = base_url + '/login';
								}
							});
						} else {
							swal("Atención", objData.msg, "error");
						}
					} else {
						swal("Atención", "Error en el proceso", "error");
					}
					divLoading.style.display = "none";
				}
			}
		}
	}

}, false);