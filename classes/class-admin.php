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

		// Load admin JavaScript.
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
	public function enqueue_admin_scripts() {

		wp_enqueue_script( 'jquery-textcomplete-script', plugins_url( 'lib/jquery-textcomplete/jquery.textcomplete.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery' ), WPCollab_HelloEmoji::$version, true );

		wp_enqueue_style( 'jquery-textcomplete-style', plugins_url( 'lib/jquery-textcomplete/jquery.textcomplete.css', WPCollab_HelloEmoji::get_file() ), array(), WPCollab_HelloEmoji::$version );

		wp_enqueue_style( 'hello-emoji-admin-style', plugins_url( 'css/admin.css', WPCollab_HelloEmoji::get_file() ), array(), WPCollab_HelloEmoji::$version );

		wp_enqueue_script( 'hello-emoji-admin-script', plugins_url( 'js/admin.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery-textcomplete-script' ), WPCollab_HelloEmoji::$version, true );
		wp_localize_script( 'hello-emoji-admin-script', 'hello_emoji', array( 'images_src' => plugins_url( 'lib/jquery-emoji/images/emojis', WPCollab_HelloEmoji::get_file() ) ) );

	}

} // END class WPCollab_HelloEmoji_Admin
