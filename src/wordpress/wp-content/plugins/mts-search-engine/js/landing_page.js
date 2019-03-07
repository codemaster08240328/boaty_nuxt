  jQuery(document).ready(function(){      
    
    jQuery("#request_form button").click(function(e){
        e.preventDefault();
        jQuery('#request_form').addClass('in_progress');
        jQuery('#request_form form').hide();
         jQuery('#request_form .mess').hide();
        jQuery.post(
    
        MTSAjax.ajaxurl,
        {
            action: 'landing_page',
            name: jQuery('#name_user').val(),
            email: jQuery("#email_user").val(),
        },
        function(data,status)
        {
            
            if( status == 'success' )
            {
                jQuery('#request_form').removeClass('in_progress');
                
                console.log(data);
     
			     if(data.send==1)
                 {
                    jQuery('#request_form .sussex').show();
                 }
                 else
                 {
                    jQuery('#request_form form').show();
                    jQuery('#request_form form .fields').hide();
                    jQuery('#request_form .mess').show();
                 }
                 
            }
     });
        
    })
    });
