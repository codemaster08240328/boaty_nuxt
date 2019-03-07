<?php
/*
Plugin Name: NEX-Forms
Plugin URI: http://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix
Description: Premium WordPress Plugin - Ultimate Drag and Drop WordPress Forms Builder.
Author: Basix
Version: 6.9.1
Author URI: http://codecanyon.net/user/Basix/portfolio?ref=Basix
License: GPL
*/

if ( ! defined( 'ABSPATH' ) ) exit;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	

$cal_id = isset($_REQUEST['calendar_Id']) ? $_REQUEST['calendar_Id'] : '';
if($page=="nex-forms-main" || $page=="nex-forms-dashboard" || $page=="nex-forms-builder")
	{
	add_action( 'wp_print_scripts', 'NEXForms5_deregister_javascript',100);
	add_action( 'wp_print_styles', 'NEXForms5_deregister_stylesheets',100);
	add_action( 'init', 'NEXForms5_deregister_javascript',100);
	add_action( 'init', 'NEXForms5_deregister_stylesheets',100);
	}
if(!$cal_id)
	add_action('widgets_init', 'NEXForms_widget::register_this_widget');
	
function NEXForms5_deregister_javascript(){
	global $wp_scripts; 
	
	$include_script_array = array('utils','common','wp-a11y','sack','quicktags','colorpicker','editor','wp-fullscreen-stub','wp-ajax-response','wp-pointer','autosave','heartbeat','wp-auth-check','wp-lists','prototype','scriptaculous-root','scriptaculous-builder','scriptaculous-dragdrop','scriptaculous-effects','scriptaculous-slider','scriptaculous-sound','scriptaculous-controls','scriptaculous','cropper','jquery','jquery-core','jquery-migrate','jquery-ui-core','jquery-effects-core','jquery-effects-blind','jquery-effects-bounce','jquery-effects-clip','jquery-effects-drop','jquery-effects-explode','jquery-effects-fade','jquery-effects-fold','jquery-effects-highlight','jquery-effects-puff','jquery-effects-pulsate','jquery-effects-scale','jquery-effects-shake','jquery-effects-size','jquery-effects-slide','jquery-effects-transfer','jquery-ui-accordion','jquery-ui-autocomplete','jquery-ui-button','jquery-ui-datepicker','jquery-ui-dialog','jquery-ui-draggable','jquery-ui-droppable','jquery-ui-menu','jquery-ui-mouse','jquery-ui-position','jquery-ui-progressbar','jquery-ui-resizable','jquery-ui-selectable','jquery-ui-selectmenu','jquery-ui-slider','jquery-ui-sortable','jquery-ui-spinner','jquery-ui-tabs','jquery-ui-tooltip','jquery-ui-widget','jquery-form','jquery-color','suggest','schedule','jquery-query','jquery-serialize-object','jquery-hotkeys','jquery-table-hotkeys','jquery-touch-punch','masonry','jquery-masonry','thickbox','jcrop','swfobject','plupload','plupload-all','plupload-html5','plupload-flash','plupload-silverlight','plupload-html4','plupload-handlers','wp-plupload','swfupload','swfupload-swfobject','swfupload-queue','swfupload-speed','swfupload-all','swfupload-handlers','comment-reply','json2','underscore','backbone','wp-util','wp-backbone','revisions','imgareaselect','mediaelement','wp-mediaelement','froogaloop','wp-playlist','zxcvbn-async','password-strength-meter','user-profile','language-chooser','user-suggest','admin-bar','wplink','wpdialogs','word-count','media-upload','hoverIntent','customize-base','customize-loader','customize-preview','customize-models','customize-views','customize-controls','customize-selective-refresh','customize-widgets','customize-preview-widgets','customize-nav-menus','customize-preview-nav-menus','accordion','shortcode','media-models','wp-embed','media-views','media-editor','media-audiovideo','mce-view','admin-tags','admin-comments','xfn','postbox','tags-box','post','press-this','editor-expand','link','comment','admin-gallery','admin-widgets','theme','inline-edit-post','inline-edit-tax','plugin-install','updates','farbtastic','iris','wp-color-picker','dashboard','list-revisions','media-grid','media','image-edit','set-post-thumbnail','nav-menu','custom-header','custom-background','media-gallery','svg-painter','nex-forms-themes-add-on','nex-forms-bootstrap.min','nex-forms-drag-and-drop','nex-forms-preview','nex-forms-field-settings','nex-forms-conditional-logic','nex-forms-wow','nex-forms-load-admin','nex-forms-admin-functions','nex-forms-fields','nex-forms-global-settings','nex-forms-pref','nex-forms-bootstrap.colorpickersliders','nex-forms-tinycolor-min','nex-forms-bootstrap.minislider','nex-forms-moment.min','nex-forms-locales.min','nex-forms-date-time','nex-forms-editor','nex-forms-custom-admin','nex-forms-signature','nex-forms-bootstrap-colorpicker','nex-forms-raty','nex-forms-tutorial','styles-chosen','styles-font-menu', 'nex-forms-charts','nex_forms-materialize.min','formilise-js-init','nex-forms-stats','nex-forms-gcharts','nex-forms-global-settings','nex-forms-pref','ion.rangeSlider.min','nex-forms-builder');
	
	if($wp_scripts)
		{
		foreach($wp_scripts->registered as $wp_script=>$array)
			{
			if(!in_array($array->handle,$include_script_array))
				{
				wp_deregister_script($array->handle);
				wp_dequeue_script($array->handle);
				}
			}	
		}
}

function NEXForms5_deregister_stylesheets(){
	global $wp_styles;
	
	$include_style_array = array('colors','common','forms','admin-menu','dashboard','list-tables','edit','revisions','media','themes','about','nav-menus','widgets','site-icon','l10n','wp-admin','login','install','wp-color-picker','customize-controls','customize-widgets','customize-nav-menus','press-this','ie','buttons','dashicons','open-sans','admin-bar','wp-auth-check','editor-buttons','media-views','wp-pointer','customize-preview','wp-embed-template-ie','imgareaselect','wp-jquery-ui-dialog','mediaelement','wp-mediaelement','thickbox','deprecated-media','farbtastic','jcrop','colors-fresh','nex-forms-jQuery-UI','nex-forms-font-awesome','nex-forms-bootstrap','nex-forms-fields','nex-forms-ui','nex-forms-admin-style','nex-forms-animate','nex-forms-admin-overrides','nex-forms-admin-bootstrap.colorpickersliders','nex-forms-public-admin','nex-forms-editor','nex-forms-custom-admin','nex-forms-jq-ui','nf-styles-chosen','nf-styles-font-menu', 'nf-color-adapt-fresh','nf-color-adapt-light','nf-color-adapt-blue','nf-color-adapt-coffee','nf-color-adapt-ectoplasm','nf-color-adapt-midnight','nf-color-adapt-ocean','nf-color-adapt-sunrise', 'nf-color-adapt-default', 'nex_forms-materialize.min','nex_forms-bootstrap.min','nex_forms-dashboard','nex_forms-font-awesome.min','nex_forms-material-icons','ion.rangeSlider','ion.rangeSlider.skinFlat','nex_forms-builder','google-roboto');

	if($wp_styles)
		{
		foreach($wp_styles->registered as $wp_style=>$array)
			{
			if(!in_array($array->handle,$include_style_array))
				{
				wp_deregister_style($array->handle);
				wp_dequeue_style($array->handle);
				}
			}
		}
}
//if($page=="nex-forms-main")
	require( dirname(__FILE__) . '/includes/load.php');
//echo $page;
//if($page=='nex-forms-dashboard')
	//require('load.php');
class NEXForms5_Config{
	/*************  General  ***************/
	/************  DONT EDIT  **************/
	public $plugin_version;
	/* The displayed name of your plugin */
	public $plugin_name;
	/* The alias of the plugin used by external entities */
	public $plugin_alias;
	/* Enable or disable external modules */
	public $enable_modules;
	/* Plugin Prefix */
	public $plugin_prefix;
	/* Plugin table */
	public $plugin_table, $component_table;
	/* Admin Menu */
	public $plugin_menu;
	/* Add TinyMCE */
	public $add_tinymce;
	
	
	/************* Database ****************/
	/* Sets the primary key for table created above */
	public $plugin_db_primary_key = 'Id';
	/* Database table fields array */
	public $plugin_db_table_fields = array
			(
			'title'								=>	'text',
			'description'						=>	'text',
			'mail_to'							=>  'text',
			'confirmation_mail_body'			=>  'longtext',
			'admin_email_body'					=>  'longtext',
			'confirmation_mail_subject'			=>	'text',
			'user_confirmation_mail_subject'	=>	'text',
			'from_address'						=>  'text',
			'from_name'							=>  'text',
			'on_screen_confirmation_message'	=>  'longtext',
			'confirmation_page'					=>  'text',
			'form_fields'						=>	'longtext',
			'clean_html'						=>	'longtext',
			'visual_settings'					=>	'text',
			'google_analytics_conversion_code'  =>  'text',
			'colour_scheme'  					=>  'text',
			'send_user_mail'					=>  'text',
			'user_email_field'					=>  'text',
			'on_form_submission'				=>  'text',
			'date_sent'							=>  'datetime',
			'is_form'							=>  'text',
			'is_template'						=>  'text',
			'hidden_fields'						=>  'longtext',
			'form_hidden_fields'				=>  'longtext',
			'custom_url'						=>  'text',
			'post_type'							=>  'text',
			'post_action'						=>  'text',
			'bcc'								=>  'text',
			'bcc_user_mail'						=>  'text',
			'custom_css'						=>  'longtext',
			'is_paypal'							=>  'text',
			'total_views'						=>  'text',
			'time_viewed'						=>  'text',
			'email_on_payment_success'			=>  'text',
			'conditional_logic'					=>  'longtext',
			'conditional_logic_array'			=>  'longtext',
			'server_side_logic'					=>  'longtext',
			'form_status'						=>  'text',
			'currency_code'						=>  'text',
			'products'							=>  'longtext',
			'business'							=>  'text',
			'cmd'								=>  'text',
			'return_url'						=>  'text',
			'cancel_url'						=>  'text',
			'lc'								=>  'text',
			'environment'						=>  'text',
			'email_subscription'				=>  'longtext',
			'mc_field_map'						=>  'longtext',
			'mc_list_id'						=>  'text',
			'gr_field_map'						=>  'longtext',
			'gr_list_id'						=>  'text',
			'pdf_html'							=>  'longtext',
			'attach_pdf_to_email'				=>	'text',
			'form_to_post_map'					=>  'longtext',
			'is_form_to_post'					=>  'text'
			);
			


	public $form_entry_table_fields = array
			(
			'nex_forms_Id'			=>	'text',
			'page'					=>	'text',
			'ip'					=>  'text',
			'hostname'				=>	'text',
			'city'					=>	'text',
			'region'				=>	'text',
			'country'				=>	'text',
			'loc'					=>	'text',
			'org'					=>	'text',
			'postal'				=>	'text',
			'user_Id'				=>	'text',
			'viewed'				=>	'text',
			'date_time'				=>  'datetime',
			'paypal_invoice'		=>	'text',
			'payment_status'		=>  'text',
			'form_data'				=>	'longtext',
			'paypal_data'			=>	'longtext',
			);
	
	public $email_table_fields = array
			(
			'nex_forms_Id'						=>	'text',
			'mail_type'							=>  'text',
			'mail_to'							=>  'text',
			'mail_body'							=>  'longtext',
			'mail_subject'						=>	'text',
			'from_address'						=>  'text',
			'from_name'							=>  'text',
			'send_user_mail'					=>  'text',
			'user_email_field'					=>  'text',
			'bcc'								=>  'text',
			'bcc_user_mail'						=>  'text',
			'attachments'						=>  'text',
			);
	
	
	public $stats_table_fields = array
			(
			'nex_forms_Id'			=>	'text',
			'time_viewed'			=>	'text',
			);
	public $form_interactions = array
			(
			'nex_forms_Id'			=>	'text',
			'time_interacted'		=>	'text',
			);
	
	public $file_manager = array
			(
			'nex_forms_Id'			=>	'text',
			'name'					=>	'text',
			'type'					=>	'text',
			'size'					=>	'text',
			'url'					=>	'text',
			'location'				=>	'text',
			'entry_Id'				=>	'text'
			);
	
	/************* Admin Menu **************/
	
	
	public function __construct()
		{ 
		
		$functions = new NEXForms_Functions();
		
		$header_info = $functions->get_file_headers(dirname(__FILE__).DIRECTORY_SEPARATOR.'main.php');
		
		$this->plugin_version 	= $header_info['Version'];
		$this->plugin_name 		= $header_info['Plugin Name'];
		$this->plugin_alias		= $functions->format_name($this->plugin_name);
		$this->plugin_prefix	= 'wap_';
		$this->plugin_table		= $this->plugin_prefix.$this->plugin_alias;
		$this->component_table	= $this->plugin_table;
		$this->add_tinymce		= $header_info['Plugin TinyMCE'];
		}
}

/***************************************/
/*************  Hooks   ****************/
/***************************************/



//add_action('wp_ajax_NEXForms5_tinymce_window', 'NEXForms5_tinymce_window');


/* On plugin activation */
register_activation_hook(__FILE__, 'NEXForms5_run_instalation' );
/* On plugin deactivation */
//register_deactivation_hook(__FILE__, 'NEXForms5_deactivate');
/* Called from page */
add_shortcode( 'NEXForms', 'NEXForms_ui_output' );
/* Build admin menu */
add_action('admin_menu', 'NEXForms5_main_menu');

