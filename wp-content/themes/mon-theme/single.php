<?php
/**
 * SINGLE.PHP - Article unique
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<div class="bg-white py-12">
    
    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            
            <!-- Header Article -->
            <header class="container mx-auto px-4 max-w-4xl text-center mb-12">
                <div class="text-sm text-secondary font-bold mb-4 uppercase tracking-wider">
                    <?php the_category( ', ' ); ?>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-display font-bold text-primary mb-6 leading-tight">
                    <?php the_title(); ?>
                </h1>
                
                <div class="flex items-center justify-center gap-4 text-gray-500 text-sm">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <?php echo get_the_date(); ?>
                    </span>
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        <?php the_author(); ?>
                    </span>
                </div>
            </header>

            <!-- Image à la une -->
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="container mx-auto px-4 max-w-5xl mb-12">
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <?php the_post_thumbnail( 'full', array( 'class' => 'w-full h-auto' ) ); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contenu -->
            <div class="container mx-auto px-4 max-w-3xl">
                <div class="prose prose-lg prose-blue mx-auto text-gray-700">
                    <?php the_content(); ?>
                </div>

                <!-- Footer Article -->
                <footer class="mt-12 pt-8 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2">
                        <?php the_tags( '<span class="text-gray-500 mr-2">Tags:</span>', '', '' ); ?>
                    </div>
                </footer>
            </div>
            
        </article>

        <?php

        if ( comments_open() || get_comments_number() ) :
            ?>
            <div class="container mx-auto px-4 max-w-3xl mt-16 pt-16 border-t border-gray-100">
                <?php comments_template(); ?>
            </div>
            <?php
        endif;
        
    endwhile;
    ?>
    
</div>

<?php
get_footer();