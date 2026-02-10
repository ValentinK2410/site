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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'dekan_wp');

/** MySQL database username */
define('DB_USER', 'dekan_wp_user');

/** MySQL database password */
define('DB_PASSWORD', 'yM5sK9nE9f');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1:3310');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'gfC3QFpWtjhCe5SGUlAOK&iwCGFaD0didn(Nt(WErB99QSBgovvndJL8VRbBDHCI');
define('SECURE_AUTH_KEY',  'FIw&4j(gg(A917%iXL8lxeYp11Gm9qqyF4XL7tm)VJ^4z8OcR4pzGp^#CW58fkJk');
define('LOGGED_IN_KEY',    'Q!R^YkXcCG%#tj5cipZJKqoqTnMH%gJ0jWF5MXeE%lgry^(kIYo^9cjHULUz(mm*');
define('NONCE_KEY',        'EHEQPWfDAS%o3tWxJYdZ@PBfVz2JWOWGxA6%J6#4Kl3N%xpJklceQKBc8PCYZJIv');
define('AUTH_SALT',        'xKwCcImYw0#WrDxAi#t)vvWX31qUmD*m&QCU75wBN%C)daKp*4eoDK2c^j38v@W2');
define('SECURE_AUTH_SALT', 'RX@F9Pa^Ux6(q&hUs9&JM!Vr4vC9mM02a!EAL!#Q%5E7SmqnPR(nRrCVZycd8apz');
define('LOGGED_IN_SALT',   '42y2%ggqy2mJXr9Z6w&zGgcHvzHrII)buAq8y1X7LGrrKTmd@NmCDm2f0r!p2Ot!');
define('NONCE_SALT',       'mNP53BgLBogQ)dJ!FFRMJHw6GnKC9ZFWgRlYiFmePiNlq%gA)HRqcoly!n8rgsSt');
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

define( 'WP_ALLOW_MULTISITE', true );

define ('FS_METHOD', 'direct');
