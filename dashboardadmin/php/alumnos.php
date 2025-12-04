<?php
// alumnos.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("conexion.php");   // conexión PostgreSQL -> $conn
include("header.php");     // navbar + sidebar + <main>

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
  <a href="registrar_alumno.php" class="btn btn-primary">
    <i class="fa-solid fa-user-plus me-2"></i>Nuevo Alumno
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>DNI</th>
            <th>Fecha Nacimiento</th>
            <th>Padre/Madre</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($res_alumnos && pg_num_rows($res_alumnos) > 0) {
            while ($a = pg_fetch_assoc($res_alumnos)) {
              $id = (int)$a['id_alumno'];
              echo "
              <tr>
                <td>{$id}</td>
                <td>".htmlspecialchars($a['nombres'])."</td>
                <td>".htmlspecialchars($a['apellidos'])."</td>
                <td>".htmlspecialchars($a['dni'])."</td>
                <td>".htmlspecialchars($a['fecha_nacimiento'])."</td>
                <td>".htmlspecialchars($a['padre_nombres'])." ".htmlspecialchars($a['padre_apellidos'])."</td>
                <td class='text-center'>
                  <a href='editar_alumno.php?id={$id}' class='btn btn-sm btn-outline-warning me-1'>
                    <i class='fa-solid fa-pen'></i>
                  </a>
                  <a href='eliminar_alumno.php?id={$id}' 
                     class='btn btn-sm btn-outline-danger' 
                     onclick=\"return confirm('¿Eliminar alumno #{$id}?');\">
                    <i class='fa-solid fa-trash'></i>
                  </a>
                </td>
              </tr>";
            }
          } else {
            echo "<tr><td colspan='7' class='text-center text-muted'>No hay registros.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>
