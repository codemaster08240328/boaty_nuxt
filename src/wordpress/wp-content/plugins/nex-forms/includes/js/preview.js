function loading_nex_forms_preview(){
	
}
jQuery(document).ready(
function()
	{

jQuery('.form-preview').live('click',
		function()
			{
		//jQuery('[data-style-tool="default-tool"]').trigger('click')
		
		jQuery('#preview_form').modal({show:true});
		
		if(!jQuery('.task-items-bar .task-preview').attr('class'))
			jQuery('.task-items-bar').append('<button data-target-window="preview_form" class="btn task-window task-preview active"><i class="fa fa-eye"></i></div>')
					
		
		
		//jQuery('.change_device.tablet').trigger('click');
		jQuery('div.clean_html').html(jQuery('div.nex-forms-container').html())
		
		clean_html = jQuery('div.clean_html');

		var hidden_fields = '';	
		jQuery('.hidden_fields_setup .hidden_fields .hidden_field').each(
			function()
				{
				hidden_fields += jQuery(this).find('input.field_name').val();
				hidden_fields += '[split]';
				hidden_fields += jQuery(this).find('input.field_value').val();
				hidden_fields += '[end]';
				}
			);
		var cl_array = '';	
		jQuery('.set_rules .new_rule').each(
			function(index)
				{
				cl_array += '[start_rule]';
					//OPERATOR
					cl_array += '[operator]';
						cl_array += jQuery(this).find('select[name="selector"]').val() + '##' + jQuery(this).find('select[name="reverse_actions"] option:selected').val();
					cl_array += '[end_operator]';
					//CONDITIONS
					cl_array += '[conditions]';
					jQuery(this).find('.get_rule_conditions .the_rule_conditions').each(
						function(index)
							{
							cl_array += '[new_condition]';
								cl_array += '[field]';
									cl_array += jQuery(this).find('.cl_field').val();
								cl_array += '[end_field]';
								cl_array += '[field_condition]';
									cl_array += jQuery(this).find('select[name="field_condition"]').val();
								cl_array += '[end_field_condition]';
								cl_array += '[value]';
									cl_array += jQuery(this).find('input[name="conditional_value"]').val();
								cl_array += '[end_value]';
							cl_array += '[end_new_condition]';
							}
						);
					cl_array += '[end_conditions]';
					cl_array += '[actions]';
					jQuery(this).find('.get_rule_actions .the_rule_actions').each(
						function(index)
							{
							cl_array += '[new_action]';
								cl_array += '[the_action]';
									cl_array += jQuery(this).find('select[name="the_action"]').val();
								cl_array += '[end_the_action]';
								cl_array += '[field_to_action]';
									cl_array += jQuery(this).find('select[name="cla_field"]').val();
								cl_array += '[end_field_to_action]';
							cl_array += '[end_new_action]';
							}
						);
					cl_array += '[end_actions]';					
				cl_array += '[end_rule]';
				}
			);
			
	var product_array = '';
									
	jQuery('.paypal_products .paypal_product').each(
		function(index)
			{
			product_array += '[start_product]';
			
				product_array += '[item_name]';
					product_array += jQuery(this).find('input[name="item_name"]').val();
				product_array += '[end_item_name]';
				
				product_array += '[item_qty]';
					product_array += jQuery(this).find('input[name="item_quantity"]').val();
				product_array += '[end_item_qty]';
				
				product_array += '[map_item_qty]';
					product_array += jQuery(this).find('select[name="map_item_quantity"]').val();
				product_array += '[end_map_item_qty]';
				
				product_array += '[set_quantity]';
					product_array += jQuery(this).find('input[name="set_quantity"]').val();
				product_array += '[end_set_quantity]';
				
				product_array += '[item_amount]';
					product_array += jQuery(this).find('input[name="item_amount"]').val();
				product_array += '[end_item_amount]';
				
				product_array += '[map_item_amount]';
					product_array += jQuery(this).find('select[name="map_item_amount"]').val();
				product_array += '[end_map_item_amount]';
				
				product_array += '[set_amount]';
					product_array += jQuery(this).find('input[name="set_amount"]').val();
				product_array += '[end_set_amount]';
						
			product_array += '[end_product]';
			}
		);	
		
	clean_html = jQuery('div.clean_html');
	
	clean_html.find('.btn-lg.move_field').remove();
	clean_html.find('.has_con' ).removeClass('has_con');
	clean_html.find('.the-thumb').removeClass('text-danger').removeClass('text-success').removeClass('checked');	
	clean_html.find('#star' ).raty('destroy');		
	clean_html.find('.js-signature canvas').remove();
	clean_html.find('.zero-clipboard, div.ui-nex-forms-container .field_settings').remove();
	clean_html.find('.grid').removeClass('grid-system')		
	clean_html.find('.editing-field-container').removeClass('.editing-field-container')
	clean_html.find('.bootstrap-touchspin-prefix').remove();
	clean_html.find('.bootstrap-select').remove();
	clean_html.find('.bootstrap-touchspin-postfix').remove();
	clean_html.find('.bootstrap-touchspin .input-group-btn').remove();
	clean_html.find('.bootstrap-tagsinput').remove();
	//clean_html.find('div#the-radios input').prop('checked',false);
	//clean_html.find('div#the-radios a').attr('class','');
	clean_html.find('.editing-field').removeClass('editing-field')
	clean_html.find('.editing-field-container').removeClass('.editing-field-container')
	clean_html.find('div.trash-can').remove();
	clean_html.find('div.draggable_object').hide();
	clean_html.find('div.draggable_object').remove();
	clean_html.find('div.form_field').removeClass('field');
	clean_html.find('.zero-clipboard').remove();
	clean_html.find('.tab-pane').removeClass('tab-pane');	
	clean_html.find('.help-block.hidden, .is_required.hidden').remove();
	clean_html.find('.has-pretty-child, .slider').removeClass('svg_ready')
	clean_html.find('.input-group').removeClass('date');
	clean_html.find('.popover').remove();
	clean_html.find('.the_input_element, .row, .svg_ready, .radio-inline').each(
		function()
			{
			if(jQuery(this).parent().hasClass('input-inner') || jQuery(this).parent().hasClass('input_holder')){
				jQuery(this).unwrap();
				}	
			}
		);
	clean_html.find('.form_field').each(
		function()
			{
			obj = jQuery(this);
			clean_html.find('.customcon').each(
					function()
						{
						if(obj.attr('id')==jQuery(this).attr('data-target') && (jQuery(this).attr('data-action')=='show' || jQuery(this).attr('data-action')=='slideDown' || jQuery(this).attr('data-action')=='fadeIn'))
							clean_html.find('#'+obj.attr('id')).hide();
						}
					);
				}
			);
	clean_html.find('div').each(
		function()
			{
			if(jQuery(this).parent().hasClass('svg_ready') || jQuery(this).parent().hasClass('form_object') || jQuery(this).parent().hasClass('input-inner')){
				jQuery(this).unwrap();
				}
			}
		);
	clean_html.find('div.form_field').each(
		function()
			{
			if(jQuery(this).parent().parent().hasClass('panel-default') && !jQuery(this).parent().prev('div').hasClass('panel-heading')){
				jQuery(this).parent().unwrap();
				jQuery(this).unwrap();
				}
			}
		);
		
	clean_html.find('.help-block').each(
		function()
			{
			if(!jQuery(this).text())
				jQuery(this).remove()
			}
		);
	clean_html.find('.sub-text').each(
		function()
			{
			if(jQuery(this).text()=='')
				{
				jQuery(this).parent().find('br').remove()
				jQuery(this).remove();
				}
			}
		);
	clean_html.find('.label_container').each(
		function()
			{
			if(jQuery(this).css('display')=='none')
				{
				jQuery(this).parent().find('.input_container').addClass('full_width');
				jQuery(this).remove()
				}
			}
		);
	clean_html.find('.ui-draggable').removeClass('ui-draggable');
	clean_html.find('.ui-draggable-handle').removeClass('ui-draggable-handle')
	clean_html.find('.dropped').removeClass('dropped')
	clean_html.find('.ui-sortable-handle').removeClass('ui-sortable-handle');
	clean_html.find('.ui-sortable').removeClass('ui-sortable-handle');
	clean_html.find('.ui-droppable').removeClass('ui-sortable-handle');
	clean_html.find('.over').removeClass('ui-sortable-handle');
	clean_html.find('.the_input_element.bs-tooltip').removeClass('bs-tooltip') 
	clean_html.find('.bs-tooltip.glyphicon').removeClass('glyphicon');
	clean_html.find('.grid-system.panel').removeClass('panel-body');
	clean_html.find('.grid-system.panel').removeClass('panel');
	clean_html.find('.form_field.grid').removeClass('grid').removeClass('form_field').addClass('is_grid');
	clean_html.find('.grid-system').removeClass('grid-system');
	clean_html.find('.move_field').remove();
	clean_html.find('.input-group-addon.btn-file span').attr('class','fa fa-cloud-upload');
	clean_html.find('.input-group-addon.fileinput-exists span').attr('class','fa fa-close');
	clean_html.find('.checkbox-inline').addClass('radio-inline');
	clean_html.find('.check-group').addClass('radio-group');
	clean_html.find('.submit-button br').remove();
	clean_html.find('.submit-button small.svg_ready').remove();
	clean_html.find('.radio-group a, .check-group a').addClass('ui-state-default')
	clean_html.find('.is_grid .panel-body').removeClass('ui-widget-content');
	clean_html.find('.bootstrap-select.ui-state-default').removeClass('ui-state-default');
	clean_html.find('.bootstrap-select').removeClass('form-control').addClass('full_width');
	clean_html.find('.selectpicker, .dropdown-menu.the_input_element').addClass('ui-state-default');
	clean_html.find('.selectpicker').removeClass('dropdown-toggle')
	clean_html.find('.is_grid .panel-body').removeClass('ui-widget-content');
	clean_html.find('.bootstrap-select.ui-state-default').removeClass('ui-state-default');
	clean_html.find('.is_grid .panel-body').removeClass('ui-sortable').removeClass('ui-droppable').removeClass('ui-widget-content').removeClass('');
	clean_html.find('.step').hide()
	clean_html.find('.step').first().show();	
	
	jQuery('.show_form_preview').hide();
	jQuery('.load_preview').html('<span class="fa fa-spin fa-circle-o-notch"></span>');	
		jQuery('.load_preview').show();		
			var data =
				{
				action	 							: 'preview_nex_form' ,
				table								: 'wap_nex_forms',
				plugin								: 'shared',
				title								: jQuery('#the_form_title').val(),
				clean_html							: clean_html.html(),
				is_form								: 'preview',
				is_template							: '0',
				post_type							: jQuery('input[name="post_type"]:checked').val(),
				post_type							: jQuery('.post_method .btn.active').attr('data-value'),
				post_action							: jQuery('.post_action .btn.active').attr('data-value'),
				custom_url							: jQuery('#on_form_submission_custum_url').val(),
				mail_to								: jQuery('#nex_autoresponder_recipients').val(),
				from_address						: jQuery('#nex_autoresponder_from_address').val(),
				from_name							: jQuery('#nex_autoresponder_from_name').val(),
				on_screen_confirmation_message		: jQuery('#nex_autoresponder_on_screen_confirmation_message').parent().find('.trumbowyg-editor').html(),
				google_analytics_conversion_code	: jQuery('#google_analytics_conversion_code').val(),
				confirmation_page					: jQuery('#nex_autoresponder_confirmation_page').val(),
				user_email_field					: jQuery('#nex_autoresponder_user_email_field').val(),
				confirmation_mail_subject			: jQuery('#nex_autoresponder_confirmation_mail_subject').val(),
				user_confirmation_mail_subject		: jQuery('#nex_autoresponder_user_confirmation_mail_subject').val(),
				confirmation_mail_body				: jQuery('#nex_autoresponder_confirmation_mail_body').parent().find('.trumbowyg-editor').html(),
				on_form_submission					: jQuery('.on_form_submission .btn.active').attr('data-value'),
				hidden_fields						: hidden_fields,
				conditional_logic					: cl_array,
				admin_email_body					: jQuery('#nex_autoresponder_admin_mail_body').parent().find('.trumbowyg-editor').html(),
				bcc									: jQuery('#nex_admin_bcc_recipients').val(),
				bcc_user_mail						: jQuery('#nex_autoresponder_bcc_recipients').val(),
				custom_css							: jQuery('#set_custom_css').val(),
				is_paypal							: jQuery('.go_to_paypal .btn.active').attr('data-value'),
				form_type							: jQuery('.form_attr .form_type').text(),
				draft_Id							: 0,
				products							: product_array,
				currency_code						: (jQuery('.paypal-column select[name="currency_code"]').val()) ? jQuery('.paypal-column select[name="currency_code"]').val() : 'USD',
				business							: jQuery('.paypal-column input[name="business"]').val(),
				cmd									: '_cart',
				return_url							: jQuery('.paypal-column input[name="return"]').val(),
				cancel_url							: jQuery('.paypal-column input[name="cancel_url"]').val(),
				lc									: (jQuery('.paypal-column select[name="paypal_language_selection"]').val()) ? jQuery('.paypal-column select[name="paypal_language_selection"]').val() : 'US',
				environment							: jQuery('.paypal-column .paypal_environment .btn.active').attr('data-value')
				};
				
			if(jQuery(this).hasClass('template_only'))
				{
				data.is_form = '0';
				data.is_template = '1';
				data.action = 'insert_nex_form';
				}
			else
				{
				data.is_template = '0';
				}
							
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					if(response)
						{
						jQuery('.show_form_preview').attr('src',jQuery('.site_url').text() + '/wp-admin/admin.php?page=nex-forms-preview&form_Id='+response);
						
						setTimeout(
								function()
									{
									jQuery('.load_preview').html('');
									jQuery('.load_preview').hide();
									jQuery('.show_form_preview').show();
									}
									,3000
								);						
							}
					jQuery('div.clean_html').html('');
					
					}
				);
			}
		);
	
	function change_device(obj){
		
		if(obj.hasClass('desktop'))
			jQuery('#preview_form').animate({width:1199},300);
		if(obj.hasClass('laptop'))
			jQuery('#preview_form').animate({width:991},300);
		if(obj.hasClass('tablet'))
			jQuery('#preview_form').animate({width:800},300);
		if(obj.hasClass('mobile'))
			jQuery('#preview_form').animate({width:320},300);
	}
	jQuery('.change_device').live('click',
		function()
			{
			jQuery('.change_device').removeClass('btn-primary');
			jQuery(this).addClass('btn-primary');
			change_device(jQuery(this));
			}
		);	
	jQuery('.close_panel .btn').live('click',
		function()
			{
			jQuery('.center_panel').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp');
			jQuery('.center_panel').addClass('admin_animated').addClass('bounceOutUp');
			jQuery('.menu-item').removeClass('active');
			show_canvas_panels();
			jQuery('.center_panel').hide();
			}
		);	

	
	
	
	}
);