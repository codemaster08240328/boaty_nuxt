jQuery(document).ready(function(){
	
var offset = 150;
window.theBoat = false;
jQuery('#myScrollspy li a.mts_navl').click(function(event) {
    var hash = this.hash;

   // animate
   jQuery('html, body').animate({
       scrollTop: ( jQuery(this.hash).offset().top - offset )
     }, 300, function(){

       // when done, add hash to url
       // (default click behaviour)
       window.location.hash = hash;
     });
});


function get_avail()
{
    
      if (jQuery("#database").val()=='booker')
    {

        jQuery.get(
    
        MTSAjax.ajaxurl,
        {
            action: 'booker_availability',
            id_boat: jQuery('#id_boat').val(),
            operator: jQuery('#operator').val(),
            homeport: jQuery('#homeport').val(),
            country: jQuery('#country').val(),
            boat_name: jQuery('#boat_name').val(),
            boat_model: jQuery('#boat_model').val(),
            boat_type: jQuery('#boat_type').val(),
            boat_year: jQuery('#boat_year').val(),
            date_from: jQuery('#mts_recheck_in').val(),
            date_to: jQuery('#mts_recheck_out').val()
            
        },
        function(data,status)
        {
            if( status == 'success' )
            {
                
			    jQuery('#mts_avail').removeClass('in_progress');
                jQuery('#mts_avail_js').hide();
                console.log(data);
     
			     if(data!='not')
                 {  
                    
					if (data.status == 'Booked'){

					jQuery('.boat_avail_ctrl.ctrl_show').hide();
				   jQuery('.boat_avail_ctrl.not_avails').show();
				   jQuery('.header_mts_avail ').hide();
                    jQuery('.boat_avail_ctrl.trip_dates').hide();
                    jQuery('.boat_avail_ctrl.ctrl_hide').addClass('show_y').show();					
					
					}else{

                     jQuery('.boat_avail_ctrl.ctrl_show').show();
                     jQuery('.boat_avail_ctrl.not_avails').hide();
                     jQuery('.boat_avail_ctrl.trip_dates').show();
                     
                     if(data.id_boat)
                     {
                        //window.theBoat = data;
                        //if boat available, show price and button
						                      
						//********subtract 1 from date end
						var ds = data.dateend.split("/");
						var ds1 = parseInt(ds[0]) -1;
						var dsmonth = (ds1 < 10) ? "0"+ds1 : ds1;
						var dateview = dsmonth+"/"+ds[1] +"/"+ds[2];
						//********* end
                        jQuery('.boat_avail_ctrl.ctrl_show').addClass('show_y');	
						jQuery('#mts_js_destination').text(data.homeport +', '+data.country);
                        jQuery('#mts_js_datestart').text( data.datestart );
                        // jQuery('#mts_js_dateend').text( data.dateend);
						 jQuery('#mts_js_dateend').text( dateview);
						  jQuery('#mts_js_duration').text( parseInt(data.no_days) -1  + ' nights');
                        // jQuery('#mts_js_duration').text( data.no_days + ' days');
                        var cur_symb="&euro;";
                        
                        
                        if(data.currency!='EUR')
                        {
                             cur_symb="$";
                        }
                     
                        
                        if(data.ask_price==1)
                        {
                            jQuery('#if_price').hide();
                            jQuery('#if_discount').hide();
                            jQuery('#if_discount_total').hide();
                            jQuery('#detinfo').html('<span class="mts_display_price first squarebrd mes">For the currect price'+
                                                ' you can contact us! We will confirm 5%'+
                                                ' discount!</span>');
                            jQuery('#detinfo').show();
                            jQuery('#caution').hide();
                        }
                        else
                        {
                            jQuery('#if_price').show();
                            jQuery('#if_discount').show();
                            jQuery('#if_discount_total').show();
                             if( data.oldprice == 0)
                        { 
                               
                                jQuery('#mts_js_price').html( '<span class="euro_sign_brs" >'+
                                        cur_symb+'</span>' + data.newprice);
                                jQuery('#if_discount').html(  '<span class="descr_av_1" >Our discount</span><span class="descr_av_2" '+ 
                                                'id="mts_js_discount"  >5%</span>');
                                jQuery('#if_discount_total').html('<span class="descr_av_1" >Final Price</span><span '+
                                                        'class="descr_av_2" id="mts_js_total" ><span class="euro_sign_brs">'
                                                        +cur_symb+'</span>' + parseInt(data.ourprice) );
                        }
                        else
                        {
         
                            jQuery('#mts_js_price').html( '<span class="euro_sign_brs" >'+
                                        cur_symb+'</span>' + parseInt(data.oldprice));
                            jQuery('#if_discount').html( '<span class="descr_av_1" >Discount</span><span class="descr_av_2" '+ 
                                                'id="mts_js_discount"  >' + parseInt(data.newprice) + '</span>');
                            jQuery('#if_discount2').html('<span class="descr_av_1" >Our discount</span><span class="descr_av_2" '+ 
                                                'id="mts_js_discount"  >5%</span></span>' );
                            jQuery('#if_discount2').show();
                            jQuery('#if_discount_total').html('<span class="descr_av_1" >Final Price</span><span '+
                                                        'class="descr_av_2" id="mts_js_total"  ><span class="euro_sign_brs" > '
                                                        +cur_symb+'</span>' + parseInt(data.ourprice) ); 
                            
                           
                        }
                        if(data.final_price!='')
                        {
                            jQuery('#caution').html(data.final_price);
                        }
                        else
                        {
                            jQuery('#caution').hide();
                        }
                        
                            jQuery('#detprice').hide();
                        }

					 }     
                    
				   }
                }
                else
                {

				 jQuery('.header_mts_avail_booked ').hide();
                    jQuery('.boat_avail_ctrl.not_avails').show();
                    jQuery('.boat_avail_ctrl.trip_dates').hide();
                    jQuery('.boat_avail_ctrl.ctrl_hide').addClass('show_y').show();
                }
            }
        });
    }
    else
    {
            
    jQuery.get(
    
        MTSAjax.ajaxurl,
        {
            action: 'boat_availability',
            id_boat: jQuery('#id_boat').val(),
            date_to: jQuery("#mts_recheck_out").val(),
            date_from: jQuery("#mts_recheck_in").val(),
            country: jQuery('#country').val() ,
            homeport: jQuery('#homeport').val()
            
            
        },
        function(data,status)
        {
            
            if( status == 'success' )
            {
			     
	       jQuery('#mts_avail').removeClass('in_progress');
                jQuery('#mts_avail_js').hide();
                console.log(data);
     
			     if(data!='not')
                 {  
                    
                     jQuery('.boat_avail_ctrl.ctrl_show').show();
                     jQuery('.boat_avail_ctrl.not_avails').hide();
                     jQuery('.boat_avail_ctrl.trip_dates').show();
                    jQuery('#caution').hide();
                     if(data.id_boat)
                     {
                        //window.theBoat = data;
                        //if boat available, show price and button
						//********subtract 1 from date end
						var ds = data.dateend.split("/");
						var ds1 = parseInt(ds[0]) -1;
						var dsmonth = (ds1 < 10) ? "0"+ds1 : ds1;
						var dateview = dsmonth+"/"+ds[1] +"/"+ds[2];
						//********* end
                        jQuery('.boat_avail_ctrl.ctrl_show').addClass('show_y');	
						jQuery('#mts_js_destination').text(data.homeport +', '+data.country);
                        jQuery('#mts_js_datestart').text( data.datestart );
						jQuery('#mts_js_dateend').text( dateview);
                        // jQuery('#mts_js_dateend').text( data.dateend);
                        // jQuery('#mts_js_duration').text( data.no_days + ' days');
						 jQuery('#mts_js_duration').text( parseInt(data.no_days) -1  + ' nights');
                        var cur_symb="&euro;";
             
                        //if(data.currency!='EUR')
                        //{
                        //     $cur_symb="$";
                        //}
                        var thebr = '';
                        
                        if(data.ask_price==1)
                        {
                            jQuery('#if_price').hide();
                            jQuery('#if_discount').hide();
                            jQuery('#if_discount_total').hide();
                            jQuery('#detinfo').html('<span class="mts_display_price first squarebrd mes">For the currect price'+
                                                ' you can contact us! We will confirm 5%'+
                                                ' discount!</span>');
                            jQuery('#detinfo').show();
                        }
                        else
                        {
                            jQuery('#if_price').show();
                            jQuery('#if_discount').show();
                            jQuery('#if_discount_total').show();
                            jQuery('#detinfo').hide();
                            jQuery('#caution').show();
                        if( parseInt(data.oldprice) == 0)
                        { 
                                
                                jQuery('#mts_js_price').html( '<span class="euro_sign_brs" >'+
                                        cur_symb+'</span>' + data.newprice);
                                jQuery('#if_discount').html(  '<span class="descr_av_1" >Our discount</span><span class="descr_av_2" '+ 
                                                'id="mts_js_discount"  >5%</span>');
                                jQuery('#if_discount_total').html('<span class="descr_av_1" >Final Price</span><span '+
                                                        'class="descr_av_2" id="mts_js_total"  ><span class="euro_sign_brs" > '
                                                        +cur_symb+'</span>' + parseInt(data.ourprice));
                        }
                        else
                        {
                            
                            
                            jQuery('#mts_js_price').html( '<span class="euro_sign_brs" >'+
                                        cur_symb+'</span>' + data.oldprice);
                            jQuery('#if_discount').html( '<span class="descr_av_1" >Discount</span><span class="descr_av_2" '+ 
                                                'id="mts_js_discount"  >' + data.newprice + '%</span>'+
                                                '<span class="descr_av_1" >Our discount</span><span class="descr_av_2" '+ 
                                                'id="mts_js_discount"  >5%</span>' );
                            jQuery('#if_discount_total').html('<span class="descr_av_1" >Final Price</span><span '+
                                                        'class="descr_av_2" id="mts_js_total"  ><span class="euro_sign_brs" > '
                                                        +cur_symb+'</span>' + parseInt(data.ourprice)); 
                            
                           
                        }
                        }

                }
        }
        else
        {
            window.theBoat = false;
                    jQuery('.boat_avail_ctrl.not_avails').show();
                    jQuery('.boat_avail_ctrl.trip_dates').hide();
                    jQuery('.boat_avail_ctrl.ctrl_hide').addClass('show_y').show();
        }
    }
    });
 
}
}




if (jQuery("#available").length==1)
{
        get_avail();
}

var fromDate = jQuery('#mts_recheck_in').datepicker({onSelect: function(selectedDate) {toDate.datepicker('option', 'minDate', jQuery(this).datepicker('getDate') || 0);},dateFormat: 'dd.mm.yy',minDate:0 });
var toDate      = jQuery('#mts_recheck_out').datepicker({onSelect: function(selectedDate) { fromDate.datepicker('option', 'maxDate', jQuery(this).datepicker('getDate'));}, dateFormat: 'dd.mm.yy',minDate:0});

jQuery('.check_availb').click(function(){
    
    jQuery('#mts_avail_js').show();

	jQuery('#mts_avail').addClass('in_progress');
	
	jQuery('.boat_avail_ctrl.ctrl_hide').hide();
	jQuery('.boat_avail_ctrl.ctrl_show').hide();
    
    get_avail();

});

jQuery('.chgtd').click(function(){
	jQuery('.boat_avail_ctrl.ctrl_hide').show();
	jQuery('.boat_avail_ctrl.ctrl_show').hide();
})




function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}





// booking 
function is_validE( email ){
		if( email == '' ){ return false; }	
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email) != false ? email : false;
}

