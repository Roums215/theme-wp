<?php
/**
 * ARCHIVE-PRODUCT.PHP - Page Boutique
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<div class="bg-brand-dark min-h-screen py-12">
    <div class="container mx-auto px-4">
        
        <!-- Header Boutique -->
        <header class="mb-12 text-center">
            <h1 class="text-4xl md:text-6xl font-display font-bold text-white mb-4">
                <?php woocommerce_page_title(); ?>
            </h1>
            <p class="text-gray-400 max-w-2xl mx-auto">
                <?php do_action( 'woocommerce_archive_description' ); ?>
            </p>
        </header>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar (Filtres) -->
            <aside class="lg:w-1/4">
                <div class="bg-brand-surface/50 backdrop-blur-md border border-brand-border p-6 rounded-2xl sticky top-24 text-gray-300">
                    <!-- Search -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-white mb-4">Rechercher</h3>
                        <form role="search" method="get" class="relative group" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                            <input type="search" class="w-full bg-brand-dark border border-brand-border rounded-xl py-3 pl-10 pr-4 text-gray-300 focus:outline-none focus:border-brand-primary transition-colors" placeholder="Rechercher..." value="<?php echo get_search_query(); ?>" name="s" />
                            <input type="hidden" name="post_type" value="product" />
                            <svg class="w-5 h-5 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 group-focus-within:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-white mb-4">Catégories</h3>
                        <ul class="space-y-2">
                            <?php
                            $categories = get_terms( array(
                                'taxonomy'   => 'product_cat',
                                'hide_empty' => true,
                            ) );

                            if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                                foreach ( $categories as $category ) {
                                    $is_active = is_product_category( $category->slug );
                                    $active_class = $is_active ? 'text-brand-primary bg-brand-primary/10 border-brand-primary/20' : 'text-gray-400 hover:text-white hover:bg-brand-surface border-transparent hover:border-brand-border';
                                    
                                    echo '<li>';
                                    echo '<a href="' . esc_url( get_term_link( $category ) ) . '" class="flex items-center justify-between px-4 py-2 rounded-lg border transition-all ' . $active_class . '">';
                                    echo '<span>' . esc_html( $category->name ) . '</span>';
                                    echo '<span class="text-xs bg-brand-dark px-2 py-1 rounded-full text-gray-500">' . $category->count . '</span>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Grille de produits -->
            <div class="lg:w-3/4">
                <?php
                if ( woocommerce_product_loop() ) {
                    

                    echo '<div class="bg-brand-surface/50 backdrop-blur-md border border-brand-border p-4 rounded-2xl mb-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-300">';
                    do_action( 'woocommerce_before_shop_loop' );
                    echo '</div>';
                    

                    echo '<ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
                    

                    if ( have_posts() ) {
                        while ( have_posts() ) {
                            the_post();
                            do_action( 'woocommerce_shop_loop' );
                            wc_get_template_part( 'content', 'product' );
                        }
                    }
                    
                    echo '</ul>';
                    

                    echo '<div class="mt-12 flex justify-center">';
                    do_action( 'woocommerce_after_shop_loop' );
                    echo '</div>';
                    
                } else {
                    echo '<div class="bg-brand-surface border border-brand-border p-12 rounded-2xl text-center text-gray-400">';
                    do_action( 'woocommerce_no_products_found' );
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();