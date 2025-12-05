<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Email_Settings
 * 
 * Handles email reminder settings and admin interface
 */
class Email_Settings {

	use Helpers;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'handle_settings_save' ) );
	}

	/**
	 * Handle settings form submission
	 */
	public function handle_settings_save() {
		if ( ! isset( $_POST['sktpr_email_settings_nonce'] ) || 
			 ! wp_verify_nonce( $_POST['sktpr_email_settings_nonce'], 'sktpr_save_email_settings' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['submit_email_settings'] ) ) {
			$settings = $this->sanitize_email_settings( $_POST );
			$this->update_email_settings( $settings );
			
			add_action( 'admin_notices', function() {
				echo '<div class="notice notice-success is-dismissible"><p>' . 
					 __( 'Email reminder settings saved successfully!', 'product-reviews' ) . 
					 '</p></div>';
			});
		}
	}

	/**
	 * Sanitize email settings
	 * 
	 * @param array $post_data POST data
	 * @return array
	 */
	private function sanitize_email_settings( $post_data ) {
		return array(
			'enable_email_reminders' => isset( $post_data['enable_email_reminders'] ) ? true : false,
			'trigger_order_status' => sanitize_text_field( $post_data['trigger_order_status'] ?? 'completed' ),
			'email_delay_days' => absint( $post_data['email_delay_days'] ?? 7 ),
			'from_name' => sanitize_text_field( $post_data['from_name'] ?? get_bloginfo( 'name' ) ),
			'from_email' => sanitize_email( $post_data['from_email'] ?? get_option( 'admin_email' ) ),
			'email_subject' => sanitize_text_field( $post_data['email_subject'] ?? '' ),
			'email_content' => wp_kses_post( $post_data['email_content'] ?? '' )
		);
	}

	/**
	 * Get available order statuses
	 * 
	 * @return array
	 */
	public function get_order_statuses() {
		$statuses = wc_get_order_statuses();
		
		// Remove wc- prefix for internal use
		$clean_statuses = array();
		foreach ( $statuses as $key => $label ) {
			$clean_key = 'wc-' === substr( $key, 0, 3 ) ? substr( $key, 3 ) : $key;
			$clean_statuses[ $clean_key ] = $label;
		}
		
		return $clean_statuses;
	}

	/**
	 * Render email settings form
	 */
	public function render_email_settings_form() {
		$settings = $this->get_email_settings();
		$order_statuses = $this->get_order_statuses();
		
		?>
		<div class="sktpr-settings-section">
			<h2><?php esc_html_e( 'Email Reminder Settings', 'product-reviews' ); ?></h2>
			<p><?php esc_html_e( 'Configure automated email reminders to encourage customers to leave product reviews.', 'product-reviews' ); ?></p>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'sktpr_save_email_settings', 'sktpr_email_settings_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="enable_email_reminders">
								<?php esc_html_e( 'Enable Email Reminders', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<input type="checkbox" name="enable_email_reminders" id="enable_email_reminders" 
								   <?php checked( $settings['enable_email_reminders'], true ); ?> />
							<label for="enable_email_reminders"><?php esc_html_e( 'Send automated review reminder emails', 'product-reviews' ); ?></label>
							<?php echo wp_kses_post( wc_help_tip( 'Enable automated email reminders for product reviews', false ) ); ?>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="trigger_order_status">
								<?php esc_html_e( 'Trigger Order Status', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<select name="trigger_order_status" id="trigger_order_status">
								<?php foreach ( $order_statuses as $status => $label ) : ?>
									<option value="<?php echo esc_attr( $status ); ?>" 
											<?php selected( $settings['trigger_order_status'], $status ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
							<?php echo wp_kses_post( wc_help_tip( 'Send reminder when order reaches this status', false ) ); ?>
							<p class="description">
								<?php esc_html_e( 'Send reminder when order reaches this status.', 'product-reviews' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="email_delay_days">
								<?php esc_html_e( 'Email Delay (Days)', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<input type="number" name="email_delay_days" id="email_delay_days" 
								   value="<?php echo esc_attr( $settings['email_delay_days'] ); ?>" 
								   min="1" max="365" style="width: 80px;" />
							<span class="sktpr-unit"><?php esc_html_e( 'days', 'product-reviews' ); ?></span>
							<?php echo wp_kses_post( wc_help_tip( 'Number of days to wait after order status change before sending reminder', false ) ); ?>
							<p class="description">
								<?php esc_html_e( 'Number of days to wait after order status change before sending reminder.', 'product-reviews' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="from_name">
								<?php esc_html_e( 'From Name', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="from_name" id="from_name" 
								   value="<?php echo esc_attr( $settings['from_name'] ); ?>" 
								   class="regular-text" />
							<?php echo wp_kses_post( wc_help_tip( 'Name that appears in the "From" field of reminder emails', false ) ); ?>
							<p class="description">
								<?php esc_html_e( 'Name that appears in the "From" field of reminder emails.', 'product-reviews' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="from_email">
								<?php esc_html_e( 'From Email', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<input type="email" name="from_email" id="from_email" 
								   value="<?php echo esc_attr( $settings['from_email'] ); ?>" 
								   class="regular-text" />
							<?php echo wp_kses_post( wc_help_tip( 'Email address that appears in the "From" field of reminder emails', false ) ); ?>
							<p class="description">
								<?php esc_html_e( 'Email address that appears in the "From" field of reminder emails.', 'product-reviews' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="email_subject">
								<?php esc_html_e( 'Email Subject', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<input type="text" name="email_subject" id="email_subject" 
								   value="<?php echo esc_attr( $settings['email_subject'] ); ?>" 
								   class="large-text" />
							<?php echo wp_kses_post( wc_help_tip( 'Subject line for reminder emails. Use {customer_name}, {order_number}, {site_name} as placeholders', false ) ); ?>
							<p class="description">
								<?php esc_html_e( 'Subject line for reminder emails. Use {customer_name}, {order_number}, {site_name} as placeholders.', 'product-reviews' ); ?>
							</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">
							<label for="email_content">
								<?php esc_html_e( 'Email Content', 'product-reviews' ); ?>
							</label>
						</th>
						<td>
							<?php
							wp_editor( 
								$settings['email_content'], 
								'email_content',
								array(
									'textarea_name' => 'email_content',
									'textarea_rows' => 15,
									'media_buttons' => false,
									'teeny' => true
								)
							);
							?>
							<?php echo wp_kses_post( wc_help_tip( 'Email content template. Available placeholders: {customer_name}, {order_number}, {order_date}, {site_name}, {site_url}, {products_list}', false ) ); ?>
							<p class="description">
								<?php esc_html_e( 'Email content template. Available placeholders: {customer_name}, {order_number}, {order_date}, {site_name}, {site_url}, {products_list}', 'product-reviews' ); ?>
							</p>
						</td>
					</tr>
				</table>
				
				<div class="sktpr-submit-section">
					<input type="submit" name="submit_email_settings" class="button button-primary" 
						   value="<?php esc_attr_e( 'Save Email Settings', 'product-reviews' ); ?>" />
				</div>
			</form>
		</div>
		<?php
	}
}