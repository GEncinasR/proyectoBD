<?php
include '../conexion.php';
include '../funciones.php';

$con = mysqli_connect($host, $user, $pw, $db);
if (!$con) {
    die("Problemas con el servidor: " . mysqli_connect_error());
}

$mensaje = '';
$tipoMensaje = '';
$cliente = null;

$from = isset($_GET['from']) ? $_GET['from'] : (isset($_POST['from']) ? $_POST['from'] : '');

if ($from === '' && !empty($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], 'consultarClientes.php') !== false) {
        $from = 'consultarClientes';
    } elseif (strpos($_SERVER['HTTP_REFERER'], 'admin/index.php') !== false || strpos($_SERVER['HTTP_REFERER'], 'admin/') !== false) {
        $from = 'admin';
    }
}

$urlVolver = 'index.php';
$textoVolver = '< Volver al Panel';
if ($from === 'consultarClientes') {
    $urlVolver = 'consultarClientes.php';
    $textoVolver = '< Volver a Consultar Clientes';
} elseif ($from === 'editarCliente') {
    $urlVolver = 'editarCliente.php';
    $textoVolver = '< Volver a Editar Cliente';
} elseif ($from === 'admin') {
    $urlVolver = 'index.php';
    $textoVolver = '< Volver al Panel';
}

$idCliente = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['idCliente']) ? intval($_POST['idCliente']) : 0);

if ($idCliente > 0) {
    $query = "SELECT * FROM Cliente WHERE idCliente = $idCliente";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $cliente = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "Error: Cliente no encontrado";
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['eliminar_cliente']) && $idCliente > 0) {
    $query = "DELETE FROM Cliente WHERE idCliente = $idCliente";
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Cliente eliminado correctamente";
        $tipoMensaje = 'success';
        $cliente = null;
        $idCliente = 0;
    } else {
        $mensaje = "Error al eliminar cliente: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['actualizar_cliente']) && $idCliente > 0) {
    $nombres = mysqli_real_escape_string($con, $_POST['cliente_nombres']);
    $apellidos = mysqli_real_escape_string($con, $_POST['cliente_apellidos']);
    $correo = mysqli_real_escape_string($con, $_POST['cliente_correo']);
    $telefono = mysqli_real_escape_string($con, $_POST['cliente_telefono']);
    $fechaNac = !empty($_POST['cliente_fechaNac']) ? "'" . mysqli_real_escape_string($con, $_POST['cliente_fechaNac']) . "'" : 'NULL';
    $idCoach = !empty($_POST['cliente_idCoach']) ? intval($_POST['cliente_idCoach']) : 'NULL';
    $fechaAsignacion = !empty($_POST['cliente_idCoach']) ? "'" . date('Y-m-d') . "'" : 'NULL';
    
    $query = "UPDATE Cliente SET 
              Nombres = '$nombres',
              Apellidos = '$apellidos',
              Correo = '$correo',
              Telefono = '$telefono',
              fechaNacimiento = $fechaNac,
              idCoach = $idCoach,
              fechaAsignacionCoach = $fechaAsignacion
              WHERE idCliente = $idCliente";
    
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Cliente actualizado correctamente";
        $tipoMensaje = 'success';
        $query = "SELECT * FROM Cliente WHERE idCliente = $idCliente";
        $resultado = mysqli_query($con, $query);
        if ($resultado) {
            $cliente = mysqli_fetch_assoc($resultado);
        }
    } else {
        $mensaje = "Error al actualizar cliente: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
}

$coaches = mysqli_query($con, "SELECT idCoach, CONCAT(Nombres, ' ', Apellidos) as nombre FROM Coach ORDER BY Nombres");
if (!$coaches) {
    die("Error al obtener coaches: " . mysqli_error($con));
}

