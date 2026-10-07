<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$vistaDescriptiva = isset($_GET['descriptiva']) && $_GET['descriptiva'] == '1';
$claseFiltro = isset($_GET['clase_id']) ? intval($_GET['clase_id']) : 0;
$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'Apellidos';

$clasesFiltro = mysqli_query(
    $con,
    "SELECT idClase, Nombre
     FROM Clases_particulares
     ORDER BY Nombre"
);

$resultado = null;

if ($claseFiltro > 0) {

    if ($vistaDescriptiva) {
        if ($columnaOrdenar == 'idCoach') {
            $ordenSQL = "ORDER BY co.Nombres, co.Apellidos";
        } else {
            $ordenSQL = "ORDER BY c." . $columnaOrdenar;
        }
        $sql = "SELECT c.*, co.Nombres AS CoachNombre, co.Apellidos AS CoachApellidos, cp.Nombre AS ClaseNombre
                FROM Toma t
                JOIN Cliente c ON c.idCliente = t.idCliente
                JOIN Clases_particulares cp ON cp.idClase = t.idClase
                LEFT JOIN Coach co ON co.idCoach = cp.idCoach
                WHERE t.idClase = $claseFiltro
                " . $ordenSQL;
    } else {
        $ordenSQL = "ORDER BY c." . $columnaOrdenar;
        $sql = "SELECT c.*, cp.Nombre AS ClaseNombre, cp.idCoach AS idCoachClase
                FROM Toma t
                JOIN Cliente c ON c.idCliente = t.idCliente
                JOIN Clases_particulares cp ON cp.idClase = t.idClase
                WHERE t.idClase = $claseFiltro
                " . $ordenSQL;
    }

    $resultado = mysqli_query($con, $sql) or die("Problemas con la consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes por Clase</title>
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
        <span>Clientes por Clase</span>
    </nav>
    
    <main class="table-container">
        <div class="contenedor-tabla">
            <a href="index.php" class="back-link">< Volver al Panel</a>
            
            <div class="toggle-container">
                <h2>Clientes por Clase</h2>
                <label class="toggle-label">
                    <span>Vista Descriptiva:</span>
                    <input
                        type="checkbox"
                        id="toggleVista"
                        class="toggle-checkbox"
                        <?php echo $vistaDescriptiva ? 'checked' : ''; ?>
                        onchange="
                            const params = new URLSearchParams(window.location.search);
                            params.set('descriptiva', this.checked ? '1' : '0');
                            window.location.search = params.toString();
                        "
                    >
                </label>
            </div>

            <form method="GET" id="filtroClaseForm" style="max-width:300px; margin-bottom:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label for="clase_id">Seleccionar Clase</label>
                    <select
                        name="clase_id"
                        id="clase_id"
                        onchange="document.getElementById('filtroClaseForm').submit();"
                    >
                        <option value="0">-- Elija una clase --</option>
                        <?php if ($clasesFiltro): ?>
                            <?php while($cl = mysqli_fetch_assoc($clasesFiltro)): ?>
                                <option value="<?php echo $cl['idClase']; ?>"
                                    <?php echo $claseFiltro == $cl['idClase'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cl['Nombre']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <input type="hidden" name="descriptiva" value="<?php echo $vistaDescriptiva ? '1' : '0'; ?>">
            </form>

            <?php if ($claseFiltro === 0): ?>
                <div class="no-data">
                    <p>Seleccione una clase y haga clic en la lupa para ver sus clientes inscritos.</p>
                </div>
            <?php elseif ($resultado && mysqli_num_rows($resultado) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th data-columna="idCliente">ID Cliente</th>
                            <th data-columna="Nombres">Nombres</th>
                            <th data-columna="Apellidos">Apellidos</th>
                            <th data-columna="Correo">Correo</th>
                            <th data-columna="Telefono">Teléfono</th>
                            <th data-columna="estadoMembresia">Tipo de Membresía</th>
                            <th data-columna="fechaVencimiento">Fecha Vencimiento</th>
                            <?php if ($vistaDescriptiva): ?>
                                <th data-columna="idCoach">Coach de la Clase</th>
                            <?php else: ?>
                                <th data-columna="idCoach">ID Coach Clase</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($reg = mysqli_fetch_assoc($resultado)):
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
                            <?php if ($vistaDescriptiva): ?>
                                <td>
                                    <?php 
                                    echo isset($reg['CoachNombre']) && $reg['CoachNombre']
                                        ? htmlspecialchars($reg['CoachNombre'] . ' ' . $reg['CoachApellidos'])
                                        : 'Sin asignar';
                                    ?>
                                </td>
                            <?php else: ?>
                                <td><?php echo htmlspecialchars($reg['idCoachClase'] ? $reg['idCoachClase'] : 'NULL'); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php elseif ($claseFiltro > 0): ?>
                <div class="no-data">
                    <p>No hay clientes inscritos en esta clase.</p>
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


