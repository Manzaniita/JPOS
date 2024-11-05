<?php
// cargar_productos.php
require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

// Variables para paginación
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 30;

$args = array(
    'post_type' => 'product',
    'posts_per_page' => $per_page,
    'paged' => $page,
);

$productos = new WP_Query($args);

if ($productos->have_posts()) {
    while ($productos->have_posts()) {
        $productos->the_post();
        global $product;
        ?>
        <div class="producto" data-id="<?php echo $product->get_id(); ?>">
            <img src="<?php echo wp_get_attachment_url($product->get_image_id()); ?>" alt="<?php the_title(); ?>">
            <h2><?php the_title(); ?></h2>
            <p class="precio"><?php echo number_format($product->get_price(), 2, ',', '.'); ?></p>
            <p>Stock: <?php echo $product->get_stock_quantity(); ?></p>
        </div>
        <?php
    }
    wp_reset_postdata();
} else {
    echo '<p>No hay productos disponibles.</p>';
}
?>