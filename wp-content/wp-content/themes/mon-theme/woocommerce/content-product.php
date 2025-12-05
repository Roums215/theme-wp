<?php
/**
 * CONTENT-PRODUCT.PHP - Carte produit
 * 
 * Template pour afficher chaque produit dans la grille
 * 
 * Emplacement : /themes/mon-theme-ecommerce/woocommerce/content-product.php
 * 
 * @package MonECommerceTheme
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class( '', $product ); ?>>
    
    <?php
    // Lien vers le produit
    do_action( 'woocommerce_before_shop_loop_item' );
    
    // Image + badges
    do_action( 'woocommerce_before_shop_loop_item_title' );
    
    // Titre
    do_action( 'woocommerce_shop_loop_item_title' );
    
    // Prix + note
    do_action( 'woocommerce_after_shop_loop_item_title' );
    
    // Bouton ajouter au panier
    do_action( 'woocommerce_after_shop_loop_item' );
    ?>
    
</li>