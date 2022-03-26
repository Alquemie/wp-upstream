<?php

// ** MySQL settings ** //
/** The name of the database for WordPress */
define('DB_NAME', 'lemp');

/** MySQL database username */
define('DB_USER', 'lemp');

/** MySQL database password */
define('DB_PASSWORD', 'lemp');

/** MySQL hostname; on Pantheon this includes a specific port number. */
define('DB_HOST', 'database' . ':' . '3306');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Changing these will force all users to have to log in again.
 * https://api.wordpress.org/secret-key/1.1/salt
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');
/**#@-*/


if ( ! defined('DISALLOW_FILE_MODS') ) {
    define( 'DISALLOW_FILE_MODS', false );
}
if ( ! defined('DISALLOW_FILE_EDIT') ) {
    define( 'DISALLOW_FILE_EDIT', false );
}	
if ( ! defined('DISALLOW_FILE_EDIT') ) {
    define( 'DISALLOW_FILE_EDIT', false );
}	
if ( ! defined( 'WP_DEBUG' ) ) {
    define('WP_DEBUG', true);
}

