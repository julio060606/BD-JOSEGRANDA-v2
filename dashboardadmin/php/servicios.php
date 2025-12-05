<?php
// servicios.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../conexion/conexion.php");   // conexión PostgreSQL -> $conn
include("header.php");     // navbar + sidebar + <main>

// Consulta servicios disponibles
$res_servicio = pg_query($conn, "
    SELECT id_servicio, nombre, descripcion, costo 
    FROM servicio 
    ORDER BY id_servicio DESC
");

// Consulta los servicios asignados a los alumnos
$res_servicios_alumno = pg_query($conn, "
    SELECT s.id_servicio, s.nombre, s.descripcion, s.costo, 
           a.nombres AS alumno_nombre, a.apellidos AS alumno_apellido, 
           a_s.fecha_inscripcion
    FROM alumno_servicio a_s
    JOIN servicio s ON s.id_servicio = a_s.id_servicio
    JOIN alumno a ON a_s.id_alumno = a.id_alumno
    ORDER BY a.apellidos, a.nombres
");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">Servicios Registrados</h2>
  <a href="servicio_nuevo.php" class="btn btn-success">
    <i class="fas fa-plus me-2"></i>Nuevo Servicio
  </a>
</div>

<!-- Lista de servicios disponibles -->
<div class="card shadow-sm">
  <div class="card-body">
    <h4 class="mb-4">Servicios Disponibles</h4>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Costo (S/)</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($res_servicio && pg_num_rows($res_servicio) > 0) {
            while ($s = pg_fetch_assoc($res_servicio)) {
              $id = (int)$s['id_servicio'];
              $nombre = htmlspecialchars($s['nombre']);
              $descripcion = htmlspecialchars($s['descripcion']);
              $costo = number_format((float)$s['costo'], 2);
              echo "
              <tr>
                <td>{$id}</td>
                <td>{$nombre}</td>
                <td>{$descripcion}</td>
                <td>S/ {$costo}</td>
                <td class='text-center'>
                  <a href='servicio_editar.php?id={$id}' class='btn btn-sm btn-outline-primary me-1'>
                    <i class='fas fa-edit'></i>
                  </a>
                  <a href='servicio_eliminar.php?id={$id}' 
                     class='btn btn-sm btn-outline-danger' 
                     onclick=\"return confirm('¿Eliminar servicio #{$id}?');\">
                    <i class='fas fa-trash'></i>
                  </a>
                </td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='5' class='text-center text-muted'>No hay servicios registrados.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Lista de servicios asignados a los alumnos -->
<div class="card shadow-sm mt-4">
  <div class="card-body">
    <h4 class="mb-4">Servicios Asignados a Alumnos</h4>
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Alumno</th>
            <th>Servicio</th>
            <th>Descripción</th>
            <th>Costo (S/)</th>
            <th>Fecha Inscripción</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($res_servicios_alumno && pg_num_rows($res_servicios_alumno) > 0) {
            while ($sa = pg_fetch_assoc($res_servicios_alumno)) {
              $id_servicio = (int)$sa['id_servicio'];
              $alumno = htmlspecialchars($sa['alumno_nombre'] . ' ' . $sa['alumno_apellido']);
              $servicio_nombre = htmlspecialchars($sa['nombre']);
              $descripcion = htmlspecialchars($sa['descripcion']);
              $costo = number_format((float)$sa['costo'], 2);
              $fecha = htmlspecialchars($sa['fecha_inscripcion']);
              echo "
              <tr>
                <td>{$id_servicio}</td>
                <td>{$alumno}</td>
                <td>{$servicio_nombre}</td>
                <td>{$descripcion}</td>
                <td>S/ {$costo}</td>
                <td>{$fecha}</td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='6' class='text-center text-muted'>No hay servicios asignados a alumnos.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
