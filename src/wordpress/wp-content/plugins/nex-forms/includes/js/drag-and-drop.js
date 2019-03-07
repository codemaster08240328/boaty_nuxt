jQuery(document).ready(
function()
	{
	create_droppable(jQuery('div.nex-forms-container'));
	jQuery(document).on('click', '.draggable_object', 
		function()
			{
			nf_form_modified('drop');
			var animation_class = 'flipInXadmin';
			var clone_element = jQuery(this).closest('.form_field ').clone();
			jQuery('div.nex-forms-container').append(clone_element);
			clone_element.addClass('admin_animated').addClass(animation_class);	
			
			setTimeout(function(){ clone_element.removeClass('admin_animated').removeClass(animation_class) },1000);
			
			setup_form_element(clone_element);
			var offset = clone_element.offset();
			setTimeout(function(){jQuery(".nex-forms-container").animate(
					{
					scrollTop:offset.top+200
					},200
				);
			},200);
			if(clone_element.hasClass('step'))
				{
				nf_count_multi_steps();
				//jQuery('select[name="skip_to_step"] option[value="0"]').prop('selected','selected');
				/*var total_steps = jQuery('select[name="skip_to_step"] option').size()
				jQuery('select[name="skip_to_step"] option[value="'+ (total_steps+2) +'"]').trigger('click');*/
				jQuery('select[name="skip_to_step"]').trigger('change');
				}
			}
		);
	});

