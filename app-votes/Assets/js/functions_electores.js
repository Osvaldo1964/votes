// functions_electores.js
// Optimizado para manejar cascada nacional de Dpto -> Muni -> Zona -> Puesto -> Mesa

let dataConfig = null;
let tableElectores;

document.addEventListener('DOMContentLoaded', async function () {
    // 1. CARGAR DEPARTAMENTOS INICIALES
    await fntGetDepartamentos();

    // 2. INICIALIZAR TABLA
    tableElectores = $('#tableElectores').DataTable({
        "processing": true,
        "language": lenguajeEspanol,
        "ajax": getDataTableFetchConfig('/electores/getElectores'),
        "columns": [
            { "data": "id_elector" },
            { "data": "ident_elector" },
            { "render": (d, t, row) => `${row.nom1_elector} ${row.nom2_elector || ""}` },
            { "render": (d, t, row) => `${row.ape1_elector} ${row.ape2_elector || ""}` },
            { "data": "telefono_elector" },
            { "data": "email_elector" },
            { "data": "name_department" },
            { "data": "name_municipality" },
            { "data": "estado_elector" },
            {
                "data": "options",
                "defaultContent": "",
                "orderable": false
            }
        ],
        "responsive": true,
        "destroy": true
    });

    // 3. EVENTO BÚSQUEDA CÉDULA
    let inputIdent = document.querySelector("#ident_elector");
    inputIdent.addEventListener('blur', async function () {
        let ident = this.value;
        if (ident.length > 5) {
            try {
                let data = await fetchData(BASE_URL_API + '/Electores/getValidaElector/' + ident);

                if (data && data.status) {
                    if (data.is_registered) {
                        swal({
                            title: "Atención",
                            text: "El elector ya se encuentra registrado.",
                            type: "warning",
                            confirmButtonText: "Aceptar"
                        }, function () {
                            inputIdent.value = "";
                            limpiarCampos();
                        });
                        return;
                    }

                    // POBLAR DATOS DE CENSO
                    document.querySelector("#ape1_elector").value = data.data.ape1_place || "";
                    document.querySelector("#ape2_elector").value = data.data.ape2_place || "";
                    document.querySelector("#nom1_elector").value = data.data.nom1_place || "";
                    document.querySelector("#nom2_elector").value = data.data.nom2_place || "";
                    document.querySelector("#insc_elector").value = "1";

                    // SELECCIONAR UBICACIÓN EN CASCADA
                    if (data.ids) {
                        await setUbicacionCascada(data.ids);
                    }

                    document.querySelector("#telefono_elector").focus();

                } else {
                    // CÉDULA NO ENCONTRADA - REGISTRO MANUAL
                    document.querySelector("#insc_elector").value = "0";
                    // No limpiamos todo, dejamos que el usuario escriba
                    swal("Info", "La cédula no se encuentra en el censo. Por favor ingrese los datos manualmente.", "info");
                    document.querySelector("#ape1_elector").focus();
                }
            } catch (error) {
                console.error("Error validando elector:", error);
            }
        }
    });

    // 4. EVENTOS DE CASCADA
    $('#dpto_elector').on('change', function () { fntGetMunicipios(this.value); });
    $('#muni_elector').on('change', function () { fntGetZonas(this.value); });
    $('#id_zone').on('change', function () { fntGetPuestos(this.value); });
    $('#id_puesto').on('change', function () { fntGetMesas(this.value); });

    // 5. SUBMIT FORM
    const formElector = document.querySelector("#formElector");
    if (formElector) {
        formElector.onsubmit = async function (e) {
            e.preventDefault();
            const formData = new FormData(formElector);
            const objData = await fetchData(`${BASE_URL_API}/electores/setElector`, 'POST', formData);

            if (objData?.status) {
                let isNew = document.querySelector("#id_elector").value == "";
                if (isNew) {
                    let currentLider = document.querySelector("#lider_elector").value;
                    formElector.reset();
                    document.querySelector("#lider_elector").value = currentLider;
                    limpiarCampos();
                    swal("Guardado", "Elector registrado correctamente", "success");
                } else {
                    $('#modalFormElector').modal("hide");
                    swal("Actualizado", objData.msg, "success");
                }
                tableElectores.ajax.reload();
            } else {
                swal("Error", objData?.msg || "Error al procesar", "error");
            }
        };
    }

    // CARGAR LÍDERES
    await fntGetLideres();
});