$other_config = get_option('nex-forms-other-config');

/* Add action button to TinyMCE Editor */
if($other_config['enable-tinymce']=='1')
	add_action('init', 'NEXForms_add_mce_button');

/* Add action button to TinyMCE Editor */
function NEXForms_add_mce_button() {
	add_filter("mce_external_plugins", "NEXForms_tinymce_plugin");
 	add_filter('mce_buttons', 'NEXForms_register_button');
}
/* register button to be called from JS */
function NEXForms_register_button($buttons) {
   array_push($buttons, "separator", "nexforms");
   return $buttons;
}
/* Send request to JS */
function NEXForms_tinymce_plugin($plugin_array) {
   $plugin_array['nexforms'] = plugins_url( '/tinyMCE/plugin.js',__FILE__);
   return $plugin_array;
}
add_action('wp_ajax_NEXForms_tinymce_window', 'NEXForms_tinymce_window');
function NEXForms_tinymce_window(){
	include_once( '/tinyMCE/window.php');
    die();
}

/***************************************/
/*********  Hook functions   ***********/
/***************************************/
/* Convert menu to WP Admin Menu */
function NEXForms5_main_menu(){
	
	$other_config = get_option('nex-forms-other-config');

	/* Add action button to TinyMCE Editor */
	$nf_user_level = 'administrator';
	
	$user_level = isset($other_config['set-wp-user-level']) ? $other_config['set-wp-user-level'] : '';
	
	if($user_level)
		$nf_user_level = $other_config['set-wp-user-level'];
	
	if(!$nf_user_level)
		$nf_user_level = 'administrator';
		
	$nf_user_level = 'administrator';
	
	add_menu_page( 'NEX-Forms', 'NEX-Forms', $nf_user_level, 'nex-forms-dashboard', 'NEXForms_dashboard', plugins_url('/assets/menu_icon.png',__FILE__) );
	//add_submenu_page( 'nex-forms-dashboard', 'NF-Dashboard','Dashboard', $nf_user_level, 'nex-forms-dashboard', 'NEXForms_dashboard');
	add_submenu_page( 'nex-forms-dashboard', 'NF-Builder','NEW Builder', $nf_user_level, 'nex-forms-builder', 'NEXForms_form_builder');
	//add_submenu_page( 'nex-forms-dashboard', 'nex-forms-main','Form Builder', $nf_user_level, 'nex-forms-main', 'NEXForms5_main_page');
	add_submenu_page( 'nex-forms-dashboard', 'NF-Preview','nex-forms-preview', $nf_user_level, 'nex-forms-preview', 'NEXForms_form_preview');
	//add_submenu_page( 'nex-forms-dashboard', 'nex-forms-stats','nex-forms-stats', $nf_user_level, 'nex-forms-stats', 'NEXForms_form_stats');
}





/* Install */
function NEXForms5_run_instalation(){
	$config = new NEXForms5_Config();
	global $wpdb;
	update_option('nex-forms-version',$config->plugin_version);
	
	//PREFERENCES
	//if(!get_option('nex-forms-preferences'))
		//{
		update_option('nex-forms-preferences',
			array(
				'field_preferences'=>
					array(
						'pref_label_align'		=>'top',
						'pref_label_text_align'	=>'align_let',
						'pref_label_size'		=>'',
						'pref_sub_label'		=>'',
					    'pref_input_text_align'	=>'aling_left',
						'pref_input_size'		=>'',
					),
				'validation_preferences'=>
					array(
						'pref_requered_msg'				=>'Required',
						'pref_email_format_msg'			=>'Invalid email address',
						'pref_phone_format_msg'			=>'Invalid phone number',
						'pref_url_format_msg'			=>'Invalid URL',						
					    'pref_numbers_format_msg'		=>'Only numbers are allowed',
						'pref_char_format_msg'			=>'Only text are allowed',
						'pref_invalid_file_ext_msg'		=>'Invalid file extension',
						'pref_max_file_exceded'			=>'Maximum File Size of {x}MB Exceeded',
						'pref_max_file_ul_exceded'		=>'Only a maximum of {x} files can be uploaded',
						'pref_max_file_af_exceded'		=>'Maximum Size for all files can not exceed {x}MB ',
					),
				'email_preferences'=>
					array(
						'pref_email_from_address'	=> get_option('admin_email'),
						'pref_email_from_name'		=> get_option('blogname'),
						'pref_email_recipients'		=> get_option('admin_email'),
						'pref_email_subject'		=> get_option('blogname').' - NEX-Forms Submmision',
						'pref_email_body'			=> '{{nf_form_data}}',
						'pref_user_email_subject'	=> get_option('blogname').' - NEX-Forms Submmision',
						'pref_user_email_body'		=>'Thank you for connecting with us. We will respond to you shortly.',
					),
				'other_preferences'=>
					array(
						'pref_other_on_screen_message' => 'Thank you for connecting with us. We will respond to you shortly.',
					),
				)
			);
		//}
	//EMAIL SETTINGS
	//if(!get_option('nex-forms-email-config'))
		//{
		update_option('nex-forms-email-config',array(
				'email_method'=>'php_mailer', 
				'email_content'=>'html', 
				'smtp_auth'=>'0',
				'smtp_host'=>'smtp.gmail.com',
				'email_smtp_secure'=>'tls',
				'mail_port'=>'587',
				'set_smtp_user'=>'',
				'set_smtp_pass'=>'')
			);
		//}	
	
	//SCRIPT SETTINGS	
	//if(!get_option('nex-forms-script-config'))
		//{
		update_option('nex-forms-script-config',array(
				'inc-jquery'=>'1',
				'inc-jquery-ui-core'=>'1',
				'inc-jquery-ui-autocomplete'=>'1',
				'inc-jquery-ui-slider'=>'1',
				'inc-jquery-form'=>'1',
				'inc-bootstrap'=>'1',
				'inc-onload'=>'1',
				'inc-moment'=>'1',
				'inc-datetime'=>'1',
				'inc-math'=>'1',
				'inc-colorpick'=>'1',
				'inc-wow'=>'1',
				'inc-raty'=>'1',
				'inc-locals'=>'1',
				'inc-sig'=>'0',
				'enable-print-scripts'=>1	
			));
		//}
	
	//STYLE SETTINGS	
	//if(!get_option('nex-forms-style-config'))
		//{
		update_option('nex-forms-style-config',array(
				'incstyle-jquery'=>'1',
				'incstyle-font-awesome'=>'1',
				'incstyle-bootstrap'=>'1',
				'incstyle-animations'=>'1',
				'incstyle-custom'=>'1',
				'enable-print-styles'=>1
			));
		//}
	
	//OTHER SETTINGS
	//if(!get_option('nex-forms-other-config'))
		//{
		update_option('nex-forms-other-config',array(
				'enable-print-scripts'=>'1',
				'enable-print-styles'=>'1',
				'enable-tinymce'=>'1',
				'enable-widget'=>'1',
				'enable-color-adapt'=>'1',
				'set-wp-user-level'=>'administrator'	
			));
		//}
		
	/* Main Table */	
	$instalation = new NF5_Instalation();
	$instalation->component_name 			=  $config->plugin_name;
	$instalation->component_prefix 			=  $config->plugin_prefix;
	$instalation->component_alias			=  'nex_forms';
	$instalation->component_menu 			=  $config->plugin_menu;	
	$instalation->db_table_fields			=  $config->plugin_db_table_fields;
	$instalation->db_table_primary_key		=  $config->plugin_db_primary_key;
	$instalation->run_instalation('full');
	
	/************************************************/
	/************  Additional Tables   **************/
	/************************************************/
	/* Entries Table */
	$instalation = new NF5_Instalation();
	$instalation->component_prefix 		=  $config->plugin_prefix;
	$instalation->component_alias		=  'nex_forms_entries';
	$instalation->db_table_fields		=  $config->form_entry_table_fields;
	$instalation->db_table_primary_key	=  $config->plugin_db_primary_key;
	$instalation->install_component_table();	
	
	/* Email Table */
	$instalation = new NF5_Instalation();
	$instalation->component_prefix 		=  $config->plugin_prefix;
	$instalation->component_alias		=  'nex_forms_email_templates';
	$instalation->db_table_fields		=  $config->email_table_fields;
	$instalation->db_table_primary_key	=  $config->plugin_db_primary_key;
	$instalation->install_component_table();	
	
	/* Stats Table */
	$instalation = new NF5_Instalation();
	$instalation->component_prefix 		=  $config->plugin_prefix;
	$instalation->component_alias		=  'nex_forms_views';
	$instalation->db_table_fields		=  $config->stats_table_fields;
	$instalation->db_table_primary_key	=  $config->plugin_db_primary_key;
	$instalation->install_component_table();	
	
	$instalation = new NF5_Instalation();
	$instalation->component_prefix 		=  $config->plugin_prefix;
	$instalation->component_alias		=  'nex_forms_stats_interactions';
	$instalation->db_table_fields		=  $config->form_interactions;
	$instalation->db_table_primary_key	=  $config->plugin_db_primary_key;
	$instalation->install_component_table();	
	
	$instalation = new NF5_Instalation();
	$instalation->component_prefix 		=  $config->plugin_prefix;
	$instalation->component_alias		=  'nex_forms_files';
	$instalation->db_table_fields		=  $config->file_manager;
	$instalation->db_table_primary_key	=  $config->plugin_db_primary_key;
	$instalation->install_component_table();	
	
	
	$database_actions = new NEXForms_Database_Actions();
	
	$database_actions->alter_plugin_table('wap_nex_forms','hidden_fields','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','clean_html','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','custom_url','text');
	$database_actions->alter_plugin_table('wap_nex_forms','admin_email_body','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','bcc','text');
	$database_actions->alter_plugin_table('wap_nex_forms','bcc_user_mail','text');
	$database_actions->alter_plugin_table('wap_nex_forms','post_type','text');
	$database_actions->alter_plugin_table('wap_nex_forms','post_action','text');	
	$database_actions->alter_plugin_table('wap_nex_forms','custom_css','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','is_paypal','text');
	$database_actions->alter_plugin_table('wap_nex_forms','total_views','text');
	$database_actions->alter_plugin_table('wap_nex_forms','time_viewed','text');
	$database_actions->alter_plugin_table('wap_nex_forms','email_on_payment_success','text');
	$database_actions->alter_plugin_table('wap_nex_forms','conditional_logic','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','conditional_logic_array','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','server_side_logic','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','form_type','text');
	$database_actions->alter_plugin_table('wap_nex_forms','template_type','text');
	$database_actions->alter_plugin_table('wap_nex_forms','user_confirmation_mail_subject','text');
	$database_actions->alter_plugin_table('wap_nex_forms','draft_Id','text');
	$database_actions->alter_plugin_table('wap_nex_forms','pdf_html','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','attach_pdf_to_email','text');
	$database_actions->alter_plugin_table('wap_nex_forms','form_to_post_map','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','is_form_to_post','text');
	$database_actions->alter_plugin_table('wap_nex_forms','form_status','text');
	$database_actions->alter_plugin_table('wap_nex_forms','form_hidden_fields','longtext');
	
	
	$database_actions->alter_plugin_table('wap_nex_forms','currency_code','text');
	$database_actions->alter_plugin_table('wap_nex_forms','products','text');
	$database_actions->alter_plugin_table('wap_nex_forms','business','text');
	$database_actions->alter_plugin_table('wap_nex_forms','cmd','text');
	$database_actions->alter_plugin_table('wap_nex_forms','return_url','text');
	$database_actions->alter_plugin_table('wap_nex_forms','cancel_url','text');
	$database_actions->alter_plugin_table('wap_nex_forms','lc','text');
	$database_actions->alter_plugin_table('wap_nex_forms','environment','text');
	$database_actions->alter_plugin_table('wap_nex_forms','email_subscription','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','mc_field_map','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','mc_list_id','text');
	$database_actions->alter_plugin_table('wap_nex_forms','gr_field_map','longtext');
	$database_actions->alter_plugin_table('wap_nex_forms','gr_list_id','text');
	
	$database_actions->alter_plugin_table('wap_nex_forms_entries','hostname','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','city','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','region','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','country','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','loc','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','org','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','postal','text');
	
	$database_actions->alter_plugin_table('wap_nex_forms_files','location','text');
	$database_actions->alter_plugin_table('wap_nex_forms_files','entry_Id','text');
	
	
	//MIGRATE PAYPAL DATA
	if(get_option('convert_paypal')!='1')
		{
		$get_paypal = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_paypal');

		foreach($get_paypal as $paypal_data)
			{
			$data_array = array(
				'currency_code'						=>  $paypal_data->currency_code,
				'products'							=>  $paypal_data->products,
				'business'							=>  $paypal_data->business,
				'cmd'								=>  $paypal_data->cmd,
				'return_url'						=>  $paypal_data->return_url,
				'cancel_url'						=>  $paypal_data->cancel_url,
				'lc'								=>  $paypal_data->lc,
				'environment'						=>  $paypal_data->environment
				);
			$update = $wpdb->update ( $wpdb->prefix . 'wap_nex_forms', $data_array, array(	'Id' => $paypal_data->nex_forms_Id) );
			}
		update_option('convert_paypal','1');
		}
	
	$database_actions->alter_plugin_table('wap_nex_forms_entries','paypal_invoice','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','payment_status','text');
	$database_actions->alter_plugin_table('wap_nex_forms_entries','paypal_data','longtext');
	
	/*$template_1  = 'INSERT INTO '.$wpdb->prefix.'wap_nex_forms';
	$template_1 .= file_get_contents('/includes/templates/contact_us.txt', true);
	$wpdb->query($template_1);
	
	$template_2  = 'INSERT INTO '.$wpdb->prefix.'wap_nex_forms';
	$template_2 .= file_get_contents('/includes/templates/conditional_logic.txt', true);
	$wpdb->query($template_2);*/
}

