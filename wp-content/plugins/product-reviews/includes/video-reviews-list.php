<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Video_Reviews_List
 *
 * Handles the display of video reviews in admin area.
 */
class Video_Reviews_List {

	/**
	 * Admin notices array
	 *
	 * @var array
	 */
	private $admin_notices = array();

	/**
	 * Video_Reviews_List constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_notices', array( $this, 'show_pending_video_review_notices' ) );
	}

	/**
	 * Add submenu for video reviews
	 */
	public function add_submenu() {
		add_submenu_page(
			'skt-product-reviews',
			__( 'Reviews', 'product-reviews' ),
			__( 'Reviews', 'product-reviews' ),
			'manage_options',
			'video-reviews-list',
			array( $this, 'display_video_reviews_page' )
		);
	}

	/**
	 * Enqueue admin scripts and styles
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'product-reviews_page_video-reviews-list' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'wp-list-table' );
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_style( 'dashicons' );
		
		// Add custom styles for video reviews table
		wp_add_inline_style( 'wp-list-table', '
			/* Vertical alignment for all table cells */
			.wp-list-table td {
				vertical-align: middle !important;
			}
			
			/* Ensure table rows have consistent height */
			.wp-list-table tbody tr {
				min-height: 80px;
			}
			
			/* Video preview styling */
			.sktpr-video-preview {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				text-align: center;
			}
			
			.sktpr-video-preview video {
				border-radius: 4px;
				border: 1px solid #ddd;
				margin-bottom: 5px;
				max-width: 100%;
				height: auto;
			}
			
			/* Star rating alignment */
			.sktpr-star-rating {
				display: flex;
				align-items: center;
				justify-content: flex-start;
				gap: 2px;
			}
			
			.sktpr-star-rating .dashicons {
				font-size: 16px;
				width: 16px;
				height: 16px;
			}
			
			.sktpr-rating-text {
				margin-left: 5px;
				font-size: 12px;
				color: #666;
			}
			
			/* Comment text styling */
			.sktpr-comment-text {
				max-width: 300px;
				line-height: 1.4;
			}
			
			/* Status labels */
			.sktpr-post-state {
				font-weight: 600;
				color: #d63638;
			}
			
			/* Author column alignment */
			.author-info {
				display: flex;
				align-items: center;
				gap: 10px;
			}
			
			.author-info img {
				flex-shrink: 0;
				border-radius: 50%;
			}
			
			.author-info .author-details {
				flex: 1;
				line-height: 1.3;
			}
			
			/* Date column styling */
			.date-info {
				text-align: center;
			}
			
			.date-main {
				font-weight: 500;
				margin-bottom: 2px;
			}
			
			.date-time {
				color: #666;
			}
			
			/* Product column alignment */
			.column-product {
				vertical-align: middle !important;
			}
			
			/* Date column alignment */
			.column-date {
				text-align: center;
				vertical-align: middle !important;
			}
			
			/* Column widths */
			.wp-list-table .column-video {
				width: 120px;
				text-align: center;
			}
			
			.wp-list-table .column-rating {
				width: 100px;
				text-align: left;
			}
			
			.wp-list-table .column-date {
				width: 120px;
			}
			
			.wp-list-table .column-author {
				width: 200px;
			}
			
			.wp-list-table .column-product {
				width: 180px;
			}
			
			/* Row actions alignment */
			.row-actions {
				margin-top: 5px;
			}
			
			/* Responsive adjustments */
			@media screen and (max-width: 1200px) {
				.sktpr-comment-text {
					max-width: 250px;
				}
			}
			
