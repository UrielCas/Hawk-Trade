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

function eliminarProducto(id) {
  if (confirm("¿Estás seguro de eliminar este producto?")) {
    fetch("eliminar_producto.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "id_producto=" + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(data => {
      alert(data);
      cargarProductos();
    });
  }
}

// Búsqueda por nombre
document.getElementById("buscarNombre").addEventListener("input", function () {
  const nombreBuscado = this.value.toLowerCase();
  const productos = document.querySelectorAll(".producto");

  productos.forEach(prod => {
    const titulo = prod.querySelector("h3").textContent.toLowerCase();
    prod.style.display = titulo.includes(nombreBuscado) ? "block" : "none";
  });
});

// Cargar productos al abrir la página
window.onload = () => cargarProductos();
function eliminarProducto(id) {
  if (confirm("¿Estás seguro de eliminar este producto?")) {
    fetch("eliminar_producto.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "id_producto=" + encodeURIComponent(id)
    })
    .then(res => res.text())
    .then(data => {
      alert(data);
      cargarProductos(); // recarga productos después de eliminar
    });
  }
}















