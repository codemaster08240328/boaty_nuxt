<?php
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'wp_ajax_do_upload_image', array('NEXForms_Functions','do_upload_image'));

if(!class_exists('NEXForms_Functions'))
	{
	class NEXForms_Functions{
		
	
	public function code_to_country( $code, $get_list=false ){

    $code = strtoupper($code);

    $countryList = array(
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

	if($get_list)
		return $countryList;

    if( !$countryList[$code] ) return $code;
    else return $countryList[$code];
    }
	
	public function file_get_contents_utf8($fn) {
		 $content = file_get_contents($fn);
		  return mb_convert_encoding($content, 'UTF-8',
			  mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
	}
	

	public function get_geo_location($ipaddress){
		// create curl resource 
			$ch = curl_init(); 
			// set url 
			curl_setopt($ch, CURLOPT_URL, "http://ipinfo.io/{$ipaddress}/json"); 
			//return the transfer as a string 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			// $output contains the output string 
			$output = curl_exec($ch); 
			// close curl resource to free up system resources 
			curl_close($ch);      
				return $output;
		}
			
		public function isJson($string) {
		 json_decode($string);
		 return (json_last_error() === JSON_ERROR_NONE);
		}
		
		public function get_ext($filename) {
			return (($pos = strrpos($filename, '.')) !== false ? substr($filename, $pos+1) : '');
		}
		
		public function format_date($str){
			$datetime = explode(' ',$str);
			$time = explode(':',$datetime[1]);
			$date = explode('/',$datetime[0]);
			return date(get_option('date_format'),mktime('0','0','0',$date[0],$date[1],$date[2]));
		}
		
		public function format_name($str){
			
			$str = trim($str);
			$str = strtolower($str);		
			$str = str_replace('  ',' ',$str);
			$str = str_replace(' ','_',$str);
			$str = str_replace('{{','',$str);
			$str = str_replace('}}','',$str);
			
			if($str=='name')
				$str = '_'.$str;
			
			return trim($str);
		}
		
		public function format_column_name($str){
			
			$str = trim($str);
			$str = strtolower($str);		
			$str = str_replace('  ',' ',$str);
			$str = str_replace(' ','_',$str);
			
			$str = preg_replace('/[^A-Za-z0-9_]/', '', $str);
			
			$utf8 = array(
				'/[áàâãªä]/u'   =>   'a',
				'/[ÁÀÂÃÄ]/u'    =>   'A',
				'/[ÍÌÎÏ]/u'     =>   'I',
				'/[íìîï]/u'     =>   'i',
				'/[éèêë]/u'     =>   'e',
				'/[ÉÈÊË]/u'     =>   'E',
				'/[óòôõºö]/u'   =>   'o',
				'/[ÓÒÔÕÖ]/u'    =>   'O',
				'/[úùûü]/u'     =>   'u',
				'/[ÚÙÛÜ]/u'     =>   'U',
				'/ç/'           =>   'c',
				'/Ç/'           =>   'C',
				'/ñ/'           =>   'n',
				'/Ñ/'           =>   'N',
				'/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
				'/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
				'/[“”«»„]/u'    =>   ' ', // Double quote
				'/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
			);
			
			$str = preg_replace(array_keys($utf8), array_values($utf8), $str);
			
			$colname = substr($str,0,50);
			
			return $colname;
		}
		
		
		public function unformat_name($str){
			
			$str = NEXForms_Functions::format_name($str);
			
			$str = str_replace('u2019','\'',$str);
			$str = str_replace('_',' ',$str);
			$str = ucfirst(trim($str));
			return trim($str);
		}
			
		
		public function get_file_headers($file){
				
			$default_headers = array(			
				'Module Name' 		=> 'Module Name',
				'For Plugin' 		=> 'For Plugin',
				'Module Prefix'		=> 'Module Prefix',
				'Module URI' 		=> 'Module URI',
				'Module Scope' 		=> 'Module Scope',
				
				'Plugin Name' 		=> 'Plugin Name',
				'Plugin TinyMCE' 	=> 'Plugin TinyMCE',
				'Plugin Prefix'		=> 'Plugin Prefix',
				'Plugin URI' 		=> 'Plugin URI',
				'Module Ready' 		=> 'Module Ready',
				
				'Version' 			=> 'Version',
				'Description' 		=> 'Description',
				'Author' 			=> 'Author',
				'AuthorURI' 		=> 'Author URI'
			);
			return get_file_data($file,$default_headers,'module');
		}
		
		public function do_upload_image() {

			foreach($_FILES as $key=>$file)
				{
				$uploadedfile = $_FILES[$key];
				$upload_overrides = array( 'test_form' => false );
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				//
				if ( $movefile )
					{
					//echo "File is valid, and was successfully uploaded.\n";
					if($movefile['file'])
						{
						$set_file_name = str_replace(ABSPATH,'',$movefile['file']);
						$_POST['image_path'] = $movefile['url'];
						$_POST['image_name'] = $file['name'];
						$_POST['image_size'] = $file['size'];
						echo $movefile['url'];
						}
					} 
				}
			
			die();
		}
	
	public function view_excerpt($content,$chars=0){
			$content = strip_tags($content);
			$excerpt = '';
			for($i=0;$i<$chars;$i++){
				$excerpt .= substr($content,$i,1);
			}
			
			if(strlen($content)>$chars)
				{
				$set_excerpt = '<span class="tooltipped" data-position="top" data-delay="50" data-html="true" data-tooltip="'.$content.'">'.$excerpt.'&hellip;</span>';
				}
			else
				{
				$set_excerpt = $excerpt;
				}
			
			return $set_excerpt;
		}
	public function view_excerpt2($content,$chars=0){
			$content = strip_tags($content);
			$excerpt = '';
			for($i=0;$i<$chars;$i++){
				$excerpt .= substr($content,$i,1);
			}
			
			if(strlen($content)>$chars)
				{
				$set_excerpt = $excerpt.'&hellip;';
				}
			else
				{
				$set_excerpt = $excerpt;
				}
			
			return $set_excerpt;
		}
	public function print_preloader($size='big',$color='blue',$hidden=true,$class=''){
			$output = '';
			$output .= '<div class="preload '.$class.' '.(($hidden) ? 'hidden' : '').'">';
				$output .= '<div class="preloader-wrapper '.$size.' active">';
				$output .= '<div class="spinner-layer spinner-'.$color.'-only">';
				$output .= '<div class="circle-clipper left">';
				$output .= '<div class="circle"></div>';
				$output .= '</div><div class="gap-patch">';
				$output .= '<div class="circle"></div>';
				$output .= '</div><div class="circle-clipper right">';
				$output .= '<div class="circle"></div>';
				$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>	';
			
			return $output;
		}
	public function time_elapsed_string($datetime, $full = false) {
		
			if(is_array($datetime))
				$set_date_time = $datetime[0];
					
			$now = new DateTime;
			$ago = new DateTime($set_date_time);
			$diff = $now->diff($ago);

		
			$diff->w = floor($diff->d / 7);
			$diff->d -= $diff->w * 7;
		
			$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
			);
			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}
		
			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}
	
	
	public function run_old_conditional_logic($logic, $unigue_form_Id){
			$rules = explode('[start_rule]',$logic);
		$i=1;
	
	$output = '';
	$print_auto_hide = '';
	$function_post_fix = rand(1,99999999);
	
	$output .= '<script type="text/javascript" name="js_con">
	
	function test_run_nf_conditional_logic'.$function_post_fix.'(){
			';		
		foreach($rules as $rule)
			{
			if($rule)
				{
				$operator =  explode('[operator]',$rule);
				$operator2 =  explode('[end_operator]',$operator[1]);
				$get_operator = trim($operator2[0]);
				
				$get_operator2 = explode('##',$get_operator);
				$rule_operator = $get_operator2[0];
				$reverse_action = $get_operator2[1];
				
				
				if($rule_operator=='any')
					$if_clause = ' || ';
				else
					$if_clause = ' && ';
					
				$conditions =  explode('[conditions]',$rule);
				$conditions2 =  explode('[end_conditions]',$conditions[1]);
				$rule_conditions = trim($conditions2[0]);
	
				$get_conditions =  explode('[new_condition]',$rule_conditions);
				$get_conditions2 =  explode('[end_new_condition]',$get_conditions[1]);
				$get_rule_conditions = trim($get_conditions2[0]);
				
				
				$output .= 'if(';
				
				$query_length = count($get_conditions);
				$i = 0;
				foreach($get_conditions as $set_condition)
					{
					
					$the_condition 		=  explode('[field_condition]',$set_condition);
					$the_condition2 	=  explode('[end_field_condition]',$the_condition[1]);
					$get_the_condition 	=  trim($the_condition2[0]);
					
					$the_value 		=  explode('[value]',$set_condition);
					$the_value2 	=  explode('[end_value]',$the_value[1]);
					$get_the_value 	=  trim($the_value2[0]);
						
					
					$con_field =  explode('[field]',$set_condition);
					$con_field2 =  explode('[end_field]',$con_field[1]);
					$get_con_field = explode('##',$con_field2[0]);;
					
					$con_field_type = $get_con_field[0];
					
					$get_con_field_attr = explode('**',$get_con_field[0]);
					
					$con_field_id	 = $get_con_field_attr[0];
					$con_field_type	 = $get_con_field_attr[1];
					$con_field_name	 = $get_con_field[1];
					
					$set_operator = '==';
					
					if($con_field_type)
						{
						if($get_the_condition=='equal_to')	
							$set_operator = '==';
						elseif($get_the_condition=='not_equal_to')
							$set_operator = '!=';
						elseif($get_the_condition=='less_than')
							$set_operator = '<';
						elseif($get_the_condition=='greater_than')
							$set_operator = '>';
							
						
						if($con_field_type=='radio')	
							$add_string = ':checked';
						elseif($con_field_type=='checkbox')
							$add_string = ':checked';
						else
							$add_string = '';
							
						if (is_numeric($get_the_value)) 
							$set_the_value = '('.$get_the_value.')';
						else
							$set_the_value = '"'.$get_the_value.'"';
							
						
						if($con_field_type=='select')
							{
							$output .= 'jQuery(\'#nf_form_'.$unigue_form_Id.' #'.$con_field_id.'\').find(\'select option:selected\').val()'.$set_operator.''.$set_the_value.' '.(($i<($query_length-1)) ? $if_clause : '' );
							}
						else if($con_field_type=='textarea')
							{
							$output .= 'jQuery(\'#nf_form_'.$unigue_form_Id.' #'.$con_field_id.'\').find(\'textarea\').val()'.$set_operator.''.$set_the_value.' '.(($i<($query_length-1)) ? $if_clause : '' );
							}
						else
							$output .= 'jQuery(\'#nf_form_'.$unigue_form_Id.' #'.$con_field_id.'\').find(\'input[type="'.$con_field_type.'"]'.$add_string.'\').val()'.$set_operator.''.$set_the_value.' '.(($i<($query_length-1)) ? $if_clause : '' );

						}
						$i++;
					}
					$output .= '){
						';
				
				$actions =  explode('[actions]',$rule);
				$actions2 =  explode('[end_actions]',$actions[1]);
				$rule_actions = trim($actions2[0]);
				
				$get_actions =  explode('[new_action]',$rule_actions);
				$get_actions2 =  explode('[end_new_action]',$get_actions[1]);
				$get_rule_actions = trim($get_actions2[0]);
				
				foreach($get_actions as $set_action)
					{
					
					$action_to_take =  explode('[the_action]',$set_action);
					$action_to_take2 =  explode('[end_the_action]',$action_to_take[1]);
					$get_action_to_take = trim($action_to_take2[0]);
					
					$action_field =  explode('[field_to_action]',$set_action);
					$action_field2 =  explode('[end_field_to_action]',$action_field[1]);
					$get_action_field = explode('##',$action_field2[0]);
					
					$action_field_type = $get_action_field[0];
					
					$get_action_field_attr = explode('**',$get_action_field[0]);
					
					$action_field_id	 = $get_action_field_attr[0];
					$action_field_type	 = $get_action_field_attr[1];
					$action_field_name	 = $get_action_field[1];
					
					
					
					if($action_field_type)
						{
						$output .= 'jQuery("#nf_form_'.$unigue_form_Id.' #'.$action_field_id.'").'.$get_action_to_take.'();';
						}
						
					}
				$output .= '
				}
			else
				{';
			
			
			foreach($get_actions as $set_action)
					{
					
					$action_to_take =  explode('[the_action]',$set_action);
					$action_to_take2 =  explode('[end_the_action]',$action_to_take[1]);
					$get_action_to_take = trim($action_to_take2[0]);
					
					$action_field =  explode('[field_to_action]',$set_action);
					$action_field2 =  explode('[end_field_to_action]',$action_field[1]);
					$get_action_field = explode('##',$action_field2[0]);
					
					$action_field_type = $get_action_field[0];
					
					$get_action_field_attr = explode('**',$get_action_field[0]);
					
					$action_field_id	 = $get_action_field_attr[0];
					$action_field_type	 = $get_action_field_attr[1];
					$action_field_name	 = $get_action_field[1];
					
					
					
					if($action_field_type)
						{
						if($get_action_to_take=='show')
							$set_reverse_action = 'hide';
						if($get_action_to_take=='hide')
							$set_reverse_action = 'show';
							
						if($reverse_action=='true' || !$reverse_action)
							$output .= 'jQuery("#nf_form_'.$unigue_form_Id.' #'.$action_field_id.'").'.$set_reverse_action.'();';
							
						$print_auto_hide .= 'jQuery("#nf_form_'.$unigue_form_Id.' #'.$action_field_id.'").hide();
						';
						
						}
						
					}
				$output .= '
			}';
				}
				
				$output .= '';
			}
	$output .= '
		}
		jQuery(document).ready(
			function()
				{
					'.$print_auto_hide.'
					
					
					jQuery(document).on(\'change\', \'#nex-forms input, #nex-forms select, #nex-forms textarea\',
						function()
							{
							test_run_nf_conditional_logic'.$function_post_fix.'()
							}
						);
				}
			);
		</script>';
	return $output;	
	}
	
	
	
	public function run_conditional_logic($logic, $unigue_form_Id){
			$rules = $logic;
		$i=1;
	
	$output = '';
	$print_auto_hide = '';
	$function_post_fix = rand(1,99999999);
	
	if(!empty($rules))
		{
	
		$output .= '<script type="text/javascript" name="js_con">
		
		function run_nf_conditional_logic'.$function_post_fix.'(){
				';		
			foreach($rules as $rule)
				{
				if($rule)
					{
						
					$rule_operator 	= $rule->operator;
					$reverse_action = $rule->reverse_actions;
					
					
					if($rule_operator=='any')
						$if_clause = ' || ';
					else
						$if_clause = ' && ';
						
		
					
					$output .= 'if(';
					
					$query_length = 0;
					
					foreach($rule->conditions as $condition){
						$query_length++;
					}
					
					$i = 0;
					foreach($rule->conditions as $condition)
						{
						
						$get_the_condition 	=  $condition->condition;
						$get_the_value 		=  $condition->condition_value;
							
						
						$con_field_id	 = $condition->field_Id;
						$con_field_type	 = $condition->field_type;
						$con_field_name	 = $condition->field_name;
						
						$set_operator = '==';
						
						if($con_field_type)
							{
							if($get_the_condition=='equal_to')	
								$set_operator = '==';
							elseif($get_the_condition=='not_equal_to')
								$set_operator = '!=';
							elseif($get_the_condition=='less_than')
								$set_operator = '<';
							elseif($get_the_condition=='greater_than')
								$set_operator = '>';
								
							
							if($con_field_type=='radio')	
								$add_string = ':checked';
							elseif($con_field_type=='checkbox')
								$add_string = ':checked';
							else
								$add_string = '';
								
							if (is_numeric($get_the_value)) 
								$set_the_value = '('.$get_the_value.')';
							else
								$set_the_value = '"'.$get_the_value.'"';
								
							
							if($con_field_type=='select')
								{
								$output .= 'jQuery(\'#nf_form_'.$unigue_form_Id.' #'.$con_field_id.'\').find(\'select option:selected\').val()'.$set_operator.''.$set_the_value.' '.(($i<($query_length-1)) ? $if_clause : '' );
								}
							else if($con_field_type=='textarea')
								{
								$output .= 'jQuery(\'#nf_form_'.$unigue_form_Id.' #'.$con_field_id.'\').find(\'textarea\').val()'.$set_operator.''.$set_the_value.' '.(($i<($query_length-1)) ? $if_clause : '' );
								}
							else
								$output .= 'jQuery(\'#nf_form_'.$unigue_form_Id.' #'.$con_field_id.'\').find(\'input[type="'.$con_field_type.'"]'.$add_string.'\').val()'.$set_operator.''.$set_the_value.' '.(($i<($query_length-1)) ? $if_clause : '' );
							$i++;
							}
							
						}
						$output .= '){
							';
					
					
					foreach($rule->actions as $action)
						{
						$get_action_to_take = $action->do_action;
						
						$action_field_id	 = $action->target_field_Id;
						$action_field_type	 = $action->target_field_name;
						$action_field_name	 = $action->target_field_type;
						
						if($action_field_type)
							{
							$output .= 'jQuery("#nf_form_'.$unigue_form_Id.' #'.$action_field_id.'").'.$get_action_to_take.'();';
							}
							
						}
					$output .= '
					}
				else
					{';
					foreach($rule->actions as $action)
						{
						$get_action_to_take = $action->do_action;
						
						$action_field_id	 = $action->target_field_Id;
						$action_field_type	 = $action->target_field_name;
						$action_field_name	 = $action->target_field_type;
						
						if($action_field_type)
							{
							if($get_action_to_take=='show')
								$set_reverse_action = 'hide';
							if($get_action_to_take=='hide')
								$set_reverse_action = 'show';
								
							if($reverse_action=='true' || !$reverse_action)
								$output .= 'jQuery("#nf_form_'.$unigue_form_Id.' #'.$action_field_id.'").'.$set_reverse_action.'();';
								
							$print_auto_hide .= 'jQuery("#nf_form_'.$unigue_form_Id.' #'.$action_field_id.'").hide();
							';
							
							}
							
						}
					$output .= '
				}';
					}
					
					$output .= '';
				}
		$output .= '
			}
			jQuery(document).ready(
				function()
					{
					'.$print_auto_hide.'
					jQuery(document).on(\'change\', \'#nex-forms input, #nex-forms select, #nex-forms textarea\',
						function()
							{
							run_nf_conditional_logic'.$function_post_fix.'()
							}
						);
					}
				);
			</script>';
		}
	return $output;	
	}
	
	
	
	}
}

?>