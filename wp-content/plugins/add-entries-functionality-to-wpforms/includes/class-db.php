<?php

/**
 * DB class.
 *
 * This handy class originated from Pippin's Easy Digital Downloads.
 * https://github.com/easydigitaldownloads/easy-digital-downloads/blob/master/includes/class-edd-db.php
 *
 * Sub-classes should define $table_name, $version, and $primary_key in __construct() method.
 *
 * @since 1.1.6
 */
abstract class Ank_WPForms_DB {

	/**
	 * Database table name.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $table_name;

	/**
	 * Database version.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Primary key (unique field) for the database table.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $primary_key;


	/**
	 * Retrieve the list of columns for the database table.
	 * Sub-classes should define an array of columns here.
	 *
	 * @return array List of columns.
	 * @since 1.0.0
	 *
	 */
	public function get_columns() {

		return array();
	}

	/**
	 * Retrieve column defaults.
	 * Sub-classes can define default for any/all of columns defined in the get_columns() method.
	 *
	 * @return array All defined column defaults.
	 * @since 1.0.0
	 *
	 */
	public function get_column_defaults() {

		return array();
	}

	/**
	 * Retrieve a row from the database based on a given row ID.
	 *
	 * @param int $row_id Row ID.
	 *
	 * @return null|object
	 * @since 1.0.0
	 *
	 */
	public function get( $row_id ) {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = %s LIMIT 1;",
				$row_id
			)
		);
	}

	/**
	 * Retrieve all rows from the database based on a given form ID.
	 *
	 * @param int $form_id form ID.
	 *
	 * @return null|object
	 * @since 1.0.0
	 *
	 */
	public function get_all_records( $form_id, $page='', $per_page='' ) {

		global $wpdb;
		$limits = "";

		//If the page is coming as blank then do not set limit
		if ( ! empty( $page ) ) {
			$pgstrt = absint( ( $page - 1 ) * $per_page ) . ', ';
			$limits = ' LIMIT ' . $pgstrt . $per_page;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE {$this->secondary_key} = %s {$limits};",
				$form_id
			)
		);
	}

	/**
	 * Retrieve all rows from the database based on a given form ID.
	 *
	 * @param int $form_id form ID.
	 *
	 * @return null|object
	 * @since 1.0.0
	 *
	 */
	public function get_count_all_records( $form_id ) {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table_name} WHERE {$this->secondary_key} = %s;",
				$form_id
			)
		);
	}


	/**
	 * Retrieve a row based on column and row ID.
	 *
	 * @param string $column Column name.
	 * @param int|string $row_id Row ID.
	 *
	 * @return object|null|bool Database query result, object or null on failure.
	 * @since 1.0.0
	 *
	 */
	public function get_by( $column, $row_id ) {

		global $wpdb;

		if ( empty( $row_id ) || ! array_key_exists( $column, $this->get_columns() ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $this->table_name WHERE $column = '%s' LIMIT 1;",
				$row_id
			)
		);
	}

	/**
	 * Retrieve a value based on column name and row ID.
	 *
	 * @param string $column Column name.
	 * @param int|string $row_id Row ID.
	 *
	 * @return string|null Database query result (as string), or null on failure.
	 * @since 1.0.0
	 *
	 */
	public function get_column( $column, $row_id ) {

		global $wpdb;

		if ( empty( $row_id ) || ! array_key_exists( $column, $this->get_columns() ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $column FROM $this->table_name WHERE $this->primary_key = '%s' LIMIT 1;",
				$row_id
			)
		);
	}

	/**
	 * Retrieve one column value based on another given column and matching value.
	 *
	 * @param string $column Column name.
	 * @param string $column_where Column to match against in the WHERE clause.
	 * @param string $column_value Value to match to the column in the WHERE clause.
	 *
	 * @return string|null Database query result (as string), or null on failure.
	 * @since 1.0.0
	 *
	 */
	public function get_column_by( $column, $column_where, $column_value ) {

		global $wpdb;

		if ( empty( $column ) || empty( $column_where ) || empty( $column_value ) || ! array_key_exists( $column, $this->get_columns() ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;",
				$column_value
			)
		);
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param array $data Column data.
	 * @param string $type Optional. Data type context.
	 *
	 * @return int ID for the newly inserted record. 0 otherwise.
	 * @since 1.0.0
	 *
	 */
	public function add( $data ) {

		global $wpdb;

		// Set default values.
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		do_action( 'ank_wpforms_entries_pre_insert_' . $type, $data );

		// Initialise column format array.
		$column_formats = $this->get_columns();

		// Force fields to lower case.
		$data = array_change_key_case( $data );

		// White list columns.
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data.
		$data_keys      = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$wpdb->insert( $this->table_name, $data, $column_formats );

		do_action( 'ank_wpforms_entries_post_insert_' . $type, $wpdb->insert_id, $data );

		return $wpdb->insert_id;
	}

	/**
	 * Insert a new record into the database. This runs the add method.
	 *
	 * @param array $data Column data.
	 *
	 * @return int ID for the newly inserted record.
	 * @since 1.0.0
	 *
	 */
	public function insert( $data ) {

		return $this->add( $data );
	}

	/**
	 * Update an existing record in the database.
	 *
	 * @param int|string $row_id Row ID for the record being updated.
	 * @param array $data Optional. Array of columns and associated data to update. Default empty array.
	 * @param string $where Optional. Column to match against in the WHERE clause. If empty, $primary_key
	 *                           will be used. Default empty.
	 * @param string $type Optional. Data type context, e.g. 'affiliate', 'creative', etc. Default empty.
	 *
	 * @return bool False if the record could not be updated, true otherwise.
	 * @since 1.0.0
	 *
	 */
	public function update( $row_id, $data = array(), $where = '', $type = '' ) {

		global $wpdb;

		// Row ID must be a positive integer.
		$row_id = absint( $row_id );

		if ( empty( $row_id ) ) {
			return false;
		}

		if ( empty( $where ) ) {
			$where = $this->primary_key;
		}

		do_action( 'ank_wpforms_entries_pre_update_' . $type, $data );

		// Initialise column format array.
		$column_formats = $this->get_columns();

		// Force fields to lower case.
		$data = array_change_key_case( $data );

		// White list columns.
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data.
		$data_keys      = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $wpdb->update( $this->table_name, $data, array( $where => $row_id ), $column_formats ) ) {
			return false;
		}

		do_action( 'ank_wpforms_entries_post_update_' . $type, $data );

		return true;
	}

	/**
	 * Delete a record from the database.
	 *
	 * @param int|string $row_id Row ID.
	 *
	 * @return bool False if the record could not be deleted, true otherwise.
	 * @since 1.0.0
	 *
	 */
	public function delete( $row_id = 0 ) {

		global $wpdb;

		// Row ID must be positive integer.
		$row_id = absint( $row_id );

		if ( empty( $row_id ) ) {
			return false;
		}

		do_action( 'ank_wpforms_entries_pre_delete', $row_id );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE {$this->primary_key} = %d", $row_id ) ) ) {
			return false;
		}

		do_action( 'ank_wpforms_entries_post_delete', $row_id );

		return true;
	}

	/**
	 * Delete a record from the database by column.
	 *
	 * @param string $column Column name.
	 * @param int|string $column_value Column value.
	 *
	 * @return bool False if the record could not be deleted, true otherwise.
	 * @since 1.0.0
	 *
	 */
	public function delete_by( $column, $column_value ) {

		global $wpdb;

		if ( empty( $column ) || empty( $column_value ) || ! array_key_exists( $column, $this->get_columns() ) ) {
			return false;
		}

		do_action( 'ank_wpforms_entries_pre_delete', $column_value );
		do_action( 'ank_wpforms_entries_pre_delete_' . $this->type, $column_value );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE $column = %s", $column_value ) ) ) {
			return false;
		}

		do_action( 'ank_wpforms_entries_post_delete', $column_value );
		do_action( 'ank_wpforms_entries_post_delete_' . $this->type, $column_value );

		return true;
	}

	/**
	 * Check if the given table exists.
	 *
	 * @param string $table The table name. Defaults to the child class table name.
	 *
	 * @return string|null If the table name exists.
	 * @since 1.0.0
	 *
	 */
	public function table_exists( $table = '' ) {

		global $wpdb;

		if ( ! empty( $table ) ) {
			$table = sanitize_text_field( $table );
		} else {
			$table = $this->table_name;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
	}
}