/***************************************/
/************  ADMIN PAGES  ************/
/***************************************/
//
function NEXForms5_main_page(){

	//echo '<link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald">';
	wp_enqueue_style('nf-color-adapt-'.get_user_option( 'admin_color' ),  plugins_url( '/includes/css/color_adapt/'.get_user_option( 'admin_color' ).'.css',__FILE__));
	
	$builder = new NF5_Form_Builder();
	$builder->form_builder_page();
	
	
	$custom_layout = get_option('nex-forms-custom-layouts');
	
	/*echo '<pre>';
		print_r($custom_layout);
	echo '</pre>';*/
	
	/*global $wpdb;
	
	$sql  = 'INSERT INTO '.$wpdb->prefix.'wap_nex_forms';
	$sql .= file_get_contents('/includes/templates/conditional_logic.txt', true);
	
	
	
	$insert = $wpdb->query($sql);
	
	echo $insert; */
	
	/*global $wp_styles;
	
	foreach($wp_styles->registered as $wp_style=>$array)
		{
		echo '\''.$wp_style.'\',';
		
		}	
	*/
	
	/*$output .= '<div class="admin_panel">';
	
		$output .= '<div class="top_panel">Test</div>';
	
	$output .= '</div>';*/
	
	//echo $output;
	
	global $wp_styles;
	$include_style_array = array('colors','common','forms','admin-menu','dashboard','list-tables','edit','revisions','media','themes','about','nav-menus','widgets','site-icon','l10n','wp-admin','login','install','wp-color-picker','customize-controls','customize-widgets','customize-nav-menus','press-this','ie','buttons','dashicons','open-sans','admin-bar','wp-auth-check','editor-buttons','media-views','wp-pointer','customize-preview','wp-embed-template-ie','imgareaselect','wp-jquery-ui-dialog','mediaelement','wp-mediaelement','thickbox','deprecated-media','farbtastic','jcrop','colors-fresh','nex-forms-jQuery-UI','nex-forms-font-awesome','nex-forms-bootstrap','nex-forms-fields','nex-forms-ui','nex-forms-admin-style','nex-forms-animate','nex-forms-admin-overrides','nex-forms-admin-bootstrap.colorpickersliders','nex-forms-public-admin','nex-forms-editor','nex-forms-custom-admin','nex-forms-jq-ui','nf-styles-chosen','nf-styles-font-menu', 'nf-color-adapt-fresh','nf-color-adapt-light','nf-color-adapt-blue','nf-color-adapt-coffee','nf-color-adapt-ectoplasm','nf-color-adapt-midnight','nf-color-adapt-ocean','nf-color-adapt-sunrise', 'nf-color-adapt-default','nex_forms-materialize.min','nex_forms-bootstrap.min','nex_forms-dashboard','nex_forms-font-awesome.min','nex_forms-material-icons','ion.rangeSlider','ion.rangeSlider.skinFlat','nex_forms-builder','google-roboto');

	echo '<div class="unwanted_css_array" style="display:none;">';
	foreach($wp_styles->registered as $wp_style=>$array)
		{
		if(!in_array($array->handle,$include_style_array))
			{
			echo '<div class="unwanted_css">'.$array->handle.'-css</div>';
			}
		}	
	echo '</div>';
	
	//$preferences = get_option('nex-forms-preferences'); 							
	//echo $preferences->field_preferences;
	
	/*echo '<pre>';
	print_r($preferences);
	echo '</pre>';*/
}

function NEXForms_form_preview(){

	wp_enqueue_style('nex-forms-preview',plugins_url( '/css/preview.css',__FILE__));

	echo '<style type="text/css">
	div.updated, .update-nag , div.error{display:none; !important}
		#wpcontent {
			margin-left: 0 !important;
			padding: 20px !important;
		}
		#wpadminbar, #adminmenumain, #wpfooter {
			display: none;
		}
		#wpbody {
			padding-top: 0 !important;
		}
		html{
			background:#fff !important;
			padding-top:0 !important;
			padding-left:15px !important;
		}
		#wpwrap{
			background:#fff !important;
		}
		.wp-admin select {
			height: auto !important;
		}
		.wp-admin .nex-forms-container select {
			height: 34px !important;
		}
		.wp-admin .nex-forms-container .multi-select select {
			height: auto !important;
		}
		#wpfooter{
			display:none;
		}
		.row.outer_container {
			margin: 0 !important;
		}
		#wpadminbar {
			z-index: 999999999999 !important;
		}
		#nex-forms .star-rating {
			white-space: unset;
		}
		.sticky-menu #adminmenuwrap{
			z-index:100000000 !important;
		}
		#adminmenuback{
			z-index:2000 !important;
		}
	</style>';
	echo NEXForms_ui_output(filter_var($_REQUEST['form_Id'],FILTER_SANITIZE_NUMBER_INT),'','');
}

