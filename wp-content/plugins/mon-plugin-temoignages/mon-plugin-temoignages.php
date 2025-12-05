<?php
/**
 * Plugin Name: Mon Plugin Témoignages
 * Plugin URI: https://monsite.com
 * Description: Ajoute un Custom Post Type "Témoignages" pour afficher les avis clients
 * Version: 1.0.0
 * Author: Ton Nom
 * Author URI: https://monsite.com
 * Text Domain: mon-plugin-temoignages
 * 
 * ============================================
 * CE PLUGIN CONTIENT :
 * ============================================
 * 1. Un Custom Post Type (CPT) : "Témoignages"
 * 2. Une taxonomie personnalisée : "Types de témoignages"
 * 3. Un champ personnalisé : "Auteur du témoignage"
 * 4. Un shortcode : [afficher_temoignages]
 * 5. Un hook WooCommerce personnalisé
 * 
 * EMPLACEMENT : /plugins/mon-plugin-temoignages/mon-plugin-temoignages.php
 * 
 * @package MonPluginTemoignages
 */

// Sécurité
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/* ============================================
   1. CUSTOM POST TYPE : TÉMOIGNAGES
   ============================================
   
   Un CPT crée un nouveau type de contenu séparé
   des articles et pages classiques
*/
function mon_plugin_register_cpt() {
    
    $labels = array(
        'name'               => 'Témoignages',
        'singular_name'      => 'Témoignage',
        'menu_name'          => 'Témoignages',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter un témoignage',
        'edit_item'          => 'Modifier',
        'new_item'           => 'Nouveau témoignage',
        'view_item'          => 'Voir',
        'search_items'       => 'Rechercher',
        'not_found'          => 'Aucun trouvé',
        'not_found_in_trash' => 'Aucun dans la corbeille',
    );
    
    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-format-quote',
        'supports'      => array( 'title', 'editor', 'thumbnail' ),
        'has_archive'   => true,
        'rewrite'       => array( 'slug' => 'temoignages' ),
        'show_in_rest'  => true,
    );
    
    register_post_type( 'temoignage', $args );
}
add_action( 'init', 'mon_plugin_register_cpt' );


/* ============================================
   2. TAXONOMIE : TYPES DE TÉMOIGNAGES
   ============================================
   
   Permet de classer les témoignages par catégorie
*/
function mon_plugin_register_taxonomy() {
    
    $labels = array(
        'name'          => 'Types de témoignages',
        'singular_name' => 'Type',
        'search_items'  => 'Rechercher',
        'all_items'     => 'Tous les types',
        'edit_item'     => 'Modifier',
        'add_new_item'  => 'Ajouter un type',
    );
    
    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'rewrite'           => array( 'slug' => 'type-temoignage' ),
        'show_in_rest'      => true,
    );
    
    register_taxonomy( 'type_temoignage', 'temoignage', $args );
}
add_action( 'init', 'mon_plugin_register_taxonomy' );


/* ============================================
   3. CHAMP PERSONNALISÉ : AUTEUR
   ============================================
   
   Ajoute un champ "Auteur du témoignage" dans l'éditeur
*/

// Ajoute la meta box
function mon_plugin_add_meta_box() {
    add_meta_box(
        'temoignage_auteur',
        'Auteur du témoignage',
        'mon_plugin_meta_box_html',
        'temoignage',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mon_plugin_add_meta_box' );

// Affiche le champ
function mon_plugin_meta_box_html( $post ) {
    wp_nonce_field( 'mon_plugin_save', 'mon_plugin_nonce' );
    $value = get_post_meta( $post->ID, 'auteur_temoignage', true );
    ?>
    <label for="auteur_temoignage">Nom du client :</label>
    <input type="text" id="auteur_temoignage" name="auteur_temoignage" 
           value="<?php echo esc_attr( $value ); ?>" style="width:100%; margin-top:5px;">
    <?php
}

// Sauvegarde
function mon_plugin_save_meta( $post_id ) {
    
    if ( ! isset( $_POST['mon_plugin_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['mon_plugin_nonce'], 'mon_plugin_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    
    if ( isset( $_POST['auteur_temoignage'] ) ) {
        update_post_meta( 
            $post_id, 
            'auteur_temoignage', 
            sanitize_text_field( $_POST['auteur_temoignage'] ) 
        );
    }
}
add_action( 'save_post', 'mon_plugin_save_meta' );


/* ============================================
   4. SHORTCODE : [afficher_temoignages]
   ============================================
   
   Usage : [afficher_temoignages nombre="3"]
*/
function mon_plugin_shortcode( $atts ) {
    
    $atts = shortcode_atts( array(
        'nombre' => 3,
    ), $atts );
    
    $query = new WP_Query( array(
        'post_type'      => 'temoignage',
        'posts_per_page' => intval( $atts['nombre'] ),
        'post_status'    => 'publish',
    ));
    
    $html = '';
    
    if ( $query->have_posts() ) {
        $html .= '<div class="shortcode-temoignages">';
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
            
            $html .= '<div class="temoignage-item">';
            $html .= '<div class="temoignage-content">"' . get_the_content() . '"</div>';
            if ( $auteur ) {
                $html .= '<div class="temoignage-auteur">— ' . esc_html( $auteur ) . '</div>';
            }
            $html .= '</div>';
        }
        
        $html .= '</div>';
        wp_reset_postdata();
    }
    
    return $html;
}
add_shortcode( 'afficher_temoignages', 'mon_plugin_shortcode' );


/* ============================================
   5. HOOK WOOCOMMERCE
   ============================================
   
   Ajoute un message sur la page boutique
*/
function mon_plugin_wc_hook() {
    if ( function_exists( 'is_shop' ) && is_shop() ) {
        $count = wp_count_posts( 'temoignage' )->publish;
        if ( $count > 0 ) {
            echo '<p style="background:#d4edda; padding:15px; border-radius:5px; margin-bottom:20px;">';
            echo '🌟 Rejoignez nos ' . $count . ' clients satisfaits !';
            echo '</p>';
        }
    }
}
add_action( 'woocommerce_before_shop_loop', 'mon_plugin_wc_hook', 5 );


/* ============================================
   6. STYLES DU SHORTCODE
   ============================================
*/
function mon_plugin_styles() {
    ?>
    <style>
        .shortcode-temoignages {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .temoignage-item {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #0066cc;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .temoignage-content {
            font-style: italic;
            margin-bottom: 10px;
        }
        .temoignage-auteur {
            font-weight: bold;
            color: #0066cc;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'mon_plugin_styles' );


/* ============================================
   7. ACTIVATION / DÉSACTIVATION
   ============================================
*/
function mon_plugin_activate() {
    mon_plugin_register_cpt();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mon_plugin_activate' );

function mon_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mon_plugin_deactivate' );