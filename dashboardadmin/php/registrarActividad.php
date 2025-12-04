<?php
// registrarActividad.php
function registrarActividad($conexion, $id_usuario, $descripcion) {
    try {
        $sql = "INSERT INTO historial_actividad (id_usuario, descripcion, fecha_hora)
                VALUES ($1, $2, NOW())";
        $params = array($id_usuario, $descripcion);
        $result = pg_query_params($conexion, $sql, $params);

        if (!$result) {
            error_log("Error al registrar actividad: " . pg_last_error($conexion));
        }
    } catch (Exception $e) {
        error_log("Excepción al registrar actividad: " . $e->getMessage());
    }
}
