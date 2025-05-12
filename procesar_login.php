<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db.php";

$matricula = $_POST['matricula'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT * FROM usuarios WHERE matricula = '$matricula'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if ($contrasena === $usuario['contraseña']) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['matricula'] = $usuario['matricula']; // 👉 ESTA LÍNEA ES CLAVE

        header("Location: index.php");
        exit;
    } else {
        echo "⚠️ Contraseña incorrecta.";
    }
} else {
    echo "⚠️ Matrícula no encontrada o error en la consulta.";
}

$conn->close();
?>
