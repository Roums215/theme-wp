<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Save_Video
 *
 * Handles the saving of video files uploaded with comments.
 */
class Save_Video {

	/**
	 * Save_Video constructor.
	 * Adds the action to save video on comment post.
	 */
	public function __construct() {
		add_action( 'comment_post', array( $this, 'save_video' ) );
		add_filter( 'pre_comment_approved', array( $this, 'set_video_review_status' ), 10, 2 );
	}

	/**
	 * Saves the uploaded video file associated with a comment.
	 *
	 * @param int $comment_id The ID of the comment.
	 */
	public function save_video( $comment_id ) {

		if ( isset( $_POST['my_form_nonce_name'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['my_form_nonce_name'] ) ), 'my_form_nonce_action' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$files = array();
			if ( isset( $_FILES['sktpr_client_video_upload'] ) && is_array( $_FILES['sktpr_client_video_upload'] ) && ! empty( $_FILES['sktpr_client_video_upload'] ) ) {
				$files['sktpr_client_video_upload'] = array_map( 'sanitize_text_field', $_FILES['sktpr_client_video_upload'] );
			}

			if ( isset( $_FILES['sktpr_file_upload'] ) && is_array( $_FILES['sktpr_file_upload'] ) && ! empty( $_FILES['sktpr_file_upload'] ) ) {
				$files['sktpr_file_upload'] = array_map( 'sanitize_text_field', $_FILES['sktpr_file_upload'] );
			}

			if ( ! empty( $files ) ) {
				foreach ( $files as $file_key => $file_array ) {

					$attachment_id = media_handle_upload( $file_key, $comment_id );

					if ( is_wp_error( $attachment_id ) ) {
						continue;
					}

					if ( $attachment_id > 0 ) {
						$allowed_video_types = array( 'video/mp4', 'video/webm', 'video/x-matroska' );

						if ( in_array( $file_array['type'], $allowed_video_types, true ) ) {
							$attachment_url[] = wp_get_attachment_url( $attachment_id );
						}
					}
				}
				if ( isset( $attachment_url ) && ! empty( $attachment_url ) ) {
					update_comment_meta( $comment_id, 'uploaded_video_url', $attachment_url, true );
				}
			}
		}
	}

	/**
	 * Set video reviews to pending status by default
	 *
	 * @param int|string|WP_Error $approved The approval status
	 * @param array $commentdata Comment data
	 * @return int|string|WP_Error Modified approval status
	 */
	public function set_video_review_status( $approved, $commentdata ) {
		// Check if this is a video review submission
		$has_video = false;
		
		// Check for video files in the submission
		if ( isset( $_FILES['sktpr_client_video_upload'] ) && ! empty( $_FILES['sktpr_client_video_upload']['name'] ) ) {
			$has_video = true;
		}
		
		if ( isset( $_FILES['sktpr_file_upload'] ) && ! empty( $_FILES['sktpr_file_upload']['name'] ) ) {
			$has_video = true;
		}
		
		// If this is a video review, set it to pending for admin approval
		if ( $has_video ) {
			// Check if admin wants video reviews to be auto-approved
			$settings = get_option( 'sktpr_review_settings', array() );
			$auto_approve_videos = isset( $settings['auto_approve_video_reviews'] ) ? $settings['auto_approve_video_reviews'] : false;
			
			// Debug: Log the setting value (remove this in production)
			error_log( 'SKTPR Debug: auto_approve_video_reviews setting = ' . var_export( $auto_approve_videos, true ) );
			
			if ( ! $auto_approve_videos ) {
				// Add action to notify admin about pending video review
				add_action( 'comment_post', array( $this, 'notify_admin_pending_video_review' ), 20 );
				
				// Set to pending (0) for admin approval
				return 0;
			}
		}
		
		// Return original approval status for non-video reviews
		return $approved;
	}

	/**
	 * Notify admin about pending video review
	 *
	 * @param int $comment_id Comment ID
	 */
	public function notify_admin_pending_video_review( $comment_id ) {
		// Check if this comment has video
		$video_urls = get_comment_meta( $comment_id, 'uploaded_video_url', true );
		if ( empty( $video_urls ) ) {
			return;
		}

		$comment = get_comment( $comment_id );
		if ( ! $comment || $comment->comment_approved !== '0' ) {
			return;
		}

		// Get admin email
		$admin_email = get_option( 'admin_email' );
		$site_name = get_bloginfo( 'name' );
		$product = get_post( $comment->comment_post_ID );
		
		// Email subject
		$subject = sprintf( 
			__( '[%s] New Video Review Awaiting Approval', 'product-reviews' ), 
			$site_name 
		);
		
		// Email message
		$message = sprintf(
			__( "A new video review has been submitted and is awaiting your approval.\n\n" .
				"Product: %s\n" .
				"Author: %s (%s)\n" .
				"Review: %s\n\n" .
				"Please review and approve it here:\n%s\n\n" .
				"You can manage all video reviews here:\n%s", 'product-reviews' ),
			$product ? $product->post_title : __( 'Unknown Product', 'product-reviews' ),
			$comment->comment_author,
			$comment->comment_author_email,
			wp_trim_words( $comment->comment_content, 20 ),
			admin_url( 'comment.php?action=editcomment&c=' . $comment_id ),
			admin_url( 'admin.php?page=video-reviews-list&comment_status=pending' )
		);
		
		// Send notification email
		wp_mail( $admin_email, $subject, $message );
		
		// Add admin notice for next admin page load
		$this->add_admin_notice_for_pending_review( $comment_id );
	}

	/**
	 * Add admin notice for pending video review
	 *
	 * @param int $comment_id Comment ID
	 */
	private function add_admin_notice_for_pending_review( $comment_id ) {
		$pending_notices = get_transient( 'sktpr_pending_video_reviews' );
		if ( ! is_array( $pending_notices ) ) {
			$pending_notices = array();
		}
		
		$pending_notices[] = $comment_id;
		set_transient( 'sktpr_pending_video_reviews', $pending_notices, DAY_IN_SECONDS );
	}
}
