<?php
/**
 * Plugin Name: Avis & Notes Produits
 * Plugin URI: https://www.example.com
 * Description: Système simple d'avis et de notes pour les produits WooCommerce. Les clients peuvent noter et commenter les produits.
 * Version: 1.0.0
 * Author: Votre Nom (Groupe X)
 * Author URI: https://www.example.com
 * License: GPL v2 or later
 * Text Domain: product-reviews
 * Domain Path: /languages
 */

// SÉCURITÉ : Empêcher l'accès direct au fichier
// Si quelqu'un essaie d'ouvrir ce fichier directement dans le navigateur, on arrête tout
if (!defined('ABSPATH')) {
    exit; // Sortir si accès direct
}

/**
 * ============================================
 * CLASSE PRINCIPALE DU PLUGIN
 * ============================================
 * 
 * Cette classe gère tout le plugin
 * C'est comme le "cerveau" du plugin
 */
class Product_Reviews_Plugin {
    
    /**
     * Version du plugin
     * Utile pour le cache des CSS/JS
     */
    const VERSION = '1.0.0';
    
    /**
     * CONSTRUCTEUR
     * Cette fonction se lance automatiquement quand on crée l'objet
     * C'est le point de départ de tout le plugin
     */
    public function __construct() {
        // Étape 1 : Définir les constantes (chemins du plugin)
        $this->define_constants();
        
        // Étape 2 : Charger les autres fichiers PHP
        $this->load_files();
        
        // Étape 3 : Accrocher nos fonctions aux hooks WordPress
        $this->init_hooks();
    }
    
    /**
     * DÉFINIR LES CONSTANTES
     * Les constantes = des variables qui ne changent jamais
     * Elles stockent les chemins importants du plugin
     */
    private function define_constants() {
        // Chemin complet vers le dossier du plugin
        // Exemple : /var/www/html/wp-content/plugins/product-reviews/
        define('PR_PLUGIN_DIR', plugin_dir_path(__FILE__));
        
        // URL complète vers le dossier du plugin
        // Exemple : http://localhost:8000/wp-content/plugins/product-reviews/
        define('PR_PLUGIN_URL', plugin_dir_url(__FILE__));
        
        // Nom du fichier principal
        // Exemple : product-reviews/product-reviews.php
        define('PR_PLUGIN_BASENAME', plugin_basename(__FILE__));
    }
    
    /**
     * CHARGER LES FICHIERS
     * On inclut les autres fichiers PHP dont on a besoin
     */
    private function load_files() {
        // Charger la classe qui gère les avis
        require_once PR_PLUGIN_DIR . 'includes/class-review.php';
        
        // Si on est dans l'administration WordPress
        if (is_admin()) {
            // Charger la page d'administration
            require_once PR_PLUGIN_DIR . 'includes/admin-page.php';
        }
    }
    
    /**
     * INITIALISER LES HOOKS
     * Les hooks = des points d'accroche où on dit à WordPress :
     * "À ce moment précis, exécute ma fonction !"
     */
    private function init_hooks() {
        // HOOK D'ACTIVATION
        // Quand on active le plugin dans l'admin
        register_activation_hook(__FILE__, array($this, 'activate'));
        
        // HOOK DE DÉSACTIVATION
        // Quand on désactive le plugin dans l'admin
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // HOOK : Charger les traductions
        // 'plugins_loaded' = quand WordPress a fini de charger les plugins
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // HOOK : Charger les CSS et JS de l'admin
        // 'admin_enqueue_scripts' = moment où WordPress charge les styles/scripts de l'admin
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        
        // HOOK : Charger les CSS et JS du site public
        // 'wp_enqueue_scripts' = moment où WordPress charge les styles/scripts du site
        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        
        // HOOK : Enregistrer le shortcode
        // 'init' = au démarrage de WordPress
        add_action('init', array($this, 'register_shortcode'));
        
        // HOOK : Afficher les notes sur les produits
        // Hook spécifique à WooCommerce : après le titre du produit
        add_action('woocommerce_after_shop_loop_item_title', array($this, 'display_rating'), 5);
        
        // HOOK : Ajouter le formulaire d'avis sur la page produit
        add_action('woocommerce_after_single_product_summary', array($this, 'display_review_form'), 15);
    }
    
