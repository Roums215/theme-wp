<?php
/**
 * CART.PHP - Panier
 * 
 * @package MonECommerceTheme
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>


<div class="bg-brand-dark min-h-screen py-16">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-12 text-center">Votre Panier</h1>

        <div class="woocommerce">
            <?php
            // Affichage des notices (succès, erreur...)
            wc_print_notices();
            ?>

            <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                
                <div class="flex flex-col xl:flex-row gap-12">
                    
                    <!-- Liste des produits -->
                    <div class="xl:w-2/3">
                        <div class="bg-brand-surface/30 backdrop-blur-md border border-brand-border rounded-3xl overflow-hidden shadow-2xl">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-brand-dark/80 text-gray-400 uppercase text-xs tracking-wider font-bold">
                                    <tr>
                                        <th class="p-6">&nbsp;</th>
                                        <th class="p-6">Produit</th>
                                        <th class="p-6">Prix</th>
                                        <th class="p-6">Quantité</th>
                                        <th class="p-6">Total</th>
                                        <th class="p-6">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-brand-border">
                                    <?php
                                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                            $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                            ?>
                                            <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?> hover:bg-brand-surface/50 transition-colors group">

                                                <!-- Image -->
                                                <td class="p-6 w-32">
                                                    <?php
                                                    $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                                    if ( ! $product_permalink ) {
                                                        echo $thumbnail;
                                                    } else {
                                                        printf( '<a href="%s" class="block rounded-xl overflow-hidden border border-brand-border group-hover:border-brand-primary/50 transition-colors">%s</a>', esc_url( $product_permalink ), $thumbnail );
                                                    }
                                                    ?>
                                                </td>

                                                <!-- Nom -->
                                                <td class="p-6">
                                                    <?php
                                                    if ( ! $product_permalink ) {
                                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                                    } else {
                                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s" class="font-bold text-lg text-white hover:text-brand-primary transition-colors">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                                    }

                                                    do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                                    // Meta data
                                                    echo wc_get_formatted_cart_item_data( $cart_item );

                                                    // Backorder notification
                                                    if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                                        echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="text-yellow-500 text-sm mt-1">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                                    }
                                                    ?>
                                                </td>

                                                <!-- Prix -->
                                                <td class="p-6 text-gray-300 font-medium">
                                                    <?php
                                                    echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                                                    ?>
                                                </td>

                                                <!-- Quantité -->
                                                <td class="p-6">
                                                    <?php
                                                    if ( $_product->is_sold_individually() ) {
                                                        $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                                    } else {
                                                        $product_quantity = woocommerce_quantity_input(
                                                            array(
                                                                'input_name'   => "cart[{$cart_item_key}][qty]",
                                                                'input_value'  => $cart_item['quantity'],
                                                                'max_value'    => $_product->get_max_purchase_quantity(),
                                                                'min_value'    => '0',
                                                                'product_name' => $_product->get_name(),
                                                                'classes'      => 'bg-brand-dark border border-brand-border text-white rounded-lg px-3 py-2 w-20 text-center focus:border-brand-primary focus:outline-none font-bold',
                                                            ),
                                                            $_product,
                                                            false
                                                        );
                                                    }

                                                    echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                                                    ?>
                                                </td>

                                                <!-- Total -->
                                                <td class="p-6 font-bold text-brand-accent text-lg">
                                                    <?php
                                                    echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                                                    ?>
                                                </td>

                                                <!-- Supprimer -->
                                                <td class="p-6 text-right">
                                                    <?php
                                                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                        'woocommerce_cart_item_remove_link',
                                                        sprintf(
                                                            '<a href="%s" class="text-gray-500 hover:text-red-500 transition-colors p-2 hover:bg-red-500/10 rounded-full" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></a>',
                                                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                            esc_html__( 'Remove this item', 'woocommerce' ),
                                                            esc_attr( $product_id ),
                                                            esc_attr( $_product->get_sku() )
                                                        ),
                                                        $cart_item_key
                                                    );
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            
                            <!-- Actions Panier (Code promo + Update) -->
                            <div class="p-8 bg-brand-dark/30 border-t border-brand-border flex flex-col md:flex-row justify-between items-center gap-6">
                                
                                <?php if ( wc_coupons_enabled() ) { ?>
                                    <div class="flex gap-3 w-full md:w-auto">
                                        <input type="text" name="coupon_code" class="bg-brand-dark border border-brand-border text-white rounded-xl px-6 py-3 w-full focus:border-brand-primary focus:outline-none" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Code promo', 'woocommerce' ); ?>" /> 
                                        <button type="submit" class="bg-brand-surface border border-brand-border text-white px-8 py-3 rounded-xl hover:bg-brand-border transition-colors whitespace-nowrap font-medium" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>">
                                            Appliquer
                                        </button>
                                    </div>
                                <?php } ?>

                                <button type="submit" class="text-brand-primary hover:text-white font-bold transition-colors uppercase tracking-wide text-sm" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
                                    <?php esc_html_e( 'Mettre à jour le panier', 'woocommerce' ); ?>
                                </button>

                                <?php do_action( 'woocommerce_cart_actions' ); ?>
                                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Totaux -->
                    <div class="xl:w-1/3">
                        <div class="bg-brand-surface/30 backdrop-blur-md border border-brand-border rounded-3xl p-10 sticky top-28 shadow-2xl">
                            <h2 class="text-2xl font-bold text-white mb-8 border-b border-brand-border pb-6">Récapitulatif</h2>
                            
                            <div class="space-y-6 text-gray-300 mb-10">
                                <!-- Sous-total -->
                                <div class="flex justify-between items-center text-lg">
                                    <span>Sous-total</span>
                                    <span class="font-bold text-white"><?php wc_cart_totals_subtotal_html(); ?></span>
                                </div>

                                <!-- Expédition -->
                                <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
                                    <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
                                    <?php wc_cart_totals_shipping_html(); ?>
                                    <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
                                <?php endif; ?>

                                <!-- Total -->
                                <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
                                <div class="flex justify-between items-center text-2xl font-bold text-white border-t border-brand-border pt-6 mt-6">
                                    <span>Total</span>
                                    <span class="text-brand-accent"><?php wc_cart_totals_order_total_html(); ?></span>
                                </div>
                                <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
                            </div>

                            <div class="wc-proceed-to-checkout">
                                <?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
                            </div>
                        </div>
                    </div>

                </div>
                
                <?php do_action( 'woocommerce_after_cart_table' ); ?>
            </form>

            <?php do_action( 'woocommerce_after_cart' ); ?>
        </div>
    </div>
</div>

<?php
get_footer();
