var tableUsuarios;

document.addEventListener('DOMContentLoaded', function () {

    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ"; // Tu token
    var apiUrl = "http://api-votes.com/usuario/getUsers";

    // Usar 'DataTable' con D mayúscula es la convención moderna
    tableUsuarios = $('#tableUsuarios').DataTable({
        "processing": true,     // Antes aProcessing
        "serverSide": false,    // ¡IMPORTANTE! (Ver explicación abajo)
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "infoThousands": ",",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            },
            "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        },
        "ajax": {
            "url": apiUrl,
            "type": "GET",
            "headers": {
                "Authorization": "Bearer " + miToken
            },
            "dataSrc": "data"
        }, // <--- Faltaba cerrar correctamente el objeto AJAX antes de 'columns'
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
        "responsive": true,  // <--- Corregido (decía "resonsieve")
        "destroy": true,     // Antes bDestroy
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    //NUEVO USUARIO
    var formUsuario = document.querySelector("#formUsuario");
    formUsuario.onsubmit = function (e) {
        e.preventDefault();

        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = 'http://api-votes.com/usuario/setUsuario';
        var formData = new FormData(formUsuario);

        request.open("POST", ajaxUrl, true);
        request.setRequestHeader('Authorization', 'Bearer ' + miToken);
        request.send(formData);

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);

                if (objData.status) {
                    // 1. Cerrar el modal correcto
                    $('#modalFormUsuario').modal("hide");

                    // 2. Limpiar el formulario para la próxima vez
                    formUsuario.reset();

                    swal("Usuarios", objData.msg, "success");

                    if (tableUsuarios) {
                        tableUsuarios.ajax.reload(function () {
                            fntEditUsuario();
                            fntDelUsuario();
                        }, false);
                    }
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

var btnNuevo = document.querySelector('#btnNuevoUsuario'); // Cambia el ID según tu HTML

if (btnNuevo) {
    btnNuevo.addEventListener('click', function () {
        // Primero cargamos los datos de los roles
        fntRolesUsuario();
        // Luego abrimos el modal (si usas Bootstrap)
        $('#modalFormUsuario').modal('show');
    }, false);
}

function fntRolesUsuario() {
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = 'http://api-votes.com/roles/getSelectRoles';

    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + miToken);
    request.setRequestHeader('Accept', 'application/json');
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                // Iniciamos con una opción vacía para forzar al usuario a elegir
                var html = '<option value="" selected disabled>Seleccione un Rol</option>';
                var data = objData.data;

                data.forEach(function (item) {
                    html += '<option value="' + item.id_rol + '">' + item.nombre_rol + '</option>';
                });

                // Inyectamos en el select
                document.querySelector('#listRolid').innerHTML = html;
                $('#listRolid').selectpicker('refresh');

                // Si usas alguna librería como Select2 o bootstrap-select, debes refrescarlo aquí:
                // $('#listRoles').selectpicker('refresh'); 

            } else {
                swal("Error", "No se pudieron cargar los roles", "error");
            }
        }
    }
}
function openModal() {
    document.querySelector('#idUsuario').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
    document.querySelector("#formUsuario").reset();
    $('#modalFormUsuario').modal('show');
}

window.addEventListener('load', function () {
    fntEditUsuario();
    fntDelUsuario();
    fntPermisos();
}, false);

function fntEditUsuario() {
    var btnEditUsuario = document.querySelectorAll(".btnEditUsuario");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ"; // Tu token
    btnEditUsuario.forEach(function (btnEditUsuario) {
        btnEditUsuario.addEventListener('click', function () {

            document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
            document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
            document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
            document.querySelector('#btnText').innerHTML = "Actualizar";

            var idUsuario = this.getAttribute("rl");
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = 'http://api-votes.com/roles/getRol/' + idrol;
            request.open("GET", ajaxUrl, true);
            // --- AGREGO LOS HEADERS ---
            request.setRequestHeader('Authorization', 'Bearer ' + miToken);
            request.setRequestHeader('Accept', 'application/json');
            // ----------------------------
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {

                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        document.querySelector("#idRol").value = objData.data.id_rol;
                        document.querySelector("#txtNombre").value = objData.data.nombre_rol;
                        document.querySelector("#txtDescripcion").value = objData.data.descript_rol;

                        if (objData.data.status_rol == 1) {
                            var optionSelect = '<option value="1" selected class="notBlock">Activo</option>';
                        } else {
                            var optionSelect = '<option value="2" selected class="notBlock">Inactivo</option>';
                        }
                        var htmlSelect = `${optionSelect}
                                          <option value="1">Activo</option>
                                          <option value="2">Inactivo</option>
                                        `;
                        document.querySelector("#listStatus").innerHTML = htmlSelect;
                        $('#modalFormRol').modal('show');
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }

        });
    });
}

