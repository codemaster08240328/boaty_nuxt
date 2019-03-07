<?php

require_once(ABSPATH . 'wp-admin' . '/includes/image.php');
require_once(ABSPATH . 'wp-admin' . '/includes/file.php');
require_once(ABSPATH . 'wp-admin' . '/includes/media.php');

class BoatImport
{


    public $all_characteristics = array();
    public $boats;

    function __construct(){
        $this->boats = new Boats();
    }
    
    
    /*********************functions for BoatBooker database**************************/
    
    //import all bases to categories of operators
    public function OperatorsImport()
    {
        $boats         = new Boats();
        $operators=$this->boats->getOperators();
        if(!empty($operators) && count($operators)>0)
        {
            return $operators;
        }
        else
        {
            return false;
        }
    }
    
    
    
     //import all boats to cboat posts 
    public function BookerBoatsImport()
    {
        $boats         = new Boats();
        $boats=$this->boats->getBoatsOperator();
        if(!empty($boats) && count($boats)>0)
        {
            return $boats;
        }
        else
        {
            return false;
        }
    }
    
    
    
    
    /*********************functions for Sedna database******************************/
     //import of all new boats
     function initialScrape($new = false, $input=false)
    {
        echo "<h3>Scraping started. Do not reload the page!</h3>";

        global $wpdb;
        $this->boats = new Boats();
        //check all boats for duplicates
        //check all uploaded boats for deleting
        $args = array(
                      'post_type' => 'operators',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => -1,
                      );
                      
        $all_operators = get_posts($args);
        $new_posts_limit=1;

        foreach ($all_operators as $operator) 
        {
            $operator_id=get_post_meta($operator->ID,'id_ope',true);
            echo '<p>Operator: '.$operator_id.'</p>';
            if($operator_id==2)
            {
            $test_boats   = $this->boats->getAllBoatsFromOperator($operator_id);
            

            if (isset($test_boats['boat']['@attributes']['id_boat'])) 
            {
                //if only one boat
                        //$boats_total = 1;
                        /*$new_post_id = $this->newBoatCreation($test_boats['boat']);
                        if($new_post_id){
                         update_post_meta($operator->ID, "all_boats", 1);
                         update_post_meta($operator->ID, "uploaded_boats", 1);
                         update_post_meta($operator->ID, "id_boats", $test_boats['boat']['@attributes']['id_boat']);
                         $new_boats++;
                         echo '<p>Boat <a href="'.get_permalink($new_post_id).'">'.$new_post_id.'</a> was added!</p>';
                        }*/
                    echo '<p>Total boats: 1</p>';
            }
            else 
            {
                $boats_total=count($test_boats['boat']);
                foreach ($test_boats['boat'] as $boat) 
                {
                    //checking on existing boats in boat pages
                     $boats = array(
                      'post_type' => 'boat_page',
                      'meta_query' => array(
	                   array(
		                  'key' => 'id_boat',
                          'value'=>$boat['@attributes']['id_boat'],
		                   'compare' => '=',
	                       )),
                      );
                       $boat_found = get_posts($boats);
                      
                      if (count($boat_found)>0)
                      {
                        
                        
                      }
                      else
                      {
                            $new_post_id = $this->newBoatCreation($boat);
                            if (!in_array($boat['@attributes']['id_boat'],$boats_id))
                            {
                                if($new_post_id)
                                {
                                    $new_posts_limit= $new_posts_limit+1;
                                    echo '<p>Boat <a href="'.get_permalink($new_post_id).'">'.$new_post_id.'</a> was added!</p>';
                                }
                            }
                      }
                    
                        if ($new_posts_limit>4)
                        {
                            break;
                        }
                }
                
            }

                if ($new_posts_limit>4)
                {
                    break;
                }
                }
        }
        
    }

    function getLastPostScraped($input){
   
        if ($input['when'] == 'Schedule'){
            return 0;
        }else{
            return (int) get_option('details_check');
        }

    }


