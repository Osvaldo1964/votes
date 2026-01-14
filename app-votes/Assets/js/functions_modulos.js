var tableModulos;

document.addEventListener('DOMContentLoaded', function () {
    tableModulos = $('#tableModulos').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": lenguajeEspanol,
        "ajax": getDataTableFetchConfig('/modulos/getModulos'),
        "columns": [
            { "data": "id_modulo" },
            { "data": "titulo_modulo" },
            { "data": "descript_modulo" },
            { "data": "estado_modulo" },
            { "data": "options" }
        ],
        "responsive": true,
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    var formModulo = document.querySelector("#formModulo");
    if (formModulo) {
        formModulo.onsubmit = async function (e) {
            e.preventDefault();
            var strTitulo = document.querySelector('#txtTitulo').value;
            var strDescripcion = document.querySelector('#txtDescripcion').value;
            var intStatus = document.querySelector('#listStatus').value;
            if (strTitulo == '' || strDescripcion == '' || intStatus == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            const url = `${BASE_URL_API}/modulos/setModulo`;
            const formData = new FormData(formModulo);
            // formData automatically captures inputs with name attributes

            try {
                const objData = await fetchData(url, 'POST', formData);
                if (objData.status) {
                    $('#modalFormModulo').modal("hide");
                    formModulo.reset();
                    swal("Módulos", objData.msg, "success");
                    tableModulos.ajax.reload();
                } else {
                    swal("Error", objData.msg, "error");
                }
            } catch (error) {
                console.error(error);
                swal("Error", "Error en el servidor", "error");
            }
        }
    }
});

function openModal() {
    document.querySelector('#idModulo').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Módulo";
    document.querySelector("#formModulo").reset();
    $('#modalFormModulo').modal('show');
}

async function fntEditModulo(idModulo) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Módulo";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const url = `${BASE_URL_API}/modulos/getModulo/${idModulo}`;
    const objData = await fetchData(url);
    if (objData.status) {
        document.querySelector("#idModulo").value = objData.data.id_modulo;
        document.querySelector("#txtTitulo").value = objData.data.titulo_modulo;
        document.querySelector("#txtDescripcion").value = objData.data.descript_modulo;
        document.querySelector("#listStatus").value = objData.data.estado_modulo;
        $('#modalFormModulo').modal('show');
    } else {
        swal("Error", objData.msg, "error");
    }
}

function fntDelModulo(idModulo) {
    swal({
        title: "Eliminar Módulo",
        text: "¿Realmente quiere eliminar el módulo?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, async function (isConfirm) {
        if (isConfirm) {
            const url = `${BASE_URL_API}/modulos/delModulo`;
            // Using POST with FormData for delete usually
            const formData = new FormData();
            formData.append('idModulo', idModulo);

            const objData = await fetchData(url, 'POST', formData);
            if (objData.status) {
                swal("Eliminar!", objData.msg, "success");
                tableModulos.ajax.reload();
            } else {
                swal("Atención!", objData.msg, "error");
            }
        }
    });

}
