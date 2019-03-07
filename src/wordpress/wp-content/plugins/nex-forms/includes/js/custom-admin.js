function nf_layer_modal_windows(obj){
	var modal_layer = 1020;
	 jQuery('div.modal').removeClass('active_window');
	 
	 var total_opened_windows = jQuery('div.modal.modal_open').size();
	 jQuery('div.modal.modal_open').each(
	 	function(index)
			{
			jQuery(this).css('z-index',(modal_layer+index));
			}
		);
	 
	 jQuery(obj).css('z-index',(modal_layer+total_opened_windows+1000)).addClass('active_window');
	 jQuery('.task-window').removeClass('active')
	 jQuery('.task-window.'+obj.attr('data-task-target')).addClass('active');
	 	
}
jQuery(document).ready(
function()
	{
		
	jQuery('div.modal').on('shown.bs.modal', function () {
	 jQuery(this).addClass('modal_open');
	 jQuery(this).attr('backdrop','static');
	  nf_layer_modal_windows(jQuery(this));
	})
	
	jQuery('div.modal').on('hide.bs.modal', function () {
	 jQuery(this).removeClass('modal_open');
	})
	
	jQuery('div.modal').on('click', function () {
	 
	 nf_layer_modal_windows(jQuery(this));
	 
	 
	});
		
	jQuery('div.modal').draggable(
		{
		drag: function( event, ui ) {   },
		handle: ".modal-header"
		
		}
	)
	//jQuery('div.modal .modal-dialog').resizable({});
	
	jQuery('.save_custom_layout').click(
		function()
			{
			var layout = {
						layout_name: jQuery('.custom_layout_name').val(),
						old_layout_name: jQuery('.old_custom_layout_name').text(),
						fields_col:
							{
							top: 	jQuery('.fields-column').css('top'),
							left:	jQuery('.fields-column').css('left'),
							width: 	jQuery('.fields-column').css('width'),
							height: jQuery('.fields-column').css('height')
							},
						form_canvas_col:
							{
							top: 	jQuery('.form-canvas-column').css('top'),
							left:	jQuery('.form-canvas-column').css('left'),
							width: 	jQuery('.form-canvas-column').css('width'),
							height: jQuery('.form-canvas-column').css('height')
							},
						field_settings_col:
							{
							top: 	jQuery('.field-settings-column').css('top'),
							left:	jQuery('.field-settings-column').css('left'),
							width: 	jQuery('.field-settings-column').css('width'),
							height: jQuery('.field-settings-column').css('height')
							}
							
						}
				
			var data =
				{
				action	 						: 'nf_create_custom_layout',
				set_layout						: layout,
				};
				
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					var old_layout_name = jQuery('.old_custom_layout_name').text();
					jQuery('.old_custom_layout_name').text(layout.layout_name);
					
					setCookie('nex_forms_admin_layout',layout.layout_name,'365');
					var exist = false;
					jQuery('a.enable_custom_layout').each(
						function()
							{
							var the_layout = jQuery(this);
							if(the_layout.attr('data-layout')==old_layout_name)
								{
								the_layout.html('<i class="fa fa-check got_add_on"></i>'+jQuery('.custom_layout_name').val());
								exist = true;
								popup_user_alert('Layout: ' + jQuery('.custom_layout_name').val() + ' Saved');
								}
							}
						);
					if(!exist)
						{
						jQuery('span.the_custom_layouts').append('<li><a class="enable_custom_layout last_added" data-layout="' + layout.layout_name + '" data-icon="fa-check"><i class="fa fa-check got_add_on"></i> ' + layout.layout_name + '</a><div class="delete_custom_layout"><span class="fa fa-close"></span></div><div class="edit_custom_layout"><span class="fa fa-edit"></span></div> </li>');	
						jQuery('.last_added').trigger('click');
						popup_user_alert('New Layout Created');
						}
					destroy_custom_layout_options();
					}
				);
		
			}
		);
	
	jQuery('.delete_custom_layout').click(
		function()
			{
			var data =
				{
				action	 						: 'nf_delete_custom_layout',
				layout_name						: jQuery(this).closest('li').find('a').attr('data-layout'),
				};
			var the_layout = jQuery(this);
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					the_layout.closest('li').remove();
					popup_user_alert('Layout Deleted');
					}
				);
		
			}
		);
	
	jQuery(document).on('click', '.edit_custom_layout', 
		function()
			{
			jQuery(this).closest('li').find('a').trigger('click');
			jQuery('.old_custom_layout_name').text(jQuery(this).closest('li').find('a').attr('data-layout'));
			jQuery('.custom_layout_name').val(jQuery(this).closest('li').find('a').attr('data-layout'));
			setup_custom_admin_layout();
			jQuery('.custom_layout_options').slideDown();
			}
		);
	
	jQuery(document).on('click', '.enable_custom_layout', 
		function()
			{
			
			jQuery('a.enable_custom_layout').each(
			function()
				{
				var the_layout = jQuery(this);
				the_layout.find('i').attr('class','fa '+the_layout.attr('data-icon'));
				}
			);
			jQuery(this).find('i').attr('class', 'fa fa-check got_add_on');	
			var data =
				{
				action	 						: 'nf_load_custom_layout',
				set_layout						: jQuery(this).attr('data-layout')
				};
			setCookie('nex_forms_admin_layout',jQuery(this).attr('data-layout'),'365');	
			jQuery('.fields-column').removeClass('is_icon');
			if(data.set_layout=='default')
				{
				jQuery('.fields-column').attr('style','');
				jQuery('.fields-column .inner').attr('style','');
				jQuery('.form-canvas-column').attr('style','');
				jQuery('.right_hand_col').attr('style','');
				}
			else if(data.set_layout=='desktop')
				{
				jQuery('.fields-column').css('top','0px');
				jQuery('.fields-column').css('left','0px');
				jQuery('.fields-column').css('width','271px');
				jQuery('.fields-column .inner').css('height','566px');
				jQuery('.fields-column').css('height','606px');
				
				jQuery('.form-canvas-column').css('top','0px');
				jQuery('.form-canvas-column').css('left','281px');
				jQuery('.form-canvas-column').css('width','768px');
				jQuery('.form-canvas-column').css('height','80vh');
				
				jQuery('.right_hand_col').css('top','0px');
				jQuery('.right_hand_col').css('left','1058px');
				jQuery('.right_hand_col').css('width','592px');
				jQuery('.right_hand_col').css('height','515px');
				
				jQuery('.right_hand_col.field-settings-column .inner').css('height','474px');
				jQuery('.right_hand_col.con-logic-column .inner').css('height','417px');
				jQuery('.right_hand_col.paypal-column .inner').css('height','407px');
				}
			else if(data.set_layout=='laptop')
				{
				jQuery('.fields-column').css('top','0px');
				jQuery('.fields-column').css('left','5px');
				jQuery('.fields-column').css('width','994px');
				jQuery('.fields-column').css('height','107px');
				jQuery('.fields-column .inner').css('height','82px');
				
				jQuery('.form-canvas-column').css('top','115px');
				jQuery('.form-canvas-column').css('left','5px');
				jQuery('.form-canvas-column').css('width','393px');
				jQuery('.form-canvas-column').css('height','440px');
				
				jQuery('.right_hand_col').css('top','115px');
				jQuery('.right_hand_col').css('left','407px');
				jQuery('.right_hand_col').css('width','583px');
				jQuery('.right_hand_col').css('height','440px');
				
				jQuery('.right_hand_col.field-settings-column .inner').css('height','398px');
				jQuery('.right_hand_col.con-logic-column .inner').css('height','367px');
				jQuery('.right_hand_col.paypal-column .inner').css('height','357px');
				}
			else if(data.set_layout=='tablet')
				{
				jQuery('.fields-column').addClass('is_icon');
				jQuery('.fields-column').css('top','0px');
				jQuery('.fields-column').css('left','0px');
				jQuery('.fields-column').css('width','65px');
				jQuery('.fields-column').css('height','474px');
				
				jQuery('.form-canvas-column').css('top','0px');
				jQuery('.form-canvas-column').css('left','73px');
				jQuery('.form-canvas-column').css('width','318px');
				jQuery('.form-canvas-column').css('height','474px');
				
				jQuery('.right_hand_col').css('top','0px');
				jQuery('.right_hand_col').css('left','416px');
				jQuery('.right_hand_col').css('width','578px');
				jQuery('.right_hand_col').css('height','474px');
				
				jQuery('.right_hand_col.field-settings-column .inner').css('height','429px');
				jQuery('.right_hand_col.con-logic-column .inner').css('height','377px');
				jQuery('.right_hand_col.paypal-column .inner').css('height','367px');
				}
			else
				{	
				jQuery.post
					(
					ajaxurl, data, function(response)
						{
						
						jQuery('.current_layout_attr').html(response);
						
						jQuery('.fields-column').css('top',jQuery('.current_layout_attr .fields_col .top').text());
						jQuery('.fields-column').css('left',jQuery('.current_layout_attr .fields_col .left').text());
						jQuery('.fields-column').css('width',jQuery('.current_layout_attr .fields_col .width').text());
						jQuery('.fields-column').css('height',jQuery('.current_layout_attr .fields_col .height').text());
						
						jQuery('.form-canvas-column').css('top',jQuery('.current_layout_attr .form_canvas_col .top').text());
						jQuery('.form-canvas-column').css('left',jQuery('.current_layout_attr .form_canvas_col .left').text());
						jQuery('.form-canvas-column').css('width',jQuery('.current_layout_attr .form_canvas_col .width').text());
						jQuery('.form-canvas-column').css('height',jQuery('.current_layout_attr .form_canvas_col .height').text());
						
						jQuery('.right_hand_col').css('top',jQuery('.current_layout_attr .field_settings_col .top').text());
						jQuery('.right_hand_col').css('left',jQuery('.current_layout_attr .field_settings_col .left').text());
						jQuery('.right_hand_col').css('width',jQuery('.current_layout_attr .field_settings_col .width').text());
						jQuery('.right_hand_col').css('height',jQuery('.current_layout_attr .field_settings_col .height').text());
						
						var right_hand_col_width = jQuery('.right_hand_col').css('height');
						right_hand_col_width = right_hand_col_width.replace('px','');
						right_hand_col_width = parseInt(right_hand_col_width);
						
						jQuery('.right_hand_col.field-settings-column .inner').css('height', (right_hand_col_width - 40) + 'px');
						jQuery('.right_hand_col.con-logic-column .inner').css('height',(right_hand_col_width - 70) + 'px');
						
						var form_col_width = jQuery('.fields-column').css('width');
						
						form_col_width = form_col_width.replace('px','');
						form_col_width = parseInt(form_col_width);
						
						if(form_col_width<250)
							jQuery('.fields-column').addClass('is_icon')
						else
							jQuery('.fields-column').removeClass('is_icon')
							
						//if(form_col_width>410)
						//	jQuery('.fields-column').addClass('is_icon')
						}
					);
				}
			}
		);
	
	jQuery('a.enable_custom_layout').each(
		function()
			{
			var the_layout = jQuery(this);
			if(jQuery(this).attr('data-layout')==getCookie('nex_forms_admin_layout'))
				setTimeout(function(){the_layout.trigger('click')},100);
			}
		);
	
	
	jQuery('a.create_custom_layout').click(
		function()
			{
			jQuery('.old_custom_layout_name').text('');
			jQuery('.custom_layout_name').val('');
			setup_custom_admin_layout();
			}
		);
	
	jQuery('.cancel_custom_layout').click(
		function()
			{
			destroy_custom_layout_options();
			}
		);
	
	//destoy_custom_layout_options();
	}
);

