<?php
#exit; 
$language = isset($_REQUEST['mts_language']) ? $_REQUEST['mts_language'] : ( isset($_SESSION['mts_language']) ? $_SESSION['mts_language'] : 0 ) ;
defined('SEDNA_CFG') ? true : define('SEDNA_CFG',json_encode(array(
																	'broker_id'=>'wxft6043',
																	'language'=> $language
																	)));

if(isset($_GET['path'])){ die( __FILE__ ); }


/* Get all boat's List. (id/status/to update)
 *  ================= This can run once a day, or once a week ================
 *  ================= This script will basically get all boat's ID's from Apis ( Sedna ) and if boats does not exists in database, will add them;
 * */

include( dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'config.php' );
ini_set('display_errros', 1);
#ini_set('memory_limit','100M');             #approximate this as per XML total size + PHP other vars storage
#ini_set('max_execution_time', 300);     #will set this as per cron interval - 2 

$apis = array('Sedna');
$h = new Helper;

$requests_limit = 50; # limit request's per execution

#$today= date('Y-m-d', strtotime('+1 week', time())); # next week
$today= date('Y-m-d');

$today_array = explode('-', $today);

class GET_BOATS {
	
	public static $results = array();
	public static $existing_results = array();
	public static $nondup = array();
	
	public static $get_src = array();
	
	public static function add_boat( $id, $api, $url ){
		if(!in_array($id, self::$existing_results)){
			if( !in_array($id, self::$nondup) ){
				self::$nondup[] = $id;
				self::$results[$api][$url][] = $id; 
			}
		}
	}
}

/* add existing UNIQUE boat_id in GET_BOATS  */
$ids_sql = " SELECT id_boat FROM mts_sedna_boats ";

$ids_re  = $conn->Execute( $ids_sql );

if( $ids_re && $ids_re->_numOfRows > 0 ){
	
	$ids_res = $ids_re->GetArray();
	foreach( $ids_res as $k => $v ){
		GET_BOATS::$existing_results[] = $v['id_boat'];
	}
	#print_r(GET_BOATS::$existing_results);
} 
/* add today solved request's to  GET_BOATS::$get_src */


function gather_all_boats( $response, $info, $request ){
	#print_r($info);	
	global $h; # let's not instantiate helper class again ( this if sv has enabled globals )
	global $conn; # bind to request object if globals not enabled;
	
	#print_r($response);
	
	if(!empty( $request->api )){
		/* reset results */
		#GET_BOATS::$results = array();
		
		switch ($request->api) {
			/* add in GET_BOATS results all founded ID's */
			case 'Sedna':
				$result = $h->XMLtoarray( $response );
				#print_r($result);
				if(!empty($result['boat'])){
					$boats = !empty($result['boat'][0]) ? $result['boat'] : array($result['boat']) ;
					foreach( $boats as $b => $oat ){
						$id = strtolower($request->api) ."-". $oat['@attributes']['id_boat'];
						if( empty( GET_BOATS::$results[$request->api ][$request->url]) || !in_array($id, GET_BOATS::$results[$request->api][$request->url]) ){
							GET_BOATS::add_boat($id,$request->api, $request->url);
						}
					}
					
				}
				break;
		}
		#print_r(GET_BOATS::$results[$request->api]);
		/* will insert here just the id's for this route, to make sure that insert for route executed ( not per api ) */
		if( !empty( GET_BOATS::$results[$request->api][$request->url]  ) ) :
			$vals = "";
			$vals_count = count( GET_BOATS::$results[$request->api][$request->url] );
			$iplus = 0;
			foreach( GET_BOATS::$results[$request->api][$request->url] as $index => $val ){
				$iplus++;	
				$comma = $iplus == $vals_count ? '' : ',' ;
				$vals .= " ('{$val}') {$comma}";
			}
			$insert = " INSERT INTO mts_sedna_boats (id_boat) VALUES {$vals} ";
			$conn->Execute( $insert );
			#echo $insert;
			#print_r($conn);
			/* what i can do further more, to insert route in database with last updated, and to exclude from adding request object, and also to add a limit */
			
		else:
			#echo $request->api." api had 0 new boats to add in list for url :".$request->url."\n<br />\n";
		endif;
	}
	
}


