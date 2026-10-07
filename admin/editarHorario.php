<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';
$horario = null;

$from = isset($_GET['from']) ? $_GET['from'] : (isset($_POST['from']) ? $_POST['from'] : '');

if ($from === '' && !empty($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], 'consultarClases.php') !== false) {
        $from = 'consultarClases';
    } elseif (strpos($_SERVER['HTTP_REFERER'], 'admin/index.php') !== false || strpos($_SERVER['HTTP_REFERER'], 'admin/') !== false) {
        $from = 'admin';
    }
}

$urlVolver = 'index.php';
$textoVolver = '< Volver al Panel';
if ($from === 'consultarClases') {
    $urlVolver = 'consultarClases.php';
    $textoVolver = '< Volver a Consultar Clases';
} elseif ($from === 'editarHorario') {
    $urlVolver = 'editarHorario.php';
    $textoVolver = '< Volver a Editar Horario';
} elseif ($from === 'admin') {
    $urlVolver = 'index.php';
    $textoVolver = '< Volver al Panel';
}

$idHorario = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['idHorario']) ? intval($_POST['idHorario']) : 0);

if ($idHorario > 0) {
    $query = "SELECT * FROM Horario_clase WHERE idHorario = $idHorario";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $horario = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "Error: Horario no encontrado";
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['eliminar_horario']) && $idHorario > 0) {
    $query = "DELETE FROM Horario_clase WHERE idHorario = $idHorario";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Horario eliminado correctamente";
        $tipoMensaje = 'success';
        $horario = null;
        $idHorario = 0;
    } else {
        $mensaje = "Error al eliminar horario: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['actualizar_horario']) && $idHorario > 0) {
    $idClase = intval($_POST['horario_idClase']);
    $diaSemana = mysqli_real_escape_string($con, $_POST['horario_diaSemana']);
    $horaInicio = mysqli_real_escape_string($con, $_POST['horario_horaInicio']);
    $horaFin = mysqli_real_escape_string($con, $_POST['horario_horaFin']);
    
    $query = "UPDATE Horario_clase SET 
              idClase = $idClase,
              diaSemana = '$diaSemana',
              horaInicio = '$horaInicio',
              horaFin = '$horaFin'
              WHERE idHorario = $idHorario";
    
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Horario actualizado correctamente";
        $tipoMensaje = 'success';
        $query = "SELECT * FROM Horario_clase WHERE idHorario = $idHorario";
        $resultado = mysqli_query($con, $query);
        if ($resultado) {
            $horario = mysqli_fetch_assoc($resultado);
        }
    } else {
        $mensaje = "Error al actualizar horario: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

$clases = mysqli_query($con, "SELECT idClase, Nombre FROM Clases_particulares ORDER BY Nombre");
if (!$clases) {
    die("Error al obtener clases: " . mysqli_error($con));
}

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idHorario';
$ordenSQL = "ORDER BY " . $columnaOrdenar . ", FIELD(diaSemana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')";
$horarios = mysqli_query($con, "SELECT h.*, c.Nombre as ClaseNombre 
                                FROM Horario_clase h 
                                INNER JOIN Clases_particulares c ON h.idClase = c.idClase 
                                " . $ordenSQL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horario</title>
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
        <?php if ($from === 'consultarClases'): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="consultarClases.php">Consultar Clases</a>
        <?php endif; ?>
        <span class="breadcrumb-separator">/</span>
        <span>Editar Horario</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="back-link"><?php echo htmlspecialchars($textoVolver); ?></a>
            <h2>Editar Horario</h2>
            
            <?php if (!empty($mensaje) && $idHorario > 0): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$horario): ?>
                <div class="form-group">
                    <label for="buscar_horario">Buscar Horario por ID</label>
                    <form method="GET" style="display: flex; gap: 10px;">
                        <input type="number" id="buscar_horario" name="id" placeholder="Ingrese ID del horario" required min="1">
                        <button type="submit" class="btn-submit">Buscar</button>
                    </form>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3>Lista de Horarios</h3>
                    <?php if ($horarios && mysqli_num_rows($horarios) > 0): ?>
                        <table style="width: 100%; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th data-columna="idHorario">ID</th>
                                    <th data-columna="idClase">Clase</th>
                                    <th data-columna="diaSemana">Día de la Semana</th>
                                    <th data-columna="horaInicio">Hora Inicio</th>
                                    <th data-columna="horaFin">Hora Fin</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($h = mysqli_fetch_assoc($horarios)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($h['idHorario']); ?></td>
                                        <td><?php echo htmlspecialchars($h['ClaseNombre']); ?></td>
                                        <td><?php echo htmlspecialchars($h['diaSemana']); ?></td>
                                        <td><?php echo htmlspecialchars($h['horaInicio']); ?></td>
                                        <td><?php echo htmlspecialchars($h['horaFin']); ?></td>
                                        <td class="tabla-acciones">
                                            <a href="?id=<?php echo $h['idHorario']; ?>&from=editarHorario" class="table-icon-btn-lg">
                                                <img src="../assets/editar.png" alt="Editar horario" class="table-icon-icon-lg">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay horarios registrados.</p>
                    <?php endif; ?>
                </div>
            <?php elseif ($horario): ?>
                <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3>Horario ID: <?php echo htmlspecialchars($horario['idHorario']); ?></h3>
                    <p><strong>Clase:</strong> 
                        <?php 
                        $claseQuery = mysqli_query($con, "SELECT Nombre FROM Clases_particulares WHERE idClase = " . $horario['idClase']);
                        if ($claseQuery && $clase = mysqli_fetch_assoc($claseQuery)) {
                            echo htmlspecialchars($clase['Nombre']);
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="idHorario" value="<?php echo $horario['idHorario']; ?>">
                    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                    
                    <div class="form-group">
                        <label for="horario_idClase">Clase <span class="required">*</span></label>
                        <select id="horario_idClase" name="horario_idClase" required>
                            <option value="">Seleccionar clase</option>
                            <?php 
                            if ($clases) {
                                mysqli_data_seek($clases, 0);
                                while($clase = mysqli_fetch_assoc($clases)): ?>
                                    <option value="<?php echo $clase['idClase']; ?>" 
                                            <?php echo ($horario['idClase'] == $clase['idClase']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($clase['Nombre']); ?>
                                    </option>
                                <?php endwhile; 
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="horario_diaSemana">Día de la Semana <span class="required">*</span></label>
                        <select id="horario_diaSemana" name="horario_diaSemana" required>
                            <option value="Lunes" <?php echo ($horario['diaSemana'] == 'Lunes') ? 'selected' : ''; ?>>Lunes</option>
                            <option value="Martes" <?php echo ($horario['diaSemana'] == 'Martes') ? 'selected' : ''; ?>>Martes</option>
                            <option value="Miércoles" <?php echo ($horario['diaSemana'] == 'Miércoles') ? 'selected' : ''; ?>>Miércoles</option>
                            <option value="Jueves" <?php echo ($horario['diaSemana'] == 'Jueves') ? 'selected' : ''; ?>>Jueves</option>
                            <option value="Viernes" <?php echo ($horario['diaSemana'] == 'Viernes') ? 'selected' : ''; ?>>Viernes</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="horario_horaInicio">Hora Inicio <span class="required">*</span></label>
                        <input type="time" id="horario_horaInicio" name="horario_horaInicio" 
                               value="<?php echo htmlspecialchars($horario['horaInicio']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="horario_horaFin">Hora Fin <span class="required">*</span></label>
                        <input type="time" id="horario_horaFin" name="horario_horaFin" 
                               value="<?php echo htmlspecialchars($horario['horaFin']); ?>" required>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="actualizar_horario" class="btn-submit">Actualizar Horario</button>
                        <button type="button" onclick="mostrarConfirmacionEliminar()" class="btn-eliminar">Eliminar Horario</button>
                        <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="btn-secundario">Cancelar</a>
                    </div>
                </form>
                
                <div id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; text-align: center;">
                        <h3 style="margin-top: 0; color: #dc3545;">¿Estás seguro?</h3>
                        <p>¿Deseas eliminar el horario?</p>
                        <p><strong>ID: <?php echo htmlspecialchars($horario['idHorario']); ?></strong></p>
                        <p style="color: #dc3545; font-weight: bold;">Esta acción no se puede deshacer.</p>
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idHorario" value="<?php echo $horario['idHorario']; ?>">
                                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                                <button type="submit" name="eliminar_horario" class="btn-submit btn-eliminar">Sí, eliminar</button>
                            </form>
                            <button type="button" onclick="cerrarConfirmacionEliminar()" class="btn-secundario">Cancelar</button>
                        </div>
                    </div>
                </div>
                
                <script>
                    function mostrarConfirmacionEliminar() {
                        document.getElementById('modalEliminar').style.display = 'flex';
                    }
                    
                    function cerrarConfirmacionEliminar() {
                        document.getElementById('modalEliminar').style.display = 'none';
                    }
                </script>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="../scripts/ordenarTabla.js"></script>
</body>
</html>

<?php
mysqli_close($con);
?>

