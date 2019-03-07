<?php 

/**
 * Creates the two shortcodes offered by the plugin: [wpfepp_submission_form] and [wpfepp_post_table]. Uses WPFEPP_Post_List and WPFEPP_Form.
 *
 * @since 1.0.0
 * @package WPFEPP
 **/
class WPFEPP_Shortcode_Manager
{
	/**
	 * Plugin version. This is used in wp_enqueue_style and wp_enqueue_script to make sure that the end user doesn't get outdated scripts and styles because of browser caching.
	 *
	 * @access private
	 * @var string
	 **/
	private $version;

	/**
	 * Plugin's text domain. Used for localization.
	 *
	 * @access private
	 * @var string
	 **/
	private $textdomain;

	/**
	 * Class constructor. Includes the files for WPFEPP_Post_List and WPFEPP_Form and initializes the $version attribute.
	 **/
	public function __construct($version, $textdomain)
	{
		$this->version 		= $version;
		$this->textdomain 	= $textdomain;
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-form.php';
		require_once plugin_dir_path( dirname(__FILE__) ) . 'includes/class-wpfepp-post-list.php';
	}

	/**
	 * Adds the actions of the class. The WPFEPP_Loader class registers this function with WordPress.
	 **/
	public function add_actions(){
		add_shortcode( 'wpfepp_submission_form', array($this, 'submission_form_shortcode') );
		add_shortcode( 'wpfepp_post_table', array($this, 'post_table_shortcode') );
		add_action('the_posts', array($this, 'enqueue') );
	}

	/**
	 * Enqueues stylesheets and scripts on the pages where a shortcode is present.
	 **/
	public function enqueue($posts) {
	    if ( empty($posts) || is_admin() )
	        return $posts;
	 
	    $found = false;
	 	foreach ($posts as $post) {
	        if ( has_shortcode($post->post_content, 'wpfepp_submission_form') ||  has_shortcode($post->post_content, 'wpfepp_post_table') ){
	        	$found = true;
	            break;
	        }
	    }
	 
	    if ($found){
	    	wp_enqueue_style('wpfepp-style');
	    	wp_enqueue_script('wpfepp-script');
			wp_enqueue_media();
	    }
	    return $posts;
	}

	/**
	 * Callback function for the [wpfepp_submission_form] shortcode registered in add_actions()
	 **/
	public function submission_form_shortcode($_args) {
	    $args = shortcode_atts( array( 'form' => -1 ), $_args );
		ob_start();
		$form_obj = new WPFEPP_Form($this->version, $this->textdomain, $args['form']);
		$form_obj->display();
		return ob_get_clean();
	}

	/**
	 * Callback function for the [wpfepp_post_table] shortcode registered in add_actions()
	 **/
	public function post_table_shortcode($_args){
		$args = shortcode_atts( array( 'form' => -1 ), $_args );
		ob_start();
		$post_list = new WPFEPP_Post_List($this->version, $this->textdomain, $args['form']);
		$post_list->display();
		return ob_get_clean();
	}
}

?>