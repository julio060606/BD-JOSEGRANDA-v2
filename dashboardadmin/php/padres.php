<?php
// padres.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../../conexion/conexion.php");   // conexión PostgreSQL -> $conn
include("header.php");     // navbar + sidebar + <main>

// Consulta de padres
$sql = "SELECT id_padre, nombres, apellidos, dni, telefono, correo 
        FROM padre 
        ORDER BY id_padre DESC";
$res_padres = pg_query($conn, $sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h2 class="fw-bold">Padres Registrados</h2>
  <a href="registrar_padre.php" class="btn btn-primary">
    <i class="fa-solid fa-user-plus me-2"></i>Nuevo Padre
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
            <th>Teléfono</th>
            <th>Correo</th>
            <th class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($res_padres && pg_num_rows($res_padres) > 0) {
            while ($p = pg_fetch_assoc($res_padres)) {
              $id = (int)$p['id_padre'];
              echo "
              <tr>
                <td>{$id}</td>
                <td>".htmlspecialchars($p['nombres'])."</td>
                <td>".htmlspecialchars($p['apellidos'])."</td>
                <td>".htmlspecialchars($p['dni'])."</td>
                <td>".htmlspecialchars($p['telefono'])."</td>
                <td>".htmlspecialchars($p['correo'])."</td>
                <td class='text-center'>
                  <a href='editar_padre.php?id={$id}' class='btn btn-sm btn-outline-warning me-1'>
                    <i class='fa-solid fa-pen'></i>
                  </a>
                  <a href='eliminar_padre.php?id={$id}' 
                     class='btn btn-sm btn-outline-danger' 
                     onclick=\"return confirm('¿Eliminar padre #{$id}?');\">
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
