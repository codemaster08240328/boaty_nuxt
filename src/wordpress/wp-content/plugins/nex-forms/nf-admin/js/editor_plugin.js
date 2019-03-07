var element = '';
var code = '';
var editor;							
jQuery(document).ready( function($) {
(function() {
	
	
	jQuery(document).on('click','.tiny_button_tags_placeholders .item', function() {
			element = '';
			code = '';
			element = $(this).attr('element');
			code = $(this).attr('code');
			editor.selection.setContent('{{' + code + '}}');
			//jQuery('.mce-flight_shortcodes.is_opened').trigger('click');
			code = '';
		});
	
    tinymce.create('tinymce.plugins.nf_tags_button', {
        init : function(ed, url) {
            ed.addButton('nf_tags_button', {
                title : 'Add an element',
				classes : 'flight_shortcodes btn nf_mce_button',
                //image : url + '/icon.png',
				text: '+ Add field tag',
                onclick : function(element) {
					
					
					editor = ed;
					
					var the_button = $('#'+element.control._id);
					var button = the_button.offset();
					var top = (button.top + 10) + "px";
					var left = (button.left) + "px";
					
					element = '';
					code = '';	
					
						if(the_button.hasClass('is_opened'))
							{
							the_button.removeClass('is_opened');
							$('.tiny_button_tags_placeholders').hide();
							}
						else
							{
							the_button.addClass('is_opened');
							$('.tiny_button_tags_placeholders').css({'top': top,'left': left}).fadeIn('slow');
							}
						
						
                }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('nf_tags_button', tinymce.plugins.nf_tags_button);
})();
});