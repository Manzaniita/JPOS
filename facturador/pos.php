<?php
require 'auth.php'; // Verifica que el usuario sea 'admin'
// Incluye el archivo de WordPress para acceder a WooCommerce
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de Venta - Productos</title>
    <link rel="stylesheet" href="css/punto.css">
    <script src="js/scripts.js" defer></script>
</head>
<body>

<header>
    <h1>Punto de Venta</h1>
    <nav>
        <a href="index.php" style="color: white; margin-right: 20px;">Inicio</a>
        <a href="admin.php" style="color: white;">Admin</a>
    </nav>
</header>

<div class="contenedor">
   <div id="carrito-fijo">
       <h2>Carrito de Compras</h2>
       <div id="carrito-detalles"></div>
       <div id="carrito-contenido">Pagar 0 productos - $0.00</div>
       <button onclick="mostrarModalPago()" id="carrito-pagar">Pagar Carrito</button>
   </div>
   <div class="productos-wrapper">
       <h2>Productos</h2>
       <div id="productos" class="productos-container"></div>
   </div>
</div>

<div id="loading" style="display:none;">Cargando más productos...</div>

<!-- Modal de Selección de Método de Pago -->
<div id="modal-overlay" onclick="cerrarModal()"></div>

<!-- Modal de Selección de Método de Pago -->
<div id="modal-pago">
    <h2>Seleccionar Método de Pago</h2>
    <select id="metodo-pago">
        <option value="">Seleccione un método</option>
        <option value="efectivo">Efectivo</option>
        <option value="transferencia">Transferencia</option>
        <option value="debito">Débito</option>
        <option value="credito">Crédito</option>
    </select>
    <button onclick="previsualizarTicket({
        id: '', // Aquí puedes poner un valor temporal si es necesario
        metodo_pago: document.getElementById('metodo-pago').value,
        fecha: new Date().toISOString(),
        productos: obtenerDetallesCarrito()
    })">Previsualizar Ticket</button>
    <button onclick="cerrarModal()">Cerrar</button>
    <button onclick="confirmarCompra()">Confirmar Pago</button>
</div>

<!-- Modal de Previsualización del Ticket -->
<div id="modal-ticket" style="display: none;">
    <h2>Previsualización del Ticket</h2>
    <div id="ticket-preview"></div>
    <button onclick="cerrarModal()">Cerrar</button>
</div>

</body>
</html>