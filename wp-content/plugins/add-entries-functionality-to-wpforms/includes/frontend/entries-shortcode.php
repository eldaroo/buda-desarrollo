<?php

/**
 * This class contains the logic of displaying WPForm entries on frontend using shortcode
 * This class relies on https://github.com/wenzhixin/bootstrap-table for search, column show/hide and pagination
 *
 * @since 1.3.0
 *
 * @var int
 */

class Ank_WPForms_Shortcode
{

    private $form_id;

    /**
     * @var array|bool|WP_Post|null
     */
    private $form;
    /**
     * @var array
     */
    private $entries;
    /**
     * @var bool|mixed
     */
    private $columns;
    /**
     * @var bool|mixed
     */
    private $fields;
    /**
     * @var string[]
     */
    private $column_keys = array();
    /**
     * @var bool
     */
    private $searchEnabled = false;
    /**
     * @var bool
     */
    private $showUserEntries = true;
    /**
     * @var bool
     */
    private $showColumns = false;
    /**
     * @var false|string[]
     */
    private $excludedFieldIds = array();
    /**
     * @var bool
     */
    private $pagination = false;
    /**
     * @var bool
     */
    private $showEntryDate;
    /**
     * @var mixed|string
     */
    private $entryDateColumnName;

    function __construct()
    {
        add_shortcode('ank-wpform-entries', array($this, 'ank_frontend_shortcode'), 10);
    }

