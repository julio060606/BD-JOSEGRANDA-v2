<?php
session_start();

// Validar que el usuario esté autenticado
if (
    !isset($_SESSION['usuario']) || empty($_SESSION['usuario']) ||
    !isset($_SESSION['codigo']) || empty($_SESSION['codigo']) ||
    !isset($_SESSION['correo']) || empty($_SESSION['correo']) ||
    !isset($_SESSION['id_alumno']) || empty($_SESSION['id_alumno'])
) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I.E.E Jóse Granda </title>
    <!--Icono de la pagina-->
    <link rel="icon" href="imagenes/logocolegio.png" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Open+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="CSS/style.css">
<link rel="stylesheet" href="CSS/styleInicio.css">
<link rel="stylesheet" href="CSS/usuario.css">
<style>
    /* ===== VENTANA FLOTANTE DE BIENVENIDA ===== */
    .welcome-modal {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        background: rgba(0, 0, 0, 0.85) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 99999 !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
    }

    .welcome-content {
        background: #ffffff !important;
        border-radius: 15px !important;
        padding: 40px !important;
        max-width: 450px !important;
        width: 90% !important;
        text-align: center !important;
        border: 3px solid #001f3f !important;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4) !important;
        font-family: 'Open Sans', sans-serif !important;
        margin: 0 !important;
        position: relative !important;
        z-index: 100000 !important;
    }

    .welcome-header {
        margin-bottom: 24px !important;
        padding: 0 !important;
    }

    .welcome-icon {
        font-size: 48px !important;
        color: #001f3f !important;
        margin-bottom: 16px !important;
        display: block !important;
    }

    .welcome-title {
        color: #001f3f !important;
        font-size: 32px !important;
        font-weight: 700 !important;
        margin-bottom: 8px !important;
        font-family: 'Playfair Display', serif !important;
        line-height: 1.2 !important;
        padding: 0 !important;
        display: block !important;
    }

    .welcome-subtitle {
        color: #333333 !important;
        font-size: 18px !important;
        margin-bottom: 0 !important;
        padding: 0 !important;
        line-height: 1.4 !important;
        display: block !important;
    }

    .welcome-user-info {
        margin: 32px 0 !important;
        padding: 24px !important;
        background-color: #f8f9fa !important;
        border-radius: 10px !important;
        border: 1px solid #e9ecef !important;
        text-align: center !important;
        display: block !important;
    }

    .welcome-label {
        color: #001f3f !important;
        margin-bottom: 8px !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        display: block !important;
        padding: 0 !important;
    }

    .user-name {
        color: #8B0000 !important;
        font-size: 22px !important;
        font-weight: 700 !important;
        font-family: 'Playfair Display', serif !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .user-name i {
        font-size: 22px !important;
        color: #8B0000 !important;
    }

    .welcome-actions {
        margin-top: 24px !important;
        padding: 0 !important;
        display: block !important;
    }

    .welcome-custom-btn {
        width: 100% !important;
        padding: 12px 24px !important;
        border: none !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        font-family: 'Open Sans', sans-serif !important;
        font-size: 18px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        background-color: #001f3f !important;
        color: #ffffff !important;
        border: 2px solid #001f3f !important;
    }

    .welcome-custom-btn i {
        font-size: 18px !important;
        color: #ffffff !important;
    }

    .welcome-custom-btn:hover {
        background-color: #8B0000 !important;
        border-color: #8B0000 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 8px 20px rgba(139, 0, 0, 0.3) !important;
        color: #ffffff !important;
    }

    .welcome-custom-btn:hover i {
        color: #ffffff !important;
    }

    @media (max-width: 576px) {
        .welcome-content {
            padding: 32px 24px !important;
        }
        
        .welcome-title {
            font-size: 28px !important;
        }
        
        .user-name {
            font-size: 20px !important;
        }
        
        .welcome-user-info {
            padding: 16px !important;
            margin: 24px 0 !important;
        }
        
        .welcome-custom-btn {
            font-size: 16px !important;
            padding: 10px 20px !important;
        }
    }
</style>

</head>

<body class="bg-custom-dark"> <!-- SOLO UN BODY -->

    <!-- BARRA SUPERIOR: horario y teléfono -->
    <div class="bg-dark border-bottom border-accent py-2">
        <div class="container d-flex justify-content-between small text-white-50">
            <div><i class="bi bi-clock me-1"></i> Lun - Vier: 08:00 - 17:00</div>
            <div><i class="bi bi-telephone me-1"></i> +51 989 908 317 • Disponibles <strong
                    class="text-gold">24h</strong>
            </div>
        </div>
    </div>

    <!-- MODAL LOGIN -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title text-gold">Iniciar sesión</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" placeholder="Tu correo electrónico"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" placeholder="Tu contraseña"
                                required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Iniciar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL REGISTRO -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title text-gold">Registrarse</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="regName" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" id="regName" placeholder="Tu nombre completo"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="regEmail" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="regEmail"
                                placeholder="Tu correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <label for="regPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="regPassword" placeholder="Tu contraseña"
                                required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Registrarse</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- NAVBAR -->
    <header class="sticky-top w-100">
        <nav class="navbar navbar-expand-md navbar-dark py-2">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="#" aria-label="Inicio JoseGranda">
                    <img src="imagenes/logocolegio.png" alt="Logo JoseGranda" class="rounded-circle" width="64"
                        height="64" loading="lazy">
                    <span class="fw-bold">I.E.E Jóse Granda</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="mainNav">
                    <ul class="navbar-nav text-uppercase fw-semibold">
                        <li class="nav-item px-2"><a class="nav-link active" href="#">Inicio</a></li>
                        <li class="nav-item px-2"><a class="nav-link" href="servicios.html">Servicios</a></li>
                        <li class="nav-item px-2"><a class="nav-link" href="contacto.html">Contacto</a></li>
                    </ul>
                </div>

                <div class="d-flex align-items-center ms-auto gap-3">
                    <!-- Dropdown usuario -->
<div class="d-flex align-items-center ms-auto gap-3">
    <!-- Usuario logueado (dinámico) -->
    <div class="dropdown">
        <a class="text-white text-decoration-none d-flex align-items-center dropdown-toggle"
            href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <!-- Foto del usuario -->
            <?php if (!empty($_SESSION['foto'])) { ?>
                <img src="<?php echo htmlspecialchars($_SESSION['foto']); ?>" 
                     alt="Foto de usuario" class="rounded-circle me-2" width="40" height="40">
            <?php } else { ?>
                <img src="https://31minutosoficial.cl/wp-content/uploads/2014/02/thumb-bodoque.jpg"
                     alt="Usuario" class="rounded-circle me-2" width="40" height="40">
            <?php } ?>
            <!-- Nombre del usuario -->
            <span class="fw-semibold"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark"
            aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><a class="dropdown-item" href="#">Configuración</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </div>
</div>

                </div>
            </div>
        </nav>
    </header>

<!-- VENTANA FLOTANTE DE BIENVENIDA -->
<div id="welcomeModal" class="welcome-modal">
    <div class="welcome-content">
        <div class="welcome-header">
            <div class="welcome-icon">
                <i class="bi bi-emoji-smile"></i>
            </div>
            <h1 class="welcome-title">¡Bienvenido!</h1>
            <p class="welcome-subtitle">Nos alegra verte de nuevo</p>
        </div>
        
        <div class="welcome-user-info">
    <p class="welcome-label">Has iniciado sesión como:</p>
    <div class="user-name">
        <i class="bi bi-person-circle"></i>
        <span><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
    </div>
</div>

        <div class="welcome-actions">
            <button class="welcome-custom-btn" onclick="closeWelcome(); return false;">
                <i class="bi bi-play-circle"></i>
                <span>Comenzar</span>
            </button>
        </div>
    </div>
</div>

        <!-- CARRUSEL -->
        <div id="myCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">

            <!-- Indicadores -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="1"></button>
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="2"></button>
            </div>

            <!-- Slides -->
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <img src="imagenes/banner1.png" class="d-block w-100" alt="Slide 1">
                    <div class="carousel-caption">
                        <h2>Bienvenido al Colegio José Granda</h2>
                        <p>Con más de 50 años al servicio de la educación peruana, hoy tiene las mismas comodidades que
                            los más
                            importantes colegios privados del Perú.</p>
                        <a href="servicios.html" class="btn btn-light btn-lg">Ver Servicios</a>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <img src="imagenes/banner 2.jpeg" class="d-block w-100" alt="Slide 2">
                    <div class="carousel-caption">
                        <h2>ACTIVIDADES ESCOLARES</h2>
                        <p>Promovemos el desarrollo personal y social a través de talleres, deportes y expresiones
                            artísticas que
                            fortalecen la identidad estudiantil.</p>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item">
                    <img src="imagenes/banner 3.jpeg" class="d-block w-100" alt="Slide 3">
                    <div class="carousel-caption">
                        <h2>Compromiso Docente</h2>
                        <p>Nuestro equipo docente está comprometido con brindar una educación pública de calidad,
                            inclusiva y
                            orientada al
                            futuro de nuestros estudiantes.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controles -->
        <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        <!-- ESTILOS EXTRA -->
        <style>
            .card-overlay {
                border-radius: 12px;
                background: linear-gradient(135deg, rgba(255, 255, 255, 0.85), rgba(255, 255, 255, 0.65));
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
            }

            .carousel-caption {
                bottom: 2rem;
            }

            .btn-hover-rojo:hover {
                background-color: #8B0000 !important;
                color: #fff !important;
            }
        </style>
        <!-- MARCAS -->
        <section class="section-institucional">
            <div class="container">
                <p class="section-text">
                    Trabajamos con los mejores
                </p>
                <!-- Swiper -->
                <div class="swiper brandSwiper">
                    <div class="swiper-wrapper text-center align-items-center">
                        <div class="swiper-slide">
                            <img src="imagenes/minedu.png" class="img-fluid brand-logo" alt="Minedu">
                        </div>
                        <div class="swiper-slide">
                            <img src="imagenes/utp.png" class="img-fluid brand-logo" alt="UTP">
                        </div>
                        <div class="swiper-slide">
                            <img src="imagenes/siseve.jpg" class="img-fluid brand-logo" alt="Siseve">
                        </div>
                        <div class="swiper-slide">
                            <img src="imagenes/escudo.jpeg" alt="Logo 1"
                                style="max-width:120px; max-height:80px; object-fit:contain; display:block; margin:auto;">
                        </div>

                        <div class="swiper-slide">
                            <img src="imagenes/stop.jpg" class="img-fluid brand-logo" alt="Logo 2"
                                style="max-width:120px; max-height:80px; object-fit:contain; display:block; margin:auto;">
                        </div>
                        <div class="swiper-slide">
                            <img src="imagenes/acoso.jpeg" class="img-fluid brand-logo" alt="Logo 3"
                                style="max-width:120px; max-height:80px; object-fit:contain; display:block; margin:auto;">
                        </div>

                    </div>
                </div>
            </div>
        </section>


        <!-- SOBRE NOSOTROS -->
        <section id="sobre-nosotros" class="container my-5 py-4" data-aos="fade-up">
            <h2 class="fs-1 fw-bold text-center mb-3 section-title">
                <i class="fas fa-school me-2"></i>Sobre nosotros
            </h2>
            <hr class="section-divider">

            <div class="row g-5 align-items-center">
                <!-- Texto institucional -->
                <div class="col-lg-6 text-dark" data-aos="fade-right">
                    <p class="lead fst-italic">
                        La Institución Educativa Emblemática “José Granda” fue fundada el 29 de octubre de 1962,
                        mediante la
                        Resolución Ministerial N° 18705, con el nombre de Colegio Nacional de Varones “José Granda”, en
                        honor al
                        ilustre maestro peruano, hombre de ciencias, humanista y gran patriota, que adquirió fama
                        continental por su
                        sabiduría y amor a la juventud.
                    </p>
                </div>



                <!-- Video institucional -->
                <div class="col-lg-6 text-center text-lg-start" data-aos="fade-left">
                    <div class="ratio ratio-16x9 video-box">
                        <iframe src="https://www.youtube.com/embed/3tIVO0cZWcU"
                            title="IE José Granda - Infraestructura moderna" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- SEPARADOR DIAGONAL -->
        <div class="separator"></div>

        <!-- MISIÓN Y VISIÓN -->
        <section class="container-fluid py-5 bg-light">
            <div class="container">
                <h2 class="fs-1 fw-bold text-center mb-3 section-title">
                    <i class="fas fa-bullseye me-2"></i>Misión y Visión
                </h2>
                <hr class="section-divider">

                <div class="row g-4">
                    <!-- Misión -->
                    <div class="col-lg-6" data-aos="zoom-in">
                        <div class="card shadow-lg border-0 h-100 hover-card">
                            <div class="card-body text-center p-4">
                                <h3 class="fs-3 fw-bold mb-3 section-subtitle">
                                    <i class="fas fa-lightbulb me-2"></i>Misión Institucional
                                </h3>
                                <p class="lead">
                                    Al año 2023, aspiramos ser una Institución Educativa líder, disciplinada y referente
                                    en el distrito de
                                    San Martín de Porres, brindando un servicio educativo virtual-presencial de calidad
                                    desde el nivel
                                    primario
                                    hasta el nivel secundario.
                                </p>
                                <img src="imagenes/mision.jpg" alt="Imagen de misión institucional"
                                    class="img-fluid rounded shadow-sm mb-3 hover-zoom"
                                    style="max-width:250px; max-height:200px; object-fit:cover; display:block; margin:auto;"
                                    loading="lazy">
                            </div>
                        </div>
                    </div>

                    <!-- Visión -->
                    <div class="col-lg-6" data-aos="zoom-in">
                        <div class="card shadow-lg border-0 h-100 hover-card">
                            <div class="card-body text-center p-4">
                                <h3 class="fs-3 fw-bold mb-3 section-subtitle">
                                    <i class="fas fa-eye me-2"></i>Visión Institucional
                                </h3>
                                <img src="imagenes/vision.jpg" alt="Imagen de visión institucional"
                                    class="img-fluid rounded shadow-sm mb-3 hover-zoom"
                                    style="max-width:250px; max-height:200px; object-fit:cover; display:block; margin:auto;"
                                    loading="lazy">
                                <p class="lead">
                                    Somos una Institución Educativa estatal que ofrece formación virtual-presencial de
                                    calidad en los
                                    niveles de
                                    primaria y secundaria de menores, orientada al desarrollo integral del educando.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PROMO -->
        <section class="container-fluid promo-banner text-center mb-5">
            <h3 class="h4 text-uppercase text-red-dark mb-3 letter-spacing-2">AYUDÁNDOTE A PROGRESAR</h3>
            <p class="display-6 fw-light text-navy">
                AHORA TE PODRÁS MATRICULAR <span class="fw-bold text-red-dark">ONLINE</span>
            </p>
            <a href="formulario.html" class="btn btn-lg mt-3 px-5 custom-btn">Matricúlate Ya</a>
        </section>

        <!-- HISTORIA -->
        <section class="container my-5 py-5 text-dark">
            <h2 class="text-center fw-bold display-5 mb-5" style="color:#8B0000;">Nuestra historia e infraestructura
            </h2>
            <div class="row g-5 mb-5">
                <div class="col-md-6">
                    <p class="lead fs-4">
                        Fundado en <strong>1962</strong>, el Colegio José Granda es una institución emblemática con más
                        de 50
                        años
                        de trayectoria educativa. Tras su completa remodelación, cuenta con instalaciones de primer
                        nivel que
                        rivalizan con los mejores colegios privados del país.
                    </p>

                    <h4 class="fw-bold mt-4 mb-3" style="color:#8B0000;">Infraestructura de vanguardia</h4>
                    <ul class="fs-5">
                        <li>Laboratorios equipados de ciencias y computación</li>
                        <li>Complejo deportivo con coliseo, piscina semi-olímpica y estadio</li>
                        <li>Talleres técnicos especializados</li>
                        <li>Amplias aulas y áreas comunes</li>
                    </ul>
                </div>

                <div class="col-md-6">
                    <h4 class="fw-bold mb-3" style="color:#8B0000;">Formación integral</h4>
                    <ul class="fs-5">
                        <li>Educación humanística, científica y técnica</li>
                        <li>Talleres extracurriculares de danzas, música, ajedrez y deportes</li>
                        <li>Cuerpo docente calificado y comprometido</li>
                        <li>Programa de valores y desarrollo moral</li>
                    </ul>

                    <h4 class="fw-bold mt-4 mb-3" style="color:#8B0000;">Trayectoria reconocida</h4>
                    <ul class="fs-5">
                        <li>Declarado Institución Educativa Emblemática en 2010</li>
                        <li>Formador de generaciones de profesionales destacados</li>
                        <li>Comprometido con la excelencia educativa</li>
                        <li>Ubicado en San Martín de Porres</li>
                    </ul>
                </div>
        </section>
        <section>
            <!-- SEPARADOR DIAGONAL -->
            <div class="separator"></div>
            <!-- SEDE -->
            <section id="sede-franja" class="py-5">
                <div class="container">
                    <div class="row align-items-center">
                        <!-- Imagen -->
                        <div class="col-md-5 text-center mb-4 mb-md-0">
                            <img src="imagenes/sede.png" class="img-fluid rounded-3 shadow-lg" alt="Local Los Olivos"
                                loading="lazy">
                        </div>
                        <!-- Texto -->
                        <div class="col-md-7">
                            <h3 class="fw-bold display-6 mb-3 text-red-dark">Nuestra sede - San Martín de Porres</h3>
                            <p class="fs-5 mb-4 text-navy">
                                Actualmente, nuestra sede institucional se ubica en el distrito de San
                                Martín de Porres, Lima, en la Av. Universitaria N.º 222. Desde este espacio emblemático,
                                brindamos educación pública de calidad en los niveles de primaria y secundaria,
                                con una infraestructura moderna que incluye laboratorios de ciencias,
                                centros de cómputo, talleres técnicos, coliseo polideportivo, piscina semi-olímpica
                                y estadio con pista atlética reglamentaria.
                            </p>
                            <p class="small mt-2 text-red-dark">
                                <i class="bi bi-calendar-event me-2"></i>Inaugurado el 29 de octubre de 1962
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </section>

        <!-- BOTÓN FLOTANTE MATRICÚLATE YA -->
        <a href="formulario.html" class="btn-floating-left" aria-label="Matricúlate Ya">
            Matricúlate Ya
        </a>

        <style>
            /* Estilo del botón flotante */
            .btn-floating-left {
                position: fixed;
                left: 20px;
                /* Espaciado desde el borde izquierdo */
                bottom: 40px;
                /* Espaciado desde abajo */
                background-color: #FFCC00;
                /* Color amarillo dorado */
                color: #000;
                font-weight: bold;
                padding: 15px 25px;
                border-radius: 50px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
                text-decoration: none;
                z-index: 9999;
                transition: all 0.3s ease;
            }

            .btn-floating-left:hover {
                background-color: #FFA500;
                /* Color al pasar el mouse */
                color: #fff;
                transform: translateY(-3px);
            }
        </style>


        <!-- BOTONES FLOATING: WhatsApp  -->
        <a href="https://wa.me/51971107501" class="btn btn-success rounded-circle shadow floating-btn btn-whatsapp"
            aria-label="WhatsApp">
            <i class="bi bi-whatsapp fs-4" aria-hidden="true"></i>
        </a>


        <!-- FOOTER -->
        <footer class="container-fluid text-light py-5 border-top border-accent mt-5">
            <div class="container">
                <div class="row gy-5">
                    <div class="col-12 col-md-4">
                        <h3 class="h4 fw-bold text-white mb-4">I.E.E Jóse Granda</h3>
                        <p class="text-white-70 mb-0">Colegio Emblematico</p>
                        <p class="text-white-70 mt-3"><i class="bi bi-geo-alt me-2"></i>Av. Universitaria, Óvalo s/n,
                            San Martín de
                            Porres, 15101</p>
                    </div>

                    <div class="col-12 col-md-4">
                        <h6 class="fw-semibold text-uppercase small text-white-70 mb-4">Enlaces rápidos</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3"><a href="#"
                                    class="text-white text-decoration-none d-flex align-items-center"><i
                                        class="bi bi-caret-right-fill text-white me-2"></i>Matriculate</a></li>
                            <li class="mb-3"><a href="#"
                                    class="text-white text-decoration-none d-flex align-items-center"><i
                                        class="bi bi-caret-right-fill text-white me-2"></i>Servicios</a></li>
                            <li class="mb-3"><a href="#"
                                    class="text-white text-decoration-none d-flex align-items-center"><i
                                        class="bi bi-caret-right-fill text-white me-2"></i>Contáctanos</a></li>

                        </ul>
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <h6 class="fw-semibold text-uppercase small text-white-70 mb-4">Síguenos</h6>
                        <div class="mb-4">
                            <a href="https://www.facebook.com/ieejosegranda/?locale=hi_IN"
                                class="social-icon facebook-icon" target="_blank" rel="noopener">
                                <i class="bi bi-facebook" aria-hidden="true"></i>
                            </a>
                        </div>

                        <h6 class="fw-semibold text-uppercase small text-white-70 mb-3">Contacto</h6>
                        <p class="mb-2"><i class="bi bi-telephone me-2"></i>+51 015 697 383 </p>
                        <p class="mb-0"><i class="bi bi-clock me-2"></i>Lun-Vier: 08:00 - 17:00</p>
                    </div>
                </div>
                <div class="text-center text-white-50 small mt-5 pt-3 border-top border-accent">© <span
                        id="year"></span>
                    Institución Educativa Emblemática José Granda. Todos los derechos reservados.</div>
            </div>
        </footer>

        <!-- JS personalizado -->
        <!-- SCRIPTS AL FINAL -->
        <script src="JS/login.js"></script>
        <script src="JS/scriptLogin.js"></script>
        <script src="JS/scriptAdmin.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- AOS Animaciones -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 900,
                once: true
            });
        </script>

        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="swiper-init.js"></script>

