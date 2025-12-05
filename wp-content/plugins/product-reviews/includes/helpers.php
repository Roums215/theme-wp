<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

trait Helpers {

	/**
	 * Generates the HTML for the video modal.
	 *
	 * @return string The HTML content for the video modal.
	 */
	public function modal_html() {

		$settings 		= $this->get_settings();
		$recording_time = intval( $settings['video_duration'] ?? 2 );
		ob_start();
		wp_nonce_field( 'my_form_nonce_action', 'my_form_nonce_name' );
		?>
		<div id="sktpr_video_modal" class="sktpr_video_modal">
			<div class="sktpr_background"></div>
			<!-- Modal content -->
			<div class="sktpr_modal-content-wrapper">
				<div class="sktpr_modal-content">
					<span class="sktpr_modal_close">&times;</span>
					<div class="sktpr_modal-content-inner">
						<h3 class="text-center"><img src="<?php echo esc_url( SKTPR_PLUGIN_URI . 'assets/img/video-icon.svg' ); ?>" alt="record video"><?php echo esc_html_e( 'Record Review', 'product-reviews' ); ?></h3>
						<div class="sktpr_preview-recording">
							<div id="sktpr_timer"><span id="sktpr_timer-text" data-maxtime="<?php echo esc_attr( $recording_time ); ?>" style="display: none;">05:00</span></div>
							<video playsinline id="sktpr_preview" width="450" height="337"  autoplay="" muted="" style="display: none;"></video>
							<video playsinline id="sktpr_recording" width="450" height="337" controls style="display: none;"></video>
							<div class="sktpr_no_camera text-center" style="display: none;">
								<div class="camera_inner">
									<img src="<?php echo esc_url( SKTPR_PLUGIN_URI . 'assets/img/video-icon.svg' ); ?>" alt="">
									<div><?php esc_html_e( 'No camera available', 'product-reviews' ); ?></div>
								</div>
							</div>
						</div>
						<div class="sktpr_record_video_buttons">
							<div id="sktpr_startButton" class="sktpr_video_button" style="display: none;">
							<i class="fa fa-video-camera" aria-hidden="true"></i><span id="sktpr_startButton_text">
									<?php esc_html_e( 'Start Recording', 'product-reviews' ); ?>
								</span>
							</div>
							<div id="sktpr_stopButton" class="sktpr_video_button stop_recording_btn" style="display: none;">
							<i class="fa fa-stop-circle" aria-hidden="true"></i>
								<?php esc_html_e( 'Stop Recording', 'product-reviews' ); ?>
							</div>
							<a id="sktpr_addButton" class="sktpr_video_button add_video_btn" style="display: none;">
							<i class="fa fa-plus-circle" aria-hidden="true"></i>
								<?php esc_html_e( 'Add this video', 'product-reviews' ); ?>
							</a>
						</div>
					</div>
					<p class="sktpr_modal-content-bottom text-center">
						<?php
							$sktpr_video_duration_unit = $recording_time >= 2 ? __( 'minutes', 'product-reviews' ) : __( 'minute', 'product-reviews' );
						?>
						<span><?php echo esc_html__( 'Maximum recording duration', 'product-reviews' ) . ' ' . esc_html( $recording_time ) . ' ' . esc_html( apply_filters( 'sktpr_video_duration_unit', $sktpr_video_duration_unit ) ); ?></span>
					</p>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Retrieves the default settings for the review.
	 *
	 * @return array The default settings.
	 */
	public function get_defaults() {

		return array(
			'enable_video_btn'        => true,
			'show_file_uploader'      => true,
			'required_video_recording'=> false,
			'required_file_upload'    => false,
			'auto_approve_video_reviews' => false,
			'video_duration'  	      => 2,
			'review_btn_color'        => '#481FEA',
			'review_btn_txt_color'    => '#FFFFFF',
			'review_btn_text'         => 'Record Video',
			'button_position'         => 'after_review_form',
		);
	}

	/**
	 * Validates and sanitizes the form data.
	 *
	 * @param array $form_data The form data to validate.
	 * @return array The validated and sanitized form data.
	 */
	public function validate_form_data( $form_data ) {

		$form_data['enable_video_btn']         = ( $form_data['enable_video_btn'] === 'on' );
		$form_data['show_file_uploader']       = ( $form_data['show_file_uploader'] === 'on' );
		$form_data['required_video_recording'] = ( $form_data['required_video_recording'] === 'on' );
		$form_data['required_file_upload']     = ( $form_data['required_file_upload'] === 'on' );
		$form_data['auto_approve_video_reviews'] = ( $form_data['auto_approve_video_reviews'] === 'on' );
		$form_data['video_duration']  	       = intval( $form_data['video_duration'] );
		$form_data['review_btn_color']         = sanitize_text_field( $form_data['review_btn_color'] );
		$form_data['review_btn_txt_color']     = sanitize_text_field( $form_data['review_btn_txt_color'] );
		$form_data['review_btn_text']          = sanitize_text_field( $form_data['review_btn_text'] );
		$form_data['button_position']          = sanitize_text_field( $form_data['button_position'] );

		return $form_data;
	}

	/**
	 * Updates the review settings.
	 *
	 * @param array $settings The new settings to update.
	 * @return bool True if the settings were updated, false otherwise.
	 */
	public function update_settings( $settings ) {

		if ( empty( $settings ) ) {
			return;
		}

		$prev_data = get_option( 'sktpr_review_settings' );

		if ( $prev_data === $settings ) {
			return true;
		}

		$is_updated = update_option( 'sktpr_review_settings', $settings );

		return $is_updated;
	}

	/**
	 * Retrieves the review settings.
	 *
	 * @return array The review settings.
	 */
	public function get_settings() {

		$settings = get_option( 'sktpr_review_settings', $this->get_defaults() );

		return $settings;
	}

	/**
	 * Get email reminder settings
	 * 
	 * @return array
	 */
	public function get_email_settings() {
		$defaults = array(
			'enable_email_reminders' => false,
			'trigger_order_status' => 'completed',
			'email_delay_days' => 7,
			'from_name' => get_bloginfo( 'name' ),
			'from_email' => get_option( 'admin_email' ),
			'email_subject' => __( 'How was your recent purchase, {customer_name}?', 'product-reviews' ),
			'email_content' => $this->get_default_email_content()
		);
		
		$settings = get_option( 'sktpr_email_reminder_settings', $defaults );
		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Update email settings
	 * 
	 * @param array $settings Settings array
	 * @return bool
	 */
	public function update_email_settings( $settings ) {
		return update_option( 'sktpr_email_reminder_settings', $settings );
	}

	/**
	 * Get default email content template
	 * 
	 * @return string
	 */
	private function get_default_email_content() {
		return '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
			<h2 style="color: #333;">Hi {customer_name},</h2>
			
			<p>Thank you for your recent purchase from {site_name}! We hope you\'re enjoying your new products.</p>
			
			<p>We\'d love to hear about your experience. Your feedback helps us improve and helps other customers make informed decisions.</p>
			
			<p><strong>Order #{order_number}</strong> placed on {order_date}</p>
			
			{products_list}
			
			<p>Thank you for taking the time to share your thoughts!</p>
			
			<p>Best regards,<br>
			The {site_name} Team</p>
			
			<hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
			<p style="font-size: 12px; color: #666;">
				This email was sent because you recently made a purchase from {site_name}. 
				If you have any questions, please contact us at <a href="mailto:' . get_option( 'admin_email' ) . '">' . get_option( 'admin_email' ) . '</a>
			</p>
		</div>';
	}
}