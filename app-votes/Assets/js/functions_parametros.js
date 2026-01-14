// functions_parametros.js

document.addEventListener('DOMContentLoaded', async function () {
    // 1. Cargar selects de configuración (Partidos, Curules)
    await cargarSelects();

    // 2. Cargar datos actuales
    await cargarDatos();

    // 3. Manejar envío del formulario
    const formParametros = document.querySelector("#formParametros");
    if (formParametros) {
        formParametros.onsubmit = async function (e) {
            e.preventDefault();

            const intIdCandidato = document.querySelector('#listCandidato').value;
            // Now we validate selection, not text fields as they are auto-filled
            if (intIdCandidato == "") {
                swal("Atención", "Debe seleccionar un Candidato Oficial.", "error");
                return false;
            }

            const formData = new FormData(formParametros);

            // Assuming BASE_URL_API is defined globally or in template
            // If not, we might need to fallback to a relative path or define it.
            // functions_candidatos.js used BASE_URL_API. I'll assume it exists.
            const url = `${BASE_URL_API}/parametros/setParametros`;

            try {
                const objData = await fetchData(url, 'POST', formData);
                if (objData.status) {
                    swal("Éxito", objData.msg, "success");
                    // Recargar foto si se actualizó?
                    cargarDatos();
                } else {
                    swal("Error", objData.msg, "error");
                }
            } catch (error) {
                swal("Error", "Error en la conexión con el servidor", "error");
                console.error(error);
            }
        };
    }
});

if (document.querySelector("#foto")) {
    let foto = document.querySelector("#foto");
    foto.onchange = function (e) {
        let uploadFoto = document.querySelector("#foto").value;
        let fileimg = document.querySelector("#foto").files;
        let nav = window.URL || window.webkitURL;
        let contactAlert = document.querySelector('#form_alert');
        if (uploadFoto != '') {
            let type = fileimg[0].type;
            let name = fileimg[0].name;
            if (type != 'image/jpeg' && type != 'image/jpg' && type != 'image/png') {
                contactAlert.innerHTML = '<p class="errorArchivo">El archivo no es válido.</p>';
                if (document.querySelector('#img')) {
                    document.querySelector('#img').remove();
                }
                document.querySelector('.delPhoto').classList.add("notBlock");
                foto.value = "";
                return false;
            } else {
                contactAlert.innerHTML = '';
                if (document.querySelector('#img')) {
                    document.querySelector('#img').remove();
                }
                document.querySelector('.delPhoto').classList.remove("notBlock");
                let objeto_url = nav.createObjectURL(this.files[0]);
                document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src=" + objeto_url + ">";
            }
        } else {
            alert("No selecciono foto");
            if (document.querySelector('#img')) {
                document.querySelector('#img').remove();
            }
            document.querySelector('.delPhoto').classList.add("notBlock");
            document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src= '" + media + "/images/uploads/portada_categoria.png'>";
        }
    }
}

if (document.querySelector(".delPhoto")) {
    let delPhoto = document.querySelector(".delPhoto");
    delPhoto.onclick = function (e) {
        document.querySelector("#foto").value = "";
        document.querySelector(".delPhoto").classList.add("notBlock");
        if (document.querySelector('#img')) {
            document.querySelector('#img').remove();
        }
        document.querySelector('.prevPhoto div').innerHTML = "<img id='img' src= '" + media + "/images/uploads/portada_categoria.png'>";
    }
}

