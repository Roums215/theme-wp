<?php // phpcs:ignore
namespace SKTPREVIEW;

// if direct access than exit the file.
defined( 'ABSPATH' ) || exit;

/**
 * Class Migration
 * 
 * Handles migration of existing reminder data to new database table
 */
class Migration {

	/**
	 * Migrate existing reminders from meta fields to database table
	 */
	public static function migrate_existing_reminders() {
		global $wpdb;
		
		// Check if migration has already been done
		$migration_done = get_option( 'sktpr_reminders_migrated', false );
		if ( $migration_done ) {
			return;
		}
		

		
		// Get all orders with reminder meta data
		$results = $wpdb->get_results( "
			SELECT p.ID as order_id, 
				   MAX(CASE WHEN pm.meta_key = '_sktpr_reminder_time' THEN pm.meta_value END) as scheduled_time,
				   MAX(CASE WHEN pm.meta_key = '_sktpr_reminder_scheduled' THEN pm.meta_value END) as reminder_scheduled,
				   MAX(CASE WHEN pm.meta_key = '_sktpr_reminder_sent' THEN pm.meta_value END) as reminder_sent
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
			WHERE p.post_type = 'shop_order'
			AND pm.meta_key IN ('_sktpr_reminder_time', '_sktpr_reminder_scheduled', '_sktpr_reminder_sent')
			GROUP BY p.ID
			HAVING scheduled_time IS NOT NULL OR reminder_scheduled IS NOT NULL OR reminder_sent IS NOT NULL
		" );
		
		$migrated_count = 0;
		
		foreach ( $results as $result ) {
			$order = wc_get_order( $result->order_id );
			if ( ! $order ) {
				continue;
			}
			
			// Skip if already exists in new table
			if ( Database::reminder_exists( $result->order_id ) ) {
				continue;
			}
			
			// Determine status
			$status = 'scheduled';
			if ( $result->reminder_sent ) {
				$status = 'sent';
			}
			
			// Get customer name
			$customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
			if ( empty( $customer_name ) ) {
				$customer_name = __( 'Guest Customer', 'product-reviews' );
			}
			
			// Calculate scheduled time
			$scheduled_time = current_time( 'mysql' );
			if ( $result->scheduled_time ) {
				$scheduled_time = date( 'Y-m-d H:i:s', intval( $result->scheduled_time ) );
			}
			
			// Insert into new table
			$reminder_id = Database::insert_reminder( array(
				'order_id' => $result->order_id,
				'customer_name' => $customer_name,
				'customer_email' => $order->get_billing_email(),
				'scheduled_time' => $scheduled_time,
				'status' => $status
			) );
			
			if ( $reminder_id ) {
				$migrated_count++;
				
				// Update sent_at if reminder was sent
				if ( $result->reminder_sent ) {
					Database::update_reminder( $reminder_id, array(
						'sent_at' => $result->reminder_sent
					) );
				}
			}
		}
		
		// Mark migration as complete
		update_option( 'sktpr_reminders_migrated', true );
		
		error_log( 'SKTPR: Migration completed. Migrated ' . $migrated_count . ' reminders' );
		
		return $migrated_count;
	}

	/**
	 * Clean up old meta data after successful migration
	 */
	public static function cleanup_old_meta_data() {
		global $wpdb;
		
		// Only cleanup if migration was successful
		$migration_done = get_option( 'sktpr_reminders_migrated', false );
		if ( ! $migration_done ) {
			return;
		}
		
		// Delete old meta keys
		$deleted = $wpdb->query( "
			DELETE FROM {$wpdb->postmeta} 
			WHERE meta_key IN ('_sktpr_reminder_time', '_sktpr_reminder_scheduled', '_sktpr_reminder_sent')
		" );
		
		error_log( 'SKTPR: Cleaned up ' . $deleted . ' old meta entries' );
		
		return $deleted;
	}
}