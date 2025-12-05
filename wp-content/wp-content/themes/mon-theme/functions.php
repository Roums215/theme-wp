<?php
/**
 * FUNCTIONS.PHP - Configuration du thème
 * 
 * Ce fichier est automatiquement chargé par WordPress.
 * Il sert à :
 * - Activer les fonctionnalités du thème
 * - Enregistrer les menus et widgets
 * - Charger les styles et scripts
 * - Ajouter des hooks personnalisés
 * 
 * @package MonECommerceTheme
 */

// SÉCURITÉ : Empêche l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ============================================
   1. CONFIGURATION DU THÈME
   ============================================
   
   Cette fonction s'exécute au démarrage du thème
   Elle active toutes les fonctionnalités de base
*/
function mon_theme_setup() {
    
    // WordPress gère automatiquement la balise <title>
    add_theme_support( 'title-tag' );
    
    // Active les images à la une sur les articles/pages
    add_theme_support( 'post-thumbnails' );
    
    // Permet d'ajouter un logo personnalisé dans l'admin
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Active le HTML5 pour certains éléments
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // === SUPPORT WOOCOMMERCE ===
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    
    // === MENUS DE NAVIGATION ===
    // Déclare les emplacements de menu disponibles
    register_nav_menus( array(
        'primary' => 'Menu Principal',  // Menu du header
        'footer'  => 'Menu Footer',     // Menu du pied de page
    ));
}
// HOOK : Exécute cette fonction après le chargement du thème
add_action( 'after_setup_theme', 'mon_theme_setup' );


/* ============================================
   2. CHARGEMENT DES STYLES ET SCRIPTS
   ============================================
   
   wp_enqueue_style()  → charge un fichier CSS
   wp_enqueue_script() → charge un fichier JS
*/
function mon_theme_scripts() {
    
    // Charge le fichier style.css principal
    wp_enqueue_style(
        'mon-theme-style',       // Identifiant unique
        get_stylesheet_uri(),    // = style.css à la racine du thème
        array(),                 // Pas de dépendances
        '1.0.0'                  // Numéro de version
    );
    
    // Charge le JavaScript personnalisé
    wp_enqueue_script(
        'mon-theme-script',
        get_template_directory_uri() . '/assets/js/main.js',
        array( 'jquery' ),  // Dépend de jQuery
        '1.0.0',
        true                // Charger dans le footer (avant </body>)
    );
}
add_action( 'wp_enqueue_scripts', 'mon_theme_scripts' );


/* ============================================
   3. ENREGISTREMENT DES WIDGETS (SIDEBARS)
   ============================================
   
   Les widgets sont des zones où on peut glisser-déposer
   des éléments dans l'admin WordPress
*/
function mon_theme_widgets_init() {
    
    // Sidebar principale
    register_sidebar( array(
        'name'          => 'Sidebar',
        'id'            => 'sidebar-1',
        'description'   => 'Zone de widgets latérale',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Zone footer
    register_sidebar( array(
        'name'          => 'Footer',
        'id'            => 'footer-1',
        'description'   => 'Zone de widgets du pied de page',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
}
add_action( 'widgets_init', 'mon_theme_widgets_init' );


/* ============================================
   4. HOOK PERSONNALISÉ #1 : FILTRE
   ============================================
   
   Un FILTRE modifie une donnée et la retourne
   Ici on modifie la longueur des extraits
*/
function mon_theme_excerpt_length( $length ) {
    return 20;  // 20 mots au lieu de 55 par défaut
}
add_filter( 'excerpt_length', 'mon_theme_excerpt_length' );


/* ============================================
   5. HOOK PERSONNALISÉ #2 : ACTION
   ============================================
   
   Une ACTION exécute du code à un moment précis
   Ici on ajoute un texte avant les articles
*/
function mon_theme_before_article() {
    if ( is_single() ) {
        echo '<p class="reading-info">📖 Temps de lecture : ~3 min</p>';
    }
}
add_action( 'mon_theme_article_start', 'mon_theme_before_article' );


/* ============================================
   6. LARGEUR DU CONTENU
   ============================================
*/
if ( ! isset( $content_width ) ) {
    $content_width = 1200;
}


/* ============================================
   7. CHARGER LES FONCTIONS WOOCOMMERCE
   ============================================
   
   On inclut un fichier séparé pour garder le code organisé
   Ce fichier contient toutes les personnalisations WooCommerce
*/
if ( class_exists( 'WooCommerce' ) ) {
    require get_template_directory() . '/inc/woocommerce-functions.php';
}