var search_timer = '';
var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
var lineChartData = '';
var form_analytics_chart = '';
var ctx;
var chart_options = {
					responsive: true,
					legend: {
							display: false,
							},
					tooltips: {
							//mode: 'x',
							intersect: false,
							}
					};


jQuery('div.updated').remove();
jQuery('.update-nag').remove();
jQuery('div.error').remove();

(function($)
	{
	$(document).ready(function()
		{
		setTimeout(function(){
		$('.tabs_nf li.tab a.active').removeClass('active').trigger('click');
		},500);
		
		
		$(document).on('click','a.global_settings',
		  function()
		   {
			$('#global_settings .tabs_nf li.tab a.active').removeClass('active').trigger('click');
		   }
		  );
		
		
		ctx = document.getElementById("chart_canvas").getContext("2d");
		//v1
		/*form_analytics_chart = new Chart(ctx).Line(lineChartData, {
			responsive: true
		});*/
		//v2
		/* form_analytics_chart.data = lineChartData; // Would update the first dataset's value of 'March' to be 50
			form_analytics_chart.update();
			form_analytics_chart.reset(); */
		form_analytics_chart = new Chart(ctx,{
			type: 'line',
			data: lineChartData,
			options: chart_options
		});
		
			
		jQuery('input[name="current_page"]').val('0')
		jQuery('input[name="table_search_term"]').val('')
		jQuery('.tooltipped').tooltip(
			{
			delay: 50,
			position: 'top',
			html: true
			}
		);
		
		setTimeout(function(){ $('a.iz-first-page').trigger('click') },200);
		
		$('.carousel').carousel();
		$('.materialbox').materialbox();
		$('.tooltipped').tooltip({delay: 50});
		$('.button-collapse').sideNav(
			{
			menuWidth: 300, // Default is 300
			edge: 'right', // Choose the horizontal origin
			closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
			draggable: true // Choose whether you can drag to open on touch screens
			}
		);
        
	 
		$('.modal').modal(
			{
			dismissible: true, // Modal can be dismissed by clicking outside of the modal
			opacity: .8, // Opacity of modal background
			inDuration: 300, // Transition in duration
			outDuration: 200, // Transition out duration (not for bottom modal)
			startingTop: '4%', // Starting top style attribute (not for bottom modal)
			endingTop: '10%', // Ending top style attribute (not for bottom modal)
			ready: function(modal, trigger)
				{ 	// Callback for Modal open. Modal and trigger parameters available.
					// console.log(modal, trigger);
				},
			complete: function() 
				{  
				} // Callback for Modal close
			}
		);
		

		/*$(document).on('click', '.dropdown-content li', function(){
			$('.dropdown-button').dropdown('close');
		});
		*/
		 $('select.material_select').material_select();
	
	
	$(document).on('change','.reporting_controls input[type="checkbox"]',
	  function()
	   {
	   //hideSelectedColumns(jQuery(this));   
	   }
	  );

		    
		//REMOVE UNWANTED STYLESHEETS
			var link_id = '';
			var css_link = '';
			jQuery('head link').each(
				function()
					{
					css_link = jQuery(this);
					link_id = jQuery(this).attr('id');
					jQuery('.unwanted_css_array .unwanted_css').each(
						function()
							{
							if(link_id)
								{
								if(link_id.trim()==jQuery(this).text())
									css_link.attr('href','');
								}
							}
						);
					
					}
				)	
			
			
		
/* SORT INTO APROPRIATE FILES */
	$(document).on('change','select[name="entry_report_id"]',
		function()
			{
			nf_get_records(0,$(this).closest('.database_table').find('.paging_wrapper'),$(this).val());
			}
		);
	$(document).on('change','select[name="form_id"]',
		function()
			{
			nf_get_records(0,$(this).closest('.database_table').find('.paging_wrapper'),'',$(this).val());
			}
		);
	$(document).on('click','a.iz-next-page',
		function()
			{
			
			var get_page = 	 parseInt($(this).closest('.paging_wrapper').find('input[name="current_page"]').val());	
				
			if((get_page+1) >= parseInt($(this).closest('.paging_wrapper').find('span.total-pages').html()))
				 return false;
			
			get_page = get_page+1
			$(this).closest('.paging_wrapper').find('input[name="current_page"]').val(get_page);
			nf_get_records(get_page,$(this).closest('.paging_wrapper'));
			}
		);
	
	$(document).on('blur','input.search_box',
		function()
			{
			if($(this).val()=='')
				$(this).parent().find('.do_search').trigger('click');
			}
		);
	
	$(document).on('keyup','input.search_box',
		function()
			{
			clearTimeout(search_timer);
			var input = $(this);
			var val = input.val();
			search_timer = setTimeout(
				function()
					{ 
					nf_get_records(0,input.closest('.dashboard-box').find('.paging_wrapper'));
					}, 
				400);
			}
		);
	
	$(document).on('click','.do_search',
		function()
			{
			if($(this).closest('.search_box').hasClass('open'))
				{
				$(this).closest('.search_box').removeClass('open');
				//$(this).closest('.search_box').find('input.search_box').val('');
				//nf_get_records(0,$(this).closest('.dashboard-box').find('.paging_wrapper'));
				}
			else
				$(this).closest('.search_box').addClass('open');
			}
		);
	
	$(document).on('click','a.iz-prev-page',
		function()
			{
			var get_page = 	 parseInt($(this).closest('.paging_wrapper').find('input[name="current_page"]').val());	
			if(get_page<=0)
				 return false;
			
			get_page = get_page-1
			$(this).closest('.paging_wrapper').find('input[name="current_page"]').val(get_page);
			nf_get_records(get_page,$(this).closest('.paging_wrapper'));
			}
		);
	$(document).on('click','a.iz-first-page',
		function()
			{
			$(this).closest('.paging_wrapper').find('input[name="current_page"]').val(0);
			nf_get_records(0,$(this).closest('.paging_wrapper'));
			}
		);
		
	$(document).on('click','a.iz-last-page',
		function()
			{
			var get_val = parseInt($(this).closest('.paging_wrapper').find('span.total-pages').html())-1;
			$(this).closest('.paging_wrapper').find('input[name="current_page"]').val(get_val);
			nf_get_records(get_val,$(this).closest('.paging_wrapper'));
			}
		);
	
	$(document).on('click','th a span.sortable-column',
		function()
			{
			jQuery('input[name="orderby"]').val(jQuery(this).attr('data-col-name'));
			
			jQuery('th a').removeClass('asc');
			jQuery('th a').removeClass('desc');
			load_form_entries(jQuery('#form_update_id').text());
			
			if(jQuery(this).attr('data-col-order')=='asc')
				{
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	removeClass('asc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	addClass('desc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a span.sortable-column').attr('data-col-order','desc');
				}
			else
				{
					
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	removeClass('desc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a').	addClass('asc');
				jQuery('th.column-'+ jQuery(this).attr('data-col-name') +' a span.sortable-column').attr('data-col-order','asc');
				}
			jQuery('input[name="order"]').val(jQuery(this).attr('data-col-order'));
			}
		);		
		$(document).on('click', '.save_form_entry',
			function()
				{
				$('form[name="save_form_entry"] input[type="submit"]').trigger('click');
				}
			);
			
		$(document).on('click', '.cancel_save_form_entry',
			function()
				{
				$('.form_record.active').trigger('click');
				}
			);
			
		$(document).on('click', '.print_form_entry',
			function()
				{
				var prtContent = $('form[name="save_form_entry"]').clone();
				prtContent.find('input[type="submit"]').remove();
				prtContent.find('table').removeClass('highlight');
				prtContent.find('.additional_entry_details').removeClass('hidden');
				prtContent.find('table thead').remove();
				var WinPrint = window.open('', '', 'left=0,top=0,width=834,height=900,toolbar=0,scrollbars=0,status=0');
				
				WinPrint.document.write('<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">');
				WinPrint.document.write('<script src="https://use.fontawesome.com/8e6615244b.js"></script>');
				
				WinPrint.document.write('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">');
				
				WinPrint.document.write('<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">');
				WinPrint.document.write('<link rel="stylesheet" type="text/css" href="'+ $('#plugins_url').text() +'nf-admin/css/print.css"/>')
			
				WinPrint.document.write(prtContent.html());
				WinPrint.document.close();
				WinPrint.focus();
				WinPrint.print();
				WinPrint.close();
				}
			);
		
		$(document).on('click','.database_table tbody tr',
			function()
				{
					var row = $(this);
					//row.closest('.database_table').find('tr').removeClass('active');
					if(row.hasClass('active'))
						{
						row.removeClass('active');
						//row.find('input[type="checkbox"]').prop('checked',0);
						}
					else
						{
						row.closest('.database_table').find('tr').removeClass('active');
						row.addClass('active');
						
						//row.find('input[type="checkbox"]').prop('checked',1);
						}
				}
			);
		
		
		$(document).on('click','.report_table_selection .wap_nex_forms.database_table tbody tr',
			function()
				{
					$('.report-loader').removeClass('hidden');
					nf_build_report_table(jQuery(this).attr('id'),'',true);
				}
			);
		$(document).on('click','.run_query',
			function()
				{
				
				var additional_params = []; 
				
				if($('.clause_container .new_clause').length>0)
					{
					$('.clause_container .new_clause').each(
						function()
							{
							additional_params.push(
									{
									column:$(this).find('select[name="column"]').val(),
									operator: $(this).find('select[name="operator"]').val(), 
									value: $(this).find('input[name="column_value"]').val()
									}
								);
							}
						);
					}
				
					
				nf_build_report_table(jQuery(this).attr('id'), additional_params);
				
				}
			);
		
		$(document).on('click','.report_table_container .table_title .btn-floating',
			function()
				{
				
				if($('.report_table_container .reporting_controls').hasClass('is_active'))
					{
					$('.report_table_container .reporting_controls').removeClass('is_active');	
					$('.report_table_container .header_text').removeClass('white_txt');
					$(this).removeClass('open');
					}
				else
					{
					$('.report_table_container .reporting_controls').addClass('is_active');
					$('.report_table_container .header_text').addClass('white_txt');	
					$(this).addClass('open');
					}
				}
			);
		
		
		$(document).on('click','.add_new_where_clause',
			function()
				{
				var clause = $('.clause_replicator').clone();
				clause.removeClass('hidden').removeClass('clause_replicator').addClass('new_clause');
				
				$('.clause_container').append(clause);
				jQuery('.clause_container select.post_ajax_select').material_select();
				}
			);
		$(document).on('click','.remove_where_clause',
			function()
				{
				$(this).closest('.new_clause').remove();
				
				}
			);
		
		
		/*$(document).on('click','.database_table .record-selection label',
			function()
				{
					var row = $(this).closest('tr');
					if(row.hasClass('active'))
						{
						row.removeClass('active');
						row.find('input[type="checkbox"]').prop('checked',0);
						}
					else
						{
						row.addClass('active');
						row.find('input[type="checkbox"]').prop('checked',1);
						}
				}
			);
		
		$(document).on('click','.database_table input[name="check-all"]',
			function()
				{
				if($(this).attr('checked')=='checked')
					{
					$('.record-selection input[type="checkbox"]').prop('checked',1)
					$(this).closest('table').find('.form_record').addClass('active');
					}
				else
					{
					$('.record-selection input[type="checkbox"]').prop('checked',0);
					$(this).closest('table').find('.form_record').removeClass('active');
					}
					
				//console.log($(this).attr('checked'));
				}
			);*/
		$(document).on('click','button.print_to_pdf',
			function()
				{
				var record_id = $(this).attr('id');
				var data =
					{
					action	 						: 'nf_print_to_pdf',
					form_entry_Id					: record_id,
					save							: 1,
					ajax							: 1
					};	
				jQuery.post
					(
					ajaxurl, data, function(response)
						{
							if(response=='not installed')
								$('#pfd_creator_not_installed').modal('open');
							else
								{
								window.open(
								  response,
								  '_blank' // <- This is what makes it open in a new window.
								);
							}
						}
					);
				}
			);
		
		
		$(document).on('click','button.print_report_to_pdf',
			function()
				{
				var data =
					{
					action	 : 'nf_print_report_to_pdf',
					};	
				Materialize.toast('Creating PDF, please wait', 60000, 'loading-pdf');
				jQuery.post
					(
					ajaxurl, data, function(response)
						{
							$('.toast.loading-pdf').remove();
							
							if(response=='not installed')
								$('#pfd_creator_not_installed').modal('open');
							else
								{
								window.open(
								  response,
								  '_blank' // <- This is what makes it open in a new window.
								);
							}
						}
					);
				}
			);
		
		
		$(document).on('click','.wap_nex_forms_entries tbody tr, button.edit_form_entry',
			function()
				{
				if($(this).hasClass('reporting_controls'))
					return;	
					
				var row = $(this);
				$('.form_entry_data').addClass('faded');
				if(!row.hasClass('edit_form_entry'))
					{
					$('.wap_nex_forms_entries tr').removeClass('active');
					$('.form_entry_view .dashboard-box-header .btn').attr('disabled',false);
					}
				row.addClass('active');
				
				var record_id = $(this).attr('id');
				
				$('button.edit_form_entry').attr('id',record_id);
				$('button.print_to_pdf').attr('id',record_id);
				
				var data =
					{
					action	 						: 'nf_populate_form_entry_dashboard',
					form_entry_Id					: record_id,
					edit_entry						: 0
					};	
				if(row.hasClass('edit_form_entry'))
					{
					data.edit_entry = 1;
					$('.form_entry_view .dashboard-box-header .btn').hide();
					$('button.save_button').show();
					}
				else
					{
					$('.form_entry_view .dashboard-box-header .btn').show();
					$('button.save_button').hide();
					}
				jQuery.post
					(
					ajaxurl, data, function(response)
						{	
						$('.form_entry_data').html(response).removeClass('faded');
						$('textarea').trigger('autoresize');
						
						$('.materialboxed').materialbox();
						
						jQuery('form[name="save_form_entry"]').ajaxForm({
							beforeSubmit: function(formData, jqForm, options) {
								
							},
						   success : function(responseText, statusText, xhr, $form) {
							   $('.wap_nex_forms_entries tr#'+record_id).trigger('click');			   
							   Materialize.toast('Saved', 2000, 'toast-success');
							   
							   $('.form_entry_view .dashboard-box-header .btn').show();
							   $('button.save_button').hide();
							},
							 error: function(jqXHR, textStatus, errorThrown)
								{
								   console.log(errorThrown)
								}
							});					
						}
					);
				}
			);
		
		jQuery(document).on('click', '.deactivate_license', function(){
				var data =
						{
						action	:  'deactivate_license' 
						};
					
					jQuery('.deactivate_license').html('<span class="fa fa-spin fa-spinner"></span> Unregistering...')
									
					jQuery.post
						(
						ajaxurl, data, function(response)
							{
							Materialize.toast('Purshase Code unregistered', 2000, 'toast-success')
							setTimeout(function(){ jQuery(location).attr('href',jQuery('#siteurl').text()+'/wp-admin/admin.php?page=nex-forms-dashboard'), 3000});
							}
						);
					}
				);
		
		jQuery(document).on('click', '.verify_purchase_code', function(){
		var data =
				{
				action	:  'get_data' ,
				eu		:	jQuery('#envato_username').val(),
				pc		:	jQuery('#purchase_code').val()
				};
			
			
			jQuery('.verify_purchase_code').html('<span class="fa fa-spin fa-spinner"></span> Verifying')
							
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					if(strstr(response, 'License Activated'))
						{
						Materialize.toast('Purchase code Registered!', 2000, 'toast-success')
						setTimeout(function(){ jQuery(location).attr('href',jQuery('#siteurl').text()+'/wp-admin/admin.php?page=nex-forms-dashboard'), 3000});
						}
					else
						jQuery('.show_code_response').html(response);
					jQuery('.verify_purchase_code').html('Register');
					}
				);
			}
		);
		
		$(document).on('click','.delete-record',
			function()
				{
				var row = $(this).closest('tr');
				row.css('background','#f3989b');
				var result = confirm("Confirm delete?");
				var table = $(this).attr('data-table');
				var record_id = $(this).attr('id');
				if (result) 
					{
					if($(this).closest('.database_table').hasClass('wap_nex_forms_temp_report'))
						{
						var data =
							{
							action	 						: 'nf_delete_record',
							table							: 'wap_nex_forms_entries ',
							Id								: row.find('td.entry_Id').text()
							};	
						jQuery.post(ajaxurl, data, function(response){});
						}
					
					if($(this).closest('.database_table').hasClass('file_manager'))
						{
						var data =
							{
							action	 						: 'nf_delete_file',
							table							: table,
							Id								: record_id
							};	
						jQuery.post(ajaxurl, data, function(response){});
						}
					
					var data =
						{
						action	 						: 'nf_delete_record',
						table							: table,
						Id								: record_id
						};	
					jQuery.post
						(
						ajaxurl, data, function(response)
							{		
							row.fadeOut('fast','',
								function()
									{
									row.remove();
									Materialize.toast('Record Deleted!', 2000, 'toast-success')
									}
								);
							}
						);
					}
				else
					row.css('background','');
				}
			);
		}
	);

	
	$(document).on('click','.switch_chart',
		function()
			{
			$('#chart_canvas').removeClass('hide_chart');
			if($(this).attr('data-chart-type')=='global')
				$('#chart_canvas').addClass('hide_chart');
			
			$('.switch_chart').removeClass('active');
			$(this).addClass('active');
			nf_print_chart($(this).attr('data-chart-type'), $('.database_table.wap_nex_forms tr.active').attr('id'));
			}
		);
	
	$(document).on('change','select[name="stats_per_form"], select[name="stats_per_year"], select[name="stats_per_month"]',
		function()
			{
			nf_print_chart($('.switch_chart.active').attr('data-chart-type'), $('.database_table.wap_nex_forms tr.active').attr('id'));
			}
		);
		
	$(document).on('click','.database_table.chart-selection tr',
		function()
			{
			if($(this).hasClass('active'))
				{
				nf_print_chart($('.switch_chart.active').attr('data-chart-type'), 0);
				}
			else
				nf_print_chart($('.switch_chart.active').attr('data-chart-type'), $(this).attr('id'));
			}
		);
	
	$(document).on('click','.duplicate_record',
		function()
			{
			var elm = jQuery(this);
			var data =
				{
				action	 						: 'nf_duplicate_record',
				table							: 'wap_nex_forms',
				Id								: jQuery(this).attr('id')
				};
				
			jQuery.post
				(
				ajaxurl, data, function(response)
					{
					Materialize.toast('Form Duplicated', 2000, 'toast-success');
					elm.closest('.database_table').find('.first-page').trigger('click');
					}
				);
			}
		);
	

	
	
	
})(jQuery);

