
document.addEventListener('DOMContentLoaded', function () {
    fntLideres();
});

async function fntLideres() {
    // Ahora consume de la API
    // Asumimos que existe constante BASE_URL_API y token en localStorage (como en Infsaldos)
    if (document.querySelector('#listLideres')) {
        let ajaxUrl = BASE_URL_API + '/infelectores/getLideres';
        try {
            const request = await fetch(ajaxUrl, {
                headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` } // Si API requiere autenticación
            });
            const objData = await request.json();

            //var htmlOptions = '<option value="">Seleccione...</option>';
            var htmlOptions = '<option value="todos">Todos</option>';

            // La API puede devolver array directo como en el anterior, o {status: true, data: []}
            // En mi controlador API Infelectores::getLideres hice echo json_encode($arrData), que es array directo de select_all.
            // Asi que objData es el array.

            if (objData.length > 0) {
                for (var i = 0; i < objData.length; i++) {
                    htmlOptions += '<option value="' + objData[i].id_lider + '">' + objData[i].nombre_lider + '</option>';
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
        const request = await fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            // headers: { 'Authorization': `Bearer ${localStorage.getItem('userToken')}` } 
            // Nota: Infsaldos usa token, lo incluyo por si acaso la API tiene middleware de auth global, 
            // aunque en mi controlador API no validé token explícitamente, pero es buena práctica enviarlo.
        });
        const objData = await request.json();

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
                    htmlReport += '<table class="table table-bordered table-striped table-sm">';
                    htmlReport += '<thead><tr><th>#</th><th>Identificación</th><th>Nombre</th><th>Teléfono</th><th>Dirección</th></tr></thead>';
                    htmlReport += '<tbody>';
                }

                htmlReport += '<tr>';
                htmlReport += '<td>' + count + '</td>';
                htmlReport += '<td>' + objData[i].ident_elector + '</td>';
                htmlReport += '<td>' + objData[i].nombre_elector + '</td>';
                htmlReport += '<td>' + objData[i].telefono_elector + '</td>';
                htmlReport += '<td>' + objData[i].direccion_elector + '</td>';
                htmlReport += '</tr>';

                subtotal++;
                grandTotal++;
                count++;
            }

            htmlReport += '</tbody></table>';
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
