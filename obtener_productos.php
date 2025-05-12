<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");

include "db.php";

$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$query = "SELECT id, nombre, precio, descripcion, categoria, imagen, matricula, telefono_vendedor FROM productos";
if (!empty($categoria)) {
    $query .= " WHERE categoria = ?";
}

$stmt = $conn->prepare($query);
if (!empty($categoria)) {
    $stmt->bind_param("s", $categoria);
}
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);
?>


