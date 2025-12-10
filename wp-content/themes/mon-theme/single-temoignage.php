<?php
/**
 * SINGLE-TEMOIGNAGE.PHP - Un témoignage unique
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<div class="bg-brand-dark py-12 text-gray-300">
    
    <?php
    while ( have_posts() ) :
        the_post();
        $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
    ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <header class="container mx-auto px-4 max-w-4xl text-center mb-12">
                <div class="text-sm text-brand-primary font-bold mb-4 uppercase tracking-wider">
                    Témoignage Client
                </div>
                
                <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-6 leading-tight">
                    <?php the_title(); ?>
                </h1>
                
                <?php if ( $auteur ) : ?>
                    <p class="text-xl text-gray-400">Client : <strong class="text-white"><?php echo esc_html( $auteur ); ?></strong></p>
                <?php endif; ?>
            </header>

            <div class="container mx-auto px-4 max-w-3xl">
                <div class="prose prose-lg prose-invert mx-auto italic relative">
                    <span class="text-6xl text-brand-primary/20 absolute -top-8 -left-8 font-serif">"</span>
                    <?php the_content(); ?>
                    <span class="text-6xl text-brand-primary/20 absolute -bottom-8 -right-8 font-serif">"</span>
                </div>
            </div>
            
        </article>

        <div class="container mx-auto px-4 max-w-3xl mt-16 pt-8 border-t border-brand-border flex justify-between">
            <div class="nav-previous text-brand-primary hover:text-white transition-colors"><?php previous_post_link( '%link', '← Précédent' ); ?></div>
            <div class="nav-next text-brand-primary hover:text-white transition-colors"><?php next_post_link( '%link', 'Suivant →' ); ?></div>
        </div>
        
    <?php endwhile; ?>
    
    <div class="text-center mt-12">
        <a href="<?php echo get_post_type_archive_link( 'temoignage' ); ?>" class="bg-brand-primary text-white font-bold py-3 px-8 rounded-lg hover:bg-brand-secondary transition-colors">
            ← Voir tous les témoignages
        </a>
    </div>
    
</div>

<?php
get_footer();