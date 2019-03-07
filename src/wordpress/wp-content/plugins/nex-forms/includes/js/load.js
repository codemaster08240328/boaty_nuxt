// JavaScript Document
var strPos = 0;
var timer;
var help_text_timer;

(function($)
	{
	
	$.extend($.fn, {
	
		rightClick: function(handler) {
			$(this).each( function() {
				$(this).mousedown( function(e) {
					var evt = e;
					$(this).mouseup( function() {
						$(this).unbind('mouseup');
						if( evt.button == 2 ) {
							handler.call( $(this), evt );
							return false;
						} else {
							return true;
						}
					});
				});
				$(this)[0].oncontextmenu = function() {
					return false;
				}
			});
			return $(this);
		},		
		
		rightMouseDown: function(handler) {
			$(this).each( function() {
				$(this).mousedown( function(e) {
					if( e.button == 2 ) {
						handler.call( $(this), e );
						return false;
					} else {
						return true;
					}
				});
				$(this)[0].oncontextmenu = function() {
					return false;
				}
			});
			return $(this);
		},
		
		rightMouseUp: function(handler) {
			$(this).each( function() {
				$(this).mouseup( function(e) {
					if( e.button == 2 ) {
						handler.call( $(this), e );
						return false;
					} else {
						return true;
					}
				});
				$(this)[0].oncontextmenu = function() {
					return false;
				}
			});
			return $(this);
		},
		
		noContext: function() {
			$(this).each( function() {
				$(this)[0].oncontextmenu = function() {
					return false;
				}
			});
			return $(this);
		}
		
	});
	
})(jQuery);
(function($)
	{
	$(document).ready
		(
		function()
			{
			
			
			$('.material_design_field.input-field select').material_select();
			
			
			
			jQuery(document).on('click', '.deactivate_license', function(){
				var data =
						{
						action	:  'deactivate_license' 
						};
					
					jQuery('.deactivate_license').html('<span class="fa fa-spin fa-spinner"></span> Deactivating...')
									
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							jQuery('.deactivate_license').html('<span class="fa fa-thumbs-up"></span> License Deactivated')
							}
						);
					}
				);
				
				
			//FIRST RUN
			/*if(!getCookie('nex_forms_special_popupv6'))
				setCookie('nex_forms_special_popupv6','yes','365');
			
			if(getCookie('nex_forms_special_popupv6')=='yes')
				jQuery('#first_run').modal({show:true});*/
			
			
			
				
			//jQuery('#reviews').modal({show:true});
			//jQuery('#demo').modal({show:true});
			
			$(document).on('click','a.docs',
				function()
					{
					jQuery('#documentation').modal({show:true});
					$('#documentation').find('iframe').attr('src','http://basixonline.net/nex-forms/nex-forms-documentation/');
					}
				);
			
			$(document).on('click','a.videos',
				function()
					{
					jQuery('#videos').modal({show:true});
					$('#videos').find('iframe').attr('src','http://basixonline.net/nex-forms/nex-forms-videos/');
					}
				);
				
			
			$('#show_first_run').change(
				function()
					{
					if($(this).prop('checked')==true)
						setCookie('nex_forms_special_popupv6','no','365');
					else
						setCookie('nex_forms_special_popupv6','yes','365');
					}
				);
			
			
	
			$(window).bind("beforeunload", function() { 
				
				if($('button.not_saved').size()>0)
					return "You have some unsaved forms"; 
			});
				
			
			
			shortcut.add("Ctrl+Alt+N",
			function(e) {
				e.preventDefault();
				jQuery('a.new_form').trigger('click');
				}
			);
			
			shortcut.add("Ctrl+Alt+O",
			function(e) {
				e.preventDefault();
				jQuery('a.open-form').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+Up",
			function(e) {
				jQuery('a#upload_form').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+S",
			function(e) {
				e.preventDefault();
				jQuery('a.save_nex_form').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+Shift+S",
			function(e) {
				e.preventDefault();
				jQuery('a.save_nex_form.is_template').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+Down",
			function(e) {
				e.preventDefault();
				jQuery('a#export_current_form').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+C",
			function(e) {
				e.preventDefault();
				jQuery('a.conditional-logic').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+P",
			function(e) {
				e.preventDefault();
				jQuery('a.form-preview').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+V",
			function(e) {
				e.preventDefault();
				jQuery('a.form-entries').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+E",
			function(e) {
				e.preventDefault();
				jQuery('a.email-setup').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+U",
			function(e) {
				e.preventDefault();
				jQuery('a.field-pref').trigger('click');
				}
			);
			shortcut.add("Ctrl+Alt+F2",
			function(e) {
				e.preventDefault();
				jQuery('a.tutorial').trigger('click');
				}
			);
			
			$(document).on('change','select[name="set_hidden_field_value"]',
				function()
					{
					$(this).closest('.input-group').find('.hidden_field_value').val($(this).val());
					$(this).find('option').prop('selected',false);
					}
				);
			
			
			
			$(document).on('click','.the-thumb',
				function()
					{
					$(this).closest('.form_field').find('.the-thumb').removeClass('checked').removeClass('text-success').removeClass('text-danger');
					if($(this).hasClass('fa-thumbs-o-up'))
						$(this).addClass('text-success').addClass('checked')						
					if($(this).hasClass('fa-thumbs-o-down'))
						$(this).addClass('text-danger').addClass('checked')
					}
				);
			$(document).on('click','.the-smile',
				function()
					{
					$(this).closest('.form_field').find('.the-smile').removeClass('checked').removeClass('text-success').removeClass('text-danger').removeClass('text-warning');
					if($(this).hasClass('fa-smile-o'))
						$(this).addClass('text-success').addClass('checked')						
					if($(this).hasClass('fa-frown-o'))
						$(this).addClass('text-danger').addClass('checked')
					if($(this).hasClass('fa-meh-o'))
						$(this).addClass('text-warning').addClass('checked')
					}
				);
			
			$('#form_name').keyup(
				function()
					{
					nf_form_modified('name change');
					$('.open_task_'+$('#form_update_id').text()).html($(this).val())	
					}
				);
			
			$(document).on('keyup change','.right_hand_col input, .right_hand_col textarea, .right_hand_col select, .admin-modal input, .admin-modal textarea, .admin-modal select, .trumbowyg-editor',
				function()
					{
					nf_form_modified('change');
					}
				);
			$(document).on('click','.right_hand_col button, .right_hand_col .input-group-addon',
				function()
					{
					nf_form_modified('change');
					}
				);
			
				
			/*window.oncontextmenu = function (e){
				e.preventDefault();
			  //alert('Right Click')
			  console.log(jQuery(this).attr('class'))
			}*/
			
			// Capture right click
			/*$('[data-toggle="help-text"]').rightClick( function(e) {
				
				jQuery('.help-text-bar').animate(
							{
								marginTop:-57
							},300);
				jQuery('.help-text-bar').fadeIn();
				
				help_text_timer = setTimeout
					(
					function()
						{
						jQuery('.help-text-bar').animate(
							{
								marginTop:-57
							},300)	
							jQuery('.help-text-bar').fadeOut();
						},4000
					)
				jQuery('.help-text-bar').html(jQuery(this).attr('data-help-text'));
			});
			
			// Capture right mouse down
			/*$("#selector").rightMouseDown( function(e) {
				// Do something
			});*/
			
			// Capture right mouseup
			/*$("#selector").rightMouseUp( function(e) {
				// Do something
			});*/
			
			// Disable context menu on an element
			/*$("#selector").noContext();
						
			*/
			$('.field_selection li a').click(
				function()
					{
					//alert($(this).attr('data-show-field'));
					$('.shown-fields').text($(this).text());
					$('.field_selection li').removeClass('active');
					$(this).parent().addClass('active');
					$('.fields-column .form_field').hide();
					
					$('.fields-column .form_field.'+ $(this).attr('data-show-field') +'_fields').show();
					}
				);
			
			$('[data-toggle="popover"]').popover({html:true})
			
			jQuery('input[name="popup_text"]').keyup(
				function()
					{
					jQuery(this).closest('.tab-pane').find('span.popup_text').html(jQuery(this).val()).addClass('alert-success');
					
					}
				);
			jQuery('select[name="popup_type"]').change(
				function()
					{
					jQuery(this).closest('.tab-pane').find('span.popup_type').html(jQuery(this).val()).addClass('alert-success');
					}
				);
			jQuery('select[name="popup_button_color"]').change(
				function()
					{
					jQuery(this).closest('.tab-pane').find('span.popup_button_color').text(jQuery(this).val()).addClass('alert-success');
					}
				);
			
			jQuery('input[name="form_name"]').val('');
			
			//$('body').append('<div class="open_user_alerts"><i class="fa fa-comment"></i></div><div class="user_alerts"><h2><span class="fa fa-comment"></span>&nbsp;&nbsp;Notifications  <i title="Close" class="fa fa-close"></i><i title="Clear Notifications" class="fa fa-trash-o"></i>&nbsp;&nbsp;</h2><div class="alerts_inner"></div></div>');
			
			load_email_setup(0);
			load_pdf_setup(0);
			load_options_setup(0);
			load_hidden_fields(0);
			buid_paypal_products(0);
			load_conditional_logic(0);
			/* TOP NAV CLICK EVENTS */
			
			
			
				
			/* OPEN FORM */
			jQuery('.open-form').live('click',
			function()
				{
				jQuery('#saved_forms').modal({show:true});
				load_forms('',0);
				//jQuery('.center_panel').hide(); 
				/*if(jQuery(this).hasClass('active'))
					{
					/*jQuery('.open_form_panel').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp');
					jQuery('.open_form_panel').addClass('admin_animated').addClass('bounceOutUp');
					jQuery(this).removeClass('active');
					show_canvas_panels();
					jQuery('.new_form_panel').hide(); // setTimeout(function(){ jQuery('.preview_panel').hide() },800);*/
				/*	}
				else
					{
					load_forms('');
					/*$('.toolbar .menu-item').removeClass('active');
					setTimeout(function(){
						jQuery('.open_form_panel').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp');
								jQuery('.open_form_panel').addClass('admin_animated').addClass('bounceInDown').show();
						},500);
					jQuery(this).addClass('active');
					hide_canvas_panels();*/
				/*	}*/
				}
			);
			
			
			jQuery('.field-pref').live('click',
			function()
				{
				jQuery('#preferences').modal({show:true});
				}
			);
			jQuery('.license-info').live('click',
			function()
				{
				jQuery('#license_info').modal({show:true});
				}
			);
			
			jQuery('.global_email_settings').live('click',
			function()
				{
				jQuery('#global_email_setup').modal({show:true});
				
				}
			);
			
			jQuery('.global_admin_settings').live('click',
			function()
				{
				jQuery('#global_admin_setup').modal({show:true});
				}
			);
			
			
			jQuery('.setup_mail_chimp').live('click',
			function()
				{
				jQuery('#mailchimpsetup').modal({show:true});
				
				}
			);
			
			jQuery('.setup_get_response').live('click',
			function()
				{
				jQuery('#get_response').modal({show:true});
				
				}
			);
			
			jQuery('.ts-js-inc').live('click',
			function()
				{
				jQuery('#global_js_inc').modal({show:true});
				}
			);
			
			jQuery('.ts-css-inc').live('click',
			function()
				{
				jQuery('#global_css_inc').modal({show:true});
				}
			);
			
			/* LOGIC */
			jQuery('.conditional-logic').live('click',
			function()
				{
				jQuery('#conditional_logic_window').modal({show:true});
				
				if(!jQuery('.task-items-bar .task-con-logic').attr('class'))
					jQuery('.task-items-bar').append('<button data-target-window="conditional_logic_window" class="btn task-window task-con-logic active"><i class="fa fa-random"></i></div>')
					
				}
			);
			
			/* EMBED FORM */
			jQuery('.form-embed').live('click',
			function()
				{
				jQuery('#embed_form').modal({show:true});
					
				if(!jQuery('.task-items-bar .task-embed').attr('class'))
					jQuery('.task-items-bar').append('<button  data-target-window="embed_form" class="btn task-window task-embed active"><i class="fa fa-code"></i></div>')

				
				}
			);
			jQuery('.tab-form-to-post').live('click',
			function()
				{
				set_ftp_field_map();
				}
			);
			$(document).on('mouseover','.sc_normal',
				function()
					{
					$(this).hide();
					
					$('.sc_normal_text').text($(this).find('.copy_code').text());
					$('.sc_normal_text').show();
					}
				);
			
			$(document).on('mouseout','.sc_normal_text',
				function()
					{
					$(this).hide();
					$('.sc_normal').show();
					}
				);
				
			$(document).on('mouseover','.sc_popup_button',
				function()
					{
					$(this).hide();
					
					$('.sc_popup_button_text').text($(this).find('.copy_code').text());
					$('.sc_popup_button_text').show();
					}
				);
			
			$(document).on('mouseout','.sc_popup_button_text',
				function()
					{
					$(this).hide();
					$('.sc_popup_button').show();
					}
				);
			
			/* FORM ENTRIES */
			jQuery('.form-entries').live('click',
			function()
				{
				jQuery('#load_form_entries').modal({show:true});
				
				if(!jQuery('.task-items-bar .task-entries').attr('class'))
					jQuery('.task-items-bar').append('<button  data-target-window="load_form_entries" class="btn task-window task-entries active"><i class="fa fa-database"></i></div>')

				
				load_form_entries(jQuery('#nex-forms #form_update_id').text());
					
					
				}
			);
			
			/* SETUP EMAIL */
			jQuery('.email-setup').live('click',
			function()
				{
				jQuery('#autoresponder').modal({show:true});
				
				if(!jQuery('.task-items-bar .task-email').attr('class'))
					jQuery('.task-items-bar').append('<button  data-target-window="autoresponder" class="btn task-window task-email active"><i class="fa fa-envelope"></i></div>')

				jQuery('#autoresponder .nav-tabs a').first().trigger('click');	
				setup_email_tags();
				}
			);
			
			/* SETUP EMAIL */
			jQuery('.pdf-setup').live('click',
			function()
				{
				jQuery('#pdf_creator').modal({show:true});
				
				if(!jQuery('.task-items-bar .task-pdf').attr('class'))
					jQuery('.task-items-bar').append('<button  data-target-window="pdf_creator" class="btn task-window task-pdf active"><i class="fa fa-file-pdf-o"></i></div>')

				setup_pdf_tags();
				}
			);
			
			/* SETUP FORM OPTIONS */
			jQuery('.form-options').live('click',
			function()
				{
				
				set_mc_field_map();
					
				jQuery('#submit_options_window').modal({show:true});
				
				if(!jQuery('.task-items-bar .task-submit-options').attr('class'))
					jQuery('.task-items-bar').append('<button data-target-window="submit_options_window" class="btn task-window task-submit-options active"><i class="fa fa-gear"></i></div>')
				}
			);
			
			/* SETUP HIDDEN FIELDS */
			jQuery('.add-hidden-fields').live('click',
			function()
				{
				jQuery('#setup_hidden_fields').modal({show:true});
				
				if(!jQuery('.task-items-bar .task-hidden-fields').attr('class'))
					jQuery('.task-items-bar').append('<button data-target-window="setup_hidden_fields" class="btn task-window task-hidden-fields active"><i class="fa fa-eye-slash"></i></div>')
				}
			);
			
			$(document).on('click','.task-window',
				function()
					{
					if(!$(this).hasClass('active'))
						{
						$('#'+ $(this).attr('data-target-window')).modal({show:true});
						$('#'+ $(this).attr('data-target-window')).modal('show');
						$('#'+ $(this).attr('data-target-window')+ ' .modal-dialog').trigger('click');
						set_mc_field_map();
						}
					}
			);
			
			$(document).on('click','.task-window.active',
				function()
					{
					$(this).removeClass('active');
					$('#'+ $(this).attr('data-target-window')).modal('hide');
					}
			);
			$(document).on('click','.modal .close',
				function()
					{
					jQuery('.task-window.'+$(this).closest('.modal').attr('data-task-target')).remove();
					}
			);
			$(document).on('click','.minimize',
				function()
					{
					 jQuery('.task-window.'+$(this).closest('.modal').attr('data-task-target')).removeClass('active');
					}
			);
			
			
			
			
			
			jQuery('.setup_email_panel .admin_email').click(
				function()
					{
					jQuery('.setup_email_panel .btn').removeClass('active');
					jQuery('.setup_email_panel .admin_email .btn').addClass('active');
					jQuery('.admin_email_setup').show();
					jQuery('.user_email_setup').hide();
					}
				);
			jQuery('#autoresponder .nav-tabs a').click(
				function()
					{
					var posible_email_fields = '<option value="">Dont send confirmation mail to user</option>';	
			var has_email_fields = false;
			jQuery('div.nex-forms-container div.form_field input.email').each(
				function()
					{
					has_email_fields = true;
					posible_email_fields += '<option value="'+  jQuery(this).attr('name') +'" '+ ((jQuery('.nex_form_attr .user_email_field').text()==jQuery(this).attr('name')) ? 'selected="selected"' : '') +' >'+ jQuery(this).closest('div.form_field').find('.the_label').text() +'</option>';
					}
				);
			/*if(!has_email_fields)
				jQuery('.no-email').removeClass('hidden');
			else
				jQuery('.no-email').addClass('hidden');*/
				
			jQuery('select[name="posible_email_fields"]').html(posible_email_fields);
			
			
			jQuery('select[name="posible_email_fields"] option').each(
				function()
					{
						var get_selected = jQuery(this).closest('select');
						if(jQuery(this).val()==get_selected.attr('data-selected'))
							{
							jQuery(this).attr('selected','selected');
							}
						}
					);
					
					}
				);
			jQuery(document).on('change', '#nex_autoresponder_user_email_field', function()
				{
				jQuery(this).attr('data-selected',jQuery(this).val());
				}
			);
			jQuery(document).on('change', '.ftp_reponse_setup select', function()
				{
				jQuery(this).attr('data-selected',jQuery(this).val());
				}
			);
			
			
			
			
			
			
			
			
			
			
			jQuery('.setup_options_panel .on_submit').click(
				function()
					{
					jQuery('.setup_options_panel .center_panel_button .btn').removeClass('active');
					jQuery('.setup_options_panel .on_submit .btn').addClass('active');
					jQuery('.on_submit_setup').show();
					jQuery('.hidden_fields_setup').hide();
					}
				);
			jQuery('.setup_options_panel .hidden_fields').click(
				function()
					{
					jQuery('.setup_options_panel .center_panel_button .btn').removeClass('active');
					jQuery('.setup_options_panel .hidden_fields .btn').addClass('active');
					jQuery('.on_submit_setup').hide();
					jQuery('.hidden_fields_setup').show();
					}
				);
			
			
			$(document).on('click','.add_hidden_field',
				function()
					{
					var hf_clone = $('.hidden_field_clone').clone();
					hf_clone.removeClass('hidden').removeClass('hidden_field_clone').addClass('hidden_field');
					
					$('.hidden_fields_setup .hidden_fields').append(hf_clone);
					
					}
				);
				
			$(document).on('click','.remove_hidden_field',
				function()
					{
					$(this).closest('.hidden_field').remove();
					}
				);
			
			
			$(document).on('click','.on_submit_setup .post_action button',
				function()
					{
					$('.on_submit_setup .post_action button').removeClass('active');
					$(this).addClass('active');
					
					if($(this).hasClass('custom'))
						{
						$('.ajax_settings, .on_form_submission').addClass('hidden');
						$('.custom_url_settings, .post_method').removeClass('hidden');	
						}
					else
						{
						$('.ajax_settings, .on_form_submission').removeClass('hidden');
					    $('.custom_url_settings, .post_method').addClass('hidden');
						
						if($('.on_form_submission .btn.message').hasClass('active'))
							{
							$('.on_screen_message_settings').removeClass('hidden');
							$('.redirect_settings').addClass('hidden');
							}
						else
							{
							$('.on_screen_message_settings').addClass('hidden');	
							$('.redirect_settings').removeClass('hidden');
							}
						
						}
					
					}
				);
			$(document).on('click','.on_submit_setup .post_method button',
				function()
					{
					$('.on_submit_setup .post_method button').removeClass('active');
					$(this).addClass('active');
					}
				);
			$(document).on('click','.on_submit_setup .on_form_submission button',
				function()
					{
					$('.on_submit_setup .on_form_submission button').removeClass('active');
					$(this).addClass('active');
					
						if($(this).hasClass('message'))
							{
							$('.on_screen_message_settings').removeClass('hidden');
							$('.redirect_settings').addClass('hidden');
							}
						else
							{
							$('.on_screen_message_settings').addClass('hidden');	
							$('.redirect_settings').removeClass('hidden');
							}
					
					}
				);
			
			
			
			
			
			/* EXTRA STYLING */
			jQuery('.form-styling').live('click',
			function()
				{
				jQuery('.center_panel').hide();
				jQuery('.con-logic-column').hide();
				jQuery('.paypal-column').hide();
				if(jQuery(this).hasClass('active'))
					{
					jQuery('.extra-styling-column').hide();
					if(jQuery('.currently_editing').attr('id'))
						jQuery('.field-settings-column').show()
					jQuery(this).removeClass('active');
					}
				else
					{
					$('.toolbar .menu-item').removeClass('active');
					show_canvas_panels();
					jQuery('.extra-styling-column').show();
					jQuery('.extra-styling-column').removeClass('admin_animated').removeClass('flipOutY').removeClass('flipInY').removeClass('pulse');
					jQuery('.extra-styling-column').addClass('admin_animated').addClass('flipInY');
					jQuery('.field-settings-column').hide();
					
					jQuery('#close-extra-styling').show();
						
					jQuery(this).addClass('active');
					}
				}
			);
			
			/* PAYPAL */
			jQuery('.paypal-options').live('click',
			function()
				{
				jQuery('.right_hand_col').hide();				
				jQuery('.paypal-column').show();
				
				jQuery('.paypal-column .field-setting-categories .tab').show();
				
				jQuery('#add_paypal_product').hide();
				
				if(jQuery(this).hasClass('paypal-setup'))
					jQuery('.paypal-column .field-setting-categories #paypal_setup').trigger('click');
				if(jQuery(this).hasClass('map-items'))
					jQuery('.paypal-column .field-setting-categories #paypal_product_list').trigger('click');
							
				set_paypal_fields();
					
				}
			);
			jQuery(document).on('change', '.paypal-column select', function()
				{
				jQuery(this).attr('data-selected',jQuery(this).val());
				}
			);
			
			jQuery(document).on('click', '#paypal_setup', function()
				{
				jQuery('.paypal-column .tab').removeClass('active');
				jQuery('.inner .paypal_items_list').hide();
				jQuery('.inner .paypal_setup').show();
				jQuery('#add_paypal_product').hide();
				jQuery(this).addClass('active');
				}
			);
			
			jQuery(document).on('click', '#paypal_product_list', function()
				{
				jQuery('.paypal-column .tab').removeClass('active');
				jQuery('.inner .paypal_items_list').show();
				jQuery('.inner .paypal_setup').hide();
				jQuery('#add_paypal_product').show();
				jQuery(this).addClass('active');
				}
			);
			$(document).on('click','.go_to_paypal button',
				function()
					{
					$('.go_to_paypal  button').removeClass('active');
					$(this).addClass('active');
					}
				);
			$(document).on('click','.paypal_environment button',
				function()
					{
					$('.paypal_environment button').removeClass('active');
					$(this).addClass('active');
					}
				);
			

/*************************************************************************************/
/***************************** DUPLICATE FORMS ***************************************/
/*************************************************************************************/
jQuery('.duplicate_record').live('click',
		function()
			{
			var data =
				{
				action	 						: 'nf_duplicate_record',
				table							: 'wap_nex_forms',
				Id								: jQuery(this).attr('id')
				};
			jQuery('.saved_forms').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					popup_user_alert('Form Duplicated');
					load_forms(response,0);
					}
				);
			}
		);
/*************************************************************************************/
/******************************* DELETE FORMS ****************************************/
/*************************************************************************************/
jQuery('.delete_the_calendar').live('click',
		function()
			{		
			jQuery(this).closest('.list-group-item').find('.do_permanent_delete').addClass('opened');
			}
		);

jQuery('.dont_delete, .do_delete').live('click',
		function()
			{		
			jQuery(this).closest('.list-group-item').find('.do_permanent_delete').removeClass('opened');
			}
		);

		
jQuery('.do_delete').live('click',
		function()
			{
			if(jQuery(this).attr('id') == jQuery('#form_update_id').text().trim())
				{
				jQuery('#form_update_id').text('');
				jQuery('#the_form_title').val('');
				//jQuery('div.nex-forms-container').html('');
				}
			//jQuery(this).closest('a').css('background','#d9534f')
			jQuery(this).closest('tr').slideUp('slow');
			
			var data =
				{
				action	 						: 'nf_delete_record',
				table							: 'wap_nex_forms',
				Id								: jQuery(this).attr('id')
				};	
			clearTimeout(timer);	
			jQuery.post
				(
				ajaxurl, data, function(response)
					{		
					popup_user_alert('Form Deleted');			
					}
				);
			}
		);


jQuery('.do_delete_entry').live('click',
		function()
			{
				
			jQuery(this).closest('tr').slideUp('slow');
			
			var data =
				{
				action	 						: 'nf_delete_record',
				table							: 'wap_nex_forms_entries',
				Id								: jQuery(this).attr('id')
				};		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{					
					}
				);
			}
		);

			
			
			
			//REMOVE UNWANTED STYLESHEETS
			var link_id = '';
			var css_link = '';
			jQuery('head link').each(
				function()
					{
					css_link = jQuery(this);
					link_id = jQuery(this).attr('id');
					jQuery('.unwanted_css_array .unwanted_css').each(
						function()
							{
							if(link_id)
								{
								if(link_id.trim()==jQuery(this).text())
									css_link.attr('href','');
								}
							}
						);
					
					}
				)
			
			$('[data-toggle="tooltip"]').tooltip({ delay:{ "show": 200, "hide": 0 } })
	
		//	$('.collapse').collapse();
			/*$(document).on('click', '.field-category', 
				function()
					{
						
					$('.field-category').removeClass('active');
					$('.fields-column .form_field').hide();
					var get_class = $(this).attr('class');
					set_class = get_class.replace('field-category','').replace(' ','')
					
					$('.fields-column .'+set_class).show();
					$(this).addClass('active');
					}
				);
			
			$('.field-category.form_fields').trigger('click');*/
			
			/*$('.new-form').click(
				function()
					{
					$('.field-category-column').removeClass('admin_animated').removeClass('bounceOutUp').removeClass('bounceInUp')
					if($(this).hasClass('active'))
						{
						$('.field-category-column').addClass('admin_animated bounceInDown');
						$(this).removeClass('active')
						}
					else
						{
						$('.field-category-column').addClass('admin_animated bounceOutUp');
						$(this).addClass('active')
						}
					}
				)*/
			
			
			
			
			//Delete Field
			jQuery('.field_settings .btn.delete').live('click',
				function()
					{
						var get_field = jQuery(this).closest('.form_field');
						
						if(get_field.attr('id')==jQuery('.field-settings-column .current_id').text())
							jQuery('#close-settings').trigger('click');
						
						get_field.remove();
						nf_form_modified('field delete');
						
					}
				);
			jQuery('.step .zero-clipboard .btn.delete').live('click',
				function()
					{
					jQuery(this).closest('.step').fadeOut('fast',
					function()
						{
						jQuery(this).remove();	
						nf_count_multi_steps();
						}
					);
				}
			);
			
			jQuery('select[name="skip_to_step"').change(
				function()
					{
					if(jQuery(this).val()!=0)
						{
						jQuery('.nex-forms-container .step').hide()
						jQuery('.nex-forms-container .nf_multi_step_'+ jQuery(this).val()).show()
						}
					else
						{
						jQuery('.nex-forms-container .step').show()
						}
					}
				);
			
			
			jQuery('span.delete-field').live('click',
				function()
					{
					$('#'+current_id).fadeOut(
						'fast',function()
							{
							jQuery(this).remove();	
							setTimeout(function(){ jQuery('span.delete-field').removeClass('btn-success'); },1500);
							}
						);
					}
				);
			
			$('div.nex-forms-container .form_field').live('mouseover',
			function()
				{
				if(!jQuery('div.nex-forms-container').hasClass('selecting_conditional_target') && !jQuery(this).hasClass('step') && !jQuery(this).hasClass('grid'))
					{
					$(this).find('.field_settings').first().show();
					$(this).find('.btn-lg.move_field').first().show();
					}
				})
			
			$('div.nex-forms-container .form_field.grid').live('mouseover',
			function()
				{
				if(!$(this).hasClass('step'))
				$(this).find('.field_settings').last().show();
				}
			);
			$('div.nex-forms-container .form_field').live('mouseout',
				function()
					{
					$(this).find('.field_settings').hide();
					}
				);
			}
		);
/*************************************************************************************/
/********************************* OPEN FORM *****************************************/
/*************************************************************************************/	
	jQuery('.make_full_screen').live('click',
		function()
			{
			if(jQuery(this).hasClass('active'))
				{
				jQuery('.form-canvas-column').removeClass('full_screen');
				jQuery('.fields-column').removeClass('full_screen');
				jQuery('.field-settings-column').removeClass('full_screen');
				jQuery('.form-name-col').removeClass('full_screen');
				jQuery('.con-logic-column').removeClass('full_screen');
				jQuery('.paypal-column').removeClass('full_screen');
				
				jQuery('.fields-column .paddle').hide();
				jQuery(this).removeClass('active');
				}
			else
				{
				jQuery('.form-canvas-column').addClass('full_screen');
				jQuery('.fields-column').addClass('full_screen');
				jQuery('.field-settings-column').addClass('full_screen');
				jQuery('.form-name-col').addClass('full_screen');
				jQuery('.con-logic-column').addClass('full_screen');
				jQuery('.paypal-column').addClass('full_screen');
				jQuery('.fields-column .paddle').show();
				jQuery(this).addClass('active');
				}
			}
		);
	jQuery('.paddle').live('click',
		function()
			{
			if(jQuery(this).hasClass('active'))
				{
				jQuery(this).parent().removeClass('opened');
				jQuery(this).removeClass('active');
				}
			else
				{
				jQuery(this).parent().addClass('opened');
				jQuery(this).addClass('active');
				}
			}
		);
	
	
	jQuery('.trigger_create_new_form').live('click',
		function()
			{
			jQuery('.new_form').trigger('click');
			$('#saved_forms .close').trigger('click');
			}
		);
		
	jQuery('.new_form').live('click',
		function()
			{
			jQuery('#new_form_wizard').modal({show:true});
			jQuery('#new_form_wizard .step_2').hide();
			jQuery('#new_form_wizard .step_1').show();
			jQuery('.go_back').hide();
			load_forms('',1);
			}
		);
	
	jQuery('.go_back').live('click',
		function()
			{
			jQuery('#new_form_wizard .step_2').hide();
			jQuery('#new_form_wizard .step_1').show();
			jQuery('.go_back').hide();
			}
		);
	
	jQuery('#new_form_wizard .create_new_form').live('click',
		function()
			{
			var form_type = jQuery(this).attr('data-form-type');
			jQuery('input[name="form_name"]').val('');
			
			
			
			jQuery('#nex-forms #form_update_id').text('');
			jQuery('form[name="do_csv_export"] input[name="nex_forms_Id"]').val('');
			if(form_type=='normal')
				{
				jQuery('.nex-forms-container').html('');
				
				jQuery('#close-settings').trigger('click');
				jQuery('#close-logic').trigger('click');
				jQuery('#new_form_wizard .close').trigger('click');
				}
			
			if(form_type=='template')
				{
				jQuery('#new_form_wizard .step_1').hide();
				jQuery('#new_form_wizard .get_form_templates').show();
				jQuery('.go_back').show();
				}
			
			
			jQuery('.toolbar .form-entries').addClass('disabled');
			jQuery('.toolbar .export-csv').addClass('disabled');
			jQuery('.toolbar .export-pdf').addClass('disabled');
			jQuery('.toolbar .form-embed').addClass('disabled');
			jQuery('#export_current_form').addClass('disabled');
			jQuery('#export_current_form').attr('href','');
			jQuery('.toolbar .export-csv').attr('href','');
			jQuery('.toolbar .export-pdf').attr('href','');
			
			load_email_setup(0);
			load_pdf_setup(0);
			load_options_setup(0, form_type);
			load_hidden_fields(0);
			load_conditional_logic(0);
			buid_paypal_products(0);
			
			jQuery('.form_attr .form_type').text(form_type)
			
			}
		);
	
	$(document).on('click','.task-item',
		function()
			{
			nf_save_nex_form(jQuery('.task-menu-item.last_edit').attr('data-form-id'),'draft',jQuery(this));
			
			jQuery('#saved_forms .close').trigger('click')
			jQuery('#new_form_wizard .close').trigger('click')
			jQuery('.open_form_panel_content tr').removeClass('active');
			
			jQuery('div.nex-forms-container').html('<div class="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>')
			
			jQuery('.task-menu-item').removeClass('active');
			jQuery('.task-menu-item').removeClass('last_edit');
			jQuery(this).addClass('active').addClass('last_edit');	
			
			
			
			jQuery('#form_name').val(jQuery(this).text())
			
			var the_ID = jQuery(this).attr('data-form-id');
			
			jQuery('input[name="nex_forms_Id"]').val(the_ID);
			
			jQuery('#nex-forms #form_update_id').text(the_ID);
			jQuery('form[name="do_csv_export"] input[name="nex_forms_Id"]').val(the_ID);
			
			//show_canvas_panels();
			//jQuery('.center_panel').hide();
			
			set_embed_form_options(the_ID);
			
			if(jQuery(this).closest('tr').hasClass('is_template'))
				{
				jQuery('input[name="nex_forms_Id"]').val('');
				jQuery('#nex-forms #form_update_id').text('');
				}
			
			
			load_nexform(jQuery(this).attr('data-form-id'),'draft');
			
			
			if(jQuery(this).hasClass('not_saved'))
				jQuery('button.save_nex_form').html('<i class="glyphicon glyphicon-floppy-disk"></i> Save *');
			
			}
		);
	jQuery('td.open_form').live('click',
		function()
			{
			
			nf_save_nex_form(jQuery('.task-menu-item.last_edit').attr('data-form-id'),'draft', jQuery(this));
			
			jQuery('#saved_forms .close').trigger('click')
			jQuery('#new_form_wizard .close').trigger('click')
			jQuery('.open_form_panel_content tr').removeClass('active');
			
			jQuery('div.nex-forms-container').html('<div class="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>')
			jQuery(this).closest('tr').addClass('active');	
			
			jQuery('#form_name').val(jQuery(this).closest('tr').find('.the_form_title').text())
			
			var the_ID = jQuery(this).closest('tr').attr('id').trim();
			
			jQuery('input[name="nex_forms_Id"]').val(the_ID);
			
			jQuery('#nex-forms #form_update_id').text(the_ID);
			jQuery('form[name="do_csv_export"] input[name="nex_forms_Id"]').val(the_ID);
			
			jQuery('.open-form').removeClass('active');
			
			//show_canvas_panels();
			//jQuery('.center_panel').hide();
			
			set_embed_form_options(the_ID);
			
			if(jQuery(this).closest('tr').hasClass('is_template'))
				{
				jQuery('input[name="nex_forms_Id"]').val('');
				jQuery('#nex-forms #form_update_id').text('');
				}
			
			
			load_nexform(the_ID);			
			nf_add_task_item(the_ID);
			}
		);
	
	$(document).on('click', '.save_nex_form', 
		function()
			{
			jQuery('button.save_nex_form').addClass('saving').html('<span class="fa fa-spin fa-refresh"></span>');
			nf_save_nex_form(0,1, jQuery(this));
			}
		);	
	
	
	$('.duplicate_field').live('click',
		function()
			{
			
			var get_field = $(this).closest('.form_field');
			var duplication = get_field.clone();
			$(duplication).insertAfter(get_field);
			duplication.attr('id','_' + Math.round(Math.random()*99999));
			duplication.find('.form_field').each(
				function()
					{
					$(this).attr('id','_' + Math.round(Math.random()*99999));
					}
				);
			jQuery(duplication).find('.edit').trigger('click');
			nf_form_modified('field duplicated');
			
			var panel = duplication.find('.panel-body');
			create_droppable(panel)
			
			//setTimeout(function(){ jQuery('.col2 .admin-panel .panel-heading .btn.glyphicon-hand-down').trigger('click');},300 );
			}
		);
	
	
	//PAYPAL PRODUCTS
	
			$('.paypal_product .input-group-addon').live('click',
				function()
					{
					if(!$(this).hasClass('is_label'))
							{
							$(this).parent().find('.input-group-addon').removeClass('active');
							$(this).addClass('active');
							
							if($(this).hasClass('static_value'))
								{
								if($(this).parent().hasClass('pp_product_quantity'))
									$(this).parent().find('input[name="set_quantity"]').val('static');
								if($(this).parent().hasClass('pp_product_amount'))
									$(this).parent().find('input[name="set_amount"]').val('static');
									
								
								$(this).parent().find('input[type="text"]').removeClass('hidden')
								$(this).parent().find('select').addClass('hidden')
								}
							else
								{
								if($(this).parent().hasClass('pp_product_quantity'))
									$(this).parent().find('input[name="set_quantity"]').val('map');
								if($(this).parent().hasClass('pp_product_amount'))
									$(this).parent().find('input[name="set_amount"]').val('map');
									
									
								$(this).parent().find('select').removeClass('hidden')
								$(this).parent().find('input[type="text"]').addClass('hidden')
								}
							}
					}
				)
			jQuery('#add_paypal_product').live('click',
				function()
					{
					var pp_clone = $('.paypal_product_clone').clone();
					pp_clone.removeClass('hidden').removeClass('paypal_product_clone').addClass('paypal_product');

					$('.paypal_products').append(pp_clone);
					
					pp_clone.find('.product_number').text($('.paypal_products .paypal_product').size());
					
					jQuery(".paypal_products").animate(
							{
							scrollTop:(jQuery(".paypal_product").height()*$('.paypal_products .paypal_product').size())+200
							},500
						);
					
			
					var set_current_fields_math_logic = '<option value="0" selected="selected">--- Map Field --</option>';
						set_current_fields_math_logic += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_current_fields_math_logic += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_math_logic += '</optgroup>';
						
						set_current_fields_math_logic += '<optgroup label="Radio Buttons">';
						
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_current_fields_math_logic += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_current_fields_math_logic += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_current_fields_math_logic += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								old_check = jQuery(this).attr('name');
								if(old_check != new_check)
									set_current_fields_math_logic += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_math_logic += '</optgroup>';
						
						set_current_fields_math_logic += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_math_logic += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_math_logic += '</optgroup>';
						
						set_current_fields_math_logic += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_math_logic += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_math_logic += '</optgroup>';
					
						set_current_fields_math_logic += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_current_fields_math_logic += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_math_logic += '</optgroup>';
						
						
						
					pp_clone.find('select').html(set_current_fields_math_logic);
		
					
					
					}
				);
				
			$('.remove_paypal_product').live('click',
				function()
					{
					$('.remove_paypal_product').remove('btn-primary');
					$(this).closest('.paypal_product').remove();
					$('.paypal_products .paypal_product').each(
						function(index)
							{
							$(this).find('.product_number').text(index+1);
							}
						);
					}
				);
	
	
	
	jQuery(document).on('click', '.verify_purchase_code', function(){
		var data =
				{
				action	:  'get_data' ,
				eu		:	jQuery('#envato_username').val(),
				pc		:	jQuery('#purchase_code').val()
				};
			
			
			jQuery('.verify_purchase_code').html('<span class="fa fa-spin fa-spinner"></span> Verifying')
							
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('.show_code_response').html(response);
					jQuery('.verify_purchase_code').html('Activate');
					}
				);
			}
		);

	
	jQuery(document).on('blur focus keyup change click', '.trumbowyg-editor', function(){
			
			}
		);
	
	$('select[name="email_field_tags"]').live('dblclick',
		function(){
			var txtarea = jQuery('#nex_autoresponder_admin_mail_body').parent().find('.trumbowyg-editor');
			
			var text =jQuery(this).val();
			//var strPos = txtarea.selectorStart.length;
			var html = txtarea.html();
			var front = html.substring(0,html.length);  
			var backtext = html.substring(strPos,html.length); 
			txtarea.html(front+text);	
		
		}
	);
	
	$('select[name="user_email_field_tags"]').live('dblclick',
		function(){
			var txtarea = jQuery('#nex_autoresponder_confirmation_mail_body').parent().find('.trumbowyg-editor');
			
			var text =jQuery(this).val();
			//var strPos = txtarea.selectorStart.length;
			var html = txtarea.html();
			var front = html.substring(0,html.length);  
			var backtext = html.substring(strPos,html.length); 
			txtarea.html(front+text);	
		}
	);
	
	$('select[name="pdf_field_tags"]').live('dblclick',
		function(){
			var txtarea = jQuery('#nex_pdf_html').parent().find('.trumbowyg-editor');
			
			var text =jQuery(this).val();
			//var strPos = txtarea.selectorStart.length;
			var html = txtarea.html();
			var front = html.substring(0,html.length);  
			var backtext = html.substring(strPos,html.length); 
			txtarea.html(front+text);	
		
		}
	);
	
	
	jQuery(document).on('click', '.user_alerts .fa-trash-o', function(){
			jQuery('.alerts_inner').html('');
			}
		);
	jQuery(document).on('click', '.user_alerts .fa-close', function(){
			$('.user_alerts').animate(
				{
					bottom:-300
				},300, function(){ jQuery('.open_user_alerts').show() } )
			}
		);
	jQuery(document).on('click', '.user_alerts', function(){
			clearTimeout(timer);	
			}
		);
	
	jQuery(document).on('click', '.open_user_alerts', function(){
			jQuery('.open_user_alerts').hide()
			$('.user_alerts').animate(
				{
					bottom:0
				},300)
			}
		);
		
	jQuery(document).on('click','.menu-item .about_nf', function()
		{
		jQuery('#about_nf').modal({show:true});
		}
	);
	
	
	jQuery(document).on('change','select[name="mc_current_fields"]', function()
		{
		jQuery(this).attr('data-selected',jQuery(this).val())	
		}
	);
	jQuery(document).on('change','select[name="mail_chimp_lists"]', function()
		{
		jQuery(this).attr('data-selected',jQuery(this).val());
		
		var data =
			{
			action	 						: 'reload_mc_form_fields',
			reload_mc_list					: 'true',
			form_Id							: jQuery('#form_update_id').text(),
			mc_list_id						: jQuery(this).val(),
			};
		jQuery('.mc_field_map').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('.mc_field_map').html(response);
				set_mc_field_map();
				}
			);
		
		}
	);
	
	
	jQuery(document).on('change','select[name="gr_current_fields"]', function()
		{
		jQuery(this).attr('data-selected',jQuery(this).val())	
		}
	);
	jQuery(document).on('change','select[name="get_response_lists"]', function()
		{
		jQuery(this).attr('data-selected',jQuery(this).val());
		
		var data =
			{
			action	 						: 'reload_gr_form_fields',
			reload_gr_list					: 'true',
			form_Id							: jQuery('#form_update_id').text(),
			gr_list_id						: jQuery(this).val(),
			};
		jQuery('.gr_field_map').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('.gr_field_map').html(response);
				set_gr_field_map();
				}
			);
		
		}
	);
	
	
	
	
	}
)(jQuery);