function NEXForms_ui_output( $atts , $echo='',$prefill_array=''){
	
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	global $wpdb;
	$config 	= new NEXForms5_Config();
	
	//echo $_SERVER['REMOTE_ADDR'];
	
	/*$get_entries = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_entries','');
	$form_entries = $wpdb->get_results($get_entries);
	
	foreach($form_entries as $form_entry)
		{
		$update = $wpdb->update ( $wpdb->prefix . 'wap_nex_forms_entries', array('ip'=>long2ip(mt_rand())), array(	'Id' => $form_entry->Id) );	
		}*/
	//echo '<pre>';
	//foreach($random_ips as $ip)
		//$geo_data = json_decode(NEXForms_Functions::get_geo_location(long2ip(mt_rand())));
	
		//echo $geo_data->country; 
	
	//echo '</pre>';


	/*echo '<pre>';
		print_r( $_REQUEST );
	echo '</pre>';*/

	if(is_array($atts))
		{
		$defaults = array(
			'id' => '0',
			'open_trigger' => '',
			'auto_popup_delay' => '',
			'auto_popup_scroll_top' => '',
			'exit_intent' => '',
			'type' => 'button',
			'text' => 'open',
			'make_sticky' => 'no',
			'paddel_text' => 'Contact Us',
			'paddel_color'=>'btn-primary',
			'button_color'=>'btn-primary',
			'position' => 'right'
			);
		extract( shortcode_atts( $defaults, $atts ) );
		wp_parse_args($atts, $defaults);
		}
	else
		{
		$id=$atts;
		$open_trigger = '';
		$auto_popup_delay = '';
		$auto_popup_scroll_top = '';
		$exit_intent = '';
		$type = 'button';
		$text = 'open';
		$make_sticky = 'no';
		$paddel_text = 'Contact Us';
		$paddel_color = 'btn-primary';
		$button_color = 'btn-primary';
		$position = 'right';
		}
		$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id = %d',filter_var($id,FILTER_SANITIZE_NUMBER_INT));
		$form_attr = $wpdb->get_row($get_form);
	
		
		
		
	$unigue_form_Id = rand(0,99999);
	
	$nf_functions = new NEXForms_Functions();
	
	$output = '';
	
	if($nf_functions->isJson($form_attr->conditional_logic_array) && !empty($form_attr->conditional_logic_array))
		{
		$output .= $nf_functions->run_conditional_logic(json_decode($form_attr->conditional_logic_array), $unigue_form_Id);	
		}
	else
		{
		$output .= $nf_functions->run_old_conditional_logic($form_attr->conditional_logic, $unigue_form_Id);
		}
	
	
		
		$output .= '<div class="pre_fill_fields">';
		if(is_array($prefill_array))
			{
			foreach($prefill_array as $key => $val)
				{			
				$output .= '<input type="hidden" name="'.$key.'" value="'.filter_var($val,FILTER_SANITIZE_STRING).'">';	
				}	
			}
		foreach($_REQUEST as $key => $val)
				{			
				$output .= '<input type="hidden" name="'.$key.'" value="'.filter_var($val,FILTER_SANITIZE_STRING).'">';	
				}
		$output .= '</div>';
		
		if($make_sticky=='yes')
			{
			$output .= '<div id="nex-forms"><div class="nf-sticky-form paddel-'.$position.'"><div class="nf-sticky-paddel btn '.$paddel_color.'">'.$paddel_text.'</div><div class="nf-sticky-container">';	
			}
		
		if($open_trigger=="popup")
			{
			if($exit_intent==1)
				{
					$output .= '<script type="text/javascript">
						// Exit intent
						function addEvent(obj, evt, fn) {
							if (obj.addEventListener) {
								obj.addEventListener(evt, fn, false);
							}
							else if (obj.attachEvent) {
								obj.attachEvent("on" + evt, fn);
							}
						}
						
						// Exit intent trigger
						addEvent(document, \'mouseout\', function(evt) {
						
							if (evt.toElement == null && evt.relatedTarget == null ) {
							   jQuery(\'#nexForms_popup_'.$atts['id'].'\').modal({show:true});
							};
						
						});
						
					</script>';
				}	
			if($auto_popup_scroll_top!='')
				{
					$output .= '<script type="text/javascript">
						var scroll_popup = 0;
						jQuery(document).on(\'scroll\',
							function()
								{
								console.log(jQuery(window).scrollTop());
							
								if(jQuery(window).scrollTop()>'.$auto_popup_scroll_top.' && scroll_popup==0)
									{
									jQuery(\'#nexForms_popup_'.$atts['id'].'\').modal({show:true});
									scroll_popup = 1;
									}
								}
							)
							
					</script>';
				}
				
			else if($auto_popup_delay!='')
				{
					$output .= '<script type="text/javascript">
						 setTimeout( function()
								{
								jQuery(\'#nexForms_popup_'.$atts['id'].'\').modal({show:true});
								},'.($auto_popup_delay*1000).'
							);
					</script>';
				}
			else
				{
				if($type == 'button')
					$output .= '<div id="nex-forms"><button class="btn '.$button_color.' open_nex_forms_popup" data-popup-id="'.$atts['id'].'">'.$text.'</button></div>';
				else
					$output .= '<a href="#" class="open_nex_forms_popup" data-popup-id="'.$atts['id'].'">'.$text.'</a>';
				}
			$output .= '<div class="modal fade nex_forms_modal" id="nexForms_popup_'.$atts['id'].'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						  <div class="modal-dialog">
							<div class="modal-content">
							  <div class="modal-header ">
							  	<button aria-hidden="true" data-dismiss="modal" class="close" type="button" style="display: inline-block;">×</button>
								<h4 class="modal-title" id="myModalLabel">'.$form_attr->title.'</h4>
							  </div>
							  <div class="modal-body">';	
			}
		
		$output .= '<div id="the_plugin_url" style="display:none;">'.plugins_url('',__FILE__).'</div>';
		
		$output .= '<div id="nex-forms" class="nex-forms">';
			$output .= '<div id="confirmation_page" class="confirmation_page" style="display:none;">'.$form_attr->confirmation_page.'</div>';
			$output .= '<div id="on_form_submmision" class="on_form_submmision" style="display:none;">'.$form_attr->on_form_submission.'</div>';
			$output .= '<div class="ui-nex-forms-container" id="ui-nex-forms-container"  >';
			$output .= '<div class="current_step" style="display:none;">1</div>';
			
			
			
			if($make_sticky=='yes')
				{
				$output .= '<div style="padding:15px; display:none;" class="nex_success_message"><div class="panel-body alert alert-success" >'.str_replace('\\','',$form_attr->on_screen_confirmation_message).'</div></div>';
				}
			else
				{
				$output .= '<div class="panel-body alert alert-success nex_success_message" style="display:none;">'.str_replace('\\','',$form_attr->on_screen_confirmation_message).'</div>';
				}
			
			
			$post_action = ($form_attr->post_action=='ajax' || !$form_attr->post_action) ? admin_url('admin-ajax.php') : $form_attr->custom_url;
			
			$set_ajax = ($form_attr->post_action=='ajax' || !$form_attr->post_action) ? 'submit-nex-form' : 'send-nex-form';
			$post_method = 'post';
			
			if($form_attr->post_action!='ajax')
				$post_method = ($form_attr->post_type=='POST' || !$form_attr->post_type) ? 'post' : 'get';
				$output .= '<div class="hidden" id="nf_ajax_url">'.admin_url('admin-ajax.php').'</div>';
				$output .= 	'<form id="nf_form_'.$unigue_form_Id.'" class="'.$set_ajax.'" name="nex_form" action="'.$post_action.'" method="'.$post_method.'" enctype="multipart/form-data">';
					$output .= '<input type="hidden" name="nex_forms_Id" value="'.$id.'">';
					$output .= '<input type="hidden" name="page" value="'.$_SERVER['REQUEST_URI'].'">';
					$output .= '<input type="hidden" name="ip" value="'.$_SERVER['REMOTE_ADDR'].'">';
					if(is_plugin_active( 'nex-forms-paypal-add-on7/main.php' ))
						$output .= '<input type="hidden" name="paypal_invoice" value="'.rand(0,9999999).'">';

					
					
					$database_actions = new NEXForms_Database_Actions();
					$nf_functions = new NEXForms_Functions();
					
					$_SERVER['DATE_TIME'] = date('Y-m-d H:i:s');
					$_SERVER['FORM_TITLE'] = $form_attr->title;
					$_SERVER['C_PAGE'] = $_SERVER['REQUEST_URI'];
					$_SERVER['WP_USER_IP'] = $_SERVER['REMOTE_ADDR'];
					$_SERVER['WP_USER'] = $database_actions->get_username(get_current_user_id());
					$_SERVER['WP_USER_EMAIL'] = $database_actions->get_useremail(get_current_user_id());
					$_SERVER['WP_USER_URL'] = $database_actions->get_userurl(get_current_user_id());
					
					
					
					
					
					
					if($nf_functions->isJson($form_attr->hidden_fields) && !empty($form_attr->hidden_fields))
						{
						$hidden_fields_array = json_decode($form_attr->hidden_fields);
						if(!empty($hidden_fields_array))
							{
							foreach($hidden_fields_array as $hidden_field)
								{
								$hidden_field_val = $hidden_field->field_value;
								$pattern = '({{+([A-Za-z 0-9_])+}})';
								preg_match_all($pattern, $hidden_field_val, $matches);
								foreach($matches[0] as $match)
									{
									$hidden_field_val = str_replace($match,$_SERVER[str_replace('{','',str_replace('}','',$match))],$hidden_field_val);
									}
								
								if($hidden_field->field_name)
									$output .= '<input type="hidden" name="'.$hidden_field->field_name.'" value="'.$hidden_field_val.'">';
								}	
							}
						}
					else
						{
						$hidden_fields_raw = explode('[end]',$form_attr->hidden_fields);
						foreach($hidden_fields_raw as $hidden_field)
							{
							$hidden_field = explode('[split]',$hidden_field);
							$hidden_field_val = (isset($hidden_field[1])) ? $hidden_field[1] : '';
							$pattern = '({{+([A-Za-z 0-9_])+}})';
							preg_match_all($pattern, $hidden_field_val, $matches);
							foreach($matches[0] as $match)
								{
								$hidden_field_val = str_replace($match,$_SERVER[str_replace('{','',str_replace('}','',$match))],$hidden_field_val);
								}
							
							if($hidden_field[0])
								$output .= '<input type="hidden" name="'.$hidden_field[0].'" value="'.$hidden_field_val.'">';
							}
						}
						
						
										
					$output .= '<input type="text" name="company_url" value="" placeholder="enter company url" class="form-control req">';			
					$output .=  ($form_attr->clean_html) ? str_replace('\\','',$form_attr->clean_html) : str_replace('\\','',$form_attr->form_fields);
					$output .= '<div style="clear:both;"></div>';
				$output .= 	'</form>';
			$output .= '</div>';
		$output .= '</div>';
		
		
		
	if($open_trigger=="popup")
			{	
	$output .= '</div>
			</div>
		  </div>
		</div>';
			}
	
	$output .= '<style type="text/css" class="nex-forms-custom-css">'.$form_attr->custom_css.'</style>';
	
	if($make_sticky=='yes')	
		$output .= '</div></div></div>';
		
		
	
	
/* SCRIPTS AND STYLE INCLUSIONS */		
	
	$script_config = get_option('nex-forms-script-config');
	$styles_config = get_option('nex-forms-style-config');
	$other_config = get_option('nex-forms-other-config');
	function isRestUrl() {
		$bIsRest = false;
		if ( function_exists( 'rest_url' ) && !empty( $_SERVER[ 'REQUEST_URI' ] ) ) {
				$sRestUrlBase = get_rest_url( get_current_blog_id(), '/' );
				$sRestPath = trim( parse_url( $sRestUrlBase, PHP_URL_PATH ), '/' );
				$sRequestPath = trim( $_SERVER[ 'REQUEST_URI' ], '/' );
				$bIsRest = ( strpos( $sRequestPath, $sRestPath ) === 0 );
		}
		return $bIsRest;
}

if (!isRestUrl()) {	
	if($script_config['inc-jquery']=='1' || !$script_config['inc-jquery'])
		wp_enqueue_script('jquery');
	if($script_config['inc-jquery-ui-core']=='1' || !$script_config['inc-jquery-ui-core'])
		wp_enqueue_script('jquery-ui-core');
	if($script_config['inc-jquery-ui-autocomplete']=='1' || !$script_config['inc-jquery-ui-autocomplete'])
		wp_enqueue_script('jquery-ui-autocomplete');
	if($script_config['inc-jquery-ui-slider']=='1' || !$script_config['inc-jquery-ui-slider'])
		wp_enqueue_script('jquery-ui-slider');
	if($script_config['inc-jquery-form']=='1' || !$script_config['inc-jquery-form'])
		wp_enqueue_script('jquery-form');
	if($script_config['inc-bootstrap']=='1' || !$script_config['inc-bootstrap'])
		wp_enqueue_script('nex-forms-bootstrap.min',  plugins_url( '/js/bootstrap.min.js',__FILE__));
	//if($script_config['inc-math']=='1' || !$script_config['inc-math'])	
		wp_enqueue_script('nex-forms-math.min',  plugins_url( '/js/math.min.js',__FILE__));	
	if($script_config['inc-sig']=='1')	
		wp_enqueue_script('nex-forms-signature',  plugins_url( '/js/nf-signature.js',__FILE__));
	if($script_config['inc-colorpick']=='1'  || !$script_config['inc-colorpick'])	
		wp_enqueue_script('nex-forms-color-picker',  plugins_url( '/js/bootstrap-colorpicker.min.js',__FILE__));	
	if($script_config['inc-wow']=='1' || !$script_config['inc-wow'])
		{
		wp_enqueue_script('nex-forms-wow',  plugins_url( '/js/wow.min.js',__FILE__));
		echo '<script type="text/javascript">var get_wow = \'enabled\';</script>';
		}
	else
		echo '<script type="text/javascript">var get_wow = \'disabled\';</script>';
		
	if($script_config['inc-raty']=='1' || !$script_config['inc-raty'])
		{
		wp_enqueue_script('nex-forms-raty-fa',  plugins_url( '/js/jquery.raty-fa.js',__FILE__));
		echo '<script type="text/javascript">var get_raty = \'enabled\';</script>';
		}
	else
		echo '<script type="text/javascript">var get_raty = \'disabled\';</script>';
		
	if($script_config['inc-onload']=='1' || !$script_config['inc-onload'])
		wp_enqueue_script('nex-forms-onload', plugins_url( '/js/nexf-onload-ui.js',__FILE__));
	if($script_config['inc-moment']=='1' || !$script_config['inc-moment'])
		wp_enqueue_script('nex-forms-moment.min', plugins_url( '/js/moment.min.js',__FILE__));
	if($script_config['inc-locals']=='1' || !$script_config['inc-locals'])
		wp_enqueue_script('nex-forms-locales.min', plugins_url( '/js/locales.js',__FILE__));	
	if($script_config['inc-datetime']=='1' || !$script_config['inc-datetime'])
		wp_enqueue_script('nex-forms-bootstrap-datetimepicker', plugins_url( '/js/bootstrap-datetimepicker.js',__FILE__));
	
	
	//wp_enqueue_script('nex-forms-materialize', plugins_url( '/assets/materialize.js',__FILE__));	
	
	wp_enqueue_script('ion.rangeSlider.min', plugins_url( '/assets/ion.rangeSlider.min.js',__FILE__));	
	
	
	//if($styles_config['incstyle-jquery']=='1' || !$script_config['incstyle-jquery'])
		wp_enqueue_style('jquery-ui');	
	//if($styles_config['incstyle-jquery']=='1' || !$script_config['incstyle-jquery'])
		wp_enqueue_style('nex-forms-jQuery-UI',plugins_url( '/css/jquery-ui.min.css',__FILE__));
	//if($styles_config['incstyle-font-awesome']=='1' || !$script_config['incstyle-font-awesome'])
		wp_enqueue_style('nex-forms-font-awesome',plugins_url( '/css/font-awesome.min.css',__FILE__));
	//if($styles_config['incstyle-bootstrap']=='1' || !$script_config['incstyle-bootstrap'])
		wp_enqueue_style('nex-forms-bootstrap-ui', plugins_url( '/css/ui-bootstrap.css',__FILE__));
	//if($styles_config['incstyle-custom']=='1' || !$script_config['incstyle-custom'])
		wp_enqueue_style('nex-forms-ui', plugins_url( '/css/ui.css',__FILE__));
	//if($styles_config['incstyle-custom']=='1' || !$script_config['incstyle-custom'])
		wp_enqueue_style('nex-forms-fields', plugins_url( '/css/fields.css',__FILE__));
	//if($styles_config['incstyle-animations']=='1' || !$script_config['incstyle-animations'])
		wp_enqueue_style('nex-forms-animations', plugins_url( '/css/animate.css',__FILE__));
	
	wp_enqueue_style('nex-forms-materialize', plugins_url( '/assets/materialize-ui.css',__FILE__));	
	wp_enqueue_style('nex_forms-material-icons','https://fonts.googleapis.com/icon?family=Material+Icons');
	
	
	wp_enqueue_style('ion.rangeSlider.skinFlat', plugins_url( '/assets/ion.rangeSlider.skinFlat.css',__FILE__));	
	wp_enqueue_style('ion.rangeSlider', plugins_url( '/assets/ion.rangeSlider.css',__FILE__));	
	
	wp_enqueue_style('google-roboto', 'https://fonts.googleapis.com/css?family=Roboto');	
	
	if($script_config['enable-print-scripts']=='1')
		wp_print_scripts();
	//if($styles_config['enable-print-styles']=='1')
		wp_print_styles();

	}
	if($echo)
		echo $output;
	else
		return $output;	
}



add_action( 'wp_ajax_submit_nex_form', 'submit_nex_form');
add_action( 'wp_ajax_nopriv_submit_nex_form', 'submit_nex_form');

add_action( 'wp_ajax_nf_add_form_view', 'NEXForms_add_view');
add_action( 'wp_ajax_nopriv_nf_add_form_view', 'NEXForms_add_view');
function NEXForms_add_view(){

	global $wpdb;
	$id = sanitize_text_field($_POST['nex_forms_id']);
	$add_view = $wpdb->insert($wpdb->prefix.'wap_nex_forms_views',
		array(								
			'time_viewed'				=> mktime(),
			'nex_forms_Id'				=> filter_var($id,FILTER_SANITIZE_NUMBER_INT)
			)
		 );
	die();
}


add_action( 'wp_ajax_nf_add_form_interaction', 'NEXForms_add_form_interaction');
add_action( 'wp_ajax_nopriv_nf_add_form_interaction', 'NEXForms_add_form_interaction');
function NEXForms_add_form_interaction(){
	
	global $wpdb;
	$id = sanitize_text_field($_POST['nex_forms_id']);
	$add_interaction = $wpdb->insert($wpdb->prefix.'wap_nex_forms_stats_interactions',
		array(								
			'time_interacted'		=> mktime(),
			'nex_forms_Id'			=> filter_var($id,FILTER_SANITIZE_NUMBER_INT)
			)
		 );
		if($add_interaction)
			echo 'added';
	die();
}

function submit_nex_form(){
	
	global $wpdb;
	//ANTI SPAM
	
	$nf_functions = new NEXForms_Functions();
	$nf7_functions = new NEXForms_Functions();
	$database_actions = new NEXForms_Database_Actions();
	if($_POST['company_url']!='')
		die();
	
/*******************************************************************************************************/
/************************************* SETUP ATTACHMENTS ***********************************************/
/*******************************************************************************************************/
	if ( ! function_exists( 'wp_handle_upload' ) ) 
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	
	if(!function_exists('wp_get_current_user')) {
		include(ABSPATH . "wp-includes/pluggable.php"); 
	}
	$time = md5(time());
	$boundary = "==Multipart_Boundary_x{$time}x";
	
	$insert_file_array = array();
	
	foreach($_FILES as $key=>$file)
		{
		$multi_file_array = array();
		if(is_array($_FILES[$key]['name']))
			{
			
				foreach($_FILES[$key]['name'] as $mkey => $mval)
					{
					$multi_file_array[$key.'_'.$mkey] = array(
						'name'=>$_FILES[$key]['name'][$mkey],
						'type'=>$_FILES[$key]['type'][$mkey],
						'tmp_name'=>$_FILES[$key]['tmp_name'][$mkey],
						'error'=>$_FILES[$key]['error'][$mkey],
						'size'=>$_FILES[$key]['size'][$mkey]
						);
					}
					$file_names = '';
					foreach($multi_file_array as $ukey=>$ufile)
						{
						$uploadedfile = $multi_file_array[$ukey];
						$upload_overrides = array( 'test_form' => false );
						$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
						
						if ( $movefile )
							{
							if($movefile['file'])
								{
								$insert_file_array[$uploadedfile['name']] = array(
									'name' 		=> $uploadedfile['name'],
									'type' 		=> $uploadedfile['type'],
									'size' 		=> $uploadedfile['size'],
									'location' 	=> $movefile['file'],
									'url' 		=> $movefile['url'],
									);		
								$set_file_name = str_replace(ABSPATH,'',$movefile['file']);
								$file_names .= get_option('siteurl').'/'.$set_file_name. ',';
								$files[] = $movefile['file'];
								$filenames[] = get_option('siteurl').'/'.$set_file_name;
								}
							}
						else
							{
							echo "Possible file upload attack!\n".$movefile['error'];
							}
						}
						
			$_POST[$key] = $file_names;
			
			}
		else
			{
			$uploadedfile = $_FILES[$key];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			
			if ( $movefile )
				{
				if($movefile['file'])
					{
					$insert_file_array[$uploadedfile['name']] = array(
						'name' 		=> $uploadedfile['name'],
						'type' 		=> $uploadedfile['type'],
						'size' 		=> $uploadedfile['size'],
						'location' 	=> $movefile['file'],
						'url' 		=> $movefile['url'],
						);	
					$set_file_name = str_replace(ABSPATH,'',$movefile['file']);
					$set_file_name = str_replace(ABSPATH,'',$movefile['file']);
					$_POST[$key] = get_option('siteurl').'/'.$set_file_name;
					$files[] = $movefile['file'];
					$filenames[] = get_option('siteurl').'/'.$set_file_name;
					}
				}
			else
				{
				echo "Possible file upload attack!\n".$movefile['error'];
				}
			}
		}
		
		
/*******************************************************************************************************/
/*********************************** SETUP FORM POST DATA **********************************************/
/**************************** for email body and database insert ***************************************/
/*******************************************************************************************************/

		$user_fields 	= '<table width="100%" cellpadding="3" cellspacing="0" style="border:1px solid #ddd;">';
		$data_array 	= array();
		$i				= 1;
		
		/*echo '<pre>';
		print_r($_POST);
		echo '</pre>';*/
		
		foreach($_POST as $key=>$val)
			{
			if(
			$key!='paypal_invoice' &&
			$key!='math_result' &&
			$key!='set_file_ext' &&
			$key!='format_date' &&
			$key!='action' &&
			$key!='set_radio_items' &&
			$key!='change_button_layout' &&
			$key!='set_check_items' &&
			$key!='set_autocomplete_items' &&
			$key!='required' &&
			$key!='xform_submit' &&
			$key!='current_page' &&
			$key!='ajaxurl' &&
			$key!='page_id' &&
			$key!='page' &&
			$key!='ip' &&
			$key!='nex_forms_Id' &&
			$key!='company_url' &&
			$key!='submit' &&
 			!strstr($key,'real_val')
			)
				{
				$admin_val = '';
				if($val!='NaN')
					{ 
					if(is_array($val))
						{
						foreach($val as $thekey=>$value)
							{
							$admin_val .='- '. $value.' ';
							}
						
						$user_fields .= '<tr>
												<td width="15%" valign="top" style="border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#f9f9f9;"><strong>'.$nf_functions->unformat_name($key).'</strong></td>
												<td width="85%" style="border-bottom:1px solid #ddd;" valign="top">'.$admin_val.'</td>
											<tr>
											';
						
						}
					else
						{
						$val =$val;
						$admin_val = $val;
						
						if($admin_val)
							{
							
							if(array_key_exists('real_val__'.$key,$_POST))
								{
								$admin_val = $_POST['real_val__'.$key];	
								$val = $_POST['real_val__'.$key];
								}
							
							if(strstr($admin_val,'data:image'))
								$admin_val = '<img src="'.$admin_val.'" />';
							
							$user_fields .= '<tr>
												<td width="30%" valign="top" style="border-bottom:1px solid #ddd;border-right:1px solid #ddd; background-color:#f9f9f9;"><strong>'.$nf_functions->unformat_name($key).'</strong></td>
												<td width="70%" style="border-bottom:1px solid #ddd;" valign="top">'.$admin_val.'</td>
											<tr>
											';
							$pt_user_fields .= ''.$nf_functions->unformat_name($key).':'.$admin_val.'
	';
							}	
						
						}
					}
				$data_array[] = array('field_name'=>$key,'field_value'=>$val);
				$i++;
				}
			}		
		$user_fields .= '</table>';


/*******************************************************************************************************/
/************************************* INSERT POST DATA ************************************************/
/*******************************************************************************************************/
	/*$data_entry = $wpdb->prepare($wpdb->insert($wpdb->prefix.'wap_nex_forms_entries',
		array(								
			'nex_forms_Id'			=>	filter_var($_REQUEST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT),
			'page'					=>	filter_var($_POST['page'],FILTER_SANITIZE_URL),
			'ip'					=>  filter_var($_POST['ip'],FILTER_SANITIZE_NUMBER_FLOAT),
			'paypal_invoice'		=>  filter_var($_POST['paypal_invoice'],FILTER_SANITIZE_NUMBER_INT),
			'user_Id'				=>	get_current_user_id(),
			'viewed'				=>	'no',
			'date_time'				=>  date('Y-m-d H:i:s'),
			'form_data'				=>	json_encode($data_array)
			)
		 )
	 );
	
	$insert = $wpdb->query($data_entry);
	$entry_id = $wpdb->insert_id;*/
	
	$geo_data = json_decode($nf7_functions->get_geo_location($_SERVER['REMOTE_ADDR']));
	
	$insert = $wpdb->insert($wpdb->prefix.'wap_nex_forms_entries',
		array(								
			'nex_forms_Id'			=>	filter_var($_REQUEST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT),
			'page'					=>	filter_var($_POST['page'],FILTER_SANITIZE_URL),
			'ip'					=>  filter_var($_POST['ip'],FILTER_SANITIZE_STRING),
			'paypal_invoice'		=>  filter_var($_POST['paypal_invoice'],FILTER_SANITIZE_NUMBER_INT),
			'user_Id'				=>	get_current_user_id(),
			'hostname'				=>	$geo_data->hostname,
			'city'					=>	$geo_data->city,
			'region'				=>	$geo_data->region,
			'country'				=>	$geo_data->country,
			'loc'					=>	$geo_data->loc,
			'org'					=>	$geo_data->org,
			'postal'				=>	$geo_data->postal,
			'date_time'				=>  date('Y-m-d H:i:s'),
			'form_data'				=>	json_encode($data_array)
			)
	 );
	$entry_id = $wpdb->insert_id;
	
	$setup_file_insert_array = array();
	
	foreach($insert_file_array as $key=>$val)
		{
		$setup_file_insert_array[$key] = array(
			'name' 		=> $val['name'],
			'type' 		=> $val['type'],
			'size' 		=> $val['size'],
			'location' 	=> $val['location'],
			'url' 		=> $val['url'],
			'nex_forms_Id'=>filter_var($_REQUEST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT),
			'entry_Id' => $entry_id
		);
	}
	foreach($setup_file_insert_array as $insert_file)
		{
		$wpdb->insert($wpdb->prefix.'wap_nex_forms_files',$insert_file);
		}
		
	$api_params = array( 'check_key' => 1,'ins_data'=>get_option('7103891'));
	$response = wp_remote_post( 'http://basixonline.net/activate-license', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
	$checked = $response['body'];
/*******************************************************************************************************/
/***************************************** SETUP EMAILS ************************************************/
/*******************************************************************************************************/
	$nex_forms_id = isset($_REQUEST['nex_forms_Id']) ? filter_var($_POST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT) : '';
	
	$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id = %d',$nex_forms_id);
	$form_attr = $wpdb->get_row($get_form);
	
	$from_address 						= ($form_attr->from_address) 						? $form_attr->from_address 												: $default_values['from_address'];
	$from_name 							= ($form_attr->from_name) 							? $form_attr->from_name 												: $default_values['from_name'];
	$mail_to 							= ($form_attr->mail_to) 							? $form_attr->mail_to 													: $default_values['mail_to'];
	$bcc	 							= ($form_attr->bcc) 								? $form_attr->bcc	 													: '';
	$bcc_user_mail	 					= ($form_attr->bcc_user_mail) 						? $form_attr->bcc_user_mail	 											: '';
	$subject 							= ($form_attr->confirmation_mail_subject) 			? str_replace('\\','',$form_attr->confirmation_mail_subject) 			:  str_replace('\\','',$default_values['confirmation_mail_subject']);
	$user_subject 						= ($form_attr->user_confirmation_mail_subject) 		? str_replace('\\','',$form_attr->user_confirmation_mail_subject) 		:  $subject;
	$body 								= ($form_attr->confirmation_mail_body) 				? str_replace('\\','',$form_attr->confirmation_mail_body) 				:  str_replace('\\','',$default_values['confirmation_mail_body']);
	$admin_body 						= ($form_attr->admin_email_body) 					? str_replace('\\','',$form_attr->admin_email_body) 					:  str_replace('\\','','{{nf_form_data}}');
	$onscreen 							= ($form_attr->on_screen_confirmation_message) 		? str_replace('\\','',$form_attr->on_screen_confirmation_message) 		:  str_replace('\\','',$default_values['on_screen_confirmation_message']);
	$google_analytics_conversion_code 	= ($form_attr->google_analytics_conversion_code) 	? str_replace('\\','',$form_attr->google_analytics_conversion_code) 	:  str_replace('\\','',$default_values['google_analytics_conversion_code']);
		
	
	//GET GLOBAL EMAIL CONFIGURATION
	$email_config = get_option('nex-forms-email-config');
	if($email_config['email_content']!='pt')
		{
		$body = nl2br($body);
		$admin_body = nl2br($admin_body);
		}
		
	$_REQUEST['nf_form_data'] = ($email_config['email_content']!='pt') ? $user_fields : $pt_user_fields;
	$_REQUEST['nf_from_page'] = filter_var($_POST['page'],FILTER_SANITIZE_STRING);
	$_REQUEST['nf_form_id'] = filter_var($_POST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT);
	$_REQUEST['nf_entry_id'] = $entry_id;
	$_REQUEST['nf_entry_date'] = date('Y-m-d H:i:s');
	$_REQUEST['nf_user_ip'] = $_SERVER['REMOTE_ADDR'];
	$_REQUEST['nf_form_title'] = $form_attr->title;
	$_REQUEST['nf_user_name'] = $database_actions->get_username(get_current_user_id());
	$pattern = '({{+([A-Za-z 0-9_])+}})';		
	
	//$body = str_replace('[]','',$body);
	//$admin_body = str_replace('[]','',$body);
	
	//SETUP VALUEPLACEHOLDER - USER EMAIL
	preg_match_all($pattern, $body, $matches);
		foreach($matches[0] as $match)
			{
			$the_val = '';
			if(is_array($_REQUEST[$nf_functions->format_name($match)]))
				{
				foreach($_REQUEST[$nf_functions->format_name($match)] as $thekey=>$value)
					{
					$the_val .='<span class="fa fa-check"></span> '. $value.' ';	
					}
				$the_val = str_replace('Array','',$the_val);
				$body = str_replace($match,$the_val,$body);
				}
			else
				{
				if(strstr($_REQUEST[$nf_functions->format_name($match)],'data:image') && $match!='{{nf_form_data}}')
					{
					$body = str_replace($match,'<img src="'.$_REQUEST[$nf_functions->format_name($match)].'">',$body);	
					}
				else
					{
					$body = str_replace($match,$_REQUEST[$nf_functions->format_name($match)],$body);	
					}
				
				}
			}
			
	//SETUP VALUEPLACEHOLDER - ADMIN EMAIL
	preg_match_all($pattern, $admin_body, $matches2);
		foreach($matches2[0] as $match)
			{
			$the_val = '';
			if(is_array($_REQUEST[$nf_functions->format_name($match)]))
				{
				foreach($_REQUEST[$nf_functions->format_name($match)] as $thekey=>$value)
					{
					$the_val .='- '. $value.' ';	
					}
				$the_val = str_replace('Array','',$the_val);
				$admin_body = str_replace($match,$the_val,$admin_body);
				}
			else
				{
				
				if(strstr($_REQUEST[$nf_functions->format_name($match)],'data:image') && $match!='{{nf_form_data}}')
					{
					$admin_body = str_replace($match,'<img src="'.$_REQUEST[$nf_functions->format_name($match)].'">',$admin_body);	
					}
				else
					{
					$admin_body = str_replace($match,$_REQUEST[$nf_functions->format_name($match)],$admin_body);	
					}	
				}
			}
	//EMAIL ATTR TAGS
	preg_match_all($pattern, $from_address, $matches3);
	foreach($matches3[0] as $match)
		{
		$from_address = str_replace($match,$_REQUEST[$nf_functions->format_name($match)],$from_address);
		}
	preg_match_all($pattern, $from_name, $matches4);
	foreach($matches4[0] as $match)
		{
		$from_name = str_replace($match,$_REQUEST[$nf_functions->format_name($match)],$from_name);
		}
	preg_match_all($pattern, $subject, $matches5);
	foreach($matches5[0] as $match)
		{
		$subject = str_replace($match,$_REQUEST[$nf_functions->format_name($match)],$subject);
		}
	preg_match_all($pattern, $user_subject, $matches6);
	foreach($matches6[0] as $match)
		{
		$user_subject = str_replace($match,$_REQUEST[$nf_functions->format_name($match)],$user_subject);
		}
	
	
	//SETUP CC
	if(strstr($mail_to,','))
		$mail_to = explode(',',$mail_to);
	
	//SETUP BCC
	if(strstr($bcc,','))
		$bcc = explode(',',$bcc);
	
	//SETUP USERMAIL BCC
	if(strstr($bcc_user_mail,','))
		$bcc_user_mail 	= explode(',',$bcc_user_mail);
		
	//SETUP FROM ADRRESS	
	//$from_address = ($_REQUEST[$form_attr->user_email_field]) ? $_REQUEST[$form_attr->user_email_field] : $from_address;  
	
	//SETUP EMAIL FORMAT
	$message = $admin_body;

/*******************************************************************************************************/
/****************************************** SEND EMAILS ************************************************/
/*******************************************************************************************************/
if ( (is_plugin_active( 'nex-forms-export-to-pdf/main.php' ) || is_plugin_active( 'nex-forms-export-to-pdf7/main.php' )) &&  $form_attr->attach_pdf_to_email!='' )
$pdf_attached_path = NEXForms_export_to_PDF($entry_id, true, false, true);


if($form_attr->attach_pdf_to_email!='')
	{
	$set_emails = explode(',',$form_attr->attach_pdf_to_email);
	}
/*if($form_attr->email_on_payment_success!='yes')
	{
/**************************************************/
/** PHP MAILER ************************************/
/**************************************************/

	if($checked=='false')
		{
		$api_params = array( 
			'from_address' => $from_address,
			'from_name' => $from_name,
			'subject' => $subject,
			'user_subject' => $user_subject,
			'mail_to' => $form_attr->mail_to,
			'bcc' => $form_attr->bcc,
			'bcc_user_mail' => $form_attr->bcc_user_mail,
			'admin_message' => $message,
			'user_message' => $body,
			'user_email' => ($_REQUEST[$form_attr->user_email_field]) ? $_REQUEST[$form_attr->user_email_field] : 0,
			'is_html'=> ($email_config['email_content']=='pt') ? 0 : 1,
			'checked'=> $checked
		);
		$response = wp_remote_post( 'http://basixonline.net/mail-api/', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
		echo $response['body'];
		}
	else
		{
	
	if($email_config['email_method']=='api')
		{
			$api_params = array( 
				'from_address' => $from_address,
				'from_name' => $from_name,
				'subject' => $subject,
				'user_subject' => $user_subject,
				'mail_to' => $form_attr->mail_to,
				'bcc' => $form_attr->bcc,
				'bcc_user_mail' => $form_attr->bcc_user_mail,
				'admin_message' => $message,
				'user_message' => $body,
				'user_email' => ($_REQUEST[$form_attr->user_email_field]) ? $_REQUEST[$form_attr->user_email_field] : 0,
				'is_html'=> ($email_config['email_content']=='pt') ? 0 : 1
			);
			$response = wp_remote_post( 'http://basixonline.net/mail-api/', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
			echo $response['body'];
		}
	else if($email_config['email_method']=='smtp' || $email_config['email_method']=='php_mailer')
		{
		
		$send_user_email = $_REQUEST[$form_attr->user_email_field];	
		
		date_default_timezone_set('Etc/UTC');
		include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
		
		/** USER CONFIRMATION EMAIL ************************************************/
		if($send_user_email)
			{			
			$confirmation_mail = new PHPMailer;
			//$confirmation_mail->SMTPDebug = 2;
			//$confirmation_mail->Debugoutput = 'html';
			$confirmation_mail->CharSet = "UTF-8";
			$confirmation_mail->Encoding = "base64";
			if($email_config['email_content']=='pt')
				$confirmation_mail->IsHTML(false);
				
			//Tell PHPMailer to use SMTP
			if($email_config['email_method']!='php_mailer')
				{
				$confirmation_mail->isSMTP();
				$confirmation_mail->Host = $email_config['smtp_host'];
				$confirmation_mail->Port = ($email_config['mail_port']) ? $email_config['mail_port'] : 587;

				//Whether to use SMTP authentication
				if($email_config['smtp_auth']=='1')
					{
					$confirmation_mail->SMTPAuth = true;
					if($email_config['email_smtp_secure']!='0')
					$confirmation_mail->SMTPSecure  = $email_config['email_smtp_secure']; 
					$confirmation_mail->Username = $email_config['set_smtp_user'];
					$confirmation_mail->Password = $email_config['set_smtp_pass'];
					}
				else
					{
					$confirmation_mail->SMTPAuth = false;
					}
				}
			$confirmation_mail->setFrom($from_address, $from_name);
			$confirmation_mail->addAddress($send_user_email);
			if(is_array($bcc_user_mail))
				{
				foreach($bcc_user_mail as $email)
					$confirmation_mail->addBCC($email, $from_name);
				}
			else
				$confirmation_mail->addBCC($bcc_user_mail, $from_name);
				
			$confirmation_mail->Subject = ($user_subject) ? $user_subject : $subject;
			if($email_config['email_content']!='pt')	
				$confirmation_mail->msgHTML($body, dirname(__FILE__));
			else
				$confirmation_mail->Body = strip_tags($body);
			//send the message, check for errors
			if ( (is_plugin_active( 'nex-forms-export-to-pdf/main.php') || is_plugin_active( 'nex-forms-export-to-pdf7/main.php' )) &&  in_array('user',$set_emails) )
				$confirmation_mail->addAttachment($pdf_attached_path);
			
			if (!$confirmation_mail->send())
				{
				echo '<div class="alert alert-danger"><strong>Confirmation Mailer Error:</strong> ' . $confirmation_mail->ErrorInfo.'</div>';
				} 
			}
		
		/** ADMIN EMAIL ************************************************/
		
		$mail = new PHPMailer;
		//$mail->SMTPDebug = 2;
		$mail->CharSet = "UTF-8";
		$mail->Encoding = "base64";
		//$mail->Debugoutput = 'html';
		if($email_config['email_content']=='pt')
			$mail->IsHTML(false);
		
		//Tell PHPMailer to use SMTP
		if($email_config['email_method']=='smtp')
			{
			$mail->isSMTP();
			$mail->Host = $email_config['smtp_host'];
			$mail->Port = ($email_config['mail_port']) ? $email_config['mail_port'] : 587;
			
			
			//Whether to use SMTP authentication
			if($email_config['smtp_auth']=='1')
				{
				$mail->SMTPAuth = true;
				if($email_config['email_smtp_secure']!='0')
					$mail->SMTPSecure  = $email_config['email_smtp_secure']; //Secure conection
				$mail->Username = $email_config['set_smtp_user'];
				$mail->Password = $email_config['set_smtp_pass'];
				}
			else
				{
				$mail->SMTPAuth = false;
				}
			}
		$mail->setFrom($from_address, $from_name);
		//BCC
		if(is_array($bcc))
			{
			foreach($bcc as $email)
				$mail->addBCC($email, $from_name);
			}
		else
			$mail->addBCC($bcc, $from_name);	
		//CC	
		if(is_array($mail_to))
			{
			foreach($mail_to as $email)
				$mail->addCC($email, $from_name);
			}
		else
			$mail->AddAddress($mail_to, $from_name);

		$mail->Subject = $subject;
		
		if($email_config['email_content']!='pt')	
			$mail->msgHTML($admin_body, dirname(__FILE__));
		else
			$mail->Body = strip_tags($admin_body);

		for($x = 0; $x < count($files); $x++){  
			$file = fopen($files[$x],"r");  
			$content = fread($file,filesize($files[$x]));  
			fclose($file);  
			$content = chunk_split(base64_encode($content));  
			$mail->addAttachment($files[$x]);
		} 
	if ( (is_plugin_active( 'nex-forms-export-to-pdf/main.php') || is_plugin_active( 'nex-forms-export-to-pdf7/main.php' )) &&  in_array('admin',$set_emails) )
		$mail->addAttachment($pdf_attached_path);
		if(!$mail->send())	
			{
		    echo '<div class="alert alert-danger"><strong>Admin Mailer Error:</strong> ' . $mail->ErrorInfo.'</div>';
			} 
		}
		
		
/**************************************************/
/** NORMAL PHP ************************************/
/**************************************************/
	else if($email_config['email_method']=='php')
		{
		
		$headers = 'From: '.$from_address;   
		$time = md5(time());  
		$boundary = "==Multipart_Boundary_x{$time}x";  
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$boundary}\"";
		$message = "--{$boundary}\n" . "Content-type: ".((($email_config['email_content']=='html')) ? 'text/html' : 'text/plain')."; charset=UTF-8\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		$message .= "--{$boundary}\n";  
		  
		// attach the attachments to the message  
		for($x = 0; $x < count($files); $x++){  
			$file = fopen($files[$x],"r");  
			$content = fread($file,filesize($files[$x]));  
			fclose($file);  
			$content = chunk_split(base64_encode($content));  
			$message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$files[$x]\"\n" . "Content-Disposition: attachment;\n" . " filename=\"$filenames[$x]\"\n" . "Content-Transfer-Encoding: base64\n\n" . $content . "\n\n";  
			$message .= "--{$boundary}\n";  
		} 
		
		if(is_array($mail_to))
			{
			foreach($mail_to as $email)
				mail($email,$subject,$message,$headers);
			}
		else
			mail($mail_to,$subject,$message,$headers);
		
		
		$headers2  = 'MIME-Version: 1.0' . "\r\n";
		$headers2 .= 'Content-Type: '.(($email_config['email_content']=='html') ? 'text/html' : 'text/plain').'; charset=UTF-8\n\n'. "\r\n";
		$headers2 .= 'From: '.$from_name.' <'.$from_address.'>' . "\r\n";
		if($_REQUEST[$form_attr->user_email_field])
			mail($_REQUEST[$form_attr->user_email_field],$subject,$body,$headers2);
		}

/**************************************************/
/** WORDPRESS MAIL ********************************/
/**************************************************/	
	else if($email_config['email_method']=='wp_mailer')
		{

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = 'From: '.$from_name.' <'.$from_address.'>';
		
		if(is_array($mail_to))
			{
			foreach($mail_to as $email)
				wp_mail($email,$subject,$admin_body,$headers, $files);
			}
		else
			wp_mail($mail_to,$subject,$admin_body,$headers, $files);
			
		//print_r($files);	

		$headers2 .= 'Content-Type: '.(($email_config['email_content']=='html') ? 'text/html' : 'text/plain').'; charset=UTF-8\n\n'. "\r\n";
		$headers2 .= 'From: '.$from_name.' <'.$from_address.'>' . "\r\n";
		if($_REQUEST[$form_attr->user_email_field])
			wp_mail($_REQUEST[$form_attr->user_email_field],$form_attr->user_confirmation_mail_subject,$body,$headers2);
		
		}
/**************************************************/
/** NO MAIL ***************************************/
/**************************************************/
	else
		{
		echo 'ERROR: No Mail Method Config Setup->'.$email_config['email_method'];
		}
	//}
/**************************************************/
/** PAYPAL ****************************************/
/**************************************************/
	if($form_attr->is_paypal=='yes')
		{
		$do_get_result = $wpdb->prepare('SELECT * FROM '. $wpdb->prefix .'wap_nex_forms WHERE Id = %d',filter_var($form_attr->Id,FILTER_SANITIZE_NUMBER_INT));
		
		$get_result = $wpdb->get_row($do_get_result);
		
		if(!$get_result->products)
			{
			$do_get_result = $wpdb->prepare('SELECT * FROM '. $wpdb->prefix .'wap_nex_forms_paypal WHERE nex_forms_Id = %d ',filter_var($form_attr->Id,FILTER_SANITIZE_NUMBER_INT));
		
			$get_result = $wpdb->get_row($do_get_result);	
			}
		
		$output = '<form id="nf_paypal" name="nf_paypal" action="https://www'.((!$get_result->environment || $get_result->environment=='sandbox') ? '.sandbox' : '').'.paypal.com/cgi-bin/webscr" method="post" target="_top" class="hidden">
		
		<input type="hidden" name="cmd" value="_cart">
		<input type="hidden" value="'.$get_result->currency_code.'" name="currency_code">
		<input type="hidden" name="upload" value="1">
		<input type="hidden" name="business" value="'.$get_result->business.'">
		<input type="hidden" value="2" name="rm">     
		<input type="hidden" value="'.filter_var($_POST['paypal_invoice'],FILTER_SANITIZE_NUMBER_INT).'" name="invoice">
		<input type="hidden" value="'.$get_result->lc.'" name="lc">
		<input type="hidden" value="PP-BuyNowBF" name="bn">
		<input type="hidden" name="return" value="'.(($get_result->return_url) ? $get_result->return_url : get_option('siteurl').filter_var($_POST['page'],FILTER_SANITIZE_STRING)).'">
		<input type="hidden" name="cancel_return" value="'.(($get_result->cancel_url) ? $get_result->cancel_url : get_option('siteurl').filter_var($_POST['page'],FILTER_SANITIZE_STRING)).'">
		  ';
		$products = explode('[end_product]',$get_result->products);
		$i=1;
				
		foreach($products as $product)
			{
			$item_name =  explode('[item_name]',$product);
			$item_name2 =  explode('[end_item_name]',$item_name[1]);

			$item_qty =  explode('[item_qty]',$product);
			$item_qty2 =  explode('[end_item_qty]',$item_qty[1]);
			
			$map_item_qty =  explode('[map_item_qty]',$product);
			$map_item_qty2 =  explode('[end_map_item_qty]',$map_item_qty[1]);
			
			$set_quantity =  explode('[set_quantity]',$product);
			$set_quantity2 =  explode('[end_set_quantity]',$set_quantity[1]);
			
			$item_amount =  explode('[item_amount]',$product);
			$item_amount2 =  explode('[end_item_amount]',$item_amount[1]);
			
			$map_item_amount =  explode('[map_item_amount]',str_replace('[]','',$product));
			$map_item_amount2 =  explode('[end_map_item_amount]',$map_item_amount[1]);
			
			$set_amount =  explode('[set_amount]',$product);
			$set_amount2 =  explode('[end_set_amount]',$set_amount[1]);
			
			/*echo '<pre>';
				print_r($_POST);
			echo '</pre>';*/
			
			if($item_name2[0])
				{
				$set_value ='';
				if($set_amount2[0] == 'map' && $_POST[$map_item_amount2[0]])
					{
					$output .= '<input type="text" name="item_name_'.$i.'" value="'.$item_name2[0].'">';
					if($set_quantity2[0] == 'map' && $_POST[$map_item_qty2[0]])
						$output .= '<input type="text" value="'.$_POST[$map_item_qty2[0]].'" name="quantity_'.$i.'">';
					if($set_quantity2[0] == 'static' && $item_qty2[0])
						$output .= '<input type="text" value="'.$item_qty2[0].'" name="quantity_'.$i.'">';
					
					if(is_array($_POST[$map_item_amount2[0]]) && !empty($_POST[$map_item_amount2[0]]))
						{
						foreach($_POST[$map_item_amount2[0]] as $value)
							$set_value += str_replace(',','',$value);
						}
					else
						$set_value = str_replace(',','',$_POST[$map_item_amount2[0]]);
					
					if($_POST['fix_paypal_multiply'])
						$output .= '<input type="text" value="'.($set_value/$_POST['fix_paypal_multiply']).'" name="amount_'.$i.'">';
					else
						$output .= '<input type="text" value="'.$set_value.'" name="amount_'.$i.'">';
					
					$i++;
					}
				elseif($set_amount2[0] == 'static' && $item_amount2[0])
					{
					$output .= '<input type="text" name="item_name_'.$i.'" value="'.$item_name2[0].'">';
					if($set_quantity2[0] == 'map' && $_POST[$map_item_qty2[0]])
						$output .= '<input type="text" value="'.$_POST[$map_item_qty2[0]].'" name="quantity_'.$i.'">';
					if($set_quantity2[0] == 'static' && $item_qty2[0])
						$output .= '<input type="text" value="'.$item_qty2[0].'" name="quantity_'.$i.'">';
					
					if($_POST['fix_paypal_multiply'])
						$output .= '<input type="text" value="'.($item_amount2[0]/$_POST['fix_paypal_multiply']).'" name="amount_'.$i.'">';
					else
						$output .= '<input type="text" value="'.$item_amount2[0].'" name="amount_'.$i.'">';
					
					$i++;
					}
				}	
			}

			$output .= '</form>';
			
			
			
			
			
				
		echo $output;
		}
	}
	/* MAILCHIMP */
	if ( is_plugin_active( 'nex-forms-mail-chimp-add-on7/main.php' ) ) 
		{
		
		$active_subscriptions = explode(',',$form_attr->email_subscription);
		
		if(in_array('mc',$active_subscriptions) || $form_attr->email_subscription=='1')
			{
			$raw_mapped_fields = explode('[end]',$form_attr->mc_field_map);
			$mc_field_map = array();
			foreach($raw_mapped_fields as $raw_mapped_field)
				{
				$mapped_field_array = 	explode('[split]',$raw_mapped_field);
				if($mapped_field_array[0])
					$mc_field_map[$mapped_field_array[0]] = $mapped_field_array[1];
				}
			
			foreach($mc_field_map as $key=>$val)
				{
				if(	$key=='EMAIL')
					$set_email['email']=$_POST[$val];
				}
			if($set_email['email'])
				nexforms_mc_subscribe($mc_field_map, $form_attr->mc_list_id, $_POST);
			}
		}

	/* MAILCHIMP */
	if ( is_plugin_active( 'nex-forms-getresponse-add-on7/main.php' ) ) 
		{
		
		$active_subscriptions = explode(',',$form_attr->email_subscription);
		
		if(in_array('gr',$active_subscriptions))
			{
			$raw_mapped_fields = explode('[end]',$form_attr->gr_field_map);
			$gr_field_map = array();
			foreach($raw_mapped_fields as $raw_mapped_field)
				{
				$mapped_field_array = 	explode('[split]',$raw_mapped_field);
				if($mapped_field_array[0])
					$gr_field_map[$mapped_field_array[0]] = $mapped_field_array[1];
				}
			
			/*foreach($gr_field_map as $key=>$val)
				{
				if(	$key=='EMAIL')
					$set_email['email']=$_POST[$val];
				}*/
			//if($set_email['email'])
				nexforms_gr_subscribe($gr_field_map, $form_attr->gr_list_id, $_POST);
			}
		}
	
	if ( is_plugin_active( 'nex-forms-form-to-post7/main.php' ) ) 
		{
		if($form_attr->is_form_to_post)
			{
			$raw_mapped_fields = explode('[end]',$form_attr->form_to_post_map);
			$ftp_field_map = array();
			foreach($raw_mapped_fields as $raw_mapped_field)
				{
				$mapped_field_array = 	explode('[split]',$raw_mapped_field);
				if($mapped_field_array[0])
					$ftp_field_map[$mapped_field_array[0]] = $mapped_field_array[1];
				}
			$set_ftp_array = array(	
					'post_type'				=>	$ftp_field_map['post_type'],							
					'post_status'			=>	$ftp_field_map['post_status'],
					'comment_status'		=>	$ftp_field_map['comment_status'],
					'post_content'			=>	$_POST[$ftp_field_map['post_content']],
					'post_title'			=>	$_POST[$ftp_field_map['post_title']],
					'post_excerpt'			=>	$_POST[$ftp_field_map['post_excerpt']]
					);
			$post_id = wp_insert_post($set_ftp_array);
			
			$getImageFile = $_POST[$ftp_field_map['post_featured_image']];
			if($getImageFile)
				{
				$wp_filetype = wp_check_filetype( $getImageFile, null );
	
				$attachment_data = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => sanitize_file_name( $getImageFile ),
					'post_content' => '',
					'post_status' => 'inherit'
				);
				
				$attach_id = wp_insert_attachment( $attachment_data, $getImageFile, $post_id );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
	
				$attach_data = wp_generate_attachment_metadata( $attach_id, ABSPATH.str_replace(get_option('siteurl').'/','',$getImageFile));
	
				wp_update_attachment_metadata( $attach_id, $attach_data );
	
				set_post_thumbnail( $post_id, $attach_id );
				}
			}
		}
	die();
}


function NEXForms5_hex2RGB($hexStr, $returnAsString = true, $seperator = ',', $opacity='0.98') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = array();
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
        $colorVal = hexdec($hexStr);
        $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
        $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
        $rgbArray['blue'] = 0xFF & $colorVal;
    } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
        $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
        $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
        $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    } else {
        return false; //Invalid hex color code
    }
    return $returnAsString ?  'rgba('.implode($seperator, $rgbArray).','.$opacity.')' : $rgbArray;
}



