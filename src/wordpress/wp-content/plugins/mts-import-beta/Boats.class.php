<?php

class Boats
{

    protected $h;
    public $agent_id;

    public function __construct()
    {
        //include_once 'Helper_API.php';

        //$this->h                     = new Helper;
        $this->mts_query             = array();
        $this->mts_query['lg']       = '0';
        $this->mts_query['refagt']   = 'wxft6043';
        $this->agent_id = '6043';
        $this->booker_user='18abb2dc5849491eaaa06ab3d4fb1dc2';
        $this->booker_pass='2ec90d50df594e419c3e52088f947556';

    }
    
    /****************************functions for BoatBooker database************************/

    
    
    
    public function getBoatsOperator()
    {
        global $wpdb;
        
        
        $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True';
                //'&availDatePeriodFrom='.date('Y-m-d').'&availDatePeriodTo='.date("Y-m-d", mktime(0,0,0,date('m')+1,date('d')+7,date(Y)));
        
        
        $new=false;
        $count=0;
        $all=0;
        $summ_boats=0;
        
        
        $args = array(
                      'post_type' => 'boat_ope',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 3,
                      'meta_query' => array(
	                   array(
		                  'key' => 'new_boats',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found_ope = get_posts($args);

        
        
        foreach($found_ope as $key=>$ope)
        {
            $all++;
            $ope_id = get_post_meta($ope->ID,"id_ope",true);
            $ope_term = get_post_meta($ope->ID,"id_term",true);
            $uploaded= get_post_meta($ope->ID,"uploaded",true);
            $total_boats=get_post_meta($ope->ID,"total_boats",true);
            delete_post_meta($ope->ID,"finished");
             delete_post_meta($ope->ID,"uploaded_boats");
            //echo 'Uploaded: '.$uploaded.' Total: '.$total_boats. ' '.$ope_id.'<br />';
            if(empty($uploaded))
            {
              $uploaded=0;  
            }
            if(empty($total_boats))
            {
                $total_boats=0;
            }
            //if($total_boats==0)
            //{

              $query=$query_start."&loadBoatsForFleetOperators=".$ope_id; 
            $boats=json_decode(file_get_contents($query));
          
            if (count($boats->Boats)>0)
            {
                $total_boats=count($boats->Boats);
                update_post_meta($ope->ID, 'total_boats',count($boats->Boats));
                echo $uploaded. ' '.$total_boats.'<br /> ';
                if ($uploaded<$total_boats)
                {
                    $uploaded=0;
                    foreach ($boats->Boats as $num=>$boat)
                    {
                        echo $uploaded.'<br />';
                    $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => -1,
                      'meta_query' => array(
	                   array(
		                  'key' => 'id_boat',
                          'value'=>$boat->ID,
		                   'compare' => '=',
	                       )));
                     print_r($found_id);
                     echo '<br />';
                     $found_id = get_posts($args);
                     if(empty($found_id) && count($found_id)==0)
                     {
                        $uploaded=$uploaded+1;
                        update_post_meta($ope->ID, 'uploaded',$uploaded);
                        $new_post = array(
                             'post_status' => 'publish',
                             'post_type'   => 'boat_post',
                             'post_title'  =>  $boat->ID
                             );
                            $post_id = wp_insert_post($new_post);
                            if($post_id>0)
                            {
                                $count++;
                                update_post_meta($post_id, 'id_boat',$boat->ID);
                                update_post_meta($post_id, 'BerthsBasic',$boat->BerthsBasic);
                                update_post_meta($post_id, 'BerthsMax',$boat->BerthsMax);
                                update_post_meta($post_id, 'BerthsStr',$boat->BerthsStr);
                                update_post_meta($post_id, 'CabinsBasic',$boat->CabinsBasic);
                                update_post_meta($post_id, 'CabinsMax',$boat->CabinsMax);
                                update_post_meta($post_id, 'CabinsStr',$boat->CabinsStr);
                                update_post_meta($post_id, 'ToiletsStr',$boat->CabinsBasic);
                                update_post_meta($post_id, 'ToiletsBasic',$boat->CabinsMax);
                                update_post_meta($post_id, 'ToiletsMax',$boat->CabinsStr);
                                update_post_meta($post_id, 'Commission',$boat->Commission);
                                update_post_meta($post_id, 'Engine',$boat->Engine);
                                update_post_meta($post_id, 'Draft',$boat->Draft);
                                update_post_meta($post_id, 'HasCrew',$boat->HasCrew);
                                update_post_meta($post_id, 'Length',$boat->Length);
                                update_post_meta($post_id, 'Name',$boat->Name);
                                update_post_meta($post_id, 'YearBuilt',$boat->YearBuilt);
                                update_post_meta($post_id, 'ModelID',$boat->ModelID);
                                $cat_ids=array(0=>$ope_term);
                                $cat_ids = array_map( 'intval', $cat_ids );
                                $cat_ids = array_unique( $cat_ids );
                                wp_set_object_terms($post_id,$cat_ids,'id_ope');
                     }
            
                    }
                    else
                    {
                        $uploaded=$uploaded+1; 
                        echo $uploaded.'<br />'; 
                        update_post_meta($ope->ID,"uploaded",$uploaded);
                    }
                    if($count>29)
                    {
                        break;
                    }
                }
           
            }
            else
           {
                update_post_meta($ope->ID, 'new_boats',1);
           }
            }
             else
            {
                update_post_meta($ope->ID, 'total_boats',0);
            }
             
           //}
            if($count>29)
            {
                break;
            }
        }


        
        return $count;

    }
    
    
    /* import all mages and make featured image for every boat */
    public function make_image()
    {
         $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True&loadBoatModels=True';
         require_once(ABSPATH . 'wp-admin/includes/media.php');
         require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
      
        $count=0;
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 2,
                      'meta_query' => array(
	                   array(
		                  'key' => 'image_uploaded',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            for($j=0;$j<count($found);$j++)
            {
                $boat_id = get_post_meta($found[$j]->ID,"id_boat",true);
                if(!empty($boat_id))
                {
                    $query=$query_start.'&loadSpecificBoats='.$boat_id;
                    $charact=json_decode(file_get_contents($query));
                    if(count($charact->Boats)==1 && isset($charact->Boats[0]))
                    {
                       $boat_desc=$charact->Boats[0];
                       if(empty($boat_desc->BoatImages))
                       {
                        update_post_meta($found[$j]->ID,'image_uploaded',1);
                            /*foreach($charact->BoatModels as $model)
                            {
                                if($model->ID==$boat_desc->ModelID)
                                {
                                    foreach($model->BoatImages as $modelimage)
                                    {

                                $content=file_get_contents($modelimage->ImageURL);
                                $data = base64_decode($content);
                                $upload_dir = wp_upload_dir();


                                $my_img = imagecreatefromstring($content);
                                if ($my_img !== false) 
                                {
                                    header( "Content-type: image/gif" );
                                    $save='model'.time().".gif";

                                    imagegif($my_img, $upload_dir['path'] . '/' . basename($save), 0, NULL);
                                    chmod( $upload_dir['path'] . '/' . basename($save),0755);
                                    imagedestroy($my_img);
                                    //temporary file from uploaded folder
                                    $tmp = download_url( $upload_dir['url'] . '/' . basename($save));
                                    $desc = "Photo for model - ".$found[$j]->post_title;
                                    $file_array = array();
                                    $file_array['name'] = basename('PhotoBoatModel'.time().'.gif');
                                    $file_array['tmp_name'] = $tmp;
                                    if (!is_wp_error( $tmp ) ) 
                                    {

                                        $id = media_handle_sideload( $file_array, $found[$j]->ID, $desc );

                                        if (!is_wp_error($id) )
                                        {
                                            $thubnail=set_post_thumbnail( $found[$j]->ID, $id  );
                                            update_post_meta($found[$j]->ID,'image_uploaded',1);
                                             echo '<p>Boat number <a href="'.get_permalink($found[$j]->ID).'">'.$found[$j]->ID.'</a> was updated!</p>';
                  
                                            break 1;
                                        }
                                        }
                                    }
                                    }
                                }
                            }*/
                  
                       }
                       else
                       {
                            foreach($boat_desc->BoatImages as $desc_image)
                            {
                                echo $desc_image->ImageURL.'<br />';
                                /*$content=file_get_contents($desc_image->ImageURL);
                                $data = base64_decode($content);
                                $upload_dir = wp_upload_dir();


                                $my_img = imagecreatefromstring($content);
                                if ($my_img !== false) 
                                {
                                    header( "Content-type: image/gif" );
                                    $save='model'.time().".gif";

                                    imagegif($my_img, $upload_dir['path'] . '/' . $save, 0, NULL);
                                    //chmod( $upload_dir['path'] . '/' . basename($save),0755);
                                    imagedestroy($my_img);
                                    //temporary file from uploaded folder
                                    $tmp = download_url( $upload_dir['url'] . '/' . basename($save));
                                    $desc = "Photo for model - ".$found[$j]->post_title;
                                    $file_array = array();
                                    $file_array['name'] = basename('PhotoBoatModel'.time().'.gif');
                                    $file_array['tmp_name'] = $tmp;
                                    if (!is_wp_error( $tmp ) ) 
                                    {

                                        $id = media_handle_sideload( $file_array, $found[$j]->ID, $desc );
*/
$content=file_get_contents($desc_image->ImageURL);
                        $data = base64_decode($content);
        $upload_dir = wp_upload_dir();

        //creating new gif image from url
        $new_img = @imagecreatefromstring($content);
        if ($new_img !== false) 
        {
            header( "Content-type: image/gif" );
            $save_name='PhotoBoat_'.$boat_id.".gif";
            $image_value =imagegif($new_img, $upload_dir['path'] . '/' . $save_name);
            //chmod( $upload_dir['path'] . '/' . basename($save_name),0755);
            imagedestroy($new_img);
            $filename=$upload_dir['url'] . '/' . basename($save_name);
            //$tmp = download_url( $upload_dir['url'] . '/' . basename($save_name));
            //$desc = "Photo for model - ".$title;
            //$file_array = array();
            //$file_array['name'] = basename('PhotoBoatModel_'.$models[$t]->ID.'.gif');
            //$file_array['tmp_name'] = $tmp;
            if (!is_wp_error( $tmp ) ) 
            {
                //id of attached image to post
                $filetype = wp_check_filetype( basename($filename), null );



// Prepare an array of post data for the attachment.
$attachment = array(
	'guid'           => $upload_dir['url'] . '/' . basename($filename), 
	'post_mime_type' => $filetype['type'],
	'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($filename) ),
	'post_content'   => '',
	'post_status'    => 'inherit'
);

// Insert the attachment.
$attach_id = wp_insert_attachment( $attachment, $filename, $found[$j]->ID);



// Generate the metadata for the attachment, and update the database record.
$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
wp_update_attachment_metadata( $attach_id, $attach_data );
                                        if (!is_wp_error($attach_id) )
                                        {
                                            $thubnail=set_post_thumbnail( $found[$j]->ID,$attach_id);
                                            update_post_meta($found[$j]->ID,'image_uploaded',1);
                                             echo '<p>Boat number <a href="'.get_permalink($found[$j]->ID).'">'.$found[$j]->ID.'</a> was updated!</p>';
                  
                                            break 1;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else
                    {
                        wp_delete_post($found[$j]->ID,true);
                    }
                }
            }
        }
    }
    
    //uploading image  and making image as featured for post
    function save_image_post($postid,$image,$featured,$title)
    {
        $content=file_get_contents($image);
        $data = base64_decode($content);
        $upload_dir = wp_upload_dir();

        //creating new gif image from url
        $new_img = imagecreatefromstring($content);
        if ($new_img !== false) 
        {
            header( "Content-type: image/gif" );
            $save_name='model'.time().".gif";
            $image_value =imagegif($new_img, $upload_dir['path'] . '/' . basename($save_name), 0, NULL);
            //chmod( $upload_dir['path'] . '/' . basename($save_name),0755);
            imagedestroy($new_img);
            
            //temporary file from uploaded folder
            $tmp = download_url( $upload_dir['url'] . '/' . basename($save_name));
            $desc = "Photo for model - ".$title;
            $file_array = array();
            $file_array['name'] = basename('PhotoBoatModel'.time().'.gif');
            $file_array['tmp_name'] = $tmp;
            if (!is_wp_error( $tmp ) ) 
            {
                //id of attached image to post
                $id = media_handle_sideload( $file_array, $postid, $desc );
                if (!is_wp_error($id) )
                {
                    echo '<p>Boat number <a href="'.get_permalink($postid).'">'.$postid.'</a> was updated!</p>';
                    update_post_meta($postid,'image_uploaded',1);
                    if($featured==true)
                    {
                        $thubnail=set_post_thumbnail($postid, $id  );                        
                    }
                    return true;
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
        else
        {
            return false;
        }
    }
    
    
    /*  import of all boat characterristics  */
    public function getBoatCharacteristics()
    {
        global $wpdb;
         $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True';
      
        $count=0;
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 8,
                      'meta_query' => array(
	                   array(
		                  'key' => 'country',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            foreach ($found as $key=>$post)
            {
                 $boat_id = get_post_meta($post->ID,"id_boat",true);
                 if(!empty($boat_id))
                 {
                    $query=$query_start.'&loadSpecificBoats='.$boat_id;
                    //echo $query.'<br />';
                    $charact=json_decode(file_get_contents($query));
                    if(count($charact->Boats)==1 && isset($charact->Boats[0]))
                    {
                       $boat_chars=$charact->Boats[0];
                       $boat_base=array();
                       foreach ($boat_chars->BoatBases as $base)
                       {
                            $boat_base[]=$base->ID;
                       } 
                       $cat_ids=array();
                       foreach ($boat_base as $key=>$id)
                       {
                            $args2 = array(
                                'post_type' => 'boat_base',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'posts_per_page' => -1,
                                'meta_query' => array(
	                               array(
                                        'key' => 'id_base',
                                        'value'=>$id,
		                              'compare' => '=',
	                               )));
                            $found_base = get_posts($args2);
                            if(!empty($found_base))
                            {
                                foreach($found_base as $key2=>$post2)
                                {
                                    $title_base=get_post_meta($post2->ID,'title_base',true);
                                    $tax_loc = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$title_base."'", OBJECT );
                                    foreach ($tax_loc as $key3=>$base)
                                    {
                                        $term_base = get_option( "base_meta_".$base->term_id);
                                        if(!empty($term_base) && ($term_base==$id))
                                        {
                                            $cat_ids[]=$base->term_id;
                                            $country_term = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$base->term_id, OBJECT );
                                            if(count($country_term)>0)
                                            {
                                                foreach($country_term as $key3=>$country_id)
                                                {
                                                    if($country_id->taxonomy=='country')
                                                    {
                                                            $cat_ids[]=$country_id->parent;
                                                    }
                                                } 
                                            }
                                            $country_term2= $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$country_id->parent, OBJECT );
                                            if(count($country_term2)>0)
                                            {
                                                foreach($country_term2 as $key4=>$country_id2)
                                                {
                                                    if($country_id2->taxonomy=='country')
                                                    {
                                                        $cat_ids[]=$country_id2->parent;
                                                    }
                                                }
                                            }
                                                     
                                        }
                                    }       
                                    if(count($cat_ids)>0)
                                    {
                                        $cat_ids = array_map( 'intval', $cat_ids );
                                        $cat_ids = array_unique( $cat_ids );
                                        wp_set_object_terms($post->ID,$cat_ids,'country');
                                    }
                                }
                            }
                               
                       }
                       update_post_meta($post->ID, 'BoatImages',count($boat_chars->BoatImages));
                       $i=0;
                       foreach ($boat_chars->BoatImages as $image)
                       {
                         update_post_meta($post->ID, 'BoatImage_'.$i,$image->ImageURL);
                         $i++;
                       }
                       update_post_meta($post->ID, 'country','filled');
  
                       echo '<p>Boat number <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> was updated!</p>';
                    }
                    else
                    {
                        echo 'Boat was deleted <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a><br />';
                        wp_delete_post($post->ID,true);
                    }
                    
                    //echo '<br />';
                    $count++;
                 }
            }
        }
        
        return $count;
        
    }
    
    
    //make the same tempate for all boats
    public function makeBoattemplate()
    {
        $args = array(
                      'post_type' => 'boat_page',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 50,
                      'meta_query' => array(
	                   array(
		                  'key' => 'new-content',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            foreach ($found as $key=>$post)
            {
                $update_content = array(
                    'ID'           => $post->ID,
                    'post_content' => '[vc_row][vc_column width="2/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"][boat_images]'.
                                        '[boat_price][boat_extra][boat_equipment][boat_map][/vc_column_text][/vc_column]'.
                                        '[vc_column width="1/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"]'.
                                        '[boat_availability][/vc_column_text][/vc_column][/vc_row]');
                $id_post=wp_update_post($update_content);
                if($id_post>0)
                {
                    update_post_meta($id_post,'new-content','new');
                    echo 'Boat  <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> has new template<br />';
                }
            }
        }
    }
    
    
    //make the same tempate for all boats
    public function makeBoatslug()
    {
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 50,
                      'meta_query' => array(
	                   array(
		                  'key' => 'long_title',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            foreach ($found as $key=>$post)
            {
                $type_name='';
                $boat_type = wp_get_object_terms($post->ID,  'bt_type' );
                if ( ! is_wp_error($boat_type) ) 
                {
                    $type_name=$boat_type[0]->name;
                }
                if(!empty($type_name))
                {
                    $model_name='';
                    $boat_model = wp_get_object_terms($post->ID,  'bt_model' );
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
                    if(!empty($model_name))
                    {
                        $boat_id=get_post_meta($post->ID,'id_boat',true);
                        $update_content = array(
                            'ID'           => $post->ID,
                            'post_name' => sanitize_title($model_name.' '.$type_name.' (ref-'.$boat_id.')'),
                            'post_title' => $model_name.' '.$type_name);
                        $id_post=wp_update_post($update_content);
                        if($id_post>0)
                        {
                            update_post_meta($id_post,'long_title','new');
                            echo 'Boat  <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> has new template<br />';
                        }
                    }
                }
            }
        }
    }
    
    //get full data for single way
    public function getBoatFullData($post_id,$boat_id)
    {
        global $wpdb;
        $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True&loadBoatBrands=True&loadBoatTypes=True&loadBoatModels=True';
        
        $type_name='';
        $model_name='';
        //get brand, nodel an type of boat
        /*if(!empty($boat_id))
        {
            //request from Booker database for one boat
            $query=$query_start.'&loadSpecificBoats='.$boat_id;
            echo $query.'<br />';
            $boat=json_decode(file_get_contents($query));
            if (isset($boat->Boats[0]))
            {
                if(count($boat->Boats[0])==1)
                {
                    $model_id=$boat->Boats[0]->ModelID;
                    update_post_meta($post_id,"ModelID",$model_id);
                    $type_id=get_post_meta($post_id,"BoatTypeID",true);
                    $brand_id=get_post_meta($post_id,"BrandID",true);
                    update_post_meta($post_id,"ModelID",$model_id);
                    update_post_meta($post_id,"Engine",$boat->Boats[0]->Engine);
                    update_post_meta($post_id,"Draft",$boat->Boats[0]->Draft);
                    update_post_meta($post_id,"BerthsMax",$boat->Boats[0]->BerthsMax);
                    update_post_meta($post_id,"BerthsStr",$boat->Boats[0]->BerthsStr);
                    update_post_meta($post_id,"BerthsBasic",$boat->Boats[0]->BerthsBasic);
                    update_post_meta($post_id,"CabinsStr",$boat->Boats[0]->CabinsStr);
                    update_post_meta($post_id,"CabinsMax",$boat->Boats[0]->CabinsMax);
                    update_post_meta($post_id,"CabinsBasic",$boat->Boats[0]->CabinsBasic);    
                        
                    //find boat model
                    $arr_model=array(
                                'post_type' => 'boat_model',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'posts_per_page' => -1,
                                    'meta_query' => array(
	                                array(
		                              'key' => 'ModelID',
                                      'value'=>$model_id,
		                              'compare' => '=',
	                           )));
                        $models=get_posts($arr_model);
                        if(!empty($models) && count($models)==1)
                        {
                            if(empty($type_id))
                            {
                                $type_id=get_post_meta($models[0]->ID,"BoatTypeID",true); 
                                update_post_meta($found[$i]->ID,"BoatTypeID",$type_id);
                            }
                            if(empty($brand_id))
                            {
                                $brand_id=get_post_meta($models[0]->ID,"BrandID",true); 
                                update_post_meta($found[$i]->ID,"BrandID",$type_id);
                            }
                            $model_name=$models[0]->post_title;
                            $term_model = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$models[0]->post_title."'", OBJECT );
                            if(!empty($term_model))
                            {
                                foreach ($term_model as $key2=>$cat_model)
                                {
                                    $tax_model = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$cat_model->term_id, OBJECT);
                                    if(!empty($tax_model))
                                    {
                                        foreach ($tax_model as $key3=>$tax_name)
                                        {
                                            if($tax_name->taxonomy=='bt_model')
                                            {
                                                $model_name=$models[0]->post_title;
                                                $id_cat_model=array();
                                                $id_cat_model[]=$tax_name->term_id;
                                                $cat_ids = array_map( 'intval', $id_cat_model );
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($post_id,$cat_ids,'bt_model');
                                            }
                                        }
                                    }
                                } 
                            }
                            else
                            {
                                $tax_boat_model=wp_insert_term($models[0]->post_title,'bt_model',array('slug'=>sanitize_title($models[0]->post_title)));
                                if (!is_wp_error($tax_boat_model))
                                {
                                    $model_name=$models[0]->post_title;
                                    $id_cat_model=array();
                                    $id_cat_model[]=$tax_boat_model['term_id'];
                                    $cat_ids = array_map( 'intval', $id_cat_model );
                                    $cat_ids = array_unique( $cat_ids );
                                    wp_set_object_terms($post_id,$cat_ids,'bt_model');
                                }
                            }

                            /*if(!empty($type_id))  
                            {  
                                $arrg_type=array('post_type' => 'boat_type',
                                        'orderby'=>'ID',
                                        'order' => 'ASC',
                                        'posts_per_page' => -1,
                                        'meta_query' => array(
                                            array(
                                                'key' => 'BoatTypeID',
                                                'value'=>$type_id,
                                                'compare' => '=')));
                                $found_type=get_posts($arrg_type);
                                if(!empty($found_type)&& count($found_type)==1)
                                {
                                    $type_name=$found_type[0]->post_title;
                                    $term_model = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$found_type[0]->post_title."'", OBJECT );
                                    if(!empty($term_model))
                                    {
                                        $type_exist=false;
                                        foreach ($term_model as $key2=>$cat_model)
                                        {
                                            $tax_model = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$cat_model->term_id, OBJECT);
                                            if(!empty($tax_model))
                                            {
                                                foreach ($tax_model as $key3=>$tax_name)
                                                {
                                                    if($tax_name->taxonomy=='bt_type')
                                                    {
                                                        $type_exist=true;
                                                        $id_cat_model=array();
                                                        $id_cat_model[]=$tax_name->term_id;
                                                        $cat_ids = array_map( 'intval', $id_cat_model );
                                                        $cat_ids = array_unique( $cat_ids );
                                                        wp_set_object_terms($post_id,$cat_ids,'bt_type');
                                                    }
                                                    elseif($tax_name->taxonomy=='bt_model')
                                                    {
                                                        wp_delete_term($tax_name->term_id,'bt_model');
                                                    }
                                                }
                                            }
                                        }
                                        if($type_exist==false) 
                                        {
                                            $type_name=$found_type[0]->post_title;
                                            $tax_boat_model=wp_insert_term($found_type[0]->post_title,'bt_type',array('slug' => strtolower($found_type[0]->post_title)));
                                            if (!is_wp_error($tax_boat_model))
                                            {
                                                $type_name=$found_type[0]->post_title;
                                                $id_cat_model=array();
                                                $id_cat_model[]=$tax_boat_model['term_id'];
                                                $cat_ids = array_map( 'intval', $id_cat_model );
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($post_id,$cat_ids,'bt_type');
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $tax_boat_model=wp_insert_term($found_type[0]->post_title,'bt_type',array('slug' => strtolower($found_type[0]->post_title)));
                                        if (!is_wp_error($tax_boat_model))
                                        {
                                            $type_name=$found_type[0]->post_title;
                                            $id_cat_model=array();
                                            $id_cat_model[]=$tax_boat_model['term_id'];
                                            $cat_ids = array_map( 'intval', $id_cat_model );
                                            $cat_ids = array_unique( $cat_ids );
                                            wp_set_object_terms($post_id,$cat_ids,'bt_type');
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $count_types=count($boat->BoatTypes);
                                $types=$boat->BoatTypes;
                                for($t=0;$t<$count_types;$t++)
                                {
                                    if($type_id==$types[$t]->ID)
                                    {
                                        $new_type=array(
                                            'post_status' => 'publish',
                                            'post_type'   => 'boat_type',
                                            'post_title'  => $types[$t]->Name->EN);
                                        $post_type_id = wp_insert_post($new_type); 
                                        $type_name=$types[$t]->Name->EN;
                                        if($post_type_id>0)
                                        {
                                            update_post_meta($post_type_id,'BoatTypeID',$found_type[0]->ID);
                                                //creating new category of boat type
                                            $tax_boat_type=wp_insert_term($types[$t]->Name->EN,'bt_type',strtolower($types[$t]->Name->EN));
                                            if (!is_wp_error($tax_boat_type))
                                            {
                                                echo '<p>New Type is added: '.$types[$t]->Name->EN.'</p>';
                                                $id_cat_type=array();
                                                $id_cat_type[]=$tax_boat_type['term_id'];
                                                $cat_ids = array_map( 'intval', $id_cat_type);
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($post_id,$cat_ids,'bt_type');
                                            }
                                        }
                                        break 1;
                                    }
                                }
                            }
                            if(!empty($brand_id))
                            {
                                //find boat brand
                                $arrg_brand=array('post_type' => 'boat_brand',
                                                        'orderby'=>'ID',
                                                            'order' => 'ASC',
                                                            'posts_per_page' => -1,
                                                            'meta_query' => array(
	                                                           array(
		                                                      'key' => 'BrandID',
                                                                'value'=>$brand_id,
		                                                      'compare' => '=')));
                                $found_brand=get_posts($arrg_brand);
                                if(!empty($found_brand)&& count($found_brand)==1)
                                {
                                    $term_model = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$found_brand[0]->post_title."'", OBJECT );
                                    if(!empty($term_model))
                                    {
                                        $brand_exist=false;
                                        foreach ($term_model as $key2=>$cat_model)
                                        {
                                            $tax_model = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$cat_model->term_id, OBJECT);
                                            if(!empty($tax_model))
                                            {
                                                foreach ($tax_model as $key3=>$tax_name)
                                                {
                                                    if($tax_name->taxonomy=='bt_brand')
                                                    {
                                                        $brand_exist=true;
                                                        $id_cat_model=array();
                                                        $id_cat_model[]=$tax_name->term_id;
                                                        $cat_ids = array_map( 'intval', $id_cat_model );
                                                        $cat_ids = array_unique( $cat_ids );
                                                        wp_set_object_terms($post_id,$cat_ids,'bt_brand');
                                                    }
                                                    elseif($tax_name->taxonomy=='bt_model')
                                                    {
                                                        wp_delete_term($tax_name->term_id,'bt_model');
                                                    }
                                                }
                                            }
                                        }
                                        if($brand_exist==false) 
                                        {
                                            $tax_boat_model=wp_insert_term($found_brand[0]->post_title,'bt_brand',array('slug' => sanitize_title($found_brand[0]->post_title)));
                                            if (!is_wp_error($tax_boat_model))
                                            {
                                                $id_cat_model=array();
                                                $id_cat_model[]=$tax_boat_model['term_id'];
                                                $cat_ids = array_map( 'intval', $id_cat_model );
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($post_id,$cat_ids,'bt_brand');
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $tax_boat_model=wp_insert_term($found_brand[0]->post_title,'bt_brand',array('slug' => strtolower($found_brand[0]->post_title)));
                                        if (!is_wp_error($tax_boat_model))
                                        {
                                            $id_cat_model=array();
                                            $id_cat_model[]=$tax_boat_model['term_id'];
                                            $cat_ids = array_map( 'intval', $id_cat_model );
                                            $cat_ids = array_unique( $cat_ids );
                                            wp_set_object_terms($post_id,$cat_ids,'bt_brand');
                                        }
                                    }
                                    update_post_meta($post_id,'brand',1);
                                }
                                else
                                {
                                    $count_brands=count($boat->BoatBrands);
                                    $brands=$boat->BoatBrands;
                                    for($t=0;$t<$count_brands;$t++)
                                    {
                                        if($brand_id==$brands[$t]->ID)
                                        {
                                            $new_brand=array(
                                                    'post_status' => 'publish',
                                                    'post_type'   => 'boat_brand',
                                                    'post_title'  => $brands[$t]->Name);
                                                $post_brand_id = wp_insert_post($new_brand); 
                                                if($post_brand_id>0)
                                                {
                                                    update_post_meta($post_brand_id,'BrandID',$brands[$t]->ID);
                                                    update_post_meta($post_brand_id,'BrandImageURL',$brands[$t]->Images->Image->ImageURL);
                                                    $tax_boat_brand=wp_insert_term($brands[$t]->ID,'bt_brand',array('slug' => strtolower($brands[$t]->Name)));
                                                    if (!is_wp_error($tax_boat_brand))
                                                    {
                                                        echo '<p>New Brand is added: '.$brands[$t]->Name.'</p>';
                                                        $id_cat_brand=array();
                                                        $id_cat_brand[]=$tax_boat_brand['term_id'];
                                                        $cat_ids = array_map( 'intval', $id_cat_brand);
                                                        $cat_ids = array_unique( $cat_ids );
                                                        wp_set_object_terms($post_id,$cat_ids,'bt_brand');
                                                    }
                                                    //creating new category of boat type
                                                }
                                                break 1;
                                            }
                                        }
                                }
                            }
                            
                        }

                        
                        if(!empty($type_name) && !empty($model_name))
                        {
                        $boat_id=get_post_meta($post_id,'id_boat',true);
                        $update_content = array(
                            'ID'           => $post_id,
                            'post_name' => $model_name.' '.$type_name.' (ref-'.$boat_id.')',
                            'post_title' => $model_name.' '.$type_name);
                        $id_post=wp_update_post($update_content);
                        if($id_post>0)
                        {
                            update_post_meta($id_post,'long_title','new');
                            echo 'Boat  <a href="'.get_permalink($post_id).'">'.$post_id.'</a> has new title<br />';
                        }
                        }
                        
                        //import boat locations
                         $boat_chars=$boat->Boats[0];
                         $boat_base=array();
                         foreach ($boat_chars->BoatBases as $base)
                         {
                            $boat_base[]=$base->ID;
                         } 
                         $cat_ids=array();
                         foreach ($boat_base as $key=>$id)
                         {
                            $args2 = array(
                                    'post_type' => 'boat_base',
                                    'orderby'=>'ID',
                                    'order' => 'ASC',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
	                                   array(
                                        'key' => 'id_base',
                                        'value'=>$id,
		                              'compare' => '=',
	                               )));
                            $found_base = get_posts($args2);
                            if(!empty($found_base))
                            {
                                foreach($found_base as $key2=>$post2)
                                {
                                    $title_base=get_post_meta($post2->ID,'title_base',true);
                                    $tax_loc = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$title_base."'", OBJECT );
                                    foreach ($tax_loc as $key3=>$base)
                                    {
                                        $term_base = get_option( "base_meta_".$base->term_id);
                                        if(!empty($term_base) && ($term_base==$id))
                                        {
                                            $cat_ids[]=$base->term_id;
                                            $country_term = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$base->term_id, OBJECT );
                                            if(count($country_term)>0)
                                            {
                                                foreach($country_term as $key3=>$country_id)
                                                {
                                                    if($country_id->taxonomy=='country')
                                                    {
                                                            $cat_ids[]=$country_id->parent;
                                                    }
                                                } 
                                            }
                                            $country_term2= $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$country_id->parent, OBJECT );
                                            if(count($country_term2)>0)
                                            {
                                                foreach($country_term2 as $key4=>$country_id2)
                                                {
                                                    if($country_id2->taxonomy=='country')
                                                    {
                                                        $cat_ids[]=$country_id2->parent;
                                                    }
                                                }
                                            }
                                                     
                                        }
                                    }       
                                    if(count($cat_ids)>0)
                                    {
                                        $cat_ids = array_map( 'intval', $cat_ids );
                                        $cat_ids = array_unique( $cat_ids );
                                        wp_set_object_terms($post_id,$cat_ids,'country');
                                    }
                                }
                            }
                               
                         }
                         update_post_meta($post_id, 'BoatImages',count($boat_chars->BoatImages));
                         $i=0;
                         foreach ($boat_chars->BoatImages as $image)
                         {
                            update_post_meta($post_id, 'BoatImage_'.$i,$image->ImageURL);
                            $i++;
                         }
                         update_post_meta($post_id, 'country','filled');
  
                        echo '<p>Boat number <a href="'.get_permalink($post_id).'">'.$post_id.'</a> was updated!</p>';
                        //end of import boat loations 

                       $prices=$boat_chars->Prices;
                       $discounts=$boat_chars->Discounts;
                       $count++;
                       $i=0;
                       foreach ($prices as $key2=>$price)
                       {
                            update_post_meta($post_id, 'DateFrom_'.$i,$price->DateFrom);
                            update_post_meta($post_id, 'DateTo_'.$i,$price->DateTo);
                            update_post_meta($post_id, 'Price_'.$i,$price->Price);
                            update_post_meta($post_id, 'CurrencyCode_'.$i,$price->CurrencyCode);
                            $i++;
                       }
                       update_post_meta($post_id, 'price',$i);
                       $i=0;
                       foreach ($discounts as $key3=>$discount)
                       {
                            update_post_meta($post_id, 'Amount_'.$i,$discount->Amount);
                            update_post_meta($post_id, 'Name_'.$i,$discount->Name);
                            update_post_meta($post_id, 'SailingDateFrom_'.$i,$discount->SailingDateFrom);
                            update_post_meta($post_id, 'SailingDateTo_'.$i,$discount->SailingDateTo);
                            if(isset($discount->ValidDurationFrom))
                            {
                                update_post_meta($post_id, 'ValidDurationFrom_'.$i,$discount->ValidDurationFrom);
                            }
                            
                            update_post_meta($post_id, 'DiscountTypeID_'.$i,$discount->DiscountTypeID);
                            $i++;
                       }
                       update_post_meta($post_id, 'discount',$i);
                       echo '<p>Boat number <a href="'.get_permalink($post_id).'">'.$post_id.'</a> was updated!</p>';
                       //echo '<br />'; 
                       
                       
                       $equipments=$boat->Boats[0]->Equipments;
                        $uploaded=get_post_meta($post_id,'uploaded_equipment',true);
                        if(empty($uploaded))
                        {
                            $uploaded=0;
                        }
                        if($uploaded<count($equipments))
                        {
                            update_post_meta($post_id,'equipment',count($equipments));
                            $i=0;
                            foreach ($equipments as $key2=>$equipment)
                            {
                                $args2 = array(
                                    'post_type' => 'boat_equipment',
                                    'orderby'=>'ID',
                                    'order' => 'ASC',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                    array(
		                              'key' => 'id_equip',
                                        'value'=>$equipment->ID,
		                              'compare' => '=',
	                                )));
                                $exist=get_posts($args2);

                                if(!empty($exist) && count($exist)==1)
                                {
                                    $id_equip=get_post_meta($exist[0]->ID,'id_equip', true);
                                    //echo $id_equip. ' '.$equipment->Quantity.'<br />';
                                    update_post_meta($post_id,'EquipQuantity_'.$i,$equipment->Quantity);
                                    update_post_meta($post_id,'EquipID_'.$i,$equipment->ID);
                                    update_post_meta($post_id,'EquipName_'.$i,$exist[0]->post_title);
                                    $i++;
                                    update_post_meta($post_id,'uploaded_equipment',$i);
                                }
                                else
                                {
                                    
                                }
                            }
                        }
                        else
                        {
                           update_post_meta($post_id,'full_equipment',count($equipments));
                           //update_post_meta($post->ID,'uploaded_equipment',count($equipments)); 
                        }
                        //echo get_post_meta($post->ID,'uploaded_equipment',true).' '.
                        //    get_post_meta($post->ID,'full_equipment',true).' '.
                        //    get_post_meta($post->ID,'equipment',true).'<br />';
                        echo '<p>Boat number <a href="'.get_permalink($post_id).'">'.$post_id.'</a> was updated!</p>';                     
                    }
                    else
                    {
                        wp_delete_post($post_id,true);
                    }
                    }
                    else
                    {
                        wp_delete_post($post_id,true);
                    }
                    echo '<p>Boat number <a href="'.get_permalink($post_id).'">'.$post_id.'</a> was updated!</p>';

                 }
                 
                 $update_content = array(
                    'ID'           => $post_id,
                    'post_content' => '[vc_row][vc_column width="2/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"][boat_images]'.
                                        '[boat_price][boat_extra][boat_equipment][boat_map][/vc_column_text][/vc_column]'.
                                        '[vc_column width="1/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"]'.
                                        '[boat_availability][/vc_column_text][/vc_column][/vc_row]');
                $id_post=wp_update_post($update_content);
                if($id_post>0)
                {
                    update_post_meta($post_id,'template','new');
                    echo 'Boat  <a href="'.get_permalink($post_id).'">'.$post_id.'</a> has new template<br />';
                }*/
    }
    
    
    public function getBoatPrices()
    {
         global $wpdb;
         $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True';
      
        $count=0;
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 8,
                      'meta_query' => array(
	                   array(
		                  'key' => 'price',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            foreach ($found as $key=>$post)
            {
                 $boat_id = get_post_meta($post->ID,"id_boat",true);
                 if(!empty($boat_id))
                 {
                    $query=$query_start.'&loadSpecificBoats='.$boat_id;
                    //echo $query.'<br />';
                    $boat=json_decode(file_get_contents($query));
                    if(count($boat->Boats)==1 && isset($boat->Boats[0]))
                    {
                       $boat_chars=$boat->Boats[0];
                       $prices=$boat_chars->Prices;
                       $discounts=$boat_chars->Discounts;
                       $count++;
                       $i=0;
                       foreach ($prices as $key2=>$price)
                       {
                            update_post_meta($post->ID, 'DateFrom_'.$i,$price->DateFrom);
                            update_post_meta($post->ID, 'DateTo_'.$i,$price->DateTo);
                            update_post_meta($post->ID, 'Price_'.$i,$price->Price);
                            update_post_meta($post->ID, 'CurrencyCode_'.$i,$price->CurrencyCode);
                            $i++;
                       }
                       update_post_meta($post->ID, 'price',$i);
                       $i=0;
                       foreach ($discounts as $key3=>$discount)
                       {
                            update_post_meta($post->ID, 'Amount_'.$i,$discount->Amount);
                            update_post_meta($post->ID, 'Name_'.$i,$discount->Name);
                            update_post_meta($post->ID, 'SailingDateFrom_'.$i,$discount->SailingDateFrom);
                            update_post_meta($post->ID, 'SailingDateTo_'.$i,$discount->SailingDateTo);
                            if(isset($discount->ValidDurationFrom))
                            {
                                update_post_meta($post->ID, 'ValidDurationFrom_'.$i,$discount->ValidDurationFrom);
                            }
                            
                            update_post_meta($post->ID, 'DiscountTypeID_'.$i,$discount->DiscountTypeID);
                            $i++;
                       }
                       update_post_meta($post->ID, 'discount',$i);
                       echo '<p>Boat number <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> was updated!</p>';
                       //echo '<br />';
                    }
                    else
                    {
                        echo 'Boat was deleted <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a><br />';
                        //wp_delete_post($post->ID,true);
                    }
                    
                 }
                 
            }
        }
        
        //function for short code
       /*
                            if (!isset($this->show_booker_boats[$i]['Discount']))
                            {
                                //information about discounts
                                foreach ($boat['Discounts'] as $row7=>$discount)
                                {
                                    $date_from = new DateTime($discount['SailingDateFrom']);
                                    $date_to = new DateTime($discount['SailingDateTo']);
                                    $date_cur=new DateTime($this->last_search['date_to']);
                                    if ($date_cur->getTimestamp()>=$date_from->getTimestamp() &&
                                                $date_cur->getTimestamp()<=$date_to->getTimestamp())
                                    {
                                        $this->show_booker_boats[$i]['Discount']=$discount['Amount'];  
                                        $this->show_booker_boats[$i]['DiscountID']=$discount['DiscountTypeID'];
                                        $this->show_booker_boats[$i]['DiscountName']=$discount['Name'];
                                        $this->show_booker_boats[$i]['DiscountFrom']=$discount['ValidDurationFrom']; 
                                        $this->show_booker_boats[$i]['DiscountTo']=$discount['ValidDurationTo'];                                          
                                        break; 
                                    }
                                        
                                } 
                                if (!isset($this->show_booker_boats[$i]['Discount']))
                                {
                                    $this->show_booker_boats[$i]['Discount']=0;
                                }
                            }//end of discrounts for request period
                                    
         
                            if ($this->show_booker_boats[$i]['Discount']>0)
                            {
                                $this->show_booker_boats[$i]['NewPrice']=$this->show_booker_boats[$i]['Price']-
                                                    $this->show_booker_boats[$i]['Price']*$this->show_booker_boats[$i]['Discount']/100;
                                $this->show_booker_boats[$i]['OurPrice']=$this->show_booker_boats[$i]['NewPrice']-
                                                    $this->show_booker_boats[$i]['NewPrice']*5/100;
                            }
                            elseif($this->show_booker_boats[$i]['Discount']==0)
                            {
                                $this->show_booker_boats[$i]['NewPrice']=0;
                                $this->show_booker_boats[$i]['OurPrice']=$this->show_booker_boats[$i]['Price']-
                                                    $this->show_booker_boats[$i]['Price']*5/100;
                            }   */
         return $count;
    }   
    
    
    public function getBoatEquipment()
    {
        global $wpdb;
         
        $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True';
        $count=0;
        $msg='';
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 8,
                      'meta_query' => array(
	                   array(
		                  'key' => 'full_equipment',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            foreach ($found as $key=>$post)
            {
                 $boat_id = get_post_meta($post->ID,"id_boat",true);
                 if(!empty($boat_id))
                 {
                    $query=$query_start.'&loadSpecificBoats='.$boat_id;
                    //echo $query.'<br />';
                    $boat=json_decode(file_get_contents($query));
                    if(count($boat->Boats)==1 && !empty($boat->Boats))
                    {
                        $count++;
                        $equipments=$boat->Boats[0]->Equipments;
                        $uploaded=get_post_meta($post->ID,'uploaded_equipment',true);
                        if(empty($uploaded))
                        {
                            $uploaded=0;
                        }
                        if($uploaded<count($equipments))
                        {
                            update_post_meta($post->ID,'equipment',count($equipments));
                            $i=0;
                            foreach ($equipments as $key2=>$equipment)
                            {
                                $args2 = array(
                                    'post_type' => 'boat_equipment',
                                    'orderby'=>'ID',
                                    'order' => 'ASC',
                                    'posts_per_page' => -1,
                                    'meta_query' => array(
                                    array(
		                              'key' => 'id_equip',
                                        'value'=>$equipment->ID,
		                              'compare' => '=',
	                                )));
                                $exist=get_posts($args2);
                                //print_r($exist);
                                if(!empty($exist) && count($exist)==1)
                                {
                                    $id_equip=get_post_meta($exist[0]->ID,'id_equip', true);
                                    //echo $id_equip. ' '.$equipment->Quantity.'<br />';
                                    update_post_meta($post->ID,'EquipQuantity_'.$i,$equipment->Quantity);
                                    update_post_meta($post->ID,'EquipID_'.$i,$equipment->ID);
                                    update_post_meta($post->ID,'EquipName_'.$i,$exist[0]->post_title);
                                    $i++;
                                    update_post_meta($post->ID,'uploaded_equipment',$i);
                                }
                                else
                                {
                                    
                                }
                            }
                        }
                        else
                        {
                           update_post_meta($post->ID,'full_equipment',count($equipments));
                           //update_post_meta($post->ID,'uploaded_equipment',count($equipments)); 
                        }
                        //echo get_post_meta($post->ID,'uploaded_equipment',true).' '.
                        //    get_post_meta($post->ID,'full_equipment',true).' '.
                        //    get_post_meta($post->ID,'equipment',true).'<br />';
                        echo '<p>Boat number <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> was updated!</p>';
                    }
                    else
                    {
                        $msg .='<p>Boat number '.$boat_id.' with post ID '.$post->ID.' was deleted!</p>';
                        $count++;
                        wp_delete_post($post->ID, true);
                    }
                }
                
            }
        }

                
                
                
        return $msg;
    } 
    
    
     public function getBoatLocations()
    {
        global $wpdb;
        
        
        $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True';
                
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 10,
                      'meta_query' => array(
	                   array(
		                  'key' => 'locations',
		                   'compare' => 'NOT EXISTS',
	                       )));
        $found_id = get_posts($args);
        if(!empty($found_id) && count($found_id)>0)
        {
            
        }
                
                
    }
    
    
    public function getServices()
    {
        $query_cat='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadServices=True&loadServiceTypes=True';
         $extras=json_decode(file_get_contents($query_cat));
         
         $count=0;
         
         if(!empty($extras))
         {
            foreach ($extras->ServiceTypes as $key=>$extra)
            {
                $args = array(
                      'post_type' => 'boat_type_sevice',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => -1,
                       'meta_query' => array(
                      array(
		                  'key' => 'id_type_service',
                          'value'=>$extra->ID,
		                   'compare' => '=',
	                       )));
                $written=get_posts($args);
                if(empty($written) || count($written)==0)
                {
                    $new_post = array(
                             'post_status' => 'publish',
                             'post_type'   => 'boat_type_sevice',
                             'post_title'  => $extra->Name->EN
                             );
                    $post_id = wp_insert_post($new_post);
                    if($post_id>0)
                    {
                        $count++;
                        update_post_meta($post_id,'id_type_service',$extra->ID);
                        update_post_meta($post_id,'type_service_ru',$extra->Name->RU);
                        $tax_cat_equip=wp_insert_term($extra->Name->EN,'id_type_service',
                                                        array('description'=> $extra->Name->EN,
                                                                'parent'=> 0));
                        if(!is_wp_error($tax_cat_equip))
                        {
                            update_post_meta($post_id,'type_service_en_term',$tax_cat_equip['term_id']); 
                        }

                        $tax_cat_equip2=wp_insert_term($extra->Name->RU,'id_type_service',
                                                        array('description'=> $extra->Name->RU,
                                                                'parent'=> 0));
                        if(!is_wp_error($tax_cat_equip2))
                        {
                            update_post_meta($post_id,'type_service_ru_term',$tax_cat_equip2['term_id']); 
                        }
                    }
                }
            }
         }
         

         
         foreach($extras->Services as $key=>$service)
         {

             $args = array(
                      'post_type' => 'boat_sevice',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => -1,
                       'meta_query' => array(
                      array(
		                  'key' => 'id_service',
                          'value'=>$service->ID,
		                   'compare' => '=',
	                       )));
                $written=get_posts($args);
                if(empty($written) || count($written)==0)
                {
                    $new_post = array(
                             'post_status' => 'publish',
                             'post_type'   => 'boat_sevice',
                             'post_title'  => $service->Name->EN,
                             'post_content'  => $service->Description->EN
                             );
                    $post_id = wp_insert_post($new_post);
                    if($post_id>0)
                    {
                        update_post_meta($post_id,'id_type_service',$service->TypeID);
                        update_post_meta($post_id,'id_service',$service->ID);
                    }
                }
         }
         

            

         return $count;
    }
    
    
    public function getAllBoatExtra()
    {
        $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=true';
        /*echo $query_start.'<br />';
        $extras=json_decode(file_get_contents($query_start));
                
        
        $count_first=0;
        
        foreach ($extras->Extras as $key=>$extra)
        {
            $count=count($extra->ExtraInfos);
            $count_first=$count_first+$count;
            $total=count($extra->ExtraInfos);              
        }
        
        echo $count_first.'<br />';

        
        
        $args = array(
            'post_type' => 'extra_prices',
           'orderby'=>'ID',
            'order' => 'ASC',
            'posts_per_page' => -1);
                
        $test=get_posts($args);
        
        $args = array(
                      'post_type' => 'boat_ope',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 30,
                      'meta_query' => array(
	                   array(
		                  'key' => 'extraprices',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found_ope = get_posts($args);
        
        $ope_num=count($found_ope);
        
        $count_extras=count($test);
        echo $count_extras.'<br />';
        $new=0;
        if($count_extras<$count_first)
        {
             for($k=0;$k<$ope_num;$k++)
             {
                $all++;
                $ope_id = get_post_meta($found_ope[$k]->ID,"id_ope",true);
                $uploaded=get_post_meta($found_ope[$k]->ID,"UploadedExtra",true);
                if(empty($uploaded))
                {
                    $uploaded=0;
                }
               
                $count=0;
                    
                $written=false;
                foreach ($extras->Extras as $key=>$extra)
                {
                    if($ope_id==$extra->FleetOperatorID)
                    {
                        $written=true;
                        $count=$count+count($extra->ExtraInfos);
                        $total=count($extra->ExtraInfos);
                        
                        
                        $up_count=0;
                        
                        if($uploaded<$total)
                        {
                            
                            foreach ($extra->ExtraInfos as $key=>$service)
                            {
                                $args = array(
                                'post_type' => 'extra_prices',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'ExtraID',
                                        'compare' => '=',
                                        'value'=>$service->ExtraID
                                )));
                                $extras_posts=get_posts($args);
                                if(empty($extras_posts) || count($extras_posts)==0)
                                {
                                    $new_post = array(
                                        'post_status' => 'publish',
                                        'post_type'   => 'extra_prices',
                                        'post_title'  => $service->ExtraID
                                        );
                                    $post_id = wp_insert_post($new_post); 
                                    if($post_id>0)
                                    {
                                        $new++;
                                        update_post_meta($post_id,"ExtraID",$service->ExtraID);
                                        update_post_meta($post_id,"IsObligatory",$service->IsObligatory);
                                        update_post_meta($post_id,"FleetOperatorID",$extra->FleetOperatorID);
                                        $up_count=$up_count+1;
                                        
                                        update_post_meta($found_ope[$k]->ID,"UploadedExtra",$up_count);
                                        if(isset($service->ServiceID))
                                        {
                                            update_post_meta($post_id,"ServiceID",$service->ServiceID);
                                        }
                                        if(isset($service->BoatEquipmentID))
                                        {
                                            update_post_meta($post_id,"BoatEquipmentID",$service->BoatEquipmentID);
                                        }
                                        if(isset($service->Name->EN))
                                        {
                                            update_post_meta($post_id,"Name",$service->Name->EN);
                                        }
                                        update_post_meta($post_id,"TotalPrices",count($service->Prices));
                                        $i=0;
                                        foreach ($service->Prices as $row=>$price)
                                        {
                                            update_post_meta($post_id,"Price_".$i,$price->Price);
                                            update_post_meta($post_id,"IncludedInCharterPrice_".$i,$price->IncludedInCharterPrice);
                                            update_post_meta($post_id,"CurrencyCode_".$i,$price->CurrencyCode);
                                            update_post_meta($post_id,"Name_EN_".$i,$price->Name->EN);
                                            update_post_meta($post_id,"Dates_".$i,count($price->CharterDates));
                                            $j=0;
                                            foreach ($price->CharterDates as $key=>$date)
                                            {
                                                update_post_meta($post_id,"DateFrom_".$j,$date->DateFrom);
                                                update_post_meta($post_id,"DateTo_".$j,$date->DateTo);
                                                $j++;
                                            }
                                            $i++;
                                        }
                                    }
                                    else
                                    {
                                        echo 'Exists!';
                                    }
                                }
                                else
                                {
                                    $up_count=$up_count+1;
                                    update_post_meta($found_ope[$k]->ID,"UploadedExtra",$up_count);
                                }
                            }
                        }
                        else
                        {
                            update_post_meta($found_ope[$k]->ID,"UploadedExtra",count($extra->ExtraInfos));
                            update_post_meta($found_ope[$k]->ID,"extraprices",count($extra->ExtraInfos));
                        }
                        $uploaded_extras=get_post_meta($found_ope[$k]->ID,"UploadedExtra",true);
                        echo $ope_id. ' '.$up_count.' '.$total. ' '.$uploaded_extras.'<br />';
                        break 1;
                    }

                }
                    if($written==false)
                    {
                        update_post_meta($found_ope[$k]->ID,"extraprices",0);
                    }
                
                
             }
        }*/
        
        
        /*$args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 2,
                      'meta_query' => array(
	                   array(
		                  'key' => 'full_extras',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        if(!empty($found) && count($found)>0)
        {
            foreach ($found as $key=>$post)
            {
                 $boat_id = get_post_meta($post->ID,"id_boat",true);
                 if(!empty($boat_id))
                 {
                    $query=$query_start.'&loadSpecificBoats='.$boat_id;
                    echo $query.'<br />';
                    $boat=json_decode(file_get_contents($query));
                    $i=0;
                    foreach ($boat->Boats[0]->Extras as $key=>$extra)
                    {
                        $args2 = array(
                                'post_type' => 'extra_prices',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'posts_per_page' => -1,
                                'meta_query' => array(
                                    array(
                                        'key' => 'ExtraID',
                                        'compare' => '=',
                                        'value'=>$extra
                                )));
                        $extras_posts=get_posts($args2);
                        if(empty($extras_posts))
                        {
 
                             /*foreach ($boat->Extras as $key2=>$extraprice)
                             {
                                $count_extras=count($extraprice->ExtraInfos);
                                
                                for($i=0;$i<$count_extras;$i++)
                                { 
                                    if($extraprice->ExtraInfos[$i]->ExtraID==$extra)
                                    {
                                        echo $extraprice->ExtraInfos[$i]->ExtraID.'<br />';
                                        break 1;
                                    }
                                }
                             }
                            
                            
                        }
                        else
                        {
                            update_post_meta($post->ID,'ExtraID_'.$i,$extra);
                            $i++;
                        } 
                    }
                    update_post_meta($post->ID,'full_extras',$i);
                    echo '<p>Boat number <a href="'.get_permalink($post->ID).'">'.$post->ID.'</a> was updated!</p>';

                }
            }
        }*/

        
        
        //return $new;
        
    }
    
    
    //get boat brand and type by boat ID from Booker database
    public function getBoatBrand()
    {
        global $wpdb;
        
         $query_start='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoats=True&loadBoatTypes=True';
        $count=0;
        $args = array(
                      'post_type' => 'boat_post',
                      'orderby'=>'ID',
                      'order' => 'ASC',
                      'posts_per_page' => 4,
                      //'ID'=>
                      'meta_query' => array(
	                   array(
		                  'key' => 'brand',
		                   'compare' => 'NOT EXISTS',
	                       ))
                           );
        $found = get_posts($args);
        $count_found=count($found);
        $type_name='';
        $model_name='';
       
        if(!empty($found) && count($found)>0)
        {
            for($i=0;$i<$count_found;$i++)
            {
                
                 
                 $boat_id = get_post_meta($found[$i]->ID,"id_boat",true);
                 $model_id=get_post_meta($found[$i]->ID,"ModelID",true);
                 
                 if(!empty($boat_id))
                 {
                    //request from Booker database for one boat
                    $query=$query_start.'&loadSpecificBoats='.$boat_id;
                    echo $query.'<br />';
                    $boat=json_decode(file_get_contents($query));
                    if (isset($boat->Boats[0]))
                    {
                        if(count($boat->Boats[0])==1)
                        {
                            $model_id=$boat->Boats[0]->ModelID;
                            update_post_meta($found[$i]->ID,"ModelID",$model_id);
                            update_post_meta($found[$i]->ID,"Engine",$boat->Boats[0]->Engine);
                            update_post_meta($found[$i]->ID,"Draft",$boat->Boats[0]->Draft);
                            update_post_meta($found[$i]->ID,"BerthsMax",$boat->Boats[0]->BerthsMax);
                            update_post_meta($found[$i]->ID,"BerthsStr",$boat->Boats[0]->BerthsStr);
                            update_post_meta($found[$i]->ID,"BerthsBasic",$boat->Boats[0]->BerthsBasic);
                            update_post_meta($found[$i]->ID,"CabinsStr",$boat->Boats[0]->CabinsStr);
                            update_post_meta($found[$i]->ID,"CabinsMax",$boat->Boats[0]->CabinsMax);
                            update_post_meta($found[$i]->ID,"CabinsBasic",$boat->Boats[0]->CabinsBasic);
                            $type_id=get_post_meta($found[$i]->ID,"BoatTypeID",true);
                            
                        
                       
                        //find boat model
                        $arr_model=array(
                                'post_type' => 'boat_model',
                                'orderby'=>'ID',
                                'order' => 'ASC',
                                'posts_per_page' => -1,
                                    'meta_query' => array(
	                                array(
		                              'key' => 'ModelID',
                                      'value'=>$model_id,
		                              'compare' => '=',
	                           )));
                        $models=get_posts($arr_model);
                        
                        if(!empty($models) && count($models)==1)
                        {
                            
                            if(empty($type_id))
                            {
                                $type_id=get_post_meta($models[0]->ID,"BoatTypeID",true); 
                                update_post_meta($found[$i]->ID,"BoatTypeID",$type_id);
                            }
                            if(empty($type_id))
                            {
                                //if(isset())
                            }
                            echo $type_id.'<br />';
                            foreach($boat->BoatTypes as $type_id_boat)
                            {
                                print_r($type_id_boat);
                                echo '<br />';
                                if($type_id_boat->ID==$type_id)
                                {
                                    $type_name=$type_id_boat->Name->EN;
                                }
                            }
                            echo $type_name.'<br />';

                            
                           
                            $term_model = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$models[0]->post_title."'", OBJECT );
                            $model_name=$models[0]->post_title;
                            if(!empty($term_model))
                            {
                                foreach ($term_model as $key2=>$cat_model)
                                {
                                    $tax_model = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$cat_model->term_id, OBJECT);
                                    if(!empty($tax_model))
                                    {
                                        foreach ($tax_model as $key3=>$tax_name)
                                        {
                                            if($tax_name->taxonomy=='bt_model')
                                            {
                                                $id_cat_model=array();
                                                $id_cat_model[]=$tax_name->term_id;
                                                $cat_ids = array_map( 'intval', $id_cat_model );
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($found[$i]->ID,$cat_ids,'bt_model');
                                            }
                                        }
                                    }
                                } 
                            }

                            
                            

                            if(!empty($type_id))  
                            {  
                                $taxonomy_types=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE taxonomy='boattype'", OBJECT );
                                    //checking the contry iso code in word press taxnoomies
                                for($j=0;$j<count($taxonomy_types);$j++)
                                {
                                       $term_type = $wpdb->get_results( "SELECT * FROM wp_terms WHERE term_id=".$taxonomy_types[$j]->term_id, OBJECT );
                                        
                                        if(!empty($term_type))
                                        {
                                            if($term_type[0]->name==$type_name)
                                            {
                                                $type_name=$term_type[0]->name;
                                                $id_cat_model=array();
                                                $id_cat_model[]=$term_type[0]->term_id;
                                                $cat_ids = array_map( 'intval', $id_cat_model );
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($found[$i]->ID,$cat_ids,'boattype');
                                            }
                                            
                                        }
                               
                                    }
                            } 
                        }
                        else
                        {
                            echo 'Model is not found!<br />';
                        }
                     
                
                        if($type_name!=='' && $model_name!=='')
                        {
                            $count++;
                            //update_post_meta($found[$i]->ID,'brand','updated');
                            echo '<p>Boat number <a href="'.get_permalink($found[$i]->ID).'">'.$found[$i]->ID.'</a> was updated!</p>';
                            $boat_id=get_post_meta($found[$i]->ID,'id_boat',true);
                            echo $model_name.' '.$type_name.' '.$boat_id.'<br />';
                            $update_content = array(
                                'ID'           => $found[$i]->ID,
                                'post_name' => sanitize_title($model_name.' '.$type_name.' (ref-'.$boat_id.')'),
                                'post_title' => $model_name.' '.$type_name);
                            $id_post=wp_update_post($update_content);
                            if($id_post>0)
                            {
                                //update_post_meta($found[$i]->ID,'long_title','new');
                                echo 'Boat  <a href="'.get_permalink( $id_post).'">'. $id_post.'</a> has new title<br />';
                            }
                        }
                    }
                    else
                    {
                         wp_delete_post($found[$i]->ID,true);
                         echo 'deleted<br />';
                    }
                        
                    }
                    else
                    {
                        wp_delete_post($found[$i]->ID,true);
                        echo 'deleted<br />';
                    }

                    
                 }
                 else
                 {
                    wp_delete_post($found[$i]->ID,true);
                 }
            }
        }
        return $count;
    }

    /*******************************functions for Sedna database**************************/
    /*public function getAllDestinations()
    {
        $mts_query=array();
        $mts_query['lg']='0';
        $mts_query['refagt']='wxft6043';
        $query = http_build_query($mts_query);
        $data = $this->h->XMLtoarray( $this->h->byGET_('http://client.sednasystem.com/API/GetDestinations2.asp?'.$query )  );
        $dest = array();

        foreach ($data['destination'] as $key => $value) {
            $dest[] = array(
                            'label'=>$this->h->JSON_char($value['@attributes']['name']),
                            'value'=>$this->h->JSON_char($value['@attributes']['name']),
                            'id'=>'d'.$value['@attributes']['id_dest']);
            if ( isset($value['country']) && is_array($value['country']) && isset($value['country'][0]) ) {
                foreach ($value['country'] as $skey => $svalue) {
                    $dest[] = array(
                                    'label'=>$this->h->JSON_char($svalue['@attributes']['name']),
                                    'value'=>$this->h->JSON_char($svalue['@attributes']['name']),
                                    'id'=>'c'.$svalue['@attributes']['id_country']);
                }

            } elseif ( isset($value['country']) && is_array($value['country']) && isset($value['country']['@attributes']) ) {
                $dest[] = array('label'=>$this->h->JSON_char($value['country']['@attributes']['name']),
                                'value'=>$this->h->JSON_char($value['country']['@attributes']['name']),
                                'id'=>'c'.$value['country']['@attributes']['id_country']);
            }
        }

        return $dest;

    }*/
    
    
    
  

    /*public function getAllOpetators($simple = false)
    {
            $query = http_build_query($this->mts_query);
            $data  = $this->h->XMLtoarray( $this->h->byGET_('http://client.sednasystem.com/API/getOperators.asp?'.$query )  );

            if ($simple) {
                $return_array = array();
                foreach ($data['operator'] as $operator) {
                    $return_array[$operator['@attributes']['id_ope']] = $operator['@attributes']['ope_company'];
                }

                return $return_array;
            }

            return $data;

    }*/

    /*public function getAllBoatsFromOperator($operator_id = false)
    {
            if(!$operator_id)

                return false;

            $query           = $this->mts_query;
            $query['Id_ope'] = $operator_id;
            $query           = http_build_query($query);

            $data = $this->h->XMLtoarray($this->h->byGET_('http://client.sednasystem.com/API/getBts3.asp?'.$query ));

            if ($simple) {
                $return_array = array();
                foreach ($data['operator'] as $operator) {
                    $return_array[$operator['@attributes']['id_ope']] = $operator['@attributes']['ope_company'];
                }

                return $return_array;
            }

            return $data;
    }*/

    /*public function getBoatsGeneralInfo($boat_id = false)
    {
            if(!$boat_id)

                return false;

            $return_array     = array();

            $query            = $this->mts_query;
            $query['Id_boat'] = $boat_id;
            $query            = http_build_query($query);

            $result= $this->h->XMLtoarray($this->h->byGET_('http://client.sednasystem.com/API/getBoat.asp?'.$query ));
            return $result;
    }*/

    /*public function getBoatsCharacteristics($boat_id = false)
    {
            if(!$boat_id)
                return false;

            $return_array     = array();

            $query            = $this->mts_query;
            $query['Id_boat'] = $boat_id;
            $query            = http_build_query($query);


            return $this->h->XMLtoarray($this->h->byGET_('http://client.sednasystem.com/API/GetCharacteristics2.asp?'.$query ));

    }*/
    /*public function getBoatsAvailabilityByDestination($destination_id = false, $d, $m, $y, $days)
    {
            if(!$destination_id)
                return false;

            $return_array     = array();

            $query                = array();
            $query['Action']      = 'search';
            $query['srh_dest']    = $destination_id;
            $query['DEPART_DD']   = $d;
            $query['DEPART_MM']   = $m;
            $query['DEPART_YYYY'] = $y;
            $query['Nombjour']    = $days;

            $query                = http_build_query($query);

            return $this->h->XMLtoarray($this->h->byGET_("http://client.sednasystem.com/m3/agt/{$this->agent_id}/default.asp?".$query ));

    } */   

    /*public function getBoatsAvailabilityById($Id_boat = false, $d, $m, $y, $days)
    {
            if(!$Id_boat)
                return false;

            $return_array     = array();

            $query                = array();
            $query['Action']      = 'search';
            $query['Id_boat']     = $Id_boat;
            $query['DEPART_DD']   = $d;
            $query['DEPART_MM']   = $m;
            $query['DEPART_YYYY'] = $y;
            $query['Nombjour']    = $days;


            $query                = http_build_query($query);
            
            
            echo "http://client.sednasystem.com/m3/agt/{$this->agent_id}/default.asp?".$query.'<br />';

            return $this->h->XMLtoarray($this->h->byGET_("http://client.sednasystem.com/m3/agt/{$this->agent_id}/default.asp?".$query ));

    }*/
    
    
    public function getBoatsExtraPrices($boat_id = false)
    {
            if(!$boat_id)
                return false;

            $return_array     = array();

            $query            = $this->mts_query;
            $query['Id_boat'] = $boat_id;
            $query            = http_build_query($query);


            return json_decode(json_encode(simplexml_load_string(file_get_contents('http://client.sednasystem.com/API/getExtras2.asp?id_boat='.$boat_id.'&refagt=wxft6043'))),true);


    }
    
    /*******************************************************************/
    //functions for duplcating all boats in destinations into Russian language
    
    //function for getting list of all destinations
    public function getCountries()
    {
        $mts_query=array();
        
        $query='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBases=True';
        
        $bases=json_decode(file_get_contents($query));
        
        return $bases->Bases;


    }
    
    
    
    //importing all destinations into taxonomy
    public function import_transl_loc()
    {
         global $iclTranslationManagement, $sitepress, $ICL_Pro_Translation, $wpdb;
         global $sitepress, $wpdb;
        $count=0;
        $destinations=$this->getCountries();
        $taxonomy_countries=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE taxonomy='destination' AND ".
                                                "parent=0", OBJECT );
                                                
        /*$taxonomy_countries=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE taxonomy='destination'",OBJECT);
        for($i=0;$i<count($taxonomy_countries);$i++)
        {
             //function for searching boat operator of boat in Sedna database
             $catID = icl_object_id($taxonomy_countries[$i]->term_id, 'destination', false, 'ru');
             $term = get_term( $catID, 'destination');
            print_r($term);
        }*/
        //the next step with storing geological data for filter by distance
        foreach($destinations as $dest)
        {
            $name_base= sanitize_title($dest->Name);
            
            $name_location=sanitize_title($dest->Location);
            $title_location=ucwords(str_replace('-',' ',$name_location));
            $title_base=ucwords(str_replace('-',' ',$name_base));
            $default_lang = $sitepress->get_default_language();
            $parent_country=0;
            $parent_location=0;
            $base_id=0;
           
            //checking the contry iso code in word press taxnoomies
            for($i=0;$i<count($taxonomy_countries);$i++)
            {
                $code_country=get_option( "tax_iso_".$taxonomy_countries[$i]->term_id);
                $lang_country=get_option( "tax_lang_".$taxonomy_countries[$i]->term_id);
                
                if (!empty($code_country))
                {
                    if ($dest->CountryCode==$code_country  && $lang_country=='en')
                    {
                        $parent_country=$taxonomy_countries[$i]->term_id;
                          
                        break 1;
                    }
                }
            }  
            
            if($parent_country==0)
            {
                //new country taxonomy
                $_POST['icl_tax_destination_language'] = $default_lang;
                $dest_country=wp_insert_term($dest->CountryCode,'destination',
                                                        array('description'=> $dest->CountryCode,
                                                                'parent'=> 0));
                if(!is_wp_error($dest_country))
                {
                    update_option( "tax_iso_".$dest_country['term_id'],$dest->CountryCode);
                    update_option( "tax_lang_".$dest_country['term_id'],'en');
                    $count++;
                    $parent_country=$dest_country['term_id'];
                }
            }
            else
            {
                 
                $tax_loc = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$title_location."'", OBJECT);
                if(!empty($tax_loc) && count($tax_loc)>0)
                {
                    for ($i=0;$i<count($tax_loc);$i++)
                    {
                        $loc_id = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE term_id=".$tax_loc[$i]->term_id, OBJECT);
                        for($j=0;$j<count($loc_id);$j++)
                        {
                            if($loc_id[$j]->taxonomy=='destination' && $loc_id[$j]->parent==$parent_country)
                            {
                                $parent_location=$loc_id[$j]->term_id;
                                break 1;
                            }
                        }  
                        
                     } 
                     
                }
                if($parent_location==0)
                {
                    $tax_location=wp_insert_term($title_location,'destination',array('description'=> $dest->Location,
                                                                        'parent'=> $parent_country));
                    $_POST['icl_tax_destination_language'] = $default_lang;
                    if (!is_wp_error($tax_location))
                    {
                        $parent_location=$tax_location['term_id'];
                        $count++;
                    }
                }
                else
                {
                    $base_id=0;
                    $tax_base = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$title_base."'", OBJECT);
                    for ($i=0;$i<count($tax_base);$i++)
                    {
                        $bases = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE term_id=".$tax_base[$i]->term_id, OBJECT);
                        for($j=0;$j<count($bases);$j++)
                        {
                            if($bases[$j]->taxonomy=='destination' && $bases[$j]->parent==$parent_location)
                            {
                                $base_id=$bases[$j]->term_id;
                                break 1;
                            }
                        }  
                        
                     } 
      
                     if($base_id==0)
                     {
                        $_POST['icl_tax_destination_language'] = $default_lang;
                        $tax_base=wp_insert_term($title_base,'destination',array('description'=> $dest->Name,
                                                                        'parent'=> $parent_location));
                        if (!is_wp_error($tax_base))
                        {
                            $count++;
                        }
                     }
      
                }
               
            }
            
            if($count>220)
            {
                break;
            }
        }
        
        return $count;
    }
    
    
    //duplicating boats with destinations into Russian language
    public function duplicate_boat_ru()
    {
         global $iclTranslationManagement, $sitepress, $ICL_Pro_Translation, $wpdb;
         include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );

        $count=0;
        $array_fields=array(0=>'id_boat', 1=>'BerthsMax', 2=>'BerthsStr',
                            3=>'BerthsBasic', 4=>'CabinsBasic', 5=>'CabinsMax',
                            6 =>'CabinsStr', 7=>'Engine', 8 =>'Draft',
                            9=>'HasCrew', 10=>'Length', 11=>'Name',
                            12=>'YearBuilt', 13=>'ModelID');
        //get destinations that have Russian trnslation
         $taxonomy_countries=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE taxonomy='destination' AND ".
                                                    " parent=".$count, OBJECT );
         for($i=0;$i<count($taxonomy_countries);$i++)
         {
            echo 'main: '.$taxonomy_countries[$i]->count.' '.$taxonomy_countries[$i]->count.'<br />';
            $term_id_ru=icl_object_id($taxonomy_countries[$i]->term_id,'destination', false, 'ru' );
            
            if(!empty($term_id_ru))
            {
                
                if($taxonomy_countries[$i]->parent==0)
                {
                    //search for the term with ID term of destination for name
                    $term_dest=$wpdb->get_results( "SELECT * FROM wp_terms WHERE term_id=".
                                                $taxonomy_countries[$i]->term_id, OBJECT );
                    
                    
                    //search for the other different terms with the same name and country taxonomy
                    $term_country=$wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".
                                                $term_dest[0]->name."'", OBJECT );
                    
                    //search taxonomy id of destination for country type
                    if(count($term_country)>0)
                    {
                        foreach($term_country as $country)
                        {
                            $type_tax=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE ".
                                                           " term_id=".$country->term_id, OBJECT );
                            
                            if(!empty($type_tax) && $type_tax[0]->taxonomy=='country')
                            {
                                echo $country->name.' '.$type_tax[0]->count.'<br />';
                                $tax_id_country=$type_tax[0]->term_id;
                                $slug_tax=$wpdb->get_results( "SELECT * FROM wp_terms WHERE ".
                                                           " term_id=".$tax_id_country, OBJECT );
                                
                                //find the destionations in Russian that have translations
                                $terms_ru=get_term_children($term_id_ru,'destination');
                                
                                $terms_country=array();
                                $terms_ru_parents=array();
                               
                                //find that translation in English
                                $m=0;
                                foreach ($terms_ru as $id)
                                {
                                    //checking what parents is needed to search
                                    $terms_ru_parents[$m]['id']=$id;
                                    $check_parent=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE ".
                                                           " term_id=".$id, OBJECT );
                                    $terms_ru_parents[$m]['parent']=$check_parent[0]->parent;
                                    $m++;
                                }
                                $reach_ru_terms=array();
                                
                                $g=0;
                                for($t=0;$t<count($terms_ru_parents);$t++)
                                {
                                    $k=0;
                                    if($terms_ru_parents[$t]['parent']!==$term_id_ru)
                                    {
                                        $reach_ru_terms[$g][$k]=$terms_ru_parents[$t]['id'];
                                        $k++;
                                        $reach_ru_terms[$g][$k]=$terms_ru_parents[$t]['parent'];
        
                                        $g++;
                                    }

                                }
                                
                                foreach($reach_ru_terms as $search_ru)
                                {
                                    $terms_en=array();
                                    $terms_ru_cat=array();
                                    $s=1;
                                    $parents=array();
                                    $slugs_search=array();
                                    $slugs_search[]=$slug_tax[0]->slug;
                                   foreach($search_ru as $id_ru) 
                                   {
                                        
                                        
                                        $slug_ru=$wpdb->get_results( "SELECT * FROM wp_terms WHERE ".
                                                           " term_id=".$id_ru, OBJECT );
                                        
                                        $term_en_id=icl_object_id($id_ru,'destination', false, 'en' );
                                        $terms_ru_cat[]=$id_ru;
                                        
                                        $terms_en[]=$term_en_id;
                                        $country_tax=$wpdb->get_results( "SELECT * FROM wp_terms WHERE ".
                                                           " term_id=".$term_en_id, OBJECT );
                                        $slug_country=$wpdb->get_results( "SELECT * FROM wp_terms WHERE ".
                                                           " name='".$country_tax[0]->name."'", OBJECT );
                                        foreach($slug_country as $slug)
                                        {
                                            $check_slug=$wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE ".
                                                           " term_id=".$slug->term_id, OBJECT );
                                            if(!empty($check_slug) && $check_slug[0]->taxonomy=='country')
                                            {
                                                    $slugs_search[$s]=$slug->slug;
                                                    $parents[$s]['id']=$slug->term_id;
                                                    $parents[$s]['parent']=$check_slug[0]->parent;
                                                    $s++;
                                            }
                                        }
                                   }
                                
                                   
                                   $terms_ru_cat[]=$term_id_ru;
              
                                   $terms_en[]=$taxonomy_countries[$i]->term_id;
                                   
                                   if(count($slugs_search)>3)
                                   {
                                   $new_id=array();
                                   foreach($parents as $key=>$parent)
                                   {
                                    
                                     for($p=1;$p<count($parents);$p++)
                                     {
                                        if($parent['id']==$parents[$p]['parent'])
                                        {
                                             $new_id[]=$parent['id'];
                                        }
                                     }
                                     
                                   }
                   
                                   foreach($parents as $key=>$parent)
                                   {
                                     
                                     if(!in_array($parent['id'],$new_id))
                                     {
                                        if(!in_array($parent['parent'],$new_id))
                                        {
                                            unset($slugs_search[$key]);

                                        }
                                     }
                                   }
                                   $num=0;
                                   $new_search=array();
                                   foreach($slugs_search as $slug)
                                   {
                                      $new_search[$num]=$slug;
                                      $num++;
                                   }
                                   $slugs_search=$new_search;
                 
                                   }
                                   
                                   $posts_search=array('post_type'=>'boat_post',  
                                                    'posts_per_page' => -1,
                                                    'tax_query' => array(
		                                              'relation' => 'AND',
		                                                              array(
			                                                         'taxonomy' => 'country',
			                                         'field'    => 'slug',
			                                             'terms'    => $slugs_search[0],
		                                              ),
                                                      array(
			                                                         'taxonomy' => 'country',
			                                         'field'    => 'slug',
			                                             'terms'    => $slugs_search[1],
		                                              ),
                                                      array(
			                                                         'taxonomy' => 'country',
			                                         'field'    => 'slug',
			                                             'terms'    => $slugs_search[2],
		                                              )));
                                   $boats_posts=get_posts($posts_search);
                                if(count($boats_posts)>0)
                                {
                                    foreach($boats_posts as $post)
                                    {
                                        
                                        $location=get_post_meta($post->ID,'rus_dest',true);
                                        $post_ru_id=icl_object_id($post->ID,'boat_post', false, 'ru' );
                                        
                                        if(empty($post_ru_id))
                                        {
                                            
                                            $count++;
                                            $_POST['icl_post_language'] = 'ru';
                                            $postarr=array('post_title'=>$post->post_title,
                                                            'post_type'=>'boat_post',
                                                            'post_status' => 'publish',
                                                            'post_name'=>sanitize_title($post->post_title).'-ru');
                                             $post_new_ru=wp_insert_post($postarr);
                                             if($post_new_ru>0)
                                             {
                                                $cat_ids = array_map( 'intval', $terms_ru_cat);
                                                $cat_ids = array_unique( $cat_ids );
                                                
                                                wp_set_object_terms($post_new_ru,$cat_ids,'destination',true);
                                                update_post_meta($post_new_ru,'rus_dest','full');
                                                  // Get trid of original post
                                            $trid = wpml_get_content_trid( 'post_boat_post', $post->ID );

                                                // Get default language
                                            $default_lang = wpml_get_default_language();

                                            $wpdb->update( $wpdb->prefix.'icl_translations', 
                                            array( 'trid' => $trid, 'language_code' => 'ru', 
                                            'source_language_code' => $default_lang ), array( 'element_id' => $post_new_ru ) );
                                                    foreach($array_fields as $num=>$meta)
                                                    {
                                                        $value=get_post_meta($post->ID,$meta,true);
                                                        update_post_meta($post_new_ru,$meta,$value);
                                                    }
                                                    
                                                    $update_content = array(
                    'ID'           => $post_new_ru,
                    'post_content' => '[vc_row][vc_column width="2/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"][boat_images]'.
                                        '[boat_price][boat_extra][boat_equipment][boat_map][/vc_column_text][/vc_column]'.
                                        '[vc_column width="1/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"]'.
                                        '[boat_availability][/vc_column_text][/vc_column][/vc_row]');
                $post_new_ru=wp_update_post($update_content);
                                             }

                                            $cat_ids = array_map( 'intval',  $terms_en);
                                            $cat_ids = array_unique( $cat_ids );
                                            wp_set_object_terms($post->ID,$cat_ids,'destination',true);
                                            update_post_meta($post->ID,'rus_dest','full');
                                            echo 'Boat <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated<br />';
            
                                        }
                                        else
                                        {
                                            if(!empty($location))
                                            {
                                                
                                            }
                                            else
                                            {
                                                
                                            
                                            if(!empty($post_ru_id))
                                            {
                                                 $count++;
                                                $cat_ids = array_map( 'intval', $terms_ru_cat);
                                                $cat_ids = array_unique( $cat_ids );
                                                
                                                wp_set_object_terms($post_ru_id,$cat_ids,'destination',true);
                                                
                                                update_post_meta($post_ru_id,'rus_dest','full');
                                                  foreach($array_fields as $num=>$meta)
                                                    {
                                                        $value=get_post_meta($post->ID,$meta,true);
                                                        update_post_meta($post_ru_id,$meta,$value);
                                                    }
                                                    
                                                    $update_content = array(
                    'ID'           => $post_ru_id,
                    'post_content' => '[vc_row][vc_column width="2/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"][boat_images]'.
                                        '[boat_price][boat_extra][boat_equipment][boat_map][/vc_column_text][/vc_column]'.
                                        '[vc_column width="1/3"][vc_column_text disable_pattern="true" align="left" margin_bottom="0"]'.
                                        '[boat_availability][/vc_column_text][/vc_column][/vc_row]');
                $post_new_ru=wp_update_post($update_content);
                                             

                                            $cat_ids = array_map( 'intval',  $terms_en);
                                            $cat_ids = array_unique( $cat_ids );
                                            wp_set_object_terms($post->ID,$cat_ids,'destination',true);
                                            update_post_meta($post->ID,'rus_dest','full');
                                            echo 'Boat <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated<br />';
            
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
                //$name_base= sanitize_title($dest->Name);
                //$name_location=sanitize_title($dest->Location);
                //$title_location=ucwords(str_replace('-',' ',$name_location));
                //$title_base=ucwords(str_replace('-',' ',$name_base));
                //print_r($taxonomy_countries[$i]);
                 
                 //print_r($term_dest);

            }
            if($count>0)
            break;
         }
         
         
        return $count;
        
    }
    
    
    public function getOperators()
    {
        
        
        $query='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadFleetOperators=True';
        
        $bases=json_decode(file_get_contents($query));
        
        return $bases->FleetOperators;


    }
    
    
    
     //importing new destinations
    public function import_transl_operators()
    {
        global $sitepress, $wpdb;
        $count=0;
        $operators=$this->getOperators();
 
        $default_lang = $sitepress->get_default_language();
                                                
        //the next step with storing geological data for filter by distance
        foreach($operators as $oper)
        {
            
            $name_oper= sanitize_title($oper->Name);
            $title_oper=ucwords(str_replace('-',' ',$name_oper));
            $operator_id=0;
            $operator_num=0;
            
            $tax_oper = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$title_oper."'", OBJECT);
            if(!empty($tax_oper) && count($tax_oper)>0)
            {
                    for ($i=0;$i<count($tax_oper);$i++)
                    {
                        $oper_id = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE term_id=".$tax_oper[$i]->term_id, OBJECT);
                        for($j=0;$j<count($oper_id);$j++)
                        {
                            if($oper_id[$j]->taxonomy=='fleet_operator')
                            {
                                $operator_id=$oper_id[$j]->term_id;
                                $operator_num++;
                            }
                        }  
                        
                     } 
                     
            }
            
            if($operator_num<2)
            {
                //new country taxonomy
                //$_POST['icl_tax_fleet_operator_language'] = 'en';
                //$tax_oper=wp_insert_term($title_oper,'fleet_operator',
                //                                        array('description'=> $oper->Name));
                //if(!is_wp_error($tax_oper))
                //{
                //    $count++;
                //}
                //new country taxonomy
                $_POST['icl_tax_fleet_operator_language'] = 'ru';
                $tax_oper=wp_insert_term($title_oper,'fleet_operator',
                                                        array('description'=> $oper->Name,
                                                        'slug'=>$name_oper.'-ru'));
                if(!is_wp_error($tax_oper))
                {
                    $count++;
                }
            }
            
            if($count>250)
            {
                break;
            }
        }
        
        return $count;
    }
    
    
    public function save_operator_info()
    {
        global $sitepress, $wpdb;
        $count=0;
        $operators=$this->getOperators();
 
                                                
        //the next step with storing geological data for filter by distance
        foreach($operators as $oper)
        {
            
            $name_oper= sanitize_title($oper->Name);
            $title_oper=ucwords(str_replace('-',' ',$name_oper));

            $arr_ope=array(
                'post_type' => 'boat_ope',
                'orderby'=>'ID',
                'order' => 'ASC',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'id_ope',
                        'value'=>$oper->ID,
                        'compare' => '=',
                        )));
            $ope_posts=get_posts($arr_ope);
            $i=0;
            if(count($ope_posts)>0)
            {
            foreach($ope_posts as $post)
            {
                if($i>0)
                {
                    wp_delete_post($post->ID,true);
                }
                else
                {
                    $updated=get_post_meta($post->ID,"Updated_time",true);
                    if(empty($updated))
                    {
                        $update_title=array('ID' => $post->ID,
                            'post_name' => $name_oper.'-ref-'.$oper->ID,
                            'post_title' => $title_oper);
                        $id_post=wp_update_post($update_title);
                        update_post_meta($post->ID,'BaseID',$oper->Bases[0]->BaseID);
                        update_post_meta($post->ID,'Website',$oper->Website);
                        update_post_meta($post->ID,'Email',$oper->DefaultMail);
                        update_post_meta($post->ID,'RealTime',$oper->RealtimeAvailability);
                        update_post_meta($post->ID,'Updated_time',time());
                        echo 'Operator <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated<br />';
                        $count++;
                    }
                 } 
                 $i++;   
            }
            }
            else
            {
                $new_post = array(
                             'post_status' => 'publish',
                             'post_type'   => 'boat_ope',
                             'post_title'  =>  $title_oper,
                             'post_name'=>$name_oper.'-ref-'.$oper->ID
                             );
                $post_id = wp_insert_post($new_post);
                if($post_id>0)
                {
                    update_post_meta($post_id,'id_ope',$oper->ID);
                    update_post_meta($post_id,'BaseID',$oper->Bases[0]->BaseID);
                    update_post_meta($post_id,'Website',$oper->Website);
                    update_post_meta($post_id,'Email',$oper->DefaultMail);
                    update_post_meta($post_id,'RealTime',$oper->RealtimeAvailability);
                    update_post_meta($post_id,'Updated_time',time());
                    echo 'Operator <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was added<br />';
                    $count++;
                }
            }
            
            if($count>54)
            {
                break;
            }
            
        }
        return $count;
    }
    //the end of operator post updated with all detail information
    
    //get the lst of all boat models
    public function getBoatModels()
    {
        $query='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoatModels=True';
        
        $models=json_decode(file_get_contents($query));
        
        return $models->BoatModels;
    }
    
    
    
     //importing new models with images
    public function import_transl_models()
    {
         global $iclTranslationManagement, $sitepress, $ICL_Pro_Translation, $wpdb;
         include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
        $count=0;
        $models=$this->getBoatModels(); 
   
         // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );  
        
        $excluded_terms=array('Tempest 605', 'Signature 280 Cruiser', 'SeaDan 360OF Open');

        $loaded_models=0;

        $uploaded_image=false;
        $updated=0;
        //from 1000 to 1200 already uodated
        for($t=1200;$t<count($models)-700;$t++)
        {
            $tax_name_model=str_replace("'","",$models[$t]->Name);
            $tax_name_model=str_replace("&","-",$tax_name_model);
            //find boat model
            $arr_model=array('post_type' => 'boat_model',
		                              'meta_key' => 'ModelID',
                                      'meta_value'=>$models[$t]->ID,
                                      'meta_compare'=>'=',
                                      'meta_type'=>'CHAR');


            $model_post=get_posts($arr_model);
            $updated++;

            if(count($model_post)>0)
            {
                foreach($model_post as $model)
                {
                    $post_language_information = wpml_get_language_information($model->ID);
                    
                    if(isset($post_language_information['locale']) && $post_language_information['locale']==="en_US")
                    {
                        $model_info=get_post_meta($model->ID,'Updated_info',true);
                        if(empty($model_info))
                        {
                            delete_post_meta($model->ID,'Updated_type'); 
                            update_post_meta($model->ID,'BoatTypeID',$models[$t]->BoatTypeID);
                            update_post_meta($model->ID,'BrandID',$models[$t]->BrandID);
                            update_post_meta($model->ID,'FuelCapacity',$models[$t]->FuelCapacity);
                            update_post_meta($model->ID,'WaterCapacity',$models[$t]->WaterCapacity);
                            update_post_meta($model->ID,'WaterlineLength',$models[$t]->WaterlineLength);
                            update_post_meta($model->ID,'Weight',$models[$t]->Weight);
                            update_post_meta($model->ID,'Engine',$models[$t]->Engine);
                            update_post_meta($model->ID,'Draft',$models[$t]->Draft);
                            update_post_meta($model->ID,'Length',$models[$t]->Length);
                            update_post_meta($model->ID,'HullLength',$models[$t]->HullLength);
                    
                            update_post_meta($model->ID,'ShowersBasic',$models[$t]->ShowersBasic);
                            update_post_meta($model->ID,'ShowersMax',$models[$t]->ShowersMax);
                            update_post_meta($model->ID,'ShowersStr',$models[$t]->ShowersStr);
                    
                            update_post_meta($model->ID,'ToiletsBasic',$models[$t]->ToiletsBasic);
                            update_post_meta($model->ID,'ToiletsMax',$models[$t]->ToiletsMax);
                            update_post_meta($model->ID,'ToiletsStr',$models[$t]->ToiletsStr);

                            update_post_meta($model->ID,'CabinsBasic',$models[$t]->CabinsBasic);
                            update_post_meta($model->ID,'CabinsMax',$models[$t]->CabinsMax);
                            update_post_meta($model->ID,'CabinsStr',$models[$t]->CabinsStr);
                    
                            update_post_meta($model->ID,'BerthsBasic',$models[$t]->BerthsBasic);
                            update_post_meta($model->ID,'BerthsMax',$models[$t]->BerthsMax);
                            update_post_meta($model->ID,'BerthsStr',$models[$t]->BerthsStr);
                            update_post_meta($model->ID,'Updated_time',time());
                            $featured_image=get_post_meta($model->ID,'image_uploaded',true);
                            $count++;
                    

                            if(empty($featured_image))
                            {
                                foreach($models[$t]->BoatImages as $model_image)
                                {
                                    $content=file_get_contents($model_image->ImageURL);
                                    $data = base64_decode($content);
                                    $upload_dir = wp_upload_dir();

                                    //creating new gif image from url
                                    $new_img = @imagecreatefromstring($content);
                                    if ($new_img !== false) 
                                    {
                                        $uploaded_image=true;
                                        $save_name='PhotoBoatModel_'.$models[$t]->ID.".gif";
                                        $file_name=$upload_dir['path'] . '/' . $save_name;
                                        $image_value =@imagegif($new_img, $file_name);
                                        imagedestroy($new_img);
                                        $filename=$upload_dir['url'] . '/' . basename($save_name);
                                        if (!is_wp_error( $tmp ) ) 
                                        {
                                            //id of attached image to post
                                            $filetype = wp_check_filetype( basename($filename), null );

                                            // Prepare an array of post data for the attachment.
                                            $attachment = array
                                            (
	                                           'guid'           => $upload_dir['url'] . '/' . basename($filename), 
	                                           'post_mime_type' => $filetype['type'],
	                                           'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($filename) ),
	                                           'post_content'   => '',
	                                           'post_status'    => 'inherit'
                                            );

                                            // Insert the attachment.
                                            $attach_id = wp_insert_attachment( $attachment, $filename, $model->ID);

                                            // Generate the metadata for the attachment, and update the database record.
                                            $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
                                            wp_update_attachment_metadata( $attach_id, $attach_data );
                                            update_post_meta($model->ID,'image_uploaded',1);
                                            $thubnail=set_post_thumbnail($model->ID, $attach_id);                        
                                        }
                                        break 1;
                                    }
                                }
                            }
                    
                            update_post_meta($model->ID,'Updated_info','full');
                                                       
                            $update_content = array(
                                'ID'           => $model->ID,
                                'post_content' => '[mts_boat_model]<div id="screen">Charter '.$model->post_title.
                                        ' boat in different destinations around the world.'.
                                        ' Reserve online charter boat from our offers. '. 
                                        'Cruise along the Adriatic Sea, islands and coast.</div>');
                            $id_model=wp_update_post($update_content);
                            $wpdb->update('wp_icl_translations', array('language_code'=>'en'), array('element_id'=> $model->ID));
                            if($id_model>0)
                            {
                                delete_post_meta($model->ID,'content_search','full');
                                update_post_meta($model->ID,'for_search','full');
                                echo 'en - Boat model <a href="'.get_permalink($model->ID).'">'.$model->ID.'</a> was updated<br />';
                            }

                    
                            $post_ru_id=icl_object_id($model->ID,'boat_model', false, 'ru' );
                                        
                            if(empty($post_ru_id))
                            {
                                $_POST['icl_post_language'] = 'ru';
                                $postarr=array('post_title'=>$models[$t]->Name,
                                        'post_type'=>'boat_model',
                                        'post_status' => 'publish',
                                        'post_name'=>sanitize_title($models[$t]->Name).'-ru');
                                        
                                $post_new_ru=wp_insert_post($postarr);
                                if($post_new_ru>0)
                                {
                                    // Get trid of original post
                                    $trid = wpml_get_content_trid( 'post_boat_model', $model->ID );
                                    // Get default language
                                    $default_lang = wpml_get_default_language();

                                    $wpdb->update( $wpdb->prefix.'icl_translations', array( 'trid' => $trid, 
                                                        'language_code' => 'ru', 
                                                        'source_language_code' => $default_lang ), 
                                                        array( 'element_id' => $post_new_ru ));
                                    $array_fields=array(0=>'BerthsBasic',1=>'BerthsMax',2=>'BerthsStr',3=>'CabinsBasic',
                                                4=>'CabinsStr',5=>'CabinsMax',6=>'Engine',7=>'Length',8=>'Draft',
                                                9=>'HullLength',10=>'ShowersBasic',11=>'ShowersMax',12=>'ShowersStr',
                                                13=>'ToiletsBasic',14=>'ToiletsStr',15=>'ToiletsMax',16=>'BoatTypeID',
                                                17=>'BrandID',18=>'FuelCapacity',19=>'WaterCapacity',20=>'WaterlineLength',
                                                21=>'Weight',22=>'ModelID');
                                    foreach($array_fields as $num=>$meta)
                                    {
                                        $value=get_post_meta($model->ID,$meta,true);
                                        update_post_meta($post_new_ru,$meta,$value);
                                    }
                            
                                    $update_content = array(
                                        'ID'           => $post_new_ru,
                                        'post_content' => '[mts_boat_model]<div id="screen">Charter '.$model->post_title.
                                        ' boat in different destinations around the world.'.
                                        ' Reserve online charter boat from our offers. '. 
                                        'Cruise along the Adriatic Sea, islands and coast.</div>');
                                    $id_model=wp_update_post($update_content);
                                    $wpdb->update('wp_icl_translations', array('language_code'=>'ru'), array('element_id'=> $post_new_ru));
                                    if($id_model>0)
                                    {
                                        update_post_meta($post_new_ru,'for_search','full');
                                        echo 'ru - Boat model <a href="'.get_permalink($post_new_ru).'">'.
                                        $post_new_ru.'</a> was updated<br />';
                                    }
                                                    
                            
                                    $image_up=get_post_meta($post_new_ru,'image_uploaded',true);
                                    if(empty($image_up))
                                    {
                                        $args = array(
                                            'post_type' => 'attachment',
                                            'numberposts' => -1,
                                            'post_status' => null,
                                            'post_parent' => $model->ID);
                
                                            $attachments = get_posts( $args );
                                            if ( $attachments ) 
                                            {
                                                foreach ( $attachments as $attachment ) 
                                                {
                                                    $array_image=wp_get_attachment_metadata( $attachment->ID);
                                                    $wp_filetype = wp_check_filetype(basename($array_image['file']), null );
                                                    $attachment2 = array(
                                                        'post_mime_type' => $wp_filetype['type'],
                                                        'post_title' => 'Photo_Model_ru_'.$models[$t]->ID,
                                                        'post_content' => '',
                                                        'post_status' => 'inherit');
                                    
                                                    $attach_id = wp_insert_attachment( $attachment2, $array_image['file'], 
                                                                $post_new_ru);

                                                    // Generate the metadata for the attachment, and update the database record.
                                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $array_image['file'] );
                                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                                    update_post_meta($post_new_ru,'image_uploaded',1);
                                                    $thubnail=set_post_thumbnail($post_new_ru, $attachment->ID);
                                    
                                                    // Generate the metadata for the attachment, and update the database record.
                                                    $attach_data = wp_generate_attachment_metadata( $attachment->ID, $filename );
                                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                                }
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if($post_ru_id>0)
                                    {
                                        $image_up=get_post_meta($post_ru_id,'image_uploaded',true);
                                        if(empty($image_up))
                                        {
                                            $args = array(
                                                'post_type' => 'attachment',
                                                'numberposts' => -1,
                                                'post_status' => null,
                                                'post_parent' => $model->ID);

                                            $attachments = get_posts( $args );
                                            if ( $attachments ) 
                                            {
                                                foreach ( $attachments as $attachment ) 
                                                {
                                                    $array_image=wp_get_attachment_metadata( $attachment->ID);
                                    
                                                    $wp_filetype = wp_check_filetype(basename($array_image['file']), null );
                                                    $attachment2 = array(
                                                        'post_mime_type' => $wp_filetype['type'],
                                                        'post_title' => 'Photo_Model_ru_'.$models[$t]->ID,
                                                        'post_content' => '',
                                                        'post_status' => 'inherit');
                                                    $attach_id = wp_insert_attachment( $attachment2, $array_image['file'], 
                                                                $post_ru_id);

                                                    // Generate the metadata for the attachment, and update the database record.
                                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $array_image['file'] );
                                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                                    update_post_meta($post_ru_id,'image_uploaded',1);
                                                    $thubnail=set_post_thumbnail($post_ru_id, $attachment->ID);
                                                }
                                            }
                                        }
       
                                        $array_fields=array(0=>'BerthsBasic',1=>'BerthsMax',2=>'BerthsStr',3=>'CabinsBasic',
                                                4=>'CabinsStr',5=>'CabinsMax',6=>'Engine',7=>'Length',8=>'Draft',
                                                9=>'HullLength',10=>'ShowersBasic',11=>'ShowersMax',12=>'ShowersStr',
                                                13=>'ToiletsBasic',14=>'ToiletsStr',15=>'ToiletsMax',16=>'BoatTypeID',
                                                17=>'BrandID',18=>'FuelCapacity',19=>'WaterCapacity',20=>'WaterlineLength',
                                                21=>'Weight',22=>'ModelID');
                                        foreach($array_fields as $num=>$meta)
                                        {
                                            $value=get_post_meta($model->ID,$meta,true);
                                            update_post_meta($post_ru_id,$meta,$value);
                                        }
                                    
                                        $update_content = array(
                                            'ID'           => $post_ru_id,
                                            'post_content' => '[mts_boat_model]<div id="screen">Charter '.$model->post_title.
                                                        ' boat in different destinations around the world.'.
                                                        ' Reserve online charter boat from our offers. '. 
                                                        'Cruise along the Adriatic Sea, islands and coast.</div>');
                                            $id_model=wp_update_post($update_content);
                                            $wpdb->update('wp_icl_translations', array('language_code'=>'ru'), array('element_id'=> $post_ru_id));
                                            if($id_model>0)
                                            {
                                                update_post_meta($post_ru_id,'for_search','full');
                                                echo 'ru2 - Boat model <a href="'.get_permalink($post_ru_id).'">'.
                                                        $post_ru_id.'</a> was updated<br />';
                                            }
                                        }
                                    }
                                }        
                            }
                        }
                    }

                    if($count>5 || $uploaded_image==true)
                    {
                        break;
                    }
        
                }
        
        return $count;
    }
    
    
    public function singleModel($postid,$modelid)
    {
         global $iclTranslationManagement, $sitepress, $ICL_Pro_Translation, $wpdb;
         include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
        $count=0;
        
        $models=$this->getBoatModels(); 
   
         // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );  
        
        $excluded_terms=array('Tempest 605', 'Signature 280 Cruiser', 'SeaDan 360OF Open');

        $loaded_models=0;
        
        $updated_images=0;
        //from 1000 to 1200 already uodated
        for($t=0;$t<count($models);$t++)
        {
            $tax_name_model=str_replace("'","",$models[$t]->Name);
            $tax_name_model=str_replace("&","-",$tax_name_model);
            
            if($modelid==$models[$t]->ID)
            {
                    echo $count.'<br />';
                    delete_post_meta($postid,'Updated_type'); 
                    update_post_meta($postid,'BoatTypeID',$models[$t]->BoatTypeID);
                    update_post_meta($postid,'BrandID',$models[$t]->BrandID);
                    update_post_meta($postid,'FuelCapacity',$models[$t]->FuelCapacity);
                    update_post_meta($postid,'WaterCapacity',$models[$t]->WaterCapacity);
                    update_post_meta($postid,'WaterlineLength',$models[$t]->WaterlineLength);
                    update_post_meta($postid,'Weight',$models[$t]->Weight);
                    update_post_meta($postid,'Engine',$models[$t]->Engine);
                    update_post_meta($postid,'Draft',$models[$t]->Draft);
                    update_post_meta($postid,'Length',$models[$t]->Length);
                    update_post_meta($postid,'HullLength',$models[$t]->HullLength);
                    
                    update_post_meta($postid,'ShowersBasic',$models[$t]->ShowersBasic);
                    update_post_meta($postid,'ShowersMax',$models[$t]->ShowersMax);
                    update_post_meta($postid,'ShowersStr',$models[$t]->ShowersStr);
                    
                    update_post_meta($postid,'ToiletsBasic',$models[$t]->ToiletsBasic);
                    update_post_meta($postid,'ToiletsMax',$models[$t]->ToiletsMax);
                    update_post_meta($postid,'ToiletsStr',$models[$t]->ToiletsStr);

                    update_post_meta($postid,'CabinsBasic',$models[$t]->CabinsBasic);
                    update_post_meta($postid,'CabinsMax',$models[$t]->CabinsMax);
                    update_post_meta($postid,'CabinsStr',$models[$t]->CabinsStr);
                    
                    update_post_meta($postid,'BerthsBasic',$models[$t]->BerthsBasic);
                    update_post_meta($postid,'BerthsMax',$models[$t]->BerthsMax);
                    update_post_meta($postid,'BerthsStr',$models[$t]->BerthsStr);
                    update_post_meta($postid,'Updated_time',time());
                    $featured_image=get_post_meta($postid,'image_uploaded',true);
                    

                    if(empty($featured_image))
                    {
                        foreach($models[$t]->BoatImages as $model_image)
                        {
                            
                            $content=file_get_contents($model_image->ImageURL);
                            $data = base64_decode($content);
                            $upload_dir = wp_upload_dir();

                            //creating new gif image from url
                            $new_img = @imagecreatefromstring($content);
                            if ($new_img !== false) 
                            {
                                //header( "Content-type: image/gif" );
                                $save_name='PhotoBoatModel_'.$models[$t]->ID.".gif";
                                $image_value =imagegif($new_img, $upload_dir['path'] . '/' . $save_name);
                                imagedestroy($new_img);
                                //chmod("test.txt",0600);
                                $filename=$upload_dir['url'] . '/' . basename($save_name);
                                //chmod($upload_dir['path'] . '/' . $save_namee,0755);
                                if (!is_wp_error( $tmp ) ) 
                                {
                                    //id of attached image to post
                                    $filetype = wp_check_filetype( basename($filename), null );

                                    // Prepare an array of post data for the attachment.
                                    $attachment = array
                                    (
	                                   'guid'           => $upload_dir['url'] . '/' . basename($filename), 
	                                   'post_mime_type' => $filetype['type'],
	                                   'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($filename) ),
	                                   'post_content'   => '',
	                                   'post_status'    => 'inherit'
                                    );

                                    // Insert the attachment.
                                    $attach_id = wp_insert_attachment( $attachment, $filename, $postid);

                                    // Generate the metadata for the attachment, and update the database record.
                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                    update_post_meta($postid,'image_uploaded',1);
                                    $thubnail=set_post_thumbnail($postid, $attach_id);                        
                                }
                                break 1;
                            }
                            $count++;
                            echo $count.'Image';
                        
                        }
                        
                        
                    }
                    
                    update_post_meta($postid,'Updated_info','full');
                    $update_content = array(
                                'ID'           => $postid,
                                'post_content' => '[mts_boat_model]');
                    $id_post=wp_update_post($update_content);
                    echo 'Boat model <a href="'.get_permalink($postid).'">'. $postid.'</a> was updated<br />';
                    
                    
                    $post_ru_id=icl_object_id($postid,'boat_model', false, 'ru' );
                                        
                    if(empty($post_ru_id))
                    {
                        $_POST['icl_post_language'] = 'ru';
                        $postarr=array('post_title'=>$tax_name_model,
                                        'post_type'=>'boat_model',
                                        'post_status' => 'publish',
                                        'post_name'=>sanitize_title($tax_name_model).'-ru');
                                        
                        $post_new_ru=wp_insert_post($postarr);
                        if($post_new_ru>0)
                        {
                            // Get trid of original post
                            $trid = wpml_get_content_trid( 'post_boat_model', $postid );
                            // Get default language
                            $default_lang = wpml_get_default_language();

                            $wpdb->update( $wpdb->prefix.'icl_translations', array( 'trid' => $trid, 
                                                        'language_code' => 'ru', 
                                                        'source_language_code' => $default_lang ), 
                                            array( 'element_id' => $post_new_ru ) );
                            $array_fields=array(0=>'BerthsBasic',1=>'BerthsMax',2=>'BerthsStr',3=>'CabinsBasic',
                                                4=>'CabinsStr',5=>'CabinsMax',6=>'Engine',7=>'Length',8=>'Draft',
                                                9=>'HullLength',10=>'ShowersBasic',11=>'ShowersMax',12=>'ShowersStr',
                                                13=>'ToiletsBasic',14=>'ToiletsStr',15=>'ToiletsMax',16=>'BoatTypeID',
                                                17=>'BrandID',18=>'FuelCapacity',19=>'WaterCapacity',20=>'WaterlineLength',
                                                21=>'Weight',22=>'ModelID');
                            foreach($array_fields as $num=>$meta)
                            {
                                $value=get_post_meta($postid,$meta,true);
                                update_post_meta($post_new_ru,$meta,$value);
                            }
                                                    
                            $update_content = array(
                                'ID'           => $post_new_ru,
                                    'post_content' => '[mts_boat_model]<div id="screen"></div>');
                            $post_new_ru=wp_update_post($update_content);
                            
                            $image_up=get_post_meta($post_new_ru,'image_uploaded',true);
                                if(empty($image_up))
                                {
                            $args = array(
                                'post_type' => 'attachment',
                                'numberposts' => -1,
                                'post_status' => null,
                                'post_parent' => $postid);
                    $id_post=wp_update_post($update_content);

                            $attachments = get_posts( $args );
                             if ( $attachments ) 
                             {
                                foreach ( $attachments as $attachment ) 
                                {
                                    $array_image=wp_get_attachment_metadata( $attachment->ID);
                                    echo basename($array_image['file']);
                                    $wp_filetype = wp_check_filetype(basename($array_image['file']), null );
                                $attachment2 = array(
                                    'post_mime_type' => $wp_filetype['type'],
                                    'post_title' => 'Photo_Model_ru_'.$models[$t]->ID,
                                    'post_content' => '',
                                    'post_status' => 'inherit');
                                    
                                     $attach_id = wp_insert_attachment( $attachment2, $array_image['file'], 
                                            $post_new_ru);

                                    // Generate the metadata for the attachment, and update the database record.
                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $array_image['file'] );
                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                    update_post_meta($post_new_ru,'image_uploaded',1);
                                    $thubnail=set_post_thumbnail($post_new_ru, $attachment->ID);
                                    
 
                                    // Generate the metadata for the attachment, and update the database record.
                                    $attach_data = wp_generate_attachment_metadata( $attachment->ID, $filename );
                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                    //update_post_meta($model_post[0]->ID,'image_uploaded',1);
                                    //title of type of image
                                     $count++;
                                }
                            }
                            
                            }
                            
                            }
                    
                        }
                        else
                        {
                            if($post_ru_id>0)
                            {
                                //$image_up=get_post_meta($post_ru_id,'image_uploaded',true);
                                //if(empty($image_up))
                                //{
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
                                    $array_image=wp_get_attachment_metadata( $attachment->ID);
                                    echo basename($array_image['file']);
                                    $wp_filetype = wp_check_filetype(basename($array_image['file']), null );
                                $attachment2 = array(
                                    'post_mime_type' => $wp_filetype['type'],
                                    'post_title' => 'Photo_Model_ru_'.$models[$t]->ID,
                                    'post_content' => '',
                                    'post_status' => 'inherit');
                                     $attach_id = wp_insert_attachment( $attachment2, $array_image['file'], 
                                            $post_ru_id);

                                    // Generate the metadata for the attachment, and update the database record.
                                    $attach_data = wp_generate_attachment_metadata( $attach_id, $array_image['file'] );
                                    wp_update_attachment_metadata( $attach_id, $attach_data );
                                    update_post_meta($post_ru_id,'image_uploaded',1);
                                    $thubnail=set_post_thumbnail($post_ru_id, $attachment->ID);
                                    $count++;
                                    // Generate the metadata for the attachment, and update the database record.
                                    //$attach_data = wp_generate_attachment_metadata( $attachment->ID, $filename );
                                    //wp_update_attachment_metadata( $attach_id, $attach_data );
                                    //update_post_meta($model_post[0]->ID,'image_uploaded',1);
                                    //title of type of image
                                }
                            }
                            
                            
                            //}
                            $array_fields=array(0=>'BerthsBasic',1=>'BerthsMax',2=>'BerthsStr',3=>'CabinsBasic',
                                                4=>'CabinsStr',5=>'CabinsMax',6=>'Engine',7=>'Length',8=>'Draft',
                                                9=>'HullLength',10=>'ShowersBasic',11=>'ShowersMax',12=>'ShowersStr',
                                                13=>'ToiletsBasic',14=>'ToiletsStr',15=>'ToiletsMax',16=>'BoatTypeID',
                                                17=>'BrandID',18=>'FuelCapacity',19=>'WaterCapacity',20=>'WaterlineLength',
                                                21=>'Weight',22=>'ModelID');
                                    foreach($array_fields as $num=>$meta)
                                    {
                                        $value=get_post_meta($postid,$meta,true);
                                        update_post_meta($post_ru_id,$meta,$value);
                                    }
                            }
                        }
                   
               }        
            if($count>1)
            {
                break;
            }
        
        }
        
        //$this->search_model();

    }
    
    
    public function getBoatTypes()
    {
        $query='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadBoatTypes=True';
        
        $models=json_decode(file_get_contents($query));
        
        return $models->BoatTypes;
    }
    
    
    //import all boat types with translation into Russian
    public function import_transl_types()
    {
        global $sitepress, $wpdb;
        $count=0;
        $types=$this->getBoatTypes();
        $default_lang = $sitepress->get_default_language();
        foreach ($types as $type)
        {
            $type_tax_id=0;
            
            $tax_type = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".$type->Name->EN."'", OBJECT);
            if(!empty($tax_type) && count($tax_type)>0)
            {
                for ($i=0;$i<count($tax_type);$i++)
                {
                    $type_id = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE term_id=".$tax_type[$i]->term_id, OBJECT);
                    for($j=0;$j<count($type_id);$j++)
                    {
                        if($type_id[$j]->taxonomy==='boattype')
                        {
                            $type_tax_id=$type_id[$j]->term_id;
                        }
                    }  
                } 
            }
            if($type_tax_id==0)
            {
                $_POST['icl_tax_destination_language'] = $default_lang;
                $tax_boat_type=wp_insert_term($type->Name->EN,'boattype');
                if(!is_wp_error($tax_boat_type))
                {
                    update_option( "tax_typeid_".$tax_boat_type['term_id'],$type->ID);
                }
            }
        }
    }
    
    
    
    //checking boat types of all boats in Russian language
    public function duplicat_transl_types()
    {
        global $sitepress, $wpdb;
        $count=0;
        $types=$this->getBoatTypes();
        $default_lang = $sitepress->get_default_language();
        
         $arr_post=array(
            'post_type' => 'boat_post',
            'orderby'=>'ID',
            'order' => 'ASC',
            'posts_per_page' => 2,
            'meta_query' => array(
                    array(
                        'key' => 'boat_type_transl',
                        'compare' => 'NOT EXISTS',
                        )));


        $boat_posts=get_posts($arr_post);
        foreach($boat_posts as $post)
        {
            $modelid=get_post_meta($post->ID,'ModelID',true);
            if(!empty($modelid))
            {
                echo $modelid.'<br />';
                $count++;
                
                $arr_model=array('post_type' => 'boat_model',
		                              'meta_key' => 'ModelID',
                                      'meta_value'=>$modelid,
                                      'meta_compare'=>'=',
                                      'meta_type'=>'CHAR');
                $model_posts=get_posts($arr_model);
               
                if(!empty($model_posts))
                {
                    
                     echo 'Boat model <a href="'.get_permalink($model_posts[0]->ID).'">'. $model_posts[0]->ID.'</a> was updated <br />';
                    
                    $typeid=get_post_meta($model_posts[0]->ID,'BoatTypeID',true);
                     
                    if(!empty($typeid))
                    {
                        
                        $post_language_information = wpml_get_language_information($post->ID);
                        
                        if(isset($post_language_information['locale']) && $post_language_information['locale']==="ru_RU")
                        {
                                //cheking boat type in Russian boat post
                        }
                        else
                        {
                            
                            $post_ru_id=icl_object_id($post->ID,'boat_post', false, 'ru' );
                            if($post_ru_id>0)
                            {
                                $types_trans = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE taxonomy=".
                                                                "'boattype'", OBJECT);
                                
                                if(!empty($types_trans) && count($types_trans)>0)
                                {
                                    for ($i=0;$i<count($types_trans);$i++)
                                    {
                                        $type_id_transl=get_option( "tax_typeid_".$types_trans[$i]->term_id);
                                        if($type_id_transl==$typeid)
                                        {
                                            echo $type_id_transl.'<br />';
                                            $type_en_names = $wpdb->get_results("SELECT * FROM wp_terms WHERE term_id=".$types_trans[$i]->term_id, OBJECT);
                                           
                                            if(!empty($type_en_names))
                                            {
                                                 
                                                $cat_ids = array_map( 'intval', $types_trans[$i]->term_id);
                                                $cat_ids = array_unique( $cat_ids );
                                                
                                                wp_set_object_terms($post->ID,$cat_ids,'boattype');
                                                update_post_meta($post->ID,'boat_type_transl',1);
                                                echo 'Boat <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated type<br />';
                    
                                                
                                                $term_ru_id=icl_object_id($types_trans[$i]->term_id,'boattype', false, 'ru' );
                                                if($term_ru_id>0)
                                                {
                                                    $cat_ids = array_map( 'intval', array($term_ru_id));
                                                    $cat_ids = array_unique( $cat_ids );
                                                    wp_set_object_terms($post_ru_id,$cat_ids,'boattype');
                                                    update_post_meta($post->ID,'boat_type_transl',2);
                                                }
                                                
                                                $type_names = $wpdb->get_results("SELECT * FROM wp_terms WHERE name='".
                                                                                    $type_en_names[0]->name."'", OBJECT);
                                                if(!empty($type_names))
                                                {
                                                    foreach($type_names as $type_en)
                                                    {
                                                         $types_trans = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE term_id=".
                                                                $type_en->term_id, OBJECT);
                                                         if(!empty($types_trans) && 
                                                                    $types_trans[0]->taxonomy=='bt_type')
                                                         {
                                                            $cat_ids = array_map( 'intval', array($types_trans[$i]->term_id));
                                                            $cat_ids = array_unique( $cat_ids );
                                                
                                                            wp_set_object_terms($post->ID,$cat_ids,'bt_type');
                                                            update_post_meta($post->ID,'boat_type',1);
                                                         }
                                                    }
                                                }
                                           

                                            }
                    
                                            break 1;
                                        }
        
                                    } 
                                }
                                
                            }
                            else
                            {
                                $types_trans = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE taxonomy=".
                                                                "'boattype'", OBJECT);
                                
                                if(!empty($types_trans) && count($types_trans)>0)
                                {
                                    for ($i=0;$i<count($types_trans);$i++)
                                    {
                                        $type_id_transl=get_option( "tax_typeid_".$types_trans[$i]->term_id);
                                        if($type_id_transl==$typeid)
                                        {
                                            
                                            $type_en_names = $wpdb->get_results("SELECT * FROM wp_terms WHERE term_id=".$types_trans[$i]->term_id, OBJECT);
                                           
                                            if(!empty($type_en_names))
                                            {
                                                 
                                                $cat_ids = array_map( 'intval', array($types_trans[$i]->term_id));
                                                $cat_ids = array_unique( $cat_ids );
                                                
                                                wp_set_object_terms($post->ID,$cat_ids,'boattype');
                                                update_post_meta($post->ID,'boat_type_transl',0);
                                                echo 'Boat <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated type<br />';
                    
                                                
                                                $type_names = $wpdb->get_results("SELECT * FROM wp_terms WHERE name='".
                                                                                    $type_en_names[0]->name."'", OBJECT);
                                                if(!empty($type_names))
                                                {
                                                    foreach($type_names as $type_en)
                                                    {
                                                         $types_trans = $wpdb->get_results("SELECT * FROM wp_term_taxonomy WHERE term_id=".
                                                                $type_en->term_id, OBJECT);
                                                         if(!empty($types_trans) && 
                                                                    $types_trans[0]->taxonomy=='bt_type')
                                                         {
                                                            
                                                            $cat_ids = array_map( 'intval', array($types_trans[0]->term_id));
                                                            $cat_ids = array_unique( $cat_ids );
                                                
                                                            wp_set_object_terms($post->ID,$cat_ids,'bt_type');
                                                            update_post_meta($post->ID,'boat_type',1);
                                                         }
                                                    }
                                                }
                                           

                                            }
                    
                                            break 1;
                                        }
        
                                    } 
                                }
                                //update_post_meta($post->ID,'boat_type_transl',1);
                            }
                        }
                         
                    }
                    else
                    {
                        update_post_meta($post->ID,'boat_type_transl',0);
                    }
                    
                                     
                    $term_model = $wpdb->get_results( "SELECT * FROM wp_terms WHERE name='".
                                            $model_posts[0]->post_title."'", OBJECT );

                    if(!empty($term_model))
                    {
                        
                        if(isset($post_language_information['locale']) && $post_language_information['locale']==="ru_RU")
                        {
                                //cheking boat type in Russian boat post
                        }
                        else
                        {
                            
                            $post_ru_id=icl_object_id($post->ID,'boat_post', false, 'ru' );
                            if($post_ru_id>0)
                            {
                                foreach ($term_model as $key=>$cat_model)
                        {
                            $tax_model = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$cat_model->term_id, OBJECT);
                            if(!empty($tax_model))
                            {
                                foreach ($tax_model as $key2=>$tax_name)
                                {
                                    if($tax_name->taxonomy=='bt_model')
                                    {
                                        
                                        $id_cat_model=array();
                                        $id_cat_model[]=$tax_name->term_id;
                                        $cat_ids = array_map( 'intval', $id_cat_model );
                                        $cat_ids = array_unique( $cat_ids );
                                        wp_set_object_terms($post->ID,$cat_ids,'bt_model');
                                        update_post_meta($post->ID,'boat_model',1);
                                    }
                                    if($tax_name->taxonomy=='boatmodel')
                                    {
                                        
 
                                            $term_ru_id=icl_object_id($tax_name->term_id,'boatmodel', false, 'ru' );
                                            if($term_ru_id>0)
                                            {
                                                $cat_ids = array_map( 'intval', $term_ru_id);
                                                $cat_ids = array_unique( $cat_ids );
                                                wp_set_object_terms($post_ru_id,$cat_ids,'boatmodel');
                                                update_post_meta($post->ID,'boat_model_transl',2);
                                            }
                                            
                                            
                                            $id_cat_model=array();
                                            $id_cat_model[]=$tax_name->term_id;
                                            $cat_ids = array_map( 'intval', $id_cat_model );
                                            $cat_ids = array_unique( $cat_ids );
                                            wp_set_object_terms($post->ID,$cat_ids,'boatmodel');
                                            update_post_meta($post->ID,'boat_model_transl',1);

                                        echo 'Boat <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated model<br />';
                    
                                    }
                                }
                            }
                        } 
                            }
                            else
                            {
                                foreach ($term_model as $key=>$cat_model)
                        {
                            $tax_model = $wpdb->get_results( "SELECT * FROM wp_term_taxonomy WHERE term_id=".$cat_model->term_id, OBJECT);
                            if(!empty($tax_model))
                            {
                                foreach ($tax_model as $key2=>$tax_name)
                                {
                                    if($tax_name->taxonomy=='bt_model')
                                    {
                                        
                                        $id_cat_model=array();
                                        $id_cat_model[]=$tax_name->term_id;
                                        $cat_ids = array_map( 'intval', $id_cat_model );
                                        $cat_ids = array_unique( $cat_ids );
                                        wp_set_object_terms($post->ID,$cat_ids,'bt_model');
                                        update_post_meta($post->ID,'boat_model',1);
                                    }
                                    if($tax_name->taxonomy=='boatmodel')
                                    {
                                        
                                            
                                            $id_cat_model=array();
                                            $id_cat_model[]=$tax_name->term_id;
                                            $cat_ids = array_map( 'intval', $id_cat_model );
                                            $cat_ids = array_unique( $cat_ids );
                                            wp_set_object_terms($post->ID,$cat_ids,'boatmodel');
                                            update_post_meta($post->ID,'boat_model_transl',1);

                                        echo 'Boat <a href="'.get_permalink($post->ID).'">'. $post->ID.'</a> was updated model<br />';
                    
                                    }
                                }
                            }
                        } 
                            }
                        }
                        
                        
                    }
                }
                else
                {
                    update_post_meta($post->ID,'boat_type_transl',0);
                }
                                    
                                    

            }
             
        }
        return $count;
      
    }
    
    
    public function getExtraImages()
    {
        $query_image='https://api.boatbooker.net/Search/GetBoatImage.ashx?ObjectID=309&DocumentID=2843&nodisposition=true';
         require_once( ABSPATH . 'wp-admin/includes/image.php' );
          $content=file_get_contents($query_image);
          $data = base64_decode($content);
          $upload_dir = wp_upload_dir();

            //creating new gif image from url
            $new_img = @imagecreatefromstring($content);
            if ($new_img !== false) 
            {
                //header( "Content-type: image/gif" );
                $save_name='Image_pinterest.gif';
                $image_value =imagegif($new_img, $upload_dir['path'] . '/' . $save_name);
                                //imagedestroy($new_img);
                                //chmod("test.txt",0600);
                $filename=$upload_dir['url'] . '/' . basename($save_name);
                                //chmod($upload_dir['path'] . '/' . $save_namee,0755);
                if (!is_wp_error( $tmp ) ) 
                {
                                    //id of attached image to post
                    $filetype = wp_check_filetype( basename($filename), null );

                                    // Prepare an array of post data for the attachment.
                    $attachment = array
                    (
	                                   'guid'           => $upload_dir['url'] . '/' . basename($filename), 
	                                   'post_mime_type' => $filetype['type'],
	                                   'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($filename) ),
	                                   'post_content'   => '',
	                                   'post_status'    => 'inherit'
                    );

                                    // Insert the attachment.
                $attach_id = wp_insert_attachment( $attachment, $filename, $postid);

                      
            }
        }
        
        
    }
    
    public function search_model()
    {
        global $wpdb;
        $arr_model=array(
                'post_type' => 'boat_model',
                'orderby'=>'ID',
                'order' => 'DESC',
                'posts_per_page' => 150,
                'meta_key' => 'for_search',
                'meta_compare'=>'NOT EXISTS');
        $models_content=get_posts($arr_model);
        foreach($models_content as $model)
        {
            $post_language_information = wpml_get_language_information($model->ID);
           
            $update_content = array(
                    'ID'           => $model->ID,
                    'post_content' => '[mts_boat_model]<div id="screen">Charter '.$model->post_title.
                                        ' boat in different destinations around the world.'.
                                        ' Reserve online charter boat from our offers. '. 
                                        'Cruise along the Adriatic Sea, islands and coast.</div>');
            if(isset($post_language_information['locale']) && $post_language_information['locale']==="ru_RU")
            {
                $id_model=wp_update_post($update_content);
                $wpdb->update('wp_icl_translations', array('language_code'=>'ru'), array('element_id'=> $model->ID));
                if($id_model>0)
                {
                    delete_post_meta($model->ID,'content_search','full');
                    update_post_meta($model->ID,'for_search','full');
                    echo 'ru - Boat model <a href="'.get_permalink($model->ID).'">'. $model->ID.'</a> was updated<br />';
                }
            }
            else
            {
                if(isset($post_language_information['locale']) && $post_language_information['locale']==="en_US")
                {
                $id_model=wp_update_post($update_content);
                $wpdb->update('wp_icl_translations', array('language_code'=>'en'), array('element_id'=> $model->ID));
                if($id_model>0)
                {
                    delete_post_meta($model->ID,'content_search','full');
                    update_post_meta($model->ID,'for_search','full');
                    echo 'en - Boat model <a href="'.get_permalink($model->ID).'">'. $model->ID.'</a> was updated<br />';
                }
                }
                else
                {
                    $id_model=wp_update_post($update_content);
                    //$wpdb->update('wp_icl_translations', array('language_code'=>'en'), array('element_id'=> $model->ID));
                    if($id_model>0)
                    {
                        delete_post_meta($model->ID,'content_search','full');
                        update_post_meta($model->ID,'for_search','full');
                        echo 'Boat model <a href="'.get_permalink($model->ID).'">'. $model->ID.'</a> was updated<br />';
                    }
                }
            }
        }
        
        return true;
    }
    
    public function getExtraPrices()
    {
        $query='https://api.boatbooker.net/ws/sync/v2/main?username='.$this->booker_user.
                '&password='.$this->booker_pass.'&loadExtras=True';
        
        $extras=json_decode(file_get_contents($query));
        
        return $extras->Extras;
    }
    
    public function import_extras_transl()
    {
         $arr_model=array(
                'post_type' => 'extra_prices',
                'orderby'=>'ID',
                'order' => 'ASC',
                'posts_per_page' => -1);
         $extras=get_posts($arr_model);
           $count=0;
         foreach($extras as $post)
         {
            $count++;
            wp_delete_post($post->ID,true);
            if($count>50)
            {
                break;
            }
         }
            
        $extra_prices=$this->getExtraPrices();
      
       
        foreach ($extra_prices as $extraprice)
        {
            $count_extras=count($extraprice->ExtraInfos);
            $extrainfo=$extraprice->ExtraInfos;
            for($i=0;$i<$count_extras;$i++)
            {
                foreach($extrainfo[$i]->Prices as $price)
                {
                    echo $price->ID.' '.$price->CurrencyCode.' '.$price->Name->EN.'<br />';
                }
          
                //service id or equipment id
                if(empty($extrainfos[$i]->IsObligatory))
                {
                    echo 'additional<br />';
                }
                if(isset($extrainfo[$i]->BoatEquipmentID) &&
                        !empty($extrainfo[$i]->BoatEquipmentID))
                {
                    echo $extrainfo[$i]->BoatEquipmentID.'<br />';
                }
                
                if(isset($extrainfo[$i]->ServiceID) &&
                        !empty($extrainfo[$i]->ServiceID))
                {
                    echo $extrainfo[$i]->ServiceID.'<br />';
                }
               
                
                $count++;
                if($count >10)
                {
                    break 2;
                }
            }
        }
        
        return $count;
    }

}
