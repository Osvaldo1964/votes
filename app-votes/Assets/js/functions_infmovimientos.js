document.addEventListener('DOMContentLoaded', function () {
    fntGetConceptos();

    // Set dates default (First and Last day of current month)
    let date = new Date();
    let firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
    let lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

    document.querySelector('#txtFechaInicio').valueAsDate = firstDay;
    document.querySelector('#txtFechaFin').valueAsDate = lastDay;
});

async function fntGetConceptos() {
    // Reutilizamos el endpoint de Movimientos que ya trae la lista
    // O si prefieres, crea uno especifico en Infmovimientos, pero DRY sugiere reuso.
    try {
        const res = await fetchData(`${BASE_URL_API}/Movimientos/getSelects`);
        if (res && res.conceptos) {
            let options = '<option value="0">TODOS LOS CONCEPTOS</option>';
            res.conceptos.forEach(c => {
                options += `<option value="${c.id_concepto}">${c.nombre_concepto}</option>`;
            });
            document.querySelector('#listConcepto').innerHTML = options;
            $('.selectpicker').selectpicker('refresh');
        }
    } catch (e) {
        console.error("Error cargando conceptos:", e);
    }
}

async function fntViewReporte() {
    let fechaInicio = document.querySelector('#txtFechaInicio').value;
    let fechaFin = document.querySelector('#txtFechaFin').value;
    let concepto = document.querySelector('#listConcepto').value;

    if (!fechaInicio || !fechaFin) {
        swal("Atención", "Seleccione el rango de fechas.", "warning");
        return;
    }

    // Loading simple visual
    let tbody = document.querySelector('#tblReporte');
    tbody.innerHTML = '<tr><td colspan="6"><i class="fa fa-spin fa-spinner"></i> Generando reporte...</td></tr>';

    let formData = new FormData();
    formData.append('fechaInicio', fechaInicio);
    formData.append('fechaFin', fechaFin);
    formData.append('concepto', concepto);

    try {
        let response = await fetch(BASE_URL_API + '/Infmovimientos/getReporte', {
            method: 'POST',
            body: formData,
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        let info = await response.json();

        if (info.status) {
            // Render Table
            let html = '';
            if (info.data && info.data.length > 0) {
                info.data.forEach(row => {
                    html += `
                        <tr>
                            <td>${row.fecha_movimiento}</td>
                            <td>${row.nombre_tercero}</td>
                            <td>${row.nombre_concepto}</td>
                            <td>${row.tipo_label}</td>
                            <td>${row.obs_movimiento || ''}</td>
                            <td>${row.valor_formato}</td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="6">No se encontraron movimientos en este rango.</td></tr>';
            }
            tbody.innerHTML = html;

            // Render Resumen
            let res = info.resumen;
            document.getElementById('lblIngresos').textContent = res.ingresos;
            document.getElementById('lblGastos').textContent = res.gastos;
            document.getElementById('lblBalance').textContent = res.balance;

            // Balance Color
            let balElem = document.getElementById('lblBalance');
            if (res.balance_val > 0) balElem.className = 'text-success';
            else if (res.balance_val < 0) balElem.className = 'text-danger';
            else balElem.className = '';

            document.getElementById('divResumen').style.display = 'flex';

        } else {
            swal("Error", info.msg, "error");
            tbody.innerHTML = '<tr><td colspan="6">Error al cargar datos.</td></tr>';
        }

    } catch (e) {
        console.error(e);
        swal("Error", "Error de conexión", "error");
        tbody.innerHTML = '<tr><td colspan="6">Error de conexión.</td></tr>';
    }
}
