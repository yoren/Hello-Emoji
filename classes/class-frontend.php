<?php
/**
 * @author		WPCollab Team
 * @copyright	Copyright (c) 2014, WPCollab Team
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @package		WPCollab\HelloEmoji\Frontend
 */

//avoid direct calls to this file
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * @todo Description
 *
 * @since	1.0.0
 */
class WPCollab_HelloEmoji_Frontend {

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
	 * @return	object	WPCollab_HelloEmoji_Frontend::$instance
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

		// Filter the_content to add a css class to the content
		add_filter( 'the_content', array( $this, 'wrap_content' ) );
		// Filter the_content to add a css class to the comment
		add_filter( 'comment_text', array( $this, 'wrap_content' ) );

		// Load admin JavaScript
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );

	} // END __construct()

	/**
	 * Add class to content and comment
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @return  void
	 */
	public function wrap_content( $content ) {

		global $post;

		if ( ! empty( $content ) ) {

			$settings = get_option( 'wpcollab_hello_emoji_settings' );

			// Check if post type is enabled
			if ( 'the_content' == current_filter() && empty( $settings[$post->post_type] ) ) {
				return $content;
			}
			// Check if comment is enabled
			if ( 'comment_text' == current_filter() && empty( $settings['comment'] ) ) {
				return $content;
			}

			$classes = apply_filters( 'wpcollab_hello_emoji_css_classes', array( 'wpcollab-hello-emoji' ) );
			$open_tag = '<div class="' . join( ' ', $classes ) . '">';
			$close_tag = '</div>';

			return $open_tag . $content . $close_tag;
		}

		return $content;

	} // END wrap_content()

	/**
	 * Enqueue frontend scripts
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @return  void
	 */
	public function enqueue_frontend_scripts() {

		global $post;

		$settings = get_option( 'wpcollab_hello_emoji_settings' );

		if ( is_singular( $post->post_type ) && comments_open( $post->ID ) && isset( $settings['comment'] ) ) {
			wp_enqueue_script( 'jquery-textcomplete-script', plugins_url( 'lib/jquery-textcomplete/jquery.textcomplete.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery' ), WPCollab_HelloEmoji::$version, true );

			wp_enqueue_style( 'hello-emoji-style', plugins_url( 'css/hello-emoji.css', WPCollab_HelloEmoji::get_file() ), array(), WPCollab_HelloEmoji::$version );

			wp_enqueue_script( 'hello-emoji-textcomplete-script', plugins_url( 'js/hello-emoji-textcomplete.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery-textcomplete-script' ), WPCollab_HelloEmoji::$version, true );

			$emoji_enable = 1;
		}

		if ( ! is_singular() || ( is_singular() && isset( $settings[$post->post_type] ) ) || isset( $emoji_enable ) ) {
			wp_enqueue_script( 'jquery-emoji-script', plugins_url( 'lib/jquery-emoji/jquery.emoji.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery' ), WPCollab_HelloEmoji::$version, true );

			wp_enqueue_script( 'hello-emoji-script', plugins_url( 'js/hello-emoji.js', WPCollab_HelloEmoji::get_file() ), array( 'jquery-emoji-script' ), WPCollab_HelloEmoji::$version, true );

			wp_localize_script( 'hello-emoji-script', 'hello_emoji', array( 'images_src' => plugins_url( 'images/emoji', WPCollab_HelloEmoji::get_file() ) ) );
		}

	} // END enqueue_frontend_scripts()

} // END class WPCollab_HelloEmoji_Frontend
