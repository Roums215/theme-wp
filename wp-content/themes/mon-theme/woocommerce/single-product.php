<?php
/**
 * SINGLE-PRODUCT.PHP - Page d'un produit
 * 
 * Override du template WooCommerce pour la page produit
 * 
 * Emplacement : /themes/mon-theme-ecommerce/woocommerce/single-product.php
 * 
 * @package MonECommerceTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
    the_post();
?>
    
    <div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
        
        <?php do_action( 'woocommerce_before_single_product' ); ?>
        
        <div class="single-product-content">
            
            <!-- Images du produit -->
            <div class="product-gallery">
                <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
            </div>
            
            <!-- Infos du produit -->
            <div class="product-summary">
                <?php do_action( 'woocommerce_single_product_summary' ); ?>
            </div>
            
        </div>
        
        <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
        
    </div>
    
<?php
endwhile;

do_action( 'woocommerce_after_main_content' );

get_footer();