jQuery('#myScrollspy #mts_book_boat').click(function(){
    jQuery('#myModal form').show();
    jQuery('#myModal .last_msg').hide();
    jQuery('#search_header').css("z-index",'0');
     jQuery('#myModal').css('top','0px');
});


jQuery('#mts_book_form').click(function(){
    if(jQuery('#modalReserve').length==1)
    {
        jQuery('#modalReserve').html(jQuery('#myModal').html());
        jQuery('#modalReserve').show();
        jQuery('#overlay_modal').show();
        var left_center=(jQuery(window).width()-jQuery('#modalReserve').outerWidth())/2+jQuery(window).scrollLeft();
        jQuery('#modalReserve').css('left',left_center+'px');
        var top_center=90;
        jQuery('#modalReserve').css('top',top_center+'px');
        jQuery('#modalReserve .modal-dialog').css('top','120px');
        jQuery('#modalReserve .mts_reserve').bind( "click", function(e) 
        {

	 e.preventDefault();
	
	       var nname = jQuery('#modalReserve .inputLastName').val();
	       var fname = jQuery('#modalReserve  .inputFirstName').val();
	       var eemail = jQuery('#modalReserve  .inputEmail').val();
	       var ttel = jQuery('#modalReserve  .inputPhone').val();

            if( nname.length < 3 || fname.length < 3 || ttel.length < 5 || is_validE( eemail ) == false )
            {
                jQuery('#modalReserve .show_error').html('<b><span class="warring">Please complete all the form fields.</span></b>').show();
                return false;
	       }
           var actiontodo='inputhold48';
           jQuery('#modalReserve .checka_optsa').each(function()
            {
                if(jQuery(this).is(':checked')==true)
                {
					actiontodo=jQuery(this).attr('id');
                }
                
            });
            var datestart=jQuery('#modalReserve .input_sel_dep').val();
            var dateend=jQuery('#modalReserve .input_sel_arv').val();
            
           
	       jQuery('#modalReserve .processingwait').show();
           jQuery('#modalReserve .wrapp_opac').hide();
           jQuery('#modalReserve .mts_reserve').hide();
          
            jQuery.post(
                MTSAjax.ajaxurl,
                {
                    action : 'boat_reservation',
                    name: nname, 
                    firstname: fname, 
                    email: eemail, 
                    tel: ttel,
                    actiontodo: actiontodo,
                    dateend: dateend,
                    datestart: datestart,
                    location_url: location.href
                },
                function(data,status)
                {
                    if(status == 'success')
                    {
                        console.log(data);   
                        jQuery('#modalReserve .processingwait').hide();
                        jQuery('#modalReserve #send_letter').html(data.message);
                    }
                });
           });
           
            jQuery('#modalReserve .close_modal').bind( "click", function(e) 
            {
                jQuery('#modalReserve').hide();
                jQuery('#overlay_modal').hide();
            });
            
            jQuery('#overlay_modal').bind( "click", function(e) 
            {
                jQuery('#modalReserve').hide();
                jQuery('#overlay_modal').hide();
            });
    }
    else
    {
        jQuery('body').append('<div id="modalReserve"></div>');
        jQuery('body').append('<div id="overlay_modal"></div>');
        jQuery('#modalReserve').html(jQuery('#myModal').html());
        jQuery('#modalReserve').show();
        var left_center=(jQuery(window).width()-jQuery('#modalReserve').outerWidth())/2+jQuery(window).scrollLeft();
        jQuery('#modalReserve').css('left',left_center+'px');
        var top_center=90;
        jQuery('#modalReserve').css('top',top_center+'px');
        jQuery('#modalReserve .modal-dialog').css('top','120px');
        jQuery('#modalReserve .mts_reserve').bind( "click", function(e) 
        {

	       e.preventDefault();
	
	       var nname = jQuery('#modalReserve  .inputLastName').val();
	       var fname = jQuery('#modalReserve  .inputFirstName').val();
	       var eemail = jQuery('#modalReserve  .inputEmail').val();
	       var ttel = jQuery('#modalReserve .inputPhone').val();
           var actiontodo='';

            if( nname.length < 3 || fname.length < 3 || ttel.length < 5 || is_validE( eemail ) == false )
            {
                jQuery('#modalReserve .show_error').html('<b><span class="warring">Please complete all the form fields.</span></b>').show();
                return false;
	       }
             var actiontodo='inputhold48';
           jQuery('#modalReserve .checka_optsa').each(function()
            {
                if(jQuery(this).is(':checked')==true)
                {
					actiontodo=jQuery(this).attr('id');
                }
                
            });
            var datestart=jQuery('#modalReserve .input_sel_dep').val();
            var dateend=jQuery('#modalReserve .input_sel_arv').val();
            
        
	       jQuery('#modalReserve .processingwait').show();
           jQuery('#modalReserve .wrapp_opac').hide();
           jQuery('#modalReserve .mts_reserve').hide();
            jQuery.post(
                MTSAjax.ajaxurl,
                {
                    action : 'boat_reservation',
                    name: nname, 
                    firstname: fname, 
                    email: eemail, 
                    tel: ttel,
                    actiontodo: actiontodo,
                    dateend: dateend,
                    datestart: datestart,
                    location_url: location.href
                },
                function(data,status)
                {
                    if(status == 'success')
                    {
                        console.log(data);   
                        jQuery('#modalReserve .processingwait').hide();
                        jQuery('#modalReserve #send_letter').html(data.message);
                    }
                });
           });
           
            
        jQuery('#modalReserve .close_modal').bind( "click", function(e) 
        {
            jQuery('#modalReserve').hide();
            jQuery('#overlay_modal').hide();
        });
        
        jQuery('#overlay_modal').bind( "click", function(e) 
        {
            jQuery('#modalReserve').hide();
            jQuery('#overlay_modal').hide();
        });
    }
    
    
});