function reset_zindex(obj){
	if(obj)
	obj.attr('style','');
}
Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};	
function setup_form_element(obj){
	jQuery('div.nex-forms-container').find('div.draggable_object').hide();
	obj.find('div.form_object').show();
	jQuery('div.nex-forms-container').find('div.form_field').removeClass('field');
	
	obj.attr('style','visibility:visible;');
	obj.removeClass('field');
	obj.css('display','block');
	
	
	if(obj.hasClass('md-slider'))
		{
		obj.find('input').ionRangeSlider({
				type: "double",
				grid: true,
				min: -1000,
				max: 1000,
				from: -500,
				to: 500,
				prefix: "$",
				postfix: " test"
			});
		}
	
	if(obj.hasClass('md-datepicker'))
		{
			obj.find('input').pickadate({
			selectMonths: true, // Creates a dropdown to control month
			selectYears: 15, // Creates a dropdown of 15 years to control year,
			today: 'Today',
			clear: 'Clear',
			close: 'Ok',
			closeOnSelect: false // Close upon selecting a date,
		  });
		}
   if(obj.hasClass('md-time-picker'))
		{
			   obj.find('input').pickatime({
				default: 'now', // Set default time: 'now', '1:30AM', '16:30'
				fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
				twelvehour: false, // Use AM/PM or 24-hour format
				donetext: 'OK', // text for done-button
				cleartext: 'Clear', // text for clear-button
				canceltext: 'Cancel', // Text for cancel-button
				autoclose: false, // automatic close timepicker
				ampmclickable: true, // make AM PM clickable
				aftershow: function(){} //Function for after opening timepicker
			  });    
		}
	
	if(obj.hasClass('digital-signature'))
		{
		obj.find('.js-signature').jqSignature();
		}

	
	if(obj.hasClass('md-select'))
		{
		obj.find('select').material_select();
		}

	if(obj.hasClass('text') || obj.hasClass('textarea'))
		{
		if(obj.find('.the_input_element').attr('data-value'))
				obj.find('.the_input_element').val(obj.find('.the_input_element').attr('data-value'));
		}
	if(obj.hasClass('paragraph') || obj.hasClass('heading'))
		{
		if(!obj.find('input.set_math_result').attr('name'))
			obj.find('.the_input_element').parent().append('<input type="hidden" class="set_math_result" value="0" name="math_result">');
		}
					
	if(obj.hasClass('grid'))
		{
		var panel = obj.find('.panel-body');
		create_droppable(panel)
		}
	if(obj.hasClass('datetime'))
		{
		obj.find('#datetimepicker').datetimepicker( 
				{ 
				//pickTime:false,
				format: (obj.find('#datetimepicker').attr('data-format')) ? obj.find('#datetimepicker').attr('data-format') : 'MM/DD/YYYY hh:mm A',
				locale: (obj.find('#datetimepicker').attr('data-language')) ? obj.find('#datetimepicker').attr('data-language') : 'en'
				} 
			);	
		}
	if(obj.hasClass('date'))
		{
		obj.find('#datetimepicker').datetimepicker( 
				{ 
				//pickTime:false,
				format: (obj.find('#datetimepicker').attr('data-format')) ? obj.find('#datetimepicker').attr('data-format') : 'MM/DD/YYYY',
				locale: (obj.find('#datetimepicker').attr('data-language')) ? obj.find('#datetimepicker').attr('data-language') : 'en'
				} 
			);	
		}	
	if(obj.hasClass('time'))
		{
		obj.find('#datetimepicker').datetimepicker( 
				{ 
				//pickTime:false,
				format: (obj.find('#datetimepicker').attr('data-format')) ? obj.find('#datetimepicker').attr('data-format') : 'hh:mm A',
				locale:(obj.find('#datetimepicker').attr('data-language')) ? obj.find('#datetimepicker').attr('data-language') : 'en'
				} 
			);	
		}
		
	if(obj.hasClass('touch_spinner'))
		{
		var the_spinner = obj.find("#spinner");
		the_spinner.TouchSpin({
			initval: parseInt(the_spinner.attr('data-starting-value')),
			min:  parseInt(the_spinner.attr('data-minimum')),
			max:  parseInt(the_spinner.attr('data-maximum')),
			step:  parseInt(the_spinner.attr('data-step')),
			decimals:  parseInt(the_spinner.attr('data-decimals')),
			boostat: 5,
			maxboostedstep: 10,
			postfix: (the_spinner.attr('data-postfix-icon')) ? '<span class="'+ the_spinner.attr('data-postfix-icon') +' '+ the_spinner.attr('data-postfix-class') +'">' + the_spinner.attr('data-postfix-text') + '</span>' : '',
			prefix: (the_spinner.attr('data-prefix-icon')) ? '<span class="'+ the_spinner.attr('data-prefix-icon') +' '+ the_spinner.attr('data-prefix-class') +'">' + the_spinner.attr('data-prefix-text') + '</span>' : '',
			buttondown_class:  'btn ' + the_spinner.attr('data-down-class'),
			buttonup_class: 'btn ' + the_spinner.attr('data-up-class')
		});
		obj.find(".bootstrap-touchspin .bootstrap-touchspin-down").html('<span class="icon '+   the_spinner.attr('data-down-icon') +'"></span>');
		obj.find(".bootstrap-touchspin .bootstrap-touchspin-up").html('<span class="icon '+   the_spinner.attr('data-up-icon') +'"></span>');
		}
	if(obj.hasClass('color_pallet'))
		{
		
		obj.find('#colorpalette').colorPalette().on('selectColor', function(e) {
		obj.find('#selected-color').val(e.color);
		obj.find('#selected-color').trigger('change');
		obj.find('.input-group-addon').css('background',e.color);
		});	
		}
	
	if(obj.hasClass('slider'))
		{
		var count_text = obj.find( "#slider" ).attr('data-starting-value');
		var the_slider = obj.find( "#slider" )
		var set_min = the_slider.attr('data-min-value');
		var set_max = the_slider.attr('data-max-value')
		var set_start = the_slider.attr('data-starting-value');
		var set_step = the_slider.attr('data-step-value')

		obj.find( "#slider" ).slider({
				range: "min",
				min: parseInt(set_min),
				max: parseInt(set_max),
				value: parseInt(set_start),
				step: parseInt(set_step),
				slide: function( event, ui ) {	
					count_text = '<span class="count-text">' + the_slider.attr('data-count-text').replace('{x}',parseInt(ui.value).format(0)) + '</span>';	
					the_slider.find( '.ui-slider-handle' ).html( '<span id="icon" class="'+ the_slider.attr('data-dragicon') +'"></span> '+ count_text).addClass(the_slider.attr('data-dragicon-class')).removeClass('ui-state-default');
					obj.find( 'input' ).val(ui.value);
					obj.find( 'input' ).trigger('change');
				},
				create: function( event, ui ) {	
					count_text = '<span class="count-text">'+ the_slider.attr('data-count-text').replace('{x}',((set_start) ? set_start : set_min)) +'</span>';	
					the_slider.find( '.ui-slider-handle' ).html( '<span id="icon" class="'+ the_slider.attr('data-dragicon') +'"></span> '+ count_text).addClass(the_slider.attr('data-dragicon-class')).removeClass('ui-state-default');
					
				}
				
			});
			//the_slider.find( '.ui-slider-handle' ).html('<span id="icon" class="'+ the_slider.attr('data-dragicon') +'"></span>' + count_text);
			
			//Slider text color
			the_slider.find('.ui-slider-handle').css('color',the_slider.attr('data-text-color'));
			//Handel border color
			the_slider.find('.ui-slider-handle').css('border-color',the_slider.attr('data-handel-border-color'));
			//Handel Background color
			the_slider.find('.ui-slider-handle').css('background-color',the_slider.attr('data-handel-background-color'));
			//Slider border color
			the_slider.find('.ui-widget-content').css('border-color',the_slider.attr('data-slider-border-color'));
			//Slider background color
			//Slider fill color
			the_slider.find('.ui-slider-range:first-child').css('background',the_slider.attr('data-fill-color'));
			the_slider.find('.ui-slider-range:last-child').css('background',the_slider.attr('data-background-color'));
			
			
			
			
		}			
	if(obj.hasClass('star-rating'))
		{
		obj.find('#star').raty({
		  size     : 24,
		  number   : parseInt(obj.find('#star').attr('data-total-stars')),
		  scoreName: format_illegal_chars(obj.find('.the_label').text()),
		  half: (obj.find('#star').attr('data-enable-half')=='false') ? false : true 
		});
		obj.find('#star input').addClass('the_input_element').addClass('hidden');
		obj.find('#star input').prop('type','text');
		}
		
		
	/*if(obj.hasClass('multi-select') || obj.hasClass('select'))
		{	
		var the_select = obj.find("#select");
		the_select.selectpicker();
		var font_family = (the_select.attr('data-font-family')) ? the_select.attr('data-font-family') : '';
		font_family = font_family.replace('sf','');
		font_family = font_family.replace('gf','');
		obj.find(".selectpicker").css('color', the_select.attr('data-text-color'))
		obj.find(".selectpicker a").css('color', the_select.attr('data-text-color'))
		obj.find(".selectpicker").removeClass('align_left').removeClass('align_right').removeClass('align_center')
		obj.find(".selectpicker").addClass(the_select.attr('data-text-alignment'))
		obj.find(".selectpicker").addClass(the_select.attr('data-input-size'))
		obj.find(".selectpicker").css('font-family',font_family);
		
		obj.find(".selectpicker").css('border-color', the_select.attr('data-border-color'));
		obj.find(".selectpicker").css('background', the_select.attr('data-background-color'))
		obj.find(".selectpicker").addClass('error_message');
		obj.find(".selectpicker").addClass('ui-state-default');
		obj.find(".selectpicker").attr('data-placement',the_select.attr('data-placement'));
		obj.find(".selectpicker").attr('data-error-class',the_select.attr('data-error-class'));
		obj.find(".selectpicker").attr('data-content',the_select.attr('data-content'));
		}*/
	if(obj.hasClass('nf-color-picker'))
		{
		obj.find('.colorpicker-component').colorpicker();
		}
	if(obj.hasClass('tags'))
		{	
		var the_tag_input = obj.find('input#tags');
		 the_tag_input.tagsinput( {maxTags: (the_tag_input.attr('data-max-tags')) ? the_tag_input.attr('data-max-tags') : '' });
		 
		obj.find('.bootstrap-tagsinput input').css('color',the_tag_input.attr('data-text-color'));
		obj.find('.bootstrap-tagsinput').css('border-color',the_tag_input.attr('data-border-color'));
		obj.find('.bootstrap-tagsinput').css('background-color',the_tag_input.attr('data-background-color'));
		obj.find('.bootstrap-tagsinput').addClass('error_message').addClass('the_input_element');
		obj.find(".bootstrap-tagsinput").attr('data-placement',the_tag_input.attr('data-placement'));
		obj.find(".bootstrap-tagsinput").attr('data-error-class',the_tag_input.attr('data-error-class'));
		obj.find(".bootstrap-tagsinput").attr('data-content',the_tag_input.attr('data-content'));
		}
		
		
	if(obj.hasClass('autocomplete'))
		{
		var items = obj.find('div.get_auto_complete_items').text();
		items = items.split('\n');
		obj.find("#autocomplete").autocomplete({
		source: items
		});	
		}	
	
	
	
	if(obj.hasClass('single-image-select-group'))
		{
		obj.find('input[type="radio"]').nexchecks();
		obj.find('input[type="radio"]').closest('label').find('.input-label').addClass('img-thumbnail');
		}
	
	if(obj.hasClass('multi-image-select-group'))
		{
		obj.find('input[type="checkbox"]').nexchecks();
		obj.find('input[type="checkbox"]').closest('label').find('.input-label').addClass('img-thumbnail');
		}
	if(obj.hasClass('radio-group'))
		{
		obj.find('input[type="radio"]').nexchecks()
		}
	if(obj.hasClass('check-group'))
		{
		obj.find('input[type="checkbox"]').nexchecks()
		}
	
	if(obj.hasClass('upload_fields'))
		{
		obj.find('.btn-file').removeClass('btn');	
		}
	
	if(obj.hasClass('grid-system'))
		obj.removeClass('ui-widget-content')
		
	/*setTimeout(
		function()
			{*/
			if(!obj.hasClass('dropped'))
				{
				var set_Id = '_' + Math.round(Math.random()*99999);
				obj.attr('id',set_Id);	
				//jQuery('#filters a:first').trigger('click').tab('show');
				obj.find('div.edit').trigger('click');
				obj.addClass('dropped');
				}/*
		},100
	);	*/
}

