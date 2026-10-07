<?php
session_start();
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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
    
    <main class="admin-container">
        <div class="admin-header">
            <h1>PANEL DE ADMINISTRACIÓN</h1>
            <p>Sistema de Gestión de Gimnasios</p>
        </div>
        
        <div class="admin-sections">
            <div class="admin-panel">
                <h2>Consultas Generales</h2>
                <div class="admin-acciones">
                    <a href="consultarClientes.php" class="btn-accion">Consultar Clientes</a>
                    <a href="consultarCoaches.php" class="btn-accion">Consultar Coaches</a>
                    <a href="consultarClases.php" class="btn-accion">Consultar Clases</a>
                    <a href="consultarSucursales.php" class="btn-accion">Consultar Sucursales</a>
                    <a href="clasesHoy.php" class="btn-secundario">Clases de Hoy</a>
                </div>

                <h2 style="margin-top:2rem;">Consultas Específicas</h2>
                <div class="admin-acciones">
                    <a href="consultarClientesPorCoach.php" class="btn-accion">Clientes por Coach</a>
                    <a href="consultarClientesPorClase.php" class="btn-accion">Clientes por Clase</a>
                </div>
            </div>
            
            <div class="admin-panel">
                <h2>Agregar Nuevos Registros</h2>
                <div class="admin-acciones">
                    <a href="agregarClientes.php" class="btn-accion">Agregar Clientes</a>
                    <a href="agregarCoaches.php" class="btn-accion">Agregar Coaches</a>
                    <a href="agregarClase.php" class="btn-accion">Agregar Clase</a>
                    <a href="agregarHorario.php" class="btn-accion">Agregar Horario</a>
                    <a href="inscribirCliente.php" class="btn-accion">Inscribir Cliente</a>
                    <a href="agregarSucursales.php" class="btn-accion">Agregar Sucursales</a>
                </div>
            </div>
            
            <div class="admin-panel">
                <h2>Editar Registros</h2>
                <div class="admin-acciones">
                    <a href="editarCliente.php?from=admin" class="btn-accion">Editar Cliente</a>
                    <a href="renovarMembresia.php?from=admin" class="btn-accion">Editar Membresía</a>
                    <a href="editarClase.php?from=admin" class="btn-accion">Editar Clase</a>
                    <a href="editarHorario.php?from=admin" class="btn-accion">Editar Horario</a>
                    <a href="editarCoach.php?from=admin" class="btn-accion">Editar Coach</a>
                    <a href="editarSucursal.php?from=admin" class="btn-accion">Editar Sucursal</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

<?php
mysqli_close($con);
?>

