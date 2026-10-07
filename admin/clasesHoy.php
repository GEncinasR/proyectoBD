<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$vistaDescriptiva = isset($_GET['descriptiva']) && $_GET['descriptiva'] == '1';

$diasLaborables = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes'];
$diaNum = date('w');
$diaActual = isset($diasLaborables[$diaNum]) ? $diasLaborables[$diaNum] : null;

if ($vistaDescriptiva) {
    $query = "SELECT c.idClase, c.Nombre as NombreClase, c.Descripcion, c.Area, 
                     h.horaInicio, h.horaFin, 
                     co.idCoach, co.Nombres as CoachNombre, co.Apellidos as CoachApellidos,
                     s.Nombre as Sucursal
              FROM Clases_particulares c
              INNER JOIN Horario_clase h ON c.idClase = h.idClase
              INNER JOIN Coach co ON c.idCoach = co.idCoach
              INNER JOIN Sucursal s ON c.idSucursal = s.idSucursal
              WHERE h.diaSemana = '$diaActual'
              ORDER BY h.horaInicio";
} else {
    $query = "SELECT c.idClase, c.Nombre as NombreClase, c.Descripcion, c.Area, 
                     h.horaInicio, h.horaFin, c.idCoach, c.idSucursal
              FROM Clases_particulares c
              INNER JOIN Horario_clase h ON c.idClase = h.idClase
              WHERE h.diaSemana = '$diaActual'
              ORDER BY h.horaInicio";
}

$resultado = mysqli_query($con, $query) or die("Problemas con la consulta: " . mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clases de Hoy</title>
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
        <span>Clases de Hoy</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <div class="toggle-container">
                <h2>Clases de Hoy<?php echo $diaActual ? ' (' . $diaActual . ')' : ''; ?></h2>
                <label class="toggle-label">
                    <span>Vista Descriptiva:</span>
                    <input type="checkbox" id="toggleVista" class="toggle-checkbox" <?php echo $vistaDescriptiva ? 'checked' : ''; ?> 
                           onchange="window.location.href='?descriptiva=' + (this.checked ? '1' : '0')">
                </label>
            </div>
            
            <?php if ($diaActual && mysqli_num_rows($resultado) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Clase</th>
                        <th>Nombre Clase</th>
                        <th>Descripción</th>
                        <th>Área</th>
                        <th>Hora Inicio</th>
                        <th>Hora Fin</th>
                        <th>Coach</th>
                        <th>Sucursal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($resultado)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idClase']); ?></td>
                        <td><?php echo htmlspecialchars($reg['NombreClase']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Area']); ?></td>
                        <td><?php echo htmlspecialchars($reg['horaInicio']); ?></td>
                        <td><?php echo htmlspecialchars($reg['horaFin']); ?></td>
                        <td><?php 
                            if ($vistaDescriptiva) {
                                echo htmlspecialchars($reg['CoachNombre'] . ' ' . $reg['CoachApellidos'] . ' (ID: ' . $reg['idCoach'] . ')');
                            } else {
                                echo htmlspecialchars($reg['idCoach']);
                            }
                        ?></td>
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
            <?php elseif (!$diaActual): ?>
            <div class="no-data">
                <p>No hay clases programadas en fines de semana.</p>
            </div>
            <?php else: ?>
            <div class="no-data">
                <p>No hay clases programadas para hoy.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

