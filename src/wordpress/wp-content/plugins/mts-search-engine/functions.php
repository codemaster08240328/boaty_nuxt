<?php
/**
 * Plugin Name: MTS Search engine
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: To use search engine for boat order the short code must be inserted in page content
 * Version: beta version 1.1
 * Author: Olga Zhilkova
 * Author URI: http://URI_Of_The_Plugin_Author
 * License: A "Slug" license name e.g. GPL2 2014
 */
//defined('ABSPATH') or die("No script kiddies please!");
/*
beta version 1.1
the main functions are:
   1) front form: short code [MTS_SEARCH_FRONT second_page=''] with the attribute "second_page" for the page of the search results
   2) the main results: short code [MTS_SEARCH_ENGINE]
    - shows list of all yacht charters
    - heading section for input new data for the next search
    - refine search with the quick functions to make current results more narrow
    - boat description page with availability section
    - download, sharing functions for boat description
   3) Request modal form is used to reserve and save search results
   4) Caching the destinations for fast autocomplete field
*/
//class for implementing search engine
class MTS_API {
    
    
    protected $pluginPath;
    protected $pluginUrl;
    protected $dsn;
    protected $conn;
    protected $language;
    protected $WEB_ROOT;
    protected $SEDNA_CFG;
    protected $g_GET;
    protected $mts_query;
    protected $boat_details;
    protected $boat_desc;
    protected $built_desc;
    protected $reserve_desc;
    protected $cabins_desc;
    
    protected $seasonprice;
    protected $extraprices;
    protected $plans;
    protected $characts;
    protected $location;
    protected $pdf_fonts;
    protected $used_ip='';
    protected $api_id='';
    //array with last serach parameters
    protected $last_search;
    //link for back serach results from cache
    protected $html_link_search;
    protected $map_loc;
    protected $cache_url;
    protected $cache_api;
    protected $cache_booker;
    protected $cache_dest;
    protected $cache_country;
    protected $show_booker_boats;
    protected $struct_booker_desc;
    protected $cache_oper;
    protected $sedna_results;
    protected $search_base;
    protected $search_total;
    protected $res_country;
    protected $boat_structure;
     
    
    /* Cache Database details */
    const DBHOST = 'localhost';
    const DBNAME = 'sailu_mts_boats';
    const DBUSER = 'sailu_mtsboats';
    const DBPASS = '5ft2zXhVDM-L?';
    const BOOKER_USER='18abb2dc5849491eaaa06ab3d4fb1dc2';
    const BOOKER_PASSWORD='2ec90d50df594e419c3e52088f947556';
  

    
    //include configuration variables 
    public function __construct()
    {  
        //error_reporting(E_ALL);
        
        
        $this->struct_booker_desc=array();
        
        // Set Plugin Path
        $this->pluginPath = dirname(__FILE__);
        //path for caching the destinations
        $this->cache_dest=$_SERVER["DOCUMENT_ROOT"]."/wp-content/uploads/xml/destinations.json";
        $this->cache_country=$_SERVER["DOCUMENT_ROOT"]."/wp-content/uploads/xml/countries.json";
        
        
        $this->cache_oper=$_SERVER["DOCUMENT_ROOT"]."/wp-content/uploads/xml/operators.json";
        $this->cache_booker=$_SERVER["DOCUMENT_ROOT"]."/wp-content/uploads/xml/bookerdata.json";
        $this->booker_dest=$_SERVER["DOCUMENT_ROOT"]."/wp-content/uploads/xml/iso_countries.xml";
        $this->boat_structure=array();
        
        //$this->update_list_taxonomy($this->cache_dest,'destination');
        $this->update_list_taxonomy($this->cache_country,'country');


        
     
        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/mts-search-engine';
        

        # sedna cfg Where language : 0- English 1- French 2- Italian 3- Spanish 4- German 5- Swedish 6- Croatian 7- Norwegian 8- Finnish 9- Dutch 10- Slovak 11- Cesky 12- Danish 
        $this->language = isset($_REQUEST['mts_language']) ? $_REQUEST['mts_language'] : ( isset($_SESSION['mts_language']) ? $_SESSION['mts_language'] : 0 ) ;

        $this->SEDNA_CFG=json_encode(array('broker_id'=>'wxft6043', 'language'=> $this->language));
        
        /* Connect to the cache Database */
        include    dirname(__FILE__).'/libs/adodb5/adodb.inc.php';
        
		 add_shortcode('bootstrap-slider-code', array($this, 'shortcode_bootstrap_slider'));
        /* Short code for first front form */
        add_shortcode('mts_search', array($this, 'shortcode_front'));
        
        /* Short code for boat model page */
        add_shortcode('mts_boat_model', array($this, 'shortcode_boat_model'));
        
        
         /* Short code for serach results */
        add_shortcode('mts_search_result', array($this, 'shortcode_mts'));
        
        /* Short code for serach results */
        add_shortcode('mts_landing_form', array($this, 'shortcode_landing_form'));
        
        
        /* Short code for boat images */
        add_shortcode('boat_images', array($this, 'boat_images'));
         
         /* Short code for boat extra prices */
        add_shortcode('boat_extra', array($this, 'boat_extraprices'));
        
         /* Short code for boat prices */
        add_shortcode('boat_price', array($this, 'boat_prices'));
        
         /* Short code for boat equipment */
        add_shortcode('boat_equipment', array($this, 'boat_equipment'));
        
         /* Short code for boat Google map */
        add_shortcode('boat_map', array($this, 'boat_googlemap'));
        
         /* Short code for boat availability */
        add_shortcode('boat_availability', array($this, 'boat_availabilityblock'));
        
        /* Short code for boat search header */
        add_shortcode('boat_top_search', array($this, 'header_search'));
        
        //functions for dysplaying pdf desription of boat  
        add_action( "wp_ajax_download", array($this, 'download_callback'));
        add_action('wp_ajax_nopriv_download', array($this, 'download_callback'));
        
 
         add_action('wp_enqueue_scripts', array($this,'my_add_frontend_scripts'));
         add_action('wp_enqueue_scripts', array($this,'include_styles_boat_model')); // now just run the function

         
        //memorizing search parameters for caching the data
        $this->last_search=array();
        //forming back link to search results from cache
        $this->html_link_search='';
        if (isset($_GET['dst']) && !empty($_GET['dst']))
        {
            if(isset($_GET['date_to']))
            {
                
                $this->last_search['date_to'] = urldecode($_GET['date_to']);
 		    }
            if(isset($_GET['date_from']))
            {
                $this->last_search['date_from'] = urldecode($_GET['date_from']);
 		    }
            if( isset( $_GET['bt_type'] ))
            {
                $this->last_search['bt_type'] = $_GET['bt_type'];
            }

            $this->last_search['dst']=$_GET['dst'];	
            
        }

            
        if (isset($this->last_search['dst']))
        {
            $this->html_link_search='http://'.$_SERVER['SERVER_NAME'].'/yacht-search/?action=search&'.urldecode(http_build_query($this->last_search));
        }


     }
     
    
    //styles for boat model post
	
	public function shortcode_bootstrap_slider(){
					wp_register_style("boatstrap-slider-css", WP_PLUGIN_URL."/mts-search-engine/js/bootstrap/css/bootstrap-slider.css", array(), false, 'all');	
            wp_enqueue_style("boatstrap-slider-css");
		wp_register_script("mts-bootstrap-slider", WP_PLUGIN_URL."/mts-search-engine/js/bootstrap-slider.js", array('jquery'), null, false);
            wp_enqueue_script("mts-bootstrap-slider");
			$html = "";
			$html = '<input id="ex13" type="text" data-slider-ticks="[0, 100, 200, 300, 400]" data-slider-ticks-snap-bounds="30" data-slider-ticks-labels="["$0", "$100", "$200", "$300", "$400"]"/>';
			return $html;
	}
	
    public function include_styles_boat_model() 
    {
        if(stripos($_SERVER['REQUEST_URI'],'boat_model')!==false)
        {
		  $boat_model_style = WP_PLUGIN_URL . '/mts-search-engine/css/boat_model.css';

            wp_register_style('boat_model_style', $boat_model_style,array(), false, 'all');
            wp_enqueue_style( 'boat_model_style');
			
        }

    }
    
    
    /*****************************short codes for interactive html forms********************************/
    //form for landing page of catamaran charter
    public function shortcode_landing_form($atts)
    {        
         //additional parameters for language
         $a = shortcode_atts( 
                array(
                    'lang' => '',
                ), 
                $atts );
                
        $land_page_style = WP_PLUGIN_URL . '/mts-search-engine/css/land_page.css';
        wp_register_style('landing_page', $land_page_style,array(), false, 'all');
        wp_enqueue_style( 'landing_page');
        
        
        wp_register_script("mts-land",WP_PLUGIN_URL."/mts-search-engine/js/landing_page.js", array('jquery'), false, true);
        wp_enqueue_script("mts-land"); 
        wp_localize_script('mts-land', 'MTSAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );


        
        $html='';
        switch ($a['lang']) 
        {
            case 'ru':
                $html .='<div id="request_form">';
                
                $html .='<h3 class="ui-accordion-header">'.iconv('windows-1251','utf-8','Аренда катамарана').'</h3>';
                $html .='<form method="POST">';
                $html .='<div class="mess">';
                $html .=iconv('windows-1251','utf-8','Пожалуйста, заполните все поля!');
                $html .='</div>';
                $html .='<label for="name_user" />'.iconv('windows-1251','utf-8','Ваше имя').'</label>';
                $html .='<input id="name_user" name="name_user" value="" placeholder="name"/>';
                $html .='<label for="email_user" />'.iconv('windows-1251','utf-8','Ваш email').'</label>';
                $html .='<input id="email_user" name="email_user" value="" placeholder="email"/>';
                $html .='<div class="fields">';
                $html .=iconv('windows-1251','utf-8','Все поля являются обязательными!');
                $html .='</div>';
                $html .='<button class="ui-button">';
                $html .='<span class="ui-button-text">'.iconv('windows-1251','utf-8','Отправить заявку').'</span>';
                $html .='</button>';
                $html .='</form>';
                $html .='<div class="sussex">';
                $html .=iconv('windows-1251','utf-8','Спасибо! Ваша заявка отправлена!');
                $html .='</div>';
                $html .='</div>'; 
                break;
            default:
                $html .='<div id="request_form">';
                $html .='<h3 class="ui-accordion-header">Catamaran Charter</h3>';
                $html .='<form method="POST">';
                $html .='<div class="mess">Please, fill all fields!</div>';
                $html .='<label  for="name_user" />Name</label>';
                $html .='<input id="name_user" name="name_user" value="" placeholder="name"/>';
                $html .='<label for="email_user" />Email</label>';
                $html .='<input id="email_user" name="email_user" value="" placeholder="email"/>';
                $html .='<div class="fields">All fields are required!</div>';
                $html .='<button class="ui-button">';
                $html .='<span class="ui-button-text">Send request</span>';
                $html .='</button>';
                $html .='</form>';
                $html .='<div class="sussex">';
                $html .=iconv('windows-1251','utf-8','Thanks! Your request was send!');
                $html .='</div>';
                $html .='</div>';
                break;
            
        }

        return $html;
    }

     /******************************short codes for each part of boat details***************************/
     //short code for boat booker boat images
     public function boat_images($attr)
     {
        //including scripts for carousel slider
        wp_register_style("mts-boats",WP_PLUGIN_URL."/mts-search-engine/css/boat_images.css", array(), false, 'all');
        wp_enqueue_style("mts-boats");
        wp_register_script("mts-bootstrap", WP_PLUGIN_URL."/mts-search-engine/js/bootstrap/js/bootstrap.min.js", array('jquery'), false, true);
        wp_enqueue_script("mts-bootstrap");
        
               
        $html='';
        $postid = get_the_ID();
        
        //Added: 7/27/2015 - fixyah for wpml not active { added if cond below }. ..
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        
        if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') ) {
             $post_language_information = wpml_get_language_information($postid);
        }
        else {
             $post_language_information = array();
        }
        //end here -kirame09
        
        $boat_id=get_post_meta($postid,'id_boat',true);
        $model_id=get_post_meta($postid,'ModelID',true);
        $country_name='';
        if(isset($post_language_information['locale']) && $post_language_information['locale']==="ru_RU")
            {
                if(!empty($boat_id))
                {
                    //getting all boat fields with images for carousel slider
                    $boat_fields=$this->get_boat_fields($postid,'ru');
                    if(!empty($boat_fields['Country']))
                    {
                        $country_name=', '.$boat_fields['Country'];   
                    }
                    
                    $str_search =iconv('windows-1251','utf-8','аренда яхты');
                    $boat_title=$boat_fields['BoatModel'].' '.$boat_fields['BoatType'];
                    if(!empty($boat_title) && trim($boat_title)!=='')
                    {
                        $html .='<h1>'.$str_search.' '.$boat_title.$country_name.', '.
                                $boat_fields['Location'].', '.$boat_fields['Homeport'].'</h1>';
                    }
                    else
                    {
                        $html .='<h1>'.$str_search.$country_name.', '.$boat_fields['Location'].', '.
                                $boat_fields['Homeport'].'</h1>';
                    }
                    
                    //boat images from Booker request
                    $query_boats=json_decode(file_get_contents('https://api.boatbooker.net/ws/sync/v2/main?username=18abb2dc5849491eaaa06ab3d4fb1dc2&password=2ec90d50df594e419c3e52088f947556&loadBoats=true&loadSpecificBoats='.$boat_id));
                    if(isset($query_boats->Boats[0]) && count($query_boats->Boats[0])==1)
                    {
                        $html .='<div class="panel panel-default">'.
                        '<div class="panel-heading">'.
                        '<h3 class="panel-title" id="overview" >Overview</h3>'.
                        '</div>'.
                        '<div class="panel-body">';
                        $all_images=$query_boats->Boats[0]->BoatImages;
                        if(!empty($all_images))
                        {
                            echo 'Images</br >';
                        }
                        else
                        {
                            $arr_model=array('post_type' => 'boat_model',
		                              'meta_key' => 'ModelID',
                                      'meta_value'=>$model_id,
                                      'meta_compare'=>'=',
                                      'meta_type'=>'CHAR');
                            $model_post=get_posts($arr_model);
                            if(count($model_post)>0)
                            {
                                $args = array(
                                'post_type' => 'attachment',
                                'numberposts' => -1,
                                'post_status' => null,
                                'post_parent' => $model_post[0]->ID);

                                $attachments = get_posts( $args );
                                 if ( $attachments ) 
                                {
                                    foreach ( $attachments as $attachment ) 
                                    {
                                        $html .='<div id="respons_image">';
                                        $html .=wp_get_attachment_image( $attachment->ID, 'full' );
                                        $html .='</div>';
                                    //title of type of image
                                    }
                                }
                                 //echo '<p>Boat number <a href="'.get_permalink($model_post[0]->ID).'">'.$model_post[0]->ID.'</a> was updated!</p>';
                  
                            }
                            else
                            {
                                echo 'Nothing';
                            }
                        }
                        $this->boat_structure=$this->form_desc_data('booker_post_ru',$boat_fields);
                        $html .='<hr /><p>'.$this->descriptions('main_ru',$this->boat_structure).'</p>';
                        $html .='<p>'.$this->descriptions('reservation_ru',$this->boat_structure,'').'</p>';
                        $html .='<p class="vital" >'.$this->descriptions('cabins_ru',$this->boat_structure,'').'</p>';
    
                        //if (!empty($this->boat_structure['operator']))
                        //{
                        //    $html .='<p class="vital">Fleet operator: '.$this->boat_structure['operator'].'</p>';
                        //}

                        $html .='<hr class="bot_desc"/></div></div>';
                    }
                    
                    
                    
       
                }
                //showing boat post on Russian language
            }
        else
        {

        //getting all boat fields with images for carousel slider
        $boat_fields=$this->get_boat_fields($postid);

        if(!empty($boat_fields['Country']))
        {
            $country_name=', '.$boat_fields['Country'];   
        }
        if(!empty($boat_fields['Homeport']))
        {
            $homeport_name=' in '.$boat_fields['Homeport'];   
        }
        
        //heading
        $html .='<h1>'.$boat_fields['BoatModel'].' '.$boat_fields['BoatType'].$homeport_name.$country_name.'</h1>';
        
        $html .='<div class="panel panel-default">'.
                        '<div class="panel-heading">'.
                        '<h3 class="panel-title" id="overview" >Overview</h3>'.
                        '</div>'.
                        '<div class="panel-body">';
        $all_images=count($boat_fields['Images']);
        if(!empty($all_images) && $all_images>0)
        {
            
            $html .='<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">'.
    			  '<ol class="carousel-indicators">';
            for ($i=0;$i<$all_images;$i++)
            {
                $html .='<li data-target="#carousel-example-generic" data-slide-to="'.$i.'" class="';
                if ($i == 0)
                {
                    $html .='active';
                }
                else
                {
                    $html .='';
                } 
                $html .='"></li>';
            }

            $html .='</ol>';

			$html .='<div class="carousel-inner">';

			for($i=0;$i<$all_images;$i++)
            {
                $html .='<div class="item ';
                 if ($i == 0)
                 {
                    $html .='active';
                 }
                 else
                 {
                    $html .='';
                 } 
                 
                 $html .='">';
                 
                 $html .='<img class="mts_slider_img" src="'.$boat_fields['Images'][$i].'" />';
                 $html .='</div>';
            }
            
            $html .='</div>';

		     $html .='<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">';
              $html .='<span class="glyphicon glyphicon-chevron-left"></span>';
              $html .='</a>';

			 $html .='<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">';

			 $html .='<span class="glyphicon glyphicon-chevron-right"></span>';

			  $html .='</a></div>';
   
            
        }
        
        //detailed description about boat appearance
        $this->boat_structure=$this->form_desc_data('booker_post',$boat_fields);
        $html .='<hr /><p>'.$this->descriptions('main',$this->boat_structure,'').'</p>';
        $html .='<p>'.$this->descriptions('reservation',$this->boat_structure,'').'</p>';
        $html .='<p class="vital" >'.$this->descriptions('cabins',$this->boat_structure,'').'</p>';
    
        if (!empty($this->boat_structure['operator']))
        {
            $html .='<p class="vital">Fleet operator: '.$this->boat_structure['operator'].'</p>';
        }

        $html .='<hr class="bot_desc"/></div></div>';
    }
        
        return $html;
    }
    
    
    //short code for boat booker boat images
     public function operator_boats($boats)
     {
        //including scripts for carousel slider
        wp_register_style("mts-bootstrap",WP_PLUGIN_URL."/mts-search-engine/js/bootstrap/css/bootstrap.min.css", array(), false, 'all');
        wp_enqueue_style("mts-bootstrap");
        wp_register_style("mts-boats",WP_PLUGIN_URL."/mts-search-engine/css/boat.css", array(), false, 'all');
        wp_enqueue_style("mts-boats");
        $html ='';
        $html .='<style>.ope_boats{width: 500px; height: 400px;margin: 20px 50px 10px 50px; overflow: auto; border: 0px solid #fff;}</style>';
        if(!empty($boats))
        {
            $num_boats=count($boats);
            $html .='<div class="ope_boats">';
            $html .='<div class="panel panel-default">'.
                        '<div class="panel-heading">'.
                        '<h3 class="panel-title" id="overview" >Boats</h3>'.
                        '</div>'.
                        '<div class="panel-body">';
            $html .='<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">'.
    			  '<ol class="carousel-indicators">';
            $j=0;
            for ($i=0;$i<$num_boats;$i++)
            {
                if(isset($boats[$i]['Images']) && (count($boats[$i]['Images'])>0))
                {
                $html .='<li data-target="#carousel-example-generic" data-slide-to="'.$j.'" class="';
                if ($j == 0)
                {
                    $html .='active';
                }
                else
                {
                    $html .='';
                } 
                $html .='"></li>';
                $j++;
                }
            }

            $html .='</ol>';

			$html .='<div class="carousel-inner">';
            $k=0;
			for($i=0;$i<$num_boats;$i++)
            {
                if(isset($boats[$i]['Images']) && (count($boats[$i]['Images'])>0))
                {
                $html .='<div class="item ';
                 if ($k == 0)
                 {
                    $html .='active';
                 }
                 else
                 {
                    $html .='';
                 } 
                 
                 $html .='">';
                 $html .='<img class="mts_slider_img" src="'.$boats[$i]['Images'][0].'" />';
                 $html .='</div>';
                 $k++;
                 }
            }
            
            $html .='</div>';

		     $html .='<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">';
              $html .='<span class="glyphicon glyphicon-chevron-left"></span>';
              $html .='</a>';

			 $html .='<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">';

			 $html .='<span class="glyphicon glyphicon-chevron-right"></span>';

			  $html .='</a></div>';
   
            $html .='</div></div>';
            $html .='</div>';

        }        
        
        
        return $html;
        
     }
    
    
    //function to get all boat images
    public function get_boat_fields($postid,$lg='en')
    {
        $boat_fields=array();
        //checking if boat has any photos
        $boat_images=array();
        $boat_database=''; 
        $boat_fields['YearBuilt']=get_post_meta($postid,'YearBuilt',true);
        if(empty($boat_fields['YearBuilt']))
        {
            $boat_fields['YearBuilt']=get_post_meta($postid,'buildyear',true);
            if(!empty($boat_fields['YearBuilt']))
            {
               $boat_database='sedna';  
            }
        }
        else
        {
            $boat_database='booker';  
        }
        if($boat_database!=='')
        {
            if($boat_database==='booker')
            {
                if($lg==='ru')
                {
                    $boat_fields['Engine']=get_post_meta($postid,'Engine',true);
                    $boat_fields['Draft']=get_post_meta($postid,'Draft',true);
                    $boat_fields['CabinsBasic']=get_post_meta($postid,'CabinsBasic',true);
                    $boat_fields['CabinsMax']=get_post_meta($postid,'CabinsMax',true);
                    $boat_fields['CabinsStr']=get_post_meta($postid,'CabinsStr',true);
                    $boat_fields['BerthsBasic']=get_post_meta($postid,'BerthsBasic',true);
                    $boat_fields['BerthsMax']=get_post_meta($postid,'BerthsMax',true);
                    $boat_fields['BerthsStr']=get_post_meta($postid,'BerthsStr',true);
                    $boat_fields['ToiletsBasic']=get_post_meta($postid,'ToiletsBasic',true);
                    $boat_fields['ToiletsMax']=get_post_meta($postid,'ToiletsMax',true);
                    $boat_fields['ToiletsStr']=get_post_meta($postid,'ToiletsStr',true);
                    $boat_fields['BoatLength']=get_post_meta($postid,'Length',true);
                    $boat_fields['BoatCrew']=get_post_meta($postid,'HasCrew',true);
                    $model_id =get_post_meta($postid,"ModelID",true);
                    if(post_type_exists('boat_model') && !empty($model_id))
                    {
                         $arr_model=array('post_type' => 'boat_model',
		                              'meta_key' => 'ModelID',
                                      'meta_value'=>$model_id,
                                      'meta_compare'=>'=',
                                      'meta_type'=>'CHAR');
                        $model_post = get_posts($arr_model);
                        if(!empty($model_post) && count($model_post)==1)
                        {
                            //getting model fields
                            if(empty($boat_fields['CabinsBasic']))
                            {
                                $boat_fields['CabinsBasic']=get_post_meta($model_post[0]->ID,'CabinsBasic',true);
                                $boat_fields['CabinsMax']=get_post_meta($model_post[0]->ID,'CabinsMax',true);
                                $boat_fields['CabinsStr']=get_post_meta($model_post[0]->ID,'CabinsStr',true);
                            }
                            if(empty($boat_fields['BerthsBasic']))
                            {
                                $boat_fields['BerthsBasic']=get_post_meta($model_post[0]->ID,'BerthsBasic',true); 
                            
                            }
                            if(empty($boat_fields['BerthsMax']))
                            {
                                $boat_fields['BerthsMax']=get_post_meta($model_post[0]->ID,'BerthsMax',true);
                            
                            }
                            if(empty($boat_fields['BerthsStr']))
                            {
                                $boat_fields['BerthsStr']=get_post_meta($model_post[0]->ID,'BerthsStr',true); 
                            }

                            if(empty($boat_fields['ToiletsBasic']))
                            {
                                $boat_fields['ToiletsBasic']=get_post_meta($model_post[0]->ID,'ToiletsBasic',true); 
                                $boat_fields['ToiletsMax']=get_post_meta($model_post[0]->ID,'ToiletsMax',true); 
                                $boat_fields['ToiletsStr']=get_post_meta($model_post[0]->ID,'ToiletsStr',true);  
                            }
                            if(empty($boat_fields['Draft']))
                            {
                                $boat_fields['Draft']=get_post_meta($model_post[0]->ID,'Draft',true);
                            }
                            if(empty($boat_fields['Engine']))
                            {
                                $boat_fields['Engine']=get_post_meta($model_post[0]->ID,'Engine',true);  
                            }
                            if(empty($boat_fields['BoatLength']))
                            {
                                $boat_fields['BoatLength']=get_post_meta($model_post[0]->ID,'Length',true);  
                            }
                        }
                        
                        //boat base
                        if(isset($_GET['dst']) && !empty($_GET['dst']))
                        {
                            $location=urldecode($_GET['dst']);
                            $boat_location=$this->get_boat_dest($postid,$location);
                        }
                        else
                        {
                            $boat_location=$this->get_boat_dest($postid);
                        }
                        if(!empty($boat_location))
                        {
                            $boat_fields['Homeport']=$boat_location['Homeport']; 
                            $boat_fields['Location']=$boat_location['Location']; 
                            $boat_fields['Country']=$boat_location['Country']; 
                        }
                        $boat_fields['BoatModel']=$this->get_boatmodel($postid);
                        $boat_fields['BoatType']=$this->get_boattype($postid);
                    }
                }
                else
                {
                    $all_images=get_post_meta($postid,'BoatImages',true);
                    if(!empty($all_images) && $all_images>0)
                    {
                        for($i=0;$i<$all_images;$i++)
                        {
                            $key_val='BoatImage_'.$i;
                            $boat_images[]=get_post_meta($postid,$key_val,true);
                        }
                    }
                    $boat_fields['Engine']=get_post_meta($postid,'Engine',true);
                    $boat_fields['Draft']=get_post_meta($postid,'Draft',true);
                    $boat_fields['CabinsBasic']=get_post_meta($postid,'CabinsBasic',true);
                    $boat_fields['CabinsMax']=get_post_meta($postid,'CabinsMax',true);
                    $boat_fields['CabinsStr']=get_post_meta($postid,'CabinsStr',true);
                    $boat_fields['BerthsBasic']=get_post_meta($postid,'BerthsBasic',true);
                    $boat_fields['BerthsMax']=get_post_meta($postid,'BerthsMax',true);
                    $boat_fields['BerthsStr']=get_post_meta($postid,'BerthsStr',true);
                    $boat_fields['ToiletsBasic']=get_post_meta($postid,'ToiletsBasic',true);
                    $boat_fields['ToiletsMax']=get_post_meta($postid,'ToiletsMax',true);
                    $boat_fields['ToiletsStr']=get_post_meta($postid,'ToiletsStr',true);
                    $boat_fields['BoatLength']=get_post_meta($postid,'Length',true);
                    $boat_fields['BoatCrew']=get_post_meta($postid,'HasCrew',true);
                
                    //getting model photos and fieldxs
                    $model_id =get_post_meta($postid,"ModelID",true);
                    if(post_type_exists('boat_model') && !empty($model_id))
                    {
                        $arr_model=array('post_type' => 'boat_model',
		                              'meta_key' => 'ModelID',
                                      'meta_value'=>$model_id,
                                      'meta_compare'=>'=',
                                      'meta_type'=>'CHAR');
                        $model_post = get_posts($arr_model);
                    if(!empty($model_post) && count($model_post)==1)
                    {
                        //getting model images
                        if(empty($all_images) || $all_image==0)
                        {
                            $all_images=get_post_meta($model_post[0]->ID,'ModelImages',true);
                            for($i=0;$i<$all_images;$i++)
                            {
                                $key_val='ImageURL_'.$i;
                                $boat_images[]=get_post_meta($model_post[0]->ID,$key_val,true);
                            }  
                        }
                
                        //getting model fields
                        if(empty($boat_fields['CabinsBasic']))
                        {
                            $boat_fields['CabinsBasic']=get_post_meta($model_post[0]->ID,'CabinsBasic',true);
                            $boat_fields['CabinsMax']=get_post_meta($model_post[0]->ID,'CabinsMax',true);
                            $boat_fields['CabinsStr']=get_post_meta($model_post[0]->ID,'CabinsStr',true);
                        }
                        if(empty($boat_fields['BerthsBasic']))
                        {
                            $boat_fields['BerthsBasic']=get_post_meta($model_post[0]->ID,'BerthsBasic',true); 
                            
                        }
                        if(empty($boat_fields['BerthsMax']))
                        {
                            $boat_fields['BerthsMax']=get_post_meta($model_post[0]->ID,'BerthsMax',true);
                            
                        }
                        if(empty($boat_fields['BerthsStr']))
                        {
                             $boat_fields['BerthsStr']=get_post_meta($model_post[0]->ID,'BerthsStr',true); 
                        }
                        
                        
                           
                        if(empty($boat_fields['ToiletsBasic']))
                        {
                            $boat_fields['ToiletsBasic']=get_post_meta($model_post[0]->ID,'ToiletsBasic',true); 
                            $boat_fields['ToiletsMax']=get_post_meta($model_post[0]->ID,'ToiletsMax',true); 
                            $boat_fields['ToiletsStr']=get_post_meta($model_post[0]->ID,'ToiletsStr',true);  
                        }
                        if(empty($boat_fields['Draft']))
                        {
                            $boat_fields['Draft']=get_post_meta($model_post[0]->ID,'Draft',true);
                        }
                        if(empty($boat_fields['Engine']))
                        {
                            $boat_fields['Engine']=get_post_meta($model_post[0]->ID,'Engine',true);  
                        }
                        if(empty($boat_fields['BoatLength']))
                        {
                            $boat_fields['BoatLength']=get_post_meta($model_post[0]->ID,'Length',true);  
                        }
                    }
                }
                
                //boat operator
                $ope=$this->boat_operator($postid);
                if(!empty($ope))
                {
                    $boat_fields['Operator']=$ope['Operator'];    
                    $boat_fields['OpeID']=$ope['OpeID'];    
                }
        
                //boat base
                if(isset($_GET['dst']) && !empty($_GET['dst']))
                {
                    $location=urldecode($_GET['dst']);
                    $boat_location=$this->get_boat_base($postid,$location);
                }
                else
                {
                    $boat_location=$this->get_boat_base($postid);
                }

                if(!empty($boat_location))
                {
                    $boat_fields['Homeport']=$boat_location['Homeport']; 
                    $boat_fields['Location']=$boat_location['Location']; 
                    $boat_fields['Country']=$boat_location['Country']; 
                }
                $boat_fields['BoatModel']=$this->get_boat_model($postid);
                $boat_fields['BoatType']=$this->get_boat_type($postid);
                }
            }
            else
            {
                $all_images = get_post_meta($postid,'images_total',true);
                $image = get_post_meta($postid,'images_link',true); 
                if(!empty($image))
                {
                    $boat_images[]=$image;   
                }
                else
                {
                    for ($i=0;$i<$all_images;$i++)
                    {
                        $key_val='images_link_'.$i;
                        $name_val=get_post_meta($postid,$key_val,true);
                        $boat_images[]=$name_val;   
                    }
                }
                $boat_fields['Engine']=get_post_meta($postid,'engine',true);
                $boat_fields['Draft']=get_post_meta($postid,'draft',true);
                $boat_fields['BoatModel']=get_post_meta($postid,'model',true);
                $boat_fields['BoatType']=get_post_meta($postid,'bt_type',true);
                $boat_fields['ToiletsBasic']=get_post_meta($postid,'heads',true);
                $boat_fields['SingleCabins']=get_post_meta($postid,'nbsimcabin',true); 
                $boat_fields['DoubleCabins']=get_post_meta($postid,'nbdoucabin',true);
                $boat_fields['BoatPrice']=get_post_meta($postid,'newprice',true);
                if(empty($boat_fields['BoatPrice']))
                {
                    //$boat_fields['BoatPrice']=$this->get_sedna_price($postid);
                }
                $boat_fields['BoatOldPrice']=get_post_meta($postid,'oldprice',true);
                $boat_fields['BoatDiscPrice']=get_post_meta($postid,'discount',true);
                $boat_fields['BerthsBasic']=0;
                if(!empty($boat_fields['DoubleCabins']))
                {
                    $boat_fields['CabinsMax']=$boat_fields['DoubleCabins'];
                    $boat_fields['CabinsStr']=$boat_fields['DoubleCabins'].' + ';
                    $boat_fields['BerthsBasic']=$boat_fields['DoubleCabins']*2;
                    $boat_fields['BerthsStr']=$boat_fields['BerthsBasic'];
                }
                else
                {
                    $boat_fields['CabinsStr']='0 + ';
                    $boat_fields['CabinsMax']=0;
                    $boat_fields['BerthsStr']='0 + ';
                }
                if(!empty($boat_fields['SingleCabins']))
                {
                    $boat_fields['CabinsBasic']=$boat_fields['SingleCabins'];
                    $boat_fields['CabinsStr'] .=$boat_fields['SingleCabins'];
                    $boat_fields['BerthsBasic'] +=$boat_fields['SingleCabins'];
                    $boat_fields['BerthsBasic'] .=' + '.$boat_fields['SingleCabins'];
                }
                else
                {
                    $boat_fields['CabinsStr'] .='0';
                    $boat_fields['BerthsBasic'] .=' + 0';
                }

                
                $boat_fields['BerthsStr']=get_post_meta($postid,'BerthsStr',true);
                $boat_fields['BoatLength']=get_post_meta($postid,'widthboat',true);
                $boat_fields['Operator']=get_post_meta($postid,'ope_company',true);
                $locations_sedna=$this->get_sedna_dest($postid);
                $boat_fields['Country']=$locations_sedna['Country'];
                $boat_fields['Homeport']=$locations_sedna['Homeport'];
            
            }
        }
        

        //getting boat fields
        $boat_fields['BoatID']=get_post_meta($postid,'id_boat',true);

        //boat images
        $boat_fields['Images']=$boat_images;
        
         
        return $boat_fields; 
    }
    
    //get boat type
    public function get_boat_type($postid)
    {
        $type_name='';
        $boat_type = wp_get_object_terms($postid,  'bt_type' );
        if ( ! is_wp_error($boat_type) ) 
        {
            $type_name=$boat_type[0]->name;
        }
        return $type_name;
    }
    
