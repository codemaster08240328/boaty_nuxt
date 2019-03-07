<?php

if(!class_exists('WPFEPP_Tab')){
    require_once 'class-wpfepp-tab.php';
}

class WPFEPP_Tab_Settings_Media extends WPFEPP_Tab
{
	/**
	 * Class constructor. Calls the parent constructor.
	 *
	 * @var string $version Plugin version.
	 * @var string $textdomain Plugin's textdomain.
	 * @var string $slug Tab slug.
	 * @var string $name Tab name.
	 **/
	function __construct($version, $textdomain, $slug, $name) {
		parent::__construct($version, $textdomain, $slug, $name);
	}

	/**
	 * Registers the actions of this class with WordPress. This function is called by add_actions of WPFEPP_Tab_Collection, which in turn is called by add_actions of WPFEPP_Plugin_Settings.
	 **/
	public function add_actions(){
		add_action( 'admin_init', array($this, 'create_options') );
	}

	/**
	 * Creates the settings section, resgisters setting and adds settings fields. Uses WordPress' settings API.
	 *
	 * @see add_settings_section()
	 * @see register_setting()
	 * @see add_settings_field()
	 **/
	public function create_options(){
		$section 		= 'wpfepp_media_settings_section';
		$tab 			= 'wpfepp_media_settings_tab';
		$option_id 		= 'wpfepp_media_settings';
		$curr_settings 	= get_option($option_id);
		$callback 		= array($this->renderer, 'render');
		$args 			= array('group' => $option_id, 'curr' => $curr_settings);

		add_settings_section( $section, __('Media Settings', $this->textdomain), array($this, 'section_callback'), $tab );

		register_setting( $tab, $option_id, array($this->validator, 'validate') );

		add_settings_field(
	        'max_upload_size', __('Max Upload Size', $this->textdomain), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('Maximum upload size in kilobytes.', $this->textdomain),
					'id' 	=> 'max_upload_size',
					'type' 	=> 'text'
				),
				$args
			)
	    );

	    add_settings_field(
	        'own_media_only', __('Show users only their media items', $this->textdomain), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('All the other media items will be hidden.', $this->textdomain),
					'id' 	=> 'own_media_only',
					'type' 	=> 'checkbox'
				),
				$args
			)
	    );

	    add_settings_field(
	        'allowed_media_types', __('Allowed Media Types', $this->textdomain), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('By default WordPress supports a wide range of media formats. You can see the full list <a target="_blank" href="http://codex.wordpress.org/Function_Reference/get_allowed_mime_types#Default_allowed_mime_types">here</a>.', $this->textdomain),
					'id' 	=> 'allowed_media_types',
					'type' 	=> 'multicheckbox',
					'items' => array('image' => __('Image', $this->textdomain), 'video' => __('Video', $this->textdomain), 'text' => __('Text', $this->textdomain), 'audio'=> __('Audio', $this->textdomain), 'office' => __('MS Office', $this->textdomain), 'open_office' => __('OpenOffice', $this->textdomain), 'wordperfect' => __('WordPerfect', $this->textdomain), 'iwork' => 'iWork', 'misc'=>__('Misc', $this->textdomain))
				),
				$args
			)
	    );

	    add_settings_field(
	        'exempt_roles', __('Exempt Roles', $this->textdomain), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('These roles will not be affected by these upload restrictions.', $this->textdomain),
					'id' 	=> 'exempt_roles',
					'type' 	=> 'multicheckbox',
					'items' => wpfepp_get_roles()
				),
				$args
			)
	    );

	    add_settings_field(
	        'force_allow_uploads', __('Give upload capability to Subscribers & Contributors', $this->textdomain), $callback, $tab, $section,
	        array_merge(
				array(
					'desc' 	=> __('By default WordPress does not allow contributors and subscribers to upload media items. Check this if you want to give upload capabilities to these roles.', $this->textdomain),
					'id' 	=> 'force_allow_uploads',
					'type' 	=> 'checkbox'
				),
				$args
			)
	    );
	}

	/**
	 * Outputs the contents of the tab with the help of WordPress' settings API.
	 **/
	public function display(){
	    ?>
	    <form method="post" action="options.php">
		    <?php
				settings_fields( 'wpfepp_media_settings_tab' );
				do_settings_sections( 'wpfepp_media_settings_tab' );
				submit_button( __('Save Settings', $this->textdomain), 'primary', null, true );
			?>
		</form>
		<?php
	}

	public function section_callback($args){
		?>
			<p class="description"><?php _e('Please note that these media settings are global. In other words they will affect your WordPress back-end as well.', $this->textdomain); ?></p>
		<?php
	}
}