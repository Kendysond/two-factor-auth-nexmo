<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://kendyson.com
 * @since      1.0.0
 *
 * @package    Two_Factor_Auth_Nexmo
 * @subpackage Two_Factor_Auth_Nexmo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Two_Factor_Auth_Nexmo
 * @subpackage Two_Factor_Auth_Nexmo/admin
 * @author     Douglas Kendyson <kendyson@kendyson.com>
 */
class Two_Factor_Auth_Nexmo_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_menu', array( $this, 'nexmo_api_keys_options_page' ) );
		add_action( 'admin_init', array( $this, 'register_nexmo_api_keys_settings' ) );
		
	}
	public function register_nexmo_api_keys_settings(){
		add_option( 'two_factor_auth_nexmo_settings', '');
  		register_setting( 'two_factor_auth_nexmo_settings', 'two_factor_auth_nexmo_settings');
		
	}
	public function nexmo_api_keys_options_page() {
		add_options_page('Nexmo Two-Factor Authentication', 'Nexmo Two-Factor Authentication', 'manage_options', $this->plugin_name, array( $this, 'nexmo_api_keys_settings_page' ) );
	}
	
	
	public function nexmo_api_keys_settings_page() {
		?>
			<div>
			<h2>Nexmo Two-Factor Authentication Settings</h2>
			<form method="post" action="options.php">
			<?php settings_fields( 'two_factor_auth_nexmo_settings' ); ?>
			<table>
				<tr valign="top">
					<th scope="row"><label for="two_factor_auth_nexmo_api_key">API Key</label></th>
					<td><input type="text" id="two_factor_auth_nexmo_api_key" name="two_factor_auth_nexmo_settings[api_key]" value="<?php echo get_option('two_factor_auth_nexmo_settings')['api_key']; ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="two_factor_auth_nexmo_api_secret">API Secret</label></th>
					<td><input type="text" id="two_factor_auth_nexmo_api_secret" name="two_factor_auth_nexmo_settings[api_secret]" value="<?php echo get_option('two_factor_auth_nexmo_settings')['api_secret']; ?>" /></td>
				</tr>
			</table>
			<?php  submit_button(); ?>
			</form>
			</div>
		<?php
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Two_Factor_Auth_Nexmo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Two_Factor_Auth_Nexmo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/two-factor-auth-nexmo-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Two_Factor_Auth_Nexmo_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Two_Factor_Auth_Nexmo_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/two-factor-auth-nexmo-admin.js', array( 'jquery' ), $this->version, false );

	}

}
