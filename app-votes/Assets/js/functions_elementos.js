// functions_elementos.js
// Optimizado para arquitectura API con JWT y Async/Await

let tableElementos;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicialización de DataTables
    tableElementos = $('#tableElementos').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global en functions_admin.js
        "ajax": {
            "url": BASE_URL_API + "/Elementos/getElementos",
            "type": "GET",
            "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
            "dataSrc": ""
        },
        "columns": [
            { "data": "nombre_elemento" },
            { "data": "estado_elemento" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. Submit Form
    if (document.querySelector("#formElemento")) {
        const formElemento = document.querySelector("#formElemento");
        formElemento.onsubmit = async function (e) {
            e.preventDefault();
            const strNombre = document.querySelector('#txtNombre').value;

            if (strNombre == '') {
                swal("Atención", "El nombre es obligatorio.", "error");
                return;
            }

            const formData = new FormData(formElemento);
            // Uso de fetchData global
            const objData = await fetchData(BASE_URL_API + '/Elementos/setElemento', 'POST', formData);

            if (objData?.status) {
                $('#modalFormElemento').modal("hide");
                formElemento.reset();
                swal("Elementos", objData.msg, "success");
                tableElementos.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
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

async function fntEditElemento(idelemento) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Elemento";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // Petición con fetchData global
    const objData = await fetchData(BASE_URL_API + '/Elementos/getElemento/' + idelemento);

    if (objData?.status) {
        document.querySelector("#idElemento").value = objData.data.id_elemento;
        document.querySelector("#txtNombre").value = objData.data.nombre_elemento;
        $('#modalFormElemento').modal('show');
    } else {
        swal("Error", objData?.msg || "Datos no encontrados", "error");
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
    }, async function (isConfirm) {
        if (isConfirm) {
            // Nota API legacy: Se usaba POST application/x-www-form-urlencoded
            let formData = new FormData();
            formData.append("idElemento", idelemento);

            const objData = await fetchData(BASE_URL_API + '/Elementos/delElemento', 'POST', formData);

            if (objData?.status) {
                swal("Eliminado!", objData.msg, "success");
                tableElementos.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al eliminar", "error");
            }
        }
    });
}