    function updateLastPostScraped($input, $new_value){
   
        if ($input['when'] == 'Schedule'){
            return 0;
        }else{
            update_option('details_check',$new_value);
        }

    }
    
    

    
    
    
    
    
    
    
    public function scrapeAvailablity($input,$id_post='')
    {
        global $wpdb;
        $boats         = new Boats();
         $month=date('m');
           $day=date('d');
           $year=date('Y');
           $numdays=7;
         
        if(empty($id_post))
        {
            $args = array(
                      'post_type' => 'boat_page',
                      'orderby'=>'ID',
                      'numberposts'=>40,
                      'order' => 'ASC',
                      'meta_query' => array(
                      'relation' => 'OR',
	                   array(
		                  'key' => 'available',
		                   'compare' => 'NOT EXISTS',
	                       ),
                           array(
		                  'key' => 'available',
                          'value'=>'',
		                   'compare' => '=',
	                       )),
                      );

        $posts = get_posts($args);
        if (count($posts)==0)
        {
            echo '<p>There are no posts to update!</p>';
        }
        foreach ($posts as $key=>$post)
        {
           $id_sedna=get_post_meta($post->ID,'id_boat',true);
             echo '<p>Boat <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> for availability!</p>';
           $country=get_post_meta($post->ID,'homeport_id_country',true);
           if (empty($country))
           {
            $homeports=get_post_meta($post->ID,'homeport_num',true);
            echo '<p>Homeport: '.$homeports.'</p>';
                   $res_avail=$this->boats->getBoatsAvailabilityById($id_sedna,$day,$month,$year,$numdays);
                   print_r($res_avail);
                   echo '<br />';

                   if (isset($res_avail['boat']['@attributes']))
                   {
                        if (isset($res_avail['boat']['homeport']['@attributes']['id_base']))
                        {
                            update_post_meta($post->ID, 'datestart',$res_avail['boat']['@attributes']['datestart'] );
                            update_post_meta($post->ID, 'dateend',$res_avail['boat']['@attributes']['dateend'] );
                            update_post_meta($post->ID, 'discount',$res_avail['boat']['@attributes']['discount'] );
                            update_post_meta($post->ID, 'oldprice',$res_avail['boat']['@attributes']['oldprice'] );
                            update_post_meta($post->ID, 'newprice',$res_avail['boat']['@attributes']['newprice'] );
                            update_post_meta($post->ID, 'def_cur',$res_avail['boat']['@attributes']['IsoCurr'] );
                            update_post_meta($post->ID, 'id_base',$res_avail['boat']['homeport']['@attributes']['id_base']);
                            update_post_meta($post->ID, 'id_ope',$res_avail['boat']['@attributes']['id_ope'] );
                            if ($res_avail['boat']['@attributes']['IsoCurr']!=='EUR')
                            {
                                $args1 = array(
                                'post_type' => 'operators',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'meta_query' => array(
	                               array(
		                          'key' => 'id_ope',
                                  'value' => $res_avail['boat']['@attributes']['id_ope'], 
		                          'compare' => '=',
	                           )));
                      
                                $operator = get_posts($args1);
                                if(count($operator)==1)
                                {
                                    foreach ($operator as $oper)
                                    {
                                    
                                    echo '<p>operator: '.get_post_meta($oper->ID, "id_ope", true).'</p>';
                                    $def_ope=get_post_meta($oper->ID, "def_cur", true); 
                                    $rate_ope=get_post_meta($oper->ID, "rate", true); 
                                    echo '<p>rate: '.$rate_ope.'</p>';
                                    if(!empty($rate_ope))
                                    {
                                        update_post_meta($post->ID, 'rate',$rate_ope); 
                                    }
                                    }
                                    update_post_meta($post->ID, 'available','yes'); 
                                    update_post_meta($post->ID, 'date_available',date('d.m.Y H:i'));  
                                }
                            }
                            else
                            {
                               update_post_meta($post->ID, 'available','yes'); 
                                update_post_meta($post->ID, 'date_available',date('d.m.Y H:i'));  
                            }
                        }
                        else
                        {
                            /*update_post_meta($post->ID, 'datestart_'.$i,$res_avail['boat']['@attributes']['datestart'] );
                        update_post_meta($post->ID, 'dateend_'.$i,$res_avail['boat']['@attributes']['dateend'] );
                        update_post_meta($post->ID, 'discount_'.$i,$res_avail['boat']['@attributes']['discount'] );
                        update_post_meta($post->ID, 'oldprice_'.$i,$res_avail['boat']['@attributes']['oldprice'] );
                        update_post_meta($post->ID, 'newprice_'.$i,$res_avail['boat']['@attributes']['newprice'] );
                        update_post_meta($post->ID, 'def_cur_'.$i,$res_avail['boat']['@attributes']['IsoCurr'] );*/
                        }
                        
             
                    }
                    else
                    {
                        update_post_meta($post->ID, 'available','no'); 
                        update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                    }
           }
           else
           {

                 $res_avail=$this->boats->getBoatsAvailabilityById($id_sedna,$day,$month,$year,$numdays);
                print_r($res_avail);
                 if (isset($res_avail['boat']['@attributes']))
                 {
                    update_post_meta($post->ID, 'datestart',$res_avail['boat']['@attributes']['datestart'] );
                    update_post_meta($post->ID, 'dateend',$res_avail['boat']['@attributes']['dateend'] );
                    update_post_meta($post->ID, 'discount',$res_avail['boat']['@attributes']['discount'] );
                    update_post_meta($post->ID, 'oldprice',$res_avail['boat']['@attributes']['oldprice'] );
                    update_post_meta($post->ID, 'newprice',$res_avail['boat']['@attributes']['newprice'] );
                    update_post_meta($post->ID, 'def_cur',$res_avail['boat']['@attributes']['IsoCurr'] );
                     update_post_meta($post->ID, 'id_base',$res_avail['boat']['homeport']['@attributes']['id_base'] );
                      update_post_meta($post->ID, 'id_ope',$res_avail['boat']['@attributes']['id_ope'] );
                    if ($res_avail['boat']['@attributes']['IsoCurr']!=='EUR')
                        {
                            $args1 = array(
                                'post_type' => 'operators',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'meta_query' => array(
	                           array(
		                          'key' => 'id_ope',
                                  'value' => $res_avail['boat']['@attributes']['id_ope'], 
		                          'compare' => '=',
	                       )));
                      
                            $operator = get_posts($args1);
                            if(count($operator)==1)
                            {
                                foreach ($operator as $oper)
                                {
                                    echo '<p>operator: '.get_post_meta($oper->ID, "id_ope", true).'</p>';
                                    $rate_ope=get_post_meta($operator->ID, "rate", true); 
                                    echo '<p>rate: '.$rate_ope.'</p>';
                                    $def_ope=get_post_meta($oper->ID, "def_cur", true); 
                                    if(!empty($rate_ope))
                                    {
                                        update_post_meta($post->ID, 'rate',$rate_ope); 
                                    }
                               }
                               update_post_meta($post->ID, 'available','yes'); 
                                update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                            }
                            
                        }
                        else
                        {
                        
                            update_post_meta($post->ID, 'available','yes'); 
                            update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                        }
                 }
                 else
                 {
                    update_post_meta($post->ID, 'available','no'); 
                    update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                 }
           }

        }
        
        }
        else
        {
            $id_sedna=get_post_meta($id_post,$day,$month,$year,$numdays);
            $res_avail=$this->boats->getBoatsAvailabilityById($id_sedna,$day,$month,$year,$numdays);
                    if (isset($res_avail['boat']['@attributes']))
                 {
                    update_post_meta($post->ID, 'datestart',$res_avail['boat']['@attributes']['datestart'] );
                    update_post_meta($post->ID, 'dateend',$res_avail['boat']['@attributes']['dateend'] );
                    update_post_meta($post->ID, 'discount',$res_avail['boat']['@attributes']['discount'] );
                    update_post_meta($post->ID, 'oldprice',$res_avail['boat']['@attributes']['oldprice'] );
                    update_post_meta($post->ID, 'newprice',$res_avail['boat']['@attributes']['newprice'] );
                    update_post_meta($post->ID, 'def_cur',$res_avail['boat']['@attributes']['IsoCurr'] );
                     update_post_meta($post->ID, 'id_base',$res_avail['boat']['homeport']['@attributes']['id_base'] );
                      update_post_meta($post->ID, 'id_ope',$res_avail['boat']['@attributes']['id_ope'] );
                    if ($res_avail['boat']['@attributes']['IsoCurr']!=='EUR')
                        {
                            $args1 = array(
                                'post_type' => 'operators',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'meta_query' => array(
	                           array(
		                          'key' => 'id_ope',
                                  'value' => $res_avail['boat']['@attributes']['id_ope'], 
		                          'compare' => '=',
	                       )));
                      
                            $operator = get_posts($args1);
                            if(count($operator)==1)
                            {
                                foreach ($operator as $oper)
                                {
                                    echo '<p>operator: '.get_post_meta($oper->ID, "id_ope", true).'</p>';
                                    $rate_ope=get_post_meta($operator->ID, "rate", true); 
                                    echo '<p>rate: '.$rate_ope.'</p>';
                                    $def_ope=get_post_meta($oper->ID, "def_cur", true); 
                                    if(!empty($rate_ope))
                                    {
                                        update_post_meta($post->ID, 'rate',$rate_ope); 
                                    }
                               }
                               update_post_meta($post->ID, 'available','yes'); 
                                update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                            }
                            
                        }
                        else
                        {
                        
                            update_post_meta($post->ID, 'available','yes'); 
                            update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                        }
                 }
                 else
                 {
                    update_post_meta($post->ID, 'available','no'); 
                    update_post_meta($post->ID, 'date_available',date('d.m.Y H:i')); 
                 }
        }
    }