function nf_print_chart(chart_type, form_id){
	
	
	//var type = jQuery('.show_line_chart.active').attr('data-chart-type');
	
	var data = 	
		{
		action	 			: 'nf_print_chart',
		ajax	 			: 1,
		form_id				: (form_id) ? form_id : 0,
		year_selected		: jQuery('select[name="stats_per_year"]').val(),
		month_selected 		: jQuery('select[name="stats_per_month"]').val(),
		chart_type			: chart_type
		};
		
	jQuery('.chart-container').addClass('faded');
	
	jQuery.post
		(
		ajaxurl, data, function(response)
			{
			jQuery('.chart-container .data_set').html(response);
			
			form_analytics_chart.destroy();
			jQuery('.chart-container').removeClass('faded');

			/*form_analytics_chart.data = lineChartData; // Would update the first dataset's value of 'March' to be 50
			form_analytics_chart.reset();
			form_analytics_chart.update();
			form_analytics_chart.render()*/
			form_analytics_chart = new Chart(ctx,{
					type: chart_type,
					data: lineChartData,
					options: chart_options
				});
		}
	);	
}



function nf_get_records(page,target,id,form_id){
	
	var show_fields = '';

		/*jQuery('.reporting_controls input[name="showhide_fields[]"]').each(
			function()
				{
				if(jQuery(this).attr('checked')=='checked')
					show_fields += 'checked,';
				else
					show_fields += 'data-checked=false,';
				}
			);*/
		
	
	var data = 	
		{
		action	 			: 'get_table_records',
		page	 			: page,
		do_ajax				: 1,
		additional_params	: target.find('input[name="additional_params"]').val(),
		header_params		: target.find('input[name="header_params"]').val(),
		search_params		: target.find('input[name="search_params"]').val(),
		table				: target.find('input[name="database_table"]').val(),
		search_term			: target.closest('.database_table').find('input[name="table_search_term"]').val(),
		entry_report_id		: (id) ? id : target.closest('.database_table').find('select[name="entry_report_id"]').val(),
		form_id				: (form_id) ? form_id : target.closest('.database_table').find('select[name="form_id"]').val(),
		field_selection		: target.find('input[name="field_selection"]').val(),
		//showhide_fields		: show_fields
		/*order	 			: jQuery('input[name="order"]').val(),
		orderby	 			: jQuery('input[name="orderby"]').val(),
		current_page		: jQuery('input[name="current_page"]').val(),
		additional_params	: jQuery('input[name="additional_params"]').val(),
		form_Id				: form_id*/
		};
	//target.closest('.database_table').find('.database-table-loader').removeClass('hidden');
	target.closest('.database_table').find('.saved_records_container').addClass('faded');
	
	jQuery.post
		(
		ajaxurl, data, function(response)
			{
				
			
				
			target.closest('.database_table').find('tbody.saved_records_container').html(response);
			target.closest('.database_table').find('tbody.saved_records_contianer').html('<tr><td colspan="100"><div class="alert alert-danger">Sorry, you need to activate this plugin to view entry reports. Go to global settings above and follow activation procedure.</strong></td><tr>');
			target.closest('.database_table').find('span.current-page').html(page+1);
			
			
			/*jQuery('.reporting_controls input[name="showhide_fields[]"]').each(
			function()
				{
				//if(jQuery(this).attr('checked')=='checked')
					hideSelectedColumns(jQuery(this));
				}
			);*/
			
			target.closest('.database_table').find('.saved_records_container').removeClass('faded');
			target.closest('.database_table').find('.database-table-loader').addClass('hidden');
			

			var total_records = target.closest('.database_table').find('.total_table_records').text()
			
			var total_pages = Math.floor((parseFloat(total_records)/10)+1);
		
			var output = '';
			
			output += '<span class="displaying-num"><span class="entry-count">'+ parseInt(total_records) +'</span> items </span>';
			
			if(total_pages>1)
				{				
				output += '<span class="pagination-links">';
				output += '<a class="first-page iz-first-page btn waves-effect waves-light"><span class="fa fa-angle-double-left"></span></a>';
				output += '<a title="Go to the next page" class="iz-prev-page btn waves-effect waves-light prev-page"><span class="fa fa-angle-left"></span></a>&nbsp;';
				output += '<span class="paging-input"> ';
				output += '<span class="current-page">'+ (page+1) +'</span> of <span class="total-pages">'+ total_pages +'</span>&nbsp;</span>';
				output += '<a title="Go to the next page" class="iz-next-page btn waves-effect waves-light next-page"><span class="fa fa-angle-right"></span></a>';
				output += '<a title="Go to the last page" class="iz-last-page btn waves-effect waves-light last-page"><span class="fa fa-angle-double-right"></span></a></span>';
				}
			target.closest('.database_table').find('.paging').html(output);	
			jQuery('.tooltipped').tooltip({delay: 50});
			 jQuery('select.material_select').material_select();
			 jQuery('.materialboxed').materialbox();
			jQuery('.dropdown-button').dropdown(
					{
					 belowOrigin: true,	
					}
				);
			}
		);	
}
function hideSelectedColumns(checkbox) {
 
 
 var index = checkbox.val();
 var table = jQuery('.report_table_container table');
  if(checkbox.attr('checked')!='checked')
   {
   table.find('tr').each(
    function()
     {
	if(jQuery(this).hasClass('reporting_controls'))
		return;	
     jQuery(this).children('td:eq('+index+')').hide();
	 jQuery(this).children('th:eq('+index+')').hide();
    
     }
    );
   }
  else
   {
   table.find('tr').each(
    function()
     {
	if(jQuery(this).hasClass('reporting_controls'))
		return;	
     jQuery(this).children('td:eq('+index+')').show();
	 jQuery(this).children('th:eq('+index+')').show();

     }
    );
   }
}

