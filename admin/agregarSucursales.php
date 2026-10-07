<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['crear_sucursal'])) {
    if (!empty($_POST['sucursal_nombre']) && !empty($_POST['sucursal_hrApertura']) && !empty($_POST['sucursal_hrCierre'])) {
        $nombre = $_POST['sucursal_nombre'];
        $telefono = isset($_POST['sucursal_telefono']) ? $_POST['sucursal_telefono'] : '';
        $direccion = isset($_POST['sucursal_direccion']) ? $_POST['sucursal_direccion'] : '';
        $ciudad = isset($_POST['sucursal_ciudad']) ? $_POST['sucursal_ciudad'] : '';
        $hrApertura = $_POST['sucursal_hrApertura'];
        $hrCierre = $_POST['sucursal_hrCierre'];
        
        $nombre = mysqli_real_escape_string($con, $nombre);
        $telefono = mysqli_real_escape_string($con, $telefono);
        $direccion = mysqli_real_escape_string($con, $direccion);
        $ciudad = mysqli_real_escape_string($con, $ciudad);
        
        $resultado = mysqli_query($con, "INSERT INTO Sucursal (Nombre, Telefono, Direccion, Ciudad, hrApertura, hrCierre) 
                     VALUES ('$nombre', '$telefono', '$direccion', '$ciudad', '$hrApertura', '$hrCierre')");
        
        if ($resultado) {
            $mensaje = "Sucursal creada correctamente";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al crear sucursal: " . mysqli_error($con);
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
    <title>Agregar Sucursales</title>
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
        <span>Agregar Sucursal</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Agregar Sucursal</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="sucursal_nombre">Nombre <span class="required">*</span></label>
                    <input type="text" id="sucursal_nombre" name="sucursal_nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="sucursal_telefono">Teléfono</label>
                    <input type="text" id="sucursal_telefono" name="sucursal_telefono">
                </div>
                
                <div class="form-group">
                    <label for="sucursal_direccion">Dirección</label>
                    <input type="text" id="sucursal_direccion" name="sucursal_direccion">
                </div>
                
                <div class="form-group">
                    <label for="sucursal_ciudad">Ciudad</label>
                    <input type="text" id="sucursal_ciudad" name="sucursal_ciudad">
                </div>
                
                <div class="form-group">
                    <label for="sucursal_hrApertura">Hora Apertura <span class="required">*</span></label>
                    <input type="time" id="sucursal_hrApertura" name="sucursal_hrApertura" required>
                </div>
                
                <div class="form-group">
                    <label for="sucursal_hrCierre">Hora Cierre <span class="required">*</span></label>
                    <input type="time" id="sucursal_hrCierre" name="sucursal_hrCierre" required>
                </div>
                
                <button type="submit" name="crear_sucursal" class="btn-submit">Crear Sucursal</button>
            </form>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

