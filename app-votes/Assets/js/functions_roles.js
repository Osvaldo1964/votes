var tableRoles;

document.addEventListener('DOMContentLoaded', function () {

    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ"; // Tu token
    var apiUrl = "http://api-votes.com/roles/getRoles";

    // Usar 'DataTable' con D mayúscula es la convención moderna
    tableRoles = $('#tableRoles').DataTable({
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
            { "data": "id_rol" },
            { "data": "nombre_rol" },
            { "data": "descript_rol" },
            { "data": "status_rol" },
            { "data": "options" }
        ],
        "responsive": true,  // <--- Corregido (decía "resonsieve")
        "destroy": true,     // Antes bDestroy
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    //NUEVO ROL
    var formRol = document.querySelector("#formRol");
    formRol.onsubmit = function (e) {
        e.preventDefault();

        var intIdRol = document.querySelector('#idRol').value;
        var strNombre = document.querySelector('#txtNombre').value;
        var strDescripcion = document.querySelector('#txtDescripcion').value;
        var intStatus = document.querySelector('#listStatus').value;
        if (strNombre == '' || strDescripcion == '' || intStatus == '') {
            swal("Atención", "Todos los campos son obligatorios.", "error");
            return false;
        }
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = 'http://api-votes.com/roles/setRol';
        var formData = new FormData(formRol);
        request.open("POST", ajaxUrl, true);
        // --- AGREGO LOS HEADERS ---
        request.setRequestHeader('Authorization', 'Bearer ' + miToken);
        request.setRequestHeader('Accept', 'application/json');
        // ----------------------------
        request.send(formData);
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {

                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    $('#modalFormRol').modal("hide");
                    formRol.reset();
                    swal("Roles de usuario", objData.msg, "success");
                    tableRoles.ajax.reload(function () {
                        fntEditRol();
                        fntDelRol();
                        fntPermisos();
                    });
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
        }
    }
});

//$('#tableRoles').DataTable();

function openModal() {
    document.querySelector('#idRol').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
    document.querySelector("#formRol").reset();
    $('#modalFormRol').modal('show');
}

window.addEventListener('load', function () {
    fntEditRol();
    fntDelRol();
    fntPermisos();
}, false);

function fntEditRol() {
    var btnEditRol = document.querySelectorAll(".btnEditRol");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ"; // Tu token
    btnEditRol.forEach(function (btnEditRol) {
        btnEditRol.addEventListener('click', function () {

            document.querySelector('#titleModal').innerHTML = "Actualizar Rol";
            document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
            document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
            document.querySelector('#btnText').innerHTML = "Actualizar";

            var idrol = this.getAttribute("rl");
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

function fntDelRol() {
    var btnDelRol = document.querySelectorAll(".btnDelRol");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ"; // Tu token
    btnDelRol.forEach(function (btnDelRol) {
        btnDelRol.addEventListener('click', function () {
            var idrol = this.getAttribute("rl");
            swal({
                title: "Eliminar Rol",
                text: "¿Realmente quiere eliminar el Rol?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Si, eliminar!",
                cancelButtonText: "No, cancelar!",
                closeOnConfirm: false,
                closeOnCancel: true
            }, function (isConfirm) {

                if (isConfirm) {
                    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                    var ajaxUrl = 'http://api-votes.com/roles/delRol/';
                    var jsonParams = JSON.stringify({ idrol: idrol });
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
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ";

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
                    //console.log(objResponse);
                    if (objResponse.status == true) {
                        var arrModulos = objResponse.data;
                        var htmlTable = "";
                        var no = 1; // Contador para la columna #

                        arrModulos.forEach(function (modulo) {

                            // 1. Validar si está chequeado o no
                            //console.log(modulo.permisos);
                            var pR = (modulo.permisos && modulo.permisos.r == 1) ? "checked" : "";
                            var pW = (modulo.permisos && modulo.permisos.w == 1) ? "checked" : "";
                            var pU = (modulo.permisos && modulo.permisos.u == 1) ? "checked" : "";
                            var pD = (modulo.permisos && modulo.permisos.d == 1) ? "checked" : "";

                            htmlTable += '<tr>';

                            // Columna #: Mostramos contador y guardamos ID modulo oculto
                            htmlTable += '<td>' + no + '<input type="hidden" name="modulos[' + modulo.id_modulo + '][idmodulo]" value="' + modulo.id_modulo + '" required></td>';

                            // Columna Nombre Módulo
                            htmlTable += '<td>' + modulo.titulo_modulo + '</td>';

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
    event.preventDefault(); // 1. Evita que la página se recargue

    var formPermisos = document.querySelector('#formPermisos');
    var formData = new FormData(formPermisos); // 2. Captura todos los inputs y checkboxes
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = 'http://api-votes.com/permisos/setPermisos'; // Tu endpoint para guardar

    request.open("POST", ajaxUrl, true);
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwNzMxNTQsImV4cCI6MTc2NjE1OTU1NH0.t-uczLWgdgnB5xrfCvHPlwccB_RJqVKNoXMFn87wgLoPFuetKjfVOqns_b3eoeGle3Ox9WCOB97Lo1Fv2VCDcQ";
    request.setRequestHeader('Authorization', 'Bearer ' + miToken);
    request.send(formData);

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                swal("Permisos", objData.msg, "success");
                $('.modalPermisos').modal('hide'); // Cerramos el modal tras éxito
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    };
}