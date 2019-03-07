jQuery(document).ready(function(){
	
var offset = 150;
window.theBoat = false;
$('#myScrollspy li a.mts_navl').click(function(event) {
    var hash = this.hash;

   // animate
   $('html, body').animate({
       scrollTop: ( $(this.hash).offset().top - offset )
     }, 300, function(){

       // when done, add hash to url
       // (default click behaviour)
       window.location.hash = hash;
     });
});


function get_avail(){
	$.get(window.WEB_URL+'&action=search&id_boat='+$('#bid').val() , function(data, status){
		
		if( status == 'success' ){
			console.log( data );
			$('#mts_avail').removeClass('in_progress');
			$('#mts_avail_js').hide();
			if(data !='not'){
				$('.boat_avail_ctrl.ctrl_show').show();
				$('.boat_avail_ctrl.not_avails').hide();
				$('.boat_avail_ctrl.trip_dates').show();
				if(data.id_boat){
					
					window.theBoat = data;
					
				/* if boat available, show price and button */	
					$('.boat_avail_ctrl.ctrl_show').addClass('show_y');	
					
					$('#mts_js_destination').text( data.country + ', ' + data.arrival );
					$('#mts_js_datestart').text( data.datestart );
					$('#mts_js_dateend').text( data.dateend );
					$('#mts_js_duration').text( data.no_days + ' days' );
					
					var thebr = '';
					if( parseInt(data.oldprice) == 0){
						$('#mts_js_price').html( '<span class="euro_sign_brs" > &#8364;</span>' + data.newprice  );
						$('#if_discount').html( '' );
						$('#if_discount_total').html( '' );
						
						var thebr = '<br />';
						
					}else{
						$('#mts_js_price').html( '<span class="euro_sign_brs" > &#8364;</span>' + data.oldprice  );
						$('#if_discount').html( '<span class="descr_av_1" >Discount</span><span class="descr_av_2" id="mts_js_discount"  >' + data.discount + '%</span>' );
						$('#if_discount_total').html( '<span class="descr_av_1" >Final Price</span><span class="descr_av_2" id="mts_js_total"  >' + data.newprice + '</span>' );
						
						var thebr = '';
					
					}
						$('#addthebr').html( thebr );
				}
			}else{
				
					window.theBoat = false;
				/* if boat not available show change dates */
	
					$('.boat_avail_ctrl.not_avails').show();
					$('.boat_avail_ctrl.trip_dates').hide();
					
					$('.boat_avail_ctrl.ctrl_hide').addClass('show_y').show();	
				
			}
		}
		
	});
}
get_avail();

var fromDate = $('#mts_recheck_in').datepicker({onSelect: function(selectedDate) {toDate.datepicker('option', 'minDate', $(this).datepicker('getDate') || 0);},dateFormat: 'dd.mm.yy',minDate:0 });
var toDate      = $('#mts_recheck_out').datepicker({onSelect: function(selectedDate) { fromDate.datepicker('option', 'maxDate', $(this).datepicker('getDate'));}, dateFormat: 'dd.mm.yy',minDate:0});

$('.check_availb').click(function(){
	var vs = fromDate.val();
	var ts = toDate.val();
	var POST = { date_from: vs , date_to:  ts };
	$('#mts_avail').addClass('in_progress');
	
	$('.boat_avail_ctrl.ctrl_hide').hide();
	$('.boat_avail_ctrl.ctrl_show').hide();
	//console.log( POST );
	$.post(window.WEB_URL+'search/boat_session.php', POST, function(data, status){
		
		if(status == 'success'){
			//console.log( data );
			get_avail();
		}
	});
	
});

$('.chgtd').click(function(){
	$('.boat_avail_ctrl.ctrl_hide').show();
	$('.boat_avail_ctrl.ctrl_show').hide();
})

/* pdf */

$('#download_boat_details').click(function(){
	
	var url = window.WEB_URL + 'search/boat_pdf.php?' + window.WEB_QUERY;
	
	download(url);
	console.log( url );
	
});
function download(url) {
	/* donwload a file */
	Log('Start download for ' + url);
    var hiddenIFrameID = 'hiddenDownloader',
        iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }
    iframe.src = url;
};	

/* booking */
function is_validE( email ){
		if( email == '' ){ return false; }	
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email) != false ? email : false;
}

$('#mts_book_boat_submit').click(function(e){
	
	e.preventDefault();
	
	var nname = $('#inputLastName').val();
	var fname = $('#inputFirstName').val();
	var eemail = $('#inputEmail').val();
	var ttel = $('#inputPhone').val();
	
	if( nname.length < 3 || fname.length < 3 || ttel.length < 5 || is_validE( eemail ) == false ){
		$('#show_error').html('<b>Please complete all the form fields. </b>').show();
		return false;
	}
	
	var POST = {  choix: 'agt', name: nname, firstname: fname, email: eemail, tel: ttel };
	var url      = window.WEB_URL + 'search/boat_add_booking.php?do=add_client';
	
	
	$('#processingwait').fadeIn('');
	$('.wrapp_opac').css('opacity', '0.7').css('z-index', '2');
	$.post(url, POST, function(data, status){
		
		if(status == 'success'){
			//console.log( data );
			//console.log( data );
			if(  $.isNumeric(data) ){
				/* if numeric ID generated. */
				
				if( window.theBoat != false ){
					
					var url1 = window.WEB_URL + 'search/boat_add_booking.php?do=add_booking&id=' + data;
					var POST1 = window.theBoat;
					POST1['bid'] = $('#bid').val();
					POST1['pax_sel'] = $('#input_sel_pax').val();
					POST1['dep_id'] = $('#input_sel_dep').val();
					POST1['arv_id'] = $('#input_sel_arv').val();
					POST1['location_url'] = window.location.href;
					
					var optsax = [];
					
					$('.checkbox_opt').each(function(){
						if($(this).is(':checked')){
							optsax.push(  $(this).attr('data-id_opt')  );
						}
					});
					
					$('.checka_optsa').each(function(){
						if($(this).is(':checked')){
							POST1['actiontodo'] = $(this).attr('id');
						}
					})
					
					POST1['orig_post'] = POST;
					
					POST1['opts_1'] = optsax;
					
					//console.log( POST1 );
					
					$.post( url1 , POST1 , function(data1, status1){
						
						console.log('post made');
						
						if( status1 == 'success' ){
							
							
							console.log(  data1 );
							$('#processingwait').fadeOut('');
							$('.wrapp_opac').css('opacity', '1').css('z-index', '2');
							$('#show_error').html('<b>Thank you,  Your request was submitted </b>').show()
							setTimeout(function(){   $('#myModal').modal('hide') ;}, 4000);
						}
						
					});
					
				}
				
			}else{
				/* generate error */
				$('#processingwait').fadeOut('');
				$('.wrapp_opac').css('opacity', '1').css('z-index', '2');
				$('#show_error').text(data).show();
				
			}
			
		}
		
	});
	
	
});


});
