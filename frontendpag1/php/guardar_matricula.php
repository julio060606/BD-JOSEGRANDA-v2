<?php
// guardar_matricula.php

include("conexion.php"); // Conexión a la base de datos
session_start(); // Para poder usar $_SESSION['id_usuario']

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =========================
    // 1. Recoger datos del formulario
    // =========================
    $padre_nombre   = $_POST['padre_nombre'] ?? '';
    $padre_apellido = $_POST['padre_apellido'] ?? '';
    $padre_dni      = $_POST['padre_dni'] ?? '';
    $padre_telefono = $_POST['padre_telefono'] ?? '';
    $padre_correo   = $_POST['padre_correo'] ?? '';

    $alumno_nombre   = $_POST['alumno_nombre'] ?? '';
    $alumno_apellido = $_POST['alumno_apellido'] ?? '';
    $alumno_dni      = $_POST['alumno_dni'] ?? '';
    $alumno_fecha    = $_POST['alumno_fecha'] ?? ''; // Fecha de nacimiento del alumno

    $grado         = $_POST['grado'] ?? '';  // Grado seleccionado
    $taller        = $_POST['taller'] ?? ''; // Taller opcional
    $monto_taller  = $_POST['monto_taller'] ?? 0;  // Monto del taller
    $metodo_pago   = $_POST['metodo'] ?? '';  // Método de pago

    // =========================
    // 2. Validación de método de pago (si hay taller)
    // =========================
    $metodos_validos = ['Yape','Plin','Efectivo','Tarjeta'];
    if (!empty($taller) && !in_array($metodo_pago, $metodos_validos)) {
        die("❌ Debe seleccionar un método de pago válido.");
    }

    // =========================
    // 3. Manejo de documentos (subir archivos)
    // =========================
    $carpeta = "uploads/"; // Carpeta para subir archivos
    if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

    function subirArchivo($inputName, $prefix, $carpeta) {
        if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
            $ruta = $carpeta . $prefix . "_" . time() . "_" . basename($_FILES[$inputName]['name']);
            move_uploaded_file($_FILES[$inputName]["tmp_name"], $ruta);
            return $ruta;
        }
        return null;
    }

    // Subir documentos
    $recibo_agua  = subirArchivo("recibo_agua", "agua", $carpeta);
    $recibo_luz   = subirArchivo("recibo_luz", "luz", $carpeta);
    $notas        = subirArchivo("notas", "notas", $carpeta);
    $voucher_pago = subirArchivo("voucher_pago", "voucher", $carpeta);

    // =========================
    // 4. Transacción
    // =========================
    pg_query($conn, "BEGIN");

    try {
        // Insertar datos del padre
        $sql_padre = "INSERT INTO padre (nombres, apellidos, dni, telefono, correo)
                      VALUES ($1,$2,$3,$4,$5) RETURNING id_padre";
        $res_padre = pg_query_params($conn, $sql_padre, [$padre_nombre,$padre_apellido,$padre_dni,$padre_telefono,$padre_correo]);
        if (!$res_padre) throw new Exception(pg_last_error($conn));
        $id_padre = pg_fetch_result($res_padre, 0, 0);

        // Calcular edad a partir de la fecha de nacimiento
        $fecha_nacimiento = new DateTime($alumno_fecha);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento)->y;

        // Insertar datos del alumno
        $sql_alumno = "INSERT INTO alumno (id_padre, nombres, apellidos, fecha_nacimiento, dni, grado)
                       VALUES ($1,$2,$3,$4,$5,$6) RETURNING id_alumno";
        $res_alumno = pg_query_params($conn, $sql_alumno, [$id_padre, $alumno_nombre, $alumno_apellido, $alumno_fecha, $alumno_dni, $grado]);
        if (!$res_alumno) throw new Exception(pg_last_error($conn));
        $id_alumno = pg_fetch_result($res_alumno, 0, 0);

        // Crear el correo institucional y la contraseña (dni)
        $correo_institucional = "J" . $alumno_dni . "ALUMNO@josegranda.edu.pe";
        $contrasena = $alumno_dni;

        // =========================
        // 5. Asignar sección automáticamente
        // =========================
        // Seleccionar una sección con cupo disponible para el grado
        $sql_sec = "SELECT seccionnombre, id_seccion FROM seccion 
                    WHERE grado=$1 AND cupo_actual < cupo_maximo 
                    ORDER BY cupo_actual ASC LIMIT 1";
        $res_sec = pg_query_params($conn, $sql_sec, [$grado]);
        if (!$res_sec || pg_num_rows($res_sec) == 0) {
            throw new Exception("❌ No hay secciones disponibles para grado $grado");
        }

        // Asignamos la sección
        $seccionnombre = pg_fetch_result($res_sec, 0, 'seccionnombre');
        $id_seccion = pg_fetch_result($res_sec, 0, 'id_seccion'); // Asignamos el id de la sección

// Ruta de la imagen predeterminada
$foto_predeterminada = "imagenes/fotosin.jpg";  // Puedes colocar la ruta de la imagen predeterminada en tu servidor

