<?php
/*
Plugin Name: Opt-In Content Locker
Plugin URI: http://halfdata.com/milkyway/subscribe-unlock.html
Description: The plugin easily allows you to lock content and ask user to subscribe.
Version: 2.60
Author: Ivan Churakov
Author URI: http://codecanyon.net/user/ichurakov?ref=ichurakov
*/
define('SUBSCRIBEUNLOCK_RECORDS_PER_PAGE', '40');
define('SUBSCRIBEUNLOCK_VERSION', 2.60);
define('SUBSCRIBEUNLOCK_COOKIE', 'ilovelencha');
define('SUBSCRIBEUNLOCK_AWEBER_APPID', '5d1a0e57');

register_activation_hook(__FILE__, array("subscribeunlock_class", "install"));

class subscribeunlock_class {
	var $options;
	
	function __construct() {
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('subscribeunlock', false, dirname(plugin_basename(__FILE__)).'/languages/');
		}
		
		$this->options = array (
			"version" => SUBSCRIBEUNLOCK_VERSION,
			"soft_mode" => "off",
			"intro" => __('Complete your name and email to read content.', 'subscribeunlock'),
			"button_label" => __('Unlock Content', 'subscribeunlock'),
			"placeholder_email" => __('Enter your e-mail...', 'subscribeunlock'),
			"placeholder_name" => __('Enter your name...', 'subscribeunlock'),
			'box_color' => '#F8F8F8',
			'box_border_color' => '#CCCCCC',
			'box_font_color' => '#333333',
			'box_font_size' => 13,
			'button_color' => '#0147A3',
			'button_font_color' => '#FFFFFF',
			'button_font_size' => 14,
			'input_font_color' => '#333333',
			'input_font_size' => 13,
			'input_border_color' => '#444444',
			'input_background_color' => '#FFFFFF',
			'input_background_opacity' => 0.7,
			"from_name" => get_bloginfo("name"),
			"from_email" => "noreply@".str_replace("www.", "", $_SERVER["SERVER_NAME"]),
			"thanks_message" => __('Thank you. You are redirecting...', 'subscribeunlock'),
			"thanksgiving_enable" => "off",
			"thanksgiving_email_subject" => __('Thank you for subscription', 'subscribeunlock'),
			"thanksgiving_email_body" => __('Dear {name},', 'subscribeunlock').PHP_EOL.PHP_EOL.__('Thank you for subscription.', 'subscribeunlock').PHP_EOL.PHP_EOL.__('Thanks,', 'subscribeunlock').PHP_EOL.get_bloginfo("name"),
			"csv_separator" => ";",
			'email_validation' => "off",
			"disable_name" => "off",
			"mailchimp_enable" => "off",
			"mailchimp_api_key" => "",
			"mailchimp_list_id" => "",
			"mailchimp_double" => "off",
			"mailchimp_welcome" => "off",
			"icontact_enable" => "off",
			"icontact_appid" => "",
			"icontact_apiusername" => "",
			"icontact_apipassword" => "",
			'icontact_listid' => "",
			'campaignmonitor_enable' => "off",
			'campaignmonitor_api_key' => '',
			'campaignmonitor_list_id' => '',
			'getresponse_enable' => "off",
			'getresponse_api_key' => '',
			'getresponse_campaign_id' => '',
			'aweber_enable' => "off",
			'aweber_consumer_key' => "",
			'aweber_consumer_secret' => "",
			'aweber_access_key' => "",
			'aweber_access_secret' => "",
			'aweber_listid' => "",
			'madmimi_enable' => 'off',
			'madmimi_login' => '',
			'madmimi_api_key' => '',
			'madmimi_list_id' => '',
			'mymail_enable' => "off",
			'mymail_listid' => "",
			'mymail_double' => "off",
			'sendy_enable' => 'off',
			'sendy_url' => '',
			'sendy_listid' => '',
			'benchmark_enable' => 'off',
			'benchmark_api_key' => '',
			'benchmark_list_id' => '',
			'benchmark_double' => 'off',
			"ga_tracking" => "off",
			"terms" => ""
		);

		if (defined('WP_ALLOW_MULTISITE')) $this->install();
		$this->get_options();
		
