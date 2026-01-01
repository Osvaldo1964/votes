// functions_terceros.js
// Optimizado para arquitectura API con JWT y Async/Await

let tableTerceros;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicialización de DataTables
    tableTerceros = $('#tableTerceros').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global en functions_admin.js
        "ajax": {
            "url": BASE_URL_API + "/Terceros/getTerceros",
            "type": "GET",
            "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
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
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. Submit Form
    if (document.querySelector("#formTercero")) {
        const formTercero = document.querySelector("#formTercero");
        formTercero.onsubmit = async function (e) {
            e.preventDefault();
            const strIdentificacion = document.querySelector('#txtIdentificacion').value;
            const strNombre = document.querySelector('#txtNombre').value;
            const strEmail = document.querySelector('#txtEmail').value;
            const strTelefono = document.querySelector('#txtTelefono').value;

            if (strIdentificacion == '' || strNombre == '' || strEmail == '' || strTelefono == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return;
            }

            const formData = new FormData(formTercero);
            // Uso de fetchData global
            const objData = await fetchData(BASE_URL_API + '/Terceros/setTercero', 'POST', formData);

            if (objData?.status) {
                $('#modalFormTercero').modal("hide");
                formTercero.reset();
                swal("Terceros", objData.msg, "success");
                tableTerceros.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
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

async function fntEditTercero(idtercero) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Tercero";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // Petición con fetchData global
    const objData = await fetchData(BASE_URL_API + '/Terceros/getTercero/' + idtercero);

    if (objData?.status) {
        document.querySelector("#idTercero").value = objData.data.id_tercero;
        document.querySelector("#txtIdentificacion").value = objData.data.ident_tercero;
        document.querySelector("#txtNombre").value = objData.data.nombre_tercero;
        document.querySelector("#txtTelefono").value = objData.data.telefono_tercero;
        document.querySelector("#txtEmail").value = objData.data.email_tercero;
        document.querySelector("#txtDireccion").value = objData.data.direccion_tercero;

        $('#modalFormTercero').modal('show');
    } else {
        swal("Error", objData?.msg || "Datos no encontrados", "error");
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
    }, async function (isConfirm) {
        if (isConfirm) {
            // Nota API legacy: Se usaba POST application/x-www-form-urlencoded
            let formData = new FormData();
            formData.append("idTercero", idtercero);

            const objData = await fetchData(BASE_URL_API + '/Terceros/delTercero', 'POST', formData);

            if (objData?.status) {
                swal("Eliminado!", objData.msg, "success");
                tableTerceros.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al eliminar", "error");
            }
        }
    });
}
