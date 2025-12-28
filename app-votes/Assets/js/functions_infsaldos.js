
document.addEventListener('DOMContentLoaded', function () {
    fntCargarElementos();
});

async function fntCargarElementos() {
    if (document.querySelector('#listElementos')) {
        let ajaxUrl = BASE_URL_API + '/elementos/getSelectElementos'; // Usamos endpoint existente de elementos
        try {
            const request = await fetch(ajaxUrl, {
                headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
            });
            const data = await request.json();
            if (data.status) {
                let options = '<option value="0">Todos los Elementos (Resumen General)</option>';
                data.data.forEach(element => {
                    options += `<option value="${element.id_elemento}">${element.nombre_elemento}</option>`;
                });
                document.querySelector('#listElementos').innerHTML = options;
                $('#listElementos').selectpicker('render');
            }
        } catch (error) {
            console.error("Error cargando elementos", error);
        }
    }
}

async function fntGenerarReporte() {
    let idElemento = document.querySelector('#listElementos').value;
    // Obtener texto del elemento seleccionado
    let select = document.querySelector('#listElementos');
    let nombreElemento = select.options[select.selectedIndex].text;

    let divResultados = document.querySelector('#divResultados');

    divResultados.innerHTML = '<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Cargando...</div>';

    let formData = new FormData();
    formData.append('idElemento', idElemento);

    try {
        let url = BASE_URL_API + '/infsaldos/getReporte';
        const request = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` }
        });
        const objData = await request.json();

        if (objData.status) {
            let html = fntGetHeaderReporte(); // Usar Helper Global

            if (objData.tipo == 'general') {
                // TABLA GENERAL
                html += `
                <h3 class="text-center">Resumen General de Saldos</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Elemento</th>
                                <th class="text-right">Saldo Cantidad</th>
                                <th class="text-right">Precio Promedio</th>
                                <th class="text-right">Saldo Total ($)</th>
                            </tr>
                        </thead>
                        <tbody>`;

                let granTotal = 0;
                objData.data.forEach(item => {
                    granTotal += parseFloat(item.saldo_pesos);
                    html += `
                        <tr>
                            <td>${item.nombre_elemento}</td>
                            <td class="text-right">${item.saldo_cantidad}</td>
                            <td class="text-right">$ ${parseFloat(item.precio_promedio).toLocaleString('es-CO', { minimumFractionDigits: 2 })}</td>
                            <td class="text-right">$ ${parseFloat(item.saldo_pesos).toLocaleString('es-CO', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    `;
                });

                html += `
                        <tr class="table-info font-weight-bold">
                            <td colspan="3" class="text-right">TOTAL INVENTARIO VALORIZADO:</td>
                            <td class="text-right">$ ${granTotal.toLocaleString('es-CO', { minimumFractionDigits: 2 })}</td>
                        </tr>
                    </tbody>
                </table>
                </div>`;
            } else {
                // TABLA DETALLADA
                html += `
                <h3 class="text-center">Kardex Detallado</h3>
                <h5 class="text-center text-muted">${nombreElemento}</h5>
                <br>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Detalle (Tercero/Líder)</th>
                                <th class="text-right">Entrada</th>
                                <th class="text-right">Salida</th>
                                <th class="text-right">Precio Unit. (Entrada)</th>
                                <th class="text-right">Total ($)</th>
                            </tr>
                        </thead>
                        <tbody>`;

                let saldoCant = 0;
                objData.data.forEach(mov => {
                    let entrada = (mov.tipo == 'ENTRADA') ? mov.cantidad : 0;
                    let salida = (mov.tipo == 'SALIDA') ? mov.cantidad : 0;
                    let claseFila = (mov.tipo == 'ENTRADA') ? '' : 'text-danger';

                    html += `
                        <tr class="${claseFila}">
                            <td>${mov.fecha}</td>
                            <td>${mov.tipo}</td>
                            <td>${mov.detalle}</td>
                            <td class="text-right font-weight-bold">${entrada > 0 ? entrada : '-'}</td>
                            <td class="text-right font-weight-bold">${salida > 0 ? salida : '-'}</td>
                            <td class="text-right">${mov.precio > 0 ? '$ ' + parseFloat(mov.precio).toLocaleString('es-CO') : '-'}</td>
                            <td class="text-right">${mov.total > 0 ? '$ ' + parseFloat(mov.total).toLocaleString('es-CO') : '-'}</td>
                        </tr>
                    `;
                });

                // Resumen final
                let res = objData.resumen;
                html += `
                    </tbody>
                    <tfoot class="table-active">
                        <tr>
                            <th colspan="3" class="text-right">RESUMEN FINAL:</th>
                            <th class="text-right text-success">Saldo: ${res.saldo_cantidad}</th>
                            <th></th>
                            <th class="text-right">P. Prom: $ ${parseFloat(res.precio_promedio).toLocaleString('es-CO', { minimumFractionDigits: 2 })}</th>
                            <th class="text-right">Total: $ ${parseFloat(res.saldo_pesos).toLocaleString('es-CO', { minimumFractionDigits: 2 })}</th>
                        </tr>
                    </tfoot>
                </table>
                </div>`;
            }

            divResultados.innerHTML = html;

        } else {
            divResultados.innerHTML = `<div class="alert alert-danger">${objData.msg || 'Error desconocido'}</div>`;
        }

    } catch (error) {
        console.error(error);
        divResultados.innerHTML = `<div class="alert alert-danger">Error de conexión con el servidor.</div>`;
    }
}

function fntImprimir() {
    let contenido = document.getElementById('divResultados').innerHTML;
    let ventana = window.open('', 'PRINT', 'height=600,width=800');
    ventana.document.write('<html><head><title>Imprimir Reporte</title>');
    // Incluir estilos básicos de bootstrap para impresión
    ventana.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
    ventana.document.write('</head><body>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    setTimeout(() => { ventana.print(); ventana.close(); }, 1000);
}
