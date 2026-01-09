document.addEventListener('DOMContentLoaded', function () {
    const formReporte = document.querySelector("#formReporteAgenda");
    if (formReporte) {
        formReporte.onsubmit = async function (e) {
            e.preventDefault();

            let fechaInicio = document.querySelector('#txtFechaInicio').value;
            let fechaFin = document.querySelector('#txtFechaFin').value;

            if (fechaInicio == '' || fechaFin == '') {
                swal("Atención", "Todos los campos son obligatorios.", "error");
                return false;
            }

            // Validar que inicio no sea mayor que fin
            if (fechaInicio > fechaFin) {
                swal("Atención", "La fecha de inicio no puede ser mayor a la fecha final.", "warning");
                return false;
            }

            let formData = new FormData();
            formData.append('fechaInicio', fechaInicio);
            formData.append('fechaFin', fechaFin);

            // Mostrar loading
            $('#divResultados').hide();
            swal({
                title: "Procesando...",
                text: "Generando reporte de agenda",
                icon: "info",
                buttons: false,
                closeOnClickOutside: false,
                closeOnEsc: false
            });

            try {
                const response = await fetchData(`${BASE_URL_API}/Agenda/getAgendaReport`, 'POST', formData);
                swal.close();

                if (response.status) {
                    mostrarResultadosAgenda(response.data);
                } else {
                    swal("Atención", response.msg, "warning");
                }
            } catch (error) {
                swal.close();
                console.error(error);
                swal("Error", "Error al conectar con el servidor", "error");
            }
        }
    }
});

function mostrarResultadosAgenda(data) {
    const tbody = document.querySelector("#tableAgendaReporte tbody");
    tbody.innerHTML = "";

    data.forEach(evento => {
        let tr = document.createElement("tr");

        // Formato fechas
        let start = moment(evento.start).format('DD/MM/YYYY HH:mm');
        let end = evento.end ? moment(evento.end).format('DD/MM/YYYY HH:mm') : '';

        tr.innerHTML = `
            <td>${start}</td>
            <td>${end}</td>
            <td style="font-weight: bold; color: ${evento.color || '#000'}">${evento.title}</td>
            <td>${evento.description || ''}</td>
        `;

        tbody.appendChild(tr);
    });

    $('#divResultados').fadeIn();
}

function fntImprimirReporte() {
    let contenidoOriginal = document.getElementById('printableArea').innerHTML;
    let header = fntGetHeaderReporte(); // Función global de main.js
    
    // Obtenemos fechas
    let fInicio = document.querySelector('#txtFechaInicio').value;
    let fFin = document.querySelector('#txtFechaFin').value;
    let subHeader = `<div class="text-center mb-3">
                        <h5>Reporte de Agenda</h5>
                        <p class="text-muted">Desde: ${moment(fInicio).format('DD/MM/YYYY')} Hasta: ${moment(fFin).format('DD/MM/YYYY')}</p>
                     </div>`;

    let contenido = header + subHeader + contenidoOriginal;

    let ventana = window.open('', 'PRINT', 'height=600,width=800');
    ventana.document.write('<html><head><title>Imprimir Agenda</title>');
    // Incluir Bootstrap 
    ventana.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
    ventana.document.write('<style>');
    ventana.document.write('body { font-size: 12px; font-family: Arial, sans-serif; }');
    ventana.document.write('.table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; color: #212529; }');
    ventana.document.write('.table th, .table td { padding: 0.75rem; vertical-align: top; border-top: 1px solid #dee2e6; }');
    ventana.document.write('.table-bordered { border: 1px solid #dee2e6; }');
    ventana.document.write('.table-bordered th, .table-bordered td { border: 1px solid #dee2e6; }');
    ventana.document.write('@media print { @page { margin: 1cm; } body { -webkit-print-color-adjust: exact; } }');
    ventana.document.write('</style>');
    ventana.document.write('</head><body>');
    ventana.document.write('<div class="container-fluid p-4">');
    ventana.document.write(contenido);
    ventana.document.write('</div>');
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.focus();

    // Esperar a que cargue css
    setTimeout(() => { 
        ventana.print(); 
        ventana.close(); 
    }, 1000);
}