function setup_custom_admin_layout(){
	jQuery('div.fields-column, div.form-canvas-column').draggable(
		{
		drag: function( event, ui ) {  /*ui.helper.addClass('moving');*/ },//ui.helper.addClass('moving');
		containment: "div.outer_container", 
		}
	)
	
	jQuery('.field-settings-column').draggable(
		{
		drag: function( event, ui ) { 
			 jQuery('.con-logic-column').css('left',ui.position.left);
			 jQuery('.con-logic-column').css('top',ui.position.top);
		
		},//ui.helper.addClass('moving');
		containment: "div.outer_container", 
		}
	)
	
	jQuery('.con-logic-column').draggable(
		{
		drag: function( event, ui ) { 
			 jQuery('.field-settings-column').css('left',ui.position.left);
			 jQuery('.field-settings-column').css('top',ui.position.top);
		
		},//ui.helper.addClass('moving');
		containment: "div.outer_container", 
		}
	)
	
	
	
	
	jQuery('div.form-canvas-column').resizable({
		containment: "div.outer_container",
	});
	
	jQuery('.field-settings-column').resizable({
		
		resize   		: function(event, ui){
		
			jQuery('.con-logic-column .inner').css('height',ui.size.height-75)
			jQuery('.field-settings-column .inner').css('height',ui.size.height-42)
			jQuery('.paypal-column .inner').css('height',ui.size.height-49)
		},
		alsoResize: 'div.con-logic-column, div.paypal-column',
		containment: "div.outer_container",
	});
	
	jQuery('.con-logic-column').resizable({
		
		resize   		: function(event, ui){
		
			jQuery('.con-logic-column .inner').css('height',ui.size.height-75)
			jQuery('.field-settings-column .inner').css('height',ui.size.height-42)
			jQuery('.paypal-column .inner').css('height',ui.size.height-49)
		},
		alsoResize: 'div.field-settings-column, div.paypal-column',
		containment: "div.outer_container",
	});
	
	jQuery('.paypal-column').resizable({
		
		resize   		: function(event, ui){
		
			jQuery('.con-logic-column .inner').css('height',ui.size.height-75)
			jQuery('.field-settings-column .inner').css('height',ui.size.height-42)
			jQuery('.paypal-column .inner').css('height',ui.size.height-49)
		},
		alsoResize: 'div.field-settings-column, div.con-logic-column',
		containment: "div.outer_container",
	});
	
	/*jQuery('div.form-name-col').resizable({
		containment: "div.outer_container",
	});*/
	
	jQuery('div.fields-column').resizable({
		containment: "div.outer_container", 
		resize   		: function(event, ui){
		
			jQuery('div.fields-column .inner').css('height',ui.size.height-40)
			console.log(jQuery('div.fields-column .inner').outerHeight())
			if(ui.size.width<250)
				{
				
				if(!jQuery('.fields-column').hasClass('is_icon'))
					jQuery('.fields-column').addClass('is_icon');
				}
			else
				{
					jQuery('.fields-column').removeClass('is_icon');	
				}
			
			
			
			},
	});
	
	
	jQuery('div.outer_container').droppable(
		{
		drop   		: function(event, ui){	},
		over		: function(event, ui) {},
		out         : function(){},	  
		tolerance 	: 'fit',
		helper 		: 'clone'	,
		accept      : '.form_field'
	})
		
	jQuery('.custom_layout_options').slideDown();
	jQuery('#nex-forms').addClass('customizing_admin_layout');
}

function destroy_custom_layout_options(){
	
	jQuery('div.fields-column, div.form-canvas-column, .field-settings-column, .con-logic-column').draggable('destroy')
	jQuery('div.fields-column, div.form-canvas-column, .field-settings-column, .con-logic-column').resizable('destroy');
	jQuery('div.outer_container').droppable('destroy');
	jQuery('.custom_layout_options').slideUp();
	jQuery('#nex-forms').removeClass('customizing_admin_layout');
}