$columnaOrdenar = isset($_GET['ordenar']) ? $_GET['ordenar'] : 'Apellidos';
$ordenSQL = "ORDER BY " . $columnaOrdenar;
$clientes = mysqli_query($con, "SELECT idCliente, Nombres, Apellidos, estadoMembresia, fechaVencimiento 
                                FROM Cliente 
                                " . $ordenSQL);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
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
        <?php if ($from === 'consultarClientes'): ?>
            <span class="breadcrumb-separator">/</span>
            <a href="consultarClientes.php">Consultar Clientes</a>
        <?php endif; ?>
        <span class="breadcrumb-separator">/</span>
        <span>Editar Cliente</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="back-link"><?php echo htmlspecialchars($textoVolver); ?></a>
            <h2>Editar Cliente</h2>
            
            <?php if (!empty($mensaje) && $idCliente > 0): ?>
                <div class="mensaje <?php echo $tipoMensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$cliente): ?>
                <div class="form-group">
                    <label for="buscar_cliente">Buscar Cliente por ID</label>
                    <form method="GET" style="display: flex; gap: 10px;">
                        <input type="number" id="buscar_cliente" name="id" placeholder="Ingrese ID del cliente" required min="1">
                        <button type="submit" class="btn-submit">Buscar</button>
                    </form>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3>Lista de Clientes</h3>
                    <?php if ($clientes && mysqli_num_rows($clientes) > 0): ?>
                        <table style="width: 100%; margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th data-columna="idCliente">ID</th>
                                    <th data-columna="Nombres">Nombre</th>
                                    <th data-columna="estadoMembresia">Membresía Actual</th>
                                    <th data-columna="fechaVencimiento">Fecha Vencimiento</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($c = mysqli_fetch_assoc($clientes)): 
                                    $estaVencida = false;
                                    if ($c['fechaVencimiento'] && $c['estadoMembresia'] !== 'PROVISIONAL') {
                                        $estaVencida = estaVencida($c['fechaVencimiento'], $c['estadoMembresia']);
                                    }
                                ?>
                                    <tr style="<?php echo $estaVencida ? 'background-color: #ffebee;' : ''; ?>">
                                        <td><?php echo htmlspecialchars($c['idCliente']); ?></td>
                                        <td><?php echo htmlspecialchars($c['Nombres'] . ' ' . $c['Apellidos']); ?></td>
                                        <td><?php echo nombreMembresia($c['estadoMembresia']); ?></td>
                                        <td><?php echo $c['fechaVencimiento'] ? htmlspecialchars($c['fechaVencimiento']) : 'N/A'; ?></td>
                                        <td class="tabla-acciones">
                                            <a href="?id=<?php echo $c['idCliente']; ?>&from=editarCliente" class="table-icon-btn-lg">
                                                <img src="../assets/editar.png" alt="Editar cliente" class="table-icon-icon-lg">
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No hay clientes registrados.</p>
                    <?php endif; ?>
                </div>
            <?php elseif ($cliente): ?>
                <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3>Cliente: <?php echo htmlspecialchars($cliente['Nombres'] . ' ' . $cliente['Apellidos']); ?></h3>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($cliente['idCliente']); ?></p>
                    <p><strong>Membresía Actual:</strong> <?php echo nombreMembresia($cliente['estadoMembresia']); ?></p>
                    <p><strong>Fecha de Vencimiento:</strong> 
                        <?php 
                        if ($cliente['fechaVencimiento']) {
                            $vencida = estaVencida($cliente['fechaVencimiento'], $cliente['estadoMembresia']);
                            echo htmlspecialchars($cliente['fechaVencimiento']);
                            if ($vencida) {
                                echo ' <span style="color: red; font-weight: bold;">(VENCIDA)</span>';
                            }
                        } else {
                            echo 'N/A (Provisional o sin fecha)';
                        }
                        ?>
                    </p>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="idCliente" value="<?php echo $cliente['idCliente']; ?>">
                    <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                    
                    <div class="form-group">
                        <label for="cliente_nombres">Nombres <span class="required">*</span></label>
                        <input type="text" id="cliente_nombres" name="cliente_nombres" 
                               value="<?php echo htmlspecialchars($cliente['Nombres']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_apellidos">Apellidos <span class="required">*</span></label>
                        <input type="text" id="cliente_apellidos" name="cliente_apellidos" 
                               value="<?php echo htmlspecialchars($cliente['Apellidos']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_correo">Correo <span class="required">*</span></label>
                        <input type="email" id="cliente_correo" name="cliente_correo" 
                               value="<?php echo htmlspecialchars($cliente['Correo']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_telefono">Teléfono</label>
                        <input type="text" id="cliente_telefono" name="cliente_telefono" 
                               value="<?php echo htmlspecialchars($cliente['Telefono']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_fechaNac">Fecha de Nacimiento</label>
                        <input type="date" id="cliente_fechaNac" name="cliente_fechaNac" 
                               value="<?php echo htmlspecialchars($cliente['fechaNacimiento']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_idCoach">Asignar Coach Personal (opcional)</label>
                        <select id="cliente_idCoach" name="cliente_idCoach">
                            <option value="">Sin coach asignado</option>
                            <?php 
                            if ($coaches) {
                                mysqli_data_seek($coaches, 0);
                                while($coach = mysqli_fetch_assoc($coaches)): ?>
                                    <option value="<?php echo $coach['idCoach']; ?>" 
                                            <?php echo ($cliente['idCoach'] == $coach['idCoach']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($coach['nombre']); ?>
                                    </option>
                                <?php endwhile; 
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="actualizar_cliente" class="btn-submit">Actualizar Cliente</button>
                        <button type="button" onclick="mostrarConfirmacionEliminar()" class="btn-eliminar">Eliminar Cliente</button>
                        <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="btn-secundario">Cancelar</a>
                    </div>
                </form>
                
                <div id="modalEliminar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
                    <div style="background: white; padding: 30px; border-radius: 8px; max-width: 400px; text-align: center;">
                        <h3 style="margin-top: 0; color: #dc3545;">¿Estás seguro?</h3>
                        <p>¿Deseas eliminar al cliente?</p>
                        <p><strong><?php echo htmlspecialchars($cliente['Nombres'] . ' ' . $cliente['Apellidos']); ?></strong> (ID: <?php echo htmlspecialchars($cliente['idCliente']); ?>)</p>
                        <p style="color: #dc3545; font-weight: bold;">Esta acción no se puede deshacer.</p>
                        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="idCliente" value="<?php echo $cliente['idCliente']; ?>">
                                <input type="hidden" name="from" value="<?php echo htmlspecialchars($from); ?>">
                                <button type="submit" name="eliminar_cliente" class="btn-submit btn-eliminar">Sí, eliminar</button>
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

