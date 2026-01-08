// functions_resultados.js - Actualizado

// Mapa de Partidos (Hardcodeado basado en Config.json)
const PARTIDOS = {
    1: "PARTIDO LIBERAL",
    2: "PARTIDO CONSERVADOR",
    3: "CENTRO DEMOCRÁTICO",
    4: "PARTIDO VERDE"
};

document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('#listDpto')) {
        fntGetDepartamentos();
    }

    // Listeners para cascada
    document.querySelector('#listDpto').addEventListener('change', function () {
        fntGetMunicipios();
        document.querySelector('#listMesa').innerHTML = '<option value="">Seleccione...</option>';
        document.querySelector('#divResultados').style.display = 'none';

        // Reset labels
        document.querySelector('#lblTotalPotencial').innerHTML = "0";
    });

    document.querySelector('#listMuni').addEventListener('change', function () {
        fntGetZonas();
        document.querySelector('#divResultados').style.display = 'none';
        document.querySelector('#lblTotalPotencial').innerHTML = "0";
    });

    document.querySelector('#listZona').addEventListener('change', function () {
        fntGetPuestos();
        document.querySelector('#divResultados').style.display = 'none';
        document.querySelector('#lblTotalPotencial').innerHTML = "0";
    });

    document.querySelector('#listPuesto').addEventListener('change', function () {
        fntGetMesas();
        document.querySelector('#divResultados').style.display = 'none';
        document.querySelector('#lblTotalPotencial').innerHTML = "0";
    });

    // Listener para boton Cargar Resultados
    if (document.querySelector('#btnCargarResultados')) {
        document.querySelector('#btnCargarResultados').addEventListener('click', function () {
            fntCargarCandidatos();
        });
    }
});