foreach($apis as $api){
	/* loop trough each api and get locations URL's */
	
	if( !class_exists($api) ){
		/* make sure the class is defined. */
		$file = WEB_ROOT.DS.'apis'.DS.strtolower($api).'.php';
		if( file_exists($file) ){ require_once( $file ); }	}
	
	/* init api; */
	$app = new $api;
	$locations_params = array();
	
	/* now  let's get all params required by boat results for every api */
	
	switch ($api) {
		case 'Sedna':
			$sedna_cfg 	= json_decode(SEDNA_CFG,1);
			$broker_id 	= $sedna_cfg['broker_id'];
			$language      = $sedna_cfg['language'];
			
			$query = http_build_query($_GET);
			$data = $h->XMLtoarray( $h->byGET_( "http://client.sednasystem.com/API/GetDestinations2.asp?lg={$language}&refagt={$broker_id}" )  );
			#echo "http://client.sednasystem.com/API/GetDestinations2.asp?lg={$language}&refagt=dghs{$broker_id}";
			#print_r($data);
			if(!empty($data['destination'])){
				foreach( $data['destination'] as $key => $value ){
					
					/* noticed that only srh_destination is required for testing. If the other param's required on live, just un comment */
					
					$locations_params[] = array( 'GET'=>array(
					'srh_dest'=>'d'.$value['@attributes']['id_dest'],
					'DEPART_DD'=>$today_array[2],
					'DEPART_MM'=>$today_array[1],
					'DEPART_YYYY'=>$today_array[0],
					'Nombjour'=>'7'
					) );
					if( isset($value['country']) && is_array($value['country']) && isset($value['country'][0]) ){
						foreach($value['country'] as $skey => $svalue){
							$locations_params[] = array( 'GET'=>array(
							'srh_dest'=>'c'.$svalue['@attributes']['id_country'],
							'DEPART_DD'=>$today_array[2],
							'DEPART_MM'=>$today_array[1],
							'DEPART_YYYY'=>$today_array[0],
							'Nombjour'=>'7'
							) );
						}
					}elseif( isset($value['country']) && is_array($value['country']) && isset($value['country']['@attributes']) ){
							$locations_params[] = array( 'GET'=>array(
							'srh_dest'=>'c'.$value['country']['@attributes']['id_country'],
							'DEPART_DD'=>$today_array[2],
							'DEPART_MM'=>$today_array[1],
							'DEPART_YYYY'=>$today_array[0],
							'Nombjour'=>'7'
							) );
					}
				}
			}
			
			break;
	}
	#print_r($locations_params);
	#exit;
	if(!empty($locations_params)){
		/* if parmas exists, build multi curl request object. */
		$count_srcs = 0;
		$rolling_curl = new RollingCurl( 'gather_all_boats' );
		/* create rolling curl object */		
		foreach( $locations_params as $index => $params ){
	
			switch ($api) {
				case 'Sedna':
				
					$sedna_cfg 	= json_decode(SEDNA_CFG,1);
					$broker_id 	= $sedna_cfg['broker_id'];
					$language      = $sedna_cfg['language'];
					$query = http_build_query( $params['GET'] );
					$src = "http://client.sednasystem.com/m3/agt/6043/default.asp?action=search&{$query}";
					
					break;
			}
			#echo $src."\n";
			/* add request ( Only Sedna now - no need for special headers or other params on request object ) */
			if( !empty($src) && !in_array($src, GET_BOATS::$get_src) /*&& $count_srcs < $requests_limit*/ ){
				/* be sure that there are not duplicate requests OR not registerd in database from @today */
				GET_BOATS::$get_src[] = $src;
				$count_srcs++;
				$request = new RollingCurlRequest($src);				
				$request->api = $api;
	    		$rolling_curl->add($request);
			}
			
			
		}
		
		if( $count_srcs != 0 ){
			/* if there are requests defined */
			$rolling_curl->execute();
		}
	}
	
	/* sql insert moved in callback per route ( how many routes can be ) */
	
}



?>