    /**
     * ACTIVATION DU PLUGIN
     * Cette fonction se lance UNE SEULE FOIS quand on active le plugin
     */
    public function activate() {
        // Créer une table dans la base de données pour stocker les avis
        $this->create_database_table();
        
        // Message de confirmation (optionnel)
        // On pourrait ajouter un message "Plugin activé avec succès"
    }
    
    /**
     * CRÉER LA TABLE EN BASE DE DONNÉES
     * On crée une table pour stocker tous les avis des clients
     */
    private function create_database_table() {
        // $wpdb = objet global de WordPress pour la base de données
        global $wpdb;
        
        // Nom de notre table
        // Si le préfixe WordPress est "wp_", notre table sera "wp_product_reviews"
        $table_name = $wpdb->prefix . 'product_reviews';
        
        // Encodage de la base de données (utf8mb4 pour supporter les emojis !)
        $charset_collate = $wpdb->get_charset_collate();
        
        // SQL pour créer la table
        // C'est comme créer un tableau Excel avec des colonnes
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            product_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            author_name varchar(100) NOT NULL,
            author_email varchar(100) NOT NULL,
            rating int(1) NOT NULL,
            review_text text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            approved tinyint(1) DEFAULT 0,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        // Explication de chaque colonne :
        // - id : numéro unique de l'avis (auto-incrémenté)
        // - product_id : ID du produit concerné
        // - user_id : ID de l'utilisateur (NULL si client non connecté)
        // - author_name : nom de la personne qui laisse l'avis
        // - author_email : email de la personne
        // - rating : note de 1 à 5 étoiles
        // - review_text : le texte de l'avis
        // - created_at : date de création automatique
        // - approved : 1 si approuvé, 0 si en attente
        
        // Fonction WordPress pour exécuter le SQL
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql); // dbDelta est une fonction intelligente qui crée ou met à jour la table
    }
    
    /**
     * DÉSACTIVATION DU PLUGIN
     * Cette fonction se lance quand on désactive le plugin
     */
    public function deactivate() {
        // Note : On ne supprime PAS la table ici
        // Si on supprime les données, les clients perdent tous leurs avis !
        // La suppression se fait uniquement si on DÉSINSTALLE complètement le plugin
        
        // On pourrait ajouter un nettoyage du cache ici
        wp_cache_flush();
    }
    
    /**
     * CHARGER LES TRADUCTIONS
     * Pour que le plugin soit disponible en plusieurs langues
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'product-reviews',                          // Text domain
            false,                                       // Deprecated
            dirname(PR_PLUGIN_BASENAME) . '/languages/' // Dossier des traductions
        );
    }
    
    /**
     * CHARGER LES SCRIPTS ADMIN
     * CSS et JavaScript pour la partie administration
     */
    public function admin_scripts($hook) {
        // $hook = la page actuelle de l'admin
        // Exemple : "toplevel_page_product-reviews"
        
        // On charge nos fichiers UNIQUEMENT sur notre page d'admin
        // Cela évite de ralentir les autres pages
        if ($hook !== 'toplevel_page_product-reviews') {
            return; // Si ce n'est pas notre page, on arrête
        }
        
        // CHARGER LE CSS
        wp_enqueue_style(
            'pr-admin-css',                              // ID unique
            PR_PLUGIN_URL . 'assets/css/admin.css',     // Chemin du fichier
            array(),                                     // Dépendances (aucune ici)
            self::VERSION                                // Version (pour le cache)
        );
        
        // CHARGER LE JAVASCRIPT
        wp_enqueue_script(
            'pr-admin-js',                               // ID unique
            PR_PLUGIN_URL . 'assets/js/admin.js',       // Chemin du fichier
            array('jquery'),                             // Dépendance : jQuery
            self::VERSION,                               // Version
            true                                         // true = charger en bas de page
        );
        
        // PASSER DES DONNÉES PHP À JAVASCRIPT
        // On crée une variable JavaScript "prAdmin" accessible dans admin.js
        wp_localize_script('pr-admin-js', 'prAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),   // URL pour les requêtes AJAX
            'nonce'   => wp_create_nonce('pr-admin'),    // Token de sécurité
        ));
    }
    
    /**
     * CHARGER LES SCRIPTS FRONTEND
     * CSS et JavaScript pour le site public
     */
    public function frontend_scripts() {
        // CHARGER LE CSS
        wp_enqueue_style(
            'pr-frontend-css',
            PR_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            self::VERSION
        );
        
        // CHARGER LE JAVASCRIPT
        wp_enqueue_script(
            'pr-frontend-js',
            PR_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            self::VERSION,
            true
        );
        
        // PASSER DES DONNÉES À JAVASCRIPT
        wp_localize_script('pr-frontend-js', 'prFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('pr-frontend'),
        ));
    }
    
    /**
     * ENREGISTRER LE SHORTCODE
     * Un shortcode = un code court qu'on peut mettre dans une page
     * Exemple : [best_reviews] dans une page WordPress
     */
    public function register_shortcode() {
        // Enregistrer le shortcode [best_reviews]
        add_shortcode('best_reviews', array($this, 'best_reviews_shortcode'));
        
        // Enregistrer le shortcode [product_reviews]
        add_shortcode('product_reviews', array($this, 'product_reviews_shortcode'));
    }
    
    /**
     * SHORTCODE : Meilleurs avis
     * Usage : [best_reviews limit="5"]
     */
    public function best_reviews_shortcode($atts) {
        // Attributs par défaut
        $atts = shortcode_atts(array(
            'limit' => 5, // Afficher 5 avis par défaut
        ), $atts);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'product_reviews';
        
        // Récupérer les meilleurs avis (5 étoiles, approuvés)
        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE approved = 1 AND rating = 5 
            ORDER BY created_at DESC 
            LIMIT %d",
            intval($atts['limit'])
        ));
        
        // ob_start() = commencer à capturer le HTML
        ob_start();
        
        if ($reviews) :
        ?>
            <div class="pr-best-reviews">
                <h3><?php _e('Avis de nos clients', 'product-reviews'); ?></h3>
                <div class="reviews-grid">
                    <?php foreach ($reviews as $review) : 
                        $product = wc_get_product($review->product_id);
                        if (!$product) continue;
                    ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-rating">
                                    <?php echo $this->display_stars($review->rating); ?>
                                </div>
                                <div class="review-author">
                                    <strong><?php echo esc_html($review->author_name); ?></strong>
                                </div>
                            </div>
                            <div class="review-content">
                                <p><?php echo esc_html($review->review_text); ?></p>
                            </div>
                            <div class="review-product">
                                <a href="<?php echo get_permalink($product->get_id()); ?>">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>
                            </div>
                            <div class="review-date">
                                <?php echo date_i18n(get_option('date_format'), strtotime($review->created_at)); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php
        else :
        ?>
            <p><?php _e('Aucun avis pour le moment.', 'product-reviews'); ?></p>
        <?php
        endif;
        
        // ob_get_clean() = arrêter la capture et retourner le HTML
        return ob_get_clean();
    }
    
    /**
     * SHORTCODE : Avis d'un produit spécifique
     * Usage : [product_reviews id="123"]
     */
    public function product_reviews_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0, // ID du produit
        ), $atts);
        
        if (!$atts['id']) {
            return '<p>' . __('Veuillez spécifier un ID de produit.', 'product-reviews') . '</p>';
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'product_reviews';
        
        // Récupérer tous les avis approuvés pour ce produit
        $reviews = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE product_id = %d AND approved = 1 
            ORDER BY created_at DESC",
            intval($atts['id'])
        ));
        
        ob_start();
        
        if ($reviews) :
        ?>
            <div class="pr-product-reviews">
                <h3><?php _e('Avis clients', 'product-reviews'); ?></h3>
                <?php foreach ($reviews as $review) : ?>
                    <div class="review-item">
                        <div class="review-header">
                            <strong><?php echo esc_html($review->author_name); ?></strong>
                            <span class="review-rating">
                                <?php echo $this->display_stars($review->rating); ?>
                            </span>
                            <span class="review-date">
                                <?php echo date_i18n(get_option('date_format'), strtotime($review->created_at)); ?>
                            </span>
                        </div>
                        <div class="review-text">
                            <p><?php echo esc_html($review->review_text); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php
        else :
        ?>
            <p><?php _e('Soyez le premier à donner votre avis !', 'product-reviews'); ?></p>
        <?php
        endif;
        
        return ob_get_clean();
    }
    
    /**
     * AFFICHER LA NOTE SUR LES PRODUITS
     * Cette fonction s'affiche automatiquement sur chaque produit
     */
    public function display_rating() {
        global $product, $wpdb;
        
        if (!$product) return;
        
        $table_name = $wpdb->prefix . 'product_reviews';
        $product_id = $product->get_id();
        
        // Calculer la note moyenne
        $avg_rating = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(rating) FROM $table_name WHERE product_id = %d AND approved = 1",
            $product_id
        ));
        
        // Compter le nombre d'avis
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE product_id = %d AND approved = 1",
            $product_id
        ));
        
        if ($count > 0) :
        ?>
            <div class="pr-product-rating">
                <?php echo $this->display_stars(round($avg_rating, 1)); ?>
                <span class="pr-rating-count">
                    (<?php echo $count; ?> <?php echo _n('avis', 'avis', $count, 'product-reviews'); ?>)
                </span>
            </div>
        <?php
        endif;
    }
    
    /**
     * AFFICHER LE FORMULAIRE D'AVIS
     * Sur la page d'un produit unique
     */
    public function display_review_form() {
        global $product;
        
        if (!$product) return;
        
        ?>
        <div class="pr-review-form-wrapper">
            <h3><?php _e('Donnez votre avis', 'product-reviews'); ?></h3>
            
            <form id="pr-review-form" class="pr-review-form">
                <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
                
                <div class="form-group">
                    <label><?php _e('Votre nom', 'product-reviews'); ?> *</label>
                    <input type="text" name="author_name" required>
                </div>
                
                <div class="form-group">
                    <label><?php _e('Votre email', 'product-reviews'); ?> *</label>
                    <input type="email" name="author_email" required>
                </div>
                
                <div class="form-group">
                    <label><?php _e('Votre note', 'product-reviews'); ?> *</label>
                    <div class="pr-star-rating">
                        <input type="radio" name="rating" value="5" id="star5" required>
                        <label for="star5">★</label>
                        <input type="radio" name="rating" value="4" id="star4">
                        <label for="star4">★</label>
                        <input type="radio" name="rating" value="3" id="star3">
                        <label for="star3">★</label>
                        <input type="radio" name="rating" value="2" id="star2">
                        <label for="star2">★</label>
                        <input type="radio" name="rating" value="1" id="star1">
                        <label for="star1">★</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><?php _e('Votre avis', 'product-reviews'); ?></label>
                    <textarea name="review_text" rows="5"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?php _e('Envoyer mon avis', 'product-reviews'); ?>
                </button>
                
                <div class="pr-form-message"></div>
            </form>
        </div>
        <?php
    }
    
    /**
     * AFFICHER LES ÉTOILES
     * Convertit un nombre en étoiles visuelles
     */
    private function display_stars($rating) {
        $output = '<span class="pr-stars">';
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $output .= '<span class="star filled">★</span>';
            } else {
                $output .= '<span class="star empty">★</span>';
            }
        }
        
        $output .= '</span>';
        
        return $output;
    }
}

/**
 * DÉMARRER LE PLUGIN
 * Créer une instance de la classe = lancer le plugin
 */
new Product_Reviews_Plugin();