    public function scrapeAllDetails($input)
    {
        global $wpdb;
        $boats         = new Boats();

        
        $args = array(
                      'post_type' => 'boat_page',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'numberposts'=>2,
                      
                      'meta_query' => array(
                      'relation' => 'AND', 
	                   array(
		                  'key' => 'chars',
		                   'compare' => 'NOT EXISTS',
	                       )),
                      );
        $exclude=array();
        $updated=false;
        $i=0;
        $posts = get_posts($args);
        if (count($posts)==0)
        {
            echo '<p>There are no posts to update characteristics!</p>';
        }
        else
        {
            
            foreach ($posts as $key=>$post)
            {

                $id_sedna=get_post_meta($post->ID,'id_boat',true);
                $new_charact = $this->boats->getBoatsCharacteristics($id_sedna);
                
                $numbers=$this->saveCharacteristics($new_charact, $post->ID);  
  
                    //echo '<p>Boat <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> was updated with new characteristics!</p>';  
                
            }

        }
    }
    
    
    //update all images
    public function scrapeImages($input)
    {
        $boats     = new Boats;

        $args = array(
                      'post_type' => 'boat_page',
                      'numberposts'=>60,
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'meta_query' => array( 
	                   array(
		                  'key' => 'images_total',
		                   'compare' => 'NOT EXISTS',
	                       )),
                      );
        
        $posts = get_posts($args);
        if (count($posts)==0)
        {
            echo '<p>There is no any post to upload images!</p>';
        }
        foreach ($posts as $key=>$post)
        {
             $sedna_id=get_post_meta($post->ID, 'id_boat',true);
             if (empty($sedna_id))
             {
                wp_delete_post( $post->ID, true);
             }
             else
             {
             $boat = $boats->getBoatsGeneralInfo($sedna_id);
             if (!isset($boat['@attributes']))
             {
                wp_delete_post( $post->ID, true);
             }
             else
             {
                $this->saveImages($boat, $post->ID);  
                echo '<p>Total uploaded images for boat <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a>: '.$number_images.'</p>';
                }
             }
        }
             
 
    }
    

    

