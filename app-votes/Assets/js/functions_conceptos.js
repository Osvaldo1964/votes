var tableConceptos;

document.addEventListener('DOMContentLoaded', function () {
    tableConceptos = $('#tableConceptos').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": " " + BASE_URL_API + "/Conceptos/getConceptos",
            "dataSrc": ""
        },
        "columns": [
            { "data": "nombre_concepto" },
            { "data": "tipo_concepto" },
            { "data": "estado_concepto" },
            { "data": "options" }
        ],
        "resonsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Submit Form
    if (document.querySelector("#formConcepto")) {
        var formConcepto = document.querySelector("#formConcepto");
        formConcepto.onsubmit = function (e) {
            e.preventDefault();
            var strNombre = document.querySelector('#txtNombre').value;
            var intTipo = document.querySelector('#listTipo').value;

            if (strNombre == '' || intTipo == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Conceptos/setConcepto';
            var formData = new FormData(formConcepto);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormConcepto').modal("hide");
                        formConcepto.reset();
                        swal("Conceptos", objData.msg, "success");
                        tableConceptos.api().ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }
});

function openModal() {
    document.querySelector('#idConcepto').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Concepto";
    document.querySelector("#formConcepto").reset();
    $('#modalFormConcepto').modal('show');
}

function fntEditConcepto(idconcepto) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Concepto";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = BASE_URL_API + '/Conceptos/getConcepto/' + idconcepto;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {

        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                document.querySelector("#idConcepto").value = objData.data.id_concepto;
                document.querySelector("#txtNombre").value = objData.data.nombre_concepto;
                document.querySelector("#listTipo").value = objData.data.tipo_concepto;
                $('#modalFormConcepto').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelConcepto(idconcepto) {
    swal({
        title: "Eliminar Concepto",
        text: "¿Realmente quiere eliminar el Concepto?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Conceptos/delConcepto';
            var strData = "idConcepto=" + idconcepto;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Eliminar!", objData.msg, "success");
                        tableConceptos.api().ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}
