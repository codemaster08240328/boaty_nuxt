// JavaScript Document
/* SET FIELD NAMES TO STANDARD FORMAT */

//IMPORT FORM
jQuery(document).ready(
function()
	{
	
		
	
	jQuery('.new_form_option').click(
			function()
				{
				jQuery('.wizard_step').hide();
				jQuery('.'+jQuery(this).attr('data-nex-step')).show();
				}
			);
		
	jQuery('#upload_form,#upload_form2').click(
			function()
				{
				jQuery('input[name="form_html"]').trigger('click');
				}
			);
		
		jQuery('input[name="form_html"]').change(
			function()
				{
				jQuery('#import_form').submit();
				jQuery('input[name="form_name"]').val('');
				jQuery('#nex-forms #form_update_id').text('');
				jQuery('.nex-forms-container').html('');
				jQuery('.open-form').removeClass('active');	
				jQuery('.center_panel').hide();
				}
		)
		
		jQuery('#import_form').ajaxForm({
			data: {
			   action: 'do_form_import'
			},
			beforeSubmit: function(formData, jqForm, options) {
				jQuery('div.nex-forms-container').html('<div class="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>');
			},
		   success : function(responseText, statusText, xhr, $form) {
			   	
				jQuery('.wizard_step').hide();
				jQuery('.creating_new_form').show();
				
				var url = jQuery('.admin_url').text() + 'admin.php?page=nex-forms-builder&open_form=' + responseText;
				jQuery(location).attr('href',url);
				//jQuery('.current_step_container').html(responseText);
			},
			 error: function(jqXHR, textStatus, errorThrown)
				{
				popup_user_alert(errorThrown)
				}
		});
		
		
	jQuery('#new_nex_form').ajaxForm({
			data: {
			   action: 'nf_insert_record',
			   table: 'wap_nex_forms',
			   is_form: 1,
			   is_template: 0
			},
			//dataType: 'json',
			beforeSubmit: function(formData, jqForm, options) {
				//alert('test');
				//console.log($('input[name="do_image_upload_preview"]').val())
				jQuery('#create_new_form button').html('&nbsp;&nbsp;&nbsp;<span class="fa fa-spin fa-spinner"></span>&nbsp;Creating...&nbsp;&nbsp;&nbsp;')
				
			},
		   success : function(responseText, statusText, xhr, $form) {
			   //jQuery('#field-pref button').html('&nbsp;&nbsp;&nbsp;Save Field Preferences&nbsp;&nbsp;&nbsp;');
			   jQuery('.wizard_step').hide();
				jQuery('.creating_new_form').show();
			  jQuery(location).attr('href',jQuery('#siteurl').text()+'/wp-admin/admin.php?page=nex-forms-builder&open_form=' + responseText)
			},
			 error: function(jqXHR, textStatus, errorThrown)
				{
				console.log(errorThrown)
				}
		});
		
		
	jQuery(document).on('click','.create_new_form',
		function()
			{
			jQuery('.wizard_step').hide();
			jQuery('.select_new_form_option').show();
			jQuery('#new_form_wizard').modal('open');
			}
		);
	
	
	jQuery('div.updated').remove();
	jQuery('.update-nag').remove();
	jQuery('div.error').remove();
		
		
		
	}
);
function hide_canvas_panels(){
	/*jQuery('.form-name-col').removeClass('admin_animated').removeClass('bounceOutLeft').removeClass('bounceInLeft');
	jQuery('.form-name-col').addClass('admin_animated').addClass('bounceOutLeft');
	setTimeout(function(){ jQuery('.form-name-col').hide() },800)
	
	jQuery('.fields-column').removeClass('admin_animated').removeClass('bounceOutLeft').removeClass('bounceInLeft');
	jQuery('.fields-column').addClass('admin_animated').addClass('bounceOutLeft');
	setTimeout(function(){ jQuery('.fields-column').hide() },800)
	
	jQuery('.field-category-column').removeClass('admin_animated').removeClass('bounceOutLeft').removeClass('bounceInLeft');
	jQuery('.field-category-column').addClass('admin_animated').addClass('bounceOutLeft');
	setTimeout(function(){ jQuery('.field-category-column').hide() },800)
	
	jQuery('.draggable-grid').removeClass('admin_animated').removeClass('bounceOutUp').removeClass('bounceInDown');
	jQuery('.draggable-grid').addClass('admin_animated').addClass('bounceOutUp');
	setTimeout(function(){ jQuery('.draggable-grid').hide() },800)
	
	jQuery('.form-canvas-column').removeClass('admin_animated').removeClass('bounceOutUp').removeClass('bounceInDown');
	jQuery('.form-canvas-column').addClass('admin_animated').addClass('bounceOutUp');
	setTimeout(function(){ jQuery('.form-canvas-column').hide() },800)
	
	jQuery('.field-settings-column').removeClass('admin_animated').removeClass('flipInY').removeClass('flipOutY');
	jQuery('.field-settings-column').addClass('admin_animated').addClass('flipOutY');
	setTimeout(function(){ jQuery('.field-settings-column').hide() },800)
	
	jQuery('.con-logic-column').removeClass('admin_animated').removeClass('flipInY').removeClass('flipOutY');
	jQuery('.con-logic-column').addClass('admin_animated').addClass('flipOutY');
	setTimeout(function(){ jQuery('.con-logic-column').hide() },800)
	jQuery('.conditional-logic').removeClass('active');
	
	jQuery('.extra-styling-column').removeClass('admin_animated').removeClass('flipInY').removeClass('flipOutY');
	jQuery('.extra-styling-column').addClass('admin_animated').addClass('flipOutY');
	setTimeout(function(){ jQuery('.extra-styling-column').hide() },800)
	jQuery('.form-styling').removeClass('active');
	
	jQuery('.paypal-column').removeClass('admin_animated').removeClass('flipInY').removeClass('flipOutY');
	jQuery('.paypal-column').addClass('admin_animated').addClass('flipOutY');
	setTimeout(function(){ jQuery('.paypal-column').hide() },800)
	jQuery('.paypal-options').removeClass('active');
	
	
	jQuery("html, body").animate(
					{
					scrollTop:0
					},200
				);*/
}

