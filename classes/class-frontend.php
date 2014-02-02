<?php
/**
 * @author		WPCollab Team
 * @copyright	Copyright (c) 2014, WPCollab Team
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @package		WPCollab\HelloEmoji\Frontend
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

			$classes = apply_filters( 'wpcollab_hello_emoji_css_classes', array( 'wpcollab-hello-emoji' ) );
			$open_tag = '<div class="' . join( ' ', $classes ) . '">';
			$close_tag = '</div>';

			return $open_tag . $content . $close_tag;
		}

		return $content;
	} // END wrap_content()

} // END class WPCollab_HelloEmoji_Frontend
