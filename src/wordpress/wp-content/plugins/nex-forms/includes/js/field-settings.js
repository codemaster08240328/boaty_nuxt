var current_style_tool = '';
var current_field = '';
var current_id = '';
var label_container = '';
var input_container = '';
jQuery(document).ready(
function()
	{
		
	jQuery('.outer_container').prepend('<div class="current-style-tool"><span class=""></span></div>');
	
	
	
		
	jQuery( "body" ).mousemove(function( event ) {
		 
		 jQuery('.current-style-tool').css('top',event.pageY);
		 jQuery('.current-style-tool').css('left',event.pageX);
		});
	
	
	jQuery('.styling-bar .styling-tool-item').click(
		function()
			{
			jQuery('#close-settings').trigger('click');
			jQuery('.nex-forms-container').removeClass('styling_field_layout').removeClass('styling_font_style').removeClass('styling_text_alignment').removeClass('styling_colors').removeClass('styling_size')
			if(jQuery(this).attr('data-style-tool-group')=='layout')
				jQuery('.nex-forms-container').addClass('styling_field_layout')
			if(jQuery(this).attr('data-style-tool-group')=='font-style')
				jQuery('.nex-forms-container').addClass('styling_font_style')
			if(jQuery(this).attr('data-style-tool-group')=='text-align')
				jQuery('.nex-forms-container').addClass('styling_text_alignment')
			if(jQuery(this).attr('data-style-tool-group')=='color')
				jQuery('.nex-forms-container').addClass('styling_colors')
			if(jQuery(this).attr('data-style-tool-group')=='size')
				jQuery('.nex-forms-container').addClass('styling_size')
			
			if(jQuery(this).hasClass('active') && jQuery(this).attr('data-style-tool')!='default-tool')
				{
				jQuery('[data-style-tool="default-tool"]').trigger('click')
				return;
				}
			jQuery('.styling-bar .styling-tool-item').removeClass('active');
			jQuery('.current-style-tool span').css('color','#444');
			if(jQuery(this).attr('data-style-tool')=='default-tool')
				{
				jQuery(this).addClass('active');
				jQuery('.nex-forms-container').removeClass('enable-form-styling')
				current_style_tool = '';
				jQuery('.current-style-tool span').attr('class','')
				}
			else
				{
				
				
				
				
				jQuery('.nex-forms-container').addClass('enable-form-styling')
				current_style_tool = jQuery(this).attr('data-style-tool');
				jQuery(this).addClass('active');	
				jQuery('.current-style-tool span').attr('class',jQuery(this).find('i').attr('class'))
				
				if(current_style_tool == 'set-font-color')
					{
					jQuery('.current-style-tool span').css('color',jQuery('input.font-color-tool').val());	
					}
				if(current_style_tool == 'set-background-color')
					{
					jQuery('.current-style-tool span').css('color',jQuery('input.background-color-tool').val());	
					}
				if(current_style_tool == 'set-border-color')
					{
					jQuery('.current-style-tool span').css('color',jQuery('input.border-color-tool').val());	
					}
				
				if(current_style_tool=='layout-top')
					{
					jQuery('.current-style-tool span').attr('class','fa fa-arrow-up')
					}
				if(current_style_tool=='layout-hide')
					{
					jQuery('.current-style-tool span').attr('class','fa fa-eye-slash')
					}
				if(current_style_tool=='layout-left')
					{
					jQuery('.current-style-tool span').attr('class','fa fa-arrow-left')
					}
				if(current_style_tool=='layout-right')
					{
					jQuery('.current-style-tool span').attr('class','fa fa-arrow-right')
					}
				
				
				}
			}
		);
		
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .label_container, .nex-forms-container.enable-form-styling .input_container',
		function()
			{
			if(current_style_tool=='align-left')
				jQuery(this).addClass('align_left').removeClass('align_center').removeClass('align_right');
			if(current_style_tool=='align-center')
				jQuery(this).addClass('align_center').removeClass('align_left').removeClass('align_right');
			if(current_style_tool=='align-right')
				jQuery(this).addClass('align_right').removeClass('align_center').removeClass('align_left');
				
			
			}
		);
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .input_container .the_input_element',
		function()
			{
			if(current_style_tool=='align-left')
				jQuery(this).addClass('align_left').removeClass('align_center').removeClass('align_right');
			if(current_style_tool=='align-center')
				jQuery(this).addClass('align_center').removeClass('align_left').removeClass('align_right');
			if(current_style_tool=='align-right')
				jQuery(this).addClass('align_right').removeClass('align_center').removeClass('align_left');
				
			if(current_style_tool=='size-sm')
				jQuery(this).addClass('input-sm').removeClass('input-lg');
			if(current_style_tool=='size-normal')
				jQuery(this).removeClass('input-sm').removeClass('input-lg');
			if(current_style_tool=='size-lg')
				jQuery(this).addClass('input-lg').removeClass('input-sm');
				
			if(current_style_tool=='text-bold')
				{
				if(	jQuery(this).hasClass('style_bold'))
					jQuery(this).removeClass('style_bold')
				else
					jQuery(this).addClass('style_bold')
				}
			if(current_style_tool=='text-italic')
				{
				if(	jQuery(this).hasClass('style_italic'))
					jQuery(this).removeClass('style_italic')
				else
					jQuery(this).addClass('style_italic')
				}
			if(current_style_tool=='text-underline')
				{
				if(	jQuery(this).hasClass('style_underline'))
					jQuery(this).removeClass('style_underline')
				else
					jQuery(this).addClass('style_underline')
				}
			
			}
		);
	
	jQuery(document).on('click','.nex-forms-container.enable-form-styling #field_container',
		function()
			{
				
				var set_label_container = jQuery(this).find('.label_container');
				var set_input_container = jQuery(this).find('.input_container');
				
				if(current_style_tool=='layout-top')
					{
					set_label_container.show();
					set_label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
					set_label_container.addClass('col-sm-12');
					set_input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
					set_input_container.addClass('col-sm-12');
					
					var copy_label = set_label_container.clone();
					set_label_container.remove();
					set_input_container.before(copy_label);
					}
				if(current_style_tool=='layout-left')
					{
					set_label_container.show();
					set_label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
					set_label_container.addClass('col-sm-3');
					
					set_input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
					set_input_container.addClass('col-sm-9');
					
					var copy_label = set_label_container.clone();
					set_label_container.remove();
					set_input_container.before(copy_label);
					
					}
				
				if(current_style_tool=='layout-right')
					{
					set_label_container.show();
					set_label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').addClass('pos_right')
					set_label_container.addClass('col-sm-3');
					
					set_input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
					set_input_container.addClass('col-sm-9');
					
					var copy_label = set_label_container.clone();
					set_label_container.remove();
					set_input_container.after(copy_label);
					
					}
				if(current_style_tool=='layout-hide')
					{
					set_input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
					set_input_container.addClass('col-sm-12');
					set_label_container.hide();
					set_input_container.find('input').attr('placeholder',set_label_container.find('.the_label').text());
					}
			}
			);
	
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .label_container .the_label,.nex-forms-container.enable-form-styling .label_container .sub-text',
		function()
			{
				
			if(current_style_tool=='font-family')
				nf_apply_font(jQuery(this));	
				//jQuery('select[name="fonts"]').stylesFontDropdown().data('stylesFontMenu').preview_font_change( jQuery(this) );	
			
			//$('select[name="fonts"]').stylesFontDropdown();
			
			if(current_style_tool=='size-sm')
				jQuery(this).parent().addClass('text-sm').removeClass('text-lg');
			if(current_style_tool=='size-normal')
				jQuery(this).parent().removeClass('text-sm').removeClass('text-lg');
			if(current_style_tool=='size-lg')
				jQuery(this).parent().addClass('text-lg').removeClass('text-sm');	
				
			if(current_style_tool=='set-font-color')
				jQuery(this).css('color',jQuery('input.font-color-tool').val());
			
			if(current_style_tool=='text-bold')
				{
				if(	jQuery(this).hasClass('style_bold'))
					jQuery(this).removeClass('style_bold')
				else
					jQuery(this).addClass('style_bold')
				}
			if(current_style_tool=='text-italic')
				{
				if(	jQuery(this).hasClass('style_italic'))
					jQuery(this).removeClass('style_italic')
				else
					jQuery(this).addClass('style_italic')
				}
			if(current_style_tool=='text-underline')
				{
				if(	jQuery(this).hasClass('style_underline'))
					jQuery(this).removeClass('style_underline')
				else
					jQuery(this).addClass('style_underline')
				}
			
			}
		);
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .input_container',
		function()
			{
			if(current_style_tool=='font-family')
				nf_apply_font(jQuery(this).find('.the_input_element'));
			
			if(current_style_tool=='set-font-color')
				{
				jQuery(this).find('.the_input_element').css('color',jQuery('input.font-color-tool').val());
				jQuery(this).find('label a').css('color',jQuery('input.font-color-tool').val());
				}
			if(current_style_tool=='set-background-color')
				{
				jQuery(this).find('.the_input_element').css('background',jQuery('input.background-color-tool').val());
				jQuery(this).find('label a').css('background',jQuery('input.background-color-tool').val());
				}
			if(current_style_tool=='set-border-color')
				{
				jQuery(this).find('.the_input_element').css('border-color',jQuery('input.border-color-tool').val());
				jQuery(this).find('label a').css('border-color',jQuery('input.border-color-tool').val());
				}
			}
		);
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .input-label',
		function()
			{
			if(current_style_tool=='set-font-color')
				jQuery(this).parent().parent().find('.input-label').css('color',jQuery('input.font-color-tool').val());
			
			if(current_style_tool=='font-family')
				nf_apply_font(jQuery(this).parent().parent().find('.input-label'));	
				
			}
		);
	
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .form_field .panel-heading ',
		function()
			{
			if(current_style_tool=='set-font-color')
				jQuery(this).css('color',jQuery('input.font-color-tool').val());
			if(current_style_tool=='set-background-color')
				jQuery(this).css('background',jQuery('input.background-color-tool').val());
			if(current_style_tool=='set-border-color')
				jQuery(this).css('border-bottom-color',jQuery('input.border-color-tool').val());
			
			if(current_style_tool=='size-sm')
				jQuery(this).addClass('btn-sm').removeClass('btn-lg');
			if(current_style_tool=='size-normal')
				jQuery(this).removeClass('btn-sm').removeClass('btn-lg');
			if(current_style_tool=='size-lg')
				jQuery(this).addClass('btn-lg').removeClass('btn-sm');	
			
			if(current_style_tool=='font-family')
				nf_apply_font(jQuery(this));	
			
			if(current_style_tool=='text-bold')
				{
				if(	jQuery(this).hasClass('style_bold'))
					jQuery(this).removeClass('style_bold')
				else
					jQuery(this).addClass('style_bold')
				}
			if(current_style_tool=='text-italic')
				{
				if(	jQuery(this).hasClass('style_italic'))
					jQuery(this).removeClass('style_italic')
				else
					jQuery(this).addClass('style_italic')
				}
			if(current_style_tool=='text-underline')
				{
				if(	jQuery(this).hasClass('style_underline'))
					jQuery(this).removeClass('style_underline')
				else
					jQuery(this).addClass('style_underline')
				}
			
			if(current_style_tool=='align-left')
				jQuery(this).addClass('align_left').removeClass('align_center').removeClass('align_right');
			if(current_style_tool=='align-center')
				jQuery(this).addClass('align_center').removeClass('align_left').removeClass('align_right');
			if(current_style_tool=='align-right')
				jQuery(this).addClass('align_right').removeClass('align_center').removeClass('align_left');
				
			}
		);
		
		jQuery(document).on('click','.nex-forms-container.enable-form-styling .form_field .panel-body ',
			function()
				{
				
				if(current_style_tool=='set-background-color')
					jQuery(this).css('background',jQuery('input.background-color-tool').val());
				if(current_style_tool=='set-border-color')
					jQuery(this).parent().css('border-color',jQuery('input.border-color-tool').val());
				
				
					
				}
			);
	
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .img-thumbnail',
		function()
			{
			if(current_style_tool=='set-border-color')
				jQuery(this).css('border-color',jQuery('input.border-color-tool').val());
			if(current_style_tool=='set-border-color')
				jQuery(this).css('background',jQuery('input.background-color-tool').val());
			}
		);
	
	jQuery(document).on('click','.nex-forms-container.enable-form-styling .input-group-addon',
		function()
			{
			if(current_style_tool=='set-font-color')
				jQuery(this).css('color',jQuery('input.font-color-tool').val());
			if(current_style_tool=='set-background-color')
				jQuery(this).css('background',jQuery('input.background-color-tool').val());
			if(current_style_tool=='set-border-color')
				jQuery(this).css('border-color',jQuery('input.border-color-tool').val());
			}
		);
		
		
	
	
	jQuery('.font-color-tool').ColorPickerSliders(
		{
		 placement: 'right',
		 hsvpanel: true,
		 previewformat: 'hex',
		 color: '#444444',
		 onchange: function(container, color)
			{
			if(current_style_tool=='set-font-color')
				jQuery('.current-style-tool span').css('color',jQuery('input.font-color-tool').val());	
			}
		}
	);
	
	jQuery('.background-color-tool').ColorPickerSliders(
		{
		 placement: 'right',
		 hsvpanel: true,
		 previewformat: 'hex',
		 color: '#FFFFFF',
		 onchange: function(container, color)
			{
			if(current_style_tool=='set-background-color')
				jQuery('.current-style-tool span').css('color',jQuery('input.background-color-tool').val());	
			}
		}
	);
	
	jQuery('.border-color-tool').ColorPickerSliders(
		{
		 placement: 'right',
		 hsvpanel: true,
		 previewformat: 'hex',
		 color: '#bbbbbb',
		 onchange: function(container, color)
			{
			if(current_style_tool=='set-border-color')
				jQuery('.current-style-tool span').css('color',jQuery('input.border-color-tool').val());	
			}
		}
	);
	
	
	
	
	
	
	
		
	jQuery('.field-setting-categories .tab').click(
		function()
			{
			jQuery('.settings-input-styling').hide();
	jQuery('#set_default_select_value').hide();
	jQuery('#set_input_val').hide();
	jQuery('#max_tags').hide();
	jQuery('#set_button_val').hide();
	jQuery('#set_heading_text').hide();
	jQuery('#spin_start_value').hide();
	jQuery('.settings-select-options').hide();
	jQuery('.settings-radio-options').hide();
	jQuery('#set_default_select_value').hide();
	jQuery('.settings-radio-styling').hide();
	jQuery('.settings-slider-options').hide();
	jQuery('.settings-slider-styling').hide();
	jQuery('.settings-date-options').hide();
	jQuery('.settings-spinner-options').hide();
	jQuery('.settings-autocomplete-options').hide();
	jQuery('.button-settings').hide();
	jQuery('.heading-settings').hide();
	jQuery('.panel-settings').hide();
	jQuery('.settings-html').hide();	
	jQuery('.settings-star-rating').hide();
	jQuery('.settings-thumb-rating').hide();
	jQuery('.settings-smily-rating').hide();
	jQuery('.survey-field-settings').hide();
	jQuery('.setting-recreate-field').hide();
	jQuery('.settings-grid-system').hide();	
	jQuery('.img-upload-input-settings').hide();
	jQuery('.multi-upload-validation-settings').hide();
	
			}
		);
	jQuery(document).on('click','div.nex-forms-container div.form_field div.form_object div.edit', //,  div.nex-forms-container div.form_field.submit-button, div.nex-forms-container input, div.nex-forms-container .label_container, div.nex-forms-container label#title,div.nex-forms-container .ui-slider-handle,div.nex-forms-container .bootstrap-tagsinput, div.nex-forms-container #the-radios a, div.nex-forms-container .grid .panel-heading, div.nex-forms-container div.input-inner .the_input_element, div.nex-forms-container div.input-inner .help-block
		function()
			{
			
			jQuery('.field-settings-sidebar').addClass('open_sidenav');
			
			setTimeout(function(){jQuery('.field-setting-categories li.tab a.active').removeClass('active').trigger('click')},100);
			
			
			current_field = '';
			current_id = '';
			
			jQuery('div.field-settings-column .current_id').text(jQuery(this).closest('.form_field').attr('id'));
			current_id = jQuery('div.field-settings-column .current_id').text();
			
			current_field = jQuery(this).closest('.form_field');
			
			label_container = current_field.find('.label_container');
			input_container = current_field.find('.input_container');
			input_element 	= current_field.find('.the_input_element');
			
			/*if(current_field.hasClass('grid-system') || current_field.hasClass('divider'))
				return;*/
			
			jQuery('.con-logic-column').hide();
			jQuery('.extra-styling-column').hide();
			jQuery('.paypal-column').hide();
			
			jQuery('.settings-input-styling').hide();
	jQuery('#set_default_select_value').hide();
	jQuery('#set_input_val').hide();
	jQuery('#max_tags').hide();
	jQuery('#set_button_val').hide();
	jQuery('#set_heading_text').hide();
	jQuery('#spin_start_value').hide();
	jQuery('.settings-select-options').hide();
	jQuery('.settings-radio-options').hide();
	jQuery('#set_default_select_value').hide();
	jQuery('.settings-radio-styling').hide();
	jQuery('.settings-slider-options').hide();
	jQuery('.settings-slider-styling').hide();
	jQuery('.settings-date-options').hide();
	jQuery('.settings-spinner-options').hide();
	jQuery('.settings-autocomplete-options').hide();
	jQuery('.button-settings').hide();
	jQuery('.heading-settings').hide();
	jQuery('.panel-settings').hide();
	jQuery('.settings-html').hide();	
	jQuery('.settings-star-rating').hide();
	jQuery('.settings-thumb-rating').hide();
	jQuery('.settings-smily-rating').hide();
	jQuery('.survey-field-settings').hide();
	jQuery('.setting-recreate-field').hide();
	jQuery('.settings-grid-system').hide();	
	jQuery('.img-upload-input-settings').hide();	
	jQuery('.multi-upload-validation-settings').hide();
			jQuery('.conditional-logic').removeClass('active');
			
			
			
			jQuery('.form_field').removeClass('currently_editing');
			current_field.addClass('currently_editing');
			
			if(jQuery('.field-setting-categories #label-settings').hasClass('active'))
				get_label_settings();
			if(jQuery('.field-setting-categories #input-settings').hasClass('active'))
				get_input_settings();
			if(jQuery('.field-setting-categories #validation-settings').hasClass('active'))
				get_validation_settings();
			if(jQuery('.field-setting-categories #animation-settings').hasClass('active'))
				get_animation_settings();
			if(jQuery('.field-setting-categories #math-settings').hasClass('active'))
				get_math_settings();
			
			show_current_field_type(current_field);
			
			if(jQuery('.field-settings-column').hasClass('flipIn'))
				{
				jQuery('.field-settings-column').removeClass('admin_animated').removeClass('flipOut').removeClass('flipIn').removeClass('pulse');
				jQuery('.field-settings-column').addClass('admin_animated').addClass('pulse');
				setTimeout(function(){ jQuery('.field-settings-column').removeClass('admin_animated').removeClass('pulse').addClass('flipIn'); },500);
				}
			else
				{
				jQuery('.field-settings-column').removeClass('admin_animated').removeClass('flipOut').removeClass('flipIn').removeClass('pulse');
				jQuery('.field-settings-column').addClass('admin_animated').addClass('flipIn');
				}
			
			jQuery('.field-settings-column').show();
			
			jQuery('.field-setting-categories .tab').hide();
			jQuery('.field-setting-categories #close-settings').show();
			jQuery('.field-setting-categories #animation-settings').show();
			
			if(current_field.hasClass('heading') || current_field.hasClass('html') || current_field.hasClass('math_logic') || current_field.hasClass('paragraph'))
				{
				jQuery('.field-setting-categories #math-settings').show();
				jQuery('.field-setting-categories #input-settings').show();
				if(jQuery('.field-setting-categories #math-settings').hasClass('active'))
					jQuery('.field-setting-categories #math-settings').trigger('click');
				else if(jQuery('.field-setting-categories #animation-settings').hasClass('active'))
					jQuery('.field-setting-categories #animation-settings').trigger('click');
				else
					jQuery('.field-setting-categories #input-settings').trigger('click');
				}
			else if(current_field.hasClass('submit-button2') || current_field.hasClass('submit-button') || current_field.hasClass('nex-step') || current_field.hasClass('prev-step'))
				{
				jQuery('.field-setting-categories #input-settings').show();
				if(!jQuery('.field-setting-categories #animation-settings').hasClass('active'))
					jQuery('.field-setting-categories #input-settings').trigger('click');
				}
			else if(current_field.hasClass('is_panel') || current_field.hasClass('grid-system'))
				{
				jQuery('.ungeneric-input-settings').hide();
				jQuery('.field-setting-categories .panel-settings').show();
				jQuery('.field-setting-categories #input-settings').show();
				if(!jQuery('.field-setting-categories #animation-settings').hasClass('active'))
					jQuery('.field-setting-categories #input-settings').trigger('click');
				}
			else if(current_field.hasClass('divider'))
				{
				jQuery('.ungeneric-input-settings').hide();
				jQuery('.field-setting-categories #animation-settings').trigger('click');
				}
			
			else
				{
				jQuery('.field-setting-categories #label-settings').show();	
				jQuery('.field-setting-categories #input-settings').show();	
				jQuery('.field-setting-categories #validation-settings').show();
				
				if(jQuery('.field-setting-categories #input-settings').hasClass('active'))
					jQuery('.field-setting-categories #input-settings').trigger('click');
				else if(jQuery('.field-setting-categories #animation-settings').hasClass('active'))
					jQuery('.field-setting-categories #animation-settings').trigger('click');
				else
					jQuery('.field-setting-categories #label-settings').trigger('click');
				}
			
			}
		);
	jQuery(document).on('click', '.field-setting-categories #close-settings', 
		function()
			{
			jQuery('.show_field_type').removeClass('show_field_type');
			jQuery('.form_field').removeClass('currently_editing');
			jQuery('.field-settings-sidebar').removeClass('open_sidenav');
			}
		);
		
	jQuery(document).on('click', '#close-logic', 
		function()
			{
			//jQuery('.con-logic-column').removeClass('admin_animated').removeClass('flipOutY').removeClass('flipInY');
			//jQuery('.con-logic-column').addClass('admin_animated').addClass('flipOutY');
			jQuery('.conditional-logic').removeClass('active');
			jQuery('.con-logic-column').hide();
			if(jQuery('.currently_editing').attr('id'))
				jQuery('.field-settings-column').show()
			
			}
		);
	jQuery(document).on('click', '#close-extra-styling', 
		function()
			{
			jQuery('.form-styling').removeClass('active');
			jQuery('.form-styling').hide()
			if(jQuery('.currently_editing').attr('id'))
				jQuery('.field-settings-column').show()
			
			}
		);
	jQuery(document).on('click', '#close-paypal', 
		function()
			{
			jQuery('.paypal-options').removeClass('active');
			jQuery('.paypal-column').hide()
			if(jQuery('.currently_editing').attr('id'))
				jQuery('.field-settings-column').show()
			
			}
		);	

	
	jQuery(document).on('click', '.field-setting-categories .tab', 
		function()
			{
			if(jQuery(this).attr('id')!='close-settings')
				{
				jQuery('.field-settings-column .field-setting-categories .tab').removeClass('active');
				jQuery(this).addClass('active');
				jQuery('.field-settings-column .inner .settings-section').hide();
				jQuery('.field-settings-column .inner .settings-section.'+jQuery(this).attr('id')).show();
				
				if(jQuery(this).attr('id')=='label-settings')
					get_label_settings();
				if(jQuery(this).attr('id')=='input-settings')
					get_input_settings();
				if(jQuery(this).attr('id')=='validation-settings')
					get_validation_settings();
				if(jQuery(this).attr('id')=='animation-settings')
					get_animation_settings();
				if(jQuery(this).attr('id')=='math-settings')
					get_math_settings();
				}
			}
		);
	
	
	


		
