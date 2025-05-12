<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include "db.php";

if (!isset($_SESSION['id_usuario'])) {
    echo "No autenticado";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_producto = $_POST['id_producto'];

// Verificar que el producto pertenece al usuario
$sql_check = "SELECT * FROM productos WHERE id_producto = $id_producto AND id_usuario = $id_usuario";
$result = $conn->query($sql_check);

if ($result->num_rows === 1) {
    $sql_delete = "DELETE FROM productos WHERE id_producto = $id_producto";
    if ($conn->query($sql_delete)) {
        echo "Producto eliminado.";
    } else {
        echo "Error al eliminar.";
    }
} else {
    echo "No tienes permiso para eliminar este producto.";
}
$conn->close();
?>
