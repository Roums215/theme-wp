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

// Ensure visibility
if ( empty( $product ) ) {
    $product = wc_get_product( get_the_ID() );
}

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class( 'bg-brand-surface border border-brand-border rounded-2xl overflow-hidden group hover:border-brand-primary transition-all duration-300 hover:shadow-[0_0_20px_rgba(99,102,241,0.15)] flex flex-col text-center p-4', $product ); ?>>
    
    <?php
    // Lien vers le produit
    do_action( 'woocommerce_before_shop_loop_item' );
    
    // Image + badges
    echo '<div class="rounded-xl overflow-hidden mb-4 relative aspect-square">';
    do_action( 'woocommerce_before_shop_loop_item_title' );
    echo '</div>';
    
    // Titre
    echo '<h2 class="text-lg font-bold text-white mb-2 group-hover:text-brand-primary transition-colors">';
    do_action( 'woocommerce_shop_loop_item_title' );
    echo '</h2>';
    
    // Prix + note
    echo '<div class="text-brand-accent font-bold text-xl mb-4">';
    do_action( 'woocommerce_after_shop_loop_item_title' );
    echo '</div>';
    
    // Bouton ajouter au panier
    echo '<div class="mt-auto">';
    do_action( 'woocommerce_after_shop_loop_item' );
    echo '</div>';
    ?>
    
</li>