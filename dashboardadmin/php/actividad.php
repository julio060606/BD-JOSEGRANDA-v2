<?php
include("../../conexion/conexion.php");
session_start();

$res = pg_query($conn, "SELECT h.descripcion, h.fecha_hora, u.nombre_usuario
                        FROM historial_actividad h
                        LEFT JOIN usuario u ON h.id_usuario = u.id_usuario
                        ORDER BY h.fecha_hora DESC
                        LIMIT 6");

if ($res && pg_num_rows($res) > 0) {
    while ($act = pg_fetch_assoc($res)) {
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