     //get boat type
    public function get_boattype($postid)
    {
        $type_name='';
        $boat_type = wp_get_object_terms($postid,  'boattype' );
        if ( ! is_wp_error($boat_type) ) 
        {
            $type_name=$boat_type[0]->name;
        }
        return $type_name;
    }
    
    
    public function get_sedna_dest($postid)
    {
        $locations=array();
        $boat_fields=array();
        $boat_fields['Country_ID']=get_post_meta($postid,'homeport_id_country',true);
        $boat_fields['Homeport_ID']=get_post_meta($postid,'homeport_id',true);
        $sedna_dest=json_decode(json_encode(simplexml_load_string(
                        file_get_contents('http://client.sednasystem.com/API/GetDestinations2.asp?refagt=wxft6043'))));
        $boat_fields['Homeport']='';
        $boat_fields['Country']='';
        foreach($sedna_dest as $dest)
        {
            foreach($dest as $attr)
                    {
                        //if search place in destination
                        if(isset($attr->name) && $boat_fields['Country_ID']==$attr->id_dest)
                        {
                            $dest_ids[]=$id_dest;
                            //return is of countries after finding id of dest
                        }
                        foreach($attr as $key2=>$attr2)
                        {
                                //if search place in country
                            if(isset($attr2->id_country) && $boat_fields['Country_ID']==$attr2->id_country)
                            {
                                  $boat_fields['Country']=$attr2->name; 
                            }
                            if($key2==='country')
                            {
                                foreach($attr2 as $key3=>$attr3)
                                {
                                    if(isset($attr3->id_country) && $boat_fields['Country_ID']==$attr3->id_country)
                                    {
                                        $boat_fields['Country']=$attr3->name;
                                    }
                                    else
                                    {
                                        foreach($attr3 as $key4=>$attr4)
                                        {
                                            if(isset($attr4->id_country) && $boat_fields['Country_ID']==$attr4->id_country)
                                            {
                                                $boat_fields['Country']=$attr4->name;
                                            }
                                            if($key4==='base')
                                            {
                                                foreach($attr4 as $key5=>$attr5)
                                                {
                                                    if(isset($attr5->id_base) && $boat_fields['Homeport_ID']==$attr5->id_base)
                                                    {
                                                        $boat_fields['Homeport']=$attr5->name;
                                                    }
                                                    else
                                                    {
                                                        if(!isset($attr5->id_base) && !isset($attr5->name))
                                                        {
                                                            foreach($attr5 as $key6=>$attr6)
                                                            {
                                                                if(isset($attr6->id_base) && $boat_fields['Homeport_ID']==$attr6->id_base)
                                                                {
                                                                    $boat_fields['Homeport']=$attr6->name;
                                                                }
                                                                
                                                            }
                                                        }

                                                    }
                                                        
                                                }
                                            }
                                        }
                                    }

                                    if($key3==='base')
                                    {
                                        if(isset($attr3->id_base)  && $boat_fields['Homeport_ID']==$attr3->id_base)
                                        {
                                            $boat_fields['Homeport']=$attr3->name;
                                        }
                                        else
                                        {
                                            if(!isset($attr3->id_base) && !isset($attr3->name))
                                            {
                                                foreach($attr3 as $key4=>$attr4)
                                                {
                                                    if(isset($attr4->id_base) && $boat_fields['Homeport_ID']==$attr4->id_base)
                                                    {
                                                        $boat_fields['Homeport']=$attr4->name;
                                                    }
                                                    else
                                                    {
                                                        foreach($attr4 as $key5=>$attr5)
                                                        {
                                                           if(isset($attr5->id_base) && $boat_fields['Homeport_ID']==$attr5->id_base) 
                                                           {
                                                                $boat_fields['Homeport']=$attr5->name;
                                                           }
                                                        }
                                                       
                                                    }
                                                }
                                            }

                                        }
                                    }                                    
                                } 
                                
                            }

                        }
                    }

                }
        $location['Homeport']=$boat_fields['Homeport'];
        $location['Country']=$boat_fields['Country'];
        
        return $location;
    }
    
    
    //get boat model
    public function get_boat_model($postid)
    {
        $model_name='';
        $boat_model = wp_get_object_terms($postid,  'bt_model' );
        if ( ! empty($boat_model) ) 
        {
	       if ( ! is_wp_error($boat_model) ) 
           {
                foreach($boat_model as $model) 
                {
				    $model_name=$model->name; 
			     }
	       }
        }
        
        return  $model_name;
    }
    
    //get boat model
    public function get_boatmodel($postid)
    {
        $model_name='';
        $boat_model = wp_get_object_terms($postid,  'boatmodel' );
        if ( ! empty($boat_model) ) 
        {
	       if ( ! is_wp_error($boat_model) ) 
           {
                foreach($boat_model as $model) 
                {
				    $model_name=$model->name; 
			     }
	       }
        }
        
        return  $model_name;
    }
    
    //get boat operator
    public function boat_operator($postid)
    {
        $boat_ope = wp_get_object_terms($postid,  'id_ope' );

        $ope=array();
        if ( ! is_wp_error($boat_ope) ) 
        {
            $ope['Operator']=$boat_ope[0]->name;
            $ope['OpeID']=get_option( "oper_id_".$boat_ope[0]->term_id);
        }
        
        return $ope;
    }
    
    //getting boat country and base names
    public function get_boat_base($postid,$location='')
    {
        $boat_base=array();
        $boat_country = wp_get_object_terms($postid,  'country' );
        $post_locations=array();
        foreach($boat_country as $loc_id)
        {
            $post_locations[]=$loc_id->term_id;
        }
        $country_name='';
        $exclude_countries=array();
        $homeport_name='';
        $location_name='';
        if($location!=='')
        {
            $original_homeport=get_term_by('name',$location,'country');
            if  ($original_homeport->parent==0)
            {
                $original_country=$original_homeport;
                $original_port=get_term_children($original_country->term_id,'country');
                if(!empty($original_port))
                {
                    foreach($original_port as $child)
                    {
                        if(in_array($child,$post_locations))
                        {
                            $original_location=get_term_by('id',$child,'country');
                            if($original_location->parent!==$original_country->term_id && $child!==$original_homeport->term_id)
                            {
                                $original_homeport=get_term_by('id',$child,'country');
                            }
                        }
                    }
                    
                }
            }
            else
            {
                $original_port=get_term_children($original_homeport->term_id,'country');
                if(!empty($original_port))
                {
                    foreach ( $original_port as $child ) 
                    {
                        if(in_array($child,$post_locations))
                        {
                            $original_homeport=get_term_by('id',$child,'country');
                            $original_location=get_term_by('id',$original_homeport->parent,'country');
                            $original_country=get_term_by('id',$original_location->parent,'country');
                         }
                    }
                }
                else
                {
                    $original_location=get_term_by('id',$original_homeport->parent,'country');
                    $original_country=get_term_by('id',$original_location->parent,'country');
                }
                
            }
            
            
            if(!empty($original_homeport))
            {
                $boat_base['Homeport']=$original_homeport->name;
            }
            if(!empty($original_location))
            {
                $boat_base['Location']=$original_location->name;
            }
            if(!empty($original_country))
            {
                $boat_base['Country']=$original_country->name;
            }
        }
        else
        {
            //for the list of all boat locations
            if ( ! empty($boat_country) ) 
            {
	       if ( ! is_wp_error($boat_country) ) 
           {
		      $country_term=0;
			foreach($boat_country as $country) 
            {
                if($country->parent==0)
                {
                    $country_name=$country->name; 
                    $country_term=$country->term_id;
                    break;
                }
			}
    
            $original_port=get_term_children($country_term,'country');

            if(!empty($original_port))
            {
                foreach ( $original_port as $child ) 
                {
                    $original_homeport=get_term_by('id',$child,'country');
                    if($original_homeport!==false)
                    {
                        if(in_array($child,$post_locations))
                        {
                        if($original_homeport->parent==$country_term)
                        {
                            $location_name=$original_homeport->name;
                        }
                        else
                        {
                            $homeport_name=$original_homeport->name;
                        }
                        }
                    }
                }
	       }
        }
        if(!empty($homeport_name))
        {
            $boat_base['Homeport']=$homeport_name;
        }
        if(!empty($country_name))
        {
            $boat_base['Country']=$country_name;
        }
        if(!empty($location_name))
        {
            $boat_base['Location']=$location_name;
        }
        }
        }
        
        return $boat_base;
    }
    
    
    
    //getting boat country and base names
    public function get_boat_dest($postid,$location='')
    {
        $boat_base=array();
        $boat_country = wp_get_object_terms($postid,  'destination' );
        $post_locations=array();
        foreach($boat_country as $loc_id)
        {
            $post_locations[]=$loc_id->term_id;
        }
        $country_name='';
        $exclude_countries=array();
        $homeport_name='';
        $location_name='';
        if($location!=='')
        {
            $original_homeport=get_term_by('name',$location,'destination');
            if  ($original_homeport->parent==0)
            {
                $original_country=$original_homeport;
                $original_port=get_term_children($original_country->term_id,'destination');
                if(!empty($original_port))
                {
                    foreach($original_port as $child)
                    {
                        if(in_array($child,$post_locations))
                        {
                            $original_location=get_term_by('id',$child,'destination');
                            if($original_location->parent!==$original_country->term_id && $child!==$original_homeport->term_id)
                            {
                                $original_homeport=get_term_by('id',$child,'destination');
                            }
                        }
                    }
                    
                }
            }
            else
            {
                $original_port=get_term_children($original_homeport->term_id,'destination');
                if(!empty($original_port))
                {
                    foreach ( $original_port as $child ) 
                    {
                        if(in_array($child,$post_locations))
                        {
                            $original_homeport=get_term_by('id',$child,'destination');
                            $original_location=get_term_by('id',$original_homeport->parent,'destination');
                            $original_country=get_term_by('id',$original_location->parent,'destination');
                         }
                    }
                }
                else
                {
                    $original_location=get_term_by('id',$original_homeport->parent,'destination');
                    $original_country=get_term_by('id',$original_location->parent,'destination');
                }
                
            }
            
            
            if(!empty($original_homeport))
            {
                $boat_base['Homeport']=$original_homeport->name;
            }
            if(!empty($original_location))
            {
                $boat_base['Location']=$original_location->name;
            }
            if(!empty($original_country))
            {
                $boat_base['Country']=$original_country->name;
            }
        }
        else
        {
            //for the list of all boat locations
            if ( ! empty($boat_country) ) 
            {
	       if ( ! is_wp_error($boat_country) ) 
           {
		      $country_term=0;
			foreach($boat_country as $country) 
            {
                if($country->parent==0)
                {
                    $country_name=$country->name; 
                    $country_term=$country->term_id;
                    break;
                }
			}
    
            $original_port=get_term_children($country_term,'destination');

            if(!empty($original_port))
            {
                foreach ( $original_port as $child ) 
                {
                    $original_homeport=get_term_by('id',$child,'destination');
                    if($original_homeport!==false)
                    {
                        if(in_array($child,$post_locations))
                        {
                        if($original_homeport->parent==$country_term)
                        {
                            $location_name=$original_homeport->name;
                        }
                        else
                        {
                            $homeport_name=$original_homeport->name;
                        }
                        }
                    }
                }
	       }
        }
        if(!empty($homeport_name))
        {
            $boat_base['Homeport']=$homeport_name;
        }
        if(!empty($country_name))
        {
            $boat_base['Country']=$country_name;
        }
        if(!empty($location_name))
        {
            $boat_base['Location']=$location_name;
        }
        }
        }
        
        return $boat_base;
    }
    
    //short code for dispay boat equipment
    public function boat_equipment($attr)
    {
        $postid = get_the_ID();
        $html='';
        $html .= '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="details" >Details</h3>'.
            '</div><div class="panel-body">';
        $plan = get_post_meta($postid,'plan_link',true);  
        $all_charact = get_post_meta($postid,'chars',true);
        if(!empty($plan) )
        {
            $html .='<p><strong>Yacht plan</strong></p>';
            $html .= '<img class="mts_plan" src="'.$plan.'" alt="boat plan" />';
            $html.=  '<br /><br />';
		}
        $html .='<p class="subheading">Charter Yacht Equipment</p>';
        if(!empty($all_charact) && $all_charact>0)
        {
            $col=0;
            $html .= '<table class="table table-hover table-bordered"><tr>';
            for ($i=0;$i<$all_charact;$i++)
            {
                if ($col<3)
                {
                    $html .= '<td><strong>'.get_post_meta($postid,'char_name_'.$i,true).'</strong>'
                                . '&nbsp;&nbsp; '.get_post_meta($postid,'char_val_'.$i,true).'</td>';  
                    $col++;
                }
                else
                {
                    $html .='</tr><tr>'; 
                    $col=0;
                }
            }
            for($i=$col;$i<3;$i++)
            {
                $html .='<td></td>';
            }
            $html .='</tr></table>';
        }
        else
        {
        
        
        
        $all_charact=get_post_meta($postid,'equipment',true);

         if(!empty($all_charact) && $all_charact>0)
         {
            $col=0;
            $html .= '<table class="table table-hover table-bordered"><tr>';
            for ($i=0;$i<$all_charact;$i++)
            {
                if ($col<3)
                {
                    $html .= '<td><strong>'.get_post_meta($postid,'EquipName_'.$i,true).'</strong>'
                                . '&nbsp;&nbsp; '.get_post_meta($postid,'EquipQuantity_'.$i,true).'</td>';  
                    $col++;
                }
                else
                {
                    $html .='</tr><tr>'; 
                    $col=0;
                }
            }
            for($i=$col;$i<3;$i++)
            {
                $html .='<td></td>';
            }
            $html .='</tr></table>';

         }else{
			  $html .= "<p style='margin-left:50px !important;'>Boat Equipment Not Available</p>";
		 }
		 
         }
            
        $html .='</div></div>';    
        return $html;
    }
    
    //short code for displaying boat prices
    public function boat_prices($attr)
    {
        $postid = get_the_ID();

        $html .= '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="prices">Prices</h3>'.
            '</div><div class="panel-body">';

       $html .='<table class="table table-hover" >'.
                '<thead><tr><td>Price per Week</td>'.
                '<td>From</td><td>To</td></tr>'.
                '</thead><tbody>';
      $prices=get_post_meta($postid,'price',true);
      
      
      $sedna=0;
      $builtyear=get_post_meta($postid,'buildyear',true);
       $boat_id=get_post_meta($postid,'id_boat',true);
      if (!empty($builtyear))
      {
        $sedna=1;
      }
      if($sedna==0)
      {
        $pboatid=get_post_meta($postid,'id_boat',true);
	   $curyear = date("Y") .'-01-02';	
	  $real_time_query = 'https://api.boatbooker.net/ws/boatinfo/getboatinfo?Username=18abb2dc5849491eaaa06ab3d4fb1dc2&Password=2ec90d50df594e419c3e52088f947556&lang=en&boatid='.$pboatid.'&loadAvailability=True';
	$boat_avail=json_decode(file_get_contents($real_time_query),true);
		foreach ($boat_avail['Availability'] as $bavail){
			 $tf = strtotime($bavail['DateFrom']);
			 $df = date("m/d/Y", $tf);
			 $df1 = date("d/m/Y", $tf);
			 $tt = strtotime($bavail['DateTo'] . "-1 days");
			 $dt = date("m/d/Y", $tt);
			 $dt1 = date("d/m/Y", $tt);
			 $y = date("Y", $tf);
			 $cury = date("Y");
			 $status = "";
			 
		    switch ($bavail['AvailabilityStatus']) {
				case 1:
					$status = 'Available';
					break;
				case 2:
					$status = 'Option';
					break;
				case 3:
					$status = 'Booked';
					break;
				case 4:
					$status = 'N/A';
					break;		
				case 5:
					$status = 'Maintenance';
					break;	
				case 6:
					$status = 'Owner';
					break;	
				default:
					$status = "";
			}
			 
			 if($y == $cury){	
				$arprice[] = array(
					'DateFrom' => $df,
					'DateTo' => $dt,
					'priceD' => $bavail['PriceWithoutDiscount'],
					'cur' => $bavail['Currency'],
					'status' => $status
				);
				 $html .='<tr>';
				  $html .='<td>';
				  if($bavail['PriceWithoutDiscount'] != 0){
				  $html .='&euro;'.$bavail['PriceWithoutDiscount'].'</td>'; 
				  }else{
					$html .='Boat Not Avaiable</td>';   
				  }
				 $html .='<td>'.$df1.'</td>';
				$html .='<td>'.$dt1.'</td>';
				$html .='<td>';
				if($bavail['PriceWithoutDiscount'] != 0){
					$html .=$status.'</td>';
				}else{
					$html .= 'N/A</td>';
				}
				$html.='</tr>';
				 
			 }
		}     
 //storing to session			
			session_start();
			$_SESSION['arrprice'] = $arprice;

  }
  else
      {
       
            $request_price='http://client.sednasystem.com/API/getBoat.asp?id_boat='.$boat_id.'&refagt=wxft6043';
            $result_sedna_price=json_decode(json_encode(simplexml_load_string(file_get_contents($request_price))),true);
            if(isset($result_sedna_price['homeport']['prices']))
            {
                $str_price=json_encode($result_sedna_price['homeport']['prices']);
                update_post_meta($postid,'price',$str_price);
            }
            $prices=get_post_meta($postid,'price',true);
            $prices=json_decode($prices,true);
      
           
      //dysplaying prices for sedna database
      if(is_array($prices) && isset($prices['price']))
      {
        $def_cur=get_post_meta($postid,'def_cur',true);
        foreach($prices['price'] as $key=>$price)
        {
            if(is_array($price))
            {
                foreach($price as $cost)
                {
                    $html .='<tr>';
                    $html .='<td>';
                    if ($def_cur!=='EUR')
                    {
                        $html .='$'.$cost['amount'].'</td>';
                    }
                    else
                    {
                        $html .='&euro;'.$cost['amount'].'</td>'; 
                    }
                    $date_from_tmp=new DateTime($cost['datestart']);
                    $date_from=date('d/m/Y',$date_from_tmp->getTimestamp());
                    $date_to_tmp=new DateTime($cost['dateend']);
                    $date_to=date('d/m/Y',$date_to_tmp->getTimestamp());
                    $html .='<td>'.$date_from.'</td>';
                    $html .='<td>'.$date_to.'</td>';
                    $html.='</tr>';
                }
            }
        }
      }
     }
     $html .='</tbody></table>';
    $html .='</div></div>';
    return $html;
}

    //get boat price for search results
    public function get_sedna_price($postid)
    {
        $boat_price=0;
        $prices=get_post_meta($postid,'price',true);
        $prices=json_decode($prices,true);
      
      if(is_array($prices) && isset($prices['price']) && !empty($prices))
      {

        foreach($prices['price'] as $key=>$price)
        {
            if(is_array($price))
            {
                if(isset($price['amount']))
                {
                    $boat_price=$price['amount'];
                    $date_from_tmp=new DateTime($price['datestart']);
                    $date_to_tmp=new DateTime($price['dateend']);
                    $date_current=new DateTime(date('d-m-Y'));
                    $actual_date=$date_current->getTimestamp();
                    if($actual_date>=$date_from_tmp->getTimestamp() && 
                        $actual_date<=$date_to_tmp->getTimestamp())
                    {
                                break;
                    }
                }
                else
                {
                    foreach($price as $cost)
                    {

                            $boat_price=$cost['amount'];
                            $date_from_tmp=new DateTime($cost['datestart']);
                            $date_to_tmp=new DateTime($cost['dateend']);
                            $date_current=new DateTime(date('d-m-Y'));
                            $actual_date=$date_current->getTimestamp();
                            if($actual_date>=$date_from_tmp->getTimestamp() && 
                                $actual_date<=$date_to_tmp->getTimestamp())
                            {
                                break;
                            }
                    }
                }
            }
        }
      }

      
      return $boat_price;
    }
    
    
      
