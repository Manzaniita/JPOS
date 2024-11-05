<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Obtener el ID del pedido de la URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) {
        wp_die('Pedido no encontrado.');
    }

    // Aquí puedes mostrar el ticket, con la información del pedido
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ticket de Venta</title>
        <style>
            /* Estilo para el ticket */
            body {
                font-family: Arial, sans-serif;
            }
            .ticket {
                width: 300px;
                margin: 0 auto;
                padding: 10px;
                border: 1px solid #000;
            }
        </style>
    </head>
    <body>

    <div class="ticket">
        <h1>Ticket de Venta</h1>
        <p>ID de Pedido: <?php echo esc_html($order->get_id()); ?></p>
        <p>Fecha: <?php echo esc_html($order->get_date_created()->date('Y-m-d H:i:s')); ?></p>
        <p>Total: <?php echo esc_html($order->get_total()); ?></p>
        <h2>Productos</h2>
        <ul>
            <?php
            foreach ($order->get_items() as $item) {
                echo '<li>' . esc_html($item->get_name()) . ' (x' . esc_html($item->get_quantity()) . ')</li>';
            }
            ?>
        </ul>
    </div>

    <script>
        window.print(); // Imprimir automáticamente el ticket
    </script>
    </body>
    </html>
    <?php
} else {
    wp_die('ID de pedido no válido.');
}
?>
