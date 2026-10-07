<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idCoach';
$ordenSQL = "ORDER BY " . $columnaOrdenar;
$resultado = mysqli_query($con, "SELECT * FROM Coach " . $ordenSQL) or die("Problemas con la consulta: " . mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Coaches</title>
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
        <span>Consultar Coaches</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Consultar Coaches</h2>
            
            <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th data-columna="idCoach">ID Coach</th>
                        <th data-columna="Nombres">Nombres</th>
                        <th data-columna="Apellidos">Apellidos</th>
                        <th data-columna="Telefono">Teléfono</th>
                        <th data-columna="areadeTrabajo">Área de Trabajo</th>
                        <th data-columna="Salario">Salario</th>
                        <th data-columna="FechadeInicio">Fecha de Inicio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($resultado)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idCoach']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Nombres']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Telefono']); ?></td>
                        <td><?php echo htmlspecialchars($reg['areadeTrabajo']); ?></td>
                        <td>$<?php echo number_format($reg['Salario'], 2); ?></td>
                        <td><?php echo htmlspecialchars($reg['FechadeInicio']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>No hay coaches registrados.</p>
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

