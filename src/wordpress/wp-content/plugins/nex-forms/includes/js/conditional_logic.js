// JavaScript Document
jQuery(document).ready(
function()
	{
	
	reset_rule_complexity();
	set_c_logic_fields();
	jQuery(document).on('change', '.cl_field, select[name="cla_field"]', function()
		{
		jQuery(this).attr('data-selected',jQuery(this).val());
		}
	);
	
	
	jQuery(document).on('change', 'input[name="adv_cl"]', function()
		{
		if(jQuery(this).prop('checked')==true)
			{
			jQuery('.conditional_logic').removeClass('simple_view').addClass('advanced_view');	
			}
		else
			{
			jQuery('.conditional_logic').addClass('simple_view').removeClass('advanced_view');
			
			var count1 = 0;
			var count2 = 0;
			
			reset_rule_complexity();
				
			}
		}
	);
	jQuery(document).on('click', '.add_new_rule', function()
		{
		var new_rule = jQuery('.conditional_logic_clonables .new_rule').clone();
		jQuery('.set_rules').append(new_rule);
		var radio_name =  Math.round(Math.random()*9999);
		
		new_rule.find('input[type="radio"]').attr('name',radio_name);

		jQuery('.con-logic-column .inner').animate(
					{
					scrollTop:100000
					},0
				);
		count_nf_conditions();
		set_c_logic_fields()
		}
	);

	jQuery(document).on('click', '.add_condition', function()
		{
		var new_condition = jQuery('.conditional_logic_clonables .set_rule_conditions').clone();
		
		new_condition.removeClass('set_rule_conditions').addClass('the_rule_conditions');
		
		jQuery(this).parent().find('.get_rule_conditions').append(new_condition);
		}
	);

	
	jQuery(document).on('click', '.add_action', function()
		{
		var new_condition = jQuery('.conditional_logic_clonables .set_rule_actions').clone();
		new_condition.removeClass('set_rule_actions').addClass('the_rule_actions');
		jQuery(this).parent().find('.get_rule_actions').append(new_condition);
		}
	);

	jQuery(document).on('click', '.delete_action, .delete_condition', function()
		{
		jQuery(this).parent().remove();
		reset_rule_complexity();
		}
	);
	jQuery(document).on('click', '.delete_rule, .delete_simple_rule', function()
		{
		jQuery(this).closest('.new_rule').remove();
		reset_rule_complexity();
		}
	);
	
	
	
	
	
	
	
});

function reset_rule_complexity(){
	jQuery('.set_rules .new_rule').each(
				function()
					{
					var count1 = jQuery(this).find('.delete_condition').size();
					var count2 = jQuery(this).find('.delete_action').size();
					
					if(count1>1 || count2>1)
						jQuery(this).addClass('advanced_view');
					else
						jQuery(this).removeClass('advanced_view');
					}
				);
	count_nf_conditions();
}

function count_nf_conditions(){
	jQuery('.set_rules .new_rule').each(
				function(index)
					{
					jQuery(this).find('.rule_number').text(index+1)
					}
				);
	
}