// --- FUNCIONES DE CASCADA ---

async function fntGetDepartamentos() {
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getDepartamentos');
        let options = '<option value="">Seleccione...</option>';
        if (data?.status && Array.isArray(data.data)) {
            data.data.forEach(d => {
                options += `<option value="${d.id_department}">${d.name_department}</option>`;
            });
        }
        document.querySelector('#dpto_elector').innerHTML = options;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetMunicipios(idDpto, idSel = null) {
    if (!idDpto) return;
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getMunicipios/' + idDpto);
        let options = '<option value="">Seleccione...</option>';
        if (data?.status && Array.isArray(data.data)) {
            data.data.forEach(m => {
                options += `<option value="${m.id_municipality}">${m.name_municipality}</option>`;
            });
        }
        const sel = document.querySelector('#muni_elector');
        sel.innerHTML = options;
        if (idSel) $(sel).val(idSel);
        $('.selectpicker').selectpicker('refresh');
        if (!idSel) {
            resetSelects(['#id_zone', '#id_puesto', '#id_mesa']);
        }
    } catch (e) { console.error(e); }
}

async function fntGetZonas(idMuni, idSel = null) {
    if (!idMuni) return;
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getZonas/' + idMuni);
        let options = '<option value="">Seleccione...</option>';
        if (data?.status && Array.isArray(data.data)) {
            data.data.forEach(z => {
                options += `<option value="${z.id_zone}">${z.name_zone}</option>`;
            });
        }
        const sel = document.querySelector('#id_zone');
        sel.innerHTML = options;
        if (idSel) $(sel).val(idSel);
        $('.selectpicker').selectpicker('refresh');
        if (!idSel) {
            resetSelects(['#id_puesto', '#id_mesa']);
        }
    } catch (e) { console.error(e); }
}

async function fntGetPuestos(idZona, idSel = null) {
    if (!idZona) return;
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getPuestos/' + idZona);
        let options = '<option value="">Seleccione...</option>';
        if (data?.status && Array.isArray(data.data)) {
            data.data.forEach(p => {
                options += `<option value="${p.id_place}">${p.nameplace_place}</option>`;
            });
        }
        const sel = document.querySelector('#id_puesto');
        sel.innerHTML = options;
        if (idSel) $(sel).val(idSel);
        $('.selectpicker').selectpicker('refresh');
        if (!idSel) {
            resetSelects(['#id_mesa']);
        }
    } catch (e) { console.error(e); }
}

async function fntGetMesas(idPuesto, idSel = null) {
    const idZona = document.querySelector('#id_zone').value;
    const formData = new FormData();
    formData.append('idZona', idZona);
    formData.append('idPuesto', idPuesto);

    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getMesas', 'POST', formData);
        let options = '<option value="">Seleccione...</option>';
        if (data?.status && Array.isArray(data.data)) {
            data.data.forEach(m => {
                options += `<option value="${m.id_mesa}">${m.nombre_mesa}</option>`;
            });
        }
        const sel = document.querySelector('#id_mesa');
        sel.innerHTML = options;
        $('.selectpicker').selectpicker('refresh'); // Rebuild options first
        if (idSel) {
            $(sel).selectpicker('val', idSel); // Then select
        }
    } catch (e) { console.error(e); }
}

function resetSelects(selectors) {
    selectors.forEach(s => {
        document.querySelector(s).innerHTML = '<option value="">Seleccione...</option>';
    });
    $('.selectpicker').selectpicker('refresh');
}

async function setUbicacionCascada(ids) {
    // ids: { dpto, muni, zone, puesto, mesa }
    if (ids.dpto) {
        $('#dpto_elector').val(ids.dpto).selectpicker('refresh');
        await fntGetMunicipios(ids.dpto, ids.muni);
        if (ids.muni) {
            await fntGetZonas(ids.muni, ids.zone);
            if (ids.zone) {
                await fntGetPuestos(ids.zone, ids.puesto);
                if (ids.puesto) {
                    await fntGetMesas(ids.puesto, ids.mesa);
                }
            }
        }
    }
}

