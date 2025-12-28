var tableTerceros;

document.addEventListener('DOMContentLoaded', function () {
    tableTerceros = $('#tableTerceros').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": " " + BASE_URL_API + "/Terceros/getTerceros",
            "dataSrc": ""
        },
        "columns": [
            { "data": "ident_tercero" },
            { "data": "nombre_tercero" },
            { "data": "telefono_tercero" },
            { "data": "email_tercero" },
            { "data": "direccion_tercero" },
            { "data": "estado_tercero" },
            { "data": "options" }
        ],
        "resonsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Nuevo / Editar
    if (document.querySelector("#formTercero")) {
        var formTercero = document.querySelector("#formTercero");
        formTercero.onsubmit = function (e) {
            e.preventDefault();
            var strIdentificacion = document.querySelector('#txtIdentificacion').value;
            var strNombre = document.querySelector('#txtNombre').value;
            var strEmail = document.querySelector('#txtEmail').value;
            var strTelefono = document.querySelector('#txtTelefono').value;

            if (strIdentificacion == '' || strNombre == '' || strEmail == '' || strTelefono == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Terceros/setTercero';
            var formData = new FormData(formTercero);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormTercero').modal("hide");
                        formTercero.reset();
                        swal("Terceros", objData.msg, "success");
                        tableTerceros.api().ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }
});

function openModal() {
    document.querySelector('#idTercero').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Tercero";
    document.querySelector("#formTercero").reset();
    $('#modalFormTercero').modal('show');
}

function fntEditTercero(idtercero) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Tercero";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = BASE_URL_API + '/Terceros/getTercero/' + idtercero;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {

        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                document.querySelector("#idTercero").value = objData.data.id_tercero;
                document.querySelector("#txtIdentificacion").value = objData.data.ident_tercero;
                document.querySelector("#txtNombre").value = objData.data.nombre_tercero;
                document.querySelector("#txtTelefono").value = objData.data.telefono_tercero;
                document.querySelector("#txtEmail").value = objData.data.email_tercero;
                document.querySelector("#txtDireccion").value = objData.data.direccion_tercero;

                $('#modalFormTercero').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelTercero(idtercero) {
    swal({
        title: "Eliminar Tercero",
        text: "¿Realmente quiere eliminar el Tercero?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Terceros/delTercero';
            var strData = "idTercero=" + idtercero;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Eliminar!", objData.msg, "success");
                        tableTerceros.api().ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}