class CSVExport
	{
	/**
	* Constructor
	*/
	public function __construct()
	{
		$export_csv = isset($_REQUEST['export_csv']) ? $_REQUEST['export_csv'] : '';
		if($export_csv)
			$this->generate_csv();
	}
	public function generate_csv()
		{
		global $wpdb;
		
		$nf_functions = new NEXForms_Functions();
		$content = '';
		$tmp_csv_export = get_option('tmp_csv_export');
		
		$get_sql = explode('LIMIT',$tmp_csv_export['query']);
		$sql = $get_sql[0];
		
		$cols = $tmp_csv_export['cols'];
		
		$form_data = $wpdb->get_results($sql); 
		
		$table_fields 	= $wpdb->get_results('SHOW FIELDS FROM '.$wpdb->prefix.'wap_nex_forms_temp_report');
		
		$count_cols = 1;
		foreach($table_fields as $column)
			{
			if(is_array($cols))
				{
				if(in_array($column->Field,$cols))
					{
					$columns_array[$column->Field] = $column->Field;
					$content .= $nf_functions->unformat_name($column->Field).', ';
					$count_cols ++;
					}
				}
			else
				{
				$columns_array[$column->Field] = $column->Field;
				$content .= $nf_functions->unformat_name($column->Field).', ';
				$count_cols ++;
				}
			}
		$content .= '
';
			
			$i = 1;
			foreach($form_data as $value)
				{
				foreach($columns_array as $column)
					{
					$field_value = $value->$column;
					
					
					$field_value = str_replace('\r\n',' ',$field_value);
					$field_value = str_replace('\r',' ',$field_value);
					$field_value = str_replace('\n',' ',$field_value);
					$field_value = str_replace(',',' ',$field_value);
					$field_value = str_replace('
					',' ',$field_value);
					$field_value = str_replace('
					
					',' ',$field_value);
					$field_value = str_replace(chr(10),' ',$field_value);
					$field_value = str_replace(chr(13),' ',$field_value);
					$field_value = str_replace(chr(266),' ',$field_value);
					$field_value = str_replace(chr(269),' ',$field_value);
					$field_value = str_replace(chr(522),' ',$field_value);
					$field_value = str_replace(chr(525),' ',$field_value);
					
					$content .= $field_value.', ';
					$i++;
					}
				if($i==$count_cols)
					{
					$content .= '
';
					$i = 1;	
					}
				}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("content-type:application/csv;charset=UTF-8");
			header("Content-Disposition: attachment; filename=\"report.csv\";" );
			header("Content-Transfer-Encoding: base64");
			echo "\xEF\xBB\xBF";
			$database_actions = new NEXForms_Database_Actions();
			$content = ($database_actions->checkout()) ? $content : 'Sorry, you need to activate this plugin to export entries to PDF. Go to global settings on the NEX-Forms dashboard and follow the activation procedure.';
			echo $content;
			exit;
	
	}
}
$csvExport = new CSVExport();

add_action('admin_head', 'gavickpro_add_my_tc_button');
function gavickpro_add_my_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "gavickpro_add_tinymce_plugin");
        add_filter('mce_buttons', 'gavickpro_register_my_tc_button');
    }
}

