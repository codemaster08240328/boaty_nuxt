window.debug = true;
window.Log = function(l){ return window.debug == true && console ? console.log(l) : '' ; };

jQuery(document).ready(function($){
    
    jQuery('#mts_date_from,#mts_date_to').val('');
    jQuery('#mts_boat_type').val('');

    jQuery('#mts_res_table_paginate').ready(function(){
        jQuery('#mts_res_table_length select').change(function(){
            setPagination();
        });
    });

    function setPagination(){
       jQuery('#mts_res_table_paginate').ready(function(){
            var paginationCount = jQuery('#mts_res_table_paginate span a');
            console.log(paginationCount.length);
            if(paginationCount.length<=1){
                jQuery('#mts_res_table_paginate').hide();            
            }else{
                jQuery('#mts_res_table_paginate').show();            
            }
        }); 
    }
    
    setPagination();

    if( jQuery('.transparent_form').length >0)
    {
        var url_cache = window.location.protocol + '//' + window.location.host + '/wp-content/uploads/xml/countries.json';

        
        jQuery(".transparent_form").each(function(i)
        {
            if(i==1)
            {   
                jQuery(this).find('form').attr('id','front2');
            }
        });
        
        jQuery(".selectdest").each(function(i)
        {
            if(i==1)
            {
                jQuery(this).attr('id','mts_dst2');
            }
        });
        
        jQuery(".search_go").each(function(i)
        {
                if(i==1){
                    
                    jQuery(this).attr('id','start_search2');
                    
                }
        });  
        

            if (jQuery(".selectboat").length>0)
            {
              jQuery(".selectboat").each(function(i){
                if(i==1){
                    jQuery(this).attr('id','mts_boat_type2');
                }
              });  
                
            }
            
            
            if (jQuery(".mts_date_to").length>0)
            {
              jQuery(".mts_date_to").each(function(i){
                if(i==1){
                    
                    jQuery(this).attr('id','mts_date_to2');
                    window.toDate2      = $('#mts_date_to2').datepicker({onSelect: 
            function(selectedDate) { window.fromDate.datepicker('option', 'maxDate', jQuery(this).datepicker('getDate'));}, 
            dateFormat: 'dd.mm.yy',minDate:0});
                }
              });  
                
            }
        
        
        if (jQuery(".mts_date_from").length>0)
            {
              jQuery(".mts_date_from").each(function(i)
              {
                if(i==1){
                    jQuery(this).attr('id','mts_date_from2');
                    
                    	window.fromDate2 = $('#mts_date_from2').datepicker({
				onSelect: function(selectedDate) {
					window.toDate2.datepicker('option', 'minDate', $(this).datepicker('getDate') || 0) ;
					var plus7 = $(this).datepicker('getDate') ;
					var plus7e = plus7.setDate( plus7.getDate() + 7 );
					window.toDate2.datepicker( 'setDate' , new Date(plus7e) );
					},
				onClose: function(e, u){
						window.toDate2.datepicker('show');
					},
				dateFormat: 'dd.mm.yy',minDate:0 }
				);
                }
              });  
                
            }
        
        if( jQuery('#mts_dst2').length >0)
        {
              $.getJSON( url_cache, function( data ) 
                {
                    jQuery('#mts_dst2').autocomplete({
                        minLength: 1,
                        autoFocus: true,
                        search:function(){},
     		             source: function( request, response )
                          {
                                var results = jQuery.ui.autocomplete.filter( data, request.term);
			      			    response(results.splice(0, 10));
			      		 },
                        open: function(event, ui) 
                        {

                            },
			      		     select: function(event, ui){
			      			/* On select -> set location ID */
			      			    var location = ui.item.value.toString().toLowerCase().replace(/\s+/g, '-');
			      			    jQuery('#mts_dst2').val( location );
			      		 },
			      		     close: function( event, ui ) {
			      			        var val = jQuery(this).val();
							         if( val.length == 0 || val == 'Where are you looking to charter?' || jQuery('#mts_from').val().length == 0 ){
								        jQuery(this).val('Where are you looking to charter?');
                                                           
							 }
			      		 }
					   });
            });
            
            jQuery('#mts_dst2').bind('focus',function(){
					   var val = $(this).val();
					       if( val == 'Where are you looking to charter?' ){
						$(this).val('');
                        
					       }
                       
					}).bind('blur',function(){
					   var val = $(this).val();
					       if( val.length == 0){
						$(this).val('Where are you looking to charter?');
					       }
					});
                    
               if (jQuery("#start_search2").length>0)
            {

                    jQuery('#start_search2').click(function(e)
                    {
				        e.preventDefault();/* in case it will be transformed in a link */
                        if ($('#front').length!=0)
                        {
                            var url = $('#front').attr('action');
                        }
                        
                        if( jQuery('#mts_dst2').val() == 'Where are you looking to charter?' )
                        {
					           return false;
				        }else
                        {
				
                            url += '?action=search&dst=' + jQuery('#mts_dst2').val();
                            if( jQuery('#mts_date_from2').val() != '')
                    {
                        url += '&date_from=' + jQuery('#mts_date_from2').val();
					}   
                     if (jQuery('#mts_date_to2').val() != '' )
                    {
						url +=  '&date_to=' + jQuery('#mts_date_to2').val() ;
					}
					if( jQuery('#mts_boat_type2').val() != '' ){
						url += '&bt_type=' + jQuery('#mts_boat_type2').val().toString().toLowerCase();
					}
					window.location = url;
                    }
			});


            }
        }
        
        
        $.getJSON( url_cache, function( data ) 
                {
                    jQuery('#mts_dst').autocomplete({
                        minLength: 1,
                        autoFocus: true,
                        search:function(){},
     		             source: function( request, response )
                          {
                                var results = jQuery.ui.autocomplete.filter( data, request.term);
			      			    response(results.splice(0, 10));
                            
			      		 },
                        open: function(event, ui) 
                        {

                            },
			      		     select: function(event, ui){
			      			/* On select -> set location ID */
			      			    var location = ui.item.value.toString().toLowerCase().replace(/\s+/g, '-');
			      			    jQuery('#mts_dst').val( location ).addClass('dark-text');
			      		 },
			      		     close: function( event, ui ) {
			      			        var val = jQuery(this).val();
							         if( val.length == 0 || val == 'Where are you looking to charter?' || jQuery('#mts_from').val().length == 0 ){
								        jQuery(this).val('Where are you looking to charter?').removeClass('dark-text');
                                                           
							 }
			      		 }
					   });
            });
            
            jQuery('#mts_dst').bind('focus',function(){
					   var val = $(this).val();
					       if( val == 'Where are you looking to charter?' ){
						$(this).val('');
                        
					       }
                       
					}).bind('blur',function(){
					   var val = $(this).val();
					       if( val.length == 0){
						$(this).val('Where are you looking to charter?').removeClass('dark-text');
					       }
					});
                    
                    
        	jQuery('#start_search').click(function(e){
				e.preventDefault();/* in case it will be transformed in a link */

                if ($('#front').length!=0)
                {
                    var url = $('#front').attr('action');
                }
                else
                {
                    return false;
                }
                    

				
				if( jQuery('#mts_dst').val() == 'Where are you looking to charter?' )
                {
					return false;
				}else
                {
                    url += '?action=search&dst=' + jQuery('#mts_dst').val();
					
					
					if( jQuery('#mts_date_from').val() != '')
                    {
                        url += '&date_from=' + jQuery('#mts_date_from').val();
					}
                      
                     if (jQuery('#mts_date_to').val() != '' )
                    {
						url +=  '&date_to=' + jQuery('#mts_date_to').val() ;
					}
					if( jQuery('#mts_boat_type').val() != '' ){
						url += '&bt_type=' + jQuery('#mts_boat_type').val().toString().toLowerCase().replace(" ", "+");
					}
					window.location = url;
                    }
				});  

    }
    
    if( jQuery('#search_fields #mts_location').length == 1  )
    {
        
         var url_cache = window.location.protocol + '//' + window.location.host + '/wp-content/uploads/xml/countries.json';
         
         
         
        $.getJSON( url_cache, function( data ) {
            
          jQuery('#search_fields #mts_location').autocomplete({
						minLength: 1,
                        autoFocus: true,
                        search:function(){},
			      		source: function( request, response ){
			      			//Log( data );
                              var results = jQuery.ui.autocomplete.filter( data, request.term);
			      			response(results.splice(0, 10));
                            
			      		},
                        open: function(event, ui) {

                        },
			      		select: function(event, ui){
			      			/* On select -> set location ID */
			      			var location = ui.item.value.toString().toLowerCase().replace(/\s+/g, '-');
			      			jQuery('#search_fields #mts_location').val( location );
			      		},
			      		close: function( event, ui ) {
			      			var val = jQuery(this).val();
							if( val.length == 0 || val == 'Where are you looking to charter?' || jQuery('#search_fields #mts_location').val().length == 0 ){
								jQuery(this).val('Where are you looking to charter?');
                                                           
							}
			      		}
					});
                    
     });

 
            jQuery('#search_fields #mts_location').bind('focus',function(){
					   var val = $(this).val();
					       if( val == 'Where are you looking to charter?' ){
						$(this).val('');
                        
					       }
                       
					}).bind('blur',function(){
					   var val = $(this).val();
					       if( val.length == 0){
						$(this).val('Where are you looking to charter?');
					       }
					});

       
       

                    
           	jQuery('#search_fields #boat_search').click(function(e){
				e.preventDefault();/* in case it will be transformed in a link */

                if ($('#search_fields form').length!=0)
                {
                    var url = $('#search_fields form').attr('action');
                }
                else
                {
                    return false;
                }
                    

				
				if( jQuery('#search_fields #mts_location').val() == 'Where are you looking to charter?' )
                {
					return false;
				}else
                {
                    url += '?action=search&dst=' + jQuery('#search_fields #mts_location').val();
					
					
					if( jQuery('#search_fields #mts_date_from').val() != '')
                    {
                        url += '&date_from=' + jQuery('#search_fields #mts_date_from').val();
					}
                      
                     if (jQuery('#search_fields #mts_date_to').val() != '' )
                    {
						url +=  '&date_to=' + jQuery('#search_fields #mts_date_to').val() ;
					}
					if( jQuery('#search_fields #mts_boat_type').val() != '' ){
						url += '&bt_type=' + jQuery('#search_fields #mts_boat_type').val().toString().toLowerCase().replace(" ", "+");
					}
					window.location = url;
                    }
				});           
        
    }
    

 
         
		window.fromDate = $('#mts_date_from').datepicker({
				onSelect: function(selectedDate) {
					window.toDate.datepicker('option', 'minDate', $(this).datepicker('getDate') || 0) ;
					var plus7 = $(this).datepicker('getDate') ;
					//Log( plus7 + ' -d?' );
					var plus7e = plus7.setDate( plus7.getDate() + 7 );
					//Log( plus7e );
					window.toDate.datepicker( 'setDate' , new Date(plus7e) );
					},
				onClose: function(e, u){
						window.toDate.datepicker('show');
					},
				dateFormat: 'dd.mm.yy',minDate:0 }
				);
	
	
		    window.toDate      = $('#mts_date_to').datepicker({onSelect: 
            function(selectedDate) { window.fromDate.datepicker('option', 'maxDate', jQuery(this).datepicker('getDate'));}, 
            dateFormat: 'dd.mm.yy',minDate:0});
	

	

/* jq inst end */});