async function fntGetDepartamentos() {
    const selector = document.querySelector('#listDpto');
    try {
        const data = await fetchData(BASE_URL_API + '/lugares/getDepartamentos');
        // fetchData devuelve data parseada
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
        let options = '<option value="">Seleccione...</option>';
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
        let options = '<option value="">Seleccione...</option>';
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

async function fntGetMesas() {
    const idZona = document.querySelector('#listZona').value;
    const nombrePuesto = document.querySelector('#listPuesto').value;
    const selector = document.querySelector('#listMesa');

    if (!idZona || !nombrePuesto) return;

    // Usamos POST porque nombres pueden tener espacios/caracteres
    const formData = new FormData();
    formData.append('idZona', idZona);
    formData.append('nombrePuesto', nombrePuesto);

    try {
        // fetchData para query de mesas
        const data = await fetchData(BASE_URL_API + '/lugares/getMesas', 'POST', formData);

        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            const mesas = Array.isArray(data.data) ? data.data : [data.data];
            mesas.forEach(mesa => {
                // Debug temporal
                // console.log("Mesa raw:", mesa);
                options += `<option value="${mesa.id_mesa}">Mesa ${mesa.nombre_mesa}</option>`;
            });
        }
        selector.innerHTML = options;
        selector.disabled = false;
        $('#listMesa').selectpicker('refresh'); // Si usas bootstrap-select
    } catch (error) {
        console.error("Error cargando mesas", error);
    }
}

async function fntCargarCandidatos() {
    const selectorMesa = document.querySelector('#listMesa');
    const idMesa = selectorMesa.value; // Esto es el MAX(id_place)

    if (!idMesa) {
        swal("Atención", "Por favor seleccione una Mesa de Votación.", "warning");
        return;
    }

    // 1. VERIFICACIÓN PREVIA
    try {
        let formDataCheck = new FormData();
        formDataCheck.append('id_mesa', idMesa);

        const dataCheck = await fetchData(BASE_URL_API + '/resultados/verificarMesa', 'POST', formDataCheck);

        if (dataCheck && !dataCheck.status) {
            swal("Atención", dataCheck.msg, "error");

            document.querySelector('#divResultados').style.display = 'none';
            // Limpiar selección
            selectorMesa.value = "";
            if ($('.selectpicker').length > 0) $('.selectpicker').selectpicker('refresh');
            return;
        }
    } catch (err) {
        console.error("Error verificando mesa", err);
        // Si falla la red, ¿dejamos continuar? Mejor avisar.
        // swal("Error", "No se pudo verificar el estado de la mesa. Revise su conexión.", "error");
        // return; 
        // Por robustez, si falla la verificación (404, 500), mejor prevenimos:
    }

    // 2. Si está libre -> Continuar con carga

    // Para obtener el potencial, necesito el NOMBRE DE LA MESA (lo saco del texto del option)
    const nombreMesaText = selectorMesa.options[selectorMesa.selectedIndex].text; // "Mesa 01"
    const nombreMesa = nombreMesaText.replace('Mesa ', '').trim(); // "01"

    // Cargar Potencial
    const idZona = document.querySelector('#listZona').value;
    const nombrePuesto = document.querySelector('#listPuesto').value;
    fntGetPotencial(idZona, nombrePuesto, nombreMesa);

    // Mostrar sección de resultados
    document.querySelector('#divResultados').style.display = 'block';

    const divResultados = document.querySelector('#divResultados .tile-body');
    divResultados.innerHTML = '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Cargando Candidatos...</div>';

    try {
        const data = await fetchData(BASE_URL_API + '/candidatos/getSelectCandidatos');

        if (data && data.status) {
            renderCandidatosForm(data.data);
        } else {
            divResultados.innerHTML = '<p class="text-danger">No se encontraron candidatos activos.</p>';
        }

    } catch (err) {
        console.error(err);
        divResultados.innerHTML = '<p class="text-danger">Error cargando candidatos.</p>';
    }
}

async function fntGetPotencial(idZona, nombrePuesto, nombreMesa) {
    const formData = new FormData();
    formData.append('idZona', idZona);
    formData.append('nombrePuesto', nombrePuesto);
    formData.append('nombreMesa', nombreMesa);

    const url = BASE_URL_API + '/lugares/getPotencialMesa';
    try {
        const data = await fetchData(url, 'POST', formData);
        if (data && data.status) {
            // data.data debe traer { total: "XX" }
            const total = data.data.total || data.data[0].total; // Protección por si viene array
            document.querySelector('#lblTotalPotencial').innerHTML = total;
        } else {
            document.querySelector('#lblTotalPotencial').innerHTML = "0";
        }
    } catch (error) {
        console.error("Error obteniendo potencial", error);
    }
}

function renderCandidatosForm(candidatos) {
    const divResultados = document.querySelector('#divResultados .tile-body');
    let html = `
    <form id="formVotosE14">
        <input type="hidden" name="id_mesa" value="${document.querySelector('#listMesa').value}">
        
        <div class="form-group row">
            <label class="control-label col-md-3">Número Formulario E-14</label>
            <div class="col-md-9">
                <input class="form-control" type="text" name="numero_formulario" placeholder="Ej: 987654" required>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Candidato</th>
                        <th>Partido</th>
                        <th width="150">Votos</th>
                    </tr>
                </thead>
                <tbody>
    `;

    candidatos.forEach(c => {
        const nombrePartido = PARTIDOS[c.partido_candidato] || "OTRO";
        html += `
            <tr>
                <td>${c.nom1_candidato} ${c.ape1_candidato}</td>
                <td>${nombrePartido}</td>
                <td>
                    <input type="number" class="form-control input-voto" 
                        name="votos[${c.id_candidato}]" 
                        min="0" placeholder="0" required>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
        <div class="row mt-3">
             <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> GUARDAR E-14</button>
             </div>
        </div>
    </form>
    `;

    divResultados.innerHTML = html;

    // Agregar Listener para Guardar
    document.querySelector('#formVotosE14').addEventListener('submit', function (e) {
        e.preventDefault();
        fntGuardarE14(this);
    });
}

async function fntGuardarE14(form) {
    swal({
        title: "Guardar E-14",
        text: "¿Está seguro de registrar estos resultados? Esta acción no se puede deshacer fácilmente.",
        type: "info",
        showCancelButton: true,
        confirmButtonText: "Sí, Guardar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, async function () {
        // Preparar datos
        const formData = new FormData(form);
        const url = BASE_URL_API + '/resultados/setE14';

        try {
            const response = await fetch(url, {
                method: "POST",
                body: formData,
                headers: {
                    // Nota: Si usas FormData, NO pongas Content-Type manualmente, fetch lo hace.
                    // Solo el Authorization
                    'Authorization': `Bearer ${localStorage.getItem('userToken')}`
                }
            });
            const data = await response.json();

            if (data.status) {
                swal("Éxito", data.msg, "success");

                // Ocultar sección de resultados
                document.querySelector('#divResultados').style.display = 'none';

                // Limpiar selector de Mesa para obligar a seleccionar la siguiente
                const selectMesa = document.querySelector('#listMesa');
                selectMesa.value = "";
                // Si usas bootstrap-select o similar, refrescar
                if ($('.selectpicker').length > 0) $('.selectpicker').selectpicker('refresh');

                // El resto de filtros (Puesto, Zona...) se mantienen igual para agilizar.
                // Resetear labels informativos
                document.querySelector('#lblTotalPotencial').innerHTML = "0";

            } else {
                swal("Atención", data.msg, "error");
            }
        } catch (error) {
            console.error(error);
            swal("Error", "Ocurrió un error al guardar.", "error");
        }
    });
}
