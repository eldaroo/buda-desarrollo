<?php


class Ank_WPForms_Entries_Table extends WP_List_Table
{

    /**
     * Number of forms to show per page.
     *
     * @since 1.0.0
     *
     * @var int
     */
    public $per_page;

    /**
     * Default form ID.
     *
     * @since 1.0.0
     *
     * @var int
     */
    public $default_form_id;

    /**
     * Default form title.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $default_form_title;

    /**
     * Fields of the selected/default form.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $fields;

    /**
     * All entries associated with a form.
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $entries;

    /**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        $this->get_form_id();
        $this->get_form_title();
        $this->get_form_fields();

        // Utilize the parent constructor to build the main class properties.
        parent::__construct(
            array(
                'singular' => 'entry',
                'plural' => 'entries',
                'ajax' => false,
            )
        );

        // Default number of forms to show per page.
        $this->per_page = (int)apply_filters('ank_entries_per_page', 20);
    }

    /**
     * Retrieve the table columns.
     *
     * @return array $columns Array of all the list table columns.
     * @since 1.0.0
     *
     */
    public function get_columns()
    {

        $columns = array();

        $columns['cb'] = '<input type="checkbox" />';

        if (empty($this->fields) || !$this->fields) {
            return;
        }

        foreach ($this->fields as $field) {
            $column = array($field['id'] . $field['type'] => $field['label']);
            $columns = array_merge($columns, $column);
        }

        //TODO: setting page to suppress/enable these options
        $columns = array_merge($columns, array('entry_date' => 'Created on'));
        //TODO: Add column viewed in future version
        //$columns = array_merge( $columns, array( 'viewed' => 'Viewed' ) );

        return apply_filters('ank_wpforms_entries_table_columns', $columns);
    }

    /**
     * Render the checkbox column.
     *
     * @param WP_Post $form
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function column_cb($item)
    {

        return '<input type="checkbox" name="entry_id[]" value="' . absint($item['row_id']) . '" />';
    }

    /**
     * Render the columns.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return string
     * @since 1.0.0
     *
     */
    public function column_default($item, $column_name)
    {

        if (strpos($column_name, "textarea") !== false) {
            $value = esc_textarea($item[$column_name]);
        } else {
            $value = esc_html($item[$column_name]);
        }

        return apply_filters('ank_wpforms_entries_table_column_value', $value, $item, $column_name);
    }

    /**
     * Define bulk actions available for our table listing.
     *
     * @return array
     * @since 1.2.0
     *
     */
    public function get_bulk_actions()
    {

        if (ank_wpforms_entries_user_permission()) {
            $actions = array(
                'delete' => esc_html__('Delete', 'ank-wpforms-entry'),
            );
        }

        return $actions;
    }

