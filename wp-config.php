<?php
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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

set_time_limit(1200);
@ini_set( 'max_input_vars' , 7000 );

define( 'DB_NAME', 'eca' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '#p /J@WD_EBG(ww<{n_fO:=#8oC(/z- >Yg9mX*tr4dSz_ 942,/E!);o^$h28Gf' );
define( 'SECURE_AUTH_KEY',  'nMAM9E%~v}e7/S2,OoD/V62oC>:m&9:u@.2;Ccj}%~to%[DT)VjKu^w|fqDI}4Ff' );
define( 'LOGGED_IN_KEY',    '&y+!r9dZh<N^,M#(49y|,dgcx3[+$4%([_}-0V5p*jfg&=*E,b#$A)(6Ilf:!+?k' );
define( 'NONCE_KEY',        'q:qsWd5i<.5:`8Pt,jeF;~.^SLLYS>senOAwEg4|-<.d3[k[1n:7m=4TYFf=ba7d' );
define( 'AUTH_SALT',        '&vre~NcPh.9t^DG3@RNt[yX#.HCNh:XbaXOaHw5`W/]][FmIOx<b: |;2x~KDIPe' );
define( 'SECURE_AUTH_SALT', '{q}:@~^O.L|i?vOIHKdXz}a`q,)E<yU&~O<w3%~X>ZO4):4PVT(Zhyyrs1Iw:53N' );
define( 'LOGGED_IN_SALT',   '3G9EC{,_^X@/eO:]Zw&{}+V^Q::Sr#ZvefLikzAImvM4|NPN#f`PA9bG,<=BlXL%' );
define( 'NONCE_SALT',       ':RTAjV*AIOdOABTq2NK9Sriii0PKIGUj^dlfO;Z};PN>muN*c[tN;cK*8p 8t[fm' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'eca_';

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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
