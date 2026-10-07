<?php
session_start();
include '../conexion.php';

$con = mysqli_connect($host, $user, $pw, $db) or die("Problemas con la BD");

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);
    
    if ($username === 'admin' || $username === 'root') {
        header('Location: ../admin/index.php');
        exit();
    }
    
    if (is_numeric($username)) {
        $idCoach = intval($username);
        
        $coachQuery = mysqli_query($con, "SELECT idCoach FROM Coach WHERE idCoach = $idCoach");
        
        if ($coachQuery && mysqli_num_rows($coachQuery) > 0) {
            $_SESSION['idCoach'] = $idCoach;
            header('Location: ../coach/index.php');
            exit();
        } else {
            header('Location: login.html?error=coach_no_encontrado');
            exit();
        }
    } else {
        header('Location: login.html?error=usuario_incorrecto');
        exit();
    }
} else {
    header('Location: login.html');
    exit();
}

mysqli_close($con);
?>

