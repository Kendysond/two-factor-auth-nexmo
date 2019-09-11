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
		

		add_action( 'show_user_profile', array( $this, 'nexmo_user_settings_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'nexmo_user_settings_fields' ) );
		add_action( 'user_profile_update_errors', array( $this, 'validate_nexmo_user_settings_fields' ), 10, 3 );
		add_action( 'personal_options_update', array( $this, 'save_nexmo_user_settings_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_nexmo_user_settings_fields' ) );

	}
	public function register_nexmo_api_keys_settings(){
		add_option( 'two_factor_auth_nexmo_settings', '');
  		register_setting( 'two_factor_auth_nexmo_settings', 'two_factor_auth_nexmo_settings');
		
	}
	public function nexmo_api_keys_options_page() {
		add_options_page('Nexmo Two-Factor Authentication', 'Nexmo Two-Factor Authentication', 'manage_options', $this->plugin_name, array( $this, 'nexmo_api_keys_settings_page' ) );
	}
	
	
	public function nexmo_api_keys_settings_page() {
		$settings = get_option('two_factor_auth_nexmo_settings');
		?>
			<div>
			<h2>Nexmo Two-Factor Authentication Settings</h2>
			<form method="post" action="options.php">
			<?php settings_fields( 'two_factor_auth_nexmo_settings' ); ?>
			<table>
				<tr valign="top">
					<th scope="row"><label for="two_factor_auth_nexmo_api_key">API Key</label></th>
					<td><input required type="text" id="two_factor_auth_nexmo_api_key" name="two_factor_auth_nexmo_settings[api_key]" value="<?php echo esc_attr( isset( $settings['api_key'] ) ? esc_attr( $settings['api_key']) : '' ) ?>" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="two_factor_auth_nexmo_api_secret">API Secret</label></th>
					<td><input required type="text" id="two_factor_auth_nexmo_api_secret" name="two_factor_auth_nexmo_settings[api_secret]" value="<?php echo esc_attr( isset( $settings['api_secret'] ) ? esc_attr( $settings['api_secret']) : '' ) ?>" /></td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="two_factor_auth_nexmo_sender_name">SMS Sender Name</label></th>
					<td><input required  type="text" id="two_factor_auth_nexmo_sender_name" name="two_factor_auth_nexmo_settings[sender_name]" value="<?php echo esc_attr( isset( $settings['sender_name'] ) ? esc_attr( $settings['sender_name']) : '' ) ?>" /></td>
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
	function nexmo_user_settings_fields( $user ) { 

		?>
			<h3><?php _e("Nexmo Two-Factor Authentication Settings ", "blank"); ?></h3>
		
			<table class="form-table">
			<tr>
				<th><label for="two_factor_auth_nexmo_mobile"><?php _e("Mobile Number"); ?></label></th>
				<td>
					<input type="text" name="two_factor_auth_nexmo_mobile" id="two_factor_auth_nexmo_mobile" value="<?php echo esc_attr( get_the_author_meta( 'two_factor_auth_nexmo_mobile', $user->ID ) ); ?>" class="regular-text" /><br />
					<span class="description"><?php _e("Please enter your mobile number with the full country code, e.g 971XXXXXXX."); ?></span>
				</td>
			</tr>
			
			<tr>
				<th>  <label for="postalcode"><?php _e("Enable Two Factor Authentication on profile"); ?></label></th>
				<td>
					<input type="checkbox" name="two_factor_auth_nexmo_enabled" <?php  echo esc_attr(get_the_author_meta( 'two_factor_auth_nexmo_enabled', $user->ID )) == 1 ?  'checked' : ''; ?> value="<?php echo esc_attr( get_the_author_meta( 'two_factor_auth_nexmo_enabled', $user->ID ) ); ?>">
				</td>
			</tr>
			</table>
		<?php 
	}
	public function mobile_number_in_use( $user_id, $mobile ) {
		if ( ! $mobile ) {
			return false;
		}

		$users = get_users(
			array(
				'meta_key' => 'two_factor_auth_nexmo_mobile',
				'meta_value' => $mobile,
				'number' => 1
			)
		);

		if ( 0 < count( $users ) && $user_id !== $users[0]->ID ) {
			return true;
		}

		return false;
	}
	public function validate_nexmo_user_settings_fields( &$errors, $update, &$user ) {
		$mobile = $_POST['two_factor_auth_nexmo_mobile'];
		$enabled_2fa = isset( $_POST['two_factor_auth_nexmo_enabled'] ) ? 1 : 0;

		if ($user && $enabled_2fa && !$mobile ) {
			$errors->add( 'two_factor_auth_nexmo_update_error', 'Mobile number must be set to enable 2FA.' );
		}
		if ($user && $mobile ) {
			if ($this->mobile_number_in_use( $user->ID, $mobile ) ) {
				$errors->add( 'two_factor_auth_nexmo_update_error', 'Mobile number already in use.' );
			}
		}
	}

	public function save_nexmo_user_settings_fields( $user_id ) {

		$mobile = $_POST['two_factor_auth_nexmo_mobile'];
		$enabled_2fa = isset($_POST['two_factor_auth_nexmo_enabled']) ? 1 : 0;
		if (!current_user_can( 'edit_user', $user_id ) || $this->mobile_number_in_use($user_id, $mobile ) ) { 
			return false; 
		}
		update_user_meta($user_id, 'two_factor_auth_nexmo_mobile', $mobile );
		if($mobile){
			update_user_meta($user_id, 'two_factor_auth_nexmo_enabled', $enabled_2fa );
		}
		
	}
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