     //boat map with location from Google API
    public function boat_googlemap($attr)
    {
        $html ='';
        $postid = get_the_ID();
        $homeport_id=get_post_meta($postid,'homeport_id',true);
        if(!empty($homeport_id))
        {
            $location=$this->get_sedna_dest($postid);
            $homeport=str_replace(' ','+',$location['Homeport']).','.str_replace(' ','+',$location['Country']);
            $html .='<a href="#" class="hide_link" id="map"></a><div class="panel panel-default">'.
                '<div class="panel-heading xxx">'.
                '<h3 class="panel-title" >Map</h3>'.
                '</div><div class="panel-body">';
            if (!empty($homeport))
            {
                $html .='<iframe style="width: 100%;height: 300px;"  src="https://www.google.com/maps/embed/v1/place?q='.
                    $homeport.'&key=AIzaSyDOZM0TA4Qhki2fM2MseW5Bbh24AqX-XQ4"></iframe>';        
            }
            $html .='</div></div>';
        }
        else
        {
             if(isset($_GET['dst']) && !empty($_GET['dst']))
            {
                $location=$this->get_boat_base($postid,urldecode($_GET['dst']));
            }
            else
            {
                $location=$this->get_boat_base($postid);
            }
        
            if(!empty($location))
            {
                $homeport=str_replace(' ','+',$location['Homeport']).','.
                        str_replace(' ','+',$location['Location']).','.
                        str_replace(' ','+',$location['Country']);
        
                $html .='<a href="#" class="hide_link" id="map"></a><div class="panel panel-default">'.
                '<div class="panel-heading">'.
                '<h3 class="panel-title" >Map</h3>'.
                '</div><div class="panel-body">';
                if (!empty($homeport))
                {
                $html .='<iframe style="width: 100%;height: 300px;"  src="https://www.google.com/maps/embed/v1/place?q='.
                    $homeport.'&key=AIzaSyDOZM0TA4Qhki2fM2MseW5Bbh24AqX-XQ4"></iframe>';        
                }
                $html .='</div></div>';
            }
            
        }
       
        return $html;
    } 
    
    
    public function boat_availabilityblock($attr)
    {
        $sedna=0;
             
        wp_register_style("mts-avail",WP_PLUGIN_URL."/mts-search-engine/css/availability.css", array(), false, 'all');
        wp_enqueue_style("mts-avail"); 
    
         wp_register_script("scroll_avail", WP_PLUGIN_URL."/mts-search-engine/js/available_scroll.js", array('jquery'), false, true);
        wp_enqueue_script("scroll_avail");  
        //$this->results_script();
        $html .='<aside id="available">';

        $postid = get_the_ID();
        $ope=$this->boat_operator($postid);
       
        $boat_id = get_post_meta($postid,'id_boat',true);
        $def_cur = get_post_meta($postid,'def_cur',true);
        $boat_bases=array();
        if(isset($_GET['dst']))
        {
            $boat_bases=$this->get_boat_base($postid,$_GET['dst']);
        }
        else
        {
            $boat_bases=$this->get_boat_base($postid);
        }
        $boat_name = get_post_meta($postid,'Name',true);
        $boat_year = get_post_meta($postid,'YearBuilt',true);
         if(empty($ope) || count($ope)==0 || empty($boat_year))
         {
            $sedna=1;
            $boat_fields = $this->get_boat_fields($postid);
            $boat_year=$boat_fields['YearBuilt'];
            $boat_bases['Homeport']=$boat_fields['Homeport'];
            $boat_bases['Country']=$boat_fields['Country'];
         }
        $boat_model=$this->get_boat_model($postid);
        if($boat_model==='')
        {
            $boat_model=$boat_fields['BoatModel'];
        }
        $boat_type=$this->get_boat_type($postid);
        if($boat_type=='')
        {
            $boat_type=$boat_fields['BoatType'];
        }

        
        
       
        
        if (!empty($this->html_link_search))
        {
            $html .="<div class='back_link'><a href='".$this->html_link_search.
                                            "'>Back to search result</a></div>";
        }
        
        $html .= '<div class="sidebar-wrapper">';
        $html .= '<ul class="nav nav-tabs nav-stacked affix-top" data-spy="affix" data-offset-top="170" '.
                    'data-offset-bottom="650">'.
                '<li id="mts_avail" class="in_progress" >'.
                '<div id="mts_avail_js">'.
                '<center style="margin-top: 20px;text-align: center;font-size: 13px;color: #e8b448;" >'.
                'Checking Availability. . .</center>'.
                '</div><!-- if available -->'.
                '<div class="boat_avail_ctrl ctrl_show" >'.
                '<span class="header_mts_avail">Boat Available</span>'.
                '<div class="trip_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Destination:</span><span class="descr_av_2" '.
                'id="mts_js_destination"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip Start on:</span><span class="descr_av_2" '.
                'id="mts_js_datestart"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip End on:</span><span class="descr_av_2" '.
                'id="mts_js_dateend"  ></span></span><span class="descr_type"><span class="descr_av_1" >Duration:</span>'.
                '<span class="descr_av_2" id="mts_js_duration"  ></span></span>'.
                '</div><div class="price_info" >'.
                '<span class="descr_type" id="if_price"><span class="descr_av_1" >Price:</span><span class="descr_av_2" '.
                'id="mts_js_price"  ></span></span>'.
                '<span class="descr_type" id="if_discount" ><span class="descr_av_1" >Discount:</span>'.
                '<span class="descr_av_2" id="mts_js_discount"  ></span></span>'.
                '<span class="descr_type" id="if_discount2" style="display: none;"><span class="descr_av_1" >Our discount:</span>'.
                '<span class="descr_av_2" id="mts_js_discount2"  ></span></span>'.
                '<span class="descr_type" id="if_discount_total" ><span class="descr_av_1" >Final Price:</span>'.
                '<span class="descr_av_2" id="mts_js_total"  ></span></span>'.
                '<span class="caution" id="caution"></span>
                <span class="detinfo" id="detinfo"></span></div>'.
                '<div class="book_btn" ><div id="addthebr" ></div>'.
                '<button type="button" class="button squarebrd"  id="mts_book_form" '.
                '>Reserve or Save for later</button>'.
                '</div><div class="change_trip_dates" >'.
                '<a href="javascript:void(0);" class="chgtd mts_check_availability squarebrd" >Change trip Dates?</a>'.
                '</div></div>'.
                '<div class="boat_avail_ctrl ctrl_hide" >'.
                '<div class="boat_avail_ctrl not_avails">'.
                '<span class="header_mts_avail " >Boat Not available</span>'.
				'<span class="header_mts_avail_booked " >Boat Booked</span>'.
                '<h3>Change Trip Dates and check Again ?</h3>'.
                '</div><div class="boat_avail_ctrl trip_dates" >'.
                '<span class="header_mts_avail" >Change trip Dates</span>'.
                '</div><div class="xhts_holder" >'.
                '<label for="mts_recheck_in" >Check In</label>';
        
        $html .='<input type="text" id="mts_recheck_in" value="';
        if (isset($_GET['date_from']) && !empty($_GET['date_from']))
        {
            $date_from_tmp=new DateTime($_GET['date_from']);
            $date_from=date('d/m/Y',$date_from_tmp->getTimestamp());
            $html .=$date_from;
        }
        else 
        {
           $html .=date("d/m/Y",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
        }
        $html .='"  /></div><div class="xhts_holder" >';
        $html .='<label for="mts_recheck_out" >Check Out</label>';
        $html .='<input type="text" id="mts_recheck_out" value="';
        if (isset($_GET['date_to']) && (!empty($_GET['date_to'])))
        {
            $date_to_tmp=new DateTime($_GET['date_to']);
            $date_to=date('d/m/Y',$date_to_tmp->getTimestamp());
            $html .=$date_to;
        }
        else
        {
            $html .=date("d/m/Y",mktime(0,0,0,date("m"),date("d")+8,date("Y"))); 
        }
        $html .='" /></div>'.
                '<a href="javascript:void(0);" class="button check_availb squarebrd">Check Availability</a>'.
                '</div><div id="mts_book_form hidden" >';
        $html .='<input type="hidden" name="id_boat" id="id_boat" value="'.$boat_id.'" />'.
                '<input type="hidden" name="homeport" id="homeport" value="'.$boat_bases['Homeport'].'" />'.
                '<input type="hidden" name="country" id="country" value="'.$boat_bases['Country'].'" />'.
                '<input type="hidden" name="boat_model" id="boat_model" value="'.$boat_model.'" />'.
                '<input type="hidden" name="boat_type" id="boat_type" value="'.$boat_type.'" />'.
                '<input type="hidden" name="boat_name" id="boat_name" value="'.$boat_name.'" />'.
                '<input type="hidden" name="boat_year" id="boat_year" value="'.$boat_year.'" />'.
                '<input type="hidden" name="operator" id="operator" value="'.$ope['Operator'].'" />'.
                '<input type="hidden" name="operator" id="ope_id" value="'.$ope['OpeID'].'" />';
        if($sedna==1)
        {
            $html .='<input type="hidden" name="database" id="database" value="sedna"/>';
        }
        else
        {
            $html .='<input type="hidden" name="database" id="database" value="booker"/>';
        }
                
              $html .=  '</div></li><li class="active"><a href="#overview" class="mts_navl" >Overview</a></li>'.
                '<li class="menu"><a href="#prices" class="mts_navl"  >Prices</a></li>';

            $html .='<li class="menu"><a href="#extra-costs" class="mts_navl" >Extra Costs</a></li>';

          $html .='<li class="menu"><a href="#details" class="mts_navl" >Details</a></li>'.
                '<li class="menu"><a href="#map" class="mts_navl" >Map</a></li>'.
                '<li class="menu"><a id="download_boat_details"  href="javascript:void(0)">Download Details</a></li>';
        $html .='</ul></div></aside>';
        $html .=$this->download_boat_form();
        //'<input type="hidden" name="rate_cur" id="rate_cur" value="'.$rate.'" />'.
        //'<input type="hidden" name="def_cur" id="def_cur" value="'.$def_cur.'" />'.*/
        $html .=$this->boat_booking_form();
        return $html;
    
    }
    
    //short code for boat booking form
    public function boat_booking_form()
    {
        $postid = get_the_ID();//passagers are counted by numbers of cabins
        //$passengers = get_post_meta($postid,'nbper',true);
        if (isset($_GET['date_from']))
        {
           $date_from=$_GET['date_from'];
        }
        else
        {
           $date_from=date('d.m.Y',mktime(0,0,0,date("m"),date("d")+1,date("Y"))); 
        }
        if (isset($_GET['date_to']))
        {
            $date_to=$_GET['date_to'];
        }
        else
        {
            $date_to=date("d.m.Y",mktime(0,0,0,date("m"),date("d")+7,date("Y")));
        }

        $html='<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" '.
                'aria-hidden="true"><div class="modal-dialog"><div class="modal-content">';
        $html .='<form class="form-horizontal" role="form" style="display: block;">';
        //modal header
        $html .='<div class="modal-header">';
        $html .='<button type="button" id="clicktrigclose" class="close close_modal" data-dismiss="modal">'.
                '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'.
                '<h4 class="modal-title" id="myModalLabel">Please complete this form and give us more informations 
                about you. We will contact you after you will submit the request.</h4>';
        $html .='</div>';
        
        $html .='<div class="modal-body">';
        $html .='<div id="processingwait" class="processingwait in_progress" '.
                'style="display: none;width: 100%;height: 100%;position: absolute;left: 0;top: 0;background-color: #fff!important;" >'.
                '<h3 style="text-align: center;" >Please wait processing..</h3></div>';
        
        //modal form fields
        $html .='<div class="wrapp_opac">';
        
        //modal first row
        $html .='<div class="row">';
        $html .='<div class="form-group col-lg-6">'.
                '<label for="inputEmail" class="col-md-4 control-label">Email</label>'.
                '<div class="col-md-8"><input type="email" class="form-control squarebrd inputEmail"  '.
                'autocomplete="off"  ></div></div>';
        $html .='<div class="form-group col-lg-6">'.
                '<label for="inputPhone" class="col-md-4 control-label">Phone number</label>'.
                '<div class="col-md-8"><input type="tel" class="form-control squarebrd inputPhone" '.
                'autocomplete="off" ></div></div>';
        $html .='</div>';
        //modal second row
        $html .='<div class="row" >';
        $html .='<div class="form-group col-lg-6">'.
                '<label for="inputFirstName" class="col-md-4 control-label">First Name</label>'.
                '<div class="col-md-8">'.
                '<input type="text" class="form-control squarebrd inputFirstName"  autocomplete="off" >'.
                '</div></div><div class="form-group col-lg-6">'.
                '<label for="inputLastName" class="col-md-4 control-label">Last Name</label>'.
                '<div class="col-md-8"><input type="text" class="form-control squarebrd inputLastName"  '.
                'autocomplete="off" ></div></div></div>';
        //modal form third row
         $html .='<div class="row" >'.
                '<div class="help-block">Please select departure and arrival '.
                'date.</div>';

          $html .='<div class="form-group col-sm-6">'.
                    '<label for="input_sel_dep" class="col-md-4 control-label ">Departure </label>'.
                    '<select class="col-md-8 form-control squarebrd input_sel_dep" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;" >';
           $html .='<option value='.$date_from.'>'.$date_from.'</option>';
           $html .='</select></div><div class="form-group col-sm-6">'.
                    '<label for="input_sel_arv" class="col-md-4 control-label ">Arrival </label>'.
                    '<select  class="col-md-8 form-control squarebrd input_sel_arv" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;"  >';
            $html .='<option value='.$date_to.'>'.$date_to.'</option>';
            $html .='</select></div>';
            $html .='</div>';

        
            $html .='<div class="row" >';
            $html .='<div class="form-group col-sm-6">'.
                    '<label for="inputhold48" class="col-md-10 control-label radio-inline">Hold boat for 48hrs</label>'.
                    '<input type="radio" name="book_opt" class="checka_optsa col-md-2 squarebrd form-control radioopt" '.
                    'id="inputhold48"  checked="checked" ></div>';
            $html .=' <div class="form-group col-sm-6">'.
                    '<label for="inputsavesearch" class="col-md-10 control-label radio-inline">Save this search </label>'.
                    '<input type="radio" '.
                    ' name="book_opt" class="checka_optsa col-md-2 form-control squarebrd radioopt" id="inputsavesearch"  >';
            $html .=' </div></div>';
                
            $postid = get_the_ID();
            //getting all boat fields with images for carousel slider
            $boat_fields=$this->get_boat_fields($postid);
            if(isset($boat_fields['BerthsMax']))
            {
                $html .='<div class="row" >'.'<div class="form-group col-sm-6">'.
                        '<label for="input_sel_pax" class="control-label">Please select the number of passengers </label>';
                $html .='<select id="input_sel_pax" class=" form-control squarebrd" '.
                        'style="min-height: 40px;min-width: 100px;max-width: 179px;margin-left: 67px;"  >';
                $passengers=$boat_fields['BerthsBasic'];
                for( $pa = 1; $pa <= $passengers; $pa++)
                {
                    $html .='<option value="'.$pa.'" >'.$pa;
                    if($pa == 1)
                        $html .='passenger';
                    else
                        $html .='passengers';
                        $html .='</option>';
                }
                $html .='</select>';
                $html .='</div></div>';
            }
        
        $html .='<div class="row" ><p style="display: none;text-align: center;" class="show_error error"></p>'.
                '<span class="help-block warring" style="text-align: center;">Please note that all the fields are required.</span>'.
                '</div>';
         
        $html .='</div>';
        $html .='<div id="send_letter"></div>' ;    
        //end of modal form fields
                
        $html .='<div class="modal-footer">'.
                '<button type="button" class="btn btn-primary squarebrd mts_reserve" >Save</button>'.
                '<button type="button" class="btn btn-default squarebrd close_modal" data-dismiss="modal">Close</button>'.
                '</div>';
                
        $html .='</div></form><div class="last_msg" style="display: none;"><div class="head">'.
            '</div><div class="content"></div>'.
            '<div class="footer"><button type="button" class="btn btn-default squarebrd close_modal" '. 
            'data-dismiss="modal">Close</button></div></div></div></div></div>';
            
        return $html;
    }
    
    //short code for display extra costs of boat
    public function boat_extraprices($attr)
     {  
        $postid = get_the_ID();
        $html='';
        $sort_extra=array();
        $exist_name=array();
        $all_mand_extra=get_post_meta($postid, 'mand_extra',true);
        $add_extra = get_post_meta($postid,'add_extra',true);
        $country_id=get_post_meta($postid,'homeport_id_country',true);
        $def_cur=get_post_meta($postid,'def_cur',true);
        $boat_id=get_post_meta($postid,'id_boat',true);
        
        
        
      $boat_year=get_post_meta($postid,'YearBuilt',true);
      if(!empty($boat_year))
      {
        //checking price from Sedna database
      $query_boats='https://api.boatbooker.net/ws/sync/v2/main?username='.
                    self::BOOKER_USER.'&password='.self::BOOKER_PASSWORD.        
                    '&loadFleetOperators=True&loadBoats=True&loadSpecificBoats='.$boat_id;
      $booker_boats = json_decode(file_get_contents($query_boats));
      //echo $query_boats.'<br />';
      $operator_id='';
      $operator_email='';
      $operator_site='';
      $operator_name='';
      $def_cur='';
      $boat_model=$this->get_boat_model($postid);
      $boat_type=$this->get_boat_type($postid);
      $boat_name=get_post_meta($postid,'Name',true);
      if (isset($booker_boats->Boats[0]) && count($booker_boats->Boats)==1)
      {
            $operator_id=$booker_boats->Boats[0]->FleetOperatorID;
            foreach($booker_boats->FleetOperators as $ope=>$desc)
            {   
                if($desc->ID==$operator_id)
                {
                    $operator_email=$desc->DefaultMail;
                    $operator_site=$desc->Website;
                    $operator_name=$desc->Name;
                    //echo $operator_email.' '.$operator_site.' '.$operator_name.' '.
                    //    $operator_id.'<br />';
                    break;
                }
            }
      }
      if(!empty($operator_id))
      {
            //find the boat in Sedna database with checking price
                $sedna_check =json_decode(json_encode(simplexml_load_string(file_get_contents('http://client.sednasystem.com/API/getOperators.asp?refagt=wxft6043'))));
                
                $count_ope=count($sedna_check->operator);
                $operator_sedna_id='';
                for($i=0;$i<$count_ope;$i++)
                {
                    foreach ($sedna_check->operator[$i] as $ope)
                    {
                        
                        if(!empty($operator_email) && ($ope->ope_email==$operator_email))
                        {
                            $operator_sedna_id=$ope->id_ope;
                            $operator_def_cur=$ope->DefCurr;
                            $def_cur=$operator_def_cur;
                            break 2;
                        }
                        elseif(!empty($operator_site) && (stripos($ope->ope_site,$operator_site)!==false ||
                                    stripos($operator_site,$ope->ope_site)!==false))
                            {
                                $operator_sedna_id=$ope->id_ope;
                                $operator_def_cur=$ope->DefCurr;
                                $def_cur=$operator_def_cur;
                                break 2;
                            }
                        elseif(!empty($operator_name) && (stripos($ope->ope_company,$operator_name)!==false ||
                        stripos($operator_name,$ope->ope_company!==false)))
                        {
                            
                            $operator_sedna_id=$ope->id_ope;
                            $operator_def_cur=$ope->DefCurr;
                            $def_cur=$operator_def_cur;
                            
                            break 2;
                        }
                    }
                }
                
                $id_sedna_boat='';
                if(!empty($operator_sedna_id))
                {
                    $countries=array();
                    $ope_boats='http://client.sednasystem.com/API/getBts3.asp?refagt=wxft6043&Id_ope='.$operator_sedna_id;
                     //echo $ope_boats.'<br />';
                    $result_ope_boats=json_decode(json_encode(simplexml_load_string(file_get_contents($ope_boats))));
                    foreach ($result_ope_boats as $boats_sedna)
                    {
                        foreach ($boats_sedna as $boat_sedna)
                        {
                            foreach ($boat_sedna as $attributes)
                            {
                                if($attributes->buildyear==$boat_year)
                                {
                                    if ($attributes->bt_type=='Monohull')
                                    {
                                        if(stripos($boat_model,$attributes->model)!==false)
                                        {
                                            if(trim($attributes->name)==trim($boat_name))
                                            {
                                                $id_sedna_boat=$attributes->id_boat;
                                                break 3;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(trim($attributes->bt_type)==trim($boat_type))
                                        {
                                            if(stripos($boat_model,$attributes->model)!==false)
                                            {
                                                if(trim($attributes->name)==trim($boat_name))
                                                {
                                                    $id_sedna_boat=$attributes->id_boat;
                                                    foreach ($boat_sedna->homeport as $attrhome)
                                                    {
                                                        foreach ($attrhome as $dethome)
                                                        {
                                                            if(isset($dethome->id_country) && 
                                                                    !(in_array($dethome->id_country,$countries)))
                                                            {
                                                                $countries[]=$dethome->id_country;
                                                            }

                                                        }
                                                            
                                                    }
                                                    break 3;
                                                }
                                            }
                                        }
                                    }
                                }
                           }
                        }
                    }
                }
                
                if ($id_sedna_boat>0)
                {
                    if (count($countries)==1)
                    {
                        $html .=$this->extra_cost($id_sedna_boat,$countries[0]);
                    }
                    
                }
                else
                {
                    
                }
                
      }
      }
        
        if(!empty($all_mand_extra) && $all_mand_extra!=0)
        {
            $html.='<div class="panel panel-default">'.
                            '<div class="panel-heading">'.
                            '<h3 class="panel-title" id="extra-costs" >Extra Costs</h3>'.
                            '</div><div class="panel-body">';
            $html .='<p><strong>MANDATORY EXTRAS</strong></p>';
            $html .='<table class="table table-hover" ><thead>'.
                        '<tr><td>Service</td><td>Quantity</td><td>Unit</td><td>Price</td></tr>'.
                        '</thead><tbody>';
            for ($i=0;$i<$all_mand_extra;$i++)
            {
                $price=get_post_meta($postid, 'mand_price_'.$i,true); 
                if ($price>0) 
                {
                  $country=get_post_meta($postid, 'mand_country_'.$i,true);
                  if ($country==0) 
                  {
                        $html .='<tr><td>'.get_post_meta($postid, 'mand_name_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'mand_quantity_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'mand_per_'.$i,true).'</td>';
                        $html .='<td>';
                        if($def_cur!=='EUR')
                        {
                          $html .='$';  
                        }
                        else
                        {
                            $html .='&euro;';                              
                        }
                        $html .=get_post_meta($postid, 'mand_price_'.$i,true).'</td>';
                        $html .='</tr>';
                  }
                  if (!empty($country_id) && $country_id==$country)
                  {
                        $html .='<tr><td>'.get_post_meta($postid, 'mand_name_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'mand_quantity_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'mand_per_'.$i,true).'</td>';
                        $html .='<td>';
                        if($def_cur!=='EUR')
                        {
                          $html .='$';  
                        }
                        else
                        {
                            $html .='&euro;';                              
                        }
                        $html .=get_post_meta($postid, 'mand_price_'.$i,true).'</td>';
                        $html .='</tr>';
                  }
                }
             }
             $html .='</tbody></table>';

           $html.='<p><strong>ADDITIONAL EXTRAS</strong></p>';
           $html .='<table class="table table-hover" ><thead>'.
                        '<tr><td>Service</td><td>Quantity</td><td>Unit</td><td>Price</td></tr>'.
                        '</thead><tbody>';

            for ($i=0;$i<$add_extra;$i++)
           { 
                $price=get_post_meta($postid, 'add_price_'.$i,true); 
                if ($price>0) 
                {
                  $country=get_post_meta($postid, 'add_country_'.$i,true);
                  if ($country==0) 
                  {
                    //must be converting to EUR
                        $html .='<tr><td>'.get_post_meta($postid, 'add_name_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'add_quantity_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'add_per_'.$i,true).'</td>';
                        $html .='<td>';
                        if($def_cur!=='EUR')
                        {
                          $html .='$';  
                        }
                        else
                        {
                            $html .='&euro;';                              
                        }
                        $html .=get_post_meta($postid, 'add_price_'.$i,true).'</td>';
                        $html .='</tr>';
                  }
                  else
                  {
                    //if country must be as homeport
                    if (!empty($country_id) && $country_id==$country)
                    {
                        $html .='<tr><td>'.get_post_meta($postid, 'add_name_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'add_quantity_'.$i,true).'</td>';
                        $html .='<td>'.get_post_meta($postid, 'add_per_'.$i,true).'</td>';
                        $html .='<td>';
                        if($def_cur!=='EUR')
                        {
                          $html .='$';  
                        }
                        else
                        {
                            $html .='&euro;';                              
                        }
                        $html .=get_post_meta($postid, 'add_price_'.$i,true).'</td>';
                        $html .='</tr>';
                    }
                    //if boat has more than one homeport
                    //must be converting to EUR
                  } 
                }
           }           
                        
            $html .='</tbody></table>';
            $html.='</div></div>';
                        
        }

        return $html;
    } 
     
     
    /***************function for inseting all necessary scripts******************/
    public function get_list_locations()
    {
        global $wpdb;
        $result=array();
        $locations = get_terms( 'country', 'orderby=name' );
        $simular_names=array();
        if ( ! empty($locations) && ! is_wp_error($locations) )
        {
            $i=0;
            foreach ($locations as $location) 
            {
                if(!in_array($location->name,$simular_names))
                {
                    $simular_names[]=$location->name;
                    $result[$i]['label']=$location->name;
                    $result[$i]['value']=$location->name;
                    $result[$i]['id']=$location->term_id;
                    $i++;
                }
            }
            header('Content-type: application/json');
            $data=json_encode($result);
            die($data); 
        }
        //$tax_locations = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE taxonomy='country' AND ".
        //                                        "parent=0", OBJECT );
    }
    
    
     public function get_list_destinations($type)
    {
        global $wpdb;
        $result=array();
         $simular_names=array();
        if ($type=='destination')
        {
            $taxonomy_countries=$wpdb->get_results( "SELECT wp_terms.name  FROM wp_term_taxonomy 
                                                 LEFT JOIN wp_terms  ON (wp_term_taxonomy.term_id=wp_terms.term_id) 
                                                 WHERE wp_term_taxonomy.taxonomy='destination'", OBJECT);
        }
        else
        {
            $taxonomy_countries=$wpdb->get_results( "SELECT wp_terms.name  FROM wp_term_taxonomy 
                                                 LEFT JOIN wp_terms  ON (wp_term_taxonomy.term_id=wp_terms.term_id) 
                                                 WHERE wp_term_taxonomy.taxonomy='country'", OBJECT);
             $i=0;
            foreach ($taxonomy_countries as $location) 
            {
                if(!in_array($location->name,$simular_names))
                {
                    $simular_names[]=$location->name;
                    $result[$i]['label']=$location->name;
                    $result[$i]['value']=$location->name;
                    $result[$i]['id']=$location->term_id;
                    $i++;
                }
            }
        }
                                                 
        return ($result);
                                                 
    }
    
    
     //function for aching the destinaion of all available boats
    public function update_list_taxonomy($cache_url,$type)
    {
        
        //checking if the file of ceched information is exists
         if (strlen($cache_url) > 0) 
         { 
            if(file_exists($cache_url))
            {
            //checking if the file is needed to update al information
            if ($this->checkForRenewal($cache_url)) 
            {
                //save new destinations               
                $new_dest=$this->get_list_destinations($type);  
                //save new destinations in xml file
                $this->stripAndSaveFile($cache_url,$new_dest);
            }
            }
            else
            {
                $new_dest=$this->get_list_destinations($type);  
                //save new destinations in xml file
                $this->stripAndSaveFile($cache_url,$new_dest);
            }
         }
         else
         {
            //the error with nonexiting xml file
            return false;
         }
        
    }
    
    
    //checking for need to update cache with all destinations
    public function checkForRenewal($file) 
    {
        //set the caching time (in seconds)
        $cachetime = (60*60*24*7); //one week
        //$cachetime = (60); //one week

        //get the file time
        $filetimemod = filemtime($file) + $cachetime;
        

        //if the renewal date is smaller than now, return true; else false (no need for update)
        if ($filetimemod < time()) 
        {
            return true;
        } 
        else 
        {
            return false;
        }
    }
    
    
        
    
    
    
    //function for writing all destinations in cache file
    public function stripAndSaveFile($cache,$all_data) 
    {
        $updated_file=file_put_contents($cache, json_encode($all_data));
        if ($updated_file==false)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
       
    
     
    //function for displaying only compact search form in front page or in footer
    function shortcode_front($attr)
    {
		   $a = shortcode_atts( array(
                'second_page' => '',), $attr );

        $this->mts_styles_front();
        $html_result="";
        $html_result .="<div class='transparent_form'>";
        //$html_result .="<div class='front_border'><div class='center_border'></div></div>";
        $html_result .="<form id='front' action='".site_url()."/".$attr['second_page']."/' method='get'>";
        //$this->scripts_form();
        $html_result .=$this->fields_form();
        
        $html_result .="</form>";
        //$html_result .="<div class='front_border'><div class='center_border'></div></div>";
        $html_result .="</div>";

        
        return $html_result;
    }
    
    
     //function for displaying searh form
    public function shortcode_boat_model($attr)
    {
        $html ='';
        global $wpdb;
        $lang='en';
        $postid=get_the_ID();
     //   $post_language_information = wpml_get_language_information($postid);
	 
	   include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        
        if(is_plugin_active('sitepress-multilingual-cms/sitepress.php') ) {
             $post_language_information = wpml_get_language_information($postid);
        }
        else {
             $post_language_information = array();
        }

        $typeid_model=get_post_meta($postid,'BoatTypeID',true);
        $type_term='';
        $title_type='';
        $title_type_ru='';
        if(!empty($typeid_model))
        {
            $types = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE taxonomy='boattype'", OBJECT);
            for($j=0;$j<count($types);$j++)
            {
                $typeid=get_option( "tax_typeid_".$types[$j]->term_id);
                if($typeid==$typeid_model)
                {
                    //checking the language of model
                    $type_term = $wpdb->get_results("SELECT * FROM wp_terms WHERE term_id=".
                                                                        $types[$j]->term_id, OBJECT);
                    $title_type=$type_term[0]->name;
                    if(isset($post_language_information['locale']) && $post_language_information['locale']==="ru_RU")
                    { 
                        $ru_type_id=icl_object_id($types[$j]->term_id,'boattype', false, 'ru' );
                        if($ru_type_id>0)
                        {
                            $type_ru=get_term_by("id",$ru_type_id,'boattype');
                            $title_type_ru=$type_ru->name;
                            $lang='ru';
                        }
                        
                    }

                }
            } 
        } 
        $title_model=get_the_title($postid);

        if(!empty($title_type_ru))
        {
            
            $title_model =$title_type_ru.' '.iconv('windows-1251','utf-8','модели').' <span class="model">'.$title_model.'</span>';
        }
        else
        {
            if(!empty($type_term))
            {
                $title_model =$type_term[0]->name.' <span class="model">'.$title_model.'</span>';
            }
        }
        //show attachment image if exists in the left top corner
        //responsive attachment for mobile devices
        $html .= '<div id="images_model">';
        $html .= '<h1 id="model_title">'.$title_model.'</h1>';
                               
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_status' => null,
            'post_parent' => $postid);

        $attachments = get_posts( $args );
        if ( $attachments ) 
        {
            foreach ( $attachments as $attachment ) 
            {
                $html .='<div id="respons_image">';
                $html .=wp_get_attachment_image( $attachment->ID, 'full' );
                $html .='</div>';
                //title of type of image
            }
        }
		else{
			$attachment_id = 61035;
			$html .='<div id="respons_image">';
            $html .=wp_get_attachment_image( $attachment_id, 'full' );
            $html .='</div>';
			
		}
        $html .='</div>';
                            
                   
        //show characteristics with icons in the left with responsive view
        $fields_icons=array('BerthsBasic','BerthsMax','BerthsStr',
                            'CabinsBasic','CabinsMax','CabinsStr',
                            'Engine','Length','Draft');
        $fields_icons_value=array();
        foreach($fields_icons as $icon_value)
        {
            $fields_icons_value[$icon_value]=get_post_meta($postid,$icon_value,true);
        }
        
        $html .='<div id="model_chars">';
        $html .='<div id="general_chars">';
         if($lang==='ru')
         {
            $html .='<h2 id="data_title">'.iconv('windows-1251','utf-8','Общие детали модели').'</h2>';
         }
         else
         {
            $html .='<h2 id="data_title">General characteristics</h2>';
         }
        
                            
        if(isset($fields_icons_value['Length']) && 
            !empty($fields_icons_value['Length']))
        {
            $html .='<div class="short first icon_length">'.
                                '<img src="/wp-content/plugins/mts-search-engine/css/img/size_icon.png" '.
                                ' alt="Length"/>';
             if($lang==='ru')
             {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','Длина').'</span><span>';
             }
             else
             {
                $html .='<span class="name_char">Length</span><span>';
             }
                                
            $html .= number_format($fields_icons_value['Length'],0).' ';
            if($lang==='ru')
            {
                $html .=iconv('windows-1251','utf-8','м').'</span></div>';
            }
            else
            {
                $html .='m</span></div>';
            }
            
        }
        
        if(isset($fields_icons_value['CabinsStr']) && 
        !empty($fields_icons_value['CabinsStr']))
        {
            $html .='<div class="short icon_cabin">'.
                                '<img src="/wp-content/plugins/mts-search-engine/css/img/cabins_icon.png" '.
                                ' alt="Total Cabins"/>';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','Кабин').'</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Cabins</span><span>';
            }
            $html .= $fields_icons_value['CabinsStr'].'</span></div>';
        }
        
        if(isset($fields_icons_value['BerthsStr']) && 
        !empty($fields_icons_value['BerthsStr']))
        {
            $html .='<div class="short icon_berth">'.
                '<img src="/wp-content/plugins/mts-search-engine/css/img/berth_icon.png" '.
                ' alt="Berths"/>';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','Мест для сна').'</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Berths</span><span>';
            }
            $html .=$fields_icons_value['BerthsStr'].'</span></div>';
        }
        
        if(isset($fields_icons_value['Draft']) && 
            !empty($fields_icons_value['Draft']))
        {
            $html .='<div class="short icon_draft">'.
                    '<img src="/wp-content/plugins/mts-search-engine/css/img/draught_icon.png" '.
                    ' alt="Draft"/>';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','осадка').'</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Draft</span><span>';
            }
            $html .=number_format($fields_icons_value['Draft'],0).' ';
            if($lang==='ru')
            {
                $html .=iconv('windows-1251','utf-8','м').'</span></div>';
            }
            else
            {
                $html .='m</span></div>';
            }
        }
        if(isset($fields_icons_value['Engine']) && 
                                !empty($fields_icons_value['Engine']))
        {
            $html .='<div class="short last icon_engine">'.
                    '<img src="/wp-content/plugins/mts-search-engine/css/img/engine_icon.png" '.
                    ' alt="Total Cabins"/>';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','Двигатель').' </span><span>';
            }
            else
            {
                $html .='<span class="name_char">Engine</span><span>';
            }
                              
            $html .=$fields_icons_value['Engine'].' </span></div>';
        }
                            
        $html .='</div><hr />';
                            
                            
        $array_spec=array('FuelCapacity','WaterCapacity','WaterlineLength',
                                                'Weight','HullLength','ShowersStr','ToiletsStr');
        $fields_value=array();
        foreach($array_spec as $value)
        {
            $fields_value[$value]=get_post_meta($postid,$value,true);
        }
                            
        $html .="<div id='detailed'>";
        if($lang==='ru')
        {
            $html .='<h2>'.iconv('windows-1251','utf-8','Детальные характеристики').'</h2>';
        }
        else
        {
            $html .="<h2>Detailed Characteristics</h2>";
        }
                             
                            
        if(isset($fields_value['ShowersStr']) && 
            !empty($fields_value['ShowersStr']))
        {
            $html .='<div class="specific first">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','душевые кабины').
                        '</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Showers</span><span>';
            }
            $html .=$fields_value['ShowersStr'].'</span></div>';
        }
                            
        if(isset($fields_value['ToiletsStr']) && 
                !empty($fields_value['ToiletsStr']))
        {
            $html .='<div class="specific">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.iconv('windows-1251','utf-8','Туалет').
                        '</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Toilets</span><span>';
            }
            $html .= $fields_value['ToiletsStr'].'</span></div>';
        }
                            
        if(isset($fields_value['WaterCapacity']) && 
            !empty($fields_value['WaterCapacity']))
        {
            $html .='<div class="specific">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.
                        iconv('windows-1251','utf-8','Вместимость бака для пресной воды').
                        '</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Water Capacity</span><span>';
            }
            $html .=$fields_value['WaterCapacity'].' ';
            if($lang==='ru')
            {
                $html .= iconv('windows-1251','utf-8','л').
                                            '</span></div>';
            }
            else
            {
                $html .= ' l</span></div>';
            }
        }
                            
        if(isset($fields_value['HullLength']) && 
            !empty($fields_value['HullLength']))
        {
            $html .='<div class="specific">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.
                        iconv('windows-1251','utf-8','длина корпуса').
                        '</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Hull Length</span><span>';
            }
            $html .=$fields_value['HullLength'].' ';
            if($lang==='ru')
            {
                $html .= iconv('windows-1251','utf-8','м').
                                            '</span></div>';
            }
            else
            {
                $html .= ' m</span></div>';
            }
        }
                            
        if(isset($fields_value['WaterlineLength']) && 
                                !empty($fields_value['WaterlineLength']))
        {
            $html .='<div class="specific">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.
                    iconv('windows-1251','utf-8','ширина по ватерлинии').'</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Waterline Length</span><span>';
            }
            $html .=$fields_value['WaterlineLength'].' ';
            if($lang==='ru')
            {
                $html .= iconv('windows-1251','utf-8','м').'</span></div>';
            }
            else
            {
                $html .= ' m</span></div>';
            }
        }
                            
        if(isset($fields_value['FuelCapacity']) && 
                                !empty($fields_value['FuelCapacity']))
        {
            $html .='<div class="specific">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.
                        iconv('windows-1251','utf-8','Вместимость топливного бака').
                        '</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Fuel Capacity</span><span>';
            }
            $html .=$fields_value['FuelCapacity'].' ';
            if($lang=='ru')
            {
                $html .= iconv('windows-1251','utf-8','л').'</span></div>';
            }
            else
            {
                $html .= ' l</span></div>';
            }
        }
                            
        if(isset($fields_value['Weight']) && 
            !empty($fields_value['Weight']))
        {
            $html .='<div class="specific">';
            if($lang==='ru')
            {
                $html .='<span class="name_char">'.
                iconv('windows-1251','utf-8','Общий вес').
                '</span><span>';
            }
            else
            {
                $html .='<span class="name_char">Weight</span><span>';
            }
            $html .=$fields_value['Weight'].' ';
            if($lang==='ru')
            {
                $html .= iconv('windows-1251','utf-8','кг').'</span></div>';
            }
            else
            {
                $html .= 'kg</span></div>';
            }
        }
        $html .='</div><hr />';
        $html .='</div>';
        //description of availability
                            
                            
        //make the request for all boat models
        //search with boat type and model
        $name_model=get_the_title($postid);
        $model_term=get_term_by('name',$name_model,'bt_model');
        $model_slug='';
        $type_slug='';
        $search_title='';
        if(!empty($model_term))
        {
            $model_slug=$model_term->slug;
        }
                   
        if(!empty($title_type))
        {
            $type_term=get_term_by('name',$title_type,'bt_type');
            if(!empty($type_term))
            {
                $type_slug=$type_term->slug;
            }
        }
        $array_locations=array();
        $search_locations=array();                  
        //choose the destinations with more count of boats
        if (!empty($type_slug) && !empty($model_slug))
        {
            $array_search=array('post_type'=>'boat_post',  
                                'posts_per_page'=>-1,
                                'orderby'    => 'meta_value',
                                    'order'      => 'DESC',
                                    'meta_key'  => 'brand',
                                'tax_query' => array(
                                'relation'=>'AND',
                                    array(
                                    'taxonomy' => 'bt_model',
                                    'field' => 'slug',
                                    'terms' => $model_slug)));
            $boats=get_posts($array_search);
            if (count($boats)>0)
            {
                $search_title=$title_type;
          
                foreach($boats as $post)
                {
                    $boat_dest = wp_get_object_terms($post->ID,  'country' );
                    if ( ! is_wp_error($boat_dest) ) 
                    {
                        foreach($boat_dest as $dest)
                        {
                            if($dest->parent==0)
                            {
                                if(!in_array($dest->name,$array_locations))
                                {
                                    $array_locations[]=$dest->name;
                                    $search_locations[]=$dest->slug;
                                }   
                            }
                                                
                        }
                    }
                }
            }
        }
                            
        $html .='<div class="clear_margin"></div>';
        $html .='<div class="to_search">';
        if (count($boats)>0)
            {
            $html .='<h5>Check our offers for yacht charter in popular destinations</h5>';
            $html .='<p>Yacht charter '. $search_title.' is available in: </p>';
        }
                            
        $search_count=array();
		$boats = array();
        foreach($search_locations as $key=>$location_slug)
        {

			if(!empty($type_slug))
            {
                $array_search1=array('post_type'=>'boat_post',
                                    'posts_per_page'=>-1, 
                                    'orderby'    => 'meta_value',
                                                'order'      => 'DESC',
                                                'meta_key'  => 'brand', 
                                    'tax_query' => array(
                                    'relation' => 'AND',
									  array('taxonomy' => 'bt_model',
                                    'field' => 'slug',
                                    'terms' => $model_slug),
                                        array('taxonomy' => 'country',
                                                'field' => 'slug',
                                                'terms' => $location_slug)
												)
									);
                $boats[$location_slug][]=get_posts($array_search1);
            }
			
        }
                           
$j = 0;

