<?php
// Incluye el archivo de WordPress para acceder a la base de datos
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Obtiene la categoría seleccionada por el usuario (si existe)
$categoria_seleccionada = isset($_GET['categoria']) ? absint($_GET['categoria']) : '';
$orden_seleccionado = isset($_GET['orden']) ? sanitize_text_field($_GET['orden']) : '';
$buscar_producto = isset($_GET['buscar']) ? sanitize_text_field($_GET['buscar']) : ''; // Búsqueda por nombre

// Obtiene todas las categorías de productos
$categorias = get_terms(array(
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
));

// Argumentos base para la consulta de productos
$args = array(
    'limit' => -1, // -1 para obtener todos los productos
    'status' => 'publish',
);

// Búsqueda en nombre de productos
if (!empty($buscar_producto)) {
    $args['s'] = $buscar_producto; // Busca en el nombre del producto
}

// Filtra por la categoría seleccionada
if ($categoria_seleccionada) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $categoria_seleccionada,
        ),
    );
}

// Aplica el orden seleccionado
switch ($orden_seleccionado) {
    case 'precio_asc':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = 'ASC';
        break;
    case 'precio_desc':
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = '_price';
        $args['order'] = 'DESC';
        break;
    case 'nombre_asc':
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
        break;
    case 'nombre_desc':
        $args['orderby'] = 'title';
        $args['order'] = 'DESC';
        break;
    default:
        // Orden por defecto
        break;
}

// Consulta los productos según los criterios
$productos = wc_get_products($args);

echo "<h1>Lista de Productos</h1>";

// Mostrar el carrito en la parte superior de la página
echo "<div id='carrito-fijo'>";
echo "<p>Carrito: <span id='carrito-contenido'>" . WC()->cart->get_cart_contents_count() . " productos - " . WC()->cart->get_cart_total() . "</span></p>";
echo "<a href='" . esc_url( wc_get_cart_url() ) . "' id='enlace-carrito'>Ir al Carrito</a>"; // Enlace al carrito
echo "</div>";

// Formulario para seleccionar categoría, orden y búsqueda
echo "<form method='GET' action=''>";
echo "<label for='categoria'>Selecciona una categoría: </label>";
echo "<select name='categoria' id='categoria' onchange='this.form.submit()'>";
echo "<option value=''>Todas las categorías</option>";

foreach ($categorias as $categoria) {
    $selected = ($categoria->term_id == $categoria_seleccionada) ? 'selected' : '';
    echo "<option value='" . esc_attr($categoria->term_id) . "' " . $selected . ">" . esc_html($categoria->name) . "</option>";
}
echo "</select>";

// Menú desplegable para ordenar productos
echo "<label for='orden'>Ordenar por: </label>";
echo "<select name='orden' id='orden' onchange='this.form.submit()'>";
echo "<option value=''>Sin orden</option>";
echo "<option value='nombre_asc' " . ($orden_seleccionado == 'nombre_asc' ? 'selected' : '') . ">Nombre (A-Z)</option>";
echo "<option value='nombre_desc' " . ($orden_seleccionado == 'nombre_desc' ? 'selected' : '') . ">Nombre (Z-A)</option>";
echo "<option value='precio_asc' " . ($orden_seleccionado == 'precio_asc' ? 'selected' : '') . ">Precio (Menor a Mayor)</option>";
echo "<option value='precio_desc' " . ($orden_seleccionado == 'precio_desc' ? 'selected' : '') . ">Precio (Mayor a Menor)</option>";
echo "</select>";

// Campo de búsqueda por nombre de productos
echo "<label for='buscar'>Buscar producto: </label>";
echo "<input type='text' name='buscar' id='buscar' value='" . esc_attr($buscar_producto) . "' />";

// Botón de búsqueda
echo "<button type='submit'>Buscar</button>";
echo "</form>";

// Mostrar productos
if (!empty($productos)) {
    echo "<table>";
    echo "<thead><tr><th>Nº</th><th>Imagen</th><th>SKU</th><th>Producto</th><th>Categoría</th><th>Stock</th><th>Precio Regular</th><th>Precio de Oferta</th><th>Acción</th></tr></thead>";
    echo "<tbody>";
    $contador = 1;
    foreach ($productos as $producto) {
        $categorias_producto = $producto->get_category_ids();
        $categoria_nombres = array_map(function($cat) {
            return get_term($cat)->name;
        }, $categorias_producto);

        // Obtener el enlace de la primera categoría del producto
        $categoria_url = !empty($categorias_producto) ? get_term_link($categorias_producto[0]) : '#'; // URL de la primera categoría (o # si no tiene categorías)

        $categorias_html = implode(', ', $categoria_nombres);

        $sku = esc_html($producto->get_sku());
        $imagen_producto = wp_get_attachment_image_url($producto->get_image_id(), 'full');
        $imagen_html = $imagen_producto ? "<a href='$imagen_producto' target='_blank'><img src='$imagen_producto' class='miniatura' alt='" . esc_attr($producto->get_name()) . "' /></a>" : '';

        $precio_regular = $producto->get_regular_price();
        $precio_oferta = $producto->get_sale_price();

        echo "<tr>";
        echo "<td><a href='" . esc_url(get_permalink($producto->get_id())) . "' class='nombre-producto'>" . esc_html($contador++) . "</a></td>";
        echo "<td>" . $imagen_html . "</td>";
        echo "<td>" . esc_html($sku) . "</td>";
        echo "<td>" . esc_html($producto->get_name()) . "</td>";
        echo "<td><a href='" . esc_url($categoria_url) . "'>$categorias_html</a></td>"; // Redirige al enlace de la categoría
        echo "<td>" . esc_html($producto->get_stock_quantity()) . "</td>";
        echo "<td>";
        if ($precio_regular) {
            echo "<span class='precio-regular'>$" . number_format($precio_regular, 2) . "</span>";
        }
        echo "</td>";
        echo "<td>";
        if ($precio_oferta) {
            echo "<span class='precio-oferta'>$" . number_format($precio_oferta, 2) . "</span>";
        }
        echo "</td>";
        echo "<td><a href='" . esc_url($producto->add_to_cart_url()) . "' class='añadir-carrito'>Añadir al carrito</a></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No se encontraron productos.</p>";
}
?>