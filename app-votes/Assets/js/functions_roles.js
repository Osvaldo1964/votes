// functions_roles.js
// Optimizado - Utiliza fetchData global

let tableRoles;
$.fn.dataTable.ext.errMode = 'none';

document.addEventListener('DOMContentLoaded', function () {

    // 2. INICIALIZACIÓN DE DATATABLE
    tableRoles = $('#tableRoles').DataTable({
        "processing": true,
        "language": { "url": `${BASE_URL}/assets/json/spanish.json` },
        "ajax": getDataTableFetchConfig('/roles/getRoles'),
        "columns": [
            { "data": "id_rol" },
            { "data": "nombre_rol" },
            { "data": "descript_rol" },
            { "data": "status_rol", "render": d => d == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>' },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "order": [[0, "desc"]]
    });

    // 3. GUARDAR ROL
    const formRol = document.querySelector("#formRol");
    if (formRol) {
        formRol.onsubmit = async function (e) {
            e.preventDefault();
            const formData = new FormData(formRol);
            const objData = await fetchData(`${BASE_URL_API}/roles/setRol`, 'POST', formData);

            if (objData?.status) {
                $('#modalFormRol').modal("hide");
                formRol.reset();
                swal("Roles", objData.msg, "success");
                tableRoles.ajax.reload();
            } else if (objData) {
                swal("Error", objData.msg, "error");
            }
        };
    }

    // 4. DELEGACIÓN DE EVENTOS
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.btnEditRol, .btnDelRol, .btnPermisosRol, #btnNuevoRol');
        if (!target) return;

        const id = target.getAttribute('rl');
        if (target.id === 'btnNuevoRol') openModal();
        if (target.classList.contains('btnEditRol')) fntEditRol(id);
        if (target.classList.contains('btnDelRol')) fntDelRol(id);
        if (target.classList.contains('btnPermisosRol')) fntPermisos(id);
    });
});

// --- ACCIONES ---

function openModal(isEdit = false, data = null) {
    const form = document.querySelector("#formRol");
    if (!form) return;
    form.reset();

    document.querySelector('#idRol').value = "";
    const header = document.querySelector('.modal-header');
    const btn = document.querySelector('#btnActionForm');

    if (isEdit && data) {
        document.querySelector('#titleModal').innerHTML = "Actualizar Rol";
        header.classList.replace("headerRegister", "headerUpdate");
        btn.classList.replace("btn-primary", "btn-info");
        document.querySelector('#btnText').innerHTML = "Actualizar";

        document.querySelector("#idRol").value = data.id_rol;
        document.querySelector("#txtNombre").value = data.nombre_rol;
        document.querySelector("#txtDescripcion").value = data.descript_rol;
        $('#listStatus').val(data.status_rol);
    } else {
        document.querySelector('#titleModal').innerHTML = "Nuevo Rol";
        header.classList.replace("headerUpdate", "headerRegister");
        btn.classList.replace("btn-info", "btn-primary");
        document.querySelector('#btnText').innerHTML = "Guardar";
        $('#listStatus').val('1');
    }
    $('.selectpicker').selectpicker('refresh');
    $('#modalFormRol').modal('show');
}

async function fntEditRol(id) {
    const res = await fetchData(`${BASE_URL_API}/roles/getRol/${id}`);
    if (res?.status) {
        openModal(true, res.data);
    }
}

function fntDelRol(id) {
    swal({
        title: "Eliminar Rol",
        text: "¿Realmente quiere eliminar el Rol?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!"
    }, async (isConfirm) => {
        if (isConfirm) {
            const res = await fetchData(`${BASE_URL_API}/roles/delRol/`, 'PUT', { idrol: id });
            if (res?.status) {
                swal("Eliminado!", res.msg, "success");
                tableRoles.ajax.reload();
            }
        }
    });
}

// --- GESTIÓN DE PERMISOS ---

async function fntPermisos(idRol) {
    const res = await fetchData(`${BASE_URL_API}/permisos/getPermisosRol/${idRol}`);
    if (res?.status) {
        let htmlTable = "";
        res.data.forEach((modulo, index) => {
            const check = (p) => (modulo.permisos && modulo.permisos[p] == 1) ? "checked" : "";

            htmlTable += `
                <tr>
                    <td>${index + 1} <input type="hidden" name="modulos[${modulo.id_modulo}][idmodulo]" value="${modulo.id_modulo}"></td>
                    <td>${modulo.titulo_modulo}</td>
                    ${['r', 'w', 'u', 'd'].map(p => `
                        <td>
                            <div class="toggle-flip">
                                <label>
                                    <input type="checkbox" name="modulos[${modulo.id_modulo}][${p}]" ${check(p)}>
                                    <span class="flip-indecator" data-toggle-on="ON" data-toggle-off="OFF"></span>
                                </label>
                            </div>
                        </td>
                    `).join('')}
                </tr>`;
        });

        document.querySelector('#contentAjax').innerHTML = htmlTable;
        if (document.querySelector('#idrol')) document.querySelector('#idrol').value = idRol;
        $('.modalPermisos').modal('show');

        document.querySelector('#formPermisos').onsubmit = fntSavePermisos;
    }
}

async function fntSavePermisos(e) {
    e.preventDefault();
    const formData = new FormData(document.querySelector('#formPermisos'));
    const res = await fetchData(`${BASE_URL_API}/permisos/setPermisos`, 'POST', formData);

    if (res?.status) {
        swal("Permisos", res.msg, "success");
        $('.modalPermisos').modal('hide');
    } else if (res) {
        swal("Error", res.msg, "error");
    }
}

function fntHandleError(xhr) {
    if (xhr.status === 401 || xhr.status === 400) {
        swal({
            title: "Sesión Expirada",
            text: "Tu sesión ha expirado o no tienes autorización.",
            type: "warning"
        }, () => { window.location.href = BASE_URL + '/logout/logout'; });
    }
}