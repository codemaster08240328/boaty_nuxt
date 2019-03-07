/* global functions and vars. They do as named */
window.debug = false;
window.debug_count_data = 1;
window.Log = function(l, p){ return window.debug == true && console && p !== false ? console.log(l) : '' ; };
window.inArray = function(key, arr){
	for( var i in arr ){ if( arr[i] == key ){ return true } };
	return false;
};
window.inArray_w = function(du,mmy){ return false; };
window.joinAssoc = function(arr){ var str = ''; for(  var i in arr ){ str += arr[ i ]; } return str; };
jQuery.fn.extend({
    getType: function(){return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); }
    });
//(function($){ 
//$.fn.getType = function(){ return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); };
//})(jQuery); 

jQuery(document).ready(function($){
    
//$.fn.getType = function(){ return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); };
	
	/* used widgets. NOTE, ordering the appearing order is made by ordering this array elements */
	var all_widgets = [
	//'brand', 
	'built_in', 
	'price', 
	'discount', 
	'blength', 
	'model_of_boat', 
	'location', 
	'no_of_berth', 
	'no_of_double_cabins', 
	'no_of_single_cabins', 
	'type_of_boat',
	'license_needed',
	'crewed'
	];
	/* init script */
	if( $('#mts_res_table').length == 1 ){
		
		/* predefine filters */
		var count_selected = {};
		$.fn.dataTableExt.afnFiltering.push(function( oSettings, aData, iDataIndex ){
			//Log( oSettings );
			var filter = '';
			var pass  = '';

			if($('.mts_icheck').length > 0){
				
				/*    dataTable aData Legend:
						case '0' : name = 'Type of boat'; type = 'str'; break;  case '1' : name = 'No of double cabins'; type = 'int'; break;   case '2' : name = 'No of single cabins'; type = 'int'; break; 
						case '3' : name = 'Built in'; type = 'int'; break; case '4' : name = 'Brand'; type = 'str'; break; case '5' : name = 'Model of boat'; type = 'str'; break; 
						case '6' : name = 'BLength'; type = 'float'; break; case '7' : name = 'No of berth'; type = 'int'; break; case '8' : name = 'Crewed'; type = 'bool'; break; 
						case '9' : name = 'License needed'; type = 'bool'; break; case '10' : name = 'Price'; type = 'float'; break; case '11' : name = 'Discount'; type = 'float'; break;
				*/
				
				$('.mts_filter_locator').each(function(){
					var index = $(this).attr('data-index');
					var type   = $(this).getType();
					
					switch(index){
						// associate index with aData numeric index, and compare values 
						case 'brand':
						if( aData[ 4 ] && aData[ 4 ] == $(this).val() ){ filter += ( isFiltered( type, index, $(this) ) == true ? '' : 'x'  ) ; }
						break;
						case 'built_in':
						// aData[ 3 ] build in is filtered by sliders 
						break;
						case 'crewed':
						if( aData[ 8 ] && aData[ 8 ] == $(this).val()){ 
							filter += ( isFiltered( type, index, $(this) ) == true ? '' : 'x' ) ; 
							}else{ 
								if($('#mts_filter_crewed_all').is(':checked') ){filter += '';} 
								}
						break;
						case 'discount':
						 //aData[ 11 ] discount is filtered by range slider 
						break;
						case 'blength':
						 //aData[ 6 ] boat length filtered by sliders 
						break;
						case 'license_needed':
						if( aData[ 9 ] && aData[ 9 ] == $(this).val() ){ 
							filter += ( isFiltered( type, index, $(this) ) == true ? '' : 'x') ; 
							}else{ 
								if($('#mts_filter_license_needed_all').is(':checked')  ){filter += '';} 
								}
						break;
						case 'model_of_boat':
						if( aData[ 5 ] && aData[ 5 ] == $(this).val() ) { filter += ( isFiltered( type, index, $(this) ) == true ? '' : 'x'  ) ; }
						break;
						case 'no_of_berth':
						// aData[ 7 ] number of berths is filtered by sliders //
						break;
						case 'no_of_double_cabins':
						// aData[ 1 ] number of double cabins is filtered by sliders //
						break;
						case 'no_of_single_cabins':
						// aData[ 2 ] no of single cabins is filtered by sliders //
						break;
						case 'price':
						// aData[ 10 ] price is filtered by sliders 
						break;
						case 'type_of_boat':
						if( aData[ 0 ] && aData[ 0 ] == $(this).val() ){ filter += ( isFiltered( type, index, $(this) ) == true ? '' : 'x'  ) ; }
						break;
						case 'location':
						if( aData[ 12 ] && aData[ 12 ] == $(this).val() ){ filter += ( isFiltered( type, index, $(this) ) == true ? '' : 'x'  ) ; }
						break;
					}
					
				});
				
			}
			
			/* sliders */
			if( window._filters && window._filters[ 'data' ]){
				/* price slider */
				if( aData[ 10 ] && window._filters.data.price[ 'current' ] && window._filters.data.price[ 'current' ].length == 2  ){
					filter += parseFloat(aData[10]) > window._filters.data.price[ 'current' ][0] &&  parseFloat(aData[10]) < window._filters.data.price[ 'current' ][1] ? '' : 'x' ;
				}
				/* discount sloder */
				if( aData[ 11 ] && window._filters.data.discount[ 'current' ] && window._filters.data.discount[ 'current' ].length == 2  ){
					filter += parseFloat(aData[11]) > window._filters.data.discount[ 'current' ][0] &&  parseFloat(aData[11]) < window._filters.data.discount[ 'current' ][1] ? '' : 'x' ;
				}
				/* boat length slider */
				if( aData[ 6 ] && window._filters.data.blength[ 'current' ] && window._filters.data.blength[ 'current' ].length == 2  ){
					filter += parseFloat(aData[6]) > window._filters.data.blength[ 'current' ][0] &&  parseFloat(aData[6]) < window._filters.data.blength[ 'current' ][1] ? '' : 'x' ;
				}
					/* berths slider */
				if( aData[ 7 ] && window._filters.data.no_of_berth[ 'current' ] && window._filters.data.no_of_berth[ 'current' ].length == 2  ){
					filter += parseInt(aData[7]) > window._filters.data.no_of_berth[ 'current' ][0] &&  parseInt(aData[7]) < window._filters.data.no_of_berth[ 'current' ][1] ? '' : 'x' ;
				}
				/* no of double cabins slider */
				if( aData[ 1 ] && window._filters.data.no_of_double_cabins[ 'current' ] && window._filters.data.no_of_double_cabins[ 'current' ].length == 2  ){
					filter += parseInt(aData[1]) > window._filters.data.no_of_double_cabins[ 'current' ][0] &&  parseInt(aData[1]) < window._filters.data.no_of_double_cabins[ 'current' ][1] ? '' : 'x' ;
				}
				/* no of single cabins slider */
				if( aData[ 2 ] && window._filters.data.no_of_single_cabins[ 'current' ] && window._filters.data.no_of_single_cabins[ 'current' ].length == 2  ){
					filter += parseInt(aData[2]) > window._filters.data.no_of_single_cabins[ 'current' ][0] &&  parseInt(aData[2]) < window._filters.data.no_of_single_cabins[ 'current' ][1] ? '' : 'x' ;
				}
				/* build year slider */
				if( aData[ 3 ] && window._filters.data.built_in[ 'current' ] && window._filters.data.built_in[ 'current' ].length == 2  ){
					filter += parseInt(aData[3]) > window._filters.data.built_in[ 'current' ][0] &&  parseInt(aData[3]) < window._filters.data.built_in[ 'current' ][1] ? '' : 'x' ;
				}
			}
			
			
			return filter == '' ? true : false ;
			
		});
		
		function isFiltered(type, name, me ){
			var filter  =  true;
			
			switch( type ){
				case 'checkbox':
				
				/* if all unchecked return but one checked and is this one, return true. */
				if(me.is(":checked")){
					
					filter = true;
					
				}else{
					var pass = '';
					if( $('.mts_filter_' + name).length > 0 ){
						$('.mts_filter_' + name).each(function(){
							pass += $(this).is(":checked") ? 'x' : '';
						});
						/* if all checkboxes of checked filter are un checked, don;t filter. */
						return pass == '' ? true : false ;
					}
					filter = false
				}
				
				break;
				case 'radio':
				
					/* if all unchecked return but one checked and is this one, return true. */
					if(me.is(":checked")){
						
						filter = true;
						
					}else{
						
						filter =$('#mts_filter_' + name + '_all').is(":checked") ? true : false ;
				}

				break;
				case 'option':
					//Log( me.val() );
					/* if all unchecked return but one checked and is this one, return true. */
					if(me.is(":selected")){
						
						filter = true;
						
					}else{
						
						filter =$('#mts_filter_' + name + '_all').is(":selected") ? true : false ;
				}

				break;
			}

			return filter;
		}
		
		/* flow step 1 */
		/* init results table */
		window.dttbl =  jQuery('#mts_res_table').dataTable({
			 'sPaginationType': 'full_numbers',
			/* setup columns */
			"aoColumns" : [ 
				{"bVisible":false,  "sType":'natural', "iDataSort": 0},/*@_f  Type of boat 0 str */
				{"bVisible":false,  "sType":'natural', "iDataSort": 1},/*@_f  No of double cabins 1 int */
				{"bVisible":false,  "sType":'natural', "iDataSort": 2},/*@_f  No of single cabins 2 int */
				{"bVisible":false,  "sType":'natural', "iDataSort": 3},/*@_f  Built in 3 int */
				{"bVisible":false,  "sType":'natural', "iDataSort": 4},/*@_f  Brand 4 str */
				{"bVisible":false, "sType":'natural', "iDataSort": 5},/*@_f  Model of boat 5 str */
				{"bVisible":false, "sType":'natural', "iDataSort": 6},/*@_f B Length 6 float */
				{"bVisible":false, "sType":'natural', "iDataSort": 7},/*@_f  No of berth 7 int */
				{"bVisible":false, "sType":'natural', "iDataSort": 8},/*@_f  Crewed 8 bool (int) */
				{"bVisible":false, "sType":'natural', "iDataSort": 9},/*@_f  License needed 9 bool (int) */
				{"bVisible":false, "sType":'natural', "iDataSort": 10},/*@_f  Price 10 float */
				{"bVisible":false, "sType":'natural', "iDataSort": 11},/*@_f  Discount 11 float */
				{"bVisible":false, "sType":'natural', "iDataSort": 11},/*@_f  Location 12 str */
				{"bSortable":false, "bSearchable":true}
			 ] 
			
		});
		/* end flow step 1 */
	
		
		window._filters = {
			/* filters object */
			data:{},
			data_count:{},
			/* filter data holder */
			add_filter : function(name, type, value){
				/* add data in filter holder obj named - name, with type [str, int, float, bool] , with value*/
				var pname = name.toString().toLowerCase().replace(/\s+/g, '_');
				if( !this.data[ pname ] ){
					  this.data[ pname ] = [];
				}
				var el =  this.format_value( value, type ) ;
				if(!this.data_count[ pname ]){
				     this.data_count[ pname ] = {};
				}
				if( inArray( el, this.data[ pname ] ) == false && el != 'NaN' && el != NaN ){
					/* if does not exits add it */
					this.data[ pname ].push( el );
					this.data_count[ pname ][ el ] = 1;
				}else{
					/* else count it */
					this.data_count[ pname ][ el ]++;
				}
					
			},
			format_value : function( value, type){
				/* make sure the data is in the same format */
				return type == 'str' ? value : (  type == 'int' ? parseInt(value) : ( type == 'float' ? parseFloat(value) : ( type == 'bool' ? (value == '' ? 'No' : value ) : value )  )  );
			},
			get_value : function(name, index){
				/* get a specific value from data obj by filter name and index */
				return name in this.data ? ( index in this.data[ name ]  ? this.data[ name ][ index ]   : false ) : false ;
			}
			
		};
		
		/* extract filters data */
		function extract_filters_data(data){
			'use strict';
			for ( var i in data ){
				/* parse datatable data in strict mode, and generalize and add it in _filters */
				for ( var j in data[i]  ){
					var name = false, type = false;
					switch( j ){ 
						case '0' : name = 'Type of boat'; type = 'str'; break;  case '1' : name = 'No of double cabins'; type = 'int'; break;   case '2' : name = 'No of single cabins'; type = 'int'; break; 
						case '3' : name = 'Built in'; type = 'int'; break; case '4' : name = 'Brand'; type = 'str'; break; case '5' : name = 'Model of boat'; type = 'str'; break; 
						case '6' : name = 'BLength'; type = 'float'; break; case '7' : name = 'No of berth'; type = 'int'; break; case '8' : name = 'Crewed'; type = 'bool'; break; 
						case '9' : name = 'License needed'; type = 'bool'; break; case '10' : name = 'Price'; type = 'float'; break; case '11' : name = 'Discount'; type = 'float'; break;
						case '12': name = 'Location'; type='str';break
						}
					if( name != false ){ window._filters.add_filter( name, type, data[i][j] ); }
				}
			}
		}	
		
		/* build filters widget */
		function build_widgets(data,widgets){
			
			var html  = [];
			
			for( var i in data ){
				/* parse array data */
				
				switch(i){
					/* build specific html for each filter if in specified widgets array and if length of the data is greather than 1 */
					case 'brand':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] += '<div class="mts_filter_widget" ><p class="mts_widget_header" >Brand</p>';
						
						var count = 0;
						for( var j in data[i] ){
							count++;
							html[ i ] += '<div class="mts_ckbx_holder" ><input type="checkbox" data-index="'+ i +'"  id="mts_filter_brand['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator mts_icheck mts_filter_brand" /><label for="mts_filter_brand['+ count +']" > '+ data[i][j] + ( i in window._filters.data_count && data[i][j] in window._filters.data_count[i]  ? ' <span class="mts_filter_count" >('+ window._filters.data_count[ i ] [ data[i][j] ]+')</span>' : '' ) + '</label></div>';
							
						}
						html[ i ] += '</div>';
					}
					break;
					case 'built_in':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_built_in" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by build year:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-built_in" id="mts_filter_built_in_from" ></span> To <span class="str-slider-built_in" id="mts_filter_built_in_to" ></span></p>'	
						html[ i ]+= '<div id="slider_built_in" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						window._filters.data[i].sort();
					}
					break;
					case 'crewed':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] += '<div class="mts_filter_widget" ><p class="mts_widget_header" >Crewed</p>';
						
						var count = 0;
						html[ i ] += '<div class="mts_radio_holder" ><input type="radio" checked="checked" name="'+ i +'" data-index="'+ i +'"  id="mts_filter_crewed_all" value="all" class="mts_filter_locator mts_icheck mts_filter_crewed" /><label for="mts_filter_crewed_all" >All</label></div>';
						for( var j in data[i] ){
							count++;
							html[ i ] += '<div class="mts_radio_holder" ><input type="radio" name="'+ i +'" data-index="'+ i +'"  id="mts_filter_crewed['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator mts_icheck mts_filter_crewed" /><label for="mts_filter_crewed['+ count +']" > '+ data[i][j] + '</label></div>';
							
						}
						html[ i ] += '</div>';
					}
					break;
					case 'discount':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_discount" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by Discount:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-discount" id="mts_filter_discount_from" ></span> To <span class="str-slider-price" id="mts_filter_discount_to" ></span></p>'	
						html[ i ]+= '<div id="slider-discount" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						window._filters.data[i].sort( function(a, b){ return parseFloat(a) - parseFloat(b); } );
						
					}
					break;
					case 'blength':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_blength" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by boat length:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-blength" id="mts_filter_blength_from" ></span> To <span class="str-slider-blength" id="mts_filter_blength_to" ></span></p>'	
						html[ i ]+= '<div id="slider-blength" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						window._filters.data[i].sort();
					}
					break;
					case 'license_needed':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] += '<div class="mts_filter_widget" ><p class="mts_widget_header" >License needed</p>';
						
						var count = 0;
						html[ i ] += '<div class="mts_radio_holder" ><input type="radio" checked="checked" name="'+ i +'" data-index="'+ i +'"  id="mts_filter_license_needed_all" value="all" class="mts_filter_locator mts_icheck mts_filter_license_needed" /><label for="mts_filter_license_needed_all" >All</label></div>';
						for( var j in data[i] ){
							count++;
							html[ i ] += '<div class="mts_radio_holder" ><input type="radio" name="'+ i +'" data-index="'+ i +'"  id="mts_filter_license_needed['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator mts_icheck mts_filter_license_needed" /><label for="mts_filter_license_needed['+ count +']" > '+ data[i][j] + '</label></div>';
							
						}
						html[ i ] += '</div>';
					}
					break;
					case 'model_of_boat':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] += '<div class="mts_filter_widget" ><p class="mts_widget_header" >Model of Boat</p>';
						
						var count = 0;
						//for( var j in data[i] ){
						//	count++;
						//	html[ i ] += '<div class="mts_ckbx_holder" ><input type="checkbox" data-index="'+ i +'"  id="mts_filter_model_of_boat['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator mts_icheck mts_filter_model_of_boat" /><label for="mts_filter_model_of_boat['+ count +']" > '+ data[i][j] + ( i in window._filters.data_count && data[i][j] in window._filters.data_count[i]  ? ' <span class="mts_filter_count" >('+ window._filters.data_count[ i ][ data[i][j] ] +')</span>' : '' ) + '</label></div>';
						//	
						//}
						html[ i ] += '<div class="mts_select_holder" ><select id="mts_filter_model_of_boat['+ count +']" >';
						
						html[ i ] += '<option data-index="'+ i +'"  id="mts_filter_' + i + '_all"  class="mts_filter_locator  mts_filter_model_of_boat" > All Boat Models </option>';
						for( var j in data[i] ){
							count++;
							html[ i ] += '<option data-index="'+ i +'"  id="mts_filter_model_of_boat['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator  mts_filter_model_of_boat" >'+ data[i][j] + ( i in window._filters.data_count && data[i][j] in window._filters.data_count[i]  ? ' ('+ window._filters.data_count[ i ][ data[i][j] ] +')' : '' ) + '</option>';
							
						}
						html[ i ] += '</select></div></div>';
					}
					break;
					case 'no_of_berth':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_no_of_berth" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by No. of Berth:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-no_of_berth" id="mts_filter_no_of_berth_from" ></span> To <span class="str-slider-no_of_berth" id="mts_filter_no_of_berth_to" ></span></p>'	
						html[ i ]+= '<div id="slider_no_of_berth" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						//window._filters.data[i].sort();
					}
					break;
					case 'no_of_double_cabins':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_no_of_double_cabins" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by No. of Double Cabins:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-no_of_double_cabins" id="mts_filter_no_of_double_cabins_from" ></span> To <span class="str-slider-no_of_double_cabins" id="mts_filter_no_of_double_cabins_to" ></span></p>'	
						html[ i ]+= '<div id="slider_no_of_double_cabins" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						window._filters.data[i].sort();
					}
					break;
					case 'no_of_single_cabins':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_no_of_single_cabins" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by No. of Single Cabins:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-no_of_single_cabins" id="mts_filter_no_of_single_cabins_from" ></span> To <span class="str-slider-no_of_single_cabins" id="mts_filter_no_of_single_cabins_to" ></span></p>'	
						html[ i ]+= '<div id="slider_no_of_single_cabins" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						window._filters.data[i].sort();
					}
					break;
					case 'price':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] = '<div class="mts_filter_widget mts_filter_price" >';
						html[ i ]+= '<p class="mts_widget_header" >Filter by price range:</p>'	;
						html[ i ]+= '<p class="sld-top-legend" >From <span class="str-slider-price" id="mts_filter_price_from" ></span> To <span class="str-slider-price" id="mts_filter_price_to" ></span></p>'	
						html[ i ]+= '<div id="slider-price" style="width:90%;margin: 0 auto;" ></div>'
						html[ i ]+= '</div>';	
						/* because settings will be applied on binding events, let's set and sort data right now */
						window._filters.data[i].sort();
						
					}
					break;
					case 'type_of_boat':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] += '<div class="mts_filter_widget" ><p class="mts_widget_header" >Type of Boat</p>';
						
						var count = 0;
						for( var j in data[i] ){
							count++;
							html[ i ] += '<div class="mts_ckbx_holder" ><input type="checkbox" data-index="'+ i +'"  id="mts_filter_type_of_boat['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator mts_icheck mts_filter_type_of_boat" /><label for="mts_filter_type_of_boat['+ count +']" > '+ data[i][j] + ( i in window._filters.data_count && data[i][j]  in window._filters.data_count[i]  ? ' <span class="mts_filter_count" >('+ window._filters.data_count[ i ][ data[i][j] ] +')</span>' : '' ) + '</label></div>';
							
						}
						html[ i ] += '</div>';
					}
					break;
					case 'location':
					if(inArray( i, widgets ) == true && data[i].length > debug_count_data){
						html[ i ]   = '';
						html[ i ] += '<div class="mts_filter_widget" ><p class="mts_widget_header" >Location:</p>';
						
						var count = 0;
						//for( var j in data[i] ){
						//	count++;
						//	html[ i ] += '<div class="mts_ckbx_holder" ><input type="checkbox" data-index="'+ i +'"  id="mts_filter_model_of_boat['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator mts_icheck mts_filter_model_of_boat" /><label for="mts_filter_model_of_boat['+ count +']" > '+ data[i][j] + ( i in window._filters.data_count && data[i][j] in window._filters.data_count[i]  ? ' <span class="mts_filter_count" >('+ window._filters.data_count[ i ][ data[i][j] ] +')</span>' : '' ) + '</label></div>';
						//	
						//}
						html[ i ] += '<div class="mts_select_holder" ><select id="mts_filter_location['+ count +']" >';
						
						html[ i ] += '<option data-index="'+ i +'"  id="mts_filter_' + i + '_all"  class="mts_filter_locator  mts_filter_location" > All Locations </option>';
						for( var j in data[i] ){
							count++;
							html[ i ] += '<option data-index="'+ i +'"  id="mts_filter_location['+ count +']" value="'+ data[i][j] +'" class="mts_filter_locator  mts_filter_location" >'+ data[i][j] + ( i in window._filters.data_count && data[i][j] in window._filters.data_count[i]  ? ' ('+ window._filters.data_count[ i ][ data[i][j] ] +')' : '' ) + '</option>';
							
						}
						html[ i ] += '</select></div></div>';
					}
					break;
				}
				
				
			}
			
			var sorted = {};
			/* sort widgets appearance by input widgets array */
			for( var x= 0; x < widgets.length; x++ ){
				if( html[ widgets[x] ]){
					sorted[ widgets[x] ] = html[ widgets[x] ] ;
				}	
			}
			return  sorted ;
		}
		
		/* flow step 2 */
		extract_filters_data( dttbl.fnGetData() );
		
		Log( window._filters );
		
		var mfsorm = '<h3 class="refine_search" >Refine your Search</h3><div class="mts_filter_widget" ><p class="mts_widget_header" >Change trip dates<p><div class="change_input_dates_holder" ><input type="text" id="mts_date_fromSS" value="'+ $('#parsed_from').val() +'"  /><input type="text" id="mts_date_toSS" value="'+ $('#parsed_to').val() +'"  /><p class="mts_search_error error" style="display: none;" >Please select a destination</p><p class="mts_search_date_error error" style="display: none;" >Please select return date. If both date fields are empty we will search for all boats available in the next 7 days</p><a  href="javascript:void(0)" id="chtripdate" >Change</a></div></div>';
		
		var html = '<section id="mts_all_widgets" class="widget advanced_text" >' + mfsorm +  joinAssoc( build_widgets( window._filters.data, all_widgets ) ) + '</section>';
		var parent = $('.sidebar-wrapper');
		
		var sorting_filters = '<span class="name_sorting">Sort by: </span>';
		if( window._filters.data.price && window._filters.data.price.length > debug_count_data ){
			sorting_filters += '<a id="sort_by_price" data-sort-column="10" class="mts_external_s sorting" href="javascript:void(0);" >Price</a>  ';
		}
		if( window._filters.data.discount && window._filters.data.discount.length > debug_count_data ){
			sorting_filters += '<a id="sort_by_discount" data-sort-column="11" class="mts_external_s sorting" href="javascript:void(0);" >Discount</a>  ';
		}
		if( window._filters.data.blength && window._filters.data.blength.length > debug_count_data ){
			sorting_filters += '<a id="sort_by_length" data-sort-column="6" class="mts_external_s sorting" href="javascript:void(0);" >Boat Length</a>  ';
		}
		if( window._filters.data.built_in && window._filters.data.built_in.length > debug_count_data ){
			sorting_filters += '<a id="sort_by_year" data-sort-column="3" class="mts_external_s sorting" href="javascript:void(0);" >Build year</a> ';
		}
		
		$('#push_sorting_filters').html( sorting_filters );
		
		
		
		parent.prepend(html);
		
		/* after filters are built, bind the events */
		
		/*window.fromDateSS = $('#mts_date_fromSS').datepicker({
				onSelect: function(selectedDate) {
					window.toDateSS.datepicker('option', 'minDate', $(this).datepicker('getDate') || 0) ;
					var plus7 = $(this).datepicker('getDate') ;
					//Log( plus7 + ' -d?' );
					var plus7e = plus7.setDate( plus7.getDate() + 7 );
					//Log( plus7e );
					window.toDateSS.datepicker( 'setDate' , new Date(plus7e) );
					},
				onClose: function(e, u){
						window.toDateSS.datepicker('show');
					},
				dateFormat: 'dd.mm.yy',minDate:0 }
				);
		    window.toDateSS      =  $('#mts_date_toSS').datepicker({onSelect: function(selectedDate) { window.fromDateSS.datepicker('option', 'maxDate', $(this).datepicker('getDate'));}, dateFormat: 'dd.mm.yy',minDate:0});
		
		$('#chtripdate').click(function(e){
				e.preventDefault();// in case it will be transformed in a link 
				var url = 'http://sailchecker.com/';
				if( $('#mts_date_fromSS').val() == '' ){
					$('.mts_search_error').fadeIn();
					return false;
				}else{
					url += 'test_search/?dst=' + $('#parsed_dest').val();

					$('.mts_search_error').fadeOut;

					// trigger another error if date from is filled and date to is not 

					

					if( ( $('#mts_date_fromSS').val() != ''  && $('#mts_date_toSS').val() == '' ) || ( $('#mts_date_fromSS').val() == ''  && $('#mts_date_toSS').val() != '') ){

						$('.mts_search_date_error').fadeIn();

						return false;

					}

					if( $('#mts_date_fromSS').val() != ''  && $('#mts_date_toSS').val() != '' ){

						url += '&date_from=' + $('#mts_date_fromSS').val() + '&date_to=' + $('#mts_date_toSS').val() ;

					}



					window.location = url;
				}
			});*/
			
		/* checkboxes, radios */
		$('.mts_icheck').iCheck({ checkboxClass: 'icheckbox_minimal-purple', radioClass: 'iradio_minimal-purple'} );
		$('.mts_icheck').on('ifToggled', function(event){ window.dttbl.fnDraw();});
		/* selects */
		$('.mts_select_holder select').change(function(event){ window.dttbl.fnDraw();  });
		/* sliders */
		if(window._filters.data){
			
			if( window._filters.data.price && window._filters.data.price.length > 1  ){
				
				window._filters.data.price.sort(function(a, b){return a-b});
				
				
				/* only if defined  */
				var currency = '\u20AC';
				$('#slider-price').slider({
					  range: true,
				      min: parseFloat( window._filters.data.price[0] ),
				      max: parseFloat( window._filters.data.price[  ( window._filters.data.price.length -1 ) ] ),
				      values: [ parseFloat( window._filters.data.price[0] ),  parseFloat( window._filters.data.price[  ( window._filters.data.price.length -1 ) ] ) ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_price_from').text( ui.values[ 0 ]  + '  ' + currency  );		
				      		$('#mts_filter_price_to').text( ui.values[ 1 ]  + '  ' + currency  );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.price[ 'current' ] ){ window._filters.data.price[ 'current' ] = []; }
				      	window._filters.data.price[ 'current' ][0] = parseFloat(ui.values[ 0 ]) - 1 ;
				      	window._filters.data.price[ 'current' ][1] = parseFloat(ui.values[ 1 ]) +1 ;	
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_price_from').text(  $( "#slider-price" ).slider( "values", 0 ) + '  ' + currency );
				$('#mts_filter_price_to').text(  $( "#slider-price" ).slider( "values", 1 ) + '  ' + currency );
			}
			
			if( window._filters.data.discount && window._filters.data.discount.length > 1  ){
				
			
				
				
				/* only if defined  */
				var currency = '\u20AC';
				$('#slider-discount').slider({
					  range: true,
				      min: parseFloat( window._filters.data.discount[0] ),
				      max: parseFloat( window._filters.data.discount[  ( window._filters.data.discount.length -1 ) ] ),
				      values: [ parseFloat( window._filters.data.discount[0] ),  parseFloat( window._filters.data.discount[  ( window._filters.data.discount.length -1 ) ] ) ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_discount_from').text( ui.values[ 0 ]  + '  ' + '%'  );		
				      		$('#mts_filter_discount_to').text( ui.values[ 1 ]  + '  ' + '%'  );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.discount[ 'current' ] ){ window._filters.data.discount[ 'current' ] = []; }
				      	window._filters.data.discount[ 'current' ][0] = parseFloat(ui.values[ 0 ]) - 1 ;
				      	window._filters.data.discount[ 'current' ][1] = parseFloat(ui.values[ 1 ]) +1 ;	
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_discount_from').text(  $( "#slider-discount" ).slider( "values", 0 ) + '  ' + '%' );
				$('#mts_filter_discount_to').text(  $( "#slider-discount" ).slider( "values", 1 ) + '  ' + '%' );
			}
			
			if( window._filters.data.blength && window._filters.data.blength.length > 1  ){
				/* only if defined  */
				
				window._filters.data.blength.sort(function(a, b){return parseFloat(a) - parseFloat(b) });
				
				//console.log( window._filters.data.blength )
					
				$('#slider-blength').slider({
					  range: true,
					  step : 0.01,
				      min: parseFloat( window._filters.data.blength[0] ) == 0 ? parseFloat( window._filters.data.blength[1] ) : parseFloat( window._filters.data.blength[0] ) ,
				      max: parseFloat( window._filters.data.blength[  ( window._filters.data.blength.length -1 ) ] ),
				      values: [ parseFloat( window._filters.data.blength[0] ),  parseFloat( window._filters.data.blength[  ( window._filters.data.blength.length -1 ) ] ) ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_blength_from').text( ui.values[ 0 ]  + '  ' + 'm'  );		
				      		$('#mts_filter_blength_to').text( ui.values[ 1 ]  + '  ' + 'm'  );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.blength[ 'current' ] ){ window._filters.data.blength[ 'current' ] = []; }
				      	window._filters.data.blength[ 'current' ][0] = parseFloat(ui.values[ 0 ]) - 0.01;
				      	window._filters.data.blength[ 'current' ][1] = parseFloat(ui.values[ 1 ]) + 0.01 ;	
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_blength_from').text(  $( "#slider-blength" ).slider( "values", 0 ) + '  ' + 'm' );
				$('#mts_filter_blength_to').text(  $( "#slider-blength" ).slider( "values", 1 ) + '  ' + 'm' );
			}
			
			if(  window._filters.data.no_of_berth && window._filters.data.no_of_berth.length > 1  ){
				/* only if defined  */
				Log(window._filters.data.no_of_berth)
				window._filters.data.no_of_berth.sort(function(a, b){return parseInt(a)  - parseInt(b); });
				Log(window._filters.data.no_of_berth)
				$('#slider_no_of_berth').slider({
					  range: true,
				      min:  window._filters.data.no_of_berth[0] ,
				      max: window._filters.data.no_of_berth[  ( window._filters.data.no_of_berth.length -1 ) ] ,
				      values: [  window._filters.data.no_of_berth[0] ,   window._filters.data.no_of_berth[  ( window._filters.data.no_of_berth.length -1 ) ]  ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_no_of_berth_from').text( ui.values[ 0 ]   );		
				      		$('#mts_filter_no_of_berth_to').text( ui.values[ 1 ]    );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.no_of_berth[ 'current' ] ){ window._filters.data.no_of_berth[ 'current' ] = []; }
				      	window._filters.data.no_of_berth[ 'current' ][0] = parseInt(ui.values[ 0 ]) -1;
				      	window._filters.data.no_of_berth[ 'current' ][1] = parseInt(ui.values[ 1 ]) + 1 ;	
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_no_of_berth_from').text(  $( "#slider_no_of_berth" ).slider( "values", 0 )  );
				$('#mts_filter_no_of_berth_to').text(  $( "#slider_no_of_berth" ).slider( "values", 1 ) );
			}
			
			if( window._filters.data.no_of_single_cabins && window._filters.data.no_of_single_cabins.length > 1  ){
				/* only if defined  */
				
				window._filters.data.no_of_single_cabins.sort(function(a, b){return a-b});
				
				$('#slider_no_of_single_cabins').slider({
					  range: true,
				      min:  window._filters.data.no_of_single_cabins[0] ,
				      max:  window._filters.data.no_of_single_cabins[  ( window._filters.data.no_of_single_cabins.length -1 ) ] ,
				      values: [ window._filters.data.no_of_single_cabins[0] , window._filters.data.no_of_single_cabins[  ( window._filters.data.no_of_single_cabins.length -1 ) ]  ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_no_of_single_cabins_from').text( ui.values[ 0 ]  );		
				      		$('#mts_filter_no_of_single_cabins_to').text( ui.values[ 1 ]   );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.no_of_single_cabins[ 'current' ] ){ window._filters.data.no_of_single_cabins[ 'current' ] = []; }
				      	window._filters.data.no_of_single_cabins[ 'current' ][0] = parseInt(ui.values[ 0 ]) - 1;
				      	window._filters.data.no_of_single_cabins[ 'current' ][1] = parseInt(ui.values[ 1 ]) + 1 ;	
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_no_of_single_cabins_from').text(  $( "#slider_no_of_single_cabins" ).slider( "values", 0 ) );
				$('#mts_filter_no_of_single_cabins_to').text(  $( "#slider_no_of_single_cabins" ).slider( "values", 1 ) );
			}
			
			if( window._filters.data.no_of_double_cabins && window._filters.data.no_of_double_cabins.length > 1  ){
				/* only if defined  */
				
				window._filters.data.no_of_double_cabins.sort(function(a, b){return a-b});
				
				$('#slider_no_of_double_cabins').slider({
					  range: true,
				      min: parseInt( window._filters.data.no_of_double_cabins[0] ),
				      max: parseInt( window._filters.data.no_of_double_cabins[  ( window._filters.data.no_of_double_cabins.length -1 ) ] ),
				      values: [ parseInt( window._filters.data.no_of_double_cabins[0] ),  parseInt( window._filters.data.no_of_double_cabins[  ( window._filters.data.no_of_double_cabins.length -1 ) ] ) ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_no_of_double_cabins_from').text( ui.values[ 0 ]  );		
				      		$('#mts_filter_no_of_double_cabins_to').text( ui.values[ 1 ]  );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.no_of_double_cabins[ 'current' ] ){ window._filters.data.no_of_double_cabins[ 'current' ] = []; }
				      	window._filters.data.no_of_double_cabins[ 'current' ][0] = parseInt(ui.values[ 0 ]) - 1;
				      	window._filters.data.no_of_double_cabins[ 'current' ][1] = parseInt(ui.values[ 1 ]) + 1 ;	
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_no_of_double_cabins_from').text(  $( "#slider_no_of_double_cabins" ).slider( "values", 0 ) );
				$('#mts_filter_no_of_double_cabins_to').text(  $( "#slider_no_of_double_cabins" ).slider( "values", 1 )  );
			}
			if( window._filters.data.built_in && window._filters.data.built_in.length > 1  ){
				/* only if defined  */
				
				window._filters.data.built_in.sort(function(a, b){return a-b});
				
				$('#slider_built_in').slider({
					  range: true,
				      min: parseInt( window._filters.data.built_in[0] ),
				      max: parseInt( window._filters.data.built_in[  ( window._filters.data.built_in.length -1 ) ] ),
				      values: [ parseInt( window._filters.data.built_in[0] ),  parseInt( window._filters.data.built_in[  ( window._filters.data.built_in.length -1 ) ] ) ],
				      slide: function( event, ui ) {
				      		$('#mts_filter_built_in_from').text( ui.values[ 0 ]  );		
				      		$('#mts_filter_built_in_to').text( ui.values[ 1 ]  );
				      }, 
				      stop:function(event, ui){
		  			 	if(!window._filters.data.built_in[ 'current' ] ){ window._filters.data.built_in[ 'current' ] = []; }
				      	window._filters.data.built_in[ 'current' ][0] = parseInt(ui.values[ 0 ]) - 1;
				      	window._filters.data.built_in[ 'current' ][1] = parseInt(ui.values[ 1 ]) + 1 ;	
				      	Log( window._filters.data.built_in[ 'current' ] )
				      	window.dttbl.fnDraw();
		  				}
				});
				$('#mts_filter_built_in_from').text(  $( "#slider_built_in" ).slider( "values", 0 )  );
				$('#mts_filter_built_in_to').text(  $( "#slider_built_in" ).slider( "values", 1 ) );
			}
		}
		
		
		
		/* exteranl sorting */
		$('#mts_res_table').on('click','.mts_external_s', function(e){
			
			if($(this).hasClass('sorting')){
				/* sort asc on data-index attr */
				$(this).attr('class', 'sorting_asc mts_external_s');
				window.dttbl.fnSort([ [  parseInt( $(this).attr('data-sort-column') ) , 'asc' ]  ]);
			}
			else if($(this).hasClass('sorting_asc')){
				/* sort desc */
				$(this).attr('class', 'sorting_desc mts_external_s');
				window.dttbl.fnSort([ [  parseInt( $(this).attr('data-sort-column') ) , 'desc' ] ]);
			}
			else if($(this).hasClass('sorting_desc')){
				/* sort asc */
				$(this).attr('class', 'sorting_asc mts_external_s');
				window.dttbl.fnSort([ [  parseInt( $(this).attr('data-sort-column') ) , 'asc' ] ]);
			}
			
			$('.mts_external_s').not($(this)).removeClass('sorting_desc  sorting_asc').addClass('sorting');
			Log('clicked');
			return false;
		});
		
		/* search term highlight */
		$('#mts_res_table_wrapper').on('keyup', '#mts_res_table_filter input',function(){
			Log( $(this).val() );
			
			if($(this).val().length > 2){
				
	
	
			    var term = $(this).val() ;
			
			    $('.display_result .display_info').each(function(){ 
			    	var src_str = $(this).text();
					term = term.replace(/(\s+)/,"(<[^>]+>)*$1(<[^>]+>)*");
					var pattern = new RegExp("("+term+")", "i");
					src_str = src_str.replace(pattern, "<mark>$1</mark>");
					$(this).html(src_str);
			    });    		
			}else{
				 $('.display_result .display_info').each(function(){ 				
					$(this).html($(this).text());
			    });    
			}
		});
		
		/* end flow step 2 */
		
	
		
	/* end datatable load condition */
	}
    
    
	
    window.fromDateSS = jQuery('#mts_date_fromSS').datepicker({
				onSelect: function(selectedDate) {
					window.toDateSS.datepicker('option', 'minDate', $(this).datepicker('getDate') || 0) ;
					var plus7 = $(this).datepicker('getDate') ;
					//Log( plus7 + ' -d?' );
					var plus7e = plus7.setDate( plus7.getDate() + 7 );
					//Log( plus7e );
					window.toDateSS.datepicker( 'setDate' , new Date(plus7e) );
					},
				onClose: function(e, u){
						window.toDateSS.datepicker('show');
					},
				dateFormat: 'dd.mm.yy',minDate:0 }
				);
	
	
		    window.toDateSS      = jQuery('#mts_date_toSS').datepicker({onSelect: 
            function(selectedDate) { window.fromDate.datepicker('option', 'maxDate', jQuery(this).datepicker('getDate'));}, 
            dateFormat: 'dd.mm.yy',minDate:0});
            
      jQuery('#chtripdate').click(function(e)
                    {
				        e.preventDefault();/* in case it will be transformed in a link */
                        if ($('#search_fields form').length!=0)
                        {
                            var url = $('#search_fields form').attr('action');
                        }
                        
                        if( jQuery('#parsed_dest').val() == 'Where are you looking to charter?' )
                        {
					           return false;
				        }else
                        {
				
                            url += '?action=search&dst=' + jQuery('#parsed_dest').val();
                    if( jQuery('#mts_date_toSS').val() != '')
                    {
                        url += '&date_from=' + jQuery('#mts_date_toSS').val();
					}   
                     if (jQuery('#mts_date_toSS').val() != '' )
                    {
						url +=  '&date_to=' + jQuery('#mts_date_toSS').val() ;
					}
					if( jQuery('#parsed_type').val() != '' && jQuery('#parsed_type').length>0){
						url += '&bt_type=' + jQuery('#parsed_type').val().toString().toLowerCase();
					}
					window.location = url;
                    }
			});
	
});
