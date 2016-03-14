<?php
/*
 * Plugin Name: Ilmenite Visitor Consent
 * Plugin URI:  http://www.ilmenite.io
 * Description: A developer friendly plugin to ask the visitor to consent to some message before being able to view the site.
 * Version:     1.0
 * Author:      Bernskiold Media
 * Author URI:  http://www.bernskioldmedia.com
 * Text Domain: ilvc
 * Domain Path: /languages
 *
 * **************************************************************************
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * **************************************************************************
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class Ilmenite_Visitor_Consent {

	/**
	 * Plugin URL
	 *
	 * @var string
	 */
	public $plugin_url = '';

	/**
	 * Plugin Directory Path
	 *
	 * @var string
	 */
	public $plugin_dir = '';

	/**
	 * Plugin Version Number
	 *
	 * @var string
	 */
	public $plugin_version = '';


	/**
	 * @var The single instance of the class
	 */
	protected static $_instance = null;

	public static function instance() {

	    if ( is_null( self::$_instance ) ) {
	    	self::$_instance = new self();
	    }

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2
	 */
	private function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2
	 */
	private function __wakeup() {}

	/**
	 * Constructor
	 */
	public function __construct() {

		// Set Plugin Version
		$this->plugin_version = '1.0';

		// Set plugin Directory
		$this->plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );

		// Set Plugin URL
		$this->plugin_url = untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );

		// Load Translations
		add_action( 'plugins_loaded', array( $this, 'languages' ) );

		// Run Activation Hook
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

		// Enqueue Scripts and Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );

		// Add Options Page
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );

		// Set Default Constant Values
		if ( ! defined( 'ILVC_NOSTYLE' ) ) {
			define( 'ILVC_NOSTYLE', false );
		}

	}

	/**
	 * Load Translations
	 */
	public function languages() {

		load_plugin_textdomain( 'ilvc', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Activation Trigger
	 *
	 * This code is run automatically when the WordPress
	 * plugin is activated.
	 */
	public function plugin_activation() {

		// Initialize all the CPTs and flush permalinks
		flush_rewrite_rules();

	}

	/**
	 * Get Plugin Settings
	 *
	 * Retrieve the plugin settings and if none is saved,
	 * we revert to a default fallback.
	 *
	 * @param $key  string  Settings Key
	 */
	public function get_plugin_settings( $key ) {

		// Transform the key with options prefix
		$options_key = 'ilvc_' . $key;

		// Get the option options array
		$plugin_options = get_option( 'ilvc_settings' );

		// If we have an option, we load it. Otherwise we use the default.
		if ( '' != $plugin_options[ $options_key ] ) {
			$output = $plugin_options[ $options_key ];
		} else {

			switch ( $key ) {

				case 'title':
					$output = __( 'Are you over 18 years old?', 'ilvc' );
					break;

				case 'description':
					$output = __( 'You need to be at least 18 years old to enter this website. By continuing you agree that you are.', 'ilvc' );
					break;

				case 'accept':
					$output = __( 'I accept', 'ilvc' );
					break;

				case 'opacity':
					$output = '0.9';
					break;

				default:
					$output = '';
					break;

			}

		}

		return $output;

	}

	/**
	 * Enqueue Scripts
	 */
	public function scripts() {

		// wp_register_script( $handle, $src, $deps, $ver, $in_footer );
		wp_register_script( 'ilvc', $this->get_plugin_assets_uri() . '/js/ilvc.js', array( 'jquery' ), $this->get_plugin_version(), true );

		// Let's localize the script with our custom settings...
		wp_localize_script( 'ilvc', 'ilvc_settings', array(
			'title'         => $this->get_plugin_settings( 'title' ),
			'description'   => $this->get_plugin_settings( 'description' ),
			'accept'        => $this->get_plugin_settings( 'accept' ),
			'opacity'       => $this->get_plugin_settings( 'opacity' ),
		) );

		wp_enqueue_script( 'ilvc' );

	}

	/**
	 * Enqueue Styles
	 */
	public function styles() {

		// wp_register_style( $handle, $src, $deps, $ver, $media );
		wp_register_style( 'ilvc', $this->get_plugin_assets_uri() . '/css/ilvc.css', false, $this->get_plugin_version(), 'screen' );

		if ( ! ILVC_NOSTYLE ) {
			wp_enqueue_style( 'ilvc' );
		}

	}

	/**
	 * Add the Admin Menu Page
	 */
	public function add_admin_menu() {

		add_submenu_page(
			'themes.php',
			__( 'Ilmenite Visitor Consent', 'ilvc' ),
			__( 'Ilmenite Visitor Consent', 'ilvc' ),
			'manage_options',
			'ilvc_settings',
			array( $this, 'options_page' )
		);

	}

	/**
	 * Create the Settings Page
	 */
	function settings_init() {

		register_setting( 'ilvc_settings', 'ilvc_settings' );

		add_settings_section(
			'general_settings',
			__( 'Settings', 'ilvc' ),
			array( $this, 'settings_section_callback' ),
			'ilvc_settings'
		);

		add_settings_field(
			'ilvc_title',
			__( 'Title', 'ilvc' ),
			array( $this, 'text_render' ),
			'ilvc_settings',
			'general_settings',
			array(
				'label_for' => 'ilvc_title',
			)
		);

		add_settings_field(
			'ilvc_description',
			__( 'Description', 'ilvc' ),
			array( $this, 'textarea_render' ),
			'ilvc_settings',
			'general_settings',
			array(
				'label_for' => 'ilvc_description',
			)
		);

		add_settings_field(
			'ilvc_accept',
			__( 'Accept Button Text', 'ilvc' ),
			array( $this, 'text_render' ),
			'ilvc_settings',
			'general_settings',
			array(
				'label_for' => 'ilvc_accept',
			)
		);

		add_settings_field(
			'ilvc_opacity',
			__( 'Overlay Opacity', 'ilvc' ),
			array( $this, 'text_render' ),
			'ilvc_settings',
			'general_settings',
			array(
				'label_for' => 'ilvc_opacity',
			)
		);

	}

	/**
	 * Settings Field: Text/Input Callback
	 *
	 * @param $args
	 */
	public function text_render( $args ) {

		$options = get_option( 'ilvc_settings' );
		$value = ( $options[ $args['label_for'] ] ?$options[ $args['label_for'] ] : '' );
		?>
		<input type='text' name='ilvc_settings[<?php echo $args['label_for']; ?>]' id="<?php echo $args['label_for']; ?>" value='<?php echo $value; ?>'>
		<?php

	}

	/**
	 * Settings Field: Textarea Callback
	 *
	 * @param $args
	 */
	public function textarea_render( $args ) {

		$options = get_option( 'ilvc_settings' );
		$value = ( $options[ $args['label_for'] ] ?$options[ $args['label_for'] ] : '' );
		?>
		<textarea cols='40' rows='5' name='ilvc_settings[<?php echo $args['label_for']; ?>]' id="<?php echo $args['label_for']; ?>"><?php echo $value; ?></textarea>
		<?php

	}

	/**
	 * Main Settings Section Callback
	 */
	function settings_section_callback() {
		_e( 'Here you can configure the consent box with your own custom title, description, accept box text and opacity.', 'ilvc' );
	}

	/**
	 * Options Page Display Callback
	 */
	function options_page() {

		?>
		<div id="wrap">
			<h1><?php _e( 'Ilmenite Visitor Consent', 'ilvc' ); ?></h1>
			<p><?php _e( 'With Ilmenite Visitor Consent you have a popup box with a message that the visitor has to agree to before being allowed into your website.', 'ilvc' ); ?></p>

			<form action='options.php' method='post'>

				<?php
				settings_fields( 'ilvc_settings' );
				do_settings_sections( 'ilvc_settings' );
				submit_button();
				?>

			</form>
		</div>
		<?php

	}

	/**
	 * Get the Plugin's Directory Path
	 *
	 * @return string
	 */
	public function get_plugin_dir() {
		return $this->plugin_dir;
	}

	/**
	 * Get the Plugin's Directory URL
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	/**
	 * Get the Plugin's Version
	 *
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Get the Plugin's Asset Directory URL
	 *
	 * @return string
	 */
	public function get_plugin_assets_uri() {
		return $this->plugin_url . '/assets/';
	}

}

function Ilmenite_Visitor_Consent() {
    return Ilmenite_Visitor_Consent::instance();
}

// Initialize the class instance only once
Ilmenite_Visitor_Consent();