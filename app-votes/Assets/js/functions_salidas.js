// functions_salidas.js
// Optimizado para arquitectura API con JWT y Async/Await

let tableSalidas;

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inicialización de DataTables
    tableSalidas = $('#tableSalidas').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global en functions_admin.js
        "ajax": {
            "url": BASE_URL_API + "/Salidas/getSalidas",
            "type": "GET",
            "headers": { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` },
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
        "responsive": true,
        "destroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // 2. Cargar Selects
    fntSelectLideres();
    fntSelectElementos();

    // 3. Submit Form
    if (document.querySelector("#formSalida")) {
        const formSalida = document.querySelector("#formSalida");
        formSalida.onsubmit = async function (e) {
            e.preventDefault();

            // Validaciones
            const lider = document.querySelector("#listLider").value;
            const elemento = document.querySelector("#listElemento").value;
            const cantidad = document.querySelector("#txtCantidad").value;

            if (lider == "" || elemento == "" || cantidad <= 0) {
                swal("Atención", "Todos los campos obligatorios deben ser llenados y la cantidad positiva.", "error");
                return;
            }

            const formData = new FormData(formSalida);
            // Usamos helper global con POST
            const objData = await fetchData(BASE_URL_API + '/Salidas/setSalida', 'POST', formData);

            if (objData?.status) {
                $('#modalFormSalida').modal("hide");
                formSalida.reset();
                document.querySelector('#divInfoLider').style.display = "none";

                swal("Salidas", objData.msg, "success");
                tableSalidas.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
    }

    // 4. Listener para cambio de líder (Mostrar info electores)
    if (document.querySelector('#listLider')) {
        document.querySelector('#listLider').addEventListener('change', fntInfoLider);
    }
});

// Función para cargar Lideres con atributo data-electores
async function fntSelectLideres() {
    if (document.querySelector('#listLider')) {
        const objData = await fetchData(BASE_URL_API + '/Salidas/getSelectLideres');
        if (Array.isArray(objData)) {
            let options = '<option value="" data-electores="0">Seleccione...</option>';
            objData.forEach(function (item) {
                // Guardamos el total_electores en un data-attribute para usarlo en la UI
                options += `<option value="${item.id_lider}" data-electores="${item.total_electores}">${item.nombre_lider}</option>`;
            });
            document.querySelector('#listLider').innerHTML = options;
            $('#listLider').selectpicker('refresh');
        }
    }
}

async function fntSelectElementos() {
    if (document.querySelector('#listElemento')) {
        const objData = await fetchData(BASE_URL_API + '/Salidas/getSelectElementos');
        if (Array.isArray(objData)) {
            let options = '<option value="">Seleccione...</option>';
            objData.forEach(function (item) {
                options += `<option value="${item.id_elemento}">${item.nombre_elemento}</option>`;
            });
            document.querySelector('#listElemento').innerHTML = options;
            $('#listElemento').selectpicker('refresh');
        }
    }
}

// LÓGICA VISUAL: Mostrar cantidad de electores al cambiar líder
function fntInfoLider() {
    const select = document.querySelector('#listLider');
    // Para selects normales o bootstrap-select, tomamos la opción seleccionada
    const selectedOption = select.options[select.selectedIndex];

    // Validación por si no hay opción (reset)
    if (!selectedOption) return;

    const electores = selectedOption.getAttribute('data-electores') || 0;
    const divInfo = document.querySelector('#divInfoLider');
    const lblElectores = document.querySelector('#lblElectores');

    if (select.value != "") {
        lblElectores.innerHTML = electores;
        divInfo.style.display = "block";

        // Cambio de color visual: Si tiene 0 electores, poner warning
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
    if (document.querySelector('#listLider')) {
        document.querySelector('#listLider').value = "";
        $('#listLider').selectpicker('refresh');
    }
    if (document.querySelector('#listElemento')) {
        document.querySelector('#listElemento').value = "";
        $('#listElemento').selectpicker('refresh');
    }

    $('#modalFormSalida').modal('show');
}

async function fntEditSalida(idsalida) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Entrega";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // Petición Data
    const objData = await fetchData(BASE_URL_API + '/Salidas/getSalida/' + idsalida);

    if (objData?.status) {
        const data = objData.data;
        document.querySelector("#idSalida").value = data.id_salida;
        document.querySelector("#txtFecha").value = data.fecha_salida;

        // Set Lider y refrescar
        document.querySelector("#listLider").value = data.lider_salida;
        $('#listLider').selectpicker('refresh');

        // Disparar manualmente la info del lider al editar para ver sus electores actuales
        fntInfoLider();

        document.querySelector("#listElemento").value = data.elemento_salida;
        $('#listElemento').selectpicker('refresh');

        document.querySelector("#txtCantidad").value = data.cantidad_salida;

        $('#modalFormSalida').modal('show');
    } else {
        swal("Error", objData?.msg || "No se encontró la salida", "error");
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
    }, async function (isConfirm) {
        if (isConfirm) {
            // Nota API: Se usaba POST application/x-www-form-urlencoded
            // Usamos FormData para compatibilidad máxima sin decodificar JSON en PHP legacy
            let formData = new FormData();
            formData.append("idSalida", idsalida);

            const objData = await fetchData(BASE_URL_API + '/Salidas/delSalida', 'POST', formData);

            if (objData?.status) {
                swal("Anulada!", objData.msg, "success");
                tableSalidas.ajax.reload();
            } else {
                swal("Atención!", objData?.msg || "Error al anular", "error");
            }
        }
    });
}
