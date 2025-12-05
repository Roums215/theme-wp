<?php
/**
 * WOOCOMMERCE-FUNCTIONS.PHP
 * 
 * Ce fichier contient TOUTES les personnalisations WooCommerce.
 * Il est inclus depuis functions.php
 * 
 * Emplacement : /themes/mon-theme-ecommerce/inc/woocommerce-functions.php
 * 
 * @package MonECommerceTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ============================================
   1. CONFIGURATION DE BASE
   ============================================ */

// Nombre de produits par ligne
add_filter( 'loop_shop_columns', function() {
    return 4;
});

// Nombre de produits par page
add_filter( 'loop_shop_per_page', function() {
    return 12;
});


/* ============================================
   2. WRAPPER PERSONNALISÉ
   ============================================
   
   Remplace les balises par défaut de WooCommerce
   par nos propres balises HTML
*/

// Supprime les wrappers par défaut
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

// Ajoute nos wrappers
add_action( 'woocommerce_before_main_content', 'mon_theme_wc_wrapper_start', 10 );
add_action( 'woocommerce_after_main_content', 'mon_theme_wc_wrapper_end', 10 );

function mon_theme_wc_wrapper_start() {
    echo '<main id="main" class="site-main woocommerce-main"><div class="container">';
}

function mon_theme_wc_wrapper_end() {
    echo '</div></main>';
}


/* ============================================
   3. HOOK #1 : MODIFIER LE BOUTON PANIER
   ============================================
   
   FILTRE : Modifie le texte "Ajouter au panier"
*/
add_filter( 'woocommerce_product_add_to_cart_text', 'mon_theme_bouton_panier' );

function mon_theme_bouton_panier( $text ) {
    return 'Acheter';
}


/* ============================================
   4. HOOK #2 : BADGE "NOUVEAU"
   ============================================
   
   ACTION : Ajoute un badge sur les produits récents
*/
add_action( 'woocommerce_before_shop_loop_item_title', 'mon_theme_badge_nouveau', 10 );

function mon_theme_badge_nouveau() {
    global $product;
    
    // Date de création du produit
    $date_creation = strtotime( $product->get_date_created() );
    
    // Nombre de jours depuis la création
    $jours = ( time() - $date_creation ) / DAY_IN_SECONDS;
    
    // Si moins de 30 jours → affiche le badge
    if ( $jours < 30 ) {
        echo '<span class="badge-new">Nouveau</span>';
    }
}


/* ============================================
   5. MESSAGE LIVRAISON GRATUITE
   ============================================
*/
add_action( 'woocommerce_before_cart', 'mon_theme_message_livraison' );

function mon_theme_message_livraison() {
    $minimum = 50;
    $total   = WC()->cart->get_subtotal();
    
    if ( $total < $minimum ) {
        $reste = $minimum - $total;
        echo '<div class="woocommerce-info">';
        echo 'Plus que ' . wc_price( $reste ) . ' pour la livraison gratuite !';
        echo '</div>';
    } else {
        echo '<div class="woocommerce-message">✓ Livraison gratuite !</div>';
    }
}


/* ============================================
   6. PERSONNALISER LE BREADCRUMB
   ============================================
*/
add_filter( 'woocommerce_breadcrumb_defaults', function() {
    return array(
        'delimiter'   => ' &gt; ',
        'wrap_before' => '<nav class="woocommerce-breadcrumb">',
        'wrap_after'  => '</nav>',
        'before'      => '<span>',
        'after'       => '</span>',
        'home'        => 'Accueil',
    );
});