//SETUP LABEL SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
	
	//SET LABEL TEXT	
		jQuery('div.field-settings-column #set_label').keyup(
			function()
				{
				current_field.find('label span.the_label').text(jQuery(this).val());
				current_field.find('.draggable_object span.field_title').text(jQuery(this).val())
				
				var formated_value = format_illegal_chars(jQuery(this).val());
				input_element.attr('name',formated_value)
				current_field.find('input[type="file"]').attr('name',formated_value)
				
				jQuery('div.field-settings-column #set_input_name').val(formated_value);
					
				if(current_field.hasClass('check-group') || current_field.hasClass('multi-select') || current_field.hasClass('classic-check-group') || current_field.hasClass('classic-multi-select'))
					input_element.attr('name',formated_value+'[]')
					
				}
			);
	//SET SUB LABEL TEXT
		jQuery('div.field-settings-column #set_subtext').keyup(
			function()
				{
				current_field.find('label small.sub-text').text(jQuery(this).val())
				}
			);
			
	//SET LABEL BIU
		set_biu_style('span.label','span.the_label','bold');
		set_biu_style('span.label','span.the_label','italic');
		set_biu_style('span.label','span.the_label','underline');
	
	//SET SUB LABEL BIU
		set_biu_style('span.sub-label','small.sub-text','bold');
		set_biu_style('span.sub-label','small.sub-text','italic');
		set_biu_style('span.sub-label','small.sub-text','underline');
		
	//SET LABEL COLORS
		change_color('label-color','span.the_label','color');
		change_color('sub-label-color','small.sub-text','color')
		
		
		
	//SET LABEL POSTITION
		jQuery('.label-position button').click(
			function()
				{
				setTimeout(function(){jQuery('.field-settings-column').removeClass('admin_animated').removeClass('pulse')},10);
				jQuery('.label-position button').removeClass('active');
				jQuery(this).addClass('active');
				
				/*var label_container = current_field.find('.label_container');
				var input_container2 = current_field.find('.input_container');
				*/
				if(jQuery(this).hasClass('top'))
					{
					label_container.show();
					label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
					label_container.addClass('col-sm-12');
					input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
					input_container.addClass('col-sm-12');
					
					var copy_label = label_container.clone();
					label_container.remove();
					input_container.before(copy_label);
					current_field.find('div.edit').trigger('click');
					jQuery('div.field-settings-column .width_indicator.left input').val('12');
					jQuery('div.field-settings-column .width_indicator.right input').val('12');
					}
				if(jQuery(this).hasClass('left'))
					{
					label_container.show();
					label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
					label_container.addClass('col-sm-3');
					
					input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
					input_container.addClass('col-sm-9');
					
					var copy_label = label_container.clone();
					label_container.remove();
					input_container.before(copy_label);
					current_field.find('div.edit').trigger('click');
					jQuery('div.field-settings-column .width_indicator.left input').val('3');
					jQuery('div.field-settings-column .width_indicator.right input').val('9');
					
					}
				
				if(jQuery(this).hasClass('right'))
					{
					label_container.show();
					label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').addClass('pos_right')
					label_container.addClass('col-sm-3');
					
					input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
					input_container.addClass('col-sm-9');
					
					var copy_label = label_container.clone();
					label_container.remove();
					input_container.after(copy_label);
					current_field.find('div.edit').trigger('click');
					jQuery('div.field-settings-column .width_indicator.left input').val('3');
					jQuery('div.field-settings-column .width_indicator.right input').val('9');
					
					}
				if(jQuery(this).hasClass('none'))
					{
					input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
					input_container.addClass('col-sm-12');
					label_container.hide();
					}
				}
		);
			
	//SET LABEL ALIGNMENT
		jQuery('.align-label button').click(
			function()
				{
				jQuery('.align-label button').removeClass('active');
				jQuery(this).addClass('active');
				
				label_container.removeClass('align_left').removeClass('align_right').removeClass('align_center');
				
				if(jQuery(this).hasClass('left'))
					label_container.addClass('align_left');
				if(jQuery(this).hasClass('right'))
					label_container.addClass('align_right');
				if(jQuery(this).hasClass('center'))
					label_container.addClass('align_center');
				}
			);
	//SET LABEL SIZE
		jQuery('.label-size button').click(
			function()
				{
				jQuery('.label-size button').removeClass('active');
				jQuery(this).addClass('active');
				
				var get_label = current_field.find('label');
				get_label.removeClass('text-lg').removeClass('text-sm');
				
				if(jQuery(this).hasClass('small'))
					get_label.addClass('text-sm');
				if(jQuery(this).hasClass('large'))
					get_label.addClass('text-lg');
				}
			);
			
	//SET LABEL WIDTH
		var selecter = jQuery( "#label_width" );
		var slider = jQuery( "<div id='slider'></div>" ).insertAfter( selecter ).slider(
			{
			min: 1,
			max: 12,
			range: "min",
			value: selecter[ 0 ].selectedIndex + 1,
			slide: function( event, ui )
				{
				selecter[ 0 ].selectedIndex = ui.value - 1;
				//count_text = '<span class="count-text">' + ui.value + '</span>';	
				jQuery(this).find( '.ui-slider-handle' ).html('&nbsp;');
				jQuery('div.field-settings-column .width_indicator.left input').val(ui.value);
				if(ui.value<12)
					jQuery('div.field-settings-column .width_indicator.right input').val((12-ui.value));
				else
					jQuery('div.field-settings-column .width_indicator.right input').val((ui.value));
				set_label_width(ui.value);
				},
			create: function( event, ui )
				{	
				count_text = '<span class="count-text">1</span>';	
				jQuery(this).find( '.ui-slider-handle' ).html('&nbsp;');				
				}
			}
		);
		jQuery( "#label_width" ).change(function()
			{
			slider.slider( "value", this.selectedIndex + 1 );
			}
		);
		
		jQuery('div.field-settings-column #set_label_width').keyup(
			function()
				{
				label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
				label_container.addClass('col-sm-'+jQuery(this).val());
				}
			);
		jQuery('div.field-settings-column #set_input_width').keyup(
			function()
				{
				input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12').removeClass('pos_right')
				input_container.addClass('col-sm-'+jQuery(this).val());
				}
			);
			