    /**
     * Message to be displayed when there are no forms.
     *
     * @since 1.0.0
     */
    public function no_items()
    {
        //when fields are present for the form i.e. empty form
        if (!$this->fields && $this->default_form_id) {
            printf(
                wp_kses(
                /* translators: %s - WPForms Builder page. */
                    __('Whoops, there are no fields defined for the selected form. Want to create fields in a form , <a href="%s">give it a go</a>?', 'ank-wpforms-entry'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ),
                esc_url(admin_url('admin.php?page=wpforms-builder&view=fields&form_id=' . $this->default_form_id))
            );
        } elseif (empty($this->entries) && $this->default_form_id) {//when no entries associated with form are present
            printf(
                __('Whoops, there are no entries associated with the selected form.', 'ank-wpforms-entry')
            );
        } else {//Catch all
            printf(
                wp_kses(
                /* translators: %s - WPForms Builder page. */
                    __('Whoops, you haven\'t created a form yet. Want to <a href="%s">give it a go</a>?', 'ank-wpforms-entry'),
                    array(
                        'a' => array(
                            'href' => array(),
                        ),
                    )
                ),
                esc_url(admin_url('admin.php?page=wpforms-builder'))
            );
        }
    }

    /**
     * Creates the top navigation for filtering entries by form
     *
     * @param string $which
     *
     */
    protected function extra_tablenav($which)
    {

        ?>
        <div class="alignleft actions">
            <?php
            if ('top' === $which && !is_singular()) {
                ob_start();

                $this->forms_dropdown();
                /**
                 * Fires before the Filter button on the Posts and Pages list tables.
                 *
                 * The Filter button allows sorting by date and/or category on the
                 * Posts list table, and sorting by date on the Pages list table.
                 *
                 * @param string $post_type The post type slug.
                 * @param string $which The location of the extra table nav markup:
                 *                          'top' or 'bottom' for WP_Posts_List_Table,
                 *                          'bar' for WP_Media_List_Table.
                 *
                 * @since 4.6.0 The `$which` parameter was added.
                 *
                 * @since 2.1.0
                 * @since 4.4.0 The `$post_type` parameter was added.
                 */
                do_action('restrict_manage_posts', $this->screen->post_type, $which);

                $output = ob_get_clean();
                if (!empty($output)) {
                    echo $output;
                    submit_button(__('Filter'), '', 'filter_action', false, array('id' => 'post-query-submit'));
                }
                $forms = ank_wpforms_entries_get_all_forms();
                if ($forms && (count($forms) > 0)) {
                    // export button should when forms exist and there is more than one form
                    submit_button(__(Ank_WPForms_Entries::EXPORT_BUTTON), 'primary', 'export', false, array('id' => 'export-request-submit'));
                }

            }
            ?>
        </div>
        <?php
        /**
         * Fires immediately following the closing "actions" div in the tablenav for the posts
         * list table.
         *
         * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
         *
         * @since 4.4.0
         *
         */
        do_action('ank_wpforms_entries_manage_posts_extra_tablenav', $which);
    }

    /**
     * Get the form fields for selected form.
     *
     *
     * @since 1.0.0
     *
     */
    protected function get_form_fields()
    {
        //TODO: Add option to restrict columns to certain form fields only
        $fields = wpforms_get_form_fields($this->default_form_id);
        $this->fields = $fields;
    }

    /**
     * Get  form ID of selected form.
     *
     * @since 1.0.0
     *
     */
    protected function get_form_id()
    {
        $form_id_request = 0;
        if (isset($_GET['form_id'])) { //if the request is coming from all forms -> entries
            $form_id_request = (int)$_GET['form_id'];
        } elseif ($_GET['m']) { //set the default form id as selected in filter on entries page i.e. in page parameters
            $form_id_request = (int)$_GET['m'];
        }

        //If incoming request is not from filter page then pick the first form (oldest form) and display its entries
        if ($form_id_request == 0) {
            $form = ank_wpforms_entries_get_first_form();
            $this->default_form_id = $form->ID;
        } else {
            $this->default_form_id = $form_id_request;
        }
    }

    /**
     * Gets form title for selected form.
     *
     *
     * @since 1.0.0
     *
     */
    protected function get_form_title()
    {
        $form = wpforms()->form->get($this->default_form_id);
        $this->default_form_title = esc_html($form->post_title);
    }

    /**
     * Displays a dropdown for filtering items in the list table by form.
     *
     *
     * @since 1.0.0
     *
     */
    protected function forms_dropdown()
    {
        $forms = ank_wpforms_entries_get_all_forms();

        if (!$forms) {
            return;
        }

        $form_count = count($forms);

        // Dropdown will not appear in case of single form
        if (!$form_count || (1 == $form_count)) {
            return;
        }

        $m = isset($_GET['m']) ? (int)$_GET['m'] : 0;
        ?>
        <label for="filter-by-form" class="screen-reader-text"><?php _e('Filter by form'); ?></label>
        <select name="m" id="filter-by-form">
            <?php
            foreach ($forms as $form) {
                printf(
                    "<option %s value='%s'>%s</option>\n",
                    selected($m, $form->ID, false),
                    esc_attr($form->ID),
                    /* translators: 1: Month name, 2: 4-digit year. */
                    sprintf(__('%1$s'), $form->post_title)
                );
            }
            ?>
        </select>
        <?php
    }

    /**
     * Fetch and setup the final data for the table.
     *
     * @since 1.0.0
     */
    public function prepare_items()
    {
        // Setup the columns.
        $columns = $this->get_columns();

        // Hidden columns (none).
        $hidden = array();

        // Define which columns can be sorted - date.
        $sortable = array(
            'entry_date' => array('date', false),
        );

        // Set column headers.
        $this->_column_headers = array($columns, $hidden, $sortable);

        //Get total number of records required for pagination
        $total = ank_wpforms_entry()->get_class_instance('entry-db')->get_count_all_records($this->default_form_id);
        $per_page = $this->get_items_per_page('ank_entries_per_page', $this->per_page);
        $page = $this->get_pagenum(); //selected page of pagination

        //get all entries associated with a form based on pagination
        $entries = ank_wpforms_entry()->get_class_instance('entry-db')->get_entries($this->default_form_id, $page, $per_page);
        $this->entries = $entries;
        $data = array();

        foreach ($entries as $entry) {
            $temp_data = array();
            $temp_data = array_merge($temp_data, array('row_id' => $entry->id));
            $entry_details = $entry->entry_details;
            foreach ($entry_details as $entry_detail) {
                $temp_data = array_merge($temp_data, array($entry_detail['id'] . $entry_detail['type'] => $entry_detail['value']));
            }
            $temp_data = array_merge($temp_data, array('entry_date' => $entry->entry_date));
            $temp_data = array_merge($temp_data, array('viewed' => $entry->viewed));
            array_push($data, $temp_data);
        }

        // Giddy up.
        $this->items = $data;

        // Finalize pagination.
        $this->set_pagination_args(
            array(
                'total_items' => $total,
                'per_page' => $per_page,
                'total_pages' => ceil($total / $per_page),
            )
        );
    }

    /**
     * Extending the `display_rows()` method in order to add hooks.
     *
     * @since 1.5.6
     */
    public function display_rows()
    {
        do_action('ank_wpforms_entries_admin_overview_before_rows', $this);

        parent::display_rows();

        do_action('ank_wpforms_entries_admin_overview_after_rows', $this);
    }

}


