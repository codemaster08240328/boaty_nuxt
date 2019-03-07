<?php
if ( ! defined( 'ABSPATH' ) ) exit;


include_once( 'classes/class.install.php');
include_once( 'classes/class.db.php');
include_once( 'classes/class.functions.php');
include_once( 'classes/class.export.php');
include_once( 'classes/class.preferences.php');
include_once( 'classes/class.icons.php');
include_once( 'classes/class.googlefonts.php');
include_once( 'classes/class.dashboard.php');
include_once( 'classes/class.builder7.php');


function enqueue_nf_admin_scripts($hook) {
    
	
	/*if ( 'toplevel_page_nex-forms-dashboard' != $hook ) {
			return;
		}*/
	wp_enqueue_script('jquery');
	wp_enqueue_style('jquery-ui');
	
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-widget');
	wp_enqueue_script('jquery-ui-mouse');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('jquery-ui-autocomplete');
	wp_enqueue_script('jquery-form');

	/* Custom Includes */
	if($hook=='toplevel_page_nex-forms-dashboard')
		{
		wp_enqueue_script('nex-forms-charts',plugins_url( '/nf-admin/js/chart.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex_forms-materialize.min',plugins_url('/assets/materialize.js',dirname(__FILE__)));
		wp_enqueue_script('formilise-js-init',plugins_url('/nf-admin/js/initialize.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-stats',plugins_url( 'nf-admin/js/dashboard.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-gcharts','https://www.gstatic.com/charts/loader.js');
		wp_enqueue_script('nex-forms-global-settings',plugins_url('/js/global-settings.js',__FILE__));
		wp_enqueue_script('nex-forms-pref',plugins_url('/js/preferences.js',__FILE__));
		wp_enqueue_script('nex-forms-admin-functions',plugins_url('/js/admin-functions.js',__FILE__));
		}
		
	if($hook=='nex-forms_page_nex-forms-builder')
		{
		wp_enqueue_style('google-roboto', 'https://fonts.googleapis.com/css?family=Roboto');	
		wp_enqueue_script('nex-forms-builder',plugins_url('/js/builder.js',__FILE__));	
			
		wp_enqueue_script('ion.rangeSlider.min',plugins_url('/assets/ion.rangeSlider.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex_forms-materialize.min',plugins_url('/assets/materialize.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-bootstrap.min',plugins_url('/js/bootstrap-admin.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-drag-and-drop',plugins_url('/js/drag-and-drop.js',__FILE__));
		wp_enqueue_script('nex-forms-preview',plugins_url('/js/preview.js',__FILE__));
		wp_enqueue_script('nex-forms-bootstrap-colorpicker',plugins_url('/js/bootstrap-colorpicker.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-bootstrap.colorpickersliders',plugins_url('/js/bootstrap.colorpickersliders.js',__FILE__));
		
		wp_enqueue_script('nex-forms-field-settings',plugins_url('/js/field-settings.js',__FILE__));
		wp_enqueue_script('nex-forms-fields',plugins_url('/js/fields.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-wow',plugins_url('/js/wow.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-admin-functions',plugins_url('/js/admin-functions.js',__FILE__));
		wp_enqueue_script('nex-forms-conditional-logic',plugins_url('/js/conditional_logic.js',__FILE__));
		
		wp_enqueue_script('nex-forms-wow',plugins_url('/js/wow.min.js',dirname(__FILE__)));
		/*wp_enqueue_script('nex-forms-load-admin',plugins_url('/js/load.js',__FILE__));
		wp_enqueue_script('nex-forms-admin-functions',plugins_url('/js/admin-functions.js',__FILE__));
		wp_enqueue_script('nex-forms-signature',plugins_url('/js/nf-signature.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-fields',plugins_url('/js/fields.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-global-settings',plugins_url('/js/global-settings.js',__FILE__));
		wp_enqueue_script('nex-forms-pref',plugins_url('/js/preferences.js',__FILE__));
		*/
		wp_enqueue_script('nex-forms-bootstrap.colorpickersliders',plugins_url('/js/bootstrap.colorpickersliders.js',__FILE__));
		wp_enqueue_script('nex-forms-tinycolor-min',plugins_url('/js/tinycolor-min.js',__FILE__));
		
		   /* date time */
		wp_enqueue_script('nex-forms-moment.min', plugins_url('/js/moment.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-locales.min',plugins_url('/js/locales.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-raty',plugins_url('/js/jquery.raty-fa.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-date-time',plugins_url('/js/bootstrap-datetimepicker.js',dirname(__FILE__)));
		
		//wp_enqueue_script('nex-forms-custom-admin',plugins_url('/js/custom-admin.js',__FILE__));
		//wp_enqueue_script('nex-forms-tutorial',plugins_url('/js/tutorial.js',__FILE__));
		}
	
	if($hook=='nex-forms_page_nex-forms-main')
		{
		wp_enqueue_script('ion.rangeSlider.min',plugins_url('/assets/ion.rangeSlider.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex_forms-materialize.min',plugins_url('/assets/materialize.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-bootstrap.min',plugins_url('/js/bootstrap.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-drag-and-drop',plugins_url('/js/drag-and-drop.js',__FILE__));
		
		wp_enqueue_script('nex-forms-preview',plugins_url('/js/preview.js',__FILE__));
		wp_enqueue_script('nex-forms-bootstrap-colorpicker',plugins_url('/js/bootstrap-colorpicker.min.js',dirname(__FILE__)));
	
		wp_enqueue_script('nex-forms-editor',plugins_url('/editor/trumbowyg.min.js',__FILE__));
		
		wp_enqueue_script('nex-forms-field-settings',plugins_url('/js/field-settings.js',__FILE__));
		wp_enqueue_script('nex-forms-conditional-logic',plugins_url('/js/conditional_logic.js',__FILE__));
		wp_enqueue_script('nex-forms-wow',plugins_url('/js/wow.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-load-admin',plugins_url('/js/load.js',__FILE__));
		wp_enqueue_script('nex-forms-admin-functions',plugins_url('/js/admin-functions.js',__FILE__));
		wp_enqueue_script('nex-forms-signature',plugins_url('/js/nf-signature.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-fields',plugins_url('/js/fields.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-global-settings',plugins_url('/js/global-settings.js',__FILE__));
		wp_enqueue_script('nex-forms-pref',plugins_url('/js/preferences.js',__FILE__));
		
		wp_enqueue_script('nex-forms-bootstrap.colorpickersliders',plugins_url('/js/bootstrap.colorpickersliders.js',__FILE__));
		wp_enqueue_script('nex-forms-tinycolor-min',plugins_url('/js/tinycolor-min.js',__FILE__));
		
		   /* date time */
		wp_enqueue_script('nex-forms-moment.min', plugins_url('/js/moment.min.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-locales.min',plugins_url('/js/locales.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-raty',plugins_url('/js/jquery.raty-fa.js',dirname(__FILE__)));
		wp_enqueue_script('nex-forms-date-time',plugins_url('/js/bootstrap-datetimepicker.js',dirname(__FILE__)));
		
		wp_enqueue_script('nex-forms-custom-admin',plugins_url('/js/custom-admin.js',__FILE__));
		wp_enqueue_script('nex-forms-tutorial',plugins_url('/js/tutorial.js',__FILE__));
		}
	
	
}

function enqueue_nf_admin_styles($hook) {
	/* CSS */
	
	
	
	/*if ( 'toplevel_page_nex-forms-dashboard' != $hook ) {
			return;
		}*/
	if($hook=='toplevel_page_nex-forms-dashboard')
		{
		wp_enqueue_style('ion.rangeSlider',plugins_url('/assets/ion.rangeSlider.css',dirname(__FILE__)));
		wp_enqueue_style('ion.rangeSlider.skinFlat',plugins_url('/assets/ion.rangeSlider.skinFlat.css',dirname(__FILE__)));	
			
		wp_enqueue_style('nex_forms-materialize.min',plugins_url('/assets/materialize.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-bootstrap.min',plugins_url('/nf-admin/css/bootstrap.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-dashboard',plugins_url('/nf-admin/css/dashboard.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-font-awesome.min',plugins_url('/assets/font-awesome.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-material-icons','https://fonts.googleapis.com/icon?family=Material+Icons');
		}
	if($hook=='nex-forms_page_nex-forms-builder')
		{
		wp_enqueue_style('nex-forms-jQuery-UI',plugins_url( '/css/jquery-ui.min.css',dirname(__FILE__)));
		wp_enqueue_style('ion.rangeSlider',plugins_url('/assets/ion.rangeSlider.css',dirname(__FILE__)));
		wp_enqueue_style('ion.rangeSlider.skinFlat',plugins_url('/assets/ion.rangeSlider.skinFlat.css',dirname(__FILE__)));	
		wp_enqueue_style('nex_forms-builder',plugins_url('/nf-admin/css/builder.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-materialize.min',plugins_url('/assets/materialize-ui.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-bootstrap.min',plugins_url('/nf-admin/css/bootstrap.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-fields',plugins_url('/css/fields.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-ui',plugins_url('/css/ui.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-animate',plugins_url('/css/animate.css',__FILE__));
		wp_enqueue_style('nex-forms-admin-bootstrap.colorpickersliders',plugins_url('/css/bootstrap.colorpickersliders.css',__FILE__));
		
		wp_enqueue_style('nex_forms-font-awesome.min',plugins_url('/assets/font-awesome.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-material-icons','https://fonts.googleapis.com/icon?family=Material+Icons');
		}
	if($hook=='nex-forms_page_nex-forms-main')
		{
		wp_enqueue_style('nex-forms-jQuery-UI',plugins_url( '/css/jquery-ui.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex_forms-materialize.min',plugins_url('/assets/materialize.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-font-awesome',plugins_url('/css/font-awesome.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-bootstrap',plugins_url('/css/bootstrap.min.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-fields',plugins_url('/css/fields.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-ui',plugins_url('/css/ui.css',dirname(__FILE__)));
		wp_enqueue_style('nex-forms-admin-style',plugins_url('/css/style.css',__FILE__));
		wp_enqueue_style('nex-forms-animate',plugins_url('/css/animate.css',__FILE__));
		wp_enqueue_style('nex-forms-admin-overrides',plugins_url('/css/overrides.css',__FILE__));
		wp_enqueue_style('nex-forms-admin-bootstrap.colorpickersliders',plugins_url('/css/bootstrap.colorpickersliders.css',__FILE__));
		wp_enqueue_style('nex_forms-material-icons','https://fonts.googleapis.com/icon?family=Material+Icons');
		wp_enqueue_style('nex-forms-editor',plugins_url('/editor/ui/trumbowyg.min.css',__FILE__));
		}
	
	
}
add_action( 'admin_enqueue_scripts', 'enqueue_nf_admin_scripts' );
add_action( 'admin_enqueue_scripts', 'enqueue_nf_admin_styles' );

?>