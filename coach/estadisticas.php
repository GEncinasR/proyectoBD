<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idCoach'])) {
    header('Location: ../main/login.html');
    exit();
}

$idCoach = $_SESSION['idCoach'];
$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$totalClases = mysqli_query($con, "SELECT COUNT(*) as total FROM Clases_particulares WHERE idCoach = $idCoach");
$totalClasesData = mysqli_fetch_array($totalClases);

$totalClientes = mysqli_query($con, "SELECT COUNT(*) as total FROM Cliente WHERE idCoach = $idCoach");
$totalClientesData = mysqli_fetch_array($totalClientes);

$totalHorarios = mysqli_query($con, "SELECT COUNT(*) as total FROM Horario_clase h INNER JOIN Clases_particulares c ON h.idClase = c.idClase WHERE c.idCoach = $idCoach");
$totalHorariosData = mysqli_fetch_array($totalHorarios);

$clasesPorDia = mysqli_query($con, "SELECT h.diaSemana, COUNT(*) as total 
                                    FROM Horario_clase h 
                                    INNER JOIN Clases_particulares c ON h.idClase = c.idClase 
                                    WHERE c.idCoach = $idCoach 
                                    GROUP BY h.diaSemana 
                                    ORDER BY FIELD(h.diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas</title>
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
    
    <nav class="breadcrumb">
        <a href="../main/login.html">Inicio</a>
        <span class="breadcrumb-separator">/</span>
        <a href="index.php">Panel del Coach</a>
        <span class="breadcrumb-separator">/</span>
        <span>Estadísticas</span>
    </nav>
    
    <main class="stats-container">
        <div class="stats-wrapper">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Estadísticas</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total de Clases</h3>
                    <p class="number"><?php echo $totalClasesData['total']; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total de Clientes</h3>
                    <p class="number"><?php echo $totalClientesData['total']; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total de Horarios</h3>
                    <p class="number"><?php echo $totalHorariosData['total']; ?></p>
                </div>
            </div>
            
            <h3>Clases por Día de la Semana</h3>
            <?php if (mysqli_num_rows($clasesPorDia) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Día de la Semana</th>
                        <th>Cantidad de Clases</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($clasesPorDia)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['diaSemana']); ?></td>
                        <td><?php echo htmlspecialchars($reg['total']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p>No hay datos disponibles.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

