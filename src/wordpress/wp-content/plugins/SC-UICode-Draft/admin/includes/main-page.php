<div class="wrap">
	<?php screen_icon(); ?>
	<h2>SailChecker UI Options</h2>
	<div>
		<?php
		$data_field_enquiry_subject	 = 'sc-enquire-ui-enquiry-subject';

		$data_field_response_message = 'sc-enquire-ui-response-message';
		$data_field_response_subject = 'sc-enquire-ui-response-subject';

		if (isset($_POST[$data_field_response_message]) && isset($_POST[$data_field_response_subject]) &&
			isset($_POST[$data_field_enquiry_subject])):

			$enquiry_subject 	= $_POST[$data_field_enquiry_subject];
			$response_subject 	= $_POST[$data_field_response_subject];
			$response_message 	= $_POST[$data_field_response_message];

			// Save options...
			update_option($data_field_enquiry_subject, $enquiry_subject);
			update_option($data_field_response_subject, $response_subject);
			update_option($data_field_response_message, $response_message);
		?>
		<div class="updated"><p><strong><?php _e("Settings saved!"); ?></strong></p></div>
		<?php endif; ?>
		<div style="padding: 0 10px;">
			<form method="post" action="">
				<?php settings_fields('sc-ui-option-group'); ?>
				<h3>Enquiry Settings</h3>
				<div style="padding: 0 10px;">
					<p>
						<strong><label for="<?php echo $data_field_enquiry_subject; ?>"><?php _e('Enquiry subject: '); ?></label></strong>
					</p>
					<div>
						<input style="width: 100%;" id="<?php echo $data_field_enquiry_subject; ?>" type="text" name="<?php echo $data_field_enquiry_subject; ?>" placeholder="Enquiry Subject" value="<?php echo get_option($data_field_enquiry_subject); ?>" />
					</div>
				</div>
				<h3>Auto Response Settings</h3>
				<div style="padding: 0 10px;">
					<p>
						<strong><label for="<?php echo $data_field_response_subject; ?>"><?php _e('Enquiry response subject: '); ?></label></strong>
					</p>
					<div>
						<input style="width: 100%;" id="<?php echo $data_field_response_subject; ?>" type="text" name="<?php echo $data_field_response_subject; ?>" placeholder="Response Subject" value="<?php echo get_option($data_field_response_subject); ?>" />
					</div>
					<p>
						<strong><label for="<?php echo $data_field_response_message; ?>"><?php _e('Enquiry response message: '); ?></label></strong>
					</p>
					<div>
						<textarea rows="5" style="width: 100%;" id="<?php echo $data_field_response_message; ?>" name="<?php echo $data_field_response_message; ?>" placeholder="Accepts HTML Tags"><?php echo get_option($data_field_response_message); ?></textarea>
					</div>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>
</div>