<?php
/**
 * Plugin Name: Mon Plugin Témoignages
 * Plugin URI: https://trouvetonclavier.fr
 * Description: Ajoute un Custom Post Type "Témoignages" pour afficher les avis clients
 * Version: 1.0.0
 * Author: IONITA Iulian, LEDOUX Johan, BONHOURE Quentin
 * Author URI: https://trouvetonclavier.fr
 * Text Domain: mon-plugin-temoignages
 * 
 * @package MonPluginTemoignages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

function mon_plugin_meta_box_html( $post ) {
    wp_nonce_field( 'mon_plugin_save', 'mon_plugin_nonce' );
    $value = get_post_meta( $post->ID, 'auteur_temoignage', true );
    ?>
    <label for="auteur_temoignage">Nom du client :</label>
    <input type="text" id="auteur_temoignage" name="auteur_temoignage" 
           value="<?php echo esc_attr( $value ); ?>" style="width:100%; margin-top:5px;">
    <?php
}

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
        $html .= '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">';
        
        while ( $query->have_posts() ) {
            $query->the_post();
            $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
            

            $html .= '<div class="bg-brand-surface border border-brand-border rounded-2xl p-8 hover:border-brand-primary transition-all duration-300 group flex flex-col h-full">';
            
            $html .= '<h3 class="text-xl font-bold text-white mb-4 group-hover:text-brand-primary transition-colors"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            
            $html .= '<div class="text-gray-400 mb-6 italic flex-1">"' . wp_trim_words( get_the_content(), 25 ) . '"</div>';
            
            if ( $auteur ) {
                $html .= '<div class="text-sm text-brand-primary font-bold mb-4 uppercase tracking-wider">— ' . esc_html( $auteur ) . '</div>';
            }
            
            $html .= '<a href="' . get_permalink() . '" class="text-sm font-medium text-white hover:text-brand-primary transition-colors mt-auto flex items-center gap-2">Lire la suite <span class="group-hover:translate-x-1 transition-transform">→</span></a>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        wp_reset_postdata();
    }
    
    return $html;
}
add_shortcode( 'afficher_temoignages', 'mon_plugin_shortcode' );

function mon_plugin_wc_hook() {
    if ( function_exists( 'is_shop' ) && is_shop() ) {
        $count = wp_count_posts( 'temoignage' )->publish;
        if ( $count > 0 ) {
            echo '<div class="bg-brand-surface border border-brand-border p-4 rounded-xl mb-6 text-white">';
            echo '🌟 Rejoignez nos ' . $count . ' clients satisfaits !';
            echo '</div>';
        }
    }
}
add_action( 'woocommerce_before_shop_loop', 'mon_plugin_wc_hook', 5 );

function mon_plugin_activate() {
    mon_plugin_register_cpt();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mon_plugin_activate' );

function mon_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mon_plugin_deactivate' );