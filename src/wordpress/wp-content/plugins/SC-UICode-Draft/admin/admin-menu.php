<?php
/**
 *
 * Description: SailChecker UI admin menu
 */

add_action('admin_menu', 'create_admin_menu');
function create_admin_menu() {
	add_menu_page('SailChecker UI Options', 'SailChecker UI', 'manage_options', 'sailchecker-ui', 'create_main_page');
}

function create_main_page() {
	// Must check that the user has the required capability 
    if (!current_user_can('manage_options')) {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
	echo get_include_contents('admin/includes/main-page.php');
}