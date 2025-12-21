const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

var tableRoles;

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
            "dataSrc": "data"
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

            // Validaciones rápidas
            /*             if (document.querySelector('#txtNombre').value == '' || document.querySelector('#txtDescripcion').value == '') {
                            swal("Atención", "Todos los campos son obligatorios.", "error");
                            return false;
                        } */
            let elements = formUsuario.querySelectorAll(".is-invalid");
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
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormRol').modal("hide");
                        formRol.reset();
                        swal("Roles", objData.msg, "success");
                        tableRoles.ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
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

        if (btnEdit) fntEditRol(btnEdit.getAttribute('rl'));
        if (btnDel) fntDelRol(btnDel.getAttribute('rl'));
        if (btnPerm) fntPermisos(btnPerm.getAttribute('rl'));
    });

});

/**
 * FUNCIONES DE APOYO
 */

function openModal() {
    document.querySelector('#idRol').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
    document.querySelector("#formRol").reset();

    // Reset select status
    $('#listStatus').val('1').selectpicker('refresh');
    $('#modalFormRol').modal('show');
}

function fntEditRol(idRol) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Rol";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary"); // Info es para update
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = new XMLHttpRequest();
    request.open("GET", BASE_URL_API + '/roles/getRol/' + idRol, true);
    request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                document.querySelector("#idRol").value = objData.data.id_rol;
                document.querySelector("#txtNombre").value = objData.data.nombre_rol;
                document.querySelector("#txtDescripcion").value = objData.data.descript_rol;

                // --- AJUSTE SELECTPICKER ---
                $('#listStatus').selectpicker('destroy');
                document.querySelector('#listStatus').value = String(objData.data.status_rol);
                $('#listStatus').selectpicker();
                // ---------------------------

                $('#modalFormRol').modal('show');
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
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Eliminado!", objData.msg, "success");
                        tableRoles.ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
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
        if (request.readyState == 4 && request.status == 200) {
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
                            <td><div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][r]" ${pR}><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div></td>
                            <td><div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][w]" ${pW}><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div></td>
                            <td><div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][u]" ${pU}><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div></td>
                            <td><div class="toggle-flip"><label><input type="checkbox" name="modulos[${modulo.id_modulo}][d]" ${pD}><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span></label></div></td>
                        </tr>`;
                    no++;
                });

                document.querySelector('#contentAjax').innerHTML = htmlTable;
                if (document.querySelector('#idrol')) document.querySelector('#idrol').value = idRol;
                $('.modalPermisos').modal('show');

                // Asignar el submit del formulario de permisos
                document.querySelector('#formPermisos').onsubmit = fntSavePermisos;
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
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                swal("Permisos", objData.msg, "success");
                $('.modalPermisos').modal('hide');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    };
}