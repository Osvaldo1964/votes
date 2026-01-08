
document.addEventListener('DOMContentLoaded', function () {
    fntGetDepartamentos();
    fntGetCandidatosSelect();

    // Listeners for Cascade Dropdowns
    document.querySelector('#listDpto').addEventListener('change', function () {
        fntGetMunicipios();

        document.querySelector('#listZona').innerHTML = '<option value="">Todas</option>';
        document.querySelector('#listZona').disabled = true;

        document.querySelector('#listPuesto').innerHTML = '<option value="">Todos</option>';
        document.querySelector('#listPuesto').disabled = true;

        $('.selectpicker').selectpicker('refresh');
    });

    document.querySelector('#listMuni').addEventListener('change', function () {
        fntGetZonas();

        document.querySelector('#listPuesto').innerHTML = '<option value="">Todos</option>';
        document.querySelector('#listPuesto').disabled = true;

        $('.selectpicker').selectpicker('refresh');
    });

    document.querySelector('#listZona').addEventListener('change', function () {
        fntGetPuestos();
    });

    // Form Submit
    if (document.querySelector("#formReporte")) {
        let formReporte = document.querySelector("#formReporte");
        formReporte.onsubmit = function (e) {
            e.preventDefault();
            fntGetReporte();
        }
    }

    // Hint update
    document.querySelector('#txtPorcentaje').addEventListener('input', function () {
        let val = this.value || 0;
        document.querySelector('#lblPorcentajeInfo').innerText = "menor al " + val + "%";
    });
});

async function fntGetDepartamentos() {
    const selector = document.querySelector('#listDpto');
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getDepartamentos');
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(dpto => {
                options += `<option value="${dpto.id_department}">${dpto.name_department}</option>`;
            });
        }
        selector.innerHTML = options;
        $('#listDpto').selectpicker('refresh');
    } catch (error) {
        console.error("Error cargando departamentos", error);
    }
}

async function fntGetMunicipios() {
    const idDpto = document.querySelector('#listDpto').value;
    const selector = document.querySelector('#listMuni');
    if (!idDpto) return;

    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getMunicipios/' + idDpto);
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(muni => {
                options += `<option value="${muni.id_municipality}">${muni.name_municipality}</option>`;
            });
        }
        selector.innerHTML = options;
        selector.disabled = false;
        $('#listMuni').selectpicker('refresh');
    } catch (error) {
        console.error("Error cargando municipios", error);
    }
}

async function fntGetZonas() {
    const idMuni = document.querySelector('#listMuni').value;
    const selector = document.querySelector('#listZona');
    if (!idMuni) return;

    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getZonas/' + idMuni);
        let options = '<option value="">Todas</option>';
        if (data && data.status) {
            data.data.forEach(zona => {
                options += `<option value="${zona.id_zone}">${zona.name_zone}</option>`;
            });
        }
        selector.innerHTML = options;
        selector.disabled = false;
        $('#listZona').selectpicker('refresh');
    } catch (error) {
        console.error("Error cargando zonas", error);
    }
}

async function fntGetPuestos() {
    const idZona = document.querySelector('#listZona').value;
    const selector = document.querySelector('#listPuesto');
    if (!idZona) return;

    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getPuestos/' + idZona);
        let options = '<option value="">Todos</option>';
        if (data && data.status) {
            data.data.forEach(puesto => {
                options += `<option value="${puesto.nameplace_place}">${puesto.nameplace_place}</option>`;
            });
        }
        selector.innerHTML = options;
        selector.disabled = false;
        $('#listPuesto').selectpicker('refresh');
    } catch (error) {
        console.error("Error cargando puestos", error);
    }
}

async function fntGetCandidatosSelect() {
    const selector = document.querySelector('#listCandidato');
    try {
        const data = await fetchData(BASE_URL_API + '/candidatos/getSelectCandidatos');
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(c => {
                let nombreCompleto = `${c.nom1_candidato} ${c.ape1_candidato}`;
                // La API getSelectCandidatos devuelve estructura de candidatos table, ajustamos si es necesario
                // Pero AnalisisModel usaba una query custom.
                // Revolvamos a usar un endpoint que devuelve 'nombre'. 
                // Si usamos el generico de candidatos:
                if (c.nombre) nombreCompleto = c.nombre; // Si viene concatenado

                options += `<option value="${c.id_candidato}">${nombreCompleto}</option>`;
            });
        }
        selector.innerHTML = options;
        $('#listCandidato').selectpicker('refresh');
    } catch (error) {
        console.error("Error cargando candidatos", error);
    }
}

