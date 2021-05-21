<?php

/**
 * Jobhunt Child
 *
 * @package jobhunt-child
 */

//la company deberia aparecer solamente si estas loggeado. Escenario: lista de jobs
function jobhunt_template_job_listing_company_details() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		if ( ($current_user instanceof WP_User) ) {
				?>
			<div class="job-listing-company company">
			<?php the_company_name( '<strong>', '</strong> ' ); ?>
			<?php the_company_tagline( '<span class="tagline">', '</span>' ); ?>
			</div>
			<?php    
		}
	}
}

//las redes sociales van en el header solamente si estas loggeado
function jobhunt_site_content_header() {
	if( apply_filters( 'jobhunt_show_site_content_header', true ) ) {
		?><header class="site-content-page-header" <?php echo jobhunt_site_content_bg_image(); ?>>
			<div class="site-content-page-header-inner">
				<div class="page-title-area">
					<?php jobhunt_site_content_page_title(); 
					if ( is_user_logged_in() ) {
					$current_user = wp_get_current_user();
					if ( ($current_user instanceof WP_User) ) {
							?>
						<div class="">
						<p class="">
							Me exita el git
						</p>
						</div>
						<?php    
					}
				}?>
					
				</div>
			</div>
		</header><?php
	}
}

 // Agregar foto y nombre del usuario en el header de la version mobile
function jobhunt_off_canvas_nav() {
	if ( has_nav_menu( 'handheld' ) ) {
	?>
		<div class="off-canvas-navigation-wrapper">
			<div class="off-canvas-navbar-toggle-buttons clearfix">
				<button class="navbar-toggler navbar-toggle-hamburger " type="button">
					<i class="la la-bars"></i>
				</button>
				<button class="navbar-toggler navbar-toggle-close " type="button">
					<i class="la la-close"></i>
				</button>
			</div>
			<div class="off-canvas-navigation" id="default-oc-header">
			<?php 
				if ( is_user_logged_in() ) {
					$current_user = wp_get_current_user();
					if ( ($current_user instanceof WP_User) ) {
							?>
						<div class="user-box-handheld">
						<div class="">
							<img class="circular--square" src="<?php echo get_avatar_url( $current_user->ID, 20 );?>">
						</div>
						<div>
							<h3 class="user-name-handheld"><?php echo $current_user->user_login;?></h3>
						</div>
						</div>
						<?php    
					}
				}
			?>
				<?php
					wp_nav_menu( array(
						'theme_location'    => 'handheld',
						'container_class'   => 'handheld',
						'menu_class'        => 'handheld-header-menu header-menu yamm'
					) );
				?>
		</div>
		<?php
	}
}

/**
 * El logo de buda solo se muestra si el usuario está conectado
 */

/*
function jobhunt_site_branding()
{
?>
	<div class="site-branding">
		<?php if (!is_user_logged_in()) {
			jobhunt_site_title_or_logo(); 
		} ?>
	</div>
<?php
}*/

/**
 * prueba 1
 */

function mostrar_conectado($atts)
{
	global $current_user, $user_login;
	wp_get_current_user();
	add_filter('widget_text', 'apply_shortcodes');
	if ($user_login) {
		echo '<div class="user-info" style="display:flex-inline;"> ';
		echo  get_avatar($current_user->ID, 32);
		echo '<h3 class="nomre-usuario" style="width:50%;display:inline;padding-top:5px;"> ' . $current_user->display_name . '</h3>';
		echo '</div> ';
	} else
		return '';
}
add_shortcode('mostrar_conectado', 'mostrar_conectado');

//function materialize_custom_styles() {

    /*Enqueue The Styles*/
  //  wp_enqueue_style( 'uniquestylesheetid', 'https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css' ); 
//}
//add_action( 'wp_enqueue_scripts', 'materialize_custom_styles' );

add_action( 'back_button', 'job_single_back_button' );
function job_single_back_button() {
	if ( wp_get_referer() ) {
		$back_text = __( '&laquo; Back' );
		$button    = "\n<button id='back-button' class='btn button back-button mb-2' onclick='javascript:history.back()'>$back_text</button>";
		echo ( $button );
	}
}
