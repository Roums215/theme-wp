<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Reminders_List
 * 
 * Handles display of scheduled email reminders
 */
class Reminders_List {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_submenu' ), 20 );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
	}

	/**
	 * Add submenu for reminders
	 */
	public function add_submenu() {
		add_submenu_page(
			'skt-product-reviews',
			__( 'Email Reminders', 'product-reviews' ),
			__( 'Email Reminders', 'product-reviews' ),
			'manage_options',
			'skt-product-reviews-reminders',
			array( $this, 'display_reminders_page' )
		);
	}

	/**
	 * Handle reminder actions
	 */
	public function handle_actions() {
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'cancel' && isset( $_GET['order_id'] ) ) {
			$order_id = intval( $_GET['order_id'] );
			if ( wp_verify_nonce( $_GET['_wpnonce'], 'cancel_reminder_' . $order_id ) ) {
				$this->cancel_reminder( $order_id );
				wp_redirect( admin_url( 'admin.php?page=skt-product-reviews-reminders&cancelled=1' ) );
				exit;
			}
		}
	}

	/**
	 * Display reminders page
	 */
	public function display_reminders_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Email Reminders', 'product-reviews' ); ?></h1>
			<p><?php esc_html_e( 'View and manage scheduled email reminders for product reviews.', 'product-reviews' ); ?></p>
			
			<?php if ( isset( $_GET['cancelled'] ) ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Reminder cancelled successfully.', 'product-reviews' ); ?></p>
				</div>
			<?php endif; ?>
			
			<?php $this->display_reminders_table(); ?>
		</div>
		<?php
	}

	/**
	 * Display reminders table
	 */
	private function display_reminders_table() {
		$reminders = $this->get_scheduled_reminders();
		?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Order ID', 'product-reviews' ); ?></th>
					<th><?php esc_html_e( 'Customer', 'product-reviews' ); ?></th>
					<th><?php esc_html_e( 'Email', 'product-reviews' ); ?></th>
					<th><?php esc_html_e( 'Scheduled Time', 'product-reviews' ); ?></th>
					<th><?php esc_html_e( 'Status', 'product-reviews' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'product-reviews' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $reminders ) ) : ?>
					<tr>
						<td colspan="6" style="text-align: center; padding: 20px;">
							<?php esc_html_e( 'No scheduled reminders found.', 'product-reviews' ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $reminders as $reminder ) : ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $reminder['order_id'] . '&action=edit' ) ); ?>">
									#<?php echo esc_html( $reminder['order_number'] ); ?>
								</a>
							</td>
							<td><?php echo esc_html( $reminder['customer_name'] ); ?></td>
							<td><?php echo esc_html( $reminder['customer_email'] ); ?></td>
							<td>
								<?php 
								echo esc_html( 
									date_i18n( 
										get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), 
										$reminder['scheduled_time'] 
									) 
								); 
								?>
							</td>
							<td>
								<?php
								$status_colors = array(
									'scheduled' => '#0073aa',
									'sent' => '#00a32a',
									'cancelled' => '#d63638',
									'failed' => '#d63638'
								);
								$status_color = isset( $status_colors[ $reminder['status'] ] ) ? $status_colors[ $reminder['status'] ] : '#666';
								$status_text = ucfirst( $reminder['status'] );
								
								if ( $reminder['status'] === 'scheduled' && $reminder['scheduled_time'] < time() ) {
									$status_text = __( 'Overdue', 'product-reviews' );
									$status_color = '#d63638';
								}
								?>
								<span style="color: <?php echo esc_attr( $status_color ); ?>;">
									<?php echo esc_html( $status_text ); ?>
								</span>
								<?php if ( $reminder['sent_at'] ) : ?>
									<br><small><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $reminder['sent_at'] ) ) ); ?></small>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $reminder['status'] === 'scheduled' ) : ?>
									<a href="<?php echo esc_url( wp_nonce_url( 
										admin_url( 'admin.php?page=skt-product-reviews-reminders&action=cancel&order_id=' . $reminder['order_id'] ),
										'cancel_reminder_' . $reminder['order_id']
									) ); ?>" 
									   class="button button-small"
									   onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to cancel this reminder?', 'product-reviews' ); ?>')">
										<?php esc_html_e( 'Cancel', 'product-reviews' ); ?>
									</a>
								<?php elseif ( $reminder['status'] === 'sent' ) : ?>
									<span style="color: #00a32a;">✓ <?php esc_html_e( 'Sent', 'product-reviews' ); ?></span>
								<?php elseif ( $reminder['status'] === 'cancelled' ) : ?>
									<span style="color: #d63638;">✗ <?php esc_html_e( 'Cancelled', 'product-reviews' ); ?></span>
								<?php elseif ( $reminder['status'] === 'failed' ) : ?>
									<span style="color: #d63638;">⚠ <?php esc_html_e( 'Failed', 'product-reviews' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Get scheduled reminders
	 * 
	 * @return array
	 */
	private function get_scheduled_reminders() {
		$reminders_data = Database::get_reminders( array(
			'status' => '', // Get all statuses
			'limit' => 100,
			'orderby' => 'scheduled_time',
			'order' => 'ASC'
		) );
		
		$reminders = array();
		if ( $reminders_data ) {
			foreach ( $reminders_data as $reminder ) {
				$order = wc_get_order( $reminder->order_id );
				if ( $order ) {
					$reminders[] = array(
						'id' => $reminder->id,
						'order_id' => $reminder->order_id,
						'order_number' => $order->get_order_number(),
						'customer_name' => $reminder->customer_name,
						'customer_email' => $reminder->customer_email,
						'scheduled_time' => strtotime( $reminder->scheduled_time ),
						'status' => $reminder->status,
						'sent_at' => $reminder->sent_at,
						'created_at' => $reminder->created_at
					);
				}
			}
		}
		
		return $reminders;
	}

	/**
	 * Cancel a reminder
	 * 
	 * @param int $order_id Order ID
	 */
	private function cancel_reminder( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Cancel the scheduled event
		wp_clear_scheduled_hook( 'sktpr_send_review_reminder', array( $order_id ) );
		
		// Update reminder status in database
		Database::update_reminder_by_order( $order_id, array(
			'status' => 'cancelled'
		) );
		
		$order->add_order_note( __( 'SKTPR: Review reminder cancelled manually from admin.', 'product-reviews' ) );
	}


}