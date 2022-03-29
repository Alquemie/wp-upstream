<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/**
 * Set root path
 */
$rootPath = realpath( __DIR__ . '/..' );

/**
 * Include the Composer autoload
 */
if (file_exists( $rootPath . '/vendor/autoload.php' )) {
	require_once( $rootPath . '/vendor/autoload.php' );
}

if (file_exists( $rootPath . '/domain.php' )) {
	require_once( $rootPath . '/domain.php' );
} else {
	$prod_domain = $_SERVER['HTTP_HOST'];
	$test_domain = $_SERVER['HTTP_HOST'];
	$dev_domain = $_SERVER['HTTP_HOST'];
}

$current_domain = $_SERVER['HTTP_HOST'];
$redirect = false;

/**
 * Pantheon platform settings. Everything you need should already be set.
 */
if (isset($_ENV['PANTHEON_ENVIRONMENT']) ) {
	define('HOSTING_ENVIRONMENT', $_ENV['PANTHEON_ENVIRONMENT']);
	$redirect = (!isset($_SERVER['HTTP_USER_AGENT_HTTPS']) || $_SERVER['HTTP_USER_AGENT_HTTPS'] != 'ON' ) ? true : false;

	/** The name of the database for WordPress */
	define('DB_NAME', $_ENV['DB_NAME']);
	/** MySQL database username */
	define('DB_USER', $_ENV['DB_USER']);
	/** MySQL database password */
	define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
	/** MySQL hostname; this includes a specific port number. */
	define('DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT']);

	/** The Database Collate type. Don't change this if in doubt. */
	define('DB_COLLATE', '');
	define('AUTH_KEY', $_ENV['AUTH_KEY']);
	define('SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY']);
	define('LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY']);
	define('NONCE_KEY', $_ENV['NONCE_KEY']);
	define('AUTH_SALT', $_ENV['AUTH_SALT']);
	define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
	define('LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT']);
	define('NONCE_SALT', $_ENV['NONCE_SALT']);
	/**#@-*/

	define('DB_CHARSET', 'utf8mb4');
	if ( ! defined('WP_TEMP_DIR') ) {
			define('WP_TEMP_DIR', $_SERVER['HOME'] .'/tmp');
	}

/**
 * DotEnv File settings.
 */
} elseif ( class_exists('Dotenv\Dotenv') && file_exists($rootPath . '/.env')  ) {
	$dotenv = Dotenv\Dotenv::create( __DIR__ . '/..' );
	$dotenv->load();

	$redirect = (!isset($_SERVER['HTTPS']) || strtoupper($_SERVER['HTTPS']) != 'ON' ) ? true : false;

	/** The name of the database for WordPress */
	define('DB_NAME', $_ENV['DB_NAME']);
	/** MySQL database username */
	define('DB_USER', $_ENV['DB_USER']);
	/** MySQL database password */
	define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
	/** MySQL hostname; this includes a specific port number. */
	define('DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT']);

	/** The Database Collate type. Don't change this if in doubt. */
	define('DB_COLLATE', '');
	define('AUTH_KEY', $_ENV['AUTH_KEY']);
	define('SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY']);
	define('LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY']);
	define('NONCE_KEY', $_ENV['NONCE_KEY']);
	define('AUTH_SALT', $_ENV['AUTH_SALT']);
	define('SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT']);
	define('LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT']);
	define('NONCE_SALT', $_ENV['NONCE_SALT']);
	/**#@-*/

	define('DB_CHARSET', 'utf8mb4');
	if ( isset($_ENV['WP_TEMP_DIR'])) {
			define('WP_TEMP_DIR', $_ENV['WP_TEMP_DIR']);
	}

