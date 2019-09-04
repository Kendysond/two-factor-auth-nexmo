<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://kendyson.com
 * @since      1.0.0
 *
 * @package    Two_Factor_Auth_Nexmo
 * @subpackage Two_Factor_Auth_Nexmo/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Two_Factor_Auth_Nexmo
 * @subpackage Two_Factor_Auth_Nexmo/includes
 * @author     Douglas Kendyson <kendyson@kendyson.com>
 */
class Two_Factor_Auth_Nexmo_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'two-factor-auth-nexmo',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
