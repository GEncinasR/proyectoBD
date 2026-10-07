<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idSucursal';
$ordenSQL = "ORDER BY " . $columnaOrdenar;
$resultado = mysqli_query($con, "SELECT * FROM Sucursal " . $ordenSQL) or die("Problemas con la consulta: " . mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Sucursales</title>
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
        <span>Consultar Sucursales</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Consultar Sucursales</h2>
            
            <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th data-columna="idSucursal">ID Sucursal</th>
                        <th data-columna="Nombre">Nombre</th>
                        <th data-columna="Telefono">Teléfono</th>
                        <th data-columna="Direccion">Dirección</th>
                        <th data-columna="Ciudad">Ciudad</th>
                        <th data-columna="hrApertura">Hora Apertura</th>
                        <th data-columna="hrCierre">Hora Cierre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($resultado)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idSucursal']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Telefono']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Direccion']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Ciudad']); ?></td>
                        <td><?php echo htmlspecialchars($reg['hrApertura']); ?></td>
                        <td><?php echo htmlspecialchars($reg['hrCierre']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>No hay sucursales registradas.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="../scripts/ordenarTabla.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>

