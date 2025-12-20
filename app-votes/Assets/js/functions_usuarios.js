/**
 * CONFIGURACIÓN GLOBAL
 * Centralizamos para facilitar el mantenimiento
 */
const BASE_URL = "http://api-votes.com";
const MI_TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYxNjc0MTMsImV4cCI6MTc2NjI1MzgxM30.CpK3aqP-1JWpv1bdIkFwRVSKKKvxGu5FzUgbiFa38ky99eXaJSvnXap_JOO3ZipyEoHGG4EGAJdWP-ZiT0Ia_A";

var tableUsuarios;

document.addEventListener('DOMContentLoaded', function () {

    // 1. INICIALIZACIÓN DE DATATABLE
    tableUsuarios = $('#tableUsuarios').DataTable({
        "processing": true,
        "serverSide": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json" // Más limpio que escribir todo el JSON
        },
        "ajax": {
            "url": BASE_URL + "/usuario/getUsers",
            "type": "GET",
            "headers": { "Authorization": "Bearer " + MI_TOKEN },
            "dataSrc": "data"
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
            var ajaxUrl = BASE_URL + '/usuario/setUsuario';

            request.open("POST", ajaxUrl, true);
            request.setRequestHeader('Authorization', 'Bearer ' + MI_TOKEN);
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormUsuario').modal("hide");
                        formUsuario.reset();
                        swal("Usuarios", objData.msg, "success");
                        tableUsuarios.ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }

    // 3. DELEGACIÓN DE EVENTOS (CLICK GLOBAL)
    // Esto maneja todos los botones de la tabla, incluso después de recargar AJAX
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

    // Cargar roles al inicio
    fntRolesUsuario();
});

/**
 * FUNCIONES DE APOYO
 */

function fntRolesUsuario() {
    var ajaxUrl = BASE_URL + '/roles/getSelectRoles';
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + MI_TOKEN);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
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
        }
    }
}

function fntViewUsuario(idUsuario) {
    var ajaxUrl = BASE_URL + '/usuario/getUsuario/' + idUsuario;
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + MI_TOKEN);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
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
        }
    }
}

function fntEditUsuario(idUsuario) {
    // Ajustes visuales del modal
    document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var ajaxUrl = BASE_URL + '/usuario/getUsuario/' + idUsuario;
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + MI_TOKEN);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                document.querySelector('#idUsuario').value = objData.data.id_usuario;
                document.querySelector('#txtNombre').value = objData.data.nombres_usuario;
                document.querySelector('#txtApellido').value = objData.data.apellidos_usuario;
                document.querySelector('#txtTelefono').value = objData.data.telefono_usuario;
                document.querySelector('#txtEmail').value = objData.data.email_usuario;

                // Actualización de Selects (Destruir y Reconstruir)
                // 1. Identifica el ID correcto (Asegúrate que el nombre coincida con tu JSON)
                let idRol = objData.data.id_rol || objData.data.rol_usuario || objData.data.id_rol_usuario;

                // 2. Destrucción total y asignación forzada
                $('#listRolid').selectpicker('destroy'); // Quitamos la capa de Bootstrap
                document.querySelector('#listRolid').value = String(idRol); // Forzamos el valor al HTML puro
                $('#listRolid').selectpicker(); // Volvemos a inicializar
                $('#listRolid').selectpicker('refresh'); // Sincronizamos

                $('#listStatus').selectpicker('destroy'); // Quitamos la capa de Bootstrap
                document.querySelector('#listStatus').value = String(objData.data.estado_usuario); // Forzamos el valor al HTML puro
                $('#listStatus').selectpicker(); // Volvemos a inicializar
                $('#listStatus').selectpicker('refresh'); // Sincronizamos

                $('#modalFormUsuario').modal('show');
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
            var ajaxUrl = BASE_URL + '/usuario/delUsuario/' + idUsuario;
            request.open("PUT", ajaxUrl, true);
            request.setRequestHeader('Authorization', 'Bearer ' + MI_TOKEN);
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Eliminado!", objData.msg, "success");
                        tableUsuarios.ajax.reload();
                    } else {
                        swal("Error!", objData.msg, "error");
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

    // Resetear selects al abrir nuevo
    $('#listRolid').val('').selectpicker('refresh');
    $('#listStatus').val('1').selectpicker('refresh');

    $('#modalFormUsuario').modal('show');
}