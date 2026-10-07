<?php
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

$vistaDescriptiva = isset($_GET['descriptiva']) && $_GET['descriptiva'] == '1';
$columnaOrdenarClases = isset($_GET['ordenar_clases']) ? $_GET['ordenar_clases'] : 'idClase';
$columnaOrdenarHorarios = isset($_GET['ordenar_horarios']) ? $_GET['ordenar_horarios'] : 'idHorario';

if ($vistaDescriptiva) {
    if ($columnaOrdenarClases == 'idCoach') {
        $ordenClasesSQL = "ORDER BY co.Nombres, co.Apellidos";
    } elseif ($columnaOrdenarClases == 'idSucursal') {
        $ordenClasesSQL = "ORDER BY s.Nombre";
    } else {
        $ordenClasesSQL = "ORDER BY c." . $columnaOrdenarClases;
    }
    $clases = mysqli_query($con, "SELECT c.*, co.Nombres as CoachNombre, co.Apellidos as CoachApellidos, s.Nombre as SucursalNombre 
                                   FROM Clases_particulares c 
                                   INNER JOIN Coach co ON c.idCoach = co.idCoach 
                                   INNER JOIN Sucursal s ON c.idSucursal = s.idSucursal 
                                   " . $ordenClasesSQL) or die("Problemas con la consulta: " . mysqli_error($con));
    
    if ($columnaOrdenarHorarios == 'idClase') {
        $ordenHorariosSQL = "ORDER BY c.Nombre, FIELD(h.diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
    } else {
        $ordenHorariosSQL = "ORDER BY h." . $columnaOrdenarHorarios . ", FIELD(h.diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
    }
    $horarios = mysqli_query($con, "SELECT h.*, c.Nombre as ClaseNombre 
                                     FROM Horario_clase h 
                                     INNER JOIN Clases_particulares c ON h.idClase = c.idClase 
                                     " . $ordenHorariosSQL) or die("Problemas con la consulta: " . mysqli_error($con));
} else {
    $ordenClasesSQL = "ORDER BY " . $columnaOrdenarClases;
    $clases = mysqli_query($con, "SELECT * FROM Clases_particulares " . $ordenClasesSQL) or die("Problemas con la consulta: " . mysqli_error($con));
    
    if ($columnaOrdenarHorarios == 'idClase') {
        $ordenHorariosSQL = "ORDER BY idClase, FIELD(diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
    } else {
        $ordenHorariosSQL = "ORDER BY " . $columnaOrdenarHorarios . ", FIELD(diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
    }
    $horarios = mysqli_query($con, "SELECT * FROM Horario_clase " . $ordenHorariosSQL) or die("Problemas con la consulta: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Clases</title>
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
        <span>Consultar Clases</span>
    </nav>
    
    <main class="table-container">
        <a href="index.php" class="back-link margin-left">< Volver al Panel</a>
        
        <div class="contenedor-tabla">
            <div class="toggle-container">
                <h2>Clases Particulares</h2>
                <label class="toggle-label">
                    <span>Vista Descriptiva:</span>
                    <input type="checkbox" id="toggleVista" class="toggle-checkbox" <?php echo $vistaDescriptiva ? 'checked' : ''; ?> 
                           onchange="window.location.href='?descriptiva=' + (this.checked ? '1' : '0') + '&ordenar_clases=<?php echo $columnaOrdenarClases; ?>&ordenar_horarios=<?php echo $columnaOrdenarHorarios; ?>'">
                </label>
            </div>
            
            <?php if (mysqli_num_rows($clases) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th data-columna="idClase" data-tabla="clases">ID Clase</th>
                        <th data-columna="Nombre" data-tabla="clases">Nombre</th>
                        <th data-columna="Descripcion" data-tabla="clases">Descripción</th>
                        <th data-columna="Area" data-tabla="clases">Área</th>
                        <th data-columna="idCoach" data-tabla="clases">Coach</th>
                        <th data-columna="idSucursal" data-tabla="clases">Sucursal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($clases)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idClase']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($reg['Area']); ?></td>
                        <td><?php 
                            if ($vistaDescriptiva) {
                                echo htmlspecialchars($reg['CoachNombre'] . ' ' . $reg['CoachApellidos']);
                            } else {
                                echo htmlspecialchars($reg['idCoach']);
                            }
                        ?></td>
                        <td><?php 
                            if ($vistaDescriptiva) {
                                echo htmlspecialchars($reg['SucursalNombre']);
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
                <p>No hay clases registradas.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="contenedor-tabla">
            <h2>Horarios de Clases</h2>
            
            <?php if (mysqli_num_rows($horarios) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th data-columna="idHorario" data-tabla="horarios">ID Horario</th>
                        <th data-columna="idClase" data-tabla="horarios">Clase</th>
                        <th data-columna="diaSemana" data-tabla="horarios">Día de la Semana</th>
                        <th data-columna="horaInicio" data-tabla="horarios">Hora Inicio</th>
                        <th data-columna="horaFin" data-tabla="horarios">Hora Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_array($horarios)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reg['idHorario']); ?></td>
                        <td><?php 
                            if ($vistaDescriptiva) {
                                echo htmlspecialchars($reg['ClaseNombre']);
                            } else {
                                echo htmlspecialchars($reg['idClase']);
                            }
                        ?></td>
                        <td><?php echo htmlspecialchars($reg['diaSemana']); ?></td>
                        <td><?php echo htmlspecialchars($reg['horaInicio']); ?></td>
                        <td><?php echo htmlspecialchars($reg['horaFin']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="no-data">
                <p>No hay horarios registrados.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var headers = document.querySelectorAll('th[data-columna]');
            var urlParams = new URLSearchParams(window.location.search);
            
            headers.forEach(function(header) {
                header.style.cursor = 'pointer';
                
                header.addEventListener('click', function() {
                    var columna = this.getAttribute('data-columna');
                    var tabla = this.getAttribute('data-tabla');
                    
                    if (tabla === 'clases') {
                        urlParams.set('ordenar_clases', columna);
                    } else if (tabla === 'horarios') {
                        urlParams.set('ordenar_horarios', columna);
                    }
                    
                    var nuevaURL = window.location.pathname + '?' + urlParams.toString();
                    window.location.href = nuevaURL;
                });
            });
        });
    </script>
</body>
</html>

<?php
mysqli_close($con);
?>

