<?php

function calcularFechaVencimiento($tipoMembresia, $fechaInicio = null) {
    if ($fechaInicio === null) {
        $fechaInicio = date('Y-m-d');
    }
    
    $fecha = new DateTime($fechaInicio);
    
    switch ($tipoMembresia) {
        case 'DIA_PRUEBA':
            $fecha->modify('+1 day');
            return $fecha->format('Y-m-d');
            
        case 'SEMANAL':
            $fecha->modify('+7 days');
            return $fecha->format('Y-m-d');
            
        case 'MENSUALIDAD':
            $fecha->modify('+1 month');
            return $fecha->format('Y-m-d');
            
        case 'ANUALIDAD':
            $fecha->modify('+1 year');
            return $fecha->format('Y-m-d');
            
        case 'PROVISIONAL':
            return null;
            
        case 'VENCIDA':
            return null;
            
        default:
            return null;
    }
}

function estaVencida($fechaVencimiento, $tipoMembresia) {
    if ($tipoMembresia === 'PROVISIONAL' || $fechaVencimiento === null) {
        return false;
    }
    
    $hoy = new DateTime();
    $vencimiento = new DateTime($fechaVencimiento);
    
    return $hoy > $vencimiento;
}

function nombreMembresia($tipoMembresia) {
    $nombres = [
        'DIA_PRUEBA' => 'Día de Prueba',
        'SEMANAL' => 'Semanal',
        'MENSUALIDAD' => 'Mensualidad',
        'ANUALIDAD' => 'Anualidad',
        'PROVISIONAL' => 'Provisional',
        'VENCIDA' => 'Vencida'
    ];
    
    return isset($nombres[$tipoMembresia]) ? $nombres[$tipoMembresia] : $tipoMembresia;
}

function actualizarEstadoVencimiento($con, $idCliente) {
    $query = "SELECT estadoMembresia, fechaVencimiento FROM Cliente WHERE idCliente = $idCliente";
    $result = mysqli_query($con, $query);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }
    
    $cliente = mysqli_fetch_assoc($result);
    $tipoMembresia = $cliente['estadoMembresia'];
    $fechaVencimiento = $cliente['fechaVencimiento'];
    
    if ($tipoMembresia === 'VENCIDA' || $tipoMembresia === 'PROVISIONAL') {
        return false;
    }
    
    if (estaVencida($fechaVencimiento, $tipoMembresia)) {
        $updateQuery = "UPDATE Cliente SET estadoMembresia = 'VENCIDA' WHERE idCliente = $idCliente";
        return mysqli_query($con, $updateQuery);
    }
    
    return false;
}

