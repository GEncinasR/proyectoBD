<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['inscribir_cliente'])) {
    if (!empty($_POST['toma_idCliente']) && !empty($_POST['toma_idClase'])) {
        $idCliente = $_POST['toma_idCliente'];
        $idClase = $_POST['toma_idClase'];
        
        $resultado = mysqli_query($con, "INSERT INTO Toma (idCliente, idClase) VALUES ('$idCliente', '$idClase')");
        
        if ($resultado) {
            $mensaje = "Cliente inscrito a clase correctamente";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al inscribir cliente: " . mysqli_error($con);
            $tipoMensaje = 'error';
        }
    } else {
        $mensaje = "Error: Debe seleccionar cliente y clase";
        $tipoMensaje = 'error';
    }
}

$clases = mysqli_query($con, "SELECT idClase, Nombre FROM Clases_particulares ORDER BY Nombre");
if (!$clases) {
    die("Error al obtener clases: " . mysqli_error($con));
}

$clientes = mysqli_query($con, "SELECT idCliente, CONCAT(Nombres, ' ', Apellidos) as nombre FROM Cliente ORDER BY Nombres");
if (!$clientes) {
    die("Error al obtener clientes: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscribir Cliente a Clase</title>
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
        <span>Inscribir Cliente a Clase</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Inscribir Cliente a Clase</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="toma_idCliente">Cliente <span class="required">*</span></label>
                    <select id="toma_idCliente" name="toma_idCliente" required>
                        <option value="">Seleccionar cliente</option>
                        <?php 
                        if ($clientes) {
                            mysqli_data_seek($clientes, 0);
                            while($cliente = mysqli_fetch_assoc($clientes)): ?>
                                <option value="<?php echo $cliente['idCliente']; ?>"><?php echo htmlspecialchars($cliente['nombre']); ?></option>
                            <?php endwhile; 
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="toma_idClase">Clase <span class="required">*</span></label>
                    <select id="toma_idClase" name="toma_idClase" required>
                        <option value="">Seleccionar clase</option>
                        <?php 
                        if ($clases) {
                            mysqli_data_seek($clases, 0);
                            while($clase = mysqli_fetch_assoc($clases)): ?>
                                <option value="<?php echo $clase['idClase']; ?>"><?php echo htmlspecialchars($clase['Nombre']); ?></option>
                            <?php endwhile; 
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="inscribir_cliente" class="btn-submit">Inscribir Cliente</button>
            </form>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

