window.debug = true;
window.Log = function(l){ return window.debug == true && console ? console.log(l) : '' ; };

jQuery(document).ready(function($){
    
    if( jQuery('#mts_dst').length == 1  ){
		/* start form actions */
			
			jQuery('#mts_dst').click(function(){
			 
                var val = jQuery(this).val();
             	if( val.length == 0 || val == 'Find by location..' ){
						jQuery(this).val('');
                         $(this).addClass("wait");
                         
					}
				
			            
            					//jQuery('#mts_dst').click(function(){
					   
				 
						//if( jQuery(this).val().length != 0 || jQuery(this).val() != 'Find by location..' ){
                          //      
   			                  
                              jQuery.get(
    
        MTSAjax.ajaxurl,
        {
            action : 'form_autocomplete',
            lg: 0,
            refagt: 'wxft6043'
            
        },
        function(dest,status)
        {
     			/* get broker destinations */
               //alert(dest);
               $(this).addClass("wait");
				if(status == 'success'){
				    
                    
					jQuery('#mts_dst').autocomplete({
						minLength: 0,
                        //search:function(){$(this).addClass("wait");},
			      		source: function( request, response ){
			      			//Log( data );
                             
			      			var results = jQuery.ui.autocomplete.filter( dest, request.term);
			      			response(results.splice(0, 10));
                            $(this).addClass("wait");
                            
			      		},
                        open: function(event, ui) {
                                $(this).removeClass("wait");
                             //$("#load_locations").show();
                        },
			      		select: function(event, ui){
			      			/* On select -> set location ID */
			      			var location = ui.item.value.toString().toLowerCase().replace(/\s+/g, '-');
			      			jQuery('#mts_from').val( location );
                                                       //$("#load_locations").hide();
			      		},
			      		close: function( event, ui ) {
			      			var val = jQuery(this).val();
							if( val.length == 0 || val == 'Find by location..' || jQuery('#mts_from').val().length == 0 ){
								jQuery(this).val('Find by location..');
                                                           
							}
			      		}
					}).click(function(){
					   
				        jQuery(this).autocomplete('search', ' ');
					   
					});
                    
                    
                    //$( "#mts_dst" ).trigger( "click" );
                    
     				}
                
                
        }
        
       
    
    );
	 }); 					//}
						
				//	});
    
    
    
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
	
	
		    window.toDate      = $('#mts_date_to').datepicker({onSelect: function(selectedDate) { window.fromDate.datepicker('option', 'maxDate', jQuery(this).datepicker('getDate'));}, dateFormat: 'dd.mm.yy',minDate:0});
			
			
			jQuery('#start_search').click(function(e){
				e.preventDefault();/* in case it will be transformed in a link */
                if ($('#front').length!=0)
                {
                    var url = $('#front').attr('action');
                }
                else
                {
                    var url = window.location.protocol + '//' + window.location.host + window.location.pathname;
                }
				
				if( jQuery('#mts_from').val() == '' ){
					jQuery('.mts_search_error').fadeIn();
					return false;
				}else{
					//url += 'yacht-charter/' + jQuery('#mts_from').val();
                    url += '?dst=' + $('#mts_from').val();
					jQuery('.mts_search_error').fadeOut;
					/* trigger another error if date from is filled and date to is not */
					
					if( ( jQuery('#mts_date_from').val() != ''  && jQuery('#mts_date_to').val() == '' ) || ( jQuery('#mts_date_from').val() == ''  && jQuery('#mts_date_to').val() != '') ){
						jQuery('.mts_search_date_error').fadeIn();
						return false;
					}
					if( jQuery('#mts_date_from').val() != ''  && jQuery('#mts_date_to').val() != '' ){
						url += '&date_from=' + jQuery('#mts_date_from').val() + '&date_to=' + jQuery('#mts_date_to').val() ;
					}
					if( jQuery('#mts_boat_type').val() != '' ){
						url += '&bt_type=' + jQuery('#mts_boat_type').val().toString().toLowerCase().replace(/\s+/g, ' ') ;
					}
					window.location = url;
					//console.log( url );
				}
			});
			
	/* end form action */}
	

	

/* jq inst end */});
