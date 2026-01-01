// functions_electores.js
// Optimizado - Utiliza fetchData y lenguajeEspanol globales (functions_admin.js)

let dataConfig = null;
let tableElectores;

// 2. INICIO DEL DOCUMENTO
document.addEventListener('DOMContentLoaded', async function () {
    // 1. CARGAR CONFIGURACIÓN PRIMERO
    await cargarJson();

    // 2. INICIALIZAR TABLA
    tableElectores = $('#tableElectores').DataTable({
        "processing": true,
        "language": lenguajeEspanol, // Variable global
        "ajax": {
            "url": `${BASE_URL_API}/electores/getElectores`,
            "type": "GET",
            "headers": { "Authorization": `Bearer ${localStorage.getItem('userToken')}` },
            "data": d => { d.rolUser = localStorage.getItem('userRol'); },
            // FORZAMOS que siempre devuelva un array, incluso si el servidor falla
            "dataSrc": json => (json && json.status && Array.isArray(json.data)) ? json.data : [],
            "error": function (xhr) {
                console.error("Error en AJAX:", xhr);
            }
        },

        "columns": [
            { "data": "id_elector" },
            { "data": "ident_elector" },
            { "render": (d, t, row) => `${row.nom1_elector} ${row.nom2_elector || ""}` },
            { "render": (d, t, row) => `${row.ape1_elector} ${row.ape2_elector || ""}` },
            { "data": "telefono_elector" },
            { "data": "email_elector" },
            { "data": "dpto_elector", "render": d => getNombreById('dptos', d) },
            { "data": "muni_elector", "render": d => getNombreById('munis', d) },
            { "data": "estado_elector" },
            // COLUMNA CRÍTICA: Definida para que nunca sea undefined
            {
                "data": "options",
                "defaultContent": "",
                "orderable": false
            }
        ],
        "columnDefs": [
            { "defaultContent": "-", "targets": "_all" } // Si falta cualquier dato, pone un guion
        ],
        "responsive": true,
        "destroy": true
    });

    let inputIdent = document.querySelector("#ident_elector");
    inputIdent.addEventListener('blur', async function () {
        let ident = this.value;
        if (ident.length > 5) { // Validación básica de longitud
            try {
                // Ahora apuntamos al controlador Electores que tiene la validación de duplicados
                let data = await fetchData(BASE_URL_API + '/Electores/getValidaElector/' + ident);


                if (data && data.status) {
                    // VERIFICACIÓN DE DUPLICADOS
                    if (data.is_registered) {
                        inputIdent.classList.remove("is-valid");
                        // inputIdent.classList.add("is-invalid"); // REMOVIDO: Evita marcar rojo antes de tiempo

                        // Limpiar campos visuales
                        document.querySelector("#txtZona").value = "";
                        document.querySelector("#txtPuesto").value = "";
                        document.querySelector("#txtMesa").value = "";
                        document.querySelector("#ape1_elector").value = "";
                        document.querySelector("#ape2_elector").value = "";
                        document.querySelector("#nom1_elector").value = "";
                        document.querySelector("#nom2_elector").value = "";

                        swal({
                            title: "Atención",
                            text: "El elector ya se encuentra registrado.",
                            type: "warning",
                            confirmButtonText: "Aceptar"
                        }, function () {
                            // Al aceptar, limpiamos el campo y quitamos cualquier rastro de error visual
                            inputIdent.value = "";
                            inputIdent.classList.remove("is-invalid", "is-valid");

                            // NUEVO: Limpiar validaciones de los otros campos también
                            const camposLimpiar = ["#ape1_elector", "#ape2_elector", "#nom1_elector", "#nom2_elector", "#email_elector"];
                            camposLimpiar.forEach(id => {
                                let el = document.querySelector(id);
                                if (el) {
                                    el.value = "";
                                    el.classList.remove("is-invalid", "is-valid");
                                }
                            });

                            // Opcional: enfocar de nuevo
                            setTimeout(() => inputIdent.focus(), 200);
                        });
                        return; // Detener ejecución
                    }

                    // SÍ existe en places y NO está registrado
                    inputIdent.classList.remove("is-invalid");
                    inputIdent.classList.add("is-valid");

                    // Poblamos los campos desactivados
                    document.querySelector("#txtZona").value = data.data.name_zone;
                    document.querySelector("#txtPuesto").value = data.data.nameplace_place;
                    document.querySelector("#txtMesa").value = data.data.mesa_place;

                    // AUTO-COMPLETAR NOMBRES Y APELLIDOS
                    document.querySelector("#ape1_elector").value = data.data.ape1_place || "";
                    document.querySelector("#ape2_elector").value = data.data.ape2_place || "";
                    document.querySelector("#nom1_elector").value = data.data.nom1_place || "";
                    document.querySelector("#nom2_elector").value = data.data.nom2_place || "";

                    // BLOQUEAR EDICIÓN DE NOMBRES Y MARCAR COMO VÁLIDOS
                    const camposNombres = ["#ape1_elector", "#ape2_elector", "#nom1_elector", "#nom2_elector"];
                    camposNombres.forEach(id => {
                        let el = document.querySelector(id);
                        el.readOnly = true;
                        el.classList.remove("is-invalid"); // Quita rojo
                        el.classList.add("is-valid");    // Pone verde (opcional, o déjalo neutro)
                    });

                    // SALTAR FOCO A TELÉFONO
                    document.querySelector("#telefono_elector").focus();

                } else {
                    // NO existe en places
                    inputIdent.classList.remove("is-valid");
                    inputIdent.classList.add("is-invalid");

                    // Limpiamos los campos visuales
                    document.querySelector("#txtZona").value = "";
                    document.querySelector("#txtPuesto").value = "";
                    document.querySelector("#txtMesa").value = "";

                    document.querySelector("#ape1_elector").value = "";
                    document.querySelector("#ape2_elector").value = "";
                    document.querySelector("#nom1_elector").value = "";
                    document.querySelector("#nom2_elector").value = "";

                    // Opcional: Limpiar el campo o mostrar alerta
                    swal({
                        title: "Atención",
                        text: data.msg,
                        type: "warning",
                        confirmButtonText: "Aceptar"
                    }, function () {
                        inputIdent.value = "";
                        setTimeout(function () {
                            // DESBLOQUEAR EDICIÓN POR SI ACASO Y LIMPIAR CLASES
                            const camposNombres = ["#ape1_elector", "#ape2_elector", "#nom1_elector", "#nom2_elector"];
                            camposNombres.forEach(id => {
                                let el = document.querySelector(id);
                                el.readOnly = false;
                                el.classList.remove("is-valid", "is-invalid");
                            });
                            inputIdent.focus();
                        }, 200);
                    });
                }
            } catch (error) {
                console.error("Error validando elector:", error);
            }
        }
    });

    const formElector = document.querySelector("#formElector");
    if (formElector) {
        formElector.onsubmit = async function (e) {
            e.preventDefault();

            // 1. Validaciones mínimas
            const strCedula = document.querySelector('#ident_elector').value;
            const strNombre = document.querySelector('#nom1_elector').value;
            const intMuni = document.querySelector('#muni_elector').value;

            if (strCedula == "" || strNombre == "" || intMuni == "") {
                swal("Atención", "Todos los campos con (*) son obligatorios.", "error");
                return false;
            }

            // 2. Captura de datos (FormData detectará todos los 'name' del HTML)
            const formData = new FormData(formElector);

            // 3. Envío al controlador corregido
            const objData = await fetchData(`${BASE_URL_API}/electores/setElector`, 'POST', formData);

            if (objData?.status) {
                // Si es un registro NUEVO (id_elector vacío), mantenemos modal abierto para seguir registrando
                // Si es EDICIÓN (id_elector tiene valor), cerramos modal
                let isNew = document.querySelector("#id_elector").value == "";
                let currentLider = document.querySelector("#lider_elector").value;

                if (isNew) {
                    // Modo "Creación Masiva"
                    formElector.reset();
                    document.querySelector("#lider_elector").value = currentLider; // Restaurar líder
                    $('.selectpicker').selectpicker('refresh'); // Refrescar select visual

                    // Limpiar clases de validación y estados
                    document.querySelectorAll(".form-control").forEach(i => {
                        i.classList.remove("is-valid", "is-invalid");
                        if (i.id.includes("ape") || i.id.includes("nom")) i.readOnly = false; // Desbloquear nombres
                    });

                    // Asegurar que el ID esté limpio
                    document.querySelector("#id_elector").value = "";

                    swal({
                        title: "Guardado",
                        text: "Elector guardado. Puede registrar el siguiente.",
                        type: "success",
                        confirmButtonText: "Continuar"
                    }, function () {
                        // Al cerrar el alert, ponemos el foco
                        setTimeout(() => {
                            document.querySelector("#ident_elector").focus();
                        }, 200);
                    });
                } else {
                    // Modo Edición Normal
                    $('#modalFormElector').modal("hide");
                    formElector.reset();
                    swal("Electores", objData.msg, "success");
                }
                tableElectores.ajax.reload();
            } else if (objData) {
                // Error controlado (ej: candidato ya existe)
                swal("Error", objData.msg, "error");
            } else {
                // Error de servidor (lo que veíamos antes como <br><b>)
                swal("Error", "Error interno del servidor al procesar la solicitud", "error");
            }
        };
    }

    // Evento para el cambio de departamento
    $('#dpto_elector').on('change', function () {
        filtrarMunicipios(this.value);
    });

    // Eventos de botones
    document.addEventListener('click', function (e) {
        const target = e.target.closest('.btnView, .btnEdit, .btnDel, #btnNuevoElector');
        if (!target) return;
        const id = target.getAttribute('can');
        if (target.id === 'btnNuevoElector') openModal();
        if (target.classList.contains('btnView')) fntViewElector(id);
        if (target.classList.contains('btnEdit')) fntEditElector(id);
        if (target.classList.contains('btnDel')) fntDelElector(id);
    });
});

