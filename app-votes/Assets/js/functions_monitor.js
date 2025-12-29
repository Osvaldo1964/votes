
// functions_monitor.js - AutoRefresh Version

var refreshInterval; // Variable global para el intervalo

document.addEventListener('DOMContentLoaded', function () {
    fntGetDepartamentos();

    // Listeners Cascadas
    document.querySelector('#listDpto').addEventListener('change', function () {
        fntGetMunicipios(this.value);
        resetSelect('#listMuni');
        resetSelect('#listZona');
        resetSelect('#listPuesto');
    });

    document.querySelector('#listMuni').addEventListener('change', function () {
        fntGetZonas(this.value);
        resetSelect('#listZona');
        resetSelect('#listPuesto');
    });

    document.querySelector('#listZona').addEventListener('change', function () {
        fntGetPuestos(this.value);
        resetSelect('#listPuesto');
    });
});

function resetSelect(selectorID) {
    let select = document.querySelector(selectorID);
    select.innerHTML = '<option value="">Seleccione...</option>';
    select.disabled = true;
    if ($('.selectpicker').length > 0) $('.selectpicker').selectpicker('refresh');
}

// ----- Control de Intervalo -----

function fntSetRefresh() {
    let time = document.getElementById('listRefresh').value;

    // Limpiar intervalo existe
    if (refreshInterval) clearInterval(refreshInterval);

    if (time > 0) {
        // Validar que ya haya filtros seleccionados
        let idZona = document.querySelector('#listZona').value;
        let puesto = document.querySelector('#listPuesto').value;

        if (!idZona || !puesto) {
            swal("Atención", "Primero seleccione un puesto para activar el monitoreo.", "warning");
            document.getElementById('listRefresh').value = "0";
            return;
        }

        refreshInterval = setInterval(function () {
            console.log("Auto-Refrescando...");
            fntMonitorShow(true); // Silent Mode = true
        }, parseInt(time));

        // Feedback visual sutil (toast o log)
        console.log("Intervalo activado: " + time + "ms");
    }
}

// ----- AJAX Funciones de Carga (GET) -----

