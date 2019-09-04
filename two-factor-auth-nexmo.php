<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://kendyson.com
 * @since             1.0.0
 * @package           Two_Factor_Auth_Nexmo
 *
 * @wordpress-plugin
 * Plugin Name:       Nexmo 2fa
 * Plugin URI:        two-factor-auth-nexmo
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Douglas Kendyson
 * Author URI:        http://kendyson.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       two-factor-auth-nexmo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TWO_FACTOR_AUTH_NEXMO_VERSION', '1.0.0' );
define( 'TWO_FACTOR_AUTH_NEXMO_KEY', '' );
define( 'TWO_FACTOR_AUTH_NEXMO_SECRET', '' );


require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-two-factor-auth-nexmo-activator.php
 */
function activate_two_factor_auth_nexmo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-two-factor-auth-nexmo-activator.php';
	Two_Factor_Auth_Nexmo_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-two-factor-auth-nexmo-deactivator.php
 */
function deactivate_two_factor_auth_nexmo() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-two-factor-auth-nexmo-deactivator.php';
	Two_Factor_Auth_Nexmo_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_two_factor_auth_nexmo' );
register_deactivation_hook( __FILE__, 'deactivate_two_factor_auth_nexmo' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-two-factor-auth-nexmo.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_two_factor_auth_nexmo() {

	$plugin = new Two_Factor_Auth_Nexmo();
	$plugin->run();

}
run_two_factor_auth_nexmo();