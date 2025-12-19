var tableUsuarios;

document.addEventListener('DOMContentLoaded', function () {

    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwOTYyNDgsImV4cCI6MTc2NjE4MjY0OH0.emJM6JJz-KAXMY6xsxGhJN9rntsd5BCt_sdIqOiR63ecL9dzxcIL3QUJd0zKTYJKpPXfAXuVzSULy43iF8sXzg"; // Tu token
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

document.addEventListener('click', function (e) {
    if (e.target && (e.target.classList.contains('btnViewUsuario') || e.target.closest('.btnViewUsuario'))) {
        // Obtenemos el atributo 'us' que ya tienes en el botón
        const btn = e.target.closest('.btnViewUsuario');
        const idUsuario = btn.getAttribute('us');
        fntViewUsuario(idUsuario);
    }
});

document.addEventListener('click', function (e) {
    if (e.target && (e.target.classList.contains('btnDelUsuario') || e.target.closest('.btnDelUsuario'))) {
        // Obtenemos el atributo 'us' que ya tienes en el botón
        const btn = e.target.closest('.btnDelUsuario');
        const idUsuario = btn.getAttribute('us');
        alert(idUsuario);
        fntDelUsuario(idUsuario);
    }
});

function fntRolesUsuario() {
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwOTYyNDgsImV4cCI6MTc2NjE4MjY0OH0.emJM6JJz-KAXMY6xsxGhJN9rntsd5BCt_sdIqOiR63ecL9dzxcIL3QUJd0zKTYJKpPXfAXuVzSULy43iF8sXzg";
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
                var html = '<option value="">Seleccione un Rol</option>';
                var data = objData.data;

                data.forEach(function (item) {
                    html += '<option value="' + item.id_rol + '">' + item.nombre_rol + '</option>';
                });

                // 1. Inyectar el HTML
                $('#listRolid').html(html);

                // 2. LA CLAVE: Destruir y volver a inicializar
                $('#listRolid').selectpicker('destroy'); // Elimina la estructura vieja
                $('#listRolid').selectpicker();          // Crea la estructura nueva con el HTML actual

                // 3. Forzar selección del primero y refrescar
                $('#listRolid').val('');
                $('#listRolid').selectpicker('refresh');

            } else {
                swal("Error", "No se pudieron cargar los roles", "error");
            }
        }
    }
}

function fntViewUsuario(idPersona) {
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwOTYyNDgsImV4cCI6MTc2NjE4MjY0OH0.emJM6JJz-KAXMY6xsxGhJN9rntsd5BCt_sdIqOiR63ecL9dzxcIL3QUJd0zKTYJKpPXfAXuVzSULy43iF8sXzg"; // Idealmente obtenido de localStorage
    var ajaxUrl = 'http://api-votes.com/usuario/getUsuario/' + idPersona;
    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + miToken);
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
            } else {
                swal("Error", objData.msg, "error");
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
}, false);

function fntEditUsuario() {
    var btnEditUsuario = document.querySelectorAll(".btnEditUsuario");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwOTYyNDgsImV4cCI6MTc2NjE4MjY0OH0.emJM6JJz-KAXMY6xsxGhJN9rntsd5BCt_sdIqOiR63ecL9dzxcIL3QUJd0zKTYJKpPXfAXuVzSULy43iF8sXzg"; // Tu token
    btnEditUsuario.forEach(function (btnEditUsuario) {
        btnEditUsuario.addEventListener('click', function () {

            document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
            document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
            document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
            document.querySelector('#btnText').innerHTML = "Actualizar";

            var idUsuario = this.getAttribute("us");
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = 'http://api-votes.com/usuarios/getUsuario/' + idUsuario;
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

function fntDelUsuario(idusuario) {
    var btnDelUsuario = document.querySelectorAll(".btnDelUsuario");
    var miToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpZF9zcCI6OSwic2NvcGUiOiJFbXByZXNhIFVubyIsImVtYWlsIjoiZW1wcmVzYXVub0BnbWFpbC5jb20iLCJpYXQiOjE3NjYwOTYyNDgsImV4cCI6MTc2NjE4MjY0OH0.emJM6JJz-KAXMY6xsxGhJN9rntsd5BCt_sdIqOiR63ecL9dzxcIL3QUJd0zKTYJKpPXfAXuVzSULy43iF8sXzg"; // Tu token
    btnDelUsuario.forEach(function (btnDelUsuario) {
        btnDelUsuario.addEventListener('click', function () {
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
                    var ajaxUrl = 'http://api-votes.com/usuario/delUsuario/' + idusuario;

                    request.open("PUT", ajaxUrl, true);
                    // --- AGREGO LOS HEADERS ---
                    request.setRequestHeader('Authorization', 'Bearer ' + miToken);
                    request.setRequestHeader('Accept', 'application/json');
                    // ----------------------------
                    request.send();
                    request.onreadystatechange = function () {
                        if (request.readyState == 4 && request.status == 200) {
                            var objData = JSON.parse(request.responseText);
                            if (objData.status) {
                                swal("Eliminar!", objData.msg, "success");
                                tableRoles.ajax.reload(function () {
                                    fntEditRol();
                                    fntDelRol();
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
