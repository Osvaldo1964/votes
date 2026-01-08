// functions_reporteelectoralcenso.js
// Optimizado para uso de funciones globales

let dataConfig = null; // Para guardar dptos y munis cargados inicialmente

document.addEventListener('DOMContentLoaded', async function () {

    // 1. Cargar Dptos y Munis (usamos el endpoint de electores que ya devuelve todo eso optimizado, o uno propio si prefieres)
    // Usaremos el de Electores/getJsons que vi en tu código anterior, parece ser un endpoint de config general.
    await cargarConfiguracionInicial();

    // Eventos de cambios en select

    // DEPARTAMENTO -> MUNICIPIO
    $('#listDpto').on('change', function () {
        filtrarMunicipios(this.value);
        // Resetear siguientes
        resetSelect('#listZona');
        resetSelect('#listPuesto');
        resetSelect('#listMesa');
    });

    // MUNICIPIO -> ZONA
    $('#listMuni').on('change', async function () {
        const idMuni = this.value;
        resetSelect('#listZona');
        resetSelect('#listPuesto');
        resetSelect('#listMesa');

        if (idMuni) {
            const data = await fetchData(`${BASE_URL_API}/ReporteElectoralCenso/getZonas/${idMuni}`);
            if (data && data.status) {
                $('#listZona').prop('disabled', false);
                llenarSelect('#listZona', data.data, 'id_zone', 'name_zone', 'Todas');
            }
        }
    });

    // ZONA -> PUESTO
    $('#listZona').on('change', async function () {
        const idZona = this.value;
        resetSelect('#listPuesto');
        resetSelect('#listMesa');

        if (idZona) {
            const data = await fetchData(`${BASE_URL_API}/ReporteElectoralCenso/getPuestos/${idZona}`);
            if (data && data.status) {
                // Ojo: getPuestos devuelve array de objetos { puesto: "Nombre" }
                // Usamos 'puesto' como ID y como Valor
                $('#listPuesto').prop('disabled', false);
                llenarSelect('#listPuesto', data.data, 'puesto', 'puesto', 'Todos');
            }
        }

    });

    // PUESTO -> MESA
    $('#listPuesto').on('change', async function () {
        const nombrePuesto = this.value;
        const idZona = $('#listZona').val();
        resetSelect('#listMesa');

        if (nombrePuesto && idZona) {
            // Usamos POST porque el nombre del puesto puede tener espacios/caracteres raros
            const formData = { idZona: idZona, nombrePuesto: nombrePuesto };
            // Para enviar como x-www-form-urlencoded al metodo tradicional PHP $_POST
            // o cambiamos el endpoint a recibir JSON.
            // El endpoint getMesas que hice: $idZona = $_POST['idZona'] 
            // Fetch manda body stringify -> php://input. 
            // Corrección: fetchData está configurado para JSON. 
            // El controlador debe leer php://input (ver corrección abajo si es necesario)
            // ASUMIRÉ que el controlador getMesas lee POST form-data o ajustaré fetchData para mandar FormData.

            // Ajustamos fetchData call para mandar msg FormData
            const params = new FormData();
            params.append('idZona', idZona);
            params.append('nombrePuesto', nombrePuesto);

            const data = await fetchData(`${BASE_URL_API}/ReporteElectoralCenso/getMesas`, 'POST', params);

            if (data && data.status) {
                $('#listMesa').prop('disabled', false);
                llenarSelect('#listMesa', data.data, 'mesa', 'mesa', 'Todas');
            }
        }
    });

    // GENERAR REPORTE
    const formReporte = document.querySelector("#formReporte");
    formReporte.onsubmit = async function (e) {
        e.preventDefault();

        const filtros = {
            dpto: $('#listDpto').val(),
            muni: $('#listMuni').val(),
            zona: $('#listZona').val(),
            puesto: $('#listPuesto').val(),
            mesa: $('#listMesa').val(),
            tipoReporte: document.querySelector('input[name="tipoReporte"]:checked').value
        };

        $('#divResultados').hide();
        // Mostrar Loading...

        const res = await fetchData(`${BASE_URL_API}/ReporteElectoralCenso/generarReporte`, 'POST', filtros);

        if (res && res.status) {
            mostrarResultados(res.data, res.resumen);
        } else {
            swal("Error", res ? res.msg : "Error al generar reporte", "error");
        }
    }

});

// FUNCIONES AUXILIARES

async function cargarConfiguracionInicial() {
    // Reutilizamos el endpoint de electores que trae Dptos y Munis
    const res = await fetchData(`${BASE_URL_API}/electores/getJsons`);
    if (res) {
        dataConfig = res;
        // Llenar Dptos
        llenarSelect('#listDpto', res.dptos, 'iddpto', 'namedpto', 'Seleccione...');
        // llenarSelect ya hace refresh, pero dataConfig se usa despues.
        // todo bien.
    }
}

function filtrarMunicipios(idDpto) {
    const listMuni = document.querySelector('#listMuni');
    listMuni.innerHTML = '<option value="">Seleccione...</option>';

    if (idDpto && dataConfig && dataConfig.munis) {
        const filtrados = dataConfig.munis.filter(m => m.dptomuni == idDpto);
        filtrados.forEach(item => {
            const option = document.createElement('option');
            option.value = item.idmuni;
            option.text = item.namemuni;
            listMuni.appendChild(option);
        });
        $('#listMuni').prop('disabled', false);
        $('#listMuni').selectpicker('refresh');
    } else {
        $('#listMuni').prop('disabled', true);
        $('#listMuni').selectpicker('refresh');
    }
}

function resetSelect(selector) {
    const el = document.querySelector(selector);
    const defaultText = selector.includes('Zona') ? 'Todas' : (selector.includes('Mesa') ? 'Todas' : (selector.includes('Puesto') ? 'Todos' : 'Seleccione...'));
    el.innerHTML = `<option value="">${defaultText}</option>`;
    $(selector).prop('disabled', true);
    $(selector).selectpicker('refresh');
}

function llenarSelect(selector, data, keyId, keyVal, defaultOption = null) {
    const el = document.querySelector(selector);
    el.innerHTML = "";
    if (defaultOption) {
        el.innerHTML = `<option value="">${defaultOption}</option>`;
    }
    data.forEach(item => {
        const option = document.createElement('option');
        option.value = item[keyId];
        option.text = item[keyVal];
        el.appendChild(option);
    });
    $(selector).selectpicker('refresh');
}

function mostrarResultados(data, resumen) {
    $('#divResultados').fadeIn();

    // Cards Resumen
    $('#lblTotalPotencial').text(resumen.total_potencial.toLocaleString());
    $('#lblMisVotos').text(resumen.total_mis_votos.toLocaleString());
    $('#lblPorcentaje').text(resumen.porcentaje_global + '%');

    // Tabla
    const tbody = document.querySelector("#tableReporte tbody");
    tbody.innerHTML = "";

    data.forEach(row => {
        const tr = document.createElement("tr");

        // Colorimetría básica para el porcentaje
        let colorClass = "bg-danger";
        if (row.porcentaje > 20) colorClass = "bg-warning";
        if (row.porcentaje > 50) colorClass = "bg-success";

        tr.innerHTML = `
            <td>${row.name_zone}</td>
            <td>${row.puesto}</td>
            <td>${row.mesa}</td>
            <td>${row.potencial}</td>
            <td>${row.mis_votos}</td>
            <td>${row.porcentaje}%</td>
            <td>
                <div class="progress">
                    <div class="progress-bar ${colorClass}" role="progressbar" style="width: ${row.porcentaje}%" aria-valuenow="${row.porcentaje}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}
