let tableUsuarios;
$.fn.dataTable.ext.errMode = 'none';

/**
 * HELPER PARA PETICIONES FETCH
 */
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

document.addEventListener('DOMContentLoaded', async function () {

    // 1. CARGAR SELECT DE ROLES AL INICIO
    await fntRolesUsuario();

    // 2. INICIALIZACIÓN DE DATATABLE
    tableUsuarios = $('#tableUsuarios').DataTable({
        "processing": true,
        "language": { "url": `${BASE_URL}/assets/json/spanish.json` },
        "ajax": {
            "url": `${BASE_URL_API}/usuario/getUsers`,
            "type": "GET",
            "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
            "data": d => { d.rolUser = localStorage.getItem('userRol'); },
            "dataSrc": json => json.status ? json.data : [],
            "error": xhr => fntHandleError(xhr)
        },
        "columns": [
            { "data": "id_usuario" },
            { "data": "nombres_usuario" },
            { "data": "apellidos_usuario" },
            { "data": "telefono_usuario" },
            { "data": "email_usuario" },
            { "data": "nombre_rol" },
            { "data": "estado_usuario", "render": d => d == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>' },
            { "data": "options" }
        ],
        "responsive": true,
        "destroy": true,
        "order": [[0, "desc"]]
    });

    // 3. ENVÍO DEL FORMULARIO
    const formUsuario = document.querySelector("#formUsuario");
    if (formUsuario) {
        formUsuario.onsubmit = async function (e) {
            e.preventDefault();
            const invalid = formUsuario.querySelector(".is-invalid");
            if (invalid) return invalid.focus();

            const formData = new FormData(formUsuario);
            const objData = await fetchData(`${BASE_URL_API}/usuario/setUsuario`, 'POST', formData);

            if (objData?.status) {
                $('#modalFormUsuario').modal("hide");
                formUsuario.reset();
                swal("Usuarios", objData.msg, "success");
                tableUsuarios.ajax.reload();
            } else if (objData) {
                swal("Error", objData.msg, "error");
            }
        };
    }

    // 4. DELEGACIÓN DE EVENTOS
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.btnViewUsuario, .btnEditUsuario, .btnDelUsuario, #btnNuevoUsuario');
        if (!target) return;

        const id = target.getAttribute('us');
        if (target.id === 'btnNuevoUsuario') openModal();
        if (target.classList.contains('btnViewUsuario')) fntViewUsuario(id);
        if (target.classList.contains('btnEditUsuario')) fntEditUsuario(id);
        if (target.classList.contains('btnDelUsuario')) fntDelUsuario(id);
    });
});

/**
 * FUNCIONES DE ACCIÓN
 */

async function fntRolesUsuario() {
    const res = await fetchData(`${BASE_URL_API}/roles/getSelectRoles`);
    if (res?.status) {
        let html = '<option value="">Seleccione un Rol</option>';
        res.data.forEach(item => {
            if (item.status_rol == 1) {
                html += `<option value="${item.id_rol}">${item.nombre_rol}</option>`;
            }
        });
        $('#listRolid').html(html).selectpicker('refresh');
    }
}

async function fntViewUsuario(id) {
    const res = await fetchData(`${BASE_URL_API}/usuario/getUsuario/${id}`);
    if (res?.status) {
        const d = res.data;
        const setHtml = (id, val) => { if (document.querySelector(id)) document.querySelector(id).innerHTML = val; };

        setHtml('#celNombre', d.nombres_usuario);
        setHtml('#celApellido', d.apellidos_usuario);
        setHtml('#celTelefono', d.telefono_usuario);
        setHtml('#celEmail', d.email_usuario);
        setHtml('#celTipoUsuario', d.nombre_rol);
        setHtml('#celEstado', d.estado_usuario == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>');

        $('#modalViewUser').modal('show');
    }
}

async function fntEditUsuario(id) {
    const res = await fetchData(`${BASE_URL_API}/usuario/getUsuario/${id}`);
    if (res?.status) {
        openModal(true, res.data);
    }
}

function fntDelUsuario(id) {
    swal({
        title: "Eliminar Usuario",
        text: "¿Realmente quiere eliminar el Usuario?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!"
    }, async (isConfirm) => {
        if (isConfirm) {
            const res = await fetchData(`${BASE_URL_API}/usuario/delUsuario/${id}`, 'PUT');
            if (res?.status) {
                swal("Eliminado!", res.msg, "success");
                tableUsuarios.ajax.reload();
            }
        }
    });
}

function openModal(isEdit = false, data = null) {
    const form = document.querySelector("#formUsuario");
    if (!form) return;

    form.reset();
    document.querySelector('#idUsuario').value = "";
    const header = document.querySelector('.modal-header');
    const btn = document.querySelector('#btnActionForm');

    if (isEdit && data) {
        document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";
        header.classList.replace("headerRegister", "headerUpdate");
        btn.classList.replace("btn-primary", "btn-info");
        document.querySelector('#btnText').innerHTML = "Actualizar";

        document.querySelector('#idUsuario').value = data.id_usuario;
        document.querySelector('#txtNombre').value = data.nombres_usuario;
        document.querySelector('#txtApellido').value = data.apellidos_usuario;
        document.querySelector('#txtTelefono').value = data.telefono_usuario;
        document.querySelector('#txtEmail').value = data.email_usuario;

        $('#listRolid').val(data.id_rol || data.rol_usuario);
        $('#listStatus').val(data.estado_usuario);
    } else {
        document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
        header.classList.replace("headerUpdate", "headerRegister");
        btn.classList.replace("btn-info", "btn-primary");
        document.querySelector('#btnText').innerHTML = "Guardar";
        $('#listRolid').val("");
        $('#listStatus').val("1");
    }

    $('.selectpicker').selectpicker('refresh');
    $('#modalFormUsuario').modal('show');
}

function fntHandleError(xhr) {
    if (xhr.status === 401 || xhr.status === 400) {
        swal({
            title: "Sesión Expirada",
            text: "Tu sesión ha expirado o no tienes autorización.",
            type: "warning",
            confirmButtonText: "Aceptar"
        }, () => { window.location.href = BASE_URL + '/logout/logout'; });
    }
}