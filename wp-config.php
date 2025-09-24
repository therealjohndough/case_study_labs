<?php
define( 'WP_CACHE', true ); // By Speed Optimizer by SiteGround

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db0gejgvbnwqe6' );

/** Database username */
define( 'DB_USER', 'ukpgvhgondxew' );

/** Database password */
define( 'DB_PASSWORD', 'o2sy0e3jnlgf' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'y; 8];;+~1/#)nbFq>M^>5V+v<zFwX&FPxDxIjoqw5}@^p3yi(]zVyvHPI@3Asu0' );
define( 'SECURE_AUTH_KEY',   'maG[t(I^G5=<Q;)A+?]K6.>u#0PA?#GB8[m%?{OJ@x;:>bBOJ;HY5VN[#.-[O+()' );
define( 'LOGGED_IN_KEY',     'lnD|-RgOx9&hMhtH[ohcUEmTFrGVV0m8y::n8 1]O@u*GSa%zt]@@oC0K.;t-7~m' );
define( 'NONCE_KEY',         '.9T&J#hq1X|?/S{]Y:;HI~_3UM#AeXe9C9<:4{sc$^*sv>_T#2NlI5>/JT+]j{/9' );
define( 'AUTH_SALT',         '2mA)J:_si%kM`7aBh3h^aQc+XaAiKK>J2CgQHD/R~Q|W<pI4/9Z vC0|gc}S<F( ' );
define( 'SECURE_AUTH_SALT',  '`v#qAP$ip.VG/*=#;#)FVsz?TT@Adg?kr6#{f OEGcJv0(}F~#BR1J_2B>g!T[&k' );
define( 'LOGGED_IN_SALT',    '6EBtvUYfn5^1W9/ymu6o3hH9.X$1Rw#};G[>S6FA@d^Mcp5mG6W):.Fx624-VmsX' );
define( 'NONCE_SALT',        ':p2WQtEe)wvtyuWYSz-F[mr^Cg5@A_nY1z$Ow#=Ey}vGwym :%OFFN2^n5JQ}:q,' );
define( 'WP_CACHE_KEY_SALT', 'nEK36#[4)A1uvkX<x0NA]-V /2?$Xvm_wh1t%HEAn|,2823e!>X8gJ1({yY+ -gJ' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'kti_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system
