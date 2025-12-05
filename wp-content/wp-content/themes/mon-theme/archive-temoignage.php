<?php
/**
 * ARCHIVE-TEMOIGNAGE.PHP - Liste des témoignages
 * 
 * Template pour l'archive du CPT "temoignage"
 * Accessible via l'URL /temoignages/
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <header class="archive-header">
        <h1 class="archive-title">Témoignages clients</h1>
        <p>Découvrez ce que nos clients pensent de nous</p>
    </header>
    
    <?php if ( have_posts() ) : ?>
        
        <div class="testimonials-grid">
            <?php
            while ( have_posts() ) :
                the_post();
                $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
            ?>

                <div class="testimonial-card">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    
                    <div class="testimonial-content">
                        "<?php echo wp_trim_words( get_the_content(), 25 ); ?>"
                    </div>
                    
                    <?php if ( $auteur ) : ?>
                        <div class="testimonial-author">— <?php echo esc_html( $auteur ); ?></div>
                    <?php endif; ?>
                    
                    <a href="<?php the_permalink(); ?>">Lire la suite →</a>
                </div>

            <?php endwhile; ?>
        </div>
        
        <?php the_posts_pagination(); ?>

    <?php else : ?>
        <p>Aucun témoignage pour le moment.</p>
    <?php endif; ?>
    
</main>

<?php
get_footer();