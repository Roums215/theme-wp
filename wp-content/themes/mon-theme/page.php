<?php
/**
 * PAGE.PHP - Page statique
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<?php
    // Check if it's a WooCommerce page that needs full width (Cart, Checkout, Account)
    $is_wc_full_width = class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() || is_account_page() );

    if ( $is_wc_full_width ) :
        // Full Width Layout for WooCommerce Pages
        while ( have_posts() ) :
            the_post();
            the_content();
        endwhile;

    else :
        // Standard Page Layout
        ?>
        <div class="bg-brand-dark min-h-screen py-20">
            
            <?php
            while ( have_posts() ) :
                the_post();
                ?>

                <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
                    
                    <header class="container mx-auto px-4 max-w-4xl text-center mb-16">
                        <h1 class="text-4xl md:text-6xl font-display font-bold text-white mb-6 leading-tight">
                            <?php the_title(); ?>
                        </h1>
                    </header>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="container mx-auto px-4 max-w-6xl mb-16">
                            <div class="rounded-3xl overflow-hidden shadow-2xl border border-brand-border">
                                <?php the_post_thumbnail( 'full', array( 'class' => 'w-full h-auto' ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="container mx-auto px-4 max-w-3xl">
                        <div class="prose prose-lg prose-invert mx-auto text-gray-300">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    
                </article>

                <?php
            endwhile;
            ?>
            
        </div>
        <?php
    endif;
    ?>

<?php
get_footer();