// 3. FUNCIONES LÓGICAS
async function cargarJson() {
    const res = await fetchData(`${BASE_URL_API}/electores/getJsons`);
    if (res) {
        dataConfig = res;
        const llenarSelect = (selectorId, datos, llaveId, llaveNombre, llaveNombre2 = null) => {
            const el = document.querySelector(selectorId);
            if (el && datos) {
                el.length = 1;
                datos.forEach(item => {
                    let nombre = item[llaveNombre];
                    if (llaveNombre2) nombre += " " + item[llaveNombre2];
                    el.add(new Option(nombre, item[llaveId]))
                });
            }
        };
        llenarSelect('#dpto_elector', res.dptos, 'iddpto', 'namedpto');
        $('.selectpicker').selectpicker('refresh');
    }

    // 2. CARGAR LÍDERES (Endpoint dedicado)
    try {
        const resLideres = await fetchData(`${BASE_URL_API}/Lideres/getSelectLideres`);
        if (resLideres && resLideres.status) {
            const agLideres = document.querySelector("#lider_elector");
            if (agLideres) {
                agLideres.length = 1;
                resLideres.data.forEach(l => {
                    agLideres.add(new Option(`${l.nom1_lider} ${l.ape1_lider}`, l.id_lider));
                });
                $('.selectpicker').selectpicker('refresh');
            }
        }
    } catch (e) {
        console.error("Error cargando líderes:", e);
    }
}

