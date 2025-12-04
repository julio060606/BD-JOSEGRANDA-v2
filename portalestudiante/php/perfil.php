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

// Recuperar los datos del alumno desde la sesión
$nombre = isset($_SESSION['alumno_nombre']) ? $_SESSION['alumno_nombre'] : 'No disponible';
$apellido = isset($_SESSION['alumno_apellido']) ? $_SESSION['alumno_apellido'] : 'No disponible';
$grado = isset($_SESSION['alumno_grado']) ? $_SESSION['alumno_grado'] : 'No disponible';
$seccion = isset($_SESSION['alumno_seccion']) && !empty($_SESSION['alumno_seccion']) ? $_SESSION['alumno_seccion'] : 'No disponible'; // Corregido
$fecha_nacimiento = isset($_SESSION['alumno_fecha_nacimiento']) ? $_SESSION['alumno_fecha_nacimiento'] : 'No disponible';
$dni = isset($_SESSION['alumno_dni']) ? $_SESSION['alumno_dni'] : 'No disponible';
$codigo = isset($_SESSION['alumno_codigo']) ? $_SESSION['alumno_codigo'] : 'No disponible';
$foto_alumno = isset($_SESSION['alumno_foto']) ? $_SESSION['alumno_foto'] : 'imagenes/fotosin.jpg'; // Foto del alumno

// Consulta para obtener la URL de la foto del alumno por su DNI
$query = "SELECT foto_url FROM alumnocorreo WHERE alumno_dni = $1";  // Cambié 'foto' por 'foto_url'
$result = pg_query_params($conn, $query, array($dni));  // Ejecutamos la consulta

if (!$result) {
    die("Error en la consulta: " . pg_last_error());  // Si hay error en la consulta
}

// Recuperar el valor de la foto
$row = pg_fetch_assoc($result);

// Depuración: Verifica lo que devuelve la consulta
var_dump($row);  // Añadir para depurar

if ($row) {
    // Si se encuentra la foto en la base de datos
    $foto_alumno = $row['foto_url'];
} else {
    // Si no se encuentra, asignamos una foto por defecto
    $foto_alumno = 'imagenes/fotosin.jpg';
}

// Cerrar la conexión a la base de datos
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>I.E.E. José Granda — Perfil</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Fuente -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- CSS Personal -->
  <link rel="stylesheet" href="perfil.css">
</head>
<body>

  <!-- SIDEBAR LATERAL -->
  <nav class="sidebar">
    <div class="brand text-center py-3">
      <img src="Imagenes/logocolegio.png" alt="Insignia" class="brand-logo mb-2">
      <h5 class="fw-bold text-white">I.E.E. José Granda</h5>
    </div>
    <ul class="nav flex-column mt-3">
      <li class="nav-item"><a class="nav-link" href="index.html"><i class="bi bi-house-door"></i> Inicio</a></li>
      <li class="nav-item"><a class="nav-link active" href="perfil.html"><i class="bi bi-person"></i> Perfil</a></li>
      <li class="nav-item"><a class="nav-link" href="cursos.html"><i class="bi bi-book"></i> Cursos</a></li>
      <li class="nav-item"><a class="nav-link" href="calendario.html"><i class="bi bi-calendar"></i> Calendario</a></li>
      <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-box-arrow-right"></i> Salir</a></li>
    </ul>
  </nav>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="content">
    <div class="container py-4">

      <!-- BIENVENIDA -->
      <div class="bienvenida d-flex flex-column flex-md-row align-items-center justify-content-center text-center gap-3 mb-4">
        <img src="Imagenes/logocolegio.png" alt="Insignia" class="logo-small">
        <div class="d-flex align-items-center gap-2 justify-content-center">
          <h2 class="fw-bold mb-0">Perfil de <?php echo $nombre . " " . $apellido; ?></h2>
        </div>
      </div>

      <!-- SECCIÓN INFORMACIÓN PERSONAL -->
      <div class="row g-4 mb-4" id="perfil-info">

        <!-- Perfil -->
        <div class="col-lg-6">
          <div class="card perfil-card shadow-sm h-100 d-flex flex-column justify-content-between">
            <div class="card-body d-flex flex-column align-items-center text-center">
              <img src="<?php echo $foto_alumno; ?>" alt="Foto alumno" class="perfil-foto mb-3" id="fotoPerfil">
              <h5 class="fw-bold mb-2"><?php echo $nombre . " " . $apellido; ?></h5>
              <p class="mb-1"><strong>Código:</strong> <?php echo $codigo; ?> <small class="text-muted">(Para cambiar, acercarse a Dirección)</small></p>
              <p class="mb-1"><strong>Edad:</strong> <?php echo (date_diff(date_create($fecha_nacimiento), date_create('today'))->y); ?> años <small class="text-muted">(Para cambiar, acercarse a Dirección)</small></p>
              <p class="mb-1"><strong>Grado:</strong> <?php echo $grado; ?>° de secundaria <small class="text-muted">(Para cambiar, acercarse a Dirección)</small></p>
              <p class="mb-1"><strong>Sección:</strong> <?php echo $seccion; ?></p>
            </div>
          </div>
        </div>

        <!-- Configuración de Perfil -->
        <div class="col-lg-6">
          <div class="card shadow-sm h-100 d-flex flex-column justify-content-between">
            <div class="card-body">
              <h5 class="card-header mb-3">⚙️ Configuración de Perfil</h5>
              <p>Opciones disponibles:</p>
              <ul>
                <li>Cambiar contraseña</li>
                <li>Cambiar foto</li>
              </ul>

              <!-- Formulario cambiar foto -->
              <form action="cambiar_foto.php" method="POST" enctype="multipart/form-data" id="cambiarFotoForm" style="display:none;">
                <label for="foto" class="form-label">Subir nueva foto:</label>
                <input type="file" name="foto" id="foto" class="form-control mb-3" required>
                <input type="hidden" name="alumno_dni" value="<?php echo $dni; ?>"> <!-- DNI del alumno -->
                <button type="submit" class="btn btn-primary w-100">Cambiar Foto</button>
              </form>

              <!-- Botones uniformes -->
              <div class="d-flex gap-2 mt-3 flex-wrap">
                <button class="btn btn-warning btn-sm flex-grow-1" id="btnCambiarContra">Cambiar Contraseña</button>
                <button class="btn btn-primary btn-sm flex-grow-1" id="btnCambiarFoto">Cambiar Foto</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- SCRIPTS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mostrar formulario de cambiar foto
    document.getElementById('btnCambiarFoto').addEventListener('click', () => {
      document.getElementById('cambiarFotoForm').style.display = 'block';
    });
  </script>
</body>
</html>
