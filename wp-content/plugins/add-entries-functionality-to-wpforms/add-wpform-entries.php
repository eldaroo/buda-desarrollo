<?php
/*
  Plugin Name: Add entries functionality to WPForms
  Plugin URI:
  Description: Free plugin to add entries functionality to WPForms
  Author: Ankur Khurana
  Author URI:http://aaivatech.com/
  COntributors:
  Version: 1.3.3
  @copyright   Copyright (c) 2021, Ankur Khurana
  @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

final class Ank_WPForms_Entries_Master { //final to avoid extension/inheritance of class

	/**
	 * Plugin version number
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $ank_wpforms_entry_version = '1.1.2';

	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $_instance;

	/**
	 * Classes registry.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $registry = array();

	/**
	 * Initiates the plugin
	 *
	 * @access  public
	 * @return  Ank_WPForms_Entries_Master
	 * @since    1.0.0
	 *
	 *
	 *
	 */
	public static function instance() {
		if (
			null === self::$_instance ||
			! self::$_instance instanceof self
		) {
			self::$_instance = new self();
			self::$_instance->define_constants();
			self::$_instance->includes();
			self::$_instance->objects();
			self::$_instance->init_hooks();
		}

		return self::$_instance;
	}//Singleton pattern

	/**
	 * Defines Constants and enable logging based on debug flag from settings page of plugin
	 *
	 * @access  private
	 * @return  void
	 * @uses    get_option(), add_filter()
	 *
	 * @since    1.0.0
	 *
	 */
	private function define_constants() {
		// Plugin Root File.
		if ( ! defined( 'ANK_WPFORM_ENTRY_PLUGIN_FILE' ) ) {
			define( 'ANK_WPFORM_ENTRY_PLUGIN_FILE', __FILE__ );
		}
		if ( ! defined( 'ANK_WPFORM_ENTRY_BASE_URL' ) ) {
			define( 'ANK_WPFORM_ENTRY_BASE_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( ! defined( 'ANK_WPFORM_ENTRY_BASE_DIR' ) ) {
			define( 'ANK_WPFORM_ENTRY_BASE_DIR', dirname( __FILE__ ) );
		}
		if ( ! defined( 'ANK_WPFORM_ENTRY_VERSION' ) ) {
			define( 'ANK_WPFORM_ENTRY_VERSION', $this->ank_wpforms_entry_version );
		}
		if ( ! defined( 'ANK_WPFORM_ENTRY_VERSION_KEY' ) ) {
			define( 'ANK_WPFORM_ENTRY_VERSION_KEY', 'ank_wp_ticket_version' );
		}
		if ( ! defined( 'ANK_WPFORM_ENTRY_MINIMUM_WP_VERSION' ) ) {
			define( 'ANK_WPFORM_ENTRY_MINIMUM_WP_VERSION', '5.4.2' );
		}
	}

	/**
	 * Includes required core files used in admin and on the frontend.
	 * Instantiates logging class
	 *
	 * @access  private
	 * @return  void
	 * @uses    is_admin()
	 *
	 * @since    1.0.0
	 *
	 */
	private function includes() {
		include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/ank-entry-install.php';
		include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/class-db.php';
		include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/functions.php';
		include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/admin/entry/entries-db.php';
		include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/admin/entry/entries-page.php';
		include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/exporter/export-csv.php';

		if ( ! is_admin() ) {// if the request is coming frontend
			include_once ANK_WPFORM_ENTRY_BASE_DIR . '/includes/frontend/entries-shortcode.php';
		}
	}

	/**
	 * Stores instances of classes in an array
	 *
	 * @access  private
	 * @return  void
	 *
	 * @since    1.0.0
	 *
	 */
	private function objects() {
		$this->registry['entry-db']   = new Ank_WPForms_Entries_DB();
		$this->registry['entry-page'] = new Ank_WPForms_Entries();
	}

	/**
	 * Get a class instance from a registry.
	 *
	 * @param string $name Class name or an alias.
	 *
	 * @return mixed|\stdClass
	 * @since 1.0.0
	 *
	 */
	public function get_class_instance( $name ) {
		if ( ! empty( $this->registry[ $name ] ) ) {
			return $this->registry[ $name ];
		}

		return new \stdClass();
	}

	/**
	 * Hook into actions and filters
	 *
	 * @access  private
	 * @return  void
	 * @uses    register_activation_hook(), register_uninstall_hook(), add_action()
	 *
	 * @since    1.0.0
	 *
	 */
	private function init_hooks() {
		/*hook to an action of wpforms to add new submenu on the admin part of wpforms*/
		add_action( 'wpforms_admin_menu', array( $this, 'ank_wpforms_entry_menu' ), 10, 1 );

		/*hook to save entries coming from WPforms into database*/
		add_action( 'wpforms_process_entry_save', array( $this, 'ank_wpforms_save_entries' ), 10, 4 );

		/*filter to change url of entries on WPForms overview page*/
		add_filter( 'wpforms_overview_row_actions', array( $this, 'change_entry_url' ), 10, 2 );
	}

	/**
	 * Hook into actions and filters
	 *
	 * @access  private
	 * @return  void
	 *
	 * @since    1.0.0
	 *
	 */
	public function ank_wpforms_entry_menu() {
		$access = wpforms()->get( 'access' );

		// Entries sub menu item.
		add_submenu_page(
			'wpforms-overview',
			esc_html__( 'Contact form entry', 'ank-wpforms-entry' ),
			esc_html__( 'Contact form entry', 'ank-wpforms-entry' ),
			$access->get_menu_cap( 'view_entries' ),
			'ank-wpforms-entries',
			array( $this, 'admin_page' )
		);
	}

	/**
	 * Hook to save form entries in database
	 *
	 * @access  public
	 * @return  void
	 *
	 * @since    1.0.0
	 *
	 */
	public function ank_wpforms_save_entries( $fields, $entry, $form_id, $form_data ) {
		//no need to sanitize data coming from WPForms as it is being sanitized in WPForms plugin before this hook using
		//wpforms_process_validate_{$field_type} in class-process.php
		$data                  = array();
		$data['form_id']       = $form_id;
		$data['entry_details'] = $fields;
		//additional sanity checks are also performed while json encoding in "add" before adding in database
		ank_wpforms_entry()->get_class_instance( 'entry-db' )->add( $data );
	}

	/**
	 * Filter to change entries page url on WPForms overview page
	 *
	 * @access  public
	 * @return  void
	 *
	 * @since    1.0.0
	 *
	 */
	public function change_entry_url( $row_actions, $form ) {
		$row_actions['entries'] = sprintf(
			'<a href="%s" title="%s">%s</a>',
			esc_url(
				add_query_arg(
					array(
						'view'    => 'list',
						'form_id' => $form->ID,
					),
					admin_url( 'admin.php?page=ank-wpforms-entries' )
				)
			),
			esc_attr__( 'View entries', 'ank-wpforms-entry' ),
			esc_html__( 'Entries', 'ank-wpforms-entry' )
		);

		return $row_actions;
	}

	/**
	 * Wrapper for the hook to render our custom settings pages.
	 *
	 * @since 1.0.0
	 */
	public function admin_page() {
		do_action( 'entry_admin_page' );
	}

	/**
	 * Initiates the statics variables with values entered in setting page of plugin
	 *
	 * @access  public
	 * @return  void
	 * @uses    get_option()
	 *
	 * @since    1.0.o
	 *
	 */
	public function init() {
		//TODO: Save the values from setting page as options
	}

}

function ank_wpforms_entry() {
	return Ank_WPForms_Entries_Master::instance();
}

//main call starts from here
ank_wpforms_entry();

/*
 *  Displays update information for a plugin.
 */
function ank_wpforms_entry_update_message( $data, $response ) {
	if ( isset( $data['upgrade_notice'] ) ) {
		printf(
			'<div class="update-message ">%s</div>',
			wpautop( $data['upgrade_notice'] )
		);
	}
}

add_action( 'in_plugin_update_message-add-wpform-entries/add-wpform-entries.php', 'ank_wpforms_entry_update_message', 10, 2 );
