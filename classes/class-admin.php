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

		if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'comment.php' ) { // @todo (de)activate for post_types activated in settings

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

		register_setting(
			'writing',                 // settings page
			'wpcollab_hello_emoji_settings'          // option name
		);

		add_settings_field(
			'comment', // ID
			__( 'Emojis', 'hello-emoji' ), // Label
			array( $this, 'comments' ), // Callback
			'writing', // Page on which to display
			'default', // Section
			array(
				'label_for' => 'wpcollab_hello_emoji_comment'
			)
		);

	} // END register_settings()

	/**
	 * @todo
	 *
	 * @since 1.0
	 */
	function comments() {

		$settings = get_option( 'wpcollab_hello_emoji_settings' );
		$setting = ( isset( $settings['comment'] ) ) ? esc_attr( $settings['comment'] ) : false;
		$checked = checked( '1', $setting, false );
		?>
		<label for="wpcollab_hello_emoji_comment">
			<input type='checkbox' id='wpcollab_hello_emoji_comment' name='wpcollab_hello_emoji_settings[comment]' value='1' <?php echo $checked; ?>/>
			<?php _e( 'Enable emojis in comments', 'hello-emoji' ); ?>
		</label>
		<p class="description"><?php _e( 'Activate to process emojis in comments in addition to post content.', 'hello-emoji' ); ?></p>
		<?php
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
