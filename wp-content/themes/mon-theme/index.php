<?php
/**
 * INDEX.PHP - Blog Index
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        
        <header class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-primary mb-4">Le Blog</h1>
            <p class="text-xl text-gray-600">Actualités, guides et astuces pour votre setup.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) : 
                    the_post();
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col h-full'); ?>>
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="block h-48 overflow-hidden">
                                <?php the_post_thumbnail( 'large', array( 'class' => 'w-full h-full object-cover transform hover:scale-105 transition-transform duration-500' ) ); ?>
                            </a>
                        <?php endif; ?>

                        <div class="p-6 flex-1 flex flex-col">
                            <div class="text-sm text-secondary font-bold mb-2 uppercase tracking-wider">
                                <?php echo get_the_date(); ?>
                            </div>

                            <h2 class="text-xl font-bold text-primary mb-3 leading-tight">
                                <a href="<?php the_permalink(); ?>" class="hover:text-secondary transition-colors">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <div class="text-gray-600 mb-4 flex-1 line-clamp-3">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-secondary font-bold hover:text-blue-700 transition-colors mt-auto">
                                Lire la suite <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                        
                    </article>

                    <?php
                endwhile;
            else :
                ?>
                <p class="col-span-3 text-center text-gray-500">Aucun article trouvé.</p>
                <?php
            endif;
            ?>
        </div>

        <div class="mt-12 flex justify-center">
            <?php
            the_posts_pagination( array(
                'prev_text' => '←',
                'next_text' => '→',
                'class'     => 'flex gap-2'
            ) );
            ?>
        </div>
        
    </div>
</div>

<?php
get_footer();