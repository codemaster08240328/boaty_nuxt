<?php
/**
 * Plugin Name: MTS import data {updated}
 * Description: To use this plugin you must run the script from setting page (or cron)
 * Version: 2.0
 * Author: Anton Sluchak
 */
//defined('ABSPATH') or die("No script kiddies please!");

//class for implementing search engine

include_once 'boat_import.php';
include_once 'Boats.class.php';



//creating all necessary custom post codes and taxonomies for boat pages
add_action( 'init', 'mts_import_cpt_init' );
function mts_import_cpt_init() {
    register_post_type( 'boat_page',
                               array(
                                     'labels' => array(
                                                       'name' => _x( 'Sedna Boats','Sedna Boats' ),
                                                       'singular_name' => _x( 'Sedna Boat','Sedna Boats' )
                                                       ),
                                     'public' => true,
                                     'has_archive' => true,
                                     'rewrite' => array('slug' => _x('boat','boat'),'with_front'=>false),
                                     )
                               );

                               
    
                               
    register_post_type( 'boat_post',
                               array(
                                     'labels' => array(
                                                       'name' => _x( 'Booker Boats','booker_boat' ),
                                                       'singular_name' => _x( 'Boat','booker_boat' )
                                                       ),
                                     'taxonomies' => array('country','id_ope','bt_model','bt_type','bt_brand',
                                                            'destination','fleet_operator','boatmodel'),  
                                     'public' => true,
                                     'has_archive' => true,
                                     'rewrite' => array('slug' => _x('boats','booker_boat'),'with_front'=>false),
                                     'supports' => array( 'thumbnail','editor','title' )
                                     )
                               );
    
                               
    register_post_type( 'boat_base',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Bases ID' ),
                                                       'singular_name' => __( 'Boat' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>false,
                                     'show_in_menu' =>false,
                                     'show_in_admin_bar'=>false,
                                     'hierarchical'=>true,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => 'id_bases','with_front'=>false),
                                     )
                               );
                               
      register_post_type( 'boat_ope',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Operators' ),
                                                       'singular_name' => __( 'Operator' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>true,
                                     'show_in_menu' =>true,
                                     'show_in_admin_bar'=>true,
                                     'hierarchical'=>false,
                                     'has_archive' => true,
                                     'rewrite' => array('slug' => 'boat_operator','with_front'=>false),
                                     )
                               );
                               
      register_post_type( 'boat_equipment',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Equipments' ),
                                                       'singular_name' => __( 'Equipment' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>false,
                                     'show_in_menu' =>false,
                                     'show_in_admin_bar'=>false,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => 'boat_equipment','with_front'=>false),
                                     )
                               );
                               
      register_post_type( 'boat_cat_equipment',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Equipment Catgories' ),
                                                       'singular_name' => __( 'Equipment category' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>false,
                                     'show_in_menu' =>false,
                                     'show_in_admin_bar'=>false,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => 'boat_type_equipment','with_front'=>false),
                                     )
                               );
                               
      register_post_type( 'boat_type_sevice',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Sevices types' ),
                                                       'singular_name' => __( 'Service type' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>false,
                                     'show_in_menu' =>false,
                                     'show_in_admin_bar'=>false,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => 'boat_type_service','with_front'=>false),
                                     )
                               );
                               
                               
      register_post_type( 'boat_sevice',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Sevices' ),
                                                       'singular_name' => __( 'Service' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>false,
                                     'show_in_menu' =>false,
                                     'show_in_admin_bar'=>false,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => 'boat_service','with_front'=>false),
                                     )
                               );
                               
      register_post_type( 'extra_prices',
                               array(
                                     'labels' => array(
                                                       'name' => _x( 'Extra prices','Extra prices' ),
                                                       'singular_name' => _x( 'Extra prices','Extra prices' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>true,
                                     'show_in_menu' =>true,
                                     'show_in_admin_bar'=>true,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => _x('extra_prices','extra_prices'),'with_front'=>false),
                                     )
                               );
                               
      register_post_type( 'boat_model',
                               array(
                                     'labels' => array(
                                                       'name' => _x( 'Boat models','Boat_models' ),
                                                       'singular_name' => _x( 'Boat model','Boat_models' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>true,
                                     'show_in_menu' =>true,
                                     'show_in_admin_bar'=>true,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => _x('boat_model','boat_model'),'with_front'=>false),
                                     'supports' => array( 'thumbnail','editor','title' )
                                     )
                               );
                               
                               
         register_post_type( 'boat_brand',
                               array(
                                     'labels' => array(
                                                       'name' => __( 'Boat brands' ),
                                                       'singular_name' => __( 'Boat brand' )
                                                       ), 
                                     'public' => true,
                                     'show_in_nav_menus' =>false,
                                     'show_in_menu' =>false,
                                     'show_in_admin_bar'=>false,
                                     'hierarchical'=>false,
                                     'has_archive' => false,
                                     'rewrite' => array('slug' => 'boat_brand','with_front'=>false),
                                     )
                               );
                               
    // old taxonomy without translataion, 
	$labels = array(
		'name'              => _x( 'Countries', 'country_taxonomy'),
		'singular_name'     => _x( 'Country', 'country_taxonomy'),
		'search_items'      => __( 'Search Country' ),
		'all_items'         => __( 'All Countries' ),
		'edit_item'         => __( 'Edit Country' ),
		'update_item'       => __( 'Update Country' ),
		'add_new_item'      => __( 'Add New Country' ),
		'new_item_name'     => __( 'New Country Name' ),
		'menu_name'         => _x( 'Countries','Countries' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => _x( 'country', 'country_taxonomy'),'with_front'=>false ),
	);

	register_taxonomy( 'country', 'boat_post', $args );
    
    
    //new taxonomy for destinations with translation
	$labels = array(
		'name'              => _x( 'Destinations', 'Destinations'),
		'singular_name'     => _x( 'Destination', 'Destination'),
		'search_items'      => _x( 'Search Destination','Search Destination' ),
		'all_items'         => _x( 'All Destinations','All Destinations' ),
		'edit_item'         => _x( 'Edit Destination','Edit Destination' ),
		'update_item'       => _x( 'Update Destination','Update Destination' ),
		'add_new_item'      => _x( 'Add New Destination','Add New Destination' ),
		'new_item_name'     => _x( 'New Destination Name','New Destination Name' ),
		'menu_name'         => _x( 'Destinations','Destinations' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => _x( 'destination', 'destination'),'with_front'=>false ),
	);
    
    register_taxonomy( 'destination', 'boat_post', $args );
                               


    
    //old taxonomy for boat models without translation
    register_taxonomy(
		'bt_model',
		'boat_post',
		array(
			'label' => __( 'Boat models' ),
			'rewrite' => array( 'slug' => 'models' ),
            'show_ui'=>true,
            'show_admin_column' => true,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,

		)
	);
    
    //new taxonomy for boat models with translation
    register_taxonomy(
		'boatmodel',
		'boat_post',
		array(
			'label' => _x( 'Boat models','Boat models' ),
			'rewrite' => array( 'slug' => _x('boatmodels','boatmodels'),'with_front'=>false ),
            'show_ui'=>true,
            'show_admin_column' => false,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,
		)
	);
    
    
    //old taxonomy for boat types wihtout translation
    register_taxonomy(
		'bt_type',
		'boat_post',
		array(
			'label' => __( 'Boat types' ),
			'rewrite' => array( 'slug' => 'boat_types','with_front'=>false ),
            'show_ui'=>true,
            'show_admin_column' => true,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,

		)
	);
    
    //new taxonomy for boat types with translation
      register_taxonomy(
		'boattype',
		'boat_post',
		array(
			'label' => _x( 'Boat types','Boat types' ),
			'rewrite' => array( 'slug' => _x('boattypes','boattypes'),'with_front'=>false),
            'show_ui'=>true,
            'show_admin_column' => false,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,

		)
	);
    
      register_taxonomy(
		'bt_brand',
		'boat_post',
		array(
			'label' => __( 'Boat brand' ),
			'rewrite' => array( 'slug' => 'boat_brands','with_front'=>false ),
            'show_ui'=>true,
            'show_admin_column' => true,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,

		)
	);
    
    

    
    register_taxonomy(
		'id_ope',
		'boat_post',
		array(
			'label' => __( 'Operators' ),
            'show_ui'=>true,
            'show_admin_column' => true,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,
            'rewrite' => array('slug' => 'operator','with_front'=>false)

		)
	);
    
    //operator for translation on other languages
      register_taxonomy(
		'fleet_operator',
		'boat_post',
		array(
			'label' => _x( 'Operators','Operators' ),
            'show_ui'=>true,
            'show_admin_column' => false,
            'show_in_nav_menus'=>true,
            'hierarchical'=>false,
            'rewrite' => array('slug' => _x('fleet-operator','fleet-operator'),'with_front'=>false)

		)
	);
    
    
    
    register_taxonomy(
		'id_equip',
		'boat_post',
		array(
			'label' => __( 'Equipments' ),
			'rewrite' => array( 'slug' => 'equipments','with_front'=>false ),
            'show_ui'=>false,
            'show_admin_column' => false,
            'show_in_nav_menus'=>false,
            'hierarchical'=>false,

		)
	);
    
    
    register_taxonomy(
		'id_type_service',
		'boat_post',
		array(
			'label' => __( 'Services types' ),
			'rewrite' => array( 'slug' => 'service_type','with_front'=>false ),
            'show_ui'=>false,
            'show_admin_column' => false,
            'show_in_nav_menus'=>false,
            'hierarchical'=>false,

		)
	);
    
}

function mts_import_rewrite_flush() {

    mts_import_cpt_init();

    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'mts_import_rewrite_flush' );






class MTS_IMPORT_BETA
{
    public $message;
    protected $symbols;
    var $general_info_keys = array(
        'id_boat',
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
        'homeport',
        'homeport_id',
        'homeport_id_country',
        'price'  
    );
    
    
        //fields for Booker boat post
       protected $fields_boat = array(
        'Boat ID'=>'id_boat',
        'Max Berths'=>'BerthsMax',
        'Berths'=>'BerthsStr',
        'Basic Berths'=>'BerthsBasic',
        'Basic cabins'=>'CabinsBasic',
        'Max cabins'=>'CabinsMax',
        'Total cabins'=>'CabinsStr',
        'Commission'=>'Commission',
        'Engine'=>'Engine',
        'Draft'=>'Draft',
        'Crew'=>'HasCrew',
        'Length'=>'Length',
        'Name'=>'Name',
        'Built Year'=>'YearBuilt',
        'ID Model'=>'ModelID',
        'Number of images'=>'BoatImages',
        );
    
    public function __construct()
    {
        
        $this->message='';

        add_action( 'admin_menu', array($this, 'mts_option_menu'));
        add_action( 'init', array($this, 'show_categories'));
        add_action( 'add_meta_boxes', array($this, 'mts_meta_boxes'));
        //form for editing fields of boat model
        add_action( 'save_post_boat_model', array($this,'mts_save_meta_boat_model') );

        add_filter( 'cron_schedules', array($this, 'cron_add_weekly'));
        add_filter( 'cron_schedules', array($this, 'cron_add_5min'));
        //additional fields for categories
        add_action( 'country_edit_form_fields', array($this,'country_edit_meta_field'));
        add_action( 'id_ope_edit_form_fields', array($this,'id_ope_edit_meta_field'));
        add_action( 'destination_edit_form_fields', array($this,'destination_edit_meta_field'));
        add_action( 'boattype_edit_form_fields', array($this,'boattype_edit_meta_field'));
        add_action( 'edited_destination', array( $this, 'save_destination_iso' ));
        add_action( 'import_boats', array($this,'new_boats'));
        add_action( 'import_characteristics', array($this,'boats_characteristics'));
        add_action( 'import_prices', array($this,'boats_prices'));
        add_action( 'import_equipments', array($this,'boats_equipment'));
        add_action( 'import_extras', array($this,'boats_extraprices'));
        add_action( 'import_brands', array($this,'boat_brand'));
        add_action( 'new_template', array($this,'change_template'));
        add_action( 'new_slug', array($this,'change_slug'));
        add_action( 'featured_image', array($this,'featured_image'));
        add_action( 'import_models', array($this,'new_models'));
        add_action( 'import_types', array($this,'new_types'));
        //styles for forms in admin
        add_action( 'wp_enqueue_scripts', array($this,'import_fields_styles') );

        
        
        //functions for cron
        
        //if ( ! wp_next_scheduled( 'import_boats' ) ) 
        //{
                //wp_schedule_event( time(), 'hourly', 'import_boats' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'import_boats' );
        //}
        //if ( ! wp_next_scheduled( 'import_characteristics' ) ) 
        //{
                //wp_schedule_event( time()+60*3, 'hourly', 'import_characteristics' );
        //}
        //else
        //{
             wp_clear_scheduled_hook( 'import_characteristics' );
        //}
        
        //if ( ! wp_next_scheduled( 'import_prices' ) ) 
        //{
                //wp_schedule_event( time()+60*6, 'hourly', 'import_prices' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'import_prices' );
        //}
        
         if ( ! wp_next_scheduled( 'import_equipments' ) ) 
        {
                wp_schedule_event( time()+60*12, 'hourly', 'import_equipments' );
        }
        else
        {
            //wp_clear_scheduled_hook( 'import_equipments' );
        }
        
        
        //if ( ! wp_next_scheduled( 'import_extras' ) ) 
       //{
                //wp_schedule_event( time()+60*15, 'hourly', 'import_extras' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'import_extras' );
        //}
        
        //if ( ! wp_next_scheduled( 'import_brands' ) ) 
        //{
                //wp_schedule_event( time()+60*18, 'hourly', 'import_brands' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'import_brands' );
        //}
        
        
        //if ( ! wp_next_scheduled( 'new_template' ) ) 
        //{
                //wp_schedule_event( time()+60*22, 'hourly', 'new_template' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'new_template' );
        //}
        
        //if ( ! wp_next_scheduled( 'new_slug' ) ) 
        ///{
                //wp_schedule_event( time()+60*22, 'hourly', 'new_slug' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'new_slug' );
        //}
        
        //if ( ! wp_next_scheduled( 'featured_image' ) ) 
        //{
                //wp_schedule_event( time()+60*26, 'hourly', 'featured_image' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'featured_image' );
        //}
        
         //if ( ! wp_next_scheduled( 'import_models' ) ) 
        //{
                //wp_schedule_event( time()+60*35, 'hourly', 'import_models' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'import_models' );
        //}
        
        // if ( ! wp_next_scheduled( 'import_types' ) ) 
        //{
                //wp_schedule_event( time()+60*40, 'hourly', 'import_types' );
        //}
        //else
        //{
            wp_clear_scheduled_hook( 'import_types' );
        //}
        
        
        wp_clear_scheduled_hook( 'scrape_general_info' );
        wp_clear_scheduled_hook( 'availabilities' );
        
        

       

   
    }
    
    //import of all categories and taxonomis for boat post
    public function show_categories()
    {
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='new_base')
        {
            $this->new_locations();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='new_operators')
        {
            $this->new_operators();
        }
        
         if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='new_models')
        {
            $this->new_models();
        }
        
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='new_types')
        {
            $this->new_types();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='new_boats')
        {
            $this->new_boats();
        }
        
         if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='scrape_details')
        {
            $this->boats_characteristics();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='prices')
        {
            $this->boats_prices();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='equipment')
        {
            $this->boats_equipment();
        }
        
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='new_extras')
        {
            $this->boats_extraprices();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='brand_type')
        {
            $this->boat_brand();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='single_scrape')
        {
            $boats  = new Boats();
            $boats->getBoatFullData($_GET['post_id'],$_GET['id_boat']);
            
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='boat_template')
        {
            
            $this->change_template();
        }
        
        if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='featured_image')
        {
            
            $this->featured_image();
        }
        
         if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='single_model')
        {
            
            $this->single_model();
        }
        
         if($_GET['page']=='mts-import-data-beta' && $_GET['action']=='search_model')
        {
            
            $this->search_model();
        }
        
        
    }
    
    //styles for forms in admin part
    function import_fields_styles() 
    {
        
	   wp_enqueue_style( 'fields-form-admin', plugins_url('css', __FILE__ ). '/form-fields-admin.css', array(), '1.0.0', true );
    }
    
    /*********************functions for impoer all data from BoatBooker database**********************/
    //change template for all boats for short codes
    public function change_template()
    {
        $boats  = new Boats();
        $boats->makeBoattemplate();
    }
    
    public function change_slug()
    {
        $boats  = new Boats();
        $boats->makeBoatslug();
    }
    
    
    public function featured_image()
    {
        $boats  = new Boats();
        $boats->make_image();
    }
    
    //function to import new bases
    public function new_locations()
    {
        $boats = new Boats;
        //$count=$boats->duplicate_boat_ru();
        $boats->getExtraImages();
         
         $this->message='<p>Were added '.$count.' new destination!</p>'; 
         $message= $this->message;
    }
    //end of importing new locations
    
    //function for import new operators
    public function new_operators()
    {
        
        $boats = new Boats;
        $import_oper=$boats->save_operator_info();
        
        if($import_oper>0)
        {
            $this->message='<p>New operators: '.$import_oper.'</p>';
        }
       
    }
    //end of import operators
    
    
    //function for import new operators
    public function new_models()
    {
        
        $boats = new Boats;
        $import_models=$boats->import_transl_models();
        
        if($import_models>0)
        {
            $this->message='<p>New models: '.$import_models.'</p>';
        }
       
    }
    
    public function search_model()
    {
        $boats = new Boats;
        $import_types=$boats->search_model();
    }
    
    
    //function for import new operators
    public function new_types()
    {
        
        $boats = new Boats;
        $import_types=$boats->duplicat_transl_types();
        
        if($import_types>0)
        {
            $this->message='<p>New boat types: '.$import_types.'</p>';
        }
       
    }
    
    
    //function for import new boats
    public function new_boats()
    {
        global $wpdb;
        $Import = new BoatImport;
        $import_boats=$Import->BookerBoatsImport();
        $this->message='<p>Number of new imported boats: '.$import_boats.'</p>';
            
    }
    
    //function for import new boats
    public function single_model()
    {
        global $wpdb;
        $boats         = new Boats();
        $boats->singleModel($_GET['post_id'],$_GET['id_model']);
            
    }
    
    
    
    
    
    //function for import boat characteristics
    public function boats_characteristics()
    {
        $boats         = new Boats();
        $boats=$boats->getBoatCharacteristics();
        if(!empty($boats))
        {
            $this->message='<p>Number of new updated boats with all characteristics: '.$boats.'</p>';
        
        }
            
    }
    
    
    
    //function for import boat equipments
    public function boats_equipment()
    {
        $boats         = new Boats();
        $msg=$boats->getBoatEquipment();
        if(!empty($boats))
        {
            $this->message=$msg;
        
        }
            
    }
    
    
    //function for import boat prices
    public function boats_prices()
    {
        $boats         = new Boats();
        $boats=$boats->getBoatPrices();
        if(!empty($boats))
        {
            $this->message='<p>Number of new updated boats with all prices: '.$boats.'</p>';
        
        }
            
    }
    
    
    
    //function for import boat extra prices
    public function boats_extraprices()
    {
        $boats         = new Boats();
        $boats=$boats->import_extras_transl();
        /*if(!empty($boats))
        {
            $this->message='<p>Number of new updated boats with all prices: '.$boats.'</p>';
        
        }*/
            
    }
    
    
    //function for import boat brand
    public function boat_brand()
    {
        $boats         = new Boats();
        $boats=$boats->getBoatBrand();
        if(!empty($boats))
        {
            $this->message='<p>Number of new updated boats with all prices: '.$boats.'</p>';
        
        }
            
    }
    
    
    /*********************functions for dysplayng additinal fields in categories************************/
    public function destination_edit_meta_field($term)
    {
        $term_iso = get_option( "tax_iso_".$_GET['tag_ID']);
         $term_lang = get_option( "tax_lang_".$_GET['tag_ID']);
       
        
	   echo '<tr class="form-field">';
	   echo '<th scope="row" valign="top">'.
            '<label for="iso_code">ISO code</label>'.
            '</th>'.
            '<td>'.
			     '<input type="text" name="iso_code" id="iso_code" value="'.
                   $term_iso.'" />'.
		   '</td>';
           
      echo '<tr class="form-field">';
        echo '<th scope="row" valign="top">'.
            '<label for="iso_code">Language code</label>'.
            '</th>'.
            '<td>'.
			     '<input type="text" name="code_lang" id="code_lang" value="'.
                   $term_lang.'" />'.
		   '</td>';
    }
    
    public function save_destination_iso($term_id)
    {
        if ( isset( $_POST['iso_ocde'] ) ) 
        {
         update_option( "tax_iso_".$term_id,$_POST['iso_code']);
 
        }
        if ( isset( $_POST['code_lang'] ) ) 
        {
         update_option( "tax_lang_".$term_id,$_POST['code_lang']);
 
        }
    }
    
    
    //displaying the iso code field in country category
    public function country_edit_meta_field($term) 
    {

	   // retrieve the existing value(s) for this meta field. This returns an array
       $term_iso = get_option( "tax_meta_".$_GET['tag_ID']);
       $term_base = get_option( "base_meta_".$_GET['tag_ID']);
       
        
	   echo '<tr class="form-field">';
	   echo '<th scope="row" valign="top">'.
            '<label for="iso_code">ISO code</label>'.
            '</th>'.
            '<td>'.
			     '<input type="text" name="iso_code" id="iso_code" value="'.
                   $term_iso.'" />'.
		   '</td>';
	   echo   '</tr>';
       echo '<tr class="form-field">';
	   echo '<th scope="row" valign="top">'.
            '<label for="iso_code">Base code</label>'.
            '</th>'.
            '<td>'.
			     '<input type="text" name="iso_code" id="iso_code" value="'.
                   $term_base.'" />'.
		   '</td>';
	   echo   '</tr>';
   
    }
    
    
     //displaying the iso code field in country category
    public function boattype_edit_meta_field($term) 
    {

	   // retrieve the existing value(s) for this meta field. This returns an array
       $term_type = get_option( "tax_typeid_".$_GET['tag_ID']);

	   echo '<tr class="form-field">';
	   echo '<th scope="row" valign="top">'.
            '<label for="iso_code">Boat type ID</label>'.
            '</th>'.
            '<td>'.
			     '<input type="text" name="type_id" id="type_id" value="'.
                   $term_type.'" />'.
		   '</td>';
	   echo   '</tr>';
   
    }
    
    
    
    //displaying the ID code field in operator category
    public function id_ope_edit_meta_field($term) 
    {

	   // retrieve the existing value(s) for this meta field. This returns an array

       $term_id = get_option( "oper_id_".$_GET['tag_ID']);
       $term_bases = get_option( "oper_bases_".$_GET['tag_ID']);
        $term_boats = get_option( "count_boat_".$_GET['tag_ID']);
       
        
	   echo '<tr class="form-field">';
	   echo '<th scope="row" valign="top">'.
            '<label for="iso_code">Operator ID</label>'.
            '</th>'.
            '<td>'.
			     '<input type="text" name="iso_code" id="iso_code" value="'.
                   $term_id.'" />'.
		   '</td>';
	   echo   '</tr>';
        //it is useful to know also the bases and number of boats in each base
    }
    
    


    
    
    


    public function mts_option_menu()
    {
        add_options_page( 'Import MTS data beta', 'MTS import beta', 'activate_plugins', 'mts-import-data-beta', array($this,'mts_import_options') );
    }

    //admin interface with meta data for boat models, operators and boats
    public function mts_meta_boxes()
    {
        add_meta_box("boat_desc", "Sedna boat description", array($this,"mts_meta_box_callback"), 'boat_page', 'normal', 'default');
        add_meta_box("boat_desc", "BoatBooker boat description", array($this,"mts_meta_booker_callback"), 'boat_post', 'normal', 'default');
        //function for displaying all fields for boat model
        add_meta_box("model_desc", "Boat model description", array($this,"mts_meta_moatmodel_callback"), 'boat_model', 'normal', 'default');
        //function for displaying all fields for operator
        add_meta_box("oper_desc", "Operator description", array($this,"mts_meta_operator_callback"), 'boat_ope', 'normal', 'default');
    }
    
    
    public function mts_meta_operator_callback($post)
    {
        $operator_id=get_post_meta($post->ID,'id_ope',true);
        echo '<label for="model_id">Operator ID:</label>';
        echo '<input name="model_id" value="'.$operator_id.'" /><br />';
        $oper_base=get_post_meta($post->ID,'BaseID',true);
        echo '<label for="model_timages">Operator Base:</label>';
        echo '<input name="model_timages" value="'.$oper_base.'" /><br />';
        $oper_site=get_post_meta($post->ID,'Website',true);
        echo '<label for="BerthsBasic">Website:</label>';
        echo '<input name="BerthsBasic" value="'.$oper_site.'" /><br />';
        $oper_email=get_post_meta($post->ID,'Email',true);
        echo '<label for="BerthsMax">Email:</label>';
        echo '<input name="BerthsMax" value="'.$oper_email.'" /><br />';
        $oper_real=get_post_meta($post->ID,'RealTime',true);
        echo '<label for="BerthsStr">Real Time Availability:</label>';
        echo '<input name="BerthsStr" value="'.$oper_real.'" /><br />';
        $oper_update=get_post_meta($post->ID,'Updated_time',true);
        echo '<label for="BrandID">Updated time:</label>';
        echo '<input name="BrandID" value="'.$oper_update.'" /><br />';
             
    }
    
    
    //form for editing fields of boat model
    function mts_save_meta_boat_model( $post_id ) 
    {

	   //checking the fields if model is updated not through admin form
	   if ( ! isset( $_POST['moat_model_fields'] ) ) 
       {
		  return;
	   }


	   if ( ! wp_verify_nonce( $_POST['moat_model_fields'], 'moat_model_fields' ) ) 
       {
		return;
	   }

	   // If this is an autosave, our form has not been submitted, so we don't want to do anything.
	   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
       {
		  return;
	   }

	   // Check the user's permissions.
	   if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) 
       {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	   } else 
       {

		  if ( ! current_user_can( 'edit_post', $post_id ) ) 
          {
			return;
		  }
	   }


	   // Sanitize user input.
	   $BerthsBasic = sanitize_text_field( $_POST['BerthsBasic'] );
       $BerthsMax = sanitize_text_field( $_POST['BerthsMax'] );
       $BerthsStr = sanitize_text_field( $_POST['BerthsStr'] );
       $CabinsBasic = sanitize_text_field( $_POST['CabinsBasic'] );
       $CabinsStr = sanitize_text_field( $_POST['CabinsStr'] );
       $CabinsMax = sanitize_text_field( $_POST['CabinsMax'] );
       $Engine = sanitize_text_field( $_POST['Engine'] );
       $Length = sanitize_text_field( $_POST['Length'] );
       $Draft = sanitize_text_field( $_POST['Draft'] );
       $HullLength = sanitize_text_field( $_POST['HullLength'] );
       $ShowersBasic = sanitize_text_field( $_POST['ShowersBasic'] );
       $ShowersMax = sanitize_text_field( $_POST['ShowersMax'] );
       $ShowersStr = sanitize_text_field( $_POST['ShowersStr'] );
       $ToiletsBasic = sanitize_text_field( $_POST['ToiletsBasic'] );
       $ToiletsStr = sanitize_text_field( $_POST['ToiletsStr'] );
       $ToiletsMax = sanitize_text_field( $_POST['ToiletsMax'] );
       $BoatTypeID = sanitize_text_field( $_POST['BoatTypeID'] );
       $BrandID = sanitize_text_field( $_POST['BrandID'] );
       $FuelCapacity = sanitize_text_field( $_POST['FuelCapacity'] );
       $WaterCapacity = sanitize_text_field( $_POST['WaterCapacity'] );
       $WaterlineLength = sanitize_text_field( $_POST['WaterlineLength'] );
       $Weight = sanitize_text_field( $_POST['Weight'] );

	   // Update the meta field in the database.
	   update_post_meta( $post_id, 'BerthsBasic', $BerthsBasic );
       update_post_meta( $post_id, 'BerthsMax', $BerthsMax );
       update_post_meta( $post_id, 'BerthsStr', $BerthsStr );
       update_post_meta( $post_id, 'CabinsBasic', $CabinsBasic );
       update_post_meta( $post_id, 'CabinsStr', $CabinsStr );
       update_post_meta( $post_id, 'CabinsMax', $CabinsMax );
       update_post_meta( $post_id, 'Engine', $Engine );
       update_post_meta( $post_id, 'Length', $Length );
       update_post_meta( $post_id, 'Draft', $Draft );
       update_post_meta( $post_id, 'HullLength', $HullLength );
       update_post_meta( $post_id, 'ShowersBasic', $ShowersBasic );
       update_post_meta( $post_id, 'ShowersMax', $ShowersMax );
       update_post_meta( $post_id, 'ShowersStr', $ShowersStr );
       update_post_meta( $post_id, 'ToiletsBasic', $ToiletsBasic );
       update_post_meta( $post_id, 'ToiletsStr', $ToiletsStr );
       update_post_meta( $post_id, 'ToiletsMax', $ToiletsMax );
       update_post_meta( $post_id, 'BoatTypeID', $BoatTypeID );
       update_post_meta( $post_id, 'BrandID', $BrandID );
       update_post_meta( $post_id, 'FuelCapacity', $FuelCapacity );
       update_post_meta( $post_id, 'WaterCapacity', $WaterCapacity );
       update_post_meta( $post_id, 'WaterlineLength', $WaterlineLength );
       update_post_meta( $post_id, 'Weight', $Weight );
       
       
	   // Update the meta field in the database.
	   update_post_meta( $post_id, 'Updated_time', time() );
       
       
    }

    //form for dysplayng all fields for boat model
    public function mts_meta_moatmodel_callback($post)
    {
        $ModelID=get_post_meta($post->ID,'ModelID',true);
        echo '<a href="'.get_bloginfo('url').'/wp-admin/options-general.php?page=mts-import-data-beta&action=single_model&post_id='.$post->ID.'&id_model='.$ModelID.'">';
        echo '<h2>Update full data for this boat model</h2></a>';
        
        $nonce = wp_create_nonce( 'moat_model_fields' );

        echo "<input type='hidden' value='$nonce' name='moat_model_fields' />";
        
        echo '<label for="model_id">Model ID:</label>';
        echo '<input name="model_id" value="'.$ModelID.'" /><br />';
        $BerthsBasic=get_post_meta($post->ID,'BerthsBasic',true);
        echo '<label for="BerthsBasic">Berths Basic:</label>';
        echo '<input name="BerthsBasic" value="'.$BerthsBasic.'" /><br />';
        $BerthsMax=get_post_meta($post->ID,'BerthsMax',true);
        echo '<label for="BerthsMax">Berths Max:</label>';
        echo '<input name="BerthsMax" value="'.$BerthsMax.'" /><br />';
        $BerthsStr=get_post_meta($post->ID,'BerthsStr',true);
        echo '<label for="BerthsStr">Berths Str:</label>';
        echo '<input name="BerthsStr" value="'.$BerthsStr.'" /><br />';
        $CabinsBasic=get_post_meta($post->ID,'CabinsBasic',true);
        echo '<label for="CabinsBasic">CabinsBasic:</label>';
        echo '<input name="CabinsBasic" value="'.$CabinsBasic.'" /><br />';
        $CabinsMax=get_post_meta($post->ID,'CabinsMax',true);
         echo '<label for="CabinsMax">CabinsMax:</label>';
        echo '<input name="CabinsMax" value="'.$CabinsMax.'" /><br />';
        $CabinsStr=get_post_meta($post->ID,'CabinsStr',true);
         echo '<label for="CabinsStr">Cabins Str</label>';
        echo '<input name="CabinsStr" value="'.$CabinsStr.'" /><br />';
        $Engine=get_post_meta($post->ID,'Engine',true);
         echo '<label for="Engine">Engine:</label>';
        echo '<input name="Engine" value="'.$Engine.'" /><br />';
        $Length=get_post_meta($post->ID,'Length',true);
         echo '<label for="Length">Length:</label>';
        echo '<input name="Length" value="'.$Length.'" /><br />';
        $Draft=get_post_meta($post->ID,'Draft',true);
         echo '<label for="Draft">Draft:</label>';
        echo '<input name="Draft" value="'.$Draft.'" /><br />';
        $HullLength=get_post_meta($post->ID,'HullLength',true);
         echo '<label for="HullLength">Hull Length:</label>';
        echo '<input name="HullLength" value="'.$HullLength.'" /><br />';
        $FuelCapacity=get_post_meta($post->ID,'FuelCapacity',true);
         echo '<label for="FuelCapacity">Fuel Capacity:</label>';
        echo '<input name="FuelCapacity" value="'.$FuelCapacity.'" /><br />';
        $WaterCapacity=get_post_meta($post->ID,'WaterCapacity',true);
         echo '<label for="WaterCapacity">Water Capacity:</label>';
        echo '<input name="WaterCapacity" value="'.$WaterCapacity.'" /><br />';
        $WaterlineLength=get_post_meta($post->ID,'WaterlineLength',true);
         echo '<label for="WaterlineLength">Waterline Length:</label>';
        echo '<input name="WaterlineLength" value="'.$WaterlineLength.'" /><br />';
        $Weight=get_post_meta($post->ID,'Weight',true);
         echo '<label for="Weight">Weight:</label>';
        echo '<input name="Weight" value="'.$Weight.'" /><br />';
        
        
        $ShowersBasic=get_post_meta($post->ID,'ShowersBasic',true);
         echo '<label for="ShowersBasic">Showers Basic:</label>';
        echo '<input name="ShowersBasic" value="'.$ShowersBasic.'" /><br />';
        $ShowersMax=get_post_meta($post->ID,'ShowersMax',true);
         echo '<label for="ShowersMax">ShowersMax:</label>';
        echo '<input name="ShowersMax" value="'.$ShowersMax.'" /><br />';
        $ShowersStr=get_post_meta($post->ID,'ShowersStr',true);
         echo '<label for="ShowersStr">Showers Str:</label>';
        echo '<input name="ShowersStr" value="'.$ShowersStr.'" /><br />';
        $ToiletsBasic=get_post_meta($post->ID,'ToiletsBasic',true);
         echo '<label for="ToiletsBasic">Toilets Basic:</label>';
        echo '<input name="ToiletsBasic" value="'.$ToiletsBasic.'" /><br />';
        $ToiletsStr=get_post_meta($post->ID,'ToiletsStr',true);
         echo '<label for="ToiletsStr">Toilets Str:</label>';
        echo '<input name="ToiletsStr" value="'.$ToiletsStr.'" /><br />';
        $ToiletsMax=get_post_meta($post->ID,'ToiletsMax',true);
         echo '<label for="ToiletsMax">Toilets Max:</label>';
        echo '<input name="ToiletsMax" value="'.$ToiletsMax.'" /><br />';
        
        
        $BrandID=get_post_meta($post->ID,'BrandID',true);
        echo '<label for="BrandID">Brand ID:</label>';
        echo '<input name="BrandID" value="'.$BrandID.'" /><br />';
        $BoatTypeID=get_post_meta($post->ID,'BoatTypeID',true);
        echo '<label for="BoatTypeID">Boat Type ID:</label>';
        echo '<input name="BoatTypeID" value="'.$BoatTypeID.'" /><br />';
        $time_updated=get_post_meta($post->ID,'Updated_time',true);
         echo '<label for="updatedtime">Last updated time:</label>';
        echo '<input name="updatedtime" value="'.date('d/m/Y',$time_updated).'" /><br />';
        
    }
    
    
     public function mts_meta_booker_callback($post)
    {
        wp_nonce_field( 'mts_meta_box', 'mts_meta_box_nonce' );
        $id_boat=get_post_meta($post->ID,'id_boat',true);
        if(!empty($id_boat))
        {
            echo '<a href="'.get_bloginfo('url').'/wp-admin/options-general.php?page=mts-import-data-beta&action=single_scrape&post_id='.$post->ID.'&id_boat='.$id_boat.'">';
            echo '<h2>Import full data for this boat</h2></a>';
        }
        
        echo '<div style="float: left;width: 60%;">';
        foreach ($this->fields_boat as $label=>$field)
        {
            $field_value=get_post_meta($post->ID,$field,true);
            echo '<label for="'.$field.'">'. $label.'</label>';
            echo '<input name="'.$field.'" value="'.$field_value.'" /><br />';
        }
        
        $images=get_post_meta($post->ID,'BoatImages',true);
        echo '<p>Boat images URL: </p>';
        if(!empty($images))
        {
            for($i=0;$i<$images;$i++)
            {
               echo '<input name="BoatImage_'.$i.'" value="'.get_post_meta($post->ID,'BoatImage_'.$i,true).'" readonly="readonly"/><br />'; 
            }
        }
        
        
        $prices=get_post_meta($post->ID,'price',true);
        if(!empty($prices) && $prices>0)
        {
            echo '<p><strong>Prices</strong></p>';
            for($i=0;$i<$prices;$i++)
            {
                echo '<p> Date from: '.get_post_meta($post->ID, 'DateFrom_'.$i,true);
                echo ' Date to: '.get_post_meta($post->ID, 'DateTo_'.$i,true);
                echo ' Price: '.get_post_meta($post->ID, 'Price_'.$i,true);
                echo ' Currency: '.get_post_meta($post->ID, 'CurrencyCode_'.$i,true);
                echo '</p>';
            }
        }
        
        $discount=get_post_meta($post->ID,'discount',true);
        if(!empty($discount) && $discount>0)
        {
            echo '<p><strong>Discounts</strong></p>';
            for($i=0;$i<$discount;$i++)
            {
                echo '<p> Amount: '.get_post_meta($post->ID, 'Amount_'.$i,true);
                echo ' Name: '.get_post_meta($post->ID, 'Name_'.$i,true);
                echo ' Sailing Date From: '.get_post_meta($post->ID, 'SailingDateFrom_'.$i,true);
                echo ' Sailing Date To: '.get_post_meta($post->ID, 'SailingDateTo_'.$i,true);
                $valid=get_post_meta($post->ID, 'ValidDurationFrom_'.$i,true);
                if(!empty($valid))
                {
                   echo ' Valid DurationFrom : '.$valid; 
                }
                
                echo '</p>';
            }
        }
        echo '</div><div style="float: left; width: 30%;">';
        echo '<p><strong>Boat Equipments</strong></p>';
        $count_equipments=get_post_meta($post->ID,'equipment',true);
        if(!empty($count_equipments))
        {
            for($i=0;$i<$count_equipments;$i++)
            {
                $quan=get_post_meta($post->ID,'EquipQuantity_'.$i,true);
                if (!empty($quan))
                {
                    echo get_post_meta($post->ID,'EquipName_'.$i,true). '<br />';
                }
                        //get_post_meta($post->ID,'EquipID_'.$i,true);
                                    
            }
        }
        $extras=get_post_meta($post->ID,'full_extras',true);
        if(!empty($extras))
        {
            echo '<p><strong>Extras ID</strong></p>';
            for($i=0;$i<$extras;$i++)
            {
               $extra_id=get_post_meta($post->ID,'ExtraID_'.$i,true);
               echo $extra_id.' ';
            }
            echo '<br />';
        }
        echo '</div><div style="clear: both;"></div>';
         
         
    }
    
    

    public function mts_meta_box_callback($post)
    {
        wp_nonce_field( 'mts_meta_box', 'mts_meta_box_nonce' );
        $all_meta = get_post_meta($post->ID);


        ?>


        <?php
        //<a href="<?//=get_bloginfo('url')/wp-admin/options-general.php?page=mts-import-data-beta&when=now&action=single_scrape&post_id=<?=$post->ID
        //&id_boat=<?=$all_meta['id_boat'][0]&images_upload=1&characteristics=1&availability=1&days_plus=0&daysamount=7">
        //<h2>Import data for this boat</h2>
        //</a> ?>
        
        <?php

        echo "<table cellpadding='30'>";
        echo "    <tr>";
        echo "        <td style='vertical-align:top;'> <h3>General info</h3>";

        foreach ($this->general_info_keys as $key) {
            $value = $all_meta[$key][0];

            echo "<input type='text' id='{$key}' name='{$key}' value='" . esc_attr( $value ) . "'/>";

            echo "<label for='{$key}'> ";
            _e( $key, 'mts_boat_id' );
            echo "</label> <br/>";

        }
        echo '<label>Sum: '.$all_home = get_post_meta($post->ID,'homeport_num',true).'</label><br />';
        
        $all_home = get_post_meta($post->ID,'homeport_num',true);
            if(!empty($all_home) && $all_home>1)
            {
                for ($i=0;$i<$all_home;$i++)
                {
                    $name_home=get_post_meta($post->ID,'homeport_'.$i,true);
                    $name_base=get_post_meta($post->ID,'homeport_id_'.$i,true);
                    $name_country=get_post_meta($post->ID,'homeport_id_country_'.$i,true);
                    $name_price=str_replace("\"","",get_post_meta($post->ID,'price_'.$i,true));
                    echo '<input name="homeport_'.$i.'" value="'.$name_home.'" />';  
                    echo '<label for="homeport_'.$i.'">Homeport </label><br />'; 
                    echo '<input name="homeport_id_'.$i.'" value="'.$name_base.'" />';  
                    echo '<label for="homeport_id_'.$i.'">ID base </label><br />'; 
                    echo '<input name="homeport_id_country_'.$i.'" value="'.$name_country.'" />';  
                    echo '<label for="homeport_id_country_'.$i.'">ID country </label><br />'; 
                    echo '<input name="price_'.$i.'" value="'.$name_price.'" />';  
                    echo '<label for="price_'.$i.'">Prices </label><br />'; 
                }
            }

            
            
            echo '<p><h3>Availability</h3></p>';
          
            echo '<label for="available">Available: </label>'; 
            echo '<input name="available" value="'.get_post_meta($post->ID,'available',true).'" /><br />';  
           
           echo '<label for="available">Date available: </label>'; 
           echo '<input name="date_available" value="'.get_post_meta($post->ID,'date_available',true).'" /><br />'; 
           $price_fields=array(0=>'id_base',1=>'datestart',2=>'dateend',3=>'discount',
                            4=>'oldprice',5=>'newprice',6=>'def_cur',7=>'id_ope',8=>'rate');
                         
                foreach ($price_fields as $key=>$price)
                {
                    $name=get_post_meta($post->ID,$price,true); 
                    echo '<input name="'.$price.'" value="'.$name.'" />';  
                    echo '<label for="'.$price.'">'.$price.'</label><br />'; 
                }
            

        echo          "</td>";
        echo "        <td style='vertical-align:top;'> <h3>Characteristics</h3><br />";

         $draft=get_post_meta($post->ID,'draft',true);
         $engine=get_post_meta($post->ID,'engine',true);
         echo '<label for="char_val_draft">Draft</label>';
         echo '<input name="char_val_engine" value="'.$draft.'" /><br />';   
         echo '<label for="char_val_draft">Engine</label>';
         echo '<input name="char_val_engine" value="'.$engine.'" /><br />';   
            
        echo '<p><h3>Equipment</h3></p>';
        $all_charact = get_post_meta($post->ID,'chars',true);
        echo '<label>Total: '.$all_charact.'</label>';
            if(!empty($all_charact))
            {
            for ($i=0;$i<$all_charact;$i++)
            {
                $key_val='char_val_'.$i;
                $key_name='char_name_'.$i;
                $name_char=get_post_meta($post->ID,$key_name,true);
                $name_val=get_post_meta($post->ID,$key_val,true);
                echo '<label for="char_val_'.$i.'"">'.$name_char.'</label>';
                echo '<input name="char_val_'.$i.'" value="'.$name_val.'" /><br />';   
            }
            }
            
            
        echo '<p><h3>Images original links</h3>';
         $image = get_post_meta($post->ID,'images_link',true);
        $all_images = get_post_meta($post->ID,'images_total',true);
            if(!empty($image))
            {
                echo '<input style="width: 380px" name="images_link" value="'.$image.'" readonly=readonly /><br />';   
            }
            else
            {
                for ($i=0;$i<$all_images;$i++)
                {
                    $key_val='images_link_'.$i;
                    $name_val=get_post_meta($post->ID,$key_val,true);
                    echo '<input style="width: 380px" name="images_link_'.$i.'" value="'.$name_val.'" readonly=readonly /><br />';   
                }
            }
            
        echo '<p><h3>Plan link</h3></p>';
        $plan = get_post_meta($post->ID,'plan_link',true);
        $plans = get_post_meta($post->ID,'plans_num',true);
        if(!empty($plan))
        {
            echo '<input name="plan" value="'.$plan.'" readonly=readonly /><br />';   
        }
        else
        {
            for ($i=0;$i<$all_images;$i++)
            {
                $key_val='plan_link_'.$i;
                $name_val=get_post_meta($post->ID,$key_val,true);
                echo '<input style="width: 380px" name="plan_link_'.$i.'" value="'.$name_val.'" readonly=readonly /><br />';   
            }
        }
        
        
         echo '<p><h3>Mandatory extra prices</h3></p>';
         $all_mand_extra=get_post_meta($post->ID, 'mand_extra',true);
         $all_add_extra=get_post_meta($post->ID, 'add_extra',true);
         for ($i=0;$i<$all_mand_extra;$i++)
         {
             echo '<label for="mand_name_'.$i.'">'.get_post_meta($post->ID, 'mand_name_'.$i,true).'</label> ';  
             echo '<label for="mand_price_'.$i.'">price: '.get_post_meta($post->ID, 'mand_price_'.$i,true).'</label> '; 
             echo '<label for="mand_per_'.$i.'">'.get_post_meta($post->ID, 'mand_per_'.$i,true).'</label> ';
             echo '<label for="mand_quantity_'.$i.'">quantity: '.get_post_meta($post->ID, 'mand_quantity_'.$i,true).'</label> ';
             echo '<label for="mand_rate_'.$i.'">rate: '.get_post_meta($post->ID, 'mand_rate_'.$i,true).'</label> ';
             echo '<label for="mand_contry_'.$i.'">country: '.get_post_meta($post->ID, 'mand_country_'.$i,true).'</label><br />';   
         }
         
         
         echo '<p><h3>Additional extra prices</h3></p>';
         for ($i=0;$i<$all_add_extra;$i++)
         {
             echo '<label for="add_name_'.$i.'"">'.get_post_meta($post->ID, 'add_name_'.$i,true).'</label> ';  
            echo '<label for="add_price_'.$i.'">price: '.get_post_meta($post->ID, 'add_price_'.$i,true).'</label> '; 
             echo '<label for="add_per_'.$i.'">'.get_post_meta($post->ID, 'add_per_'.$i,true).'</label> ';
             echo '<label for="add_quantity_'.$i.'">quantity: '.get_post_meta($post->ID, 'add_quantity_'.$i,true).'</label> ';
             echo '<label for="add_rate_'.$i.'">rate: '.get_post_meta($post->ID, 'add_rate_'.$i,true).'</label> ';
             echo '<label for="add_contry_'.$i.'">country: '.get_post_meta($post->ID, 'add_country_'.$i,true).'</label><br />';        
         }


        echo   "</td>";
        echo "</tr>";
        echo "</table>";



        
    }
     


 
     function cron_add_weekly( $schedules ) {
        // Adds once weekly to the existing schedules.
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display' => __( 'Once Weekly' )
        );
        return $schedules;
     }


 
     function cron_add_5min( $schedules ) {
        $schedules['5min'] = array(
            'interval' => 5*60,
            'display' => __( 'Once per 5 minutes' )
        );
        return $schedules;
     }


    public function mts_import_options()
    {
        if ( !current_user_can( 'activate_plugins' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }
        $message=$this->message;
        include __DIR__."/options.php";
    }

}

$wpMTS_BETA = new  MTS_IMPORT_BETA;
