<?php
session_start();
include("conexion.php");

// 1. Capturar datos del formulario
// Usamos null coalesce (??) para evitar errores de variables no definidas
$nombre = $_POST['nombre_completo'] ?? '';
$correo = $_POST['correo_electronico'] ?? '';
$clave = $_POST['clave'] ?? '';
$clave2 = $_POST['confirmar_clave'] ?? '';

// 2. Validar campos obligatorios
if (empty($nombre) || empty($correo) || empty($clave) || empty($clave2)) {
    echo "<script>alert('❌ Todos los campos son obligatorios.'); window.history.back();</script>";
    exit();
}

// 3. Validar coincidencia de contraseñas
if ($clave !== $clave2) {
    echo "<script>alert('❌ Las contraseñas no coinciden.'); window.history.back();</script>";
    exit();
}

// 4. Verificar si el correo ya está registrado
$sql_check = "SELECT id FROM usuarios WHERE correo_electronico = $1";
$result_check = pg_query_params($conn, $sql_check, array($correo));

if (pg_num_rows($result_check) > 0) {
    echo "<script>alert('❌ El correo ya está registrado.'); window.history.back();</script>";
    exit();
}

// 5. Encriptar contraseña
$clave_hash = password_hash($clave, PASSWORD_DEFAULT);

// 6. Insertar nuevo usuario
// AGREGADO: fecha_registro con valor NOW() por si la base de datos no lo pone automático
$sql_insert = "INSERT INTO usuarios (nombre_completo, correo_electronico, clave_hash, fecha_registro) VALUES ($1, $2, $3, NOW())";
$result_insert = pg_query_params($conn, $sql_insert, array($nombre, $correo, $clave_hash));

if ($result_insert) {
    // Registro exitoso
    header("Location: login.html?registro=exitoso");
    exit();
} else {
    // Si falla, mostramos el error exacto de PostgreSQL
    $error_real = pg_last_error($conn);
    echo "<script>
        alert('❌ Error al registrar usuario: " . addslashes($error_real) . "'); 
        window.history.back();
    </script>";
    exit();
}
?>