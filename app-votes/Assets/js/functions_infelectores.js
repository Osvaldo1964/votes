
document.addEventListener('DOMContentLoaded', function () {
    fntLideres();
});

async function fntLideres() {
    // Ahora consume de la API
    if (document.querySelector('#listLideres')) {
        let ajaxUrl = BASE_URL_API + '/infelectores/getLideres';
        try {
            const objData = await fetchData(ajaxUrl);
            var htmlOptions = '<option value="todos">Todos</option>';

            // La API puede devolver array directo como en el anterior, o {status: true, data: []}
            // fetchData devuelve el JSON parseado.
            // Si objData es array, iterar. Si es objeto con .data, iterar .data.
            let dataList = Array.isArray(objData) ? objData : (objData && objData.data ? objData.data : []);

            if (dataList.length > 0) {
                for (var i = 0; i < dataList.length; i++) {
                    htmlOptions += '<option value="' + dataList[i].id_lider + '">' + dataList[i].nombre_lider + '</option>';
                }
            }
            document.querySelector('#listLideres').innerHTML = htmlOptions;

        } catch (error) {
            console.error("Error cargando lideres de API", error);
        }
    }
}

async function fntViewReporte() {
    var lider = document.querySelector('#listLideres').value;
    if (lider == '') {
        swal("Atención", "Seleccione un Líder o Todos para generar el reporte.", "error");
        return false;
    }

    let divReporte = document.querySelector('#divReporte');
    divReporte.innerHTML = '<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><br>Cargando...</div>';

    let ajaxUrl = BASE_URL_API + '/infelectores/getReporte';
    let formData = new FormData();
    formData.append('lider', lider);

    try {
        const objData = await fetchData(ajaxUrl, 'POST', formData);

        var htmlReport = '';

        if (typeof fntGetHeaderReporte === 'function') {
            htmlReport += fntGetHeaderReporte();
        }

        htmlReport += '<h3 class="text-center">Informe de Electores</h3><br>';

        if (objData.length > 0) {
            var currentLider = '';
            var subtotal = 0;
            var grandTotal = 0;
            var count = 1;

            for (var i = 0; i < objData.length; i++) {
                if (objData[i].nombre_lider != currentLider) {
                    if (currentLider != '') {
                        htmlReport += '</tbody></table>';
                        htmlReport += '<div class="text-right"><strong>Subtotal ' + currentLider + ': ' + subtotal + '</strong></div><hr>';
                    }

                    currentLider = objData[i].nombre_lider;
                    subtotal = 0;
                    count = 1;

                    htmlReport += '<h4>Líder: ' + currentLider + '</h4>';
                    htmlReport += '<div class="table-responsive">';
                    htmlReport += '<table class="table table-bordered table-striped table-sm">';
                    htmlReport += '<thead><tr><th>#</th><th>Identificación</th><th>Nombre</th><th>Teléfono</th><th>Dirección</th><th>Dpto</th><th>Muni</th><th>Zona</th><th>Puesto</th><th>Mesa</th></tr></thead>';
                    htmlReport += '<tbody>';
                }

                htmlReport += '<tr>';
                htmlReport += '<td>' + count + '</td>';
                htmlReport += '<td>' + objData[i].ident_elector + '</td>';
                htmlReport += '<td>' + objData[i].nombre_elector + '</td>';
                htmlReport += '<td>' + objData[i].telefono_elector + '</td>';
                htmlReport += '<td>' + (objData[i].direccion_elector || '') + '</td>';
                htmlReport += '<td>' + (objData[i].dpto || '') + '</td>';
                htmlReport += '<td>' + (objData[i].muni || '') + '</td>';
                htmlReport += '<td>' + (objData[i].zona || '') + '</td>';
                htmlReport += '<td>' + (objData[i].puesto || '') + '</td>';
                htmlReport += '<td>' + (objData[i].mesa || '') + '</td>';
                htmlReport += '</tr>';

                subtotal++;
                grandTotal++;
                count++;
            }

            htmlReport += '</tbody></table></div>';
            htmlReport += '<div class="text-right"><strong>Subtotal ' + currentLider + ': ' + subtotal + '</strong></div><hr>';
            htmlReport += '<div class="text-right"><h3>Total General: ' + grandTotal + '</h3></div>';

        } else {
            htmlReport += '<h4 class="text-center">No hay datos para mostrar</h4>';
        }

        divReporte.innerHTML = htmlReport;

    } catch (error) {
        console.error(error);
        divReporte.innerHTML = `<div class="alert alert-danger">Error de conexión con la API.</div>`;
    }
}

function fntImprimir() {
    let divReporte = document.querySelector('#divReporte');
    if (divReporte.innerHTML.trim() == "" || divReporte.innerHTML.includes('fa-spinner')) {
        swal("Atención", "Primero debe generar el reporte para poder imprimir.", "warning");
        return;
    }

    let ventana = window.open('', 'PRINT', 'height=600,width=800');
    ventana.document.write('<html><head><title>Informe de Electores</title>');
    ventana.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
    ventana.document.write('<style>body { font-size: 12px; padding: 20px; } .table { width: 100%; margin-bottom: 1rem; color: #212529; } .table-bordered { border: 1px solid #dee2e6; } .thead-dark th { color: #fff; background-color: #343a40; border-color: #454d55; } hr { border-top: 1px solid #000; } </style>');
    ventana.document.write('</head><body>');
    ventana.document.write(divReporte.innerHTML);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();
    setTimeout(() => {
        ventana.print();
        ventana.close();
    }, 1000);
}

function fntExportExcel() {
    let divReporte = document.querySelector('#divReporte');
    if (divReporte.innerHTML.trim() == "" || divReporte.innerHTML.includes('fa-spinner')) {
        swal("Atención", "Primero debe generar el reporte para poder exportar a Excel.", "warning");
        return;
    }

    let html = divReporte.innerHTML;
    // Eliminar clases de bootstrap que no son necesarias en Excel o que pueden estorbar
    html = html.replace(/<hr[^>]*>/g, '<br>');

    let uri = 'data:application/vnd.ms-excel;base64,';
    let template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><meta http-equiv="content-type" content="text/plain; charset=UTF-8"></head><body><table>{table}</table></body></html>';
    let base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))); };
    let format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }); };

    let ctx = { worksheet: 'Informe de Electores', table: html };
    let link = document.createElement("a");
    link.download = "Informe_Electores.xls";
    link.href = uri + base64(format(template, ctx));
    link.click();
}
