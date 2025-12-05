<?php
// editar_pago.php
include("../../conexion/conexion.php");

// Obtener el ID del pago
$id_pago = $_GET['id'] ?? 0;
if ($id_pago == 0) {
    die("❌ Error: ID de pago no válido.");
}

// Obtener el pago actual
$sql = "SELECT * FROM pago WHERE id_pago = $1";
$result = pg_query_params($conn, $sql, array($id_pago));
if (!$result || pg_num_rows($result) == 0) {
    die("❌ Error: Pago no encontrado.");
}

$pago = pg_fetch_assoc($result);

// Actualizar el estado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_estado = $_POST['estado'];
    
    // Validar el estado
    if (!in_array($nuevo_estado, ['Pendiente', 'Confirmado', 'Rechazado'])) {
        die("❌ Error: Estado de pago no válido.");
    }

    // Actualizar el estado en la base de datos
    $sql_update = "UPDATE pago SET estado = $1 WHERE id_pago = $2";
    pg_query_params($conn, $sql_update, array($nuevo_estado, $id_pago));
    
    header("Location: pagos.php?msg=success"); // Redirigir a la página de pagos
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pago</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6dd5ed, #2193b0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            animation: fadeInUp 0.8s ease;
        }
        @keyframes fadeInUp {
            from {opacity: 0; transform: translateY(40px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .btn-custom {
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="col-md-6 offset-md-3">
            <div class="card p-4">
                <div class="card-body text-center">
                    <h3 class="card-title mb-4"><i class="fas fa-credit-card"></i> Editar Estado del Pago</h3>
                    <form method="POST">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-bold">Estado actual:</label>
                            <input type="text" class="form-control" value="<?php echo $pago['estado']; ?>" disabled>
                        </div>
                        <div class="mb-3 text-start">
                            <label for="estado" class="form-label fw-bold">Nuevo Estado:</label>
                            <select name="estado" id="estado" class="form-select" required>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Rechazado">Rechazado</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="pagos.php" class="btn btn-secondary btn-custom"><i class="fas fa-arrow-left"></i> Volver</a>
                            <button type="submit" class="btn btn-primary btn-custom"><i class="fas fa-save"></i> Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