jQuery('#myModal').click(function(){
    jQuery('#mk-header .mk-header-inner').show();
});







jQuery('#mts_book_boat_submit').click(function(e){
	
	e.preventDefault();
	
	var nname = jQuery('#inputLastName').val();
	var fname = jQuery('#inputFirstName').val();
	var eemail = jQuery('#inputEmail').val();
	var ttel = jQuery('#inputPhone').val();
    
    
	
	if( nname.length < 3 || fname.length < 3 || ttel.length < 5 || is_validE( eemail ) == false ){
		jQuery('#show_error').html('<b><span class="warring">Please complete all the form fields.</span></b>').show();
		return false;
	}
	
	//var POST = {  choix: 'agt', name: nname, firstname: fname, email: eemail, tel: ttel };
	//var url      = window.WEB_URL + 'search/boat_add_booking.php?do=add_client';
    
    var POST = {  choix: 'agt', name: nname, firstname: fname, email: eemail, tel: ttel, doneed: 'add_client' };
	
	
	jQuery('#processingwait').fadeIn('');
	jQuery('.wrapp_opac').css('opacity', '0.7').css('z-index', '2');
        jQuery.post(
    
        MTSAjax.ajaxurl,
        {
            action : 'boat_booking',
            choix: 'agt', 
            name: nname, 
            firstname: fname, 
            email: eemail, 
            tel: ttel,
            doneed: 'add_client'
        },
        function(data,status)
        {
               
      		if(status == 'success'){
      		    
			if(  jQuery.isNumeric(data) ){
			 
				// if numeric ID generated. 
				
				if( window.theBoat != false ){
					
					//var url1 = window.WEB_URL + 'search/boat_add_booking.php?do=add_booking&id=' + data;
					var POST1 = window.theBoat;
                    POST1['actiontodo']='';
					POST1['bid'] = jQuery('#bid').val();
					POST1['pax_sel'] = jQuery('#input_sel_pax').val();
                    POST1['dep_id'] = jQuery('#base_id').val();
					POST1['arv_id'] = jQuery('#base_id').val();
					POST1['location_url'] = window.location.href;
					
					var optsax = [];
					
					jQuery('.checkbox_opt').each(function(){
						if(jQuery(this).is(':checked')){
							optsax.push(  jQuery(this).attr('data-id_opt')  );
						}
					});
					
					jQuery('.checka_optsa').each(function(){
						if(jQuery(this).is(':checked')){
							POST1['actiontodo'] = jQuery(this).attr('id');
						}
					})
					
					POST1['orig_post'] = POST;
					
					POST1['opts_1'] = optsax;
					
					//console.log( POST1 );
                jQuery.post(
                    MTSAjax.ajaxurl,
                        {
                            action: 'boat_booking',
                            bid: jQuery('#bid').val(),
					        pax_sel: jQuery('#input_sel_pax').val(),
                            dep_id: jQuery('#base_id').val(),
					       arv_id:  jQuery('#base_id').val(),
					       location_url: window.location.href,
                           actiontodo: POST1['actiontodo'],
                           orig_post: POST1['orig_post'],
                           opts_1: POST1['opts_1'],
                           doneed: 'add_booking',
                           datestart: jQuery("#mts_js_datestart").text(),
                           dateend: jQuery("#mts_js_dateend").text(),
                           id: data
                        },
                        function(data1, status1)
                        {

						      if( status1 == 'success' )
                              {
							     console.log(  data1 );
							     jQuery('#processingwait').fadeOut('');
                                 jQuery('#search_header').css("z-index","10010");
							     jQuery('.wrapp_opac').css('opacity', '1').css('z-index', '2');
							     jQuery('#show_error').html('<b>Thank you,  Your request was submitted </b>').show();
                                 jQuery('#myModal').css('top','200px');
                                 jQuery('#myModal .last_msg .content').html('<b>Please check your InBox and Spam. <br />If you have not received a reply within 15 minute please write to Joanne@sailChecker.com.</b>');
                                 jQuery('#myModal .last_msg ').show();
                                
                                 jQuery('#myModal form').hide();
							    
							     //setTimeout(function(){   $('#myModal').modal('hide') ;}, 5000);
						      }
                        }
                        );
                        
				    }
				
			}else{
				//generate error 
				jQuery('#processingwait').fadeOut('');
				jQuery('.wrapp_opac').css('opacity', '1').css('z-index', '2');
				jQuery('#show_error').text(data).show();
				}
		}});
	   });
       

       
       
  jQuery('#mts_book_booker').click(function(e){
	
	e.preventDefault();
	
	var nname = jQuery('#inputLastName').val();
	var fname = jQuery('#inputFirstName').val();
	var eemail = jQuery('#inputEmail').val();
	var ttel = jQuery('#inputPhone').val();
    
    
	
	if( nname.length < 3 || fname.length < 3 || ttel.length < 5 || is_validE( eemail ) == false ){
		jQuery('#show_error').html('<b><span class="warring">Please complete all the form fields.</span></b>').show();
		return false;
	}
	
	//var POST = {  choix: 'agt', name: nname, firstname: fname, email: eemail, tel: ttel };
	//var url      = window.WEB_URL + 'search/boat_add_booking.php?do=add_client';
    
    var POST = {  choix: 'agt', name: nname, firstname: fname, email: eemail, tel: ttel, doneed: 'add_client' };
	
	
	jQuery('#processingwait').fadeIn('');
	jQuery('.wrapp_opac').css('opacity', '0.7').css('z-index', '2');
        jQuery.post(
    
        MTSAjax.ajaxurl,
        {
            action : 'boat_booking_booker',
            choix: 'agt', 
            name: nname, 
            firstname: fname, 
            email: eemail, 
            tel: ttel,
            doneed: 'add_client'
        },
        function(data,status)
        {
               
      		if(status == 'success'){
      		    
			if(  jQuery.isNumeric(data) ){
			 
				// if numeric ID generated. 
				
				if( window.theBoat != false ){
					
					//var url1 = window.WEB_URL + 'search/boat_add_booking.php?do=add_booking&id=' + data;
					var POST1 = window.theBoat;
                    POST1['actiontodo']='';
					POST1['bid'] = jQuery('#bid').val();
					POST1['pax_sel'] = jQuery('#input_sel_pax').val();
					POST1['dep_id'] = jQuery('#input_sel_dep').val();
					POST1['arv_id'] = jQuery('#input_sel_arv').val();
					POST1['location_url'] = window.location.href;
					
					var optsax = [];
					
					jQuery('.checkbox_opt').each(function(){
						if(jQuery(this).is(':checked')){
							optsax.push(  jQuery(this).attr('data-id_opt')  );
						}
					});
					
					jQuery('.checka_optsa').each(function(){
						if(jQuery(this).is(':checked')){
							POST1['actiontodo'] = jQuery(this).attr('id');
						}
					})
					
					POST1['orig_post'] = POST;
					
					POST1['opts_1'] = optsax;
					
					//console.log( POST1 );
                jQuery.post(
                    MTSAjax.ajaxurl,
                        {
                            action: 'boat_booking_booker',
                            bid: getUrlVars()["boat_id"],
					        pax_sel: jQuery('#input_sel_pax').val(),
                            dep_id: jQuery('#input_sel_dep').val(),
					       arv_id:  jQuery('#input_sel_arv').val(),
					       location_url: window.location.href,
                           actiontodo: POST1['actiontodo'],
                           orig_post: POST1['orig_post'],
                           opts_1: POST1['opts_1'],
                           doneed: 'add_booking',
                           datestart: jQuery("#mts_js_datestart").text(),
                           dateend: jQuery("#mts_js_dateend").text(),
                           id: data
                        },
                        function(data1, status1)
                        {
                            console.log('post made');
                           
						
						      if( status1 == 'success' )
                              {
							      
							     console.log(  data1 );
							     jQuery('#processingwait').fadeOut('');
                                 jQuery('#search_header').css("z-index","10010");
							     jQuery('.wrapp_opac').css('opacity', '1').css('z-index', '2');
							     jQuery('#show_error').html('<b>Thank you,  Your request was submitted </b>').show();
                                 jQuery('#myModal').css('top','200px');
                                 jQuery('#myModal .last_msg .content').html('<b>Please check your InBox and Spam. <br />If you have not received a reply within 15 minute please write to Joanne@sailChecker.com.</b>');
                                 jQuery('#myModal .last_msg ').show();
                                
                                 jQuery('#myModal form').hide();
							    
							     //setTimeout(function(){   $('#myModal').modal('hide') ;}, 5000);
						      }
                        }
                        );
                        
				    }
				
			}else{
				//generate error 
				jQuery('#processingwait').fadeOut('');
                jQuery('#search_header').css("z-index","10010");
				jQuery('.wrapp_opac').css('opacity', '1').css('z-index', '2');
				jQuery('#show_error').text(data).show();
				}
		}});
	   });

	
	
});


