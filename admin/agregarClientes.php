<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['crear_cliente'])) {
    if (!empty($_POST['cliente_nombres']) && !empty($_POST['cliente_apellidos']) && !empty($_POST['cliente_correo'])) {
        $nombres = $_POST['cliente_nombres'];
        $apellidos = $_POST['cliente_apellidos'];
        $correo = $_POST['cliente_correo'];
        $telefono = isset($_POST['cliente_telefono']) ? $_POST['cliente_telefono'] : '';
        $fechaNac = isset($_POST['cliente_fechaNac']) ? $_POST['cliente_fechaNac'] : '';
        $tipoMembresia = isset($_POST['cliente_membresia']) && !empty($_POST['cliente_membresia']) ? $_POST['cliente_membresia'] : null;
        $idCoach = !empty($_POST['cliente_idCoach']) ? $_POST['cliente_idCoach'] : 'NULL';
        $fechaAsignacion = !empty($_POST['cliente_idCoach']) ? "'" . date('Y-m-d') . "'" : 'NULL';
        
        $nombres = mysqli_real_escape_string($con, $nombres);
        $apellidos = mysqli_real_escape_string($con, $apellidos);
        $correo = mysqli_real_escape_string($con, $correo);
        $telefono = mysqli_real_escape_string($con, $telefono);
        
        $fechaVencimiento = $tipoMembresia ? calcularFechaVencimiento($tipoMembresia) : null;
        $fechaVencimientoSQL = $fechaVencimiento ? "'$fechaVencimiento'" : 'NULL';
        
        $fechaNacSQL = !empty($fechaNac) ? "'$fechaNac'" : 'NULL';
        $telefonoSQL = !empty($telefono) ? "'$telefono'" : 'NULL';
        $tipoMembresiaSQL = $tipoMembresia ? "'" . mysqli_real_escape_string($con, $tipoMembresia) . "'" : 'NULL';
        
        $resultado = mysqli_query($con, "INSERT INTO Cliente (Nombres, Apellidos, Correo, Telefono, fechaNacimiento, estadoMembresia, fechaVencimiento, idCoach, fechaAsignacionCoach) 
                     VALUES ('$nombres', '$apellidos', '$correo', $telefonoSQL, $fechaNacSQL, $tipoMembresiaSQL, $fechaVencimientoSQL, $idCoach, $fechaAsignacion)");
        
        if ($resultado) {
            $mensaje = "Cliente creado correctamente";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al crear cliente: " . mysqli_error($con);
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Clientes</title>
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
        <span>Agregar Cliente</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Agregar Cliente</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="cliente_nombres">Nombres <span class="required">*</span></label>
                    <input type="text" id="cliente_nombres" name="cliente_nombres" required>
                </div>
                
                <div class="form-group">
                    <label for="cliente_apellidos">Apellidos <span class="required">*</span></label>
                    <input type="text" id="cliente_apellidos" name="cliente_apellidos" required>
                </div>
                
                <div class="form-group">
                    <label for="cliente_correo">Correo <span class="required">*</span></label>
                    <input type="email" id="cliente_correo" name="cliente_correo" required>
                </div>
                
                <div class="form-group">
                    <label for="cliente_telefono">Teléfono</label>
                    <input type="text" id="cliente_telefono" name="cliente_telefono">
                </div>
                
                <div class="form-group">
                    <label for="cliente_fechaNac">Fecha de Nacimiento</label>
                    <input 
                        type="date" 
                        id="cliente_fechaNac" 
                        name="cliente_fechaNac"
                        placeholder="DD/MM/YYYY"
                    >
                    <small class="date-help-text">
                        Puedes escribir la fecha en formato DD/MM/YYYY o usar el calendario
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="cliente_membresia">Tipo de Membresía</label>
                    <select id="cliente_membresia" name="cliente_membresia" onchange="actualizarPreview()">
                        <option value="">Seleccione un tipo</option>
                        <option value="DIA_PRUEBA">Día de Prueba (1 día)</option>
                        <option value="SEMANAL">Semanal (7 días)</option>
                        <option value="MENSUALIDAD">Mensualidad (1 mes)</option>
                        <option value="ANUALIDAD">Anualidad (1 año)</option>
                        <option value="PROVISIONAL">Provisional (sin vencimiento)</option>
                    </select>
                    <small id="preview_fecha" style="display: block; margin-top: 5px; color: #666;"></small>
                </div>
                
                <div class="form-group">
                    <label for="cliente_idCoach">Asignar Coach Personal (opcional)</label>
                    <select id="cliente_idCoach" name="cliente_idCoach">
                        <option value="">Sin coach asignado</option>
                        <?php 
                        if ($coaches) {
                            mysqli_data_seek($coaches, 0);
                            while($coach = mysqli_fetch_assoc($coaches)): ?>
                                <option value="<?php echo $coach['idCoach']; ?>"><?php echo htmlspecialchars($coach['nombre']); ?></option>
                            <?php endwhile; 
                        }
                        ?>
                    </select>
                </div>
                
                <button type="submit" name="crear_cliente" class="btn-submit">Crear Cliente</button>
            </form>
        </div>
    </main>
    
    <script src="../scripts/agregarClientes.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>

