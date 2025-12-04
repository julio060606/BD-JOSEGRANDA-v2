<?php
// Datos de conexión a PostgreSQL
$host = "localhost";       // Servidor
$port = "5432";            // Puerto por defecto de PostgreSQL
$dbname = "JoseGranda_BD";       // Nombre de tu base de datos
$user = "postgres";        // Usuario de PostgreSQL
$password = "25240718"; // Cambia por tu contraseña real

// Conexión
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error de conexión: " . pg_last_error());
}
?>
