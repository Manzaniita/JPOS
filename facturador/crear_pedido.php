<?php
// Incluye el archivo de WordPress para acceder a WooCommerce
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Configura el encabezado para recibir JSON
header('Content-Type: application/json');

// Obtiene el cuerpo de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Verifica que los datos estén correctamente recibidos
if (!isset($data['productos']) || !isset($data['metodo_pago'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
    exit;
}

// Obtiene el método de pago
$metodo_pago = sanitize_text_field($data['metodo_pago']);

// Crear un nuevo pedido
$pedido = new WC_Order();
$pedido->set_currency(get_woocommerce_currency()); // Establecer la moneda
$pedido->set_payment_method($metodo_pago); // Establecer el método de pago
$pedido->set_payment_method_title(ucfirst($metodo_pago)); // Título del método de pago

$total = 0; // Inicializa el total del pedido

// Agregar productos al pedido
foreach ($data['productos'] as $producto) {
    // Asegúrate de que el ID del producto sea válido
    $producto_id = intval($producto['id']);
    $cantidad = intval($producto['cantidad']);
    
    if ($cantidad > 0) {
        $prod = wc_get_product($producto_id);
        if ($prod) {
            $item_id = $pedido->add_product($prod, $cantidad); // Añadir producto
            $total += $prod->get_price() * $cantidad; // Sumar al total
        }
    }
}

// Establecer el total del pedido
$pedido->set_total($total); // Establecer el total del pedido
$pedido->calculate_taxes(); // Calcular impuestos (si corresponde)
$pedido->update_status('wc-completed'); // Marcar como completado (puedes cambiar a 'pending' si es necesario)

// Guardar el pedido
$pedido_id = $pedido->save();

// Retornar el ID del pedido o la URL del ticket
echo json_encode(['success' => true, 'ticket_url' => "pedido_completo.php?pedido_id={$pedido->get_id()}"]);
