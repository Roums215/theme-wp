<?php
/**
 * SINGLE-PRODUCT.PHP - Page Produit Unique
 * 
 * @package MonECommerceTheme
 */

get_header();
?>

<div class="bg-brand-dark min-h-screen py-12">
    <div class="container mx-auto px-4">
        
        <?php
        while ( have_posts() ) :
            the_post();
            ?>

            <div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
                
                <div class="flex flex-col md:flex-row gap-12">
                    
                    <!-- Colonne Image -->
                    <div class="md:w-1/2">
                        <div class="product-gallery sticky top-24 bg-brand-surface rounded-2xl overflow-hidden border border-brand-border">
                            <?php do_action( 'woocommerce_before_single_product_summary' ); ?>
                        </div>
                    </div>
                    
                    <!-- Colonne Infos -->
                    <div class="md:w-1/2">
                        <div class="product-summary text-gray-300">
                            <h1 class="text-4xl font-display font-bold text-white mb-4 leading-tight"><?php the_title(); ?></h1>
                            
                            <div class="text-2xl font-bold text-brand-accent mb-6">
                                <?php echo $product->get_price_html(); ?>
                            </div>

                            <div class="prose prose-invert prose-lg mb-8 text-gray-400">
                                <?php the_excerpt(); ?>
                            </div>

                            <div class="bg-brand-surface/50 backdrop-blur-sm border border-brand-border p-6 rounded-2xl mb-8">
                                <?php do_action( 'woocommerce_single_product_summary' ); ?>
                            </div>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-500 border-t border-brand-border pt-6">
                                <span>SKU: <?php echo ( $sku = $product->get_sku() ) ? $sku : 'N/A'; ?></span>
                                <span>Catégorie: <?php echo wc_get_product_category_list( $product->get_id(), ', ' ); ?></span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Tabs / Description longue -->
                <div class="mt-16 pt-16 border-t border-brand-border/50">
                    <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
                </div>

                <!-- MISSION BLOCK -->
                <div class="mt-20 mb-12">
                    <div class="bg-gradient-to-br from-brand-surface to-brand-dark border border-brand-border rounded-3xl p-10 md:p-16 text-center relative overflow-hidden group">
                        <!-- Glow Effects -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full bg-brand-primary/5 blur-[100px] rounded-full pointer-events-none"></div>
                        
                        <div class="relative z-10 max-w-3xl mx-auto">
                            <h3 class="text-3xl font-display font-bold text-white mb-6">Conçu par des passionnés, pour des passionnés.</h3>
                            <p class="text-gray-400 text-lg leading-relaxed mb-8">
                                Nous sommes une équipe de jeunes fans de beaux claviers. Notre mission est simple : rendre l'excellence accessible. 
                                Nous sélectionnons méticuleusement chaque switch, chaque keycap et chaque châssis pour vous offrir le meilleur, 
                                car nous savons qu'un bon setup change tout.
                            </p>
                            <div class="flex justify-center gap-4">
                                <span class="px-4 py-2 bg-brand-dark border border-brand-border rounded-full text-sm text-brand-primary font-bold">Qualité Premium</span>
                                <span class="px-4 py-2 bg-brand-dark border border-brand-border rounded-full text-sm text-brand-secondary font-bold">Support Français</span>
                                <span class="px-4 py-2 bg-brand-dark border border-brand-border rounded-full text-sm text-brand-accent font-bold">Expédition Rapide</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AVANTAGES / RASSURANCE -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
                    <!-- Livraison -->
                    <div class="bg-brand-surface/50 backdrop-blur-sm border border-brand-border p-8 rounded-2xl text-center hover:border-brand-primary/50 transition-colors group">
                        <div class="w-16 h-16 bg-brand-dark rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-brand-border group-hover:border-brand-primary">
                            <svg class="w-8 h-8 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Livraison Rapide</h3>
                        <p class="text-gray-400">Expédition sous 24/48h depuis la France. Suivi en temps réel.</p>
                    </div>

                    <!-- Support -->
                    <div class="bg-brand-surface/50 backdrop-blur-sm border border-brand-border p-8 rounded-2xl text-center hover:border-brand-secondary/50 transition-colors group">
                        <div class="w-16 h-16 bg-brand-dark rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-brand-border group-hover:border-brand-secondary">
                            <svg class="w-8 h-8 text-brand-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Support Expert</h3>
                        <p class="text-gray-400">Une question ? Notre équipe vous répond sous 2h, 7j/7.</p>
                    </div>

                    <!-- Paiement -->
                    <div class="bg-brand-surface/50 backdrop-blur-sm border border-brand-border p-8 rounded-2xl text-center hover:border-brand-accent/50 transition-colors group">
                        <div class="w-16 h-16 bg-brand-dark rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 border border-brand-border group-hover:border-brand-accent">
                            <svg class="w-8 h-8 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-3">Paiement Sécurisé</h3>
                        <p class="text-gray-400">Transactions cryptées SSL. Carte bancaire, PayPal, Apple Pay.</p>
                    </div>
                </div>
                
            </div>
            
        <?php
        endwhile;
        ?>
        
    </div>
</div>

<?php
get_footer();