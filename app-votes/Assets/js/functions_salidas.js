var tableSalidas;

document.addEventListener('DOMContentLoaded', function () {
    tableSalidas = $('#tableSalidas').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": " " + BASE_URL_API + "/Salidas/getSalidas",
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_salida" },
            { "data": "fecha_salida" },
            { "data": "nombre_lider" },
            { "data": "nombre_elemento" },
            { "data": "cantidad_salida" },
            { "data": "estado_salida" },
            { "data": "options" }
        ],
        "resonsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Cargar Selects
    fntSelectLideres();
    fntSelectElementos();

    // Submit Form
    if (document.querySelector("#formSalida")) {
        var formSalida = document.querySelector("#formSalida");
        formSalida.onsubmit = function (e) {
            e.preventDefault();

            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Salidas/setSalida';
            var formData = new FormData(formSalida);
            request.open("POST", ajaxUrl, true);
            request.send(formData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        $('#modalFormSalida').modal("hide");
                        formSalida.reset();
                        // Ocultar alerta
                        document.querySelector('#divInfoLider').style.display = "none";

                        swal("Salidas", objData.msg, "success");
                        tableSalidas.api().ajax.reload();
                    } else {
                        swal("Error", objData.msg, "error");
                    }
                }
            }
        }
    }
});

// Funcion para cargar Lideres con atributo data-electores
function fntSelectLideres() {
    if (document.querySelector('#listLider')) {
        var ajaxUrl = BASE_URL_API + '/Salidas/getSelectLideres';
        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        request.open("GET", ajaxUrl, true);
        request.send();
        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                var options = '<option value="" data-electores="0">Seleccione...</option>';
                objData.forEach(function (item) {
                    // AQUI guardo el total_electores en un data-attribute
                    options += '<option value="' + item.id_lider + '" data-electores="' + item.total_electores + '">' + item.nombre_lider + '</option>';
                });
                document.querySelector('#listLider').innerHTML = options;
                $('#listLider').selectpicker('render');
            }
        }
    }
}

function fntSelectElementos() {
    if (document.querySelector('#listElemento')) {
        var ajaxUrl = BASE_URL_API + '/Salidas/getSelectElementos';
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

// LOGICA VISUAL: Mostrar cantidad de electores al cambiar líder
function fntInfoLider() {
    var select = document.querySelector('#listLider');
    var selectedOption = select.options[select.selectedIndex];
    var electores = selectedOption.getAttribute('data-electores');

    var divInfo = document.querySelector('#divInfoLider');
    var lblElectores = document.querySelector('#lblElectores');

    if (select.value != "") {
        lblElectores.innerHTML = electores;
        divInfo.style.display = "block";

        // Cambio de color visual: Si tiene 0 electores, poner rojo para alertar mas
        if (electores == 0) {
            divInfo.classList.replace("alert-info", "alert-warning");
        } else {
            divInfo.classList.replace("alert-warning", "alert-info");
        }
    } else {
        divInfo.style.display = "none";
    }
}

function openModal() {
    document.querySelector('#idSalida').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nueva Entrega";
    document.querySelector("#formSalida").reset();
    document.querySelector('#divInfoLider').style.display = "none";

    // Resetear selects
    $('#listLider').selectpicker('render');
    $('#listElemento').selectpicker('render');

    $('#modalFormSalida').modal('show');
}

function fntEditSalida(idsalida) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Entrega";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = BASE_URL_API + '/Salidas/getSalida/' + idsalida;
    request.open("GET", ajaxUrl, true);
    request.send();
    request.onreadystatechange = function () {

        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);

            if (objData.status) {
                var data = objData.data;
                document.querySelector("#idSalida").value = data.id_salida;
                document.querySelector("#txtFecha").value = data.fecha_salida;

                document.querySelector("#listLider").value = data.lider_salida;
                $('#listLider').selectpicker('render');
                // Disparar manualmente la info del lider al editar
                fntInfoLider();

                document.querySelector("#listElemento").value = data.elemento_salida;
                $('#listElemento').selectpicker('render');

                document.querySelector("#txtCantidad").value = data.cantidad_salida;

                $('#modalFormSalida').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

function fntDelSalida(idsalida) {
    swal({
        title: "Anular Entrega",
        text: "¿Realmente quiere anular esta Entrega?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, anular!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = BASE_URL_API + '/Salidas/delSalida';
            var strData = "idSalida=" + idsalida;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal("Anulada!", objData.msg, "success");
                        tableSalidas.api().ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}
