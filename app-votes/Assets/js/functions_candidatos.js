// functions_candidatos.js
// Optimizado - Utiliza fetchData y lenguajeEspanol globales (functions_admin.js)

let dataConfig = null;
let tableCandidatos;

// 2. INICIO DEL DOCUMENTO
document.addEventListener('DOMContentLoaded', async function () {
    // 1. CARGAR CONFIGURACIÓN PRIMERO
    await cargarJson();

    // 2. INICIALIZAR TABLA
    tableCandidatos = $('#tableCandidatos').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global
        "ajax": getDataTableFetchConfig('/candidatos/getCandidatos'),
        "columns": [
            { "data": "id_candidato" },
            { "data": "ident_candidato" },
            { "render": (d, t, row) => `${row.nom1_candidato || ""} ${row.nom2_candidato || ""}` },
            { "render": (d, t, row) => `${row.ape1_candidato || ""} ${row.ape2_candidato || ""}` },
            { "data": "telefono_candidato" },
            { "data": "email_candidato" },
            { "data": "dpto_candidato", "render": d => getNombreById('dptos', d) },
            { "data": "muni_candidato", "render": d => getNombreById('munis', d) },
            { "data": "curul_candidato", "render": d => getNombreById('curules', d) },
            { "data": "partido_candidato", "render": d => getNombreById('partidos', d) },
            {
                "data": "estado_candidato",
                "render": d => d == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'
            },
            // COLUMNA BLINDADA: defaultContent evita el error si 'options' no viene en el JSON
            {
                "data": "options",
                "defaultContent": "",
                "orderable": false
            }
        ],
        // REGLA DE ORO: Si cualquier celda viene undefined por error, pone un guion en lugar de romperse
        "columnDefs": [
            { "defaultContent": "-", "targets": "_all" }
        ],
        "responsive": true,
        "destroy": true,
        "order": [[0, "desc"]]
    });

    const formCandidato = document.querySelector("#formCandidato");
    if (formCandidato) {
        formCandidato.onsubmit = async function (e) {
            e.preventDefault();

            // 1. Validaciones mínimas
            const strCedula = document.querySelector('#ident_candidato').value;
            const strNombre = document.querySelector('#nom1_candidato').value;
            const intMuni = document.querySelector('#muni_candidato').value;

            if (strCedula == "" || strNombre == "" || intMuni == "") {
                swal("Atención", "Todos los campos con (*) son obligatorios.", "error");
                return false;
            }

            // 2. Captura de datos (FormData detectará todos los 'name' del HTML)
            const formData = new FormData(formCandidato);

            // 3. Envío al controlador corregido
            const objData = await fetchData(`${BASE_URL_API}/candidatos/setCandidato`, 'POST', formData);

            if (objData?.status) {
                // Éxito: Cerrar modal, limpiar form y refrescar tabla
                $('#modalFormCandidato').modal("hide");
                formCandidato.reset();
                swal("Candidatos", objData.msg, "success");
                tableCandidatos.ajax.reload();
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
    $('#dpto_candidato').on('change', function () {
        filtrarMunicipios(this.value);
    });

    // Eventos de botones
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.btnView, .btnEdit, .btnDel, #btnNuevoCandidato');
        if (!target) return;
        const id = target.getAttribute('can');
        if (target.id === 'btnNuevoCandidato') openModal();
        if (target.classList.contains('btnView')) fntViewCandidato(id);
        if (target.classList.contains('btnEdit')) fntEditCandidato(id);
        if (target.classList.contains('btnDel')) fntDelCandidato(id);
    });
});

// 3. FUNCIONES LÓGICAS
async function cargarJson() {
    const res = await fetchData(`${BASE_URL_API}/candidatos/getJsons`);
    if (res) {
        dataConfig = res;
        const llenarSelect = (selectorId, datos, llaveId, llaveNombre) => {
            const el = document.querySelector(selectorId);
            if (el && datos) {
                el.length = 1;
                datos.forEach(item => el.add(new Option(item[llaveNombre], item[llaveId])));
            }
        };
        llenarSelect('#curul_candidato', res.curules, 'id', 'nombre');
        llenarSelect('#partido_candidato', res.partidos, 'id', 'nombre');
        llenarSelect('#dpto_candidato', res.dptos, 'iddpto', 'namedpto');
        $('.selectpicker').selectpicker('refresh');
    }
}

