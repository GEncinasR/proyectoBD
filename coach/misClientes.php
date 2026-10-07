<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['idCoach'])) {
    header('Location: ../main/login.html');
    exit();
}

$idCoach = $_SESSION['idCoach'];
$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$query = "SELECT idCliente, Nombres, Apellidos, Correo, Telefono, fechaNacimiento, estadoMembresia, fechaAsignacionCoach
          FROM Cliente
          WHERE idCoach = $idCoach
          ORDER BY Apellidos, Nombres";

$resultado = mysqli_query($con, $query) or die("Problemas con la consulta: " . mysqli_error($con));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Clientes</title>
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
        <span>Mis Clientes</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <h2>Mis Clientes Asignados</h2>
            
            <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Cliente</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Fecha de Nacimiento</th>
                        <th>Estado de Membresía</th>
                        <th>Fecha Asignación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($resultado)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idCliente']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Nombres']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Correo']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Telefono']); ?></td>
                        <td><?php echo htmlspecialchars($reg['fechaNacimiento']); ?></td>
                        <td><?php echo htmlspecialchars($reg['estadoMembresia']); ?></td>
                        <td><?php echo htmlspecialchars($reg['fechaAsignacionCoach']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>No tienes clientes asignados.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

