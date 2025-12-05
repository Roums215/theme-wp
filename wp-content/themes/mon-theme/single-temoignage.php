<?php
/**
 * SINGLE-TEMOIGNAGE.PHP - Un témoignage unique
 * 
 * Template pour le Custom Post Type "temoignage"
 * WordPress utilise ce fichier automatiquement
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<main id="main" class="site-main">
    
    <?php
    while ( have_posts() ) :
        the_post();
        $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
    ?>

        <article class="temoignage-single">
            
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
                
                <?php if ( $auteur ) : ?>
                    <p class="testimonial-author">Témoignage de : <strong><?php echo esc_html( $auteur ); ?></strong></p>
                <?php endif; ?>
            </header>

            <div class="entry-content">
                <blockquote>
                    <?php the_content(); ?>
                </blockquote>
            </div>
            
        </article>

        <?php
        the_post_navigation( array(
            'prev_text' => '← Précédent',
            'next_text' => 'Suivant →',
        ));
        
    endwhile;
    ?>
    
    <p style="margin-top: 30px;">
        <a href="<?php echo get_post_type_archive_link( 'temoignage' ); ?>" class="btn">
            ← Voir tous les témoignages
        </a>
    </p>
    
</main>

<?php
get_footer();