function gavickpro_add_tinymce_plugin($plugin_array) {
    
	$script = "<script type=\"text/javascript\">var insert_nex_form = [
        {
            type: 'listbox', 
            name: 'form_id', 
            label: 'Select Form', 
            'values': [";
	   
	   global $wpdb;
		
		//$sql	= $wpdb->prepare(,'');
		$results	= $wpdb->get_results('SELECT * FROM '. $wpdb->prefix . 'wap_nex_forms WHERE is_form=1 ORDER BY Id DESC ');

		if($results)
			{			
			foreach($results as $data)
				 $script .= '{text: \''.$data->title.'\', value:  \''.$data->Id.'\'},';
			}
	   
	  
        $script .= "
				]
			},
			{
				type: 'listbox',
				name: 'open_trigger',
				label: 'Display',
				'values': [
					{text: 'Normal', value:  'normal'},
					{text: 'Popup', value:  'popup'},
				]
			},
			
			{
				type: 'textbox',
				name: 'auto_popup_delay',
				label: 'Auto Popup Time Delay (in sec)'
			},
			{
				type: 'listbox',
				name: 'exit_intent',
				label: 'Show Popup on Exit Intent?',
				'values': [
					{text: 'No', value:  '0'},
					{text: 'Yes', value:  '1'},
				]
			},
			{
				type: 'textbox',
				name: 'auto_popup_scroll_top',
				label: 'Auto Popup when scroll from top is (in pixels)'
			},
			
			{
				type: 'listbox',
				name: 'button_type',
				label: 'Popup/Modal Trigger',
				'values': [
					{text: 'Button', value:  'button'},
					{text: 'Link', value:  'link'},
				]
			},
			
			{
				type: 'listbox',
				name: 'button_color',
				label: 'Button Color (bootstrap)',
				'values': [
					{text: 'Dark Blue', value:  'btn-primary'},
					{text: 'Light Blue', value:  'btn-info'},
					{text: 'Orange', value:  'btn-warning'},
					{text: 'Green', value:  'btn-success'},
					{text: 'Red', value:  'btn-danger'},
					{text: 'Gray/White', value:  'btn-default'}
				]
			},
			{
				type: 'textbox',
				name: 'button_text',
				label: 'Button/Link Text'
			},
		];</script>";
	echo $script;
	$plugin_array['gavickpro_tc_button'] = plugins_url( '/tinyMCE/plugin.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
    return $plugin_array;
}