foreach ($boats as $key => $ncountry){

	foreach ($ncountry as $nc){
	
		$html .= '<br /><br /><div class="mk-fancy-table mk-shortcode table-style1">';
			$html .=	'<table width="100%">';
			$html .= '<thead><tr>';
			$html .= '<th style="text-align: justify";>'.$array_locations[$j].'('.count($nc).') </th>';
			$html .= '<th></th><th></th><th></th>';
			$html .='<tbody><tr>';
				   $i = 0;
				   
				   foreach ($nc as $value) {

			
					   		    $kinds = explode('/',$value->guid);
					
					$results = "select post_id from $wpdb->postmeta where meta_value =" .$kinds[4]." and meta_key='id_boat'";
					$postids = $wpdb->get_var($wpdb->prepare($results));
					
					 $boat_fields=$this->get_boat_fields($postids);

					if(!empty($boat_fields['Country']))
					{
						$country_name=', '.$boat_fields['Country'];   
					}
					if(!empty($boat_fields['Homeport']))
					{
						$homeport_name=' in '.$boat_fields['Homeport'];   
					}
				
					   if (strtolower($kinds[3]) == 'booker_boats'){
							$ref = site_url().'/boats/'.$value->post_name;
					   }else{
						   $ref = site_url().'/boat/'.$value->post_name;
					   }
					  if ($i % 4 === 0) {
							$html .= '</tr><tr>';
						}
					  $html .= "<td><a href='".$ref.  " ' target='_blank'>" .$boat_fields['BoatModel'].' '.$boat_fields['BoatType'].$homeport_name.$country_name. "</a></td>";
					  $i++;
				   }
				   
				$html .= '</tbody></tr></table>';
				$html .="</div>";
		
	}
	
	$j++;
	
}
        $html .='<div class="clear_margin"></div>';
        $html .='</div>';
        
        return $html;
    }
    
     
     
     //function for displaying searh form
    public function shortcode_mts($attr)
    {
        //return "work";
        return $this->all_engine();
    }
    
     

    //header for the result page for input new data for the next search
     public  function header_search($attr)
     {
        //$this->form_scripts();
        wp_register_style("top_search_form",WP_PLUGIN_URL."/mts-search-engine/css/top_search_form.css", array(), false, 'all');
        wp_enqueue_style("top_search_form");  
        $html="<div id='search_header'>".
                "<a  href='http://sailchecker.com' title='SailChecker' class='logo_main'>".
                "<img alt='SailChecker' nopin='nopin' data-pin-no-hover='true' src='http://sailchecker.com/wp-content/uploads/2014/02/Perspective-sailchecker-jupiter-logo.png'>".
                "</a>".
            "<input type='hidden' name='from' id='mts_from' value='' class='mts_from' />".
            "<div class='dest_block'>".
            "<input type='text' class='header_dest selectdest' id='mts_dst' name='dst' autocomplete='off' value='Where are you looking to charter?' />".
            "</div>".
            "<div class='date_block'>".
            "<input type='text' placeholder='Check In'  id='mts_date_from' name='date_from' ".
            " class='mts_datepick header_date mts_date_from' value=' ' />".
            "</div>".
            "<div class='date_block'>".
            "<input type='text' placeholder='Check Out'  id='mts_date_to' name='date_from' ".
            " class='mts_datepick header_date mts_date_to' value=' ' />".
            "</div>".
            "<div class='boat_type_block'><select name='boat_type' class='select_boat'  id='mts_boat_type' >".
            $this->boat_type_options()."</select></div>".
            "<div class='button_block'>".
            "<input type='button' class='header_start'  id='start_search' value='Search' />".
            "</div>".
            "<div class='clearfix' ></div>".
            //'<p class="mts_search_error error" style="display: none;" >Please select a destination</p>'.
            //'<p class="mts_search_date_error error" style="display: none;" >Please select return date. If both date fields are empty we will search for all boats available in the next 7 days</p>';
            "</div>";
        return $html;
     
     }
     
     
     //header for the result page for input new data for the next search
     public function top_search_bar($attr)
     {
        //$this->form_scripts();
            wp_register_script("mts-scrolltofixed", WP_PLUGIN_URL."/mts-search-engine/js/jquery-scrolltofixed.js", array(), false, true);
        wp_enqueue_script("mts-scrolltofixed");
        wp_register_style("top_search_form",WP_PLUGIN_URL."/mts-search-engine/css/top_search_form.css", array(), false, 'all');
        wp_enqueue_style("top_search_form");
        wp_register_script("scroll_bar", WP_PLUGIN_URL."/mts-search-engine/js/top_search_bar.js", array('mts-scrolltofixed'), false, true);
        wp_enqueue_script("scroll_bar"); 
        $html='<div id="page_search">';  
        $html .="<div id='search_fields'>".
            "<form method='GET' action='".get_permalink()."'>".
            "<a class='logo_main' href='http://sailchecker.com'>".
            "<img src='http://sailchecker.com/wp-content/uploads/2014/12/Square-sailchecker-banner-logo-search-engine.png' />".
            "</a>".
            "<div class='dest_block'>".
            "<input type='text' class='header_dest mts_location selectdest' id='mts_location' name='dst' autocomplete='off' value='Where are you looking to charter?' />".
            "</div>".
            "<div class='date_block'>".
            "<input type='text' placeholder='Check In'  id='mts_date_from' name='date_from' ".
            " class='mts_datepick header_date mts_date_from' value=' ' />".
            "</div>".
            "<div class='date_block'>".
            "<input type='text' placeholder='Check Out'  id='mts_date_to' name='date_from' ".
            " class='mts_datepick header_date mts_date_to' value=' ' />".
            "</div>".
            "<div class='boat_type_block'><select name='bt_type' class='selectboat select_boat'  id='mts_boat_type' >".
            $this->boat_type_options()."</select></div>".
            "<div class='button_block'>".
            "<input type='button' class='header_start search_go'  id='boat_search' value='Search' />".
            "</div>".
            "<div class='clearfix' ></div>".
            //'<p class="mts_search_error error" style="display: none;" >Please select a destination</p>'.
            //'<p class="mts_search_date_error error" style="display: none;" >Please select return date. If both date fields are empty we will search for all boats available in the next 7 days</p>';
            "</div></form>";
        $html .='</div>';
        return $html;
     
     }
     
     
     
     //header for the result page for input new data for the next search
     public  function top_search()
     { 
        $html="<div id='search_header'>".
                "<a  href='http://sailchecker.com' title='SailChecker' class='logo_main'>".
                "<img alt='SailChecker' nopin='nopin' data-pin-no-hover='true' ".
                " src='http://sailchecker.com/wp-content/uploads/2014/02/Perspective-sailchecker-jupiter-logo.png'>".
                "</a>".
            "<input type='hidden' name='from' id='mts_from' value='' class='mts_from' />".
            "<div class='dest_block'>".
            "<input type='text' class='header_dest selectdest' id='mts_dst' name='dst' autocomplete='off' value='Where are you looking to charter?' />".
            "</div>".
            "<div class='date_block'>".
            "<input type='text' placeholder='Check In'  id='mts_date_from' name='date_from' ".
            " class='mts_datepick header_date mts_date_from' value=' ' />".
            "</div>".
            "<div class='date_block'>".
            "<input type='text' placeholder='Check Out'  id='mts_date_to' name='date_from' ".
            " class='mts_datepick header_date mts_date_to' value=' ' />".
            "</div>".
            "<div class='boat_type_block'><select name='boat_type' class='select_boat'  id='mts_boat_type' >".
            $this->boat_type_options()."</select></div>".
            "<div class='button_block'>".
            "<input type='button' class='header_start'  id='start_search' value='Search' />".
            "</div>".
            "<div class='clearfix' ></div>".
            //'<p class="mts_search_error error" style="display: none;" >Please select a destination</p>'.
            //'<p class="mts_search_date_error error" style="display: none;" >Please select return date. If both date fields are empty we will search for all boats available in the next 7 days</p>';
            "</div>";
        return $html;
     
     }
     
     
     //html code for html output of boat types
     public function boat_type_options()
     {
        
         $options=array(
                        0=>'<option value="" >All Boats</option>',
                        1=>'<option value="catamaran" >Catamaran</option>',
                        2=>'<option value="gulet">Gulet</option>',
                        3=>'<option value="motorboat" >Motorboat</option>',
                        4=>'<option value="sailboat" >Sailboat</option>',
                        5=>'<option value="riverboat" >Riverboat</option>',
                        6=>'<option value="trimaran" >Trimaran</option>',
                    );

        
        $form_options="";
        foreach ($options as $key=>$value)
        {
           $form_options .=$value; 
        }
        
        
        return $form_options;
     }
     
     
     //function for display content with search results
    public function all_engine()
    {
        $html ='';
         
        $this->mts_styles_result();
        //displaying the header of table with result
        $html .=$this->top_search_bar();
        
        
        if(isset($_GET['action']) && $_GET['action']=='search')
        {
           
            $html .='<div id="charter">';
            $html .='<div id="allresults class="result-page">';
            $html .='<div class="list-boats">';
            if (!empty($_GET['dst']))
            {
                $location=get_term_by('name',urldecode($_GET['dst']),'country');
   
                if($location->parent==0)
                {
                    $country=$location->name;
                }
                else
                {
                    $location2=get_term_by('id',$location->term_id,'country');
                    if($location2->parent==0)
                    {
                        $country=$location2->name;
                    }
                }
                if(!isset($_GET['date_from']) || urldecode($_GET['date_from'])==' ' || empty($_GET['date_from']))
                {
                    $date_from=date('d/m/Y',mktime(0,0,0,date("m"),date("d")+1,date('Y')));
                    $date_from_boat= date('d.m.Y',mktime(0,0,0,date("m"),date("d")+1,date('Y')));
                }
                else
                {
                    $date_from_tmp=new DateTime($_GET['date_from']);
                    $date_from=date('d/m/Y',$date_from_tmp->getTimestamp());
                    $date_from_boat= date('d.m.Y',$date_from_tmp->getTimestamp());
                }
                if(!isset($_GET['date_to']) || urldecode($_GET['date_to'])==' ' || empty($_GET['date_to']))
                {
                    $date_to=date('d/m/Y',mktime(0,0,0,date("m"),date("d")+8,date('Y'))); 
                    $date_to_boat= date('d.m.Y',mktime(0,0,0,date("m"),date("d")+8,date('Y')));
                }
                else
                {
                    $date_to_tmp=new DateTime($_GET['date_to']);
                    $date_to=date('d/m/Y',$date_to_tmp->getTimestamp());
                    $date_to_boat= date('d.m.Y',$date_to_tmp->getTimestamp());
                }
                //print_r($location);
                //sedna destinations and search
                $sedna_dest=json_decode(json_encode(simplexml_load_string(
                        file_get_contents('http://client.sednasystem.com/API/GetDestinations2.asp?refagt=wxft6043'))));
                $port_ids=array();
                $country_ids=array();
                $dest_ids=array();
              
                
                
                foreach($sedna_dest as $dest)
                {
                    foreach($dest as $attr)
                    {
                        //if search place in destination
                        if(isset($attr->name) && stripos($location->name,$attr->name)!==false)
                        {
                            $dest_ids[]=$id_dest;
                            //return is of countries after finding id of dest
                        }
                        foreach($attr as $key2=>$attr2)
                        {
                                
                            if(isset($attr2->id_dest) && stripos($attr2->name,$location->name)!==false)
                            {
                                //name of destination like France
                                  $country_ids[]=$attr2->id_dest; 
                                     
                            }
                            foreach ($attr2 as $key_country2=>$country_arr)
                            {
                                
                                foreach($country_arr as $key_country=>$attr3)
                                {
                                    
                                    if(isset($attr3->id_country) && stripos($attr3->name,$location->name)!==false)
                                    {
                                        $country_ids[]=$attr3->id_country;
                                        if(isset($country_arr->base))
                                        {
                                             foreach($country_arr->base as $key5=>$attr5)
                                             {
                                                foreach($attr5 as $key6=>$attr6)
                                                {
                                                    if(isset($attr6->id_base))
                                                    {
                                                        $port_ids[]=$attr6->id_base;
                                                    }
                                                    
                                                }
                                             }
                                        }
                                    }
                                    else
                                    {
                                        /*foreach($attr3 as $key4=>$attr4)
                                        {
                                            
                                            if(isset($attr4->id_country) && stripos($attr4->name,$location->name)!==false)
                                            {
                                                $country_ids[]=$attr4->id_country;
                                                foreach($attr4 as $key5=>$attr5)
                                                {
                                                    //the same ports array from country entry
                                                }
  
                                            }
                                            if($key4==='base')
                                            {
                                                foreach($attr4 as $key5=>$attr5)
                                                {
                                                    if(isset($attr5->id_base) && stripos($attr5->name,$location->name)!==false)
                                                    {
                                                        $port_ids[]=$attr5->id_base;
                                                    }
                                                    else
                                                    {
                                                        if(!isset($attr5->id_base) && !isset($attr5->name))
                                                        {
                                                            foreach($attr5 as $key6=>$attr6)
                                                            {
                                                                if(isset($attr6->id_base) && stripos($attr6->name,$location->name)!==false)
                                                                {
                                                                    $port_ids[]=$attr6->id_base;
                                                                }
                                                                
                                                            }
                                                        }

                                                    }
                                                        
                                                }
                                            }
                                        }*/
                                    }

                                    if($key3==='base')
                                    {
                                        if(isset($attr3->id_base)  && stripos($attr3->name,$location->name)!==false)
                                        {
                                            $port_ids[]=$attr3->id_base;
                                        }
                                        else
                                        {
                                            if(!isset($attr3->id_base) && !isset($attr3->name))
                                            {
                                                foreach($attr3 as $key4=>$attr4)
                                                {
                                                    if(isset($attr4->id_base) && stripos($attr4->name,$location->name)!==false)
                                                    {
                                                        $port_ids[]=$attr4->id_base;
                                                    }
                                                    else
                                                    {
                                                        foreach($attr4 as $key5=>$attr5)
                                                        {
                                                           if(isset($attr5->id_base) && stripos($attr5->name,$location->name)!==false) 
                                                           {
                                                                $port_ids[]=$attr5->id_base;
                                                           }
                                                        }
                                                       
                                                    }
                                                }
                                            }

                                        }
                                    }                                    
                                } 
                                
                            }

                        }
                    }
  
                }
                  

                $html .='<input type="hidden" id="parsed_from" value="'.$date_from.'" />';   
		        $html .='<input type="hidden" id="parsed_to" value="'.$date_to.'" />';
			    $html .='<input type="hidden" id="parsed_dest" value="'.$location->name.'" />';
                 if (isset($_GET['bt_type']) && !empty($_GET['bt_type']))
                {
                    $html .='<input type="hidden" id="parsed_type" value="'.ucfirst($_GET['bt_type']).'" />';	
                }
                $html .=$this->head_result();   
                $html .='<tbody>';
                if(count($port_ids)>0)
                {
                    //sedna results
                    $args = array(
                        'post_type' => 'boat_page',
                        'orderby'=>'ID',
                        'order' => 'ASC',
                        'posts_per_page' => 8,
                        'meta_query' => array(
	                   array(
		                  'key' => 'homeport_id',
		                   'value' => $port_ids,
                           'compare' => 'IN'
	                       ))
                           );
                    $sedna_found = get_posts($args);
                    foreach($sedna_found as $sedna_boat)
                    {
                         $boat_page =  get_permalink($sedna_boat->ID);
                         $boat_page .='?dst='.$location->name.'&date_from='.$date_from_boat.
                                            '&date_to='.$date_to_boat;
                         if (isset($_GET['bt_type']) && !empty($_GET['bt_type']))
                         {
                            $boat_page .='&bt_type='.$bt_type->name;
                         }
                         $boat_info=$this->get_boat_fields($sedna_boat->ID);
                         
                            //$boat_info['BoatPrice']=$boat_price;
                            //$boat_info['BoatCurrency']=$boat_currency;
                            $boat_info['BoatPage']=$boat_page;
                            //display each boat data
                        // if(!empty($boat_info['BoatModel']))
                         //{
                              $html .=$this->boat_result_row($boat_info);  
                         //}
                        
                    }
                }
                
                
                //search in booker database
                
                
                $array_search=array('post_type'=>'boat_post',  'posts_per_page' => 30, 'country' => $location->slug,
                'orderby'    => 'meta_value',
	               'order'      => 'DESC',
                    'meta_key'  => 'brand');
                
                if (isset($_GET['bt_type']) && !empty($_GET['bt_type']))
                {
                    $bt_type=get_term_by('name',ucfirst($_GET['bt_type']),'bt_type');
                    $array_search=array('post_type'=>'boat_post',  'posts_per_page' => 30, 'tax_query' => array(
		              array(
			         'taxonomy' => 'country',
			         'field' => 'slug',
			         'terms' => $location->slug),
                     array(
			         'taxonomy' => 'bt_type',
			         'field' => 'slug',
			         'terms' => $bt_type->slug)),
                      'orderby'    => 'meta_value',
	               'order'      => 'DESC',
                    'meta_key'  => 'brand');
                }
                $boats=get_posts($array_search);
                
                //'posts_per_page'=>1,'meta_key'=>'id_boat',
                if (!empty($boats) && count($boats)>0)
                {
					// boat price query from Boatbooker Database
					$boatid = array();
					foreach($boats as $bt_price){
						$boatid[]=get_post_meta($bt_price->ID,'id_boat',true);
					}
                    
					$ids = implode(',',$boatid);
				   $boat_id = $boat_fields['BoatID'];
					$boatDateFr = explode(".",$_GET['date_from']);	
					$boatDPF = $boatDateFr[2].'-'.$boatDateFr[1].'-'.$boatDateFr[0];		
					$boatDateTo = explode(".",$_GET['date_to']);
					$boatDPT = $boatDateTo[2].'-'.$boatDateTo[1].'-'.$boatDateTo[0];						
			       $real_time_query='https://api.boatbooker.net/ws/sync/v2/main?username=18abb2dc5849491eaaa06ab3d4fb1dc2&password=2ec90d50df594e419c3e52088f947556&loadBoats=true&loadSpecificBoats='.$ids.'&availDatePeriodFrom='.$boatDPF.'&availDatePeriodTo='.$boatDPT;

					$boat_data=json_decode(file_get_contents($real_time_query),true);
					
                    foreach($boats as $boat)
                    {
                        //checking the price for current  search date per month and day
						  // $current_date = new DateTime(date('d.m.Y'));
                        $prices=get_post_meta($boat->ID,'price',true);
                        $boat_price=0;
                        $boat_currency='EUR';
                        if(!empty($prices) && $prices>0)
                        {
                            // $found_price=false;
                            // $last_date=new DateTime(get_post_meta($boat->ID, 'DateTo_0',true));
                            // $last_price=0;
							$ar_prices = array();
                            for($i=0;$i<$prices;$i++)
                            {
                                $date_from_price1= get_post_meta($boat->ID, 'DateFrom_'.$i,true);
								 $date_from_price1 = explode("-",$date_from_price1);	
								 $date_from_price = '2014-'.$date_from_price1[1].'-'.$date_from_price1[2];
								  $date_to_price1=get_post_meta($boat->ID, 'DateTo_'.$i,true);
								  $date_to_price1 = explode("-",$date_to_price1);
								   $date_to_price = '2014-'.$date_to_price1[1].'-'.$date_to_price1[2];
								 $boat_price1=get_post_meta($boat->ID, 'Price_'.$i,true);
								$ar_prices[$i] = array(
											'DateFrom' =>  $date_from_price,
											'DateTo' => $date_to_price,
											'Price' => $boat_price1
								
								);
                                // $date_from_price= new DateTime(get_post_meta($boat->ID, 'DateFrom_'.$i,true));
                                // $date_to_price=new DateTime(get_post_meta($boat->ID, 'DateTo_'.$i,true));
                                // if($current_date->getTimestamp()>=$date_from_price->getTimestamp() &&
                                    // $current_date->getTimestamp()<=$date_to_price->getTimestamp())
                                // {
                                    // $boat_price=get_post_meta($boat->ID, 'Price_'.$i,true);
                                    // $boat_currency=get_post_meta($boat->ID, 'CurrencyCode_'.$i,true);
                                // }
                                // if($last_date->getTimestamp()>$date_to_price->getTimestamp())
                                // {
                                    // $last_price=$i;
                                    // $last_date=new DateTime(get_post_meta($boat->ID, 'DateTo_'.$i,true));
                                // }
                            }
							$boatdpf1 = explode("-",$boatDPF);
						    $boatdpf = '2014-'.$boatdpf1[1].'-'.$boatdpf1[2];
							foreach ($ar_prices as $bprice){
								$start_ts = strtotime($bprice['DateFrom']);
								$end_ts = strtotime($bprice['DateTo']);
								$user_ts = strtotime($boatdpf);
								if($user_ts >= $start_ts && $user_ts <= $end_ts){
									$boat_price = 	$bprice['Price'];
								}								
	
							} 
							
                        }
                        // if($boat_price==0 && !empty($prices) && ($prices>0))
                        // {
                            // $boat_price=get_post_meta($boat->ID, 'Price_'.$last_price,true);
                            // $boat_currency=get_post_meta($boat->ID, 'CurrencyCode_'.$last_price,true);
                        // }
                        
                         
                         //if($boat_price>0)
                         //{ 
                            //boat page must be with dst
                            $boat_page =  get_permalink($boat->ID);
                            $boat_page .='?dst='.$location->name.'&date_from='.$date_from_boat.
                                            '&date_to='.$date_to_boat;
                            if (isset($_GET['bt_type']) && !empty($_GET['bt_type']))
                            {
                                $boat_page .='&bt_type='.$bt_type->name;
                            }
                            $boat_info=$this->get_boat_fields($boat->ID);
                            //print_r($boat_info);
                            $boat_info['BoatPrice']=$boat_price;
                            $boat_info['BoatCurrency']=$boat_currency;
                            $boat_info['BoatPage']=$boat_page;
                            //display each boat data
                            if(!empty($boat_info['BoatModel']))
                            {
                                $html .=$this->boat_result_row($boat_info,$boat_data);
                            }
                            else
                            {
                                //$html .='<a href="'.$boat->ID.'">'.$boat->ID.'</a>';
                            }
                    }
                    
                }
            }  
         }    
            $html .='</tbody>';
            $html .='</table>';
            $html .='</div>';
            $html .='<aside id="side_filter" class="mk-builtin refine">'.
                        '<div class="sidebar-wrapper"></div>'.
                    '</aside>'.	
                    '<div class="clearboth"></div>';
            
            $html .='</div></div>';
            return $html;  
    }
    
    public function boat_result_row($boat_fields,$boatdata)
    {
        
    // Boat price query from boatbooker Database
	$boat_id = $boat_fields['BoatID'];
	$boatDateFr = explode(".",$_GET['date_from']);	
	$boatDPF = $boatDateFr[2].'-'.$boatDateFr[1].'-'.$boatDateFr[0];		
	$boatDateTo = explode(".",$_GET['date_to']);
	$boatDPT = $boatDateTo[2].'-'.$boatDateTo[1].'-'.$boatDateTo[0];	
	
	foreach ($boatdata['Boats'] as $value){
		if($value['ID'] == $boat_id){
			foreach($value['Prices'] as $price){
				  $start_ts = strtotime($price['DateFrom']);
					$end_ts = strtotime($price['DateTo']);
					$user_ts = strtotime($boatDPF);
					if($user_ts >= $start_ts && $user_ts <= $end_ts){
						$boat_fields['BoatPrice'] = $price['Price'];
					}
			}
		}
	}
	
	// end query here
		
		$html ='';
        $html .='<tr>'.
                    '<td  class="filter_cell" >'.
                    $boat_fields['BoatType'].'</td>';
                    $html .='<td  class="filter_cell" >';
                    if(empty($boat_fields['CabinsMax']))
                    {
                        if(empty($boat_fields['CabinsBasic']))
                        {
                            $html .='0';
                        }
                        else
                        {
                            $html .=$boat_fields['CabinsBasic'];
                        }
                    }
                    else
                    {
                        $html .=$boat_fields['CabinsMax'];
                    }
                    
                    $html .='</td>';
                    $html .='<td  class="filter_cell" >';
                    if(empty($boat_fields['CabinsBasic']))
                    {
                        $html .='0';
                    }
                    else
                    {
                        $html .=$boat_fields['CabinsBasic'];
                    }
                    $html .='</td>';
                    $html .='<td  class="filter_cell" >'.
                            $boat_fields['YearBuilt'].'</td>';
                    $html .='<td  class="filter_cell" >'.
                            $boat_fields['Operator'].'</td>';
                    $html .='<td  class="filter_cell" >'.
                            $boat_fields['BoatModel'].'</td>';
                    $html .='<td  class="filter_cell" >';
                    $html .= ($boat_fields['BoatLength'])?  number_format($boat_fields['BoatLength'], 2):0;                                
                    $html .= '</td>';
                    $html .='<td  class="filter_cell" >';
                    if(empty($boat_fields['BerthsMax']))
                    {
                        if(empty($boat_fields['BerthsBasic']))
                        {
                            $html .='0';
                        }
                        else
                        {
                            $html .=$boat_fields['BerthsBasic'];
                        }
                    }
                    else
                    {
                        $html .=$boat_fields['BerthsMax'];
                    }
                    
                    $html .='</td>'.
                    '<td  class="filter_cell" >No'.
                    '</td>'.
                    '<td  class="filter_cell" >No</td>'.
                    '<td  class="filter_cell" >';

                    $html .= ($boat_fields['BoatPrice']) ? $boat_fields['BoatPrice'] : 0 ;
                    
                     $html .= '</td>'.
                    '<td  class="filter_cell" >5</td>'.
                    '<td  class="filter_cell" >'.$boat_fields['Homeport'].'</td>';
        $html .='<td  class="display_cell" >';
        $html .='<div class="display_result" >';
        
        $boat_description=$this->form_desc_data('booker_post',$boat_fields);
        //dysplaying price
        $html .='<div class="display_price" >';
        $price_discount=number_format($boat_fields['BoatPrice']-($boat_fields['BoatPrice']*5/100),0,'',' ');
        $symbol_cur='&euro;';
        
        //Direct price -  prie with disconts from fleet operator
        if($boat_fields['BoatCurrency']!=='EUR')
        {
			$symbol_cur='$';			
        }
        
        if($boat_fields['BoatPriceNew']>0)
        {
            if($boat_fields['BoatOldPrice']>0)
            {
                $html .='<span class="mts_display_price first squarebrd"><span class="mts_our_price">'.
                    'Direct Price</span><span style="text-decoration: line-through;">'.
                    number_format($boat_fields['DirectPrice'],0,'',' ').$symbol_cur.'</span></span>';
                $html .='<span class="mts_display_price first squarebrd"><span class="mts_our_price">'.
                    'Discount</span><span style="text-decoration: line-through;">'.
                    number_format($boat_fields['DirectDiscPrice'],0,'',' ').$symbol_cur.'</span></span>';
            }
            else
            {
                $html .='<span class="mts_display_price first squarebrd"><span class="mts_our_price">'.
                    'Base Price</span><span style="text-decoration: line-through;">'.
                    number_format($boat_fields['BoatPriceNew'],0,'',' ').$symbol_cur.'</span></span>';
            }
            $html .='<span class="mts_display_price squarebrd"><span class="mts_our_price" >Our Price '.
                    '<span class="our_discount" >- 5%</span></span>'.$price_discount.$symbol_cur.'</span>';
        } 
        else
        {
            $html .='<span class="mts_display_price first squarebrd mes">For the current price you can contact us'.
                    ' by <a title="sailchecker.com" href="mailto:info@sailchecker.com">email</a>.</span>';
            $html .='<span class="mts_display_price squarebrd mes">We will confirm 5%'.
                    ' discount!</span>';
        }           
        //dysplaying link to boat page
        $html .='<a class="mts_display_book_link squarebrd" href="'.$boat_fields['BoatPage'].'" >More Info</a>';
                
        $html .='</div>';
        //the end of displying price
        
        if(!empty($boat_fields['Images']))
        {
            //displaying description of boat
            $html .='<div class="display_info" >';
        }
        else
        {
            $html .='<div class="display_info full" >';
        }
        //displaying the eader of boat
        $html .='<div class="display_header_mts" >';
        $html .='<p><h3>'.$boat_fields['BoatType'].' '.$boat_fields['BoatModel'].
                ' - Available in '.$boat_fields['Homeport'].', '.$boat_fields['Country'].'</h3></p>';
        
        $html .='</div>';
        //the end of displaying boat header
        
        //displaying boat info
        $html .='<div class="display_content_mts">';
        $short_desc = $this->descriptions('main',$boat_description); 
        $short_desc .= ' '.$this->descriptions('reservation',$boat_description);
        $html .=substr($short_desc, 0 , 200).'...'; 
        $html .='</div>';
        
        
        //displaying footer 
        $html .='<div  class="display_footer_mts" >'.
                '<p class="other_info" >'.$this->descriptions('cabins',$boat_description).'</p>';
        if(!empty($boat_fields['Operator']))
        {
                '<p class="other_info">Fleet Operator: <span class="name">'.
                $boat_fields['Operator'].'</span></p>';
        }
        $html .='</div>';
        //the end of dysplaying footer
        
        //displaying icons
        $html .= '<div  class="display_icons_mts" >';
        $html .= '<span class="berth_icon">';
        $html .= '<span>Berths:<br />';
        if(empty($boat_fields['BerthsStr']))
        {
            $html .='&nbsp;';
        }
        else
        {
            $html .=$boat_fields['BerthsStr'];
        }
        
        $html .='</span>';
        $html .= '</span>';
        $html .= '<span class="engine_icon">';
        $html .= '<span>Engine: <br />';
        if(!empty($boat_fields['Engine']))
        {
            $html .=$boat_fields['Engine'];
        }
        else
        {
            $html .='&nbsp;';
        }
        $html .='</span>';
        $html .= '</span>';
        $html .= '<span class="cabins_icon">';
        $html .= '<span>Cabins: <br />'.$boat_fields['CabinsStr'].'</span>';
        $html .= '</span>';
        $html .= '<span class="draft_icon">';
        $html .= '<span>Draught: <br />';
        if(!empty($boat_fields['Draft']))
        {
            $html .=$boat_fields['Draft'];
        }
        else
        {
            $html .='&nbsp;';
        }
        $html .='</span>';
        $html .= '</span>';
        $html .= '<span class="size_icon">';
        $html .= '<span>Size: <br />'.$boat_fields['BoatLength'].'</span>';
        $html .= '</span>';
        $html .= '</div>';
        //end of boat icons	
        
        $html .='</div>';
        //the end of displaying boat info
        
        
        
        //left part of bot description
         if(!empty($boat_fields['Images']))
        {
        $html .='<div class="left_info">';
        //displaying picture
       
            $html .='<div class="display_pic" >';
            $html .='<span class="wrapp-search-res-img" >';
            $html .='<img class="search-res-img" src="'.$boat_fields['Images'][0].'" />';
            $html .='</span></div>';
         //end of left part description
        $html .='</div>';
        
        } 
       
        $html .='<div class="clearfix"></div>';
        
         $html .='</div>';
        //the end of displaying description

        $html .='</td>';
        $html .='</tr>';
        
        return $html;
    }
    
    
    public function head_result()
    {
        $html ='';
        $html .='<table id="mts_res_table" >
				<thead>
					<tr>
						<th class="filter_cell" >Type of boat</th><!-- 0 -->
						<th class="filter_cell" >No of double cabins</th><!-- 1 -->
						<th class="filter_cell" >No of single cabins</th><!-- 2 -->
						<th class="filter_cell" >Built in</th><!--3 -->
						<th class="filter_cell" >Brand</th><!-- 4 -->
						<th class="filter_cell" >Model of boat</th><!-- 5 -->
						<th class="filter_cell" >Length</th><!-- 6-->
						<th class="filter_cell" >No of berth</th><!-- 7 -->
						<th class="filter_cell" >Crewed</th><!-- 8 -->
						<th class="filter_cell" >License needed</th><!-- 9 -->
						<th class="filter_cell" >Price</th><!-- 10 -->
						<th class="filter_cell" >Discount</th><!-- 11 -->
						<th class="filter_cell" >Base</th>
						<th id="push_sorting_filters" ></th>
					</tr>
				</thead>';
        return $html;
    }


    //function for inserting all styles
    public function mts_styles_front()
    {
        if(stripos($_SERVER['REQUEST_URI'],'admin')===false)
        {
            //choosing css files depending on short_code searching form
            //wp_register_style("mts-all", WP_PLUGIN_URL."/mts-search-engine/js/icheck/skins/all.css", array(), false, 'all');
             //wp_register_style("mts-jquery-google","http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-darkness/jquery-ui.css?ver=3.9.1", array(), false, 'all');
               wp_register_style("mts-front", WP_PLUGIN_URL."/mts-search-engine/css/front_form.css", array(), false, 'all');
               // wp_register_style("mts-jquery-ui1", WP_PLUGIN_URL."/mts-search-engine/js/jqueryui/css/ui-lightness/jquery-ui-1.10.4.min.css", array(), false, 'all');
             //wp_register_style("mts-skin",WP_PLUGIN_URL."/mts-search-engine/js/icheck/skins/all.css", array(), false, 'all');
               wp_register_style("mts-jquery-ui", WP_PLUGIN_URL."/mts-search-engine/css/jquery-ui.css", array(), false, 'all');
                 wp_enqueue_style("mts-jquery-ui");
            //wp_enqueue_style("mts-all");
             wp_enqueue_style("mts-front");
            // wp_enqueue_style("mts-jquery-ui1");
            //wp_enqueue_style("mts-skin");
           // wp_enqueue_style("mts-jquery-google");
          }    

    }
    
    
    
    public function mts_styles_result()
    {
            //choosing css files depending on short_code searching form
            wp_register_style("mts-all", WP_PLUGIN_URL."/mts-search-engine/js/icheck/skins/all.css", array(), false, 'all');
            wp_register_style("mts-dataTables", WP_PLUGIN_URL."/mts-search-engine/js/datatables/css/jquery.dataTables.css", array(), false, 'all');
            //wp_register_style("mts-jquery-ui", WP_PLUGIN_URL."/mts-search-engine/js/jqueryui/css/ui-lightness/jquery-ui-1.10.4.min.css", array(), false, 'all');
           // wp_register_style("mts-jquery-google","http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/ui-darkness/jquery-ui.css?ver=3.9.1", array(), false, 'all');
              wp_register_style("mts-form", WP_PLUGIN_URL."/mts-search-engine/css/form.css", array(), false, 'all');
           
           //wp_register_script("mts-background", WP_PLUGIN_URL."/mts-search-engine/js/changing_background.js", array('jquery'), false, true);
                
                wp_register_style("mts-result", WP_PLUGIN_URL."/mts-search-engine/css/results.css", array(), false, 'all');
                
            //wp_register_style("mts-skin",WP_PLUGIN_URL."/mts-search-engine/js/icheck/skins/all.css", array(), false, 'all');
             wp_register_style("mts-jquery-ui", WP_PLUGIN_URL."/mts-search-engine/css/jquery-ui.css", array(), false, 'all');
            
            //wp_enqueue_style("mts-all");
            wp_enqueue_style("mts-dataTables");
            
            
            //wp_enqueue_style("mts-jquery-google");         
             //wp_enqueue_style("mts-form");
              
             wp_enqueue_style("mts-result");
 
             wp_enqueue_style("mts-jquery-ui");
         
            
            wp_enqueue_style("mts-all");

    }
    
    

   
        
        
        function my_add_frontend_scripts() {
            if (strpos($_SERVER['REQUEST_URI'],'wp-admin')===false) {
            //wp_deregister_script('jquery');
   //wp_register_script('jquery', "http" . ($_SERVER['SERVER_PORT'] == 443 ? "s" : "") . "://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js", false, null);
   //wp_enqueue_script('jquery');
        //wp_enqueue_script('jquery');
        //wp_register_script("mts-jquery-1.10", WP_PLUGIN_URL."/mts-search-engine/js/jqueryui/js/jquery-1.10.2.js", array(), false, true);
            
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script("jquery-ui-widget");
        wp_enqueue_script("jquery-ui-autocomplete");
        wp_enqueue_script("jquery-ui-datepicker");
        wp_register_script("mts-formjs", WP_PLUGIN_URL."/mts-search-engine/js/ajax_form.js", array('jquery-ui-autocomplete'), false, true);
           wp_enqueue_script("mts-formjs");
           wp_localize_script( 'mts-formjs', 'MTSAjax', array( 'ajaxurl' => admin_url('admin-ajax.php')) );
           wp_enqueue_script("jquery-ui-dialog");
            wp_enqueue_script("jquery-ui-droppable");
            wp_enqueue_script("jquery-ui-draggable");
            wp_enqueue_script("jquery-ui-mouse");
            wp_enqueue_script("jquery-ui-slider");

                   
            wp_register_script("mts-initjs", WP_PLUGIN_URL."/mts-search-engine/js/init.js", array('jquery'), false, true);
             wp_register_script("mts-dataTablesjs", WP_PLUGIN_URL."/mts-search-engine/js/datatables/js/jquery.dataTables.min.js", array( 'jquery' ), false, true);
          wp_register_script("mts-naturaljs", WP_PLUGIN_URL."/mts-search-engine/js/datatables/js/datatables.natural.js", array( 'jquery' ), false, true);
                 wp_register_script("mts-icheckjs", WP_PLUGIN_URL."/mts-search-engine/js/icheck/icheck.min.js", array('jquery'), false, true);
          
             
         
         wp_enqueue_script("mts-dataTablesjs");
           wp_enqueue_script("mts-naturaljs");
            wp_enqueue_script("mts-icheckjs"); 
            wp_enqueue_script("mts-initjs");
            
             wp_register_script("mts-boatjs",WP_PLUGIN_URL."/mts-search-engine/js/ajax_boat.js", array('jquery'), false, true);
                wp_enqueue_script("mts-boatjs"); 
                wp_localize_script( 'mts-boatjs', 'MTSAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
      
                 

            
           }
           
        }
            
        
        
        
        public function results_script()
        {
               
        }
    
    /************************************functions for download pdf format description of boat***********************/    
       
    //the function for downloading pdf file with boat description
    public function download_callback(){
        //rewrite this function with php fnctions
         ob_start();
         
         echo '<html>
	<head>
		<style>
			.panel {
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid transparent;
        border-radius: 4px;
        }
        .panel-heading {
            color: #333;
            background-color: #f5f5f5;
            border-color: #ddd;
            padding: 10px 15px;
            border-bottom: 1px solid transparent;
        border-top-right-radius: 3px;
        border-top-left-radius: 3px;
        }
        .panel-title{
            font-size: 24px;
            color: #393836;
            font-weight: bold;
            text-transform: none;
            margin-top: 0;
            margin-bottom: 0;
        }
        .panel-body {
            padding: 15px;
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
        }
        td {
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
            text-align: center;
            }
        </style>
   </head>
   <body>';

   	echo '<h1><a href="'.$_POST['boat_link'].'">'.$_POST['boat_heading'].'</a></h1>';
    echo '<div class="panel panel-default">'.
	       '<div class="panel-heading">'.
	       '<h3 class="panel-title" id="overview" >Overview</h3>'.
	       '</div>'.
	       '<div class="panel-body"><img style="width: 100%;height: auto;" src="'.
            $_POST['boat_img'].'"  /><br /><br /></div><hr />';
	echo '<p>'.$_POST['boat_desc'].'</p>';
    echo '<hr /><p><strong>'.$_POST['boat_built'].'</p><p>'.$_POST['boat_reserv'].'</p><p>'. $_POST['boat_cabins'].'</strong></p>';
    echo '<p><strong>Fleet operator: '.$_POST['boat_operator'].'</strong></p>';
			
	echo '</div></div>';


    echo '<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title" id="prices" >Prices</h3>
	  </div>
	  <div class="panel-body">';
    if(!empty( $_POST['sprice']))
    {

    echo '<table class="table table-hover" >
			<thead>
				<tr>
					<td>Price</td>
					<td>From</td>
					<td>To</td>
				</tr>
			</thead>
			<tbody>';
   foreach($_POST['sprice'] as $kss=>$vsss)
    {
        $cur=$vsss['currency'];
        if($vsss['currency']!=='$')
        {
           $cur='&euro;';
        }
        echo "<tr><td>".$vsss['price']." ".$cur."</td><td>".$vsss['start']."</td><td>".$vsss['end']."</td></tr>";
}
	echo '</tbody>
		</table>';
	}
	echo '</div></div>';
    
    
    /*<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title" id="extra-costs" >Extra Costs</h3>
	  </div>
	  <div class="panel-body">
	    <?php
	    
	    if(!empty( $_POST['eprice'])){
				?>
				<table class="table table-hover" >
					<thead>
						<tr>
							<td>Service</td>
							<td>Price</td>
							<!--<td>Quantity</td>-->
						</tr>
					</thead>
					<tbody>
				<?php
				foreach( $_POST['eprice'] as $k=> $v )
                {
					
					?>
					
						<tr>
							<td><?php echo $v['name']; ?></td>
							<td>&#8364; <?php echo $v['price']; ?> / <?php echo $v['per']; ?> / <?php echo $v['per2']; ?>  </td>
							<!--<td><?php //echo $v['quantity']; ?></td>-->
						</tr>
					
					<?php
					
				}
				
				?>
				</tbody>
				</table>
				<?php
				
			}

		
	    ?>
	  </div>
</div>*/

        echo '<div class="panel panel-default">
	           <div class="panel-heading">
	               <h3 class="panel-title" id="details" >Details</h3>
	           </div>
	           <div class="panel-body">';
	  		  	
		//if( is_array($_POST['plans']) ) :
	  	//<p>Yacht Layout</p>
	    //<img class="mts_plan" src=";
	      if(isset($_POST['chars']))
            {
                
            echo '<p>Charter Yacht Equipment</p>';
			$nr_elm = count($_POST['chars']);        // gets number of elements in $aray
    		$html_table = '<table class="table table-hover table-bordered"><tr>';
			$nr_col = 3;       // Sets the number of columns
				
			// If the array has elements
			if ($nr_elm > 0) 
            {
				  // Traverse the array with FOR
				  for($i=0; $i<$nr_elm; $i++) {
				    $html_table .= '<td><strong>' .$_POST['chars'][$i]['name']. '</strong> '.$_POST['chars'][$i]['quantity'].'</td>';       // adds the value in column in table
				
				    // If the number of columns is completed for a row (rest of division of ($i + 1) to $nr_col is 0)
				    // Closes the current row, and begins another row
				    $col_to_add = ($i+1) % $nr_col;
				    if($col_to_add == 0) { $html_table .= '</tr><tr>'; }
				  }
				
				  // Adds empty column if the current row is not completed
				  if($col_to_add != 0) $html_table .= '<td colspan="'. ($nr_col - $col_to_add). '">&nbsp;</td>';
				}
				
				$html_table .= '</tr></table>';         // ends the last row, and the table
				
				// Delete posible empty row (<tr></tr>) which cand be created after last column
				$html_table = str_replace('<tr></tr>', '', $html_table);
				
				echo $html_table; 
              }       // display the HTML table
        echo '</div></div>';



       echo '<div class="panel panel-default">
	           <div class="panel-heading">
	               <h3 class="panel-title" id="map" >Map</h3>
	           </div>
	           <div class="panel-body">
	  		       <img src="http://maps.googleapis.com/maps/api/staticmap?center='.$_POST['location'].'&size=600x300'.
                     '&key=AIzaSyDOZM0TA4Qhki2fM2MseW5Bbh24AqX-XQ4" />'. 
                '</div>
            </div>';

                      

        echo '</body></html>';
        
        
        $html = ob_get_contents();
        ob_end_clean();


        include(dirname(__FILE__).'/libs/dompdf/dompdf_config.inc.php');
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        //file_put_contents($_SERVER['DOCUMENT_ROOT'].'document.pdf', $output);
        $dompdf->stream("boat_details.pdf",array("Attachment" => 0));
        
        exit;

        
    }
    
    //initialising the main variables for scrips
    public function scripts_form()
    {
        /*$this->mts_query=array();
        if (empty($_GET))
        {
            $this->mts_query['query']='yacht-charter';
        }
        else
        {   
            foreach ($_GET as $key=>$value)
            {
                $this->mts_query[$key]=$value;
            }
            
        }
       $first_script='<script type="text/javascript">'.
                     'window.WEB_URL = "'.$this->pluginUrl.'/"; '.
                     'window.WEB_QUERY = "'.http_build_query($this->mts_query).'"; '.
                     'window.Sedna_cfg = '.$this->SEDNA_CFG.'; '.
                     '</script>';
                     
       return $first_script;*/
    }    
    
    
    
    //showing all fields in search form 
    public function fields_form()
    {
        $formHTML='';

        $form_options=$this->boat_type_options();

        
        $formHTML .= '<input type="text" class="mts_n_input selectdest" id="mts_dst" name="dst" autocomplete="off" value="Where are you looking to charter?" />'.
                    '<div class="clearfix"></div>'.
                    '<input type="text" placeholder="Check In"  id="mts_date_from" name="date_from" class="mts_datepick left mts_date_from" '.
                    'value=" " />'.
                   	'<input type="text" placeholder="Check Out"  id="mts_date_to" name="date_to" class="mts_datepick right mts_date_to" '.
                    'value=" " />'.
                    '<div class="clearfix" ></div>'.
                    '<select name="boat_type" class=" left selectboat"  id="mts_boat_type" >'.$form_options.'</select> '.
                    '<input type="button" class=" right search_go"  id="start_search" value="Search" />'.
                    '<div class="clearfix"></div>'.
                    '<p class="mts_search_error error" style="display: none;" >Please select a destination</p>'.
                    '<p class="mts_search_date_error error" style="display: none;" >Please select return date. If both date fields are empty we will search for all boats available in the next 7 days</p>';
       
        return $formHTML;

    }
    
    
    //getting value of destination
    function getSq($str)
    {
	
	   /* if more apis load them in a loop here */
        include 'Helper_API.php';
     
	   $sedna_cfg 	= json_decode($this->SEDNA_CFG,1);
	   $broker_id 	= $sedna_cfg['broker_id'];
	   $language      = $sedna_cfg['language'];
        $mts_query=array();
        $mts_query['language']=0;
        $mts_query['refagt']='wxft6043';
	   $h = new Helper;
	    $query = http_build_query($mts_query);
       $data = $h->XMLtoarray( $h->byGET_( 'http://client.sednasystem.com/API/GetDestinations2.asp?'.$query )  );

        //search destinaion according to its code
	   if(!empty($data['destination']))
       {
		  foreach( $data['destination'] as $key => $value )
          {
			
			/* noticed that only srh_destination is required for testing. If the other param's required on live, just un comment */
			if( str_replace(' ', '-', strtolower($value['@attributes']['name']))  == $str){ return 'd'.$value['@attributes']['id_dest']; }

			if( isset($value['country']) && is_array($value['country']) && isset($value['country'][0]) ){
				foreach($value['country'] as $skey => $svalue){
					if( str_replace(' ', '-', strtolower($svalue['@attributes']['name']))  == $str){ return'c'.$svalue['@attributes']['id_country']; }
				}
			}elseif( isset($value['country']) && is_array($value['country']) && isset($value['country']['@attributes']) ){
					if( str_replace(' ', '-', strtolower( $value['country']['@attributes']['name'] ))  == $str){ return 'c'.$value['country']['@attributes']['id_country']; }
			}
		  }
	   }
    }
    
    
    
    //getting data for searchparameters   
    public function mts_getdata()
    {

        if (isset($_GET['dst']) && !empty($_GET['dst']))
        {
            if(isset($_GET['date_to']) && isset($_GET['date_from']))
            {
                return true;
            }

        }
        else
        {
            return false;
        }
     
    }
    
    
    /*
     *Functions for result serach time for stopping function when the time is over
     */
     public function rutime($ru, $rus, $index) 
     {
        return ($ru["ru_$index.tv_sec"]*1000000 + intval($ru["ru_$index.tv_usec"]/1000))-  ($rus["ru_$index.tv_sec"]*1000000 + intval($rus["ru_$index.tv_usec"]/1000));
     }
    
    
    //showing results
    public function result_form_top()
    {
        $result_HTML="";
        $date_from='';
        $date_to='';
        $from='';
        
        if (isset($this->parsed_query['date_from']))
        {
            $date_from=$this->parsed_query['date_from'];
        }
        if (isset($this->parsed_query['date_to']))
        {
            $date_to=$this->parsed_query['date_to'];
        }
        if (isset($this->parsed_query['from']))
        {
            $from=$this->g_GET['dst'];
        }
        
        $result_HTML .='<input type="hidden" id="parsed_from" value="<'.$date_from.'" />'.
                        '<input type="hidden" id="parsed_to" value="'.$date_to.'" />'.
                        '<input type="hidden" id="parsed_dest" value="'.$from.'" />'.
                        '<!-- results table tansoy gay -->'.
                        '<table id="mts_res_table" >'.
                        '<thead><tr><!-- hidden filters -->'.
                            '<th class="filter_cell" >Type of boat</th><!-- 0 -->'.
                            '<th  class="filter_cell" >No of double cabins</th><!-- 1 -->'.
                            '<th  class="filter_cell" >No of single cabins</th><!-- 2 -->'.
                            '<th  class="filter_cell" >Built in</th><!--3 -->'.
                            '<th  class="filter_cell" >Brand</th><!-- 4 -->'.
                            '<th  class="filter_cell" >Model of boat</th><!-- 5 -->'.
                            '<th  class="filter_cell" >Length</th><!-- 6-->'.
                            '<th  class="filter_cell" >No of berth</th><!-- 7 -->'.
                            '<th  class="filter_cell" >Crewed</th><!-- 8 -->'.
                            '<th  class="filter_cell" >License needed</th><!-- 9 -->'.
                            '<th  class="filter_cell" >Price</th><!-- 10 -->'.
                            '<th  class="filter_cell" >Discount</th><!-- 11 -->'.
                            '<th  class="filter_cell" >Base</th><!-- 11 -->'.
                            '<!-- content -->'.
                            '<th id="push_sorting_filters" >'.
                            '<!-- i can moove this on build_widgets and to display only if enough data -->'.
                         '</th>'.
	                       '</tr>'.
                        '</thead>'.
				        '<tbody>';
        return $result_HTML;
    }
    
    
    
    /**************************functions for formatting and converting data****************************/
    private function form_desc_data($type,$data,$country='')
    {
        $structure=array();
        
        switch($type)
        {
            case 'booker':
                $structure['builtyear']=$data['BuiltYear'];
                $structure['bt_type']=$data['Type'];
                $structure['homeport']=$data['BaseName'];
                $structure['model']=$data['ModelName'];
                $structure['total_cab']=$data['MaxCabins'];
                $structure['short_cabins']=$data['CabinsStr'];
                $extra_cab=$data['MaxCabins']-$data['TotalCabins'];
                if ($extra_cab>0)
                {
                    $structure['cabins_str']=$data['TotalCabins'].' basic '.$data['TotalCabins'].
                                            ' and '.$extra_cab.' extra cabins';
                }
                else
                {
                    $structure['cabins_str']=$data['TotalCabins'].' basic cabins';
                }
                $structure['heads']=$data['Toilets'];
                break;
            case 'sedna':
                $structure['builtyear']=$data['@attributes']['buildyear'];
                if (isset($data['@attributes']['bt_Type']))
                {
                    $structure['bt_type']=$data['@attributes']['bt_Type'];
                }
                else
                {
                    $structure['bt_type']=$data['@attributes']['type'];
                }
                $structure['homeport']=$data['homeport']['@attributes']['name'];
                if (!empty($data['country']))
                {
                   $structure['homeport'] .=', '.$data['country']; 
                }
                $structure['model']=$data['@attributes']['model'];
                $structure['cabins_str']='';
                if (isset($data['@attributes']['nbsimcabin']))
                {
                    $structure['total_cab']=$data['@attributes']['nbsimcabin']+$data['@attributes']['nbdoucabin'];
                    $double_berth=intval($data['@attributes']['nbdoucabin']*2);
                    $structure['berth']=$double_berth.'+'.$data['@attributes']['nbsimcabin'];
                    if ($data['@attributes']['nbdoucabin']>0 && $data['@attributes']['nbsimcabin']>0)
                    {
                        $structure['cabins_str']=$data['@attributes']['nbdoucabin'].' double '.
                                                  ' and '.$data['@attributes']['nbsimcabin'].' single cabins';
                    }
                    elseif($data['@attributes']['nbsimcabin']>0)
                    {
                        $structure['cabins_str']=$data['@attributes']['nbsimcabin'].' single cabins';
                    }
                    elseif($data['@attributes']['nbdoucabin']>0)
                    {
                        $structure['cabins_str']=$data['@attributes']['nbdoucabin'].' double cabins'; 
                    }
                $structure['heads']=$data['@attributes']['heads'];
                $structure['total_people']=$data['@attributes']['nbper'];
                $structure['short_cabins']=$data['@attributes']['nbdoucabin'].'+'.$data['@attributes']['nbsimcabin'];
                $structure['reffit']=$data['@attributes']['reffitedyear'];
                }
                else{
                    $structure['homeport'] .=', '.$country; 
                    $structure['total_cab']=$data['@attributes']['NbDouCabin'];
                    $double_berth=intval($data['@attributes']['NbDouCabin']*2);
                    $structure['berth']=$double_berth.'+ 0';
                    if ($data['@attributes']['NbDouCabin']>0 && $data['@attributes']['NbSimCabin']>0)
                    {
                        $structure['cabins_str']=$data['@attributes']['NbDouCabin'].' double '.
                                                ' and '.$data['@attributes']['NbSimCabin'].' single cabins';
                    }
                    elseif($data['@attributes']['NbSimCabin']>0)
                    {
                        $structure['cabins_str']=$data['@attributes']['NbSimCabin'].' single cabins';
                    }
                    elseif($data['@attributes']['NbDouCabin']>0)
                    {
                        $structure['cabins_str']=$data['@attributes']['NbDouCabin'].' double cabins'; 
                    }
                    $structure['heads']=$data['@attributes']['heads'];
                    $structure['total_people']=$data['NbPax'];
                    $structure['short_cabins']=$data['@attributes']['NbDouCabin'].'+'.$data['@attributes']['NbSimCabin'];
                    $structure['reffit']='';
                }
                break;
 
            
            case 'booker_post':
                $structure['builtyear']=$data['YearBuilt'];
                $structure['bt_type']=$data['BoatType'];
                $structure['model']=$data['BoatModel'];
                $structure['total_cab']=$data['MaxCabins'];
                $structure['short_cabins']=$data['CabinsStr'];
                $extra_cab=$data['MaxCabins']-$data['CabinsBasic'];
                $structure['berths']=$data['BerthsMax'];
                $structure['berths_str']=$data['BerthsStr'];
                $structure['operator']=$data['Operator'];
                $structure['country']=$data['Country'];
                $structure['homeport']=$data['Homeport'];
                if(!empty($data['Country']))
                {
                    $structure['homeport'] .=', '.$structure['country'];
                }
                /*$structure['def_cur']=$data['def_cur'];*/
                if ($extra_cab>0)
                {
                    $structure['cabins_str']=$data['CabinsBasic'].' basic '.
                                            ' and '.$extra_cab.' extra cabins';
                }
                else
                {
                    $structure['cabins_str']=$data['CabinsBasic'].' basic cabins';
                }
                $structure['heads']=$data['ToiletsMax'];
                
                if(isset($data['SingleCabins']))
                {
                      if ((empty($data['SingleCabins']) || $data['SingleCabins']==0) && $data['DoubleCabins']>0)
                      {
                            $double_berth=intval($data['DoubleCabins']*2);
                            $structure['berth']=$double_berth.' + 0';
                            $structure['cabins_str']=$data['DoubleCabins'].' double cabins';
                            $structure['short_cabins']=$data['DoubleCabins'].' + 0';
                        }
                        elseif((empty($data['DoubleCabins']) || $data['DoubleCabins']==0) && $data['SingleCabins']>0)
                        {
                            $structure['berth']='0 + '.$data['SingleCabins'];
                            $structure['cabins_str']=$data['SingleCabins'].' single cabins';
                            $structure['short_cabins']='0 + '.$data['SingleCabins'];
                        }
                        else
                        {
                            $double_berth=intval($data['DoubleCabins']*2);
                            $structure['berth']=$double_berth.' + '.$data['DoubleCabins'];
                            $structure['cabins_str']=$data['DoubleCabins'].' double and'.
                                                $data['DoubleCabins'].' single cabins';
                            $structure['short_cabins']=$data['DoubleCabins'].' + '.$data['DoubleCabins'];
                        }
                }
                break;
                
            case 'booker_post_ru':
                $structure['builtyear']=$data['YearBuilt'];
                $structure['bt_type']=$data['BoatType'];
                $structure['model']=$data['BoatModel'];
                $structure['total_cab']=$data['MaxCabins'];
                $structure['short_cabins']=$data['CabinsStr'];
                $extra_cab=$data['MaxCabins']-$data['CabinsBasic'];
                $structure['berths']=$data['BerthsMax'];
                $structure['berths_str']=$data['BerthsStr'];
                $structure['operator']=$data['Operator'];
                $structure['country']=$data['Country'];
                $structure['homeport']=$data['Homeport'];
                $structure['location']=$data['Location'];
                $structure['country']=$data['Country'];
                /*$structure['def_cur']=$data['def_cur'];*/
                if ($extra_cab>0)
                {
                    $structure['cabins_str']=$data['CabinsBasic'].' basic '.
                                            ' and '.$extra_cab.' extra cabins';
                }
                else
                {
                    $structure['cabins_str']=$data['CabinsBasic'].' basic cabins';
                }
                $structure['heads']=$data['ToiletsMax'];
                
                if(isset($data['SingleCabins']))
                {
                      if ((empty($data['SingleCabins']) || $data['SingleCabins']==0) && $data['DoubleCabins']>0)
                      {
                            $double_berth=intval($data['DoubleCabins']*2);
                            $structure['berth']=$double_berth.' + 0';
                            $structure['cabins_str']=$data['DoubleCabins'].' double cabins';
                            $structure['short_cabins']=$data['DoubleCabins'].' + 0';
                        }
                        elseif((empty($data['DoubleCabins']) || $data['DoubleCabins']==0) && $data['SingleCabins']>0)
                        {
                            $structure['berth']='0 + '.$data['SingleCabins'];
                            $structure['cabins_str']=$data['SingleCabins'].' single cabins';
                            $structure['short_cabins']='0 + '.$data['SingleCabins'];
                        }
                        else
                        {
                            $double_berth=intval($data['DoubleCabins']*2);
                            $structure['berth']=$double_berth.' + '.$data['DoubleCabins'];
                            $structure['cabins_str']=$data['DoubleCabins'].' double and'.
                                                $data['DoubleCabins'].' single cabins';
                            $structure['short_cabins']=$data['DoubleCabins'].' + '.$data['DoubleCabins'];
                        }
                }
                break;
            
        }
        

        return $structure;
    }
    
    
    private function descriptions($type,$data)
    {
        $html='';
        $desc_variants=array(
            'h1'=>"<h1>{{model}} {{bt_type}} in {{homeport}}</h1>",
            'h1_ru'=>"<h1>Аренда яхты {{model}} {{bt_type}}, {{homeport}}</h1>",
             'main'=>'Sail this {{builtyear}} {{model}} {{bt_type}} available for bareboat yacht charter in  
                        {{homeport}}.',
             'main_ru'=>'Яхта {{bt_type}}  модели {{model}} {{builtyear}} года выпуска доступна для аренды  
                        без команды в порту   {{homeport}}, месторасположение - {{location}},   
                        страна  - {{country}}.',
            'additional'=>"Built {{builtyear}} {{reffit}} {{model}} {{bt_type}} available for Bareboat Charter in {{homeport}}.",
            'additional_ru'=>"???? {{bt_type}} {{builtyear}} ???? ??????? ?????? {{model}}  
                                ???????? ??? ?????? ??? ??????? ? ?????   {{homeport}}, ??????????????? - {{location}},   
                                ??????  - {{country}}.",
            'additional_booker'=>"Built {{builtyear}} {{model}} {{bt_type}} available for Bareboat Charter in {{homeport}}.",
            'reservation'=>"Reserve for 48 hours with no obligation and let our team help you refine your perfect {{bt_type}} 
                            charter now.",
            'reservation_ru'=>"Забронируйте яхту на 48 часов без каких-либо обязательств и позвольте нашей 
                                команде помочь вам определиться с арендой яхты прямо сейчас.",
            'cabins'=>"{{bt_type}} - Bult in: {{builtyear}} - Cabins: {{short_cabins}}.",
            'cabins_ru'=>"{{bt_type}} - Год выпуска: {{builtyear}} - Количество кают: {{short_cabins}}.");
            
        if (array_key_exists($type,$desc_variants))
        {
            $html= $desc_variants[$type];
            if(stripos($type,'_ru')!==false)
            {
                $html= iconv('windows-1251','utf-8',$desc_variants[$type]);
                foreach ($data as $key=>$text)
                {
                    if(!is_array($text))
                    {
                        $html=str_replace('{{'.$key.'}}', $text, $html);
                    }
 
                } 
            }
            else
            {
                foreach ($data as $key=>$text)
                {
                    if(!is_array($text))
                    {
                        $html=str_replace('{{'.$key.'}}', $text, $html);
                    }
 
                } 
            }

        }
        
        return $html;
    }
    
    
    //function for convering result values from json format
    private function _enull($str, $type = 'null')
    {
	   if( $type == 'int' ){ return strlen($str) > 0 ? str_replace(',', '.', $str) : 0 ; }

	   return is_string($str) && strlen($str) > 0 ? $str : ( $type == 'str' ? '' :  ( $type == 'int' ? 0 : 'null' ) ) ;

    }
    
    
    //function for convering result values from json format
    private function enull($str, $type = 'null')
    {

	   if( $type == 'int' ){ echo strlen($str) > 0 ? str_replace(',', '.', $str) : 0 ;return true; }

	   echo is_string($str) && strlen($str) > 0 ? $str : (  $type == 'str' ? '' :  ( $type == 'int' ? 0 : 'null' )  );

    }
    
    
    //function for convering result values from json format
    private function _count($arr)
    {

	   return is_array($arr) ? count($arr) : 0 ;

    }
    
    
    //function for convering result values from json format
    private function get_meta_boat( $v )
    {

	   $departure_bases = !empty($v['departure_bases']) && strlen($v['departure_bases']) > 2 ? json_decode($v['departure_bases'], 1) : array() ;

        $departure_base = null;

        if(isset($v['homeport']) && strlen($v['homeport']) > 2)
        {

            $vsdsdseport = json_decode($v['homeport'], 1);
            if( !empty($vsdsdseport['name']) )
            {
                $vhomeport = $vsdsdseport['name'];
            }

        }

       $departure_base = isset($departure_bases[0]) && isset($departure_bases[0]['name']) && strlen($departure_bases[0]['name']) > 1 && empty($vhomeport) ? ( isset($v['destination']) && strlen($v['destination']) > 1 ? $v['destination']." out of " : '' ) .$departure_bases[0]['name'] : ( !empty($vhomeport) ?$v['destination']." out of ".$vhomeport  : $v['destination']  ) ;
       $title = "<title>{$v['bt_type']} Yacht available for charter in {$departure_base}</title>";

	   $decr = "<meta name=\"description\" content=\"{$v['bt_type']} - {$v['name']}   {$departure_base} yacht charters.Yacht Charter No.1 - Boat Rentals and Yacht Charter Worldwide. More than 15,000 yachts to rent directly online. Bareboat sailing yachts, catamarans, houseboats, motor yachts or skippered mega luxury yachts for charter. Rent a yacht for your boating vacation.\" />";

	   $kwd = "<meta name=\"keywords\" content=\"{$v['bt_type']},{$v['name']}, {$v['model']} ,{$departure_base},boat rentals,rent a boat,rent a yacht,boat rental,hire a boat,yacht charter,charter a boat,boat charters,boating vacation,boating holidays,yachts and yachting\" />";

	   echo $title.$decr.$kwd;


	}
    
    
    //function for convering result values from json format
    private function minus_procent($str, $proc)
    {
        
        $val = round( str_replace(',', '.', $str), 2 );
        $diff = ($proc * $val) / 100;
        return round( ($val - $diff) , 2);
    }
    
    
  
    
    
    
    
    public function request_destinations()
    {
        include 'Helper_API.php';
        # Get Broker destinations ID's;
        $h = new Helper;

        $mts_query=array();
        $mts_query['lg']=0;
        $mts_query['refagt']='wxft6043';
        $query = http_build_query($mts_query);

        $data = $h->XMLtoarray( $h->byGET_( 'http://client.sednasystem.com/API/GetDestinations2.asp?'.$query )  );

        # Loop trough data and build autocomplete src.;

        $dest = array();

        foreach( $data['destination'] as $key => $value )
        {
            $dest[] = array(

		  'label'=>$h->JSON_char($value['@attributes']['name']),

		  'value'=>$h->JSON_char($value['@attributes']['name']),

		  'id'=>'d'.$value['@attributes']['id_dest']);

	       if( isset($value['country']) && is_array($value['country']) && isset($value['country'][0]) )
            {

		      foreach($value['country'] as $skey => $svalue)
                {

			     $dest[] = array(

				'label'=>$h->JSON_char($svalue['@attributes']['name']),

				'value'=>$h->JSON_char($svalue['@attributes']['name']),

				'id'=>'c'.$svalue['@attributes']['id_country']);
                }

	       }
            elseif( isset($value['country']) && is_array($value['country']) && isset($value['country']['@attributes']) )
            {

		          $dest[] = array('label'=>$h->JSON_char($value['country']['@attributes']['name']),

				        'value'=>$h->JSON_char($value['country']['@attributes']['name']),

				    'id'=>'c'.$value['country']['@attributes']['id_country']);
            }
        }
         //header('Content-type: application/json');

        //print_r(json_encode($dest));

        //exit;
        return $dest;        
    }
    
    
    
    
    public function request_operators()
    {

        $mts_query=array();
        $mts_query['lg']=0;
        $mts_query['refagt']='wxft6043';
        $query = http_build_query($mts_query);

        $oper = simplexml_load_string(file_get_contents('http://client.sednasystem.com/API/getOperators.asp?'.$query));


        return $oper;        
    }

    /*****************************functions for dysplaying images of boats*********************/
    //showing the photos of boat
    public function carusel_boat()
    {
        
        echo '<div class="panel panel-default">'.
                '<div class="panel-heading">'.
                '<h3 class="panel-title" id="overview" >Overview</h3>'.
                '</div>'.
                '<div class="panel-body">';
        $images = $this->boat_details['picts']['pict'];
        
        if(is_array($images))
        {

	       echo '<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">'.
    			  '<ol class="carousel-indicators">';

			  foreach($images as $m =>$g)
              {

			    echo '<li data-target="#carousel-example-generic" data-slide-to="'.$m.'" class="';
                if ($m == 0)
                {
                    echo 'active';
                }
                else
                {
                    echo '';
                } 
                echo '"></li>';
            }

            echo '</ol>';

			echo '<div class="carousel-inner">';

			foreach($images as $i => $mg)
            {
			     echo '<div class="item ';
                 if ($i == 0)
                 {
                    echo 'active';
                 }
                 else
                 {
                    echo '';
                 } 
                 
                 echo '">';
                 
                 echo '<div class="mts_slider_img" style="background-image: url(';
                 echo $mg['@attributes']['link'];
                 echo '); " ></div>';
                 echo '</div>';
            }
            
            echo '</div>';

		      echo '<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">';
              echo '<span class="glyphicon glyphicon-chevron-left"></span>';
              echo '</a>';

			  echo '<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">';

			  echo '<span class="glyphicon glyphicon-chevron-right"></span>';

			  echo '</a></div>';

        }
        
        //detailed description about boat appearance
        echo '<hr /><p>'.$this->descriptions('main',$this->sedna_structure).'</p><hr />';
        echo '<p>'.$this->descriptions('reservation',$this->sedna_structure).'</p>';
        echo '<p class="other_info" >'.$this->descriptions('cabins',$this->sedna_structure).'</p>';
           echo '<p class="other_info" >Fleet operator: '.$this->boat_details['operator']['oper_name'].'</p>';   

        
        echo '</div></div>';

    }
    
    //showing the photos of boat
    public function carusel_boat2()
    {
        $html ='';
        $html .='<div class="panel panel-default">'.
                '<div class="panel-heading">'.
                '<h3 class="panel-title" id="overview" >Overview</h3>'.
                '</div>'.
                '<div class="panel-body">';
        $images = $this->boat_details['picts']['pict'];
        
        if(is_array($images))
        {

	       $html .='<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">'.
    			  '<ol class="carousel-indicators">';

			  foreach($images as $m =>$g)
              {

			    $html .='<li data-target="#carousel-example-generic" data-slide-to="'.$m.'" class="';
                if ($m == 0)
                {
                    $html .='active';
                }
                else
                {
                    $html .='';
                } 
                $html .='"></li>';
            }

            $html .='</ol>';

			$html .='<div class="carousel-inner">';

			foreach($images as $i => $mg)
            {
			     $html .='<div class="item ';
                 if ($i == 0)
                 {
                    $html .='active';
                 }
                 else
                 {
                    $html .='';
                 } 
                 
                 $html .='">';
                 
                 $html .='<div class="mts_slider_img" style="background-image: url(';
                 $html .=$mg['@attributes']['link'];
                 $html .='); " ></div>';
                 $html .='</div>';
            }
            
            $html .='</div>';

		    $html .='<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">';
             $html .='<span class="glyphicon glyphicon-chevron-left"></span>';
              $html .='</a>';

			  $html .='<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">';

			  $html .='<span class="glyphicon glyphicon-chevron-right"></span>';

			  $html .='</a></div>';

        }
        
        //detailed description about boat appearance
        $html .='<hr /><p>'.$this->descriptions('main',$this->sedna_structure).'</p><hr />';
        $html .='<p>'.$this->descriptions('reservation',$this->sedna_structure).'</p>';
        $html .='<p class="other_info" >'.$this->descriptions('cabins',$this->sedna_structure).'</p>';
        $html .='<p class="other_info" >Fleet operator: '.$this->boat_details['operator']['oper_name'].'</p>';   

        
        $html .='</div></div>';
        
        return $html;

    }
    
 
    
    
    /******************************function for extracting all prices**********************/
    //detailed information about prices
    public function boat_body()
    {       
       
      echo '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="prices">Prices</h3>'.
            '</div><div class="panel-body">';

       echo '<table class="table table-hover" >'.
                '<thead><tr><td>Price per Week</td>'.
                '<td>From</td><td>To</td></tr>'.
                '</thead><tbody>';

       foreach($this->boat_details['homeport']['prices']['price'] as $key=>$price)
       {
            echo "<tr><td>";
            if ($this->boat_details['operator']['rate_cur']>0)
            {
                $amount=intval($price['@attributes']['amount']*$this->boat_details['operator']['rate_cur']);
                echo '&euro;'.$amount; 
            }
            else
            {
                if ($this->boat_details['operator']['def_cur']!='EUR')
                {
                    echo '$'.$price['@attributes']['amount'];     
                }
                else
                {
                    echo '&euro;'.$price['@attributes']['amount'];     
                }
                
            }
            echo '</td><td>'.$price['@attributes']['datestart']."</td><td>";
            echo  $price['@attributes']['dateend'].'</td></tr>';
       }

        echo '</tbody></table>';
       echo '</div></div>';
    }
    
    
    
    //detailed information about prices
    public function boat_body2()
    {       
       $html='';
        $html .='<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="prices">Prices</h3>'.
            '</div><div class="panel-body">';

       $html .='<table class="table table-hover" >'.
                '<thead><tr><td>Price per Week</td>'.
                '<td>From</td><td>To</td></tr>'.
                '</thead><tbody>';

       
       
       if(isset($this->boat_details['homeport']['prices']['price']['@attributes']))
       {
            $html .="<tr><td>";
            if ($this->boat_details['operator']['rate_cur']>0)
            {
                $amount=intval($this->boat_details['homeport']['prices']['price']['@attributes']['amount']*$this->boat_details['operator']['rate_cur']);
                $html .='&euro;'.$amount; 
            }
            else
            {
                if ($this->boat_details['operator']['def_cur']!='EUR')
                {
                    $html .= '$'.$this->boat_details['homeport']['prices']['price']['@attributes']['amount'];     
                }
                else
                {
                    $html .= '&euro;'.$this->boat_details['homeport']['prices']['price']['@attributes']['amount'];     
                }
                
            }
            $html .='</td><td>'.$this->boat_details['homeport']['prices']['price']['@attributes']['datestart']."</td><td>";
            $html .=$this->boat_details['homeport']['prices']['price']['@attributes']['dateend'].'</td></tr>';
       }
       else
       {
            foreach($this->boat_details['homeport']['prices']['price'] as $key=>$price)
            {
            $html .="<tr><td>";
            if ($this->boat_details['operator']['rate_cur']>0)
            {
                $amount=intval($price['@attributes']['amount']*$this->boat_details['operator']['rate_cur']);
                $html .='&euro;'.$amount; 
            }
            else
            {
                if ($this->boat_details['operator']['def_cur']!='EUR')
                {
                    $html .= '$'.$price['@attributes']['amount'];     
                }
                else
                {
                    $html .= '&euro;'.$price['@attributes']['amount'];     
                }
                
            }
            $html .='</td><td>'.$price['@attributes']['datestart']."</td><td>";
            $html .=$price['@attributes']['dateend'].'</td></tr>';
            }
       }

        $html .='</tbody></table>';
       $html .='</div></div>';
       
       return $html;
    }
    
    
     //detailed information about prices from booker database
    public function booker_price()
    {
           
      $html= '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="prices" >Prices</h3>'.
            '</div><div class="panel-body">';
 	   if(count($this->show_booker_boats[0]['Prices'])>0)
       {
         $html .= '<table class="table table-hover" >'.
                '<thead><tr><td>Price per Week</td>'.
                '<td>From</td><td>To</td></tr>'.
                '</thead><tbody>';
                
         foreach($this->show_booker_boats[0]['Prices'] as $kss=>$vsss)
         {
            $html .= "<tr><td>".$vsss['Price']." ".$vsss['Currency']."</td>";
            $html .="<td>".$vsss['DateFrom']."</td>";
            $html .="<td>".$vsss['DateTo']."</td></tr>";

        }
        $html .= '</tbody></table>';
       }
       $html .= '</div></div>';
       
       return $html;
    }
    
    
    //detailed information about extra consts
    public function extra_cost($api_id,$country_code=0)
    {
        $html='';
        //additional query for extra cost
        $url_costs='http://client.sednasystem.com/API/getExtras2.asp?id_boat='.$api_id.'&refagt=wxft6043';
        $res_costs=json_decode(json_encode(simplexml_load_string(file_get_contents($url_costs))),true);
        $sedna_structure=array();
        
        
        $sort_extra=array();
        $exist_name=array();
     
        if($country_code>0)
        {
        foreach ($res_costs['extra'] as $row=>$price)
        {
            if($price['@attributes']['price']>0)
            {
                    if ($price['@attributes']['id_country']==$country_code || $price['@attributes']['id_country']==0)
                    {

               
            $sort_extra[$price['@attributes']['id_opt_bt']]=$price['@attributes']['mand'];
            $sedna_structure[$price['@attributes']['id_opt_bt']]['name']=$price['@attributes']['name'];

                $sedna_structure[$price['@attributes']['id_opt_bt']]['price']=$price['@attributes']['price'];
            
            $sedna_structure[$price['@attributes']['id_opt_bt']]['per']=$price['@attributes']['per'].'/'.$price['@attributes']['per2'];
            $sedna_structure[$price['@attributes']['id_opt_bt']]['quantity']=$price['@attributes']['quantity'];
                }

            }
        }
        ksort($sort_extra);
        $structure['extra_mand']=array();
        
        $structure['extra_add']=array();
        foreach($sort_extra as $key=>$value)
        {
            if ($value==1)
            {
                $structure['extra_mand'][$key]=$sedna_structure[$key];
            }
            else
            {
                $structure['extra_add'][$key]=$sedna_structure[$key];  
            }
          
        }
       
      $html .='<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="extra-costs" >Extra Costs</h3>'.
            '</div><div class="panel-body">';
            
      if(count($structure['extra_mand']) >0)
      {
            $html .='<p><strong>MANDATORY EXTRAS</strong></p>';
            $html .='<table class="table table-hover" ><thead>'.
                     '<tr><td>Service</td><td>Quantity</td><td>Unit</td><td>Price</td></tr>'.
                     '</thead><tbody>';

            foreach($structure['extra_mand'] as $key=> $price)
            {
                $html .='<tr><td>'.$price['name'].'</td>';
                $html .='<td>'.$price['quantity'].'</td>';
                $html .= '<td>'.$price['per'].'</td>';
                $html .= '<td>'.$price['price'].'</td>';
                $html .='</tr>';
            }
            $html .='</tbody></table>';
        }
        
        
        if(count($structure['extra_add']) >0)
        {
            $html .= '<p><strong>ADDITIONAL EXTRAS</strong></p>';
            $html .= '<table class="table table-hover" ><thead>'.
                     '<tr><td>Service</td><td>Quantity</td><td>Unit</td><td>Price</td></tr>'.
                     '</thead><tbody>';

            foreach($structure['extra_add'] as $key=> $price )
            {
                $html .= '<tr><td>'.$price['name'].'</td>';
                $html .= '<td>'.$price['quantity'].'</td>';
                $html .= '<td>'.$price['per'].'</td>';
                $html .= '<td>'.$price['price'].'</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        }

        
        
        $html .= '</div></div>';
        }
        
        return $html;
            
    }
    
    
    
    
    //detailed information about extra consts
    public function extra_cost2($api_id)
    {
        $html ='';
        //additional query for extra cost
        $url_costs='http://client.sednasystem.com/API/getExtras2.asp?id_boat='.$api_id.'&refagt=wxft6043';
        $res_costs=json_decode(json_encode(simplexml_load_string(file_get_contents($url_costs))),true);
        $this->sedna_structure['extra']=array();
        $id_country=$this->boat_details['homeport']['@attributes']['id_country'];
        $sort_extra=array();
        $exist_name=array();
        //function to know the miimum extra price
        //at first we need to count total numbers of prices
        //then if the number is more then 100 we will filter the same costs
        //find the most similar names
        //find numbers and words
        $second_price=array('more pax','weeks','5','6','7','8','9','10');
        foreach ($res_costs['extra'] as $row=>$price)
        {
            if($price['@attributes']['price']>0)
            {
                    if ($price['@attributes']['id_country']==$id_country || $price['@attributes']['id_country']==0)
                    {
                            if(!in_array($price['@attributes']['name'],$exist_name))
                            {
                                $exist_name[] =$price['@attributes']['name'];
                                $del=false;
                                //foreach ($second_price as $word)
                                //{
                                 //   if (stripos($price['@attributes']['name'],$word)!==false)
                                 //   {
                                 //       $del=true;
                                 //   }
                                //}
                                if ($del==false)
                                {
                                        $sort_extra[$price['@attributes']['id_opt_bt']]=$price['@attributes']['mand'];
                                        $this->sedna_structure['extra'][$price['@attributes']['id_opt_bt']]['name']=$price['@attributes']['name'];
                                        if ($this->boat_details['operator']['def_cur']!="EUR" && $this->boat_details['operator']['rate_cur']>0)
                                        {
                                            $this->sedna_structure['extra'][$price['@attributes']['id_opt_bt']]['price']=
                                                        intval($price['@attributes']['price']*$this->boat_details['operator']['rate_cur']);
                                        }
                                        elseif($this->boat_details['operator']['rate_cur']==0)
                                        {
                                            $this->sedna_structure['extra'][$price['@attributes']['id_opt_bt']]['price']=$price['@attributes']['price'];
                                        }
                                        $this->sedna_structure['extra'][$price['@attributes']['id_opt_bt']]['per']=$price['@attributes']['per'].'/'.$price['@attributes']['per2'];
                                        $this->sedna_structure['extra'][$price['@attributes']['id_opt_bt']]['quantity']=$price['@attributes']['quantity'];
                                }
                        }
            }
            }
        }
        ksort($sort_extra);
        $this->sedna_structure['extra_mand']=array();

        $this->sedna_structure['extra_add']=array();
        foreach($sort_extra as $key=>$value)
        {
            if ($value==1)
            {
                $this->sedna_structure['extra_mand'][$key]=$this->sedna_structure['extra'][$key];
            }
            else
            {
                $this->sedna_structure['extra_add'][$key]=$this->sedna_structure['extra'][$key];  
            }
          
        }
       
      $html .='<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="extra-costs" >Extra Costs</h3>'.
            '</div><div class="panel-body">';
          
      if(count($this->sedna_structure['extra_mand']) >0)
      {
            $html .='<p><strong>MANDATORY EXTRAS</strong></p>';
            $html .='<table class="table table-hover" ><thead>'.
                     '<tr><td>Service</td><td>Quantity</td><td>Unit</td><td>Price</td></tr>'.
                     '</thead><tbody>';

            foreach($this->sedna_structure['extra_mand'] as $key=> $price)
            {
                $html .='<tr><td>'.$price['name'].'</td>';
                $html .='<td>'.$price['quantity'].'</td>';
                $html .='<td>'.$price['per'].'</td>';
                $html .='<td>'.$this->boat_details['operator']['symb'].$price['price'].'</td>';
                $html .='</tr>';
            }
            $html .='</tbody></table>';
        }
        
        
        if(count($this->sedna_structure['extra_add']) >0)
        {
            $html .='<p><strong>ADDITIONAL EXTRAS</strong></p>';
            $html .='<table class="table table-hover" ><thead>'.
                     '<tr><td>Service</td><td>Quantity</td><td>Unit</td><td>Price</td></tr>'.
                     '</thead><tbody>';

            foreach($this->sedna_structure['extra_add'] as $key=> $price )
            {
                $html .='<tr><td>'.$price['name'].'</td>';
                $html .='<td>'.$price['quantity'].'</td>';
                $html .='<td>'.$price['per'].'</td>';
                $html .='<td>'.$this->boat_details['operator']['symb'].$price['price'].'</td>';
                $html .='</tr>';
            }
            $html .='</tbody></table>';
        }

        
        
        $html .='</div></div>';
        
        return $html;
            
    }
    
    
    
    //detailed information about extra costs for Booker boat
    public function booker_extra()
    {
        
         echo '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="extra-costs" >Extra Costs</h3>'.
            '</div><div class="panel-body">';
            
      if(count($this->show_booker_boats[0]['Extras'])>0)
        {
              echo '<table class="table table-hover" ><thead>'.
                     '<tr><td>Service</td><td>Price</td>'.
                     '</tr>'.
                    '</thead><tbody>';
                 
                
				foreach($this->show_booker_boats[0]['Extras'] as $k=> $v)
                {
                        if (!empty($v['Name']))
                        {
                                if (trim($v['Currency'])=='EUR')
                                {
                                    $curr='&euro;';    
                                }
                                else
                                {
                                    $curr='$';
                                }
		                  echo '<tr><td>'.$v['Name'].'</td>';
                            echo '<td>'.$curr.$v['Price'].'</td>';
                            echo '</tr>';
                        }
                }
                echo '</tbody></table>';
        }
        else
        {
                echo "<p>There are no Extra Costs</p>";
        }

        echo '</div></div>';
            
    }
    
    /*************************************function about boats equipments********************/
    
    //detailed information about extra characteristics
    public function boat_detinfo($api_id)
    {
            $url_chars='http://client.sednasystem.com/API/GetCharacteristics2.asp?id_boat='.$api_id.'&refagt=wxft6043';
            $res_chars=json_decode(json_encode(simplexml_load_string(file_get_contents($url_chars))),true);
            
        
         echo '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="details" >Details</h3>'.
            '</div><div class="panel-body">';
         $plans = json_decode($this->boat_details['plans']);
		if( is_array($plans) )
        {
		  echo '<p>Yacht Layout</p>';
          foreach($plans as $p =>$lans)
          {
             echo '<img class="mts_plan" src="'.$lans.'" alt="boat plan" />';
          }
		}
         echo '<br /><br />'.
                        '<p>Charter Yacht Equipment</p>';

        $temp_char=array();                
        foreach ($res_chars['characteristic_topic'] as $key=>$charact)
        {
           foreach ($charact['characteristic'] as $key2=>$char) 
            {

                if ($key2==='@attributes' && (stripos('no',$char['quantity'])===false))
                {
                    if ((trim(strtolower($char['name']))!=='engine') &&
                        (trim(strtolower($char['name']))!=='engines') &&
                        (trim(strtolower($char['name']))!=='draft') &&
                        (trim(strtolower($char['name']))!=='water capacity') &&
                        (trim(strtolower($char['name']))!=='maximum passengers') &&
                        (trim(strtolower($char['name']))!=='beam'))
                    {
                        if((trim(strtolower($char['name']))!=='fuel capacity') && 
                        (trim(strtolower($char['name']))!=='fuel tank'))
                    {
                  $temp_char[]= $char;
                  }
                  }   
                }
                else
                {
                       
                if (stripos('no',$char['@attributes']['quantity'])===false)
                {
                        if ((trim(strtolower($char['@attributes']['name']))!=='engine')
                            && (trim(strtolower($char['@attributes']['name']))!=='engines') &&
                            (trim(strtolower($char['@attributes']['name']))!=='draft') &&
                            (trim(strtolower($char['@attributes']['name']))!=='water capacity') &&
                            (trim(strtolower($char['@attributes']['name']))!=='maximum passengers') && 
                            (trim(strtolower($char['@attributes']['name']))!=='beam'))
                          {
                            if ((trim(strtolower($char['@attributes']['name']))!=='fuel capacity') && 
                             (trim(strtolower($char['@attributes']['name']))!=='fuel tank'))
                    {
                   $temp_char[]=$char['@attributes'];
                   } 
                }
                }
            }  
            }
        }
            
                  $col=0;
            $html_table = '<table class="table table-hover table-bordered"><tr>';
            foreach ($temp_char as $key=>$value)
            {
                if ($col<3)
                {
                    $html_table .= '<td><strong>' .$value['name']. '</strong> '
                                    .$value['quantity'].'</td>';  
                    $col++;
                }
                else
                {
                    $html_table .='</tr><tr>'; 
                    $col=0;
                }
            }
            for($i=$col;$i<3;$i++)
            {
                $html_table .='<td></td>';
            }
            
      
            $html_table .= '</tr></table>'; 
            echo $html_table;  
            
        

        echo '</div></div>';
    }
    
    
    
     //detailed information about extra characteristics
    public function boat_detinfo2($api_id)
    {
            $url_chars='http://client.sednasystem.com/API/GetCharacteristics2.asp?id_boat='.$api_id.'&refagt=wxft6043';
            $res_chars=json_decode(json_encode(simplexml_load_string(file_get_contents($url_chars))),true);
          $html='';  
        
         $html .='<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="details" >Details</h3>'.
            '</div><div class="panel-body">';
         $plans = $this->boat_details['plans'];

		if(isset($plans['plan']['@attributes']))
        {
		  $html .='<p>Yacht Layout</p>';
          $html .='<img class="mts_plan" src="'.$plans['plan']['@attributes']['link'].'" alt="boat plan" />';
          
		}
        else
        {
            $html .='<p>Yacht Layout</p>';
            foreach($plans['plan'] as $p =>$lans)
            {
                $html .='<img class="mts_plan" src="'.$lans['@attributes']['link'].'" alt="boat plan" />';
            }
        }
         $html .='<br /><br />'.
                        '<p>Charter Yacht Equipment</p>';

        $temp_char=array();                
        foreach ($res_chars['characteristic_topic'] as $key=>$charact)
        {
           foreach ($charact['characteristic'] as $key2=>$char) 
            {

                if ($key2==='@attributes' && (stripos('no',$char['quantity'])===false))
                {
                    if ((trim(strtolower($char['name']))!=='engine') &&
                        (trim(strtolower($char['name']))!=='engines') &&
                        (trim(strtolower($char['name']))!=='draft') &&
                        (trim(strtolower($char['name']))!=='water capacity') &&
                        (trim(strtolower($char['name']))!=='maximum passengers') &&
                        (trim(strtolower($char['name']))!=='beam'))
                    {
                        if((trim(strtolower($char['name']))!=='fuel capacity') && 
                        (trim(strtolower($char['name']))!=='fuel tank'))
                    {
                  $temp_char[]= $char;
                  }
                  }   
                }
                else
                {
                       
                if (stripos('no',$char['@attributes']['quantity'])===false)
                {
                        if ((trim(strtolower($char['@attributes']['name']))!=='engine')
                            && (trim(strtolower($char['@attributes']['name']))!=='engines') &&
                            (trim(strtolower($char['@attributes']['name']))!=='draft') &&
                            (trim(strtolower($char['@attributes']['name']))!=='water capacity') &&
                            (trim(strtolower($char['@attributes']['name']))!=='maximum passengers') && 
                            (trim(strtolower($char['@attributes']['name']))!=='beam'))
                          {
                            if ((trim(strtolower($char['@attributes']['name']))!=='fuel capacity') && 
                             (trim(strtolower($char['@attributes']['name']))!=='fuel tank'))
                    {
                   $temp_char[]=$char['@attributes'];
                   } 
                }
                }
            }  
            }
        }
            
                  $col=0;
            $html_table = '<table class="table table-hover table-bordered"><tr>';
            foreach ($temp_char as $key=>$value)
            {
                if ($col<3)
                {
                    $html_table .= '<td><strong>' .$value['name']. '</strong> '
                                    .$value['quantity'].'</td>';  
                    $col++;
                }
                else
                {
                    $html_table .='</tr><tr>'; 
                    $col=0;
                }
            }
            for($i=$col;$i<3;$i++)
            {
                $html_table .='<td></td>';
            }
            
      
            $html_table .= '</tr></table>'; 
            $html .=$html_table;  
            
        

        $html .='</div></div>';
        
        return $html;
    }
    
    
    
    
    public function boat_engine($api_id)
    {
            $url_chars='http://client.sednasystem.com/API/GetCharacteristics2.asp?id_boat='.$api_id.'&refagt=wxft6043';
            $res_chars=json_decode(json_encode(simplexml_load_string(file_get_contents($url_chars))),true);
            $engine=array();
            $engine['draft']='';
            $engine['engine']='';  
        foreach ($res_chars['characteristic_topic'] as $key=>$charact)
        {
                    foreach($charact['characteristic'] as $key=>$value)
                    {
                            if ($key==='@attributes')
                            {
                                
                                if (trim(strtolower($value['name']))==='draft')
                                {
                                if (stripos($value['quantity'],",")!==false)
                                {
                                    $engine['draft']=str_replace(",",".",$value['quantity']);
                                }
                                else
                                {
                                    $engine['draft']=$value['quantity'];
                                }
                           
                            }
                            if (trim(strtolower($value['name']))==='engines' || 
                            trim(strtolower($value['name']))==='engine' )
                            {
                                $engine['engine']=$value['quantity'];
                            }
                            }
                            else
                            {

                        if (trim(strtolower($value['@attributes']['name']))==='draft')
                        {
                                $engine['draft']=$value['@attributes']['quantity'];
                            //if (stripos($value['@attributes']['quantity'],",")!==false)
                            //{
                            //    $engine['draft']=str_replace(",",".",$value['@attributes']['quantity']);
                            //}

                           
                        }
                        if (trim(strtolower($value['@attributes']['name']))==='engines' || 
                        trim(strtolower($value['@attributes']['name']))==='engine' )
                        {
                           $engine['engine']=$value['@attributes']['quantity'];
                        }
                        }
                    }

        }

        return $engine;
    }
    
    //detailed information about extra characteristics
    public function booker_detinfo()
    {
         echo '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="details" >Details</h3>'.
            '</div><div class="panel-body">';
         /*$plans = json_decode($this->boat_details['plans']);
		if( is_array($plans) )
        {
		  echo '<p>Yacht Layout</p>';
          foreach($plans as $p =>$lans)
          {
             echo '<img class="mts_plan" src="'.$lans.'" alt="boat plan" />';
          }
		}*/
        
        if(count($this->show_booker_boats[0]['Equipments'])>0)
        {
                    echo '<br /><br />'.
                        '<p>Charter Yacht Equipment</p>';
                    $nr_elm = count($this->show_booker_boats[0]['Equipments']);      
				    $html_table = '<table class="table table-hover table-bordered"><tr>';
				    $nr_col = 3; 
     			    if ($nr_elm > 0) 
                     {
        			  // Traverse the array with FOR
                	  for($i=0; $i<$nr_elm; $i++) 
                      {
                        $html_table .= '<td><strong>' .$this->show_booker_boats[0]['Equipments'][$i]['Name']. '</strong> '.
                                        $this->show_booker_boats[0]['Equipments'][$i]['Quantity'].'</td>';       // adds the value in column in table
                         // If the number of columns is completed for a row (rest of division of ($i + 1) to $nr_col is 0)
                	    // Closes the current row, and begins another row
                        $col_to_add = ($i+1) % $nr_col;
                	    if($col_to_add == 0) { $html_table .= '</tr><tr>'; }
            		  }
                   if($col_to_add != 0) $html_table .= '<td colspan="'. ($nr_col - $col_to_add). '">&nbsp;</td>'; 
					} 
                   $html_table .= '</tr></table>';  
                   $html_table = str_replace('<tr></tr>', '', $html_table);  
                    echo $html_table; 
		 }
         else
         {
            
	    	echo "<p>Boat does not have more Details</p>";
         }
        echo '</div></div>';
    }
    
    
    /***************************************functions about boat locations **************************/
    //boat map with location from Google API
    public function boat_map($place)
    {
	
        echo '<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="map" >Map</h3>'.
            '</div><div class="panel-body">';
  
          echo '<iframe style="width: 100%;height: 300px;"  src="https://www.google.com/maps/embed/v1/place?q='.
                urlencode($place).'&key=AIzaSyDOZM0TA4Qhki2fM2MseW5Bbh24AqX-XQ4"></iframe>';        
  
        echo '</div></div>';
    }
    
    
    public function boat_map2($place)
    {
	   $html='';
       $html .='<div class="panel panel-default">'.
            '<div class="panel-heading">'.
            '<h3 class="panel-title" id="map" >Map</h3>'.
            '</div><div class="panel-body">';
  
        $html .='<iframe style="width: 100%;height: 300px;"  src="https://www.google.com/maps/embed/v1/place?q='.
                urlencode($place).'&key=AIzaSyDOZM0TA4Qhki2fM2MseW5Bbh24AqX-XQ4"></iframe>';        
  
        $html .='</div></div>';
        
        return $html;
    }
    
    
    //boat map with location from Google API
    public function boat_booker_map()
    {
        $html='';
		if(!empty( $this->show_booker_boats[0]['BaseName']))
        {
            
            $html .='<div class="panel panel-default">'.
                    '<div class="panel-heading">'.
                    '<h3 class="panel-title" id="map" >Map</h3>'.
                    '</div><div class="panel-body">';

           $html .='<iframe style="width: 100%;height: 300px;"  src="https://www.google.com/maps/embed/v1/place?q='.
                    urlencode($this->show_booker_boats[0]['BaseName']).'&key=AIzaSyDOZM0TA4Qhki2fM2MseW5Bbh24AqX-XQ4"></iframe>';  
           $html .= '</div></div>';
        }
        return $html;
    }
    
    
    
    
    /***************************************8functions for booking boat*********************************/
    //booking form for sending request to data base
    public function book_form()
    {
        echo '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" '.
                'aria-hidden="true"><div class="modal-dialog"><div class="modal-content">';
        echo '<form class="form-horizontal" role="form" style="display: block;"><div class="modal-header">';
        echo '<button type="button" id="clicktrigclose" class="close" data-dismiss="modal">'.
                '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'.
                '<h4 class="modal-title" id="myModalLabel">Please complete this form and give us more informations 
                about you. We will contact you after you will submit the request.</h4>';
        echo '</div><div class="modal-body">';
        echo '<div id="processingwait" class="in_progress" '.
                'style="display: none;width: 100%;height: 100%;position: absolute;left: 0;top: 0;background-color: #fff!important;" >'.
                '<h3 style="text-align: center;" >Please wait processing..</h3></div>'.
                '<div class="wrapp_opac" ><div class="row" >';
        echo '<div class="form-group col-lg-6">'.
			 '<label for="inputEmail" class="col-md-4 control-label">Email</label>'.
             '<div class="col-md-8"><input type="email" class="form-control squarebrd" id="inputEmail"  '.
             'autocomplete="off"  ></div></div>';
        echo '<div class="form-group col-lg-6">'.
                '<label for="inputPhone" class="col-md-4 control-label">Phone number</label>'.
                '<div class="col-md-8"><input type="tel" class="form-control squarebrd" id="inputPhone" '.
                'autocomplete="off" ></div></div></div>';
        echo '<div class="row" ><div class="form-group col-lg-6">'.
                '<label for="inputFirstName" class="col-md-4 control-label">First Name</label>'.
                '<div class="col-md-8">'.
                '<input type="text" class="form-control squarebrd" id="inputFirstName"  autocomplete="off" >'.
                '</div></div><div class="form-group col-lg-6">'.
                '<label for="inputLastName" class="col-md-4 control-label">Last Name</label>'.
                '<div class="col-md-8"><input type="text" class="form-control squarebrd" id="inputLastName"  '.
                'autocomplete="off" ></div></div></div>';

           echo '<div class="row" >'.
                '<span class="help-block" style="text-align: center;" >Please select departure and arrival '.
                'locations.</span>';

            echo '<div class="form-group col-sm-6">'.
                    '<label for="input_sel_dep" class="col-md-4 control-label ">Departure </label>'.
                    '<select id="input_sel_dep" class="col-md-8 form-control squarebrd" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;" >';
            echo '<option value='.$this->last_search['date_from'].'>'.$this->last_search['date_from'].'</option>';
            echo '</select></div><div class="form-group col-sm-6">'.
                    '<label for="input_sel_arv" class="col-md-4 control-label ">Arrival </label>'.
                    '<select id="input_sel_arv" class="col-md-8 form-control squarebrd" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;"  >';
            echo '<option value='.$this->last_search['date_to'].'>'.$this->last_search['date_to'].'</option>';
            echo '</select></div></div>';

        
        echo ' <div class="row" >';
            echo '<div class="form-group col-sm-6">'.
                    '<label for="inputhold48" class="col-md-10 control-label radio-inline">Hold boat for 48hrs</label>'.
                    '<input type="radio" name="book_opt" class="checka_optsa col-md-2 squarebrd form-control radioopt" '.
                    'id="inputhold48"  checked="checked" ></div>';
        echo ' <div class="form-group col-sm-6">'.
                '<label for="inputsavesearch" class="col-md-10 control-label radio-inline">Save this search </label>'.
                '<input type="radio" '.
                ' name="book_opt" class="checka_optsa col-md-2 form-control squarebrd radioopt" id="inputsavesearch"  >';
        echo ' </div></div>';
        
        echo '<div class="row" >'.
                '<div class="form-group col-sm-6">'.
                '<label for="input_sel_pax" class="control-label">Please select the number of passengers </label>'.
                '</div><div class="form-group col-sm-6" >'.
                '<select id="input_sel_pax" class=" form-control squarebrd" '.
                'style="min-height: 40px;min-width: 100px;max-width: 179px;margin-left: 67px;"  >';
        for( $pa = 1; $pa <= $this->sedna_structure['total_people']; $pa++)
        {
           echo '<option value="'.$pa.'" >'.$pa;
           if($pa == 1)
           echo 'passenger';
           else
           echo 'passengers';
           echo '</option>';
        }
        
        echo '</select></div></div>';
        echo '<div class="row" ><p style="display: none;text-align: center;" id="show_error"  class="error"></p>'.
                '<span class="help-block warring" style="text-align: center;">Please note that all the fields are required.</span>'.
                '</div>';
                
        echo '</div><div class="modal-footer">'.
                '<button type="button" class="btn btn-primary squarebrd" id="mts_book_boat_submit" >Save</button>'.
                '<button type="button" class="btn btn-default squarebrd" id="close_modal" data-dismiss="modal">Close</button>'.
                '</div>';
                
        echo '</div></form><div class="last_msg" style="display: none;"><div class="head">'.
            '</div><div class="content"></div>'.
            '<div class="footer"><button type="button" class="btn btn-default squarebrd" id="close_modal" '. 
            'data-dismiss="modal">Close</button></div></div></div></div></div>';
    }
    
    
    
    
    //booking form for sending request to data base
    public function book_form2()
    {
        $html='';
        $html .='<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" '.
                'aria-hidden="true"><div class="modal-dialog"><div class="modal-content">';
        $html .='<form class="form-horizontal" role="form" style="display: block;"><div class="modal-header">';
        $html .='<button type="button" id="clicktrigclose" class="close" data-dismiss="modal">'.
                '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'.
                '<h4 class="modal-title" id="myModalLabel">Please complete this form and give us more informations 
                about you. We will contact you after you will submit the request.</h4>';
        $html .='</div><div class="modal-body">';
        $html .='<div id="processingwait" class="in_progress" '.
                'style="display: none;width: 100%;height: 100%;position: absolute;left: 0;top: 0;background-color: #fff!important;" >'.
                '<h3 style="text-align: center;" >Please wait processing..</h3></div>'.
                '<div class="wrapp_opac" ><div class="row" >';
        $html .='<div class="form-group col-lg-6">'.
			 '<label for="inputEmail" class="col-md-4 control-label">Email</label>'.
             '<div class="col-md-8"><input type="email" class="form-control squarebrd" id="inputEmail"  '.
             'autocomplete="off"  ></div></div>';
        $html .='<div class="form-group col-lg-6">'.
                '<label for="inputPhone" class="col-md-4 control-label">Phone number</label>'.
                '<div class="col-md-8"><input type="tel" class="form-control squarebrd" id="inputPhone" '.
                'autocomplete="off" ></div></div></div>';
        $html .='<div class="row" ><div class="form-group col-lg-6">'.
                '<label for="inputFirstName" class="col-md-4 control-label">First Name</label>'.
                '<div class="col-md-8">'.
                '<input type="text" class="form-control squarebrd" id="inputFirstName"  autocomplete="off" >'.
                '</div></div><div class="form-group col-lg-6">'.
                '<label for="inputLastName" class="col-md-4 control-label">Last Name</label>'.
                '<div class="col-md-8"><input type="text" class="form-control squarebrd" id="inputLastName"  '.
                'autocomplete="off" ></div></div></div>';

         $html .='<div class="row" >'.
                '<span class="help-block" style="text-align: center;" >Please select departure and arrival '.
                'locations.</span>';

          $html .='<div class="form-group col-sm-6">'.
                    '<label for="input_sel_dep" class="col-md-4 control-label ">Departure </label>'.
                    '<select id="input_sel_dep" class="col-md-8 form-control squarebrd" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;" >';
            $html .='<option value='.$this->last_search['date_from'].'>'.$this->last_search['date_from'].'</option>';
            $html .='</select></div><div class="form-group col-sm-6">'.
                    '<label for="input_sel_arv" class="col-md-4 control-label ">Arrival </label>'.
                    '<select id="input_sel_arv" class="col-md-8 form-control squarebrd" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;"  >';
            $html .='<option value='.$this->last_search['date_to'].'>'.$this->last_search['date_to'].'</option>';
            $html .='</select></div></div>';

        
        $html .=' <div class="row" >';
        $html .='<div class="form-group col-sm-6">'.
                    '<label for="inputhold48" class="col-md-10 control-label radio-inline">Hold boat for 48hrs</label>'.
                    '<input type="radio" name="book_opt" class="checka_optsa col-md-2 squarebrd form-control radioopt" '.
                    'id="inputhold48"  checked="checked" ></div>';
        $html .=' <div class="form-group col-sm-6">'.
                '<label for="inputsavesearch" class="col-md-10 control-label radio-inline">Save this search </label>'.
                '<input type="radio" '.
                ' name="book_opt" class="checka_optsa col-md-2 form-control squarebrd radioopt" id="inputsavesearch"  >';
        $html .=' </div></div>';
        
        $html .='<div class="row" >'.
                '<div class="form-group col-sm-6">'.
                '<label for="input_sel_pax" class="control-label">Please select the number of passengers </label>'.
                '</div><div class="form-group col-sm-6" >'.
                '<select id="input_sel_pax" class=" form-control squarebrd" '.
                'style="min-height: 40px;min-width: 100px;max-width: 179px;margin-left: 67px;"  >';
        for( $pa = 1; $pa <= $this->sedna_structure['total_people']; $pa++)
        {
           $html .='<option value="'.$pa.'" >'.$pa;
           if($pa == 1)
           $html .='passenger';
           else
           $html .='passengers';
           $html .='</option>';
        }
        
        $html .='</select></div></div>';
        $html .='<div class="row" ><p style="display: none;text-align: center;" id="show_error"  class="error"></p>'.
                '<span class="help-block warring" style="text-align: center;">Please note that all the fields are required.</span>'.
                '</div>';
                
        $html .='</div><div class="modal-footer">'.
                '<button type="button" class="btn btn-primary squarebrd" id="mts_book_boat_submit" >Save</button>'.
                '<button type="button" class="btn btn-default squarebrd" id="close_modal" data-dismiss="modal">Close</button>'.
                '</div>';
                
        $html .='</div></form><div class="last_msg" style="display: none;"><div class="head">'.
            '</div><div class="content"></div>'.
            '<div class="footer"><button type="button" class="btn btn-default squarebrd" id="close_modal" '. 
            'data-dismiss="modal">Close</button></div></div></div></div></div>';
            
        return $html;
    }
    
    
    
    
    
    //booking form for sending request to data base
    public function book_form_boker()
    {
        echo '<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" '.
                'aria-hidden="true"><div class="modal-dialog"><div class="modal-content">';
        echo '<form class="form-horizontal" role="form" style="display: block;"><div class="modal-header">';
        echo '<button type="button" id="clicktrigclose" class="close" data-dismiss="modal">'.
                '<span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'.
                '<h4 class="modal-title" id="myModalLabel">Please complete this form and give us more informations 
                about you. We will contact you after you will submit the request.</h4>';
        echo '</div><div class="modal-body">';
        echo '<div id="processingwait" class="in_progress" '.
                'style="display: none;width: 100%;height: 100%;position: absolute;left: 0;top: 0;background-color: #fff!important;" >'.
                '<h3 style="text-align: center;" >Please wait processing..</h3></div>'.
                '<div class="wrapp_opac" ><div class="row" >';
        echo '<div class="form-group col-lg-6">'.
			 '<label for="inputEmail" class="col-md-4 control-label">Email</label>'.
             '<div class="col-md-8"><input type="email" class="form-control squarebrd" id="inputEmail"  '.
             'autocomplete="off"  ></div></div>';
        echo '<div class="form-group col-lg-6">'.
                '<label for="inputPhone" class="col-md-4 control-label">Phone number</label>'.
                '<div class="col-md-8"><input type="tel" class="form-control squarebrd" id="inputPhone" '.
                'autocomplete="off" ></div></div></div>';
        echo '<div class="row" ><div class="form-group col-lg-6">'.
                '<label for="inputFirstName" class="col-md-4 control-label">First Name</label>'.
                '<div class="col-md-8">'.
                '<input type="text" class="form-control squarebrd" id="inputFirstName"  autocomplete="off" >'.
                '</div></div><div class="form-group col-lg-6">'.
                '<label for="inputLastName" class="col-md-4 control-label">Last Name</label>'.
                '<div class="col-md-8"><input type="text" class="form-control squarebrd" id="inputLastName"  '.
                'autocomplete="off" ></div></div></div>';
                
        if( !empty($this->show_booker_boats) )
        {
           echo '<div class="row" >'.
                '<span class="help-block" style="text-align: center;" >Please select departure and arrival '.
                'locations.</span>';
               echo '<div class="form-group col-sm-6">'.
                    '<label for="input_sel_dep" class="col-md-4 control-label ">Departure </label>'.
                    '<select id="input_sel_dep" class="col-md-8 form-control squarebrd" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;" >';
            echo '<option value='.$this->last_search['date_from'].'>'.$this->last_search['date_from'].'</option>';

            echo '</select></div><div class="form-group col-sm-6">'.
                    '<label for="input_sel_arv" class="col-md-4 control-label ">Arrival </label>'.
                    '<select id="input_sel_arv" class="col-md-8 form-control squarebrd" '.
                    'style="min-width: 100px!important;min-height: 40px;max-width: 140px;margin-left: 17px;"  >';
                 echo '<option value='.$this->last_search['date_to'].'>'.$this->last_search['date_to'].'</option>';
            echo '</select></div></div>';

 
        }
        
        echo ' <div class="row" >';
 
            echo '<div class="form-group col-sm-6">'.
                    '<label for="inputhold48" class="col-md-10 control-label radio-inline">Hold boat for 48hrs</label>'.
                    '<input type="radio" name="book_opt" class="checka_optsa col-md-2 squarebrd form-control radioopt" '.
                    'id="inputhold48"   ></div>';

        echo ' <div class="form-group col-sm-6">'.
                '<label for="inputsavesearch" class="col-md-10 control-label radio-inline">Save this search </label>'.
                '<input type="radio" ';

            echo 'checked="checked"';

        echo ' name="book_opt" class="checka_optsa col-md-2 form-control squarebrd radioopt" id="inputsavesearch"  >';
        echo ' </div></div>';
        
        echo '<div class="row" >'.
                '<div class="form-group col-sm-6">'.
                '<label for="input_sel_pax" class="control-label">Please select the number of passengers </label>'.
                '</div><div class="form-group col-sm-6" >'.
                '<select id="input_sel_pax" class=" form-control squarebrd" '.
                'style="min-height: 40px;min-width: 100px;max-width: 179px;margin-left: 67px;"  >';
        for( $pa = 1; $pa <= 10; $pa++)
        {
           echo '<option value="'.$pa.'" >'.$pa;
           if($pa == 1)
           echo 'passenger';
           else
           echo 'passengers';
           echo '</option>';
        }
        
        echo '</select></div></div>';
        echo '<div class="row" ><p style="display: none;text-align: center;" id="show_error"  class="error"></p>'.
                '<span class="help-block warring" style="text-align: center;">Please note that all the fields are required.</span>'.
                '</div>';
                
        echo '</div><div class="modal-footer">'.
                '<button type="button" class="btn btn-primary squarebrd" id="mts_book_booker" >Save</button>'.
                '<button type="button" class="btn btn-default squarebrd" id="close_modal" data-dismiss="modal">Close</button>'.
                '</div>';
                
        echo '</div></form><div class="last_msg" style="display: none;"><div class="head">'.
            '</div><div class="content"></div>'.
            '<div class="footer"><button type="button" class="btn btn-default squarebrd" id="close_modal" '. 
            'data-dismiss="modal">Close</button></div></div></div></div></div>';
    }
    
    /***********************************functions about boat availability***************************/
    //right menu for navigation n boat detailed information
    public function aside_boat()
    {
        
        echo '<aside id="available" class="mk-builtin">';
        
        if (!empty($this->html_link_search))
        {
            echo "<div class='back_link'><a href='".get_permalink().$this->html_link_search.
                                            "'>Back to search result</a></div>";
        }
        
        echo    '<div class="sidebar-wrapper">'.
                '<div id="myScrollspy">'.
                '<ul class="nav nav-tabs nav-stacked affix-top" data-spy="affix" data-offset-top="170" '.
                'data-offset-bottom="650"><li id="mts_avail" class="in_progress" >'.
                '<div id="mts_avail_js">'.
                '<center style="margin-top: 20px;text-align: center;font-size: 13px;color: #e8b448;" >'.
                'Checking Availability. . .</center>'.
                '</div><!-- if available -->'.
                '<div class="boat_avail_ctrl ctrl_show" >'.
                '<span class="header_mts_avail">Boat Available</span>'.
                '<div class="trip_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Destination</span><span class="descr_av_2" '.
                'id="mts_js_destination"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip Start on</span><span class="descr_av_2" '.
                'id="mts_js_datestart"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip End on</span><span class="descr_av_2" '.
                'id="mts_js_dateend"  ></span></span><span class="descr_type"><span class="descr_av_1" >Duration</span>'.
                '<span class="descr_av_2" id="mts_js_duration"  ></span></span>'.
                '</div><div class="price_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Price</span><span class="descr_av_2" '.
                'id="mts_js_price"  ></span></span>'.
                '<span class="descr_type" id="if_discount" ><span class="descr_av_1" >Discount</span>'.
                '<span class="descr_av_2" id="mts_js_discount"  ></span></span>'.
                '<span class="descr_type" id="if_discount_total" ><span class="descr_av_1" >Final Price</span>'.
                '<span class="descr_av_2" id="mts_js_total"  ></span></span></div>'.
                '<div class="book_btn" ><div id="addthebr" ></div>'.
                '<button type="button" class="button squarebrd"  id="mts_book_boat" data-toggle="modal" '.
                'data-target="#myModal" >Reserve or <br />Save for later</button>'.
                '</div><div class="change_trip_dates" ><br />'.
                '<a href="javascript:void(0);" class="chgtd mts_check_availability squarebrd" >Change trip Dates?</a>'.
                '</div></div>'.
                '<div class="boat_avail_ctrl ctrl_hide" >'.
                '<div class="boat_avail_ctrl not_avails">'.
                '<span class="header_mts_avail " >Boat Not available</span>'.
                '<h3>Change Trip Dates and check Again ?</h3>'.
                '</div><div class="boat_avail_ctrl trip_dates" >'.
                '<span class="header_mts_avail" >Change trip Dates</span>'.
                '</div><div class="xhts_holder" >'.
                '<label for="mts_recheck_in" >Check In</label>'.
                '<input type="text" id="mts_recheck_in" value="';
                if (isset($_GET['date_from']))
                {
                 echo $_GET['date_from'];
                }
                 else 
                 {
                    echo date("d.m.Y");
                 }
                 echo '"  /></div><div class="xhts_holder" >';
                 echo '<label for="mts_recheck_out" >Check Out</label>';
                 echo '<input type="text" id="mts_recheck_out" value="';
                 if (isset($_GET['date_to']))
                 {
                    echo $_GET['date_to'];
                 }
                 else
                 {
                        echo date("d.m.Y",mktime(0,0,0,date("m"),date("d")+7,date("Y"))); 
                 }
                 echo '" /></div>'.
                        '<a href="javascript:void(0);" class="button check_availb squarebrd">Check Availability</a>'.
                        '</div><div id="mts_book_form hidden" >'.
                        '<input type="hidden" name="bid" id="bid" value="'.substr($_GET['boat_id'],1).'" />'.
                        '<input type="hidden" name="base_id" id="base_id" value="d'.
                            $this->boat_details['homeport']['@attributes']['id_base'].'" />'.
                        '<input type="hidden" name="base_id" id="country_id" value="c'.
                            $this->boat_details['homeport']['@attributes']['id_country'].'" />'.
                        '<input type="hidden" name="homeport" id="homeport" value="'.
                            $this->boat_details['homeport']['@attributes']['name'].'" />'.
                        '<input type="hidden" name="rate_cur" id="rate_cur" value="'.
                            $this->boat_details['operator']['rate_cur'].'" />'.
                        '<input type="hidden" name="def_cur" id="def_cur" value="'.
                            $this->boat_details['operator']['def_cur'].'" />'.
                        '</div></li><li class="active"><a href="#overview" class="mts_navl" >Overview</a></li>'.
                        '<li><a href="#prices" class="mts_navl"  >Prices</a></li>'.
                        '<li><a href="#extra-costs" class="mts_navl" >Extra Costs</a></li>'.
                        '<li><a href="#details" class="mts_navl" >Details</a></li>'.
                        '<li><a href="#map" class="mts_navl" >Map</a></li>'.
                        '<li><a id="download_boat_details"  href="javascript:void(0)">Download Details</a></li>';
                 echo '</ul></div></div></aside>';

  
    }
    
    
    
     //right menu for navigation n boat detailed information
    public function aside_boat2()
    {
        $html ='';
        $html .='<aside id="available" class="mk-builtin">';
        
        if (!empty($this->html_link_search))
        {
            $html .="<div class='back_link'><a href='".get_permalink().$this->html_link_search.
                                            "'>Back to search result</a></div>";
        }
        
        $html .= '<div class="sidebar-wrapper">'.
                '<div id="myScrollspy">'.
                '<ul class="nav nav-tabs nav-stacked affix-top" data-spy="affix" data-offset-top="170" '.
                'data-offset-bottom="650"><li id="mts_avail" class="in_progress" >'.
                '<div id="mts_avail_js">'.
                '<center style="margin-top: 20px;text-align: center;font-size: 13px;color: #e8b448;" >'.
                'Checking Availability. . .</center>'.
                '</div><!-- if available -->'.
                '<div class="boat_avail_ctrl ctrl_show" >'.
                '<span class="header_mts_avail">Boat Available</span>'.
                '<div class="trip_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Destination</span><span class="descr_av_2" '.
                'id="mts_js_destination"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip Start on</span><span class="descr_av_2" '.
                'id="mts_js_datestart"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip End on</span><span class="descr_av_2" '.
                'id="mts_js_dateend"  ></span></span><span class="descr_type"><span class="descr_av_1" >Duration</span>'.
                '<span class="descr_av_2" id="mts_js_duration"  ></span></span>'.
                '</div><div class="price_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Price</span><span class="descr_av_2" '.
                'id="mts_js_price"  ></span></span>'.
                '<span class="descr_type" id="if_discount" ><span class="descr_av_1" >Discount</span>'.
                '<span class="descr_av_2" id="mts_js_discount"  ></span></span>'.
                '<span class="descr_type" id="if_discount_total" ><span class="descr_av_1" >Final Price</span>'.
                '<span class="descr_av_2" id="mts_js_total"  ></span></span></div>'.
                '<div class="book_btn" ><div id="addthebr" ></div>'.
                '<button type="button" class="button squarebrd"  id="mts_book_boat" data-toggle="modal" '.
                'data-target="#myModal" >Reserve or <br />Save for later</button>'.
                '</div><div class="change_trip_dates" ><br />'.
                '<a href="javascript:void(0);" class="chgtd mts_check_availability squarebrd" >Change trip Dates?</a>'.
                '</div></div>'.
                '<div class="boat_avail_ctrl ctrl_hide" >'.
                '<div class="boat_avail_ctrl not_avails">'.
                '<span class="header_mts_avail " >Boat Not available</span>'.
                '<h3>Change Trip Dates and check Again ?</h3>'.
                '</div><div class="boat_avail_ctrl trip_dates" >'.
                '<span class="header_mts_avail" >Change trip Dates</span>'.
                '</div><div class="xhts_holder" >'.
                '<label for="mts_recheck_in" >Check In</label>'.
                '<input type="text" id="mts_recheck_in" value="';
                if (isset($_GET['date_from']))
                {
                 $html .=$_GET['date_from'];
                }
                 else 
                 {
                    $html .=date("d.m.Y");
                 }
                 $html .='"  /></div><div class="xhts_holder" >';
                 $html .='<label for="mts_recheck_out" >Check Out</label>';
                 $html .='<input type="text" id="mts_recheck_out" value="';
                 if (isset($_GET['date_to']))
                 {
                    $html .=$_GET['date_to'];
                 }
                 else
                 {
                    $html .=date("d.m.Y",mktime(0,0,0,date("m"),date("d")+7,date("Y"))); 
                 }
                 $html .='" /></div>'.
                        '<a href="javascript:void(0);" class="button check_availb squarebrd">Check Availability</a>'.
                        '</div><div id="mts_book_form hidden" >'.
                        '<input type="hidden" name="bid" id="bid" value="'.substr($_GET['boat_id'],1).'" />'.
                        '<input type="hidden" name="base_id" id="base_id" value="d'.
                            $this->boat_details['homeport']['@attributes']['id_base'].'" />'.
                        '<input type="hidden" name="base_id" id="country_id" value="c'.
                            $this->boat_details['homeport']['@attributes']['id_country'].'" />'.
                        '<input type="hidden" name="homeport" id="homeport" value="'.
                            $this->boat_details['homeport']['@attributes']['name'].'" />'.
                        '<input type="hidden" name="rate_cur" id="rate_cur" value="'.
                            $this->boat_details['operator']['rate_cur'].'" />'.
                        '<input type="hidden" name="def_cur" id="def_cur" value="'.
                            $this->boat_details['operator']['def_cur'].'" />'.
                        '</div></li><li class="active"><a href="#overview" class="mts_navl" >Overview</a></li>'.
                        '<li><a href="#prices" class="mts_navl"  >Prices</a></li>'.
                        '<li><a href="#extra-costs" class="mts_navl" >Extra Costs</a></li>'.
                        '<li><a href="#details" class="mts_navl" >Details</a></li>'.
                        '<li><a href="#map" class="mts_navl" >Map</a></li>'.
                        '<li><a id="download_boat_details"  href="javascript:void(0)">Download Details</a></li>';
                 $html .='</ul></div></div></aside>';
                 
        return $html;

  
    }
    
    
    
    //right menu for navigation n boat detailed information
    public function booker_aside()
    {

        echo '<aside id="available" class="mk-builtin">';
        if (!empty($this->html_link_search))
        {
            echo "<div class='back_link'><a href='".$this->html_link_search.
                                            "'>Back to search result</a></div>";
        }
        echo    '<div class="sidebar-wrapper">'.
                '<div id="myScrollspy">'.
                '<input type="hidden" name="database" id="database" value="booker"/>'.
                '<input type="hidden" name="country" id="country" value="'.ucfirst(str_replace('-',' ',$this->last_search['dst'])).'"/>'.
                '<input type="hidden" name="basename" id="basename" value="'.$this->show_booker_boats[0]['BaseName'].'"/>'.
                '<ul class="nav nav-tabs nav-stacked affix-top" data-spy="affix" data-offset-top="170" '.
                'data-offset-bottom="650"><li id="mts_avail" class="in_progress" >'.
                '<div id="mts_avail_js">'.
                '<center style="margin-top: 20px;text-align: center;font-size: 13px;color: #e8b448;" >'.
                'Checking Availability. . .</center>'.
                '</div><!-- if available -->'.
                '<div class="boat_avail_ctrl ctrl_show" >'.
                '<span class="header_mts_avail">Boat Available</span>'.
                '<div class="trip_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Destination</span><span class="descr_av_2" '.
                'id="mts_js_destination"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip Start on</span><span class="descr_av_2" '.
                'id="mts_js_datestart"  ></span></span>'.
                '<span class="descr_type"><span class="descr_av_1" >Trip End on</span><span class="descr_av_2" '.
                'id="mts_js_dateend"  ></span></span><span class="descr_type"><span class="descr_av_1" >Duration</span>'.
                '<span class="descr_av_2" id="mts_js_duration"  ></span></span>'.
                '</div><div class="price_info" >'.
                '<span class="descr_type"><span class="descr_av_1" >Price</span><span class="descr_av_2" '.
                'id="mts_js_price"  ></span></span>'.
                '<span class="descr_type" id="if_discount" ><span class="descr_av_1" >Discount</span>'.
                '<span class="descr_av_2" id="mts_js_discount"  ></span></span>'.
                '<span class="descr_type" id="if_discount_total" ><span class="descr_av_1" >Final Price</span>'.
                '<span class="descr_av_2" id="mts_js_total"  ></span></span></div>'.
                '<div class="book_btn" ><div id="addthebr" ></div>'.
                '<button type="button" class="button squarebrd"  id="mts_book_boat" data-toggle="modal" '.
                'data-target="#myModal" >Reserve or <br />Save for later</button>'.
                '</div><div class="change_trip_dates" ><br />'.
                '<a href="javascript:void(0);" class="chgtd mts_check_availability squarebrd" >Change trip Dates?</a>'.
                '</div></div>'.
                '<div class="boat_avail_ctrl ctrl_hide" >'.
                '<div class="boat_avail_ctrl not_avails">'.
                '<span class="header_mts_avail " >Boat Not available</span>'.
                '<h3>Change Trip Dates and check Again ?</h3>'.
                '</div><div class="boat_avail_ctrl trip_dates" >'.
                '<span class="header_mts_avail" >Change trip Dates</span>'.
                '</div><div class="xhts_holder" >'.
                '<label for="mts_recheck_in" >Check In</label>'.
                '<input type="text" id="mts_recheck_in" value="';
                if (isset($this->last_search['date_from']))
                 echo $this->last_search['date_from'];
                 else echo date("d.m.Y");
                 echo '"  /></div><div class="xhts_holder" >';
                 echo '<label for="mts_recheck_out" >Check Out</label>';
                 echo '<input type="text" id="mts_recheck_out" value="';
                 if (isset($this->last_search['date_to']))
                 echo $this->last_search['date_to'];
                 else echo date("d.m.Y",mktime(0,0,0,date("m"),date("d")+7,date("Y"))); 
                 echo '" /></div>'.
                        '<a href="javascript:void(0);" class="button check_availb squarebrd">Check Availability</a>'.
                        '</div><div id="mts_book_form hidden" >'.
                        '<input type="hidden" name="bid" id="bid" value="'.str_replace('b','',$_GET['boat_id']).'" />'.
                        '</div></li><li class="active"><a href="#overview" class="mts_navl" >Overview</a></li>'.
                        '<li><a href="#prices" class="mts_navl"  >Prices</a></li>'.
                        //'<li><a href="#extra-costs" class="mts_navl" >Extra Costs</a></li>'.
                        '<li><a href="#details" class="mts_navl" >Details</a></li>'.
                        '<li><a href="#map" class="mts_navl" >Map</a></li>'.
                        '<li><a id="download_boat_details"  href="javascript:void(0)">Download Details</a></li>';
                 echo '</ul></div></div></aside>';  
    }
    
    
    public function booker_availability()
    {
        $result=array();
        $BOOKER_USER='18abb2dc5849491eaaa06ab3d4fb1dc2';
        $BOOKER_PASSWORD='2ec90d50df594e419c3e52088f947556';  
        $query_boats='https://api.boatbooker.net/ws/sync/v2/main?username='.
                                                $BOOKER_USER.'&password='.$BOOKER_PASSWORD;                 
         $query_boats .='&loadFleetOperators=True&loadAvailabilityTypes=True'.
                        '&loadBoats=True&loadAvailabilityData=True';
                        
         $boat_id=$_GET['id_boat'];
         //name of boat for query
         $boat_name=$_GET['boat_name'];
         //boat  yearbuilt
         $boat_year=$_GET['boat_year'];
         //boat  model
         $boat_model=$_GET['boat_model'];
         //boat  type
         $boat_type=$_GET['boat_type'];
         //boat  country
         $boat_country=$_GET['country'];
         //boat  homeport
         $boat_homeport=$_GET['homeport'];
         $result['id_boat']=$boat_id;
         $result['country']=$boat_country;
         $result['homeport']=$boat_homeport;
         $result['ask_price']=1;
         $result['newprice']=0;
         
         if(!empty($boat_model) && !empty($boat_year) && !empty($boat_type) && !empty($boat_country))
         {
            $query_boats .='&loadSpecificBoats='.$boat_id;
            
            //period for boat availability
            if(isset($_GET['date_from']))
            {
                if(strpos($_GET['date_from'],'.')!==false)
                {
                    $date_from=new DateTime($_GET['date_from']);
                }
                else
                {
                    $date_from_tmp=str_replace('/','.',$_GET['date_from']);
                    $date_from=new DateTime($date_from_tmp);
                }
                
                
            }
            else
            {
                $date_from=new DateTime(date('Y-m-d',mktime(0,0,0,date("m"),date("d")+1,date('Y'))));
            }
         
            if(isset($_GET['date_to']))
            {
                 if(strpos($_GET['date_to'],'.')!==false)
                {
                    $date_to=new DateTime($_GET['date_to']);
                }
                else
                {
                    $date_to_tmp=str_replace('/','.',$_GET['date_to']);
                    $date_to=new DateTime($date_to_tmp);
                }
                
                
            }
            else
            {
                $date_to=new DateTime(date('Y-m-d',mktime(0,0,0,date("m"),date("d")+8,date('Y'))));
            }
            
             $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
             $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
            
            
            $date_from_query=date("Y-m-d",$date_from->getTimestamp());
            $date_to_query=date("Y-m-d",$date_to->getTimestamp()); 
            //nuber of days for boat booking 
            $numberofdays=($date_to->getTimestamp()-$date_from->getTimestamp())/(60*60*24)+1;
            $query_boats .='&availDatePeriodFrom='.$date_from_query.'&availDatePeriodTo='.$date_to_query;
            $booker_boats = json_decode(file_get_contents($query_boats));
            $result['booker_request']=$query_boats;
            
            $result['no_days']=$numberofdays;
            $result['url']=$query_boats;
              
            //fleet operator name, email, site and ID
            //$operator_name=$ope_term->name;
            $operator_id='';
            $operator_email='';
            $operator_site='';
            $operator_name='';
            if (isset($booker_boats->Boats[0]) && count($booker_boats->Boats)==1)
            {
                $operator_id=$booker_boats->Boats[0]->FleetOperatorID;
                foreach($booker_boats->FleetOperators as $ope=>$desc)
                {   
                    if($desc->ID==$operator_id)
                    {
                        $operator_email=$desc->DefaultMail;
                        $operator_site=$desc->Website;
                        $operator_name=$desc->Name;
                        break;
                    }
                }
            }
            else
            {
                die('not'); 
            }
            if(!empty($operator_id)) 
            { 
                //find the boat in Sedna database with checking price
                $sedna_check =json_decode(json_encode(simplexml_load_string(file_get_contents('http://client.sednasystem.com/API/getOperators.asp?refagt=wxft6043'))));
                $count_ope=count($sedna_check->operator);
                $operator_sedna_id='';
                for($i=0;$i<$count_ope;$i++)
                {
                    foreach ($sedna_check->operator[$i] as $ope)
                    {
                        if(!empty($operator_email) && ($ope->ope_email==$operator_email))
                        {
                            $operator_sedna_id=$ope->id_ope;
                            $operator_def_cur=$ope->DefCurr;
                            $result['currency']=$operator_def_cur;
                            break 2;
                        }
                        elseif(!empty($operator_site) && (stripos($ope->ope_site,$operator_site)!==false ||
                                    stripos($operator_site,$ope->ope_site)!==false))
                            {
                                $operator_sedna_id=$ope->id_ope;
                                $operator_def_cur=$ope->DefCurr;
                                $result['currency']=$operator_def_cur;
                                break 2;

                        }
                        elseif(!empty($operator_name) && (stripos($ope->ope_company,$operator_name)!==false ||
                        stripos($operator_name,$ope->ope_company)!==false))
                        {
                            $operator_sedna_id=$ope->id_ope;
                            $operator_def_cur=$ope->DefCurr;
                            $result['currency']=$operator_def_cur;
                            break 2;
                        }
                    }
                }
                
                $id_sedna_boat='';
                //request for sedna operator boats
                if(!empty($operator_sedna_id))
                {
                    $result['sedna_ope']=$operator_sedna_id;
                     $ope_boats='http://client.sednasystem.com/API/getBts3.asp?refagt=wxft6043&Id_ope='.$operator_sedna_id;
                     $result['sedna_url']= $ope_boats;
                    $result_ope_boats=json_decode(json_encode(simplexml_load_string(file_get_contents($ope_boats))));
                    foreach ($result_ope_boats as $boats_sedna)
                    {
                        foreach ($boats_sedna as $boat_sedna)
                        {
                            foreach ($boat_sedna as $attributes)
                            {
                                if($attributes->buildyear==$boat_year)
                                {
                                    if ($attributes->bt_type=='Monohull')
                                    {
                                        if(stripos($boat_model,$attributes->model)!==false)
                                        {
                                            if(trim($attributes->name)==trim($boat_name))
                                            {
                                                $id_sedna_boat=$attributes->id_boat;
                                                break 3;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if(trim($attributes->bt_type)==trim($boat_type))
                                        {
                                            if(stripos($boat_model,$attributes->model)!==false)
                                            {
                                                if(trim($attributes->name)==trim($boat_name))
                                                {
                                                    $id_sedna_boat=$attributes->id_boat;
                                                    break 3;
                                                }
                                            }
                                        }
                                    }
                                }
                           }
                        }
                    }
                    $result['sedna_id']=$id_sedna_boat;
                   
                    if(!empty($id_sedna_boat))
                    {
                        //echo $id_sedna_boat.'<br />';
                        $query_price_sedna='http://client.sednasystem.com/API/getBoat.asp?Id_boat='.$id_sedna_boat.
                                        '&refagt=wxft6043';
                        $query_sedna_avail='http://client.sednasystem.com/m3/agt/6043/default.asp?Action=search&Id_boat='.$id_sedna_boat.
                                        '&DEPART_YYYY='.date('Y',$date_from->getTimestamp()).
                                        '&DEPART_DD='.date('d',$date_from->getTimestamp()).
                                        '&DEPART_MM='.date('m',$date_from->getTimestamp()).
                                        '&Nombjour='.$numberofdays;
                        $boat_avail=json_decode(json_encode(simplexml_load_string(file_get_contents($query_sedna_avail))));
                        $availability=false;
                        
                        if (isset($boat_avail->boat))
                        {
                            foreach ($boat_avail->boat as $pricedet)
                            {
                                foreach($pricedet as $attr)
                                {
                                                    if(isset($attr->newprice))
                                {
                                    //checking country for correct price
                                    if(stripos($attr->country,$boat_country)!==false)
                                    {
                                        $availability=true;
                                        $price_boat_sedna=$attr->newprice;
                                        $discount_boat_sedna=$attr->discount;
                                        $oldprice_boat_sedna=$attr->oldprice;
                                        $NbPax_boat_sedna=$attr->NbPax;
                                        $DefCur_boat_sedna=$attr->IsoCurr;
                                        $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                                        $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                                        $result['no_days']=$numberofdays;
                                        $result['oldprice']=$oldprice_boat_sedna;
                                        $result['ask_price']=0;
                                        $result['newprice']=$price_boat_sedna;
                                        $result['discount']=$discount_boat_sedna;
                                        $result['ourprice']=$result['newprice']-$result['newprice']*5/100;
                                        $result['final_price']='';    
                                        header('Content-type: application/json');
                                        $data=json_encode($result);
                                        die($data);  
                                    }
                                }
                                }
                
                            }
                            if($availability==false)
                            {
                                die('not');
                            }
                            
                            //for future purposes about checking extra and prices
                            //array of result data
                        }
                        else
                        {
                             
                            $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                            $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                            header('Content-type: application/json');
                            $data=json_encode($result);
                             die($data); 
                            /*
                            $result['no_days']=$numberofdays;
                            $result['oldprice']=0;
                            $result['newprice']=0;
                            $result['discount']=0;
                            $result['final_price']='';    
                            
                            die('not');   */
                        }
                    }
                    else
                    {
                         //checking availability in booker database
                    $availability=false;
                    foreach($booker_boats->Availability as $boat_avail)
                    {       
                        if($boat_avail->BoatID==$boat_id)
                        {
                            
                            $availability_type='';
                            $avail_price=0;
                            $avail_number_days=0;
                            $date_end='';
                            $avail_curr='';
                            $avail_start_date=0;
                            $avail_end_date=0;
                            //checking the discount for available period
                            foreach($boat_avail->AvailabilityInfo as $date_avail)
                            {
                                //checking availability type
                                foreach ($booker_boats->AvailabilityTypes as $type)
                                {
                                    if($date_avail->Type==$type->ID)
                                    {
                                        $availability_type=$type->Name;
                                    }
                                }
                            
                                if($availability_type!=='Booked' && $availability_type!=='')
                                {
                                    $availability=true;
                                    $date_start= new DateTime($date_avail->DateFrom);
                                    $date_end= new DateTime($date_avail->DateTo);
                                    if($date_from->getTimestamp()>=$date_start->getTimestamp() && 
                                            $date_from->getTimestamp()<=$date_end->getTimestamp())
                                    {
                                        $avail_start_date=$date_from->getTimestamp();
                                        $result['avail_start']=$avail_start_date;
                                        
                                        $avail_end_date=$date_end->getTimestamp();
                                        $result['avail_end']=$avail_end_date;
                                    }
                                    else
                                    {
                                        if($date_to->getTimestamp()<=$date_end->getTimestamp() &&
                                                $date_to->getTimestamp()>=$date_start->getTimestamp())
                                        {
                                            $avail_end_date=$date_to->getTimestamp();  
                                        }
                                    }
                                }
                                else
                                {
                                    //die('not');  
                                }
                            }
                            break 1;
                        }
                    }
                    if($availability==false)
                    {
                         $boat_prices=$booker_boats->Boats[0]->Prices;
                         $avail_numberdays=($date_to->getTimestamp()-$date_from->getTimestamp())/(60*24*60)+1;
                                      
                         foreach($boat_prices as $boat_price)
                         {
                            $date_start_price=new DateTime($boat_price->DateFrom);
                            $date_end_price=new DateTime($boat_price->DateTo);
                            if($date_from->getTimestamp()>=$date_start_price->getTimestamp() &&
                                            $date_from->getTimestamp()<=$date_end_price->getTimestamp())
                            {
                                    $avail_price=$boat_price->Price;
                                    $avail_curr=$boat_price->CurrencyCode;
                                    $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                                    $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                                    $result['no_days']=$numberofdays;
                                    $result['oldprice']=0;
                                    $result['ask_price']=0;
                                    $result['newprice']=$avail_price;
                                    $result['discount']=0;  
                                    $result['currency']=$avail_curr;  
                                    $result['ourprice']=$result['newprice']-$result['newprice']*5/100;
                                    $result['final_price']='This price is estimated. For the latest price and offers contact us.';    
                                    header('Content-type: application/json');
                                    $data=json_encode($result);
                                    die($data);     
                                
                            }
                         } 
                         if($result['newprice']==0) 
                         {
                            $result['ask_price']=1; 
                            $result['final_price']='';
                            $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                            $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                            $result['no_days']=$numberofdays; 
                            header('Content-type: application/json');
                            $data=json_encode($result);
                            die($data);   
                         }
                    }
                    else
                    {
                        //notice user about not confimed price
                        //$availability_price=
                        $boat_prices=$booker_boats->Boats[0]->Prices;
                        $avail_price_start=0;
                        $avail_price_end=0;
                        $avail_price=0;
                        $avail_number_days_end=0;
                        $avail_number_days_start=0;
                        $avail_price_start=0;
                        $avail_price_end=0;
                        foreach($boat_prices as $boat_price)
                        {
                            $date_start_price=new DateTime($boat_price->DateFrom);
                            $date_end_price=new DateTime($boat_price->DateTo);
                            if($avail_start_date>=$date_start_price->getTimestamp() &&
                                            $avail_end_date<=$date_end_price->getTimestamp())
                            {
                                //number of days
                                $avail_price=$boat_price->Price;
                                $avail_curr=$boat_price->CurrencyCode;
                                //echo $avail_number_days_start.' '.$avail_price_start.' '.$avail_curr.'<br />';
                                //available disounts
                            }
                            else
                            {
                                /*if($avail_end_date>=$date_start_price->getTimestamp() &&
                                            $avail_end_date<=$date_end_price->getTimestamp())
                                {
                                    $price_date_end=$date_end_price->getTimestamp();
                                    $price_date_start=$date_start_price->getTimestamp();
                                    $avail_number_days_end=($avail_end_date-$date_start_price->getTimestamp())/(60*24*60)+1;
                                    $avail_price_end=$boat_price->Price*$avail_number_days_end;
                                    $avail_curr=$boat_price->CurrencyCode;          
                                }*/
                            }
                        }
                        //$avail_number_days=$avail_number_days_end+$avail_number_days_start;
                        //$avail_price=$avail_price_start+$avail_price_end;
                        //if($avail_number_days<$numberofdays)
                        //{
                        //    $avail_price=$avail_price*$numberofdays/$avail_number_days; 
                        //}
                        //array of result data
                        if($avail_price>0)
                        {
                           $result['ask_price']=0; 
                        }
                        else
                        {
                            $result['ask_price']=1; 
                        }
                        $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                        $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                        $result['no_days']=$numberofdays;
                        $result['oldprice']=0;
                        $result['newprice']=$avail_price;
                        $result['dayprice']=$avail_price;
                        $result['discount']=0;  
                        $result['ourprice']=$result['newprice']-$result['newprice']*5/100;
                        $result['currency']=$avail_curr;  
                        $result['final_price']='This price is estimated. For the latest price and offers contact us.';      
                        header('Content-type: application/json');
                        $data=json_encode($result);
                        die($data);     
                    }
                    }
                }
                else
                {
                    //checking availability in booker database
                    $availability=false;
                    foreach($booker_boats->Availability as $boat_avail)
                    {       
                        if($boat_avail->BoatID==$boat_id)
                        {
                            
                            $availability_type='';
                            $avail_price=0;
                            $avail_number_days=0;
                            $date_end='';
                            $avail_curr='';
                            $avail_start_date=0;
                            $avail_end_date=0;
                            //checking the discount for available period
                            foreach($boat_avail->AvailabilityInfo as $date_avail)
                            {
                                //checking availability type
                                foreach ($booker_boats->AvailabilityTypes as $type)
                                {
                                    if($date_avail->Type==$type->ID)
                                    {
                                        $availability_type=$type->Name;
                                    }
                                }
                            
                                if($availability_type!=='Booked' && $availability_type!=='')
                                {
                                    $availability=true;
                                    $date_start= new DateTime($date_avail->DateFrom);
                                    $date_end= new DateTime($date_avail->DateTo);
                                    if($date_from->getTimestamp()>=$date_start->getTimestamp() && 
                                            $date_from->getTimestamp()<=$date_end->getTimestamp())
                                    {
                                        $avail_start_date=$date_from->getTimestamp();
                                        $result['avail_start']=$avail_start_date;
                                        
                                        $avail_end_date=$date_end->getTimestamp();
                                        $result['avail_end']=$avail_end_date;
                                    }
                                    else
                                    {
                                        if($date_to->getTimestamp()<=$date_end->getTimestamp() &&
                                                $date_to->getTimestamp()>=$date_start->getTimestamp())
                                        {
                                            $avail_end_date=$date_to->getTimestamp();  
                                        }
                                    }
                                }
                                else
                                {
                                    //die('not');  
                                }
                            }
                            break 1;
                        }
                    }
                    if($availability==false)
                    {
                         $boat_prices=$booker_boats->Boats[0]->Prices;
                         $avail_numberdays=($date_to->getTimestamp()-$date_from->getTimestamp())/(60*24*60)+1;
                          $arprice1 = $_SESSION['arrprice'];  
						$boatDateFr = explode("/",$_GET['date_from']);	
						$boatDPF = $boatDateFr[1].'/'.$boatDateFr[0].'/'.$boatDateFr[2];		
						$boatDateTo = explode("/",$_GET['date_to']);
						$boatDPT = $boatDateTo[1].'/'.$boatDateTo[0].'/'.$boatDateTo[2];	 	
						foreach ($arprice1  as $value){

							$start_ts = strtotime($value['DateFrom']);
							$end_ts = strtotime($value['DateTo']);
							$user_ts = strtotime($boatDPF);
							$user_ts1 = strtotime($boatDPT);
							if($user_ts <= $start_ts){
								if($user_ts1 >= $end_ts){
									$curprice[] = $value['priceD'];
									$b_status[] = $value['status'];
								}
							}							
						}	
						$sumprc = array_sum($curprice);
						
						if (in_array("Booked", $b_status)){
							$bstatus = "Booked";
						}else{
							$bstatus = "Available";
						} 
						
                         foreach($boat_prices as $boat_price)
                         {
                            $date_start_price=new DateTime($boat_price->DateFrom);
                            $date_end_price=new DateTime($boat_price->DateTo);
                            if($date_from->getTimestamp()>=$date_start_price->getTimestamp() &&
                                            $date_from->getTimestamp()<=$date_end_price->getTimestamp())
                            {
                                    $avail_price=$boat_price->Price;
                                    $avail_curr=$boat_price->CurrencyCode;
                                    $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                                    $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                                    $result['no_days']=$numberofdays;
                                    $result['oldprice']=0;
                                    $result['ask_price']=0;
                                    $result['dayprice']=$boat_price->Price;
									if($sumprc){
										  $result['newprice']=$sumprc;
									}else{
										$result['newprice']=$avail_price;
									}		
                                    $result['discount']=0;  
                                    $result['ourprice']=$result['newprice']-$result['newprice']*5/100;
                                    $result['currency']=$avail_curr;  
									$result['status'] = $bstatus;
                                    $result['final_price']='This price is estimated. For the latest price and offers contact us.';    
                                    header('Content-type: application/json');
                                    $data=json_encode($result);
                                    die($data);     
                                
                            }
                         } 
                         if($result['newprice']==0) 
                         {
                            $result['ask_price']=1; 
                            $result['final_price']='';
                            $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                            $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                            $result['no_days']=$numberofdays; 
                            header('Content-type: application/json');
                            $data=json_encode($result);
                            die($data);   
                         }
                    }
                    else
                    {
                        //notice user about not confimed price
                        //$availability_price=
                        $boat_prices=$booker_boats->Boats[0]->Prices;
                        $avail_price_start=0;
                        $avail_price_end=0;
                        $avail_price=0;
                        $avail_number_days_end=0;
                        $avail_number_days_start=0;
                        $avail_price_start=0;
                        $avail_price_end=0;
                        foreach($boat_prices as $boat_price)
                        {
                            $date_start_price=new DateTime($boat_price->DateFrom);
                            $date_end_price=new DateTime($boat_price->DateTo);
                            if($avail_start_date>=$date_start_price->getTimestamp() &&
                                            $avail_end_date<=$date_end_price->getTimestamp())
                            {
                                //number of days
                                $avail_price=$boat_price->Price;
                                $avail_curr=$boat_price->CurrencyCode;
                                //echo $avail_number_days_start.' '.$avail_price_start.' '.$avail_curr.'<br />';
                                //available disounts
                            }
                            else
                            {
                                /*if($avail_end_date>=$date_start_price->getTimestamp() &&
                                            $avail_end_date<=$date_end_price->getTimestamp())
                                {
                                    $price_date_end=$date_end_price->getTimestamp();
                                    $price_date_start=$date_start_price->getTimestamp();
                                    $avail_number_days_end=($avail_end_date-$date_start_price->getTimestamp())/(60*24*60)+1;
                                    $avail_price_end=$boat_price->Price*$avail_number_days_end;
                                    $avail_curr=$boat_price->CurrencyCode;          
                                }*/
                            }
                        }
                        //$avail_number_days=$avail_number_days_end+$avail_number_days_start;
                        //$avail_price=$avail_price_start+$avail_price_end;
                        //if($avail_number_days<$numberofdays)
                        //{
                        //    $avail_price=$avail_price*$numberofdays/$avail_number_days; 
                        //}
                        //array of result data
                        if($avail_price>0)
                        {
                           $result['ask_price']=0; 
                        }
                        else
                        {
                            $result['ask_price']=1; 
                        }
                        $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
                        $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
                        $result['no_days']=$numberofdays;
                        $result['oldprice']=0;
                        $result['newprice']=$avail_price;
                        $result['discount']=0;  
                        $result['dayprice']=$avail_price;
                        $result['ourprice']=$result['newprice']-$result['newprice']*5/100;
                        $result['currency']=$avail_curr;  
                        $result['final_price']='This price is estimated. For the latest price and offers contact us.';      
                        header('Content-type: application/json');
                        $data=json_encode($result);
                        die($data);     
                    }
                }
            }
            else
            {
                die('not');  
            }

         }
         else
         {
            die('not');   
         }

       
                        //end of seaching boat discount
                        /*if ($show_boats[$i]['Price']>0)
                        {
                            //information about discounts
                            foreach ($boat2->Discounts as $row7=>$discount)
                            {
                                $date_fromD = new DateTime($discount->SailingDateFrom);
                                $date_toD = new DateTime($discount->SailingDateTo);
                                $date_cur=new DateTime($_GET['date_from']);
                                if ($date_cur->getTimestamp()>=$date_fromD->getTimestamp() &&
                                            $date_cur->getTimestamp()<=$date_toD->getTimestamp())
                                {
                                    $show_boats[$i]['Discount']=$discount->Amount;  
                                    $show_boats[$i]['DiscountID']=$discount->DiscountTypeID;
                                    $show_boats[$i]['DiscountName']=$discount->Name;
                                    $show_boats[$i]['DiscountFrom']=$discount->ValidDurationFrom; 
                                    $show_boats[$i]['DiscountTo']=$discount->ValidDurationTo;                                          
                                    break; 
                                }
                            }
                            if (!isset($show_boats[$i]['Discount']))
                            {
                                //information about discounts
                                foreach ($boat2->Discounts as $row7=>$discount)
                                {
                                    $date_fromD = new DateTime($discount->SailingDateFrom);
                                    $date_toD = new DateTime($discount->SailingDateTo);
                                    $date_cur=new DateTime($_GET['date_to']);
                                    if ($date_cur->getTimestamp()>=$date_fromD->getTimestamp() &&
                                                $date_cur->getTimestamp()<=$date_toD->getTimestamp())
                                    {
                                        $show_boats[$i]['Discount']=$discount->Amount;  
                                        $show_boats[$i]['DiscountID']=$discount->DiscountTypeID;
                                        $show_boats[$i]['DiscountName']=$discount->Name;
                                        $show_boats[$i]['DiscountFrom']=$discount->ValidDurationFrom; 
                                        $show_boats[$i]['DiscountTo']=$discount->ValidDurationTo;                                          
                                        break; 
                                    }
                                        
                                } 
                                if (!isset($show_boats[$i]['Discount']))
                                {
                                    $show_boats[$i]['Discount']=0;
                                }
                            }//end of discrounts for request period
                                    
         
                            if ($show_boats[$i]['Discount']>0)
                            {
                                $show_boats[$i]['NewPrice']=$show_boats[$i]['Price']-
                                                    $show_boats[$i]['Price']*$show_boats[$i]['Discount']/100;
                                $show_boats[$i]['OurPrice']=$show_boats[$i]['NewPrice']-
                                                    $show_boats[$i]['NewPrice']*5/100;
                            }
                            elseif($show_boats[$i]['Discount']==0)
                            {
                                $show_boats[$i]['NewPrice']=0;
                                $show_boats[$i]['OurPrice']=$show_boats[$i]['Price']-
                                                    $show_boats[$i]['Price']*5/100;
                            }
                        }*/
       
    }
    
    
    public function show_avail()
    {
        include 'Helper_API.php';
        $used_api = "Sedna";
        $api_src = dirname(__FILE__).'/apis/'.strtolower($used_api).'.php';
            
        if(file_exists($api_src))
        {
            require_once($api_src);
            include dirname(__FILE__).'/libs/rolling_curl/RollingCurl.php';
            if(class_exists($used_api))
            {
                 $h = new Helper;
                        
                
                 $mts_query=array();
                 $mts_query['action']='search';
                 $mts_query['id_boat']=substr($this->boat_details['id_boat'],6);
                 $mts_query['date_from']=$this->last_search['date_from'];
                 $mts_query['date_to']=$this->last_search['date_to'];
                 $mts_query['dst']=$this->last_search['dst'];

                 
                $query = http_build_query($mts_query);
 
                
     
                $input = new Input($parsed_Str);
                $api = new $used_api;
                $api_url = $api->sync_search($input);
                $mts_query2 = $query."&".http_build_query($api_url);

                $url =  'http://client.sednasystem.com/m3/agt/6043/default.asp?'.$mts_query2;
               	$data_x = $h->byGET_( $url );
                $res2 = $h->XMLtoarray($data_x);
      
                        
            }
        }
        
    }


    //description of with all functions for dysplay all information
    public function boat_data()
    {
        $rustart = getrusage();
        include 'LoadTime_API.php';
        include 'Helper_API.php';
        $load_time = new loadTime();
        $this->boat_details='';
        $api_id='';
        $boat_page_name = '';
        
        if(isset($_GET['boat_id']) && !empty($_GET['boat_id']))
        {
            $db_id = $_GET['boat_id'];
            if (strpos($db_id,"s")!==false)
            {
                //Sedna boat description
                $num_id=str_replace('s','',$db_id);
                $boat_query='http://client.sednasystem.com/API/getBoat.asp?id_boat='.$num_id.'&refagt=wxft6043';
                $this->boat_details = json_decode(json_encode(simplexml_load_string(file_get_contents($boat_query))),true);
                //print_r($this->boat_details);
                return true;
            }
            elseif(strpos($db_id,"b")!==false)
            {
                //BoatBooker description
                //$num_id=str_replace('b','',$db_id);
                //$this->booker_boat_cache($num_id);
                //form structure desc result
                //$this->struct_booker_desc['main']=$this->form_desc_data('booker',$this->show_booker_boats[0]);
                            
                //if (count($this->show_booker_boats)>0)
                //{
                //    return true; 
                //}
            }
            else
            {
                return false;
            }

        }
        else
        {
            return false;
        }
    }
    //end of boat description
        
        
       
        
        public function download_boat_form()
        {
            $postid = get_the_ID();
            $boat_fields=$this->get_boat_fields($postid);
            $this->boat_structure=$this->form_desc_data('booker_post',$boat_fields);
            $homeport_name='';
            if(!empty($boat_fields['Homeport']))
            {
                $homeport_name=' in '.$boat_fields['Homeport'];    
            }
            $country_name='';
            if(!empty($boat_fields['Country']))
            {
                $country_name=', '.$boat_fields['Country'];    
            }
            
            $boat_heading=$boat_fields['BoatModel'].' '.$boat_fields['BoatType'].$homeport_name.$country_name;
            $html='';
            $html .='<form id="download_form" method="post" style="display: none;" target="_blank" action="http://sailchecker.com/wp-admin/admin-ajax.php">
                    <input type="button" id="download" name="download" value="Download" />
                    <input type="hidden" name="action" value="download"/>
                    <input type="hidden" name="boat_link" value="http://sailchecker.com/booker_boats/'.
                        $boat_fields['BoatID'].'" />
                    <input type="hidden" name="boat_heading" value="'.$boat_heading.'" />
                    <input type="hidden" name="boat_img" value="'.$boat_fields['Images'][0].'" />
                    <input type="hidden" name="boat_desc" value="'.$this->descriptions('main',$this->boat_structure).'" />
                    <input type="hidden" name="boat_reserv" value="'.$this->descriptions('reservation',$this->boat_structure).'" />
                    <input type="hidden" name="boat_cabins" value="'.$this->descriptions('cabins',$this->boat_structure).'" />
                    <input type="hidden" name="boat_operator" value="'.$boat_fields['Operator'].'" />';
            $prices=get_post_meta($postid,'price',true);
            if(!empty($prices) && $prices>0)
            {
                for($i=0;$i<$prices;$i++)
                {                    
                    $def_cur=get_post_meta($postid, 'CurrencyCode_'.$i,true);
                    if ($def_cur!=='EUR')
                    {
                        $def_cur='$';
                    }
                    else
                    {
                        $def_cur=''; 
                    }
                    $html .='<input type="hidden" name="sprice['.$i.'][price]" value="'.
                        $price=get_post_meta($postid, 'Price_'.$i,true).'" />';
                    $html .='<input type="hidden" name="sprice['.$i.'][start]" value="'.
                            get_post_meta($postid, 'DateFrom_'.$i,true).'" />';
                    $html .='<input type="hidden" name="sprice['.$i.'][currency]" value="'.$def_cur.'" />';
                    $html .='<input type="hidden" name="sprice['.$i.'][end]" value="'.
                        get_post_meta($postid, 'DateTo_'.$i,true).'" />';

                }
        
            }
                /*foreach  ($this->sedna_structure['prices'] as $num=>$value)
                {
                    
                }
               	foreach($this->sedna_structure['extra'] as $num=> $value )
                {
                    echo '<input type="hidden" name="eprice['.$num.'][name]" value="'.$value['name'].'" />';
                    echo '<input type="hidden" name="eprice['.$num.'][price]" value="'.$value['price'].'" />';
                    echo '<input type="hidden" name="eprice['.$num.'][per]" value="'.$value['per'].'" />';
                    echo '<input type="hidden" name="eprice['.$num.'][per2]" value="'.$value['per2'].'" />';
                    echo '<input type="hidden" name="eprice['.$num.'][quantity]" value="'.$value['quantity'].'" />';
                }

                    echo '<input type="hidden" name="plans[0]" value="'.$this->boat_details['plans']['plan']['@attributes']['link'].'" />';*/
            $all_charact=get_post_meta($postid,'equipment',true);

            if(!empty($all_charact) && $all_charact>0)
            { 
               for($i=0; $i<$all_charact; $i++) 
               {
                    $html .='<input type="hidden" name="chars['.$i.'][name]" value="'.
                            get_post_meta($postid,'EquipName_'.$i,true).'" />';
                    $html .='<input type="hidden" name="chars['.$i.'][quantity]" value="'.
                        get_post_meta($postid,'EquipQuantity_'.$i,true).'" />';
               }
            }

                $html .='<input type="hidden" name="location" value="'.str_replace(' ','+',$boat_fields['Homeport']).','.
                            str_replace(' ','+',$boat_fields['Location']).','.str_replace(' ','+',$boat_fields['Country']).
                            '" />';
                $html .= '</form>';
                $html .= '<script type="text/javascript" >'.
                        'jQuery(document).ready(function($)'.
                        '{'.
                         '$("#download").click(function(event)'.
                        '{'.
                        '$("#download_form").submit();'.
                        '});'.
                        '});'.
                        '</script>';
            return  $html;
        }
        
        
        
    
        
        
        
        /****************************functions for checking availability of boat***************************/
        //function for chaching the current availability for boat request
        public function boat_availability()
        {
            $result=array();
            $result['homeport']=$_GET['homeport'];
            $result['country']= $_GET['country'];
            $result['newprice']=0;
            $result['id_boat']=$_GET['id_boat'];
            $result['ask_price']=0; 
            
             
            if(isset($_GET['date_from']))
            {
                if(strpos($_GET['date_from'],'.')!==false)
                {
                         $date_from=new DateTime($_GET['date_from']);
                }
                else
                {
                    $date_from_tmp=str_replace('/','.',$_GET['date_from']);
                    $date_from=new DateTime($date_from_tmp);
                }
                
            }
            else
            {
                $date_from=new DateTime(date('Y-m-d',mktime(0,0,0,date("m"),date("d")+1,date('Y'))));
            }
         
            if(isset($_GET['date_to']))
            {
                if(strpos($_GET['date_to'],'.')!==false)
                {
                    $date_to=new DateTime($_GET['date_to']);
                }
                else
                {
                    $date_to_tmp=str_replace('/','.',$_GET['date_to']);
                     $date_to=new DateTime($date_to_tmp);
                }
                
                
            }
            else
            {
                $date_to=new DateTime(date('Y-m-d',mktime(0,0,0,date("m"),date("d")+8,date('Y'))));
            }
            $result['datestart']=date("d/m/Y",$date_from->getTimestamp());
            $result['dateend']=date("d/m/Y",$date_to->getTimestamp());
            $no_days=($date_to->getTimestamp()-$date_from->getTimestamp())/(60*60*24);
            $result['no_days']=$no_days;

            $url =  'http://client.sednasystem.com/m3/agt/6043/default.asp?action=search&id_boat='.$_GET['id_boat'].'&'.
                    'DEPART_DD='.date('d',$date_from->getTimestamp()).'&DEPART_MM='.date('m',$date_from->getTimestamp()). 
                    '&DEPART_YYYY='.date('Y',$date_from->getTimestamp()).'&Nombjour='.$no_days;
            $sedna_dest=json_decode(json_encode(simplexml_load_string(file_get_contents($url))),true);
            $result['sedna_query']=$url;
            
            
            if(count($sedna_dest)>0)
            {
               foreach($sedna_dest['boat'] as $row)
               {
                   
                    if(isset($row['oldprice']))
                    {
                        $result['oldprice']=$row['oldprice'];
                    }
                    if(isset($row['discount']))
                    {
                        $result['discount']=$row['discount'];
                    }
                    if(isset($row['newprice']))
                    {
                        $result['newprice']=$row['newprice'];
                        $result['ourprice']=intval($row['newprice']-$row['newprice']*5/100);
                    }
                    
               }
               if($result['newprice']>0)
               {
                    header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data);
               }
               else
               {
                    $result['ask_price']=1; 
                     header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data);
                    //die('not');
               }
               
            }
            else
            {
                    $result['ask_price']=1; 
                     header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data);
                    //die('not');
            }

        }
        
        
        //boat booking request from form
        public function boat_reservation()
        {
            $ADMIN_EMAIL='olga.progr@gmail.com,info@sailchecker.com';
            $FROM_NAME='SailChecker';
            $FROM_EMAIL='info@sailchecker.com'; 
            $result=array();
            
            if(!empty($_POST['name']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['tel']))
            {
          		$name = !empty($_POST['name']) ? $_POST['name']  : false ;
          		$fname = !empty($_POST['firstname']) ? $_POST['firstname']  : false ;
          		$email = !empty($_POST['email']) ? $_POST['email']  : false ;
           	    $tel = !empty($_POST['tel']) ? $_POST['tel']  : false ;
           	    if( $name == false || $fname == false || $email == false || $tel == false )
                {
                    $result['send']='0'; 
                    $result['message']='All fields are required!';    
                    header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data);  
                }
                else
                {
                    if($_POST['actiontodo'] == 'inputhold48')
                    {
                         $result['datestart']=$_POST["datestart"];
                         $result['dateend']=$_POST["dateend"];
                         
                         $letter='<p>Hello '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter .='<h3>Thank you for your interest on booking via Sailchecker.com</h3>';
                         $letter .='<p>We will contact you soon about your booking request.</p>';
                         $headers = "MIME-Version: 1.0" . "\r\n";
                         $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                         $headers .= "From: Sailchecker.com info@sailchecker.com"."\r\n";
                         if(mail($_POST['email'],"Your booking request confirmation on Sailchecker.com",$letter,$headers)!==false)
                         {
                            $result['action']='boat_request'; 
                            $result['message']= "<p>Your request for boat was sent!</p>";
                            $result['message'] .= "<p>We will contact you soon about your booking request.</p>";
                            $result['send']='1';
                         }
                         else
                         {
                            $result['action']='error'; 
                            $result['message']= "<p>Please, try to make new request next time.</p>";
                         }
                         $letter_admin='<p><h3>New user request for yacht charter from: '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter_admin .='<p>Email: <b>'.$_POST['email'].'</b></p>';
                         $letter_admin .='<p>Phone: <b>'.$_POST['tel'].'</b></p>'; 
                         $letter_admin .='<p>Date start: <b>'.$_POST['datestart'].'</b></p>'; 
                         $letter_admin .='<p>Date end: <b>'.$_POST['dateend'].'</b></p>'; 
                         $letter_admin .='<p>Seach link: <a href="'.$_POST['location_url'].'" >Yacht search</a></p>';
                         $letter_admin .='<p>Seach url address: '.$_POST['location_url'].'</p>';
                         if(mail($ADMIN_EMAIL,"New user request for yacht charter on Sailchecker.com.",$letter_admin,$headers)!==false)
                         {
                         
                         }
                         
                    }
                    if($_POST['actiontodo'] == 'inputsavesearch')
                    {
                         $letter='<p>Hello '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter .='<h3>Thank you for your interest on booking via Sailchecker.com</h3>';
                         $letter .='<p>Please come back to this url when you are ready for booking  - '; 
                         $letter .='<a href="'.$_POST['location_url'].'" >Selected Yacht</a>.</p>';
                         $headers = "MIME-Version: 1.0" . "\r\n";
                         $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                         $headers .= "From: Sailchecker.com info@sailchecker.com"."\r\n";
                         if(mail($_POST['email'],"Your save search confirmation on Sailchecker.com.",$letter,$headers)!==false)
                         {
                            $result['action']='boat_save'; 
                            $result['message']= "<p>Your selected yacht charter was sent on your email!</p>";
                            $result['send']='1';
                         }
                         else
                         {
                            $result['action']='error'; 
                            $result['message']= "<p>Please, try to make new request next time.</p>";
                         }
                         $letter_admin='<p><h3>User requested save search from '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter_admin .='<h3>Thank you for your interest on booking via Sailchecker.com</h3>';
                         $letter_admin .='<p>Email: <b>'.$_POST['email'].'</b></p>';
                         $letter_admin .='<p>Phone: <b>'.$_POST['tel'].'</b></p>'; 
                         $letter_admin .='<p>Seach link: <a href="'.$_POST['location_url'].'" >Yacht search</a></p>';
                         $letter_admin .='<p>Seach url address: '.$_POST['location_url'].'</p>';
                         if(mail($ADMIN_EMAIL,"New save search on Sailchecker.com.",$letter_admin,$headers)!==false)
                         {
                         
                         }
                    }
                    header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data); 
                        
                    
                }
           
            }
            else
            {
                if($_POST['actiontodo'] == 'inputhold48')
                    {
                         $result['datestart']=$_POST["datestart"];
                         $result['dateend']=$_POST["dateend"];
                         
                         $letter='<p>Hello '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter .='<h3>Thank you for your interest on booking via Sailchecker.com</h3>';
                         $letter .='<p>We will contact you soon about your booking request.</p>';
                         $headers = "MIME-Version: 1.0" . "\r\n";
                         $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                         $headers .= "From: Sailchecker.com info@sailchecker.com"."\r\n";
                         if(mail($_POST['email'],"Your booking request confirmation on Sailchecker.com",$letter,$headers)!==false)
                         {
                            $result['action']='boat_request'; 
                            $result['message']= "<p>Your request for boat was sent!</p>";
                            $result['message'] .= "<p>We will contact you soon about your booking request.</p>";
                            $result['send']='1';
                         }
                         else
                         {
                            $result['action']='error'; 
                            $result['message']= "<p>Please, try to make new request next time.</p>";
                         }
                         $letter_admin='<p><h3>New user request for yacht charter from: '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter_admin .='<p>Email: <b>'.$_POST['email'].'</b></p>';
                         $letter_admin .='<p>Phone: <b>'.$_POST['tel'].'</b></p>'; 
                         $letter_admin .='<p>Date start: <b>'.$_POST['datestart'].'</b></p>'; 
                         $letter_admin .='<p>Date end: <b>'.$_POST['dateend'].'</b></p>'; 
                         $letter_admin .='<p>Seach link: <a href="'.$_POST['location_url'].'" >Yacht search</a></p>';
                         if(mail($ADMIN_EMAIL,"New user request for yacht charter on Sailchecker.com.",$letter_admin,$headers)!==false)
                         {
                         
                         }
                         
                    }
                    if($_POST['actiontodo'] == 'inputsavesearch')
                    {
                         $letter='<p>Hello '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter .='<h3>Thank you for your interest on booking via Sailchecker.com</h3>';
                         $letter .='<p>Please come back to this url when you are ready for booking  - '; 
                         $letter .='<a href="'.$_POST['location_url'].'" >Selected Yacht</a>.</p>';
                         $headers = "MIME-Version: 1.0" . "\r\n";
                         $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                         $headers .= "From: Sailchecker.com info@sailchecker.com"."\r\n";
                         if(mail($_POST['email'],"Your save search confirmation on Sailchecker.com.",$letter,$headers)!==false)
                         {
                            $result['action']='boat_save'; 
                            $result['message']= "<p>Your selected yacht charter was sent on your email!</p>";
                            $result['send']='1';
                         }
                         else
                         {
                            $result['action']='error'; 
                            $result['message']= "<p>Please, try to make new request next time.</p>";
                         }
                         $letter_admin='<p><h3>User requested save search from '.$_POST['firstname'].' '.$_POST['name'].'!</p>';
                         $letter_admin .='<h3>Thank you for your interest on booking via Sailchecker.com</h3>';
                         $letter_admin .='<p>Email: <b>'.$_POST['email'].'</b></p>';
                         $letter_admin .='<p>Phone: <b>'.$_POST['tel'].'</b></p>'; 
                         $letter_admin .='<p>Seach link: <a href="'.$_POST['location_url'].'" >Yacht search</a></p>';
                         if(mail($ADMIN_EMAIL,"New save search on Sailchecker.com.",$letter_admin,$headers)!==false)
                         {
                         
                         }
                    }
                    header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data); 
            }
     
        }
        
        
          //boat booking request from form
        public function landing_page()
        {
            $ADMIN_EMAIL='olga@sailchecker.com,info@sailchecker.com';
            //$ADMIN_EMAIL='olga@sailchecker.com';
            $FROM_NAME='SailChecker';
            $FROM_EMAIL='info@sailchecker.com'; 
            $result=array();
            
            if(!empty($_POST['name']) && !empty($_POST['email']))
            {
          		$name = !empty($_POST['name']) ? $_POST['name']  : false ;
          		$email = !empty($_POST['email']) ? $_POST['email']  : false ;
           	    if( $name == false || $email == false)
                {
                    $result['send']='0'; 
                    $result['message']='All fields are required!';    
                    header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data);  
                }
                else
                {
                         $letter='<p>Hello '.$_POST['name'].'!</p>';
                         $letter .='<h3>Thank you for your interest in catamaran charter!</h3>';
                         $letter .='<p>We will contact you soon about your request.</p>';
                         $headers = "MIME-Version: 1.0" . "\r\n";
                         $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                         $headers .= "From: Sailchecker.com info@sailchecker.com"."\r\n";
                         if(mail($_POST['email'],"Your request confirmation on Sailchecker.com",$letter,$headers)!==false)
                         {
                            $result['action']='boat_request'; 
                            $result['message']= "<p>Your request for catamaran charter was sent!</p>";
                            $result['message'] .= "<p>We will contact you soon about your request.</p>";
                            $result['send']='1';
                         }
                         else
                         {
                            $result['action']='error';
                            $result['send']='0'; 
                            $result['message']= "<p>Please, try to make new request next time.</p>";
                         }
                         $letter_admin='<p><h3>New user request for catamaran charter from: '
                                    .$_POST['name'].'!</p>';
                         $letter_admin .='<p>Email: <b>'.$_POST['email'].'</b></p>';
                         if(mail($ADMIN_EMAIL,"New user request for yacht charter on Sailchecker.com.",$letter_admin,$headers)!==false)
                         {
                         
                         }
                    header('Content-type: application/json');
                    $data=json_encode($result);
                    die($data); 
                        
                    
                }
           
            }
            
     
        }
        
        
        
        
        
        //boat booking request from form
        public function boat_booking()
        {
            //defined('ADMIN_EMAIL') ? true : define('ADMIN_EMAIL', '');
            $ADMIN_EMAIL='info@sailchecker.com';
            $FROM_NAME='SailChecker';
            $FROM_EMAIL='info@sailchecker.com'; 
           
            $language = isset($_REQUEST['mts_language']) ? $_REQUEST['mts_language'] : ( isset($_SESSION['mts_language']) ? $_SESSION['mts_language'] : 0 ) ;

            include 'Helper_API.php';
            $h = new Helper;
    
	
            if(!empty($_POST['doneed']) && $_POST['doneed'] == 'add_client')
            {
	           if(!empty($_POST['name']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['tel']))
                {
            		$name = !empty($_POST['name']) ? $_POST['name']  : false ;
            		$fname = !empty($_POST['firstname']) ? $_POST['firstname']  : false ;
            		$email = !empty($_POST['email']) ? $_POST['email']  : false ;
                	$tel = !empty($_POST['tel']) ? $_POST['tel']  : false ;
                	if( $name == false || $fname == false || $email == false || $tel == false )
                    {
            		  die('All fields are required');
		              }
            		//$find_existing_sql = " SELECT * FROM mts_sedna_clients WHERE email='{$email}' AND tel='{$tel}' AND name='{$name}' AND firstname='{$fname}' LIMIT 1 ";
                     $used_api = "Sedna";
                    $api_src = dirname(__FILE__).'/apis/'.strtolower($used_api).'.php';
     
                    require_once($api_src);
                    $DBHOST = 'localhost';
                    $DBNAME = 'sailu_mts_boats';
                    $DBUSER = 'sailu_mtsboats';
                    $DBPASS = '5ft2zXhVDM-L';
                    $dsn = 'mysql://'.$DBUSER .':'. $DBPASS .'@'. $DBHOST .'/'. $DBNAME .'?persist=0';
                    $conn = ADONewConnection($dsn);
                    if (!$conn) echo("Connection failed");
                   //     $conn->SetFetchMode(ADODB_FETCH_ASSOC);
            		//$find_existing_res = $conn->Execute( $find_existing_sql ); 

		          //if($find_existing_res && !empty($find_existing_res->fields['sedna_id']) )
                   // {
                   //     die( $find_existing_res->fields['sedna_id']);
		            //  }
                   // else
                   // {
			             //$sedna_cfg 	= json_decode(SEDNA_CFG,1);
                         $SEDNA_CFG=json_encode(array('broker_id'=>'wxft6043','language'=> $language));
                         $sedna_cfg 	= json_decode($SEDNA_CFG,1);
                         $array_query=array("choix"=>$_POST["choix"], "name"=>$_POST["name"], "firstname"=>$_POST["firstname"], "email"=>$_POST["email"], "tel" => $_POST["tel"], "do" => $_POST["doneed"]);
			             $query = http_build_query($array_query);
			             $url = "http://client.sednasystem.com/api/insertclient.asp?{$query}&refagt=wxft6043";
			             $data_x =$h->byGET_( $url );
			         $data = $h->XMLtoarray( $data_x  );
			         if(!empty($data) && !empty($data['@attributes']) && !empty($data['@attributes']['id']))
                    {

				        $id = trim($data['@attributes']['id']);

				        if(is_numeric($id))
                        {
        			         $insert_sql = " INSERT INTO  mts_sedna_clients (sedna_id, email, tel, name, firstname) VALUES ('{$id}', '{$email}' , '{$tel}' , '{$name}', '{$fname}' ) ";
					       $conn->Execute( $insert_sql );
				            die($id);
                        }
			         }
		              //}
                    //echo "No";
	               }
                }
                elseif( !empty($_POST['doneed']) && !empty($_POST['id'])  && $_POST['doneed'] == 'add_booking'  && is_numeric($_POST['id']) )
                {
    	           if(!empty($_POST))
                    {

		              $body_user = '';
    
		              $body_admin = '';

		              $user_title = '';

		              $admin_title = '';

		              if($_POST['actiontodo'] == 'inputhold48')
                        {
			             ## book

                         $datestart=$_POST["datestart"];
                         $dateend=$_POST["dateend"];
                         $data=array();

			             $URL_to = "http://client.sednasystem.com/api/insert_charter.asp?id_base_dep=".$_POST['dep_id']."&id_base_arr=".$_POST['arv_id']."&id_boat=".$_POST['bid']."";

			             $URL_to.= "&direct_insert=true&typ_command=ope&id_client=".$_POST['id']."&pax=".$_POST['pax_sel']."";

			             $URL_to.= "&pay_status=1&SRH_datestart=".$_POST["datestart"]."&SRH_DateEnd=".$_POST["dateend"]."&refagt=wxft6043";
			             $book_vars = "We will contact you soon about your booking request.";

                            $data_x = $h->byGET_( $URL_to );

			             $data = $h->XMLtoarray( $data_x  );
			            
                         //$book_vars = print_r($data,1).' '.$datestart.' '.$dateend;
                         
                		//$errdet = "XML resp<br /> <pre>{$data_x}</pre> <br /> URL <br /> {$URL_to}<br />";

			             $body_user.= 'Hello '.$_POST['orig_post']['name'].' '.$_POST['orig_post']['firstname'].',';

			             $body_user.= '<h3>Thank you for your interest on booking via Sailchecker.com</h3>'.
                                        '<p>'.$book_vars.'</p>';

			             $body_admin.=  '<h3>Booking request from '.$_POST['orig_post']['name'].' '.$_POST['orig_post']['firstname'].'</h3>';

			             $body_admin.= '<p>Email: <b>'.$_POST['orig_post']['email'].'</b></p>';

			             $body_admin.= '<p>Phone: <b>'.$_POST['orig_post']['tel'].'</b></p>';
                        $body_admin.= "<p><strong>Booking details</strong></p>";

			             $body_admin.= '<p>Start date:'.$datestart.'</p>';
                         $body_admin .= '<p>End date:'.$dateend.'</p>';
                         $body_admin .='<p>User ID: '.$_POST['id'].'</p>';
                         if(count($data)>0)
                        {
                        	$success_book = true;
                    		$book_vars = '<p>Status: '.$data['@attributes']['status'].'</p>';
                            $book_vars .='<p>Price: '.$data['@attributes']['price'].'</p>';
			             
                         }
                         else
                         {
                            $book_vars='';
                         }
                         $body_admin .=$book_vars;

			             $user_title.= ' Your booking request confirmation on Sailchecker.com';

			             $admin_title.= 'New booking request on on Sailchecker.com';
		              }
                      elseif($_POST['actiontodo'] == 'inputsavesearch')
                    {
                        ## just email

			             $body_user.= '<p>Please come back to this url when you are ready for booking  - ';

			             $body_user.= '<a href="'.$_POST['location_url'].'" >Yacht search</a>.</p>';

			             $body_admin.=  '<h3>User requested save search from '.$_POST['orig_post']['name'].' '.$_POST['orig_post']['firstname'].'</h3>';

			             $body_admin.= '<p>Email: <b>'.$_POST['orig_post']['email'].'</b></p>';

			             $body_admin.= '<p>Phone: <b>'.$_POST['orig_post']['tel'].'</b></p>';

			             $body_admin.= '<p>Seach link: <a href="'.$_POST['location_url'].'" >Yacht search</a></p>';

			             $user_title.= 'Your save search confirmation on Sailchecker.com.';

			             $admin_title.= 'New save search on Sailchecker.com';

		              }
		              include dirname(__FILE__).'/libs/swiftmailer/swift_required.php';

		              $transport = Swift_MailTransport::newInstance();
		              $mailer = Swift_Mailer::newInstance($transport);
		              $exp = explode(',', $ADMIN_EMAIL);

		              foreach($exp as $k => $v){

			             $exp[$k] = trim($v);

		              }
		              $expMAIL =array( $_POST['orig_post']['email'] ) ;
		              $name = $FROM_NAME;
		              $email = $FROM_EMAIL;
		              $message = Swift_Message::newInstance($user_title)
//
		                  ->setFrom(array($email => $name))

		                  ->setTo($expMAIL)

		                  ->setBody($body_user,'text/html');
                          $message = Swift_Message::newInstance($user_title)

		                  ->setFrom(array("olga.progr@gmail.com" => "Olga"))

		                  ->setTo("olga.progr@gmail.com")

		                  ->setBody($body_admin.'<br />'.$book_vars.'<br />'.$body_user,'text/html');
		              $result1 = $mailer->send($message);

		                  ####################################################

		              $transport = Swift_MailTransport::newInstance();
		              $mailer = Swift_Mailer::newInstance($transport);
		              $exp = explode(',', $ADMIN_EMAIL);

		              foreach($exp as $k => $v)
                      {
                         $exp[$k] = trim($v);
                        }

		              $expMAIL = count($exp) > 1 ? $exp : array(trim($ADMIN_EMAIL)) ;
		              $name = $FROM_NAME;

		              $email = $FROM_EMAIL;

		              $message = Swift_Message::newInstance($admin_title)

		                  ->setFrom(array($email => $name))

		                  ->setTo($expMAIL)

		                  ->setBody($body_admin,'text/html');

		              $result2 = $mailer->send($message);
		              if($result1 && $result2)
                      {
			             echo "Ok"." ".$datestart." ".$dateend." ".$data;

		              }else
                      {

			             echo "Nok";
                        }
	               }
                }
                }
                

