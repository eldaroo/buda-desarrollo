<?php

/**
 * Class responsible for implementing DB functions for entries associated with WPForms
 *
 * @since 1.0.0
 */
class Ank_WPForms_Entries_DB extends Ank_WPForms_DB {

	/**
	 * Primary key (unique field) for the database table.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $primary_key = 'id';

	/**
	 * Secondary key for the database table.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $secondary_key = 'form_id';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.5.9
	 */
	public function __construct() {

		$this->table_name = self::get_table_name();
	}

	/**
	 * Get the DB table name.
	 *
	 * @return string
	 * @since 1.5.9
	 *
	 */
	public static function get_table_name() {

		global $wpdb;

		return $wpdb->prefix . 'ank_wpforms_entries';
	}

	/**
	 * Get table columns.
	 *
	 * @since 1.0.0
	 */
	public function get_columns() {

		return array(
			'id'            => '%d',
			'form_id'       => '%d',
			'entry_details' => '%s',
			'entry_date'    => '%s',
			'viewed'        => '%s',
		);
	}

	/**
	 * Default column values.
	 *
	 * @return array
	 * @since 1.0.0
	 *
	 */
	public function get_column_defaults() {
		//TODO: Option to select format of date from settings page
		return array(
			'entry_date' => gmdate( 'Y-m-d H:i:s' ),
			'viewed'     => '0',
		);
	}

	/**
	 * Create custom entry meta database table.
	 * Used in migration and on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function create_table() {

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$sql = "CREATE TABLE {$this->table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			entry_details longtext NOT NULL,
			entry_date datetime NOT NULL,
			viewed varchar(10),
			PRIMARY KEY  (id)
		) {$charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Drop custom entry database table.
	 * Used on plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public function drop_table() {
		global $wpdb;
		$sql = "DROP TABLE IF EXISTS {$this->table_name}";
		$wpdb->query( $sql );
	}

	/**
	 * Remove records for a defined period of time in the past.
	 * Calling this method will remove queue records that are older than $period seconds.
	 *
	 * @param string $action Action that should be cleaned up.
	 * @param int $interval Number of seconds from now.
	 *
	 * @return int Number of removed tasks meta records.
	 * @since 1.0.0
	 *
	 */
	public function clean_by( $action, $interval ) {
		//TODO: to be used in future for deleting entries from entries page

		global $wpdb;

		if ( empty( $action ) || empty( $interval ) ) {
			return 0;
		}

		$table  = self::get_table_name();
		$action = sanitize_key( $action );
		$date   = gmdate( 'Y-m-d H:i:s', time() - (int) $interval );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$table` WHERE action = %s AND date < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$action,
				$date
			)
		);
	}

	/**
	 * Inserts a new record into the database.
	 *
	 * @param array $data Column data.
	 *
	 * @return int ID for the newly inserted record. 0 otherwise.
	 * @since 1.0.0
	 *
	 */
	public function add( $data ) {

		if ( empty( $data['entry_details'] ) || empty( $data['form_id'] ) ) {
			return 0;
		}

		//$data['entry_details'] = sanitize_key( $data['entry_details'] );

		if ( isset( $data['entry_details'] ) ) {
			$string = wp_json_encode( $data['entry_details'] );

			if ( $string === false ) {
				$string = '';
			}

			/*
			 * We are encoding the string representation of all the data
			 * to make sure that nothing can harm the database.
			 * This is not an encryption, and we need this data later as is,
			 * so we are using one of the fastest way to do that.
			 * This data is removed from DB on a daily basis.
			 */
			// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			$data['entry_details'] = base64_encode( $string );
		}

		return parent::add( $data );
	}

	/**
	 * Retrieve a row from the database based on a given row ID.
	 *
	 * @param int $meta_id Meta ID.
	 *
	 * @return null|object
	 * @since 1.0.0
	 *
	 */
	public function get( $meta_id ) {

		$meta = parent::get( $meta_id );

		if ( empty( $meta ) || empty( $meta->data ) ) {
			return $meta;
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$decoded = base64_decode( $meta->data );

		if ( $decoded === false || ! is_string( $decoded ) ) {
			$meta->data = '';
		} else {
			$meta->data = json_decode( $decoded, true );
		}

		return $meta;
	}

	/**
	 * Retrieve all entries for a given form ID.
	 *
	 * @param int $form_id form ID.
	 *
	 * @return null|object
	 * @since 1.0.0
	 *
	 */
	public function get_entries( $form_id, $page = '', $per_page = '' ) {

		$temp_entry = array();

		$entries = parent::get_all_records( $form_id, $page, $per_page );

		foreach ( $entries as $entry ) {
			$entry->entry_details = ank_wpforms_entries_decode_value( $entry->entry_details );
			array_push( $temp_entry, $entry );
		}

		return $temp_entry;

	}

}
