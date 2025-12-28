var tableElementos;

document.addEventListener('DOMContentLoaded', function () {
    tableElementos = $('#tableElementos').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": " " + BASE_URL_API + "/Elementos/getElementos",
            "dataSrc": ""
        },
        "columns": [
            { "data": "nombre_elemento" },
            { "data": "estado_elemento" },
            { "data": "options" }
        ],
        "resonsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Submit Form
    if (document.querySelector("#formElemento")) {
        var formElemento = document.querySelector("#formElemento");
        formElemento.onsubmit = function (e) {
            e.preventDefault();
            var strNombre = document.querySelector('#txtNombre').value;

            if (strNombre == '') {
                swal("Atención", "El nombre es obligatorio.", "error");
                return false;
            }

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Elementos/setElemento';
            var formData = new FormData(formElemento);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormElemento').modal("hide");
                        formElemento.reset();
                        swal("Elementos", objData.msg, "success");
                        tableElementos.api().ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }
});

function openModal() {
    document.querySelector('#idElemento').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Elemento";
    document.querySelector("#formElemento").reset();
    $('#modalFormElemento').modal('show');
}

function fntEditElemento(idelemento) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Elemento";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = BASE_URL_API + '/Elementos/getElemento/' + idelemento;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {

        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                document.querySelector("#idElemento").value = objData.data.id_elemento;
                document.querySelector("#txtNombre").value = objData.data.nombre_elemento;
                $('#modalFormElemento').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelElemento(idelemento) {
    swal({
        title: "Eliminar Elemento",
        text: "¿Realmente quiere eliminar el Elemento?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Elementos/delElemento';
            var strData = "idElemento=" + idelemento;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Eliminar!", objData.msg, "success");
                        tableElementos.api().ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}
