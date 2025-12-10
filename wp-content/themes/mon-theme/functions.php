<?php
/**
 * FUNCTIONS.PHP
 * 
 * @package MonECommerceTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Setup du thème
 */
function mon_theme_setup() {
    // Support de WooCommerce
    add_theme_support( 'woocommerce' );
    
    // Support des images à la une
    add_theme_support( 'post-thumbnails' );
    
    // Support du titre de la page
    add_theme_support( 'title-tag' );
    
    // Support du logo personnalisé
    add_theme_support( 'custom-logo' );
    
    // Menus
    register_nav_menus( array(
        'primary' => 'Menu Principal',
        'footer'  => 'Menu Pied de page',
    ) );
}
add_action( 'after_setup_theme', 'mon_theme_setup' );

/**
 * 2. Chargement des scripts et styles
 */
function mon_theme_scripts() {
    // Style principal (style.css)
    wp_enqueue_style( 'mon-theme-style', get_stylesheet_uri(), array(), '1.0.0' );
    
    // Tailwind (via CDN pour le dev, comme vu dans header.php)
    // Note: Dans header.php vous avez déjà le script CDN, donc pas besoin de l'ajouter ici si c'est hardcodé.
    // Mais c'est mieux de gérer ici. Pour l'instant on laisse header.php gérer le CDN.
}
add_action( 'wp_enqueue_scripts', 'mon_theme_scripts' );

/**
 * 3. Inclusions
 */
// Fonctions WooCommerce personnalisées
if ( class_exists( 'WooCommerce' ) ) {
    require get_template_directory() . '/inc/woocommerce-functions.php';
}

/**
 * 4. Sidebar
 */
function mon_theme_widgets_init() {
    register_sidebar( array(
        'name'          => 'Barre latérale Boutique',
        'id'            => 'sidebar-1',
        'description'   => 'Ajoutez des widgets ici.',
        'before_widget' => '<section id="%1$s" class="widget %2$s mb-8">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title text-xl font-bold text-white mb-4">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'mon_theme_widgets_init' );

/**
 * 5. Critical CSS Fixes (Force White Labels for Checkout)
 */
function mon_theme_critical_css() {
    ?>
    <style>
        /* Force White Labels on Checkout - Critical Overrides */
        .woocommerce form .form-row label,
        .woocommerce-checkout label,
        .woocommerce-checkout .woocommerce-billing-fields__field-wrapper label,
        .woocommerce-checkout .woocommerce-shipping-fields__field-wrapper label,
        .woocommerce-checkout .woocommerce-additional-fields__field-wrapper label,
        .woocommerce-checkout .woocommerce-billing-fields h3,
        .woocommerce-checkout .woocommerce-shipping-fields h3,
        .woocommerce-checkout .woocommerce-additional-fields h3,
        .woocommerce-checkout #order_review_heading {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5); /* Added contrast */
        }
    </style>
    <?php
}
add_action( 'wp_head', 'mon_theme_critical_css', 100 );
