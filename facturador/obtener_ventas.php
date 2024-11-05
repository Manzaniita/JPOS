<?php
// Incluye el archivo de WordPress para acceder a la base de datos
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Verificar permisos
if (!is_user_logged_in() || !current_user_can('manage_woocommerce')) {
    wp_die('No tienes permiso para acceder a esta página.');
}


// Argumentos para obtener los pedidos completados
$args = array(
    'post_type' => 'shop_order',
    'posts_per_page' => -1, // Obtener todos los pedidos
    'post_status' => 'wc-completed', // Filtra por pedidos completados
);

// Consulta los pedidos
$pedidos = get_posts($args);

echo "<h1>Lista de Ventas</h1>";

// Mostrar el carrito en la parte superior de la página
echo "<div id='carrito-fijo'>";
echo "<p>Carrito: <span id='carrito-contenido'>" . WC()->cart->get_cart_contents_count() . " productos - " . WC()->cart->get_cart_total() . "</span></p>";
echo "<a href='" . esc_url( wc_get_cart_url() ) . "' id='enlace-carrito'>Ir al Carrito</a>"; // Enlace al carrito
echo "</div>";

// Estilo de la tabla
echo "<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}
th {
    background-color: #f2f2f2;
}
</style>";

// Mostrar ventas
if (!empty($pedidos)) {
    echo "<table>";
    echo "<thead><tr><th>ID</th><th>Fecha</th><th>Total</th><th>Cliente</th><th>Estado</th><th>Productos</th></tr></thead>";
    echo "<tbody>";
    foreach ($pedidos as $pedido) {
        $order = wc_get_order($pedido->ID);
        echo "<tr>";
        echo "<td>" . esc_html($order->get_id()) . "</td>";
        echo "<td>" . esc_html($order->get_date_created()->date('Y-m-d H:i:s')) . "</td>";
        echo "<td>" . esc_html($order->get_total()) . "</td>";
        echo "<td>" . esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()) . "</td>";
        echo "<td>" . esc_html($order->get_status()) . "</td>";
        
        // Mostrar productos
        echo "<td>";
        $items = $order->get_items();
        $productos = [];
        foreach ($items as $item) {
            $producto = $item->get_name();
            $cantidad = $item->get_quantity();
            $productos[] = "{$producto} (x{$cantidad})";
        }
        echo implode(", ", $productos);
        echo "</td>";

        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No se encontraron ventas.</p>";
}

// Botón para imprimir
echo "<button onclick='window.print()'>Imprimir Ventas</button>";
?>