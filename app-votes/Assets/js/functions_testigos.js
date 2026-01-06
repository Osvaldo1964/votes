
let tableTestigos;
let rowTable = "";

document.addEventListener('DOMContentLoaded', function () {

    let rolUser = localStorage.getItem('userRol');

    tableTestigos = $('#tableTestigos').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": lenguajeEspanol,
        "ajax": getDataTableFetchConfig('/Testigos/getTestigos?rolUser=' + rolUser),
        "columns": [
            { "data": "id_testigo" },
            { "data": "ident_elector" },
            {
                "data": null,
                "render": function (data, type, row) {
                    return row.nom1_elector + " " + row.ape1_elector;
                }
            },
            {
                "data": null,
                "render": function (data, type, row) {
                    return row.nameplace_place ? row.nameplace_place : '<span class="text-muted">No Asignado</span>';
                }
            },
            { "data": "estado_testigo" },
            { "data": "options" }
        ],
        "resonsive": "true",
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [[0, "desc"]]
    });

    // Cargar listas iniciales
    fntGetElectores(); // Carga TODOS los electores en el select
    fntGetDepartamentos(); // Carga Dptos

    // Listeners Cascadas Ubicacion
    document.querySelector('#listDpto').addEventListener('change', function () {
        fntGetMunicipios(this.value);
    });
    document.querySelector('#listMuni').addEventListener('change', function () {
        fntGetZonas(this.value);
    });
    document.querySelector('#listZona').addEventListener('change', function () {
        fntGetPuestos(this.value);
    });


    // Submit Form
    if (document.querySelector("#formTestigo")) {
        let formTestigo = document.querySelector("#formTestigo");
        formTestigo.onsubmit = function (e) {
            e.preventDefault();
            let strElector = document.querySelector('#listElector').value;
            let intEstado = document.querySelector('#listEstado').value;

            if (strElector == '' || intEstado == '') {
                swal("Atención", "Todos los campos obligatorios deben ser llenados.", "error");
                return false;
            }

            // Asegurar que los selects estén habilitados para que viajen en el POST
            document.querySelector('#listMuni').disabled = false;
            document.querySelector('#listZona').disabled = false;
            document.querySelector('#listPuesto').disabled = false;

            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = BASE_URL_API + '/Testigos/setTestigo';
            let formData = new FormData(formTestigo);

            // Añadir Token de Auth (Obtenido de localStorage o session)
            // Asumimos que fetchData maneja el token, pero XMLHttpRequest manual NO.
            // Aqui usaremos fetchData simulado o agregaremos header. 
            // MEJOR REEMPLAZAR POR fetchData para consistencia y Auth

            fntSaveTestigo(formData);
        }
    }
});

async function fntSaveTestigo(formData) {
    try {
        const data = await fetchData(BASE_URL_API + '/Testigos/setTestigo', 'POST', formData);
        if (data.status) {
            $('#modalFormTestigo').modal("hide");
            formTestigo.reset();
            swal("Testigos", data.msg, "success");
            tableTestigos.ajax.reload();
        } else {
            swal("Error", data.msg, "error");
        }
    } catch (error) {
        console.error(error);
        swal("Error", "Error al guardar.", "error");
    }
}

async function fntGetElectores() {
    try {
        const data = await fetchData(BASE_URL_API + '/Electores/getSelectElectores');
        if (data.status) {
            let htmlOptions = '<option value="">Seleccione Elector...</option>';
            data.data.forEach(e => {
                htmlOptions += `<option value="${e.id_elector}">${e.ident_elector} - ${e.nom1_elector} ${e.ape1_elector}</option>`;
            });
            document.querySelector('#listElector').innerHTML = htmlOptions;
            $('#listElector').selectpicker('refresh');
        }
    } catch (e) {
        console.error("Error cargando electores para select", e);
    }
}


function openModal() {
    rowTable = "";
    document.querySelector('#idTestigo').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Testigo";
    document.querySelector("#formTestigo").reset();

    // Reset Selects
    $('#listElector').val('').selectpicker('refresh');
    $('#listDpto').val('').selectpicker('refresh'); // Ajustar si tienes default
    // Limpiar cascadas...

    $('#modalFormTestigo').modal('show');
}