public function boat_booking_booker()
        {
            //defined('ADMIN_EMAIL') ? true : define('ADMIN_EMAIL', '');
            $ADMIN_EMAIL='info@sailchecker.com';
            $FROM_NAME='SailChecker';
            $FROM_EMAIL='info@sailchecker.com'; 
           
            $language = isset($_REQUEST['mts_language']) ? $_REQUEST['mts_language'] : ( isset($_SESSION['mts_language']) ? $_SESSION['mts_language'] : 0 ) ;

            

            include 'Helper_API.php';
            $h = new Helper;
    
	
            if(!empty($_POST['doneed']) && $_POST['doneed'] == 'add_client')
            {
	           if(!empty($_POST['name']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['tel']))
                {
            		$name = !empty($_POST['name']) ? $_POST['name']  : false ;
            		$fname = !empty($_POST['firstname']) ? $_POST['firstname']  : false ;
            		$email = !empty($_POST['email']) ? $_POST['email']  : false ;
                	$tel = !empty($_POST['tel']) ? $_POST['tel']  : false ;
                	if( $name == false || $fname == false || $email == false || $tel == false )
                    {
            		  die('All fields are required');
		              }
            		$find_existing_sql = " SELECT * FROM mts_sedna_clients WHERE email='{$email}' AND tel='{$tel}' AND name='{$name}' AND firstname='{$fname}' LIMIT 1 ";
                     $used_api = "Sedna";
                    $api_src = dirname(__FILE__).'/apis/'.strtolower($used_api).'.php';
     
                    require_once($api_src);
                    $DBHOST = 'localhost';
                    $DBNAME = 'sailu_mts_boats';
                    $DBUSER = 'sailu_mtsboats';
                    $DBPASS = '5ft2zXhVDM-L';
                    $dsn = 'mysql://'.$DBUSER .':'. $DBPASS .'@'. $DBHOST .'/'. $DBNAME .'?persist=0';
                    $conn = ADONewConnection($dsn);
                    if (!$conn) echo("Connection failed");
                        $conn->SetFetchMode(ADODB_FETCH_ASSOC);
            		$find_existing_res = $conn->Execute( $find_existing_sql ); 

		          if($find_existing_res && !empty($find_existing_res->fields['sedna_id']) )
                    {
                        die( $find_existing_res->fields['sedna_id']);
		              }
                    else
                    {
                    //new booker client
                    }
                    //echo "No";
	               }
                }
                elseif( !empty($_POST['doneed']) && !empty($_POST['id'])  && $_POST['doneed'] == 'add_booking'  && is_numeric($_POST['id']) )
                {
    	           if(!empty($_POST))
                    {

		              $body_user = '';
    
		              $body_admin = '';

		              $user_title = '';

		              $admin_title = '';

		              if($_POST['actiontodo'] == 'inputhold48')
                        {
			             ## book
		
                		#$sedna_cfg 	= json_decode(SEDNA_CFG,1);

			             #$broker_id 	= $sedna_cfg['broker_id'];
                         //$datestart=date("d/m/Y");
                         //$dateend=date("d/m/Y", mktime(date("H")+48,0,0,date("m"),date("d"),date("Y")));
                         //$datestart=str_replace(".","/",$_POST["datestart"]);
                         //$dateend=str_replace(".","/",$_POST["dateend"]);
                         $datestart=$_POST["datestart"];
                         $dateend=$_POST["dateend"];

                         
                		$errdet = "XML resp<br /> <pre>{$data_x}</pre> <br /> URL <br /> {$URL_to}<br />";

			             $body_user.= 'Hello '.$_POST['orig_post']['name'].' '.$_POST['orig_post']['firstname'].',';

			             $body_user.= '<h3>Thank you for your interest on booking via Sailchecker.com</h3>';

			             $body_user.= "<p>{$book_vars}</p>";

			             $body_admin.=  '<h3>Booking request from '.$_POST['orig_post']['name'].' '.$_POST['orig_post']['firstname'].'</h3>';

			             $body_admin.= '<p>Email: <b>'.$_POST['orig_post']['email'].'</b></p>';

			             $body_admin.= '<p>Phone: <b>'.$_POST['orig_post']['tel'].'</b></p>';

			             $body_admin.= '<p>'.(!empty($success_book) ? $book_vars : 'Automate booking was unsucessfull ( BOAT NOT AVAILABLE FOR THEESE DATES )<br /> '.$errdet ).'<br/> for user with <b>ID '.$_GET['id'].'</b></p>';

			             $user_title.= ' Your booking request confirmation on Sailchecker.com';

			             $admin_title.= 'New booking request on on Sailchecker.com';
		              }
                      elseif($_POST['actiontodo'] == 'inputsavesearch')
                    {
                        ## just email

			             $body_user.= '<p>Please come back to this url when you are ready for booking  - ';

			             $body_user.= '<a href="'.$_POST['location_url'].'" >Yacht search</a>.</p>';

			             $body_admin.=  '<h3>User requested save search from '.$_POST['orig_post']['name'].' '.$_POST['orig_post']['firstname'].'</h3>';

			             $body_admin.= '<p>Email: <b>'.$_POST['orig_post']['email'].'</b></p>';

			             $body_admin.= '<p>Phone: <b>'.$_POST['orig_post']['tel'].'</b></p>';

			             $body_admin.= '<p>Seach link: <a href="'.$_POST['location_url'].'" >Yacht search</a></p>';

			             $user_title.= 'Your save search confirmation on Sailchecker.com.';

			             $admin_title.= 'New save search on Sailchecker.com';

		              }
		              include dirname(__FILE__).'/libs/swiftmailer/swift_required.php';

		              $transport = Swift_MailTransport::newInstance();
		              $mailer = Swift_Mailer::newInstance($transport);
		              $exp = explode(',', $ADMIN_EMAIL);

		              foreach($exp as $k => $v){

			             $exp[$k] = trim($v);

		              }
		              $expMAIL =$_POST['orig_post']['email'];
		              $name = $FROM_NAME;
		              $email = $FROM_EMAIL;
		              $message = Swift_Message::newInstance($user_title)
//
		                  ->setFrom(array($email => $name))

		                  ->setTo($expMAIL)

		                  ->setBody($body_user,'text/html');
                           $result0 = $mailer->send($message);
                    $message = Swift_Message::newInstance($user_title)
                   

		                  ->setFrom(array("olga.progr@gmail.com" => "Olga"))

		                  ->setTo("olga.progr@gmail.com")

		                  ->setBody($body_admin,'text/html');
		              $result1 = $mailer->send($message);

		                  ####################################################

		              $transport = Swift_MailTransport::newInstance();
		              $mailer = Swift_Mailer::newInstance($transport);
		              $exp = explode(',', $ADMIN_EMAIL);

		              foreach($exp as $k => $v)
                      {
                         $exp[$k] = trim($v);
                        }

		              $expMAIL = count($exp) > 1 ? $exp : array(trim($ADMIN_EMAIL)) ;
		              $name = $FROM_NAME;

		              $email = $FROM_EMAIL;

		              $message = Swift_Message::newInstance($admin_title)

		                  ->setFrom(array($email => $name))

		                  ->setTo($expMAIL)

		                  ->setBody($body_admin,'text/html');

		              $result2 = $mailer->send($message);
		              if($result1 && $result2)
                      {
			             echo "Ok"." ".$datestart." ".$dateend;

		              }else
                      {

			             echo "Nok";
                        }
	               }
                }
    }
    
    
}






add_action('wp_ajax_boat_availability', array('MTS_API','boat_availability'));
add_action('wp_ajax_nopriv_boat_availability', array('MTS_API','boat_availability'));

add_action('wp_ajax_nopriv_get_list_locations', array('MTS_API','get_list_locations'));
add_action('wp_ajax_get_list_locations', array('MTS_API','get_list_locations'));

add_action('wp_ajax_boat_booking', array('MTS_API','boat_booking'));
add_action('wp_ajax_nopriv_boat_booking', array('MTS_API','boat_booking'));

add_action('wp_ajax_landing_page', array('MTS_API','landing_page'));
add_action('wp_ajax_nopriv_landing_page', array('MTS_API','landing_page'));

add_action('wp_ajax_boat_reservation', array('MTS_API','boat_reservation'));
add_action('wp_ajax_nopriv_boat_reservation', array('MTS_API','boat_reservation'));
add_action('wp_ajax_booker_availability', array('MTS_API','booker_availability'));
add_action('wp_ajax_nopriv_booker_availability', array('MTS_API','booker_availability'));


//insert all neseccary scripts and styles
 //register the short code for using plugin






$wpMTS = new  MTS_API;






?>