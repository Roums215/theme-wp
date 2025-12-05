<?php
/**
 * Template Name: Page d'accueil
 * 
 * Le commentaire "Template Name:" permet à WordPress de reconnaître
 * ce fichier comme template personnalisé sélectionnable dans l'admin
 * (quand on édite une page → Attributs de page → Modèle)
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <!-- ============================
         SECTION HERO (BANNIÈRE)
         ============================ -->
    <section class="hero-section">
        <h1 class="hero-title">Bienvenue sur notre boutique</h1>
        <p class="hero-description">Découvrez nos produits de qualité à prix imbattables</p>
        
        <?php if ( class_exists( 'WooCommerce' ) ) : ?>
            <a href="<?php echo wc_get_page_permalink( 'shop' ); ?>" class="btn">
                Voir la boutique
            </a>
        <?php endif; ?>
    </section>
    
    
    <!-- ============================
         PRODUITS RÉCENTS
         ============================ -->
    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <section class="featured-products">
        <h2 class="section-title">Nos derniers produits</h2>
        
        <?php 
        // Shortcode WooCommerce pour afficher les produits récents
        echo do_shortcode( '[recent_products limit="4" columns="4"]' ); 
        ?>
    </section>
    <?php endif; ?>
    
    
    <!-- ============================
         TÉMOIGNAGES (notre CPT)
         ============================ -->
    <?php
    // Requête pour récupérer les témoignages
    $temoignages = new WP_Query( array(
        'post_type'      => 'temoignage',
        'posts_per_page' => 3,
        'post_status'    => 'publish',
    ));
    
    if ( $temoignages->have_posts() ) :
    ?>
    <section class="testimonials-section">
        <h2 class="section-title">Ce que disent nos clients</h2>
        
        <div class="testimonials-grid">
            <?php
            while ( $temoignages->have_posts() ) :
                $temoignages->the_post();
                $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
            ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "<?php echo wp_trim_words( get_the_content(), 30 ); ?>"
                    </div>
                    <?php if ( $auteur ) : ?>
                        <div class="testimonial-author">— <?php echo esc_html( $auteur ); ?></div>
                    <?php endif; ?>
                </div>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </section>
    <?php endif; ?>
    
    
    <!-- ============================
         CATÉGORIES
         ============================ -->
    <?php if ( class_exists( 'WooCommerce' ) ) : ?>
    <section class="product-categories">
        <h2 class="section-title">Nos catégories</h2>
        
        <div class="categories-grid">
            <?php
            $categories = get_terms( array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
                'parent'     => 0,
                'number'     => 3,
            ));
            
            foreach ( $categories as $category ) :
            ?>
                <div class="category-card">
                    <a href="<?php echo get_term_link( $category ); ?>">
                        <h3><?php echo esc_html( $category->name ); ?></h3>
                        <span><?php echo $category->count; ?> produit(s)</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    
    <!-- ============================
         AVANTAGES
         ============================ -->
    <section class="benefits-section">
        <div class="benefits-grid">
            
            <div class="benefit-item">
                <span class="benefit-icon">🚚</span>
                <h3>Livraison rapide</h3>
                <p>Livraison en 24-48h</p>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">💳</span>
                <h3>Paiement sécurisé</h3>
                <p>Transactions 100% sécurisées</p>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">↩️</span>
                <h3>Retours gratuits</h3>
                <p>30 jours pour changer d'avis</p>
            </div>
            
            <div class="benefit-item">
                <span class="benefit-icon">📞</span>
                <h3>Support client</h3>
                <p>À votre écoute 7j/7</p>
            </div>
            
        </div>
    </section>
    
</main>

<?php
get_footer();