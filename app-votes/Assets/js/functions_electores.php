const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

var tableRoles;
// Evitar alerts nativos de DataTables
$.fn.dataTable.ext.errMode = 'none';

document.addEventListener('DOMContentLoaded', function () {

// 1. INICIALIZACIÓN DE DATATABLE
tableRoles = $('#tableRoles').DataTable({
"processing": true,
"serverSide": false,
"language": {
"url": BASE_URL + "/assets/json/spanish.json"
},
"ajax": {
"url": BASE_URL_API + "/roles/getRoles",
"type": "GET",
"headers": { "Authorization": "Bearer " + localStorage.getItem('userToken') },
"data": function (d) {
d.rolUser = localStorage.getItem('userRol');
},
"dataSrc": function (json) {
if (json.status == false && json.msg) {
return [];
}
return json.data;
},
"error": function (xhr, error, thrown) {
fntHandleError(xhr);
}
},
"columns": [
{ "data": "id_rol" },
{ "data": "nombre_rol" },
{ "data": "descript_rol" },
{ "data": "status_rol" },
{ "data": "options" }
],
"responsive": true,
"destroy": true,
"displayLength": 10,
"order": [[0, "desc"]]
});

// 2. GUARDAR ROL (NUEVO/ACTUALIZAR)
var formRol = document.querySelector("#formRol");
if (formRol) {
formRol.onsubmit = function (e) {
e.preventDefault();

let elements = formRol.querySelectorAll(".is-invalid");
if (elements.length > 0) {
elements[0].focus();
return;
}

var formData = new FormData(formRol);
var request = new XMLHttpRequest();
request.open("POST", BASE_URL_API + '/roles/setRol', true);
request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
request.send(formData);

request.onreadystatechange = function () {
if (request.readyState == 4) {
if (request.status == 200) {
var objData = JSON.parse(request.responseText);
if (objData.status) {
$('#modalFormRol').modal("hide");
formRol.reset();
swal("Roles", objData.msg, "success");
tableRoles.ajax.reload();
} else {
swal("Error", objData.msg, "error");
}
} else {
fntHandleError(request);
}
}
}
}
}

// 3. DELEGACIÓN DE EVENTOS (CLICK GLOBAL)
document.addEventListener('click', function (e) {
const btnEdit = e.target.closest('.btnEditRol');
const btnDel = e.target.closest('.btnDelRol');
const btnPerm = e.target.closest('.btnPermisosRol');
const btnNuevo = e.target.closest('#btnNuevoRol'); // Asegúrate de que el ID del botón coincida

if (btnNuevo) openModal();
if (btnEdit) fntEditRol(btnEdit.getAttribute('rl'));
if (btnDel) fntDelRol(btnDel.getAttribute('rl'));
if (btnPerm) fntPermisos(btnPerm.getAttribute('rl'));
});

});

/**
* FUNCION GLOBAL PARA MANEJO DE ERRORES DE AUTORIZACIÓN
*/
function fntHandleError(xhr) {
if (xhr.status === 401 || xhr.status === 400) {
let mensaje = "Tu sesión ha expirado o no tienes autorización.";
try {
let res = JSON.parse(xhr.responseText);
if (res.msg) mensaje = res.msg;
} catch (e) { }

swal({
title: "Sesión Expirada",
text: mensaje,
type: "warning",
confirmButtonText: "Aceptar",
closeOnConfirm: true
}, function (isConfirm) {
if (isConfirm) {
window.location.href = BASE_URL + '/logout/logout';
}
});
} else {
console.error("Error del sistema:", xhr.responseText);
}
}

function openModal() {
if (document.querySelector('#idRol')) document.querySelector('#idRol').value = "";
document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
document.querySelector('#btnText').innerHTML = "Guardar";
document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
document.querySelector("#formRol").reset();

$('#listStatus').val('1').selectpicker('refresh');
$('#modalFormRol').modal('show');
}

