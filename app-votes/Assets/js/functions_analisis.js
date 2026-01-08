
document.addEventListener('DOMContentLoaded', function () {
    fntGetDepartamentos();
    fntGetCandidatos(); // Cargar candidatos al inicio

    // Listeners Cascadas Location
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

// Reuse generic location fetchers or duplicate them if strictly decoupled. 
// Assuming functions_infelectores.js pattern (duplication for safety or use global if available).
// I will duplicate strictly necessary ones pointing to API specific endpoints or Generic.

async function fntGetDepartamentos() {
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getDepartamentos');
        // fetchData devuelve data parseada
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) data.data.forEach(d => options += `<option value="${d.id_department}">${d.name_department}</option>`);
        document.querySelector('#listDpto').innerHTML = options;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetMunicipios(id) {
    if (!id) return;
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getMunicipios/' + id);
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) data.data.forEach(d => options += `<option value="${d.id_municipality}">${d.name_municipality}</option>`);
        let sel = document.querySelector('#listMuni');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetZonas(id) {
    if (!id) return;
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getZonas/' + id);
        let options = '<option value="">Seleccione...</option><option value="todas">TODAS</option>';
        if (data && data.status) data.data.forEach(d => options += `<option value="${d.id_zone}">${d.name_zone}</option>`);
        let sel = document.querySelector('#listZona');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetPuestos(id) {
    if (!id) return;
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getPuestos/' + id);
        let options = '<option value="">Seleccione...</option><option value="todos">TODOS</option>';
        if (data && data.status) data.data.forEach(d => options += `<option value="${d.nameplace_place}">${d.nameplace_place}</option>`);
        let sel = document.querySelector('#listPuesto');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntGetCandidatos() {
    try {
        const data = await fetchData(BASE_URL_API + '/analisis/getCandidatos');
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(c => {
                options += `<option value="${c.id_candidato}">${c.nombre}</option>`;
            });
        }
        document.querySelector('#listCandidato').innerHTML = options;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntViewReporte() {
    let dpto = document.querySelector('#listDpto').value;
    let muni = document.querySelector('#listMuni').value;
    let zona = document.querySelector('#listZona').value;
    let puesto = document.querySelector('#listPuesto').value;
    let idCandidato = document.querySelector('#listCandidato').value;

    if (!dpto || !muni || !idCandidato) {
        swal("Atención", "Debe seleccionar Dpto, Municipio y Candidato.", "warning");
        return;
    }

    // Loading
    document.querySelector('#divReporte').innerHTML = '<div class="text-center p-5"><i class="fa fa-spin fa-spinner fa-3x"></i><br>Consultando Especies Fiscales...</div>';

    let formData = new FormData();
    formData.append('dpto', dpto);
    formData.append('muni', muni);
    formData.append('zona', zona);
    formData.append('puesto', puesto);
    formData.append('idCandidato', idCandidato);

    try {
        const data = await fetchData(BASE_URL_API + '/analisis/getReporte', 'POST', formData);

        if (data.status) {
            renderSummary(data.resumen); // Mostrar Boxes

            let html = `
                <div class="row mb-3">
                     <div class="col-12">${fntGetHeaderReporte()}</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Mesa</th>
                                <th>Censo Oficial</th>
                                <th>Potencial (Mío)</th>
                                <th>Votos Reales</th>
                                <th class="bg-warning text-dark">Escrutinio E-14</th>
                                <th>Diferencia</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.data.length > 0) {
                data.data.forEach(row => {
                    // Diferencia Style (Logic Inverted in API: E14 - Reales)
                    let dif = parseInt(row.diferencia);
                    // > 0 success, < 0 danger
                    let classDif = dif > 0 ? 'text-success font-weight-bold' : (dif < 0 ? 'text-danger font-weight-bold' : 'text-primary');

                    html += `
                        <tr>
                            <td>${row.mesa}</td>
                            <td>${row.censo_mesa}</td>
                            <td>${row.mi_potencial}</td>
                            <td>${row.mis_testigos}</td>
                            <td class="table-warning font-weight-bold">${row.votos_e14}</td>
                            <td class="${classDif}">${dif > 0 ? '+' : ''}${dif}</td>
                            <td>${row.estado}</td>
                        </tr>
                    `;
                });

                // Totales
                html += `
                        <tr class="bg-light font-weight-bold">
                            <td>TOTALES</td>
                            <td>${data.resumen.total_censo}</td>
                            <td>${data.resumen.total_potencial}</td>
                            <td>${data.resumen.total_testigos}</td>
                            <td>${data.resumen.total_e14}</td>
                            <td>${data.resumen.total_diferencia}</td>
                            <td>-</td>
                        </tr>
                `;
            } else {
                html += '<tr><td colspan="7">No se encontraron datos para estos filtros.</td></tr>';
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;
            document.querySelector('#divReporte').innerHTML = html;
        } else {
            document.getElementById('divSummaryContainer').style.display = 'none'; // Ocultar si hay error
            document.querySelector('#divReporte').innerHTML = `<div class="alert alert-danger">${data.msg}</div>`;
        }

    } catch (e) {
        console.error(e);
        swal("Error", "Error de servidor", "error");
    }
}

function renderSummary(resumen) {
    if (!resumen) return;

    // Asegurar números
    let tCenso = resumen.total_censo || 0;
    let tPotencial = resumen.total_potencial || 0;
    let tTestigos = resumen.total_testigos || 0;
    let tE14 = resumen.total_e14 || 0;

    // Calcular porcentajes simples para visualización (opcional)
    // % Participación Real = Testigos / Potencial
    let pctPart = 0;
    let potNum = parseInt(tPotencial.toString().replace(/\./g, ''));
    let tesNum = parseInt(tTestigos.toString().replace(/\./g, ''));
    if (potNum > 0) pctPart = ((tesNum / potNum) * 100).toFixed(1);

    let html = `
        <div class="col-md-3">
            <div class="widget-small primary coloured-icon"><i class="icon fa fa-users fa-3x"></i>
                <div class="info">
                    <h4>CENSO</h4>
                    <p><b>${tCenso}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget-small info coloured-icon"><i class="icon fa fa-address-book fa-3x"></i>
                <div class="info">
                    <h4>MI POTENCIAL</h4>
                    <p><b>${tPotencial}</b></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-check-square-o fa-3x"></i>
                <div class="info">
                    <h4>REALES</h4>
                    <p><b>${tTestigos}</b> <small>(${pctPart}%)</small></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="widget-small danger coloured-icon"><i class="icon fa fa-file-text-o fa-3x"></i>
                <div class="info">
                    <h4>E-14</h4>
                    <p><b>${tE14}</b></p>
                </div>
            </div>
        </div>
    `;

    let divSummary = document.getElementById('divSummaryContainer');
    divSummary.innerHTML = html;
    divSummary.style.display = 'flex';
}