function create_droppable(obj){
	
	var the_droppable 	= obj;
	var the_draggable 	= jQuery('div.field-selection-wrapper .form_field');
	//Drag
   the_draggable.draggable(
		{
		drag: function( event, ui ) {  ui.helper.addClass('moving'); nf_form_modified('drag'); },//ui.helper.addClass('moving');
		stop: function( event, ui ) {  ui.helper.removeClass('moving'); setTimeout(function(){ jQuery('.reset_droppables').trigger('click');},300 ); },
		stack  : '.draggable',
		revert : 'invalid', 
		tolerance: 'intersect',
		connectToSortable:obj,
		snap:true,
		helper : 'clone',
		}
	);
	
	//Enable panel nesting -> still need to find a better way some day
	jQuery('.reset_droppables').live('click',
		function()
			{
			jQuery('.reset_droppables').removeClass('btn-success').addClass('btn-primary');
				
			if(jQuery(this).hasClass('btn-success'))
				{
				jQuery(this).removeClass('btn-success');
				jQuery(this).addClass('btn-primary');
				the_draggable.draggable("option", "connectToSortable",the_droppable);
				}
			else
				{
				jQuery(this).addClass('btn-success');
				jQuery(this).removeClass('btn-primary');
				the_draggable.draggable("option", "connectToSortable", jQuery(this).parent().parent().find('.panel-body'));
				}
			}
		);
	the_droppable.droppable(
		{
		drop   		: function(event, ui)
						{
						if(!ui.draggable.hasClass('dropped'))
						setup_form_element(ui.draggable);
						reset_zindex(ui.draggable);
						jQuery(this).removeClass('over');
						nf_form_modified('drop');
						//					
						},
		over        : function(){jQuery(this).addClass('over')},
		out         : function(){jQuery(this).removeClass('over')},	  
		tolerance 	: 'fit',
		helper 		: 'clone'	,
		accept      : '.form_field'
	}).sortable(
		{
		start : function(event, ui)
			{ 
			ui.helper.find('div.draggable_object').hide();
			ui.helper.find('div.form_object').show();
			/*ui.helper.find('div.field_settings').hide();*/
			ui.helper.removeClass('field');
			ui.helper.addClass('moving');
			}, 
		stop : function(event, ui){ 
		nf_reset_multi_steps();
		jQuery('.moving').removeClass('moving'); nf_form_modified('sort'); setTimeout(function(){  reset_zindex(ui.helper); jQuery('.reset_droppables').trigger('click');},300 ); },           
		placeholder: 'place-holder',
		forcePlaceholderSize : true,
		connectWith:'.panel-body'
		}
	);
}