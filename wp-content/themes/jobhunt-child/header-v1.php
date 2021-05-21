<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package jobhunt
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<style>
    
.user-name {
    color: orange !important;
}

.user-box-handheld {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
}

.user-name-handheld {
    color: white !important;
}

.author-photo {
    float: left;
    text-align: left;
    padding: 3px;
    border-radius: 50%;
}

.author-photo-handheld {
    position: inherit;
}

.circular--square {
    border-radius: 50%;
    transform: scale(0.9);
}

h2 {
    color: white !important;
}
</style>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php do_action( 'jobhunt_before_site' ); ?>

<div class="off-canvas-wrapper">
<div id="page" class="hfeed site">
    <?php do_action( 'jobhunt_before_header' ); ?>
    <?php if(is_user_logged_in()){ ?>
        <style>
        @media screen and (min-width:1200px){
          .site-branding{
                    position: absolute;
                    top:-1vh;
                    left:0%;
                    width:22%!important;
               }
          }
        @media screen and (min-width:1400px){
          .site-branding{
                    position: absolute;
                    top:-1vh;
                    left:-32%;
                    width:22%!important;
               }
          }
        </style> 
        
    <?php }
    ?>
    <header class="site-header header-v1 <?php echo jobhunt_header_additional_classes(); ?>" style="<?php jobhunt_header_styles(); ?>">
        
        <div class="<?php echo jobhunt_has_handheld_header() ? 'desktop-only' : ''; ?> <?php echo jobhunt_has_sticky_header() ? 'jobhunt-stick-this' : ''; ?>">
            <div class="container">
                <div class="site-header-inner">

                    <?php 
                        if ( is_user_logged_in() ) {
                            $current_user = wp_get_current_user();
                            if ( ($current_user instanceof WP_User) ) {
                                    ?>
                                <div class="author-photo">
                                <a href="https://budait.com/main-dashboard/">
                                    <img class="circular--square" src="<?php echo get_avatar_url( $current_user->ID, 20 );?>">
                                </a>
                                </div>
                                
                                <div>
                                <a href="https://budait.com/main-dashboard/">
                                    <h3 class="user-name"><?php echo $current_user->user_login;?></h3>
                                </a>
                                </div>
                                <?php    
                            }
                        }
                    ?>
                    <?php
                    /**
                     * Functions hooked into jobhunt_header action
                     *
                     * @hooked jobhunt_site_branding             - 10
                     * @hooked jobhunt_primary_nav               - 20
                     * @hooked jobhunt_post_a_job                - 30
                     * @hooked jobhunt_secondary_nav             - 40
                     */
                    do_action( 'jobhunt_header_v1' ); ?>

                </div>
            </div>
        </div>

        <?php
        /**
         * @hooked jobhunt_header_handheld - 10
         */
        do_action( 'jobhunt_after_header' ); ?>
    
    </header><!-- /.site-header -->

    <?php
    /**
     * Functions hooked in to jobhunt_before_content
     *
     */
    do_action( 'jobhunt_before_content' ); ?>

    <div id="content" class="site-content" tabindex="-1">
        <div class="container">
            <div class="site-content-inner">
                <?php
                do_action( 'jobhunt_content_top' );
