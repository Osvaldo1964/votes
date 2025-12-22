const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

var tableCandidatos;
// Evitar alerts nativos de DataTables
$.fn.dataTable.ext.errMode = 'none';

document.addEventListener('DOMContentLoaded', function () {

    // 1. INICIALIZACIÓN DE DATATABLE
    tableCandidatos = $('#tableCandidatos').DataTable({
        "processing": true,
        "serverSide": false,
        "language": {
            "url": BASE_URL + "/assets/json/spanish.json"
        },
        "ajax": {
            "url": BASE_URL_API + "/candidatos/getCandidatos",
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
            { "data": "id_candidato" },
            {
                "data": null, // 'null' porque usaremos varios campos
                "render": function (data, type, row) {
                    // concatenamos nombre 1 y nombre 2
                    return row.nom1_candidato + ' ' + (row.nom2_candidato || "");
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    // concatenamos apellido 1 y apellido 2
                    return row.ape1_candidato + ' ' + (row.ape2_candidato || "");
                }
            },
            { "data": "telefono_candidato" },
            { "data": "email_candidato" },
            { "data": "direccion_candidato" },
            { "data": "curul_candidato" },
            { "data": "partido_candidato" },
            { "data": "estado_candidato" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "displayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. GUARDAR CANDIDATO (NUEVO/ACTUALIZAR)
    var formCandidato = document.querySelector("#formCandidato");
    if (formCandidato) {
        formCandidato.onsubmit = function (e) {
            e.preventDefault();

            let elements = formCandidato.querySelectorAll(".is-invalid");
            if (elements.length > 0) {
                elements[0].focus();
                return;
            }

            var formData = new FormData(formCandidato);
            var request = new XMLHttpRequest();
            request.open("POST", BASE_URL_API + '/candidatos/setCandidato', true);
            request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
            request.send(formData);

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            $('#modalFormCandidato').modal("hide");
                            formCandidato.reset();
                            swal("Candidatos", objData.msg, "success");
                            tableCandidatos.ajax.reload();
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
        const btnView = e.target.closest('.btnView');
        const btnEdit = e.target.closest('.btnEdit');
        const btnDel = e.target.closest('.btnDel');
        const btnNuevo = e.target.closest('#btnNuevoCandidato');

        if (btnNuevo) openModal();
        if (btnView) fntViewCandidato(btnView.getAttribute('can'));
        if (btnEdit) fntEditCandidato(btnEdit.getAttribute('can'));
        if (btnDel) fntDelCandidato(btnDel.getAttribute('can'));
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

function fntViewCandidato(idCandidato) {
    var ajaxUrl = BASE_URL_API + '/candidatos/getCandidato/' + idCandidato;
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    document.querySelector('#celNombre').innerHTML = objData.data.nom1_candidato + ' ' + objData.data.nom2_candidato;
                    document.querySelector('#celApellido').innerHTML = objData.data.ape1_candidato + ' ' + objData.data.ape2_candidato;
                    document.querySelector('#celTelefono').innerHTML = objData.data.telefono_candidato;
                    document.querySelector('#celEmail').innerHTML = objData.data.email_candidato;
                    document.querySelector('#celPartido').innerHTML = objData.data.partido_candidato;
                    var estado = objData.data.estado_candidato == 1
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>';
                    document.querySelector('#celEstado').innerHTML = estado;
                    $('#modalViewCandidato').modal('show');
                }
            } else {
                fntHandleError(request);
            }
        }
    }
}

function fntEditCandidato(idCandidato) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Candidato";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = new XMLHttpRequest();
    request.open("GET", BASE_URL_API + '/candidatos/getCandidato/' + idCandidato, true);
    request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    document.querySelector("#idCandidato").value = objData.data.id_candidato;
                    document.querySelector("#txtNombre").value = objData.data.nom1_candidato + ' ' + objData.data.nom2_candidato;
                    document.querySelector("#txtApellido").value = objData.data.ape1_candidato + ' ' + objData.data.ape2_candidato;
                    document.querySelector("#txtTelefono").value = objData.data.telefono_candidato;
                    document.querySelector("#txtEmail").value = objData.data.email_candidato;
                    document.querySelector("#txtPartido").value = objData.data.partido_candidato;

                    $('#listEstado').selectpicker('destroy');
                    document.querySelector('#listEstado').value = String(objData.data.estado_candidato);
                    $('#listEstado').selectpicker();
                    $('#listEstado').selectpicker('refresh');

                    $('#modalFormCandidato').modal('show');
                }
            } else {
                fntHandleError(request);
            }
        }
    }
}

function fntDelCandidato(idCandidato) {
    swal({
        title: "Eliminar Candidato",
        text: "¿Realmente quiere eliminar el Candidato?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        closeOnConfirm: false
    }, function (isConfirm) {
        if (isConfirm) {
            var request = new XMLHttpRequest();
            var jsonParams = JSON.stringify({ idcandidato: idCandidato });
            request.open("PUT", BASE_URL_API + '/candidatos/delCandidato/', true);
            request.setRequestHeader('Authorization', 'Bearer ' + localStorage.getItem('userToken'));
            request.setRequestHeader('Content-Type', 'application/json');
            request.send(jsonParams);

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            swal("Eliminado!", objData.msg, "success");
                            tableCandidatos.ajax.reload();
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

function openModal() {
    // Buscamos el formulario justo en este momento, no antes
    let formCandidato = document.getElementById("formCandidato");

    if (formCandidato) {
        formCandidato.reset();

        // Limpiar ID oculto
        if (document.querySelector('#idCandidato')) {
            document.querySelector('#idCandidato').value = "";
        }

        // Cambios visuales obligatorios
        document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
        document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
        document.querySelector('#btnText').innerHTML = "Guardar";
        document.querySelector('#titleModal').innerHTML = "Nuevo Candidato";

        // Abrir el modal usando jQuery (que es lo que usa Bootstrap 4)
        $('#modalFormCandidato').modal('show');
    } else {
        // Este mensaje te confirma que el problema es que el HTML del modal no está cargado
        console.error("ERROR CRÍTICO: El formulario con ID 'formCandidato' no existe en el DOM.");
        swal("Error", "El formulario de candidatos no se cargó correctamente.", "error");
    }
}