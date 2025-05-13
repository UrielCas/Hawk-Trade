<?php
session_start();
include "db.php";

// Obtener los datos del formulario
$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$descripcion = $_POST['descripcion'];
$categoria = $_POST['categoria'];
$matricula = $_POST['matricula'];
$telefono = $_POST['telefono_vendedor'];

// Validar categoría
$categoriasPermitidas = ['Ropa', 'Electrónica', 'Hogar', 'Otros'];
if (!in_array($categoria, $categoriasPermitidas)) {
    echo "Categoría no válida.";
    exit;
}

// Validar matrícula
if (!preg_match('/^ZS[0-9]{8}$/', $matricula)) {
    echo "Matrícula no válida.";
    exit;
}

// Validar imagen
/////////////////////////////////////////////////////////7

$carpeta_destino = __DIR__ . "/uploads/";

// Crear la carpeta si no existe
if (!file_exists($carpeta_destino)) {
    mkdir($carpeta_destino, 0777, true);
}
////////////////////////////////////////////////////////////


$nombreImagen = "";
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['imagen']['type'], $tiposPermitidos)) {
        echo "Formato de imagen no permitido.";
        exit;
    }

    $nombreOriginal = basename($_FILES['imagen']['name']);
    $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
    $nombreImagen = uniqid("img_") . "." . $extension;

    $directorioDestino = "uploads/" . $nombreImagen;

    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $directorioDestino)) {
        echo "Error al mover la imagen al servidor.";
        exit;
    }
} else {
    echo "Error al subir la imagen.";
    exit;
}

// Validar teléfono
if (!preg_match('/^\d{10}$/', $telefono)) {
    die("El número de teléfono debe tener exactamente 10 dígitos.");
}

// Guardar en base de datos con consulta preparada
$stmt = $conn->prepare("INSERT INTO productos (nombre, precio, descripcion, categoria, imagen, matricula, telefono_vendedor)
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sdsssss", $nombre, $precio, $descripcion, $categoria, $nombreImagen, $matricula, $telefono);

if ($stmt->execute()) {
    echo "Producto guardado correctamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