function set_c_logic_fields(the_select){
	
					var set_current_fields_conditional_logic = '<option selected="selected" value="0">-- Field --</option>';
						var set_current_action_fields_conditional_logic ='';
						set_current_fields_conditional_logic += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								if(jQuery(this).closest('.form_field').hasClass('date'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="date" value="'+ jQuery(this).closest('.form_field').attr('id') +'**date##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('datetime'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="datetime" value="'+ jQuery(this).closest('.form_field').attr('id') +'**datetime##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('time'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="time"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**time##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('star-rating'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="stars"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**hidden##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="text"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**text##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
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
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="radio"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**radio##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
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
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="checkbox"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**checkbox##'+ jQuery(this).attr('name')  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="select"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**select##'+ jQuery(this).attr('name')  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="textarea"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**textarea##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						
						set_current_fields_conditional_logic += '<optgroup label="File Uploaders">';
						jQuery('div.nex-forms-container div.form_field input[type="file"]').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="file"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**file##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="hidden"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**hidden##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Buttons">';
						jQuery('div.nex-forms-container div.form_field.submit-button').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).find('.the_input_element').text())  +'" data-field-type="button"  value="'+ jQuery(this).attr('id') +'**button##button">'+ jQuery(this).find('.the_input_element').text() +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Panels">';
						jQuery('div.nex-forms-container div.form_field.is_panel').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option  data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="panel" data-field-type="panel"   value="'+ jQuery(this).attr('id') +'**panel##panel">'+ short_str(jQuery(this).find('.panel-heading').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Headings">';
						jQuery('div.nex-forms-container div.form_field.heading').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option   data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="heading" data-field-type="heading"   value="'+ jQuery(this).attr('id') +'**heading##heading">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="HTML/Paragraphs">';
						jQuery('div.nex-forms-container div.form_field.html').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="html" data-field-type="html"  value="'+ jQuery(this).attr('id') +'**paragraph##html">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						jQuery('div.nex-forms-container div.form_field.paragraph').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="paragraph" data-field-type="paragraph" value="'+ jQuery(this).attr('id') +'**heading##html">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
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





function set_c_logic_fields(the_select){
	
					var set_current_fields_conditional_logic = '<option selected="selected" value="0">-- Field --</option>';
						var set_current_action_fields_conditional_logic ='';
						set_current_fields_conditional_logic += '<optgroup label="Text Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="text"]').each(
							function()
								{
								if(jQuery(this).closest('.form_field').hasClass('date'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="date" value="'+ jQuery(this).closest('.form_field').attr('id') +'**date##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('datetime'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="datetime" value="'+ jQuery(this).closest('.form_field').attr('id') +'**datetime##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('time'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="time"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**time##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else if(jQuery(this).closest('.form_field').hasClass('star-rating'))
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="stars"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**hidden##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								else
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="text"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**text##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
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
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="radio"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**radio##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								
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
									set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="checkbox"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**checkbox##'+ jQuery(this).attr('name')  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								new_check = old_check;
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Selects">';
						jQuery('div.nex-forms-container div.form_field select').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="select"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**select##'+ jQuery(this).attr('name')  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Text Areas">';
						jQuery('div.nex-forms-container div.form_field textarea').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="textarea"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**textarea##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						
						set_current_fields_conditional_logic += '<optgroup label="File Uploaders">';
						jQuery('div.nex-forms-container div.form_field input[type="file"]').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="file"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**file##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_fields_conditional_logic += '<optgroup label="Hidden Fields">';
						jQuery('div.nex-forms-container div.form_field input[type="hidden"]').each(
							function()
								{
								set_current_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).attr('name'))  +'" data-field-type="hidden"  value="'+ jQuery(this).closest('.form_field').attr('id') +'**hidden##'+ format_illegal_chars(jQuery(this).attr('name'))  +'">'+ unformat_name(jQuery(this).attr('name')) +'</option>';
								}
							);	
						set_current_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Buttons">';
						jQuery('div.nex-forms-container div.form_field.submit-button').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="'+ format_illegal_chars(jQuery(this).find('.the_input_element').text())  +'" data-field-type="button"  value="'+ jQuery(this).attr('id') +'**button##button">'+ jQuery(this).find('.the_input_element').text() +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Panels">';
						jQuery('div.nex-forms-container div.form_field.is_panel').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option  data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="panel" data-field-type="panel"   value="'+ jQuery(this).attr('id') +'**panel##panel">'+ short_str(jQuery(this).find('.panel-heading').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="Headings">';
						jQuery('div.nex-forms-container div.form_field.heading').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option   data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="heading" data-field-type="heading"   value="'+ jQuery(this).attr('id') +'**heading##heading">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						set_current_action_fields_conditional_logic += '</optgroup>';
						
						set_current_action_fields_conditional_logic += '<optgroup label="HTML/Paragraphs">';
						jQuery('div.nex-forms-container div.form_field.html').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="html" data-field-type="html"  value="'+ jQuery(this).attr('id') +'**paragraph##html">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
								}
							);	
						jQuery('div.nex-forms-container div.form_field.paragraph').each(
							function()
								{
								set_current_action_fields_conditional_logic += '<option data-field-id="'+ jQuery(this).closest('.form_field').attr('id') +'" data-field-name="paragraph" data-field-type="paragraph" value="'+ jQuery(this).attr('id') +'**heading##html">'+ short_str(jQuery(this).find('.the_input_element').text()) +'</option>';
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