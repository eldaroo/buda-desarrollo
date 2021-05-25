<?php
/**
 * Class responsible for implementing entries page on  WP dashboard
 *
 * @since 1.0.0
 */

class Ank_WPForms_Entries {

	/**
	 * Admin menu page slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const SLUG = 'ank-wpforms-entries';

	const EXPORT_BUTTON = 'Export Entries';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Maybe load entries page.
		add_action( 'admin_init', array( $this, 'init' ) );

		// Maybe load entries page.
		add_action( 'admin_init', array( $this, 'catch_export_request' ) );

		// Setup screen options. Needs to be here as admin_init hook it too late.
		add_action( 'load-wpforms_page_ank-wpforms-entries', array( $this, 'screen_options' ) );
		//handle bulk delete request (hook called from get_plugin_page_hookname from plugin.php)
		add_action( 'load-wpforms_page_ank-wpforms-entries', array( $this, 'notices' ) );
		add_action( 'load-wpforms_page_ank-wpforms-entries', array( $this, 'process_bulk_actions' ) );
		add_filter( 'set-screen-option', array( $this, 'screen_options_set' ), 10, 3 );
		add_filter( 'set_screen_option_ank_entries_per_page', [ $this, 'screen_options_set' ], 10, 3 );
	}

	/**
	 * Determine if the user is viewing the entries page, if so, party on.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? \sanitize_key( \wp_unslash( $_GET['page'] ) ) : '';

		// Only load if we are actually on the settings page.
		if ( self::SLUG !== $page ) {
			return;
		}

		// The entries page leverages WP_List_Table so we must load it.
		if ( ! class_exists( 'WP_List_Table', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
		}

		// Load the class that builds the entries table.
		require_once dirname( __FILE__ ) . '/entries-table.php';

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
		add_action( 'entry_admin_page', array( $this, 'output' ) );

		// Provide hook for addons.
		do_action( 'ank_wpforms_entries_init' );
	}

	/**
	 * Determine if the request has come exporting entries.
	 *
	 * @since 1.1.0
	 */
	public function catch_export_request() {
		// Check what page we are on.
		$page = isset( $_GET['page'] ) ? \sanitize_key( \wp_unslash( $_GET['page'] ) ) : '';

		// Export only if we are on right page and export is requested.
		if ( self::SLUG === $page && $_GET['export'] === self::EXPORT_BUTTON ) {
			$user_can_export = ank_wpforms_entries_user_permission();

			if ( $user_can_export ) {
				//core processing on CSV export happens here
				$export = new Ank_WPForms_Export();
				$export->process_export();
			} else {
				if ( is_admin() ) {
					//Admin notice to mention about permissions required for exporting
					add_action( 'admin_notices', array( $this, 'export_lack_of_permission_admin_notice' ) );
				} else {
					wp_redirect( wp_login_url() );
				}
			}

		} else {
			return;
		}

	}

	/**
	 * Admin notice to show if the user does not have access to export
	 *
	 * @since 1.1.0
	 */
	public function export_lack_of_permission_admin_notice() {
		echo '<div class="notice notice-error"><p>' . __( 'By default, admin  are given access to export WPForm entries. ', 'ank-wpforms-entry' ) . '</p></div>';
	}


