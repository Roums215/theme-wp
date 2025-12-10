<?php
/**
 * FORM-CHECKOUT.PHP - Page de paiement
 * 
 * @package MonECommerceTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div class="bg-brand-dark min-h-screen py-12">
    <div class="container mx-auto px-4">
        
        <h1 class="text-4xl font-display font-bold text-white mb-8 text-center">Finaliser la commande</h1>

        <div class="woocommerce">
            <?php

            wc_print_notices();


            if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
                echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
                return;
            }
            ?>

            <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

                <?php if ( $checkout->get_checkout_fields() ) : ?>

                    <div class="flex flex-col lg:flex-row gap-8">
                        
                        <!-- Colonne Gauche : Détails Client -->
                        <div class="lg:w-2/3">
                            
                            <!-- Facturation -->
                            <div class="bg-brand-surface border border-brand-border rounded-2xl p-8 mb-8 shadow-lg">
                                <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                                    <span class="bg-brand-primary text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                                    Détails de facturation
                                </h3>
                                
                                <div class="grid grid-cols-1 gap-4 text-gray-300">
                                    <?php do_action( 'woocommerce_checkout_billing' ); ?>
                                </div>
                            </div>

                            <!-- Expédition (si activée) -->
                            <div class="bg-brand-surface border border-brand-border rounded-2xl p-8 shadow-lg">
                                <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                                    <span class="bg-brand-primary text-white w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                                    Expédition
                                </h3>
                                
                                <div class="text-gray-300">
                                    <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                                </div>
                            </div>

                        </div>

                        <!-- Colonne Droite : Récapitulatif & Paiement -->
                        <div class="lg:w-1/3">
                            <div class="bg-brand-surface border border-brand-border rounded-2xl p-8 sticky top-24 shadow-lg">
                                <h3 class="text-2xl font-bold text-white mb-6 border-b border-brand-border pb-4">Votre Commande</h3>
                                
                                <!-- Review Order Table -->
                                <div class="mb-8 text-gray-300">
                                    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
                                </div>

                            </div>
                        </div>

                    </div>

                <?php endif; ?>

            </form>

            <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
        </div>
    </div>
</div>

<?php
get_footer();
```
