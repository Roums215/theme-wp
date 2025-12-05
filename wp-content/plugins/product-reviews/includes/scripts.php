<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Handles plugin shortcode.
 *
 * @since 1.0.0
 */
class Scripts {

	use Helpers;

	/**
	 * Initialize the class
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'public_enqueue_scripts' ) );
		add_action( 'admin_footer', array( $this, 'sktpr_plugin_wc_tooltips' ) );
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook The current admin page.
	 */
	public function admin_enqueue_scripts( $hook ) {

		if ( ! in_array( $hook, [ 'product-reviews_page_video-reviews-list', 'toplevel_page_skt-product-reviews' ] ) ) {
			return;
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Styles.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'sktpr_toastr', SKTPR_PLUGIN_URI . 'assets/libs/toastr/toastr.min.css', array(), SKTPR_VERSION );
		wp_enqueue_style( 'sktpr_admin', SKTPR_PLUGIN_URI . 'assets/admin/css/admin.min.css', array(), SKTPR_VERSION );

		// Scripts.
		wp_enqueue_script( 'sktpr_toastr', SKTPR_PLUGIN_URI . 'assets/libs/toastr/toastr.min.js', array(), SKTPR_VERSION, true );
		wp_enqueue_script( 'sktpr_admin', SKTPR_PLUGIN_URI . 'assets/admin/js/admin.min.js', array( 'wp-color-picker', 'jquery-tiptip' ), SKTPR_VERSION, true );

		wp_localize_script(
			'sktpr_admin',
			'sktpr_plugin',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'sktpr_plugin_nonce' ),
			)
		);
	}

	/**
	 * Enqueue public-facing scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function public_enqueue_scripts() {

		if ( is_product() ) {
			wp_enqueue_style( 'font-awesome', SKTPR_PLUGIN_URI . 'assets/libs/font-awesome/css/font-awesome.min.css', array(), SKTPR_VERSION );
		}
		wp_enqueue_media();
		wp_enqueue_style( 'sktpr_public', SKTPR_PLUGIN_URI . 'assets/public/css/public.min.css', array(), SKTPR_VERSION );
		wp_register_script( 'sktpr_public', SKTPR_PLUGIN_URI . 'assets/public/js/public.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'sktpr_public' );

		$settings = $this->get_settings();
		plugin()->generate_css->generate_custom_css( $settings );
		
		// Pass settings to JavaScript
		wp_localize_script( 'sktpr_public', 'sktpr_settings', array(
			'required_video_recording' => $settings['required_video_recording'] ?? false,
			'required_file_upload'     => $settings['required_file_upload'] ?? false,
		) );
	}

	/**
	 * Enqueue WooCommerce tooltips script.
	 *
	 * @since 1.0.0
	 */
	public function sktpr_plugin_wc_tooltips() {

		if ( class_exists( 'WooCommerce' ) ) {
			wc_enqueue_js(
				"
				jQuery(document).ready(function($) {
					$('.woocommerce-help-tip').tipTip({
						'attribute': 'data-tip',
						'fadeIn': 50,
						'fadeOut': 50,
						'delay': 200
					});
				});
			"
			);
		}
	}
}