function fntEditRol(idRol) {
document.querySelector('#titleModal').innerHTML = "Actualizar Rol";
document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
document.querySelector('#btnText').innerHTML = "Actualizar";

var request = new XMLHttpRequest();
request.open("GET", BASE_URL_API + '/roles/getRol/' + idRol, true);
request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
request.send();

request.onreadystatechange = function () {
if (request.readyState == 4) {
if (request.status == 200) {
var objData = JSON.parse(request.responseText);
if (objData.status) {
document.querySelector("#idRol").value = objData.data.id_rol;
document.querySelector("#txtNombre").value = objData.data.nombre_rol;
document.querySelector("#txtDescripcion").value = objData.data.descript_rol;

$('#listStatus').selectpicker('destroy');
document.querySelector('#listStatus').value = String(objData.data.status_rol);
$('#listStatus').selectpicker();
$('#listStatus').selectpicker('refresh');

$('#modalFormRol').modal('show');
}
} else {
fntHandleError(request);
}
}
}
}

function fntDelRol(idRol) {
swal({
title: "Eliminar Rol",
text: "¿Realmente quiere eliminar el Rol?",
type: "warning",
showCancelButton: true,
confirmButtonText: "Si, eliminar!",
closeOnConfirm: false
}, function (isConfirm) {
if (isConfirm) {
var request = new XMLHttpRequest();
var jsonParams = JSON.stringify({ idrol: idRol });
request.open("PUT", BASE_URL_API + '/roles/delRol/', true);
request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
request.setRequestHeader('Content-Type', 'application/json');
request.send(jsonParams);

request.onreadystatechange = function () {
if (request.readyState == 4) {
if (request.status == 200) {
var objData = JSON.parse(request.responseText);
if (objData.status) {
swal("Eliminado!", objData.msg, "success");
tableRoles.ajax.reload();
} else {
swal("Atención!", objData.msg, "error");
}
} else {
fntHandleError(request);
}
}
}
}
});
}

function fntPermisos(idRol) {
var request = new XMLHttpRequest();
request.open("GET", BASE_URL_API + '/permisos/getPermisosRol/' + idRol, true);
request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
request.send();

request.onreadystatechange = function () {
if (request.readyState == 4) {
if (request.status == 200) {
var objResponse = JSON.parse(request.responseText);
if (objResponse.status) {
var htmlTable = "";
var no = 1;
objResponse.data.forEach(function (modulo) {
var pR = (modulo.permisos && modulo.permisos.r == 1) ? "checked" : "";
var pW = (modulo.permisos && modulo.permisos.w == 1) ? "checked" : "";
var pU = (modulo.permisos && modulo.permisos.u == 1) ? "checked" : "";
var pD = (modulo.permisos && modulo.permisos.d == 1) ? "checked" : "";

htmlTable += `
<tr>
    <td>${no} <input type="hidden" name="modulos[${modulo.id_modulo}][idmodulo]" value="${modulo.id_modulo}"></td>
    <td>${modulo.titulo_modulo}</td>
    <td>
        <div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][r]" ${pR}><span
                    class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div>
    </td>
    <td>
        <div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][w]" ${pW}><span
                    class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div>
    </td>
    <td>
        <div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][u]" ${pU}><span
                    class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div>
    </td>
    <td>
        <div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][d]" ${pD}><span
                    class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div>
    </td>
</tr>`;
no++;
});

document.querySelector('#contentAjax').innerHTML = htmlTable;
if (document.querySelector('#idrol')) document.querySelector('#idrol').value = idRol;
$('.modalPermisos').modal('show');

document.querySelector('#formPermisos').onsubmit = fntSavePermisos;
}
} else {
fntHandleError(request);
}
}
}
}

function fntSavePermisos(e) {
e.preventDefault();
var formData = new FormData(document.querySelector('#formPermisos'));
var request = new XMLHttpRequest();
request.open("POST", BASE_URL_API + '/permisos/setPermisos', true);
request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
request.send(formData);

request.onreadystatechange = function () {
if (request.readyState == 4) {
if (request.status == 200) {
var objData = JSON.parse(request.responseText);
if (objData.status) {
swal("Permisos", objData.msg, "success");
$('.modalPermisos').modal('hide');
} else {
swal("Error", objData.msg, "error");
}
} else {
fntHandleError(request);
}
}
};
}