<?php
$servername = "localhost";
$username = "root";
$password = ""; // puede que tu amigo le haya puesto contraseña
$dbname = "tienda";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
