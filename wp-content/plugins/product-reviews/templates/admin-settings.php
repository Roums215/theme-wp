<?php

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

?>

<div class="sktpr-settings-wrap">
	<div class="sktpr-header">
		<h1><?php esc_html_e( 'Product Reviews Settings', 'product-reviews' ); ?></h1>
		<p><?php esc_html_e( 'Customize the video review experience for your customers.', 'product-reviews' ); ?></p>
	</div>

	<div class="sktpr-settings-container">
		<div class="sktpr-tabs">
			<button class="sktpr-tab active" data-tab="general"><?php esc_html_e( 'General', 'product-reviews' ); ?></button>
			<button class="sktpr-tab" data-tab="display"><?php esc_html_e( 'Display', 'product-reviews' ); ?></button>
			<button class="sktpr-tab" data-tab="email-reminders"><?php esc_html_e( 'Email Reminders', 'product-reviews' ); ?></button>
			<?php if( !pr_fs()->can_use_premium_code__premium_only() ): ?>
				<button class="sktpr-tab premium-tab" data-tab="premium">
					<?php esc_html_e( 'Premium Features', 'product-reviews' ); ?>
					<span class="sktpr-badge"><?php esc_html_e( 'PRO', 'product-reviews' ); ?></span>
				</button>
			<?php endif; ?>
		</div>

		<form action="" id="sktpr_plugin_settings" method="post" class="sktpr-tab-content active" data-tab-content="general">
			<div class="sktpr-settings-section">
				<h2><?php esc_html_e( 'Review Collection', 'product-reviews' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row" class="titledesc">
							<label for="enable_video_btn">
								<?php esc_html_e( 'Video Recording', 'product-reviews' ); ?>
							</label>
						</th>

						<td class="sktpr_video_btn">
							<input type="checkbox" name="enable_video_btn" id="enable_video_btn" <?php checked( $settings['enable_video_btn'], true ); ?> >
							<label for="enable_video_btn"><?php esc_html_e( 'Enable video recording via webcam', 'product-reviews' ); ?></label>
							<?php echo wp_kses_post( wc_help_tip( 'Allow customers to record video testimonials directly from product pages using their webcam', false ) ); ?>
						</td>
					</tr>

					<tr>
						<th scope="row" class="titledesc">
							<label for="required_video_recording">
								<?php esc_html_e( 'Required Video Recording', 'product-reviews' ); ?>
							</label>
						</th>
						<td class="sktpr_video_btn">
							<input type="checkbox" name="required_video_recording" id="required_video_recording" <?php checked( $settings['required_video_recording'] ?? false, true ); ?> >
							<label for="required_video_recording"><?php esc_html_e( 'Make video recording required', 'product-reviews' ); ?></label>
							<?php echo wp_kses_post( wc_help_tip( 'Require customers to record a video testimonial when submitting a review', false ) ); ?>
						</td>
					</tr>

					<tr>
						<th scope="row" class="titledesc">
							<label for="show_file_uploader">
								<?php esc_html_e( 'File Upload', 'product-reviews' ); ?>
							</label>
						</th>
						<td class="sktpr_video_btn">
							<input type="checkbox" name="show_file_uploader" id="show_file_uploader" <?php checked( $settings['show_file_uploader'], true ); ?> >
							<label for="show_file_uploader"><?php esc_html_e( 'Allow customers to upload video files', 'product-reviews' ); ?></label>
							<?php echo wp_kses_post( wc_help_tip( 'Allow customers to upload pre-recorded video testimonials', false ) ); ?>
						</td>
					</tr>

					<tr>
						<th scope="row" class="titledesc">
							<label for="required_file_upload">
								<?php esc_html_e( 'Required File Upload', 'product-reviews' ); ?>
							</label>
						</th>
						<td class="sktpr_video_btn">
							<input type="checkbox" name="required_file_upload" id="required_file_upload" <?php checked( $settings['required_file_upload'] ?? false, true ); ?> >
							<label for="required_file_upload"><?php esc_html_e( 'Make file upload required', 'product-reviews' ); ?></label>
							<?php echo wp_kses_post( wc_help_tip( 'Require customers to upload a video file when submitting a review', false ) ); ?>
						</td>
					</tr>

					<tr>
						<th scope="row" class="titledesc">
							<label for="auto_approve_video_reviews">
								<?php esc_html_e( 'Auto-Approve Video Reviews', 'product-reviews' ); ?>
							</label>
						</th>
						<td class="sktpr_video_btn">
							<input type="checkbox" name="auto_approve_video_reviews" id="auto_approve_video_reviews" <?php checked( $settings['auto_approve_video_reviews'] ?? false, true ); ?> >
							<label for="auto_approve_video_reviews"><?php esc_html_e( 'Automatically approve video reviews', 'product-reviews' ); ?></label>
							<?php echo wp_kses_post( wc_help_tip( 'When disabled, video reviews will be set to pending status and require admin approval. When enabled, video reviews will be automatically approved like regular reviews.', false ) ); ?>
							<p class="description"><?php esc_html_e( 'Unchecked: Video reviews require admin approval (recommended for quality control)', 'product-reviews' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<?php if( pr_fs()->can_use_premium_code__premium_only() ): ?>
				<div class="sktpr-settings-section">
					<h2><?php esc_html_e( 'Premium Settings', 'product-reviews' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row" class="titledesc">
								<label for="video_duration"><?php esc_html_e( 'Video Duration', 'product-reviews' ); ?></label>
							</th>
							<td class="forminp forminp-number">
								<input type="number" name="video_duration" id="video_duration" placeholder="2" step="1" min="1" max="10" value="<?php echo esc_attr( $settings['video_duration'] ?? 2 ); ?>">
								<span class="sktpr-unit"><?php esc_html_e( 'minutes', 'product-reviews' ); ?></span>
								<?php echo wp_kses_post( wc_help_tip( 'Set the maximum recording duration for video testimonials', false ) ); ?>
								<p class="description"><?php esc_html_e( 'Maximum allowed recording time for customer videos', 'product-reviews' ); ?></p>
							</td>
						</tr>
					</table>
				</div>
			<?php else: ?>

				<div class="sktpr-settings-section">
					<h2><?php esc_html_e( 'Premium Settings', 'product-reviews' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row" class="titledesc">
								<label for="video_duration"><?php esc_html_e( 'Video Duration', 'product-reviews' ); ?></label>
							</th>
							<td class="forminp forminp-number">
								<input type="number" name="video_duration" id="video_duration" placeholder="2" step="1" min="1" max="2" disabled value="<?php echo esc_attr( $settings['video_duration'] ?? 2 ); ?>">
								<span class="sktpr-unit"><?php esc_html_e( 'minutes', 'product-reviews' ); ?></span>
								<?php echo wp_kses_post( wc_help_tip( 'Set the maximum recording duration for video testimonials', false ) ); ?>
								<p class="description"><?php esc_html_e( 'Maximum allowed recording time for customer videos', 'product-reviews' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

			<?php endif; ?>

			<div class="sktpr-submit-section">
				<input type="submit" name="submit" id="sktpr_plugin_submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'product-reviews' ); ?>">
				<span class="sktpr_submit_successful" style="display:none;"><?php esc_html_e( 'Settings saved successfully!', 'product-reviews' ); ?></span>
			</div>
		</form>

		<div class="sktpr-tab-content" data-tab-content="display">
			<form action="" id="sktpr_plugin_settings_display" method="post">
				<div class="sktpr-settings-section">
					<h2><?php esc_html_e( 'Button Styling', 'product-reviews' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row" class="titledesc">
								<label for="review_btn_color"><?php esc_html_e( 'Button Background', 'product-reviews' ); ?></label>
							</th>
							<td class="forminp forminp-select">
								<input type="text" name="review_btn_color" id="review_btn_color" value="<?php echo esc_attr( $settings['review_btn_color'] ); ?>" />
								<?php echo wp_kses_post( wc_help_tip( 'Choose the background color for the video review button', false ) ); ?>
								<p class="description"><?php esc_html_e( 'Select a color that matches your brand identity', 'product-reviews' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" class="titledesc">
								<label for="review_btn_txt_color"><?php esc_html_e( 'Button Text Color', 'product-reviews' ); ?></label>
							</th>
							<td class="forminp forminp-select">
								<input type="text" name="review_btn_txt_color" id="review_btn_txt_color" value="<?php echo esc_attr( $settings['review_btn_txt_color'] ); ?>" />
								<?php echo wp_kses_post( wc_help_tip( 'Choose the text color for the video review button', false ) ); ?>
								<p class="description"><?php esc_html_e( 'Ensure good contrast with your background color', 'product-reviews' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row" class="titledesc">
								<label for="review_btn_text"><?php esc_html_e( 'Button Text', 'product-reviews' ); ?></label>
							</th>
							<td class="forminp forminp-select">
								<input type="text" name="review_btn_text" id="review_btn_text" value="<?php echo esc_attr( $settings['review_btn_text'] ); ?>" />
								<?php echo wp_kses_post( wc_help_tip( 'Customize the text displayed on the video review button', false ) ); ?>
								<p class="description"><?php esc_html_e( 'Enter the text you want to appear on the video review button', 'product-reviews' ); ?></p>
							</td>
						</tr>
					</table>
				</div>

				<div class="sktpr-submit-section">
					<input type="submit" name="submit" id="sktpr_plugin_submit_display" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'product-reviews' ); ?>">
					<span class="sktpr_submit_successful" style="display:none;"><?php esc_html_e( 'Settings saved successfully!', 'product-reviews' ); ?></span>
				</div>
			</form>
		</div>

		<div class="sktpr-tab-content" data-tab-content="email-reminders">
			<?php if ( isset( $email_settings ) ) $email_settings->render_email_settings_form(); ?>
		</div>

		<?php if( !pr_fs()->can_use_premium_code__premium_only() ): ?>
			<div class="sktpr-tab-content" data-tab-content="premium">
				<div class="sktpr-settings-section">
					<h2><?php esc_html_e( 'Unlock Premium Features', 'product-reviews' ); ?></h2>
					<div class="sktpr-premium-features">
						<div class="sktpr-feature">
							<div class="sktpr-feature-icon">
								<i class="dashicons dashicons-clock"></i>
							</div>
							<div class="sktpr-feature-content">
								<h3><?php esc_html_e( 'Custom Video Duration', 'product-reviews' ); ?></h3>
								<p><?php esc_html_e( 'Set custom recording times from 30 seconds to 10 minutes for your video testimonials.', 'product-reviews' ); ?></p>
							</div>
						</div>

						<div class="sktpr-feature">
							<div class="sktpr-feature-icon">
								<i class="dashicons dashicons-admin-appearance"></i>
							</div>
							<div class="sktpr-feature-content">
								<h3><?php esc_html_e( 'Advanced Styling', 'product-reviews' ); ?></h3>
								<p><?php esc_html_e( 'Customize the recording interface with your brand colors and logo.', 'product-reviews' ); ?></p>
							</div>
						</div>

						<div class="sktpr-feature">
							<div class="sktpr-feature-icon">
								<i class="dashicons dashicons-editor-help"></i>
							</div>
							<div class="sktpr-feature-content">
								<h3><?php esc_html_e( 'Custom Questions', 'product-reviews' ); ?></h3>
								<p><?php esc_html_e( 'Prompt customers with custom questions before recording their testimonials.', 'product-reviews' ); ?></p>
							</div>
						</div>

						<div class="sktpr-feature">
							<div class="sktpr-feature-icon">
								<i class="dashicons dashicons-shield"></i>
							</div>
							<div class="sktpr-feature-content">
								<h3><?php esc_html_e( 'Moderation Tools', 'product-reviews' ); ?></h3>
								<p><?php esc_html_e( 'Review and approve video testimonials before they go live on your site.', 'product-reviews' ); ?></p>
							</div>
						</div>
					</div>

					<div class="sktpr-premium-cta">
						<a href="<?php echo esc_url( site_url( '/wp-admin/admin.php?page=skt-product-reviews-pricing' ) ); ?>" class="button button-primary button-hero">
							<?php esc_html_e( 'Upgrade to Premium', 'product-reviews' ); ?>
						</a>
						<p><?php esc_html_e( '30-day money-back guarantee. No risk, all reward!', 'product-reviews' ); ?></p>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>