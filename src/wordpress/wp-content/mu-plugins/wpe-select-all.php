<?php
/*
* Plugin Name: WP Engine Select-All Tables Workaround
* Plugin URI: https://gist.github.com/futuernorn/0c53994e7a0a2194be5f
* Description: Ensures all tables are selected when a '&select=all' parameter is passed to the WP Engine plugin staging tab URL
* Version: 0.1
* Author: Jeffrey Hogan
*/

if (isset($_GET['src']) && $_GET['src'] == 'wpe-select-all.js') {
  $js = "var ignore_table_prefix = '".(isset($_GET['ignore-table-prefix']) ? $_GET['ignore-table-prefix'] : '')."';";
  $js .= <<<JAVASCRIPT

jQuery(document).ready(function($) {
	var selectAllParagraph = jQuery(document.createElement('p'));
	selectAllParagraph.addClass('submit submit-top');

	selectAllParagraph.append('<button type="button" id="wpe_select_all_tables" name="wpe_select_all" value="Select all tables" class="wpe-pointer btn btn-success" data-toggle="tooltip" data-placement="top" title="Tooltip on top">Select all tables</button>');
	selectAllParagraph.append('<button type="button" id="wpe_deselect_all_tables" name="wpe_deselect_all" value="Deselect all tables" class="wpe-pointer btn btn-info" data-toggle="tooltip" data-placement="top" title="Tooltip on top">Deselect all tables</button>');
	
	if (ignore_table_prefix != '')
		selectAllParagraph.append('<div>(excluding tables starting with: '+ignore_table_prefix+')</div>');
	
	$('p.table-select').prepend(selectAllParagraph);

	$('#wpe_select_all_tables').click(function() {
		jQuery("[name='tables[]'] option").each(function() {
    			if (ignore_table_prefix != '' && $(this).val().toLowerCase().indexOf(ignore_table_prefix) >= 0)
				return true;
			$(this).prop('selected', true);
		});
		jQuery("[name='tables[]']").trigger('liszt:updated');
	});

	$('#wpe_deselect_all_tables').click(function() {
		jQuery("[name='tables[]'] option").prop('selected', false);
		jQuery("[name='tables[]']").trigger('liszt:updated');
	});
});
JAVASCRIPT;
  die($js);
}

function wpe_select_all_enqueue($hook) {
	if ($hook == 'toplevel_page_wpengine-common' && $_GET['tab'] == 'staging' ) {
    		$src_name = 'wpe-select-all.js'.(isset($_GET['ignore-table-prefix']) ? '&ignore-table-prefix='.$_GET['ignore-table-prefix'] : '');
    		wp_enqueue_script('wpe-sel-all-main', plugins_url( 'wpe-select-all.php?src='.$src_name, __FILE__ ), array('jquery', 'wpe-common'));
		wp_dequeue_script('userpro_chosen');
	}
}

add_action( 'admin_enqueue_scripts', 'wpe_select_all_enqueue');
