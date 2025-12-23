const BASE_URL = "http://app-votes.com";
const BASE_URL_API = "http://api-votes.com";

let dataConfig = null;
let tableCandidatos;
$.fn.dataTable.ext.errMode = 'none';

// 1. HELPER DE PETICIONES
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
        if (response.status === 401 || response.status === 400) {
            fntHandleError(response);
            return null;
        }
        return await response.json();
    } catch (error) {
        console.error("Error en la petición:", error);
        return null;
    }
}

// 2. INICIO DEL DOCUMENTO
document.addEventListener('DOMContentLoaded', async function () {

    // Esperar a que el JSON cargue para que la tabla tenga nombres desde el inicio
    await cargarJson();

    tableCandidatos = $('#tableCandidatos').DataTable({
        "processing": true,
        "language": { "url": `${BASE_URL}/assets/json/spanish.json` },
        "ajax": {
            "url": `${BASE_URL_API}/candidatos/getCandidatos`,
            "type": "GET",
            "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
            "data": d => { d.rolUser = localStorage.getItem('userRol'); },
            "dataSrc": json => json.status ? json.data : [],
            "error": xhr => fntHandleError(xhr)
        },
        "columns": [
            { "data": "id_candidato" },
            { "data": "ident_candidato" },
            { "render": (d, t, row) => `${row.nom1_candidato} ${row.nom2_candidato || ""}` },
            { "render": (d, t, row) => `${row.ape1_candidato} ${row.ape2_candidato || ""}` },
            { "data": "telefono_candidato" },
            { "data": "email_candidato" },
            { "data": "direccion_candidato" },
            { "data": "curul_candidato", "render": d => getNombreById('curules', d) },
            { "data": "partido_candidato", "render": d => getNombreById('partidos', d) },
            { "data": "estado_candidato", "render": d => d == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>' },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "order": [[0, "desc"]]
    });

    const formCandidato = document.querySelector("#formCandidato");
    if (formCandidato) {
        formCandidato.onsubmit = async function (e) {
            e.preventDefault();

            if (formCandidato.listCurul.value == "" || formCandidato.listPartido.value == "") {
                swal("Atención", "Seleccione una Curul y un Partido.", "error");
                return false;
            }

            const formData = new FormData(formCandidato);
            const objData = await fetchData(`${BASE_URL_API}/candidatos/setCandidato`, 'POST', formData);

            if (objData?.status) {
                $('#modalFormCandidato').modal("hide");
                formCandidato.reset();
                swal("Candidatos", objData.msg, "success");
                tableCandidatos.ajax.reload();
            } else if (objData) {
                swal("Error", objData.msg, "error");
            }
        };
    }

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

// 3. FUNCIONES DE LÓGICA Y DATOS
async function cargarJson() {
    const res = await fetchData(`${BASE_URL_API}/candidatos/getJsons`);
    if (res) {
        dataConfig = res;
        const llenarSelect = (id, datos) => {
            const el = document.querySelector(id);
            if (el) {
                el.length = 1;
                datos.forEach(item => el.add(new Option(item.nombre, item.id)));
            }
        };
        llenarSelect('#listCurul', res.curules);
        llenarSelect('#listPartido', res.partidos);
        $('.selectpicker').selectpicker('refresh');
    }
}

function getNombreById(tipo, id) {
    if (!dataConfig || !dataConfig[tipo]) return id;
    const item = dataConfig[tipo].find(x => x.id == id);
    return item ? item.nombre : id;
}

// 4. ACCIONES (VIEW, EDIT, DELETE, MODAL)
async function fntViewCandidato(id) {
    const res = await fetchData(`${BASE_URL_API}/candidatos/getCandidato/${id}`);
    if (res?.status) {
        const d = res.data;
        const setHtml = (id, val) => { if (document.querySelector(id)) document.querySelector(id).innerHTML = val; };

        setHtml('#celIdent', d.ident_candidato);
        setHtml('#celNombre', `${d.nom1_candidato} ${d.nom2_candidato}`);
        setHtml('#celApellido', `${d.ape1_candidato} ${d.ape2_candidato}`);
        setHtml('#celTelefono', d.telefono_candidato);
        setHtml('#celEmail', d.email_candidato);
        setHtml('#celDireccion', d.direccion_candidato);
        setHtml('#celCurul', getNombreById('curules', d.curul_candidato));
        setHtml('#celPartido', getNombreById('partidos', d.partido_candidato));
        setHtml('#celEstado', d.estado_candidato == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>');

        $('#modalViewCandidato').modal('show');
    }
}

async function fntEditCandidato(idCandidato) {
    const res = await fetchData(`${BASE_URL_API}/candidatos/getCandidato/${idCandidato}`);
    if (res?.status) {
        openModal(true, res.data);
    } else {
        swal("Error", "No se pudieron obtener los datos", "error");
    }
}

function fntDelCandidato(id) {
    swal({
        title: "Eliminar",
        text: "¿Realmente quiere eliminar el Candidato?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!"
    }, async (isConfirm) => {
        if (isConfirm) {
            const res = await fetchData(`${BASE_URL_API}/candidatos/delCandidato/`, 'PUT', { idcandidato: id });
            if (res?.status) {
                swal("Eliminado!", res.msg, "success");
                tableCandidatos.ajax.reload();
            }
        }
    });
}

function openModal(isEdit = false, data = null) {
    const form = document.getElementById("formCandidato");
    if (!form) return;

    form.reset();
    document.querySelector('#idCandidato').value = "";
    const header = document.querySelector('.modal-header');
    const btn = document.querySelector('#btnActionForm');

    if (isEdit && data) {
        document.querySelector('#titleModal').innerHTML = "Actualizar Candidato";
        header.classList.replace("headerRegister", "headerUpdate");
        btn.classList.replace("btn-primary", "btn-info");
        document.querySelector('#btnText').innerHTML = "Actualizar";

        document.querySelector("#idCandidato").value = data.id_candidato;
        document.querySelector("#txtCedula").value = data.ident_candidato;
        document.querySelector("#txtApe1").value = data.ape1_candidato;
        document.querySelector("#txtApe2").value = data.ape2_candidato;
        document.querySelector("#txtNom1").value = data.nom1_candidato;
        document.querySelector("#txtNom2").value = data.nom2_candidato;
        document.querySelector("#txtTelefono").value = data.telefono_candidato;
        document.querySelector("#txtEmail").value = data.email_candidato;
        document.querySelector("#txtDireccion").value = data.direccion_candidato;

        $('#listCurul').val(data.curul_candidato);
        $('#listPartido').val(data.partido_candidato);
        $('#listEstado').val(data.estado_candidato);
    } else {
        document.querySelector('#titleModal').innerHTML = "Nuevo Candidato";
        header.classList.replace("headerUpdate", "headerRegister");
        btn.classList.replace("btn-info", "btn-primary");
        document.querySelector('#btnText').innerHTML = "Guardar";
        $('#listCurul').val("");
        $('#listPartido').val("");
        $('#listEstado').val("1");
    }
    $('.selectpicker').selectpicker('refresh');
    $('#modalFormCandidato').modal('show');
}