function show_canvas_panels(){
	/*jQuery('.form-name-col').removeClass('admin_animated').removeClass('bounceOutLeft').removeClass('bounceInLeft');
	jQuery('.form-name-col').addClass('admin_animated').addClass('bounceInRight').show();
	
	jQuery('.fields-column').removeClass('admin_animated').removeClass('bounceOutLeft').removeClass('bounceInLeft');
	jQuery('.fields-column').addClass('admin_animated').addClass('bounceInLeft').show();
	
	jQuery('.field-category-column').removeClass('admin_animated').removeClass('bounceOutLeft').removeClass('bounceInLeft');
	jQuery('.field-category-column').addClass('admin_animated').addClass('bounceInLeft').show();
	
	jQuery('.draggable-grid').removeClass('admin_animated').removeClass('bounceOutUp').removeClass('bounceInDown');
	jQuery('.draggable-grid').addClass('admin_animated').addClass('bounceInDown').show();
	
	jQuery('.form-canvas-column').removeClass('admin_animated').removeClass('bounceOutUp').removeClass('bounceInDown');
	jQuery('.form-canvas-column').addClass('admin_animated').addClass('bounceInDown').show();
	
	jQuery('.currently_editing').find('div.edit').trigger('click');
	
	jQuery("html, body").animate(
					{
					scrollTop:0
					},200
				);*/

}

function unformat_name(input_value){
	if(!input_value)
		return;
	
	var new_value = input_value.replace('_',' ').replace('[','').replace(']','');
	
	return new_value;
}
function format_illegal_chars(input_value){
	
	if(!input_value)
		return;
	
	input_value = input_value.toLowerCase();
	if(input_value=='name' || input_value=='page' || input_value=='post' || input_value=='id')
		input_value = '_'+input_value;
		
	var illigal_chars = '+=!@#$%^&*()*{};<>,.?~`|/\'';
	
	var new_value ='';
	
    for(i=0;i<input_value.length;i++)
		{
		if (illigal_chars.indexOf(input_value.charAt(i)) != -1)
			{
			input_value.replace(input_value.charAt(i),'');
			}
		else
			{
			if(input_value.charAt(i)==' ')
			new_value += '_';
			else
			new_value += input_value.charAt(i);
			}
		}
	return new_value;	
}

function strstr(haystack, needle, bool) {
    var pos = 0;

    haystack += "";
    pos = haystack.indexOf(needle); if (pos == -1) {
       return false;
    } else {
       return true;
    }
}

function short_str(str) {
    if(str)
       return str.substring(0, 10);
    
}

function insertAtCaret(areaId,text) {
    var txtarea = document.getElementById(areaId);
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ? 
    	"ff" : (document.selection ? "ie" : false ) );
    if (br == "ie") { 
    	txtarea.focus();
    	var range = document.selection.createRange();
    	range.moveStart ('character', -txtarea.value.length);
    	strPos = range.text.length;
    }
    else if (br == "ff") strPos = txtarea.selectionStart;

    var front = (txtarea.value).substring(0,strPos);  
    var back = (txtarea.value).substring(strPos,txtarea.value.length); 
    txtarea.value=front+text+back;
    strPos = strPos + text.length;
    if (br == "ie") { 
    	txtarea.focus();
    	var range = document.selection.createRange();
    	range.moveStart ('character', -txtarea.value.length);
    	range.moveStart ('character', strPos);
    	range.moveEnd ('character', 0);
    	range.select();
    }
    else if (br == "ff") {
    	txtarea.selectionStart = strPos;
    	txtarea.selectionEnd = strPos;
    	txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
} 

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function nf_form_modified(modification){
	
	jQuery('.check_save').addClass('not_saved');
	
	jQuery('.prime_save').find('.ns').remove();	

	jQuery('.prime_save').append('<span class="ns">*</span>');
	
}