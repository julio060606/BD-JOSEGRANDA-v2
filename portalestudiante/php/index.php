<?php
session_start();  // Asegúrate de iniciar la sesión al principio del archivo

// Datos de conexión a PostgreSQL
$host = "localhost";       // Servidor
$port = "5432";            // Puerto por defecto de PostgreSQL
$dbname = "JoseGranda_BD"; // Nombre de tu base de datos
$user = "postgres";        // Usuario de PostgreSQL
$password = "12345678";    // Contraseña de PostgreSQL

// Conexión a PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error de conexión: " . pg_last_error());  // Si no se conecta, muestra el error
}

// Recuperar todos los datos del alumno desde la sesión
$nombre = isset($_SESSION['alumno_nombre']) ? $_SESSION['alumno_nombre'] : 'No disponible';
$apellido = isset($_SESSION['alumno_apellido']) ? $_SESSION['alumno_apellido'] : 'No disponible';
$grado = isset($_SESSION['alumno_grado']) ? $_SESSION['alumno_grado'] : 'No disponible';
$seccion = isset($_SESSION['alumno_seccion']) && !empty($_SESSION['alumno_seccion']) ? $_SESSION['alumno_seccion'] : 'No disponible'; // Corregido
$fecha_nacimiento = isset($_SESSION['alumno_fecha_nacimiento']) ? $_SESSION['alumno_fecha_nacimiento'] : 'No disponible';
$dni = isset($_SESSION['alumno_dni']) ? $_SESSION['alumno_dni'] : 'No disponible';
$codigo = isset($_SESSION['alumno_codigo']) ? $_SESSION['alumno_codigo'] : 'No disponible';
$foto_alumno = isset($_SESSION['alumno_foto']) ? $_SESSION['alumno_foto'] : 'imagenes/fotosin.jpg'; // Foto del alumno

// Consulta para obtener "seccionnombre" según el DNI (usamos 'alumno_dni')
$query = "SELECT seccionnombre FROM alumnocorreo WHERE alumno_dni = $1";  // Usamos $1 para el parámetro preparado
$result = pg_query_params($conn, $query, array($dni));  // Ejecutamos la consulta

if (!$result) {
    die("Error en la consulta: " . pg_last_error());  // Si hay error en la consulta
}

// Recuperar el valor de seccionnombre
$row = pg_fetch_assoc($result);
if ($row) {
    $seccionnombre = $row['seccionnombre'];  // Si se encuentra, obtenemos la sección
    $_SESSION['alumno_seccion'] = $seccionnombre;  // Guardamos en la sesión
} else {
    $seccionnombre = 'No disponible';  // Si no se encuentra el DNI, asignamos 'No disponible'
    $_SESSION['alumno_seccion'] = $seccionnombre;  // Guardamos el valor predeterminado en la sesión
}

// Cerrar la conexión a la base de datos
pg_close($conn);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>I.E.E. José Granda — Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- SIDEBAR LATERAL -->
  <nav class="sidebar">
    <div class="brand text-center py-3">
      <img src="Imagenes/logocolegio.png" alt="Insignia" class="brand-logo mb-2">
      <h5 class="fw-bold text-white">I.E.E. José Granda</h5>
    </div>
    <ul class="nav flex-column mt-3">
      <li class="nav-item"><a class="nav-link active" href="#inicio"><i class="bi bi-house-door"></i> Inicio</a></li>
      <li class="nav-item"><a class="nav-link" href="perfil.html"><i class="bi bi-person"></i> Perfil</a></li>
      <li class="nav-item"><a class="nav-link" href="cursos.html"><i class="bi bi-book"></i> Cursos</a></li>
      <li class="nav-item"><a class="nav-link" href="calendario.html"><i class="bi bi-calendar"></i> Calendario</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i> Salir</a></li>
    </ul>
  </nav>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="content">
    <div class="container py-4">
      <!-- BIENVENIDA -->
      <div class="bienvenida d-flex flex-column flex-md-row align-items-center justify-content-center text-center gap-3 mb-2">
        <img src="Imagenes/logocolegio.png" alt="Insignia" class="logo-small">
        <div class="d-flex align-items-center gap-2 justify-content-center">
          <span class="fs-2">👋</span>
          <h2 class="fw-bold mb-0"> Bienvenido <?php echo $nombre . " " . $apellido; ?></h2>
        </div>
      </div>

      <!-- Portal del estudiante debajo -->
      <div class="text-center mb-4">
        <p class="text-secondary fs-5 mb-0">Portal del estudiante — I.E.E. José Granda</p>
      </div>

      <!-- CARRUSEL -->
      <div id="carouselInicio" class="carousel slide mb-5 shadow rounded" data-bs-ride="carousel" data-bs-interval="4500">
        <div class="carousel-inner rounded">
          <div class="carousel-item active">
            <img src="Imagenes/Noticia 1.png" class="d-block w-100" alt="Noticia 1">
          </div>
          <div class="carousel-item">
            <img src="Imagenes/Noticia 2.jfif" class="d-block w-100" alt="Noticia 2">
          </div>
          <div class="carousel-item">
            <img src="Imagenes/Noticia 3.jfif" class="d-block w-100" alt="Noticia 3">
          </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselInicio" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselInicio" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Siguiente</span>
        </button>
      </div>

      <!-- PERFIL Y CALENDARIO -->
      <div class="row g-4 mb-4" id="perfil">
