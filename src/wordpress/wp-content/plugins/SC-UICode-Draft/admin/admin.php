<?php

/**
 *
 *
 */
add_action('admin_init', 'sc_ui_register_settings');
function sc_ui_register_settings() {
	register_setting('sc-ui-option-group', 'sc-enquire-ui-enquiry-subject');
	register_setting('sc-ui-option-group', 'sc-enquire-ui-response-message');
	register_setting('sc-ui-option-group', 'sc-enquire-ui-response-subject');
}