    public function updateBoatById($input){

        $post_id = $input['post_id'];
        $id_boat = $input['id_boat'];

        $this->scrapeAvailablity($input,$post_id);
        //$characteristics = $this->boats->getBoatsCharacteristics($boat['@attributes']['id_boat']);
        //$this->saveCharacteristics($characteristics, $post_id);

        $title        = get_the_title($post_id);
        $permalink    = get_permalink($post_id);
        echo "<a href='{$permalink}'>{$title}</a> was updated <br>";


    }

    function newBoatCreation($boat_info, $new = false){
        global $wpdb;

        if (is_array($boat_info['@attributes'])) {
            $boat_info = $boat_info['@attributes'];
        }


            $my_post = array(
                             'post_status' => 'publish',
                             'post_type'   => 'boat_page',
                             'post_title'  =>  's'.$boat_info['id_boat']
                             );
            $post_id             = wp_insert_post( $my_post );
            $this->saveGeneralInfo( $boat_info, $post_id,'all');
            return $post_id;

        return false;

    }

    public function scrapeNewBoats($input)
    {

        update_option('operators_initial_added', serialize(array()));
        $new_boats = $this->initialScrape(true, $input);

        foreach ($new_boats as $boat_id) {

            $title        = get_the_title($boat_id);
            $permalink    = get_permalink($boat_id);
            echo "<a href='{$permalink}'>{$title}</a> was added <br>";

        }

    }



     function scrapeAvailabilitiesById($boatinfo, $post_id, $input)
    {

        $days_plus  = $input['days_plus'];
        $datetime = new DateTime('NOW');
        $datetime->modify("+{$days_plus} day");
        $DD   = $datetime->format('d');
        $MM   = $datetime->format('m');
        $YYYY = $datetime->format('Y');


        $availabilities = $this->boats->getBoatsAvailabilityById($boatinfo['@attributes']['id_boat'], $DD, $MM, $YYYY, $input['daysamount']);

        if(!empty($availabilities['boat'])){
                $post_id = $this->updateAvailability($availabilities['boat']);
        }
    

    }


    public function scrapeAvailabilitiesByDestination($input)
    {

        $days_plus  = $input['days_plus'];
        $datetime = new DateTime('NOW');
        $datetime->modify("+{$days_plus} day");
        $DD   = $datetime->format('d');
        $MM   = $datetime->format('m');
        $YYYY = $datetime->format('Y');

        if ($input['destination'] == 'all') {
            $Boats = new Boats;
            $destinations = $Boats->getAllDestinations();

            foreach ($destinations as $destination) {

                $availabilities = $this->boats->getBoatsAvailabilityByDestination($destination['id'], $DD, $MM, $YYYY, $input['daysamount']);
                if(!empty($availabilities['boat'])){

                    if (count($availabilities['boat']) > 1) {
                        foreach ($availabilities['boat'] as $boat) {
                            $this->updateAvailability($boat, $destination['id']);
                        }
                    }else{
                            $post_id = $this->updateAvailability($availabilities['boat'], $destination['id']);
                    }
                }
            }

        }else{

                $availabilities = $this->boats->getBoatsAvailabilityByDestination($input['destination'], $DD, $MM, $YYYY, $input['daysamount']);

                if(!empty($availabilities['boat'])){

                    if (count($availabilities['boat']) > 1) {
                        foreach ($availabilities['boat'] as $boat) {
                            $this->updateAvailability($boat, $input['destination']);
                        }
                    }else{
                        $post_id = $this->updateAvailability($availabilities['boat'], $input['destination']);
                    }
                }
        }

    }





     function updateAvailability($boat, $destination_id = false){

        global $wpdb;

        $source = ($boat['@attributes']['id_boat'] == null) ? $boat : $boat['@attributes'];

        if (empty($source['id_boat'])) {
            return;
        }



        $post_id     = $wpdb->get_var("SELECT `post_id` FROM `wp_postmeta` WHERE `meta_key` = 'id_boat' AND `meta_value` = '{$source['id_boat']}' LIMIT 1");

        if (empty($post_id)) {

            echo "new boat with id {$source['id_boat']}! <br>";
            flush();
            return;

        }

        $availability = array(
                              'destination'    => $source['destination'],
                              'destination_id' => $destination_id,
                              'datestart'      => $source['datestart'],
                              'dateend'        => $source['dateend'],
                              'discount'       => $source['discount'],
                              'oldprice'       => $source['oldprice'],
                              'newprice'       => $source['newprice'],
                              'IsoCurr'        => $source['IsoCurr'],
                              'Caution'        => $source['Caution'],
                              'CautionWithSI'  => $source['CautionWithSI']
                              );

        $all_availabilities = get_post_meta($post_id, 'availability');
        $all_availabilities = $all_availabilities[0];

        if(empty($all_availabilities)){
            $all_availabilities = array();
        }else{
            $all_availabilities = json_decode($all_availabilities, true);
            $all_availabilities = $this->prepareAvailabilityData($all_availabilities, $availability);

        }
        $all_availabilities[] = $availability;
        $all_availabilities = json_encode($all_availabilities);
        update_post_meta($post_id, 'availability', $all_availabilities);

        $title        = get_the_title($post_id);
        $permalink    = get_permalink($post_id );
        echo "<a href='{$permalink}'>{$title}</a> was updated <br>";
        flush();

    }


