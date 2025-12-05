<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;


/**
 * Class Hooks
 *
 * Handles AJAX requests for review settings.
 */
class Hooks {

	use Helpers;

	/**
	 * Constructor for the Hooks class.
	 */
	public function __construct() {
		add_filter( 'plugin_action_links_' . plugin_basename( SKTPR_PLUGIN_FILE ), array( $this, 'add_settings_link' ) );
		add_action( 'wp_ajax_get_review_settings', array( $this, 'handle_get_review_settings' ) );
	}

	/**
	 * Handles the AJAX request to get review settings.
	 */
	public function handle_get_review_settings() {

		check_admin_referer( 'sktpr_plugin_nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'status' => 'error' ) );
		}

		// Get current settings
		$current_settings = $this->get_settings();

		// Merge submitted data with current settings
		// Note: Checkboxes only send data when checked, so we need to handle unchecked state
		$data = array(
			'enable_video_btn'         => isset( $_POST['enable_video_btn'] ) ? 'on' : 'off',
			'show_file_uploader'       => isset( $_POST['show_file_uploader'] ) ? 'on' : 'off',
			'required_video_recording' => isset( $_POST['required_video_recording'] ) ? 'on' : 'off',
			'required_file_upload'     => isset( $_POST['required_file_upload'] ) ? 'on' : 'off',
			'auto_approve_video_reviews' => isset( $_POST['auto_approve_video_reviews'] ) ? 'on' : 'off',
			'video_duration'           => isset( $_POST['video_duration'] ) ? absint( wp_unslash( $_POST['video_duration'] ) ) : $current_settings['video_duration'],
			'review_btn_color'         => isset( $_POST['review_btn_color'] ) ? sanitize_text_field( wp_unslash( $_POST['review_btn_color'] ) ) : $current_settings['review_btn_color'],
			'review_btn_txt_color'     => isset( $_POST['review_btn_txt_color'] ) ? sanitize_text_field( wp_unslash( $_POST['review_btn_txt_color'] ) ) : $current_settings['review_btn_txt_color'],
			'review_btn_text'          => isset( $_POST['review_btn_text'] ) ? sanitize_text_field( wp_unslash( $_POST['review_btn_text'] ) ) : $current_settings['review_btn_text'],
			'button_position'          => isset( $_POST['button_position'] ) ? sanitize_text_field( wp_unslash( $_POST['button_position'] ) ) : $current_settings['button_position'],
		);

		$form_data = $this->validate_form_data( $data );

		// Debug: Log the form data being saved (remove this in production)
		error_log( 'SKTPR Debug: Saving settings = ' . var_export( $form_data, true ) );

		$is_updated = $this->update_settings( $form_data );

		if ( ! $is_updated ) {
			wp_send_json_error( array( 'status' => 'Something Went Wrong!' ) );
		}

		wp_send_json_success( array( 'status' => 'Successfully Saved' ) );
	}

	/**
	 * Adds a settings link to the plugin action links.
	 *
	 * @param array $links An array of plugin action links.
	 * @return array Modified array of plugin action links.
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf( 
			'<a href="%s">%s</a>',
			admin_url( 'admin.php?page=skt-product-reviews' ),
			__( 'Settings', 'product-reviews' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}
}
