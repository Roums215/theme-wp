<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Email_Reminders
 * 
 * Handles email reminders for product reviews based on order status
 */
class Email_Reminders {

	use Helpers;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init_hooks' ) );
		add_action( 'sktpr_send_review_reminder', array( $this, 'send_review_reminder' ), 10, 1 );
		add_action( 'sktpr_process_due_reminders', array( $this, 'process_due_reminders' ) );
		add_action( 'sktpr_migrate_reminders', array( 'SKTPREVIEW\Migration', 'migrate_existing_reminders' ) );
		
		// Schedule recurring cron job to process due reminders
		if ( ! wp_next_scheduled( 'sktpr_process_due_reminders' ) ) {
			wp_schedule_event( time(), 'hourly', 'sktpr_process_due_reminders' );
		}
	}

	/**
	 * Initialize hooks based on settings
	 */
	public function init_hooks() {
		$settings = $this->get_email_settings();
		
		if ( ! $settings['enable_email_reminders'] ) {
			return;
		}

		$order_status = $settings['trigger_order_status'];
		// Ensure we have the correct status format (without wc- prefix for the hook)
		$order_status = 'wc-' === substr( $order_status, 0, 3 ) ? substr( $order_status, 3 ) : $order_status;
		
		// Hook into order status change
		add_action( 'woocommerce_order_status_' . $order_status, array( $this, 'schedule_reminder' ), 20, 1 );
		
		// Hook for order cancellation/refund to cancel reminders
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'cancel_reminder' ), 20, 1 );
		add_action( 'woocommerce_order_status_refunded', array( $this, 'cancel_reminder' ), 20, 1 );
		

	}
	


	/**
	 * Schedule a review reminder for an order
	 * 
	 * @param int $order_id Order ID
	 */
	public function schedule_reminder( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		$settings = $this->get_email_settings();
		
		// Check if reminder already exists in database
		if ( Database::reminder_exists( $order_id ) ) {
			return;
		}

		// Check if customer has valid email
		$customer_email = $order->get_billing_email();
		if ( ! is_email( $customer_email ) ) {
			$order->add_order_note( __( 'SKTPR: Review reminder not scheduled - invalid email address.', 'product-reviews' ) );
			return;
		}

		// Check if order has reviewable products
		if ( ! $this->has_reviewable_products( $order ) ) {
			$order->add_order_note( __( 'SKTPR: Review reminder not scheduled - no reviewable products in order.', 'product-reviews' ) );
			return;
		}

		// Calculate delay in days
		$delay_days = intval( $settings['email_delay_days'] );
		if ( $delay_days <= 0 ) {
			$delay_days = 7; // Default fallback
		}
		
		// Calculate scheduled time
		$scheduled_time = date( 'Y-m-d H:i:s', time() + ( $delay_days * DAY_IN_SECONDS ) );
		
		// Get customer name
		$customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
		if ( empty( $customer_name ) ) {
			$customer_name = __( 'Guest Customer', 'product-reviews' );
		}
		
		// Insert reminder into database
		$reminder_id = Database::insert_reminder( array(
			'order_id' => $order_id,
			'customer_name' => $customer_name,
			'customer_email' => $customer_email,
			'scheduled_time' => $scheduled_time,
			'status' => 'scheduled'
		) );
		
		if ( ! $reminder_id ) {
			$order->add_order_note( __( 'SKTPR: Failed to schedule review reminder - database error.', 'product-reviews' ) );
			return;
		}
		
		// Schedule the cron event
		$scheduled_timestamp = strtotime( $scheduled_time );
		$scheduled = wp_schedule_single_event( $scheduled_timestamp, 'sktpr_send_review_reminder', array( $order_id ) );
		
		if ( $scheduled === false ) {
			// Remove from database if cron scheduling failed
			Database::delete_reminder( $reminder_id );
			$order->add_order_note( __( 'SKTPR: Failed to schedule review reminder - cron error.', 'product-reviews' ) );
			return;
		}
		
		$scheduled_date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $scheduled_timestamp );
		$order->add_order_note( 
			sprintf( 
				__( 'SKTPR: Review reminder scheduled for %s', 'product-reviews' ), 
				$scheduled_date
			) 
		);
	}

	/**
	 * Cancel a scheduled reminder
	 * 
	 * @param int $order_id Order ID
	 */
	public function cancel_reminder( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Check if reminder exists in database
		$reminder = Database::get_reminder_by_order( $order_id );
		if ( ! $reminder ) {
			return;
		}

		// Cancel the scheduled event
		wp_clear_scheduled_hook( 'sktpr_send_review_reminder', array( $order_id ) );
		
		// Update reminder status to cancelled
		Database::update_reminder_by_order( $order_id, array(
			'status' => 'cancelled'
		) );
		
		$order->add_order_note( __( 'SKTPR: Review reminder cancelled due to order status change.', 'product-reviews' ) );
	}

	/**
	 * Send review reminder email
	 * 
	 * @param int $order_id Order ID
	 */
	public function send_review_reminder( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}

		// Get reminder from database
		$reminder = Database::get_reminder_by_order( $order_id );
		if ( ! $reminder || $reminder->status !== 'scheduled' ) {
			return;
		}

		$settings = $this->get_email_settings();
		$customer_email = $reminder->customer_email;
		$customer_name = $order->get_billing_first_name();
		
		// Get order items for review
		$items = $this->get_reviewable_items( $order );
		if ( empty( $items ) ) {
			$order->add_order_note( __( 'SKTPR: Review reminder not sent - no reviewable products found.', 'product-reviews' ) );
			Database::update_reminder_by_order( $order_id, array(
				'status' => 'failed'
			) );
			return;
		}

		// Prepare email content
		$subject = $this->parse_email_template( $settings['email_subject'], $order, $customer_name );
		$message = $this->parse_email_template( $settings['email_content'], $order, $customer_name );
		
		// Add products list to email
		$products_html = $this->generate_products_html( $items );
		$message = str_replace( '{products_list}', $products_html, $message );
		
		// Email headers
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $settings['from_name'] . ' <' . $settings['from_email'] . '>'
		);

		// Send email
		$sent = wp_mail( $customer_email, $subject, $message, $headers );
		
		if ( $sent ) {
			$order->add_order_note( __( 'SKTPR: Review reminder email sent successfully.', 'product-reviews' ) );
			
			// Update reminder status to sent
			Database::update_reminder_by_order( $order_id, array(
				'status' => 'sent',
				'sent_at' => current_time( 'mysql' )
			) );
		} else {
			$order->add_order_note( __( 'SKTPR: Failed to send review reminder email.', 'product-reviews' ) );
			
			// Update reminder status to failed
			Database::update_reminder_by_order( $order_id, array(
				'status' => 'failed'
			) );
		}
	}

	/**
	 * Check if order has reviewable products
	 * 
	 * @param WC_Order $order Order object
	 * @return bool
	 */
	private function has_reviewable_products( $order ) {
		$items = $order->get_items();
		
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();
			$product = wc_get_product( $product_id );
			
			if ( $product && $product->is_type( array( 'simple', 'variable' ) ) ) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * Get reviewable items from order
	 * 
	 * @param WC_Order $order Order object
	 * @return array
	 */
	private function get_reviewable_items( $order ) {
		$items = array();
		$order_items = $order->get_items();
		
		foreach ( $order_items as $item ) {
			$product_id = $item->get_product_id();
			$product = wc_get_product( $product_id );
			
			if ( $product && $product->is_type( array( 'simple', 'variable' ) ) ) {
				$items[] = array(
					'product_id' => $product_id,
					'product_name' => $product->get_name(),
					'product_url' => get_permalink( $product_id ),
					'product_image' => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
				);
			}
		}
		
		return $items;
	}

	/**
	 * Generate HTML for products list in email
	 * 
	 * @param array $items Product items
	 * @return string
	 */
	private function generate_products_html( $items ) {
		$html = '<div style="margin: 20px 0;">';
		
		foreach ( $items as $item ) {
			$html .= '<div style="border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px;">';
			
			if ( $item['product_image'] ) {
				$html .= '<img src="' . esc_url( $item['product_image'] ) . '" alt="' . esc_attr( $item['product_name'] ) . '" style="width: 60px; height: 60px; float: left; margin-right: 15px; border-radius: 3px;">';
			}
			
			$html .= '<h3 style="margin: 0 0 10px 0; font-size: 16px;">' . esc_html( $item['product_name'] ) . '</h3>';
			$html .= '<a href="' . esc_url( $item['product_url'] ) . '#reviews" style="background: #0073aa; color: white; padding: 8px 16px; text-decoration: none; border-radius: 3px; display: inline-block;">' . __( 'Write Review', 'product-reviews' ) . '</a>';
			$html .= '<div style="clear: both;"></div>';
			$html .= '</div>';
		}
		
		$html .= '</div>';
		
		return $html;
	}

	/**
	 * Parse email template variables
	 * 
	 * @param string $template Template string
	 * @param WC_Order $order Order object
	 * @param string $customer_name Customer name
	 * @return string
	 */
	private function parse_email_template( $template, $order, $customer_name ) {
		$variables = array(
			'{customer_name}' => $customer_name,
			'{order_number}' => $order->get_order_number(),
			'{order_date}' => $order->get_date_created()->date_i18n( get_option( 'date_format' ) ),
			'{site_name}' => get_bloginfo( 'name' ),
			'{site_url}' => home_url()
		);
		
		return str_replace( array_keys( $variables ), array_values( $variables ), $template );
	}

	/**
	 * Process due reminders (fallback cron job)
	 */
	public function process_due_reminders() {
		$due_reminders = Database::get_due_reminders();
		
		if ( empty( $due_reminders ) ) {
			return;
		}
		
		foreach ( $due_reminders as $reminder ) {
			$this->send_review_reminder( $reminder->order_id );
		}
	}
}