    /**
     * Constructor for shortcode.
     *
     * @since 1.3.0
     */
    public function ank_frontend_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'id' => '', // WPForms ID
                'search' => '', // Flag to show search box
                'show_columns' => '', // Flag to show/hide columns
                'exclude_field_ids' => '', // comma separated field IDs to be exclude from table
                'pagination' => '', // Flag to turn on/off pagination
                'show_entry_date' => '', // Flag to show entry date
                'show_user_entries' => '', // Flag to show only user entries
            ), $atts);

        if (empty($atts['id'])) {
            return;
        }

        $form_id = $atts['id'];

        if (strtolower($atts['search']) === 'yes') {
            $this->searchEnabled = true;
        }

        if (strtolower($atts['show_user_entries']) === 'no') {
            $this->showUserEntries = false;
        }

        if (strtolower($atts['show_columns']) === 'yes') {
            $this->showColumns = true;
        }

        if (strtolower($atts['pagination']) === 'yes') {
            $this->pagination = true;
        }

        if (isset($atts['exclude_field_ids'])) {
            $this->excludedFieldIds = explode(',', $atts['exclude_field_ids']);
        }

        if (isset($atts['show_entry_date'])) {
            $fields = explode(',', $atts['show_entry_date']);
            if ($fields[0] === 'yes') {
                $this->showEntryDate = true;
            }
            $fields[1] ? $this->entryDateColumnName = $fields[1] : $this->entryDateColumnName = 'Entry Date';
        }


        //validate if the wpform exists for selected form ID in shortcode
        $this->form = wpforms()->form->get(absint($form_id));

        // If the form doesn't exists, abort.
        if (empty($this->form)) {
            return;
        }

        $this->form_id = $this->form->ID;
        $this->enqueue_scripts();
        $this->set_form_fields();
        $this->set_table_data();

        return $this->create_entries_table();
    }

    /**
     * Enqueues required CSS files and JS files (Bootstarp, fontawesome and bootstrap-table files).
     *
     * @return null
     * @since 1.3.0
     *
     */
    private function enqueue_scripts()
    {
        wp_enqueue_style('ank-wpforms-custom-css', ANK_WPFORM_ENTRY_BASE_URL . 'assets/css/ank-wpforms-custom-css.css', false, ANK_WPFORM_ENTRY_VERSION);
        wp_enqueue_style('ank-wpforms-bootstrap-css', ANK_WPFORM_ENTRY_BASE_URL . 'assets/css/bootstrap.min.css', false, ANK_WPFORM_ENTRY_VERSION);
        wp_enqueue_style('ank-wpforms-bootstrap-table-css', ANK_WPFORM_ENTRY_BASE_URL . 'assets/css/bootstrap-table.min.css', 'bootstrap-css', ANK_WPFORM_ENTRY_VERSION);

        wp_enqueue_script('ank-wpforms-bootstrap-jquery', ANK_WPFORM_ENTRY_BASE_URL . '/assets/js/bootstrap.bundle.min.js', array('jquery'), ANK_WPFORM_ENTRY_VERSION);
        wp_enqueue_script('ank-wpforms-bootstrap-table-js', ANK_WPFORM_ENTRY_BASE_URL . '/assets/js/bootstrap-table.min.js', array('ank-wpforms-bootstrap-jquery'), ANK_WPFORM_ENTRY_VERSION);
        wp_enqueue_script('ank-wpforms-fontawesome', 'https://use.fontawesome.com/d99a831dce.js', array('ank-wpforms-bootstrap-jquery'), ANK_WPFORM_ENTRY_VERSION);
    }

    /**
     * Get field IDs for selected WPForm ID and sets the variable. Logic of mapping table data with its columns is based on field IDs
     *
     * @return null
     * @since 1.3.0
     *
     */
    private function set_form_fields()
    {
        //TODO: Add option to restrict columns to certain form fields only
        $fields = wpforms_get_form_fields($this->form_id);
        $this->fields = $fields;
    }

    /**
     * Function to set columns and row entries of selected form ID
     *
     * @return null
     * @since 1.3.0
     *
     */
    private function set_table_data()
    {
        $this->columns = $this->get_table_columns();
        $this->entries = $this->get_table_entries();
    }

    /**
     * Gets the columns of based on field IDs of the selected form ID
     * This function also holds the functionality of excluding of column from the table based on field ID selected in shortcode
     *
     * @return array
     * @since 1.3.0
     *
     */
    private function get_table_columns()
    {

        $columns = array();
        if (empty($this->fields) || !$this->fields) {
            return;
        }

        foreach ($this->fields as $field) {
            //TODO add the functionality to exclude columns based on $field['id'] . $field['type']
            if (!in_array($field['id'], $this->excludedFieldIds)) {
                $excluded_columns = array();
                array_push($this->column_keys, $field['id'] . $field['type']); //store column keys
                $this->column_keys = array_diff($this->column_keys, $excluded_columns);
                $column = array($field['id'] . $field['type'] => $field['label']);
                $columns = array_merge($columns, $column);
            }
        }

        // logic for adding entry date column
        if ($this->showEntryDate) {
            array_push($this->column_keys, 'entry_date');
            $column = array('entry_date' => $this->entryDateColumnName);
            $columns = array_merge($columns, $column);
        }

        return apply_filters('ank_wpforms_frontend_entries_column', $columns);
    }

    /**
     * Gets the entries of table based on form ID selected in shortcode
     *
     * @return array
     * @since 1.3.0
     *
     */
    private function get_table_entries()
    {
        $entries = ank_wpforms_entry()->get_class_instance('entry-db')->get_entries($this->form_id);


        $data = array();

        foreach ($entries as $entry) {
            $temp_data = array();
            $json_data = array();
            $temp_data = array_merge($temp_data, array('row_id' => $entry->id));
            $entry_details = $entry->entry_details;
            foreach ($entry_details as $entry_detail) {
                $data_key = $entry_detail['id'] . $entry_detail['type'];
                $temp_data = array_merge($temp_data, array($data_key => $entry_detail['value']));
                //$json_data = array_merge( $json_data, array( $this->columns[$data_key] => $entry_detail['value'] ) );
            }
            $temp_data = array_merge($temp_data, array('entry_date' => date('m/d/Y', strtotime($entry->entry_date))));
            $temp_data = array_merge($temp_data, array('viewed' => $entry->viewed));
            array_push($data, $temp_data);
        }

        return $data;

    }

    /**
     * Generates the table based on the columns and rows set
     *
     * @return string
     * @since 1.3.0
     *
     */
    private function create_entries_table()
    {
        $content = '';
        //$content = '<table class="ank-wpforms-frontend-' . $this->form_id . '-table pure-table pure-table-bordered">';
        $content = '<div class="ank-wpforms-frontend-container ank-wpforms-frontend-container-full" id=ank-wpforms-frontend-' . $this->form_id . '>';
        $content .= '<table class="ank-wpforms-frontend-table ank-wpforms-frontend-' . $this->form_id . ' table alignfull"
					data-toggle="table"  
					data-pagination="' . $this->pagination . '"  
					data-search="' . $this->searchEnabled . '"  
					data-page-list="[10,20,30,All]" 
					data-search-highlight="true"   
					data-show-columns="' . $this->showColumns . '"
					data-show-columns-toggle-all="true">';
        $content .= '<thead>';
        $content .= $this->create_column_row();
        $content .= '</thead>';

        $content .= '<tbody>';
        $rows = '';
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
                
            foreach ($this->entries as $entry_rows) {
                if ($this->showUserEntries) {
                    if ($entry_rows['2email'] === $current_user->user_email) {
                        $rows .= $this->create_entry_row($entry_rows);
                    }
                } else {
                    $rows .= $this->create_entry_row($entry_rows);
                }
                
            }
        } else {
            $rows .= "private";
        }
        $content .= $rows;
        $content .= '</tbody></table></div>';

        return $content;
    }

    /**
     * Generates columns based on the columns set
     *
     * @return string
     * @since 1.3.0
     *
     */
    private function create_column_row()
    {
        $row = '';
        $row .= '<tr>';

        foreach ($this->column_keys as $column_key) {
            $row .= '<th>' . $this->columns[$column_key] . '</th>';
        }

        /*		foreach ( $this->columns as $column_id => $column_text ) {
                    $content .= '<th>' . $column_text . '</th>';
                }*/
        $row .= '</tr>';

        return $row;
    }

    /**
     * Generates rows of entries based on the columns set
     *
     * @return string
     * @since 1.3.0
     *
     */
    private function create_entry_row($entry_rows)
    {
        $row = '';
        $row .= '<tr>';
        foreach ($this->column_keys as $row_key) { //note entry is extracted based on column_key so that data is always mapped to column
            $row .= '<td>' . $entry_rows[$row_key] . '</td>';
        }
        /*		foreach ( $entry_rows as $row_id => $row_text ) {
                    $row .= '<td>' . $row_text . '</td>';
                }*/
        $row .= '</tr>';

        return $row;
    }


}

new Ank_WPForms_Shortcode();
