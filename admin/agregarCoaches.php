<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['crear_coach'])) {
    if (!empty($_POST['coach_nombres']) && !empty($_POST['coach_apellidos']) && !empty($_POST['coach_fechaInicio'])) {
        $nombres = $_POST['coach_nombres'];
        $apellidos = $_POST['coach_apellidos'];
        $telefono = isset($_POST['coach_telefono']) ? $_POST['coach_telefono'] : '';
        $area = isset($_POST['coach_area']) ? $_POST['coach_area'] : '';
        $salario = isset($_POST['coach_salario']) ? $_POST['coach_salario'] : '';
        $fechaInicio = $_POST['coach_fechaInicio'];
        
        $nombres = mysqli_real_escape_string($con, $nombres);
        $apellidos = mysqli_real_escape_string($con, $apellidos);
        $telefono = mysqli_real_escape_string($con, $telefono);
        $area = mysqli_real_escape_string($con, $area);
        
        $resultado = mysqli_query($con, "INSERT INTO Coach (Nombres, Apellidos, Telefono, areadeTrabajo, Salario, FechadeInicio) 
                     VALUES ('$nombres', '$apellidos', '$telefono', '$area', '$salario', '$fechaInicio')");
        
        if ($resultado) {
            $mensaje = "Coach creado correctamente";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al crear coach: " . mysqli_error($con);
            $tipoMensaje = 'error';
        }
    } else {
        $mensaje = "Error: Debe llenar todos los campos obligatorios";
        $tipoMensaje = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Coaches</title>
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/admin.css" rel="stylesheet" />
</head>
<body>
    <header class="header-admin">
        <div class="nav-logo">
            <a href="index.php">X-AUST</a>
        </div>

        <ul class="nav-menu">
            <li><a href="../main/index.html">Inicio</a></li>
            <li><a href="../main/sucursales.html">Sucursales</a></li>
            <li><a href="../main/contactanos.html">Contactanos</a></li>
        </ul>

        <ul class="nav-acciones">
            <li><a href="../main/login.html">Logout</a></li>
            <li><a href="../main/checkIn.php">Check In</a></li>
        </ul>
    </header>
    
    <nav class="breadcrumb">
        <a href="../main/login.html">Inicio</a>
        <span class="breadcrumb-separator">/</span>
        <a href="index.php">Panel de Administración</a>
        <span class="breadcrumb-separator">/</span>
        <span>Agregar Coach</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Agregar Coach</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="coach_nombres">Nombres <span class="required">*</span></label>
                    <input type="text" id="coach_nombres" name="coach_nombres" required>
                </div>
                
                <div class="form-group">
                    <label for="coach_apellidos">Apellidos <span class="required">*</span></label>
                    <input type="text" id="coach_apellidos" name="coach_apellidos" required>
                </div>
                
                <div class="form-group">
                    <label for="coach_telefono">Teléfono</label>
                    <input type="text" id="coach_telefono" name="coach_telefono">
                </div>
                
                <div class="form-group">
                    <label for="coach_area">Área de Trabajo</label>
                    <input type="text" id="coach_area" name="coach_area">
                </div>
                
                <div class="form-group">
                    <label for="coach_salario">Salario</label>
                    <input type="number" step="0.01" id="coach_salario" name="coach_salario">
                </div>
                
                <div class="form-group">
                    <label for="coach_fechaInicio">Fecha de Inicio <span class="required">*</span></label>
                    <input type="date" id="coach_fechaInicio" name="coach_fechaInicio" required>
                    <small class="date-help-text">
                        Puedes escribir la fecha en formato DD/MM/YYYY o usar el calendario
                    </small>
                </div>
                
                <button type="submit" name="crear_coach" class="btn-submit">Crear Coach</button>
            </form>
        </div>
    </main>
    
    <script src="../scripts/agregarCoaches.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>

