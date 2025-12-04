<?php
session_start();

require_once 'config-google.php';  // Asegúrate de que incluye vendor/autoload.php y configura $client
include("conexion.php");           // Debe establecer conexión PostgreSQL en $conn

use Google_Service_Oauth2;

// Verifica si viene el código de Google OAuth
if (isset($_GET['code'])) {
    // Intercambiar el código por un token de acceso
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Obtener información del usuario
    $oauth = new Google_Service_Oauth2($client);
    $userData = $oauth->userinfo->get();

    $nombre = $userData->name ?? 'Usuario sin nombre';
    $correo = $userData->email ?? '';

    if (empty($correo)) {
        die("No se pudo obtener el correo del usuario.");
    }

    // Verificar si el usuario ya existe en la base de datos
    $sql = "SELECT id FROM usuarios WHERE correo_electronico = $1";
    $resultado = pg_query_params($conn, $sql, array($correo));

    if (!$resultado) {
        die("Error al consultar la base de datos.");
    }

    if (pg_num_rows($resultado) === 0) {
        // Insertar nuevo usuario
        $claveDummy = password_hash('google', PASSWORD_DEFAULT);
        $sqlInsert = "INSERT INTO usuarios (nombre_completo, correo_electronico, clave_hash) VALUES ($1, $2, $3)";
        $insertResult = pg_query_params($conn, $sqlInsert, array($nombre, $correo, $claveDummy));

        if (!$insertResult) {
            die("Error al insertar el nuevo usuario.");
        }
    }

    // Obtener ID del usuario para la sesión
    $sqlID = "SELECT id FROM usuarios WHERE correo_electronico = $1";
    $resID = pg_query_params($conn, $sqlID, array($correo));

    if (!$resID || pg_num_rows($resID) === 0) {
        die("Error al obtener los datos del usuario.");
    }

    $usuario = pg_fetch_assoc($resID);

    // Guardar datos en sesión
    $_SESSION['usuario'] = $nombre;
    $_SESSION['correo'] = $correo;
    $_SESSION['id_alumno'] = $usuario['id'];
    $_SESSION['codigo'] = uniqid();

    // Redirigir al usuario
    header("Location: usuario.php");
    exit();
}
?>
