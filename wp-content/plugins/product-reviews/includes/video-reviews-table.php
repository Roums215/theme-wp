<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class Video_Reviews_Table
 *
 * Extends WP_List_Table to display video reviews in admin area.
 */
class Video_Reviews_Table extends \WP_List_Table {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'video-review',
			'plural'   => 'video-reviews',
			'ajax'     => false,
		) );
	}

	/**
	 * Get table columns
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />',
			'author'       => __( 'Author', 'product-reviews' ),
			'comment'      => __( 'Review', 'product-reviews' ),
			'product'      => __( 'Product', 'product-reviews' ),
			'rating'       => __( 'Rating', 'product-reviews' ),
			'video'        => __( 'Video', 'product-reviews' ),
			'date'         => __( 'Date', 'product-reviews' ),
		);
	}

	/**
	 * Get sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array(
			'author'  => array( 'comment_author', false ),
			'product' => array( 'post_title', false ),
			'date'    => array( 'comment_date', true ),
		);
	}

	/**
	 * Get bulk actions
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'trash'    => __( 'Move to Trash', 'product-reviews' ),
			'untrash'  => __( 'Restore', 'product-reviews' ),
			'spam'     => __( 'Mark as Spam', 'product-reviews' ),
			'unspam'   => __( 'Not Spam', 'product-reviews' ),
			'approve'  => __( 'Approve', 'product-reviews' ),
			'unapprove' => __( 'Unapprove', 'product-reviews' ),
		);
	}

	/**
	 * Prepare table items
	 */
	public function prepare_items() {
		$per_page = 20;
		$current_page = $this->get_pagenum();
		$offset = ( $current_page - 1 ) * $per_page;

		$search = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
		$orderby = isset( $_REQUEST['orderby'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['orderby'] ) ) : 'comment_date';
		$order = isset( $_REQUEST['order'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['order'] ) ) : 'desc';
		$comment_status = isset( $_REQUEST['comment_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['comment_status'] ) ) : 'all';

		// Map comment status to database values
		$status_map = array(
			'all' => 'approve',
			'approved' => '1',
			'pending' => '0',
			'spam' => 'spam',
			'trash' => 'trash',
		);

		$status = isset( $status_map[ $comment_status ] ) ? $status_map[ $comment_status ] : 'approve';
		if ( $comment_status === 'all' ) {
			$status = array( '1', '0' ); // Show approved and pending by default
		}

		$args = array(
			'number'  => $per_page,
			'offset'  => $offset,
			'search'  => $search,
			'orderby' => $orderby,
			'order'   => $order,
			'status'  => $status,
		);

		$this->items = Video_Reviews_List::get_video_reviews( $args );
		$total_items = Video_Reviews_List::get_video_reviews_count( array( 'search' => $search, 'status' => $status ) );

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );

		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);
	}

	/**
	 * Default column display
	 *
	 * @param object $item
	 * @param string $column_name
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'author':
				return $this->column_author( $item );
			case 'comment':
				return $this->column_comment( $item );
			case 'product':
				return $this->column_product( $item );
			case 'rating':
				return $this->column_rating( $item );
			case 'video':
				return $this->column_video( $item );
			case 'date':
				return $this->column_date( $item );
			default:
				return '';
		}
	}

	/**
	 * Checkbox column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="comment[]" value="%s" />',
			$item->comment_ID
		);
	}

	/**
	 * Author column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_author( $item ) {
		$avatar = get_avatar( $item->comment_author_email, 32 );
		$author_name = ! empty( $item->comment_author ) ? $item->comment_author : __( 'Anonymous', 'product-reviews' );
		
		$actions = array();
		$base_url = admin_url( 'admin.php?page=video-reviews-list' );
		
		// Edit action - use WordPress comment edit page
		$edit_url = admin_url( 'comment.php?action=editcomment&c=' . $item->comment_ID );
		$actions['edit'] = sprintf(
			'<a href="%s">%s</a>',
			$edit_url,
			__( 'Edit', 'product-reviews' )
		);

		// View action
		$comment_link = get_comment_link( $item->comment_ID );
		$actions['view'] = sprintf(
			'<a href="%s" target="_blank">%s</a>',
			$comment_link,
			__( 'View', 'product-reviews' )
		);

		// Status actions - use our custom handlers
		if ( $item->comment_approved === '1' ) {
			$actions['unapprove'] = sprintf(
				'<a href="%s">%s</a>',
				wp_nonce_url( add_query_arg( array( 'action' => 'unapprove', 'comment_id' => $item->comment_ID ), $base_url ), 'video-review-action_' . $item->comment_ID ),
				__( 'Unapprove', 'product-reviews' )
			);
		} elseif ( $item->comment_approved === '0' ) {
			$actions['approve'] = sprintf(
				'<a href="%s">%s</a>',
				wp_nonce_url( add_query_arg( array( 'action' => 'approve', 'comment_id' => $item->comment_ID ), $base_url ), 'video-review-action_' . $item->comment_ID ),
				__( 'Approve', 'product-reviews' )
			);
		}

		// Spam/Unspam actions
		if ( $item->comment_approved === 'spam' ) {
			$actions['unspam'] = sprintf(
				'<a href="%s">%s</a>',
				wp_nonce_url( add_query_arg( array( 'action' => 'unspam', 'comment_id' => $item->comment_ID ), $base_url ), 'video-review-action_' . $item->comment_ID ),
				__( 'Not Spam', 'product-reviews' )
			);
		} else {
			$actions['spam'] = sprintf(
				'<a href="%s">%s</a>',
				wp_nonce_url( add_query_arg( array( 'action' => 'spam', 'comment_id' => $item->comment_ID ), $base_url ), 'video-review-action_' . $item->comment_ID ),
				__( 'Spam', 'product-reviews' )
			);
		}

		// Trash/Untrash actions
		if ( $item->comment_approved === 'trash' ) {
			$actions['untrash'] = sprintf(
				'<a href="%s">%s</a>',
				wp_nonce_url( add_query_arg( array( 'action' => 'untrash', 'comment_id' => $item->comment_ID ), $base_url ), 'video-review-action_' . $item->comment_ID ),
				__( 'Restore', 'product-reviews' )
			);
		} else {
			$actions['trash'] = sprintf(
				'<a href="%s" class="submitdelete">%s</a>',
				wp_nonce_url( add_query_arg( array( 'action' => 'trash', 'comment_id' => $item->comment_ID ), $base_url ), 'video-review-action_' . $item->comment_ID ),
				__( 'Trash', 'product-reviews' )
			);
		}

		return sprintf(
			'<div class="author-info">
				%s
				<div class="author-details">
					<strong>%s</strong><br>
					<small>%s</small>
					%s
				</div>
			</div>',
			$avatar,
			esc_html( $author_name ),
			esc_html( $item->comment_author_email ),
			$this->row_actions( $actions )
		);
	}

	/**
	 * Comment column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_comment( $item ) {
		$comment_text = wp_trim_words( $item->comment_content, 20, '...' );
		
		$status_label = '';
		if ( $item->comment_approved === '0' ) {
			$status_label = ' — <span class="sktpr-post-state">' . __( 'Pending', 'product-reviews' ) . '</span>';
		} elseif ( $item->comment_approved === 'spam' ) {
			$status_label = ' — <span class="sktpr-post-state">' . __( 'Spam', 'product-reviews' ) . '</span>';
		} elseif ( $item->comment_approved === 'trash' ) {
			$status_label = ' — <span class="sktpr-post-state">' . __( 'Trash', 'product-reviews' ) . '</span>';
		}

		return sprintf(
			'<div class="sktpr-comment-text">%s%s</div>',
			esc_html( $comment_text ),
			$status_label
		);
	}

	/**
	 * Product column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_product( $item ) {
		$product_link = get_edit_post_link( $item->product_id );
		$product_view_link = get_permalink( $item->product_id );
		
		return sprintf(
			'<strong><a href="%s">%s</a></strong><br><a href="%s" target="_blank">%s</a>',
			esc_url( $product_link ),
			esc_html( $item->post_title ),
			esc_url( $product_view_link ),
			__( 'View Product', 'product-reviews' )
		);
	}

	/**
	 * Rating column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_rating( $item ) {
		$rating = get_comment_meta( $item->comment_ID, 'rating', true );
		
		if ( empty( $rating ) ) {
			return '—';
		}

		$stars = '';
		for ( $i = 1; $i <= 5; $i++ ) {
			if ( $i <= $rating ) {
				$stars .= '<span class="dashicons dashicons-star-filled" style="color: #ffb900;"></span>';
			} else {
				$stars .= '<span class="dashicons dashicons-star-empty" style="color: #ffb900;"></span>';
			}
		}

		return sprintf(
			'<div class="sktpr-star-rating">%s <span class="sktpr-rating-text">(%d/5)</span></div>',
			$stars,
			$rating
		);
	}

	/**
	 * Video column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_video( $item ) {
		$video_urls = maybe_unserialize( $item->video_urls );
		
		if ( empty( $video_urls ) ) {
			return '—';
		}

		// Flatten array if needed
		if ( is_array( $video_urls ) && isset( $video_urls[0] ) && is_array( $video_urls[0] ) ) {
			$video_urls = $video_urls[0];
		}

		if ( ! is_array( $video_urls ) ) {
			$video_urls = array( $video_urls );
		}

		$video_count = count( $video_urls );
		$first_video = $video_urls[0];

		return sprintf(
			'<div class="sktpr-video-preview">
				<video controls preload="metadata" style="max-width: 200px;">
					<source src="%s" type="video/mp4">
				</video>
				<br><small>%s</small>
			</div>',
			esc_url( $first_video ),
			sprintf( _n( '%d video', '%d videos', $video_count, 'product-reviews' ), $video_count )
		);
	}

	/**
	 * Date column
	 *
	 * @param object $item
	 * @return string
	 */
	public function column_date( $item ) {
		$date = mysql2date( get_option( 'date_format' ), $item->comment_date );
		$time = mysql2date( get_option( 'time_format' ), $item->comment_date );
		
		return sprintf(
			'<div class="date-info">
				<div class="date-main">%s</div>
				<div class="date-time"><small>%s</small></div>
			</div>',
			esc_html( $date ),
			esc_html( $time )
		);
	}

	/**
	 * Display when no items found
	 */
	public function no_items() {
		esc_html_e( 'No video reviews found.', 'product-reviews' );
	}

	/**
	 * Get views for different comment statuses
	 *
	 * @return array
	 */
	public function get_views() {
		global $wpdb;

		$views = array();
		$current = isset( $_REQUEST['comment_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['comment_status'] ) ) : 'all';

		// Get counts for each status
		$counts = $wpdb->get_results( "
			SELECT c.comment_approved, COUNT(*) as count
			FROM {$wpdb->comments} c
			INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
			INNER JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID
			WHERE cm.meta_key = 'uploaded_video_url'
			AND p.post_type = 'product'
			GROUP BY c.comment_approved
		" );

		$status_counts = array(
			'all' => 0,
			'approved' => 0,
			'pending' => 0,
			'spam' => 0,
			'trash' => 0,
		);

		foreach ( $counts as $count ) {
			$status_counts['all'] += $count->count;
			
			switch ( $count->comment_approved ) {
				case '1':
					$status_counts['approved'] = $count->count;
					break;
				case '0':
					$status_counts['pending'] = $count->count;
					break;
				case 'spam':
					$status_counts['spam'] = $count->count;
					break;
				case 'trash':
					$status_counts['trash'] = $count->count;
					break;
			}
		}

		$views['all'] = sprintf(
			'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
			admin_url( 'admin.php?page=video-reviews-list' ),
			$current === 'all' ? ' class="current"' : '',
			__( 'All', 'product-reviews' ),
			$status_counts['all']
		);

		if ( $status_counts['approved'] > 0 ) {
			$views['approved'] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				admin_url( 'admin.php?page=video-reviews-list&comment_status=approved' ),
				$current === 'approved' ? ' class="current"' : '',
				__( 'Approved', 'product-reviews' ),
				$status_counts['approved']
			);
		}

		if ( $status_counts['pending'] > 0 ) {
			$views['pending'] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				admin_url( 'admin.php?page=video-reviews-list&comment_status=pending' ),
				$current === 'pending' ? ' class="current"' : '',
				__( 'Pending', 'product-reviews' ),
				$status_counts['pending']
			);
		}

		if ( $status_counts['spam'] > 0 ) {
			$views['spam'] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				admin_url( 'admin.php?page=video-reviews-list&comment_status=spam' ),
				$current === 'spam' ? ' class="current"' : '',
				__( 'Spam', 'product-reviews' ),
				$status_counts['spam']
			);
		}

		if ( $status_counts['trash'] > 0 ) {
			$views['trash'] = sprintf(
				'<a href="%s"%s>%s <span class="count">(%d)</span></a>',
				admin_url( 'admin.php?page=video-reviews-list&comment_status=trash' ),
				$current === 'trash' ? ' class="current"' : '',
				__( 'Trash', 'product-reviews' ),
				$status_counts['trash']
			);
		}

		return $views;
	}
}