async function cargarSelects() {
    try {
        // Cargar Curules, Partidos y Candidatos (Reusing Parametros endpoint or existing ones)
        // We added getCandidatos to Parametros controller.

        // Parallel requests for efficiency
        const [promCurules, promPartidos, promCandidatos] = await Promise.all([
            fetchData(BASE_URL_API + '/candidatos/getJsons'), // Assuming this returns {curules:[], partidos:[]} logic or similar
            // Actually getJsons usually returns a huge JSON. Let's check what it returns or if we need separate.
            // Previous code used getJsons for both. Let's stick to it for those two.
            // But we need Candidatos from DB.
            null,
            fetchData(BASE_URL_API + '/parametros/getCandidatos')
        ]);

        // Re-request getJsons since it was combined above logic-wise
        const dataJson = await fetchData(BASE_URL_API + '/candidatos/getJsons');

        if (dataJson) {
            // Curules
            let optionsCurules = '<option value="">Seleccione...</option>';
            if (dataJson.curules) dataJson.curules.forEach(c => optionsCurules += `<option value="${c.id}">${c.nombre}</option>`);
            document.querySelector('#listCurul').innerHTML = optionsCurules;

            // Partidos
            let optionsPartidos = '<option value="">Seleccione...</option>';
            if (dataJson.partidos) dataJson.partidos.forEach(p => optionsPartidos += `<option value="${p.id}">${p.nombre}</option>`);
            document.querySelector('#listPartido').innerHTML = optionsPartidos;
        }

        // Candidatos System
        const dataCandidatos = await fetchData(BASE_URL_API + '/parametros/getCandidatos');
        let optionsCand = '<option value="">Seleccione...</option>';
        if (dataCandidatos.status) {
            dataCandidatos.data.forEach(c => {
                optionsCand += `<option value="${c.id_candidato}">${c.nombre}</option>`;
            });
        }
        document.querySelector('#listCandidato').innerHTML = optionsCand;

        // Listener for change
        $('#listCandidato').on('change', async function () {
            let id = $(this).val();
            if (id) {
                // Fetch detail
                const res = await fetchData(BASE_URL_API + '/candidatos/getCandidato/' + id);
                if (res.status) {
                    let d = res.data;
                    // txtCandidato removed from view
                    document.querySelector("#txtIdentificacion").value = d.ident_candidato || "";
                    document.querySelector("#txtDireccion").value = d.direccion_candidato || "";
                    document.querySelector("#txtTelefono").value = d.telefono_candidato || "";
                    document.querySelector("#txtEmail").value = d.email_candidato || "";
                    $('#listCurul').val(d.curul_candidato);
                    $('#listPartido').val(d.partido_candidato);
                    $('.selectpicker').selectpicker('refresh');
                }
            } else {
                // Clean if empty
                document.querySelector("#txtIdentificacion").value = "";
                document.querySelector("#txtDireccion").value = "";
                document.querySelector("#txtTelefono").value = "";
                document.querySelector("#txtEmail").value = "";
                $('#listCurul').val("");
                $('#listPartido').val("");
                $('.selectpicker').selectpicker('refresh');
            }
        });

        $('.selectpicker').selectpicker('refresh');

    } catch (error) {
        console.error("Error cargando selects:", error);
    }
}

async function cargarDatos() {
    try {
        const objData = await fetchData(BASE_URL_API + '/parametros/getParametros');
        if (objData.status && objData.data) {
            const d = objData.data;
            // Removed txtCandidato
            $('#listCandidato').val(d.id_candidato);
            $('#listCandidato').selectpicker('refresh');

            document.querySelector("#txtIdentificacion").value = d.ident_candidato || "";
            document.querySelector("#txtDireccion").value = d.direccion_candidato || "";
            document.querySelector("#txtTelefono").value = d.telefono_candidato || "";
            document.querySelector("#txtEmail").value = d.email_candidato || "";
            document.querySelector("#listCurul").value = d.curul_candidato;
            document.querySelector("#listPartido").value = d.partido_candidato;
            document.querySelector("#txtEslogan").value = d.eslogan || ""; // Fix undefined
            document.querySelector("#txtNumLista").value = d.numlista || "";

            // Image logic...
            if (d.foto) {
                document.querySelector("#img").src = media + "/images/uploads/" + d.foto;
            }
            $('.selectpicker').selectpicker('render');
        }
    } catch (e) {
        console.error(e);
    }
}
