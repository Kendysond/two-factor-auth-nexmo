<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://kendyson.com
 * @since      1.0.0
 *
 * @package    Two_Factor_Auth_Nexmo
 * @subpackage Two_Factor_Auth_Nexmo/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Two_Factor_Auth_Nexmo
 * @subpackage Two_Factor_Auth_Nexmo/public
 * @author     Douglas Kendyson <kendyson@kendyson.com>
 */
class Two_Factor_Auth_Nexmo_Public {

	
	protected $nexmo_client;
	protected $settings;

	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		if($this->api_keys_set()){
			$this->settings = get_option( 'two_factor_auth_nexmo_settings' );
			$basic  = new \Nexmo\Client\Credentials\Basic($this->settings['api_key'], $this->settings['api_secret']);
			$this->nexmo_client = new \Nexmo\Client(new \Nexmo\Client\Credentials\Container($basic));
		}
		
		add_action( 'authenticate', array( $this, 'intercept_login_with_two_factor_auth' ), 10, 3 );
	}

	public function intercept_login_with_two_factor_auth( $user, $username, $password ) {
		$errors = array();
		$redirect_to = isset( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : admin_url();
		$remember_me = ( isset( $_POST['rememberme'] ) && $_POST['rememberme'] === 'forever' ) ? true : false;
		
		if ( ! $this->api_keys_set() ) {
			return $user;
		}
		
		$_user = get_user_by( 'login', $username );

		$saved_request_id =  ($_user) ? get_user_meta($_user->ID, 'two_factor_auth_nexmo_request_id', true ) : null;
		$nexmo_pin_code = isset( $_POST['two_factor_auth_nexmo_pin_code'] ) ? $_POST['two_factor_auth_nexmo_pin_code'] : false;
		$nexmo_request_id = isset( $_POST['two_factor_auth_nexmo_request_id'] ) ? $_POST['two_factor_auth_nexmo_request_id'] : false;
		
		if ( $nexmo_request_id && $nexmo_pin_code && $saved_request_id == $nexmo_request_id ) {
			
			$verification = new \Nexmo\Verify\Verification($nexmo_request_id);
			// Add try catch
			try {
				$result = $this->nexmo_client->verify()->check($verification, $nexmo_pin_code);
				$response = $result->getResponseData();
				if ($response['status']  == "0") {
					wp_set_auth_cookie( $_user->ID, $remember_me );
					wp_safe_redirect( $redirect_to );
					exit;
				}
			}
			catch(Exception $e) {
				// handle invalid pin code
				if ($e->getCode() == 16){
					$errors = array( "Invalid PIN code" );
				}
				$this->verify_user( $_user, $redirect_to, $remember_me,$errors );
			}
		}

		if ( $_user ) {
			$this->verify_user( $_user, $redirect_to, $remember_me );
		}

		return $user;
	}

	private function verify_user( $user, $redirect_to, $remember_me, $errors = array() ) {
		
		$enabled_2fa = get_user_meta($user->ID, 'two_factor_auth_nexmo_enabled', true );
		$mobile = get_user_meta($user->ID, 'two_factor_auth_nexmo_mobile', true );
		$saved_request_id = get_user_meta($user->ID, 'two_factor_auth_nexmo_request_id', true );
		
		if ( ! $mobile || ! $enabled_2fa || !$this->api_keys_set()) {
			return;
		}

		try {
			$verification = new \Nexmo\Verify\Verification($mobile, $this->settings['sender_name']);
			$this->nexmo_client->verify()->start($verification);
		}
		catch(Exception $e) {
			$errors = array( "Error sending verification request" );
		}
		

		$request_id = $verification->getRequestId();
		update_user_meta( $user->ID, 'two_factor_auth_nexmo_request_id', $request_id);
		
		wp_logout();
		nocache_headers();
		header('Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );
		login_header('Nexmo Two-Factor Authentication', '<p class="message">' . sprintf( 'Enter the PIN code sent to your mobile number ending in <strong>%1$s</strong>' , substr($mobile, -5) ) . '</p>');

		if(!empty($errors)) { 
		
		?>
			<div id="login_error"><?php echo implode( '<br />', $errors ) ?></div>
		<?php  } ?>

		<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ) ?>" method="post" autocomplete="off">
			<p>
				<label for="two_factor_auth_nexmo_pin_code">PIN code
					<br />
					<input type="number" name="two_factor_auth_nexmo_pin_code" id="two_factor_auth_nexmo_pin_code" class="input" value="" size="6" />
				</label>
			</p>
			<p class="submit">
				<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="Verify" />
				<input type="hidden" name="log" value="<?php echo esc_attr( $user->user_login ) ?>" />
				<input type="hidden" name="two_factor_auth_nexmo_request_id" value="<?php echo esc_attr( $request_id ) ?>" />
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ) ?>" />

				<?php if ( $remember_me ) : ?>
					<input type="hidden" name="rememberme" value="forever" />
				<?php endif; ?>
			</p>
		</form>

		<?php 
		
		login_footer( 'two_factor_auth_nexmo_pin_code' );

		exit;
	}
	

	
	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/two-factor-auth-nexmo-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/two-factor-auth-nexmo-public.js', array( 'jquery' ), $this->version, false );

	}
	public function api_keys_set() {
		$settings = get_option( 'two_factor_auth_nexmo_settings' );
		return $settings['api_key'] && $settings['api_secret'] ? true : false;
	
	}
}
