<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'buda-desarrollo' );

/** MySQL database username */
define( 'DB_USER', 'budaituser' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Perrosorete100%' );

/** MySQL hostname */
define( 'DB_HOST', '190.228.29.62' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Xe_up7Uap!!e$57m.Gj?1H|-0H1~/MisB0Eh0Pp>h.iQbAL3m>-$g)A1<@C>4?>/' );
define( 'SECURE_AUTH_KEY',  '}[|Wvz){Z&XB@:BZvsE.phwWh(Coh{2]7B5*Em50ROi(3nJHch5CdF!,t].Wyo(e' );
define( 'LOGGED_IN_KEY',    '#L |pH8Z=?z.Cm34lNYcP^[JP:_RAQF3`;5Zxzj;jr}[JqA9l7|-_i8vSYa:(IsX' );
define( 'NONCE_KEY',        'ko8+Na vk``aDFPDL..75:)NBT](CBvI?;)]w)*c(2g$mzunbW~A]]IaR8Ux@f)@' );
define( 'AUTH_SALT',        'x2Mc!kvM}}vro{nxsFvECzPytZI{;#z_Q{m[q5951Z}1h3p~8;_,>**teYsbC.Ve' );
define( 'SECURE_AUTH_SALT', '9c+w48!x+=t/|$aH=B1T6guB&w)8jB/}0U5E3z1tm-vo,+U(&(<CIBGCo:a*iX/j' );
define( 'LOGGED_IN_SALT',   'Jx]_a~9iibvHO=}<$v%@$)Y|[beA#Q5Dp-eoN;^Y/+(!4n~{|II?uD`NL/>on`K9' );
define( 'NONCE_SALT',       'Q/*HlsH!iH1<cPm5#o4U 0+HSR&s;V}aqO%)k{ERXAY)N{_u.}?{q2Qpn6$OI7Gd' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
