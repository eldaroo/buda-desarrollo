=== Add entries functionality to WPForms ===
Contributors: ankgne
Tags: entries, wprforms, entry, wpform, export, csv, export WPForm entries in CSV, CSV, export
Requires at least: 1.0.0
Tested up to: 5.6.2
Requires PHP: 7.0.33
Stable tag: 1.3.3
License: GNU General Public License v2.0 or later


This plugin adds the functionality of saving WPforms entries into database, displaying them on WP dashboard, exporting WPForms entries in csv and also displaying WPForm entries on frontend using shortcode


== Description ==
This plugin adds following functionalities to WPForms Lite
1) Saving WPForms entries into a wordpress database table
2) Displaying WPForms entries on WP dashboard
3) Displaying WPForms entries on a page, post and custom template using a shortcode (Details on usage of shortcode are mentioned below)
4) Displaying "Entry created on" date on frontend
5) Exporting WPForms entries in csv from admin screen

Entries are saved into custom database table in the back-end so it does not interferes with any of the functionalities of WPForms.
This is a lightweight plugin, does not require any configuration or settings and provides simple yet effective view of all the entries associated with WPForms.

WPForm entries on a page, post and custom template are displayed using below shortcode (read about shortcode options carefully) and details for using shortcode are as below
<pre><code>[ank-wpform-entries id=123 search="Yes" show_columns="yes" , exclude_field_ids="", pagination="yes", show_entry_date=yes,Created On]</code></pre>

Details of Options of shortcode
1) id (it's a required option of shortcode) = should be the WPForms ID for which you want to display the entries (you can get it from WPForms>All Forms , refer to screenshot section for details)

2) search (By default it's turned off) = flag to enable search on frontend (set it as "Yes" or "YES" or "yes" to enable search) and any other value is considered as "No"

3) show_columns (By default it's turned off) = flag to enable show/hide column functionality on frontend (set it as "Yes" or "YES" or "yes" to enable show/hide column functionality) and any other value is considered as "No"

4) exclude_field_ids (By default all fields are displayed) = comma separated field IDs to exclude create fields from frontend table (you can get field Ids of WPForms WPForms>All Forms>Select the form>Edit>Click on the field , Refer to screenshot section for more details)

5) pagination (By default it's turned off) = flag to enable pagination on frontend (set it as "Yes" or "YES" or "yes" to enable pagination) and any other value is considered as "No"

6) show_entry_date (By default it's turned off) = flag to hide/show "Created on" date frontend (set it as "Yes" or "YES" or "yes" to enable it and to change name of the column add the column name after comma)

== Installation ==
No special installation needed for the plugin
Download and extract plugin files to a wp-content/plugin directory.
Activate the plugin through the WordPress admin interface.


== Frequently Asked Questions ==
= Does this plugin provide shortcode to display WPForm entries on frontend? =
Answer >> Yes, this feature has been included since 1.3.0.

= I do not want to display all the columns/fields of my forms to end users so how can i exclude them on frontend? =
Answer>> Yes, by using exclude_field_ids options of shortcode. Add comma separated field IDs that you want to exclude and they will not be displayed on frontend

= I do  want to display entry/record date/created on date on frontend, is it possible to do so? =
Answer>> Yes, by using show_entry_date options of shortcode. Set the option as show_entry_date=yes,[name of the column]

= Can i change the column name of the entry date on frontend? =
Answer>> Yes, add custom column name as second field of show_entry_date options of shortcode. Set the option as show_entry_date=yes,[name of the column]

= Does it save geo -locations of the users? =
Answer >> No, it does not

= Does this plugin support export functionality? =
Answer >> Yes, this feature has been included since 1.1.0.

= Does this plugin support deletion of entries? =
Answer >> Yes, this feature has been included since 1.2.0.

= Does this plugin deletes the entries when de-activated? =
Answer>> No, it does not at the moment. A future release will have an option to delete the entries on deactivation in settings page

= Does this plugin support multi-site? =
Answer>> No, it does not at the moment. 

= Does this plugin support multi-lignual? =
Answer>> No, it does not at the moment. 


== Screenshots ==

1. Screenshot of entries for selected WPForms
2. Entries are also accessible form WPForms overview page
3. How to get WPForms ID
4. How to get field IDs
5. Screenshot of displaying WPForm entries on frontend

== Changelog ==

= 1.1.0 =
* Added functionality of exporting entries of selected WPForm in CSV format

= 1.1.1 =
* Fixed a minor bug related to export functionality

= 1.2.0 =
* Added the functionality of bulk delete of entries

= 1.3.0 =
* Fixed: A minor bug related to display of WPForm entries in Admin dashboard (WPForms>All Forms> Entries) as entries from first form were only being displayed
* Enhancement: Added the functionality of displaying entries via shortcode on frontend

= 1.3.3 =
* Fixed: A minor bug related to display of export button which was not appearing in case of single form
* Enhancement: Added the functionality of displaying entry date via shortcode on frontend

== Upgrade Notice ==

= 1.3.3 =
* Fixed: A minor bug related to display of export button which was not appearing in case of single form
* Enhancement: Added the functionality of displaying entry date via shortcode on frontend
