<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Database
 * 
 * Handles database operations for email reminders
 */
class Database {

	/**
	 * Table name for email reminders
	 */
	const TABLE_NAME = 'sktpr_email_reminders';

	/**
	 * Constructor
	 */
	public function __construct() {
		// Check for database updates on admin init
		add_action( 'admin_init', array( $this, 'check_database_version' ) );
	}

	/**
	 * Get the full table name with WordPress prefix
	 * 
	 * @return string
	 */
	public static function get_table_name() {
		global $wpdb;
		return $wpdb->prefix . self::TABLE_NAME;
	}

	/**
	 * Create the email reminders table (called on plugin activation)
	 */
	public static function create_table_on_activation() {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		// Check if table already exists
		$table_exists = $wpdb->get_var( $wpdb->prepare( 
			"SHOW TABLES LIKE %s", 
			$table_name 
		) );
		
		if ( $table_exists !== $table_name ) {
			self::create_table();
			
			// Schedule migration to run on next page load
			wp_schedule_single_event( time() + 10, 'sktpr_migrate_reminders' );
		}
	}

	/**
	 * Create the email reminders table
	 */
	private static function create_table() {
		global $wpdb;
		
		$table_name = self::get_table_name();
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			order_id bigint(20) unsigned NOT NULL,
			customer_name varchar(255) NOT NULL DEFAULT '',
			customer_email varchar(255) NOT NULL DEFAULT '',
			scheduled_time datetime NOT NULL,
			status varchar(20) NOT NULL DEFAULT 'scheduled',
			created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			sent_at datetime NULL DEFAULT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY order_id (order_id),
			KEY status (status),
			KEY scheduled_time (scheduled_time),
			KEY customer_email (customer_email)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Insert a new email reminder
	 * 
	 * @param array $data Reminder data
	 * @return int|false Insert ID on success, false on failure
	 */
	public static function insert_reminder( $data ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$defaults = array(
			'order_id' => 0,
			'customer_name' => '',
			'customer_email' => '',
			'scheduled_time' => current_time( 'mysql' ),
			'status' => 'scheduled'
		);
		
		$data = wp_parse_args( $data, $defaults );
		
		$result = $wpdb->insert(
			$table_name,
			array(
				'order_id' => intval( $data['order_id'] ),
				'customer_name' => sanitize_text_field( $data['customer_name'] ),
				'customer_email' => sanitize_email( $data['customer_email'] ),
				'scheduled_time' => $data['scheduled_time'],
				'status' => sanitize_text_field( $data['status'] )
			),
			array( '%d', '%s', '%s', '%s', '%s' )
		);
		
		if ( $result === false ) {
			return false;
		}
		
		return $wpdb->insert_id;
	}

	/**
	 * Update a reminder
	 * 
	 * @param int $id Reminder ID
	 * @param array $data Update data
	 * @return bool
	 */
	public static function update_reminder( $id, $data ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$allowed_fields = array( 'status', 'sent_at', 'scheduled_time' );
		$update_data = array();
		$format = array();
		
		foreach ( $data as $key => $value ) {
			if ( in_array( $key, $allowed_fields ) ) {
				$update_data[ $key ] = $value;
				$format[] = in_array( $key, array( 'sent_at', 'scheduled_time' ) ) ? '%s' : '%s';
			}
		}
		
		if ( empty( $update_data ) ) {
			return false;
		}
		
		$result = $wpdb->update(
			$table_name,
			$update_data,
			array( 'id' => intval( $id ) ),
			$format,
			array( '%d' )
		);
		
		return $result !== false;
	}

	/**
	 * Update reminder by order ID
	 * 
	 * @param int $order_id Order ID
	 * @param array $data Update data
	 * @return bool
	 */
	public static function update_reminder_by_order( $order_id, $data ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$allowed_fields = array( 'status', 'sent_at', 'scheduled_time' );
		$update_data = array();
		$format = array();
		
		foreach ( $data as $key => $value ) {
			if ( in_array( $key, $allowed_fields ) ) {
				$update_data[ $key ] = $value;
				$format[] = in_array( $key, array( 'sent_at', 'scheduled_time' ) ) ? '%s' : '%s';
			}
		}
		
		if ( empty( $update_data ) ) {
			return false;
		}
		
		$result = $wpdb->update(
			$table_name,
			$update_data,
			array( 'order_id' => intval( $order_id ) ),
			$format,
			array( '%d' )
		);
		
		return $result !== false;
	}

	/**
	 * Delete a reminder
	 * 
	 * @param int $id Reminder ID
	 * @return bool
	 */
	public static function delete_reminder( $id ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$result = $wpdb->delete(
			$table_name,
			array( 'id' => intval( $id ) ),
			array( '%d' )
		);
		
		return $result !== false;
	}

	/**
	 * Delete reminder by order ID
	 * 
	 * @param int $order_id Order ID
	 * @return bool
	 */
	public static function delete_reminder_by_order( $order_id ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$result = $wpdb->delete(
			$table_name,
			array( 'order_id' => intval( $order_id ) ),
			array( '%d' )
		);
		
		return $result !== false;
	}

	/**
	 * Get a reminder by ID
	 * 
	 * @param int $id Reminder ID
	 * @return object|null
	 */
	public static function get_reminder( $id ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE id = %d",
			intval( $id )
		) );
	}

	/**
	 * Get reminder by order ID
	 * 
	 * @param int $order_id Order ID
	 * @return object|null
	 */
	public static function get_reminder_by_order( $order_id ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		return $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM $table_name WHERE order_id = %d",
			intval( $order_id )
		) );
	}

	/**
	 * Get all reminders with optional filters
	 * 
	 * @param array $args Query arguments
	 * @return array
	 */
	public static function get_reminders( $args = array() ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$defaults = array(
			'status' => '',
			'limit' => 50,
			'offset' => 0,
			'orderby' => 'scheduled_time',
			'order' => 'ASC'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		$where = array( '1=1' );
		$where_values = array();
		
		if ( ! empty( $args['status'] ) ) {
			$where[] = 'status = %s';
			$where_values[] = $args['status'];
		}
		
		$where_clause = implode( ' AND ', $where );
		
		$orderby = sanitize_sql_orderby( $args['orderby'] . ' ' . $args['order'] );
		if ( ! $orderby ) {
			$orderby = 'scheduled_time ASC';
		}
		
		$limit = '';
		if ( $args['limit'] > 0 ) {
			$limit = $wpdb->prepare( 'LIMIT %d OFFSET %d', intval( $args['limit'] ), intval( $args['offset'] ) );
		}
		
		$query = "SELECT * FROM $table_name WHERE $where_clause ORDER BY $orderby $limit";
		
		if ( ! empty( $where_values ) ) {
			$query = $wpdb->prepare( $query, $where_values );
		}
		
		return $wpdb->get_results( $query );
	}

	/**
	 * Get reminders due for sending
	 * 
	 * @return array
	 */
	public static function get_due_reminders() {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table_name 
			WHERE status = 'scheduled' 
			AND scheduled_time <= %s 
			ORDER BY scheduled_time ASC",
			current_time( 'mysql' )
		) );
	}

	/**
	 * Get reminder statistics
	 * 
	 * @return array
	 */
	public static function get_stats() {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$stats = $wpdb->get_results(
			"SELECT status, COUNT(*) as count 
			FROM $table_name 
			GROUP BY status"
		);
		
		$result = array(
			'scheduled' => 0,
			'sent' => 0,
			'cancelled' => 0,
			'failed' => 0,
			'total' => 0
		);
		
		foreach ( $stats as $stat ) {
			$result[ $stat->status ] = intval( $stat->count );
			$result['total'] += intval( $stat->count );
		}
		
		return $result;
	}

	/**
	 * Check if reminder exists for order
	 * 
	 * @param int $order_id Order ID
	 * @return bool
	 */
	public static function reminder_exists( $order_id ) {
		global $wpdb;
		
		$table_name = self::get_table_name();
		
		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM $table_name WHERE order_id = %d",
			intval( $order_id )
		) );
		
		return intval( $count ) > 0;
	}

	/**
	 * Check database version and create table if needed (for updates)
	 */
	public function check_database_version() {
		$db_version = get_option( 'sktpr_db_version', '0' );
		$current_version = '1.0';
		
		if ( version_compare( $db_version, $current_version, '<' ) ) {
			self::create_table_on_activation();
			update_option( 'sktpr_db_version', $current_version );
			
			// Show admin notice
			add_action( 'admin_notices', array( $this, 'show_database_update_notice' ) );
		}
	}

	/**
	 * Show admin notice after database update
	 */
	public function show_database_update_notice() {
		?>
		<div class="notice notice-success is-dismissible">
			<p>
				<strong><?php esc_html_e( 'Product Reviews:', 'product-reviews' ); ?></strong>
				<?php esc_html_e( 'Email reminders database table has been created/updated successfully.', 'product-reviews' ); ?>
			</p>
		</div>
		<?php
	}
}