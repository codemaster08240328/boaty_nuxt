jQuery(document).ready(
function()
	{
		jQuery('#preferences .alert .close').click(
			function()
				{
				jQuery(this).parent().slideUp('slow')
				}
			);
		
		jQuery('#field-pref').ajaxForm({
			data: {
			   action: 'save_field_pref'
			},
			//dataType: 'json',
			beforeSubmit: function(formData, jqForm, options) {
				//alert('test');
				//console.log($('input[name="do_image_upload_preview"]').val())
				jQuery('#field-pref button').html('&nbsp;&nbsp;&nbsp;<span class="fa fa-spin fa-spinner"></span>&nbsp;Saving...&nbsp;&nbsp;&nbsp;')
			},
		   success : function(responseText, statusText, xhr, $form) {
			   jQuery('#field-pref button').html('&nbsp;&nbsp;&nbsp;Save Field Preferences&nbsp;&nbsp;&nbsp;');
			},
			 error: function(jqXHR, textStatus, errorThrown)
				{
				console.log(errorThrown)
				}
		});
		
		jQuery('#validation-pref').ajaxForm({
			data: {
			   action: 'save_validation_pref'
			},
			//dataType: 'json',
			beforeSubmit: function(formData, jqForm, options) {
				//alert('test');
				//console.log($('input[name="do_image_upload_preview"]').val())
				jQuery('#validation-pref button').html('&nbsp;&nbsp;&nbsp;<span class="fa fa-spin fa-spinner"></span>&nbsp;Saving...&nbsp;&nbsp;&nbsp;')
			},
		   success : function(responseText, statusText, xhr, $form) {
			   jQuery('#validation-pref button').html('&nbsp;&nbsp;&nbsp;Save Validation Preferences&nbsp;&nbsp;&nbsp;');
			  Materialize.toast('Validation Preferences Saved', 2000, 'toast-success');
			   
			},
			 error: function(jqXHR, textStatus, errorThrown)
				{
				console.log(errorThrown)
				}
		});
		
		jQuery('#emails-pref').ajaxForm({
			data: {
			   action: 'save_email_pref'
			},
			//dataType: 'json',
			beforeSubmit: function(formData, jqForm, options) {
				//alert('test');
				//console.log($('input[name="do_image_upload_preview"]').val())
				jQuery('#emails-pref button').html('&nbsp;&nbsp;&nbsp;<span class="fa fa-spin fa-spinner"></span>&nbsp;Saving...&nbsp;&nbsp;&nbsp;')
			},
		   success : function(responseText, statusText, xhr, $form) {
			   jQuery('#emails-pref button').html('&nbsp;&nbsp;&nbsp;Save Email Preferences&nbsp;&nbsp;&nbsp;');
			  Materialize.toast('Email Preferences Saved', 2000, 'toast-success');
			   
			},
			 error: function(jqXHR, textStatus, errorThrown)
				{
				console.log(errorThrown)
				}
		});
		
		jQuery('#other-pref').ajaxForm({
			data: {
			   action: 'save_other_pref'
			},
			//dataType: 'json',
			beforeSubmit: function(formData, jqForm, options) {
				//alert('test');
				//console.log($('input[name="do_image_upload_preview"]').val())
				jQuery('#other-pref button').html('&nbsp;&nbsp;&nbsp;<span class="fa fa-spin fa-spinner"></span>&nbsp;Saving...&nbsp;&nbsp;&nbsp;')
			},
		   success : function(responseText, statusText, xhr, $form) {
			   jQuery('#other-pref button').html('&nbsp;&nbsp;&nbsp;Save Other Preferences&nbsp;&nbsp;&nbsp;');
			   Materialize.toast('Other Preferences Saved', 2000, 'toast-success');
			   
			},
			 error: function(jqXHR, textStatus, errorThrown)
				{
				console.log(errorThrown)
				}
		});
		
		
		
	}
);