$sql_correo = "INSERT INTO alumnocorreo (alumno_dni, correo_institucional, contrasena, fecha_nacimiento, edad, grado, seccionnombre, foto_url)
               VALUES ($1,$2,$3,$4,$5,$6,$7,$8)";
$res_correo = pg_query_params($conn, $sql_correo, [$alumno_dni, $correo_institucional, $contrasena, $alumno_fecha, $edad, $grado, $seccionnombre, $foto_predeterminada]);
if (!$res_correo) throw new Exception(pg_last_error($conn));


        // Obtener el año académico activo
        $sql_anio = "SELECT id_anio FROM anio_academico ORDER BY id_anio DESC LIMIT 1";
        $res_anio = pg_query($conn, $sql_anio);
        if (!$res_anio) throw new Exception(pg_last_error($conn));
        $id_anio = pg_fetch_result($res_anio, 0, 0);

        // =========================
        // 6. Insertar matrícula
        // =========================
        $fecha_matricula = isset($_POST['fecha_matricula']) ? $_POST['fecha_matricula'] : date('Y-m-d'); // Fecha actual si no se proporciona
        $sql_matr = "INSERT INTO matricula (id_alumno,id_padre,id_anio,grado,id_seccion,recibo_agua,recibo_luz,notas_anterior,fecha_matricula) 
                     VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9) RETURNING id_matricula";
        $params_matr = [$id_alumno,$id_padre,$id_anio,$grado,$id_seccion,$recibo_agua,$recibo_luz,$notas,$fecha_matricula];
        $res_matr = pg_query_params($conn, $sql_matr, $params_matr);
        if (!$res_matr) throw new Exception(pg_last_error($conn));
        $id_matricula = pg_fetch_result($res_matr, 0, 0); // Obtener el ID de la matrícula recién insertada

        // =========================
        // 7. Asignación de cursos (según el grado)
        // =========================
        $sql_cursos = "SELECT id_curso FROM curso WHERE grado = $1";
        $res_cursos = pg_query_params($conn, $sql_cursos, [$grado]);
        while ($row = pg_fetch_assoc($res_cursos)) {
            $id_curso = $row['id_curso'];

            // Verificar si la combinación id_matricula, id_curso ya existe en matricula_curso
            $sql_verificar = "SELECT 1 FROM matricula_curso WHERE id_matricula = $1 AND id_curso = $2";
            $res_verificar = pg_query_params($conn, $sql_verificar, [$id_matricula, $id_curso]);
            if (pg_num_rows($res_verificar) == 0) { // Si no existe, realizar la inserción
                // Insertar en la tabla matricula_curso
                $sql_matr_curso = "INSERT INTO matricula_curso (id_matricula, id_curso) 
                                   VALUES ($1, $2)";
                $res_matr_curso = pg_query_params($conn, $sql_matr_curso, [$id_matricula, $id_curso]);
                if (!$res_matr_curso) throw new Exception(pg_last_error($conn));
            }
        }

        // =========================
        // 8. Pago (solo si hay taller)
        // =========================
        if (!empty($taller)) {
            $sql_pago = "INSERT INTO pago (id_alumno, monto, metodo, estado, voucher_url)
                         VALUES ($1,$2,$3,'Pendiente',$4)";
            $res_pago = pg_query_params($conn, $sql_pago, [$id_alumno,$monto_taller,$metodo_pago,$voucher_pago]);
            if (!$res_pago) throw new Exception(pg_last_error($conn));

            // Insertar servicio opcional
            $sql_serv = "INSERT INTO alumno_servicio (id_alumno,id_servicio,fecha_inscripcion)
                         VALUES ($1,$2,CURRENT_DATE)";
            $res_serv = pg_query_params($conn, $sql_serv, [$id_alumno,$taller]);
            if (!$res_serv) throw new Exception(pg_last_error($conn));
        }

        // =========================
        // 9. Actualizar cupo en la sección
        // =========================
        $sql_upd = "UPDATE seccion SET cupo_actual=cupo_actual+1 WHERE id_seccion=$1";
        $res_upd = pg_query_params($conn, $sql_upd, [$id_seccion]);
        if (!$res_upd) throw new Exception(pg_last_error($conn));

        // =========================
        // 10. Registrar actividad (si hay usuario en sesión)
        // =========================
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        if ($id_usuario) {
            $descripcion = "Registró matrícula de alumno: $alumno_nombre $alumno_apellido";
            $sql_historial = "INSERT INTO historial_actividad (id_usuario, descripcion) VALUES ($1,$2)";
            pg_query_params($conn, $sql_historial, [$id_usuario, $descripcion]);
        }

        // Confirmar transacción
        pg_query($conn, "COMMIT");
        echo "✅ Matrícula registrada correctamente (Alumno ID: $id_alumno, Sección: $seccionnombre)";

    } catch (Exception $e) {
        pg_query($conn, "ROLLBACK");
        echo "❌ Error: " . $e->getMessage();
    }

} else {
    echo "❌ Acceso no permitido.";
}
?>
