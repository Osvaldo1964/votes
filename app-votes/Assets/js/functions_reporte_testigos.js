
document.addEventListener('DOMContentLoaded', function () {
    fntGetDepartamentos();

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

async function fntGetDepartamentos() {
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getDepartamentos');
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
        if (data && data.status) data.data.forEach(d => options += `<option value="${d.id_place}">${d.nameplace_place}</option>`); // Sending ID
        let sel = document.querySelector('#listPuesto');
        sel.innerHTML = options;
        sel.disabled = false;
        $('.selectpicker').selectpicker('refresh');
    } catch (e) { console.error(e); }
}

async function fntViewReporte() {
    let dpto = document.querySelector('#listDpto').value;
    let muni = document.querySelector('#listMuni').value;
    let zona = document.querySelector('#listZona').value;
    let puesto = document.querySelector('#listPuesto').value;

    if (!dpto || !muni) {
        swal("Atención", "Debe seleccionar al menos Departamento y Municipio.", "warning");
        return;
    }

    // Loading
    document.querySelector('#divReporte').innerHTML = '<div class="text-center p-5"><i class="fa fa-spin fa-spinner fa-3x"></i><br>Generando Reporte...</div>';

    let formData = new FormData();
    formData.append('dpto', dpto);
    formData.append('muni', muni);
    formData.append('zona', zona);
    formData.append('puesto', puesto);

    try {
        const data = await fetchData(BASE_URL_API + '/ReporteTestigos/getReporte', 'POST', formData);

        if (data.status) {
            let html = `
                <div class="row mb-3">
                     <div class="col-12 px-5">
                        ${fntGetHeaderReporte()}
                     </div>
                </div>
                <div class="row mb-3">
                     <div class="col-12 text-center">
                        <h5>Reporte de Testigos Electorales</h5>
                        <p class="text-muted font-italic">${fntGetHeaderReporteTexto()}</p>
                     </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover text-center table-sm" id="tableTestigosReport">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nombre Testigo</th>
                                <th>Teléfono</th>
                                <th>Puesto Asignado</th>
                                <th>Mesas (Seguimiento)</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.data.length > 0) {
                data.data.forEach(row => {
                    let cssClass = row.is_unassigned ? 'table-danger font-weight-bold' : '';
                    let icon = row.is_unassigned ? '<i class="fa fa-exclamation-triangle text-danger"></i> ' : '';

                    html += `
                        <tr class="${cssClass}">
                            <td class="text-left" style="text-transform: capitalize;">${icon}${row.nombre_completo.toLowerCase()}</td>
                            <td>${row.telefono_elector}</td>
                            <td class="text-left">${row.puesto_asignado}</td>
                            <td class="text-left font-weight-bold">${row.mesas_asignadas ? row.mesas_asignadas : '<span class="text-danger">Sin Asignación</span>'}</td>
                        </tr>
                    `;
                });
            } else {
                html += '<tr><td colspan="4">No se encontraron datos para estos filtros.</td></tr>';
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;
            document.querySelector('#divReporte').innerHTML = html;

        } else {
            document.querySelector('#divReporte').innerHTML = `<div class="alert alert-info text-center">${data.msg}</div>`;
        }
    } catch (e) {
        console.error(e);
        swal("Error", "Error al generar el reporte", "error");
    }
}

// Helper para texto de cabecera filtros
function fntGetHeaderReporteTexto() {
    let dptoText = $("#listDpto option:selected").text();
    let muniText = $("#listMuni option:selected").text();
    let zonaText = $("#listZona option:selected").text();
    let puestoText = $("#listPuesto option:selected").text();

    return `${dptoText} / ${muniText} / Zona: ${zonaText} / Puesto: ${puestoText}`;
}

function fntImprimir() {
    let contenido = document.getElementById('divReporte').innerHTML;
    let ventana = window.open('', 'PRINT', 'height=600,width=800');
    ventana.document.write('<html><head><title>Imprimir Reporte Testigos</title>');
    // Incluir estilos básicos de bootstrap y custom para impresión
    ventana.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
    ventana.document.write('<style>body { font-size: 12px; } .table { width: 100%; margin-bottom: 1rem; color: #212529; } .table-bordered { border: 1px solid #dee2e6; } .thead-dark th { color: #fff; background-color: #343a40; border-color: #454d55; } </style>');
    ventana.document.write('</head><body>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    setTimeout(() => { ventana.print(); ventana.close(); }, 1000);
}
