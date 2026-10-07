<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';
$clase = null;

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
} elseif ($from === 'editarClase') {
    $urlVolver = 'editarClase.php';
    $textoVolver = '< Volver a Editar Clase';
} elseif ($from === 'admin') {
    $urlVolver = 'index.php';
    $textoVolver = '< Volver al Panel';
}

$idClase = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['idClase']) ? intval($_POST['idClase']) : 0);

if ($idClase > 0) {
    $query = "SELECT * FROM Clases_particulares WHERE idClase = $idClase";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $clase = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "Error: Clase no encontrada";
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['eliminar_clase']) && $idClase > 0) {
    $query = "DELETE FROM Clases_particulares WHERE idClase = $idClase";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Clase eliminada correctamente";
        $tipoMensaje = 'success';
        $clase = null;
        $idClase = 0;
    } else {
        $mensaje = "Error al eliminar clase: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['actualizar_clase']) && $idClase > 0) {
    $nombre = mysqli_real_escape_string($con, $_POST['clase_nombre']);
    $descripcion = mysqli_real_escape_string($con, $_POST['clase_descripcion']);
    $area = mysqli_real_escape_string($con, $_POST['clase_area']);
    $idCoach = intval($_POST['clase_idCoach']);
    $idSucursal = intval($_POST['clase_idSucursal']);
    
    $query = "UPDATE Clases_particulares SET 
              Nombre = '$nombre',
              Descripcion = '$descripcion',
              Area = '$area',
              idCoach = $idCoach,
              idSucursal = $idSucursal
              WHERE idClase = $idClase";
    
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Clase actualizada correctamente";
        $tipoMensaje = 'success';
        $query = "SELECT * FROM Clases_particulares WHERE idClase = $idClase";
        $resultado = mysqli_query($con, $query);
        if ($resultado) {
            $clase = mysqli_fetch_assoc($resultado);
        }
    } else {
        $mensaje = "Error al actualizar clase: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

$coaches = mysqli_query($con, "SELECT idCoach, CONCAT(Nombres, ' ', Apellidos) as nombre FROM Coach ORDER BY Nombres");
if (!$coaches) {
    die("Error al obtener coaches: " . mysqli_error($con));
}

$sucursales = mysqli_query($con, "SELECT idSucursal, Nombre FROM Sucursal ORDER BY Nombre");
if (!$sucursales) {
    die("Error al obtener sucursales: " . mysqli_error($con));
}

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'idClase';
$ordenSQL = "ORDER BY " . $columnaOrdenar;
$clases = mysqli_query($con, "SELECT idClase, Nombre, Descripcion, Area, idCoach, idSucursal 
                                FROM Clases_particulares 
                                " . $ordenSQL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Clase</title>
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
        <span>Editar Clase</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="back-link"><?php echo htmlspecialchars($textoVolver); ?></a>
            <h2>Editar Clase</h2>
            
            <?php if (!empty($mensaje) && $idClase > 0): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$clase): ?>
                <div class="form-group">
                    <label for="buscar_clase">Buscar Clase por ID</label>
                    <form method="GET" style="display: flex; gap: 10px;">
                        <input type="number" id="buscar_clase" name="id" placeholder="Ingrese ID de la clase" required min="1">
                        <button type="submit" class="btn-submit">Buscar</button>
                    </form>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3>Lista de Clases</h3>
                    <?php if ($clases && mysqli_num_rows($clases) > 0): ?>
                        <table style="width: 100%; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th data-columna="idClase">ID</th>
                                    <th data-columna="Nombre">Nombre</th>
                                    <th data-columna="Descripcion">Descripción</th>
                                    <th data-columna="Area">Área</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($c = mysqli_fetch_assoc($clases)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($c['idClase']); ?></td>
                                        <td><?php echo htmlspecialchars($c['Nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($c['Descripcion']); ?></td>
                                        <td><?php echo htmlspecialchars($c['Area']); ?></td>
                                        <td class="tabla-acciones">
                                            <a href="?id=<?php echo $c['idClase']; ?>&from=editarClase" class="table-icon-btn-lg">
                                                <img src="../assets/editar.png" alt="Editar clase" class="table-icon-icon-lg">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay clases registradas.</p>
                    <?php endif; ?>
                </div>
            <?php elseif ($clase): ?>
                <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3>Clase: <?php echo htmlspecialchars($clase['Nombre']); ?></h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($clase['idClase']); ?></p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="idClase" value="<?php echo $clase['idClase']; ?>">
                    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                    
                    <div class="form-group">
                        <label for="clase_nombre">Nombre de la Clase <span class="required">*</span></label>
                        <input type="text" id="clase_nombre" name="clase_nombre" 
                               value="<?php echo htmlspecialchars($clase['Nombre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="clase_descripcion">Descripción</label>
                        <textarea id="clase_descripcion" name="clase_descripcion" rows="3"><?php echo htmlspecialchars($clase['Descripcion']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="clase_area">Área</label>
                        <input type="text" id="clase_area" name="clase_area" 
                               value="<?php echo htmlspecialchars($clase['Area']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="clase_idCoach">Coach Instructor <span class="required">*</span></label>
                        <select id="clase_idCoach" name="clase_idCoach" required>
                            <option value="">Seleccionar coach</option>
                            <?php 
                            if ($coaches) {
                                mysqli_data_seek($coaches, 0);
                                while($coach = mysqli_fetch_assoc($coaches)): ?>
                                    <option value="<?php echo $coach['idCoach']; ?>" 
                                            <?php echo ($clase['idCoach'] == $coach['idCoach']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($coach['nombre']); ?>
                                    </option>
                                <?php endwhile; 
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="clase_idSucursal">Sucursal <span class="required">*</span></label>
                        <select id="clase_idSucursal" name="clase_idSucursal" required>
                            <option value="">Seleccionar sucursal</option>
                            <?php 
                            if ($sucursales) {
                                mysqli_data_seek($sucursales, 0);
                                while($sucursal = mysqli_fetch_assoc($sucursales)): ?>
                                    <option value="<?php echo $sucursal['idSucursal']; ?>" 
                                            <?php echo ($clase['idSucursal'] == $sucursal['idSucursal']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sucursal['Nombre']); ?>
                                    </option>
                                <?php endwhile; 
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="actualizar_clase" class="btn-submit">Actualizar Clase</button>
                        <button type="button" onclick="mostrarConfirmacionEliminar()" class="btn-eliminar">Eliminar Clase</button>
                        <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="btn-secundario">Cancelar</a>
                    </div>
                </form>
                
                <div id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; text-align: center;">
                        <h3 style="margin-top: 0; color: #dc3545;">¿Estás seguro?</h3>
                        <p>¿Deseas eliminar la clase?</p>
                        <p><strong><?php echo htmlspecialchars($clase['Nombre']); ?></strong> (ID: <?php echo htmlspecialchars($clase['idClase']); ?>)</p>
                        <p style="color: #dc3545; font-weight: bold;">Esta acción no se puede deshacer.</p>
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idClase" value="<?php echo $clase['idClase']; ?>">
                                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                                <button type="submit" name="eliminar_clase" class="btn-submit btn-eliminar">Sí, eliminar</button>
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