    /**
     * deletes all old ones with same date and destination (no replicas)
     * @param  [type] $all_availabilities [description]
     * @param  [type] $availability       [description]
     * @return [type]                     [description]
     */
     function prepareAvailabilityData($all_availabilities, $availability){
          

        $new_availabilities = array();

            if (count($all_availabilities) > 1) {
                foreach ($all_availabilities as $key => $old_availability) {
                    if ($old_availability['datestart'] != $availability['datestart'] &&
                        $old_availability['dateend'] != $availability['dateend']){
                         $new_availabilities[] = $old_availability;
                    }
                }
            }else{

                 if ($all_availabilities[0]['datestart'] != $availability['datestart'] &&
                        $all_availabilities[0]['dateend'] != $availability['dateend']){
                        $new_availabilities[] = $all_availabilities[0];

                    }

            }

            return $new_availabilities;
    }
    



     function saveCharacteristics($characteristics, $post_id)
    {
            $boats         = new Boats();
         $important_charact=array(0=>'draft',1=>'engine',2=>'engines');
         $exluded_charact=array(0=>'water capacity',1=>'sail area',2=>'beam',
                                3=>'fuel capacity',4=>'hot water',5=>'water tank',
                                6=>'fuel tank',7=>'displacement');
                                
        $chars=get_post_meta($post_id,'chars',true);
        if (empty($chars))
        {
        if(empty($characteristics['characteristic_topic'])  || !isset($characteristics['characteristic_topic']))
        {
            update_post_meta($post_id, 'chars', 'empty');
            //return false;
        }
        else
        {
            
        $all_topics = array();
        $meta = array();
        $i=0;
        if (isset($characteristics['characteristic_topic']['@attributes']))
        {

            foreach ($characteristics['characteristic_topic']['characteristic'] as $key=>$characteristic)
            {
                     
                 $name     = strtolower(trim($characteristic['@attributes']['name']));
                $quantity = trim($characteristic['@attributes']['quantity']);
                $unit     = trim($characteristic['@attributes']['unit']);
                if (!in_array($name,$exluded_charact) && (!empty($unit) || !empty($quantity)))
                {
                    $meta[$i] = $characteristic['@attributes'];
                    $i++;
                }
            }
        }
        else
        {
        foreach ($characteristics['characteristic_topic'] as $topic_id=>$topic) {

           

            $caracteristics_topic = ( (empty($topic['@attributes']['topic'])) ) ? $topic['characteristic']['@attributes']['name'] : $topic['@attributes']['topic'];
            $caracteristics_topic = trim($caracteristics_topic);

           
            if (count($topic['characteristic']) > 1) {
                

                foreach ($topic['characteristic'] as $characteristic) {

                    $name     = strtolower(trim($characteristic['@attributes']['name']));
                    $quantity = trim($characteristic['@attributes']['quantity']);
                    $unit     = trim($characteristic['@attributes']['unit']);
                    if (!in_array($name,$exluded_charact) && (!empty($unit) || !empty($quantity)))
                    {
                        $meta[$i] = $characteristic['@attributes'];
                    $i++;
                    }

                    
                }

            } else {

                $name     = strtolower(trim($topic['characteristic']['@attributes']['name']));
                $quantity = trim($topic['characteristic']['@attributes']['quantity']);
                $unit     = trim($topic['characteristic']['@attributes']['unit']);
                if (!in_array($name,$exluded_charact) && (!empty($unit) || !empty($quantity)))
                {
                $meta[$i] = $topic['characteristic']['@attributes'];
                $i++;
                }
            }
            }

            
            }
            $i=0;
            foreach ($meta as $key => $value) 
            {

                $name=$value['name'];
                if (in_array(strtolower(trim($name)),$important_charact))
                {
                    if (strtolower(trim($name))==='draft')
                    {
                       if (!empty($value['quantity']))
                        {
                            update_post_meta($post_id, 'draft', $value['quantity']);
                        }
                        else
                        {
                            update_post_meta($post_id, 'draft', $value['unit']);
                        } 
                    }
                    if (stripos(strtolower(trim($name)),'engine')!==false)
                    {
                       if (!empty($value['quantity']))
                        {
                            update_post_meta($post_id, 'engine', $value['quantity']);
                        }
                        else
                        {
                            update_post_meta($post_id, 'engine', $value['unit']);
                        } 
                    }
                    
                }
                else
                {
                    update_post_meta($post_id, 'char_name_'.$i, $name);
                    if (!empty($value['quantity']))
                    {
                        update_post_meta($post_id, 'char_val_'.$i, $value['quantity']);
                    }
                    else
                    {
                        update_post_meta($post_id, 'char_val_'.$i, $value['unit']);
                    }
                    $i=$i+1;
                }
            }
            update_post_meta($post_id, 'chars', $i);
            echo '<p>Boat <a href="'.get_permalink($post_id).'">'.$post_id.'</a> was updated with all characteristics!</p>';
     
            }
            }
            
            $extras=get_post_meta($post_id, 'mand_extra',true);
            $add_extras=get_post_meta($post_id, 'add_extra',true);

            if ($extras!==0)
            {
            $mand_extra=array();
            $add_extra=array();
            $boat=get_post_meta($post_id,'id_boat',true);
            $new_extras = $this->boats->getBoatsExtraPrices($boat); 

            if (count($new_extras['extra'])>1)
            {

                        $i=0;
                        $j=0;
                        foreach ($new_extras['extra'] as $key=>$name)
                        {
                                    
                            if ($name['@attributes']['mand']==1)
                            {
                                 
                              $mand_extra[]=$name['@attributes'];
                               update_post_meta($post_id, 'mand_name_'.$i,$name['@attributes']['name']);
                               update_post_meta($post_id, 'mand_price_'.$i,$name['@attributes']['price']);
                               update_post_meta($post_id, 'mand_per_'.$i,$name['@attributes']['per']."/".$name['@attributes']['per2']);
                               update_post_meta($post_id, 'mand_quantity_'.$i,$name['@attributes']['quantity']);
                               update_post_meta($post_id, 'mand_rate_'.$i,$name['@attributes']['rate']);
                               update_post_meta($post_id, 'mand_country_'.$i,$name['@attributes']['id_country']);
                                $i++;
                            }
                            else
                            {
                                if (empty($add_extras) || $add_extras==0)
                                {
                               $add_extra[]=$name['@attributes']; 
                               update_post_meta($post_id, 'add_name_'.$j,$name['@attributes']['name']);
                               update_post_meta($post_id, 'add_price_'.$j,$name['@attributes']['price']);
                               update_post_meta($post_id, 'add_per_'.$j,$name['@attributes']['per']."/".$name['@attributes']['per2']);
                               update_post_meta($post_id, 'add_quantity_'.$j,$name['@attributes']['quantity']);
                               update_post_meta($post_id, 'add_rate_'.$j,$name['@attributes']['rate']);
                               update_post_meta($post_id, 'add_country_'.$j,$name['@attributes']['id_country']);
                               $j++;
                               }
                            }
                        }
                        update_post_meta($post_id, 'mand_extra',count($mand_extra));
                        update_post_meta($post_id, 'add_extra',count($add_extra));  
                        echo '<p>Boat <a href="'.get_permalink($post_id).'">'.$post_id.'</a> was updated with new extra prices!</p>';
            }
            else
            {
                        if (!isset($new_extras['extra']))
                        {
                            update_post_meta($post_id, 'mand_extra',0);
                            echo '<p>Boat <a href="'.get_permalink($post_id).'"> has no extra prices</a>'; 
                        }
            }
            }
            return count($meta);

    }

