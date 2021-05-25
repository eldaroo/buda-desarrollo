<?php
/**
 * Class responsible for implementing export functionality of WPForm entries
 *
 * @since 1.1.0
 */


class Ank_WPForms_Export {

	/**
	 * Delimited to be used for csv file
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $delimiter = "";

	/**
	 * Instance of entries table
	 *
	 * @since 1.1.0
	 *
	 * @var object
	 */
	private $entries_table_instance = null;

	/**
	 * Columns to be added as header of CSV file
	 *
	 * @since 1.1.0
	 *
	 * @var object
	 */
	private $csv_columns = array();

	/**
	 * WPForm entries rows
	 *
	 * @since 1.1.0
	 *
	 * @var object
	 */
	private $form_entries = array();

	/**
	 * Form ID of the selected form
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $form_id = "";

	/**
	 * Form title of the selected form
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	private $form_title = "";

	/**
	 * Constructor to initialize the settings for export functionality
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct() {
		$this->delimiter = ",";

		//fresh instance of the entries tables ensures that selected form and its corresponding properties i.e. columns and entries are picked
		$this->entries_table_instance = new Ank_WPForms_Entries_Table();

		//get all the properties of the selected form
		$this->form_id      = $this->entries_table_instance->default_form_id;
		$this->csv_columns  = $this->entries_table_instance->get_columns();
		$this->form_entries = ank_wpforms_entry()->get_class_instance( 'entry-db' )->get_entries( $this->form_id );
		$this->form_title   = $this->entries_table_instance->default_form_title;
	}

	/**
	 * Process the export request coming for WPForm entries into CSV formatted file.
	 *
	 * @since 1.1.0
	 *
	 */
	public function process_export() {
		@set_time_limit( 0 );
		if ( function_exists( 'apache_setenv' ) ) {
			@apache_setenv( 'no-gzip', 1 );
		}
		@ini_set( 'zlib.output_compression', 0 );
		@ob_end_clean();

		header( 'Content-Type: text/csv; charset=UTF-8' );
		header( 'Content-Disposition: attachment; filename=' . $this->form_title . date( 'Y_m_d_H_i_s', current_time( 'timestamp' ) ) . ".csv" );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$fp = fopen( 'php://output', 'w' );

		$headers = $this->get_csv_header();
		$this->add_data_scv( $fp, $headers );

		ini_set( 'max_execution_time', - 1 );
		ini_set( 'memory_limit', - 1 );

		// Loop entries
		foreach ( $this->form_entries as $entry ) {
			$temp_data     = array();
			$entry_details = $entry->entry_details;
			foreach ( $entry_details as $entry_detail ) {
				$temp_data = array_merge( $temp_data, array( $entry_detail['id'] . $entry_detail['type'] => $entry_detail['value'] ) );
			}
			$temp_data = array_merge( $temp_data, array( 'entry_date' => $entry->entry_date ) );
			$temp_data = array_merge( $temp_data, array( 'viewed' => $entry->viewed ) );

			//this function ensures that for each column header the value is matched if there is no matching data then blank is returned
			$entry_row = Ank_WPForms_Export::get_entries_csv_row( array_keys( $this->csv_columns ), $temp_data );
			$this->add_data_scv( $fp, $entry_row );
		}

		fclose( $fp );
		exit;
	}

	/**
	 * Wrap a column in quotes for the CSV
	 *
	 * @param string data to wrap
	 *
	 * @return string wrapped data
	 * @since 1.1.0
	 *
	 */
	public static function wrap_column( $data ) {
		return '"' . str_replace( '"', '""', $data ) . '"';
	}

	/**
	 * Converts the entries into csv row data
	 * If there is no data for a corresponding column then it is marked as blank
	 *
	 * @param array column keys
	 * @param array associated array (key and value)
	 *
	 * @return array
	 *
	 * @since 1.1.0
	 */
	public static function get_entries_csv_row( $column_keys, $entries ) {
		$data = array();
		foreach ( $column_keys as $column_key ) {
			if ( $column_key === 'cb' ) {
				continue;
			}
			$data[] = $entries[ $column_key ];
		}

		return $data;

	}

	/**
	 * Write to csv file
	 *
	 * @param array data to be written in csv file
	 *
	 * @since 1.1.0
	 *
	 */
	private function add_data_scv( $fp, $row ) {
		$row = array_map( 'Ank_WPForms_Export::wrap_column', $row );
		fwrite( $fp, implode( $this->delimiter, $row ) . "\n" );
		unset( $row );
	}

	/**
	 * Write to csv file
	 *
	 * @param array data to be written in csv file
	 *
	 * @since 1.1.0
	 *
	 */
	private function get_csv_header() {
		// Variable to hold the CSV data we're exporting
		$row = array();

		// Export header rows
		foreach ( $this->csv_columns as $column => $value ) {
			if ( $column === 'cb' ) {
				continue;
			}
			$row[] = $value;
		}

		return $row;
	}

}