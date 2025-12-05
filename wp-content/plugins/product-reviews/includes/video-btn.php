<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Video_Btn
 *
 * Handles the addition of a video button to the WooCommerce product review form.
 */
class Video_Btn {

	use Helpers;

	/**
	 * Video_Btn constructor.
	 * Adds actions and filters for the video button functionality.
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'add_enctype_to_review_form' ) );
		add_filter( 'woocommerce_product_review_comment_form_args', array( $this, 'add_custom_review_field' ) );
	}

	/**
	 * Adds enctype attribute to the review form.
	 */
	public function add_enctype_to_review_form() {

		if ( is_product() ) {
			wp_enqueue_script( 'sktpr_public' );
		}
	}

	/**
	 * Adds a custom video upload field to the WooCommerce product review form.
	 *
	 * @param array $args The arguments for the comment form.
	 * @return array Modified comment form arguments.
	 */
	public function add_custom_review_field( $args ) {

		$settings = $this->get_settings();

		$button = '';
		if ( $settings['enable_video_btn'] ) {

			$button = sprintf(
				'<div class="skt-input-field">
							<div class="skt-video-wrapper" style="display: none;">
								<video playsinline controls src="" type="video/mp4"></video>
							</div>
							<div class="sktpr_video_uploader"><a href="#" id="sktpr_modal_btn"><i class="fa fa-video-camera" aria-hidden="true"></i>%s</a></div>
							<input type="file" name="sktpr_client_video_upload" id="sktpr_client_video_upload" accept="video/mp4, video/x-m4v,video/webm,video/*" />
						</div>',
				$settings['review_btn_text']
			);
		}

		$rating = '';
		if ( wc_review_ratings_enabled() ) {
			$rating = '<div class="comment-form-rating">
			<label for="rating">' . esc_html__( 'Your rating', 'product-reviews' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label>
						<select name="rating" id="rating" required>
							<option value="">Rate&hellip;</option>
							<option value="5">Perfect</option>
							<option value="4">Good</option>
							<option value="3">Average</option>
							<option value="2">Not that bad</option>
							<option value="1">Very poor</option>
						</select>
			</div>';
		}

		$modal = $this->modal_html();

		$text_comment = '<p class="comment-form-comment">
                <label for="comment">Your review&nbsp;<span class="required">*</span></label>
                <textarea id="comment" name="comment" cols="45" rows="8" ></textarea>
            </p>';

		$file_input = '';
		if ( $settings['show_file_uploader'] ) {
			if ( $settings['show_file_uploader'] ) {
				$file_input = '<div class="sktpr_file_uploader"><input type="file" name="sktpr_file_upload" id="sktpr_file_upload" accept="video/mp4, video/x-m4v,video/webm,video/*"></div><br><video playsinline controls src="" style="display: none;max-width: 450px;" class="inp_file_video" type="video/mp4"></video>';
			}
		}

		$args['comment_field']     = sprintf( '<div class="comment-form-rating">%s %s</div>%s %s %s', $rating, $modal, $text_comment, $button, $file_input );
		$args['fields']['comment'] = false;

		return $args;
	}
}
