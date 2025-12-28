var tableEntradas;

document.addEventListener('DOMContentLoaded', function () {
    tableEntradas = $('#tableEntradas').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": " " + BASE_URL_API + "/Entradas/getEntradas",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_entrada" }, // Mostrar ID para referencia
            { "data": "fecha_entrada" },
            { "data": "nombre_tercero" },
            { "data": "nombre_elemento" }, // OJO: Asegurarse de que el modelo retorne 'nombre_elemento'
            { "data": "cantidad_entrada" },
            { "data": "unitario_entrada" },
            { "data": "total_entrada" },
            { "data": "estado_entrada" },
            { "data": "options" }
        ],
        "resonsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Cargar Selects al inicio
    fntSelectTerceros();
    fntSelectElementos();

    // Submit Form
    if (document.querySelector("#formEntrada")) {
        var formEntrada = document.querySelector("#formEntrada");
        formEntrada.onsubmit = function (e) {
            e.preventDefault();

            // Validaciones extras si hacen falta

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Entradas/setEntrada';
            var formData = new FormData(formEntrada);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormEntrada').modal("hide");
                        formEntrada.reset();
                        swal("Entradas", objData.msg, "success");
                        tableEntradas.api().ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }
});

function fntCalcularTotal() {
    let cant = document.querySelector("#txtCantidad").value;
    let unit = document.querySelector("#txtUnitario").value;
    if (cant > 0 && unit > 0) {
        let total = cant * unit;
        document.querySelector("#txtTotal").value = total.toFixed(2); // 2 decimales
    } else {
        document.querySelector("#txtTotal").value = 0;
    }
}

function fntSelectTerceros() {
    if (document.querySelector('#listTercero')) {
        var ajaxUrl = BASE_URL_API + '/Entradas/getSelectTerceros';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET", ajaxUrl, true);
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                var options = '<option value="">Seleccione...</option>';
                objData.forEach(function (item) {
                    options += '<option value="' + item.id_tercero + '">' + item.nombre_tercero + '</option>';
                });
                document.querySelector('#listTercero').innerHTML = options;
                $('#listTercero').selectpicker('render'); // Si usas bootstrap-select
            }
        }
    }
}

function fntSelectElementos() {
    if (document.querySelector('#listElemento')) {
        var ajaxUrl = BASE_URL_API + '/Entradas/getSelectElementos';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET", ajaxUrl, true);
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                var options = '<option value="">Seleccione...</option>';
                objData.forEach(function (item) {
                    options += '<option value="' + item.id_elemento + '">' + item.nombre_elemento + '</option>';
                });
                document.querySelector('#listElemento').innerHTML = options;
                $('#listElemento').selectpicker('render');
            }
        }
    }
}

function openModal() {
    document.querySelector('#idEntrada').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nueva Entrada";
    document.querySelector("#formEntrada").reset();

    // Resetear selects
    $('#listTercero').selectpicker('render');
    $('#listElemento').selectpicker('render');

    $('#modalFormEntrada').modal('show');
}

function fntEditEntrada(identrada) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Entrada";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = BASE_URL_API + '/Entradas/getEntrada/' + identrada;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {

        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                var data = objData.data;
                document.querySelector("#idEntrada").value = data.id_entrada;
                document.querySelector("#txtFecha").value = data.fecha_entrada;
                document.querySelector("#txtFactura").value = data.factura_entrada;

                document.querySelector("#listTercero").value = data.tercero_entrada;
                $('#listTercero').selectpicker('render');

                document.querySelector("#listElemento").value = data.elemento_entrada;
                $('#listElemento').selectpicker('render');

                document.querySelector("#txtCantidad").value = data.cantidad_entrada;
                document.querySelector("#txtUnitario").value = data.unitario_entrada;
                document.querySelector("#txtTotal").value = data.total_entrada;

                $('#modalFormEntrada').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelEntrada(identrada) {
    swal({
        title: "Anular Entrada",
        text: "¿Realmente quiere anular esta Entrada?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, anular!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Entradas/delEntrada';
            var strData = "idEntrada=" + identrada;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Anulada!", objData.msg, "success");
                        tableEntradas.api().ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}
