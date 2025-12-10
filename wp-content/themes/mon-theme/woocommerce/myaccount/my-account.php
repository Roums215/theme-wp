<?php
/**
 * MY-ACCOUNT.PHP - Page Mon Compte
 * 
 * @package MonECommerceTheme
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="bg-brand-dark min-h-screen py-24">
    <div class="w-full px-4 md:px-12">
        
        <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-12 text-center">Mon Espace</h1>

        <div class="woocommerce">
            <?php wc_print_notices(); ?>

            <?php if ( ! is_user_logged_in() ) : ?>
                
                <!-- LOGIN / REGISTER -->
                <div class="max-w-6xl mx-auto bg-brand-surface/50 backdrop-blur-xl border border-brand-border rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
                    
                    <!-- Login -->
                    <div class="w-full md:w-1/2 p-12 md:p-20 border-b md:border-b-0 md:border-r border-brand-border">
                        <h2 class="text-3xl font-bold text-white mb-8">Connexion</h2>
                        <?php do_action( 'woocommerce_login_form' ); ?>
                    </div>

                    <!-- Register -->
                    <?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
                        <div class="w-full md:w-1/2 p-12 md:p-20 bg-brand-dark/30">
                            <h2 class="text-3xl font-bold text-white mb-8">Créer un compte</h2>
                            <?php do_action( 'woocommerce_register_form' ); ?>
                        </div>
                    <?php endif; ?>

                </div>

            <?php else : ?>

                <!-- DASHBOARD LOGGED IN -->
                <div class="flex flex-col lg:flex-row gap-12">
                    
                    <!-- Navigation Latérale -->
                    <aside class="">
                        <div class="bg-brand-surface/50 backdrop-blur-md border border-brand-border rounded-2xl overflow-hidden shadow-lg sticky top-32">
                            <?php do_action( 'woocommerce_account_navigation' ); ?>
                        </div>
                    </aside>

                    <!-- Contenu Principal -->
                    <div class="lg:w-4/5">
                        <div class="bg-brand-surface/30 backdrop-blur-sm border border-brand-border rounded-3xl p-10 md:p-16 shadow-2xl min-h-[600px] text-gray-300">
                            <?php
                                /**
                                 * My Account content.
                                 * @since 2.6.0
                                 */
                                do_action( 'woocommerce_account_content' );
                            ?>
                        </div>
                    </div>

                </div>

            <?php endif; ?>

        </div>
    </div>
</div>

<?php
get_footer(); ?>
