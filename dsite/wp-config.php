<?php

/**

 * The base configurations of the WordPress.

 *

 * This file has the following configurations: MySQL settings, Table Prefix,

 * Secret Keys, WordPress Language, and ABSPATH. You can find more information

 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing

 * wp-config.php} Codex page. You can get the MySQL settings from your web host.

 *

 * This file is used by the wp-config.php creation script during the

 * installation. You don't have to use the web site, you can just copy this file

 * to "wp-config.php" and fill in the values.

 *

 * @package WordPress

 */

 

 define('WP_SITE_URI', ($_SERVER["HTTPS"]?"https://":"http://").$_SERVER["SERVER_NAME"]);

define('WP_SITEURI', ($_SERVER["HTTPS"]?"https://":"http://").$_SERVER["SERVER_NAME"]);



// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define('DB_NAME', 'teleheal_newfi');



/** MySQL database username */

define('DB_USER', 'teleheal_newfi');



/** MySQL database password */

define('DB_PASSWORD', 'MorganHill123$');



/** MySQL hostname */

define('DB_HOST', 'localhost');



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

define('AUTH_KEY',         'znbf8zifl1stjhjuwyvgtnvwmizk8blgae5ezi1vucr2lvll5htiqrzfmoxf0cl6');

define('SECURE_AUTH_KEY',  'xo17dtcdx21mgpawvlxpayqgagdwz12dsf8uvvbt1qpyz0jp1hbs7ilk6unacihe');

define('LOGGED_IN_KEY',    'nsoh86yofxln1v2dfq0g5lp3z4dd4fpxb7tw3uhnoaapb7rd7w8ofybnrnzbybhm');

define('NONCE_KEY',        'dv7ap6ovwknxrgxmrpew4assfdz4kqim1ip32adellmkmlluy6wymdtkuntnjvdr');

define('AUTH_SALT',        'tazewzk7zrkpf7e11ttnv6a5ntwr7jovp6uvkxfy6ujcrmwsjik1g0oynhrjuusv');

define('SECURE_AUTH_SALT', 'kgjmgfikdgtpoehdg5wmpikbatwhhvdcz81yrqtwn4wiue7dznqz5kzkf2uftwvc');

define('LOGGED_IN_SALT',   '9akdbodndcpuko3x6kmkjg21vkmprkwmr9od40u82ase8qempr3kid5ysdg1ua40');

define('NONCE_SALT',       'q1oda3zjizogm2mzc1mdrknmnlzjliy2q0n2uyy2zhm2zizdyzmtdkmjnlndg4zd');



/**#@-*/



/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each a unique

 * prefix. Only numbers, letters, and underscores please!

 */

$table_prefix  = 'wp_';



/**

 * WordPress Localized Language, defaults to English.

 *

 * Change this to localize WordPress. A corresponding MO file for the chosen

 * language must be installed to wp-content/languages. For example, install

 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German

 * language support.

 */

define('WPLANG', '');



/**

 * For developers: WordPress debugging mode.

 *

 * Change this to true to enable the display of notices during development.

 * It is strongly recommended that plugin and theme developers use WP_DEBUG

 * in their development environments.

 */

define('WP_DEBUG', false);





/* That's all, stop editing! Happy blogging. */



/** Absolute path to the WordPress directory. */

if ( !defined('ABSPATH') )

	define('ABSPATH', dirname(__FILE__) . '/');



/** Sets up WordPress vars and included files. */

require_once(ABSPATH . 'wp-settings.php');



