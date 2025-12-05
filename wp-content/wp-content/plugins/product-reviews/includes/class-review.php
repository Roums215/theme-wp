<?php
/**
 * Classe pour gérer les avis
 * 
 * Cette classe contient toutes les fonctions pour :
 * - Créer un avis
 * - Récupérer des avis
 * - Approuver/Supprimer un avis
 * - Calculer les statistiques
 * 
 * @package Product_Reviews
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe PR_Review
 */
class PR_Review {
    
    /**
     * Nom de la table en base de données
     */
    private static $table_name;
    
    /**
     * CONSTRUCTEUR
     * Se lance automatiquement quand on crée un objet PR_Review
     */
    public function __construct() {
        global $wpdb;
        
        // Définir le nom de la table
        self::$table_name = $wpdb->prefix . 'product_reviews';
        
        // Accrocher nos fonctions aux hooks
        $this->init_hooks();
    }
    
    /**
     * INITIALISER LES HOOKS
     */
    private function init_hooks() {
        // AJAX : Soumettre un avis (utilisateur connecté)
        add_action('wp_ajax_pr_submit_review', array($this, 'ajax_submit_review'));
        
        // AJAX : Soumettre un avis (visiteur non connecté)
        add_action('wp_ajax_nopriv_pr_submit_review', array($this, 'ajax_submit_review'));
        
        // AJAX : Approuver un avis (admin seulement)
        add_action('wp_ajax_pr_approve_review', array($this, 'ajax_approve_review'));
        
        // AJAX : Supprimer un avis (admin seulement)
        add_action('wp_ajax_pr_delete_review', array($this, 'ajax_delete_review'));
    }
    
    /**
     * SOUMETTRE UN AVIS (via AJAX)
     * Cette fonction est appelée quand un client envoie le formulaire
     */
    public function ajax_submit_review() {
        // ÉTAPE 1 : Vérifier la sécurité (nonce)
        // Le nonce = un jeton de sécurité pour éviter les fausses requêtes
        check_ajax_referer('pr-frontend', 'nonce');
        
        // ÉTAPE 2 : Récupérer et nettoyer les données du formulaire
        $product_id   = intval($_POST['product_id']);          // ID du produit (nombre entier)
        $author_name  = sanitize_text_field($_POST['author_name']);   // Nom (nettoyer le texte)
        $author_email = sanitize_email($_POST['author_email']);       // Email (valider l'email)
        $rating       = intval($_POST['rating']);              // Note (nombre de 1 à 5)
        $review_text  = sanitize_textarea_field($_POST['review_text']); // Texte de l'avis
        
        // ÉTAPE 3 : Valider les données
        // Vérifier que tout est correct avant de sauvegarder
        
        // Le produit existe-t-il ?
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(array(
                'message' => __('Produit introuvable.', 'product-reviews')
            ));
        }
        
        // Le nom est-il rempli ?
        if (empty($author_name)) {
            wp_send_json_error(array(
                'message' => __('Veuillez entrer votre nom.', 'product-reviews')
            ));
        }
        
        // L'email est-il valide ?
        if (!is_email($author_email)) {
            wp_send_json_error(array(
                'message' => __('Veuillez entrer un email valide.', 'product-reviews')
            ));
        }
        
        // La note est-elle entre 1 et 5 ?
        if ($rating < 1 || $rating > 5) {
            wp_send_json_error(array(
                'message' => __('La note doit être entre 1 et 5 étoiles.', 'product-reviews')
            ));
        }
        
        // ÉTAPE 4 : Sauvegarder l'avis dans la base de données
        $result = self::create_review(array(
            'product_id'   => $product_id,
            'author_name'  => $author_name,
            'author_email' => $author_email,
            'rating'       => $rating,
            'review_text'  => $review_text,
            'user_id'      => get_current_user_id(), // ID de l'utilisateur (0 si non connecté)
        ));
        
