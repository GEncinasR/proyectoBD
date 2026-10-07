<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';
$sucursal = null;

$from = isset($_GET['from']) ? $_GET['from'] : (isset($_POST['from']) ? $_POST['from'] : '');

if ($from === '' && !empty($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], 'consultarSucursales.php') !== false) {
        $from = 'consultarSucursales';
    } elseif (strpos($_SERVER['HTTP_REFERER'], 'admin/index.php') !== false || strpos($_SERVER['HTTP_REFERER'], 'admin/') !== false) {
        $from = 'admin';
    }
}

$urlVolver = 'index.php';
$textoVolver = '< Volver al Panel';
if ($from === 'consultarSucursales') {
    $urlVolver = 'consultarSucursales.php';
    $textoVolver = '< Volver a Consultar Sucursales';
} elseif ($from === 'editarSucursal') {
    $urlVolver = 'editarSucursal.php';
    $textoVolver = '< Volver a Editar Sucursal';
} elseif ($from === 'admin') {
    $urlVolver = 'index.php';
    $textoVolver = '< Volver al Panel';
}

$idSucursal = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['idSucursal']) ? intval($_POST['idSucursal']) : 0);

if ($idSucursal > 0) {
    $query = "SELECT * FROM Sucursal WHERE idSucursal = $idSucursal";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $sucursal = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "Error: Sucursal no encontrada";
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['eliminar_sucursal']) && $idSucursal > 0) {
    $query = "DELETE FROM Sucursal WHERE idSucursal = $idSucursal";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Sucursal eliminada correctamente";
        $tipoMensaje = 'success';
        $sucursal = null;
        $idSucursal = 0;
    } else {
        $mensaje = "Error al eliminar sucursal: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['actualizar_sucursal']) && $idSucursal > 0) {
    $nombre = mysqli_real_escape_string($con, $_POST['sucursal_nombre']);
    $telefono = mysqli_real_escape_string($con, $_POST['sucursal_telefono']);
    $direccion = mysqli_real_escape_string($con, $_POST['sucursal_direccion']);
    $ciudad = mysqli_real_escape_string($con, $_POST['sucursal_ciudad']);
    $hrApertura = mysqli_real_escape_string($con, $_POST['sucursal_hrApertura']);
    $hrCierre = mysqli_real_escape_string($con, $_POST['sucursal_hrCierre']);
    
    $query = "UPDATE Sucursal SET 
              Nombre = '$nombre',
              Telefono = '$telefono',
              Direccion = '$direccion',
              Ciudad = '$ciudad',
              hrApertura = '$hrApertura',
              hrCierre = '$hrCierre'
              WHERE idSucursal = $idSucursal";
    
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Sucursal actualizada correctamente";
        $tipoMensaje = 'success';
        $query = "SELECT * FROM Sucursal WHERE idSucursal = $idSucursal";
        $resultado = mysqli_query($con, $query);
        if ($resultado) {
            $sucursal = mysqli_fetch_assoc($resultado);
        }
    } else {
        $mensaje = "Error al actualizar sucursal: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idSucursal';
$ordenSQL = "ORDER BY " . $columnaOrdenar;
$sucursales = mysqli_query($con, "SELECT idSucursal, Nombre, Telefono, Direccion, Ciudad, hrApertura, hrCierre 
                                FROM Sucursal 
                                " . $ordenSQL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Sucursal</title>
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
        <?php if ($from === 'consultarSucursales'): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="consultarSucursales.php">Consultar Sucursales</a>
        <?php endif; ?>
        <span class="breadcrumb-separator">/</span>
        <span>Editar Sucursal</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="back-link"><?php echo htmlspecialchars($textoVolver); ?></a>
            <h2>Editar Sucursal</h2>
            
            <?php if (!empty($mensaje) && $idSucursal > 0): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$sucursal): ?>
                <div class="form-group">
                    <label for="buscar_sucursal">Buscar Sucursal por ID</label>
                    <form method="GET" style="display: flex; gap: 10px;">
                        <input type="number" id="buscar_sucursal" name="id" placeholder="Ingrese ID de la sucursal" required min="1">
                        <button type="submit" class="btn-submit">Buscar</button>
                    </form>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3>Lista de Sucursales</h3>
                    <?php if ($sucursales && mysqli_num_rows($sucursales) > 0): ?>
                        <table style="width: 100%; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th data-columna="idSucursal">ID</th>
                                    <th data-columna="Nombre">Nombre</th>
                                    <th data-columna="Telefono">Teléfono</th>
                                    <th data-columna="Ciudad">Ciudad</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($s = mysqli_fetch_assoc($sucursales)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($s['idSucursal']); ?></td>
                                        <td><?php echo htmlspecialchars($s['Nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($s['Telefono']); ?></td>
                                        <td><?php echo htmlspecialchars($s['Ciudad']); ?></td>
                                        <td class="tabla-acciones">
                                            <a href="?id=<?php echo $s['idSucursal']; ?>&from=editarSucursal" class="table-icon-btn-lg">
                                                <img src="../assets/editar.png" alt="Editar sucursal" class="table-icon-icon-lg">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay sucursales registradas.</p>
                    <?php endif; ?>
                </div>
            <?php elseif ($sucursal): ?>
                <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3>Sucursal: <?php echo htmlspecialchars($sucursal['Nombre']); ?></h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($sucursal['idSucursal']); ?></p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="idSucursal" value="<?php echo $sucursal['idSucursal']; ?>">
                    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                    
                    <div class="form-group">
                        <label for="sucursal_nombre">Nombre <span class="required">*</span></label>
                        <input type="text" id="sucursal_nombre" name="sucursal_nombre" 
                               value="<?php echo htmlspecialchars($sucursal['Nombre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sucursal_telefono">Teléfono</label>
                        <input type="text" id="sucursal_telefono" name="sucursal_telefono" 
                               value="<?php echo htmlspecialchars($sucursal['Telefono']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sucursal_direccion">Dirección</label>
                        <input type="text" id="sucursal_direccion" name="sucursal_direccion" 
                               value="<?php echo htmlspecialchars($sucursal['Direccion']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sucursal_ciudad">Ciudad</label>
                        <input type="text" id="sucursal_ciudad" name="sucursal_ciudad" 
                               value="<?php echo htmlspecialchars($sucursal['Ciudad']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="sucursal_hrApertura">Hora Apertura <span class="required">*</span></label>
                        <input type="time" id="sucursal_hrApertura" name="sucursal_hrApertura" 
                               value="<?php echo htmlspecialchars($sucursal['hrApertura']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sucursal_hrCierre">Hora Cierre <span class="required">*</span></label>
                        <input type="time" id="sucursal_hrCierre" name="sucursal_hrCierre" 
                               value="<?php echo htmlspecialchars($sucursal['hrCierre']); ?>" required>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="actualizar_sucursal" class="btn-submit">Actualizar Sucursal</button>
                        <button type="button" onclick="mostrarConfirmacionEliminar()" class="btn-eliminar">Eliminar Sucursal</button>
                        <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="btn-secundario">Cancelar</a>
                    </div>
                </form>
                
                <div id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; text-align: center;">
                        <h3 style="margin-top: 0; color: #dc3545;">¿Estás seguro?</h3>
                        <p>¿Deseas eliminar la sucursal?</p>
                        <p><strong><?php echo htmlspecialchars($sucursal['Nombre']); ?></strong> (ID: <?php echo htmlspecialchars($sucursal['idSucursal']); ?>)</p>
                        <p style="color: #dc3545; font-weight: bold;">Esta acción no se puede deshacer.</p>
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idSucursal" value="<?php echo $sucursal['idSucursal']; ?>">
                                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                                <button type="submit" name="eliminar_sucursal" class="btn-submit btn-eliminar">Sí, eliminar</button>
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

