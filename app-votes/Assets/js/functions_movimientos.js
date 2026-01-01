// functions_movimientos.js
// Optimizado para uso de funciones globales


let tableMovimientos;


let divLoading = document.querySelector("#divLoading");


document.addEventListener('DOMContentLoaded', async function () {

    // 1. Cargar datos de la tabla
    tableMovimientos = $('#tableMovimientos').DataTable({
        "processing": true,
        "language": lenguajeEspanol,
        "ajax": {
            "url": `${base_url_api}Movimientos/getMovimientos`, // Nótese: base_url_api ya trae slash final usualmente. Si no, ajustar.
            "type": "GET",
            "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
            "data": d => { d.rolUser = localStorage.getItem('userRol'); },
            "dataSrc": json => (json && json.status && Array.isArray(json.data)) ? json.data : [],
            "error": function (xhr) {
                console.error("Error en AJAX:", xhr);
            }
        },
        "columns": [
            { "data": "id_movimiento" },
            { "data": "fecha_movimiento" },
            { "data": "nombre_tercero" }, // Viene del JOIN
            { "data": "nombre_concepto" }, // Viene del JOIN
            { "data": "tipo_badged" },     // Generado en Controller (Ingreso/Gasto)
            { "data": "valor_fmt" },       // Formateado
            { "data": "options", "orderable": false }
        ],
        "columnDefs": [
            { "defaultContent": "-", "targets": "_all" }
        ],
        "responsive": true,
        "destroy": true,
        "order": [[0, "desc"]]
    });

    // 2. Cargar Selects (Terceros y Conceptos)
    await cargarCombos();

    // 3. Manejo del Formulario
    const formMovimiento = document.querySelector("#formMovimiento");
    if (formMovimiento) {
        formMovimiento.onsubmit = async function (e) {
            e.preventDefault();

            // Validaciones HTML5 ya actúan (required), pero podemos añadir custom
            const strFecha = document.querySelector('#fecha_movimiento').value;
            const intTercero = document.querySelector('#tercero_movimiento').value;
            const intConcepto = document.querySelector('#concepto_movimiento').value;
            const decValor = document.querySelector('#valor_movimiento').value;

            if (strFecha == '' || intTercero == '' || intConcepto == '' || decValor == '') {
                swal("Atención", "Todos los campos obligatorios deben ser llenados.", "error");
                return false;
            }

            // Preparar datos JSON
            // Lideres usa FormData si hay archivos, o JSON. 
            // setLider usa FormData en Lideres.php. 
            // Movimientos NO tiene archivos, pero por consistencia usaré objeto object -> JSON
            // La API espera JSON crudo en mi implementación de Movimientos.php (setMovimiento)

            let formData = new FormData(formMovimiento);
            let object = {};
            formData.forEach((value, key) => object[key] = value);

            // Enviar
            const objData = await fetchData(`${base_url_api}Movimientos/setMovimiento`, 'POST', object);

            if (objData?.status) {
                $('#modalFormMovimiento').modal("hide");
                formMovimiento.reset();
                swal("Movimientos", objData.msg, "success");
                tableMovimientos.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error desconocido", "error");
            }
        };
    }
});

async function cargarCombos() {
    const res = await fetchData(`${base_url_api}Movimientos/getSelects`);
    if (res) {
        // Terceros
        const slTercero = document.querySelector("#tercero_movimiento");
        slTercero.innerHTML = '<option value="">Seleccione...</option>';
        if (res.terceros) {
            res.terceros.forEach(t => {
                slTercero.innerHTML += `<option value="${t.id_tercero}">${t.nombre_tercero}</option>`;
            });
        }

        // Conceptos
        const slConcepto = document.querySelector("#concepto_movimiento");
        slConcepto.innerHTML = '<option value="">Seleccione...</option>';
        if (res.conceptos) {
            res.conceptos.forEach(c => {
                // Guardamos el tipo como data attribute por si acaso sirve luego
                slConcepto.innerHTML += `<option value="${c.id_concepto}" data-tipo="${c.tipo_concepto}">${c.nombre_concepto}</option>`;
            });
        }

        // Refresh selectpicker si existe (Bootstrap Select)
        if ($('.selectpicker').length > 0) {
            $('.selectpicker').selectpicker('refresh');
        }
    }
}

function openModal() {
    document.querySelector('#id_movimiento').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Movimiento";
    document.querySelector("#formMovimiento").reset();

    // Set default date
    document.querySelector('#fecha_movimiento').valueAsDate = new Date();

    $('#modalFormMovimiento').modal('show');
}

async function fntEditMovimiento(id) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Movimiento";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    const res = await fetchData(`${base_url_api}Movimientos/getMovimiento/${id}`);
    if (res?.status) {
        const d = res.data;
        document.querySelector("#id_movimiento").value = d.id_movimiento;
        document.querySelector("#fecha_movimiento").value = d.fecha_movimiento;
        document.querySelector("#tercero_movimiento").value = d.tercero_movimiento;
        document.querySelector("#concepto_movimiento").value = d.concepto_movimiento;
        document.querySelector("#tipo_movimiento").value = d.tipo_movimiento; // Norma contable
        document.querySelector("#valor_movimiento").value = d.valor_movimiento;
        document.querySelector("#obs_movimiento").value = d.obs_movimiento;

        // Refresh selects (si fuera bootstrap-select hay que forzar update)
        // $('#tercero_movimiento').val(d.tercero_movimiento).selectpicker('refresh'); // Ejemplo si fuera necesario

        $('#modalFormMovimiento').modal('show');
    } else {
        swal("Error", res?.msg || "No se pudo cargar el movimiento", "error");
    }
}

function fntDelMovimiento(id) {
    swal({
        title: "Eliminar Movimiento",
        text: "¿Realmente quiere eliminar el movimiento?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, async (isConfirm) => {
        if (isConfirm) {
            const res = await fetchData(`${base_url_api}Movimientos/delMovimiento`, 'POST', { id_movimiento: id });
            if (res?.status) {
                swal("Eliminado", res.msg, "success");
                tableMovimientos.ajax.reload();
            } else {
                swal("Error", res?.msg || "No se pudo eliminar", "error");
            }
        }
    });
}
