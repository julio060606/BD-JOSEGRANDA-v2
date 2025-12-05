<?php
session_start();
include("conexion.php");

// Capturar datos del formulario de login
$correo = $_POST['correo'];  // Este es el correo institucional
$clave = $_POST['clave'];    // El DNI que se usa como contraseña

// Consultar el usuario por correo institucional (en la tabla alumnocorreo)
$sql = "SELECT ac.id, ac.alumno_dni, ac.correo_institucional 
        FROM alumnocorreo ac 
        WHERE ac.correo_institucional = $1";
$resultado = pg_query_params($conn, $sql, array($correo));

if (pg_num_rows($resultado) === 1) {
    $usuario = pg_fetch_assoc($resultado);

    // Verificar que el DNI coincide con la clave proporcionada
    if ($clave === $usuario['alumno_dni']) {
        // Guardar datos en sesión
        $_SESSION['usuario'] = $usuario['correo_institucional'];
        $_SESSION['id_alumno'] = $usuario['id'];
        $_SESSION['codigo'] = uniqid(); // Código único para la sesión

        // Obtener los datos del alumno desde la tabla 'alumno' usando el 'dni' recuperado
        $sql_alumno = "SELECT * FROM alumno WHERE dni = $1";
        $res_alumno = pg_query_params($conn, $sql_alumno, array($usuario['alumno_dni']));
        
        if ($res_alumno) {
            $alumno = pg_fetch_assoc($res_alumno);

            // Guardar todos los datos del alumno en sesión
            $_SESSION['alumno_id'] = $alumno['id_alumno'];
            $_SESSION['alumno_nombre'] = $alumno['nombres'];
            $_SESSION['alumno_apellido'] = $alumno['apellidos'];
            $_SESSION['alumno_fecha_nacimiento'] = $alumno['fecha_nacimiento'];
            $_SESSION['alumno_dni'] = $alumno['dni'];
            $_SESSION['alumno_grado'] = $alumno['grado'];
            $_SESSION['alumno_codigo'] = $alumno['codigo'];
            $_SESSION['alumno_foto'] = $alumno['foto_alumno'];
            $_SESSION['alumno_seccion'] = $alumno['seccion']; // Aquí estamos asumiendo que 'seccion' existe
        }

        // Redirigir al portal institucional
                header("Location: /josegrandaproyecto992025/avances/avances2/avances/avances/portalestudiante/index.php");
                exit();
    } else {
        echo "<script>
            alert('❌ Contraseña incorrecta.');
            window.history.back();
        </script>";
        exit();
    }
} else {
    echo "<script>
        alert('❌ Correo no registrado.');
        window.history.back();
    </script>";
    exit();
}