function filtrarMunicipios(idDpto, idMuniSeleccionado = null) {
    if (!dataConfig || !dataConfig.munis) return;
    const listMuni = document.querySelector('#muni_elector');
    listMuni.length = 1;
    if (idDpto) {
        const filtrados = dataConfig.munis.filter(m => m.dptomuni == idDpto);
        filtrados.forEach(item => listMuni.add(new Option(item.namemuni, item.idmuni)));
    }
    if (idMuniSeleccionado) $(listMuni).val(idMuniSeleccionado);
    $(listMuni).selectpicker('refresh');
}

function getNombreById(tipo, id) {
    if (!dataConfig || !dataConfig[tipo]) return id;
    const campos = {
        'curules': { id: 'id', nombre: 'nombre' },
        'partidos': { id: 'id', nombre: 'nombre' },
        'dptos': { id: 'iddpto', nombre: 'namedpto' },
        'munis': { id: 'idmuni', nombre: 'namemuni' }
    };
    const config = campos[tipo];
    const item = dataConfig[tipo].find(x => x[config.id] == id);
    return item ? item[config.nombre] : id;
}

// 4. ACCIONES MODAL
function openModal(isEdit = false, data = null) {
    const form = document.querySelector("#formElector");
    form.reset();
    $('#id_elector').val("");

    if (isEdit && data) {
        $('#titleModal').html("Actualizar Elector");
        $('#id_elector').val(data.id_elector);
        $('#ident_elector').val(data.ident_elector);
        $('#ape1_elector').val(data.ape1_elector);
        $('#ape2_elector').val(data.ape2_elector);
        $('#nom1_elector').val(data.nom1_elector);
        $('#nom2_elector').val(data.nom2_elector);

        // Bloquear campos de nombres en edición
        document.querySelector("#ape1_elector").readOnly = true;
        document.querySelector("#ape2_elector").readOnly = true;
        document.querySelector("#nom1_elector").readOnly = true;
        document.querySelector("#nom2_elector").readOnly = true;

        $('#telefono_elector').val(data.telefono_elector);
        $('#email_elector').val(data.email_elector);
        $('#direccion_elector').val(data.direccion_elector);

        $('#dpto_elector').val(data.dpto_elector);
        filtrarMunicipios(data.dpto_elector, data.muni_elector);

        $('#lider_elector').val(data.lider_elector); // Seleccionar Líder

        $('#estado_elector').val(data.estado_elector);

        // --- NUEVO: Traer datos informativos de Puesto/Mesa (Places) ---
        if (data.ident_elector) {
            fetchData(BASE_URL_API + '/Electores/getValidaElector/' + data.ident_elector)
                .then(info => {
                    if (info && info.status && info.data) {
                        document.querySelector("#txtZona").value = info.data.name_zone || "";
                        document.querySelector("#txtPuesto").value = info.data.nameplace_place || "";
                        document.querySelector("#txtMesa").value = info.data.mesa_place || "";
                    }
                });
        }

    } else {
        $('#titleModal').html("Nuevo Elector");

        // Datos informativos limpios
        document.querySelector("#txtZona").value = "";
        document.querySelector("#txtPuesto").value = "";
        document.querySelector("#txtMesa").value = "";

        // Desbloquear campos en modo nuevo
        document.querySelector("#ape1_elector").readOnly = false;
        document.querySelector("#ape2_elector").readOnly = false;
        document.querySelector("#nom1_elector").readOnly = false;
        document.querySelector("#nom2_elector").readOnly = false;

        filtrarMunicipios(""); // Limpia municipios
        $('#lider_elector').val(""); // Limpia líder
    }
    $('.selectpicker').selectpicker('refresh');
    $('#modalFormElector').modal('show');
}