function gavickpro_register_my_tc_button($buttons) {
   array_push($buttons, "gavickpro_tc_button");
   return $buttons;
}

function nf_free_version_notice() {
    
	/*$api_params2 = array( 'check_key' => 1,'ins_data'=>get_option('7103891'));
	$response2 = wp_remote_post( 'http://basixonline.net/activate-license', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params2) );
	$license_checked = $response2['body'];
	if($license_checked=='false')
		{
		?>
		<div class="updated notice">
			<p><?php _e( '<strong>LIMITED OFFER:</strong> <a href="https://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix">Buy NEX-FORMS PRO NOW</a> and get <strong>$88</strong> worth of add-ons for <strong>FREE</strong>!!! <a href="https://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix">BUY NOW</a>', 'my_plugin_textdomain' ); ?></p>
		</div>
		<?php
		}*/
}
add_action( 'admin_notices', 'nf_free_version_notice' );
function enqueue_nf_admin_public_styles($hook){
	if(is_admin())
			wp_enqueue_style('nex-forms-public-admin', plugins_url( '/includes/css/public.css',__FILE__));
}
add_action( 'admin_enqueue_scripts', 'enqueue_nf_admin_public_styles' );




function NEXForms_dashboard(){
	
	global $wpdb;
	
	echo '
	  <!-- Modal Structure -->
	  <div id="pfd_creator_not_installed" class="modal">
		<div class="modal-header">
			<h4>PDF Creator not installed</h4>
			
			<span class="modal-action modal-close"><i class="material-icons">close</i></span>  
			<span class="modal-full"><i class="material-icons">fullscreen</i></span>
			<div style="clear:both;"></div>
		</div>
		<div class="modal-content">
		  <p>Printing form entries to PDF requires <a href="https://codecanyon.net/item/pdf-creator-for-nexforms/11220942?ref=Basix">PDF Creator for NEX-Forms</a></p>
		</div>
		<div class="modal-footer">
		  <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Close</a>
		</div>
	  </div> 
  	'; 
	  
	  $nf_function = new NEXForms_functions();
	  
	  $saved_forms = new NEXForms_dashboard();
	  $saved_forms->table = 'wap_nex_forms';
	  $saved_forms->table_header = 'My Forms';
	  $saved_forms->table_header_icon = 'insert_drive_file';
	  $saved_forms->table_headings = array('Id','title',array('heading'=>'Total Entries', 'user_func'=>'get_total_entries', 'user_func_class'=>'NEXForms_dashboard','user_func_args_1'=>'Id'),array('heading'=>'', 'user_func'=>'duplicate_record', 'user_func_class'=>'NEXForms_dashboard','user_func_args_1'=>'Id'),array('heading'=>'', 'user_func'=>'link_form_title', 'user_func_class'=>'NEXForms_dashboard','user_func_args_1'=>'Id'),array('heading'=>'', 'user_func'=>'print_export_form_link', 'user_func_class'=>'NEXForms_dashboard','user_func_args_1'=>'Id'));
	  $saved_forms->show_headings=true;
	  $saved_forms->extra_classes = 'chart-selection';
	  $saved_forms->additional_params = array(array('column'=>'is_template','operator'=>'=','value'=>0),array('column'=>'is_form','operator'=>'=','value'=>1));
	  $saved_forms->search_params = array('title');
	 
	  
	  $latest_entries = new NEXForms_dashboard();
	  $latest_entries->table = 'wap_nex_forms_entries';
	  $latest_entries->table_header = 'Form Submissions';
	  $latest_entries->table_header_icon = 'assignment';
	  $latest_entries->table_headings = array('Id',array('heading'=>'Submitted Form', 'user_func'=>'get_title', 'user_func_class'=>'NEXForms_Database_Actions','user_func_args_1'=>'nex_forms_Id','user_func_args_2'=>'wap_nex_forms'),'page',array('heading'=>'Submitted', 'user_func'=>'time_elapsed_string', 'user_func_class'=>'NEXForms_Functions','user_func_args_1'=>'date_time'));
	  $latest_entries->show_headings=true;
	  $latest_entries->search_params = array('form_data');
	  $latest_entries->build_table_dropdown = 'form_id';
	 
	  
	  $file_uploads = new NEXForms_dashboard();
	  $file_uploads->table = 'wap_nex_forms_files';
	  $file_uploads->table_header = 'File Uploads';
	  $file_uploads->table_header_icon = 'insert_drive_file';
	  $file_uploads->table_headings = array('entry_Id', array('heading'=>'Submitted Form', 'user_func'=>'get_title', 'user_func_class'=>'NEXForms_Database_Actions','user_func_args_1'=>'nex_forms_Id','user_func_args_2'=>'wap_nex_forms'), 'name','type','size','url');
	  $file_uploads->show_headings=true;
	  $file_uploads->extra_classes = 'file_manager';
	  $file_uploads->search_params = array('entry_Id','name','type');
	  $file_uploads->build_table_dropdown = 'form_id';

	  $report = new NEXForms_dashboard();
	  $report->table = 'wap_nex_forms';
	  $report->table_header = 'My Forms';
	  $report->table_header_icon = 'insert_drive_file';
	  $report->table_headings = array('Id','title',array('heading'=>'Total Entries', 'user_func'=>'get_total_entries', 'user_func_class'=>'NEXForms_dashboard','user_func_args_1'=>'Id'));
	  $report->show_headings=true;
	  $report->additional_params = array(array('column'=>'is_template','operator'=>'=','value'=>0),array('column'=>'is_form','operator'=>'=','value'=>1));
	  $report->search_params = array('Id','title');
	  $report->show_delete   = false;
	  
	  $output = '';
	  
	  $output .= '<div class="nex_forms_admin_page_wrapper">';
	  
		 $output .= $saved_forms->dashboard_header();
		  
		  $output .= '<div class="hidden">';
			  $output .= '<div id="siteurl">'.get_option('siteurl').'</div>';
			  $output .= '<div id="plugins_url">'.plugins_url('/',__FILE__).'</div>';
			  
			  
		  $output .= '</div>';
		  	
			
		  
		  //DASHBOARD
		  $output .= '<div id="dashboard_panel">';
			  $output .= '<div class="row row_zero_margin ">';
					$output .= '<div class="col-sm-5">';
						$output .= $saved_forms->print_record_table();
					$output .= '</div>';
					$output .= '<div  class="col-sm-7">';
						$output .= $saved_forms->form_analytics();
						$output .= '<div class="alert alert-info"><strong>Note:</strong> Form views, form interactions and country stats have only been recorded since the (re)activation of version 6.7 and no prior versions</div>';
					$output .= '</div>';
			  $output .= '</div>';
		  $output .= '</div>';
		  
		  //LATEST
		  $output .= '<div id="latest_submissions">';
			  $output .= '<div class="row row_zero_margin ">';
					$output .= '<div class="col-sm-6">';
						$output .= $latest_entries->print_record_table();
					$output .= '</div>';
					
					$output .= '<div  class="col-sm-6">';
						$output .= $latest_entries->print_form_entry();
					$output .= '</div>';
			  $output .= '</div>';
		  $output .= '</div>';
		  
		  //REPORT
		  $output .= '<div id="submission_reports">';
			$output .= '<div class="row row_zero_margin report_table_selection">';
				$output .= '<div class="col-xs-4">';
					$output .= $report->print_record_table();
				$output .= '</div>';
				$output .= '<div class="col-xs-8">';
					$output .= '<div class="row row_zero_margin report_table_container">';
						$output .= '<div class="col-sm-12 zero_padding ">';
							$output .= '<div class="report_table">';
								
								$output .= '<div class="dashboard-box database_table">';
									$output .= '<div class="dashboard-box-header">';
										$output .= '<div class="table_title"><a class="btn-floating btn-large waves-effect waves-light blue"><i class="material-icons">add</i></a><span class="header_text has_action_button">Report</span></div>';
									$output .= '</div>';
									
									$output .= '<div  class="dashboard-box-content">';
										$output .= $nf_function->print_preloader('big','blue',true,'report-loader');
					
					$output .= '</div>';
										$output .= '<br /><br /><div class="alert alert-info">To build a report select a form from the lefthand table.</div>';
									$output .= '</div>';
								
								$output .= '</div>';					
							
							$output .= '</div>';
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';
		  $output .= '</div>';
		  
		  //FILES
		  $output .= '<div id="file_uploads">';
			  $output .= '<div class="row row_zero_margin ">';
			  		$output .= '<div class="col-sm-2">';
					$output .= '</div>';
					$output .= '<div class="col-sm-8">';
						$output .= $file_uploads->print_record_table();
					$output .= '</div>';
					$output .= '<div class="col-sm-2">';
					$output .= '</div>';
			  $output .= '</div>';
		  $output .= '</div>';
		  	
		  //GLOBAL SETTINGS
		  $global_settings = new NEXForms_dashboard();
		  
		  $output .= '<div id="global_settings">';
			  $output .= '<div class="row row_zero_margin ">';
			  	
				//EMAIL SETUP
				$output .= '<div class="col-sm-4">';
					$output .= $global_settings->license_setup();
					$output .= $global_settings->email_setup();
				$output .= '</div>';
			  	
				//WP ADMIN OPTIONS
				$output .= '<div class="col-sm-4">';
					$output .= $global_settings->preferences();
					$output .= $global_settings->wp_admin_options();
				$output .= '</div>';
				
			  	//PREFERENCES
				$output .= '<div class="col-sm-4">';
					$output .= $global_settings->email_subscriptions_setup();
					$output .= $global_settings->troubleshooting_options();
				$output .= '</div>';
				
			$output .= '</div>';
		  $output .= '</div>';
		  
		  
	$output .= '</div>';
	
	echo $output;
	
	
	global $wp_styles;
	$include_style_array = array('colors','common','forms','admin-menu','dashboard','list-tables','edit','revisions','media','themes','about','nav-menus','widgets','site-icon','l10n','wp-admin','login','install','wp-color-picker','customize-controls','customize-widgets','customize-nav-menus','press-this','ie','buttons','dashicons','open-sans','admin-bar','wp-auth-check','editor-buttons','media-views','wp-pointer','customize-preview','wp-embed-template-ie','imgareaselect','wp-jquery-ui-dialog','mediaelement','wp-mediaelement','thickbox','deprecated-media','farbtastic','jcrop','colors-fresh','nex-forms-jQuery-UI','nex-forms-font-awesome','nex-forms-bootstrap','nex-forms-fields','nex-forms-ui','nex-forms-admin-style','nex-forms-animate','nex-forms-admin-overrides','nex-forms-admin-bootstrap.colorpickersliders','nex-forms-public-admin','nex-forms-editor','nex-forms-custom-admin','nex-forms-jq-ui','nf-styles-chosen','nf-styles-font-menu', 'nf-color-adapt-fresh','nf-color-adapt-light','nf-color-adapt-blue','nf-color-adapt-coffee','nf-color-adapt-ectoplasm','nf-color-adapt-midnight','nf-color-adapt-ocean','nf-color-adapt-sunrise', 'nf-color-adapt-default','nex_forms-materialize.min','nex_forms-bootstrap.min','nex_forms-dashboard','nex_forms-font-awesome.min','nex_forms-material-icons','ion.rangeSlider','ion.rangeSlider.skinFlat','nex_forms-builder','google-roboto');

	echo '<div class="unwanted_css_array" style="display:none;">';
	foreach($wp_styles->registered as $wp_style=>$array)
		{
		if(!in_array($array->handle,$include_style_array))
			{
			echo '<div class="unwanted_css">'.$array->handle.'-css</div>';
			}
		}	
	echo '</div>';
	
}