		if (is_admin()) {
			if ($this->check_options() !== true) add_action('admin_notices', array(&$this, 'admin_warning'));
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
			add_action('admin_menu', array(&$this, 'admin_menu'));
			add_action('init', array(&$this, 'admin_request_handler'));
			add_filter('mce_external_plugins', array(&$this, "mce_external_plugin"));
			add_filter('mce_buttons', array(&$this, "mce_button"), 0);
			add_action('wp_ajax_subscribeunlock_submit', array(&$this, "subscribeunlock_submit"));
			add_action('wp_ajax_nopriv_subscribeunlock_submit', array(&$this, "subscribeunlock_submit"));
			add_action('wp_ajax_subscribeunlock_aweber_connect', array(&$this, "aweber_connect"));
			add_action('wp_ajax_subscribeunlock_aweber_disconnect', array(&$this, "aweber_disconnect"));
		} else {
			add_action('wp', array(&$this, 'front_init'));
			add_shortcode('subscribelocker', array(&$this, "shortcode_handler"));
			add_shortcode('optinlocker', array(&$this, "shortcode_handler"));
		}
	}

	function admin_enqueue_scripts() {
		wp_enqueue_script("jquery");
		wp_enqueue_style('subscribeunlock', plugins_url('/css/admin.css', __FILE__), array(), SUBSCRIBEUNLOCK_VERSION);
		wp_enqueue_script('subscribeunlock', plugins_url('/js/admin.js', __FILE__), array(), SUBSCRIBEUNLOCK_VERSION);
		if (isset($_GET['page']) && $_GET['page'] == 'subscribeunlock') {
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('wp-color-picker');
		}
	}

	function front_init() {
		add_action('wp_enqueue_scripts', array(&$this, 'front_enqueue_scripts'));
		add_action('wp_head', array(&$this, 'front_header'));
	}

	function front_enqueue_scripts() {
		wp_enqueue_script("jquery");
		wp_enqueue_script('subscribeunlock', plugins_url('/js/script.js', __FILE__), array(), SUBSCRIBEUNLOCK_VERSION);
		wp_enqueue_style('subscribeunlock', plugins_url('/css/style.css', __FILE__), array(), SUBSCRIBEUNLOCK_VERSION);
	}

	function install () {
		global $wpdb;
		$table_name = $wpdb->prefix."sp_users";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE " . $table_name . " (
				id int(11) NOT NULL auto_increment,
				name varchar(255) collate utf8_unicode_ci NOT NULL,
				email varchar(255) collate utf8_unicode_ci NOT NULL,
				registered int(11) NOT NULL,
				deleted int(11) NOT NULL default '0',
				UNIQUE KEY  id (id)
			);";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	}

	function get_options() {
		$exists = get_option('subscribeunlock_version');
		if ($exists) {
			foreach ($this->options as $key => $value) {
				$this->options[$key] = get_option('subscribeunlock_'.$key, $this->options[$key]);
			}
		}
	}

	function update_options() {
		//if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('subscribeunlock_'.$key, $value);
			}
		//}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['subscribeunlock_'.$key])) {
				$this->options[$key] = stripslashes($_POST['subscribeunlock_'.$key]);
			}
		}
	}

	function check_options() {
		$errors = array();
		if ($this->options['thanksgiving_enable'] == 'on') {
			if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $this->options['from_email']) || strlen($this->options['from_email']) == 0) $errors[] = __('Sender e-mail must be valid e-mail address', 'subscribeunlock');
			if (strlen($this->options['from_name']) < 3) $errors[] = __('Sender name is too short', 'subscribeunlock');
			if (strlen($this->options['thanksgiving_email_subject']) < 3) $errors[] = __('Thanksgiving e-mail subject must contain at least 3 characters', 'subscribeunlock');
			else if (strlen($this->options['thanksgiving_email_subject']) > 64) $errors[] = __('Thanksgiving e-mail subject must contain maximum 64 characters', 'subscribeunlock');
			if (strlen($this->options['thanksgiving_email_body']) < 3) $errors[] = __('Thanksgiving e-mail body must contain at least 3 characters', 'subscribeunlock');
		}
		if (strlen($this->options['thanks_message']) < 3) $errors[] = __('Redirecting message is too short', 'subscribeunlock');
		if (strlen($this->options['placeholder_name']) < 3) $errors[] = __('"Name" field placeholder is too short', 'subscribeunlock');
		if (strlen($this->options['placeholder_email']) < 3) $errors[] = __('"E-mail" field placeholder is too short', 'subscribeunlock');
		if (strlen($this->options['button_label']) < 2) $errors[] = __('"Subscribe" button label is too short', 'subscribeunlock');
		
		if (strlen($this->options['box_color']) > 0 && $this->get_rgb($this->options['box_color']) === false) $errors[] = __('Box background color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['box_border_color']) > 0 && $this->get_rgb($this->options['box_border_color']) === false) $errors[] = __('Box border color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['box_font_color']) > 0 && $this->get_rgb($this->options['box_font_color']) === false) $errors[] = __('Font color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['input_border_color']) > 0 && $this->get_rgb($this->options['input_border_color']) === false) $errors[] = __('Input field border color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['input_background_color']) > 0 && $this->get_rgb($this->options['input_background_color']) === false) $errors[] = __('Input field background color must be a valid value.', 'subscribeunlock');
		if (floatval($this->options['input_background_opacity']) < 0 || floatval($this->options['input_background_opacity']) > 1) $errors[] = __('Input field background opacity must be in a range [0...1].', 'subscribeunlock');
		if (strlen($this->options['input_font_color']) == 0 || $this->get_rgb($this->options['input_font_color']) === false) $errors[] = __('I font color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['button_color']) == 0 || $this->get_rgb($this->options['button_color']) === false) $errors[] = __('Button color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['button_font_color']) == 0 || $this->get_rgb($this->options['button_font_color']) === false) $errors[] = __('Button font color must be a valid value.', 'subscribeunlock');
		if (strlen($this->options['box_font_size']) > 0 && $this->options['box_font_size'] != preg_replace('/[^0-9]/', '', $this->options['box_font_size']) && ($this->options['box_font_size'] > 72 || $this->options['box_font_size'] < 10)) $errors[] = __('Font size must be in a range [10...72].', 'subscribeunlock');
		if (strlen($this->options['input_font_size']) > 0 && $this->options['input_font_size'] != preg_replace('/[^0-9]/', '', $this->options['input_font_size']) && ($this->options['input_font_size'] > 72 || $this->options['input_font_size'] < 10)) $errors[] = __('Input field font size must be in a range [10...72].', 'subscribeunlock');
		if (strlen($this->options['button_font_size']) > 0 && $this->options['button_font_size'] != preg_replace('/[^0-9]/', '', $this->options['button_font_size']) && ($this->options['button_font_size'] > 72 || $this->options['button_font_size'] < 10)) $errors[] = __('Button font size must be in a range [10...72].', 'subscribeunlock');
		
		if ($this->options['mailchimp_enable'] == 'on') {
			if (empty($this->options['mailchimp_api_key']) || strpos($this->options['mailchimp_api_key'], '-') === false) $errors[] = __('Invalid MailChimp API Key', 'subscribeunlock');
			if (empty($this->options['mailchimp_list_id'])) $errors[] = __('Invalid MailChimp List ID', 'subscribeunlock');
		}
		if ($this->options['icontact_enable'] == 'on') {
			if (empty($this->options['icontact_appid'])) $errors[] = __('Invalid iContact AppID', 'subscribeunlock');
			if (empty($this->options['icontact_apiusername'])) $errors[] = __('Invalid iContact API Username', 'subscribeunlock');
			if (empty($this->options['icontact_apipassword'])) $errors[] = __('Invalid iContact API Password', 'subscribeunlock');
			if (empty($this->options['icontact_listid'])) $errors[] = __('Invalid iContact List ID', 'subscribeunlock');
		}
		if ($this->options['campaignmonitor_enable'] == 'on') {
			if (empty($this->options['campaignmonitor_api_key'])) $errors[] = __('Invalid Campaign Monitor API Key', 'subscribeunlock');
			if (empty($this->options['campaignmonitor_list_id'])) $errors[] = __('Invalid Campaign Monitor List ID', 'subscribeunlock');
		}
		if ($this->options['getresponse_enable'] == 'on') {
			if (empty($this->options['getresponse_api_key'])) $errors[] = __('Invalid GetResponse API Key', 'subscribeunlock');
			if (empty($this->options['getresponse_campaign_id'])) $errors[] = __('Invalid GetResponse Campaign ID', 'subscribeunlock');
		}
		if ($this->options['aweber_enable'] == 'on') {
			if (empty($this->options['aweber_access_secret'])) $errors[] = __('Invalid AWeber Connection', 'subscribeunlock');
			else if (empty($this->options['aweber_listid'])) $errors[] = __('Invalid AWeber List ID', 'subscribeunlock');
		}
		if ($this->options['mymail_enable'] == 'on') {
			if (empty($this->options['mymail_listid'])) $errors[] = __('Invalid MyMail List ID', 'subscribeunlock');
		}
		if ($this->options['madmimi_enable'] == 'on') {
			if (empty($this->options['madmimi_login'])) $errors[] = __('Invalid Mad Mimi login', 'subscribeunlock');
			if (empty($this->options['madmimi_api_key'])) $errors[] = __('Invalid Mad Mimi API key', 'subscribeunlock');
			if (empty($this->options['madmimi_list_id'])) $errors[] = __('Invalid Mad Mimi list ID', 'subscribeunlock');
		}
		if ($this->options['sendy_enable'] == 'on') {
			if (strlen($this->options['sendy_url']) == 0 || !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->options['sendy_url'])) $errors[] = __('Sendy installation URL must be a valid URL.', 'subscribeunlock');
			if (empty($this->options['sendy_listid'])) $errors[] = __('Invalid Sendy list ID', 'subscribeunlock');
		}
		if ($this->options['benchmark_enable'] == 'on') {
			if (empty($this->options['benchmark_api_key'])) $errors[] = __('Invalid Benchmark Email API key', 'subscribeunlock');
			if (empty($this->options['benchmark_list_id'])) $errors[] = __('Invalid Benchmark Email list ID', 'subscribeunlock');
		}
		if (empty($errors)) return true;
		return $errors;
	}

	function admin_menu() {
		add_menu_page(
			__('Opt-In Locker', 'subscribeunlock')
			, __('Opt-In Locker', 'subscribeunlock')
			, 'manage_options'
			, 'subscribeunlock'
			, array(&$this, 'admin_settings')
		);
		add_submenu_page(
			'subscribeunlock'
			, __('Settings', 'subscribeunlock')
			, __('Settings', 'subscribeunlock')
			, 'manage_options'
			, 'subscribeunlock'
			, array(&$this, 'admin_settings')
		);
		add_submenu_page(
			'subscribeunlock'
			, __('Subscribers', 'subscribeunlock')
			, __('Subscribers', 'subscribeunlock')
			, 'manage_options'
			, 'subscribeunlock-users'
			, array(&$this, 'admin_users')
		);
	}

	function admin_settings() {
		global $wpdb;
		$message = "";
		$errors = $this->check_options();
		if (is_array($errors)) echo "<div class='error'><p>".__('The following error(s) exists:', 'subscribeunlock')."<br />- ".implode("<br />- ", $errors)."</p></div>";
		if (isset($_GET["updated"]) && $_GET["updated"] == "true") {
			$message = '<div class="updated"><p>'.__('Plugin settings successfully <strong>updated</strong>.', 'subscribeunlock').'</p></div>';
		}
		echo '
		<div class="wrap admin_subscribeunlock_wrap">
			<div id="icon-options-general" class="icon32"><br /></div><h2>'.__('Opt-In Content Locker - Settings', 'subscribeunlock').'</h2><br /> 
			'.$message.'
			<form enctype="multipart/form-data" method="post" style="margin: 0px" action="'.admin_url('admin.php').'">
			<div class="postbox-container" style="width: 100%;">
				<div class="metabox-holder">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox subscribeunlock_postbox">
							<!--<div class="handlediv" title="Click to toggle"><br></div>-->
							<h3 class="hndle" style="cursor: default;"><span>'.__('General Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Soft mode', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_soft_mode" name="subscribeunlock_soft_mode" '.($this->options['soft_mode'] == "on" ? 'checked="checked"' : '').'"> '.__('Enable soft mode locker', 'subscribeunlock').'
											<br /><em>'.__('Soft mode makes locked content available for search engines (content is locked by JavaScript).', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Enable thanksgiving', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_thanksgiving_enable" name="subscribeunlock_thanksgiving_enable" '.($this->options['thanksgiving_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Send thanksgiving message', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to send thanksgiving message.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Sender name', 'subscribeunlock').':</th>
										<td><input type="text" id="subscribeunlock_from_name" name="subscribeunlock_from_name" value="'.htmlspecialchars($this->options['from_name'], ENT_QUOTES).'" class="widefat"><br /><em>'.__('Please enter sender name. All messages to buyers are sent using this name as "FROM:" header value.', 'subscribeunlock').'</em></td>
									</tr>
									<tr>
										<th>'.__('Sender e-mail', 'subscribeunlock').':</th>
										<td><input type="text" id="subscribeunlock_from_email" name="subscribeunlock_from_email" value="'.htmlspecialchars($this->options['from_email'], ENT_QUOTES).'" class="widefat"><br /><em>'.__('Please enter sender e-mail. All messages to buyers are sent using this e-mail as "FROM:" header value.', 'subscribeunlock').'</em></td>
									</tr>
									<tr>
										<th>'.__('Thanksgiving e-mail subject', 'subscribeunlock').':</th>
										<td><input type="text" id="subscribeunlock_thanksgiving_email_subject" name="subscribeunlock_thanksgiving_email_subject" value="'.htmlspecialchars($this->options['thanksgiving_email_subject'], ENT_QUOTES).'" class="widefat"><br /><em>'.__('In case of successful subscription, your visitors receive e-mail message which contains thanksgiving message. This is subject field of the message.', 'subscribeunlock').'</em></td>
									</tr>
									<tr>
										<th>'.__('Thanksgiving e-mail body', 'subscribeunlock').':</th>
										<td><textarea id="subscribeunlock_thanksgiving_email_body" name="subscribeunlock_thanksgiving_email_body" class="widefat" style="height: 120px;">'.htmlspecialchars($this->options['thanksgiving_email_body'], ENT_QUOTES).'</textarea><br /><em>'.__('This e-mail message is sent to your visitor in case of successful subscription. You can use the following keywords: {name}, {e-mail}.', 'subscribeunlock').'</em></td>
									</tr>
									<tr>
										<th>'.__('Intro:', 'subscribeunlock').'</th>
										<td>';
		if (function_exists('wp_editor')) {
			wp_editor($this->options['intro'], "subscribeunlock_intro", array('wpautop' => false));
		} else {
			echo '
											<textarea class="widefat" id="subscribeunlock_intro" name="subscribeunlock_intro" style="height: 120px;">'.htmlspecialchars($this->options['intro'], ENT_QUOTES).'</textarea><br />';
		}
		echo '									
											<em>'.__('Please enter content of opt-in box. HTML allowed. Opt-in form is inserted below this content. You can overload this content for each shortcode by inserting "title" attribute like: [sdfile url="http://url-goes-here" title="Content goes here"]', 'subscribeunlock').'</em>
										</td>
									</tr> 
									<tr>
										<th>'.__('"Name" field placeholder', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_placeholder_name" name="subscribeunlock_placeholder_name" value="'.htmlspecialchars($this->options['placeholder_name'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter the placeholder for "Name" input field.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Disable "name" field', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_disable_name" name="subscribeunlock_disable_name" '.($this->options['disable_name'] == "on" ? 'checked="checked"' : '').'"> '.__('Disable "Name" field in opt-in form', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to disable "Name" field in opt-in form.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('"E-mail" field placeholder', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_placeholder_email" name="subscribeunlock_placeholder_email" value="'.htmlspecialchars($this->options['placeholder_email'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter the placeholder for "E-mail" input field.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Extended e-mail validation', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_email_validation" name="subscribeunlock_email_validation" '.($this->options['email_validation'] == "on" ? 'checked="checked"' : '').'"> '.__('Enable extended e-mail address validation', 'subscribeunlock').'
											<br /><em>'.__('If you turn this option on, the plugin will check MX records according to the host provided within the email address. PHP 5 >= 5.3 required!', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('"Subscribe" button label', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_button_label" name="subscribeunlock_button_label" value="'.htmlspecialchars($this->options['button_label'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter the label for "Subscribe" button.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Terms & Conditions', 'subscribeunlock').':</th>
										<td><textarea id="subscribeunlock_terms" name="subscribeunlock_terms" class="widefat" style="height: 120px;">'.htmlspecialchars($this->options['terms'], ENT_QUOTES).'</textarea><br /><em>'.__('Your customers must be agree with Terms & Conditions before donating. Leave this field blank if you do not need Terms & Conditions box to be shown.', 'subscribeunlock').'</em></td>
									</tr>
									<tr>
										<th>'.__('Redirecting message', 'subscribeunlock').':</th>
										<td><input type="text" id="subscribeunlock_thanks_message" name="subscribeunlock_thanks_message" value="'.htmlspecialchars($this->options['thanks_message'], ENT_QUOTES).'" class="widefat"><br /><em>'.__('This message appears in case of successful subscription (regular locker mode only).', 'subscribeunlock').'</em></td>
									</tr>
									<tr>
										<th>'.__('Google Analytics tracking', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_ga_tracking" name="subscribeunlock_ga_tracking" '.($this->options['ga_tracking'] == "on" ? 'checked="checked"' : '').'"> '.__('Enable Google Analytics tracking', 'subscribeunlock').'
											<br /><em>'.__('Send subscribtion event to Google Analytics. Google Analytics must be installed on your website.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('CSV column separator', 'subscribeunlock').':</th>
										<td>
											<select id="subscribeunlock-csv_separator" name="subscribeunlock_csv_separator">
												<option value=";"'.($this->options['csv_separator'] == ';' ? ' selected="selected"' : '').'>'.__('Semicolon - ";"', 'subscribeunlock').'</option>
												<option value=","'.($this->options['csv_separator'] == ',' ? ' selected="selected"' : '').'>'.__('Comma - ","', 'subscribeunlock').'</option>
												<option value="tab"'.($this->options['csv_separator'] == 'tab' ? ' selected="selected"' : '').'>'.__('Tab', 'subscribeunlock').'</option>
											</select>
											<br /><em>'.__('Please select CSV column separator.', 'subscribeunlock').'</em></td>
									</tr>
								</table>
								<div class="alignright">
								<input type="hidden" name="action" value="subscribeunlock_update_options" />
								<input type="hidden" name="subscribeunlock_version" value="'.SUBSCRIBEUNLOCK_VERSION.'" />
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Design Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Box background color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_box_color" value="'.esc_attr($this->options['box_color']).'" placeholder=""> 
											<br /><em>'.__('Set main box background color. Leave empty for transparent background.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Box border color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_box_border_color" value="'.esc_attr($this->options['box_border_color']).'" placeholder=""> 
											<br /><em>'.__('Set main box border color. Leave empty for transparent border.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Font color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_box_font_color" value="'.esc_attr($this->options['box_font_color']).'" placeholder="">
											<br /><em>'.__('Set font color. Leave empty for default font color.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Font size', 'subscribeunlock').':</th>
										<td style="vertical-align: middle;">
											<input type="text" class="ic_input_number" name="subscribeunlock_box_font_size" value="'.esc_attr($this->options['box_font_size']).'" placeholder="pixels"> '.__('pixels', 'subscribeunlock').'
											<br /><em>'.__('Set font size (pixels).', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Input field border color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_input_border_color" value="'.esc_attr($this->options['input_border_color']).'" placeholder="">
											<br /><em>'.__('Set border color of "Name" and "E-mail" input fields.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Input field background color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_input_background_color" value="'.esc_attr($this->options['input_background_color']).'" placeholder="">
											<br /><em>'.__('Set background color of "Name" and "E-mail" input fields.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Input field background opacity', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="ic_input_number" name="subscribeunlock_input_background_opacity" value="'.esc_attr($this->options['input_background_opacity']).'" placeholder="[0...1]">
											<br /><em>'.__('Set background opacity of "Name" and "E-mail" input fields. The value must be in a range [0...1].', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Input field font color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_input_font_color" value="'.esc_attr($this->options['input_font_color']).'" placeholder="">
											<br /><em>'.__('Set font color of "Name" and "E-mail" input fields.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Input field font size', 'subscribeunlock').':</th>
										<td style="vertical-align: middle;">
											<input type="text" class="ic_input_number" name="subscribeunlock_input_font_size" value="'.esc_attr($this->options['input_font_size']).'" placeholder="pixels"> '.__('pixels', 'subscribeunlock').'
											<br /><em>'.__('Set font size (pixels) of "Name" and "E-mail" input fields.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Button color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_button_color" value="'.esc_attr($this->options['button_color']).'" placeholder=""> 
											<br /><em>'.__('Set button color.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Button font color', 'subscribeunlock').':</th>
										<td>
											<input type="text" class="subscribeunlock-color ic_input_number" name="subscribeunlock_button_font_color" value="'.esc_attr($this->options['button_font_color']).'" placeholder="">
											<br /><em>'.__('Set button font color.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Button font size', 'subscribeunlock').':</th>
										<td style="vertical-align: middle;">
											<input type="text" class="ic_input_number" name="subscribeunlock_button_font_size" value="'.esc_attr($this->options['button_font_size']).'" placeholder="pixels"> '.__('pixels', 'subscribeunlock').'
											<br /><em>'.__('Set font size (pixels) of the button.', 'subscribeunlock').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('MailChimp Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable MailChimp', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_mailchimp_enable" name="subscribeunlock_mailchimp_enable" '.($this->options['mailchimp_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to MailChimp', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to MailChimp. <strong>CURL required!</strong>', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('MailChimp API Key', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_mailchimp_api_key" name="subscribeunlock_mailchimp_api_key" value="'.htmlspecialchars($this->options['mailchimp_api_key'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter your MailChimp API Key. You can get it <a href="https://admin.mailchimp.com/account/api-key-popup" target="_blank">here</a>.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_mailchimp_list_id" name="subscribeunlock_mailchimp_list_id" value="'.htmlspecialchars($this->options['mailchimp_list_id'], ENT_QUOTES).'" class="widefat">
											<br /><em>'.__('Enter your List ID. You can get it <a href="https://admin.mailchimp.com/lists/" target="_blank">here</a> (click <strong>Settings</strong>).', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Double opt-in', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_mailchimp_double" name="subscribeunlock_mailchimp_double" '.($this->options['mailchimp_double'] == "on" ? 'checked="checked"' : '').'"> '.__('Ask users to confirm their subscription', 'subscribeunlock').'
											<br /><em>'.__('Control whether a double opt-in confirmation message is sent.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Send Welcome', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_mailchimp_welcome" name="subscribeunlock_mailchimp_welcome" '.($this->options['mailchimp_welcome'] == "on" ? 'checked="checked"' : '').'"> '.__('Send Lists Welcome message', 'subscribeunlock').'
											<br /><em>'.__('If your <strong>Double opt-in</strong> is disabled and this is enabled, MailChimp will send your lists Welcome Email if this subscribe succeeds. If <strong>Double opt-in</strong> is enabled, this has no effect.', 'subscribeunlock').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('iContact Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable iContact', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_icontact_enable" name="subscribeunlock_icontact_enable" '.($this->options['icontact_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to iContact', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to iContact.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('AppID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_icontact_appid" name="subscribeunlock_icontact_appid" value="'.htmlspecialchars($this->options['icontact_appid'], ENT_QUOTES).'" class="widefat subscribeunlock-input" onchange="subscribeunlock_icontact_handler();">
											<br /><em>'.__('Obtained when you <a href="http://developer.icontact.com/documentation/register-your-app/" target="_blank">Register the API application</a>. This identifier is used to uniquely identify your application.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Username', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_icontact_apiusername" name="subscribeunlock_icontact_apiusername" value="'.htmlspecialchars($this->options['icontact_apiusername'], ENT_QUOTES).'" class="widefat subscribeunlock-input" onchange="subscribeunlock_icontact_handler();">
											<br /><em>'.__('The iContact username for logging into your iContact account.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Password', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_icontact_apipassword" name="subscribeunlock_icontact_apipassword" value="'.htmlspecialchars($this->options['icontact_apipassword'], ENT_QUOTES).'" class="widefat subscribeunlock-input" onchange="subscribeunlock_icontact_handler();">
											<br /><em>'.__('The API application password set when the application was registered. This API password is used as input when your application authenticates to the API. This password is not the same as the password you use to log in to iContact.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_icontact_listid" name="subscribeunlock_icontact_listid" value="'.esc_attr($this->options['icontact_listid']).'" class="widefat subscribeunlock-input">
											<br /><em>'.__('Enter your List ID. You can get List ID from', 'subscribeunlock').' <a href="'.admin_url('admin.php').'?action=subscribeunlock-icontact-lists&appid='.base64_encode($this->options['icontact_appid']).'&user='.base64_encode($this->options['icontact_apiusername']).'&pass='.base64_encode($this->options['icontact_apipassword']).'" class="thickbox" id="subscribeunlock_icontact_lists" title="'.__('Available Lists', 'subscribeunlock').'">'.__('this table', 'subscribeunlock').'</a>.</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('GetResponse Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable GetResponse', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_getresponse_enable" name="subscribeunlock_getresponse_enable" '.($this->options['getresponse_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to GetResponse', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to GetResponse.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Key', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_getresponse_api_key" name="subscribeunlock_getresponse_api_key" value="'.esc_attr($this->options['getresponse_api_key']).'" class="widefat subscribeunlock-input" onchange="subscribeunlock_getresponse_handler();">
											<br /><em>'.__('Enter your GetResponse API Key. You can get your API Key <a href="https://app.getresponse.com/my_api_key.html" target="_blank">here</a>.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Campaign ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_getresponse_campaign_id" name="subscribeunlock_getresponse_campaign_id" value="'.esc_attr($this->options['getresponse_campaign_id']).'" class="widefat subscribeunlock-input">
											<br /><em>'.__('Enter your Campaign ID. You can get Campaign ID from', 'subscribeunlock').' <a href="'.admin_url('admin.php').'?action=subscribeunlock-getresponse-campaigns&key='.base64_encode($this->options['getresponse_api_key']).'" class="thickbox" id="subscribeunlock_getresponse_campaigns" title="'.__('Available Campaigns', 'subscribeunlock').'">'.__('this table', 'subscribeunlock').'</a>.</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Campaign Monitor Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable Campaign Monitor', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_campaignmonitor_enable" name="subscribeunlock_campaignmonitor_enable" '.($this->options['campaignmonitor_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to Campaign Monitor', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to Campaign Monitor. <strong>CURL required!</strong>', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Key', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_campaignmonitor_api_key" name="subscribeunlock_campaignmonitor_api_key" value="'.esc_attr($this->options['campaignmonitor_api_key']).'" class="widefat">
											<br /><em>'.__('Enter your Campaign Monitor API Key. You can get your API Key from the Account Settings page when logged into your Campaign Monitor account.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_campaignmonitor_list_id" name="subscribeunlock_campaignmonitor_list_id" value="'.esc_attr($this->options['campaignmonitor_list_id']).'" class="widefat">
											<br /><em>'.__('Enter your List ID. You can get List ID from the list editor page when logged into your Campaign Monitor account.', 'subscribeunlock').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Mad Mimi Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable Mad Mimi', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_madmimi_enable" name="subscribeunlock_madmimi_enable" '.($this->options['madmimi_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to Mad Mimi', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to Mad Mimi.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Username/E-mail', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_madmimi_login" name="subscribeunlock_madmimi_login" value="'.esc_attr($this->options['madmimi_login']).'" class="widefat subscribeunlock-input" onchange="subscribeunlock_madmimi_handler();">
											<br /><em>'.__('Enter your Mad Mimi username/e-mail.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Key', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_madmimi_api_key" name="subscribeunlock_madmimi_api_key" value="'.esc_attr($this->options['madmimi_api_key']).'" class="widefat subscribeunlock-input" onchange="subscribeunlock_madmimi_handler();">
											<br /><em>'.__('Enter your Mad Mimi API Key. You can get your API Key <a href="https://madmimi.com/user/edit?account_info_tabs=account_info_personal" target="_blank">here</a>.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_madmimi_list_id" name="subscribeunlock_madmimi_list_id" value="'.esc_attr($this->options['madmimi_list_id']).'" class="widefat subscribeunlock-input">
											<br /><em>'.__('Enter your List ID. You can get List ID from', 'subscribeunlock').' <a href="'.admin_url('admin.php').'?action=subscribeunlock-madmimi-lists&login='.base64_encode($this->options['madmimi_login']).'&key='.base64_encode($this->options['madmimi_api_key']).'" class="thickbox" id="subscribeunlock_madmimi_lists" title="'.__('Available Lists', 'subscribeunlock').'">'.__('this table', 'subscribeunlock').'</a>.</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Sendy Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable Sendy', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_sendy_enable" name="subscribeunlock_sendy_enable" '.($this->options['sendy_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to Sendy', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to Sendy.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Installation URL', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_sendy_url" name="subscribeunlock_sendy_url" value="'.esc_attr($this->options['sendy_url']).'" class="widefat">
											<br /><em>'.__('Enter your Sendy installation URL (without the trailing slash).', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_sendy_listid" name="subscribeunlock_sendy_listid" value="'.esc_attr($this->options['sendy_listid']).'" class="widefat">
											<br /><em>'.__('Enter your List ID. This encrypted & hashed id can be found under View all lists section named ID.', 'subscribeunlock').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('Benchmark Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable Benchmark', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_benchmark_enable" name="subscribeunlock_benchmark_enable" '.($this->options['benchmark_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to Benchmark Email', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to Benchmark Email.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('API Key', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_benchmark_api_key" name="subscribeunlock_benchmark_api_key" value="'.esc_attr($this->options['benchmark_api_key']).'" class="widefat" onchange="subscribeunlock_benchmark_handler();">
											<br /><em>'.__('Enter your Benchmark Email API Key. You can get your API Key <a href="https://ui.benchmarkemail.com/EditSetting" target="_blank">here</a>.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<input type="text" id="subscribeunlock_benchmark_list_id" name="subscribeunlock_benchmark_list_id" value="'.esc_attr($this->options['benchmark_list_id']).'" class="widefat">
											<br /><em>'.__('Enter your List ID. You can get List ID from', 'subscribeunlock').' <a href="'.admin_url('admin.php').'?action=subscribeunlock-benchmark-lists&key='.base64_encode($this->options['benchmark_api_key']).'" class="thickbox" id="subscribeunlock_benchmark_lists" title="'.__('Available Lists', 'subscribeunlock').'">'.__('this table', 'subscribeunlock').'</a>.</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Double opt-in', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_benchmark_double" name="subscribeunlock_benchmark_double" '.($this->options['benchmark_double'] == "on" ? 'checked="checked"' : '').'"> '.__('Ask users to confirm their subscription', 'subscribeunlock').'
											<br /><em>'.__('Control whether a double opt-in confirmation message is sent.', 'subscribeunlock').'</em>
										</td>
									</tr>
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('AWeber Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">
									<tr>
										<th>'.__('Enable AWeber', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_aweber_enable" name="subscribeunlock_aweber_enable" '.($this->options['aweber_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to AWeber', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to AWeber.', 'subscribeunlock').'</em>
										</td>
									</tr>';
		$account = null;
		if ($this->options['aweber_access_secret']) {
			if (!class_exists('AWeberAPI')) {
				require_once(dirname(__FILE__).'/aweber_api/aweber_api.php');
			}
			try {
				$aweber = new AWeberAPI($this->options['aweber_consumer_key'], $this->options['aweber_consumer_secret']);
				$account = $aweber->getAccount($this->options['aweber_access_key'], $this->options['aweber_access_secret']);
			} catch (AWeberException $e) {
				$account = null;
			}
		}
		if (!$account) {
			echo '
									<tbody id="subscribeunlock-aweber-group">
										<tr>
											<th>'.__('Authorization code', 'subscribeunlock').':</th>
											<td>
												<input type="text" id="subscribeunlock_aweber_oauth_id" value="" class="widefat subscribeunlock-input" placeholder="AWeber authorization code">
												<br />Get your authorization code <a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/'.SUBSCRIBEUNLOCK_AWEBER_APPID.'">'.__('here', 'subscribeunlock').'</a>.
											</td>
										</tr>
										<tr>
											<th></th>
											<td style="vertical-align: middle;">
												<input type="button" class="subscribeunlock_button button-secondary" value="'.__('Make Connection', 'subscribeunlock').'" onclick="return subscribeunlock_aweber_connect();" >
												<img id="subscribeunlock-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
											</td>
										</tr>
									</tbody>';
		} else {
			echo '
									<tbody id="subscribeunlock-aweber-group">
										<tr>
											<th>'.__('Connected', 'subscribeunlock').':</th>
											<td>
												<input type="button" class="subscribeunlock_button button-secondary" value="'.__('Disconnect', 'subscribeunlock').'" onclick="return subscribeunlock_aweber_disconnect();" >
												<img id="subscribeunlock-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
												<br /><em>'.__('Click the button to disconnect.', 'subscribeunlock').'</em>
											</td>
										</tr>
										<tr>
											<th>'.__('List ID', 'subscribeunlock').':</th>
											<td>
												<select name="subscribeunlock_aweber_listid" style="width: 40%;">
													<option value="">'.__('--- Select List ID ---', 'subscribeunlock').'</option>';
				$lists = $account->lists;
				foreach ($lists as $list) {
					echo '
													<option value="'.$list->id.'"'.($list->id == $this->options['aweber_listid'] ? ' selected="selected"' : '').'>'.$list->name.'</option>';
				}
				echo '
												</select>
												<br /><em>'.__('Select your List ID.', 'subscribeunlock').'</em>
											</td>
										</tr>
									</tbody>';
		}
		echo '
								</table>
								<div id="subscribeunlock-aweber-message"></div>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>';
						
		if (function_exists('mymail_subscribe') || function_exists('mymail')) {
			echo '
						<div class="postbox subscribeunlock_postbox">
							<h3 class="hndle" style="cursor: default;"><span>'.__('MyMail Settings', 'subscribeunlock').'</span></h3>
							<div class="inside">
								<table class="subscribeunlock_useroptions">';
			if (function_exists('mymail')) {
				$lists = mymail('lists')->get();
				$create_list_url = 'edit.php?post_type=newsletter&page=mymail_lists';
			} else {
				$lists = get_terms('newsletter_lists', array('hide_empty' => false));
				$create_list_url = 'edit-tags.php?taxonomy=newsletter_lists&post_type=newsletter';
			}
			if (sizeof($lists) == 0) {
				echo '
									<tr>
										<th>'.__('Enable MyMail', 'subscribeunlock').':</th>
										<td>'.__('Please <a href="'.$create_list_url.'">create</a> at least one list.', 'subscribeunlock').'</td>
									</tr>';
			} else {
				echo '
									<tr>
										<th>'.__('Enable MyMail', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_mymail_enable" name="subscribeunlock_mymail_enable" '.($this->options['mymail_enable'] == "on" ? 'checked="checked"' : '').'"> '.__('Submit contact details to MyMail', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to submit contact details to MyMail.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('List ID', 'subscribeunlock').':</th>
										<td>
											<select name="subscribeunlock_mymail_listid" class="ic_input_m">';
				foreach ($lists as $list) {
					if (function_exists('mymail')) $id = $list->ID;
					else $id = $list->term_id;
					echo '
												<option value="'.$id.'"'.($id == $this->options['mymail_listid'] ? ' selected="selected"' : '').'>'.$list->name.'</option>';
				}
				echo '
											</select>
											<br /><em>'.__('Select your List ID.', 'subscribeunlock').'</em>
										</td>
									</tr>
									<tr>
										<th>'.__('Double Opt-In', 'subscribeunlock').':</th>
										<td>
											<input type="checkbox" id="subscribeunlock_mymail_double" name="subscribeunlock_mymail_double" '.($this->options['mymail_double'] == "on" ? 'checked="checked"' : '').'"> '.__('Enable Double Opt-In', 'subscribeunlock').'
											<br /><em>'.__('Please tick checkbox if you want to enable double opt-in feature.', 'subscribeunlock').'</em>
										</td>
									</tr>';
			}
			echo '
								</table>
								<div class="alignright">
								<input type="submit" class="subscribeunlock_button button-primary" name="submit" value="'.__('Update Settings', 'subscribeunlock').'">
								</div>
							</div>
						</div>';
		}
		echo '
					</div>
				</div>
			</div>
			</form>
			<script type="text/javascript">
				function subscribeunlock_getresponse_handler() {
					jQuery("#subscribeunlock_getresponse_campaigns").attr("href", "'.admin_url('admin.php').'?action=subscribeunlock-getresponse-campaigns&key="+subscribeunlock_encode64(jQuery("#subscribeunlock_getresponse_api_key").val()));
				}
				function subscribeunlock_icontact_handler() {
					jQuery("#subscribeunlock_icontact_lists").attr("href", "'.admin_url('admin.php').'?action=subscribeunlock-icontact-lists&appid="+subscribeunlock_encode64(jQuery("#subscribeunlock_icontact_appid").val())+"&user="+subscribeunlock_encode64(jQuery("#subscribeunlock_icontact_apiusername").val())+"&pass="+subscribeunlock_encode64(jQuery("#subscribeunlock_icontact_apipassword").val()));
				}
				function subscribeunlock_madmimi_handler() {
					jQuery("#subscribeunlock_madmimi_lists").attr("href", "'.admin_url('admin.php').'?action=subscribeunlock-madmimi-lists&login="+subscribeunlock_encode64(jQuery("#subscribeunlock_madmimi_login").val())+"&key="+subscribeunlock_encode64(jQuery("#subscribeunlock_madmimi_api_key").val()));
				}
				function subscribeunlock_benchmark_handler() {
					jQuery("#subscribeunlock_benchmark_lists").attr("href", "'.admin_url('admin.php').'?action=subscribeunlock-benchmark-lists&key="+subscribeunlock_encode64(jQuery("#subscribeunlock_benchmark_api_key").val()));
				}
				function subscribeunlock_aweber_connect() {
					jQuery("#subscribeunlock-aweber-loading").fadeIn(350);
					jQuery("#subscribeunlock-aweber-message").slideUp(350);
					var data = {action: "subscribeunlock_aweber_connect", subscribeunlock_aweber_oauth_id: jQuery("#subscribeunlock_aweber_oauth_id").val()};
					jQuery.post("'.admin_url('admin-ajax.php').'", data, function(return_data) {
						jQuery("#subscribeunlock-aweber-loading").fadeOut(350);
						try {
							//alert(return_data);
							var data = jQuery.parseJSON(return_data);
							var status = data.status;
							if (status == "OK") {
								jQuery("#subscribeunlock-aweber-group").slideUp(350, function() {
									jQuery("#subscribeunlock-aweber-group").html(data.html);
									jQuery("#subscribeunlock-aweber-group").slideDown(350);
								});
							} else if (status == "ERROR") {
								jQuery("#subscribeunlock-aweber-message").html(data.message);
								jQuery("#subscribeunlock-aweber-message").slideDown(350);
							} else {
								jQuery("#subscribeunlock-aweber-message").html("Service is not available.");
								jQuery("#subscribeunlock-aweber-message").slideDown(350);
							}
						} catch(error) {
							jQuery("#subscribeunlock-aweber-message").html("Service is not available.");
							jQuery("#subscribeunlock-aweber-message").slideDown(350);
						}
					});
					return false;
				}
				function subscribeunlock_aweber_disconnect() {
					jQuery("#subscribeunlock-aweber-loading").fadeIn(350);
					var data = {action: "subscribeunlock_aweber_disconnect"};
						jQuery.post("'.admin_url('admin-ajax.php').'", data, function(return_data) {
						jQuery("#subscribeunlock-aweber-loading").fadeOut(350);
						try {
							//alert(return_data);
							var data = jQuery.parseJSON(return_data);
							var status = data.status;
							if (status == "OK") {
								jQuery("#subscribeunlock-aweber-group").slideUp(350, function() {
									jQuery("#subscribeunlock-aweber-group").html(data.html);
									jQuery("#subscribeunlock-aweber-group").slideDown(350);
								});
							} else if (status == "ERROR") {
								jQuery("#subscribeunlock-aweber-message").html(data.message);
								jQuery("#subscribeunlock-aweber-message").slideDown(350);
							} else {
								jQuery("#subscribeunlock-aweber-message").html("Service is not available.");
								jQuery("#subscribeunlock-aweber-message").slideDown(350);
							}
						} catch(error) {
							jQuery("#subscribeunlock-aweber-message").html("Service is not available.");
							jQuery("#subscribeunlock-aweber-message").slideDown(350);
						}
					});
					return false;
				}
				jQuery(document).ready(function(){
					jQuery(".subscribeunlock-color").wpColorPicker();
				});
			</script>			
		</div>';
	}

	function aweber_connect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			if (!isset($_POST['subscribeunlock_aweber_oauth_id']) || empty($_POST['subscribeunlock_aweber_oauth_id'])) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = __('Authorization Code not found.', 'subscribeunlock');
				echo json_encode($return_object);
				exit;
			}
			$code = trim(stripslashes($_POST['subscribeunlock_aweber_oauth_id']));
			if (!class_exists('AWeberAPI')) {
				require_once(dirname(__FILE__).'/aweber_api/aweber_api.php');
			}
			$account = null;
			try {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID($code);
			} catch (AWeberAPIException $exc) {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
			} catch (AWeberOAuthDataMissing $exc) {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
			} catch (AWeberException $exc) {
				list($consumer_key, $consumer_secret, $access_key, $access_secret) = null;
			}
			if (!$access_secret) {
				$return_object = array();
				$return_object['status'] = 'ERROR';
				$return_object['message'] = __('Invalid Authorization Code!', 'subscribeunlock');
				echo json_encode($return_object);
				exit;
			} else {
				try {
					$aweber = new AWeberAPI($consumer_key, $consumer_secret);
					$account = $aweber->getAccount($access_key, $access_secret);
				} catch (AWeberException $e) {
					$return_object = array();
					$return_object['status'] = 'ERROR';
					$return_object['message'] = __('Can not access AWeber account!', 'subscribeunlock');
					echo json_encode($return_object);
					exit;
				}
			}
			$this->options['aweber_consumer_key'] = $consumer_key;
			$this->options['aweber_consumer_secret'] = $consumer_secret;
			$this->options['aweber_access_key'] = $access_key;
			$this->options['aweber_access_secret'] = $access_secret;
			$this->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = '
										<tr>
											<th>'.__('Connected', 'subscribeunlock').':</th>
											<td>
												<input type="button" class="subscribeunlock_button button-secondary" value="'.__('Disconnect', 'subscribeunlock').'" onclick="return subscribeunlock_aweber_disconnect();" >
												<img id="subscribeunlock-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
												<br /><em>'.__('Click the button to disconnect.', 'subscribeunlock').'</em>
											</td>
										</tr>
										<tr>
											<th>'.__('List ID', 'subscribeunlock').':</th>
											<td>
												<select name="subscribeunlock_aweber_listid" style="width: 40%;">
													<option value="">'.__('--- Select List ID ---', 'subscribeunlock').'</option>';
				$lists = $account->lists;
				foreach ($lists as $list) {
					$return_object['html'] .= '
													<option value="'.$list->id.'"'.($list->id == $this->options['aweber_listid'] ? ' selected="selected"' : '').'>'.$list->name.'</option>';
				}
				$return_object['html'] .= '
												</select>
												<br /><em>'.__('Select your List ID.', 'subscribeunlock').'</em>
											</td>
										</tr>';
			echo json_encode($return_object);
			exit;
		}
		exit;
	}
	
	function aweber_disconnect() {
		global $wpdb;
		if (current_user_can('manage_options')) {
			$this->options['aweber_consumer_key'] = '';
			$this->options['aweber_consumer_secret'] = '';
			$this->options['aweber_access_key'] = '';
			$this->options['aweber_access_secret'] = '';
			$this->update_options();
			$return_object = array();
			$return_object['status'] = 'OK';
			$return_object['html'] = '
					<tr>
						<th>'.__('Authorization code', 'subscribeunlock').':</th>
						<td>
							<input type="text" id="subscribeunlock_aweber_oauth_id" value="" class="widefat subscribeunlock-input" placeholder="AWeber authorization code">
							<br />Get your authorization code <a target="_blank" href="https://auth.aweber.com/1.0/oauth/authorize_app/'.SUBSCRIBEUNLOCK_AWEBER_APPID.'">'.__('here', 'subscribeunlock').'</a>.
						</td>
					</tr>
					<tr>
						<th></th>
						<td style="vertical-align: middle;">
							<input type="button" class="subscribeunlock_button button-secondary" value="'.__('Make Connection', 'subscribeunlock').'" onclick="return subscribeunlock_aweber_connect();" >
							<img id="subscribeunlock-aweber-loading" src="'.plugins_url('/images/loading.gif', __FILE__).'">
						</td>
					</tr>';
			echo json_encode($return_object);
			exit;
		}
		exit;
	}

	function admin_users() {
		global $wpdb;

		if (isset($_GET["s"])) $search_query = trim(stripslashes($_GET["s"]));
		else $search_query = "";
		
		$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."sp_users WHERE deleted = '0'".((strlen($search_query) > 0) ? " AND (name LIKE '%".addslashes($search_query)."%' OR email LIKE '%".addslashes($search_query)."%')" : ""), ARRAY_A);
		$total = $tmp["total"];
		$totalpages = ceil($total/SUBSCRIBEUNLOCK_RECORDS_PER_PAGE);
		if ($totalpages == 0) $totalpages = 1;
		if (isset($_GET["p"])) $page = intval($_GET["p"]);
		else $page = 1;
		if ($page < 1 || $page > $totalpages) $page = 1;
		$switcher = $this->page_switcher(get_bloginfo("wpurl")."/wp-admin/admin.php?page=subscribeunlock-users".((strlen($search_query) > 0) ? "&s=".rawurlencode($search_query) : ""), $page, $totalpages);

		$sql = "SELECT * FROM ".$wpdb->prefix."sp_users WHERE deleted = '0'".((strlen($search_query) > 0) ? " AND (name LIKE '%".addslashes($search_query)."%' OR email LIKE '%".addslashes($search_query)."%')" : "")." ORDER BY registered DESC LIMIT ".(($page-1)*SUBSCRIBEUNLOCK_RECORDS_PER_PAGE).", ".SUBSCRIBEUNLOCK_RECORDS_PER_PAGE;
		$rows = $wpdb->get_results($sql, ARRAY_A);
		if (isset($_GET['deleted'])) $message = "<div class='updated'><p>".__('Record successfully deleted!', 'subscribeunlock')."</p></div>";
		else $message = '';

		print ('
			<div class="wrap admin_subscribeunlock_wrap">
				<div id="icon-users" class="icon32"><br /></div><h2>'.__('Opt-In Content Locker - Users', 'subscribeunlock').'</h2><br />
				'.$message.'
				<form action="'.admin_url('admin.php').'" method="get" style="margin-bottom: 10px;">
				<input type="hidden" name="page" value="subscribeunlock-users" />
				'.__('Search:', 'subscribeunlock').' <input type="text" name="s" value="'.htmlspecialchars($search_query, ENT_QUOTES).'">
				<input type="submit" class="button-secondary action" value="'.__('Search', 'subscribeunlock').'" />
				'.((strlen($search_query) > 0) ? '<input type="button" class="button-secondary action" value="'.__('Reset search results', 'subscribeunlock').'" onclick="window.location.href=\''.admin_url('admin.php').'?page=subscribeunlock-users\';" />' : '').'
				</form>
				<div class="subscribeunlock_buttons"><a class="button" href="'.admin_url('admin.php').'?action=subscribeunlock-csv">'.__('CSV Export', 'subscribeunlock').'</a></div>
				<div class="subscribeunlock_pageswitcher">'.$switcher.'</div>
				<table class="subscribeunlock_users">
				<tr>
					<th>'.__('Name', 'subscribeunlock').'</th>
					<th>'.__('E-mail', 'subscribeunlock').'</th>
					<th style="width: 120px;">'.__('Registered', 'subscribeunlock').'</th>
					<th style="width: 25px;"></th>
				</tr>');
		if (sizeof($rows) > 0) {
			foreach ($rows as $row) {
				print ('
				<tr>
					<td>'.($row['name'] == '' ? '-' : esc_attr($row['name'])).'</td>
					<td>'.esc_attr($row['email']).'</td>
					<td>'.date("Y-m-d H:i", $row['registered']).'</td>
					<td style="text-align: center;">
						<a href="'.admin_url('admin.php').'?action=subscribeunlock-delete&id='.$row['id'].'" title="'.__('Delete record', 'subscribeunlock').'" onclick="return subscribeunlock_submitOperation();"><img src="'.plugins_url('/images/delete.png', __FILE__).'" alt="'.__('Delete record', 'subscribeunlock').'" border="0"></a>
					</td>
				</tr>
				');
			}
		} else {
			print ('
				<tr><td colspan="4" style="padding: 20px; text-align: center;">'.((strlen($search_query) > 0) ? __('No results found for', 'subscribeunlock').' "<strong>'.htmlspecialchars($search_query, ENT_QUOTES).'</strong>"' : __('List is empty.', 'subscribeunlock')).'</td></tr>');
		}
		print ('
				</table>
				<div class="subscribeunlock_buttons">
					<a class="button" href="'.admin_url('admin.php').'?action=subscribeunlock-deleteall" onclick="return subscribeunlock_submitOperation();">'.__('Delete All', 'subscribeunlock').'</a>
					<a class="button" href="'.admin_url('admin.php').'?action=subscribeunlock-csv">'.__('CSV Export', 'subscribeunlock').'</a>
				</div>
				<div class="subscribeunlock_pageswitcher">'.$switcher.'</div>
				<div class="subscribeunlock_legend">
				<strong>'.__('Legend', 'subscribeunlock').':</strong>
					<p><img src="'.plugins_url('/images/delete.png', __FILE__).'" alt="'.__('Delete record', 'subscribeunlock').'" border="0"> '.__('Delete record', 'subscribeunlock').'</p>
				</div>
			</div>
			<script type="text/javascript">
				function subscribeunlock_submitOperation() {
					var answer = confirm("'.__('Do you really want to continue?', 'subscribeunlock').'")
					if (answer) return true;
					else return false;
				}
			</script>');
	}
	
	function admin_request_handler() {
		global $wpdb;
		if (!empty($_POST['action'])) {
			switch($_POST['action']) {
				case 'subscribeunlock_update_options':
					$this->populate_options();
					if (isset($_POST["subscribeunlock_soft_mode"])) $this->options['soft_mode'] = "on";
					else $this->options['soft_mode'] = "off";
					if (isset($_POST["subscribeunlock_thanksgiving_enable"])) $this->options['thanksgiving_enable'] = "on";
					else $this->options['thanksgiving_enable'] = "off";
					if (isset($_POST["subscribeunlock_mailchimp_double"])) $this->options['mailchimp_double'] = "on";
					else $this->options['mailchimp_double'] = "off";
					if (isset($_POST["subscribeunlock_mailchimp_welcome"])) $this->options['mailchimp_welcome'] = "on";
					else $this->options['mailchimp_welcome'] = "off";
					if (isset($_POST["subscribeunlock_mailchimp_enable"])) $this->options['mailchimp_enable'] = "on";
					else $this->options['mailchimp_enable'] = "off";
					if (isset($_POST["subscribeunlock_icontact_enable"])) $this->options['icontact_enable'] = "on";
					else $this->options['icontact_enable'] = "off";
					if (isset($_POST["subscribeunlock_campaignmonitor_enable"])) $this->options['campaignmonitor_enable'] = "on";
					else $this->options['campaignmonitor_enable'] = "off";
					if (isset($_POST["subscribeunlock_getresponse_enable"])) $this->options['getresponse_enable'] = "on";
					else $this->options['getresponse_enable'] = "off";
					if (isset($_POST["subscribeunlock_madmimi_enable"])) $this->options['madmimi_enable'] = "on";
					else $this->options['madmimi_enable'] = "off";
					if (isset($_POST["subscribeunlock_disable_name"])) $this->options['disable_name'] = "on";
					else $this->options['disable_name'] = "off";
					if (isset($_POST["subscribeunlock_aweber_enable"])) $this->options['aweber_enable'] = "on";
					else $this->options['aweber_enable'] = "off";
					if (isset($_POST["subscribeunlock_mymail_enable"])) $this->options['mymail_enable'] = "on";
					else $this->options['mymail_enable'] = "off";
					if (isset($_POST["subscribeunlock_mymail_double"])) $this->options['mymail_double'] = "on";
					else $this->options['mymail_double'] = "off";
					if (isset($_POST['subscribeunlock_email_validation'])) $this->options['email_validation'] = 'on';
					else $this->options['email_validation'] = 'off';
					if (isset($_POST["subscribeunlock_sendy_enable"])) $this->options['sendy_enable'] = "on";
					else $this->options['sendy_enable'] = "off";
					if (isset($_POST["subscribeunlock_benchmark_enable"])) $this->options['benchmark_enable'] = "on";
					else $this->options['benchmark_enable'] = "off";
					if (isset($_POST["subscribeunlock_benchmark_double"])) $this->options['benchmark_double'] = "on";
					else $this->options['benchmark_double'] = "off";
					if (isset($_POST['subscribeunlock_ga_tracking'])) $this->options['ga_tracking'] = 'on';
					else $this->options['ga_tracking'] = 'off';

					$this->update_options();
					$errors = $this->check_options();
					if (!is_array($errors)) header('Location: '.admin_url('admin.php').'?page=subscribeunlock&updated=true');
					else header('Location: '.admin_url('admin.php').'?page=subscribeunlock');
					die();
					break;
				default:
					break;
			}
		}
		if (isset($_GET['action'])) {
			switch($_GET['action']) {
				case 'subscribeunlock-delete':
					$id = intval($_GET["id"]);
					$user_details = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."sp_users WHERE id = '".$id."' AND deleted = '0'", ARRAY_A);
					if (intval($user_details["id"]) == 0) {
						header('Location: '.admin_url('admin.php').'?page=subscribeunlock-users');
						die();
					}
					$sql = "UPDATE ".$wpdb->prefix."sp_users SET deleted = '1' WHERE id = '".$id."'";
					if ($wpdb->query($sql) !== false) {
						header('Location: '.admin_url('admin.php').'?page=subscribeunlock-users&deleted=1');
					} else {
						header('Location: '.admin_url('admin.php').'?page=subscribeunlock-users');
					}
					die();
					break;
				case 'subscribeunlock-csv':
					$rows = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sp_users WHERE deleted = '0' ORDER BY registered DESC", ARRAY_A);
					if (sizeof($rows) > 0) {
						if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE")) {
							header("Pragma: public");
							header("Expires: 0");
							header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
							header("Content-type: application-download");
							header("Content-Disposition: attachment; filename=\"emails.csv\"");
							header("Content-Transfer-Encoding: binary");
						} else {
							header("Content-type: application-download");
							header("Content-Disposition: attachment; filename=\"emails.csv\"");
						}
						$separator = $this->options['csv_separator'];
						if ($separator == 'tab') $separator = "\t";
						echo '"Name"'.$separator.'"E-Mail"'.$separator.'"Registered"'."\r\n";
						foreach ($rows as $row) {
							echo '"'.str_replace('"', '', $row["name"]).'"'.$separator.'"'.str_replace('"', "", $row["email"]).'"'.$separator.'"'.date("Y-m-d H:i:s", $row["registered"]).'"'."\r\n";
						}
						die();
		            }
		            header("Location: ".get_bloginfo('wpurl')."/wp-admin/admin.php?page=subscribeunlock");
					die();
					break;
				case 'subscribeunlock-deleteall':
					$sql = "UPDATE ".$wpdb->prefix."sp_users SET deleted = '1' WHERE deleted = '0'";
					if ($wpdb->query($sql) !== false) {
						header('Location: '.admin_url('admin.php').'?page=subscribeunlock-users&deleted=1');
					} else {
						header('Location: '.admin_url('admin.php').'?page=subscribeunlock-users');
					}
					die();
					break;
				case 'subscribeunlock-getresponse-campaigns':
					if (isset($_GET["key"]) && !empty($_GET["key"])) {
						$key = base64_decode($_GET["key"]);
						$request = json_encode(
							array(
								'method' => 'get_campaigns',
								'params' => array(
									$key
								),
								'id' => ''
							)
						);

						$curl = curl_init('http://api2.getresponse.com/');
						curl_setopt($curl, CURLOPT_POST, 1);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
						$header = array(
							'Content-Type: application/json',
							'Content-Length: '.strlen($request)
						);
						//curl_setopt($curl, CURLOPT_PORT, 443);
						curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
						curl_setopt($curl, CURLOPT_TIMEOUT, 10);
						//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
						//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
						curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
						curl_setopt($curl, CURLOPT_HEADER, 0);
									
						$response = curl_exec($curl);
						
						if (curl_error($curl)) die('<div style="text-align: center; margin: 20px 0px;">'.__('API call failed.','subscribeunlock').'</div>');
						$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
						if ($httpCode != '200') die('<div style="text-align: center; margin: 20px 0px;">'.__('API call failed.','subscribeunlock').'</div>');
						curl_close($curl);
						
						$post = json_decode($response, true);
						if(!empty($post['error'])) die('<div style="text-align: center; margin: 20px 0px;">'.__('API Key failed','subscribeunlock').': '.$post['error']['message'].'</div>');
						
						if (!empty($post['result'])) {
							echo '
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.__('GetResponse Campaigns', 'subscribeunlock').'</title>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="width: 170px; font-weight: bold;">'.__('Campaign ID', 'subscribeunlock').'</td>
			<td style="font-weight: bold;">'.__('Campaign Name', 'subscribeunlock').'</td>
		</tr>';
							foreach ($post['result'] as $key => $value) {
								echo '
		<tr>
			<td>'.esc_attr($key).'</td>
			<td>'.esc_attr(esc_attr($value['name'])).'</td>
		</tr>';
							}
							echo '
	</table>						
</body>
</html>';
						} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					die();
					break;
				case 'subscribeunlock-icontact-lists':
					if (isset($_GET["appid"]) && isset($_GET["user"]) && isset($_GET["pass"])) {
						$this->options['icontact_appid'] = base64_decode($_GET["appid"]);
						$this->options['icontact_apiusername'] = base64_decode($_GET["user"]);
						$this->options['icontact_apipassword'] = base64_decode($_GET["pass"]);
						
						$lists = $this->icontact_getlists();
						if (!empty($lists)) {
							echo '
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.__('GetResponse Campaigns', 'subscribeunlock').'</title>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="width: 170px; font-weight: bold;">'.__('List ID', 'subscribeunlock').'</td>
			<td style="font-weight: bold;">'.__('List Name', 'subscribeunlock').'</td>
		</tr>';
							foreach ($lists as $key => $value) {
								echo '
		<tr>
			<td>'.esc_attr($key).'</td>
			<td>'.esc_attr(esc_attr($value)).'</td>
		</tr>';
							}
							echo '
	</table>						
</body>
</html>';
						} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					die();
					break;
				case 'subscribeunlock-madmimi-lists':
					if (isset($_GET["login"]) && isset($_GET["key"])) {
						$login = base64_decode($_GET["login"]);
						$key = base64_decode($_GET["key"]);
						
						$lists = $this->madmimi_getlists($login, $key);
						if (!empty($lists)) {
							echo '
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.__('Mad Mimi Lists', 'subscribeunlock').'</title>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="width: 170px; font-weight: bold;">'.__('List ID', 'subscribeunlock').'</td>
			<td style="font-weight: bold;">'.__('List Name', 'subscribeunlock').'</td>
		</tr>';
							foreach ($lists as $key => $value) {
								echo '
		<tr>
			<td>'.esc_attr($key).'</td>
			<td>'.esc_attr(esc_attr($value)).'</td>
		</tr>';
							}
							echo '
	</table>						
</body>
</html>';
						} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					die();
					break;
				case 'subscribeunlock-benchmark-lists':
					if (isset($_GET["key"])) {
						$key = base64_decode($_GET["key"]);
						
						$lists = $this->benchmark_getlists($key);
						if (!empty($lists)) {
							echo '
<html>
<head>
	<meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
	<title>'.__('Benchmark Lists', 'subscribeunlock').'</title>
</head>
<body>
	<table style="width: 100%;">
		<tr>
			<td style="width: 170px; font-weight: bold;">'.__('List ID', 'subscribeunlock').'</td>
			<td style="font-weight: bold;">'.__('List Name', 'subscribeunlock').'</td>
		</tr>';
							foreach ($lists as $key => $value) {
								echo '
		<tr>
			<td>'.esc_attr($key).'</td>
			<td>'.esc_attr(esc_attr($value)).'</td>
		</tr>';
							}
							echo '
	</table>						
</body>
</html>';
						} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					} else echo '<div style="text-align: center; margin: 20px 0px;">'.__('No data found!', 'subscribeunlock').'</div>';
					die();
					break;
				default:
					break;
			}
		}
	}

	function mce_button($buttons) {
		array_push($buttons, "separator", "optinlocker");
		return $buttons;
	}

	function mce_external_plugin($plugin_array){
		$plugin_array['optinlocker'] = plugins_url('/js/button.js', __FILE__);
		return $plugin_array;
	}

	function admin_warning() {
		echo '
		<div class="updated"><p>'.__('<strong>Opt-In Content Locker</strong> plugin almost ready. You must do some <a href="admin.php?page=subscribeunlock">settings</a> for it to work.', 'subscribeunlock').'</p></div>';
	}

	function front_header() {
		global $post, $wpdb, $current_user;
		
		$from = $this->get_rgb($this->options['button_color']);
		$total = $from['r']+$from['g']+$from['b'];
		if ($total == 0) $total = 1;
		$to = array();
		$to['r'] = max(0, $from['r']-intval(48*$from['r']/$total));
		$to['g'] = max(0, $from['g']-intval(48*$from['g']/$total));
		$to['b'] = max(0, $from['b']-intval(48*$from['b']/$total));
		$to_color = '#'.($to['r'] < 16 ? '0' : '').dechex($to['r']).($to['g'] < 16 ? '0' : '').dechex($to['g']).($to['b'] < 16 ? '0' : '').dechex($to['b']);
		$from_color = $this->options['button_color'];
		if (!empty($this->options['input_background_color'])) $bg_color = $this->get_rgb($this->options['input_background_color']);
		echo '
		<style>
			.subscribeunlock_signup_form, .subscribeunlock_confirmation_info {background-color: '.(empty($this->options['box_color']) ? 'transparent' : $this->options['box_color']).' !important; border-color: '.(empty($this->options['box_border_color']) ? 'transparent' : $this->options['box_border_color']).' !important;'.(empty($this->options['box_font_color']) ? '' : ' color: '.$this->options['box_font_color'].' !important;').' font-size: '.intval($this->options['box_font_size']).'px !important;}
			.subscribeunlock_signup_form a, .subscribeunlock_confirmation_info a, .subscribeunlock_signup_form p, .subscribeunlock_confirmation_info p {'.(empty($this->options['box_font_color']) ? '' : ' color: '.$this->options['box_font_color'].';').' !important; font-size: '.intval($this->options['box_font_size']).'px !important;}
			.subscribeunlock_signup_form a, .subscribeunlock_confirmation_info a {'.(empty($this->options['box_font_color']) ? '' : ' color: '.$this->options['box_font_color'].';').' !important; text-decoration: underline !important;}
			a.subscribeunlock-submit, a.subscribeunlock-submit:visited {background-color: '.$from_color.' !important; border-color: '.$from_color.' !important; color: '.$this->options['button_font_color'].' !important; font-size: '.intval($this->options['button_font_size']).'px !important; text-decoration: none !important;}
			a.subscribeunlock-submit:hover, a.subscribeunlock-submit:active {background-color: '.$to_color.' !important; border-color: '.$to_color.' !important; color: '.$this->options['button_font_color'].' !important; font-size: '.intval($this->options['button_font_size']).'px !important; text-decoration: none !important;}
			.subscribeunlock_terms, .subscribeunlock-input, .subscribeunlock-input:hover, .subscribeunlock-input:active, .subscribeunlock-input:focus{border-color:'.(empty($this->options['input_border_color']) ? 'transparent' : $this->options['input_border_color']).' !important; background-color:'.(empty($this->options['input_background_color']) ? 'transparent' : $this->options['input_background_color']).' !important; background-color:'.(empty($this->options['input_background_color']) ? 'transparent' : 'rgba('.$bg_color['r'].','.$bg_color['g'].','.$bg_color['b'].','.floatval($this->options['input_background_opacity'])).') !important;'.(empty($this->options['input_font_color']) ? '' : ' color: '.$this->options['input_font_color'].' !important;').' font-size: '.intval($this->options['input_font_size']).'px !important;}
		</style>
		<script>
			var subscribeunlock_action = "'.admin_url('admin-ajax.php').'";
			var subscribeunlock_cookie_value = "'.SUBSCRIBEUNLOCK_COOKIE.'";
			var subscribeunlock_ga_tracking = "'.$this->options['ga_tracking'].'";
		</script>';
	}
	
	function shortcode_handler($_atts, $content=null) {
		global $post, $wpdb, $current_user;
		$form = '';
		if (isset($_COOKIE["subscribeunlock"]) && $_COOKIE["subscribeunlock"] == SUBSCRIBEUNLOCK_COOKIE) $content = do_shortcode(wpautop($content));
		else {
			if ($this->check_options() === true) {
				if (isset($_atts["width"])) $width = intval($_atts["width"]);
				if (isset($width) && $width < 100) unset($width);
				if (isset($_atts["title"])) $intro = do_shortcode(trim($_atts["title"]));
				else $intro = do_shortcode(trim($this->options['intro']));
				if (strlen($intro) > 0) $intro = '<div class="subscribeunlock_form_row">'.$intro.'</div>';
				if (isset($_atts["soft_mode"])) $soft_mode = $_atts["soft_mode"];
				else $soft_mode = '';
				if ($soft_mode != 'on' && $soft_mode != 'off') $soft_mode = $this->options["soft_mode"];
				
				$suffix = "_".rand(1000, 9999);
				$tac = '';
				$terms = htmlspecialchars($this->options['terms'], ENT_QUOTES);
				$terms = str_replace("\n", "<br />", $terms);
				$terms = str_replace("\r", "", $terms);
				if (!empty($this->options['terms'])) {
					$terms_id = "t".rand(100,999).rand(100,999).rand(100,999);
					$tac = '
						<div class="subscribeunlock_form_row">
							<div id="terms'.$suffix.'" class="subscribeunlock_invisible">
								<div class="subscribeunlock_terms">'.$terms.'</div>
							</div>
							'.__('By clicking the button below, I agree with the', 'subscribeunlock').' <a href="#" onclick="jQuery(\'#terms'.$suffix.'\').slideToggle(300); return false;">'.__('Terms & Conditions', 'subscribeunlock').'</a>.
						</div>';
				}
				
				$form = '
		<div class="subscribeunlock_container'.($soft_mode == 'on' ? ' subscribeunlock_invisible' : '').'"'.(!empty($width) ? ' style="width: '.$width.'px;"' : '').'>
			<div name="subscribeunlock" class="subscribeunlock_box" id="subscribeunlock'.$suffix.'">
				<div class="subscribeunlock_signup_form" id="subscribeunlock_signup_form'.$suffix.'">
					'.$intro.'
					<div class="subscribeunlock_form_row">
						'.($this->options['disable_name'] == 'on' ? '
						<div class="subscribeunlock_form_column subscribeunlock_fullwidth subscribeunlock_form_right_column">
							<div>
								<input required="required" tabindex="101" class="subscribeunlock-input subscribeunlock_fullwidth" type="text" id="email'.$suffix.'" placeholder="'.esc_attr($this->options['placeholder_email']).'" value="'.esc_attr($this->options['placeholder_email']).'" onfocus="if (this.value == \''.esc_attr($this->options['placeholder_email']).'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.esc_attr($this->options['placeholder_email']).'\';}" title="'.esc_attr($this->options['placeholder_email']).'" />
							</div>
						</div>' : '
						<div class="subscribeunlock_form_column subscribeunlock_50">
							<div>
								<input required="required" tabindex="101" class="subscribeunlock-input subscribeunlock_fullwidth" type="text" id="name'.$suffix.'" placeholder="'.esc_attr($this->options['placeholder_name']).'" value="'.esc_attr($this->options['placeholder_name']).'" onfocus="if (this.value == \''.esc_attr($this->options['placeholder_name']).'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.esc_attr($this->options['placeholder_name']).'\';}" title="'.esc_attr($this->options['placeholder_name']).'" />
							</div>
						</div>
						<div class="subscribeunlock_form_column subscribeunlock_50 subscribeunlock_form_right_column">
							<div>
								<input required="required" tabindex="102" class="subscribeunlock-input subscribeunlock_fullwidth" type="text" id="email'.$suffix.'" placeholder="'.esc_attr($this->options['placeholder_email']).'" value="'.esc_attr($this->options['placeholder_email']).'" onfocus="if (this.value == \''.esc_attr($this->options['placeholder_email']).'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.esc_attr($this->options['placeholder_email']).'\';}" title="'.esc_attr($this->options['placeholder_email']).'" />
							</div>
						</div>').'
					</div>
					'.$tac.'
					<div>
						<a href="#" tabindex="103" class="subscribeunlock-submit" id="submit'.$suffix.'" onclick=\'subscribeunlock_submit("'.$suffix.'", "'.$soft_mode.'"); return false;\'>'.esc_attr($this->options['button_label']).'</a>
						<img id="loading'.$suffix.'" class="subscribeunlock_loading" src="'.plugins_url('/images/loading.gif', __FILE__).'" alt="">
					</div>
					<div id="message'.$suffix.'" class="subscribeunlock_message"></div>
				</div>
				<div class="subscribeunlock_confirmation_container" id="subscribeunlock_confirmation_container'.$suffix.'"></div>
			</div>
		</div>';				
			}
			if ($soft_mode != 'on') $content = $form;
			else $content = '<div class="subscribeunlock_content subscribeunlock_invisible">'.do_shortcode(wpautop($content)).'</div>'.$form.'<script type="text/javascript">if (subscribeunlock_cookie == "'.SUBSCRIBEUNLOCK_COOKIE.'") {jQuery(".subscribeunlock_content").removeClass("subscribeunlock_invisible");} else {jQuery(".subscribeunlock_container").removeClass("subscribeunlock_invisible");}</script>';
		}
		return $content;
	}

	function subscribeunlock_submit() {
		global $wpdb;
		header("Content-type: application/json");
		$jsonp_callback = $_REQUEST['callback'];
	
		$email = trim(stripslashes($_REQUEST['subscribeunlock_email']));
		$email = str_replace('+', '@', $email);
		$suffix = trim(stripslashes($_REQUEST['subscribeunlock_suffix']));
		$error = '';
		if ($this->options['disable_name'] != 'on') {
			$name = trim(stripslashes($_REQUEST['subscribeunlock_name']));
			$name = str_replace('+', '@', $name);
			if ($name == '' || $name == $this->options['placeholder_name']) {
				$error .= '<li>'.__('Your name is required.', 'subscribeunlock').'</li>';
			}
		} else $name = '';
		
		if ($email == '') {
			$error .= '<li>'.__('Your e-mail address is required.', 'subscribeunlock').'</li>';
		} else if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)) {
			$error .= '<li>'.__('You have entered an invalid e-mail address.', 'subscribeunlock').'</li>';
		} else {
			if ($this->options['email_validation'] == 'on') {
				$email_parts = explode('@',$email);
				if(checkdnsrr($email_parts[1], 'MX')) {
					//if(!fsockopen($email_parts[1], 25, $errno, $errstr, 30)) $error .= '<li>'.__('You have entered an invalid e-mail address.', 'subscribeunlock').'</li>';
				} else $error .= '<li>'.__('You have entered an invalid e-mail address.', 'subscribeunlock').'</li>';
			}
		}
		if ($error != '') {
			$html = '<div class="subscribeunlock_error_message">'.__('Attention! Please correct the errors below and try again.', 'subscribeunlock').'<ul class="subscribeunlock_error_messages">'.$error.'</ul></div>';
			$return = array();
			$return['html'] = $html;
			echo $jsonp_callback.'('.json_encode($return).')';
		} else {
			$tmp = $wpdb->get_row("SELECT COUNT(*) AS total FROM ".$wpdb->prefix."sp_users WHERE deleted = '0' AND email = '".esc_sql($email)."'", ARRAY_A);
			if ($tmp["total"] > 0) {
				$sql = "UPDATE ".$wpdb->prefix."sp_users SET
					name = '".esc_sql($name)."',
					registered = '".time()."'
					WHERE deleted = '0' AND email = '".esc_sql($email)."'";
				$wpdb->query($sql);
			} else {
				$sql = "INSERT INTO ".$wpdb->prefix."sp_users (
					name, email, registered, deleted) VALUES (
					'".esc_sql($name)."',
					'".esc_sql($email)."',
					'".time()."', '0'
				)";
				$wpdb->query($sql);
			}
			if (empty($name)) $name = substr($email, 0, strpos($email, '@'));
			if ($this->options['mailchimp_enable'] == 'on') {
				$list_id = $this->options['mailchimp_list_id'];
				$dc = "us1";
				if (strstr($this->options['mailchimp_api_key'], "-")) {
					list($key, $dc) = explode("-", $this->options['mailchimp_api_key'], 2);
					if (!$dc) $dc = "us1";
				}
				$mailchimp_url = 'http://'.$dc.'.api.mailchimp.com/1.3/?method=listSubscribe&apikey='.$this->options['mailchimp_api_key'].'&id='.$list_id.'&email_address='.urlencode($email).'&merge_vars[FNAME]='.urlencode($name).'&merge_vars[LNAME]='.urlencode($name).'&merge_vars[NAME]='.urlencode($name).'&merge_vars[OPTIN_IP]='.$_SERVER['REMOTE_ADDR'].'&output=php&double_optin='.($this->options['mailchimp_double'] == 'on' ? '1' : '0').'&send_welcome='.($this->options['mailchimp_welcome'] == 'on' ? '1' : '0');
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_URL, $mailchimp_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_ENCODING, "");
				curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: MCAPI/1.3');
				curl_setopt($ch, CURLOPT_TIMEOUT, 30);
				curl_setopt($ch, CURLOPT_FAILONERROR, 1);
				curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, null);
				$data  = curl_exec( $ch );
				curl_close( $ch );
			}
			if ($this->options['icontact_enable'] == 'on') {
				$this->icontact_addcontact($name, $email);
			}
			if ($this->options['campaignmonitor_enable'] == 'on') {
				$options['EmailAddress'] = $email;
				$options['Name'] = $name;
				$options['Resubscribe'] = 'true';
				$options['RestartSubscriptionBasedAutoresponders'] = 'true';
				$post = json_encode($options);

				$curl = curl_init('http://api.createsend.com/api/v3/subscribers/'.urlencode($this->options['campaignmonitor_list_id']).'.json');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
				
				$header = array(
					'Content-Type: application/json',
					'Content-Length: '.strlen($post),
					'Authorization: Basic '.base64_encode($this->options['campaignmonitor_api_key'])
					);

				//curl_setopt($curl, CURLOPT_PORT, 443);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC ) ;
				//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
				//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
					
				$response = curl_exec($curl);
				curl_close($curl);
			}
			if ($this->options['getresponse_enable'] == 'on') {
				$request = json_encode(
					array(
						'method' => 'add_contact',
						'params' => array(
							$this->options['getresponse_api_key'],
							array(
								'campaign' => $this->options['getresponse_campaign_id'],
								'action' => 'standard',
								'name' => $name,
								'email' => $email,
								'cycle_day' => 0,
								'ip' => $_SERVER['REMOTE_ADDR']
							)
						),
						'id' => ''
					)
				);

				$curl = curl_init('http://api2.getresponse.com/');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
							
				$header = array(
					'Content-Type: application/json',
					'Content-Length: '.strlen($request)
				);

				//curl_setopt($curl, CURLOPT_PORT, 443);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_TIMEOUT, 10);
				//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1); // verify certificate
				//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // check existence of CN and verify that it matches hostname
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_HEADER, 0);
							
				$response = curl_exec($curl);
				curl_close($curl);
			}
			if ($this->options['aweber_access_secret']) {
				if ($this->options['aweber_enable'] == 'on') {
					$account = null;
					if (!class_exists('AWeberAPI')) {
						require_once(dirname(__FILE__).'/aweber_api/aweber_api.php');
					}
					try {
						$aweber = new AWeberAPI($this->options['aweber_consumer_key'], $this->options['aweber_consumer_secret']);
						$account = $aweber->getAccount($this->options['aweber_access_key'], $this->options['aweber_access_secret']);
						$subscribers = $account->loadFromUrl('/accounts/' . $account->id . '/lists/' . $this->options['aweber_listid'] . '/subscribers');
						$subscribers->create(array(
							'email' => $email,
							'ip_address' => $_SERVER['REMOTE_ADDR'],
							'name' => $name,
							'ad_tracking' => 'Opt-In Panel',
						));
					} catch (Exception $e) {
						$account = null;
					}
				}
			}
			if ($this->options['madmimi_enable'] == 'on') {
				$request = http_build_query(array(
					'email' => $email,
					'first_name' => $name,
					'last_name' => '',
					'username' => $this->options['madmimi_login'],
					'api_key' => $this->options['madmimi_api_key']
				));

				$curl = curl_init('http://api.madmimi.com/audience_lists/'.$this->options['madmimi_list_id'].'/add');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

				curl_setopt($curl, CURLOPT_TIMEOUT, 20);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_HEADER, 0);
									
				$response = curl_exec($curl);
				curl_close($curl);
			}
			if ($this->options['sendy_enable'] == 'on') {
				$request = http_build_query(array(
					'email' => $email,
					'name' => $name,
					'list' => $this->options['sendy_listid'],
					'boolean' => 'true'
				));

				$this->options['sendy_url'] = rtrim($this->options['sendy_url'], '/');
				$curl = curl_init($this->options['sendy_url'].'/subscribe');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

				curl_setopt($curl, CURLOPT_TIMEOUT, 20);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_HEADER, 0);
									
				$response = curl_exec($curl);
				curl_close($curl);
			}
			if ($this->options['benchmark_enable'] == 'on') {
				$request = http_build_query(array(
					'contacts' => array(
						'email' => $email,
						'firstname' => $name,
						'lastname' => ''),
					'optin' => ($this->options['benchmark_double'] == 'on' ? 1 : 0),
					'listID' => $this->options['benchmark_list_id'],
					'token' => $this->options['benchmark_api_key']
				));

				$curl = curl_init('http://www.benchmarkemail.com/api/1.0/?output=php&method=listAddContacts');
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

				curl_setopt($curl, CURLOPT_TIMEOUT, 20);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
				curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($curl, CURLOPT_HEADER, 0);
									
				$response = curl_exec($curl);
				curl_close($curl);
			}
			if (function_exists('mymail_subscribe') || function_exists('mymail')) {
				if ($this->options['mymail_enable'] == 'on') {
					if (function_exists('mymail')) {
						$list = mymail('lists')->get($this->options['mymail_listid']);
					} else {
						$list = get_term_by('id', $this->options['mymail_listid'], 'newsletter_lists');
					}
					if (!empty($list)) {
						try {
							if ($this->options['mymail_double'] == "on") $double = true;
							else $double = false;
							if (function_exists('mymail')) {
								$entry = array(
									'firstname' => $name,
									'email' => $email,
									'status' => $double ? 0 : 1,
									'ip' => $_SERVER['REMOTE_ADDR'],
									'signup_ip' => $_SERVER['REMOTE_ADDR'],
									'referer' => $_SERVER['HTTP_REFERER'],
									'signup' =>time()
								);
								$subscriber_id = mymail('subscribers')->add($entry, true);
								if (is_wp_error( $subscriber_id )) return;
								$result = mymail('subscribers')->assign_lists($subscriber_id, array($list->ID));
							} else {
								$result = mymail_subscribe($email, array('firstname' => $name), array($term->slug), $double);
							}
						} catch (Exception $e) {
						}
					}
				}
			}
			$domain_parts = explode('.', $_SERVER["HTTP_HOST"]);
			if (sizeof($domain_parts) > 2) $domain = '.'.$domain_parts[sizeof($domain_parts)-2].'.'.$domain_parts[sizeof($domain_parts)-1];
			else $domain = '.'.$_SERVER["HTTP_HOST"];
			setcookie("subscribeunlock", SUBSCRIBEUNLOCK_COOKIE, time()+3600*24*180, "/", $domain);
			if ($this->options['thanksgiving_enable'] == 'on') {
				$tags = array("{name}", "{e-mail}", "{email}");
				$vals = array($name, $email, $email);
				$body = str_replace($tags, $vals, $this->options['thanksgiving_email_body']);
				$mail_headers = "Content-Type: text/plain; charset=utf-8\r\n";
				$mail_headers .= "From: ".$this->options['from_name']." <".$this->options['from_email'].">\r\n";
				$mail_headers .= "X-Mailer: PHP/".phpversion()."\r\n";
				wp_mail($email, $this->options['thanksgiving_email_subject'], $body, $mail_headers);
			}
			$html = '<div class="subscribeunlock_confirmation_info" style="text-align: center;">'.esc_attr($this->options['thanks_message']).'</div>';
			$return = array();
			$return['html'] = $html;
			echo $jsonp_callback.'('.json_encode($return).')';
		}
		exit;
	}

	function get_rgb($_color) {
		if (strlen($_color) != 7 && strlen($_color) != 4) return false;
		$color = preg_replace('/[^#a-fA-F0-9]/', '', $_color);
		if (strlen($color) != strlen($_color)) return false;
		if (strlen($color) == 7) list($r, $g, $b) = array($color[1].$color[2], $color[3].$color[4], $color[5].$color[6]);
		else list($r, $g, $b) = array($color[1].$color[1], $color[2].$color[2], $color[3].$color[3]);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	}

	function madmimi_getlists($_login, $_key) {
		$curl = curl_init('http://api.madmimi.com/audience_lists/lists.json?'.http_build_query(array('username' => $_login, 'api_key' => $_key)));
		curl_setopt($curl, CURLOPT_POST, 0);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);
														
		$response = curl_exec($curl);
											
		if (curl_error($curl)) return array();
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($httpCode != '200') return array();
		curl_close($curl);
											
		$result = json_decode($response, true);
		if(!$result) return array();
		$lists = array();
		foreach ($result as $key => $value) {
			$lists[$value['id']] = $value['name'];
		}
		return $lists;
	}

	function benchmark_getlists($_key) {
		$request = http_build_query(array(
			'token' => $_key
		));

		$curl = curl_init('http://www.benchmarkemail.com/api/1.0/?output=php&method=listGet');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

		curl_setopt($curl, CURLOPT_TIMEOUT, 20);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);
								
		$response = curl_exec($curl);
		curl_close($curl);

		$result = unserialize($response);
		if (!is_array($result) || isset($result['error'])) return array();
		$lists = array();
		foreach ($result as $key => $value) {
			$lists[$value['id']] = $value['listname'];
		}
		return $lists;
	}

	function icontact_getlists() {
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/', null, 'accounts');
		if (!empty($data['errors'])) return array();
		$account = $data['response'][0];
		if (empty($account) || intval($account->enabled != 1)) return;
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/', null, 'clientfolders');
		if (!empty($data['errors'])) return array();
		$client = $data['response'][0];
		if (empty($client)) return array();
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/'.$client->clientFolderId.'/lists', array(), 'lists');
		if (!empty($data['errors'])) return array();
		if (!is_array($data['response'])) return array();
		$lists = array();
		foreach ($data['response'] as $list) {
			$lists[$list->listId] = $list->name;
		}
		return $lists;
	}

	function icontact_addcontact($name, $email) {
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/', null, 'accounts');
		if (!empty($data['errors'])) return;
		$account = $data['response'][0];
		if (empty($account) || intval($account->enabled != 1)) return;
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/', null, 'clientfolders');
		if (!empty($data['errors'])) return;
		$client = $data['response'][0];
		if (empty($client)) return;
		$contact['email'] = $email;
		$contact['firstName'] = $name;
		$contact['status'] = 'normal';
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/'.$client->clientFolderId.'/contacts', array($contact), 'contacts');
		if (!empty($data['errors'])) return;
		$contact = $data['response'][0];
		if (empty($contact)) return;
		$subscriber['contactId'] = $contact->contactId;
		$subscriber['listId'] = $this->options['icontact_listid'];
		$subscriber['status'] = 'normal';
		$data = $this->icontact_makecall($this->options['icontact_appid'], $this->options['icontact_apiusername'], $this->options['icontact_apipassword'], '/a/'.$account->accountId.'/c/'.$client->clientFolderId.'/subscriptions', array($subscriber), 'subscriptions');
	}

	function icontact_makecall($appid, $apiusername, $apipassword, $resource, $postdata = null, $returnkey = null) {
		$return = array();
		$url = "https://app.icontact.com/icp".$resource;
		$headers = array(
			'Except:', 
			'Accept:  application/json', 
			'Content-type:  application/json', 
			'Api-Version:  2.2',
			'Api-AppId:  '.$appid, 
			'Api-Username:  '.$apiusername, 
			'Api-Password:  '.$apipassword
		);
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		if (!empty($postdata)) {
			curl_setopt($handle, CURLOPT_POST, true);
			curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($postdata));
		}
		curl_setopt($handle, CURLOPT_URL, $url);
		if (!$response_json = curl_exec($handle)) {
			$return['errors'][] = __('Unable to execute the cURL handle.', 'subscribeunlock');
		}
		if (!$response = json_decode($response_json)) {
			$return['errors'][] = __('The iContact API did not return valid JSON.', 'subscribeunlock');
		}
		curl_close($handle);
		if (!empty($response->errors)) {
			foreach ($response->errors as $error) {
				$return['errors'][] = $error;
			}
		}
		if (!empty($return['errors'])) return $return;
		if (empty($returnkey)) {
			$return['response'] = $response;
		} else {
			$return['response'] = $response->$returnkey;
		}
		return $return;
	}

	function page_switcher ($_urlbase, $_currentpage, $_totalpages) {
		$pageswitcher = "";
		if ($_totalpages > 1) {
			$pageswitcher = '<div class="tablenav bottom"><div class="tablenav-pages">'.__('Pages:', 'subscribeunlock').' <span class="pagiation-links">';
			if (strpos($_urlbase,"?") !== false) $_urlbase .= "&amp;";
			else $_urlbase .= "?";
			if ($_currentpage == 1) $pageswitcher .= "<a class='page disabled'>1</a> ";
			else $pageswitcher .= " <a class='page' href='".$_urlbase."p=1'>1</a> ";

			$start = max($_currentpage-3, 2);
			$end = min(max($_currentpage+3,$start+6), $_totalpages-1);
			$start = max(min($start,$end-6), 2);
			if ($start > 2) $pageswitcher .= " <b>...</b> ";
			for ($i=$start; $i<=$end; $i++) {
				if ($_currentpage == $i) $pageswitcher .= " <a class='page disabled'>".$i."</a> ";
				else $pageswitcher .= " <a class='page' href='".$_urlbase."p=".$i."'>".$i."</a> ";
			}
			if ($end < $_totalpages-1) $pageswitcher .= " <b>...</b> ";

			if ($_currentpage == $_totalpages) $pageswitcher .= " <a class='page disabled'>".$_totalpages."</a> ";
			else $pageswitcher .= " <a class='page' href='".$_urlbase."p=".$_totalpages."'>".$_totalpages."</a> ";
			$pageswitcher .= "</span></div></div>";
		}
		return $pageswitcher;
	}
}
$subscribeunlock = new subscribeunlock_class();
?>