<script>
// Función mejorada para cerrar el modal
function closeWelcome() {
    console.log('Intentando cerrar modal...');
    const modal = document.getElementById('welcomeModal');
    
    if (modal) {
        console.log('Modal encontrado, ocultando...');
        // Método 1: Cambiar display
        modal.style.display = 'none';
        
        // Método 2: Cambiar opacity y visibility (más suave)
        modal.style.opacity = '0';
        modal.style.visibility = 'hidden';
        modal.style.transition = 'all 0.3s ease';
        
        console.log('Modal debería estar oculto ahora');
    } else {
        console.error('ERROR: No se pudo encontrar el modal con ID welcomeModal');
    }
}

// Hacer la función global
window.closeWelcome = closeWelcome;

// Múltiples métodos para asegurar que funcione
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado');
    
    const btn = document.querySelector('.welcome-custom-btn');
    console.log('Botón encontrado:', btn);
    
    if (btn) {
        // Método 1: Event listener
        btn.addEventListener('click', function(e) {
            console.log('Click detectado en botón (event listener)');
            e.preventDefault();
            closeWelcome();
        });
        
        // Método 2: onclick directo
        btn.onclick = function(e) {
            console.log('Click detectado en botón (onclick)');
            e.preventDefault();
            closeWelcome();
            return false;
        };
        
        console.log('Eventos agregados al botón');
    } else {
        console.error('ERROR: No se encontró el botón con clase welcome-custom-btn');
    }
    
    // Verificar que el modal existe
    const modal = document.getElementById('welcomeModal');
    console.log('Modal en DOM:', modal);
    
    // Debug: mostrar información del modal
    if (modal) {
        console.log('Estilos del modal:', window.getComputedStyle(modal));
    }
});

// También agregar evento para tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        console.log('Tecla ESC presionada');
        closeWelcome();
    }
});
</script>
    </body> <!-- SOLO UN CIERRE DE BODY -->

</html>