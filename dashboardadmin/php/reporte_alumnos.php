<?php
include("conexion.php");
include("header.php");

// Consulta alumnos
$sql = "SELECT a.id_alumno, a.nombres, a.apellidos, a.dni, a.fecha_nacimiento, 
               p.nombres AS padre_nombres, p.apellidos AS padre_apellidos
        FROM alumno a
        LEFT JOIN padre p ON a.id_padre = p.id_padre
        ORDER BY a.id_alumno DESC";
$res_alumnos = pg_query($conn, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">Alumnos Registrados</h2>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle text-dark">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>DNI</th>
            <th>Fecha Nac.</th>
            <th>Padre/Madre</th>
            <th class="text-center">Reporte PDF</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res_alumnos && pg_num_rows($res_alumnos) > 0): ?>
              <?php while ($a = pg_fetch_assoc($res_alumnos)): 
                  $id = (int)$a['id_alumno'];
                  $nombre = htmlspecialchars($a['nombres']);
                  $apellido = htmlspecialchars($a['apellidos']);
                  $dni = htmlspecialchars($a['dni']);
                  $fecha = htmlspecialchars($a['fecha_nacimiento']);
                  $padre = htmlspecialchars($a['padre_nombres'] . ' ' . $a['padre_apellidos']);
              ?>
                  <tr>
                      <td><?= $id ?></td>
                      <td><?= $nombre ?></td>
                      <td><?= $apellido ?></td>
                      <td><?= $dni ?></td>
                      <td><?= $fecha ?></td>
                      <td><?= $padre ?></td>
                      <td class="text-center">
                          <a href="generar_pdf_alumnos.php?id=<?= $id ?>" class="btn btn-sm btn-danger" title="Descargar PDF">
                              <i class="fas fa-file-pdf"></i>
                          </a>
                      </td>
                  </tr>
              <?php endwhile; ?>
          <?php else: ?>
              <tr>
                  <td colspan="7" class="text-center text-muted">No hay registros.</td>
              </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