/*************************************************************************************/
/******************************** LOAD FORMS *****************************************/
/*************************************************************************************/
	function load_forms(form_id, get_templates){
		var data =
				{
				action			: 'nf_get_forms',
				cal_id  		: form_id,
				get_templates 	: get_templates
				};
		if(!get_templates)
			jQuery('.modal-body.saved_forms').html('<div class="loading">Loading<span class="fa fa-spin fa-circle-o-notch"></span></div>');
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				if(get_templates)
					jQuery('.get_form_templates').html(response);
				else
					jQuery('.modal-body.saved_forms').html(response);
				
				//if(jQuery('#form_update_id').text())
				//		jQuery('.open_form_panel_content a#'+jQuery('#form_update_id').text().trim()).addClass('active');
				}
			);
		}
	
	function load_ftp_field_map(the_ID,status){
		var data =
			{
			action	 						: 'nexforms_ftp_setup',
			form_Id							: the_ID,
			status							: status
			};
		jQuery('.ftp_reponse_setup').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('.ftp_reponse_setup').html(response);
				set_ftp_field_map();
				}
			);	
	}
	
	function load_conditional_logic(the_ID,status){
		var data =
				{
				action	 							: 'nf_load_conditional_logic',
				form_Id								: the_ID,
				status								: status
				};		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('.set_rules').html(response);
					
					jQuery('.set_rules .set_rule_conditions').removeClass('set_rule_conditions').addClass('the_rule_conditions');
					jQuery('.set_rules .set_rule_actions').removeClass('set_rule_actions').addClass('the_rule_actions')
					
					
					
					
					if(jQuery('.customcon').size()>0)
						{
						var convert_conditions = '<div class="old-conditions alert alert-info"><i class="fa fa-arrow-down"></i> Old Conditions to be converted on save.<div data-content="These are rules created with versions older then version 5. If you already re-created these below conditions in version 5 or higher then please kindly delete them before you save.<br><br>This message will not appear again after you saved this form." data-toggle="popover" data-placement="left" class="fa fa-question-circle" data-original-title="" title=""></div></div>';
						
						jQuery('.customcon').each(
							function()
								{
								var get_id = jQuery(this).attr('class');
								get_id = get_id.replace(' ','');
								get_id = get_id.replace('customcon','');
								get_id = get_id.replace('field_','');
								
								var get_condition = jQuery(this).attr('data-condition')
								var set_condition = 'equal_to';
								if(get_condition=="Equal to")
									set_condition = 'equal_to';
								if(get_condition=="Greater than")
									set_condition = 'greater_than';
								if(get_condition=="Less than")
									set_condition = 'less_than';
								
								
								convert_conditions += '<div class="panel new_rule converted">';
								convert_conditions += '<div class="panel-heading advanced_options"><button aria-hidden="true" data-dismiss="modal" class="close delete_rule" type="button"><span class="fa fa-close "></span></button></div>';
								convert_conditions += '<div class="panel-body">';
								convert_conditions +=  '<div class="col-xs-7 con_col">';
										convert_conditions +=  '<h3 class="advanced_options"><strong><div class="badge rule_number">1</div>IF</strong> ';
											convert_conditions +=  '<select id="operator" style="width:15%; float:none !important; display: inline" class="form-control" name="selector">';
												convert_conditions +=  '<option value="any" selected="selected"> any </option>';
												convert_conditions +=  '<option value="all"> all </option>';
											convert_conditions +=  '</select> ';
										convert_conditions +=  'of these conditions are true</h3>';
										convert_conditions +=  '<div class="get_rule_conditions">';
											convert_conditions +=  '<div class="the_rule_conditions">';
											convert_conditions +=  '<span class="statment_head"><div class="badge rule_number">1</div>IF</span> <select name="fields_for_conditions" class="form-control cl_field" style="width:33%;" covert-selected="'+ get_id +'">';
													convert_conditions +=  '<option selected="selected" value="0">-- Field --</option>';
												convert_conditions +=  '</select>';
												convert_conditions +=  '<select name="field_condition" class="form-control" style="width:28%;" covert-selected="'+ set_condition +'" data-selected="'+ set_condition +'">';
													convert_conditions +=  '<option selected="selected" value="0">-- Condition --</option>';
													convert_conditions +=  '<option value="equal_to">Equal To</option>';
													convert_conditions +=  '<option value="not_equal_to">Not Equal To</option>';
													convert_conditions +=  '<option value="less_than">Less Than</option>';
													convert_conditions +=  '<option value="greater_than">Greater Than</option>';
													/*convert_conditions +=  '<option value="contains">Contains</option>';
													convert_conditions +=  '<option value="not_contians">Does not Contain</option>';
													convert_conditions +=  '<option value="is_empty">Is Empty</option>';*/
												convert_conditions +=  '</select>';
												convert_conditions +=  '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value" value="'+ jQuery(this).attr('data-value') +'">';
												convert_conditions +=  '<button class="btn btn-sm btn-default delete_condition advanced_options" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
										convert_conditions +=  '</div>';
									convert_conditions +=  '</div>';
										
										convert_conditions +=  '<button class="btn btn-sm btn-default add_condition advanced_options" style="width:100%;">Add Condition</button>';
									convert_conditions +=  '</div>';
									
									//THEN
									convert_conditions +=  '<div class="col-xs-5 con_col">';
										convert_conditions +=  '<h3 class="advanced_options" style="margin-top:8px !important;padding-bottom:4px !important;">THEN</h3>';
										convert_conditions +=  '<div class="get_rule_actions">';
											convert_conditions +=  '<div class="the_rule_actions">';
											convert_conditions +=  '<span class="statment_head">THEN</span> <select name="the_action" class="form-control" style="width:40%;" covert-selected="'+ jQuery(this).attr('data-action') +'">';
												convert_conditions +=  '<option selected="selected" value="0">-- Action --</option>';
												convert_conditions +=  '<option value="show">Show</option>';
												convert_conditions +=  '<option value="hide">Hide</option>';
											convert_conditions +=  '</select>';
											convert_conditions +=  '<select name="cla_field" class="form-control" style="width:45%;" covert-selected="'+ jQuery(this).attr('data-target') +'">';
											convert_conditions +=  '</select>';
											convert_conditions +=  '<button class="btn btn-sm btn-default delete_action advanced_options" style="width:15%;"><span class="fa fa-close"></span></button>';
											convert_conditions +=  '<button class="btn btn-sm btn-default delete_simple_rule" style="width:15%;"><span class="fa fa-close"></span></button>';
											
											convert_conditions +=  '<div style="clear:both;"></div>';
														
											convert_conditions += '</div>';
										convert_conditions += '</div>';
										convert_conditions += '<button class="btn btn-sm btn-default add_action advanced_options" style="width:100%;">Add Action</button>';
										
										convert_conditions += '<div class="else_condition" style="display:none">';
										 convert_conditions += '<h3 style="margin-top:8px !important;padding-bottom:4px !important;">ELSE</h3>';
											convert_conditions += '<input type="radio"  value="true" checked="checked"> Reverse actions<br />';
											convert_conditions += '<input type="radio"  value="false"> Dont reverse actions';
										convert_conditions += '</div>';
									convert_conditions += '</div>';
								convert_conditions += '</div>';
								convert_conditions += '</div>';
								jQuery(this).remove();
								}
							);
							jQuery('.set_rules').append(convert_conditions);
							
							jQuery('[data-toggle="popover"]').popover({html:true})
						}
					
					set_c_logic_fields();
					reset_rule_complexity();
					
					/*jQuery('.converted select option').each(
								function()
									{
									var get_selected = jQuery(this).closest('select');
									if(strstr(jQuery(this).val(),get_selected.attr('covert-selected')))
										{
										jQuery(this).prop('selected');
										}
									}
								);
					
					jQuery('.converted select option:selected').trigger('click');
					jQuery('.converted select option:selected').trigger('click');*/
					
					}
				);
	}
		
	function load_form_templates(form_id){
		var data =
				{
				action	: 'load_templates',
				cal_id  : form_id
				};
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.template_forms').html(response);
				}
			);
		}
	
	
		
	function load_email_setup(form_id,status){
		
		var data =
				{
				action	: 'nf_get_email_setup',
				form_Id : form_id,
				status	: status
				};
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.setup_email_panel_content').html(response);
				jQuery('#nex_autoresponder_user_email_field option:selected').trigger('click');

				jQuery('#nex_autoresponder_admin_mail_body').trumbowyg();
				jQuery('#nex_autoresponder_confirmation_mail_body').trumbowyg();
				
				setup_email_tags();
				}
			);
		
	}
	
	
	function load_pdf_setup(form_id,status){
		
		var data =
				{
				action	: 'nf_get_pdf_setup',
				form_Id : form_id,
				status	: status
				};
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.pdf-creator-content').html(response);

				jQuery('#nex_pdf_html').trumbowyg();
				
				setup_pdf_tags();
				}
			);
		
	}
	
	
	function load_form_entries(form_id){

		var data = 	
			{
			action	 			: 'nf_load_form_entries',
			page	 			: jQuery('input[name="page"]').val(),
			order	 			: jQuery('input[name="order"]').val(),
			orderby	 			: jQuery('input[name="orderby"]').val(),
			current_page		: jQuery('input[name="current_page"]').val(),
			additional_params	: jQuery('input[name="additional_params"]').val(),
			form_Id				: form_id
			};
		
		jQuery('div.form_entries_panel_content').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>');
		
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.form_entries_panel_content').html(response);
				}
			);
	
		var data = 	
			{
			action	 			: 'nf_load_pagination',
			plugin_alias		: jQuery('input[name="plugin_alias"]').val(),
			page	 			: jQuery('input[name="page"]').val(),
			orderby	 			: jQuery('input[name="orderby"]').val(),
			order	 			: jQuery('input[name="order"]').val(),
			current_page		: jQuery('input[name="current_page"]').val(),
			additional_params	: jQuery('input[name="additional_params"]').val(),
			form_Id				: form_id
			};
			
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.pagination-links').html(response);
				}
			);
	}
	
	
	
	
	jQuery('a.iz-next-page').live('click',
		function()
			{
			
			var get_page = 	 parseInt(jQuery('input[name="current_page"]').val());	
				
			if((get_page+2) > parseInt(jQuery('span.total-pages').html()))
				 return false;
			
			get_page = get_page+1
			 
			jQuery('input[name="current_page"]').val(get_page);
			//function populate_list(args,table,page,plugin_alias,additional_params)
			load_form_entries(jQuery('#form_update_id').text());
			}
		);
	
	jQuery('a.iz-prev-page').live('click',
		function()
			{
			var get_page = 	 parseInt(jQuery('input[name="current_page"]').val());	
			if(get_page<=0)
				 return false;
			
			get_page = get_page-1
			jQuery('input[name="current_page"]').val(get_page);
			load_form_entries(jQuery('#form_update_id').text());
			}
		);
	jQuery('a.iz-first-page').live('click',
		function()
			{
			jQuery('input[name="current_page"]').val(0);
			load_form_entries(jQuery('#form_update_id').text());
			}
		);
		
	jQuery('a.iz-last-page').live('click',
		function()
			{
			var get_val = parseInt(jQuery('span.total-pages').html())-1;
			jQuery('input[name="current_page"]').val(get_val);
			load_form_entries(jQuery('#form_update_id').text());
			}
		);
	
	jQuery('th a span.sortable-column').live('click',
		function()
			{
			jQuery('input[name="orderby"]').val(jQuery(this).attr('data-col-name'));
			
			jQuery('th a').removeClass('asc');
			jQuery('th a').removeClass('desc');
			//populate_list(args,table,page,plugin_alias,additional_params)
			load_form_entries(jQuery('#form_update_id').text());
			
			if(jQuery(this).attr('data-col-order')=='asc')
				{
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	removeClass('asc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	addClass('desc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a span.sortable-column').attr('data-col-order','desc');
				}
			else
				{
					
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	removeClass('desc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	addClass('asc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a span.sortable-column').attr('data-col-order','asc');
				}
			jQuery('input[name="order"]').val(jQuery(this).attr('data-col-order'));
			
			}
		);
	
	
	jQuery('.view_form_entry').live('click',
		function()
			{
			var data = 
				{
				action	 				: 'nf_populate_form_entry',
				form_entry_Id			: jQuery(this).attr('data-id')
				};
			jQuery('#viewFormEntry .modal-body').html('Loading...<span class="fa fa-spin fa-spinner"></span>');
				jQuery.post
					(
					ajaxurl, data, function(response)
						{
						jQuery('#viewFormEntry .modal-body').html(response);
						}
					);
			}
		);
	
	
	function buid_paypal_products(form_id,status){
			var data =
				{
				action	 							: 'nf_buid_paypal_products',
				nex_forms_Id						: form_id,
				status								: status
				};
			jQuery.post
					(
					ajaxurl, data, function(response)
						{
						//alert(response)
						setTimeout(function(){
							
							jQuery('.paypal-column .inner').html(response);
							
							set_paypal_fields();
							
							jQuery('.paypal-column select option:selected').trigger('click');

						},500);
						
						}
					);
			}
	
	function load_options_setup(form_id,form_type,status){

		var data =
				{
				action	  : 'nf_get_options_setup',
				form_Id   : form_id,
				form_type : form_type,
				status	  : status
				};
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.setup_options_panel_content').html(response);
				jQuery('#nex_autoresponder_on_screen_confirmation_message').trumbowyg();
				
				if(!jQuery('#form_name').val())
					jQuery('#form_name').val(jQuery('.form_attr .form_title').html());
				
				}
			);
		
	}
	
	
	function load_hidden_fields(form_id,status){

		var data =
				{
				action	: 'nf_hidden_fields',
				form_Id : form_id,
				status	: status
				};
		jQuery.post
			(
			ajaxurl, data, function(response)
				{
				jQuery('div.setup_hidden_fields_content').html(response);				
				}
			);
		
	}
	
	function setup_email_tags(){
		var set_email_tags = '';
						set_email_tags += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_email_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
								}
							);	
						set_email_tags += '</optgroup>';
						
						set_email_tags += '<optgroup label="Radio Buttons">';
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_email_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_email_tags += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_email_tags += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								var check_name = jQuery(this).attr('name').replace('[]','')
									
								old_check = check_name;
								if(old_check != new_check)
									set_email_tags += '<option value="{{'+ format_illegal_chars(check_name)  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								new_check = old_check;
								}
							);	
						set_email_tags += '</optgroup>';
						
						set_email_tags += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_email_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_email_tags += '</optgroup>';
						
						set_email_tags += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_email_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_email_tags += '</optgroup>';
						
						
						set_email_tags += '<optgroup label="File Uploaders">';
						jQuery('div.nex-forms-container div.form_field input[type="file"]').each(
							function()
								{
								set_email_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_email_tags += '</optgroup>';
						
						set_email_tags += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_email_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_email_tags += jQuery('.hidden_form_fields').html()
						set_email_tags += '</optgroup>';
						
						
						set_email_tags += '<optgroup label="More Tags">';
						set_email_tags += '<option value="{{nf_form_data}}">Form Data Table</option>';
						set_email_tags += '<option value="{{nf_user_ip}}">IP Address</option>';
						set_email_tags += '<option value="{{nf_from_page}}">Page Title</option>';
						set_email_tags += '<option value="{{nf_form_title}}">Form Title</option>';
						set_email_tags += '<option value="{{nf_user_name}}">User Name</option>';
						
					
						
						set_email_tags += '</optgroup>';
						
						
						
					jQuery('select[name="email_field_tags"], select[name="user_email_field_tags"]').html(set_email_tags);
						
	}

function setup_pdf_tags(){
		var set_pdf_tags = '';
						set_pdf_tags += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_pdf_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
								}
							);	
						set_pdf_tags += '</optgroup>';
						
						set_pdf_tags += '<optgroup label="Radio Buttons">';
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_pdf_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_pdf_tags += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_pdf_tags += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								var check_name = jQuery(this).attr('name').replace('[]','')
									
								old_check = check_name;
								if(old_check != new_check)
									set_pdf_tags += '<option value="{{'+ format_illegal_chars(check_name)  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								new_check = old_check;
								}
							);	
						set_pdf_tags += '</optgroup>';
						
						set_pdf_tags += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_pdf_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_pdf_tags += '</optgroup>';
						
						set_pdf_tags += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_pdf_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_pdf_tags += '</optgroup>';
						
						
						set_pdf_tags += '<optgroup label="File Uploaders">';
						jQuery('div.nex-forms-container div.form_field input[type="file"]').each(
							function()
								{
								set_pdf_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_pdf_tags += '</optgroup>';
						
						set_pdf_tags += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_pdf_tags += '<option value="{{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}}">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_pdf_tags += jQuery('.hidden_form_fields').html()
						set_pdf_tags += '</optgroup>';
							
						
						set_pdf_tags += '<optgroup label="More Tags">';
						set_pdf_tags += '<option value="{{nf_form_data}}">Form Data Table</option>';
						set_pdf_tags += '<option value="{{nf_user_ip}}">IP Address</option>';
						set_pdf_tags += '<option value="{{nf_from_page}}">Page Title</option>';
						set_pdf_tags += '<option value="{{nf_form_title}}">Form Title</option>';
						set_pdf_tags += '<option value="{{nf_user_name}}">User Name</option>';
						
					
						
						set_pdf_tags += '</optgroup>';
						
						
						
					jQuery('select[name="pdf_field_tags"]').html(set_pdf_tags);
						
	}	

	
function set_paypal_fields(){
	var set_current_fields_paypal = '<option value="0" selected="selected">--- Map Field --</option>';
						set_current_fields_paypal += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Radio Buttons">';
						
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_current_fields_paypal += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								old_check = jQuery(this).attr('name');
								if(old_check != new_check)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
					
						set_current_fields_paypal += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += jQuery('.hidden_form_fields').html()
						set_current_fields_paypal += '</optgroup>';
						
						
						
					jQuery('.paypal_products').find('select').html(set_current_fields_paypal);
					
					jQuery('.paypal-column').find('select option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('data-selected'))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
}

function set_mc_field_map(){
	var set_current_fields_paypal = '<option value="0" selected="selected">--- Map Field --</option>';
						set_current_fields_paypal += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Radio Buttons">';
						
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_current_fields_paypal += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								old_check = jQuery(this).attr('name');
								if(old_check != new_check)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						set_current_fields_paypal += '<optgroup label="Hidden Fields">';
							set_current_fields_paypal += jQuery('.hidden_form_fields').html()
						set_current_fields_paypal += '</optgroup>';
						
						
					jQuery('.mc_field_map').find('select').html(set_current_fields_paypal);
					
					jQuery('.mc_field_map').find('select option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('data-selected'))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
}


function set_gr_field_map(){
	var set_current_fields_paypal = '<option value="0" selected="selected">--- Map Field --</option>';
						set_current_fields_paypal += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Radio Buttons">';
						
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_current_fields_paypal += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								old_check = jQuery(this).attr('name');
								if(old_check != new_check)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						set_current_fields_paypal += '<optgroup label="Hidden Fields">';
							set_current_fields_paypal += jQuery('.hidden_form_fields').html()
						set_current_fields_paypal += '</optgroup>';
						
						
						
						
					jQuery('.gr_field_map').find('select').html(set_current_fields_paypal);
					
					jQuery('.gr_field_map').find('select option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('data-selected'))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
}

function set_ftp_field_map(){
	var set_current_fields_paypal = '<option value="0" selected="selected">--- Map Field --</option>';
						set_current_fields_paypal += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Radio Buttons">';
						
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_current_fields_paypal += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								old_check = jQuery(this).attr('name');
								if(old_check != new_check)
									set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						set_current_fields_paypal += '<optgroup label="File Uploaders">';
						jQuery('div.nex-forms-container div.form_field input[type="file"]').each(
							function()
								{
								set_current_fields_paypal += '<option value="'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ jQuery(this).attr('name') +'</option>';
								}
							);	
						set_current_fields_paypal += '</optgroup>';
						
						
						set_current_fields_paypal += '<optgroup label="Hidden Fields">';
							set_current_fields_paypal += jQuery('.hidden_form_fields').html()
						set_current_fields_paypal += '</optgroup>';
						
						
					jQuery('.ftp-form-field').find('select').html(set_current_fields_paypal);
					
					jQuery('.ftp-form-field').find('select option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('data-selected'))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
}



function set_c_logic_fields(){
	
					var set_current_fields_conditional_logic = '<option selected="selected" value="0">-- Field --</option>';
						var set_current_action_fields_conditional_logic ='';
						set_current_fields_conditional_logic += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								if(jQuery(this).closest('.form_field').hasClass('date'))
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**date##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('datetime'))
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**datetime##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('time'))
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**time##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('star-rating'))
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**hidden##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**text##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Radio Buttons">';
						var old_radio = '';
						var new_radio = '';
						
						jQuery('div.nex-forms-container div.form_field input[type="radio"]').each(
							function()
								{
								old_radio = jQuery(this).attr('name');
								if(old_radio != new_radio)
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**radio##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
								new_radio = old_radio;
								
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						var old_check = '';
						var new_check = '';
						set_current_fields_conditional_logic += '<optgroup label="Check Boxes">';
						jQuery('div.nex-forms-container div.form_field input[type="checkbox"]').each(
							function()
								{
								old_check = jQuery(this).attr('name');
								if(old_check != new_check)
									set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**checkbox##'+ jQuery(this).attr('name')  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**select##'+ jQuery(this).attr('name')  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**textarea##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						
						set_current_fields_conditional_logic += '<optgroup label="File Uploaders">';
						jQuery('div.nex-forms-container div.form_field input[type="file"]').each(
							function()
								{
								set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**file##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_current_fields_conditional_logic += '<option value="'+ jQuery(this).closest('.form_field').attr('id') +'**hidden##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Buttons">';
						jQuery('div.nex-forms-container div.form_field.submit-button').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option value="'+ jQuery(this).attr('id') +'**button##button">'+ jQuery(this).find('.the_input_element').text() +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Panels">';
						jQuery('div.nex-forms-container div.form_field.is_panel').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option value="'+ jQuery(this).attr('id') +'**panel##panel">'+ short_str(jQuery(this).find('.panel-heading').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Headings">';
						jQuery('div.nex-forms-container div.form_field.heading').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option value="'+ jQuery(this).attr('id') +'**heading##heading">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="HTML/Paragraphs">';
						jQuery('div.nex-forms-container div.form_field.html').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option value="'+ jQuery(this).attr('id') +'**paragraph##html">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						jQuery('div.nex-forms-container div.form_field.paragraph').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option value="'+ jQuery(this).attr('id') +'**heading##html">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						
					jQuery('select[name="fields_for_conditions"]').html(set_current_fields_conditional_logic);
					
					jQuery('select[name="cla_field"]').html(set_current_fields_conditional_logic + set_current_action_fields_conditional_logic);
					
					jQuery('select[name="fields_for_conditions"] option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('data-selected') || strstr(jQuery(this).val(),get_selected.attr('covert-selected')))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
					jQuery('select[name="cla_field"] option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('data-selected') || strstr(jQuery(this).val(),get_selected.attr('covert-selected')))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
					jQuery('select[name="field_condition"] option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('covert-selected'))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
					jQuery('select[name="the_action"] option').each(
						function()
							{
							var get_selected = jQuery(this).closest('select');
							if(jQuery(this).val()==get_selected.attr('covert-selected'))
								{
								jQuery(this).attr('selected','selected');
								}
							}
						);
					
					jQuery('.cl_field option:selected').trigger('click');
					jQuery('select[name="cla_field"] option:selected').trigger('click');
					jQuery('select[name="field_condition"] option:selected').trigger('click');
					jQuery('select[name="the_action"] option:selected').trigger('click');
}

function popup_user_alert(msg){
	timer = setTimeout
		(
		function()
			{
			jQuery('.user_alerts').animate(
				{
					bottom:-300
				},300, function(){ jQuery('.open_user_alerts').show() })	
			},4000
		)
	jQuery('.open_user_alerts').hide()
	jQuery('.user_alerts').animate(
		{
		bottom:0
		},300	
	);
	var currentdate = new Date(); 
	jQuery('.alerts_inner').prepend('<p class="alert_item"><span class="alert_time">'+ currentdate.getHours() +':'+ currentdate.getMinutes() +'</span>&nbsp;'+ msg +'</p>')
}

function load_nexform(the_ID,the_status){
		
			
			
			jQuery('button.save_nex_form').removeClass('saving').html('<i class="glyphicon glyphicon-floppy-disk"></i> Save');				
						
			jQuery('#export_current_form').attr('href',jQuery('.admin_url').text()+ 'admin.php?page=nex-forms-main&nex_forms_Id='+ the_ID +'&export_form=true');
			jQuery('.toolbar .export-csv').attr('href',jQuery('.admin_url').text()+ 'admin.php?page=nex-forms-main&nex_forms_Id='+ the_ID +'&export_csv=true');
			
			var data =
				{
				action	 							: 'nf_load_nex_form',
				form_Id								: the_ID,
				status							: the_status
				};		
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					jQuery('div.nex-forms-container').html(response)
					jQuery('div.nex-forms-container #star' ).raty('destroy');
						jQuery('#close-settings').trigger('click');
					
					jQuery('.custom-prefix').addClass('text').removeClass('custom-prefix')
					jQuery('.custom-postfix').addClass('text').removeClass('custom-postfix')
					jQuery('.custom-pre-postfix').addClass('text').removeClass('custom-pre-postfix')
					
					jQuery('span.opened_form_title').html('<i class="fa fa-folder-open">&nbsp;</i>&nbsp;' + jQuery('#form_name').val());
					
					load_email_setup(the_ID,the_status);
					load_pdf_setup(the_ID,the_status);
					load_conditional_logic(the_ID,the_status);
					buid_paypal_products(the_ID,the_status);
					load_options_setup(the_ID,'',the_status);
					load_hidden_fields(the_ID,the_status);
					load_ftp_field_map(the_ID,the_status);
					
					
					 var data =
						{
						action	 						: 'reload_mc_list',
						reload_mc_list					: 'true',
						form_Id							: the_ID,
						};
					jQuery('.mail_chimp_setup').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							jQuery('.mail_chimp_setup').html(response);
							}
						);
						
					var data =
						{
						action	 						: 'reload_mc_form_fields',
						reload_mc_list					: 'true',
						form_Id							: the_ID,
						};
					jQuery('.mc_field_map').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							jQuery('.mc_field_map').html(response);
							set_mc_field_map();
							}
						);
						
						
					 var data =
						{
						action	 						: 'reload_gr_list',
						reload_gr_list					: 'true',
						form_Id							: the_ID,
						};
					jQuery('.get_reponse_setup').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							jQuery('.get_reponse_setup').html(response);
							}
						);
						
					var data =
						{
						action	 						: 'reload_gr_form_fields',
						reload_gr_list					: 'true',
						form_Id							: the_ID,
						};
					jQuery('.gr_field_map').html('<div class="loading">Loading <i class="fa fa-circle-o-notch fa-spin"></i></div>')		
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							jQuery('.gr_field_map').html(response);
							set_gr_field_map();
							}
						);
					
					setup_email_tags();
					set_c_logic_fields();
					reset_rule_complexity();
					set_paypal_fields();
					
					jQuery('div.nex-forms-container .field_settings').html('<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div><div title="Edit Field Attributes" class="btn btn-default btn-xs edit"><i class="fa fa-edit"></i></div><div class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div><div title="Delete field" class="btn btn-default btn-xs delete"><i class="fa fa-close"></i></div>')
					
						
					
					var set_current_fields = '';
						jQuery('div.nex-forms-container div.form_field .the_label').each(
							function()
								{
								set_current_fields += '<option value="{{'+ format_illegal_chars(jQuery(this).text())  +'}}">'+ jQuery(this).text() +'</option>';
								}
							);
					
						
					jQuery('select[name="current_fields"]').html(set_current_fields);
					
					jQuery('.toolbar .form-entries').removeClass('disabled');
					jQuery('.toolbar .export-csv').removeClass('disabled');
					jQuery('.toolbar .export-pdf').removeClass('disabled');
					jQuery('.toolbar .form-embed').removeClass('disabled');
					jQuery('#export_current_form').removeClass('disabled');
					
					
					if(!jQuery('link.color_scheme').attr('class'))
						{
						jQuery('.nex-forms-container').prepend('<div class="active_theme" style="display:none;">default</div>');
						}
					else
						{
						jQuery('.overall-form-settings li.'+jQuery('.active_theme').text()).trigger('click');
						}
					jQuery('div.nex-forms-container .form_field').each(
						function(index)
							{
							//jQuery(this).css('z-index',1000-index)
							setup_form_element(jQuery(this))
							
							//jQuery('.move_field').remove();
							//if(!jQuery(this).find('.move_field').length<0)
								//jQuery(this).find('.field_settings').prepend('<div title="Edit Field Attributes" class="btn btn-default btn-sm move_field"><i class="fa fa-arrows"></i></div>')
							
							
							/*if(jQuery(this).hasClass('text') || jQuery(this).hasClass('textarea'))
								{
								if(jQuery(this).find('.the_input_element').attr('data-maxlength-show')=='true')
									jQuery(this).find('.the_input_element').maxlength({ placement:(jQuery(this).find('.the_input_element').attr('data-maxlength-position')) ? jQuery(this).find('.the_input_element').attr('data-maxlength-position') : 'bottom', alwaysShow: true , set_ID: jQuery(this).attr('id'), warningClass: 'label '+ jQuery(this).find('.the_input_element').attr('data-maxlength-color') });
								}
							/*if(jQuery(this).hasClass('grid-system'))
								{
								jQuery(this).find('.input-inner').first().append('<div class="field_settings bs-callout bs-callout-info" style="display:none;"><div class="btn btn-danger btn-sm delete " title="Delete field"><i class="glyphicon glyphicon-remove"></i></div></div>');
								}*/
							}
						);
					//reset_zindex();
					nf_count_multi_steps();
					jQuery('div.nex-forms-container').find('.btn-lg.move_field').remove();
					jQuery('div.nex-forms-container .form_field select').show()
					jQuery('.radio-group a, .check-group a').addClass('ui-state-default')
					jQuery('.grid-system .panel-body').removeClass('ui-widget-content');
					jQuery('.bootstrap-select.ui-state-default').removeClass('ui-state-default');
					//jQuery('.bootstrap-select').removeClass('form-control').addClass('full_width');
					jQuery('.selectpicker, .dropdown-menu.the_input_element').addClass('ui-state-default')
					jQuery('.grid-system .panel-body').removeClass('ui-widget-content');
					jQuery('.bootstrap-select.ui-state-default').removeClass('ui-state-default');
					
					
					jQuery('.panel-heading .btn').trigger('click');
					}
				);
}

function set_embed_form_options(the_form_id){
	jQuery('#embed_form').find('span.set_embed_id').text(the_form_id);
	/*jQuery('#embed_form').find('.sc_popup_button').text('[NEXForms id="'+ the_form_id +'" open_trigger="popup" type="button" text="Open Form"]');
	jQuery('#embed_form').find('.sc_popup_link').text('[NEXForms id="'+ the_form_id +'" open_trigger="popup" type="link" text="Open Form"]');
	
	jQuery('#embed_form').find('.php_normal').html('&lt;?php NEXForms_ui_output(array("id"=>'+ the_form_id +'),true); ?&gt;');
	jQuery('#embed_form').find('.php_popup_button').html('&lt;?php NEXForms_ui_output(array("id"=>'+ the_form_id +',"open_trigger"=>"popup", "type"=>"button", "text"=>"Open Form"),true); ?&gt;');
	jQuery('#embed_form').find('.php_popup_link').html('&lt;?php NEXForms_ui_output(array("id"=>'+ the_form_id +',"open_trigger"=>"popup", "type"=>"link", "text"=>"Open Form"),true); ?&gt;');*/
}


function nf_save_nex_form(form_id,form_status, clicked_obj)
	{
	if(jQuery('#form_name').val()=='')
			{
			jQuery('#form_name').popover('show');
			setTimeout(function(){jQuery('#form_name').popover('hide'); jQuery('#form_name').popover('destroy');},2000)
			return;
			}
	
		jQuery('div.admin_html').html(jQuery('div.nex-forms-container').html())
		jQuery('div.clean_html').html(jQuery('div.nex-forms-container').html())
		
		clean_html = jQuery('div.clean_html');
		admin_html = jQuery('div.admin_html');
		
		
		
		admin_html.find('.btn-lg.move_field').remove();
		admin_html.find('#slider').html('');
		admin_html.find('.the-thumb').removeClass('text-danger').removeClass('text-success').removeClass('checked');
		admin_html.find('.js-signature canvas').remove();
		admin_html.find('#star' ).raty('destroy');
		admin_html.find('.bootstrap-touchspin-prefix').remove();
		admin_html.find('.bootstrap-touchspin-postfix').remove();
		admin_html.find('.bootstrap-touchspin .input-group-btn').remove();
		admin_html.find('.bootstrap-tagsinput').remove();
		admin_html.find('.popover').remove();
		admin_html.find('div.cd-dropdown').remove();
		admin_html.find('.form_field').removeClass('edit-field').removeClass('currently_editing');
		admin_html.find('.bootstrap-select').remove();
		admin_html.find('.popover').remove();
		
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
		
		
		var mc_field_map = '';	
		jQuery('.mc_field_map .mc-form-field').each(
			function()
				{
				mc_field_map += jQuery(this).attr('data-field-tag');
				mc_field_map += '[split]';
				mc_field_map += jQuery(this).find('select').attr('data-selected');
				mc_field_map += '[end]';
				}
			);
			
		var gr_field_map = '';	
		jQuery('.gr_field_map .gr-form-field').each(
			function()
				{
				gr_field_map += jQuery(this).attr('data-field-tag');
				gr_field_map += '[split]';
				gr_field_map += jQuery(this).find('select').attr('data-selected');
				gr_field_map += '[end]';
				}
			);
		
		var ftp_field_map = '';	
		
		jQuery('.ftp_reponse_setup .ftp-attr').each(
			function()
				{
				ftp_field_map += jQuery(this).attr('data-field-tag');
				ftp_field_map += '[split]';
				ftp_field_map += jQuery(this).find('select').attr('data-selected');
				ftp_field_map += '[end]';
				}
			);
		
		jQuery('.ftp_reponse_setup .ftp-form-field').each(
			function()
				{
				ftp_field_map += jQuery(this).attr('data-field-tag');
				ftp_field_map += '[split]';
				ftp_field_map += jQuery(this).find('select').attr('data-selected');
				ftp_field_map += '[end]';
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
											
											//ACTIONS
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
	//jQuery('.nex-forms-field-settings').removeClass('opened');
	//jQuery('.form_field').removeClass('currently_editing');
	jQuery('.current_id').text('');
	
	
	
	clean_html = jQuery('div.clean_html');
		
	clean_html.find('.btn-lg.move_field').remove();
	clean_html.find('#star' ).raty('destroy');	
	clean_html.find('.the-thumb').removeClass('text-danger').removeClass('text-success').removeClass('checked');
	clean_html.find('.js-signature canvas').remove();	
	clean_html.find('.zero-clipboard, div.ui-nex-forms-container .field_settings').remove();
	clean_html.find('.grid').removeClass('grid-system')		
	clean_html.find('.editing-field-container').removeClass('.editing-field-container')
	clean_html.find('.bootstrap-touchspin-prefix').remove();
	//clean_html.find('.bootstrap-select').remove();
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
	clean_html.find('div.form_field').removeClass('field').removeClass('currently_editing');
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
				//jQuery(this).parent().find('.input_container').addClass('full_width');
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
	//clean_html.find('.bootstrap-select').removeClass('form-control').addClass('full_width');
	clean_html.find('.selectpicker, .dropdown-menu.the_input_element').addClass('ui-state-default');
	clean_html.find('.selectpicker').removeClass('dropdown-toggle')
	clean_html.find('.is_grid .panel-body').removeClass('ui-widget-content');
	clean_html.find('.bootstrap-select.ui-state-default').removeClass('ui-state-default');
	clean_html.find('.is_grid .panel-body').removeClass('ui-sortable').removeClass('ui-droppable').removeClass('ui-widget-content').removeClass('');
	clean_html.find('.step').hide()
	clean_html.find('.step').first().show();	
		

		var take_action = 'nf_insert_record';
		
		if(jQuery('#form_update_id').text() || form_id)
			take_action = 'nf_update_record'
		if(form_status == 'preview')
			take_action = 'preview_nex_form'
		if(form_status == 'draft')
			take_action = 'nf_update_draft'
	    var active_mail_subscriptions = '';
		
	if(jQuery('input[name="mc_integration"]:checked').val()=='1')
		active_mail_subscriptions += 'mc,';
	if(jQuery('input[name="gr_integration"]:checked').val()=='1')
		active_mail_subscriptions += 'gr,';
		
	 var pdf_attachements = '';
	if(jQuery('input[name="pdf_admin_attach"]:checked').val()=='1')
		pdf_attachements += 'admin,';
	if(jQuery('input[name="pdf_user_attach"]:checked').val()=='1')
		pdf_attachements += 'user,';
		
		//clicked.html('<span class="fa fa-refresh fa-spin"></span>&nbsp;&nbsp;Saving...')
			var data =
				{
				action	 							: take_action,
				table								: 'wap_nex_forms',
				edit_Id								: (form_id) ? form_id : jQuery('#form_update_id').text().trim(),
				plugin								: 'shared',
				title								: jQuery('#form_name').val(),
				form_fields							: admin_html.html(),
				clean_html							: clean_html.html(),
				is_form								: form_status,
				is_template							: '0',
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
				environment							: jQuery('.paypal-column .paypal_environment .btn.active').attr('data-value'),
				mc_field_map						: mc_field_map,
				mc_list_id							: jQuery('select[name="mail_chimp_lists"]').attr('data-selected'),
				gr_field_map						: gr_field_map,
				gr_list_id							: jQuery('select[name="get_response_lists"]').attr('data-selected'),
				email_subscription					: active_mail_subscriptions,
				//email_on_payment_success			: (jQuery('.slide_in_paypal_setup input[name="email_on_payment_success"]:checked').val()) ? jQuery('.slide_in_paypal_setup  input[name="email_on_payment_success"]:checked').val() : 'no'
				pdf_html							: jQuery('#nex_pdf_html').parent().find('.trumbowyg-editor').html(),
				attach_pdf_to_email					: pdf_attachements,
				form_to_post_map					: ftp_field_map,
				is_form_to_post						: jQuery('.ftp_reponse_setup input[name="ftp_integration"]:checked').val(),
				};
				
			if(clicked_obj.hasClass('is_template'))
				{
				data.is_form = '0';
				data.is_template = '1';
				data.action = 'nf_insert_record';
				var is_template = '1';
				}
			else
				{
				data.is_template = '0';
				var is_template = '0';
				}
			
			
			clearTimeout(timer);				
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					if(form_status=='preview')
						{
						jQuery('.show_form_preview').attr('src',jQuery('.site_url').text() + '/wp-admin/admin.php?page=nex-forms-preview&form_Id='+response);
						
						
						//jQuery('.form_update_id').text(response.trim())
						//loading_nex_forms_preview();
						setTimeout(
								function()
									{
									jQuery('.load_preview').html('');
									jQuery('.load_preview').hide();
									jQuery('.show_form_preview').show();
									}
									,3000
								);
						jQuery('div.clean_html').html('');	
						}
					else
						{
						jQuery('div.clean_html').html('');
						jQuery('div.admin_html').html('');
						//setTimeout(function(){ clicked.html(current_button);},1500);
						if(form_status!='draft')
							{
							if(is_template=='1')
								{
								popup_user_alert('Template Saved');
								}
							else
								{
								if(jQuery('#form_update_id').text())
									popup_user_alert('Form Saved');
								else
									popup_user_alert('New Form Created');
								}
							}
							
						jQuery('.toolbar .form-entries').removeClass('disabled');
						jQuery('.toolbar .export-csv').removeClass('disabled');
						jQuery('.toolbar .export-pdf').removeClass('disabled');
						jQuery('.toolbar .form-embed').removeClass('disabled');
						jQuery('#export_current_form').removeClass('disabled');
						
						jQuery('button.save_nex_form').removeClass('saving').html('<i class="glyphicon glyphicon-floppy-disk"></i> Save');				
						if(response)
							{
							if(!is_template || is_template==0 || form_status!='draft')
								{
								jQuery('#form_update_id').text(response.trim())
								}
							jQuery('#export_current_form').attr('href',jQuery('.admin_url').text()+ 'admin.php?page=nex-forms-main&nex_forms_Id='+ response.trim() +'&export_form=true');
							jQuery('.toolbar .export-csv').attr('href',jQuery('.admin_url').text()+ 'admin.php?page=nex-forms-main&nex_forms_Id='+ response.trim() +'&export_csv=true');
							
								
							jQuery('form[name="do_csv_export"] input[name="nex_forms_Id"]').val(response.trim());
							
							nf_add_task_item(response.trim());
							jQuery('.open_task_'+response.trim()).removeClass('not_saved');
							}
						}
					}
				);	
	}
function nf_form_modified(modification){
	
	jQuery('.open_task_'+jQuery('#form_update_id').text()).addClass('not_saved');
	jQuery('button.save_nex_form').html('<i class="glyphicon glyphicon-floppy-disk"></i> Save *');
}

function nf_add_task_item(the_ID){
	jQuery('.task-menu-item').removeClass('active');
	jQuery('.task-menu-item').removeClass('last_edit');
	
	
	
	if(!jQuery('.task-menu-item.open_task_'+ the_ID).attr('data-form-id'))
		jQuery('.task-items-bar .form-list').append('<li><a data-form-id="'+the_ID+'" class="active last_edit open_task_'+ the_ID +' task-menu-item task-item"  id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" title="'+ jQuery('#form_name').val() +'"><span class="inner_text"><i class="fa fa-file">&nbsp;</i>&nbsp;'+ jQuery('#form_name').val() +'</span></a>');
	else
		jQuery('.task-menu-item.open_task_'+ the_ID).addClass('active').addClass('last_edit');
		
	
	//var count_open_forms = jQuery('.task-items-bar .task-menu-item').size();
	
	//jQuery('.task-items-bar .task-menu-item').each(
		//function()
			//{
			//jQuery(this).css('width',(Math.floor(100/count_open_forms)-1)+'%');
			//}
		//)	
}
function nf_count_multi_steps(){
	var total_steps = jQuery('.nex-forms-container .form_field.step').size();
	var set_steps = '<option selected="selected" value="0">All steps (' +total_steps+ ')</option>';
	
	jQuery('.nex-forms-container .form_field.step').each(
		function(index, element)
			{
			jQuery(this).addClass('nf_multi_step_'+(index+1));
			set_steps += '<option selected="selected" value="'+ (index+1) +'">Step '+ (index+1) +' / ' + total_steps +  '</option>';
	  
			if(!jQuery(this).find('.btn-clipboard .the_step_number').attr('class'))
				{
				jQuery(this).find('.btn-clipboard').html('<span class="badge the_step_number">Step '+ (index+1) +' / ' + total_steps +  '</span>&nbsp;<div class="btn btn-default btn-sm delete " title="Delete field"><i class="glyphicon glyphicon-remove"></i></div>');
				}
			else
				{
				jQuery(this).find('.the_step_number').html('Step '+ (index+1) +' / ' + total_steps );
				jQuery(this).addClass('nf_multi_step_'+(index+1))
				}
			}
		);
	jQuery('select[name="skip_to_step"]').html(set_steps);
}
function nf_reset_multi_steps(){
		for(var i=0;i<30;i++)
			jQuery('.nex-forms-container .form_field.step').removeClass('nf_multi_step_'+(i))
			
		jQuery('.nex-forms-container .form_field.step').each(function(index, element) {
		  jQuery(this).find('.the_step_number').html('Step '+ (index+1));
		  jQuery(this).addClass('nf_multi_step_'+(index+1))
        });
}
/**
 * http://www.openjs.com/scripts/events/keyboard_shortcuts/
 * Version : 2.01.A
 * By Binny V A
 * License : BSD
 */
shortcut = {
	'all_shortcuts':{},//All the shortcuts are stored in this array
	'add': function(shortcut_combination,callback,opt) {
		//Provide a set of default options
		var default_options = {
			'type':'keydown',
			'propagate':false,
			'disable_in_input':false,
			'target':document,
			'keycode':false
		}
		if(!opt) opt = default_options;
		else {
			for(var dfo in default_options) {
				if(typeof opt[dfo] == 'undefined') opt[dfo] = default_options[dfo];
			}
		}

		var ele = opt.target
		if(typeof opt.target == 'string') ele = document.getElementById(opt.target);
		var ths = this;
		shortcut_combination = shortcut_combination.toLowerCase();

		//The function to be called at keypress
		var func = function(e) {
			e = e || window.event;
			
			if(opt['disable_in_input']) { //Don't enable shortcut keys in Input, Textarea fields
				var element;
				if(e.target) element=e.target;
				else if(e.srcElement) element=e.srcElement;
				if(element.nodeType==3) element=element.parentNode;

				if(element.tagName == 'INPUT' || element.tagName == 'TEXTAREA') return;
			}
	
			//Find Which key is pressed
			if (e.keyCode) code = e.keyCode;
			else if (e.which) code = e.which;
			var character = String.fromCharCode(code).toLowerCase();
			
			if(code == 188) character=","; //If the user presses , when the type is onkeydown
			if(code == 190) character="."; //If the user presses , when the type is onkeydown
	
			var keys = shortcut_combination.split("+");
			//Key Pressed - counts the number of valid keypresses - if it is same as the number of keys, the shortcut function is invoked
			var kp = 0;
			
			//Work around for stupid Shift key bug created by using lowercase - as a result the shift+num combination was broken
			var shift_nums = {
				"`":"~",
				"1":"!",
				"2":"@",
				"3":"#",
				"4":"$",
				"5":"%",
				"6":"^",
				"7":"&",
				"8":"*",
				"9":"(",
				"0":")",
				"-":"_",
				"=":"+",
				";":":",
				"'":"\"",
				",":"<",
				".":">",
				"/":"?",
				"\\":"|"
			}
			//Special Keys - and their codes
			var special_keys = {
				'esc':27,
				'escape':27,
				'tab':9,
				'space':32,
				'return':13,
				'enter':13,
				'backspace':8,
	
				'scrolllock':145,
				'scroll_lock':145,
				'scroll':145,
				'capslock':20,
				'caps_lock':20,
				'caps':20,
				'numlock':144,
				'num_lock':144,
				'num':144,
				
				'pause':19,
				'break':19,
				
				'insert':45,
				'home':36,
				'delete':46,
				'end':35,
				
				'pageup':33,
				'page_up':33,
				'pu':33,
	
				'pagedown':34,
				'page_down':34,
				'pd':34,
	
				'left':37,
				'up':38,
				'right':39,
				'down':40,
	
				'f1':112,
				'f2':113,
				'f3':114,
				'f4':115,
				'f5':116,
				'f6':117,
				'f7':118,
				'f8':119,
				'f9':120,
				'f10':121,
				'f11':122,
				'f12':123
			}
	
			var modifiers = { 
				shift: { wanted:false, pressed:false},
				ctrl : { wanted:false, pressed:false},
				alt  : { wanted:false, pressed:false},
				meta : { wanted:false, pressed:false}	//Meta is Mac specific
			};
                        
			if(e.ctrlKey)	modifiers.ctrl.pressed = true;
			if(e.shiftKey)	modifiers.shift.pressed = true;
			if(e.altKey)	modifiers.alt.pressed = true;
			if(e.metaKey)   modifiers.meta.pressed = true;
                        
			for(var i=0; k=keys[i],i<keys.length; i++) {
				//Modifiers
				if(k == 'ctrl' || k == 'control') {
					kp++;
					modifiers.ctrl.wanted = true;

				} else if(k == 'shift') {
					kp++;
					modifiers.shift.wanted = true;

				} else if(k == 'alt') {
					kp++;
					modifiers.alt.wanted = true;
				} else if(k == 'meta') {
					kp++;
					modifiers.meta.wanted = true;
				} else if(k.length > 1) { //If it is a special key
					if(special_keys[k] == code) kp++;
					
				} else if(opt['keycode']) {
					if(opt['keycode'] == code) kp++;

				} else { //The special keys did not match
					if(character == k) kp++;
					else {
						if(shift_nums[character] && e.shiftKey) { //Stupid Shift key bug created by using lowercase
							character = shift_nums[character]; 
							if(character == k) kp++;
						}
					}
				}
			}

			if(kp == keys.length && 
						modifiers.ctrl.pressed == modifiers.ctrl.wanted &&
						modifiers.shift.pressed == modifiers.shift.wanted &&
						modifiers.alt.pressed == modifiers.alt.wanted &&
						modifiers.meta.pressed == modifiers.meta.wanted) {
				callback(e);
	
				if(!opt['propagate']) { //Stop the event
					//e.cancelBubble is supported by IE - this will kill the bubbling process.
					e.cancelBubble = true;
					e.returnValue = false;
	
					//e.stopPropagation works in Firefox.
					if (e.stopPropagation) {
						e.stopPropagation();
						e.preventDefault();
					}
					return false;
				}
			}
		}
		this.all_shortcuts[shortcut_combination] = {
			'callback':func, 
			'target':ele, 
			'event': opt['type']
		};
		//Attach the function with the event
		if(ele.addEventListener) ele.addEventListener(opt['type'], func, false);
		else if(ele.attachEvent) ele.attachEvent('on'+opt['type'], func);
		else ele['on'+opt['type']] = func;
	},

	//Remove the shortcut - just specify the shortcut and I will remove the binding
	'remove':function(shortcut_combination) {
		shortcut_combination = shortcut_combination.toLowerCase();
		var binding = this.all_shortcuts[shortcut_combination];
		delete(this.all_shortcuts[shortcut_combination])
		if(!binding) return;
		var type = binding['event'];
		var ele = binding['target'];
		var callback = binding['callback'];

		if(ele.detachEvent) ele.detachEvent('on'+type, callback);
		else if(ele.removeEventListener) ele.removeEventListener(type, callback, false);
		else ele['on'+type] = false;
	}
}

function nf_apply_font(obj){	
	  var font = JSON.parse( jQuery('select[name="google_fonts"]').val() )
	  obj.css('font-family', font.family);
	  
	  if ( 'undefined' !== font.name ) {
			if(!jQuery('link[id="'+ format_illegal_chars(font.name) +'"]').length>0)
				jQuery( '<link id="'+format_illegal_chars(font.name)+'" type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family='+ font.name +'">').appendTo( '.nex-forms-container' );
		}
	  
}
