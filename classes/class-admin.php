<?php
/**
 * @author		WPCollab Team
 * @copyright	Copyright (c) 2014, WPCollab Team
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @package		WPCollab\HelloEmoji\Admin
 */

//avoid direct calls to this file
if ( ! function_exists( 'add_filter' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * @todo Description
 * 
 * @since	1.0.0
 */
class WPCollab_HelloEmoji_Admin {
	
	/**
	 * Holds a copy of the object for easy reference.
	 * 
	 * @since	1.0.0
	 * @static
	 * @access	private
	 * @var		object	$instance
	 */
	private static $instance;

	/**
	 * Getter method for retrieving the object instance.
	 * 
	 * @since	1.0.0
	 * @static
	 * @access	public
	 * 
	 * @return	object	WPCollab_HelloEmoji_Admin::$instance
	 */
	public static function get_instance() {

		return self::$instance;

	} // END get_instance()

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 * 
	 * @since	1.0.0
	 * @access	public
	 * 
	 * @return	void
	 */
	public function __construct() {
		
		self::$instance = $this;
		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
	} // END __construct()
	
	/**
	 * @todo description
	 * 
	 * @since	1.0.0
	 * @access	public
	 * 
	 * @see __()
	 * @see	add_settings_section()
	 * @see	add_settings_field()
	 * 
	 * @return	void
	 */
	public function register_settings() {
		
		add_settings_section(
			'defaults',
			__( 'Defaults', 'hallo-emoji' ),
			array( $this, 'defaults_desc'),
			'wpcollab-hello-emoji-settings'
		);
		
		add_settings_field(
			'defaults-output',
			__( 'Defaults Field', 'hallo-emoji' ),
			array( $this, 'render_field' ), // @TODO
			'wpcollab-hello-emoji-settings',
			'defaults',
			array()
		);
		
	} // END register_settings()
	
	/**
	 * @todo description
	 * 
	 * @since	1.0.0
	 * @access	public
	 * 
	 * @see	add_submenu_page()
	 * @see	apply_filters()
	 * 
	 * @return	void
	 */
	public function admin_menu() {
		
		$this->pagehook = add_submenu_page(
			'options-general.php',
			__( 'Emoji Settings', 'hallo-emoji' ),
			__( 'Emoji', 'hallo-emoji' ),
			apply_filters( 'wpcollab_hello_emoji_settings_cap', 'manage_options' ),
			'emoji',
			array( $this, 'settings_page' )
		);
		
		add_action( "load-{$this->pagehook}", array( $this, 'help_tabs' ) );
		
	} // END admin_menu()
	
	/**
	 * @todo
	 *
	 * @since	1.0.0
	 * @access	public
	 *
	 * @see		get_current_screen()
	 *
	 * @return	string
 	 */
	public function help_tabs() {
		
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id'        => 'wpcollab-hello-emoji_options',
				'title'     => __( 'Help', 'hello-emoji' ),
				'callback'  => '__return_empty_string' // array( $this, 'option_tab')
			)
		);
		
	} // END help_tabs()
	
	/**
	 * @todo
	 *
	 * @since 1.0
	 */		
	function defaults_desc() {
		echo '<p>' . __( 'Some Description', 'hallo-emoji' ) . '</p>';
	}
	
	/**
	 * @todo
	 *
	 * @since 1.0
	 */		
	function render_field() {
		echo 'some-output';
	}
	
	/**
	 * @todo description
	 * 
	 * @since	1.0.0
	 * @access	public
	 * 
	 * @return	string
	 */
	public function settings_page() { ?>

		<div class="wrap">
			<h2><?php _e( 'Emoji Settings', 'hello-emoji' ); ?></h2>
			<form action="" method="post">
			<?php 
				wp_nonce_field( 'wpcollab-hello-emoji-settings-update', 'wpcollab-hello-emoji-settings-nonce' );
				do_settings_sections( 'wpcollab-hello-emoji-settings' );
				submit_button();
			?>
			</form>
		</div><!-- .wrap -->

	<?php	
	} // END settings_page()
		
} // END class WPCollab_HelloEmoji_Admin
