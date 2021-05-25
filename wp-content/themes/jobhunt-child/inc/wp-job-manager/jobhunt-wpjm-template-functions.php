<?php
/**
 *
 */


//la company deberia aparecer solamente si estas loggeado. Escenario: lista de jobs
function jobhunt_template_job_listing_company_details() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		if ( ($current_user instanceof WP_User) ) {
				?>
			<div class="job-listing-company company">
			<?php the_company_tagline( '<span class="tagline">', '</span>' ); ?>
			</div>
			<?php    
		}
	}
}
