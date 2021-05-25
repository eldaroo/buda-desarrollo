<?php

/**
 * Handle plugin installation upon activation.
 *
 * @since 1.0.0
 */
class Ank_WPForms_Entry_Install {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		//create custom database when the plugins are loaded
		//hook added on 'plugins_loaded' as dependency on WPForms is checked before creating custom table for entries
		add_action( 'plugins_loaded', array( $this, 'create_entry_db' ) );
		//Admin notices to show WPForms dependency messages
		add_action( 'admin_notices', array( $this, 'installation_notices' ) );

		register_activation_hook( ANK_WPFORM_ENTRY_PLUGIN_FILE, array( $this, 'install' ) );
		//Not dropping table on deactivation of plugin
		register_deactivation_hook( ANK_WPFORM_ENTRY_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Perform certain actions on plugin activation.
	 *
	 * @since 1.0.0
	 *
	 */
	public function install() {
		//TODO: Add installation methods
	}

	/**
	 * Create database table to store entries coming from WPForms
	 * Database table is created if WPForms lite plugin is activated
	 *
	 * @since 1.0.0
	 *
	 */
	public function create_entry_db() {
		if ( class_exists( 'WPForms\WPForms' ) ) {
			//get db class instance from magic function
			ank_wpforms_entry()->get_class_instance( 'entry-db' )->create_table();
			// transient is set to fix the issue related to de-activation of this plugin when WPForms is deactivated
			set_transient( 'plugin_activated', 'true' );
		}
	}

	/**
	 * Installation notices to show dependency on WPForms
	 * If WPForms is not installed then this plugin is not activated
	 *
	 * @since 1.0.0
	 *
	 */
	public function installation_notices() {
		// if the plugin is already activated then do not deactivate it if WPForms is de-activated
		$plugin_just_activated = get_transient( 'plugin_activated' );

		if ( $plugin_just_activated ) {
			return;
		}

		unset( $_GET['activate'] );

		// Currently tried to activate Lite with Pro still active, so display the message.
		printf(
			'<div class="notice notice-error">
					<p>%1$s</p>
					<p>%2$s</p>
				</div>',
			esc_html__( 'Error!!!', 'ank-wpforms-entry'),
			esc_html__( 'Kindly activate WPForms lite plugin before activating this plugin',  'ank-wpforms-entry' )
		);

		deactivate_plugins( ANK_WPFORM_ENTRY_PLUGIN_FILE );
	}

	/**
	 * Perform certain actions on plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		//TODO: Add settings options to drop table on deactivation
		delete_transient( 'plugin_activated' );
		//$sql = $this->entry_db_instance->drop_table();
	}
}

new Ank_WPForms_Entry_Install();