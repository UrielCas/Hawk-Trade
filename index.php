<?php
session_start();
if (!isset($_SESSION['matricula'])) {
    header("Location: login.php");
    exit();
}
$matriculaUsuario = $_SESSION['matricula'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Productos - Universidad Veracruzana</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Incluir jsPDF desde un CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>

<header>
    <h1>Universidad Veracruzana</h1>
    <p class="bienvenido">Bienvenido, <span><?php echo htmlspecialchars($matriculaUsuario); ?></span></p>
    <nav>
        <a href="#publicar">Publicar Producto</a>
        <a href="#productos">Filtrar Productos por Categoría</a>
        <a href="logout.php">Cerrar Sesión</a>
    </nav>
</header>

    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .formulario {
            margin-bottom: 20px;
        }
        .producto {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        img {
            max-width: 200px;
            height: auto;
            display: block;
            margin-top: 10px;
        }
        .acciones {
            margin-top: 10px;
        }
        .filtro-categoria {
            margin-bottom: 20px;
        }
    </style>


    <!-- Contenedor para el título dinámico -->
    <h2 id="tituloFormulario" style="display: none;">Publicar Producto</h2>

    <!-- Formulario de Publicar Producto -->
    <form class="formulario" id="formProducto" enctype="multipart/form-data" style="display: none;">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <input type="number" name="precio" placeholder="Precio" required>
        <input type="text" name="descripcion" placeholder="Descripción" required>
        <label>Teléfono de contacto (10 dígitos):</label>
        <input type="text" name="telefono_vendedor" pattern="\d{10}" maxlength="10" required>

        <!-- Select para la categoría -->
        <select name="categoria" required>
            <option value="">Selecciona una categoría</option>
            <option value="Ropa">Ropa</option>
            <option value="Electrónica">Electrónica</option>
            <option value="Hogar">Hogar</option>
            <option value="Otros">Otros</option>
        </select>

        <input type="file" name="imagen" accept="image/*" required>
        <input type="hidden" name="matricula" value="<?php echo htmlspecialchars($matriculaUsuario); ?>">
        <button type="submit">Publicar</button>
    </form>

    <!-- Filtro de categoría -->
    <div class="filtro-categoria" id="filtroCategoriaContainer" style="display: none;">
        <select id="filtroCategoria">
            <option value="">Todas</option>
            <option value="Ropa">Ropa</option>
            <option value="Electrónica">Electrónica</option>
            <option value="Hogar">Hogar</option>
            <option value="Otros">Otros</option>
        </select>
    </div>

    <!-- Contenedor de productos -->
    <div id="lista-productos"></div>

<!-- Modal de compra -->
<div id="modalCompra" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:white; max-width:400px; margin:10% auto; padding:20px; border-radius:10px; position:relative;">
        <h3>Formulario de Compra</h3>
        <form id="formCompra">
            <input type="hidden" id="compraNombreProd">
            <input type="hidden" id="compraPrecioProd">
            <input type="hidden" id="compraMatriculaVendedor">
            <input type="hidden" id="compraTelefonoVendedor"> <!-- Agregado este campo -->

            <label>Tu nombre:</label>
            <input type="text" id="nombreComprador" required><br><br>
            
            <label>Cantidad:</label>
            <input type="number" id="cantidadCompra" min="1" value="1" required><br><br>
            
            <label>Forma de pago:</label><br>
            <label><input type="radio" name="pago" value="Efectivo" checked> Efectivo</label>
            <label><input type="radio" name="pago" value="Transferencia"> Transferencia</label><br><br>
            
            <button type="submit">Generar recibo</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>
        </form>
    </div>
</div>

    <script>

    document.getElementById("formProducto").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("guardar_producto.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            alert(data);
            this.reset();
            cargarProductos();
        });
    });

    // Mostrar el formulario de "Publicar Producto" al hacer clic en el enlace
    document.querySelector('a[href="#publicar"]').addEventListener("click", function (e) {
        e.preventDefault(); // Evitar el comportamiento predeterminado del enlace
        const formulario = document.getElementById("formProducto");
        const tituloFormulario = document.getElementById("tituloFormulario");

        // Alternar la visibilidad del formulario y el título
        const isHidden = formulario.style.display === "none";
        formulario.style.display = isHidden ? "block" : "none";
        tituloFormulario.style.display = isHidden ? "block" : "none";
    });

    // Mostrar/ocultar el filtro de categoría al hacer clic en el enlace
    document.querySelector('a[href="#productos"]').addEventListener("click", function (e) {
        e.preventDefault(); // Evitar el comportamiento predeterminado del enlace
        const filtroCategoriaContainer = document.getElementById("filtroCategoriaContainer");

        // Alternar la visibilidad del filtro
        const isHidden = filtroCategoriaContainer.style.display === "none";
        filtroCategoriaContainer.style.display = isHidden ? "block" : "none";
    });

    // Filtrar por categoría
    document.getElementById("filtroCategoria").addEventListener("change", function () {
        cargarProductos(this.value);
    });

    function cargarProductos(categoria = "") {
        let url = "obtener_productos.php";
        if (categoria) {
            url += "?categoria=" + encodeURIComponent(categoria);
        }

        fetch(url)
            .then(res => res.json())
            .then(productos => {
                const contenedor = document.getElementById("lista-productos");
                contenedor.innerHTML = "";

                productos.forEach(prod => {
                    console.log(prod.matricula); // Verifica si la matrícula tiene un valor válido
                    const div = document.createElement("div");
                    div.className = "producto";
                    div.innerHTML = `
                        <h3>${prod.nombre}</h3>
                        <p><strong>Precio:</strong> $${prod.precio}</p>
                        <p><strong>Descripción:</strong> ${prod.descripcion}</p>
                        <p><strong>Categoría:</strong> ${prod.categoria}</p>
                        ${prod.imagen ? `<img src="uploads/${prod.imagen}" alt="Producto">` : ""}
                        <div class="acciones">
                            ${prod.matricula === "<?php echo $matriculaUsuario; ?>" ? `
                                <button onclick="eliminarProducto(${prod.id})">Eliminar</button>` : `
                                <button onclick="abrirFormularioCompra('${prod.nombre}', ${prod.precio}, '${prod.matricula}', '${prod.telefono_vendedor}')">Comprar</button>
                            `}
                        </div>
                    `;
                    contenedor.appendChild(div);
                });
            });
    }

    // Eliminar producto
    function eliminarProducto(id) {
        if (confirm("¿Estás seguro de que quieres eliminar este producto?")) {
            fetch("eliminar_producto.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + id
            })
            .then(res => res.text())
            .then(data => {
                alert(data);
                cargarProductos();
            });
        }
    }

    window.onload = () => cargarProductos(); // Cargar productos al cargar la página


