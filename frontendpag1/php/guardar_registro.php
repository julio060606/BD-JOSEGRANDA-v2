<?php
session_start();
include("conexion.php");

// Capturar datos del formulario
$nombre = $_POST['nombre_completo'] ?? '';
$correo = $_POST['correo_electronico'] ?? '';
$clave = $_POST['clave'] ?? '';
$clave2 = $_POST['confirmar_clave'] ?? '';

// Validar campos obligatorios
if (empty($nombre) || empty($correo) || empty($clave) || empty($clave2)) {
    echo "<script>alert('❌ Todos los campos son obligatorios.'); window.history.back();</script>";
    exit();
}

// Validar coincidencia de contraseñas
if ($clave !== $clave2) {
    echo "<script>alert('❌ Las contraseñas no coinciden.'); window.history.back();</script>";
    exit();
}

// Verificar si el correo ya está registrado
$sql_check = "SELECT id FROM usuarios WHERE correo_electronico = $1";
$result_check = pg_query_params($conn, $sql_check, array($correo));

if (pg_num_rows($result_check) > 0) {
    echo "<script>alert('❌ El correo ya está registrado.'); window.history.back();</script>";
    exit();
}

// Encriptar contraseña
$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

// Insertar nuevo usuario
$sql_insert = "INSERT INTO usuarios (nombre_completo, correo_electronico, clave_hash) VALUES ($1, $2, $3)";
$result_insert = pg_query_params($conn, $sql_insert, array($nombre, $correo, $clave_hash));

if ($result_insert) {
    header("Location: login.html?registro=exitoso");
exit();
} else {
    echo "<script>alert('❌ Error al registrar usuario.'); window.history.back();</script>";
    exit();
}
?>