<?php
/**
 * Fetch all published forms
 *
 * @param array $args Additional arguments array.
 *
 * @return array
 * @since 1.0.0
 *
 */
function ank_wpforms_entries_get_all_forms() {
	$args  = array( 'post_status' => 'publish' );
	$forms = wpforms()->form->get( "", $args );

	return $forms;
}

/**
 * Fetch first form
 *
 *
 * @param array $args Additional arguments array.
 *
 * @return array
 * @since 1.0.0
 *
 */
function ank_wpforms_entries_get_first_form() {

	$args = array(
		'post_status'    => 'publish',
		'posts_per_page' => '1',
	);

	$forms = wpforms()->form->get( "", $args );

	return $forms[0];
}

/**
 * Decode the passed values and return the decoded value
 *
 *
 * @param array $data
 *
 * @return array $data
 * @since 1.0.0
 *
 */
function ank_wpforms_entries_decode_value( $data ) {
	$decoded = base64_decode( $data );

	if ( $decoded === false || ! is_string( $decoded ) ) {
		$data = '';
	} else {
		$data = json_decode( $decoded, true );
	}

	return $data;
}

/**
 * Validate user for permissions
 *
 *
 * @since 1.1.0
 *
 */
function ank_wpforms_entries_user_permission() {

	// Check if user has rights to export
	$current_user        = wp_get_current_user();
	$current_user->roles = apply_filters( 'ank_wpforms_entries_add_user_roles', $current_user->roles );
	$current_user->roles = array_unique( $current_user->roles );

	$user_can_export              = false;
	$roles_with_export_permission = apply_filters( 'ank_wpforms_entries_user_export_permission_roles', array( 'administrator' ) );

	if ( $current_user instanceof WP_User ) {
		$can_users = array_intersect( $roles_with_export_permission, $current_user->roles );
		if ( ! empty( $can_users ) || is_super_admin( $current_user->ID ) ) {
			$user_can_export = true;
		}
	}

	return $user_can_export;
}
