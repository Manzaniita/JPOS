<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Verificar si hay un pedido ID en la URL
if (!isset($_GET['pedido_id'])) {
    echo "No se ha proporcionado un ID de pedido.";
    exit;
}

$pedido_id = intval($_GET['pedido_id']);
$order = wc_get_order($pedido_id);

if (!$order) {
    echo "Pedido no encontrado.";
    exit;
}

// Obtener los detalles del pedido
$productos = $order->get_items();
$total = $order->get_total();
$metodo_pago = $order->get_payment_method_title();

// Formato de fecha
$fecha = $order->get_date_created()->date('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pedido Completo</title>
    <link rel="stylesheet" href="css/punto.css">
    <script src="js/scripts.js" defer></script>
    <style>
        /* Estilos para el ticket */
        #ticket-preview {
            display: none; /* Oculto por defecto */
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <header>
        <h1>Pedido Completo</h1>
    </header>

    <div class="contenedor">
        <h2>Detalles del Pedido</h2>
        <p>Nº de pedido: #<?php echo $pedido_id; ?></p>
        <p>Fecha: <?php echo $fecha; ?></p>
        <p>Método de Pago: <?php echo $metodo_pago; ?></p>
        <h3>Productos Comprados:</h3>
        <table>
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th>Cant.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo $producto->get_name(); ?></td>
                        <td><?php echo $producto->get_quantity(); ?></td>
                        <td>$<?php echo number_format($producto->get_total(), 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4>Total: $<?php echo number_format($total, 2); ?></h4>
        <p>GRACIAS POR SU COMPRA!</p>

        <button onclick="imprimirTicket()">Imprimir Ticket</button>
        <a href="pos.php">Crear Nueva Venta</a>
    </div>

    <!-- Contenido del ticket -->
    <div id="ticket-preview">
        <h3 style="text-align: center;">DDR - Computación</h3>
        <p style="text-align: center;">Teléfono: 2235752058</p>
        <p style="text-align: center;">Santiago del Estero 1581, Mar del Plata-7600, B</p>
        <p>Nº de pedido: #<?php echo $pedido_id; ?></p>
        <p>Fecha: <?php echo $fecha; ?></p>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="border: 1px solid #000; padding: 5px;">Artículo</th>
                <th style="border: 1px solid #000; padding: 5px;">Cant.</th>
                <th style="border: 1px solid #000; padding: 5px;">Total</th>
            </tr>
            <?php foreach ($productos as $producto): ?>
                <tr>
                    <td style="border: 1px solid #000; padding: 5px;"><?php echo $producto->get_name(); ?></td>
                    <td style="border: 1px solid #000; padding: 5px;"><?php echo $producto->get_quantity(); ?></td>
                    <td style="border: 1px solid #000; padding: 5px;">$<?php echo number_format($producto->get_total(), 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <h4 style="text-align: right;">Total: $<?php echo number_format($total, 2); ?></h4>
        <h4 style="text-align: right;">Método de pago: <?php echo $metodo_pago; ?></h4>
        <p style="text-align: center;">GRACIAS POR SU COMPRA!</p>
        <p style="text-align: center;">VISITE NUESTRA WEB: DDR.COM.AR</p>
        <p style="text-align: center;">INSTAGRAM: @DDRCOMPUTACION</p>
        <p style="text-align: center;">GARANTÍA 3 MESES</p>
    </div>

    <script>
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
    </script>
</body>
</html>