async function fntGetReporte() {
    const dpto = document.querySelector('#listDpto').value;
    const muni = document.querySelector('#listMuni').value;
    const zona = document.querySelector('#listZona').value || 'todas';
    const puesto = document.querySelector('#listPuesto').value || 'todos';
    const candidato = document.querySelector('#listCandidato').value;
    const porcentaje = document.querySelector('#txtPorcentaje').value;

    if (!dpto || !muni || !candidato || !porcentaje) {
        swal("Atención", "Todos los campos obligatorios deben ser diligenciados.", "error");
        return;
    }

    const formData = new FormData();
    formData.append('dpto', dpto);
    formData.append('muni', muni);
    formData.append('zona', zona);
    formData.append('puesto', puesto);
    formData.append('candidato', candidato);
    formData.append('porcentaje', porcentaje);

    document.querySelector('#divResultados').style.display = 'block';

    // Loader
    document.querySelector('#divTableReporte').innerHTML = '<div class="text-center"><i class="fa fa-spin fa-spinner fa-3x"></i><br>Consultando...</div>';

    try {
        const data = await fetchData(BASE_URL_API + '/ReporteImpugnaciones/getReporte', 'POST', formData);

        if (data.status) {
            fntViewReporte(data.data);
        } else {
            document.querySelector('#divTableReporte').innerHTML = `<div class="alert alert-info text-center">${data.msg}</div>`;
        }

    } catch (error) {
        console.error(error);
        swal("Error", "Ocurrió un error en la consulta.", "error");
        document.querySelector('#divTableReporte').innerHTML = '';
    }
}

function fntViewReporte(data) {
    // Calculo de totales para footer
    let totalCenso = 0;
    let totalPotencial = 0;
    let totalTestigos = 0;
    let totalE14 = 0;

    let html = `
        <table class="table table-hover table-bordered" id="tableImpugnaciones">
            <thead>
                <tr>
                    <th>Mesa</th>
                    <th>Censo Mesa</th>
                    <th>Mi Potencial</th>
                    <th>Votos E-14</th>
                    <th>Déficit (Votos)</th>
                    <th>Rendimiento (%)</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(row => {
        let potencial = parseInt(row.mi_potencial);
        let e14 = parseInt(row.votos_e14);
        let deficit = potencial - e14;
        let rendimiento = (potencial > 0) ? ((e14 / potencial) * 100).toFixed(1) : 0;

        totalCenso += parseInt(row.censo_mesa);
        totalPotencial += parseInt(row.mi_potencial);
        totalTestigos += parseInt(row.mis_testigos);
        totalE14 += parseInt(row.votos_e14);

        html += `
            <tr>
                <td>${row.mesa}</td>
                <td>${row.censo_mesa}</td>
                <td>${row.mi_potencial}</td>
                <td>${row.votos_e14}</td>
                <td class="text-danger">-${deficit}</td>
                <td>${rendimiento}%</td>
            </tr>
        `;
    });

    html += `
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td>TOTALES</td>
                    <td>${totalCenso}</td>
                    <td>${totalPotencial}</td>
                    <td>${totalE14}</td>
                    <td>-${totalPotencial - totalE14}</td>
                    <td>${(totalPotencial > 0 ? ((totalE14 / totalPotencial) * 100).toFixed(1) : 0)}%</td>
                </tr>
            </tfoot>
        </table>
    `;

    document.querySelector('#divTableReporte').innerHTML = html;
}

function fntImprimir() {
    const contenido = document.getElementById('divTableReporte').innerHTML;
    const candidatoName = document.querySelector('#listCandidato option:checked').text;
    const porcentaje = document.querySelector('#txtPorcentaje').value;

    // Obtener header
    const header = (typeof fntGetHeaderReporte === 'function') ? fntGetHeaderReporte() : '';

    let ventanaPro = window.open('', '', 'height=600,width=800');
    ventanaPro.document.write('<html><head><title>Reporte de Impugnaciones</title>');
    ventanaPro.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
    ventanaPro.document.write('<style>body{ font-family: Arial, sans-serif; } .table { width: 100%; border-collapse: collapse; } .table-bordered th, .table-bordered td { border: 1px solid #dee2e6; padding: 8px; } .text-center { text-align: center; } .text-danger { color: #dc3545 !important; }</style>');
    ventanaPro.document.write('</head><body>');

    ventanaPro.document.write(header);

    ventanaPro.document.write('<div class="container-fluid mt-4">');
    ventanaPro.document.write('<h4 class="text-center">REPORTE DE IMPUGNACIONES</h4>');
    ventanaPro.document.write('<h5 class="text-center">Candidato: ' + candidatoName + '</h5>');
    ventanaPro.document.write('<h6 class="text-center">Criterio: Mesas con < ' + porcentaje + '% de fidelidad del potencial</h6>');
    ventanaPro.document.write('<hr>');
    ventanaPro.document.write(contenido);
    ventanaPro.document.write('</div>');

    ventanaPro.document.write('</body></html>');
    ventanaPro.document.close();
    ventanaPro.focus();
    setTimeout(function () { ventanaPro.print(); }, 1000);
}
