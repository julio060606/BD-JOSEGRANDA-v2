<?php
// admin.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexion.php");

/*
 * DATOS PRINCIPALES
 */
// Totales
$total_alumnos = (int) (pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM alumno"))['total'] ?? 0);
$total_padres = (int) (pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM padre"))['total'] ?? 0);
$total_matriculas = (int) (pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM matricula"))['total'] ?? 0);
$total_pagos_confirmados = (int) (pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM pago WHERE estado='Confirmado'"))['total'] ?? 0);
$total_pendientes = (int) (pg_fetch_assoc(pg_query($conn, "SELECT COUNT(*) AS total FROM pago WHERE estado='Pendiente'"))['total'] ?? 0);

// Total monto pagado (sum)
$total_monto_pagado_row = pg_fetch_assoc(pg_query($conn, "SELECT COALESCE(SUM(monto),0) AS suma FROM pago WHERE estado='Confirmado'"));
$total_monto_pagado = $total_monto_pagado_row ? (float)$total_monto_pagado_row['suma'] : 0.00;

/*
 * DATOS PARA GRÁFICOS
 */
// Matrículas por grado (para barras)
$mat_by_grade_res = pg_query($conn, "SELECT grado, COUNT(*) AS total 
                                    FROM matricula 
                                    WHERE grado NOT IN ('1', '2', '3')  -- Cambiar '1ro' por '1', '2do' por '2', etc.
                                    GROUP BY grado 
                                    ORDER BY grado");

$mat_by_grade = [];
while ($r = pg_fetch_assoc($mat_by_grade_res)) {
    $mat_by_grade[] = $r;
}


// Pagos por estado (torta)
$pagos_estado_res = pg_query($conn, "SELECT estado, COUNT(*) AS total FROM pago GROUP BY estado");
$pagos_estado = [];
while ($r = pg_fetch_assoc($pagos_estado_res)) {
    $pagos_estado[] = $r;
}

/*
 * SERVICIOS MÁS SOLICITADOS (top 5)
 */
$servicios_top_res = pg_query($conn, "SELECT s.nombre, COUNT(*) AS total
                                       FROM alumno_servicio a
                                       JOIN servicio s ON a.id_servicio = s.id_servicio
                                       GROUP BY s.nombre
                                       ORDER BY total DESC
                                       LIMIT 5");
$servicios_top = [];
while ($r = pg_fetch_assoc($servicios_top_res)) {
    $servicios_top[] = $r;
}

/*
 * ACTIVIDAD RECIENTE (cargamos inicialmente para tener contenido en la página)
 * NOTA: la actualización continua la maneja el JS que llama actividad.php
 */
$actividad_res = pg_query($conn, "SELECT h.descripcion, h.fecha_hora, u.nombre_usuario
                                  FROM historial_actividad h
                                  LEFT JOIN usuario u ON h.id_usuario = u.id_usuario
                                  ORDER BY h.fecha_hora DESC
                                  LIMIT 6");

/*
 * LISTAS (últimos 5)
 */
$ultimos_alumnos_res = pg_query($conn, "SELECT id_alumno, nombres, apellidos, dni, fecha_nacimiento FROM alumno ORDER BY id_alumno DESC LIMIT 5");
$ultimos_pagos_res = pg_query($conn, "SELECT p.id_pago, a.nombres AS alumno_nombre, p.monto, p.metodo, p.estado, p.fecha
                                      FROM pago p
                                      LEFT JOIN alumno a ON p.id_alumno = a.id_alumno
                                      ORDER BY p.fecha DESC
                                      LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard - Colegio</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Iconos -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- CSS personalizado -->
<link rel="stylesheet" href="CSS/admin.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* ====== Añadidos específicos para actividad dinámica ====== */

/* animación para los items cuando aparecen */
.fade-item {
  opacity: 0;
  transform: translateX(12px);
  animation: fadeInRight 0.48s ease forwards;
}
@keyframes fadeInRight {
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* destacar nueva actividad momentáneamente */
.new-activity {
  border-left: 4px solid #0d6efd;
  background: linear-gradient(90deg, rgba(13,110,253,0.04), transparent);
  transition: background 0.4s ease;
}

/* spinner (oculto por defecto) */
#actividad-spinner {
  display: none;
}
</style>
</head>
<body>
<!-- HEADER -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid ps-4">
    
    <!-- Botón para abrir sidebar en móviles -->
    <button class="btn btn-dark d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar">
      <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Logo -->
    <a class="navbar-brand fw-bold navbar-logo" href="#">Colegio — Panel Admin</a>

    <!-- Botones y usuario a la derecha -->
    <div class="d-flex align-items-center ms-auto flex-wrap gap-2">
      
      <!-- Botón registrar alumno -->
      <button class="btn btn-outline-primary me-3 d-none d-md-inline-flex">
        <i class="fa-solid fa-user-plus me-2"></i>Registrar Alumno
      </button>

      <!-- Botón matricular -->
      <button class="btn btn-outline-success me-3 d-none d-md-inline-flex">
        <i class="fa-solid fa-file-contract me-2"></i>Matricular
      </button>

      <!-- Campanita -->
      <div class="me-3 position-relative">
        <button class="btn btn-light border rounded-circle p-2 position-relative">
          <i class="fa-solid fa-bell"></i>
          <?php if($total_pendientes > 0): ?>
            <span class="badge bg-danger badge-notif"><?php echo $total_pendientes; ?></span>
          <?php endif; ?>
        </button>
      </div>

      <!-- Dropdown usuario -->
      <div class="dropdown">
        <a class="d-flex align-items-center text-decoration-none" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="https://via.placeholder.com/40x40.png?text=AD" alt="avatar" class="rounded-circle me-2" />
          <strong>Admin</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><a class="dropdown-item" href="#">Perfil</a></li>
          <li><a class="dropdown-item" href="#">Ajustes</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#">Cerrar sesión</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="d-flex">
  <!-- SIDEBAR -->
  <aside class="offcanvas-lg offcanvas-start sidebar bg-dark text-white" tabindex="-1" id="sidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Menu</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
      <ul class="nav nav-pills flex-column">
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="admin.php"><i class="fa-solid fa-gauge-high me-2"></i>Dashboard</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="padres.php"><i class="fa-solid fa-user-tie me-2"></i>Padres</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="alumnos.php"><i class="fa-solid fa-user-graduate me-2"></i>Alumnos</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="pagos.php"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Pagos</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="servicios.php"><i class="fa-solid fa-chalkboard-user me-2"></i>Servicios</a></li>
        <li class="nav-item mb-2"><a class="nav-link btn-sidebar" href="reporte_alumnos.php"><i class="fa-solid fa-file-export me-2"></i>Reportes</a></li>
      </ul>
    </div>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content p-4 flex-grow-1">
    <!-- CARDS RESUMEN -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-md-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="text-muted">Alumnos</h6>
                <h3 class="fw-bold"><?php echo $total_alumnos; ?></h3>
              </div>
              <div class="icon-circle bg-primary text-white">
                <i class="fa-solid fa-user-graduate"></i>
              </div>
            </div>
            <small class="text-muted">Total registrados</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="text-muted">Padres</h6>
                <h3 class="fw-bold"><?php echo $total_padres; ?></h3>
              </div>
              <div class="icon-circle bg-success text-white">
                <i class="fa-solid fa-user-tie"></i>
              </div>
            </div>
            <small class="text-muted">Contactos</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="text-muted">Matrículas</h6>
                <h3 class="fw-bold"><?php echo $total_matriculas; ?></h3>
              </div>
              <div class="icon-circle bg-warning text-white">
                <i class="fa-solid fa-clipboard-list"></i>
              </div>
            </div>
            <small class="text-muted">Totales</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6 class="text-muted">Pagos (S/)</h6>
                <h3 class="fw-bold"><?php echo number_format($total_monto_pagado,2); ?></h3>
              </div>
              <div class="icon-circle bg-danger text-white">
                <i class="fa-solid fa-sack-dollar"></i>
              </div>
            </div>
            <small class="text-muted"><?php echo $total_pagos_confirmados; ?> confirmados</small>
          </div>
        </div>
      </div>
    </div>

    <!-- GRAFICO + ACTIVIDAD -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="card-title mb-0">Matrículas por Grado</h5>
              <small class="text-muted">Últimos registros</small>
            </div>
            <div style="width:100%; height:250px; min-height:200px;">
              <canvas id="matriculasChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h5 class="card-title mb-0">🔔 Actividad reciente</h5>
              <!-- spinner pequeño -->
              <div id="actividad-spinner" class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></div>
            </div>

            <!-- Le puse id para que podamos actualizarlo por AJAX -->
            <ul class="list-group list-group-flush" id="actividad-list">
              <?php
              if ($actividad_res) {
                while ($act = pg_fetch_assoc($actividad_res)) {
                    $user = $act['nombre_usuario'] ?? 'Sistema';
                    $desc = htmlspecialchars($act['descripcion']);
                    $fecha = date('d/m/Y H:i', strtotime($act['fecha_hora']));
                    echo "<li class='list-group-item d-flex align-items-start'>
                            <span class='me-3 notif-icon'>🔔</span>
                            <div>
                              <div class='small text-muted'>{$fecha} · {$user}</div>
                              <div>{$desc}</div>
                            </div>
                          </li>";
                }
              } else {
                echo "<li class='list-group-item'>No hay actividad reciente.</li>";
              }
              ?>
            </ul>
          </div>
        </div>

        <!-- Pagos por estado (pequeño) -->
        <div class="card shadow-sm mt-3">
          <div class="card-body">
            <h6 class="card-title">Pagos por Estado</h6>
            <div style="width:100%; height:160px;">
              <canvas id="pagosChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TABLAS -->
    <div class="row g-3">
      <div class="col-12 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Últimos Alumnos</h5>
            <div class="table-responsive">
              <table class="table table-striped table-sm mb-0">
                <thead class="table-light">
                  <tr><th>ID</th><th>Nombre</th><th>DNI</th><th>FNac</th></tr>
                </thead>
                <tbody>
                <?php
                if ($ultimos_alumnos_res) {
                  while ($al = pg_fetch_assoc($ultimos_alumnos_res)) {
                      $fn = $al['fecha_nacimiento'] ? date('d/m/Y', strtotime($al['fecha_nacimiento'])) : '-';
                      echo "<tr>
                              <td>{$al['id_alumno']}</td>
                              <td>".htmlspecialchars($al['nombres'].' '.$al['apellidos'])."</td>
                              <td>{$al['dni']}</td>
                              <td>{$fn}</td>
                            </tr>";
                  }
                } else {
                  echo "<tr><td colspan='4'>No hay alumnos.</td></tr>";
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Últimos Pagos</h5>
            <div class="table-responsive">
              <table class="table table-striped table-sm mb-0">
                <thead class="table-light">
                  <tr><th>ID</th><th>Alumno</th><th>Monto</th><th>Estado</th></tr>
                </thead>
                <tbody>
                <?php
                if ($ultimos_pagos_res) {
                  while ($p = pg_fetch_assoc($ultimos_pagos_res)) {
                      $monto = number_format((float)$p['monto'],2);
                      echo "<tr>
                              <td>{$p['id_pago']}</td>
                              <td>".htmlspecialchars($p['alumno_nombre'] ?? '—')."</td>
                              <td>S/ {$monto}</td>
                              <td>{$p['estado']}</td>
                            </tr>";
                  }
                } else {
                  echo "<tr><td colspan='4'>No hay pagos recientes.</td></tr>";
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- SERVICIOS TOP -->
    <div class="card shadow-sm mt-4">
      <div class="card-body">
        <h5 class="card-title">Servicios más solicitados</h5>
        <div class="row">
          <?php
          if (!empty($servicios_top)) {
            foreach ($servicios_top as $s) {
              $nombre = htmlspecialchars($s['nombre']);
              $total = (int)$s['total'];
              echo "<div class='col-6 col-md-3 mb-2'>
                      <div class='small text-muted'>{$nombre}</div>
                      <div class='fw-bold'>{$total}</div>
                    </div>";
            }
          } else {
            echo "<div class='col-12'>No hay servicios registrados.</div>";
          }
          ?>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* === Charts originales (sin tocar) === */
// Datos para Matriculas por grado
const matLabels = <?php
    $labels = [];
    $values = [];
    foreach ($mat_by_grade as $r) {
        $labels[] = $r['grado'];
        $values[] = (int)$r['total'];
    }
    echo json_encode($labels);
?>;
const matValues = <?php echo json_encode($values); ?>;

// Matriculas chart
const ctxMat = document.getElementById('matriculasChart').getContext('2d');
new Chart(ctxMat, {
    type: 'bar',
    data: {
        labels: matLabels,
        datasets: [{
            label: 'Matrículas',
            data: matValues,
            backgroundColor: matLabels.map((_,i) => ['#4CAF50','#2196F3','#FF9800','#9C27B0','#3AA0FF','#8BC34A'][i % 6])
        }]
    },
    options: {
        indexAxis: 'x',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Pagos por estado (doughnut)
const pagosData = <?php
  $pLabels = [];
  $pVals = [];
  foreach ($pagos_estado as $p) { $pLabels[] = $p['estado']; $pVals[] = (int)$p['total']; }
  echo json_encode(['labels'=>$pLabels,'values'=>$pVals]);
?>;

const ctxPag = document.getElementById('pagosChart').getContext('2d');
new Chart(ctxPag, {
    type: 'doughnut',
    data: {
        labels: pagosData.labels,
        datasets: [{
            data: pagosData.values,
            backgroundColor: ['#FFC107','#198754','#DC3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

<!-- ====== Script para recargar la lista de actividad sin recargar la página ====== -->
<script>
/**
 * Carga la lista de actividad desde actividad.php y la inserta en #actividad-list
 * Aplica animación fade-in a cada item. Ejecutar cada X ms.
 * Detecta si hay nueva actividad comparando el primer item (texto).
 */
let lastFirstActivityText = null;
const spinner = document.getElementById('actividad-spinner');

async function cargarActividad() {
  const list = document.getElementById('actividad-list');
  if (!list) return;

  try {
    // show spinner
    if (spinner) spinner.style.display = 'inline-block';

    const res = await fetch('actividad.php', {cache: "no-store"});
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const html = await res.text();

    // replace content
    const previousFirst = list.querySelector('li') ? list.querySelector('li').innerText.trim() : null;
    list.innerHTML = html;

    const items = list.querySelectorAll('li');
    items.forEach((li, i) => {
      li.classList.remove('fade-item', 'new-activity');
      // force reflow to restart animation
      void li.offsetWidth;
      li.style.animationDelay = (i * 0.06) + 's';
      li.classList.add('fade-item');
    });

    // detect new activity by comparing first item text
    const newFirst = list.querySelector('li') ? list.querySelector('li').innerText.trim() : null;
    if (newFirst && previousFirst && newFirst !== previousFirst) {
      // highlight the new item
      const firstLi = list.querySelector('li');
      if (firstLi) {
        firstLi.classList.add('new-activity');
        // remove highlight after a short time
        setTimeout(() => firstLi.classList.remove('new-activity'), 4000);
      }
      // optional: play a tiny notification sound (commented by default)
      // const audio = new Audio('sonido-notificacion.mp3'); audio.play();
    }

    lastFirstActivityText = newFirst;
  } catch (err) {
    console.error('Error al cargar actividad:', err);
  } finally {
    if (spinner) spinner.style.display = 'none';
  }
}

// Primera carga al abrir la página
cargarActividad();

// Programamos recarga cada 5 segundos (5000 ms)
setInterval(cargarActividad, 5000);
</script>

</body>
</html>
