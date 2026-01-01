// functions_entradas.js
// Optimizado para arquitectura API con JWT

let tableEntradas;

document.addEventListener('DOMContentLoaded', function () {
    // Inicialización del DataTable con la configuración global 'lenguajeEspanol' y Auth Header
    tableEntradas = $('#tableEntradas').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global en functions_admin.js
        "ajax": {
            "url": BASE_URL_API + "/Entradas/getEntradas",
            "type": "GET",
            "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
            "dataSrc": ""
        },
        "columns": [
            { "data": "id_entrada" },
            { "data": "fecha_entrada" },
            { "data": "nombre_tercero" },
            { "data": "nombre_elemento" },
            { "data": "cantidad_entrada" },
            { "data": "unitario_entrada" },
            { "data": "total_entrada" },
            { "data": "estado_entrada" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Cargar Selects
    fntSelectTerceros();
    fntSelectElementos();

    // Submit Form
    if (document.querySelector("#formEntrada")) {
        const formEntrada = document.querySelector("#formEntrada");
        formEntrada.onsubmit = async function (e) {
            e.preventDefault();

            // Validaciones básicas:
            const cantidad = document.querySelector("#txtCantidad").value;
            const unitario = document.querySelector("#txtUnitario").value;

            if (cantidad <= 0 || unitario <= 0) {
                swal("Atención", "Cantidad y valor unitario deben ser mayores a 0", "warning");
                return;
            }

            const formData = new FormData(formEntrada);
            // Usamos el helper global 'fetchData'. 
            // Nota: fetchData maneja FormData automáticamente (Content-Type multipart).
            const objData = await fetchData(BASE_URL_API + '/Entradas/setEntrada', 'POST', formData);

            if (objData?.status) {
                $('#modalFormEntrada').modal("hide");
                formEntrada.reset();
                swal("Entradas", objData.msg, "success");
                tableEntradas.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
    }
});

function fntCalcularTotal() {
    let cant = parseFloat(document.querySelector("#txtCantidad").value) || 0;
    let unit = parseFloat(document.querySelector("#txtUnitario").value) || 0;
    if (cant > 0 && unit > 0) {
        let total = cant * unit;
        document.querySelector("#txtTotal").value = total.toFixed(2);
    } else {
        document.querySelector("#txtTotal").value = 0;
    }
}

async function fntSelectTerceros() {
    if (document.querySelector('#listTercero')) {
        const objData = await fetchData(BASE_URL_API + '/Entradas/getSelectTerceros');
        if (Array.isArray(objData)) {
            let options = '<option value="">Seleccione...</option>';
            objData.forEach(function (item) {
                options += '<option value="' + item.id_tercero + '">' + item.nombre_tercero + '</option>';
            });
            document.querySelector('#listTercero').innerHTML = options;
            $('#listTercero').selectpicker('refresh');
        }
    }
}

async function fntSelectElementos() {
    if (document.querySelector('#listElemento')) {
        const objData = await fetchData(BASE_URL_API + '/Entradas/getSelectElementos');
        if (Array.isArray(objData)) {
            let options = '<option value="">Seleccione...</option>';
            objData.forEach(function (item) {
                options += '<option value="' + item.id_elemento + '">' + item.nombre_elemento + '</option>';
            });
            document.querySelector('#listElemento').innerHTML = options;
            $('#listElemento').selectpicker('refresh');
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
    if (document.querySelector('#listTercero')) {
        document.querySelector('#listTercero').value = "";
        $('#listTercero').selectpicker('refresh');
    }
    if (document.querySelector('#listElemento')) {
        document.querySelector('#listElemento').value = "";
        $('#listElemento').selectpicker('refresh');
    }

    $('#modalFormEntrada').modal('show');
}

async function fntEditEntrada(identrada) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Entrada";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // Usamos fetchData global
    const objData = await fetchData(BASE_URL_API + '/Entradas/getEntrada/' + identrada);

    if (objData?.status) {
        const data = objData.data;
        document.querySelector("#idEntrada").value = data.id_entrada;
        document.querySelector("#txtFecha").value = data.fecha_entrada;
        document.querySelector("#txtFactura").value = data.factura_entrada;

        document.querySelector("#listTercero").value = data.tercero_entrada;
        $('#listTercero').selectpicker('refresh');

        document.querySelector("#listElemento").value = data.elemento_entrada;
        $('#listElemento').selectpicker('refresh');

        document.querySelector("#txtCantidad").value = data.cantidad_entrada;
        document.querySelector("#txtUnitario").value = data.unitario_entrada;
        document.querySelector("#txtTotal").value = data.total_entrada;

        $('#modalFormEntrada').modal('show');
    } else {
        swal("Error", objData?.msg || "Datos no encontrados", "error");
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
    }, async function (isConfirm) {
        if (isConfirm) {
            // Nota: La API original usaba POST con x-www-form-urlencoded para delEntrada.
            // fetchData usa JSON por defecto si el body es objeto. 
            // Si la API espera $_POST directo sin JSON decode, hay que enviar FormData.

            let formData = new FormData();
            formData.append("idEntrada", identrada);

            const objData = await fetchData(BASE_URL_API + '/Entradas/delEntrada', 'POST', formData);

            if (objData?.status) {
                swal("Anulada!", objData.msg, "success");
                tableEntradas.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al anular", "error");
            }
        }
    });
}
