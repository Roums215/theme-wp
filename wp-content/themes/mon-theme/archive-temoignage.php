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

<div class="bg-brand-dark py-12">
    <div class="container mx-auto px-4">
    
        <header class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-4">Témoignages clients</h1>
            <p class="text-xl text-gray-400">Découvrez ce que nos clients pensent de nous</p>
        </header>
        
        <?php if ( have_posts() ) : ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                while ( have_posts() ) :
                    the_post();
                    $auteur = get_post_meta( get_the_ID(), 'auteur_temoignage', true );
                ?>

                    <article class="bg-brand-surface border border-brand-border rounded-2xl p-8 hover:border-brand-primary transition-all duration-300 group flex flex-col h-full">
                        <h3 class="text-xl font-bold text-white mb-4 group-hover:text-brand-primary transition-colors"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        
                        <div class="text-gray-400 mb-6 italic flex-1">
                            "<?php echo wp_trim_words( get_the_content(), 25 ); ?>"
                        </div>
                        
                        <?php if ( $auteur ) : ?>
                            <div class="text-sm text-brand-primary font-bold mb-4 uppercase tracking-wider">— <?php echo esc_html( $auteur ); ?></div>
                        <?php endif; ?>
                        
                        <a href="<?php the_permalink(); ?>" class="text-sm font-medium text-white hover:text-brand-primary transition-colors mt-auto flex items-center gap-2">Lire la suite <span class="group-hover:translate-x-1 transition-transform">→</span></a>
                    </article>

                <?php endwhile; ?>
            </div>
            
            <div class="mt-12 flex justify-center">
                <?php the_posts_pagination( array( 'class' => 'flex gap-2' ) ); ?>
            </div>

        <?php else : ?>
            <p class="text-center text-gray-500">Aucun témoignage pour le moment.</p>
        <?php endif; ?>
        
    </div>
</div>

<?php
get_footer();