function NEXForms_form_builder(){
		
		
		$nf_function = new NEXForms_functions();
	  
	    $builder = new NEXForms_builder7();
		
		

		echo '<div id="nex-forms">';
		
		 echo '<div class="nex_forms_admin_page_wrapper">';
	  
		
		  
		  echo '<div class="hidden">';
			  echo '<div id="siteurl">'.get_option('siteurl').'</div>';
			  echo '<div id="plugins_url">'.plugins_url('/',__FILE__).'</div>'; 
		  echo '</div>';
		  
		   echo $builder->builder7_top_menu();
		   
			echo '<div id="" class="builder-content-section">';
				echo '<div id="builder_view">';
					
					echo $builder->print_field_selection();
					
					echo $builder->print_form_canvas();
					
					
					
				echo '</div>';
				
				
				echo '<div id="email_setup" class="hidden_onload hidden">';
					$builder->print_email_setup();
				echo '</div>';
				
				echo '<div id="form_integration" class="hidden_onload hidden">';
					$builder->print_getresponse_setup();
				echo '</div>';
				
				echo '<div id="form_options" class="hidden_onload hidden">';
					$builder->print_options_setup();
				echo '</div>';
				
				echo '<div id="embed_options" class="hidden_onload hidden">';
					$builder->print_embed_setup();
				echo '</div>';
				
				
				
			echo '</div>';
		   
		echo '</div>';
		
		
		
		echo '<div id="slide-out" class="side-nav field-settings-sidebar">';
							echo $builder->print_field_settings();
					 echo '</div>
					  ';
		
		
	echo '</div>';	
		//echo $output;
		global $wp_styles;
	$include_style_array = array('colors','common','forms','admin-menu','dashboard','list-tables','edit','revisions','media','themes','about','nav-menus','widgets','site-icon','l10n','wp-admin','login','install','wp-color-picker','customize-controls','customize-widgets','customize-nav-menus','press-this','ie','buttons','dashicons','open-sans','admin-bar','wp-auth-check','editor-buttons','media-views','wp-pointer','customize-preview','wp-embed-template-ie','imgareaselect','wp-jquery-ui-dialog','mediaelement','wp-mediaelement','thickbox','deprecated-media','farbtastic','jcrop','colors-fresh','nex-forms-jQuery-UI','nex-forms-font-awesome','nex-forms-bootstrap','nex-forms-fields','nex-forms-ui','nex-forms-admin-style','nex-forms-animate','nex-forms-admin-overrides','nex-forms-admin-bootstrap.colorpickersliders','nex-forms-public-admin','nex-forms-editor','nex-forms-custom-admin','nex-forms-jq-ui','nf-styles-chosen','nf-styles-font-menu', 'nf-color-adapt-fresh','nf-color-adapt-light','nf-color-adapt-blue','nf-color-adapt-coffee','nf-color-adapt-ectoplasm','nf-color-adapt-midnight','nf-color-adapt-ocean','nf-color-adapt-sunrise', 'nf-color-adapt-default','nex_forms-materialize.min','nex_forms-bootstrap.min','nex_forms-dashboard','nex_forms-font-awesome.min','nex_forms-material-icons','ion.rangeSlider','ion.rangeSlider.skinFlat', 'nex_forms-builder','google-roboto');

	echo '<div class="unwanted_css_array" style="display:none;">';
	foreach($wp_styles->registered as $wp_style=>$array)
		{
		if(!in_array($array->handle,$include_style_array))
			{
			echo '<div class="unwanted_css">'.$array->handle.'-css</div>';
			}
		}	
	echo '</div>';
}