/**
 * Local configuration information.
 *
 * If you are working in a local/desktop development environment and want to
 * keep your config separate, we recommend using a 'wp-config-local.php' file,
 * which you should also make sure you .gitignore.
 */
} elseif (file_exists(dirname(__FILE__) . '/wp-config-local.php') && !isset($_ENV['PANTHEON_ENVIRONMENT'])){
	# IMPORTANT: ensure your local config does not include wp-settings.php
	require_once(dirname(__FILE__) . '/wp-config-local.php');

/**
 * This block will be executed if you are NOT running on Pantheon and have NO
 * wp-config-local.php. Insert alternate config here if necessary.
 *
 * If you are only running on Pantheon, you can ignore this block.
 */
} else {

	define('DB_NAME',          'lemp');
	define('DB_USER',          'lemp');
	define('DB_PASSWORD',      'lemp');
	define('DB_HOST',          'database:3306');
	define('DB_CHARSET',       'utf8');
	define('DB_COLLATE',       '');
	define('AUTH_KEY',         'put your unique phrase here');
	define('SECURE_AUTH_KEY',  'put your unique phrase here');
	define('LOGGED_IN_KEY',    'put your unique phrase here');
	define('NONCE_KEY',        'put your unique phrase here');
	define('AUTH_SALT',        'put your unique phrase here');
	define('SECURE_AUTH_SALT', 'put your unique phrase here');
	define('LOGGED_IN_SALT',   'put your unique phrase here');
	define('NONCE_SALT',       'put your unique phrase here');
	
}

if (! isset($_ENV['HOSTING_ENVIRONMENT'])) {
	putenv('HOSTING_ENVIRONMENT=dev');
}

if (isset($_ENV['HOSTING_ENVIRONMENT']) && php_sapi_name() != 'cli') {
  // Redirect to https://$primary_domain in the Live environment

  if ($_ENV['HOSTING_ENVIRONMENT'] === 'live') {
    $current_domain = $prod_domain;
  } 
  elseif ($_ENV['HOSTING_ENVIRONMENT'] === 'test') {
    $current_domain = $stage_domain;
  } 
  elseif ($_ENV['HOSTING_ENVIRONMENT'] === 'dev') {
    $current_domain = $dev_domain;
  } 

  
}

if ( (php_sapi_name() != 'cli') && ( ($_SERVER['HTTP_HOST'] != $current_domain) || $redirect ) ) {

	# Name transaction "redirect" in New Relic for improved reporting (optional)
	if (extension_loaded('newrelic')) {
		newrelic_name_transaction("redirect");
	}

	header('HTTP/1.0 301 Moved Permanently');
	header('Location: https://'. $current_domain . $_SERVER['REQUEST_URI']);
	exit();
}


/** A couple extra tweaks to help things run well on Pantheon. **/
if ( isset( $_SERVER['HTTP_HOST'] ) ) {
    $scheme = 'https';
    define( 'WP_HOME', $scheme . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_SITEURL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/cms' );
}

/**
 * Force SSL
 */
if ( ! defined('FORCE_SSL_ADMIN') ) {
    define( 'FORCE_SSL_ADMIN', true );
}

/*
* Define wp-content directory outside of WordPress core directory
*/
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/app' );
define( 'WP_CONTENT_URL', WP_HOME . '/app' );


/** Standard wp-config.php stuff from here on down. **/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = (isset($_ENV['DB_PREFIX'])) ? $_ENV['DB_PREFIX'] : 'dev_';

if (in_array($_ENV['HOSTING_ENVIRONMENT'], array( 'test', 'live' ) ) ) {
	if ( ! defined('DISALLOW_FILE_MODS') ) {
			define( 'DISALLOW_FILE_MODS', true );
	}
	if ( ! defined('DISALLOW_FILE_EDIT') ) {
			define( 'DISALLOW_FILE_EDIT', true );
	}	
	if ( ! defined('DISALLOW_FILE_EDIT') ) {
			define( 'DISALLOW_FILE_EDIT', true );
	}	
} else {
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
}

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * You may want to examine $_ENV['PANTHEON_ENVIRONMENT'] to set this to be
 * "true" in dev, but false in test and live.
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define('WP_DEBUG', false);
}

if ( WP_DEBUG ) {
    define( 'WP_DEBUG_LOG', true );
    define( 'WP_DEBUG_DISPLAY', false );
    @ini_set( 'display_errors', 0 );
}

define('AUTOSAVE_INTERVAL', 240 );
define('WP_POST_REVISIONS', 4);
define('EMPTY_TRASH_DAYS', 15);  // Default is 30
define('DISABLE_WP_CRON', false);  // If you set to TRUE, configure another method to run jobs
define('WP_AUTO_UPDATE_CORE', false );

/* That's all, stop editing! Happy Pressing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
