const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

var tableUsuarios;
// Evitar alerts nativos de DataTables
$.fn.dataTable.ext.errMode = 'none';

document.addEventListener('DOMContentLoaded', function () {
    // 1. INICIALIZACIÓN DE DATATABLE
    tableUsuarios = $('#tableUsuarios').DataTable({
        "processing": true,
        "serverSide": false,
        "language": {
            "url": BASE_URL + "/assets/json/spanish.json"
        },
        "ajax": {
            "url": BASE_URL_API + "/usuario/getUsers",
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
            { "data": "id_usuario" },
            { "data": "nombres_usuario" },
            { "data": "apellidos_usuario" },
            { "data": "telefono_usuario" },
            { "data": "email_usuario" },
            { "data": "nombre_rol" },
            { "data": "estado_usuario" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "displayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. ENVÍO DEL FORMULARIO (NUEVO/ACTUALIZAR)
    var formUsuario = document.querySelector("#formUsuario");
    if (formUsuario) {
        formUsuario.onsubmit = function (e) {
            e.preventDefault();

            let elements = formUsuario.querySelectorAll(".is-invalid");
            if (elements.length > 0) {
                elements[0].focus();
                return;
            }

            var formData = new FormData(formUsuario);
            var request = new XMLHttpRequest();
            var ajaxUrl = BASE_URL_API + '/usuario/setUsuario';

            request.open("POST", ajaxUrl, true);
            request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            $('#modalFormUsuario').modal("hide");
                            formUsuario.reset();
                            swal("Usuarios", objData.msg, "success");
                            tableUsuarios.ajax.reload();
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
        const btnView = e.target.closest('.btnViewUsuario');
        const btnEdit = e.target.closest('.btnEditUsuario');
        const btnDel = e.target.closest('.btnDelUsuario');
        const btnNuevo = e.target.closest('#btnNuevoUsuario');

        if (btnNuevo) openModal();
        if (btnView) fntViewUsuario(btnView.getAttribute('us'));
        if (btnEdit) fntEditUsuario(btnEdit.getAttribute('us'));
        if (btnDel) fntDelUsuario(btnDel.getAttribute('us'));
    });

    fntRolesUsuario();
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

function fntRolesUsuario() {
    var ajaxUrl = BASE_URL_API + '/roles/getSelectRoles';
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    var html = '<option value="">Seleccione un Rol</option>';
                    objData.data.forEach(item => {
                        if (item.status_rol == 1) {
                            html += `<option value="${item.id_rol}">${item.nombre_rol}</option>`;
                        }
                    });
                    $('#listRolid').html(html).selectpicker('destroy').selectpicker();
                }
            } else {
                fntHandleError(request);
            }
        }
    }
}

function fntViewUsuario(idUsuario) {
    var ajaxUrl = BASE_URL_API + '/usuario/getUsuario/' + idUsuario;
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    document.querySelector('#celNombre').innerHTML = objData.data.nombres_usuario;
                    document.querySelector('#celApellido').innerHTML = objData.data.apellidos_usuario;
                    document.querySelector('#celTelefono').innerHTML = objData.data.telefono_usuario;
                    document.querySelector('#celEmail').innerHTML = objData.data.email_usuario;
                    document.querySelector('#celTipoUsuario').innerHTML = objData.data.nombre_rol;
                    var estado = objData.data.estado_usuario == 1
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                    document.querySelector('#celEstado').innerHTML = estado;
                    $('#modalViewUser').modal('show');
                }
            } else {
                fntHandleError(request);
            }
        }
    }
}

function fntEditUsuario(idUsuario) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var ajaxUrl = BASE_URL_API + '/usuario/getUsuario/' + idUsuario;
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    document.querySelector('#idUsuario').value = objData.data.id_usuario;
                    document.querySelector('#txtNombre').value = objData.data.nombres_usuario;
                    document.querySelector('#txtApellido').value = objData.data.apellidos_usuario;
                    document.querySelector('#txtTelefono').value = objData.data.telefono_usuario;
                    document.querySelector('#txtEmail').value = objData.data.email_usuario;

                    let idRol = objData.data.id_rol || objData.data.rol_usuario;

                    $('#listRolid').selectpicker('destroy');
                    document.querySelector('#listRolid').value = String(idRol);
                    $('#listRolid').selectpicker();
                    $('#listRolid').selectpicker('refresh');

                    $('#listStatus').selectpicker('destroy');
                    document.querySelector('#listStatus').value = String(objData.data.estado_usuario);
                    $('#listStatus').selectpicker();
                    $('#listStatus').selectpicker('refresh');

                    $('#modalFormUsuario').modal('show');
                }
            } else {
                fntHandleError(request);
            }
        }
    }
}

function fntDelUsuario(idUsuario) {
    swal({
        title: "Eliminar Usuario",
        text: "¿Realmente quiere eliminar el Usuario?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false
    }, function (isConfirm) {
        if (isConfirm) {
            var request = new XMLHttpRequest();
            var ajaxUrl = BASE_URL_API + '/usuario/delUsuario/' + idUsuario;
            request.open("PUT", ajaxUrl, true);
            request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            swal("Eliminado!", objData.msg, "success");
                            tableUsuarios.ajax.reload();
                        } else {
                            swal("Error!", objData.msg, "error");
                        }
                    } else {
                        fntHandleError(request);
                    }
                }
            }
        }
    });
}

function openModal() {
    document.querySelector('#idUsuario').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
    document.querySelector("#formUsuario").reset();
    $('#listRolid').val('').selectpicker('refresh');
    $('#listStatus').val('1').selectpicker('refresh');
    $('#modalFormUsuario').modal('show');
}