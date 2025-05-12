<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db.php";

$matricula = $_POST['matricula'];
$contrasena = $_POST['contrasena'];

// Escape de las variables para prevenir inyección SQL
$matricula = $conn->real_escape_string($matricula);

// En PostgreSQL, las comillas simples son para valores de texto
// y las comillas dobles son para nombres de columnas/tablas
$sql = "SELECT * FROM usuarios WHERE matricula = '$matricula'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    
    // Nota: En PostgreSQL, los nombres de columnas son sensibles a mayúsculas/minúsculas
    // Si en la base de datos se llama 'contraseña' con ñ, úsalo así
    if ($contrasena === $usuario['contraseña']) {
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['matricula'] = $usuario['matricula'];

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
