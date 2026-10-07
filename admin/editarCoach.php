<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';
$coach = null;

$from = isset($_GET['from']) ? $_GET['from'] : (isset($_POST['from']) ? $_POST['from'] : '');

if ($from === '' && !empty($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], 'consultarCoaches.php') !== false) {
        $from = 'consultarCoaches';
    } elseif (strpos($_SERVER['HTTP_REFERER'], 'admin/index.php') !== false || strpos($_SERVER['HTTP_REFERER'], 'admin/') !== false) {
        $from = 'admin';
    }
}

$urlVolver = 'index.php';
$textoVolver = '< Volver al Panel';
if ($from === 'consultarCoaches') {
    $urlVolver = 'consultarCoaches.php';
    $textoVolver = '< Volver a Consultar Coaches';
} elseif ($from === 'editarCoach') {
    $urlVolver = 'editarCoach.php';
    $textoVolver = '< Volver a Editar Coach';
} elseif ($from === 'admin') {
    $urlVolver = 'index.php';
    $textoVolver = '< Volver al Panel';
}

$idCoach = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['idCoach']) ? intval($_POST['idCoach']) : 0);

if ($idCoach > 0) {
    $query = "SELECT * FROM Coach WHERE idCoach = $idCoach";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $coach = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "Error: Coach no encontrado";
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['eliminar_coach']) && $idCoach > 0) {
    $query = "DELETE FROM Coach WHERE idCoach = $idCoach";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Coach eliminado correctamente";
        $tipoMensaje = 'success';
        $coach = null;
        $idCoach = 0;
    } else {
        $mensaje = "Error al eliminar coach: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['actualizar_coach']) && $idCoach > 0) {
    $nombres = mysqli_real_escape_string($con, $_POST['coach_nombres']);
    $apellidos = mysqli_real_escape_string($con, $_POST['coach_apellidos']);
    $telefono = mysqli_real_escape_string($con, $_POST['coach_telefono']);
    $area = mysqli_real_escape_string($con, $_POST['coach_area']);
    $salario = !empty($_POST['coach_salario']) ? floatval($_POST['coach_salario']) : 'NULL';
    $fechaInicio = mysqli_real_escape_string($con, $_POST['coach_fechaInicio']);
    
    $salarioSQL = $salario !== 'NULL' ? $salario : 'NULL';
    
    $query = "UPDATE Coach SET 
              Nombres = '$nombres',
              Apellidos = '$apellidos',
              Telefono = '$telefono',
              areadeTrabajo = '$area',
              Salario = $salarioSQL,
              FechadeInicio = '$fechaInicio'
              WHERE idCoach = $idCoach";
    
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Coach actualizado correctamente";
        $tipoMensaje = 'success';
        $query = "SELECT * FROM Coach WHERE idCoach = $idCoach";
        $resultado = mysqli_query($con, $query);
        if ($resultado) {
            $coach = mysqli_fetch_assoc($resultado);
        }
    } else {
        $mensaje = "Error al actualizar coach: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idCoach';
$ordenSQL = "ORDER BY " . $columnaOrdenar;
$coaches = mysqli_query($con, "SELECT idCoach, Nombres, Apellidos, Telefono, areadeTrabajo, Salario, FechadeInicio 
                                FROM Coach 
                                " . $ordenSQL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Coach</title>
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
        <?php if ($from === 'consultarCoaches'): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="consultarCoaches.php">Consultar Coaches</a>
        <?php endif; ?>
        <span class="breadcrumb-separator">/</span>
        <span>Editar Coach</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="back-link"><?php echo htmlspecialchars($textoVolver); ?></a>
            <h2>Editar Coach</h2>
            
            <?php if (!empty($mensaje) && $idCoach > 0): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$coach): ?>
                <div class="form-group">
                    <label for="buscar_coach">Buscar Coach por ID</label>
                    <form method="GET" style="display: flex; gap: 10px;">
                        <input type="number" id="buscar_coach" name="id" placeholder="Ingrese ID del coach" required min="1">
                        <button type="submit" class="btn-submit">Buscar</button>
                    </form>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3>Lista de Coaches</h3>
                    <?php if ($coaches && mysqli_num_rows($coaches) > 0): ?>
                        <table style="width: 100%; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th data-columna="idCoach">ID</th>
                                    <th data-columna="Nombres">Nombre</th>
                                    <th data-columna="Telefono">Teléfono</th>
                                    <th data-columna="areadeTrabajo">Área de Trabajo</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($c = mysqli_fetch_assoc($coaches)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['idCoach']); ?></td>
                                        <td><?php echo htmlspecialchars($c['Nombres'] . ' ' . $c['Apellidos']); ?></td>
                                        <td><?php echo htmlspecialchars($c['Telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($c['areadeTrabajo']); ?></td>
                                        <td class="tabla-acciones">
                                            <a href="?id=<?php echo $c['idCoach']; ?>&from=editarCoach" class="table-icon-btn-lg">
                                                <img src="../assets/editar.png" alt="Editar coach" class="table-icon-icon-lg">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay coaches registrados.</p>
                    <?php endif; ?>
                </div>
            <?php elseif ($coach): ?>
                <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3>Coach: <?php echo htmlspecialchars($coach['Nombres'] . ' ' . $coach['Apellidos']); ?></h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($coach['idCoach']); ?></p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="idCoach" value="<?php echo $coach['idCoach']; ?>">
                    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                    
                    <div class="form-group">
                        <label for="coach_nombres">Nombres <span class="required">*</span></label>
                        <input type="text" id="coach_nombres" name="coach_nombres" 
                               value="<?php echo htmlspecialchars($coach['Nombres']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="coach_apellidos">Apellidos <span class="required">*</span></label>
                        <input type="text" id="coach_apellidos" name="coach_apellidos" 
                               value="<?php echo htmlspecialchars($coach['Apellidos']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="coach_telefono">Teléfono</label>
                        <input type="text" id="coach_telefono" name="coach_telefono" 
                               value="<?php echo htmlspecialchars($coach['Telefono']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="coach_area">Área de Trabajo</label>
                        <input type="text" id="coach_area" name="coach_area" 
                               value="<?php echo htmlspecialchars($coach['areadeTrabajo']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="coach_salario">Salario</label>
                        <input type="number" step="0.01" id="coach_salario" name="coach_salario" 
                               value="<?php echo htmlspecialchars($coach['Salario']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="coach_fechaInicio">Fecha de Inicio <span class="required">*</span></label>
                        <input type="date" id="coach_fechaInicio" name="coach_fechaInicio" 
                               value="<?php echo htmlspecialchars($coach['FechadeInicio']); ?>" required>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="actualizar_coach" class="btn-submit">Actualizar Coach</button>
                        <button type="button" onclick="mostrarConfirmacionEliminar()" class="btn-eliminar">Eliminar Coach</button>
                        <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="btn-secundario">Cancelar</a>
                    </div>
                </form>
                
                <div id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; text-align: center;">
                        <h3 style="margin-top: 0; color: #dc3545;">¿Estás seguro?</h3>
                        <p>¿Deseas eliminar al coach?</p>
                        <p><strong><?php echo htmlspecialchars($coach['Nombres'] . ' ' . $coach['Apellidos']); ?></strong> (ID: <?php echo htmlspecialchars($coach['idCoach']); ?>)</p>
                        <p style="color: #dc3545; font-weight: bold;">Esta acción no se puede deshacer.</p>
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idCoach" value="<?php echo $coach['idCoach']; ?>">
                                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                                <button type="submit" name="eliminar_coach" class="btn-submit btn-eliminar">Sí, eliminar</button>
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