// 4. ACCIONES (VIEW, EDIT, DELETE)

async function fntViewElector(id) {
    const res = await fetchData(`${BASE_URL_API}/electores/getElector/${id}`);
    if (res?.status) {
        const d = res.data;
        // Función auxiliar para actualizar el HTML de los campos del modal de vista
        const setHtml = (selector, val) => {
            const el = document.querySelector(selector);
            if (el) el.innerHTML = val || "---";
        };

        let nombreLider = d.nom1_lider ? `${d.nom1_lider} ${d.ape1_lider || ""}` : "---";
        setHtml('#celLider', nombreLider);
        setHtml('#celIdent', d.ident_elector);
        setHtml('#celNombre', `${d.nom1_elector} ${d.nom2_elector || ""}`);
        setHtml('#celApellido', `${d.ape1_elector} ${d.ape2_elector || ""}`);
        setHtml('#celTelefono', d.telefono_elector);
        setHtml('#celEmail', d.email_elector);
        setHtml('#celDireccion', d.direccion_elector);

        // Traducimos IDs a Nombres usando dataConfig
        setHtml('#celDpto', getNombreById('dptos', d.dpto_elector));
        setHtml('#celMuni', getNombreById('munis', d.muni_elector));


        setHtml('#celEstado', d.estado_elector == 1
            ? '<span class="badge badge-success">Activo</span>'
            : '<span class="badge badge-danger">Inactivo</span>');

        $('#modalViewElector').modal('show');
    } else {
        swal("Error", "No se pudo obtener la información del elector", "error");
    }
}

async function fntEditElector(idElector) {
    const res = await fetchData(`${BASE_URL_API}/electores/getElector/${idElector}`);
    if (res?.status) {
        // Llamamos a openModal en modo edición pasando los datos
        openModal(true, res.data);
    } else {
        swal("Error", "No se pudieron obtener los datos para editar", "error");
    }
}

function fntDelElector(id) {
    swal({
        title: "Eliminar Elector",
        text: "¿Realmente quiere eliminar este registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false
    }, async (isConfirm) => {
        if (isConfirm) {
            // Usamos PUT o DELETE según tu API, aquí mantengo PUT según tu código original
            const res = await fetchData(`${BASE_URL_API}/electores/delElector/`, 'PUT', { idelector: id });
            if (res?.status) {
                swal("Eliminado", res.msg, "success");
                tableElectores.ajax.reload();
            } else {
                swal("Error", res?.msg || "No se pudo eliminar", "error");
            }
        }
    });
}
// Mantenemos tus funciones fntViewCandidato, fntEditCandidato, fntDelCandidato igual...