function filtrarMunicipios(idDpto, idMuniSeleccionado = null) {
    if (!dataConfig || !dataConfig.munis) return;
    const listMuni = document.querySelector('#muni_candidato');
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
    const form = document.querySelector("#formCandidato");
    form.reset();
    $('#idCandidato').val("");

    if (isEdit && data) {
        $('#titleModal').html("Actualizar Candidato");
        $('#id_candidato').val(data.id_candidato);
        $('#ident_candidato').val(data.ident_candidato);
        $('#ape1_candidato').val(data.ape1_candidato);
        $('#ape2_candidato').val(data.ape2_candidato);
        $('#nom1_candidato').val(data.nom1_candidato);
        $('#nom2_candidato').val(data.nom2_candidato);
        $('#telefono_candidato').val(data.telefono_candidato);
        $('#email_candidato').val(data.email_candidato);
        $('#direccion_candidato').val(data.direccion_candidato);

        $('#dpto_candidato').val(data.dpto_candidato);
        filtrarMunicipios(data.dpto_candidato, data.muni_candidato);

        $('#curul_candidato').val(data.curul_candidato);
        $('#partido_candidato').val(data.partido_candidato);
        $('#estado_candidato').val(data.estado_candidato);
    } else {
        $('#titleModal').html("Nuevo Candidato");
        filtrarMunicipios(""); // Limpia municipios
    }
    $('.selectpicker').selectpicker('refresh');
    $('#modalFormCandidato').modal('show');
}

// 4. ACCIONES (VIEW, EDIT, DELETE)

async function fntViewCandidato(id) {
    const res = await fetchData(`${BASE_URL_API}/candidatos/getCandidato/${id}`);
    if (res?.status) {
        const d = res.data;
        // Función auxiliar para actualizar el HTML de los campos del modal de vista
        const setHtml = (selector, val) => {
            const el = document.querySelector(selector);
            if (el) el.innerHTML = val || "---";
        };

        setHtml('#celIdent', d.ident_candidato);
        setHtml('#celNombre', `${d.nom1_candidato} ${d.nom2_candidato || ""}`);
        setHtml('#celApellido', `${d.ape1_candidato} ${d.ape2_candidato || ""}`);
        setHtml('#celTelefono', d.telefono_candidato);
        setHtml('#celEmail', d.email_candidato);
        setHtml('#celDireccion', d.direccion_candidato);

        // Traducimos IDs a Nombres usando dataConfig
        setHtml('#celDpto', getNombreById('dptos', d.dpto_candidato));
        setHtml('#celMuni', getNombreById('munis', d.muni_candidato));
        setHtml('#celCurul', getNombreById('curules', d.curul_candidato));
        setHtml('#celPartido', getNombreById('partidos', d.partido_candidato));

        setHtml('#celEstado', d.estado_candidato == 1
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>');

        $('#modalViewCandidato').modal('show');
    } else {
        swal("Error", "No se pudo obtener la información del candidato", "error");
    }
}

async function fntEditCandidato(idCandidato) {
    const res = await fetchData(`${BASE_URL_API}/candidatos/getCandidato/${idCandidato}`);
    if (res?.status) {
        // Llamamos a openModal en modo edición pasando los datos
        openModal(true, res.data);
    } else {
        swal("Error", "No se pudieron obtener los datos para editar", "error");
    }
}

function fntDelCandidato(id) {
    swal({
        title: "Eliminar Candidato",
        text: "¿Realmente quiere eliminar este registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, async (isConfirm) => {
        if (isConfirm) {
            // Usamos PUT o DELETE según tu API, aquí mantengo PUT según tu código original
            const res = await fetchData(`${BASE_URL_API}/candidatos/delCandidato/`, 'PUT', { idcandidato: id });
            if (res?.status) {
                swal("Eliminado", res.msg, "success");
                tableCandidatos.ajax.reload();
            } else {
                swal("Error", res?.msg || "No se pudo eliminar", "error");
            }
        }
    });
}
// Mantenemos tus funciones fntViewCandidato, fntEditCandidato, fntDelCandidato igual...