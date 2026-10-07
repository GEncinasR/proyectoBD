<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['crear_horario'])) {
    if (!empty($_POST['horario_idClase']) && !empty($_POST['horario_diaSemana']) && !empty($_POST['horario_horaInicio']) && !empty($_POST['horario_horaFin'])) {
        $idClase = $_POST['horario_idClase'];
        $diaSemana = $_POST['horario_diaSemana'];
        $horaInicio = $_POST['horario_horaInicio'];
        $horaFin = $_POST['horario_horaFin'];
        
        $diaSemana = mysqli_real_escape_string($con, $diaSemana);
        
        $resultado = mysqli_query($con, "INSERT INTO Horario_clase (idClase, diaSemana, horaInicio, horaFin) 
                     VALUES ('$idClase', '$diaSemana', '$horaInicio', '$horaFin')");
        
        if ($resultado) {
            $mensaje = "Horario creado correctamente";
            $tipoMensaje = 'success';
        } else {
            $mensaje = "Error al crear horario: " . mysqli_error($con);
            $tipoMensaje = 'error';
        }
    } else {
        $mensaje = "Error: Debe llenar todos los campos obligatorios";
        $tipoMensaje = 'error';
    }
}

$clases = mysqli_query($con, "SELECT idClase, Nombre FROM Clases_particulares ORDER BY Nombre");
if (!$clases) {
    die("Error al obtener clases: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Horario</title>
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
        <span>Agregar Horario</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Crear Horario de Clase</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="horario_idClase">Clase <span class="required">*</span></label>
                    <select id="horario_idClase" name="horario_idClase" required>
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
                
                <div class="form-group">
                    <label for="horario_diaSemana">Día de la Semana <span class="required">*</span></label>
                    <select id="horario_diaSemana" name="horario_diaSemana" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="horario_horaInicio">Hora Inicio <span class="required">*</span></label>
                    <input type="time" id="horario_horaInicio" name="horario_horaInicio" required>
                </div>
                
                <div class="form-group">
                    <label for="horario_horaFin">Hora Fin <span class="required">*</span></label>
                    <input type="time" id="horario_horaFin" name="horario_horaFin" required>
                </div>
                
                <button type="submit" name="crear_horario" class="btn-submit">Crear Horario</button>
            </form>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

