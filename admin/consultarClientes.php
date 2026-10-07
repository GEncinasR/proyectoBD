<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$vistaDescriptiva = isset($_GET['descriptiva']) && $_GET['descriptiva'] == '1';
$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idCliente';

if ($vistaDescriptiva) {
    if ($columnaOrdenar == 'idCoach') {
        $ordenSQL = "ORDER BY co.Nombres, co.Apellidos";
    } else {
        $ordenSQL = "ORDER BY c." . $columnaOrdenar;
    }
    $resultado = mysqli_query($con, "SELECT c.*, co.Nombres as CoachNombre, co.Apellidos as CoachApellidos 
                                      FROM Cliente c 
                                      LEFT JOIN Coach co ON c.idCoach = co.idCoach 
                                      " . $ordenSQL) or die("Problemas con la consulta: " . mysqli_error($con));
} else {
    $ordenSQL = "ORDER BY " . $columnaOrdenar;
    $resultado = mysqli_query($con, "SELECT * FROM Cliente " . $ordenSQL) or die("Problemas con la consulta: " . mysqli_error($con));
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Clientes</title>
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
        <span>Consultar Clientes</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            <div class="toggle-container">
                <h2>Consultar Clientes</h2>
                <label class="toggle-label">
                    <span>Vista Descriptiva:</span>
                    <input type="checkbox" id="toggleVista" class="toggle-checkbox" <?php echo $vistaDescriptiva ? 'checked' : ''; ?> 
                           onchange="window.location.href='?descriptiva=' + (this.checked ? '1' : '0') + '&ordenar=<?php echo $columnaOrdenar; ?>'">
                </label>
            </div>
            
            <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th data-columna="idCliente">ID Cliente</th>
                        <th data-columna="Nombres">Nombres</th>
                        <th data-columna="Apellidos">Apellidos</th>
                        <th data-columna="Correo">Correo</th>
                        <th data-columna="Telefono">Teléfono</th>
                        <th data-columna="fechaNacimiento">Fecha de Nacimiento</th>
                        <th data-columna="estadoMembresia">Tipo de Membresía</th>
                        <th data-columna="fechaVencimiento">Fecha Vencimiento</th>
                        <th data-columna="idCoach">Coach Asignado</th>
                        <th data-columna="fechaAsignacionCoach">Fecha Asignación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($resultado)): 
                        $vencida = false;
                        if (isset($reg['fechaVencimiento']) && $reg['fechaVencimiento'] && $reg['estadoMembresia'] !== 'PROVISIONAL') {
                            $vencida = estaVencida($reg['fechaVencimiento'], $reg['estadoMembresia']);
                        }
                    ?>
                    <tr style="<?php echo ($vencida || $reg['estadoMembresia'] === 'VENCIDA') ? 'background-color: #ffebee;' : ''; ?>">
                        <td><?php echo htmlspecialchars($reg['idCliente']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Nombres']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Apellidos']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Correo']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Telefono']); ?></td>
                        <td><?php echo htmlspecialchars($reg['fechaNacimiento']); ?></td>
                        <td>
                            <?php 
                            echo nombreMembresia($reg['estadoMembresia']);
                            if ($vencida || $reg['estadoMembresia'] === 'VENCIDA') {
                                echo ' <span style="color: red; font-weight: bold;">(VENCIDA)</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (isset($reg['fechaVencimiento']) && $reg['fechaVencimiento']) {
                                echo htmlspecialchars($reg['fechaVencimiento']);
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td><?php 
                            if ($vistaDescriptiva) {
                                echo $reg['CoachNombre'] ? htmlspecialchars($reg['CoachNombre'] . ' ' . $reg['CoachApellidos']) : 'Sin asignar';
                            } else {
                                echo htmlspecialchars($reg['idCoach'] ? $reg['idCoach'] : 'NULL');
                            }
                        ?></td>
                        <td><?php echo htmlspecialchars($reg['fechaAsignacionCoach']); ?></td>
                        <td class="tabla-acciones">
                            <a href="editarCliente.php?id=<?php echo $reg['idCliente']; ?>&from=consultarClientes" class="cliente-action-btn">
                                <img src="../assets/editar.png" alt="Editar" class="cliente-action-icon">
                            </a>
                            <a href="renovarMembresia.php?id=<?php echo $reg['idCliente']; ?>&from=consultarClientes" class="cliente-action-btn">
                                <img src="../assets/renovar.png" alt="Renovar" class="cliente-action-icon-renovar">
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>No hay clientes registrados.</p>
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

