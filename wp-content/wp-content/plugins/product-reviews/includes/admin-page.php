<?php
/**
 * Page d'administration du plugin
 * 
 * Cette page permet de :
 * - Voir tous les avis
 * - Approuver les avis en attente
 * - Supprimer des avis
 * - Voir les statistiques
 * 
 * @package Product_Reviews
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe pour la page d'administration
 */
class PR_Admin_Page {
    
    /**
     * CONSTRUCTEUR
     */
    public function __construct() {
        // Hook : Ajouter le menu dans l'administration
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * AJOUTER LE MENU DANS L'ADMIN
     */
    public function add_admin_menu() {
        // Ajouter une page au menu principal
        add_menu_page(
            __('Avis Produits', 'product-reviews'),      // Titre de la page
            __('Avis Produits', 'product-reviews'),      // Texte du menu
            'manage_options',                             // Capacité requise (admin)
            'product-reviews',                            // Slug de la page
            array($this, 'display_admin_page'),          // Fonction d'affichage
            'dashicons-star-filled',                     // Icône (étoile)
            26                                            // Position dans le menu
        );
    }
    
    /**
     * AFFICHER LA PAGE D'ADMINISTRATION
     */
    public function display_admin_page() {
        // Récupérer l'onglet actif (par défaut : "all")
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
        
        ?>
        <div class="wrap pr-admin-page">
            <h1>
                <span class="dashicons dashicons-star-filled"></span>
                <?php _e('Gestion des Avis Produits', 'product-reviews'); ?>
            </h1>
            
            <!-- ONGLETS -->
            <h2 class="nav-tab-wrapper">
                <a href="?page=product-reviews&tab=all" 
                   class="nav-tab <?php echo $active_tab === 'all' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Tous les avis', 'product-reviews'); ?>
                </a>
                <a href="?page=product-reviews&tab=pending" 
                   class="nav-tab <?php echo $active_tab === 'pending' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('En attente', 'product-reviews'); ?>
                    <?php
                    $pending_count = PR_Review::get_statistics()['pending'];
                    if ($pending_count > 0) {
                        echo '<span class="awaiting-mod count-' . $pending_count . '">' . $pending_count . '</span>';
                    }
                    ?>
                </a>
                <a href="?page=product-reviews&tab=approved" 
                   class="nav-tab <?php echo $active_tab === 'approved' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Approuvés', 'product-reviews'); ?>
                </a>
                <a href="?page=product-reviews&tab=stats" 
                   class="nav-tab <?php echo $active_tab === 'stats' ? 'nav-tab-active' : ''; ?>">
                    <?php _e('Statistiques', 'product-reviews'); ?>
                </a>
            </h2>
            
            <!-- CONTENU DE L'ONGLET -->
            <div class="pr-tab-content">
                <?php
                switch ($active_tab) {
                    case 'pending':
                        $this->display_reviews_list('pending');
                        break;
                    case 'approved':
                        $this->display_reviews_list('approved');
                        break;
                    case 'stats':
                        $this->display_statistics();
                        break;
                    default:
                        $this->display_reviews_list('all');
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * AFFICHER LA LISTE DES AVIS
     */
    private function display_reviews_list($filter = 'all') {
        // Définir le filtre d'approbation
        $approved = null;
        if ($filter === 'pending') {
            $approved = 0;
        } elseif ($filter === 'approved') {
            $approved = 1;
        }
        
        // Récupérer les avis
        $reviews = PR_Review::get_all_reviews(array(
            'approved' => $approved,
            'limit'    => 100,
        ));
        
        ?>
        <div class="pr-reviews-list">
            <?php if ($reviews) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="5%"><?php _e('ID', 'product-reviews'); ?></th>
                            <th width="15%"><?php _e('Produit', 'product-reviews'); ?></th>
                            <th width="15%"><?php _e('Auteur', 'product-reviews'); ?></th>
                            <th width="10%"><?php _e('Note', 'product-reviews'); ?></th>
                            <th width="30%"><?php _e('Avis', 'product-reviews'); ?></th>
                            <th width="15%"><?php _e('Date', 'product-reviews'); ?></th>
                            <th width="10%"><?php _e('Actions', 'product-reviews'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review) : 
                            $product = wc_get_product($review->product_id);
                        ?>
                            <tr class="review-row" data-review-id="<?php echo $review->id; ?>">
                                <td><?php echo $review->id; ?></td>
                                <td>
                                    <?php if ($product) : ?>
                                        <a href="<?php echo get_edit_post_link($review->product_id); ?>" target="_blank">
                                            <?php echo esc_html($product->get_name()); ?>
                                        </a>
                                    <?php else : ?>
                                        <em><?php _e('Produit supprimé', 'product-reviews'); ?></em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo esc_html($review->author_name); ?></strong><br>
                                    <small><?php echo esc_html($review->author_email); ?></small>
                                </td>
                                <td>
                                    <?php echo $this->display_stars($review->rating); ?>
                                    <br>
                                    <small>(<?php echo $review->rating; ?>/5)</small>
                                </td>
                                <td>
                                    <?php 
                                    if ($review->review_text) {
                                        echo '<p>' . esc_html(wp_trim_words($review->review_text, 20)) . '</p>';
                                    } else {
                                        echo '<em>' . __('Pas de commentaire', 'product-reviews') . '</em>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo date_i18n(
                                        get_option('date_format') . ' ' . get_option('time_format'),
                                        strtotime($review->created_at)
                                    ); ?>
                                </td>
                                <td>
                                    <?php if ($review->approved == 0) : ?>
                                        <button class="button button-primary pr-approve-btn" 
                                                data-review-id="<?php echo $review->id; ?>">
                                            <?php _e('Approuver', 'product-reviews'); ?>
                                        </button>
                                        <br><br>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                                        <em><?php _e('Approuvé', 'product-reviews'); ?></em>
                                        <br><br>
                                    <?php endif; ?>
                                    
                                    <button class="button button-link-delete pr-delete-btn" 
                                            data-review-id="<?php echo $review->id; ?>">
                                        <?php _e('Supprimer', 'product-reviews'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="notice notice-info">
                    <p><?php _e('Aucun avis à afficher.', 'product-reviews'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * AFFICHER LES STATISTIQUES
     */
    private function display_statistics() {
        $stats = PR_Review::get_statistics();
        
        ?>
        <div class="pr-statistics">
            <h2><?php _e('Statistiques globales', 'product-reviews'); ?></h2>
            
            <!-- CARTES DE STATISTIQUES -->
            <div class="pr-stats-cards">
                <div class="pr-stat-card">
                    <div class="pr-stat-icon">
                        <span class="dashicons dashicons-star-filled"></span>
                    </div>
                    <div class="pr-stat-content">
                        <h3><?php echo $stats['total']; ?></h3>
                        <p><?php _e('Total d\'avis', 'product-reviews'); ?></p>
                    </div>
                </div>
                
                <div class="pr-stat-card">
                    <div class="pr-stat-icon pending">
                        <span class="dashicons dashicons-clock"></span>
                    </div>
                    <div class="pr-stat-content">
                        <h3><?php echo $stats['pending']; ?></h3>
                        <p><?php _e('En attente', 'product-reviews'); ?></p>
                    </div>
                </div>
                
                <div class="pr-stat-card">
                    <div class="pr-stat-icon approved">
                        <span class="dashicons dashicons-yes-alt"></span>
                    </div>
                    <div class="pr-stat-content">
                        <h3><?php echo $stats['approved']; ?></h3>
                        <p><?php _e('Approuvés', 'product-reviews'); ?></p>
                    </div>
                </div>
                
                <div class="pr-stat-card">
                    <div class="pr-stat-icon rating">
                        <span class="dashicons dashicons-chart-line"></span>
                    </div>
                    <div class="pr-stat-content">
                        <h3><?php echo $stats['average_rating']; ?>/5</h3>
                        <p><?php _e('Note moyenne', 'product-reviews'); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- RÉPARTITION PAR ÉTOILES -->
            <div class="pr-stars-distribution">
                <h3><?php _e('Répartition par étoiles', 'product-reviews'); ?></h3>
                <table class="wp-list-table widefat">
                    <thead>
                        <tr>
                            <th><?php _e('Note', 'product-reviews'); ?></th>
                            <th><?php _e('Nombre d\'avis', 'product-reviews'); ?></th>
                            <th><?php _e('Pourcentage', 'product-reviews'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 5; $i >= 1; $i--) : 
                            $count = $stats['stars_' . $i];
                            $percentage = $stats['approved'] > 0 ? round(($count / $stats['approved']) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td><?php echo $this->display_stars($i); ?></td>
                                <td><?php echo $count; ?></td>
                                <td>
                                    <div class="pr-progress-bar">
                                        <div class="pr-progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span><?php echo $percentage; ?>%</span>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * AFFICHER LES ÉTOILES
     */
    private function display_stars($rating) {
        $output = '<span class="pr-stars">';
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $output .= '<span class="dashicons dashicons-star-filled" style="color: #f90;"></span>';
            } else {
                $output .= '<span class="dashicons dashicons-star-empty" style="color: #ccc;"></span>';
            }
        }
        
        $output .= '</span>';
        
        return $output;
    }
}

// Créer une instance
new PR_Admin_Page();