     function saveGeneralInfo($general_info, $post_id, $includes = 'all')
    {

        $all_keys = array('id_boat',
        'model',
        'name',
        'ope_company',
        'bt_type',
        'crew',
        'widthboat',
        'caution',
        'cautionwithsI',
        'nbdoucabin',
        'nbsimcabin',
        'heads',
        'nbbathroom',
        'nbper',
        'buildyear',
        'reffitedyear',
        'crew_members',
        'class',
        'nbdbkcabin',
        'bt_salon',
        'bt_frontpeak',
        'bt_dbberth',  
    );

        foreach ($all_keys as $key) 
        {

                update_post_meta($post_id, $key, $general_info[$key]);
          
        }


    }


    function deleteAttachmentFromPost($post_id){

        $args = array(
                      'post_type' => 'attachment',
                      'numberposts' => -1,
                      'post_status' => null,
                      'post_parent' => $post_id
                      );

        $attachments = get_posts( $args );
        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                wp_delete_attachment($attachment->ID);
            }
        }

    }


    function saveImages($general_info, $post_id)
    {

      
        //$timecur=new DateTime(date("d.m.Y H:i"));
        if(isset($general_info['picts']['pict']['@attributes']))
        { 
            update_post_meta($post_id,"images_link",$general_info['picts']['pict']['@attributes']['link']);
            update_post_meta($post_id,"images_total",1);
            
        }
        elseif(count($general_info['picts']['pict'])>1)
        {
            $i=0;
            foreach($general_info['picts']['pict'] as $key=>$image_link)
            {
                $link = update_post_meta($post_id,"images_link_".$i,$image_link['@attributes']['link']);
                $i++;
            }
            update_post_meta($post_id,"images_total",count($general_info['picts']['pict']));
        }
        else
        {
            update_post_meta($post_id,"images_total",0);
        }
        //    $i=0;
          //  foreach($general_info['picts']['pict'] as $key=>$image_link)
          //  {
          //      $link = update_post_meta($post_id,"images_link_".$i,true);
          //  }
          //  update_post_meta($post_id,"images_total",count($general_info['picts']['pict']));
            /*$count_done=0;
             $all_images = get_post_meta($post_id,'images_total',true);
             $all_done = get_post_meta($post_id,'total_images_done',true);

             if ($all_images==count($general_info['picts']['pict']) && ($all_done<$all_images || empty($all_done)))
             {
                        for($i=0;$i<$all_images;$i++)
                        {
                            $link = get_post_meta($post_id,"images_link_".$i,true);
                            $done = get_post_meta($post_id,"images_link_done_".$i,true);
                            if ($count_done<4)
                            {
                                if (empty($done))
                                {
                                    //media_sideload_image($link, $post_id); 
                                    //update_post_meta($post_id,"images_link_done_".$i,1);
                                    $count_done++;
                                }
                            }
                            else
                            {
                               break; 
                            }
                        }
                         update_post_meta($post_id,"images_date",$timecur->getTimestamp()); 
                         update_post_meta($post_id,"total_images_done",$count_done);
             }
             else
             {
                if($all_images<count($general_info['picts']['pict']))
                {
                    $i=0;
                    foreach ($general_info['picts']['pict'] as $pict) 
                    {
                        update_post_meta($post_id,"images_link_".$i,$pict['@attributes']['link']);
                        $i++;
                    }
                     update_post_meta($post_id,"images_date",$timecur->getTimestamp());
                }
                update_post_meta($post_id,"images_total",count($general_info['picts']['pict']));
             }   */
        //}  
        //else
        //{
            //update_post_meta($post_id,"images_total",0);
             //update_post_meta($post_id,"total_images_done",0);
             //update_post_meta($post_id,"images_date",$timecur->getTimestamp()); 
        //}
       
   
        if (isset($general_info['plans']['plan']['@attributes']))
        {
           update_post_meta($post_id,"plan_link",$general_info['plans']['plan']['@attributes']['link']); 
           update_post_meta($post_id,"plans_num",1);
        }
        elseif(count($general_info['plans']['plan'])>1)
        {
            $i=0;
            foreach($general_info['plans']['plan'] as $key=>$image_link)
            {
                $link = update_post_meta($post_id,"plan_link_".$i,$image_link['@attributes']['link']);
                $i++;
            }
            update_post_meta($post_id,"plans_num",count($general_info['plans']['plan']));
        }
        else
        {
            update_post_meta($post_id,"plans_num",0);
        }

     

        if (isset($general_info['homeport']['@attributes']))
        {
            update_post_meta($post_id,"homeport_num",1);
            update_post_meta($post_id,"homeport",$general_info['homeport']['@attributes']['name']);
            update_post_meta($post_id,"homeport_id_country",$general_info['homeport']['@attributes']['id_country']);
            update_post_meta($post_id,"homeport_id",$general_info['homeport']['@attributes']['id_base']);
            update_post_meta($post_id,"price",json_encode($general_info['homeport']['prices']));
            
        }
        elseif(count($general_info['homeport'])>1)
        {

            update_post_meta($post_id,"homeport_num",count($general_info['homeport']));
            $i=0;
            foreach($general_info['homeport'] as $key=>$homeport)
            {
                update_post_meta($post_id,"homeport_".$i,$homeport['@attributes']['name']);
                update_post_meta($post_id,"homeport_id_country_".$i,$homeport['@attributes']['id_country']);
                update_post_meta($post_id,"homeport_id_".$i,$homeport['@attributes']['id_base']);
                update_post_meta($post_id,"price_".$i,json_encode($homeport['prices']));
                $i++;
            }
        }
        else
        {
            update_post_meta($post_id,"homeport_num",0);    
        }
        

    }
    
    
    /***********************function for export all boats*******************************/
    function exportXML()
    {
        //find all posts from boat custom type
         $args = array(
                      'post_type' => 'boat_page',
                      'numberposts' => 50,
                      'orderby'=>'ID'
                      );

        $posts = get_posts($args);
         $xml = new DOMDocument('1.0');

         $upload_folder=wp_upload_dir();
         $xml->formatOutput = true;

        foreach ($posts as $boat=>$data)
        {
           $xml_boat = $xml->createElement("boat");
           $xml->appendChild($xml_boat);
           //Sedna ID
           $sedna_id=get_post_meta($data->ID, 'id_boat',true);
           $xml_boat_id = $xml->createElement("boat_id",$sedna_id);
           $xml_boat->appendChild($xml_boat_id);
           //Model
           $model=get_post_meta($data->ID, 'model',true);
           $xml_model = $xml->createElement("model",$model);
           $xml_boat->appendChild($xml_model);
           //Name
           $name=get_post_meta($data->ID, 'name',true);
           $xml_name = $xml->createElement("model",$name);
           $xml_boat->appendChild($xml_name);
           //Operator company
           $company=get_post_meta($data->ID, 'ope_company',true);
           $xml_company = $xml->createElement("company",$company);
           $xml_boat->appendChild($xml_company);
           //Type
           $type=get_post_meta($data->ID, 'bt_type',true);
           $xml_type = $xml->createElement("type",$type);
           $xml_boat->appendChild($xml_type);
           //Length
           $length=get_post_meta($data->ID, 'widthboat',true);
           $xml_length = $xml->createElement("widthboat",$length);
           $xml_boat->appendChild($xml_length);
           //Double cabins
           $dbcabins=get_post_meta($data->ID, 'nbdoucabin',true);
           $xml_dbcab = $xml->createElement("dbcabins",$dbcabins);
           $xml_boat->appendChild($xml_dbcab);
           //Single cabins
           $scabins=get_post_meta($data->ID, 'nbsimcabin',true);
           $xml_scab = $xml->createElement("scabins",$scabins);
           $xml_boat->appendChild($xml_scab);
           //WC
           $toilets=get_post_meta($data->ID, 'heads',true);
           $xml_toil = $xml->createElement("heads",$toilets);
           $xml_boat->appendChild($xml_toil);
           //Showers
           $baths=get_post_meta($data->ID, 'nbbathroom',true);
           $xml_bath = $xml->createElement("bathrooms",$baths);
           $xml_boat->appendChild($xml_bath);
           //Passangers
           $passengers=get_post_meta($data->ID, 'nbper',true);
           $xml_pass = $xml->createElement("passengers",$passengers);
           $xml_boat->appendChild($xml_pass);
           //Built year
           $builtyear=get_post_meta($data->ID, 'buildyear',true);
           $xml_year = $xml->createElement("builtyear",$builtyear);
           $xml_boat->appendChild($xml_year);
           //Reffit year
           $reffityear=get_post_meta($data->ID, 'reffitedyear',true);
           $xml_ref = $xml->createElement("reffityear",$reffityear);
           $xml_boat->appendChild($xml_ref);
           //Crew
           $crew=get_post_meta($data->ID, 'crew',true);
           $xml_crew = $xml->createElement("crew",$crew);
           $xml_boat->appendChild($xml_crew);
           //Homeport
           $homeport=get_post_meta($data->ID, 'homeport',true);
           $xml_homeport = $xml->createElement("homeport",$homeport);
           $xml_boat->appendChild($xml_homeport);
           //Price
           $prices=json_decode(get_post_meta($data->ID, 'price',true),true);
           $xml_prices = $xml->createElement("prices");
           foreach ($prices as $key=>$price)
           {
                $xml_price = $xml->createElement("price");
                $xml_prices->appendChild($xml_price);
                $xml_amount = $xml->createElement("amount",$price['@attributes']['amount']);
                $xml_price->appendChild($xml_amount);
                $xml_datest = $xml->createElement("datestart",$price['@attributes']['datestart']);
                $xml_price->appendChild($xml_datest);
                $xml_dateend = $xml->createElement("dateend",$price['@attributes']['dateend']);
                $xml_price->appendChild($xml_dateend);
           }
           $xml_boat->appendChild($xml_prices);
           
           $id=$data->ID;
        }
        //$save=$xml->saveXML()

        $xml->save($upload_folder['basedir'].'/xml/export_boats.xml');


    }



}



