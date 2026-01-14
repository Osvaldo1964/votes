<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Campaña Chadan Rosado Taylor 2026 - Inicio</title>
    <!-- Main CSS (Bootstrap + Theme) -->
    <link rel="stylesheet" type="text/css" href="<?= media(); ?>/css/main.css">
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            background-color: #f5f5f5;
        }

        .hero-section {
            background: linear-gradient(135deg, #009688 0%, #004d40 100%);
            color: #fff;
            padding: 120px 0 160px;
            text-align: center;
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
            margin-bottom: -50px;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: 3.5rem;
            text-transform: uppercase;
        }

        .hero-section p {
            font-size: 1.5rem;
            opacity: 0.9;
        }

        .search-container {
            position: relative;
            z-index: 10;
        }

        .search-card {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .search-card h3 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .btn-consultar {
            background-color: #009688;
            border-color: #009688;
            color: #fff;
            font-weight: bold;
        }

        .btn-consultar:hover {
            background-color: #00796b;
        }

        .info-section {
            padding: 80px 0;
        }

        .feature-box {
            text-align: center;
            padding: 20px;
        }

        .feature-icon {
            font-size: 3rem;
            color: #009688;
            margin-bottom: 15px;
        }

        .footer-landing {
            background: #333;
            color: #aaa;
            padding: 40px 0;
            text-align: center;
        }

        .footer-landing a {
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fa fa-slideshare"></i> CHADAN ROSADO</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active"><a class="nav-link" href="#">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#propuestas">Propuestas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ml-3"
                            href="<?= base_url(); ?>/login">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1>Chadan Rosado Taylor</h1>
            <h3 class="mb-3" style="font-weight: 300;">Candidato a la Cámara de Representantes de Colombia</h3>
            <p class="font-weight-bold" style="font-size: 1.2rem; text-transform: uppercase; letter-spacing: 2px;">Por
                el Departamento del Magdalena</p>
            <p class="mt-4">Compromiso, Transparencia y Gestión para nuestra comunidad.</p>
        </div>
    </header>

    <!-- Consulta Puesto -->
    <section class="search-container container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="search-card">
                    <h3><i class="fa fa-search"></i> Consulta tu Lugar de Votación</h3>
                    <p class="text-muted mb-4">Ingresa tu número de cédula para saber dónde votar.</p>

                    <form id="formConsulta" onsubmit="return false;">
                        <div class="input-group input-group-lg mb-3">
                            <input type="number" id="txtCedula" class="form-control" placeholder="Ej: 12345678"
                                required>
                            <div class="input-group-append">
                                <button class="btn btn-consultar" type="button" id="btnConsultar">Consultar</button>
                            </div>
                        </div>
                    </form>

                    <div id="loading" style="display:none;" class="mt-3">
                        <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
                    </div>

                    <div id="resultado" class="mt-4" style="display:none;">
                    </div>
                </div>

                <!-- SECCIÓN REGISTRO DE VOTO -->
                <div class="search-card mt-5 border-top pt-4">
                    <h3 class="text-success"><i class="fa fa-check-circle"></i> Registra tu Voto</h3>
                    <p class="text-muted mb-4">¡Ya votaste? Confirma tu participación aquí.</p>

                    <form id="formVotoPublico" onsubmit="return false;">
                        <div class="input-group input-group-lg mb-3">
                            <input type="number" id="txtIdentificacionPublico" name="identificacion"
                                class="form-control" placeholder="Tu Cédula" required>
                            <div class="input-group-append">
                                <button class="btn btn-success font-weight-bold" type="submit">¡YA VOTÉ!</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Propuestas / Info -->
    <section class="info-section bg-white" id="propuestas">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold">Nuestros Pilares</h2>
                <p class="text-muted">Ejes fundamentales de nuestro plan de gobierno</p>
            </div>
            <div class="row">
                <div class="col-md-4 feature-box">
                    <i class="feature-icon fa fa-users"></i>
                    <h4>Participación Ciudadana</h4>
                    <p>Haremos que tu voz cuente en cada decisión importante del municipio.</p>
                </div>
                <div class="col-md-4 feature-box">
                    <i class="feature-icon fa fa-briefcase"></i>
                    <h4>Empleo y Desarrollo</h4>
                    <p>Fomento al emprendimiento y atracción de inversión para generar puestos de trabajo.</p>
                </div>
                <div class="col-md-4 feature-box">
                    <i class="feature-icon fa fa-leaf"></i>
                    <h4>Sostenibilidad</h4>
                    <p>Protección de nuestros recursos naturales y espacios públicos para el futuro.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto / PQRS -->
    <section class="info-section bg-light" id="contacto">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-weight-bold">Contáctanos / PQRS</h2>
                <p class="text-muted">Escríbenos, tu opinión es vital para construir futuro.</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form id="formContacto" class="card p-4 shadow-sm">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Cédula</label>
                                <input type="number" class="form-control" id="contactCedula" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Teléfono</label>
                                <input type="tel" class="form-control" id="contactTelefono" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nombre Completo</label>
                            <input type="text" class="form-control" id="contactNombre" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" id="contactEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Detalle / Mensaje</label>
                            <textarea class="form-control" id="contactMensaje" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-consultar btn-block" id="btnEnviarMensaje">Enviar
                            Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-landing">
        <div class="container">
            <h3>Campaña Chadan Rosado Taylor</h3>
            <p>Sede Principal: Calle 10 # 5-20, Centro</p>
            <p>Email: contacto@chadanrosado.com</p>
            <div class="mt-4">
                <a href="#" class="mx-2"><i class="fa fa-facebook fa-2x"></i></a>
                <a href="#" class="mx-2"><i class="fa fa-twitter fa-2x"></i></a>
                <a href="#" class="mx-2"><i class="fa fa-instagram fa-2x"></i></a>
            </div>
            <hr style="border-color: #555;">
            <p class="small">&copy; 2026 Campaña Chadan Rosado Taylor. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= media(); ?>/js/jquery-3.3.1.min.js"></script>
    <script src="<?= media(); ?>/js/popper.min.js"></script>
    <script src="<?= media(); ?>/js/bootstrap.min.js"></script>
    <script src="<?= media(); ?>/js/sweetalert.min.js"></script>

    <script>
        const BASE_URL_API = "<?= BASE_URL_API ?>";
        const BASE_URL = "<?= base_url() ?>";
    </script>
    <script src="<?= media(); ?>/js/functions_landing.js"></script>
    <script src="<?= media(); ?>/js/functions_votacion_publica.js?v=<?= time(); ?>"></script>

</body>

</html>