function fntDelUsuario() {
    var btnDelUsuario = document.querySelectorAll(".btnDelUsuario");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwMDk0MzcsImV4cCI6MTc2NjA5NTgzN30.s8pRhBWGfx_ecPxedcC5yPbw_GsBwXHEICAwreUV4NX5rs8T-l27q4u-Jt71-fNJVBx3nwjlfbNAGLAq_gDwGQ"; // Tu token
    btnDelUsuario.forEach(function (btnDelUsuario) {
        btnDelUsuario.addEventListener('click', function () {
            var idusuario = this.getAttribute("us");
            swal({
                title: "Eliminar Usuario",
                text: "¿Realmente quiere eliminar el Usuario?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "No, cancelar!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {

                if (isConfirm) {
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = 'http://api-votes.com/usuarios/delUsuario/';
                    var jsonParams = JSON.stringify({ idusuario: idusuario });
                    request.open("PUT", ajaxUrl, true);
                    // --- AGREGO LOS HEADERS ---
                    request.setRequestHeader('Authorization', 'Bearer ' + miToken);
                    request.setRequestHeader('Accept', 'application/json');
                    // ----------------------------
                    request.send(jsonParams);
                    request.onreadystatechange = function () {
                        if (request.readyState == 4 && request.status == 200) {
                            var objData = JSON.parse(request.responseText);
                            if (objData.status) {
                                swal("Eliminar!", objData.msg, "success");
                                tableRoles.ajax.reload(function () {
                                    fntEditRol();
                                    fntDelRol();
                                    fntPermisos();
                                });
                            } else {
                                swal("Atención!", objData.msg, "error");
                            }
                        }
                    }
                }
            });
        });
    });
}

function fntPermisos() {
    var btnPermisosRol = document.querySelectorAll(".btnPermisosRol");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwMDk0MzcsImV4cCI6MTc2NjA5NTgzN30.s8pRhBWGfx_ecPxedcC5yPbw_GsBwXHEICAwreUV4NX5rs8T-l27q4u-Jt71-fNJVBx3nwjlfbNAGLAq_gDwGQ";

    btnPermisosRol.forEach(function (btnPermisosRol) {
        btnPermisosRol.addEventListener('click', function () {

            var idrol = this.getAttribute("rl");
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = 'http://api-votes.com/permisos/getPermisosRol/' + idrol;

            request.open("GET", ajaxUrl, true);
            request.setRequestHeader('Authorization', 'Bearer ' + miToken);
            request.setRequestHeader('Accept', 'application/json');
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {

                    var objResponse = JSON.parse(request.responseText);

                    if (objResponse.status == true) {
                        var arrModulos = objResponse.data;
                        var htmlTable = "";
                        var no = 1; // Contador para la columna #

                        arrModulos.forEach(function (modulo) {

                            // 1. Validar si está chequeado o no
                            var pR = modulo.permisos.r == 1 ? "checked" : "";
                            var pW = modulo.permisos.w == 1 ? "checked" : "";
                            var pU = modulo.permisos.u == 1 ? "checked" : "";
                            var pD = modulo.permisos.d == 1 ? "checked" : "";

                            htmlTable += '<tr>';

                            // Columna #: Mostramos contador y guardamos ID modulo oculto
                            htmlTable += '<td>' + no + '<input type="hidden" name="modulos[' + modulo.id_modulo + '][idmodulo]" value="' + modulo.id_modulo + '" required></td>';

                            // Columna Nombre Módulo
                            htmlTable += '<td>' + modulo.tittulo_modulo + '</td>';

                            // --- Toggle VER (R) ---
                            htmlTable += '<td><div class="toggle-flip"><label>';
                            htmlTable += '<input type="checkbox" name="modulos[' + modulo.id_modulo + '][r]" ' + pR + '><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span>';
                            htmlTable += '</label></div></td>';

                            // --- Toggle CREAR (W) ---
                            htmlTable += '<td><div class="toggle-flip"><label>';
                            htmlTable += '<input type="checkbox" name="modulos[' + modulo.id_modulo + '][w]" ' + pW + '><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span>';
                            htmlTable += '</label></div></td>';

                            // --- Toggle ACTUALIZAR (U) ---
                            htmlTable += '<td><div class="toggle-flip"><label>';
                            htmlTable += '<input type="checkbox" name="modulos[' + modulo.id_modulo + '][u]" ' + pU + '><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span>';
                            htmlTable += '</label></div></td>';

                            // --- Toggle ELIMINAR (D) ---
                            htmlTable += '<td><div class="toggle-flip"><label>';
                            htmlTable += '<input type="checkbox" name="modulos[' + modulo.id_modulo + '][d]" ' + pD + '><span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span>';
                            htmlTable += '</label></div></td>';

                            htmlTable += '</tr>';
                            no++; // Aumentamos el contador
                        });

                        // 2. Inyectamos el HTML en el tbody limpio
                        document.querySelector('#contentAjax').innerHTML = htmlTable;

                        // 3. Asignamos el ID Rol al input hidden
                        if (document.querySelector('#idrol')) {
                            document.querySelector('#idrol').value = idrol;
                        }

                        // 4. Mostramos Modal
                        $('.modalPermisos').modal('show');

                        // 5. Asignar evento de guardado (quitando el anterior para no duplicar)
                        var formPermisos = document.querySelector('#formPermisos');
                        if (formPermisos) {
                            formPermisos.removeEventListener('submit', fntSavePermisos); // Limpieza preventiva
                            formPermisos.addEventListener('submit', fntSavePermisos, false);
                        }

                    } else {
                        swal("Error", objResponse.msg, "error");
                    }
                }
            }
        });
    });
}

function fntSavePermisos(event) {
    //event.preventDefault();
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwMDk0MzcsImV4cCI6MTc2NjA5NTgzN30.s8pRhBWGfx_ecPxedcC5yPbw_GsBwXHEICAwreUV4NX5rs8T-l27q4u-Jt71-fNJVBx3nwjlfbNAGLAq_gDwGQ";
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = 'http://api-votes.com/permisos/setPermisos';
    var formElement = document.querySelector("#formPermisos");
    var formData = new FormData(formElement);
    request.open("POST", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + miToken);
    request.setRequestHeader('Accept', 'application/json');
    request.send(formData);

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                swal("Permisos de usuario", objData.msg, "success");
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }

}