async function fntGetLideres() {
    const res = await fetchData(`${BASE_URL_API}/Lideres/getSelectLideres`);
    if (res?.status) {
        let options = '<option value="">Seleccione</option>';
        res.data.forEach(l => {
            options += `<option value="${l.id_lider}">${l.nom1_lider} ${l.ape1_lider}</option>`;
        });
        document.querySelector("#lider_elector").innerHTML = options;
        $('.selectpicker').selectpicker('refresh');
    }
}

function openModal(isEdit = false, data = null) {
    const form = document.querySelector("#formElector");
    form.reset();
    $('#id_elector').val("");
    limpiarCampos();

    if (isEdit && data) {
        $('#titleModal').html("Actualizar Elector");
        $('#id_elector').val(data.id_elector);
        $('#ident_elector').val(data.ident_elector);
        $('#ape1_elector').val(data.ape1_elector);
        $('#ape2_elector').val(data.ape2_elector);
        $('#nom1_elector').val(data.nom1_elector);
        $('#nom2_elector').val(data.nom2_elector);
        $('#telefono_elector').val(data.telefono_elector);
        $('#email_elector').val(data.email_elector);
        $('#direccion_elector').val(data.direccion_elector);
        $('#lider_elector').val(data.lider_elector);
        $('#estado_elector').val(data.estado_elector);
        $('#insc_elector').val(data.insc_elector);

        // Cargar ubicación si tiene mesa asignada
        if (data.id_mesa_new) {
            fetchData(BASE_URL_API + '/Electores/getUbicacionMesa/' + data.id_mesa_new)
                .then(res => {
                    if (res.status) setUbicacionCascada(res.ids);
                });
        }
    } else {
        $('#titleModal').html("Nuevo Elector");
        $('#insc_elector').val("1");
    }
    $('.selectpicker').selectpicker('refresh');
    $('#modalFormElector').modal('show');
}

function limpiarCampos() {
    document.querySelectorAll(".form-control").forEach(i => {
        i.classList.remove("is-valid", "is-invalid");
    });
    // No reseteamos el líder si estamos en registro masivo (manejado en submit)
}

// Botones de acción
document.addEventListener('click', function (e) {
    const target = e.target.closest('.btnView, .btnEdit, .btnDel, #btnNuevoElector');
    if (!target) return;
    const id = target.getAttribute('can');
    if (target.id === 'btnNuevoElector') openModal();
    if (target.classList.contains('btnEdit')) fntEditElector(id);
    if (target.classList.contains('btnDel')) fntDelElector(id);
    if (target.classList.contains('btnView')) fntViewElector(id);
});

async function fntEditElector(id) {
    const res = await fetchData(`${BASE_URL_API}/electores/getElector/${id}`);
    if (res?.status) openModal(true, res.data);
}

async function fntViewElector(id) {
    const res = await fetchData(`${BASE_URL_API}/electores/getElector/${id}`);
    if (res?.status) {
        const d = res.data;
        const setHtml = (selector, val) => {
            const el = document.querySelector(selector);
            if (el) el.innerHTML = val || "---";
        };
        setHtml('#celLider', `${d.nom1_lider} ${d.ape1_lider || ""}`);
        setHtml('#celIdent', d.ident_elector);
        setHtml('#celNombre', `${d.nom1_elector} ${d.nom2_elector || ""}`);
        setHtml('#celApellido', `${d.ape1_elector} ${d.ape2_elector || ""}`);
        setHtml('#celTelefono', d.telefono_elector);
        setHtml('#celEmail', d.email_elector);
        setHtml('#celDireccion', d.direccion_elector);
        setHtml('#celDpto', d.name_department);
        setHtml('#celMuni', d.name_municipality);
        setHtml('#celEstado', d.estado_elector == 1 ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>');
        $('#modalViewElector').modal('show');
    }
}

async function fntDelElector(id) {
    swal({
        title: "Eliminar Elector",
        text: "¿Realmente quiere eliminar este registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        closeOnConfirm: false
    }, async (isConfirm) => {
        if (isConfirm) {
            const res = await fetchData(`${BASE_URL_API}/electores/delElector/`, 'PUT', { id_elector: id });
            if (res?.status) {
                swal("Eliminado", res.msg, "success");
                tableElectores.ajax.reload();
            }
        }
    });
}