	/**
	 * Add per-page screen option to the Forms table.
	 *
	 * @since 1.0.0
	 */
	public function screen_options() {
		$screen = get_current_screen();

		if ( null === $screen || 'wpforms_page_ank-wpforms-entries' !== $screen->id ) {
			return;
		}

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Number of entries per page:', 'ank-wpforms-entry' ),
				'option'  => 'ank_entries_per_page',
				'default' => apply_filters( 'ank_entries_per_page', 20 ),
			)
		);
	}

	/**
	 * Form table per-page screen option value.
	 *
	 * @param bool $keep Whether to save or skip saving the screen option value. Default false.
	 * @param string $option The option name.
	 * @param int $value The number of rows to use.
	 *
	 * @return mixed
	 * @since 1.0.0
	 *
	 */
	public function screen_options_set( $keep, $option, $value ) {
		if ( 'ank_entries_per_page' === $option ) {
			return $value;
		}

		return $keep;
	}

	/**
	 * Enqueue assets for the entries page.
	 *
	 * @since 1.0.0
	 */
	public function enqueues() {

		// Hook for addons.
		do_action( 'ank_wpforms_entries_enqueue' );
	}

	/**
	 * Builds the output for the entries page.
	 *
	 * @since 1.0.0
	 */
	public function output() {
		?>
        <div id="wpforms-overview" class="wrap wpforms-admin-wrap">

			<?php
			$entries_table = new Ank_WPForms_Entries_Table();
			?>
            <h1 class="page-title">
				<?php esc_html_e( 'Contact form entries', 'ank-wpforms-entry' ); ?>
				<?php
				if ( $entries_table->default_form_title ) {
					esc_html_e( " for " . $entries_table->default_form_title );
				}
				?>
            </h1>

			<?php
			//prepare entries only if the fields/columns for selected form are defined
			//no items message is displayed if no fields are present for the form
			if ( $entries_table->fields ) {
				$entries_table->prepare_items();
			}
			?>

            <div class="wpforms-admin-content">
				<?php do_action( 'ank_wpforms_entries_admin_overview_before_table' ); ?>
                <form id="ank-entries-table" method="get"
                      action="<?php echo esc_url( admin_url( 'admin.php?page=ank-wpforms-entries' ) ); ?>">
                    <input type="hidden" name="page" value="ank-wpforms-entries"/>
					<?php $entries_table->views(); ?>
					<?php $entries_table->display(); ?>
                </form>

            </div>

        </div>
		<?php
	}

	/**
	 * Add admin action notices and process bulk actions.
	 *
	 * @since 1.2.0
	 */
	public function notices() {
		$deleted = ! empty( $_REQUEST['deleted'] ) ? sanitize_key( $_REQUEST['deleted'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification

		if ( $deleted && 'error' !== $deleted ) {
			printf(
				'<div class="notice notice-success">
					<p>%1$s</p>
				</div>',
				sprintf( _n( '%s entry was successfully deleted.', '%s entries were successfully deleted.', $deleted, 'ank-wpforms-entry' ), $deleted )
			);
		}
		if ( 'error' === $deleted ) {
			printf(
				'<div class="notice notice-error">
					<p>%1$s</p>
				</div>',
				sprintf( "Error in deleting entries, please open a support ticket", 'ank-wpforms-entry' )
			);
		}

	}

	/**
	 * Process the bulk table actions.
	 *
	 * @since 1.2.0
	 */
	public function process_bulk_actions() {
		$ids    = isset( $_GET['entry_id'] ) ? array_map( 'absint', (array) $_GET['entry_id'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification
		$action = false;

		if ( isset( $_REQUEST['action'] ) && - 1 != $_REQUEST['action'] ) {
			$action = sanitize_key( $_REQUEST['action'] );
		} elseif ( isset( $_REQUEST['action2'] ) && - 1 != $_REQUEST['action2'] ) {
			$action = sanitize_key( $_REQUEST['action2'] );
		}

		// Checking the sortable column link.
		$is_orderby_link = ! empty( $_REQUEST['orderby'] ) && ! empty( $_REQUEST['order'] );

		if ( empty( $ids ) || empty( $action ) || $is_orderby_link ) {
			return;
		}

		// Check exact action values.
		if ( ! in_array( $action, [ 'delete' ], true ) ) {
			return;
		}

		if ( empty( $_GET['_wpnonce'] ) ) {
			return;
		}

		// Check the nonce. (plural is set as entries)
		check_admin_referer( 'bulk-entries' );

		// Check that we have a method for this action.
		if ( ! method_exists( $this, 'bulk_action_' . $action . '_entries' ) ) {
			return;
		}

		$processed_forms = count( $this->{'bulk_action_' . $action . '_entries'}( $ids ) );

		// Unset get vars and perform redirect to avoid action reuse.
		wp_safe_redirect(
			add_query_arg(
				$action . 'd',
				$processed_forms,
				remove_query_arg( array( 'action', 'action2', '_wpnonce', 'form_id', 'paged', '_wp_http_referer' ) )
			)
		);
		exit;
	}


	/**
	 * Delete entries.
	 *
	 * @param array $ids entries ids to delete.
	 *
	 * @return array List of deleted entries.
	 * @since 1.2.0
	 *
	 */
	private function bulk_action_delete_entries( $ids ) {
		if ( ! is_array( $ids ) ) {
			return [];
		}

		$deleted = [];
		foreach ( $ids as $id ) {

			$deleted[ $id ] = ank_wpforms_entry()->get_class_instance( 'entry-db' )->delete( $id );
		}

		return array_keys( array_filter( $deleted ) );
	}
}