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
} elseif ($from === 'renovarMembresia') {
    $urlVolver = 'renovarMembresia.php';
    $textoVolver = '< Volver a Editar Membresía';
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
        actualizarEstadoVencimiento($con, $idCliente);
        $resultado = mysqli_query($con, $query);
        $cliente = mysqli_fetch_assoc($resultado);
    } else {
        $mensaje = "Error: Cliente no encontrado";
        $tipoMensaje = 'error';
    }
}

if (isset($_POST['renovar_membresia']) && $idCliente > 0) {
    $nuevoTipo = mysqli_real_escape_string($con, $_POST['nuevo_tipo_membresia']);
    $extenderDesde = isset($_POST['extender_desde']) && $_POST['extender_desde'] === 'actual' ? 'actual' : 'hoy';
    
    if ($extenderDesde === 'actual' && !empty($cliente['fechaVencimiento']) && $cliente['estadoMembresia'] !== 'PROVISIONAL') {
        $fechaBase = $cliente['fechaVencimiento'];
    } else {
        $fechaBase = date('Y-m-d');
    }
    
    $nuevaFechaVencimiento = calcularFechaVencimiento($nuevoTipo, $fechaBase);
    
    if ($nuevoTipo === 'PROVISIONAL') {
        $nuevaFechaVencimiento = null;
    }
    
    $fechaVencimientoSQL = $nuevaFechaVencimiento ? "'$nuevaFechaVencimiento'" : 'NULL';
    
    $query = "UPDATE Cliente SET 
              estadoMembresia = '$nuevoTipo',
              fechaVencimiento = $fechaVencimientoSQL
              WHERE idCliente = $idCliente";
    
    $resultado = mysqli_query($con, $query);
    
    if ($resultado) {
        $mensaje = "Membresía renovada/extendida correctamente";
        $tipoMensaje = 'success';
        $query = "SELECT * FROM Cliente WHERE idCliente = $idCliente";
        $resultado = mysqli_query($con, $query);
        if ($resultado) {
            $cliente = mysqli_fetch_assoc($resultado);
        }
    } else {
        $mensaje = "Error al renovar membresía: " . mysqli_error($con);
        $tipoMensaje = 'error';
    }
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
    <title>Editar Membresía</title>
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
        <span>Editar Membresía</span>
    </nav>
    
    <main class="form-container">
        <div class="form-wrapper">
            <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="back-link"><?php echo htmlspecialchars($textoVolver); ?></a>
            <h2>Editar Membresía</h2>
            
            <?php if (!empty($mensaje)): ?>
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
                                            <a href="?id=<?php echo $c['idCliente']; ?>&from=renovarMembresia" class="table-icon-btn-lg">
                                                <img src="../assets/editar.png" alt="Editar membresía" class="table-icon-icon-lg">
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
            <?php else: ?>
                <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3>Cliente: <?php echo htmlspecialchars($cliente['Nombres'] . ' ' . $cliente['Apellidos']); ?></h3>
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
                        <label for="nuevo_tipo_membresia">Nuevo Tipo de Membresía <span class="required">*</span></label>
                        <select id="nuevo_tipo_membresia" name="nuevo_tipo_membresia" required>
                            <option value="DIA_PRUEBA">Día de Prueba (1 día)</option>
                            <option value="SEMANAL">Semanal (7 días)</option>
                            <option value="MENSUALIDAD">Mensualidad (1 mes)</option>
                            <option value="ANUALIDAD">Anualidad (1 año)</option>
                            <option value="PROVISIONAL">Provisional (sin vencimiento)</option>
                        </select>
                    </div>
                    
                    <?php if ($cliente['fechaVencimiento'] && $cliente['estadoMembresia'] !== 'PROVISIONAL' && $cliente['estadoMembresia'] !== 'VENCIDA'): ?>
                        <div class="form-group">
                            <label>Extender desde:</label>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="radio" name="extender_desde" value="actual" checked>
                                    <span>Desde la fecha actual de vencimiento (<?php echo htmlspecialchars($cliente['fechaVencimiento']); ?>)</span>
                                </label>
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="radio" name="extender_desde" value="hoy">
                                    <span>Desde hoy (<?php echo date('Y-m-d'); ?>)</span>
                                </label>
                            </div>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="extender_desde" value="hoy">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <p><strong>Nueva fecha de vencimiento calculada:</strong> <span id="preview_fecha"></span></p>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" name="renovar_membresia" class="btn-submit">Guardar cambios de membresía</button>
                        <a href="<?php echo htmlspecialchars($urlVolver); ?>" class="btn-secundario">Cancelar</a>
                    </div>
                </form>
                
                <script>
                    function actualizarPreview() {
                        const tipo = document.getElementById('nuevo_tipo_membresia').value;
                        const extenderDesde = document.querySelector('input[name="extender_desde"]:checked')?.value || 'hoy';
                        const fechaBase = extenderDesde === 'actual' ? '<?php echo $cliente['fechaVencimiento']; ?>' : '<?php echo date('Y-m-d'); ?>';
                        
                        const fecha = new Date(fechaBase);
                        let nuevaFecha = new Date(fecha);
                        
                        switch(tipo) {
                            case 'DIA_PRUEBA':
                                nuevaFecha.setDate(fecha.getDate() + 1);
                                break;
                            case 'SEMANAL':
                                nuevaFecha.setDate(fecha.getDate() + 7);
                                break;
                            case 'MENSUALIDAD':
                                nuevaFecha.setMonth(fecha.getMonth() + 1);
                                break;
                            case 'ANUALIDAD':
                                nuevaFecha.setFullYear(fecha.getFullYear() + 1);
                                break;
                            case 'PROVISIONAL':
                                document.getElementById('preview_fecha').textContent = 'Sin vencimiento (Provisional)';
                                return;
                        }
                        
                        if (tipo !== 'PROVISIONAL') {
                            document.getElementById('preview_fecha').textContent = nuevaFecha.toISOString().split('T')[0];
                        }
                    }
                    
                    document.getElementById('nuevo_tipo_membresia').addEventListener('change', actualizarPreview);
                    document.querySelectorAll('input[name="extender_desde"]').forEach(radio => {
                        radio.addEventListener('change', actualizarPreview);
                    });
                    
                    document.addEventListener('DOMContentLoaded', actualizarPreview);
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