        // ÉTAPE 5 : Envoyer la réponse
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Merci pour votre avis ! Il sera publié après validation.', 'product-reviews')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erreur lors de l\'enregistrement. Veuillez réessayer.', 'product-reviews')
            ));
        }
    }
    
    /**
     * CRÉER UN AVIS
     * Fonction statique = on peut l'appeler sans créer d'objet
     * Exemple : PR_Review::create_review($data);
     */
    public static function create_review($data) {
        global $wpdb;
        
        // Insérer dans la base de données
        $result = $wpdb->insert(
            self::$table_name,  // Nom de la table
            array(
                'product_id'   => $data['product_id'],
                'user_id'      => $data['user_id'],
                'author_name'  => $data['author_name'],
                'author_email' => $data['author_email'],
                'rating'       => $data['rating'],
                'review_text'  => $data['review_text'],
                'approved'     => 0, // 0 = en attente d'approbation
            ),
            array(
                '%d', // product_id = nombre
                '%d', // user_id = nombre
                '%s', // author_name = texte
                '%s', // author_email = texte
                '%d', // rating = nombre
                '%s', // review_text = texte
                '%d', // approved = nombre
            )
        );
        
        // Retourner true si succès, false si erreur
        return $result !== false;
    }
    
    /**
     * RÉCUPÉRER TOUS LES AVIS
     */
    public static function get_all_reviews($args = array()) {
        global $wpdb;
        
        // Arguments par défaut
        $defaults = array(
            'product_id' => 0,      // 0 = tous les produits
            'approved'   => null,   // null = tous, 0 = en attente, 1 = approuvés
            'limit'      => 50,     // Nombre maximum d'avis
            'offset'     => 0,      // Décalage (pour la pagination)
            'orderby'    => 'created_at', // Trier par date
            'order'      => 'DESC', // DESC = du plus récent au plus ancien
        );
        
        // Fusionner avec les arguments fournis
        $args = wp_parse_args($args, $defaults);
        
        // CONSTRUIRE LA REQUÊTE SQL
        $sql = "SELECT * FROM " . self::$table_name . " WHERE 1=1";
        
        // Filtrer par produit si spécifié
        if ($args['product_id'] > 0) {
            $sql .= $wpdb->prepare(" AND product_id = %d", $args['product_id']);
        }
        
        // Filtrer par statut d'approbation si spécifié
        if ($args['approved'] !== null) {
            $sql .= $wpdb->prepare(" AND approved = %d", $args['approved']);
        }
        
        // Tri
        $sql .= " ORDER BY " . esc_sql($args['orderby']) . " " . esc_sql($args['order']);
        
        // Limite
        $sql .= $wpdb->prepare(" LIMIT %d OFFSET %d", $args['limit'], $args['offset']);
        
        // Exécuter la requête
        return $wpdb->get_results($sql);
    }
    
    /**
     * RÉCUPÉRER UN AVIS PAR ID
     */
    public static function get_review($review_id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM " . self::$table_name . " WHERE id = %d",
            $review_id
        ));
    }
    
    /**
     * APPROUVER UN AVIS (via AJAX)
     */
    public function ajax_approve_review() {
        // Vérifier les permissions (seulement les admins)
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('Vous n\'avez pas les permissions nécessaires.', 'product-reviews')
            ));
        }
        
        // Vérifier la sécurité
        check_ajax_referer('pr-admin', 'nonce');
        
        // Récupérer l'ID de l'avis
        $review_id = intval($_POST['review_id']);
        
        // Approuver l'avis
        $result = self::approve_review($review_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Avis approuvé avec succès.', 'product-reviews')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erreur lors de l\'approbation.', 'product-reviews')
            ));
        }
    }
    
    /**
     * APPROUVER UN AVIS
     */
    public static function approve_review($review_id) {
        global $wpdb;
        
        return $wpdb->update(
            self::$table_name,
            array('approved' => 1),      // Mettre approved à 1
            array('id' => $review_id),   // Pour l'avis avec cet ID
            array('%d'),                 // approved est un nombre
            array('%d')                  // id est un nombre
        );
    }
    
    /**
     * SUPPRIMER UN AVIS (via AJAX)
     */
    public function ajax_delete_review() {
        // Vérifier les permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array(
                'message' => __('Vous n\'avez pas les permissions nécessaires.', 'product-reviews')
            ));
        }
        
        // Vérifier la sécurité
        check_ajax_referer('pr-admin', 'nonce');
        
        // Récupérer l'ID de l'avis
        $review_id = intval($_POST['review_id']);
        
        // Supprimer l'avis
        $result = self::delete_review($review_id);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Avis supprimé avec succès.', 'product-reviews')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Erreur lors de la suppression.', 'product-reviews')
            ));
        }
    }
    
    /**
     * SUPPRIMER UN AVIS
     */
    public static function delete_review($review_id) {
        global $wpdb;
        
        return $wpdb->delete(
            self::$table_name,
            array('id' => $review_id),
            array('%d')
        );
    }
    
    /**
     * CALCULER LA NOTE MOYENNE D'UN PRODUIT
     */
    public static function get_average_rating($product_id) {
        global $wpdb;
        
        $avg = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(rating) FROM " . self::$table_name . " 
            WHERE product_id = %d AND approved = 1",
            $product_id
        ));
        
        return $avg ? round($avg, 1) : 0;
    }
    
    /**
     * COMPTER LES AVIS D'UN PRODUIT
     */
    public static function count_reviews($product_id, $approved_only = true) {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM " . self::$table_name . " WHERE product_id = %d";
        
        if ($approved_only) {
            $sql .= " AND approved = 1";
        }
        
        return $wpdb->get_var($wpdb->prepare($sql, $product_id));
    }
    
    /**
     * OBTENIR LES STATISTIQUES GLOBALES
     */
    public static function get_statistics() {
        global $wpdb;
        
        $stats = array();
        
        // Nombre total d'avis
        $stats['total'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::$table_name
        );
        
        // Avis en attente
        $stats['pending'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::$table_name . " WHERE approved = 0"
        );
        
        // Avis approuvés
        $stats['approved'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM " . self::$table_name . " WHERE approved = 1"
        );
        
        // Note moyenne globale
        $stats['average_rating'] = $wpdb->get_var(
            "SELECT AVG(rating) FROM " . self::$table_name . " WHERE approved = 1"
        );
        
        if ($stats['average_rating']) {
            $stats['average_rating'] = round($stats['average_rating'], 1);
        } else {
            $stats['average_rating'] = 0;
        }
        
        // Répartition par étoiles
        for ($i = 1; $i <= 5; $i++) {
            $stats['stars_' . $i] = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM " . self::$table_name . " 
                WHERE rating = %d AND approved = 1",
                $i
            ));
        }
        
        return $stats;
    }
}

// Créer une instance de la classe
new PR_Review();