$events = array(
'availabilities',
'scrape_details',
'scrape_general_info',
'new');

foreach ($events as $event) {
    add_action( $event, 'doItNow');
}

function handleTasks($input){
    $Import = new BoatImport;
    if ($input['initial_scrape'] == true) {
        $Import->initialScrape();
        return;
    }

    /*if ($input['when'] == 'Schedule'){
        scheduleTaskInput($input);
    }else{
        doItNow($input);
    }*/
    
    
    if ($input['export'] == true) {
        doItNow($input);
        return;
    }

}


/*function scheduleTaskInput($input){

    $seconds   = (int)$input['days_plus_schedule'] * 24 * 3600;
    $timestamp = mktime((int)$input['start_hours'], (int)$input['start_minutes'], 0,  date("n", mktime() + $seconds),  date("j", mktime() + $seconds), date("Y", mktime() + $seconds));
    wp_schedule_event($timestamp, $input['schedule_interval'], $input['action'], $input);

    $all_crons = get_option('all_crons');
    if(!$all_crons){
        $all_crons = array();
    }else{
        $all_crons = unserialize($all_crons);
    }

    $all_crons[] = array('input' => $input,
                         'datestart' => date('Y-m-d H:i:s', $timestamp),
                         'interval'  => $input['schedule_interval'],
                         'latest_id' => 0,
                         'action' => $input['action']
                         );
    $all_crons = serialize($all_crons);
    update_option('all_crons', $all_crons);
}*/


/***********************88functions for implementing user actions*********************/

function doItNow($input){

    $Import = new BoatImport;

    switch ($input['action']){

        case 'scrape_details':
            //$Import->scrapeAllDetails($input);
            //break;
        case 'scrape_general_info':
            //$Import->scrapeImages($input);
            break;
        case 'availabilities':
            //$Import->scrapeAvailablity($input);
            break;
        case 'single_scrape':
            //$Import->updateBoatById($input);
            break;
        case 'export':
            $Import->exportXML();
            break;
    }

}

function showCrons($action){
    $all_crons = get_option('all_crons');
    $all_crons = unserialize($all_crons);
    foreach ($all_crons as $cron) {
        if ($cron['action'] == $action) {
            echo "{$cron['datestart']} {$cron['interval']} {$cron['action']}<br>";
        }
    }
    
 

}



