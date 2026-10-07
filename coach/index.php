<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idCoach'])) {
    header('Location: ../main/login.html');
    exit();
}

$idCoach = $_SESSION['idCoach'];
$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$coachQuery = mysqli_query($con, "SELECT * FROM Coach WHERE idCoach = $idCoach") or die("Problemas con la consulta: " . mysqli_error($con));
$coach = mysqli_fetch_array($coachQuery);

if (!$coach) {
    header('Location: ../main/login.html');
    exit();
}

$diasLaborables = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];
$diaNum = date('w');
$diaActual = isset($diasLaborables[$diaNum]) ? $diasLaborables[$diaNum] : null;

$clasesHoyQuery = "SELECT COUNT(*) as total 
                   FROM Clases_particulares c 
                   INNER JOIN Horario_clase h ON c.idClase = h.idClase 
                   WHERE c.idCoach = $idCoach" . ($diaActual ? " AND h.diaSemana = '$diaActual'" : "");
$clasesHoyResult = mysqli_query($con, $clasesHoyQuery) or die("Problemas con la consulta: " . mysqli_error($con));
$clasesHoy = mysqli_fetch_array($clasesHoyResult);
$totalClasesHoy = $clasesHoy['total'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Coach</title>
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/coach.css" rel="stylesheet" />
</head>
<body>
    <header class="header-coach">
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
    
    <main class="coach-container">
        <div class="coach-encabezado">
            <h1>BIENVENIDO <?php echo htmlspecialchars($coach['Nombres'] . ' ' . $coach['Apellidos']); ?></h1>
        </div>
        
        <div class="coach-stats">
            <h3><?php echo $diaActual ? "Hoy ($diaActual) tienes $totalClasesHoy clases" : "No hay clases programadas en fines de semana"; ?></h3>
        </div>
        
        <div class="coach-buttons">
            <a href="clasesHoy.php" class="accion-coach">
                <h3>Clases de Hoy</h3>
                <p>Ver todas tus clases programadas para hoy</p>
            </a>
            
            <a href="todasClases.php" class="accion-coach">
                <h3>Todas mis Clases</h3>
                <p>Consultar todas las clases que impartes</p>
            </a>
            
            <a href="misClientes.php" class="accion-coach">
                <h3>Mis Clientes</h3>
                <p>Ver todos los clientes asignados a ti</p>
            </a>
            
            <a href="horariosClases.php" class="accion-coach">
                <h3>Horarios de Clases</h3>
                <p>Ver horarios completos de todas tus clases</p>
            </a>
            
            <a href="estadisticas.php" class="accion-coach">
                <h3>Estadísticas</h3>
                <p>Ver estadísticas de tus clases y clientes</p>
            </a>
        </div>
        
        <a href="../main/login.html" class="btn-salir-coach">Cerrar Sesión</a>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

