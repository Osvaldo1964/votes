const lenguajeEspanol = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "paginate": { "first": "Primero", "last": "Último", "next": "Siguiente", "previous": "Anterior" }
};

let dataConfig = null;
let tableLideres;

// 1. HELPER DE PETICIONES (CON DEPURACIÓN)
async function fetchData(url, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('userToken')}`,
            'Content-Type': 'application/json'
        }
    };
    if (body && method !== 'GET') options.body = body instanceof FormData ? body : JSON.stringify(body);
    if (body instanceof FormData) delete options.headers['Content-Type'];

    try {
        const response = await fetch(url, options);
        const text = await response.text(); // Leemos como texto primero para evitar el error de JSON
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error("Respuesta no válida del servidor:", text); // Aquí verás el error de PHP en la consola
            return null;
        }
    } catch (error) {
        console.error("Error de conexión:", error);
        return null;
    }
}

// 2. INICIO DEL DOCUMENTO
document.addEventListener('DOMContentLoaded', async function () {
    // 1. CARGAR CONFIGURACIÓN PRIMERO
    await cargarJson();

    // 2. INICIALIZAR TABLA
    tableLideres = $('#tableLideres').DataTable({
        "processing": true,
        "language": lenguajeEspanol,
        "ajax": {
            "url": `${BASE_URL_API}/lideres/getLideres`,
            "type": "GET",
            "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
            "data": d => { d.rolUser = localStorage.getItem('userRol'); },
            // FORZAMOS que siempre devuelva un array, incluso si el servidor falla
            "dataSrc": json => (json && json.status && Array.isArray(json.data)) ? json.data : [],
            "error": function (xhr) {
                console.error("Error en AJAX:", xhr);
                fntHandleError(xhr);
            }
        },
        "columns": [
            { "data": "id_lider" },
            { "data": "ident_lider" },
            { "render": (d, t, row) => `${row.nom1_lider} ${row.nom2_lider || ""}` },
            { "render": (d, t, row) => `${row.ape1_lider} ${row.ape2_lider || ""}` },
            { "data": "telefono_lider" },
            { "data": "dpto_lider", "render": d => getNombreById('dptos', d) },
            { "data": "muni_lider", "render": d => getNombreById('munis', d) },
            { "data": "estado_lider" },
            // COLUMNA CRÍTICA: Definida para que nunca sea undefined
            {
                "data": "options",
                "defaultContent": "",
                "orderable": false
            }
        ],
        "columnDefs": [
            { "defaultContent": "-", "targets": "_all" } // Si falta cualquier dato, pone un guion
        ],
        "responsive": true,
        "destroy": true
    });

    const formLider = document.querySelector("#formLider");
    if (formLider) {
        formLider.onsubmit = async function (e) {
            e.preventDefault();

            // 1. Validaciones mínimas
            const strCedula = document.querySelector('#ident_lider').value;
            const strNombre = document.querySelector('#nom1_lider').value;
            const intMuni = document.querySelector('#muni_lider').value;

            if (strCedula == "" || strNombre == "" || intMuni == "") {
                swal("Atención", "Todos los campos con (*) son obligatorios.", "error");
                return false;
            }

            // 2. Captura de datos (FormData detectará todos los 'name' del HTML)
            const formData = new FormData(formLider);

            // 3. Envío al controlador corregido
            const objData = await fetchData(`${BASE_URL_API}/lideres/setLider`, 'POST', formData);

            if (objData?.status) {
                // Éxito: Cerrar modal, limpiar form y refrescar tabla
                $('#modalFormLider').modal("hide");
                formLider.reset();
                swal("Lideres", objData.msg, "success");
                tableLideres.ajax.reload();
            } else if (objData) {
                // Error controlado (ej: candidato ya existe)
                swal("Error", objData.msg, "error");
            } else {
                // Error de servidor (lo que veíamos antes como <br><b>)
                swal("Error", "Error interno del servidor al procesar la solicitud", "error");
            }
        };
    }

    // Evento para el cambio de departamento
    $('#dpto_lider').on('change', function () {
        filtrarMunicipios(this.value);
    });

    // Eventos de botones
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.btnView, .btnEdit, .btnDel, #btnNuevoLider');
        if (!target) return;
        const id = target.getAttribute('can');
        if (target.id === 'btnNuevoLider') openModal();
        if (target.classList.contains('btnView')) fntViewLider(id);
        if (target.classList.contains('btnEdit')) fntEditLider(id);
        if (target.classList.contains('btnDel')) fntDelLider(id);
    });
});

// 3. FUNCIONES LÓGICAS
async function cargarJson() {
    const res = await fetchData(`${BASE_URL_API}/lideres/getJsons`);
    if (res) {
        dataConfig = res;
        const llenarSelect = (selectorId, datos, llaveId, llaveNombre) => {
            const el = document.querySelector(selectorId);
            if (el && datos) {
                el.length = 1;
                datos.forEach(item => el.add(new Option(item[llaveNombre], item[llaveId])));
            }
        };
        llenarSelect('#dpto_lider', res.dptos, 'iddpto', 'namedpto');
        $('.selectpicker').selectpicker('refresh');
    }
}

function filtrarMunicipios(idDpto, idMuniSeleccionado = null) {
    if (!dataConfig || !dataConfig.munis) return;
    const listMuni = document.querySelector('#muni_lider');
    listMuni.length = 1;
    if (idDpto) {
        const filtrados = dataConfig.munis.filter(m => m.dptomuni == idDpto);
        filtrados.forEach(item => listMuni.add(new Option(item.namemuni, item.idmuni)));
    }
    if (idMuniSeleccionado) $(listMuni).val(idMuniSeleccionado);
    $(listMuni).selectpicker('refresh');
}

function getNombreById(tipo, id) {
    if (!dataConfig || !dataConfig[tipo]) return id;
    const campos = {
        'curules': { id: 'id', nombre: 'nombre' },
        'partidos': { id: 'id', nombre: 'nombre' },
        'dptos': { id: 'iddpto', nombre: 'namedpto' },
        'munis': { id: 'idmuni', nombre: 'namemuni' }
    };
    const config = campos[tipo];
    const item = dataConfig[tipo].find(x => x[config.id] == id);
    return item ? item[config.nombre] : id;
}

// 4. ACCIONES MODAL
function openModal(isEdit = false, data = null) {
    const form = document.querySelector("#formLider");
    form.reset();
    $('#idLider').val("");

    if (isEdit && data) {
        $('#titleModal').html("Actualizar Lider");
        $('#id_lider').val(data.id_lider);
        $('#ident_lider').val(data.ident_lider);
        $('#ape1_lider').val(data.ape1_lider);
        $('#ape2_lider').val(data.ape2_lider);
        $('#nom1_lider').val(data.nom1_lider);
        $('#nom2_lider').val(data.nom2_lider);
        $('#telefono_lider').val(data.telefono_lider);
        $('#email_lider').val(data.email_lider);
        $('#direccion_lider').val(data.direccion_lider);

        $('#dpto_lider').val(data.dpto_lider);
        filtrarMunicipios(data.dpto_lider, data.muni_lider);

        $('#estado_lider').val(data.estado_lider);
    } else {
        $('#titleModal').html("Nuevo Lider");
        filtrarMunicipios(""); // Limpia municipios
    }
    $('.selectpicker').selectpicker('refresh');
    $('#modalFormLider').modal('show');
}

// 4. ACCIONES (VIEW, EDIT, DELETE)

async function fntViewLider(id) {
    const res = await fetchData(`${BASE_URL_API}/lideres/getLider/${id}`);
    if (res?.status) {
        const d = res.data;
        // Función auxiliar para actualizar el HTML de los campos del modal de vista
        const setHtml = (selector, val) => {
            const el = document.querySelector(selector);
            if (el) el.innerHTML = val || "---";
        };

        setHtml('#celIdent', d.ident_lider);
        setHtml('#celNombre', `${d.nom1_lider} ${d.nom2_lider || ""}`);
        setHtml('#celApellido', `${d.ape1_lider} ${d.ape2_lider || ""}`);
        setHtml('#celTelefono', d.telefono_lider);
        setHtml('#celEmail', d.email_lider);
        setHtml('#celDireccion', d.direccion_lider);

        // Traducimos IDs a Nombres usando dataConfig
        setHtml('#celDpto', getNombreById('dptos', d.dpto_lider));
        setHtml('#celMuni', getNombreById('munis', d.muni_lider));

        setHtml('#celEstado', d.estado_lider == 1
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>');

        $('#modalViewLider').modal('show');
    } else {
        swal("Error", "No se pudo obtener la información del lider", "error");
    }
}

async function fntEditLider(idLider) {
    const res = await fetchData(`${BASE_URL_API}/lideres/getLider/${idLider}`);
    if (res?.status) {
        // Llamamos a openModal en modo edición pasando los datos
        openModal(true, res.data);
    } else {
        swal("Error", "No se pudieron obtener los datos para editar", "error");
    }
}

function fntDelLider(id) {
    swal({
        title: "Eliminar Lider",
        text: "¿Realmente quiere eliminar este registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, async (isConfirm) => {
        if (isConfirm) {
            // Usamos PUT o DELETE según tu API, aquí mantengo PUT según tu código original
            const res = await fetchData(`${BASE_URL_API}/lideres/delLider/`, 'PUT', { idlider: id });
            if (res?.status) {
                swal("Eliminado", res.msg, "success");
                tableLideres.ajax.reload();
            } else {
                swal("Error", res?.msg || "No se pudo eliminar", "error");
            }
        }
    });
}
// Mantenemos tus funciones fntViewCandidato, fntEditCandidato, fntDelCandidato igual...