async function fntGetDepartamentos() {
    try {
        const response = await fetch(BASE_URL_API + '/lugares/getDepartamentos', {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const data = await response.json();
        let options = '<option value="">Seleccione...</option>';
        if (data.status) {
            data.data.forEach(dpto => {
                options += `<option value="${dpto.id_department}">${dpto.name_department}</option>`;
            });
        }
        document.querySelector('#listDpto').innerHTML = options;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetMunicipios(idDpto) {
    if (!idDpto) return;
    try {
        const response = await fetch(BASE_URL_API + '/lugares/getMunicipios/' + idDpto, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const data = await response.json();
        let options = '<option value="">Seleccione...</option>';
        if (data.status) {
            data.data.forEach(muni => {
                options += `<option value="${muni.id_municipality}">${muni.name_municipality}</option>`;
            });
        }
        let sel = document.querySelector('#listMuni');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetZonas(idMuni) {
    if (!idMuni) return;
    try {
        const response = await fetch(BASE_URL_API + '/lugares/getZonas/' + idMuni, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const data = await response.json();
        let options = '<option value="">Seleccione...</option>';
        if (data.status) {
            data.data.forEach(zona => {
                options += `<option value="${zona.id_zone}">${zona.name_zone}</option>`;
            });
        }
        let sel = document.querySelector('#listZona');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetPuestos(idZona) {
    if (!idZona) return;
    try {
        const response = await fetch(BASE_URL_API + '/lugares/getPuestos/' + idZona, {
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const data = await response.json();
        let options = '<option value="">Seleccione...</option>';
        if (data.status) {
            data.data.forEach(puesto => {
                options += `<option value="${puesto.nameplace_place}">${puesto.nameplace_place}</option>`;
            });
        }
        let sel = document.querySelector('#listPuesto');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

// ----- FUNCIÓN PRINCIPAL: MOSTRAR DATOS MONITOR -----

async function fntMonitorShow(silent = false) {
    let idZona = document.querySelector('#listZona').value;
    let puesto = document.querySelector('#listPuesto').value;

    if (!idZona || !puesto) {
        if (!silent) swal("Atención", "Seleccione todos los filtros hasta Puesto.", "warning");
        return;
    }

    let container = document.getElementById('divMesasContainer');

    // Si NO es silencioso (click manual), mostramos Loading
    if (!silent) {
        container.style.display = ''; // Limpiar inline
        container.classList.add('loading');
        container.innerHTML = '<div class="col-12 text-center py-5"><i class="fa fa-spin fa-spinner fa-3x"></i><br>Cargando datos en tiempo real...</div>';
    }

    try {
        let formData = new FormData();
        formData.append('idZona', idZona);
        formData.append('puesto', puesto);

        const response = await fetch(BASE_URL_API + '/monitor/getStats', {
            method: 'POST',
            body: formData,
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const data = await response.json();

        if (data.status) {
            // Quitamos loading (si estaba)
            container.classList.remove('loading');
            // Activamos grid en display (por si acaso estaba hidden)
            // container.style.display = 'grid' !important (CSS)

            renderCards(data.data);
            renderSummary(data.data); // Nuevo: Renderizar Totales

            // Si fue silencioso, quizás un pequeño indicador en consola o UI?
            if (silent) {
                // Opcional: poner un "Actualizado: HH:mm:ss" en algún lado
            }

        } else {
            if (!silent) {
                container.classList.add('loading');
                container.innerHTML = `<div class="col-12 text-center text-muted"><h4>${data.msg}</h4></div>`;
                document.getElementById('divSummaryContainer').style.display = 'none';
            }
        }
    } catch (error) {
        console.error(error);
        if (!silent) swal("Error", "Error obteniendo datos.", "error");
    }
}

function renderSummary(mesas) {
    if (!Array.isArray(mesas)) return;

    let totalCenso = 0;
    let totalMios = 0;
    let totalVotaron = 0;

    mesas.forEach(m => {
        totalCenso += parseInt(m.potencial || 0);
        totalMios += parseInt(m.mios || 0);
        totalVotaron += parseInt(m.votaron || 0);
    });

    let pctGlobal = 0;
    if (totalMios > 0) pctGlobal = ((totalVotaron / totalMios) * 100).toFixed(1);

    let html = `
        <div class="col-md-4">
            <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                    <h4>CENSO PUESTO</h4>
                    <p><b>${totalCenso.toLocaleString()}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="widget-small info coloured-icon"><i class="icon fa fa-address-book fa-3x"></i>
                <div class="info">
                    <h4>MI POTENCIAL</h4>
                    <p><b>${totalMios.toLocaleString()}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-check-square-o fa-3x"></i>
                <div class="info">
                    <h4>VOTOS REALES (${pctGlobal}%)</h4>
                    <p><b>${totalVotaron.toLocaleString()}</b></p>
                </div>
            </div>
        </div>
    `;

    let divSummary = document.getElementById('divSummaryContainer');
    divSummary.innerHTML = html;
    divSummary.style.display = 'flex'; // Mostrar row
}

function renderCards(mesas) {
    if (!Array.isArray(mesas)) return;

    let html = '';
    mesas.forEach(mesa => {
        let pot = parseInt(mesa.potencial);
        let mios = parseInt(mesa.mios);
        let votaron = parseInt(mesa.votaron || 0);

        let pct = 0;
        if (mios > 0) pct = Math.round((votaron / mios) * 100);

        let badgeColor = "secondary";
        if (pct < 30) badgeColor = "danger";
        else if (pct < 70) badgeColor = "warning";
        else badgeColor = "success";

        html += `
        <div class="mb-3" style="width: 100%; min-width: 0;">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-1 px-3">
                    <h6 class="mb-0">MESA ${mesa.mesa}</h6>
                    <span class="badge badge-${badgeColor}">${pct}%</span>
                </div>
                <div class="card-body p-2">
                    <div class="row text-center h-100 align-items-center">
                        <div class="col-4 border-right px-1">
                            <span class="d-block text-muted" style="font-size:10px;">CENSO</span>
                            <b class="text-secondary h6">${pot}</b>
                        </div>
                        <div class="col-4 border-right px-1">
                            <span class="d-block text-muted" style="font-size:10px;">PROPIOS</span>
                            <b class="text-primary h6">${mios}</b>
                        </div>
                        <div class="col-4 px-1">
                            <span class="d-block text-muted" style="font-size:10px;">VOTARON</span>
                            <b class="text-success h6">${votaron}</b>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-0 border-0 bg-white">
                    <div class="progress" style="height: 5px; border-radius: 0 0 4px 4px;">
                        <div class="progress-bar bg-${badgeColor}" role="progressbar" style="width: ${pct}%" aria-valuenow="${pct}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        `;
    });
    document.getElementById('divMesasContainer').innerHTML = html;
}