//SETUP INPUT SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
	
	//ENABLE/DISABLE FIELD REPLICATION
	
	
	
	
	jQuery('.recreate-field button').click(
			function()
				{
				jQuery('.recreate-field button').removeClass('active');
				jQuery(this).addClass('active');
				
				
				
				if(jQuery(this).hasClass('enable-recreation'))
					{
					if(!strstr(input_element.attr('name'),'['))
						input_element.attr('name',input_element.attr('name')+'[]');
					
					current_field.addClass('field-replication-enabled');
						
						
					if(!input_element.closest('.input_container').find('.input-group').attr('class'))
						{
						input_element.wrap('<div class="input-group"></div>');
						}
					if(!input_element.closest('.input_container').find('.recreate-this-field').attr('class'))
						input_element.closest('.input-group').append('<span class="input-group-addon recreate-this-field"><i class="fa fa-plus"></i></span>');
						
					}
				else
					{
					
					var set_input_name = input_element.attr('name')
					set_input_name = set_input_name.replace('[]','');
					input_element.attr('name',set_input_name);
					current_field.removeClass('field-replication-enabled');
					
					current_field.find('.recreate-this-field').remove();
					if(!input_element.closest('.input_container').find('.prefix').attr('class') && !input_element.closest('.input_container').find('.postfix').attr('class'))
						{
						input_element.unwrap();
						}
					}
				}
			);
	
	//SET INPUT NAME
		jQuery('div.field-settings-column #set_input_name').keyup(
			function()
				{
				var formated_value = format_illegal_chars(jQuery(this).val());
				input_element.attr('name',formated_value)
				current_field.find('input[type="file"]').attr('name',formated_value)
				
				jQuery('div#nex-forms-field-settings #set_input_name').val(formated_value);
				
				if(current_field.hasClass('check-group') || current_field.hasClass('multi-select') || current_field.hasClass('classic-check-group') || current_field.hasClass('classic-multi-select'))
						input_element.attr('name',format_illegal_chars(jQuery(this).val())+'[]')
				}
			);

	//SET IMG SELECT BUTTON
		jQuery('div.field-settings-column #img-upload-select').keyup(
			function()
				{
				current_field.find('span.fileinput-new').text(jQuery(this).val());
				}
			);
	//SET IMG CHANGE BUTTON
		jQuery('div.field-settings-column #img-upload-change').keyup(
			function()
				{
				current_field.find('span.fileinput-exists').text(jQuery(this).val());
				}
			);
	//SET IMG REMOVE BUTTON
		jQuery('div.field-settings-column #img-upload-remove').keyup(
			function()
				{
				current_field.find('a.fileinput-exists').text(jQuery(this).val());
				}
			);
		
	//SET INPUT PLACEHOLDER
		jQuery('div.field-settings-column #set_input_placeholder').keyup(
			function()
				{
				input_element.attr('placeholder',jQuery(this).val())
				}
			);
	//SET INPUT ID
		jQuery('div.field-settings-column #set_input_id').keyup(
			function()
				{
				input_element.attr('id',jQuery(this).val())
				}
			);
	//SET INPUT ID
		jQuery('div.field-settings-column #set_input_class').keyup(
			function()
				{
				input_element.attr('class',jQuery(this).val())
				}
			);
	
	//SET INPUT VALUE
	jQuery('div.field-settings-column #set_input_val').keyup(
			function()
				{
				input_element.attr('value',jQuery(this).val())
				input_element.attr('data-value',jQuery(this).val())
				}
			);
	//SET INPUT BIU
		set_biu_style('span.input','.the_input_element','bold');
		set_biu_style('span.input','.the_input_element','italic');
		set_biu_style('span.input','.the_input_element','underline');
				
	//SET INPUT COLOR
		change_color('input-color','.the_input_element','color');
	//SET INPUT BG COLOR
		change_color('input-bg-color','.the_input_element','background-color');
	//SET INPUT BORDER COLOR
		change_color('input-border-color','.the_input_element','border-color');
	
	//SET INPUT CONTAINER ALIGNMENT
		jQuery('.align-input-container button').click(
			function()
				{
				jQuery('.align-input-container button').removeClass('active');
				jQuery(this).addClass('active');
				
				current_field.find('.input_container').removeClass('align_left').removeClass('align_right').removeClass('align_center');
				
				if(jQuery(this).hasClass('left'))
					current_field.find('.input_container').addClass('align_left');
				if(jQuery(this).hasClass('right'))
					current_field.find('.input_container').addClass('align_right');
				if(jQuery(this).hasClass('center'))
					current_field.find('.input_container').addClass('align_center');
				}
			);
	
	//SET LABEL ALIGNMENT
		jQuery('.align-input button').click(
			function()
				{
				jQuery('.align-input button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_element.removeClass('align_left').removeClass('align_right').removeClass('align_center');
				
				if(jQuery(this).hasClass('left'))
					input_element.addClass('align_left');
				if(jQuery(this).hasClass('right'))
					input_element.addClass('align_right');
				if(jQuery(this).hasClass('center'))
					input_element.addClass('align_center');
				}
			);
	//SET INPUT SIZE
		jQuery('.input-size button').click(
			function()
				{
				jQuery('.input-size button').removeClass('active');
				jQuery(this).addClass('active');
				
				var get_label = current_field.find('.the_input_element');
				get_label.removeClass('input-lg').removeClass('input-sm');
				
				if(jQuery(this).hasClass('small'))
					get_label.addClass('input-sm');
				if(jQuery(this).hasClass('large'))
					get_label.addClass('input-lg');
				}
			);
	
	//SET CORNERS
		jQuery('.input-corners button').click(
			function()
				{
				jQuery('.input-corners button').removeClass('active');
				jQuery(this).addClass('active');
				
				current_field.removeClass('pill').removeClass('square');
				
				if(jQuery(this).hasClass('square'))
					current_field.addClass('square');
				if(jQuery(this).hasClass('pill'))
					square.addClass('pill');
				}
			);
	
	
	//SET INPUT PRE-ICON CLASS
	jQuery(document).on('keyup','div.field-settings-column #set_icon_before',
			function()
				{
				jQuery(this).parent().find('.current_icon_before i').attr('class',jQuery(this).val())
				input_element.parent().find('.prefix span').attr('class',jQuery(this).val())
				
				if(jQuery(this).val()=='')
					set_icon(jQuery(this).val(),'before', 'icon_before', 'prefix', 'postfix',true)
				else
					set_icon(jQuery(this).val(),'before', 'icon_before', 'prefix', 'postfix','')
				
				}
			);
	//SET INPUT PRE-ICON
	jQuery('.current_icon_before').click(
			function()
				{
				jQuery('.fa-icons-list').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp').removeClass('icon_after').addClass('icon_before')
				jQuery('.fa-icons-list').addClass('admin_animated').addClass('bounceInDown').show();
				jQuery('.fa-icons-list i').removeClass('active');
				var current_icon = jQuery('.current_icon_before i').attr('class');
				set_current_icon_class = current_icon.replace('fa','').replace(' ','');
				if(current_icon)
					jQuery('.fa-icons-list i.' + set_current_icon_class).addClass('active');
				}
			);
	
			
	
	
	//SET PRE-ICON TEXT COLOR
		change_color('pre-icon-text-color','.prefix','color');
	//SET PRE-ICON BG COLOR
		change_color('pre-icon-bg-color','.prefix','background-color');
	//SET PRE-ICON BORDER COLOR
		change_color('pre-icon-border-color','.prefix','border-color');
	
	
	//SET INPUT POST-ICON CLASS
	jQuery(document).on('keyup','div.field-settings-column #set_icon_after',
			function()
				{
				jQuery(this).parent().find('.current_icon_after i').attr('class',jQuery(this).val())
				input_element.parent().find('.postfix span').attr('class',jQuery(this).val())
				
				if(jQuery(this).val()=='')
					set_icon(jQuery(this).val(),'after', 'icon_after', 'postfix', 'prefix', true)
				else
					set_icon(jQuery(this).val(),'after', 'icon_after', 'postfix', 'prefix', '')
				}
			);
	
	//SET INPUT POST-ICON
	jQuery('.current_icon_after').click(
			function()
				{
				jQuery('.fa-icons-list').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp').removeClass('icon_before').addClass('icon_after')
				jQuery('.fa-icons-list').addClass('admin_animated').addClass('bounceInDown').show();
				jQuery('.fa-icons-list i').removeClass('active');
				var current_icon = jQuery('.current_icon_after i').attr('class');
				set_current_icon_class = current_icon.replace('fa','').replace(' ','');
				if(current_icon)
					jQuery('.fa-icons-list i.' + set_current_icon_class).addClass('active');
				
				}
			);
	
	//SET POST-ICON TEXT COLOR
		change_color('post-icon-text-color','.postfix','color');
	//SET POST-ICON BG COLOR
		change_color('post-icon-bg-color','.postfix','background-color');
	//SET POST-ICON BORDER COLOR
		change_color('post-icon-border-color','.postfix','border-color');
	
	
	//SET ICON	
	jQuery('.fa-icons-list .inner i').click(
		function()
			{
			var remove_icon = false;
			
			if(jQuery(this).hasClass('no-icon'))
				remove_icon = true;
			
			jQuery('.fa-icons-list i').removeClass('active');
			jQuery(this).removeClass('active');
			
		if(current_field.hasClass('radio-group') || current_field.hasClass('check-group') || current_field.hasClass('single-image-select-group') || current_field.hasClass('multi-image-select-group'))
			{
			current_field.find('.the-radios').attr('data-checked-class',jQuery(this).attr('class'));
			current_field.find('a.checked').attr('class','checked ui-state-active '+ jQuery(this).attr('class'));
			if(remove_icon)
				{
				jQuery('.current_radio_icon i').text('Select Icon');
				jQuery('.current_radio_icon i').attr('class','');
				jQuery('#set_radio_icon').val('');
				}
			else
				{
				jQuery('.current_radio_icon i').text('');
				jQuery('.current_radio_icon i').attr('class',jQuery(this).attr('class'));
				jQuery('#set_radio_icon').val(jQuery(this).attr('class'));
				}
			}
		else
			{
			if(jQuery(this).parent().parent().hasClass('icon_before'))
				set_icon(jQuery(this).attr('class'),'before', 'icon_before', 'prefix', 'postfix',remove_icon)
			if(jQuery(this).parent().parent().hasClass('icon_after'))
				set_icon(jQuery(this).attr('class'),'after', 'icon_after', 'postfix', 'prefix',remove_icon)
			}
			
			jQuery(this).addClass('active');
			jQuery('.fa-icons-list').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp')
			jQuery('.fa-icons-list').addClass('admin_animated').addClass('bounceOutUp').hide();
			}
		);
	//CLOSE ICONS
	jQuery('.fa-icons-list .close_icons').click(
		function()
			{
			jQuery('.fa-icons-list').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp')
			jQuery('.fa-icons-list').addClass('admin_animated').addClass('bounceOutUp').hide()
			}
		);
	//ICON SEARCH
	jQuery('.icon_search').change(
		function()
			{
			var search_term = jQuery(this).val();
			jQuery('.fa-icons-list .inner i').each(
				function()
					{
					if(!strstr(jQuery(this).attr('class'),search_term))
						jQuery(this).hide();
					else
						jQuery(this).show();
					}
				);
			}
		)	
	//SET BACKGROUND IMAGE
		jQuery('#do-upload-image input').change(
		function()
			{
			jQuery('#do-upload-image').submit();
			}
		)
		jQuery('#do-upload-image').ajaxForm(
			{
			data:{action: 'do_upload_image'},
			beforeSubmit: function(formData, jqForm, options) {},
			success : function(responseText, statusText, xhr, $form)
				{
				if(current_field.hasClass('other-elements') && current_field.hasClass('grid'))
					current_field.find('.panel-heading').next('.panel-body').css('background','url("'+ responseText +'")');
				else
					input_element.css('background','url("'+ responseText +'")');
				},
			error: function(jqXHR, textStatus, errorThrown){/*console.log(errorThrown)*/}
			}
		);	
	
	
	
	//SET BACKGROUND SIZE
	jQuery('.bg-size button').click(
		function()
			{
			jQuery('.bg-size button').removeClass('active');
			jQuery(this).addClass('active');
			if(current_field.hasClass('other-elements') && current_field.hasClass('grid'))
				var get_obj = current_field.find('.panel-body');
			else
				var get_obj = input_element;
				
			if(jQuery(this).hasClass('auto'))
				get_obj.css('background-size','auto');
			if(jQuery(this).hasClass('cover'))
				get_obj.css('background-size','cover');
			if(jQuery(this).hasClass('contain'))
				get_obj.css('background-size','contain');
			}
		);
	
	//SET BACKGROUND SIZE
	jQuery('.bg-repeat button').click(
		function()
			{
			jQuery('.bg-repeat button').removeClass('active');
			jQuery(this).addClass('active');
			if(current_field.hasClass('other-elements') && current_field.hasClass('grid'))
				var get_obj = current_field.find('.panel-body');
			else
				var get_obj = input_element;
				
			if(jQuery(this).hasClass('no-repeat'))
				get_obj.css('background-repeat','no-repeat');
			if(jQuery(this).hasClass('repeat'))
				get_obj.css('background-repeat','repeat');
			if(jQuery(this).hasClass('repeat-x'))
				get_obj.css('background-repeat','repeat-x');
			if(jQuery(this).hasClass('repeat-y'))
				get_obj.css('background-repeat','repeat-y');
			}
		);
	
	//SET BACKGROUND POSITION
	jQuery('.bg-position button').click(
		function()
			{
			jQuery('.bg-position button').removeClass('active');
			jQuery(this).addClass('active');
			if(current_field.hasClass('other-elements') && current_field.hasClass('grid'))
				var get_obj = current_field.find('.panel-body');
			else
				var get_obj = input_element;
				
			if(jQuery(this).hasClass('left'))
				get_obj.css('background-position','left');
			if(jQuery(this).hasClass('right'))
				get_obj.css('background-position','right');
			if(jQuery(this).hasClass('center'))
				get_obj.css('background-position','center');
			}
		);
/********************************************************************************************/
//TAGS
		jQuery('#max_tags').keyup(
			function()
				{
				current_field.find( "#tags" ).attr('data-max-tags',jQuery(this).val());				
				}
			);
/********************************************************************************************/
//THUMB RATING
		jQuery('#set_thumbs_up_val').keyup(
				function()
					{
					current_field.find('input.nf-thumbs-o-up').attr('value',jQuery(this).val())
					current_field.find('span.the-thumb.fa-thumbs-o-up').attr('data-original-title',jQuery(this).val())
					current_field.find('span.the-thumb.fa-thumbs-o-up').attr('title',jQuery(this).val())
					}
				)
		jQuery('#set_thumbs_down_val').keyup(
				function()
					{
					current_field.find('input.nf-thumbs-o-down').attr('value',jQuery(this).val())
					current_field.find('span.the-thumb.fa-thumbs-o-down').attr('data-original-title',jQuery(this).val())
					current_field.find('span.the-thumb.fa-thumbs-o-down').attr('title',jQuery(this).val())
					}
				)

/********************************************************************************************/
//SMILY RATING
		jQuery('#set_smily_frown_val').keyup(
				function()
					{
					current_field.find('input.nf-smile-bad').attr('value',jQuery(this).val())
					current_field.find('span.the-smile.nf-smile-bad').attr('data-original-title',jQuery(this).val())
					current_field.find('span.the-smile.nf-smile-bad').attr('title',jQuery(this).val())
					}
				)
		jQuery('#set_smily_average_val').keyup(
				function()
					{
					current_field.find('input.nf-smile-average').attr('value',jQuery(this).val())
					current_field.find('span.the-smile.nf-smile-average').attr('data-original-title',jQuery(this).val())
					current_field.find('span.the-smile.nf-smile-average').attr('title',jQuery(this).val())
					}
				)
		jQuery('#set_smily_good_val').keyup(
				function()
					{
					current_field.find('input.nf-smile-good').attr('value',jQuery(this).val())
					current_field.find('span.the-smile.nf-smile-good').attr('data-original-title',jQuery(this).val())
					current_field.find('span.the-smile.nf-smile-good').attr('title',jQuery(this).val())
					}
				)
		
/********************************************************************************************/
//STAR RATING
		
			jQuery('#total_stars').keyup(
				function()
					{
					current_field.find( "#star" ).attr('data-total-stars',jQuery(this).val());
					current_field.find( "#star" ).raty('set',{ number: jQuery(this).val() })					
					}
				);
		jQuery('select[name="set_half_stars"]').change(
			function()
				{				
				if(jQuery(this).val()=='yes')
					{
					current_field.find( "#star" ).attr('data-enable-half','true');
					current_field.find( "#star" ).raty('set',{ half: true });
					}
				else
					{
					current_field.find( "#star" ).raty('set',{ half: false });
					current_field.find( "#star" ).attr('data-enable-half','false');
					}
				}
			);

//SET SELECT OPTIONS
		jQuery('#set_options').live('change',
				function()
					{
					var items = jQuery(this).val();
					if(strstr(jQuery(' #set_default_select_value').val(),'=='))
						{
						var split_default_option = jQuery(' #set_default_select_value').val().split('==')
						var set_options = '<option value="'+ split_default_option[0] +'" selected="selected">'+ split_default_option[1] +'</option>';
						current_field.find('select').attr('data-default-selected-value',split_default_option[0])
						}
					else
						{
						var set_options = '<option value="'+ jQuery(' #set_default_select_value').val() +'" selected="selected">'+ jQuery(' #set_default_select_value').val() +'</option>';
						current_field.find('select').attr('data-default-selected-value',jQuery(' #set_default_select_value').val())
						}
					var set_selections = '';
					items = items.split('\n');
					for (var i = 0; i < items.length; i++)
						{
						if(items[i]!='')
							{
							if(strstr(items[i],'=='))
								{
								var split_option = items[i].split('==')
								set_options += '<option value="'+ split_option[0] +'">'+ split_option[1] +'</option>';
								}
							else
								set_options += '<option value="'+ items[i] +'">'+ items[i] +'</option>';
							}
						}	
					current_field.find('select').html(set_options);
					}
				);
		jQuery('#set_default_select_value').keyup(
				function()
					{
					
					if(strstr(jQuery(this).val(),'=='))
						{
						var split_default_option = jQuery(this).val().split('==')
						current_field.find('select option:selected').text(split_default_option[1])
						current_field.find('select option:selected').val(split_default_option[0])
						current_field.find('select').attr('data-default-selected-value',split_default_option[0])
						}
					else
						{
						current_field.find('select option:selected').text(jQuery(this).val())
						current_field.find('select option:selected').val(jQuery(this).val())
						current_field.find('select').attr('data-default-selected-value',jQuery(this).val())
						}
					}
				);

//SET RADIO/CHECK SETTINGS	
	jQuery('#set_radios').live('change',
		function()
			{
			var items = jQuery(this).val();
			var set_inputs = '';
			items = items.split('\n');
			for (var i = 0; i < items.length; i++)
				{
				if(items[i]!='')
					{
					var radio_layout = current_field.find('.the-radios').attr('data-layout');
					var set_layout = '';
					if(radio_layout=='1c')
						set_layout='display-block col-sm-12'; 
					if(radio_layout=='2c')
						set_layout='display-block col-sm-6';
					if(radio_layout=='3c')
						set_layout='display-block col-sm-4';
					if(radio_layout=='4c')
						set_layout='display-block col-sm-3';
					
					
					if(current_field.find('div#the-radios .input-inner label:eq('+ i +') img').attr('src'))
						var the_image = '<img class="radio-image" src="' + current_field.find('div#the-radios .input-inner label:eq('+ i +') img').attr('src') + '">';
					else
						var the_image = '';
					if(current_field.hasClass('multi-image-select-group') || current_field.hasClass('single-image-select-group'))
						{
						if(current_field.hasClass('multi-image-select-group'))
											{											
											if(strstr(items[i],'=='))
												{
												var split_option = items[i].split('==')
												set_inputs += '<label class="radio-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'"  data-svg="demo-input-1"><span class="svg_ready has-pretty-child"><input class="radio the_input_element" type="checkbox" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'[]" id="'+format_illegal_chars(items[i])+'" value="'+split_option[0]+'"><span class="input-label radio-label  img-thumbnail">'+split_option[1]+ the_image +'</span></span></label>';
												}
											else
												{
												set_inputs += '<label class="radio-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'"  data-svg="demo-input-1"><span class="svg_ready has-pretty-child"><input class="radio the_input_element" type="checkbox" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'[]" id="'+format_illegal_chars(items[i])+'" value="'+items[i]+'"><span class="input-label radio-label  img-thumbnail">'+items[i]+ the_image +'</span></span></label>';
												}
											}
										else
											{
											if(strstr(items[i],'=='))
												{
												var split_option = items[i].split('==')
												set_inputs += '<label class="radio-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'"  data-svg="demo-input-1"><span class="svg_ready has-pretty-child"><input class="radio the_input_element" type="radio" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'" id="'+format_illegal_chars(items[i])+'" value="'+split_option[0]+'"><span class="input-label radio-label  img-thumbnail">'+split_option[1]+the_image +'</span></span></label>';
												}
											else
												{
												set_inputs += '<label class="radio-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'"  data-svg="demo-input-1"><span class="svg_ready has-pretty-child"><input class="radio the_input_element" type="radio" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'" id="'+format_illegal_chars(items[i])+'" value="'+items[i]+'"><span class="input-label radio-label  img-thumbnail">'+items[i]+the_image +'</span></span></label>';
												}
											
											}
					}
					else if(current_field.hasClass('check-group') || current_field.hasClass('classic-check-group'))
						{
						if(strstr(items[i],'=='))
							{
							var split_option = items[i].split('==')
							set_inputs += '<label class="checkbox-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'" ><span class="svg_ready"><input class="check the_input_element" type="checkbox" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'[]" id="'+format_illegal_chars(items[i])+'" value="'+split_option[0]+'"><span class="input-label check-label">'+split_option[1]+'</span></span></label>';
							}
						else
							set_inputs += '<label class="checkbox-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'" ><span class="svg_ready"><input class="check the_input_element" type="checkbox" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'[]" id="'+format_illegal_chars(items[i])+'" value="'+items[i]+'"><span class="input-label check-label">'+items[i]+'</span></span></label>';
						}
					else
						{
						if(strstr(items[i],'=='))
							{
							var split_option = items[i].split('==')
							set_inputs += '<label class="radio-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'" ><span class="svg_ready"><input class="radio the_input_element" type="radio" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'" id="'+format_illegal_chars(items[i])+'" value="'+split_option[0]+'"><span class="input-label radio-label">'+split_option[1]+'</span></span></label>';
							}
						else
							set_inputs += '<label class="radio-inline '+ set_layout +'" for="'+ format_illegal_chars(items[i]) +'" ><span class="svg_ready"><input class="radio the_input_element" type="radio" name="'+ format_illegal_chars(current_field.find('.the_label').text()) +'" id="'+format_illegal_chars(items[i])+'" value="'+items[i]+'"><span class="input-label radio-label">'+items[i]+'</span></span></label>';
						}
					}
				}	
			current_field.find('div#the-radios .input-inner').html(set_inputs);
			if(!current_field.hasClass('classic-check-group') && !current_field.hasClass('classic-radio-group'))
				current_field.find('div#the-radios input').nexchecks();
			}

		);	
	//SET RADIO TEXT COLOR
		change_color('set-radio-label-color','span.input-label','color');
	//SET RADIO TEXT COLOR
		change_color('set-radio-text-color','a','color');
	//SET RADIO BG COLOR
		change_color('set-radio-bg-color','a','background-color');
	//SET RADIO BORDER COLOR
		change_color('set-radio-border-color','a','border-color');
	
	//SET INPUT RADIO CLASS
	jQuery(document).on('keyup','#set_radio_icon',
			function()
				{
				jQuery('.current_radio_icon i').attr('class',jQuery(this).val())
				
				
				if(jQuery(this).val()=='')
					{
					jQuery('.current_radio_icon i').text('Select Icon');
					jQuery('.current_radio_icon i').attr('class','');
					input_element.parent().find('a.checked').attr('class','checked ui-state-active fa fa-check');
					current_field.find('.the-radios').attr('data-checked-class','fa-check');
					}
				else
					{
					jQuery('.current_radio_icon i').text('');
					jQuery('.current_radio_icon i').attr('class',jQuery(this).val());
					current_field.find('.the-radios').attr('data-checked-class',jQuery(this).val());
					input_element.parent().find('a.checked').attr('class','checked ui-state-active ' + jQuery(this).val());
					}
				}
			);
	//SET RADIO LAYOUT
	jQuery('.display-radios-checks button').click(
				function()
					{
					jQuery('.display-radios-checks button').removeClass('active');
					jQuery(this).addClass('active');
					current_field.find('.the-radios label').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-6')	.removeClass('col-sm-12');				
					
					if(jQuery(this).hasClass('1c'))
						{
						current_field.find('.the-radios label').addClass('col-sm-12').addClass('display-block');
						current_field.find('.the-radios').attr('data-layout','1c');
						}
					else if(jQuery(this).hasClass('2c'))
						{
						current_field.find('.the-radios label').addClass('col-sm-6').addClass('display-block');
						current_field.find('.the-radios').attr('data-layout','2c');
						}
					else if(jQuery(this).hasClass('3c'))
						{
						current_field.find('.the-radios label').addClass('col-sm-4').addClass('display-block');
						current_field.find('.the-radios').attr('data-layout','3c');
						}
					else if(jQuery(this).hasClass('4c'))
						{
						current_field.find('.the-radios label').addClass('col-sm-3').addClass('display-block');
						current_field.find('.the-radios').attr('data-layout','4c');
						}
					else
						{
						current_field.find('.the-radios label').removeClass('display-block');
						}
					}
				);
	
	//SET INPUT POST-ICON
	jQuery('.current_radio_icon').click(
			function()
				{
				jQuery('.fa-icons-list').removeClass('admin_animated').removeClass('bounceInDown').removeClass('bounceOutUp').removeClass('icon_before').addClass('icon_after')
				jQuery('.fa-icons-list').addClass('admin_animated').addClass('bounceInDown').show();
				jQuery('.fa-icons-list i').removeClass('active');
				var current_icon = jQuery('.current_radio_icon i').attr('class');
				set_current_icon_class = current_icon.replace('fa','').replace(' ','');
				if(current_icon)
					jQuery('.fa-icons-list i.' + set_current_icon_class).addClass('active');
				
				}
			);
			
//SET SLIDER SETTINGS	
	//SET SLIDER HANDEL COLORS
		change_color('set-slider-handel-text-color','.ui-slider-handle','color');
		change_color('set-slider-handel-bg-color','.ui-slider-handle','background-color');
		change_color('set-slider-handel-border-color','.ui-slider-handle','border-color');
	//SET SLIDER COLORS
		change_color('set-slider-bg-color','.ui-slider','background-color');
		change_color('set-slider-fill-color','.ui-slider-range','background-color');
		change_color('set-slider-border-color','.ui-slider','border-color');
		
	jQuery('#count_text').keyup(
		function()
			{
			current_field.find('#slider .ui-slider-handle span.count-text').html(jQuery(this).val());
			current_field.find('#slider').attr('data-count-text',jQuery(this).val());
			}
		);			

	jQuery('#minimum_value').keyup(
		function()
			{
			current_field.find( "#slider" ).attr('data-min-value',jQuery(this).val());
			current_field.find( "#slider" ).slider('option','min',parseInt(jQuery(this).val()))						
			}
		);
	
	jQuery('#step_value').keyup(
		function()
			{
			current_field.find( "#slider" ).attr('data-step-value',jQuery(this).val());
			current_field.find( "#slider" ).slider('option','step',parseInt(jQuery(this).val()))						
			}
		);
	
	jQuery('#maximum_value').keyup(
		function()
			{
			current_field.find( "#slider" ).attr('data-max-value',jQuery(this).val());
			current_field.find( "#slider" ).slider('option','max',parseInt(jQuery(this).val()))					
			}
		);
	
	jQuery('#start_value').keyup(
		function()
			{
			current_field.find( "#slider" ).attr('data-starting-value',jQuery(this).val());	
			current_field.find( "#slider" ).slider('value',parseInt(jQuery(this).val()))				
			}
		);
	
//SET SPINNER SETTINGS
	
	jQuery('#spin_minimum_value').keyup(
		function()
			{
			current_field.find( "#spinner" ).attr('data-minimum',jQuery(this).val());
			current_field.find( "#spinner" ).trigger("touchspin.updatesettings"	, {min:parseInt(jQuery(this).val())});				
			}
		);
	
	jQuery('#spin_maximum_value').keyup(
		function()
			{
			current_field.find( "#spinner" ).attr('data-maximum',jQuery(this).val());
			current_field.find( "#spinner" ).trigger("touchspin.updatesettings"	, {max:parseInt(jQuery(this).val())});				
			}
		);
		
	jQuery('#spin_start_value').keyup(
		function()
			{
			current_field.find( "#spinner" ).attr('data-starting-value',jQuery(this).val());
			current_field.find( "#spinner" ).trigger("touchspin.updatesettings"	, { initval:parseInt(jQuery(this).val()) } );				
			}
		);
		
	jQuery('#spin_step_value').keyup(
		function()
			{
			current_field.find( "#spinner" ).attr('data-step',jQuery(this).val());
			current_field.find( "#spinner" ).trigger("touchspin.updatesettings"	, { step:parseFloat(jQuery(this).val()) } );				
			}
		);
		
	jQuery('#spin_decimal').keyup(
		function()
			{
			current_field.find( "#spinner" ).attr('data-decimals',jQuery(this).val());
			current_field.find( "#spinner" ).trigger("touchspin.updatesettings"	, { decimals:parseInt(jQuery(this).val()) } );				
			}
		);
			

//SET SATE TIME SETTINGS

	jQuery('select#select_date_format').change(
		function()
			{
			if(jQuery(this).val()!='custom')
				{
				current_field.find('#datetimepicker').attr('data-format',jQuery(this).val())
				jQuery('.set-sutom-date-format').addClass('hidden');
				}
			else
				{
				current_field.find('#datetimepicker').attr('data-format',jQuery('#set_date_format').val())
				jQuery('.set-sutom-date-format').removeClass('hidden');
				}
			}
	)

	jQuery('#set_date_format').keyup(
		function()
			{
			current_field.find('#datetimepicker').attr('data-format',jQuery(this).val())
			}
		);
				

	jQuery('select#date-picker-lang-selector').change(
		function()
			{
			current_field.find('#datetimepicker').attr('data-language',jQuery(this).val())
			}
	)
		
//SET AUTOCOMPLETE SETTINGS
	jQuery('#set_selections').live('change',
		function()
			{
				current_field.find('.get_auto_complete_items').text(jQuery(this).val());
				if(current_field.hasClass('md-select'))
				{
				//jQuery('div.cd-dropdown').remove();
				build_md_select(current_field.find('#cd-dropdown'))
				}
			else if(current_field.hasClass('classic-select') || current_field.hasClass('classic-multi-select') || current_field.hasClass('classic-multi-select'))
				{
				}
			else
				{
				current_field.find('select').selectpicker('refresh');
				}
				
				var items = jQuery(this).val();
					//console.log(items);
					items = items.split('\n');
					current_field.find("#autocomplete").autocomplete({
					source: items
					});	
			}
		);

//SET BUTTON SETTINGS
	//SET BUTTON VALUE
		jQuery('div.field-settings-column #set_button_val').keyup(
			function()
				{
				input_element.html(jQuery(this).val())
				}
			);
	//SET BUTTON POSITION
		jQuery('.button-position button').click(
			function()
				{
				jQuery('.button-position button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_container.removeClass('align_left').removeClass('align_right').removeClass('align_center');
				
				if(jQuery(this).hasClass('left'))
					input_container.addClass('align_left');
				if(jQuery(this).hasClass('right'))
					input_container.addClass('align_right');
				if(jQuery(this).hasClass('center'))
					input_container.addClass('align_center');
				}
			);
	//SET BUTTON TEXT ALIGNMENT
		jQuery('.button-text-align button').click(
			function()
				{
				jQuery('.button-text-align button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_element.removeClass('text-left').removeClass('text-right').removeClass('text-center');
				
				if(jQuery(this).hasClass('left'))
					input_element.addClass('text-left');
				if(jQuery(this).hasClass('right'))
					input_element.addClass('text-right');
				if(jQuery(this).hasClass('center'))
					input_element.addClass('text-center');
				}
			);
		jQuery('.button-size button').click(
			function()
				{
				jQuery('.button-size button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_element.removeClass('btn-lg').removeClass('btn-sm');
				
				if(jQuery(this).hasClass('small'))
					input_element.addClass('btn-sm');
				if(jQuery(this).hasClass('large'))
					input_element.addClass('btn-lg');
				}
			);
		jQuery('.button-width button').click(
			function()
				{
				jQuery('.button-width button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_element.removeClass('full_width').removeClass('col-sm-12');
				
				if(jQuery(this).hasClass('full_button'))
					input_element.addClass('col-sm-12');
				}
			);
		
		
		jQuery('.button-type button').click(
			function()
				{
				jQuery('.button-type button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_element.removeClass('nex-submit').removeClass('nex-step').removeClass('prev-step');
				
				if(jQuery(this).hasClass('next'))
					input_element.addClass('nex-step');
				else if(jQuery(this).hasClass('prev'))
					input_element.addClass('prev-step');
				else
					input_element.addClass('nex-submit');
					
				}
			);



//SET HEADING SETTINGS
	//SET HEADING TEXT
		jQuery('div.field-settings-column #set_heading_text').keyup(
			function()
				{
				input_element.html(jQuery(this).val())
				}
			);
	//SET HEADING SIZE	
		jQuery('.heading-size button').click(
			function()
				{
				jQuery('.heading-size button').removeClass('active');
				jQuery(this).addClass('active');
				
				var get_style = input_element.attr('style');
				var get_class = input_element.attr('class');
				var get_text  = input_element.html();
				
				if(jQuery(this).hasClass('heading_1'))
					input_element.replaceWith('<h1 style="'+ get_style +'" class="'+ get_class +'">'+ get_text +'</h1>');
				if(jQuery(this).hasClass('heading_2'))
					input_element.replaceWith('<h2 style="'+ get_style +'" class="'+ get_class +'">'+ get_text +'</h2>');
				if(jQuery(this).hasClass('heading_3'))
					input_element.replaceWith('<h3 style="'+ get_style +'" class="'+ get_class +'">'+ get_text +'</h3>');
				if(jQuery(this).hasClass('heading_4'))
					input_element.replaceWith('<h4 style="'+ get_style +'" class="'+ get_class +'">'+ get_text +'</h4>');
				if(jQuery(this).hasClass('heading_5'))
					input_element.replaceWith('<h5 style="'+ get_style +'" class="'+ get_class +'">'+ get_text +'</h5>');
				if(jQuery(this).hasClass('heading_6'))
					input_element.replaceWith('<h6 style="'+ get_style +'" class="'+ get_class +'">'+ get_text +'</h6>');
				
				current_field.find('div.edit').trigger('click');
				setTimeout(function(){jQuery('.field-settings-column').removeClass('admin_animated').removeClass('pulse')},10);
				
				}
			);
	//SET HEADING ALINGMENT
		jQuery('.heading-text-align button').click(
			function()
				{
				jQuery('.heading-text-align button').removeClass('active');
				jQuery(this).addClass('active');
				
				input_element.removeClass('align_left').removeClass('align_right').removeClass('align_center');
				
				if(jQuery(this).hasClass('left'))
					input_element.addClass('align_left');
				if(jQuery(this).hasClass('right'))
					input_element.addClass('align_right');
				if(jQuery(this).hasClass('center'))
					input_element.addClass('align_center');
				}
			);	

//SET HTML SETTINGS
	//SET HTML
		jQuery('div.field-settings-column #set_html').keyup(
			function()
				{
				input_element.html(jQuery(this).val())
				}
			);

// SET IMAGE THUMB SELECTION 
jQuery('.nex-forms-container .single-image-select-group .radio-label, .nex-forms-container .multi-image-select-group .radio-label').live('click',
				function()
					{
					if(!jQuery('.nex-forms-container').hasClass('enable-form-styling'))
						{
						current_image_selection = jQuery(this);
						jQuery('#do_upload_image_selection .fileinput input').trigger('click');
						}
					}
				);
			
			
			jQuery('#do_upload_image_selection .fileinput input').change(
				function()
					{	
					jQuery('#do_upload_image_selection').submit();
					//console.log(jQuery(this).val());	
					}
				)
			
			jQuery('#do_upload_image_selection').ajaxForm({
				data: {
				   action: 'do_upload_image',
				   mimeType: "multipart/form-data"
				},
				//dataType: 'json',
				beforeSubmit: function(formData, jqForm, options) {
					//alert('test');
					//console.log(jQuery('input[name="do_image_upload_preview"]').val())
				},
			   success : function(responseText, statusText, xhr, $form) {
				 //current_image_selection.css('background','url("'+ responseText +'")');
				 current_image_selection.find('img').remove();
				 current_image_selection.append('<img src="'+ responseText +'" class="radio-image">')
				},
				 error: function(jqXHR, textStatus, errorThrown)
					{
					   console.log(errorThrown)
					}
			});

//PANEL SETTINGS
	//SET PANEL HEADING TEXT
		jQuery('div.field-settings-column #set_panel_heading').keyup(
			function()
				{
				current_field.find('div.panel-heading').html(jQuery(this).val())
				}
			);
	//SET PANEL BIU
		set_biu_style('span.panel-heading','div.panel-heading','bold');
		set_biu_style('span.panel-heading','div.panel-heading','italic');
		set_biu_style('span.panel-heading','div.panel-heading','underline');
	
	//SET PANEL COLORS
	change_color('set-panel-heading-text-color','div.panel-heading','color');
	change_color('set-panel-heading-bg-color','div.panel-heading','background-color');
	change_color('set-panel-heading-border-color','div.panel-heading','border-color');
	
	change_color('set-panel-body-bg-color','div.panel-body','background-color');
	change_color('set-panel-body-border-color','div.panel','border-color');
	
	
	
	//SET PANEL HEADING DISPLAY
	jQuery('.show_panel-heading button').click(
		function()
			{
			jQuery('.show_panel-heading button').removeClass('active');
			jQuery(this).addClass('active');
			
			var get_obj = current_field.find('.panel-heading');
		
				if(jQuery(this).hasClass('yes'))
					get_obj.removeClass('hidden');
				if(jQuery(this).hasClass('no'))
					get_obj.addClass('hidden');
			}
		);	
	
	jQuery('.panel-heading-size button').click(
			function()
				{
				jQuery('.panel-heading-size button').removeClass('active');
				jQuery(this).addClass('active');
				
				var get_obj = current_field.find('.panel-heading');
				get_obj.removeClass('input-lg').removeClass('input-sm').removeClass('btn-lg').removeClass('btn-sm');
				
					if(jQuery(this).hasClass('small'))
						get_obj.addClass('btn-sm');
					if(jQuery(this).hasClass('large'))
						get_obj.addClass('btn-lg');
				}
			);
	
	//SET HEADING ALINGMENT
		jQuery('.panel-heading-text-align button').click(
			function()
				{
				jQuery('.panel-heading-text-align button').removeClass('active');
				jQuery(this).addClass('active');
				
				var get_obj = current_field.find('.panel-heading');
				
				get_obj.removeClass('align_left').removeClass('align_right').removeClass('align_center');
				
				if(jQuery(this).hasClass('left'))
					get_obj.addClass('align_left');
				if(jQuery(this).hasClass('right'))
					get_obj.addClass('align_right');
				if(jQuery(this).hasClass('center'))
					get_obj.addClass('align_center');
				}
			);	
	
//SETUP VALIDATION SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
	//SET REQUERED FIELD
		jQuery('.required button').click(
				function()
					{
					jQuery('.required button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.the_input_element');
					var get_obj = current_field.find('label');
					current_field.removeClass('required')
					
					get_input.removeClass('required');
					current_field.find('.is_required').addClass('hidden')
						
					if(current_field.hasClass('select') || current_field.hasClass('multi-select'))
						current_field.find('select').attr('data-required','false').removeClass('required');
					
					if(jQuery(this).hasClass('yes'))
						{
						
						current_field.addClass('required')
						get_input.addClass('required');
						current_field.find('.is_required').removeClass('hidden');
						
						if(current_field.hasClass('select') || current_field.hasClass('multi-select'))
							current_field.find('select').attr('data-required','true').addClass('required');
						
						}
					else
						{
						//current_field.find('.error_message').popover('hide');
						}
					}
				);
		//SET REQUIRED INDICATOR
			jQuery('.required-star button').click(
				function()
					{
					jQuery('.required-star button').removeClass('active');
					jQuery(this).addClass('active');
					//var get_obj = jQuery('#'+current_id).find('label');
					if(jQuery(this).hasClass('empty'))
						current_field.find('.is_required').removeClass('glyphicon-star').removeClass('glyphicon-asterisk').addClass('glyphicon-star-empty');
					else if(jQuery(this).hasClass('asterisk'))
						current_field.find('.is_required').removeClass('glyphicon-star-empty').removeClass('glyphicon-star').addClass('glyphicon-asterisk');
					else if(jQuery(this).hasClass('none'))
						current_field.find('.is_required').removeClass('glyphicon-star-empty').removeClass('glyphicon-star').removeClass('glyphicon-asterisk');
					else
						current_field.find('.is_required').removeClass('glyphicon-star-empty').removeClass('glyphicon-asterisk').addClass('glyphicon-star');	
					}
				);
		//SET VALIDATION FORMAT
			jQuery('select[name="validate-as"]').live('change',
			function()
				{
				current_field.find('input').removeClass('email').removeClass('url').removeClass('phone_number').removeClass('numbers_only').removeClass('text_only');
				current_field.removeClass('email').removeClass('url').removeClass('phone_number').removeClass('numbers_only').removeClass('text_only');	
				
				current_field.find('input').addClass(jQuery(this).val());
				current_field.addClass(jQuery(this).val());
				
				if(jQuery(this).val()=='email')
					current_field.find('.error_message').attr('data-secondary-message','Invalid e-mail format');
				if(jQuery(this).val()=='url')
					current_field.find('.error_message').attr('data-secondary-message','Invalid url format');
				if(jQuery(this).val()=='phone_number')
					current_field.find('.error_message').attr('data-secondary-message','Invalid phone number format');
				if(jQuery(this).val()=='numbers_only')
					current_field.find('.error_message').attr('data-secondary-message','Only numbers are allowed');
				if(jQuery(this).val()=='text_only')
					current_field.find('.error_message').attr('data-secondary-message','Only text are allowed');
				
				jQuery('#set_secondary_error').val(current_field.find('.error_message').attr('data-secondary-message'));
				
				}
		)	
	//SET MAX CHARS
		jQuery('div.field-settings-column #set_max_val').keyup(
			function()
				{
				input_element.attr('maxlength',jQuery(this).val())
				}
			);
	//SET MAX CHARS
		jQuery('div.field-settings-column #set_min_val').keyup(
			function()
				{
				input_element.attr('minlength',jQuery(this).val())
				}
			);
	//SET ERROR MESSAGE
		jQuery('div.field-settings-column #the_error_mesage').keyup(
			function()
				{
				current_field.find('.error_message').attr('data-content',jQuery(this).val())
				}
			);
	//SET SECONDARY ERROR MESSAGE
		jQuery('div.field-settings-column #set_secondary_error').keyup(
			function()
				{
				current_field.find('.error_message').attr('data-secondary-message',jQuery(this).val())
				}
			);
	//SET MAX SIZE PER FILE
	jQuery('div.field-settings-column #max_file_size_pf').keyup(
		function()
			{
			input_element.attr('data-max-size-pf',jQuery(this).val())
			}
		);
	jQuery('div.field-settings-column #max_file_size_pf_error').keyup(
		function()
			{
			input_element.attr('data-max-per-file-message',jQuery(this).val())
			}
		);
	
	
	//SET MAX SIZE ALL FILES
	jQuery('div.field-settings-column #max_file_size_af').keyup(
		function()
			{
			input_element.attr('data-max-size-overall',jQuery(this).val())
			}
		);
	jQuery('div.field-settings-column #max_file_size_af_error').keyup(
		function()
			{
			input_element.attr('data-max-all-file-message',jQuery(this).val())
			}
		);

	//SET MAX UPLOAD LIMIT
	jQuery('div.field-settings-column #max_upload_limit').keyup(
		function()
			{
			input_element.attr('data-max-files',jQuery(this).val())
			}
		);
	jQuery('div.field-settings-column #max_upload_limit_error').keyup(
		function()
			{
			input_element.attr('data-file-upload-limit-message',jQuery(this).val())
			}
		);
	
	
			
			
			
	//SET ALLOWED EXTENTIONS
	jQuery('#set_extensions').live('change',
			function()
				{
				current_field.find('div.get_file_ext').html(jQuery(this).val());
				}
			);	
//SETUP ANIMATION SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////					   
	//SET ANIMATION
	   jQuery('#field_animation').change(
			function()
				{
				
				var animation_preview = jQuery('.animation_preview')
				animation_preview.attr('class','');
				
				animation_preview.addClass('animation_preview');
				animation_preview.addClass(jQuery(this).val());
				animation_preview.addClass('admin_animated');
				
				setTimeout(function(){ animation_preview.removeClass('admin_animated') },1000);
				
				jQuery('#field_animation option').each(
					function()
						{
						current_field.removeClass(jQuery(this).text());
						}
					)
				
				
				if(jQuery(this).val()=='no_animation')
					current_field.removeClass('wow');
				else
					current_field.addClass('wow').addClass(jQuery(this).val());
					}
			);
	//SET DELAY
		jQuery('#animation_delay').keyup(
			function()
				{
				current_field.attr('data-wow-delay', jQuery(this).val()+'s');
				}
			);
	//SET DURATION
		jQuery('#animation_duration').keyup(
			function()
				{
				current_field.attr('data-wow-duration', jQuery(this).val()+'s');
				}
			);
//SET MATH LOGIC SETTINGS
	//SET CURRENT FIELDS
		jQuery('select[name="math_fields"]').change(
			function(){
				jQuery('#set_math_logic_equation').trigger('focus');
				insertAtCaret('set_math_logic_equation', jQuery(this).val());
			}
		);
	//SET MATH EQUATION	
		jQuery('#set_math_logic_equation').keyup(
			function()
				{
				current_field.find('.the_input_element').attr('data-math-equation',jQuery(this).val());
				current_field.find('.the_input_element').attr('data-original-math-equation',jQuery(this).val());
				}
			);
		jQuery('#set_math_logic_equation').blur(
			function()
				{
				current_field.find('.the_input_element').attr('data-math-equation',jQuery(this).val());
				current_field.find('.the_input_element').attr('data-original-math-equation',jQuery(this).val());
				}
			);
		jQuery('#set_math_input_name').keyup(
			function()
				{
				var formated_value = format_illegal_chars(jQuery(this).val());
				jQuery('#set_math_input_name').val(formated_value);
				current_field.find('.set_math_result').attr('name',formated_value)
				}
			);
		jQuery('#set_decimals').keyup(
			function()
				{
				current_field.find('.the_input_element').attr('data-decimal-places',jQuery(this).val())
				}
			);
		
//SET GRID SETTINGS
//COL-1 WIDTH
			jQuery('.col-1-width button').click(
				function()
					{
					jQuery('.col-1-width button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.row .grid_input_holder:eq(0)');
					get_input.removeClass('col-sm-1');
					get_input.removeClass('col-sm-2');
					get_input.removeClass('col-sm-3');
					get_input.removeClass('col-sm-4');
					get_input.removeClass('col-sm-5');
					get_input.removeClass('col-sm-6');
					get_input.removeClass('col-sm-7');
					get_input.removeClass('col-sm-8');
					get_input.removeClass('col-sm-9');
					get_input.removeClass('col-sm-10');
					get_input.removeClass('col-sm-11');
					get_input.removeClass('col-sm-12');

					get_input.addClass(jQuery(this).attr('data-col-width'))

					}
				);
		//COL-2 WIDTH
			jQuery('.col-2-width button').click(
				function()
					{
					jQuery('.col-2-width button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.row .grid_input_holder:eq(1)');
					get_input.removeClass('col-sm-1');
					get_input.removeClass('col-sm-2');
					get_input.removeClass('col-sm-3');
					get_input.removeClass('col-sm-4');
					get_input.removeClass('col-sm-5');
					get_input.removeClass('col-sm-6');
					get_input.removeClass('col-sm-7');
					get_input.removeClass('col-sm-8');
					get_input.removeClass('col-sm-9');
					get_input.removeClass('col-sm-10');
					get_input.removeClass('col-sm-11');
					get_input.removeClass('col-sm-12');
					
					get_input.addClass(jQuery(this).attr('data-col-width'))

					}
				);
		//COL-3 WIDTH
			jQuery('.col-3-width button').click(
				function()
					{
					jQuery('.col-3-width button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.row .grid_input_holder:eq(2)');
					get_input.removeClass('col-sm-1');
					get_input.removeClass('col-sm-2');
					get_input.removeClass('col-sm-3');
					get_input.removeClass('col-sm-4');
					get_input.removeClass('col-sm-5');
					get_input.removeClass('col-sm-6');
					get_input.removeClass('col-sm-7');
					get_input.removeClass('col-sm-8');
					get_input.removeClass('col-sm-9');
					get_input.removeClass('col-sm-10');
					get_input.removeClass('col-sm-11');
					get_input.removeClass('col-sm-12');
					
					get_input.addClass(jQuery(this).attr('data-col-width'))

					}
				);
		
		//COL-4 WIDTH
			jQuery('.col-4-width button').click(
				function()
					{
					jQuery('.col-4-width button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.row .grid_input_holder:eq(3)');
					get_input.removeClass('col-sm-1');
					get_input.removeClass('col-sm-2');
					get_input.removeClass('col-sm-3');
					get_input.removeClass('col-sm-4');
					get_input.removeClass('col-sm-5');
					get_input.removeClass('col-sm-6');
					get_input.removeClass('col-sm-7');
					get_input.removeClass('col-sm-8');
					get_input.removeClass('col-sm-9');
					get_input.removeClass('col-sm-10');
					get_input.removeClass('col-sm-11');
					get_input.removeClass('col-sm-12');
					
					get_input.addClass(jQuery(this).attr('data-col-width'))

					}
				);
		//COL-5 WIDTH
			jQuery('.col-5-width button').click(
				function()
					{
					jQuery('.col-5-width button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.row .grid_input_holder:eq(4)');
					get_input.removeClass('col-sm-1');
					get_input.removeClass('col-sm-2');
					get_input.removeClass('col-sm-3');
					get_input.removeClass('col-sm-4');
					get_input.removeClass('col-sm-5');
					get_input.removeClass('col-sm-6');
					get_input.removeClass('col-sm-7');
					get_input.removeClass('col-sm-8');
					get_input.removeClass('col-sm-9');
					get_input.removeClass('col-sm-10');
					get_input.removeClass('col-sm-11');
					get_input.removeClass('col-sm-12');
					
					get_input.addClass(jQuery(this).attr('data-col-width'))

					}
				);
		//COL-6 WIDTH
			jQuery('.col-6-width button').click(
				function()
					{
					jQuery('.col-6-width button').removeClass('active');
					jQuery(this).addClass('active');
					
					var get_input = current_field.find('.row .grid_input_holder:eq(5)');
					get_input.removeClass('col-sm-1');
					get_input.removeClass('col-sm-2');
					get_input.removeClass('col-sm-3');
					get_input.removeClass('col-sm-4');
					get_input.removeClass('col-sm-5');
					get_input.removeClass('col-sm-6');
					get_input.removeClass('col-sm-7');
					get_input.removeClass('col-sm-8');
					get_input.removeClass('col-sm-9');
					get_input.removeClass('col-sm-10');
					get_input.removeClass('col-sm-11');
					get_input.removeClass('col-sm-12');
					
					get_input.addClass(jQuery(this).attr('data-col-width'))

					}
				);
	}
);


//SETUP MATH LOGIC
function get_math_settings()
	{
	var set_current_fields_math_logic = '';
	set_current_fields_math_logic += '<option value="" selected="selected">--- Select ---</option><optgroup label="Text Fields">';
	jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
		function()
			{
			set_current_fields_math_logic += '<option value="{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}">'+ jQuery(this).attr('name') +'</option>';
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
				set_current_fields_math_logic += '<option value="{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}">'+ jQuery(this).attr('name') +'</option>';
			
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
				set_current_fields_math_logic += '<option value="{'+ jQuery(this).attr('name')  +'}">'+ jQuery(this).attr('name') +'</option>';
			new_check = old_check;
			}
		);	
	set_current_fields_math_logic += '</optgroup>';
	
	set_current_fields_math_logic += '<optgroup label="Selects">';
	jQuery('div.nex-forms-container div.form_field select').each(
		function()
			{
			set_current_fields_math_logic += '<option value="{'+ jQuery(this).attr('name')  +'}">'+ jQuery(this).attr('name') +'</option>';
			}
		);	
	set_current_fields_math_logic += '</optgroup>';
	
	set_current_fields_math_logic += '<optgroup label="Text Areas">';
	jQuery('div.nex-forms-container div.form_field textarea').each(
		function()
			{
			set_current_fields_math_logic += '<option value="{'+ format_illegal_chars(jQuery(this).attr('name'))  +'}">'+ jQuery(this).attr('name') +'</option>';
			}
		);	
	set_current_fields_math_logic += '</optgroup>';
						
	jQuery('select[name="math_fields"]').html(set_current_fields_math_logic);
	
	jQuery('#set_math_input_name').val(format_illegal_chars(current_field.find('.set_math_result').attr('name')))
	jQuery('#set_decimals').val(current_field.find('.the_input_element').attr('data-decimal-places'));
	jQuery('#set_math_logic_equation').val(current_field.find('.the_input_element').attr('data-math-equation'))
	}



function get_label_settings(){		
//LABEL TEXT
	jQuery('div.field-settings-column #set_label').val(current_field.find('label span.the_label').text())

//GET LABEL BIU
	get_biu_style(current_field,'span.label','span.the_label','bold')
	get_biu_style(current_field,'span.label','span.the_label','italic')
	get_biu_style(current_field,'span.label','span.the_label','underline')
	
//GET SUB LABEL BIU	
	get_biu_style(current_field,'span.sub-label','small.sub-text','bold')
	get_biu_style(current_field,'span.sub-label','small.sub-text','italic')
	get_biu_style(current_field,'span.sub-label','small.sub-text','underline')
	
//GET LABEL COLOR	
	jQuery(".label-color").trigger("colorpickersliders.updateColor", current_field.find('span.the_label').css('color'));
//GET SUB-LABEL COLOR
	jQuery(".sub-label-color").trigger("colorpickersliders.updateColor", current_field.find('small.sub-text').css('color'));
		
//SUB-LABEL TEXT
	jQuery('div.field-settings-column #set_subtext').val(current_field.find('label small.sub-text').text())
	
//GET LABEL POSITION
	//var label_container = current_field.find('.label_container');
	jQuery('.label-position button').removeClass('active');
	if(label_container.hasClass('col-sm-12') && label_container.attr('style')!='display: none;')
		jQuery('.label-position button.top').addClass('active');
	if(!label_container.hasClass('col-sm-12') && !label_container.hasClass('pos_right'))
		jQuery('.label-position button.left').addClass('active');
	if(label_container.hasClass('pos_right'))
		jQuery('.label-position button.right').addClass('active');
	if(label_container.attr('style')=='display: none;')
		jQuery('.label-position button.none').addClass('active');
		
//GET LABEL ALINGMENT
	jQuery('.align-label button').removeClass('active');
	if(label_container.hasClass('align_left'))
		jQuery('.align-label button.left').addClass('active');	
	else if(label_container.hasClass('align_right'))
		jQuery('.align-label button.right').addClass('active');
	else if(label_container.hasClass('align_center'))
		jQuery('.align-label button.center').addClass('active');
	else
		jQuery('.align-label button.top').addClass('active');
	
//GET LABEL SIZE
	jQuery('.label-size button').removeClass('active');
	if(label_container.find('label').hasClass('text-lg'))
		jQuery('.label-size button.large').addClass('active');
	else if(label_container.find('label').hasClass('text-sm'))
		jQuery('.label-size button.small ').addClass('active');
	else
		jQuery('.label-size button.normal').addClass('active');
	
	for(var i=1;i<=12;i++)
		{
		if(label_container.hasClass('col-sm-'+i))
			var get_label_width = i;
		}
	
//GET LABEL WIDTH	
	jQuery('div.field-settings-column #slider').slider({ value: get_label_width});
	jQuery('div.field-settings-column .width_indicator.left input').val(get_label_width);
	if(get_label_width<12)
		jQuery('div.field-settings-column .width_indicator.right input').val(12-get_label_width);
	else
		jQuery('div.field-settings-column .width_indicator.right input').val(get_label_width);

}



function get_input_settings(){
	
	jQuery('#set_input_placeholder').prop('disabled',false);
	jQuery('#set_input_id').prop('disabled',false);
	
	
	
	
	jQuery('.setting-wrapper').hide();
	
	if(current_field.hasClass('grid-system'))
		{
		jQuery('.settings-grid-system').hide();
		
		if(current_field.hasClass('grid-system-1'))
			{
			jQuery('.settings-col-1').show();	
			}
		if(current_field.hasClass('grid-system-2'))
			{
			jQuery('.settings-col-1').show();
			jQuery('.settings-col-2').show();	
			}
		if(current_field.hasClass('grid-system-3'))
			{
			jQuery('.settings-col-1').show();
			jQuery('.settings-col-2').show();
			jQuery('.settings-col-3').show();		
			}
		if(current_field.hasClass('grid-system-4'))
			{
			jQuery('.settings-col-1').show();
			jQuery('.settings-col-2').show();
			jQuery('.settings-col-3').show();
			jQuery('.settings-col-4').show();		
			}
		if(current_field.hasClass('grid-system-6'))
			{
			jQuery('.settings-col-1').show();
			jQuery('.settings-col-2').show();
			jQuery('.settings-col-3').show();
			jQuery('.settings-col-4').show();
			jQuery('.settings-col-5').show();
			jQuery('.settings-col-6').show();		
			}
		
		
		for(var i=0;i<=5;i++)
		{
		jQuery('.col-'+(i+1)+'-width button').removeClass('active');
		var grid_col = current_field.find('.row .grid_input_holder:eq('+i+')');
		if(grid_col)
			{
			var grid_class = grid_col.attr('class');
			if(grid_class)
				var grid_class2 = grid_class.replace('grid_input_holder','');
			if(grid_class2)
				var grid_class3 = grid_class2.replace('-sm','');
			if(grid_class3)
				{
				jQuery('.col-'+(i+1)+'-width button.'+grid_class3.trim()).addClass('active');
				}
			}
		}
		
		}
	
	
	if(current_field.hasClass('text'))
		{
		jQuery('.setting-recreate-field').show();	
		}
	if(current_field.hasClass('text') || current_field.hasClass('textarea') || current_field.hasClass('select') || current_field.hasClass('multi-select')  || current_field.hasClass('touch_spinner') || current_field.hasClass('autocomplete') || current_field.hasClass('password') || current_field.hasClass('date') || current_field.hasClass('time') || current_field.hasClass('upload-single') || current_field.hasClass('upload-multi') || current_field.hasClass('preset_fields') || current_field.hasClass('is_panel'))
		jQuery('.setting-wrapper.setting-bg-image').show();
	if(current_field.hasClass('text') || current_field.hasClass('textarea') || current_field.hasClass('select') || current_field.hasClass('multi-select')  || current_field.hasClass('touch_spinner') || current_field.hasClass('autocomplete') || current_field.hasClass('password') || current_field.hasClass('date') || current_field.hasClass('time') ||  current_field.hasClass('upload-single') || current_field.hasClass('upload-multi') || current_field.hasClass('preset_fields') || current_field.hasClass('nf-color-picker'))
		{
		jQuery('.setting-wrapper.setting-input-add-ons').show();
		jQuery('.settings-input-styling').show();
		jQuery('#set_input_val').show();
		jQuery('.ungeneric-input-settings').show();
		}
	if(current_field.hasClass('radio-group') || current_field.hasClass('check-group') || current_field.hasClass('single-image-select-group') || current_field.hasClass('multi-image-select-group') || current_field.hasClass('tags') || current_field.hasClass('smily-rating')  || current_field.hasClass('thumb-rating'))
		{
		jQuery('.ungeneric-input-settings').show();	
		}
	
//GET TAGS SETTINGS
	if(current_field.hasClass('tags'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		jQuery('#set_input_id').prop('disabled',true);
		
		jQuery('#max_tags').show();
		jQuery('.ungeneric-input-settings').show();
		jQuery('.settings-input-styling').show();
		//jQuery('.setting-wrapper.setting-input-add-ons').show();
		jQuery('#max_tags').val(current_field.find('#tags').attr('data-max-tags'))
		
		}
//GET THUMB RATING SETTINGS
	if(current_field.hasClass('thumb-rating'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		jQuery('#set_input_id').prop('disabled',true);
			
		jQuery('.setting-wrapper.settings-thumb-rating').show();
		
		jQuery('#set_thumbs_up_val').val(current_field.find('input.nf-thumbs-o-up').attr('value'));
		jQuery('#set_thumbs_down_val').val(current_field.find('input.nf-thumbs-o-down').attr('value'));
		}	

//GET SMILY RATING SETTINGS
	if(current_field.hasClass('smily-rating'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		jQuery('#set_input_id').prop('disabled',true);
			
		jQuery('.setting-wrapper.settings-smily-rating').show();
		jQuery('.survey-field-settings').show();
		
		jQuery('#set_smily_frown_val').val(current_field.find('input.nf-smile-bad').attr('value'));
		jQuery('#set_smily_average_val').val(current_field.find('input.nf-smile-average').attr('value'));
		jQuery('#set_smily_good_val').val(current_field.find('input.nf-smile-good').attr('value'));
		}	
	
//GET STAR RATING SETTINGS
	if(current_field.hasClass('star-rating'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		jQuery('.survey-field-settings').show();	
		jQuery('.setting-wrapper.settings-star-rating').show();
	//GET TOTAL STARS	
		jQuery('#total_stars').val(current_field.find('#star').attr('data-total-stars'))
	//ENABLE HALF STAR
		jQuery('select[name="set_half_stars"] option').prop('selected',false);		
		if(current_field.find('#star').attr('data-enable-half')=='false')
			jQuery('select[name="set_half_stars"] option[value="no"]').attr('selected','selected');
		else
			jQuery('select[name="set_half_stars"] option[value="yes"]').attr('selected','selected');	
		
		}
	if(current_field.hasClass('upload-image'))
		{
		jQuery('.img-upload-input-settings').show();
		}
	
//GET SELECT SETTINGS	
	if (current_field.hasClass('select') || current_field.hasClass('multi-select'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		
		jQuery('.settings-select-options').show();
		
		jQuery('#set_default_select_value').show();
		jQuery('#set_input_val').hide();
	//GET OPTIONS
		var current_options = ''
		current_field.find('select option').each(
			function()
				{
				if(jQuery(this).attr('selected')!='selected')
					{
					if(jQuery(this).text()!=jQuery(this).attr('value'))
						current_options += jQuery(this).attr('value')+'=='+jQuery(this).text() +'\n';
					else
						current_options += jQuery(this).text() +'\n';
					
					}
						
				}
			);
		jQuery('#set_options').val(current_options)
		
	//GET DEFAULT OPTION
		jQuery('#set_default_select_value').val((current_field.find('select option:selected').text()) ? current_field.find('select option:selected').val()+'=='+current_field.find('select option:selected').text() : '--- Select ---')
		}

//GET RADIO/CHECK SETTINGS	
	if (current_field.hasClass('check-group') || current_field.hasClass('radio-group') || current_field.hasClass('single-image-select-group') || current_field.hasClass('multi-image-select-group'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		jQuery('#set_input_id').prop('disabled',true);
		jQuery('.survey-field-settings').show();
		jQuery('.settings-radio-options').show();
		jQuery('.settings-radio-styling').show();
		
		
		//GET RADIO/CHECK LAYOUT
		jQuery('.display-radios-checks button').removeClass('active');
		if(current_field.find('.the-radios').attr('data-layout')=='1c')
			jQuery('.display-radios-checks button.1c').addClass('active');
		else if(current_field.find('.the-radios').attr('data-layout')=='2c')
			jQuery('.display-radios-checks button.2c').addClass('active');
		else if(current_field.find('.the-radios').attr('data-layout')=='3c')
			jQuery('.display-radios-checks button.3c').addClass('active');
		else if(current_field.find('.the-radios').attr('data-layout')=='4c')
			jQuery('.display-radios-checks button.4c').addClass('active');
		else
			jQuery('.display-radios-checks button.inline').addClass('active');
		
		var current_inputs = ''
		if(current_field.hasClass('check-group') || current_field.hasClass('classic-check-group'))
			{
			current_field.find('div span.check-label').each(
				function()
					{
					if(jQuery(this).html()!=jQuery(this).parent().find('input').val())
						current_inputs += jQuery(this).parent().find('input').val()+'=='+jQuery(this).html() +'\n';	
					else
						current_inputs += jQuery(this).html() +'\n';	
					}
				);	
			}
		else
			{
			current_field.find('div span.radio-label').each(
				function()
					{
					if(jQuery(this).html()!=jQuery(this).parent().find('input').val())
						current_inputs += jQuery(this).parent().find('input').val()+'=='+jQuery(this).html() +'\n';	
					else
						current_inputs += jQuery(this).html() +'\n';
					}
				);
			}
		jQuery('#set_radios').val(current_inputs)
		
		
//RESET RADIO COLORS
	jQuery(".set-radio-label-color").trigger("colorpickersliders.updateColor",'#444444');
	jQuery(".set-radio-text-color").trigger("colorpickersliders.updateColor",'#555555');
	jQuery(".set-radio-bg-color").trigger("colorpickersliders.updateColor",'#eeeeee');
	jQuery(".set-radio-border-color").trigger("colorpickersliders.updateColor",'#cccccc');
//GET INPUT RADIO COLOR	
	jQuery(".set-radio-label-color").trigger("colorpickersliders.updateColor", current_field.find('span.input-label').css('color'));	
//GET INPUT RADIO COLOR	
	jQuery(".set-radio-text-color").trigger("colorpickersliders.updateColor", current_field.find('a').css('color'));	
//GET INPUT RADIO BACKGOUND COLOR	
	jQuery(".set-radio-bg-color").trigger("colorpickersliders.updateColor", current_field.find('a').css('background-color'));
//GET INPUT RADIO BORDER COLOR	
	jQuery(".set-radio-border-color").trigger("colorpickersliders.updateColor", current_field.find('a').css('border-top-color'));

//GET RADIO ICON
	if(strstr(current_field.find('.the-radios').attr('data-checked-class'),'fa-'))
		{
		jQuery('div.field-settings-column .current_radio_icon i').attr('class','fa '+current_field.find('.the-radios').attr('data-checked-class')).text('')
		jQuery('div.field-settings-column #set_radio_icon').val('fa '+current_field.find('.the-radios').attr('data-checked-class'));
		}
	else
		{
		jQuery('div.field-settings-column .current_radio_icon i').attr('class',current_field.find('.the-radios').attr('data-checked-class')).text('')
		jQuery('div.field-settings-column #set_radio_icon').val(current_field.find('.the-radios').attr('data-checked-class'));
		}
		
	}

//GET SLIDER SETTINGS	
	if (current_field.hasClass('slider'))
		{
		jQuery('#set_input_placeholder').prop('disabled',true);
		
		jQuery('.settings-slider-options').show();
		jQuery('.settings-slider-styling').show();
		
	//GET SLIDER COLORS
		jQuery(".set-slider-handel-text-color").trigger("colorpickersliders.updateColor",'#444444');
		jQuery(".set-slider-handel-bg-color").trigger("colorpickersliders.updateColor",'#ffffff');
		jQuery(".set-slider-handel-border-color").trigger("colorpickersliders.updateColor",'#eeeeee');
		jQuery(".set-slider-bg-color").trigger("colorpickersliders.updateColor",'#ffffff');
		jQuery(".set-slider-fill-color").trigger("colorpickersliders.updateColor",'#f2f2f2');
		jQuery(".set-slider-border-color").trigger("colorpickersliders.updateColor",'#eeeeee');
	
		jQuery(".set-slider-handel-text-color").trigger("colorpickersliders.updateColor", current_field.find('.ui-slider-handle').css('color'));	
		jQuery(".set-slider-handel-bg-color").trigger("colorpickersliders.updateColor", current_field.find('.ui-slider-handle').css('background-color'));
		jQuery(".set-slider-handel-border-color").trigger("colorpickersliders.updateColor", current_field.find('.ui-slider-handle').css('border-top-color'));
	
		jQuery(".set-slider-bg-color").trigger("colorpickersliders.updateColor", current_field.find('.ui-slider').css('background-color'));	
		jQuery(".set-slider-fill-color").trigger("colorpickersliders.updateColor", current_field.find('.ui-slider-range').css('background-color'));
		jQuery(".set-slider-border-color").trigger("colorpickersliders.updateColor", current_field.find('.ui-slider').css('border-top-color'));
		
		
		
		jQuery('#count_text').val(current_field.find('#slider').attr('data-count-text'))
		
		jQuery('#minimum_value').val(current_field.find('#slider').attr('data-min-value'))
	
		jQuery('#step_value').val(current_field.find('#slider').attr('data-step-value'))

		jQuery('#maximum_value').val(current_field.find('#slider').attr('data-max-value'))

		jQuery('#start_value').val(current_field.find('#slider').attr('data-starting-value'))
		}
		
		
//GET DATE TIME SETTINGS		
	if (current_field.hasClass('date') || current_field.hasClass('date-time') || current_field.hasClass('time'))
		{
		jQuery('.settings-date-options').show();
		jQuery('#set_date_format').val(current_field.find('#datetimepicker').attr('data-format'))
		}
//GET SPINNER SETTINGS
if (current_field.hasClass('touch_spinner'))
		{
		jQuery('.settings-spinner-options').show();
		jQuery('#set_input_val').hide();
		jQuery('#spin_start_value').show();
		//jQuery('#set_date_format').val(current_field.find('#datetimepicker').attr('data-format'))
		
		
		jQuery('#spin_minimum_value').val(current_field.find('#spinner').attr('data-minimum'))
	
		jQuery('#spin_step_value').val(current_field.find('#spinner').attr('data-step'))

		jQuery('#spin_maximum_value').val(current_field.find('#spinner').attr('data-maximum'))

		jQuery('#spin_start_value').val(current_field.find('#spinner').attr('data-starting-value'))
		
		jQuery('#spin_decimal').val(current_field.find('#spinner').attr('data-decimals'))
		
		}
//GET AUTOCOMPLETE SETTINGS
	if (current_field.hasClass('autocomplete'))
		{
			
		jQuery('.settings-autocomplete-options').show();
		jQuery('#set_selections').val(current_field.find('.get_auto_complete_items').text())			
		}

//GET SUBMIT BUTTON SETTINGS		
	if(current_field.hasClass('submit-button') || current_field.hasClass('submit-button') || current_field.hasClass('nex-step') || current_field.hasClass('prev-step'))
		{
		jQuery('.settings-input-styling, .setting-bg-image').show();
		jQuery('.ungeneric-input-settings').hide();
		jQuery('.button-settings').show();
		
		jQuery('#set_input_val').hide();
		jQuery('#set_button_val').show();
		//GET BUTTON TEXT
		jQuery('div.field-settings-column #set_button_val').val(input_element.html())
		
	//GET BUTTON POSITION
		jQuery('.button-position button').removeClass('active');
		if(input_container.hasClass('align_right'))
			jQuery('.button-position button.right').addClass('active');
		else if(input_container.hasClass('align_center'))
			jQuery('.button-position button.center').addClass('active');
		else
			jQuery('.button-position button.left').addClass('active');	
	
	
	//GET BUTTON TEXT ALINGMENT
		jQuery('.button-text-align button').removeClass('active');
		if(input_element.hasClass('text-right'))
			jQuery('.button-text-align button.right').addClass('active');
		else if(input_element.hasClass('text-center'))
			jQuery('.button-text-align button.center').addClass('active');
		else
			jQuery('.button-text-align button.left').addClass('active');	
	
	//GET BUTTON SIZE
		jQuery('.button-size button').removeClass('active');
		if(input_element.hasClass('btn-lg'))
			jQuery('.button-size button.large').addClass('active');
		else if(input_element.hasClass('btn-sm'))
			jQuery('.button-size button.small ').addClass('active');
		else
			jQuery('.button-size button.normal').addClass('active');
		
		
	//GET BUTTON WIDTH
		jQuery('.button-width button').removeClass('active');
		if(input_element.hasClass('full_width') || input_element.hasClass('col-sm-12'))
			jQuery('.button-width button.full_button').addClass('active');
		else
			jQuery('.button-width button.default').addClass('active');
	
	//GET BUTTON TYPE
		jQuery('.button-type button').removeClass('active');
		if(input_element.hasClass('nex-step'))
			jQuery('.button-type button.next').addClass('active');
		else if(input_element.hasClass('prev-step'))
			jQuery('.button-type button.prev').addClass('active');
		else
			jQuery('.button-type button.do-submit').addClass('active');
		
		}

//GET HEADING SETTINGS
	if(current_field.hasClass('heading') || current_field.hasClass('math_logic'))
		{
		jQuery('.settings-input-styling').show();
		jQuery('.ungeneric-input-settings').hide();
		jQuery('.heading-settings').show();
		
		jQuery('#set_input_val').hide();
		jQuery('#set_heading_text').show();
		//GET HEADING TEXT
		jQuery('div.field-settings-column #set_heading_text').val(input_element.html());
		
		//GET HEADING SIZE
		jQuery('.heading-size button').removeClass('active');
		if(input_element.is('h2'))
			jQuery('.heading-size button.heading_2').addClass('active');
		else if(input_element.is('h3'))
			jQuery('.heading-size button.heading_3').addClass('active');
		else if(input_element.is('h4'))
			jQuery('.heading-size button.heading_4').addClass('active');
		else if(input_element.is('h5'))
			jQuery('.heading-size button.heading_5').addClass('active');
		else if(input_element.is('h6'))
			jQuery('.heading-size button.heading_6').addClass('active');
		else
			jQuery('.heading-size button.heading_1').addClass('active');
		
		//GET HEADING ALINGMENT
		jQuery('.heading-text-align button').removeClass('active');
		if(input_element.hasClass('align_right'))
			jQuery('.heading-text-align button.right').addClass('active');
		else if(input_element.hasClass('align_center'))
			jQuery('.heading-text-align button.center').addClass('active');
		else
			jQuery('.heading-text-align button.left').addClass('active');	
		
		
		}
		

//GET HTML SETTINGS
	if(current_field.hasClass('paragraph') || current_field.hasClass('html'))
		{
		jQuery('.settings-html').show();
		jQuery('.ungeneric-input-settings').hide();
		jQuery('div.field-settings-column #set_html').val(input_element.html());
		}

//GET PANEL SETTINGS
	if(current_field.hasClass('is_panel'))
		{
		//jQuery('.settings-input-styling').show();
		////jQuery('.ungeneric-input-settings').hide();
		jQuery('.panel-settings').show();
		jQuery('.ungeneric-input-settings').hide();
		//GET HEADING TEXT
		//jQuery('div.field-settings-column #set_heading_text').val(input_element.html());
		
		//GET INPUT BIU
		get_biu_style(current_field,'span.panel-heading','div.panel-heading','bold')
		get_biu_style(current_field,'span.panel-heading','div.panel-heading','italic')
		get_biu_style(current_field,'span.panel-heading','div.panel-heading','underline')
		
		jQuery(".set-panel-heading-text-color").trigger("colorpickersliders.updateColor", current_field.find('div.panel-heading').css('color'));	
		jQuery(".set-panel-heading-bg-color").trigger("colorpickersliders.updateColor", current_field.find('div.panel-heading').css('background-color'));
		jQuery(".set-panel-heading-border-color").trigger("colorpickersliders.updateColor", current_field.find('div.panel-heading').css('border-bottom-color'));
		
		jQuery(".set-panel-body-bg-color").trigger("colorpickersliders.updateColor", current_field.find('div.panel-body').css('background-color'));
		jQuery(".set-panel-body-border-color").trigger("colorpickersliders.updateColor", current_field.find('div.panel').css('border-top-color'));
		
	//heading size
		jQuery('.panel-heading-size button').removeClass('active');
		if(current_field.find('div.panel-heading').hasClass('btn-lg'))
			jQuery('.panel-heading-size button.large').addClass('active');
		else if(current_field.find('div.panel-heading').hasClass('btn-sm'))
			jQuery('.panel-heading-size button.small ').addClass('active');
		else
			jQuery('.panel-heading-size button.normal').addClass('active');
	
	//show heading		
		jQuery('.show_panel-heading button').removeClass('active');
		if(current_field.find('div.panel-heading').hasClass('hidden'))
			jQuery('.show_panel-heading button.no').addClass('active');
		else
			jQuery('.show_panel-heading button.yes').addClass('active');
	
	
	//Panel heading text aling
		jQuery('.panel-heading-text-align button').removeClass('active');
		if(current_field.find('div.panel-heading').hasClass('align_right'))
			jQuery('.panel-heading-text-align button.right').addClass('active');
		else if(current_field.find('div.panel-heading').hasClass('align_center'))
			jQuery('.panel-heading-text-align button.center').addClass('active');
		else
			jQuery('.panel-heading-text-align button.left').addClass('active');	
		
		jQuery('div.field-settings-column #set_panel_heading').val(current_field.find('.panel-heading').html());
		//jQuery('div.field-settings-column #set_panel_body').val(current_field.find('.panel-body').html());
		
		}
		

//GET INPUT SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				

//GET REPLICATE
	jQuery('.recreate-field button').removeClass('active');
	if(current_field.hasClass('field-replication-enabled'))
		jQuery('.recreate-field button.enable-recreation').addClass('active');
	else
		jQuery('.recreate-field button.disable-recreation').addClass('active');

//GET INPUT NAME
	jQuery('div.field-settings-column #set_input_name').val(format_illegal_chars(input_element.attr('name')))
//GET INPUT PLACEHOLDER
	jQuery('div.field-settings-column #set_input_placeholder').val(input_element.attr('placeholder'))
//GET INPUT ID
	jQuery('div.field-settings-column #set_input_id').val(input_element.attr('id'))
//GET INPUT CLASS
	jQuery('div.field-settings-column #set_input_class').val(input_element.attr('class'))
//GET INPUT VALUE
	jQuery('div.field-settings-column #set_input_val').val(input_element.attr('value'))

//GET INPUT BIU
	get_biu_style(current_field,'span.input','.the_input_element','bold')
	get_biu_style(current_field,'span.input','.the_input_element','italic')
	get_biu_style(current_field,'span.input','.the_input_element','underline')

//GET INPUT COLOR	
	jQuery(".input-color").trigger("colorpickersliders.updateColor", input_element.css('color'));
//GET INPUT BG COLOR	
	jQuery(".input-bg-color").trigger("colorpickersliders.updateColor", input_element.css('background-color'));
//GET INPUT BORDER COLOR	
	jQuery(".input-border-color").trigger("colorpickersliders.updateColor", input_element.css('border-top-color'));


	if(current_field.find('.prefix span').attr('class'))
		{
		//GET INPUT PRE-ADD-ON CLASS
		jQuery('div.field-settings-column #set_icon_before').val(current_field.find('.prefix span').attr('class'))
		//GET INPUT PRE-ADD-ON ICON
		jQuery('div.field-settings-column .current_icon_before i').attr('class',current_field.find('.prefix span').attr('class')).text('')
		}
	else
		{
		jQuery('div.field-settings-column #set_icon_before').val('')
		jQuery('div.field-settings-column .current_icon_before i').attr('class','').text('Select Icon');
		}
//GET INPUT CONTAINER ALINGMENT
	jQuery('.align-input-container button').removeClass('active');
	if(current_field.find('.input_container').hasClass('align_right'))
		jQuery('.align-input-container button.right').addClass('active');
	else if(current_field.find('.input_container').hasClass('align_center'))
		jQuery('.align-input-container button.center').addClass('active');
	else
		jQuery('.align-input-container button.left').addClass('active');


//GET INPUT ALINGMENT
	jQuery('.align-input button').removeClass('active');
	if(input_element.hasClass('align_right'))
		jQuery('.align-input button.right').addClass('active');
	else if(input_element.hasClass('align_center'))
		jQuery('.align-input button.center').addClass('active');
	else
		jQuery('.align-input button.left').addClass('active');
	
//GET INPUT SIZE
	jQuery('.input-size button').removeClass('active');
	if(input_element.hasClass('input-lg'))
		jQuery('.input-size button.large').addClass('active');
	else if(input_element.hasClass('input-sm'))
		jQuery('.input-size button.small ').addClass('active');
	else
		jQuery('.input-size button.normal').addClass('active');

//GET CORNERS 
	jQuery('.input-corners button').removeClass('active');
	if(current_field.hasClass('square'))
		jQuery('.input-corners button.square').addClass('active');
	else if(current_field.hasClass('pill'))
		jQuery('.input-corners button.pill ').addClass('active');
	else
		jQuery('.input-corners button.normal').addClass('active');

//RESET PRE-ADD-ON COLORS
	jQuery(".pre-icon-text-color").trigger("colorpickersliders.updateColor",'#555555');
	jQuery(".pre-icon-bg-color").trigger("colorpickersliders.updateColor",'#eeeeee');
	jQuery(".pre-icon-border-color").trigger("colorpickersliders.updateColor",'#cccccc');
//GET INPUT PRE-ADD-ON COLOR	
	jQuery(".pre-icon-text-color").trigger("colorpickersliders.updateColor", input_element.parent().find('.prefix ').css('color'));	
//GET INPUT PRE-ADD-ON BACKGOUND COLOR	
	jQuery(".pre-icon-bg-color").trigger("colorpickersliders.updateColor", input_element.parent().find('.prefix ').css('background-color'));
//GET INPUT PRE-ADD-ON BORDER COLOR	
	jQuery(".pre-icon-border-color").trigger("colorpickersliders.updateColor", input_element.parent().find('.prefix ').css('border-top-color'));

	if(current_field.find('.postfix span').attr('class'))
		{
		//GET INPUT POST-ADD-ON CLASS
		jQuery('div.field-settings-column #set_icon_after').val(current_field.find('.postfix span').attr('class'))
		//GET INPUT POST-ADD-ON ICON
		jQuery('div.field-settings-column .current_icon_after i').attr('class',current_field.find('.postfix span').attr('class')).text('')
		}
	else
		{
		jQuery('div.field-settings-column #set_icon_after').val('')
		jQuery('div.field-settings-column .current_icon_after i').attr('class','').text('Select Icon');
		}
		
//RESET POST-ADD-ON COLORS
	jQuery(".post-icon-text-color").trigger("colorpickersliders.updateColor",'#555555');
	jQuery(".post-icon-bg-color").trigger("colorpickersliders.updateColor",'#eeeeee');
	jQuery(".post-icon-border-color").trigger("colorpickersliders.updateColor",'#cccccc');
//GET INPUT POST-ADD-ON COLOR	
	jQuery(".post-icon-text-color").trigger("colorpickersliders.updateColor", input_element.parent().find('.postfix ').css('color'));	
//GET INPUT POST-ADD-ON BACKGOUND COLOR	
	jQuery(".post-icon-bg-color").trigger("colorpickersliders.updateColor", input_element.parent().find('.postfix ').css('background-color'));
//GET INPUT POST-ADD-ON BACKGOUND COLOR	
	jQuery(".post-icon-border-color").trigger("colorpickersliders.updateColor", input_element.parent().find('.postfix ').css('border-top-color'));

//GET BACKGOUND TARGET ELEMENT
	if(current_field.hasClass('other-elements') && current_field.hasClass('grid'))
		var get_bg_target = current_field.find('.panel-body');
	else
		var get_bg_target = input_element;

//GET INPUT BACKGROUND IMAGE
	var image = get_bg_target.css('background-image');
	if(image)
		var image2 = image.replace( 'url("','');
	if(image2)
		var image3 = image2.replace( '")','');
	
	if(	image3 && image3!='undefined' && image3!='none' && !strstr(image3,'nex-forms-main'))
		{
		if(jQuery('#do-upload-image .fileinput-preview img').length > 0)
			jQuery('#do-upload-image .fileinput-preview img').attr('src',image3);
		else
			jQuery('#do-upload-image .fileinput-preview').append('<img src="'+ image3 +'">');
			
		jQuery('.field-settings-column .fileinput').removeClass('fileinput-new').addClass('fileinput-exists');
		jQuery('.field-settings-column .fileinput input').attr('name','do_image_upload_preview');
		}
	else
		{
		jQuery('#do-upload-image .fileinput-preview img').remove();
		jQuery('.field-settings-column .fileinput').removeClass('fileinput-exists').addClass('fileinput-new');
		jQuery('.field-settings-column .fileinput input').attr('name','do_image_upload_preview');
		}
	
//GeT Upload button text
	jQuery('div.field-settings-column #img-upload-select').val(current_field.find('span.fileinput-new').text())
//GeT Change button text text
	jQuery('div.field-settings-column #img-upload-change').val(current_field.find('span.fileinput-exists').text())
//GeT Upload remkove button text
	jQuery('div.field-settings-column #img-upload-remove').val(current_field.find('a.fileinput-exists').text())
		
	

//GET BACKGOUND IMAGE SIZE		
	jQuery('.bg-size button').removeClass('active');
	if(get_bg_target.css('background-size')=='cover')
		jQuery('.bg-size button.cover').addClass('active');
	else if(get_bg_target.css('background-size')=='contain')
		jQuery('.bg-size button.contain').addClass('active');
	else
		jQuery('.bg-size button.auto').addClass('active');

//GET BACKGOUND IMAGE REPEAT
	jQuery('.bg-repeat button').removeClass('active');

	if(get_bg_target.css('background-repeat')=='no-repeat')
		jQuery('.bg-repeat button.no-repeat').addClass('active');
	else if(get_bg_target.css('background-repeat')=='repeat-x')
		jQuery('.bg-repeat button.repeat-x').addClass('active');
	else if(get_bg_target.css('background-repeat')=='repeat-y')
		jQuery('.bg-repeat button.repeat-y').addClass('active');
	else
		jQuery('.bg-repeat button.repeat').addClass('active');

//GET BACKGOUND POSITION	
	jQuery('.bg-position button').removeClass('active');
	if(get_bg_target.css('background-position')=='center' || get_bg_target.css('background-position')=='50% 50%')
		jQuery('.bg-position button.center').addClass('active');
	else if(get_bg_target.css('background-position')=='right' || get_bg_target.css('background-position')=='100% 50%')
		jQuery('.bg-position button.right').addClass('active');
	else
		jQuery('.bg-position button.left').addClass('active');

}

function get_validation_settings(){
	
	//console.log('test')
	jQuery('.uploader-settings').hide();
	jQuery('.max-min-settings').hide();
	
	if(current_field.hasClass('upload_fields'))
		{
			jQuery('.uploader-settings').show();
			jQuery('#set_extensions').val(current_field.find('div.get_file_ext').text())
			
		}
	if(current_field.hasClass('text') || current_field.hasClass('classic-text')  || current_field.hasClass('classic-textarea') || current_field.hasClass('preset_fields') || current_field.hasClass('textarea'))
		{
			jQuery('.max-min-settings').show();
			
		}
	if(current_field.hasClass('upload-multi'))
		{
		jQuery('.multi-upload-validation-settings').show();
		}
	//GET MIN/MAX	
	jQuery('div.field-settings-column #set_max_val').val(input_element.attr('maxlength'))
	jQuery('div.field-settings-column #set_min_val').val(input_element.attr('minlength'))
	
	//GET ERROR MESSAGE
	jQuery('div.field-settings-column #the_error_mesage').val(current_field.find('.error_message').attr('data-content'))
	
	//GET SECONDARY ERROR MESSAGE
	jQuery('div.field-settings-column #set_secondary_error').val(current_field.find('.error_message').attr('data-secondary-message'))
	
	//GET MAX SIZE PER FILE
	jQuery('div.field-settings-column #max_file_size_pf').val(current_field.find('.error_message').attr('data-max-size-pf'))
	jQuery('div.field-settings-column #max_file_size_pf_error').val(current_field.find('.error_message').attr('data-max-per-file-message'))
	
	//GET MAX SIZE ALL FILES
	jQuery('div.field-settings-column #max_file_size_af').val(current_field.find('.error_message').attr('data-max-size-overall'))
	jQuery('div.field-settings-column #max_file_size_af_error').val(current_field.find('.error_message').attr('data-max-all-file-message'))
	
	//GET FILE UPLOAD LIMIT
	jQuery('div.field-settings-column #max_upload_limit').val(current_field.find('.error_message').attr('data-max-files'))
	jQuery('div.field-settings-column #max_upload_limit_error').val(current_field.find('.error_message').attr('data-file-upload-limit-message'))
	
	
	
	//GET REQUIRED FIELD STATUS
	jQuery('.required button').removeClass('active');
	if(current_field.hasClass('required'))
		jQuery('.required button.yes').addClass('active');
	else
		jQuery('.required button.no').addClass('active');
	
	//GET REQUIRED FIELD INDICATOR
	jQuery('.required-star button').removeClass('active');
	if(current_field.find('.is_required').hasClass('glyphicon-star-empty'))
		jQuery('.required-star button.empty').addClass('active');
	else if(current_field.find('.is_required').hasClass('glyphicon-asterisk'))
		jQuery('.required-star button.asterisk').addClass('active');
	else if(current_field.find('.is_required').hasClass('glyphicon-star'))
		jQuery('.required-star button.full').addClass('active');
	else
		jQuery('.required-star button.none').addClass('active');
	
	//GET VALIDATION FORMAT
	jQuery('select[name="validate-as"] option').prop('selected',false);	    
	if(current_field.hasClass('email'))
		jQuery('select[name="validate-as"] option[value="email"]').attr('selected','selected');
	else if(current_field.hasClass('phone_number'))
		jQuery('select[name="validate-as"] option[value="phone_number"]').attr('selected','selected');
	else if(current_field.hasClass('url'))
		jQuery('select[name="validate-as"] option[value="url"]').attr('selected','selected');
	else if(current_field.hasClass('numbers_only'))
		jQuery('select[name="validate-as"] option[value="numbers_only"]').attr('selected','selected');
	else if(current_field.hasClass('text_only'))
		jQuery('select[name="validate-as"] option[value="text_only"]').attr('selected','selected');
	else
		jQuery('select[name="validate-as"] option:first').attr('selected','selected');	
		
}
function get_animation_settings(){
	//GET ANIMATION DELAY
	var animation_delay = current_field.attr('data-wow-delay');
	if(animation_delay)
		{
		animation_delay = animation_delay.replace('s','')
		jQuery('#animation_delay').val(animation_delay)
		}
	else
		jQuery('#animation_delay').val('')
	
	//GET ANIMATION DURATION
	var animation_duration = current_field.attr('data-wow-duration');
			if(animation_duration)
				{
				animation_duration = animation_duration.replace('s','')
				jQuery('#animation_duration').val(animation_duration)
				}
			else
				jQuery('#animation_duration').val('')
	
	//GET ANIMATION EFFECT
	jQuery('#field_animation option[value="no_animation"]').prop('selected',true);
   	jQuery('#field_animation option').each(
		function()
			{
			if(current_field.hasClass(jQuery(this).text()))
				jQuery(this).attr('selected','selected');
			}
		);
}

function set_icon(set_class,icon_pos, icon_trigger, icon_target, icon_reverse_target, remove_icon){
	
		if(remove_icon == true)
			{
			jQuery('div.field-settings-column').find('.current_'+ icon_trigger + ' i').attr('class','')
			jQuery('div.field-settings-column').find('.current_'+ icon_trigger + ' i').text('Select Icon')
			jQuery('div.field-settings-column #set_'+icon_trigger).val('');
			
			if(icon_pos=='before')
				{
				jQuery(".pre-icon-text-color").trigger("colorpickersliders.updateColor",'#555555');
				jQuery(".pre-icon-bg-color").trigger("colorpickersliders.updateColor",'#eeeeee');
				jQuery(".pre-icon-border-color").trigger("colorpickersliders.updateColor",'#cccccc');
				}
			else
				{
				jQuery(".post-icon-text-color").trigger("colorpickersliders.updateColor",'#555555');
				jQuery(".post-icon-bg-color").trigger("colorpickersliders.updateColor",'#eeeeee');
				jQuery(".post-icon-border-color").trigger("colorpickersliders.updateColor",'#cccccc');
				}
			
			current_field.find('.'+icon_target).remove();
			
			if(input_element.parent().hasClass('input-group') && !input_element.parent().find('.' + icon_reverse_target).attr('class'))
				{
				input_element.unwrap()
				}
			}
		else
			{
			if(!input_element.parent().hasClass('input-group'))
				input_element.wrap('<div class="input-group"></div>');
			if(!input_element.parent().find('.'+icon_target).attr('class'))	
				{							
				if(icon_pos=='before')
					input_element.before('<span class="input-group-addon '+ icon_target +'"><span class=""></span></span>');
				else
					input_element.after('<span class="input-group-addon '+ icon_target +'"><span class=""></span></span>');
				}
			jQuery('div.field-settings-column').find('.current_'+ icon_trigger + ' i').attr('class',set_class)
			jQuery('div.field-settings-column').find('.current_'+ icon_trigger + ' i').text('')
			current_field.find('.'+ icon_target +' span').attr('class',set_class);
			jQuery('div.field-settings-column #set_'+ icon_trigger).val(set_class)
			}	
	
}


function set_label_width(count){
	
	label_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
	label_container.addClass('col-sm-'+	count);
	input_container.removeClass('col-sm-1').removeClass('col-sm-2').removeClass('col-sm-3').removeClass('col-sm-4').removeClass('col-sm-5').removeClass('col-sm-6').removeClass('col-sm-7').removeClass('col-sm-8').removeClass('col-sm-9').removeClass('col-sm-10').removeClass('col-sm-11').removeClass('col-sm-12')
	if(parseInt(count)==12)
		{
		input_container.addClass('col-sm-12');
		jQuery('div.field-settings-column .width_indicator.left input').val('12');
		jQuery('div.field-settings-column .width_indicator.right input').val('12');
		}
	else
		input_container.addClass('col-sm-'+	parseInt((12-parseInt(count))));	
}

function change_color(trigger,target,css){

	jQuery("." + trigger).ColorPickerSliders(
		{
		 placement: 'bottom',
		 hsvpanel: true,
		 previewformat: 'hex',
		 color: '#FFFFFF',
		 onchange: function(container, color)
			{
			current_field.find(target).css(css,'rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')')
			
			if(current_field.hasClass('slider'))
				{
				//console.log(trigger);
				//SET SLIDER HANDEL COLORS
					if(trigger=='set-slider-handel-text-color')
						current_field.find('.slider').attr('data-text-color','rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')');
					if(trigger=='set-slider-handel-bg-color')
						current_field.find('.slider').attr('data-handel-background-color','rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')');
					if(trigger=='set-slider-handel-border-color')
						current_field.find('.slider').attr('data-handel-border-color','rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')');
				//SET SLIDER COLORS
					if(trigger=='set-slider-bg-color')
						current_field.find('.slider').attr('data-background-color','rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')');
					if(trigger=='set-slider-fill-color')
						current_field.find('.slider').attr('data-fill-color','rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')');
					if(trigger=='set-slider-border-color')
						current_field.find('.slider').attr('data-slider-border-color','rgba('+color.rgba.r+','+color.rgba.g+','+color.rgba.b+','+color.rgba.a+')');
				}
			}
		}
	);
}





function set_biu_style(trigger, target, style){
	jQuery('div.field-settings-column '+ trigger +'-'+style).click(
	function()
		{
		if(current_field.find(target).hasClass('style_'+style))
			{
			current_field.find(target).removeClass('style_'+style);
			jQuery(this).removeClass('active');
			}
		else
			{
			current_field.find(target).addClass('style_'+style);
			jQuery(this).addClass('active');
			}
		}
	);
}

function get_biu_style(the_field, trigger, target,  style){
	jQuery('div.field-settings-column').find(trigger + '-' + style).removeClass('active');
	if(the_field.find(target).hasClass('style_'+style))
		jQuery('div.field-settings-column').find(trigger + '-' + style).addClass('active');
}

function show_current_field_type(the_obj){
	
	jQuery('.fields-column .show_field_type').removeClass('show_field_type');
	
	var obj = the_obj.clone();
	
	if(obj.hasClass('form_fields'))
		jQuery('.field-category.form_fields').trigger('click');
	if(obj.hasClass('preset_fields'))
		jQuery('.field-category.preset_fields').trigger('click');
	if(obj.hasClass('upload_fields'))
		jQuery('.field-category.upload_fields').trigger('click');
	if(obj.hasClass('other-elements'))
		jQuery('.field-category.other-elements').trigger('click');
		
	obj.removeClass('form_fields').removeClass('preset_fields').removeClass('upload_fields').removeClass('other-elements').removeClass('field').removeClass('form_field').removeClass('form_fields').removeClass('ui-draggable').removeClass('ui-draggable-handle').removeClass('admin_animated').removeClass('flipInXadmin').removeClass('ui-draggable-handle').removeClass('currently_editing').removeClass('dropped').removeClass('show_field_type')
	jQuery('.fields-column .'+obj.attr('class')).addClass('show_field_type');
	//console.log(obj.attr('class'));
}