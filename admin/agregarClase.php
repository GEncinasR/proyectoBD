<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['crear_clase'])) {
    if (!empty($_POST['clase_nombre']) && !empty($_POST['clase_idCoach']) && !empty($_POST['clase_idSucursal'])) {
        $nombre = $_POST['clase_nombre'];
        $descripcion = isset($_POST['clase_descripcion']) ? $_POST['clase_descripcion'] : '';
        $area = isset($_POST['clase_area']) ? $_POST['clase_area'] : '';
        $idCoach = $_POST['clase_idCoach'];
        $idSucursal = $_POST['clase_idSucursal'];

        $nombre = mysqli_real_escape_string($con, $nombre);
        $descripcion = mysqli_real_escape_string($con, $descripcion);
        $area = mysqli_real_escape_string($con, $area);

        $resultado = mysqli_query($con, "INSERT INTO Clases_particulares (Nombre, Descripcion, Area, idCoach, idSucursal) 
                     VALUES ('$nombre', '$descripcion', '$area', '$idCoach', '$idSucursal')");

        if ($resultado) {
            $mensaje = "Clase creada correctamente";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al crear clase: " . mysqli_error($con);
            $tipoMensaje = 'error';
        }
    } else {
        $mensaje = "Error: Debe llenar todos los campos obligatorios";
        $tipoMensaje = 'error';
    }
}

$coaches = mysqli_query($con, "SELECT idCoach, CONCAT(Nombres, ' ', Apellidos) as nombre FROM Coach ORDER BY Nombres");
if (!$coaches) {
    die("Error al obtener coaches: " . mysqli_error($con));
}

$sucursales = mysqli_query($con, "SELECT idSucursal, Nombre FROM Sucursal ORDER BY Nombre");
if (!$sucursales) {
    die("Error al obtener sucursales: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Clase</title>
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
        <span>Agregar Clase</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Crear Clase</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="clase_nombre">Nombre de la Clase <span class="required">*</span></label>
                    <input type="text" id="clase_nombre" name="clase_nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="clase_descripcion">Descripción</label>
                    <textarea id="clase_descripcion" name="clase_descripcion" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="clase_area">Área</label>
                    <input type="text" id="clase_area" name="clase_area">
                </div>
                
                <div class="form-group">
                    <label for="clase_idCoach">Coach Instructor <span class="required">*</span></label>
                    <select id="clase_idCoach" name="clase_idCoach" required>
                        <option value="">Seleccionar coach</option>
                        <?php
                        if ($coaches) {
                            mysqli_data_seek($coaches, 0);
                            while ($coach = mysqli_fetch_assoc($coaches)): ?>
                                <option value="<?php echo $coach['idCoach']; ?>"><?php echo htmlspecialchars($coach['nombre']); ?></option>
                            <?php endwhile;
                        }
?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="clase_idSucursal">Sucursal <span class="required">*</span></label>
                    <select id="clase_idSucursal" name="clase_idSucursal" required>
                        <option value="">Seleccionar sucursal</option>
                        <?php
if ($sucursales) {
    mysqli_data_seek($sucursales, 0);
    while ($sucursal = mysqli_fetch_assoc($sucursales)): ?>
                                <option value="<?php echo $sucursal['idSucursal']; ?>"><?php echo htmlspecialchars($sucursal['Nombre']); ?></option>
                            <?php endwhile;
}
?>
                    </select>
                </div>
                
                <button type="submit" name="crear_clase" class="btn-submit">Crear Clase</button>
            </form>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

