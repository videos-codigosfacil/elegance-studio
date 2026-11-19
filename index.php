
<?php
require_once "config/conexion.php";
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Élégance Studio | Peluquería</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="/peluqueria/img/icon.png">
    <!-- Estilos propios -->
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<!-- MENÚ DE NAVEGACIÓN -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">

    <div class="container">

        <a class="navbar-brand" href="index.php">
            ÉLÉGANCE<span>.</span>
            <small>HAIR STUDIO</small>
        </a>

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse" data-bs-target="#menu">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link" href="#inicio">Inicio</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#nosotros">Nosotros</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#servicios">Servicios</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#contacto">Contacto</a>
                </li>

                <li class="nav-item ms-lg-3">
                    <a href="login.php" class="btn btn-outline-dark btn-login">
                        Iniciar sesión
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a href="peluquero/registro.php" class="nav-link">
                        <i class="bi bi-scissors"></i>
                            Acceso del personal
                    </a>
</li>

                <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                    <a href="cliente/reservar.php" class="btn btn-dark btn-reservar">
                        Reservar cita
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- PORTADA -->
<header class="hero" id="inicio">

    <div class="hero-overlay"></div>

    <div class="container hero-content">

        <span class="hero-subtitle">
            BELLEZA · ESTILO · ELEGANCIA
        </span>

        <h1>
            Tu estilo merece<br>
            <em>un momento especial.</em>
        </h1>

        <p>
            Descubre una nueva experiencia de belleza.
            Nuestro equipo está listo para ayudarte a encontrar
            el estilo que mejor representa tu personalidad.
        </p>

        <div class="hero-buttons">

            <a href="cliente/reservar.php" class="btn btn-gold">
                Reserva tu cita
                <i class="bi bi-arrow-right"></i>
            </a>

            <a href="#servicios" class="btn btn-outline-light">
                Explorar servicios
            </a>

        </div>

        <div class="hero-note">

            <i class="bi bi-stars"></i>
            Un espacio creado para resaltar tu belleza.

        </div>

    </div>

</header>


<!-- NOSOTROS -->
<section class="about-section" id="nosotros">

    <div class="container">

        <div class="row align-items-center g-5">

            <div class="col-lg-6">

                <div class="about-image">

                    <img
                        src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=900"
                        alt="Interior de nuestro salón de belleza">

                    <div class="image-label">

                        <span>ÉLÉGANCE STUDIO</span>
                        <small>Tu belleza, nuestra pasión</small>

                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <span class="section-subtitle">CONÓCENOS</span>

                <h2 class="section-title">
                    Un espacio para<br>
                    <em>sentirte especial.</em>
                </h2>

                <p class="about-text">
                    En Élégance Studio creemos que cada persona
                    tiene un estilo único. Por eso ofrecemos un
                    ambiente cómodo, atención personalizada y
                    servicios pensados para cuidar tu imagen.
                </p>

                <p class="about-text">
                    Queremos que cada visita sea una experiencia
                    agradable, desde el momento en que reservas
                    tu cita hasta el resultado final.
                </p>

                <a href="cliente/reservar.php" class="text-link">
                    Agenda tu visita
                    <i class="bi bi-arrow-up-right"></i>
                </a>

            </div>

        </div>

    </div>

</section>


<!-- SERVICIOS -->
<section class="services-section" id="servicios">

    <div class="container">

        <div class="text-center section-heading">

            <span class="section-subtitle">LO QUE OFRECEMOS</span>

            <h2 class="section-title">
                Nuestros <em>servicios</em>
            </h2>

            <p>
                Encuentra el cuidado y estilo que estás buscando.
            </p>

        </div>

        <div class="row g-4">

            <!-- SERVICIO 1 -->
            <div class="col-md-4">

                <div class="service-card">

                    <img
                        src="https://images.unsplash.com/photo-1562322140-8baeececf3df?w=700"
                        alt="Corte de cabello">

                    <div class="service-info">

                        <span>01 / ESTILO</span>

                        <h3>Corte de cabello</h3>

                        <p>
                            Cortes personalizados para renovar
                            tu imagen y destacar tu estilo.
                        </p>

                    </div>

                </div>

            </div>


            <!-- SERVICIO 2 -->
            <div class="col-md-4">

                <div class="service-card">

                    <img
                        src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=700"
                        alt="Peinados profesionales">

                    <div class="service-info">

                        <span>02 / CUIDADO</span>

                        <h3>Peinados</h3>

                        <p>
                            Peinados para ocasiones especiales
                            y momentos importantes.
                        </p>

                    </div>

                </div>

            </div>


            <!-- SERVICIO 3 -->
            <div class="col-md-4">

                <div class="service-card">

                    <img
                        src="https://images.unsplash.com/photo-1519699047748-de8e457a634e?w=700"
                        alt="Tratamientos capilares">

                    <div class="service-info">

                        <span>03 / BELLEZA</span>

                        <h3>Tratamientos</h3>

                        <p>
                            Cuidados capilares para mantener
                            tu cabello saludable y brillante.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- RESERVAS -->
<section class="booking-section">

    <div class="container text-center">

        <span class="section-subtitle">
            TU MOMENTO COMIENZA AQUÍ
        </span>

        <h2>
            Regálate un momento<br>
            <em>para ti.</em>
        </h2>

        <p>
            Reserva tu cita de manera fácil y elige el horario
            que mejor se adapte a ti.
        </p>

        <a href="cliente/reservar.php" class="btn btn-gold">
            Crear mi reserva
            <i class="bi bi-arrow-right"></i>
        </a>

    </div>

</section>


<!-- CONTACTO -->
<section class="contact-section" id="contacto">

    <div class="container">

        <div class="row g-4 align-items-center">

            <div class="col-md-6">

                <span class="section-subtitle">
                    ESTAMOS PARA TI
                </span>

                <h2 class="section-title">
                    Visítanos en<br>
                    <em>Élégance Studio.</em>
                </h2>

            </div>

            <div class="col-md-6">

                <div class="contact-item">

                    <i class="bi bi-clock"></i>

                    <div>
                        <strong>Horario de atención</strong>
                        <p>Lunes a sábado · 09:00 a 18:00</p>
                    </div>

                </div>

                <div class="contact-item">

                    <i class="bi bi-calendar-check"></i>

                    <div>
                        <strong>Reserva en línea</strong>
                        <p>Solicita tu cita desde nuestra página web.</p>
                    </div>

                </div>

                <div class="contact-item">

                    <i class="bi bi-geo-alt"></i>

                    <div>
                        <strong>Ubicación</strong>
                        <p>Visítanos y disfruta de nuestros servicios.</p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- PIE DE PÁGINA -->
<footer class="footer">

    <div class="container">

        <div class="row align-items-center g-3">

            <div class="col-md-6">

                <a class="footer-brand" href="index.php">
                    ÉLÉGANCE.
                </a>

                <p>Tu belleza, nuestra inspiración.</p>

            </div>

            <div class="col-md-6 text-md-end">

                <a href="#inicio">Inicio</a>
                <a href="#servicios">Servicios</a>
                <a href="login.php">Iniciar sesión</a>
                <a href="login.php">Acceso del personal</a>

            </div>

        </div>

        <hr>

        <p class="copyright">
            © 2026 Élégance Studio. Todos los derechos reservados.
        </p>

    </div>

</footer>


<!-- BOOTSTRAP Y JAVASCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/script.js"></script>

</body>
</html>