<!-- Perfil -->
<div class="col-lg-4">
  <div class="card perfil-card shadow-sm h-100 d-flex flex-column justify-content-between">
    <div class="card-body d-flex flex-column align-items-center justify-content-center">
      <img src="<?php echo $foto_alumno; ?>" alt="Foto alumno" class="perfil-foto border-roja mb-3">
      <h5 class="fw-bold mb-2"><?php echo $nombre . " " . $apellido; ?></h5>
      <p class="mb-1"><strong>Código:</strong> <?php echo $codigo; ?></p>
      <p class="mb-1"><strong>Edad:</strong> <?php echo (date_diff(date_create($fecha_nacimiento), date_create('today'))->y); ?> años</p>
      <p class="mb-1"><strong>Grado:</strong> <?php echo $grado; ?>° de secundaria</p>
      <p class="mb-0"><strong>Sección:</strong> <?php echo $seccionnombre; ?></p> <!-- Aquí se muestra la sección -->
    </div>
    <div class="card-footer text-center bg-transparent border-0">
      <a href="perfil.html" class="btn btn-primary btn-sm w-100">Ir a Perfil</a>
    </div>
  </div>
</div>

        <!-- Calendario -->
        <div class="col-lg-8" id="calendario">
          <div class="card shadow-sm h-100 p-3 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">🗓️ Calendario Escolar</h5>
                <small class="text-muted">Eventos oficiales (feriados y avisos)</small>
              </div>
              <div style="min-height:100%;">
                <iframe src="https://calendar.google.com/calendar/embed?src=es.peruvian%23holiday%40group.v.calendar.google.com&ctz=America%2FLima"
                        style="border:0; width:100%; height:420px; border-radius:12px;" frameborder="0" scrolling="no" title="Calendario Escolar">
                </iframe>
              </div>
            </div>
            <div class="mt-3 text-center">
              <a href="calendario.html" class="btn btn-success btn-sm w-100">Ir a Calendario</a>
            </div>
          </div>
        </div>
      </div>

      <!-- AVISOS Y CLIMA -->
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <div class="card shadow-sm p-3">
            <h5 class="fw-bold text-danger">📢 Avisos</h5>
            <ul class="mb-0">
              <li>🏫 Ceremonia aniversario — 15 de octubre</li>
              <li>📝 Exámenes finales — 18 a 22 de noviembre</li>
              <li>🎉 Día del estudiante — 25 de octubre</li>
            </ul>
            <small class="text-muted">Si necesitas, sincroniza este calendario con tu Google Calendar.</small>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card shadow-sm p-3 text-center clima-card">
            <h5 class="fw-bold text-primary">🌤️ Clima — Lima</h5>
            <p class="fs-4 text-primary mb-1">22°C — Parcialmente nublado</p>
            <small class="text-muted">Actualizado hace 5 min</small>
          </div>
        </div>
      </div>

      <!-- PROGRESO ACADÉMICO -->
      <div class="card shadow-sm p-3 mb-5">
        <h5 class="fw-bold mb-3 text-primary">📈 Progreso Académico</h5>
        <div class="row gx-3">
          <div class="col-md-6 mb-2">
            <div class="small mb-1">Matemáticas — 85%</div>
            <div class="progress"><div class="progress-bar bg-danger" style="width:85%"></div></div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="small mb-1">Comunicación — 75%</div>
            <div class="progress"><div class="progress-bar bg-primary" style="width:75%"></div></div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="small mb-1">Historia — 70%</div>
            <div class="progress"><div class="progress-bar bg-info" style="width:70%"></div></div>
          </div>
          <div class="col-md-6 mb-2">
            <div class="small mb-1">Inglés — 60%</div>
            <div class="progress"><div class="progress-bar bg-warning" style="width:60%"></div></div>
          </div>
        </div>
      </div>

      <!-- BOTÓN WHATSAPP FLOTANTE -->
      <a href="https://wa.me/51999999999" target="_blank" class="btn-wsp" aria-label="WhatsApp" title="Contactar por WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>

    </div>
  </main>

  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const targets = document.querySelectorAll('.perfil-card, .card, .clima-card, .progress, .carousel, .logo-small');

      // Animación en cascada
      targets.forEach((target, index) => {
        target.style.transitionDelay = (index * 0.15) + "s";
      });

      const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if(entry.isIntersecting){
            entry.target.classList.add('visible');
          }
        });
      }, { threshold: 0.2 });

      targets.forEach(t => io.observe(t));
    });
  </script>

</body>
</html>
