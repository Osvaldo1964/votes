
var tableRoles;

document.addEventListener('DOMContentLoaded', function () {

    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjU4Mzg5NDcsImV4cCI6MTc2NTkyNTM0N30.-nQ6aclVvcY5qtZvA3dAHgCT20QZ--VUzCjr8u4A1yU2xTs0TBX-yarTyMXw_167oPFAd_CgGFKbvD1lRsM9Rg"; // Tu token
    var apiUrl = "http://api-votes.com/roles/getRoles";

    // Usar 'DataTable' con D mayúscula es la convención moderna
    var tableRoles = $('#tableRoles').DataTable({
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

$('#tableRoles').DataTable();

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
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjU4Mzg5NDcsImV4cCI6MTc2NTkyNTM0N30.-nQ6aclVvcY5qtZvA3dAHgCT20QZ--VUzCjr8u4A1yU2xTs0TBX-yarTyMXw_167oPFAd_CgGFKbvD1lRsM9Rg"; // Tu token
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
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjU4Mzg5NDcsImV4cCI6MTc2NTkyNTM0N30.-nQ6aclVvcY5qtZvA3dAHgCT20QZ--VUzCjr8u4A1yU2xTs0TBX-yarTyMXw_167oPFAd_CgGFKbvD1lRsM9Rg"; // Tu token
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
                                tableRoles.api().ajax.reload(function () {
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
    btnPermisosRol.forEach(function (btnPermisosRol) {
        btnPermisosRol.addEventListener('click', function () {

            var idrol = this.getAttribute("rl");
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url + '/Permisos/getPermisosRol/' + idrol;
            request.open("GET", ajaxUrl, true);
            request.send();

            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    document.querySelector('#contentAjax').innerHTML = request.responseText;
                    $('.modalPermisos').modal('show');
                    document.querySelector('#formPermisos').addEventListener('submit', fntSavePermisos, false);
                }
            }


        });
    });
}

function fntSavePermisos(evnet) {
    evnet.preventDefault();
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url + '/Permisos/setPermisos';
    var formElement = document.querySelector("#formPermisos");
    var formData = new FormData(formElement);
    request.open("POST", ajaxUrl, true);
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