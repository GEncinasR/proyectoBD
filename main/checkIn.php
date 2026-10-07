<?php
date_default_timezone_set('America/Tijuana');
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';

if (isset($_POST['checkin_cliente']) || isset($_POST['idCliente'])) {
    $idCliente = isset($_POST['idCliente']) ? trim($_POST['idCliente']) : '';
    
    if (!empty($idCliente) && is_numeric($idCliente)) {
        $idCliente = intval($idCliente);
        
        actualizarEstadoVencimiento($con, $idCliente);
        
        $verificarCliente = mysqli_query($con, "SELECT idCliente, Nombres, Apellidos, estadoMembresia, fechaVencimiento FROM Cliente WHERE idCliente = $idCliente");
        
        if ($verificarCliente && mysqli_num_rows($verificarCliente) > 0) {
            $cliente = mysqli_fetch_assoc($verificarCliente);
            
            $membresiaVencida = estaVencida($cliente['fechaVencimiento'], $cliente['estadoMembresia']) || $cliente['estadoMembresia'] === 'VENCIDA';
            
            if ($membresiaVencida) {
                $mensaje = "MEMBRESIA VENCIDA\nContacte a un administrador para renovar";
                $tipoMensaje = 'error';
            } else {
                $diaEntrada = date('Y-m-d');
                $horaEntrada = date('H:i:s');
                
                $resultado = mysqli_query($con, "INSERT INTO checkIn (idCliente, diaEntrada, horaEntrada) 
                               VALUES ($idCliente, '$diaEntrada', '$horaEntrada')");
                
                if ($resultado) {
                    $mensaje = $idCliente . " - " . $cliente['Nombres'] . " " . $cliente['Apellidos'] . "<br>" . nombreMembresia($cliente['estadoMembresia']);
                    $tipoMensaje = 'success';
                } else {
                    $mensaje = "Error al registrar check-in: " . mysqli_error($con);
                    $tipoMensaje = 'error';
                }
            }
        } else {
            $mensaje = "Error: No se encontró un cliente con ID $idCliente";
            $tipoMensaje = 'error';
        }
    } else {
        $mensaje = "Error: Por favor ingrese un ID de cliente válido";
        $tipoMensaje = 'error';
    }
}

$ultimosCheckIns = mysqli_query($con, "SELECT c.idCliente, cl.Nombres, cl.Apellidos, c.diaEntrada, c.horaEntrada 
                                       FROM checkIn c 
                                       INNER JOIN Cliente cl ON c.idCliente = cl.idCliente 
                                       ORDER BY c.diaEntrada DESC, c.horaEntrada DESC 
                                       LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check In - Gimnasio</title>
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/checkIn.css" rel="stylesheet" />
</head>
<body>
    <header class="header-publico">
        <div class="nav-logo">
            <a href="index.html">X-AUST</a>
        </div>

        <ul class="nav-menu">
            <li><a href="index.html">Inicio</a></li>
            <li><a href="sucursales.html">Sucursales</a></li>
            <li><a href="contactanos.html">Contactanos</a></li>
        </ul>

        <ul class="nav-acciones">
            <li><a href="login.html">Login</a></li>
            <li><a href="checkIn.php">Check In</a></li>
        </ul>
    </header>
    
    <main class="checkin-container">
        <div class="checkin-box">
            <h2>Check In</h2>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>" style="<?php echo $tipoMensaje === 'error' && strpos($mensaje, 'VENCIDA') !== false ? 'background-color: #f44336; color: white; font-weight: bold; white-space: pre-line;' : ''; ?>">
                    <?php 
                    if ($tipoMensaje === 'success') {
                        echo $mensaje; 
                    } else {
                        echo nl2br(htmlspecialchars($mensaje));
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="checkIn.php" id="checkinForm">
                <div class="form-group">
                    <label for="idCliente">ID de Cliente</label>
                    <input 
                        type="number" 
                        id="idCliente" 
                        name="idCliente" 
                        placeholder="Ingrese el ID del cliente"
                        required 
                        autofocus
                        min="1"
                    />
                </div>
                
                <button type="submit" class="btn-submit" name="checkin_cliente">
                    Registrar Check In
                </button>
            </form>
            
            <p class="help-text">
                Presione Enter o haga clic en el botón para registrar
            </p>
        </div>
        
        <?php if ($ultimosCheckIns && mysqli_num_rows($ultimosCheckIns) > 0): ?>
        <div class="checkins-recientes">
            <h3>Últimos Check-Ins Registrados</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Cliente</th>
                        <th>Nombre</th>
                        <th>Día de Entrada</th>
                        <th>Hora de Entrada</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($checkin = mysqli_fetch_assoc($ultimosCheckIns)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($checkin['idCliente']); ?></td>
                        <td><?php echo htmlspecialchars($checkin['Nombres'] . ' ' . $checkin['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($checkin['diaEntrada']); ?></td>
                        <td><?php echo htmlspecialchars($checkin['horaEntrada']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </main>
    
    <script>
        document.getElementById('idCliente').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('checkinForm').submit();
            }
        });
        
        <?php if ($tipoMensaje === 'success'): ?>
        setTimeout(function() {
            document.getElementById('idCliente').value = '';
            document.getElementById('idCliente').focus();
        }, 100);
        <?php endif; ?>
    </script>
</body>
</html>

<?php
mysqli_close($con);?>