			@media screen and (max-width: 960px) {
				.wp-list-table .column-author {
					width: 150px;
				}
				
				.wp-list-table .column-product {
					width: 140px;
				}
				
				.sktpr-comment-text {
					max-width: 200px;
				}
			}
		' );
	}

	/**
	 * Display video reviews page
	 */
	public function display_video_reviews_page() {
		// Handle individual actions
		$this->process_individual_actions();
		
		// Handle bulk actions
		$this->process_bulk_actions();
		
		$list_table = new Video_Reviews_Table();
		$list_table->prepare_items();

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Video Reviews', 'product-reviews' ); ?></h1>
			<hr class="wp-header-end">

			<?php $this->display_admin_notices(); ?>
			<?php $list_table->views(); ?>

			<form method="post">
				<input type="hidden" name="page" value="video-reviews-list" />
				<?php wp_nonce_field( 'bulk-video-reviews' ); ?>
				<?php $list_table->search_box( __( 'Search reviews', 'product-reviews' ), 'video-review' ); ?>
				<?php $list_table->display(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Process individual actions
	 */
	private function process_individual_actions() {
		// Check if we have an individual action
		if ( empty( $_GET['action'] ) || empty( $_GET['comment_id'] ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );
		$comment_id = intval( $_GET['comment_id'] );

		// Verify nonce
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'video-review-action_' . $comment_id ) ) {
			wp_die( __( 'Security check failed.', 'product-reviews' ) );
		}

		// Process the action
		if ( $this->process_single_action( $action, $comment_id ) ) {
			$message = $this->get_single_action_message( $action );
			$this->add_admin_notice( $message, 'success' );
		} else {
			$this->add_admin_notice( __( 'Action failed. Please try again.', 'product-reviews' ), 'error' );
		}

		// Redirect to clean URL
		$redirect_url = remove_query_arg( array( 'action', 'comment_id', '_wpnonce' ) );
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Process bulk actions
	 */
	private function process_bulk_actions() {
		// Check if we have a bulk action
		$action = '';
		if ( isset( $_POST['action'] ) && $_POST['action'] !== '-1' ) {
			$action = sanitize_text_field( wp_unslash( $_POST['action'] ) );
		} elseif ( isset( $_POST['action2'] ) && $_POST['action2'] !== '-1' ) {
			$action = sanitize_text_field( wp_unslash( $_POST['action2'] ) );
		}

		if ( empty( $action ) ) {
			return;
		}

		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'bulk-video-reviews' ) ) {
			wp_die( __( 'Security check failed.', 'product-reviews' ) );
		}

		// Check if we have comments selected
		if ( empty( $_POST['comment'] ) || ! is_array( $_POST['comment'] ) ) {
			$this->add_admin_notice( __( 'No reviews selected.', 'product-reviews' ), 'error' );
			return;
		}

		$comment_ids = array_map( 'intval', $_POST['comment'] );
		$processed = 0;

		foreach ( $comment_ids as $comment_id ) {
			if ( $this->process_single_action( $action, $comment_id ) ) {
				$processed++;
			}
		}

		// Add success message
		if ( $processed > 0 ) {
			$message = $this->get_bulk_action_message( $action, $processed );
			$this->add_admin_notice( $message, 'success' );
		}

		// Redirect to avoid resubmission
		$redirect_url = remove_query_arg( array( 'action', 'action2', 'comment', '_wpnonce' ) );
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Process single action on a comment
	 *
	 * @param string $action Action to perform
	 * @param int $comment_id Comment ID
	 * @return bool Success status
	 */
	private function process_single_action( $action, $comment_id ) {
		if ( ! current_user_can( 'moderate_comments' ) ) {
			return false;
		}

		switch ( $action ) {
			case 'approve':
				return wp_set_comment_status( $comment_id, 'approve' );
			
			case 'unapprove':
				return wp_set_comment_status( $comment_id, 'hold' );
			
			case 'spam':
				return wp_spam_comment( $comment_id );
			
			case 'unspam':
				return wp_unspam_comment( $comment_id );
			
			case 'trash':
				return wp_trash_comment( $comment_id );
			
			case 'untrash':
				return wp_untrash_comment( $comment_id );
			
			default:
				return false;
		}
	}

	/**
	 * Get single action success message
	 *
	 * @param string $action Action performed
	 * @return string Message
	 */
	private function get_single_action_message( $action ) {
		switch ( $action ) {
			case 'approve':
				return __( 'Review approved.', 'product-reviews' );
			
			case 'unapprove':
				return __( 'Review unapproved.', 'product-reviews' );
			
			case 'spam':
				return __( 'Review marked as spam.', 'product-reviews' );
			
			case 'unspam':
				return __( 'Review restored from spam.', 'product-reviews' );
			
			case 'trash':
				return __( 'Review moved to trash.', 'product-reviews' );
			
			case 'untrash':
				return __( 'Review restored from trash.', 'product-reviews' );
			
			default:
				return __( 'Review updated.', 'product-reviews' );
		}
	}

	/**
	 * Get bulk action success message
	 *
	 * @param string $action Action performed
	 * @param int $count Number of items processed
	 * @return string Message
	 */
	private function get_bulk_action_message( $action, $count ) {
		switch ( $action ) {
			case 'approve':
				return sprintf( _n( '%d review approved.', '%d reviews approved.', $count, 'product-reviews' ), $count );
			
			case 'unapprove':
				return sprintf( _n( '%d review unapproved.', '%d reviews unapproved.', $count, 'product-reviews' ), $count );
			
			case 'spam':
				return sprintf( _n( '%d review marked as spam.', '%d reviews marked as spam.', $count, 'product-reviews' ), $count );
			
			case 'unspam':
				return sprintf( _n( '%d review restored from spam.', '%d reviews restored from spam.', $count, 'product-reviews' ), $count );
			
			case 'trash':
				return sprintf( _n( '%d review moved to trash.', '%d reviews moved to trash.', $count, 'product-reviews' ), $count );
			
			case 'untrash':
				return sprintf( _n( '%d review restored from trash.', '%d reviews restored from trash.', $count, 'product-reviews' ), $count );
			
			default:
				return sprintf( _n( '%d review processed.', '%d reviews processed.', $count, 'product-reviews' ), $count );
		}
	}

	/**
	 * Add admin notice
	 *
	 * @param string $message Notice message
	 * @param string $type Notice type (success, error, warning, info)
	 */
	private function add_admin_notice( $message, $type = 'success' ) {
		if ( ! isset( $this->admin_notices ) ) {
			$this->admin_notices = array();
		}
		
		$this->admin_notices[] = array(
			'message' => $message,
			'type' => $type
		);
	}

	/**
	 * Display admin notices
	 */
	private function display_admin_notices() {
		if ( empty( $this->admin_notices ) ) {
			return;
		}

		foreach ( $this->admin_notices as $notice ) {
			printf(
				'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
				esc_attr( $notice['type'] ),
				esc_html( $notice['message'] )
			);
		}
	}

	/**
	 * Get video reviews data
	 *
	 * @param array $args Query arguments
	 * @return array
	 */
	public static function get_video_reviews( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'number' => 20,
			'offset' => 0,
			'orderby' => 'comment_date',
			'order' => 'DESC',
			'search' => '',
			'status' => 'approve',
		);

		$args = wp_parse_args( $args, $defaults );

		// Base query to get comments with video meta
		$query = "
			SELECT DISTINCT c.*, cm.meta_value as video_urls, p.post_title, p.ID as product_id
			FROM {$wpdb->comments} c
			INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
			INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
			WHERE cm.meta_key = 'uploaded_video_url'
			AND p.post_type = 'product'
		";

		$query_args = array();

		// Handle status filtering
		if ( is_array( $args['status'] ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $args['status'] ), '%s' ) );
			$query .= " AND c.comment_approved IN ($placeholders)";
			$query_args = array_merge( $query_args, $args['status'] );
		} else {
			$query .= " AND c.comment_approved = %s";
			$query_args[] = $args['status'];
		}

		// Add search functionality
		if ( ! empty( $args['search'] ) ) {
			$query .= " AND (c.comment_content LIKE %s OR c.comment_author LIKE %s OR p.post_title LIKE %s)";
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$query_args[] = $search_term;
			$query_args[] = $search_term;
			$query_args[] = $search_term;
		}

		// Add ordering
		$allowed_orderby = array( 'comment_date', 'comment_author', 'post_title', 'comment_ID' );
		$orderby = in_array( $args['orderby'], $allowed_orderby ) ? $args['orderby'] : 'comment_date';
		$order = strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC';
		
		if ( $orderby === 'post_title' ) {
			$query .= " ORDER BY p.post_title {$order}";
		} else {
			$query .= " ORDER BY c.{$orderby} {$order}";
		}

		// Add pagination
		if ( $args['number'] > 0 ) {
			$query .= " LIMIT %d OFFSET %d";
			$query_args[] = $args['number'];
			$query_args[] = $args['offset'];
		}

		return $wpdb->get_results( $wpdb->prepare( $query, $query_args ) );
	}

	/**
	 * Get total count of video reviews
	 *
	 * @param array $args Query arguments
	 * @return int
	 */
	public static function get_video_reviews_count( $args = array() ) {
		global $wpdb;

		$defaults = array(
			'search' => '',
			'status' => 'approve',
		);

		$args = wp_parse_args( $args, $defaults );

		$query = "
			SELECT COUNT(DISTINCT c.comment_ID)
			FROM {$wpdb->comments} c
			INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
			INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
			WHERE cm.meta_key = 'uploaded_video_url'
			AND p.post_type = 'product'
		";

		$query_args = array();

		// Handle status filtering
		if ( is_array( $args['status'] ) ) {
			$placeholders = implode( ',', array_fill( 0, count( $args['status'] ), '%s' ) );
			$query .= " AND c.comment_approved IN ($placeholders)";
			$query_args = array_merge( $query_args, $args['status'] );
		} else {
			$query .= " AND c.comment_approved = %s";
			$query_args[] = $args['status'];
		}

		// Add search functionality
		if ( ! empty( $args['search'] ) ) {
			$query .= " AND (c.comment_content LIKE %s OR c.comment_author LIKE %s OR p.post_title LIKE %s)";
			$search_term = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$query_args[] = $search_term;
			$query_args[] = $search_term;
			$query_args[] = $search_term;
		}

		return (int) $wpdb->get_var( $wpdb->prepare( $query, $query_args ) );
	}

	/**
	 * Show admin notices for pending video reviews
	 */
	public function show_pending_video_review_notices() {
		// Only show on admin pages
		if ( ! is_admin() ) {
			return;
		}

		// Get pending notices
		$pending_notices = get_transient( 'sktpr_pending_video_reviews' );
		if ( empty( $pending_notices ) || ! is_array( $pending_notices ) ) {
			return;
		}

		// Count pending video reviews
		$pending_count = count( $pending_notices );
		
		if ( $pending_count > 0 ) {
			$message = sprintf(
				_n(
					'%d new video review is awaiting approval. <a href="%s">Review it now</a>',
					'%d new video reviews are awaiting approval. <a href="%s">Review them now</a>',
					$pending_count,
					'product-reviews'
				),
				$pending_count,
				admin_url( 'admin.php?page=video-reviews-list&comment_status=pending' )
			);
			
			printf(
				'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
				wp_kses_post( $message )
			);
		}

		// Clear the notices after showing them
		delete_transient( 'sktpr_pending_video_reviews' );
	}
}