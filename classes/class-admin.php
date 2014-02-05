<?php
/**
 * @author		WPCollab Team
 * @copyright	Copyright (c) 2014, WPCollab Team
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @package		WPCollab\HelloEmoji\Admin
 */

//avoid direct calls to this file
if ( !defined( 'ABSPATH' ) ) {
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
		/** Load admin JavaScript. */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

	} // END __construct()

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @return  void
	 */
	public function enqueue_admin_scripts( $hook ) {

		$dev = apply_filters( 'wpcollab_hello_emoji_debug_mode', WP_DEBUG ) ? '' : '.min';

		if ( $hook == 'post.php' || $hook == 'post-new.php' && isset( $_GET['post_type'] ) ) { // @todo (de)activate for post_types activated in settings

			wp_enqueue_script( 'jquery-textcomplete-script', plugins_url( "lib/jquery-textcomplete/jquery.textcomplete{$dev}.js", WPCollab_HelloEmoji::get_file() ), array( 'jquery' ), WPCollab_HelloEmoji::$version, true );
			wp_enqueue_script( 'hello-emoji-admin-script', plugins_url( 'js/admin.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery-textcomplete-script' ), WPCollab_HelloEmoji::$version, true );

			wp_enqueue_style( 'jquery-textcomplete-style', plugins_url( "lib/jquery-textcomplete/jquery.textcomplete{$dev}.css", WPCollab_HelloEmoji::get_file() ), array(), WPCollab_HelloEmoji::$version );
			wp_enqueue_style( 'hello-emoji-admin-style', plugins_url( 'css/admin.css', WPCollab_HelloEmoji::get_file() ), array(), WPCollab_HelloEmoji::$version );

			wp_localize_script( 'hello-emoji-admin-script', 'hello_emoji', array( 'images_src' => plugins_url( 'images/emoji', WPCollab_HelloEmoji::get_file() ) ) );

		}

	} // END enqueue_admin_scripts()

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
			'post_types', // Section name
			__( 'Post Types', 'hello-emoji' ), // Section title
			array( $this, 'post_types_desc'), // Callback function
			'wpcollab_hello_emoji_settings' // Page on which to display
		);

		/**Create an option to enable emojis per post type**/
		//get all register post types
		$post_types = get_post_types();
		//remove attachments, revisions and nav_menu_items from array
		unset($post_types["attachment"], $post_types["revision"], $post_types["nav_menu_item"] );
		foreach ($post_types as $post_type ) {
			/**Prepare to make setting**/
			//get post type object
			$obj = get_post_type_object( $post_type );
			//get the label for the post type for display use
			$post_type_label = $obj->labels->name;
			//create option name with post type name
			$uniqueOptionName = $post_type;

			//This will have to loop, make a create settings field function and pass the field ID
			add_settings_field(
				$uniqueOptionName, // ID
				__( 'Enable for ' . $post_type_label, 'hello-emoji' ), // Label
				array( $this, 'per_post_types' ), // Callback
				'wpcollab_hello_emoji_settings', // Page on which to display
				'post_types', // Section
				array(
					$uniqueOptionName
				)
			);
		}

		add_settings_section(
			'comments', // Section name
			__( 'Comments', 'hello-emoji' ), // Section title
			array( $this, 'comments_desc'), // Callback function
			'wpcollab_hello_emoji_settings' // Page on which to display
		);

		add_settings_field(
				'comment', // ID
				__( 'Enable for Comments', 'hello-emoji' ), // Label
				array( $this, 'comments' ), // Callback
				'wpcollab_hello_emoji_settings', // Page on which to display
				'comments', // Section
				array(
					'label_for' => 'wpcollab_hello_emoji_comment'
				)
			);

		register_setting(
			'wpcollab_hello_emoji_settings',
			'wpcollab_hello_emoji_settings'
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

		$this->pagehook = add_options_page(
			__( 'Emoji Settings', 'hello-emoji' ),
			__( 'Emoji', 'hello-emoji' ),
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
	* Callbacks for post type enable options
	*
	* @since 0.1.0
	*/
	function per_post_types( $args ) {
		$settings = get_option( 'wpcollab_hello_emoji_settings' );
		$setting = ( isset( $settings[$args[0]] ) ) ? esc_attr( $settings[$args[0]] ) : false;
		$post_type = $args[0];
		$checked = checked( '1', $setting, false );

		echo "<input type='checkbox' id='wpcollab_hello_emoji_$post_type' name='wpcollab_hello_emoji_settings[$post_type]' value='1' $checked />";
	}

	/**
	 * @todo
	 *
	 * @since 1.0
	 */
	function comments() {

		$settings = get_option( 'wpcollab_hello_emoji_settings' );
		$setting = ( isset( $settings['comment'] ) ) ? esc_attr( $settings['comment'] ) : false;
		$checked = checked( '1', $setting, false );

		echo "<input type='checkbox' id='wpcollab_hello_emoji_comment' name='wpcollab_hello_emoji_settings[comment]' value='1' $checked />";
	}

	/**
	 * @todo
	 *
	 * @since 1.0
	 */
	function post_types_desc() {
		echo '<p>' . __( 'Check the post types you\'d like to enable Emoji.', 'hello-emoji' ) . '</p>';
	}

	/**
	 * @todo
	 *
	 * @since 1.0
	 */
	function comments_desc() {

		echo '<p>' . __( 'Check if you want to enable Emoji for the comments.', 'hello-emoji' ) . '</p>';
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
			<form action="options.php" method="post">
			<?php
				wp_nonce_field( 'wpcollab-hello-emoji-settings-update', 'wpcollab-hello-emoji-settings-nonce' );
				settings_fields( 'wpcollab_hello_emoji_settings' );
				do_settings_sections( 'wpcollab_hello_emoji_settings' );
				submit_button();
			?>
			</form>
		</div><!-- .wrap -->

	<?php
	} // END settings_page()

} // END class WPCollab_HelloEmoji_Admin
