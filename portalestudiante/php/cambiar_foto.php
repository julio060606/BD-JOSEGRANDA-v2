<?php
// cambiar_foto.php
session_start();

// Verificar si el alumno está autenticado
if (!isset($_SESSION['id_alumno'])) {
    header("Location: login.php");
    exit();
}

include("conexion.php");

// Verificar si se ha subido una foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    // Obtener el DNI del alumno
    $alumno_dni = $_POST['alumno_dni'];

    // Ruta donde se almacenará la imagen
    $carpeta = "uploads/";
    if (!file_exists($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    // Guardar la imagen con un nombre único
    $ruta_foto = $carpeta . "foto_" . time() . "_" . basename($_FILES['foto']['name']);
    move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_foto);

    // Actualizar la foto del alumno en la base de datos
    $sql_actualizar_foto = "UPDATE alumnocorreo SET foto_url = $1 WHERE alumno_dni = $2";
    $res_actualizar_foto = pg_query_params($conn, $sql_actualizar_foto, [$ruta_foto, $alumno_dni]);

    if ($res_actualizar_foto) {
        echo "✅ Foto actualizada correctamente.";
    } else {
        echo "❌ Error al actualizar la foto.";
    }
} else {
    echo "❌ No se ha subido ninguna imagen o hubo un error en la carga.";
}
?>
