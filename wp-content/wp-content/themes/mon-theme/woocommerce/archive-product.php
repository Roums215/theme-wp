<?php
/**
 * ARCHIVE-PRODUCT.PHP - Page boutique
 * 
 * Override du template WooCommerce pour la page boutique
 * 
 * Emplacement : /themes/mon-theme-ecommerce/woocommerce/archive-product.php
 * 
 * @package MonECommerceTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// Hook : ouvre le wrapper
do_action( 'woocommerce_before_main_content' );
?>

<header class="woocommerce-products-header">
    <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
    <?php do_action( 'woocommerce_archive_description' ); ?>
</header>

<?php
if ( woocommerce_product_loop() ) {
    
    // Tri et compteur de résultats
    do_action( 'woocommerce_before_shop_loop' );
    
    // Ouvre <ul class="products">
    woocommerce_product_loop_start();
    
    // Boucle des produits
    if ( wc_get_loop_prop( 'total' ) ) {
        while ( have_posts() ) {
            the_post();
            do_action( 'woocommerce_shop_loop' );
            wc_get_template_part( 'content', 'product' );
        }
    }
    
    // Ferme </ul>
    woocommerce_product_loop_end();
    
    // Pagination
    do_action( 'woocommerce_after_shop_loop' );
    
} else {
    do_action( 'woocommerce_no_products_found' );
}

// Hook : ferme le wrapper
do_action( 'woocommerce_after_main_content' );

get_footer();