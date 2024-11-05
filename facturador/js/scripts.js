// Variables de control para la paginación
let page = 1;
const perPage = 30;
let loading = false;

// Carrito de compras
let carrito = [];

// Función para cargar productos
function cargarProductos() {
    if (loading) return;
    loading = true;
    document.getElementById('loading').style.display = 'block';

    fetch(`cargar_productos.php?page=${page}&per_page=${perPage}`)
        .then(response => response.text())
        .then(data => {
            const container = document.getElementById('productos');
            container.insertAdjacentHTML('beforeend', data);
            page++;
            loading = false;
            document.getElementById('loading').style.display = 'none';
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
            loading = false;
            document.getElementById('loading').style.display = 'none';
        });
}

// Detecta el scroll para cargar más productos
window.addEventListener('scroll', () => {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
        cargarProductos();
    }
});

// Cargar productos iniciales
cargarProductos();

// Función para agregar al carrito
function agregarAlCarrito(id, nombre, precio) {
    const producto = { id, nombre, precio, cantidad: 1 };
    const existe = carrito.find(item => item.id === id);
    if (existe) {
        existe.cantidad++;
    } else {
        carrito.push(producto);
    }
    actualizarCarrito();
}

// Función para eliminar del carrito
function eliminarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    actualizarCarrito();
}

// Función para actualizar el contenido del carrito
function actualizarCarrito() {
    let total = 0;
    let html = '';

    carrito.forEach(item => {
        html += `
            <div class="item-carrito">
                <h3>${item.nombre}</h3>
                <p>Cantidad: ${item.cantidad}</p>
                <p>Precio: $${(item.precio * item.cantidad).toFixed(2)}</p>
                <button class="btn-eliminar" onclick="eliminarDelCarrito(${item.id})">Eliminar</button>
            </div>
        `;
        total += item.precio * item.cantidad;
    });

    document.getElementById('carrito-detalles').innerHTML = html || "<p>El carrito está vacío.</p>";
    document.getElementById('carrito-contenido').innerText = `${carrito.length} productos - $${formatearPrecio(total)}`;
}

function formatearPrecio(precio) {
    return precio.toString().replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Agregar event listeners a las tarjetas de productos
document.addEventListener('click', function(event) {
    if (event.target.closest('.producto')) {
        const productoElement = event.target.closest('.producto');
        const id = parseInt(productoElement.dataset.id);
        const nombre = productoElement.querySelector('h2').innerText;
        const precioStr = productoElement.querySelector('.precio').innerText.replace('$', '').replace('.', '').replace(',', '.');
        const precio = parseFloat(precioStr);

        agregarAlCarrito(id, nombre, precio);
    }
});

// Función para mostrar el modal de pago
function mostrarModalPago() {
    document.getElementById('modal-overlay').style.display = 'block';
    document.getElementById('modal-pago').style.display = 'block';
    document.getElementById('modal-ticket').style.display = 'none';
}

// Función para mostrar el modal de previsualización del ticket
function mostrarModalPrevisualizar() {
    document.getElementById('modal-overlay').style.display = 'block';
    document.getElementById('modal-ticket').style.display = 'block';
    document.getElementById('modal-pago').style.display = 'none';
}

// Función para cerrar el modal
function cerrarModal() {
    document.getElementById('modal-overlay').style.display = 'none';
    document.getElementById('modal-pago').style.display = 'none';
    document.getElementById('modal-ticket').style.display = 'none';
}

// Función para previsualizar el ticket (llama al modal de previsualización)
function previsualizarTicket(pedido) {
    console.log(pedido); // Para verificar qué datos se están pasando

    const metodoPago = pedido.metodo_pago; // Obtener el método de pago del pedido
    let total = 0;
    let html = `
        <img src="https://ddr.com.ar/wp-content/uploads/2023/12/img-logo-1.png" alt="Logo" style="width: 40%; height: auto; margin: 0 auto; display: block;">
        <h3 style="text-align: center;">DDR - Computación</h3>
        <p style="text-align: center;">Teléfono: 2235752058</p>
        <p style="text-align: center;">Santiago del Estero 1581, Mar del Plata-7600, B</p>
        <p>Nº de pedido: #${pedido.id}</p>
        <p>Fecha: ${new Date(pedido.fecha).toLocaleString()}</p>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid #000; padding: 5px;">Artículo</th>
                <th style="border: 1px solid #000; padding: 5px;">Cant.</th>
                <th style="border: 1px solid #000; padding: 5px;">Total</th>
            </tr>`;

    // Iterar sobre los productos del pedido
    pedido.productos.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        html += `
            <tr>
                <td style="border: 1px solid #000; padding: 5px;">${item.nombre}</td>
                <td style="border: 1px solid #000; padding: 5px;">${item.cantidad}</td>
                <td style="border: 1px solid #000; padding: 5px;">$${formatearPrecio(subtotal)}</td>
            </tr>`;
        total += subtotal;
    });

    html += `
        </table>
        <h4 style="text-align: right;">Subtotal: $${formatearPrecio(total)}</h4>
        <h4 style="text-align: right;">Total: $${formatearPrecio(total)}</h4>
        <h4 style="text-align: right;">Método de pago: ${metodoPago}</h4>
        <p style="text-align: center;">GRACIAS POR SU COMPRA!</p>
        <p style="text-align: center;">VISITE NUESTRA WEB: DDR.COM.AR</p>
        <p style="text-align: center;">INSTAGRAM: @DDRCOMPUTACION</p>
        <p style="text-align: center;">GARANTÍA 3 MESES</p>
    `;

    document.getElementById('ticket-preview').innerHTML = html;
    mostrarModalPrevisualizar();
}

// Función para confirmar la compra
function confirmarCompra() {
    const productos = obtenerDetallesCarrito(); // Obtener detalles del carrito
    const metodoPago = document.getElementById('metodo-pago').value;

    // Verificar que haya productos en el carrito y un método de pago seleccionado
    if (productos.length === 0 || !metodoPago) {
        alert('Por favor, añade productos al carrito y selecciona un método de pago.');
        return;
    }

    // Crear el objeto de datos a enviar
    const data = {
        productos: productos,
        metodo_pago: metodoPago
    };

    // Realiza la solicitud al servidor
    fetch('crear_pedido.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirige a la página de pedido completo con el ID del pedido
            window.location.href = data.ticket_url; // Redirige a la URL del ticket
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar el pedido.');
    });
}


// Función para obtener los detalles del carrito
function obtenerDetallesCarrito() {
    return carrito.map(item => {
        return {
            id: item.id,
            nombre: item.nombre,
            cantidad: item.cantidad,
            precio: item.precio
        };
    });
}

function imprimirTicket() {
    const contenido = document.getElementById('ticket-preview').innerHTML;
    const ventanaImpresion = window.open('', '', 'height=600,width=800');
    ventanaImpresion.document.write('<html><head><title>Imprimir Ticket</title>');
    ventanaImpresion.document.write('<style>body{font-family: Arial, sans-serif;}</style>'); // Añadir estilos
    ventanaImpresion.document.write('</head><body>');
    ventanaImpresion.document.write(contenido);
    ventanaImpresion.document.write('</body></html>');
    ventanaImpresion.document.close();
    ventanaImpresion.print();
}

