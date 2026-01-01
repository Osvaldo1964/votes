// functions_conceptos.js
// Optimizado para arquitectura API con JWT y Async/Await

let tableConceptos;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicialización de DataTables
    tableConceptos = $('#tableConceptos').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global en functions_admin.js
        "ajax": {
            "url": BASE_URL_API + "/Conceptos/getConceptos",
            "type": "GET",
            "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
            "dataSrc": ""
        },
        "columns": [
            { "data": "nombre_concepto" },
            { "data": "tipo_concepto" },
            { "data": "estado_concepto" },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. Submit Form
    if (document.querySelector("#formConcepto")) {
        const formConcepto = document.querySelector("#formConcepto");
        formConcepto.onsubmit = async function (e) {
            e.preventDefault();
            const strNombre = document.querySelector('#txtNombre').value;
            const intTipo = document.querySelector('#listTipo').value;

            if (strNombre == '' || intTipo == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return;
            }

            const formData = new FormData(formConcepto);
            // Uso de fetchData global
            const objData = await fetchData(BASE_URL_API + '/Conceptos/setConcepto', 'POST', formData);

            if (objData?.status) {
                $('#modalFormConcepto').modal("hide");
                formConcepto.reset();
                swal("Conceptos", objData.msg, "success");
                tableConceptos.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
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

async function fntEditConcepto(idconcepto) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Concepto";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // Petición con fetchData global
    const objData = await fetchData(BASE_URL_API + '/Conceptos/getConcepto/' + idconcepto);

    if (objData?.status) {
        document.querySelector("#idConcepto").value = objData.data.id_concepto;
        document.querySelector("#txtNombre").value = objData.data.nombre_concepto;
        document.querySelector("#listTipo").value = objData.data.tipo_concepto;
        $('#modalFormConcepto').modal('show');
    } else {
        swal("Error", objData?.msg || "Datos no encontrados", "error");
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
    }, async function (isConfirm) {
        if (isConfirm) {
            // Nota API legacy: Se usaba POST application/x-www-form-urlencoded
            let formData = new FormData();
            formData.append("idConcepto", idconcepto);

            const objData = await fetchData(BASE_URL_API + '/Conceptos/delConcepto', 'POST', formData);

            if (objData?.status) {
                swal("Eliminado!", objData.msg, "success");
                tableConceptos.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al eliminar", "error");
            }
        }
    });
}
