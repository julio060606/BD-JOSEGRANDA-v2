<?php
// pagos.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexion.php");   // conexión PostgreSQL -> $conn
include("header.php");     // navbar + sidebar + <main>

// Consulta pagos con nombre de alumno, monto del taller, sección y estado
$sql = "SELECT p.id_pago, p.monto, p.metodo, p.estado, p.fecha,
               a.nombres AS alumno_nombres, a.apellidos AS alumno_apellidos,
               s.nombre AS seccion_nombre, p.voucher_url
        FROM pago p
        LEFT JOIN alumno a ON p.id_alumno = a.id_alumno
        LEFT JOIN matricula m ON p.id_alumno = m.id_alumno
        LEFT JOIN seccion s ON m.id_seccion = s.id_seccion
        ORDER BY p.id_pago DESC";

$res_pagos = pg_query($conn, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">Pagos Registrados</h2>
  <a href="registrar_pago.php" class="btn btn-success">
    <i class="fa-solid fa-cash-register me-2"></i>Nuevo Pago
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Alumno</th>
            <th>Sección</th>
            <th>Monto (S/.)</th>
            <th>Método</th>
            <th>Estado</th>
            <th>Voucher</th>
            <th>Fecha</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($res_pagos && pg_num_rows($res_pagos) > 0) {
            while ($p = pg_fetch_assoc($res_pagos)) {
              $id = (int)$p['id_pago'];
              $estado = $p['estado'];
              
              // badge de estado
              $badgeClass = "secondary";
              if ($estado === "Confirmado") $badgeClass = "success";
              elseif ($estado === "Pendiente") $badgeClass = "warning";
              elseif ($estado === "Rechazado") $badgeClass = "danger";

              echo "
              <tr>
                <td>{$id}</td>
                <td>".htmlspecialchars($p['alumno_nombres'] ?? 'No disponible')." ".htmlspecialchars($p['alumno_apellidos'] ?? 'No disponible')."</td>
                <td>".htmlspecialchars($p['seccion_nombre'] ?? 'No disponible')."</td>
                <td>S/ ".number_format($p['monto'], 2)."</td>
                <td>".htmlspecialchars($p['metodo'] ?? 'No especificado')."</td>
                <td><span class='badge bg-{$badgeClass}'>{$estado}</span></td>
                <td><a href='".htmlspecialchars($p['voucher_url'] ?? '#')."' target='_blank'>Ver Voucher</a></td>
                <td>".htmlspecialchars($p['fecha'] ?? 'No disponible')."</td>
                <td class='text-center'>
                  <a href='editar_pago.php?id={$id}' class='btn btn-sm btn-outline-warning me-1'>
                    <i class='fa-solid fa-pen'></i> Cambiar Estado
                  </a>
                  <a href='eliminar_pago.php?id={$id}' 
                     class='btn btn-sm btn-outline-danger' 
                     onclick=\"return confirm('¿Eliminar pago #{$id}?');\">
                    <i class='fa-solid fa-trash'></i>
                  </a>
                </td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='9' class='text-center text-muted'>No hay registros.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
