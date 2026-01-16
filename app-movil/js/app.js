const app = {

    // Config
    state: {
        currentView: 'home',
        isLoading: false
    },

    init: function () {
        console.log("App Initialized");
        this.setupListeners();
        this.navTo('home');
    },

    setupListeners: function () {
        // Form Consulta
        const formConsulta = document.getElementById('form-consulta');
        if (formConsulta) {
            formConsulta.addEventListener('submit', (e) => {
                const cedula = document.getElementById('cedula-consulta').value;
                this.handleConsulta(cedula);
            });
        }

        // Form Voto
        const formVoto = document.getElementById('form-voto');
        if (formVoto) {
            formVoto.addEventListener('submit', (e) => {
                const cedula = document.getElementById('cedula-voto').value;
                this.handleVoto(cedula);
            });
        }

        // Form Login - Access Admin
        const formLogin = document.getElementById('form-login');
        if (formLogin) {
            formLogin.addEventListener('submit', (e) => {
                const email = document.getElementById('txtEmail').value;
                const password = document.getElementById('txtPassword').value;
                this.handleLogin(email, password);
            });
        }
    },

    // Navigation (SPA simple style)
    navTo: function (viewName) {
        // Ocultar todas las secciones
        document.querySelectorAll('.view-section').forEach(el => el.classList.add('d-none'));

        // Mostrar la deseada
        const target = document.getElementById(`view-${viewName}`);
        if (target) {
            target.classList.remove('d-none');
            this.state.currentView = viewName;
        }

        // Limpiar formularios al cambiar
        if (viewName === 'home') {
            document.getElementById('form-consulta').reset();
            document.getElementById('result-consulta').classList.add('d-none');
            document.getElementById('form-voto').reset();
            document.getElementById('form-login').reset();
            // Clear E-14 also
            if (window.appE14) {
                document.getElementById('listDpto').value = "";
                appE14.resetSelect('listMuni');
                appE14.resetSelect('listZona');
                appE14.resetSelect('listPuesto');
                appE14.resetSelect('listMesa');
                document.getElementById('container-resultados').classList.add('d-none');
            }
        }
    },

    setLoading: function (loading) {
        const loader = document.getElementById('loading');
        if (loading) {
            loader.classList.remove('d-none');
        } else {
            loader.classList.add('d-none');
        }
    },

    // API Calls
    handleConsulta: async function (cedula) {
        if (!cedula) return;

        this.setLoading(true);
        try {
            const response = await fetch(`${CONFIG.API_URL}Publico/consultarPuesto/${cedula}`);
            const data = await response.json();

            this.setLoading(false);

            if (data.status) {
                // Populate data
                const voteData = data.data;
                document.getElementById('res-nombre').textContent = voteData.nombre;
                document.getElementById('res-dep').textContent = voteData.departamento;
                document.getElementById('res-mun').textContent = voteData.municipio;
                document.getElementById('res-puesto').textContent = voteData.puesto;
                document.getElementById('res-mesa').textContent = voteData.mesa;
                document.getElementById('res-dir').textContent = voteData.direccion;

                document.getElementById('result-consulta').classList.remove('d-none');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'No encontrado',
                    text: data.msg || 'Cédula no encontrada en el censo.',
                    confirmButtonText: 'Entendido'
                });
                document.getElementById('result-consulta').classList.add('d-none');
            }

        } catch (error) {
            this.setLoading(false);
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor.',
            });
        }
    },

    handleVoto: async function (cedula) {
        if (!cedula) return;

        const confirm = await Swal.fire({
            title: '¿Confirmar Voto?',
            text: `Se registrará que la cédula ${cedula} ya votó. Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirm.isConfirmed) return;

        this.setLoading(true);
        try {
            const response = await fetch(`${CONFIG.API_URL}Publico/registrarVoto`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ cedula: cedula })
            });
            const data = await response.json();

            this.setLoading(false);

            if (data.status) {
                await Swal.fire({
                    icon: 'success',
                    title: '¡Registrado!',
                    text: data.msg,
                    confirmButtonText: 'Aceptar'
                });
                this.navTo('home');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atención',
                    text: data.msg,
                });
            }

        } catch (error) {
            this.setLoading(false);
            console.error(error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al registrar el voto.',
            });
        }
    },

    handleLogin: async function (email, password) {
        if (!email || !password) {
            Swal.fire('Error', 'Ingrese usuario y contraseña', 'error');
            return;
        }

        this.setLoading(true);
        const formData = new FormData();
        formData.append('txtEmail', email);
        formData.append('txtPassword', password);

        try {
            const response = await fetch(`${CONFIG.API_URL}login/loginUser`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            this.setLoading(false);

            if (data.status) {
                // TOKEN SAVE
                localStorage.setItem('userToken', data.auth.access_token);
                localStorage.setItem('userEmail', data.auth.email_usuario);

                // NATIVE REDIRECT
                this.navTo('resultados');

                // Initialize E-14 Module
                appE14.init();

            } else {
                Swal.fire('Error', data.msg || 'Datos incorrectos', 'error');
            }

        } catch (error) {
            this.setLoading(false);
            console.error(error);
            Swal.fire('Error', 'Error de conexión', 'error');
        }
    },

    logout: function () {
        localStorage.clear();
        this.navTo('login');
    }
};

const appE14 = {
    // Cache
    potencial: 0,

    init: function () {
        this.loadDepartamentos();
    },

    fetchData: async function (endpoint, method = 'GET', body = null) {
        const token = localStorage.getItem('userToken');
        if (!token) {
            app.navTo('login');
            return null;
        }

        const options = {
            method: method,
            headers: {
                'Authorization': `Bearer ${token}`
            }
        };
        if (body) options.body = body;

        try {
            const res = await fetch(`${CONFIG.API_URL}${endpoint}`, options);
            return await res.json();
        } catch (e) {
            console.error(e);
            return null;
        }
    },

    loadDepartamentos: async function () {
        const data = await this.fetchData('lugares/getDepartamentos');
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(d => options += `<option value="${d.id_department}">${d.name_department}</option>`);
        }
        document.getElementById('listDpto').innerHTML = options;
    },

    loadMunicipios: async function () {
        const id = document.getElementById('listDpto').value;
        if (!id) return;

        // Reset downstream
        this.resetSelect('listMuni'); this.resetSelect('listZona');
        this.resetSelect('listPuesto'); this.resetSelect('listMesa');
        document.getElementById('container-resultados').classList.add('d-none');

        const data = await this.fetchData(`lugares/getMunicipios/${id}`);
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(m => options += `<option value="${m.id_municipality}">${m.name_municipality}</option>`);
        }
        const sel = document.getElementById('listMuni');
        sel.innerHTML = options;
        sel.disabled = false;
    },

    loadZonas: async function () {
        const id = document.getElementById('listMuni').value;
        if (!id) return;

        this.resetSelect('listZona'); this.resetSelect('listPuesto'); this.resetSelect('listMesa');
        document.getElementById('container-resultados').classList.add('d-none');

        const data = await this.fetchData(`lugares/getZonas/${id}`);
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(z => options += `<option value="${z.id_zone}">${z.name_zone}</option>`);
        }
        const sel = document.getElementById('listZona');
        sel.innerHTML = options;
        sel.disabled = false;
    },

    loadPuestos: async function () {
        const id = document.getElementById('listZona').value;
        if (!id) return;

        this.resetSelect('listPuesto'); this.resetSelect('listMesa');
        document.getElementById('container-resultados').classList.add('d-none');

        const data = await this.fetchData(`lugares/getPuestos/${id}`);
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            data.data.forEach(p => options += `<option value="${p.nameplace_place}">${p.nameplace_place}</option>`);
        }
        const sel = document.getElementById('listPuesto');
        sel.innerHTML = options;
        sel.disabled = false;
    },

    loadMesas: async function () {
        const zona = document.getElementById('listZona').value;
        const puesto = document.getElementById('listPuesto').value;
        if (!zona || !puesto) return;

        this.resetSelect('listMesa');
        document.getElementById('container-resultados').classList.add('d-none');

        const formData = new FormData();
        formData.append('idZona', zona);
        formData.append('nombrePuesto', puesto);

        const data = await this.fetchData('lugares/getMesas', 'POST', formData);
        let options = '<option value="">Seleccione...</option>';
        if (data && data.status) {
            const mesas = Array.isArray(data.data) ? data.data : [data.data];
            mesas.forEach(m => options += `<option value="${m.id_mesa}">Mesa ${m.nombre_mesa}</option>`);
        }
        const sel = document.getElementById('listMesa');
        sel.innerHTML = options;
        sel.disabled = false;
    },

    checkMesa: async function () {
        const idMesa = document.getElementById('listMesa').value;
        if (!idMesa) return;

        app.setLoading(true);

        const formData = new FormData();
        formData.append('id_mesa', idMesa);

        // 1. Verify
        const check = await this.fetchData('resultados/verificarMesa', 'POST', formData);

        if (check && !check.status) {
            app.setLoading(false);
            Swal.fire('Atención', check.msg, 'warning');
            document.getElementById('listMesa').value = "";
            return;
        }

        // 2. Load Candidates & Potencial
        await this.loadPotencial(idMesa);
        await this.loadCandidatos(idMesa);

        app.setLoading(false);
    },

    loadPotencial: async function (idMesa) {
        // UI Update logic for Potencial (Optional but good)
        // Skipping deep implementation for brevity, setting defaults
        document.getElementById('lblTotalPotencial').textContent = '-';
        document.getElementById('lblMisVotos').textContent = '-';
        document.getElementById('lblPorcentaje').textContent = '-';
    },

    loadCandidatos: async function (idMesa) {
        document.getElementById('container-resultados').classList.remove('d-none');
        document.getElementById('e14-form-body').innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>';

        const data = await this.fetchData('candidatos/getSelectCandidatos');

        if (data && data.status) {
            this.renderForm(data.data, idMesa);
        } else {
            document.getElementById('e14-form-body').innerHTML = '<p class="text-danger">No hay candidatos.</p>';
        }
    },

    renderForm: function (candidatos, idMesa) {
        let html = `
        <form id="form-e14-native">
            <input type="hidden" name="id_mesa" value="${idMesa}">
            <div class="mb-3">
                <label class="form-label fw-bold">Número Formulario E-14</label>
                <input type="number" class="form-control" name="numero_formulario" placeholder="Ej: 987654" required>
            </div>
            <div class="list-group mb-3">
        `;

        candidatos.forEach(c => {
            html += `
             <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                <div>
                    <h6 class="mb-0 fw-bold">${c.nom1_candidato} ${c.ape1_candidato}</h6>
                    <small class="text-muted">Partido ${c.partido_candidato}</small>
                </div>
                <input type="number" class="form-control" style="width: 100px; text-align: center; font-size: 1.2rem;" 
                       name="votos[${c.id_candidato}]" placeholder="0" min="0" required>
             </div>
             `;
        });

        html += `
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg py-3 shadow">
                    <i class="fas fa-save me-2"></i> GUARDAR E-14
                </button>
            </div>
        </form>`;

        document.getElementById('e14-form-body').innerHTML = html;

        document.getElementById('form-e14-native').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitE14(e.target);
        });
    },

    submitE14: async function (form) {
        const confirm = await Swal.fire({
            title: '¿Guardar Resultados?',
            text: "Esta acción registrará los votos para esta mesa.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, Guardar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirm.isConfirmed) return;

        app.setLoading(true);
        const formData = new FormData(form);

        const data = await this.fetchData('resultados/setE14', 'POST', formData);
        app.setLoading(false);

        if (data && data.status) {
            await Swal.fire('Éxito', data.msg, 'success');

            // Cleanup
            this.resetSelect('listMesa');
            document.getElementById('container-resultados').classList.add('d-none');
        } else {
            Swal.fire('Error', data ? data.msg : 'Error al guardar', 'error');
        }
    },

    resetSelect: function (id) {
        const s = document.getElementById(id);
        s.innerHTML = '<option value="">Seleccione...</option>';
        s.disabled = true;
    }
};

// Start
document.addEventListener('DOMContentLoaded', () => {
    app.init();
});
