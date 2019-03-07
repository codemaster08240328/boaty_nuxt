<?php
/**
 * @author Olga Zhilkova
 * @copyright 2014
 */

/************************************************/
/*  classes of library for easy filling from  */

if(!class_exists('Helper')){

	

	

	class Helper {

		

		

		

		public function JSON_char($str){

			# Format string to JSON compliant chars;

			return $str;

		}

		

		public function byGET_($url, $options = array() ){

			/* return web service response via get query */

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url); 

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			curl_setopt($ch, CURLOPT_VERBOSE, true);

			curl_setopt($ch, CURLOPT_TIMEOUT, 30);

			$headers = array(

			/* start array, just to make sure that it comes easy to add more headers */

			'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.16)',

			);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			if(!empty($options)){

				/* if defined add extra options */

				foreach($options as $k => $v){

					curl_setopt($ch, $k, $v);

				}

				

			}

			$content = curl_exec($ch);

			if(curl_error($ch) != ''){

				$content = "<pre>".curl_error($ch)."</pre>";

			}			

			curl_close($ch);

			return $content;

		}

		

		public function compress($string){

			$string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);

			$string = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '),'',$string);

			return $string;

		}

		

		public function getArrayFromObject($obj){

			

			return (array) $obj;

		}

		public function getURLStringFromArray($arr){

			/* URL query parameters builder */

			return http_build_query($arr,'','&');

		}

		public function XMLtoarray($XML){

			/* XML to array shortcut */		

			try {					

				$r = json_decode(json_encode((array)simplexml_load_string($XML)),1);			

			} catch ( Exception $e) {		

				$r = array();

			}

			return $r; 

		}

		

		public function isValidSession(){

			

			return true;

		}

		

		public function daysBetween($date1, $date2){

		    $datediff = strtotime($date2) - strtotime($date1);

		    return floor($datediff/(60*60*24));

		}

		

	/* End Class Helper */}

	

	

	class Input extends Helper {

		

		public function __construct($input = null){

			

			$PARAMS = $input != null && is_array($input) ? $input : $_REQUEST;

			

			$this->FROM = isset($PARAMS['from']) && strlen($PARAMS['from']) > 1 ? $PARAMS['from'] : null ;

			if(isset($PARAMS['date_from']) && strlen($PARAMS['date_from']) > 1){

				$from = explode('.', $PARAMS['date_from']);

				$this->FROM_DD = is_array($from) && isset($from[0]) ? $from[0] : null ;

				$this->FROM_MM = is_array($from) && isset($from[1]) ? $from[1] : null ;

				$this->FROM_YY = is_array($from) && isset($from[2]) ? $from[2] : null ;

			}

			if(isset($PARAMS['date_to']) && strlen($PARAMS['date_to']) > 1){

				$to = explode('.', $PARAMS['date_to']);

				$this->TO_DD = is_array($to) && isset($to[0]) ? $to[0] : null ;

				$this->TO_MM = is_array($to) && isset($to[1]) ? $to[1] : null ;

				$this->TO_YY = is_array($to) && isset($to[2]) ? $to[2] : null ;

				

				$this->DAYS_BETWEEN = $this->daysBetween($PARAMS['date_from'], $PARAMS['date_to']);

			}

			$this->BOAT = isset($PARAMS['boat_type']) && strlen($PARAMS['boat_type']) > 0 && $PARAMS['boat_type'] != 'all' ? $PARAMS['boat_type'] : null ;

		}

		

	/* End Class Input */}

	

	

	class Output extends Helper {

		

		/* adodb sql connection object */

		public static $conn = null;

		

		/* default api */

		public static $currentApi = 'Sedna';

		

		/*[group] results storage */

		public static $results = array();

		/* boat id's array($results[index]=>id_boat) */

		public static $boat_ids = array();

		

		/*[group] image cache trigger identifier @bool */

		public static $img_cache_start = false;

		public static $img_cache_stop  = false;

			

		/* rolling curl object */

		public static $rolling_curl = null;

		

		public static $debug_count_curl = 0;

		public static $debug_count_curl_time = 0;

		

		

		public static $js_results = array();

		public static $html_results = array();

		public static $html_results_limit = 10;

		

		public static $curl_only = false;

		

		public static function setDB($obj){

			/* adopt adodb database object */

			self::$conn = $obj;

		}

		

		/*[group] @methods that by eg. can be developed and used to output JSON results; */

		public static function formatResults($arr){

			/*  markup the results with html  */

			$html = '';

			$html.= '';# 

			

			return $html;

		}

		public static function formatCurrency($str){

			/* switch currency name and return html signs; */

			switch ($str) {
				case 'EUR':
					$str = ' &#8364; ';
					break;
			}

			return "<span class='currency_sp' >{$str}</span>";

		}

		public static function templateInfo($arr,$pam=0){

			/* this can be moved in a separated @STATIC class, for lighten up this file */

			

			/* template array. template=> wording + {{data}},  required_fields=>data that exists (=>array(condition=>value)). */

			//$template = array();

			//$template[] = array(

			//	'template'=>"{{buildyear}} {{model}} {{bt_type}} available for Bareboat Charter in {{departure_bases}} - {{homeport}} - {{country}}",

			//	'required_fields'=>array(

			//		'model',

			//		'buildyear',

			//		'departure_bases',

			//		'homeport',

			//		'country'

			//	)

			//);

			/*$template[] = array(

				'template'=>"The {{model}} from {{company}} is a great Sailing boat available for charter in {{destination}}. With {{total_cabins}} cabins and {{total_berths}} berths, 

					it has the ability to cater up to {{total_peoples}} people overnight. This makes it a perfect choice for small groups wishing to charter the region. Book online 

					with SailChecker.com for a great sailing holiday in {{destination}}.",

				'required_fields'=>array(

					'model',

					'company',

					'destination',

					'total_cabins',

					'total_berths',

					'total_peoples'=>array('max'=>10)

				)

			);*/

			/*$template[] = array(

				'template'=>"The {{model}} from {{company}} is a spacious and fanastic Sailing boat available for rent in {{destination}}. 

					With {{total_cabins}} cabins and {{total_berths}} berths, it has the ability to cater up to {{total_peoples}} people overnight. 

					This makes it a perfect choice for families and large groups wishing to charter the region. 

					Book online with SailChecker.com and set sail from {{departure_base}} for a great sailing holiday.",

				'required_fields'=>array(

						'model',

						'company',

						'destination',

						'total_cabins',

						'total_berths',

						'total_peoples'=>array('min'=>10),

						'departure_base'

				)

			);*/

			

			/*$template[] = array(

				'template'=>"Find more about  <b>{{model}}</b> from {{company}}. Rent and reserve this boat for your holiday right now with SailChecker.com!  Book online today, save big with Us,  and have a great vacation in {{destination}}!",

				'required_fields'=>array('model', 'destination', 'company')

			);*/

			

			/*$template[] = array(

				'template'=>"Yacht rental in {{departure_base}}. Rent a boat for your holiday. Jump aboard and enjoy your trip on the Sailing boat. Book online today and have a great vacation in {{destination}}!",

				'required_fields'=>array('departure_base', 'destination')

			);*/
            
            
            
            if ($pam==0)
            {
                 /*   $template[] = array(

				'template'=>"Charter this {{buildyear}} year built {{length}}  {{nbper}} Berth {{model}} {{bt_type}} {{departure_base}} is available for bareboat yacht charter in {{homeports}}. She has {{doublec}}{{singlec}}, {{heads}} WC and has the capabilty to cater up to {{total_people}} people overnight.",

				'required_fields'=>array()

			     );*/
                 
                 $template[] = array(

				'template'=>"Charter this {{buildyear}} {{model}} {{bt_type}} available for bareboat yacht charter in {{homeports}}. She sleep {{total_cab}} {{doublec}}{{singlec}}, {{heads}} WC and has the capabilty to cater up to {{total_people}} people overnight.",

				'required_fields'=>array()

			     );
            }
            elseif ($pam==1)
            {
               $template[] = array(

				'template'=>"Built {{buildyear}} {{reffit}} {{model}} {{bt_type}} available for Bareboat Charter in {{homeports}}.",

				'required_fields'=>array()

			     ); 
            }
             elseif ($pam==2)
            {
               $template[] = array(

				'template'=>"Reserve for 48 hours with no obligation and let our team help you refine your perfect {{bt_type}} charter now.",

				'required_fields'=>array()

			     ); 
            }
            elseif ($pam==3)
            {
               $template[] = array(

				'template'=>"{{bt_type}} - Bult in: {{buildyear}} - Cabins: {{single}} {{double}}.",

				'required_fields'=>array()

			     ); 
            }
            
            

			

			/* template data. The fields from here should be defined in @input $arr, or defined as vars$ below */

			$data_in_description =array(

						'model',

						'company',

						'destination',

						'total_cabins',

						'total_berths',

						'total_peoples',

						'buildyear',

						'bt_type',
                        
                        'length',
                        
                        'nbper',

						'country',
                        
                        'nbdoucabin',
                        
                        'nbsimcabin',
                        
                        'heads',
                        
                        'reffitedyear'

				);

			/* custom data */

			

				/* total no of cabins (single + double) */	

			$total_cabins = null;
            
           
				$double = isset($arr['nbdoucabin']) && is_numeric($arr['nbdoucabin']) && $arr['nbdoucabin']>0 ? $arr['nbdoucabin'] : null ;
   	            //$single = '';

                //$double = isset($arr['nbdoucabin']) && is_numeric($arr['nbdoucabin']) && $arr['nbdoucabin']>0 ? $arr['nbdoucabin'] : null ;
   	            $single = isset($arr['nbsimcabin']) && is_numeric($arr['nbsimcabin']) && $arr['nbsimcabin']>0 ? $arr['nbsimcabin']. ($double>0 && $arr['nbsimcabin']>0 ? ' + ': null)  : null ;


				$single_cabinsc = isset($arr['nbsimcabin']) && is_numeric($arr['nbsimcabin']) && $arr['nbsimcabin']>0 ? ', '.$arr['nbsimcabin']. ' Single cabins': null ;
                $double_cabinsc = isset($arr['nbdoucabin']) && is_numeric($arr['nbdoucabin']) &&  $arr['nbdoucabin']>0 ? $arr['nbdoucabin']. ' Double cabins' : null ;

                $single_cabins = isset($arr['nbsimcabin']) && is_numeric($arr['nbsimcabin']) ? $arr['nbsimcabin'] : 0 ;
                $double_cabins = isset($arr['nbdoucabin']) && is_numeric($arr['nbdoucabin']) ? $arr['nbdoucabin'] : 0 ;

				$total = ($double + $single) > 0 ? ($double + $single) : null;
                
               	$length = isset($arr['widthboat']) && is_numeric($arr['widthboat']) ? intval($arr['widthboat']*3.28).'\' ' : null ;
                $reffit= isset($arr['reffitedyear']) && ($arr['reffitedyear']>0) ? ' refitted in '.$arr['reffitedyear'] : null ;

				

				/* no of berths */

			$total_berths = null;

				try{

					$departure_bases = !empty($arr['departure_bases']) && strlen($arr['departure_bases']) > 2 ? json_decode($arr['departure_bases'], 1) : array() ;	

				}catch( Exception $e ){

					$departure_bases = array();

				}

				try{

					$arrival_bases = !empty($arr['arrival_bases']) && strlen($arr['arrival_bases']) > 2 ? json_decode($arr['arrival_bases'], 1) : array() ;	

				}catch( Exception $e ){

					$arrival_bases = array();

				}
                
                	try{

					$homeports = !empty($arr['homeport']) && strlen($arr['homeport']) > 2 ? json_decode($arr['homeport'],  true) : array() ;	

				}catch( Exception $e ){

					$homeports = array();

				}
                
                

				$total_berths = ( count($departure_bases) + count($arrival_bases) ) > 0 ? ( count($departure_bases) + count($arrival_bases) )  : null ;

				

				/* total no of peoples (single cabins*1 + double cabins*2) */

			$total_peoples = null;

				$total_people = ($double * 2 + $single) > 0 ? ($double * 2 + $single)  : null ; 
                $total_cab=$double + $single+$arr['heads'];

				

				/* from $arr[departure_bases] first port + ', country' */

			$departure_base = null;

            //$departure_base = isset($departure_bases[0]) && isset($departure_bases[0]['name']) && strlen($departure_bases[0]['name']) > 1 ? $departure_bases[0]['name'].( isset($arr['country']) && strlen($arr['country']) > 1 ? ", ".$arr['country'] : '' ) : null ;
            $departure_base = isset($departure_bases[0]) && isset($departure_bases[0]['name']) && strlen($departure_bases[0]['name']) > 1 ? $departure_bases[0]['name'] : null ;
            
            
            $homeport = null;


            $homeport = isset($homeports['name']) && strlen($homeports['name']) > 1 ? $homeports['name'].( isset($arr['country']) && strlen($arr['country']) > 1 ? ", ".$arr['country'] : '' ) : null ;

			

			

			

			foreach($arr as $k=>$v){

				/* sett all boat details in var. set var null if no string or if numeric but 0 */

				$$k = strlen($v) > 0 ? ( is_numeric($v)  ? ( $v > 0 ? $v : null )  : $v ) : null ;

			}

			

			$allowed_templates = array();	

			/* loop trough all template conditions, if not null allow, if has condition check  */			

			foreach($template as $k => $v){

				if(isset($v['required_fields'])){

					$allow = '';

					foreach($v['required_fields'] as $to_replace => $if_condition){

						if(is_numeric($to_replace)){

							/*  no condition */

							$allow .= isset( $$if_condition ) && $$if_condition != null ? '' : 'x' ;

						}else{

							/* else we have a condition. check first for var */

							if(is_array($if_condition)){

								$allow_now = isset( $$to_replace ) && $$to_replace != null ? true : false ;

								if($allow_now == true){

									/* let's check conditions */

									$against = $$to_replace;

									if( isset($if_condition['max']) ){

										/* check for min max for numeric and string length */

										if( is_numeric($against) ){

											$allow .= $if_condition['max'] < $against ? '' : 'x' ;

										}elseif( is_string($against) ){

											$allow .= $if_condition['max'] < strlen($against) ? '' : 'x' ;

										}

									}

									if( isset($if_condition['min']) ){

										/* same check */

										if(is_numeric($against)){

											$allow .= $if_condition['min'] > $against ? '' : 'x' ;

										}elseif(is_string($against)){

											$allow .= $if_condition['min'] > strlen($against) ? '' : 'x' ;

										}

									}

									

								}else{

									$allow .= 'x';

								}

							}

						}				

					}

						/* add in allowed templates */

						if($allow == ''){

							$allowed_templates[] = $k;

						}

				}

			}

			if(!empty($allowed_templates)){

				$search = explode(',',  "{{".implode('}},{{', $data_in_description)."}}");

				$replace = array();

				foreach($data_in_description as $k => $v){

					$replace[] = isset($$v) ? $$v : null ;

				}
                
                //$result_description=str_replace($search, $replace, $template[$allowed_templates[0]]['template']);
                //$result_description=str_replace('{{length}}', $length, $result_description);
                //$result_description=str_replace('{{departure_base}}', $departure_base, $result_description);
                //$result_description=str_replace('{{homeports}}', $homeports['name'], $result_description);
                //print_r($arr);
                 $result_description=str_replace('{{length}}', $length, $template[$allowed_templates[0]]['template']);
                  $result_description=str_replace('{{double}}', $double, $result_description);
                  $result_description=str_replace('{{single}}', $single, $result_description);
                 $result_description=str_replace('{{doublec}}', $double_cabinsc, $result_description);
                  $result_description=str_replace('{{singlec}}', $single_cabinsc, $result_description);
                  $result_description=str_replace('{{total_people}}', $total_people, $result_description);
                $result_description=str_replace('{{homeports}}', $homeports['name'], $result_description);
                 $result_description=str_replace('{{total_cab}}', $total_cab." in ", $result_description);
                $result_description=str_replace($search, $replace, $result_description);
                //if ($pam==0)
                //{
                //    $result_description=str_replace('{{departure_base}}', ' from '.$departure_base, $result_description);
                //}
                //else
                //{
                    if ($departure_base==Null)
                    {
                         $result_description=str_replace('{{departure_base}}', $homeports['name'], $result_description);
                    }
                    else
                    {
                $result_description=str_replace('{{departure_base}}',$departure_base , $result_description);
                }
                //}
                $result_description=str_replace('{{reffit}}', $reffit, $result_description);



				       return $result_description;

			}else{ return "No Description Available!"; }

			

		}

		public static function print_results(){

			/* print the html results */

			self::formatResults(  json_encode( self::extract_results() ) );

		}

		

		/*[group] pre processing add, format, extract @methods  */

		public static function addResultRow($arr, $boat_id = null){

			/* store the results in the @static array with the api BOAT index if defined */

			if($boat_id == null){

				self::$results[] = self::preFormat($arr,  count(self::$results) );	

			}else{

				$boat_id = strtolower(self::$currentApi)."-".$boat_id;

				self::$results[$boat_id] = self::preFormat($arr,  $boat_id );

			}

		}

		public static function extract_results(){

			/* this will be called after all results are extracted from api. add here any closing script actions */

			

			if(self::$rolling_curl != null){

				/* if multi cURL object created, then execute, and nullify the object. This should apply to any @_preformat multi cURL requests. */

				self::$rolling_curl->execute();

				self::$rolling_curl  = null;

			}

			

			/* get all boat details from database. if not stored in cache yet, create another multi curl instance and get results for all boats */

			$found_ids = array();

			if(!empty(self::$boat_ids)){

				/* ##Read Cache */

				$getBoats_sql = " SELECT * FROM mts_sedna_boats WHERE id_boat IN ('".implode("', '", self::$boat_ids)."' ) ";

				$getBoats_res = self::$curl_only == true ? false : self::$conn->Execute( $getBoats_sql );

				if( $getBoats_res && $getBoats_res->_numOfRows > 0 ){

					$getBoats = $getBoats_res->GetArray();

					foreach($getBoats as $get => $boats){

						$found_ids[] = $boats['id_boat'];

						if(isset(self::$results[$boats['id_boat']])){

							/* if something found in the cahe database, merge results. */

							

							self::$results[$boats['id_boat']] = array_merge(self::$results[$boats['id_boat']], $boats);

						}

					}

				}

			}

			

			/* ## Create Cache */



			$boats_to_get = array_values(  array_diff(array_values(self::$boat_ids), array_values($found_ids)) ) ;

			if(!empty($boats_to_get) && count($boats_to_get) > 0){

				#print_r(self::$boat_ids)	;

				#die( print_r($boats_to_get) );

				

				/* not really @agnostic */

				$api = new self::$currentApi;

				

				foreach($boats_to_get as $boa => $ts_to_get){

					/* request boat details */

					$api->requestBoat($boats_to_get, $ts_to_get);

				}

				$temp_details = $api->boatsDetails;

				

				$cache_picts = array();

				foreach($temp_details as $boat_id => $array){

					#$boat_id = strtolower(self::$currentApi)."-".$boat_id;

					/* loop trough all details */

					if(isset(self::$results[$boat_id])){

						/* build img file cache array @array_merge inusable in this case. # disable internal cc

						if(isset($array['picts']) && is_array($array['plans'])){

							foreach( $array['picts'] as $k=>$v ){

								$cache_picts[$boat_id][] = $v;

							}

						}

						if(isset($array['plans']) && is_array($array['plans'])){

							foreach( $array['plans'] as $k=>$v ){

								$cache_picts[$boat_id][] = $v;

							}

						}

						/ */

						/* merge boat details with boat search results other details */

						self::$results[$boat_id] = array_merge(self::$results[$boat_id], $array);

						

					}

				}

				

				if(!empty($cache_picts)){

					foreach($cache_picts as $boat_id => $srcs){

						/* download the plans and boat pictures */

						if(is_array($srcs)){

							foreach($srcs as $s =>$rcs){

								 #self::cacheURLimage($rcs, $boat_id, 'pict');	

							}

						}

					}

					

					if(self::$rolling_curl != null){

						self::$rolling_curl->execute();

						self::$rolling_curl  = null;

					}

				}

			}

			

			foreach(self::$results as $final => $process){

				/* store api name */

				self::$results[$final]['api'] = self::$currentApi;

				/* jsonify the array from results, @and any other operaions needed to the result */

				foreach($process as $field => $value){

					if( is_array($value) ){

						self::$results[$final][$field] = json_encode($value);

					}

				}

			}

			

			if(!empty($boats_to_get) && count($boats_to_get) > 0){

				/* and now, after pictures were dld, and boat details set, store them in the cache database. */

				foreach($boats_to_get as $boat => $to_cache){

					#$to_cache = strtolower(self::$currentApi)."-".$to_cache;

					self::$results[$to_cache]['lu'] = date('Y-m-d h:i:s');

					self::$results[$to_cache]['id_boat'] = $to_cache;

					/* to add more fields to cache, just add those fields to database as named in results (@keys) */



					if( isset(self::$results[$to_cache]) ) self::$conn->AutoExecute('mts_sedna_boats', self::$results[$to_cache],'INSERT');

				}

			}

			

			return self::$results;

		}

		public static function preFormat($arr, $currentIndex){

			/* perform any Format operations here /per Result */

			

			if(isset($arr['img']) && strlen($arr['img']) > 4 && self::checkIfValidImageUrl($arr['img']) /* extension+something */ ){

				/* cache the Image file */

				#$arr['img'] = self::cacheURLimage($arr['img'], $currentIndex);	

			}else{

				/* if exists, if does not add the default img. */

				$arr['img'] = WP_PLUGIN_URL . '/mts-search-engine/cache/images/no-image-search-results.jpg';

			}

			

			/* cache boat ID */

			self::$boat_ids[$currentIndex] = strtolower(self::$currentApi)."-".$arr['id_boat'];

			

			return $arr;

		}

		

		

		/* [group] methods to handle the images cache from url */

		public static function cacheURLimage($src, $currentIndex, $mod = 'image'){

			/* download the image if does not exists in cache */

			$ext = explode('.',$src);

			$ext = end($ext);

			$file = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($src).".{$ext}";

			if(file_exists( $file )){

				if($mod == 'image'){

					/* if cached return only the name */

		        	return WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($src).".{$ext}";

					

		        }elseif($mod == 'pict'){

		        	/* or if called for picts replace in pic, and return  */

		        	if( isset( self::$results[$currentIndex]['picts'] ) ){

		        		foreach( self::$results[$currentIndex]['picts']  as $p=> $ict ){

		        			if($ict == $src){

		        				self::$results[$currentIndex]['picts'][$p] = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($src).".{$ext}";

		        			}

		        		}

		        	}

		        	if( isset( self::$results[$currentIndex]['plans'] ) ){

		        		foreach( self::$results[$currentIndex]['plans']  as $p=> $lans ){

		        			if($lans == $src){

		        				self::$results[$currentIndex]['plans'][$p] = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($src).".{$ext}";

		        			}

		        		}

		        	}

					return true;

		        }/* if no other case should throw E; */

			}

			/* else continue */

			if(self::$rolling_curl == null){

				/* if is first time , init multi curl object -> close in extract_results */

				self::$rolling_curl =  new RollingCurl( "Output::download_image" );

			}

			/* add to current request */

			$request = new RollingCurlRequest($src);

			$request->results_index = $currentIndex;

			$request->mod = $mod;

    		self::$rolling_curl->add($request);

			/* They see me rolling.. */

		}

		

		public static function checkIfValidImageUrl($src){

			return in_array($src, array(

			/* array with all possible Image not exist's URL; */

			'../bibliovid/img/no_preview2.jpg'

			) ) || !filter_var($src, FILTER_VALIDATE_URL) ? false : true ;

		}

		

		public static function download_image($response, $info, $request){

			/* call back from rolling curl. here the boat image is saved */

			#print_r($info);

			self::$debug_count_curl++;

			self::$debug_count_curl_time+= floatval($info['total_time']);

			

			$ext = explode('.',$request->url);

			$ext = end($ext);

			$to = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($request->url).".{$ext}";

			if(!file_exists($to)){

				/* for extra safe right ? */

				$fp = fopen($to,'x');

			    fwrite($fp, $response);

			    fclose($fp);

			}

			

	        /* replace image in results */

	        if($request->mod == 'image'){

	        	self::$results[$request->results_index]['img'] = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($request->url).".{$ext}";

	        }elseif($request->mod == 'pict'){

	        	#echo ":\nWAS\n";

	        	if( isset( self::$results[$request->results_index]['picts'] ) ){

	        		foreach( self::$results[$request->results_index]['picts']  as $p=> $ict ){

	        			if($ict == $request->url){

	        				self::$results[$request->results_index]['picts'][$p] = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($request->url).".{$ext}";

	        			}

	        		}

	        	}

	        	if( isset( self::$results[$request->results_index]['plans'] ) ){

	        		foreach( self::$results[$request->results_index]['plans']  as $p=> $lans ){

	        			if($lans == $request->url){

	        				self::$results[$request->results_index]['plans'][$p] = WP_PLUGIN_URL . '/mts-search-engine/cache/images/'.md5($request->url).".{$ext}";

	        			}

	        		}

	        	}

	        }

			

		}

		

	/* End Class Output */}

	

}



?>