function nf_build_report_table(form_id, additional_params, refresh_data){
	
	var show_fields = [];
	jQuery('.reporting_controls input[name="showhide_fields[]"]').each(
			function()
				{
				if(jQuery(this).attr('checked')=='checked')
					show_fields.push(jQuery(this).attr('value'));
				}
			); 
	if(refresh_data)
		show_fields = '';
	jQuery('.report_table_container').removeClass('hidden');
	jQuery('.report_table_container').find('.database-table-loader').removeClass('hidden');
	jQuery('.report_table_container').find('.dashboard-box-content').addClass('faded');						
	var data =
		{
		action	 						: 'submission_report',
		form_Id							: form_id,
		additional_params				: additional_params,
		field_selection					: (show_fields!='') ? show_fields : '*',
		};	
	
	jQuery.post
		(
		ajaxurl, data, function(response)
			{	
		/*jQuery('.reporting_controls input[name="showhide_fields[]"]').each(
			function()
				{
				//if(jQuery(this).attr('checked')=='checked')
					hideSelectedColumns(jQuery(this));
				}
			);*/	
			jQuery('.report_table').html(response);
			nf_get_records(0,jQuery('.report_table_container .database_table').find('.paging_wrapper'));
			jQuery('.report_table_container .table_title .btn-floating').trigger('click');
			jQuery('.clause_container select.post_ajax_select').material_select();
			}
		);	
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
        