async function fntEditTestigo(idtestigo) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Testigo";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    try {
        const data = await fetchData(BASE_URL_API + '/Testigos/getTestigo/' + idtestigo);
        if (data.status) {
            let t = data.data; // objeto testigo directo (no join)
            document.querySelector("#idTestigo").value = t.id_testigo;
            document.querySelector("#listEstado").value = t.estado_testigo;

            // Set Elector
            $('#listElector').val(t.elector_testigo).selectpicker('refresh');

            // Cargar Cascada Ubicacion
            // Nota: Esto requiere que las funciones sean asincronas para esperar carga antes de setear value
            // Por simplicidad, seteamos Dpto y gatillamos change... NO, mejor llamamos manualmente y esperamos

            if (t.dpto_testigo > 0) {
                $('#listDpto').val(t.dpto_testigo).selectpicker('refresh');
                await fntGetMunicipios(t.dpto_testigo);
                $('#listMuni').val(t.muni_testigo).selectpicker('refresh');

                if (t.muni_testigo > 0) {
                    await fntGetZonas(t.muni_testigo);
                    $('#listZona').val(t.zona_testigo).selectpicker('refresh');

                    if (t.zona_testigo > 0) {
                        await fntGetPuestos(t.zona_testigo);
                        $('#listPuesto').val(t.puesto_testigo).selectpicker('refresh');
                    }
                }
            }

            $('#modalFormTestigo').modal('show');
        } else {
            swal("Error", data.msg, "error");
        }
    } catch (e) {
        console.error(e);
    }
}

async function fntDelTestigo(idtestigo) {
    swal({
        title: "Eliminar Testigo",
        text: "¿Realmente quiere eliminar este testigo?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, async function (isConfirm) {
        if (isConfirm) {
            let formData = new FormData();
            formData.append('idTestigo', idtestigo);

            try {
                const data = await fetchData(BASE_URL_API + '/Testigos/delTestigo', 'POST', formData);
                if (data.status) {
                    swal("Eliminado!", data.msg, "success");
                    tableTestigos.ajax.reload();
                } else {
                    swal("Atención!", data.msg, "error");
                }
            } catch (e) {
                console.error(e);
            }
        }
    });
}


// --- Funciones de Ubicación (Copiadas/Adaptadas de Electores o Monitor) ---
async function fntGetDepartamentos() {
    const data = await fetchData(BASE_URL_API + '/lugares/getDepartamentos');
    if (data.status) {
        let opts = '<option value="">Seleccione...</option>';
        data.data.forEach(d => opts += `<option value="${d.id_department}">${d.name_department}</option>`);
        document.querySelector('#listDpto').innerHTML = opts;
        $('#listDpto').selectpicker('refresh');
    }
}

async function fntGetMunicipios(id) {
    if (!id) return;
    const data = await fetchData(BASE_URL_API + '/lugares/getMunicipios/' + id);
    let sel = document.querySelector('#listMuni');
    if (data.status) {
        let opts = '<option value="">Seleccione...</option>';
        data.data.forEach(d => opts += `<option value="${d.id_municipality}">${d.name_municipality}</option>`);
        sel.innerHTML = opts;
        sel.disabled = false;
    } else {
        sel.innerHTML = '<option value="">Seleccione...</option>';
        sel.disabled = true;
    }
    $('#listMuni').selectpicker('refresh');
}

async function fntGetZonas(id) {
    if (!id) return;
    const data = await fetchData(BASE_URL_API + '/lugares/getZonas/' + id);
    let sel = document.querySelector('#listZona');
    if (data.status) {
        let opts = '<option value="">Seleccione...</option>';
        data.data.forEach(d => opts += `<option value="${d.id_zone}">${d.name_zone}</option>`);
        sel.innerHTML = opts;
        sel.disabled = false;
    } else {
        sel.innerHTML = '<option value="">Seleccione...</option>';
        sel.disabled = true;
    }
    $('#listZona').selectpicker('refresh');
}

async function fntGetPuestos(id) {
    if (!id) return;
    const data = await fetchData(BASE_URL_API + '/lugares/getPuestos/' + id); // OJO: Devuelve puestos con nameplace_place
    // En TestigosModel.php insertamos 'intPuesto'.
    // getPuestos de Lugares Controller devuelve el objeto {id_place, nameplace_place, ...} ?
    // Usualmente devuelve 'nameplace_place' para el select.
    // Si Testigos guarda ID, necesitamos el ID aquí.
    // MONITOR usa nameplace_place como value.
    // ELECTORES usa nameplace_place como value (string) para búsqueda textual.
    /*
    Voy a asumir que `Lugares::getPuestos` retorna el ID y Nombre.
    Si `TestigosModel` espera un INT en puesto_testigo, tengo que mandar el ID_PLACE.
    Aseguremos que este select mande el ID.
    */
    let sel = document.querySelector('#listPuesto');
    if (data.status) {
        let opts = '<option value="">Seleccione...</option>';
        data.data.forEach(d => {
            // Si el Controller devuelve id_place, perfecto.
            // Verificare en ejecución.
            opts += `<option value="${d.id_place}">${d.nameplace_place}</option>`;
        });
        sel.innerHTML = opts;
        sel.disabled = false;
    } else {
        sel.innerHTML = '<option value="">Seleccione...</option>';
        sel.disabled = true;
    }
    $('#listPuesto').selectpicker('refresh');
}
