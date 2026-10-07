<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idCoach'])) {
    header('Location: ../main/login.html');
    exit();
}

$idCoach = $_SESSION['idCoach'];
$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$vistaDescriptiva = isset($_GET['descriptiva']) && $_GET['descriptiva'] == '1';

if ($vistaDescriptiva) {
    $query = "SELECT c.idClase, c.Nombre, c.Descripcion, c.Area, s.Nombre as Sucursal
              FROM Clases_particulares c
              INNER JOIN Sucursal s ON c.idSucursal = s.idSucursal
              WHERE c.idCoach = $idCoach
              ORDER BY c.Nombre";
} else {
    $query = "SELECT idClase, Nombre, Descripcion, Area, idSucursal
              FROM Clases_particulares
              WHERE idCoach = $idCoach
              ORDER BY Nombre";
}

$resultado = mysqli_query($con, $query) or die("Problemas con la consulta: " . mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas mis Clases</title>
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
        <span>Todas mis Clases</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <div class="toggle-container">
                <h2>Todas mis Clases</h2>
                <label class="toggle-label">
                    <span>Vista Descriptiva:</span>
                    <input type="checkbox" id="toggleVista" class="toggle-checkbox" <?php echo $vistaDescriptiva ? 'checked' : ''; ?> 
                           onchange="window.location.href='?descriptiva=' + (this.checked ? '1' : '0')">
                </label>
            </div>
            
            <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Clase</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Sucursal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($resultado)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idClase']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Area']); ?></td>
                        <td><?php 
                            if ($vistaDescriptiva) {
                                echo htmlspecialchars($reg['Sucursal']);
                            } else {
                                echo htmlspecialchars($reg['idSucursal']);
                            }
                        ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>No tienes clases asignadas.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