function abrirFormularioCompra(nombre, precio, matriculaVendedor, telefonoVendedor) {
    document.getElementById("modalCompra").style.display = "block";
    document.getElementById("compraNombreProd").value = nombre;
    document.getElementById("compraPrecioProd").value = precio;
    document.getElementById("compraMatriculaVendedor").value = matriculaVendedor || "No disponible"; // Valor por defecto si es null
    document.getElementById("compraTelefonoVendedor").value = telefonoVendedor || "No disponible"; // Valor por defecto si es null
}


function cerrarModal() {
    document.getElementById("modalCompra").style.display = "none";
}


document.getElementById("formCompra").addEventListener("submit", function(e) {
    e.preventDefault();

    const nombre = document.getElementById("nombreComprador").value;
    const cantidad = parseInt(document.getElementById("cantidadCompra").value);
    const metodoPago = document.querySelector('input[name="pago"]:checked').value;

    const nombreProducto = document.getElementById("compraNombreProd").value;
    const precioUnitario = parseFloat(document.getElementById("compraPrecioProd").value);
    const matriculaVendedor = document.getElementById("compraMatriculaVendedor").value;
    const telefonoVendedor = document.getElementById("compraTelefonoVendedor").value;

    const total = precioUnitario * cantidad;

    cerrarModal();

    // Verificar que jsPDF esté disponible
    if (window.jspdf) {
        const { jsPDF } = window.jspdf;

        const doc = new jsPDF();

        doc.setFontSize(14);
        doc.text("--- RECIBO DE COMPRA ---", 10, 20);
        doc.text(`Producto: ${nombreProducto}`, 10, 30);
        doc.text(`Cantidad: ${cantidad}`, 10, 40);
        doc.text(`Precio unitario: $${precioUnitario.toFixed(2)}`, 10, 50);
        doc.text(`Total a pagar: $${total.toFixed(2)}`, 10, 60);
        doc.text(`Nombre del comprador: ${nombre}`, 10, 80);
        doc.text(`Forma de pago: ${metodoPago}`, 10, 90);
        doc.text("--- DATOS DE CONTACTO DEL VENDEDOR ---", 10, 110);
        doc.text(`Matrícula: ${matriculaVendedor}`, 10, 120);
        doc.text(`Teléfono: ${telefonoVendedor}`, 10, 130);

        // Descargar el PDF
        doc.save("recibo_compra.pdf");
    } else {
        console.error("jsPDF no está disponible.");
    }
});

    </script>

</body>
</html>
