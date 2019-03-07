<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_ajax_deactivate_license', array('NEXForms_Database_Actions','deactivate_license'));
add_action('wp_ajax_nf_insert_record', array('NEXForms_Database_Actions','insert_record'));
add_action('wp_ajax_nf_update_record', array('NEXForms_Database_Actions','update_record'));
add_action('wp_ajax_nf_update_draft', array('NEXForms_Database_Actions','update_draft'));
add_action('wp_ajax_nf_delete_record', array('NEXForms_Database_Actions','delete_record'));
add_action('wp_ajax_nf_duplicate_record', array('NEXForms_Database_Actions','duplicate_record'));
add_action('wp_ajax_nf_delete_file', array('NEXForms_Database_Actions','delete_file'));


add_action('wp_ajax_preview_nex_form', array('NEXForms_Database_Actions','preview_nex_form'));
add_action('wp_ajax_nf_get_forms', array('NEXForms_Database_Actions','get_forms'));
add_action('wp_ajax_nf_load_nex_form', array('NEXForms_Database_Actions','load_nex_form'));
add_action('wp_ajax_nf_get_email_setup', array('NEXForms_Database_Actions','get_email_setup'));
add_action('wp_ajax_nf_get_pdf_setup', array('NEXForms_Database_Actions','get_pdf_setup'));
add_action('wp_ajax_nf_get_options_setup', array('NEXForms_Database_Actions','get_options_setup'));
add_action('wp_ajax_nf_hidden_fields', array('NEXForms_Database_Actions','get_hidden_fields'));
add_action('wp_ajax_nf_load_form_entries', array('NEXForms_Database_Actions','load_form_entries'));
add_action('wp_ajax_nf_populate_form_entry', array('NEXForms_Database_Actions','populate_form_entry'));
add_action('wp_ajax_nf_load_pagination', array('NEXForms_Database_Actions','load_pagination'));

add_action('wp_ajax_nf_populate_form_entry_dashboard', array('NEXForms_Database_Actions','populate_form_entry'));


add_action( 'wp_ajax_save_email_config', array('NEXForms_Database_Actions','save_email_config'));
add_action( 'wp_ajax_save_script_config', array('NEXForms_Database_Actions','save_script_config'));
add_action( 'wp_ajax_save_style_config', array('NEXForms_Database_Actions','save_style_config'));
add_action( 'wp_ajax_save_other_config', array('NEXForms_Database_Actions','save_other_config'));
add_action( 'wp_ajax_save_mc_key', array('NEXForms_Database_Actions','save_mc_key'));
add_action( 'wp_ajax_save_gr_key', array('NEXForms_Database_Actions','save_gr_key'));

add_action( 'wp_ajax_nf_create_custom_layout', array('NEXForms_Database_Actions','create_custom_layout'));
add_action( 'wp_ajax_nf_load_custom_layout', array('NEXForms_Database_Actions','load_custom_layout'));
add_action( 'wp_ajax_nf_delete_custom_layout', array('NEXForms_Database_Actions','delete_custom_layout'));


add_action( 'wp_ajax_do_form_import', array('NEXForms_Database_Actions','do_form_import'));


add_action('wp_ajax_nf_load_conditional_logic', array('NEXForms_Database_Actions','load_conditional_logic'));
add_action('wp_ajax_nf_send_test_email', array('NEXForms_Database_Actions','nf_send_test_email'));

//add_action( 'wp_ajax_do_form_import', array('NEXForms_Database_Actions','do_form_import'));
add_action('wp_ajax_update_paypal', array('NEXForms_Database_Actions','update_paypal'));
add_action( 'wp_ajax_nf_buid_paypal_products', array('NEXForms_Database_Actions','buid_paypal_products'));

add_action('wp_ajax_get_data', array('NEXForms_Database_Actions','NEXForms_get_data'));





if(!class_exists('NEXForms_Database_Actions'))
	{
	class NEXForms_Database_Actions{

/* INSERT */
		
		public function checkout()
			{
			$api_params = array( 'check_key' => 1,'ins_data'=>get_option('7103891'));
			$response = wp_remote_post( 'http://basixonline.net/activate-license-test', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
			return ($response['body']=='true') ? true : false;	
			}
		
		public function insert_record(){
			global $wpdb;
			
			$db_table = sanitize_text_field($_POST['table']);
			
			$fields 	= $wpdb->get_results("SHOW FIELDS FROM " . $wpdb->prefix .filter_var($db_table,FILTER_SANITIZE_STRING));
			$field_array = array();
			foreach($fields as $field)
				{
				if(isset($_POST[$field->Field]))
					{
					$field_array[$field->Field] = $_POST[$field->Field];
					}	
				}
			$insert = $wpdb->insert ( $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING), $field_array );
			$insert_id = $wpdb->insert_id;
			echo $insert_id;
			die();
		}
		
/* UPDATE */
		
		
		public function update_record(){
			global $wpdb;
			
			$db_table = sanitize_text_field($_POST['table']);
			
			$edit_id = sanitize_text_field($_POST['edit_Id']);
			
			$fields 	= $wpdb->get_results("SHOW FIELDS FROM " . $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING));
			$field_array = array();
			foreach($fields as $field)
				{
				if(isset($_POST[$field->Field]))
					{
					if(is_array($_POST[$field->Field]))
						$field_array[$field->Field] = json_encode($_POST[$field->Field],JSON_FORCE_OBJECT);
					else
						$field_array[$field->Field] = $_POST[$field->Field];
					}	
				}
			$update = $wpdb->update ( $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING), $field_array, array(	'Id' => filter_var($edit_id,FILTER_SANITIZE_NUMBER_INT)) );
			echo filter_var($edit_id,FILTER_SANITIZE_NUMBER_INT);
			die();
		}

	public function update_paypal(){
		global $wpdb;
		
		$db_table = sanitize_text_field($_POST['table']);
		
		$nex_forms_Id = sanitize_text_field($_POST['nex_forms_Id']);
		
		$fields 	= $wpdb->get_results("SHOW FIELDS FROM " . $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING));
		$field_array = array();
		foreach($fields as $field)
			{
			if(isset($_POST[$field->Field]))
				{
				if(is_array($_POST[$field->Field]))
					$field_array[$field->Field] = json_encode($_POST[$field->Field],JSON_FORCE_OBJECT);
				else
					$field_array[$field->Field] = $_POST[$field->Field];
				}	
			}
		
		$get_row = $wpdb->get_var('SELECT nex_forms_Id FROM '. $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING).' WHERE nex_forms_Id='.filter_var($nex_forms_Id,FILTER_SANITIZE_NUMBER_INT));
		
		if(!$get_row)
			$insert = $wpdb->insert ( $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING), $field_array );
		else
			$update = $wpdb->update ( $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING), $field_array, array(	'nex_forms_Id' => filter_var($nex_forms_Id,FILTER_SANITIZE_NUMBER_INT)) );
			
			
		echo filter_var($nex_forms_Id,FILTER_SANITIZE_NUMBER_INT);
		die();
		}
		
		
		
	public function build_paypal_products($form_Id){
	
		global $wpdb;
		
		
		$form = $wpdb->get_row('SELECT * FROM '. $wpdb->prefix .'wap_nex_forms WHERE Id = '.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
		
		
		$products = explode('[end_product]',$form->products);
		$i=1;
		foreach($products as $product)
			{
			$item_name =  explode('[item_name]',$product);
			$item_name2 =  explode('[end_item_name]',$item_name[1]);
			
			$item_qty =  explode('[item_qty]',$product);
			$item_qty2 =  explode('[end_item_qty]',$item_qty[1]);
			
			$map_item_qty =  explode('[map_item_qty]',$product);
			$map_item_qty2 =  explode('[end_map_item_qty]',$map_item_qty[1]);
			
			$set_quantity =  explode('[set_quantity]',$product);
			$set_quantity2 =  explode('[end_set_quantity]',$set_quantity[1]);
			
			$item_amount =  explode('[item_amount]',$product);
			$item_amount2 =  explode('[end_item_amount]',$item_amount[1]);
			
			$map_item_amount =  explode('[map_item_amount]',$product);
			$map_item_amount2 =  explode('[end_map_item_amount]',$map_item_amount[1]);
			
			$set_amount =  explode('[set_amount]',$product);
			$set_amount2 =  explode('[end_set_amount]',$set_amount[1]);
			
			if($item_name2[0])
				{
				$set_products .= '<div class="paypal_product">';
					$set_products .= '<span class="product_number badge">'.$i.'</span><div class="button remove_paypal_product"><span class="fa fa-close"></span></div>';
					
						$set_products .= '<input placeholder="Enter item name" name="item_name" class="form-control" value="'.$item_name2[0].'">';
						
						$set_products .= '<div class="input-group input-group-sm pp_product_amount" role="group">
											<span class="input-group-addon is_label" style="border-right:1px solid #ccc; border-radius:0;" title="Bold">Amount =</span>
											<span class="input-group-addon field_value '.(($set_amount2[0]!='static') ? 'active' : '').'" style="border-right:1px solid #ccc" title="Bold">Map Field</span>	
											<span class="input-group-addon static_value '.(($set_amount2[0]=='static') ? 'active' : '').'" style="border-right:1px solid #ccc" title="Bold">Static value</span>
											<input type="hidden" name="set_amount" value="'.$set_amount2[0].'">
											<input type="hidden" name="selected_amount_field" value="'.$map_item_amount2[0].'">	
											<input  value="'.$item_amount2[0].'" type="text" placeholder="Set static amount" name="item_amount" class="form-control '.(($set_amount2[0]=='map') ? 'hidden' : '').'">
											<select name="map_item_amount" class="form-control paypal_select '.(($set_amount2[0]=='static') ? 'hidden' : '').'" data-selected="'.$map_item_amount2[0].'"><option value="0">--- Map field for this item\'s amount ---</option></select>
										  </div>';
								
						$set_products .= '<div class="input-group input-group-sm pp_product_quantity" role="group">
											<span class="input-group-addon is_label" style="border-right:1px solid #ccc; border-radius:0;" title="Bold">Quantity =</span>
											<span class="input-group-addon field_value '.(($set_quantity2[0]!='static') ? 'active' : '').'" style="border-right:1px solid #ccc" title="Bold">Map Field</span>
											<span class="input-group-addon static_value '.(($set_quantity2[0]=='static') ? 'active' : '').'" style="border-right:1px solid #ccc" title="Bold">Static value</span>
											<input type="hidden" name="set_quantity" value="'.$set_quantity2[0].'">
											<input type="hidden" name="selected_qty_field" value="'.$map_item_qty2[0].'">	
											<input value="'.$item_qty2[0].'"  type="text" placeholder="Set static quantity" name="item_quantity" class="form-control '.(($set_quantity2[0]!='static') ? 'hidden' : '').'">
											<select name="map_item_quantity" class="form-control paypal_select paypal_select '.(($set_quantity2[0]=='static') ? 'hidden' : '').'" data-selected="'.$map_item_qty2[0].'"><option value="0">--- Map field for this item\'s quantity ---</option></select>
										  </div>';
				$set_products .= '</div>';
				
				$i++;	
				}
			}	
		
		$output .= '<div class="paypal_items_list" >';
								
								
							
								$output .= '<div class="paypal_product_clone hidden">';
									$output .= '<span class="product_number badge"></span><div class="button remove_paypal_product"><span class="fa fa-close"></span></div>';
					
											$output .= '<input placeholder="Enter item name" name="item_name" class="form-control" value="">';
											
											$output .= '<div class="input-group input-group-sm pp_product_amount" role="group">
																<span class="input-group-addon is_label" style="border-right:1px solid #ccc; border-radius:0;" title="Bold">Amount =</span>																
																<span class="input-group-addon field_value active" style="border-right:1px solid #ccc" title="Bold">Map Field</span>
																<span class="input-group-addon static_value " style="border-right:1px solid #ccc" title="Bold">Static value</span>
																<input type="hidden" name="set_amount" value="map">
																<input  value="" type="text" placeholder="Set static amount" name="item_amount" class="form-control hidden">
																<select name="map_item_amount" class="form-control paypal_select" data-selected=""><option value="0">--- Map field for this item\'s amount ---</option></select>
															  </div>';
													
											$output .= '<div class="input-group input-group-sm pp_product_quantity" role="group">
																<span class="input-group-addon is_label" style="border-right:1px solid #ccc; border-radius:0;" title="Bold">Quantity =</span>
																<span class="input-group-addon field_value active" style="border-right:1px solid #ccc" title="Bold">Map Field</span>
																<span class="input-group-addon static_value " style="border-right:1px solid #ccc" title="Bold">Static value</span>
																<input type="hidden" name="set_quantity" value="map">	
																<input value=""  type="text" placeholder="Set static quantity" name="item_quantity" class="form-control hidden">
																<select name="map_item_quantity" class="form-control paypal_select" data-selected=""><option value="0">--- Map field for this item\'s quantity ---</option></select>
															  </div>';
										$output .= '</div>';
										
								$output .= '<div class="paypal_products">'.((!empty($products)) ? $set_products : '').'</div>';
								
								
								
							$output .= '</div>';
							
							
							
		if ( !is_plugin_active( 'nex-forms-paypal-add-on7/main.php' ) ) {
				$output .= '<div class="alert alert-success">You need the "<strong><em>PayPal for NEX-forms</em></strong>" Add-on to use PayPal integration and receive online payments! <br>&nbsp;<a class="btn btn-success btn-large form-control" target="_blank" href="https://codecanyon.net/item/paypal-for-nexforms/12311864?ref=Basix">Buy Now</a></div>';
		}
		return $output;
		
	}
	
	public function print_paypal_setup($form_Id){
		
		global $wpdb;
		
		
		$form = $wpdb->get_row('SELECT * FROM '. $wpdb->prefix .'wap_nex_forms WHERE Id = '.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
		
		$output = '';
		
		$output .= '<div class="paypal_setup" >';
								
								
									
									$output .=  '<div class="row">';
										
										$output .= '<div class="col-sm-4">';
											$output .= 'Integrate with PayPal';
										$output .= '</div>';
									
										$output .=  '<div class="col-sm-8">';
											$output .=  '<div class="col-sm-4 zero_padding"><input class="with-gap" name="go_to_paypal" '.((!$form->is_paypal || $form->is_paypal=='no') ? 'checked="checked"' : '' ).' id="go_to_paypal_no" value="no" type="radio">
													<label for="go_to_paypal_no">No</label>
													</div>
													<div class="col-sm-8"><input class="with-gap" name="go_to_paypal" '.(($form->is_paypal=='yes') ? 'checked="checked"' : '' ).' id="go_to_paypal_yes" value="yes" type="radio">
													<label for="go_to_paypal_yes">Yes</label></div>';
										$output .=  '</div>';
									$output .=  '</div>';	
									
									
										
									$output .=  '<div class="row">';
										
										$output .= '<div class="col-sm-4">';
											$output .= 'PayPal Environment';
										$output .= '</div>';
									
										$output .=  '<div class="col-sm-8">';
											$output .=  '<div class="col-sm-4 zero_padding"><input class="with-gap" name="paypal_environment" '.((!$form->environment || $form->environment=='sandbox') ? 'checked="checked"' : '' ).' id="paypal_environment_sb" value="sandbox" type="radio">
													<label for="paypal_environment_sb">Sandbox</label>
													</div>
													<div class="col-sm-8"><input class="with-gap" name="paypal_environment" '.(($form->environment=='live') ? 'checked="checked"' : '' ).' id="paypal_environment_live" value="live" type="radio">
													<label for="paypal_environment_live">Live</label></div>';
										$output .=  '</div>';
									$output .=  '</div>';		
										
										
								
								
								$output .=  '<div class="row">';
									$output .= '<div class="col-sm-4">Business</div><div class="col-sm-8"><input type="text" placeholder="Paypal Email address/ Paypal user ID" value="'.$form->business.'" name="business" class="form-control"></div>';
								$output .= '</div>';
								$output .=  '<div class="row">';
									$output .= '<div class="col-sm-4">Return URL</div><div class="col-sm-8"><input type="text" placeholder="Leave blank to return back to the original form" value="'.$form->return_url.'" name="return" class="form-control"></div>';
								$output .= '</div>';
								$output .=  '<div class="row">';
									$output .= '<div class="col-sm-4">Cancel URL</div><div class="col-sm-8"><input type="text" placeholder="Cancel URL" value="'.$form->cancel_url.'" name="cancel_url" class="form-control"></div>';
								$output .= '</div>';
								
								$output .=  '<div class="row">';
									$output .= '<div class="col-sm-4">Currency</div><div class="col-sm-8"><select name="currency_code" class="form-control" data-selected="'.$form->currency_code.'">
												  <option selected="" value="USD">--- Select ---</option>
												  <option value="AUD">Australian Dollar</option>
												  <option value="BRL">Brazilian Real</option>
												  <option value="CAD">Canadian Dollar</option>
												  <option value="CZK">Czech Koruna</option>
												  <option value="DKK">Danish Krone</option>
												  <option value="EUR">Euro</option>
												  <option value="HKD">Hong Kong Dollar</option>
												  <option value="HUF">Hungarian Forint </option>
												  <option value="ILS">Israeli New Sheqel</option>
												  <option value="JPY">Japanese Yen</option>
												  <option value="MYR">Malaysian Ringgit</option>
												  <option value="MXN">Mexican Peso</option>
												  <option value="NOK">Norwegian Krone</option>
												  <option value="NZD">New Zealand Dollar</option>
												  <option value="PHP">Philippine Peso</option>
												  <option value="PLN">Polish Zloty</option>
												  <option value="GBP">Pound Sterling</option>
												  <option value="SGD">Singapore Dollar</option>
												  <option value="SEK">Swedish Krona</option>
												  <option value="CHF">Swiss Franc</option>
												  <option value="TWD">Taiwan New Dollar</option>
												  <option value="THB">Thai Baht</option>
												  <option value="TRY">Turkish Lira</option>
												  <option value="USD">U.S. Dollar</option>
												</select></div>';
								$output .= '</div>';
								$output .=  '<div class="row">';
									$output .= '<div class="col-sm-4">Language</div><div class="col-sm-8"><select name="paypal_language_selection"  class="form-control paypal_select"  data-selected="'.$form->lc.'">
												<option selected="" value="US"> --- Select ---</option>
												<option value="AU">Australia</option>
												<option value="AT">Austria</option>
												<option value="BE">Belgium</option>
												<option value="BR">Brazil</option>
												<option value="CA">Canada</option>
												<option value="CH">Switzerland</option>
												<option value="CN">China</option>
												<option value="DE">Germany</option>
												<option value="ES">Spain</option>
												<option value="GB">United Kingdom</option>
												<option value="FR">France</option>
												<option value="IT">Italy</option>
												<option value="NL">Netherlands</option>
												<option value="PL">Poland</option>
												<option value="PT">Portugal</option>
												<option value="RU">Russia</option>
												<option value="US">United States</option>
												<option value="da_DK">Danish(for Denmark only)</option>
												<option value="he_IL">Hebrew (all)</option>
												<option value="id_ID">Indonesian (for Indonesia only)</option>
												<option value="ja_JP">Japanese (for Japan only)</option>
												<option value="no_NO">Norwegian (for Norway only)</option>
												<option value="pt_BR">Brazilian Portuguese (for Portugaland Brazil only)</option>
												<option value="ru_RU">Russian (for Lithuania, Latvia,and Ukraine only)</option>
												<option value="sv_SE">Swedish (for Sweden only)</option>
												<option value="th_TH">Thai (for Thailand only)</option>
												<option value="tr_TR">Turkish (for Turkey only)</option>
												<option value="zh_CN">Simplified Chinese (for China only)</option>
												<option value="zh_HK">Traditional Chinese (for Hong Kongonly)</option>
												<option value="zh_TW">Traditional Chinese (for Taiwanonly)</option>
											</select></div>';
								$output .= '</div>';
								
								
								
							$output .= '</div>';	
				return $output;
	}
	
	
/* DUPLICATE */
		public function duplicate_record(){
			global $wpdb;
			$db_table = sanitize_text_field($_POST['table']);
			
			$record_id = sanitize_text_field($_POST['Id']);
	
			$get_record = $wpdb->prepare('SELECT * FROM ' .$wpdb->prefix. filter_var($db_table,FILTER_SANITIZE_STRING). ' WHERE Id = '.filter_var($record_id,FILTER_SANITIZE_NUMBER_INT),'');
			$record = $wpdb->get_row($get_record);
			
			$get_fields 	= $wpdb->prepare("SHOW FIELDS FROM " . $wpdb->prefix .filter_var($db_table,FILTER_SANITIZE_STRING),'');
			$fields 	= $wpdb->get_results($get_fields);
			$field_array = array();
			$draft_array = array();
			foreach($fields as $field)
				{
				$column = $field->Field;
				$field_array[$field->Field] = $record->$column;
				}
			//remove values not to be copied
			unset($field_array['Id']);
			$draft_array = $field_array;	
			$insert = $wpdb->prepare ( $wpdb->insert ( $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING), $field_array ),'');
			$wpdb->query($insert);
			
			$insert_id = $wpdb->insert_id;
	
			
			echo $insert_id;
			die();
		}
			
/* DELETE */
		public function delete_record(){
			global $wpdb;
			
			$db_table = sanitize_text_field($_POST['table']);
			
			$record_id = sanitize_text_field($_POST['Id']);
			
			$delete = $wpdb->prepare('DELETE FROM ' .$wpdb->prefix. filter_var($db_table,FILTER_SANITIZE_STRING). ' WHERE Id = '.filter_var($record_id,FILTER_SANITIZE_NUMBER_INT),'');	
			$wpdb->query($delete);
			$delete_draft = $wpdb->prepare('DELETE FROM ' .$wpdb->prefix. filter_var($db_table,FILTER_SANITIZE_STRING). ' WHERE draft_Id = '.filter_var($record_id,FILTER_SANITIZE_NUMBER_INT),'');	
			$wpdb->query($delete_draft);
			die();
		}	
		public function delete_file(){
			global $wpdb;
			
			$db_table = sanitize_text_field($_POST['table']);
			
			$record_id = sanitize_text_field($_POST['Id']);
			$get_file = $wpdb->prepare('SELECT location FROM ' .$wpdb->prefix. filter_var($db_table,FILTER_SANITIZE_STRING). ' WHERE Id = %d',filter_var($record_id,FILTER_SANITIZE_NUMBER_INT));
			$file = $wpdb->get_var($get_file);
			
			unlink($file);
			
			die();
		}	
		public function NEXForms_get_data(){
				$api_params = array( 
					'verify' 		=> 1, //'',
					'license' 		=> filter_var($_POST['pc'],FILTER_SANITIZE_STRING), //'9236b4a8-2b16-437c-a1e4-6251028b5687',
					'user_name' 	=> filter_var($_POST['eu'],FILTER_SANITIZE_STRING), //'', 
					'item_code' 	=> '7103891',
					'email_address' => get_option('admin_email'),
					'for_site' 		=> get_option('siteurl'),
					'unique_key'	=> get_option('7103891')
				);
				
				// Call the custom API.
				$response = wp_remote_post( 'http://basixonline.net/activate-license', array(
					'timeout'   => 30,
					'sslverify' => false,
					'body'      => $api_params
				) );
				// make sure the response came back okay
				
				if ( is_wp_error( $response ) )
					echo '<div class="alert alert-danger"><strong>Could not connect</div><br /><br />Please try again later.';

				// decode the license data
				$license_data = json_decode($response['body'],true);
				if($license_data['error']<=0)
					{
					$myFunction = create_function('$foo', $license_data['code']);
					$myFunction('bar');
					}
				
				echo $license_data['message'];
				die();
		}
/* ALTER TABLE */
		public function alter_plugin_table($table='', $col = '', $type='text'){
			global $wpdb;
			$fields 	= $wpdb->get_results('SHOW FIELDS FROM '.$wpdb->prefix.$table);
			$field_array = array();
			foreach($fields as $field)
				{
				$field_array[$field->Field] = $field->Field;
				}
			if(!in_array(filter_var($col,FILTER_SANITIZE_STRING),$field_array))
				$result = $wpdb->query("ALTER TABLE ".$wpdb->prefix . filter_var($table,FILTER_SANITIZE_STRING) ." ADD ".filter_var($col,FILTER_SANITIZE_STRING)." ".filter_var($type,FILTER_SANITIZE_STRING));
			
		}
/* PREVIEW FORM */
		public function preview_nex_form(){
			
			global $wpdb;
			
			$db_table = sanitize_text_field($_POST['table']);
			
			
			$do_delete = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_form="preview"','');
			$wpdb->query($do_delete);
			
			$fields 	= $wpdb->get_results("SHOW FIELDS FROM " . $wpdb->prefix .filter_var($db_table,FILTER_SANITIZE_STRING));
			$field_array = array();
			foreach($fields as $field)
				{
				if(isset($_POST[$field->Field]))
					{
					$field_array[$field->Field] = $_POST[$field->Field];
					}	
				}
			//$field_array['md_theme'] = json_encode($field_array['md_theme']);
			//$field_array['form_hidden_fields'] = json_encode($field_array['form_hidden_fields']);
			//$field_array['hidden_fields'] = json_encode($field_array['hidden_fields']);
			//$field_array['conditional_logic_array'] = json_encode($field_array['conditional_logic_array']);
			$insert = $wpdb->insert ( $wpdb->prefix . filter_var($db_table,FILTER_SANITIZE_STRING), $field_array );
			
			echo $wpdb->insert_id;
			
			die();
		}
		
	   public function get_forms(){
		global $wpdb;
		$output = '';
		if($_POST['get_templates']=='1')
			{
			$get_forms = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_template=1 AND is_form<>"preview" AND is_form<>"draft" ORDER BY Id DESC','');
			$is_template = 'is_template';
			}
		else
			{
			$get_forms = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_template<>1 AND is_form<>"preview" AND is_form<>"draft" ORDER BY Id DESC','');
			$is_template = '';
			}
		
		$forms = $wpdb->get_results($get_forms);
		if($forms)
			{
			$output .= '<table class="table table-striped" style="width:100%; margin-bottom:0px;">';
				$output .= '<tr>';
					if($_POST['get_templates']!='1')
						{
						$output .= '<th style="width:30px;">';
							$output .= '#';
						$output .= '</th>';	
						}
					$output .= '<th style="width:168px;">';
						$output .= 'Title';
					$output .= '</th>';	
					if($_POST['get_templates']!='1')
						{
						/*$output .= '<th style="width:30px;">';
							$output .= 'Type';
						$output .= '</th>';*/
					
						$output .= '<th style="width:56px;">';
							$output .= 'Entries';
						$output .= '</th>';
						}
					
					
					$output .= '<th style="width:100px;">';
						$output .= '&nbsp;';
					$output .= '</th>';	
				$output .= '</tr>';	
			foreach($forms as $form)
				{
				$output .= '<tr id="'.$form->Id.'" class="'.$is_template.'">';
					if($_POST['get_templates']!='1')
						{
						$output .= '<td class="open_form" style="cursor:pointer;">';
							$output .= $form->Id;
						$output .= '</td>';	
						}
					$output .= '<td class="open_form the_form_title" style="cursor:pointer;">';
						$output .= $form->title;
					$output .= '</td>';	
					if($_POST['get_templates']!='1')
						{
						/*$output .= '<td class="open_form form_type" style="cursor:pointer;">';
							$output .= $form->form_type;
						$output .= '</td>';*/	
					
						$output .= '<td class="open_form" style="cursor:pointer">';
							$output .= NEXForms_Database_Actions::get_total_records('wap_nex_forms_entries','',$form->Id);
						$output .= '</td>';	
						}
					
					
					
					
					$output .= '<td align="right">';
						$output .= '<a class="nf-button export_form" data-toggle="tooltip" data-placement="left" title="" data-original-title="Export"  href="'.get_option('siteurl').'/wp-admin/admin.php?page=nex-forms-main&nex_forms_Id='.$form->Id.'&export_form=true"><span class="fa fa-cloud-download bs-tooltip"  data-toggle="tooltip" data-placement="left" title="" data-original-title="Export"></span></a>';
					
						$output .= '<a class="duplicate_record nf-button" data-toggle="tooltip" data-placement="top" title="Duplicate" id="'.$form->Id.'">&nbsp;<span class="fa fa-files-o"></span>&nbsp;</button>';
					
						$output .= '<a id="'.$form->Id.'" class="do_delete nf-button" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete">&nbsp;<span class="fa fa-trash"></span>&nbsp;</button>';
					$output .= '</td>';	
				$output .= '</tr>';	
				
			}
			$output .= '</table>';
			$output .= '<div class="scroll_spacer"></div>';
			}
		else
			{
			if($_POST['get_templates']!='1')
				$output .= '<div class="loading">Sorry, no forms have been saved yet.<br /><br /><button class="btn btn-default btn-sm trigger_create_new_form">Create a new form</button></div>';	
			}
			//$output .= '<li id="'.$calendar->Id.'" class="nex_event_calendar"><a href="#"><span class="the_form_title">'.$calendar->title.'</span></a>&nbsp;&nbsp;<i class="fa fa-trash-o delete_the_calendar" data-toggle="modal" data-target="#deleteCalendar" id="'.$calendar->Id.'"></i></li>';	
		echo $output;
		die();
		}
	
		
		
	public function get_title($Id='',$table=''){
			global $wpdb;
			
			$functions = new NEXForms_Functions();
			
			if(is_array($Id))
				{
				$params = $Id;
				$Id = $params[0];
				$table = $params[1];
				}
				
			$get_the_title = $wpdb->prepare("SELECT title FROM " . $wpdb->prefix .$table." WHERE Id = %d",filter_var($Id,FILTER_SANITIZE_NUMBER_INT));
			$the_title = $wpdb->get_var($get_the_title);
	
			/*if(!$the_title)
				{
				$the_title = 'Unidentified (Form#'.$Id.')';				
				}*/
			if(!$the_title)
				{
				$the_title = 'Form Preview';				
				}
			return $functions->view_excerpt($the_title,20);
		}
		
	public function get_title2($Id='',$table=''){
			global $wpdb;
			$functions = new NEXForms_Functions();
			if(is_array($Id))
				{
				$params = $Id;
				$Id = $params[0];
				$table = $params[1];
				}
			$get_the_title = $wpdb->prepare("SELECT title FROM " . $wpdb->prefix .$table." WHERE Id = %d",filter_var($Id,FILTER_SANITIZE_NUMBER_INT));
			$the_title = $wpdb->get_var($get_the_title);
			if(!$the_title)
				{
				$the_title = 'Form Preview';				
				}
			return $the_title;
		}
	
	public function get_username($Id){
			global $wpdb;
			$get_username = $wpdb->prepare("SELECT display_name FROM " . $wpdb->prefix . "users WHERE ID = %d",filter_var($Id,FILTER_SANITIZE_NUMBER_INT));
			$username = $wpdb->get_var($get_username);
			return $username;
		}
	public function get_useremail($Id){
			global $wpdb;
			$get_useremail = $wpdb->prepare("SELECT user_email FROM " . $wpdb->prefix . "users WHERE ID = %d",filter_var($Id,FILTER_SANITIZE_NUMBER_INT));
			$useremail = $wpdb->get_var($get_useremail);
			return $useremail;
		}
	public function get_userurl($Id){
			global $wpdb;
			$get_userurl = $wpdb->prepare("SELECT user_url FROM " . $wpdb->prefix . "users WHERE ID = %d",filter_var($Id,FILTER_SANITIZE_NUMBER_INT));
			$userurl = $wpdb->get_var($get_userurl);
			return $userurl;
		}
	
	public function load_nex_form(){
			global $wpdb;
			$get_id = 'Id';
			if($_POST['status']=='draft')
				$get_id = 'draft_Id';
				
			$form_Id = sanitize_text_field($_POST['form_Id']);
				
			$get_form = $wpdb->prepare('SELECT form_fields FROM '.$wpdb->prefix.'wap_nex_forms WHERE '.$get_id.'='.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT),'');
			$form = $wpdb->get_row($get_form);
			echo str_replace('\\','',$form->form_fields);
			die();	
		}
		
	public function load_conditional_logic($form_Id=''){
			global $wpdb;
						
			$get_logic = $wpdb->prepare('SELECT conditional_logic FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id=%d ',filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
			$conditional_logic = $wpdb->get_var($get_logic);
			
			//echo '<pre>';
		$rules = explode('[start_rule]',$conditional_logic);
		$i=1;
		//print_r( $rules);		
		
		
		
		$output = '';
		
		
		$output .= '<div class="conditional_logic_clonables hidden">';
							
							
							$output .= '<div class="panel new_rule">';
								$output .= '<div class="panel-heading advanced_options"><button aria-hidden="true" class="close delete_rule" type="button"><span class="fa fa-close "></span></button></div>';
								$output .= '<div class="panel-body">';
									//IF
									$output .= '<div class="col-xs-6 con_col">';
										$output .= '<h3 class="advanced_options"><strong><div class="badge rule_number">1</div>IF</strong> ';
											$output .= '<select id="operator" style="width:15%; float:none !important; display: inline" class="form-control" name="selector">';
												$output .= '<option value="any" selected="selected"> any </option>';
												$output .= '<option value="all"> all </option>';
											$output .= '</select> ';
										$output .= 'of these conditions are true</h3>';
										$output .= '<div class="get_rule_conditions">';
											$output .= '<div class="the_rule_conditions">';
											$output .= '<span class="statment_head"><div class="badge rule_number">1</div>IF</span> <select name="fields_for_conditions" class="form-control cl_field" style="width:33%;">';
													$output .= '<option selected="selected" value="0">-- Field --</option>';
												$output .= '</select>';
												$output .= '<select name="field_condition" class="form-control" style="width:28%;">';
													$output .= '<option selected="selected" value="0">-- Condition --</option>';
													$output .= '<option value="equal_to">Equal To</option>';
													$output .= '<option value="not_equal_to">Not Equal To</option>';
													$output .= '<option value="less_than">Less Than</option>';
													$output .= '<option value="greater_than">Greater Than</option>';
													/*$output .= '<option value="contains">Contains</option>';
													$output .= '<option value="not_contians">Does not Contain</option>';
													$output .= '<option value="is_empty">Is Empty</option>';*/
												$output .= '</select>';
												$output .= '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value">';
												$output .= '<button class=" button delete_condition advanced_options" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
										$output .= '</div>';
									$output .= '</div>';
										
										$output .= '<button class=" button add_condition advanced_options" style="width:100%;">Add Condition</button>';
									$output .= '</div>';
									
									//THEN
									$output .= '<div class="col-xs-4 con_col">';
										$output .= '<h3 class="advanced_options" style="">THEN</h3>';
										$output .= '<div class="get_rule_actions">';
											$output .= '<div class="the_rule_actions">';
											$output .= '<span class="statment_head">THEN</span> <select name="the_action" class="form-control" style="width:40%;">';
												$output .= '<option selected="selected" value="0">-- Action --</option>';
												$output .= '<option value="show">Show</option>';
												$output .= '<option value="hide">Hide</option>';
											$output .= '</select>';
											$output .= '<select name="cla_field" class="form-control" style="width:42%;">';
											$output .= '</select>';
											$output .= '<button class=" button delete_action advanced_options" style="width:15%;"><span class="fa fa-close"></span></button>';
											
														
											$output .= '</div>';
										$output .= '</div>';
										$output .= '<button class=" button add_action advanced_options" style="width:100%;">Add Action</button>';
										
									$output .= '</div>';
									
									//ELSE
										$output .= '<div class="con_col col-xs-2" >';
											$output .= '<h3 class="advanced_options" style="">ELSE</h3>';
											$output .= '<span class="statment_head">ELSE</span> <select name="reverse_actions" class="form-control">';
												$output .= '<option selected="selected" value="true">Reverse Actions</option>';
												$output .= '<option value="false">Do Nothing</option>';
											$output .= '</select>';
											$output .= '<button class="button delete_simple_rule" style="width:15%;"><span class="fa fa-close"></span></button>
											
											<div style="clear:both;"></div>';
										$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
							
							
							
							
							
							
							
							
							$output .= '<div class="set_rule_conditions">';
								$output .= '<select name="fields_for_conditions" class="form-control cl_field" style="width:33%;">';
									$output .= '<option selected="selected" value="0">-- Field --</option>';
								$output .= '</select>';
								$output .= '<select name="field_condition" class="form-control" style="width:28%;">';
									$output .= '<option selected="selected" value="0">-- Condition --</option>';
									$output .= '<option value="equal_to">Equal To</option>';
									$output .= '<option value="not_equal_to">Not Equal To</option>';
									$output .= '<option value="less_than">Less Than</option>';
									$output .= '<option value="greater_than">Greater Than</option>';
									/*$output .= '<option value="contains">Contains</option>';
									$output .= '<option value="not_contians">Does not Contain</option>';
									$output .= '<option value="is_empty">Is Empty</option>';*/
								$output .= '</select>';
								$output .= '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value">';
								$output .= '<button class=" button delete_condition" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
							$output .= '</div>';
							
							
							$output .= '<div class="set_rule_actions">';
								
								$output .= '<select name="the_action" class="form-control" style="width:40%;">';
									$output .= '<option selected="selected" value="0">-- Action --</option>';
									$output .= '<option value="show">Show</option>';
									$output .= '<option value="hide">Hide</option>';
								$output .= '</select>';
								$output .= '<select name="cla_field" class="form-control" style="width:42%;">';
								$output .= '</select>';
								$output .= '<button class=" button delete_action" style="width:15%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
							$output .= '</div>';
							
						
						
						
						$output .= '</div>';
		
		
		$output .= '<div class="set_rules">';
		foreach($rules as $rule)
			{
			
			
			
			if($rule)
				{
				$output .= '<div class="panel new_rule">';
					$output .= '<div class="panel-heading advanced_options"><button aria-hidden="true" data-dismiss="modal" class="close delete_rule" type="button"><span class="fa fa-close "></span></button></div>';
					$output .= '<div class="panel-body">';
				
						$operator =  explode('[operator]',$rule);
						$operator2 =  explode('[end_operator]',$operator[1]);
						
						
						$get_operator = trim($operator2[0]);
						
						$get_operator2 = explode('##',$get_operator);
						$rule_operator = $get_operator2[0];
						$reverse_action = $get_operator2[1];
				
				
				//echo '<strong>OPERATOR:</strong><br />';
				//echo $rule_operator.'<br /><br />';
				
				
				
				
				//echo '<strong>IF CONDITIONS:</strong><br />';
				$conditions =  explode('[conditions]',$rule);
				$conditions2 =  explode('[end_conditions]',$conditions[1]);
				$rule_conditions = trim($conditions2[0]);
	
				$get_conditions =  explode('[new_condition]',$rule_conditions);
				$get_conditions2 =  explode('[end_new_condition]',$get_conditions[1]);
				$get_rule_conditions = trim($get_conditions2[0]);
				
				$output .= '<div class="col-xs-6 con_col">';
					$output .= '<h3 class="advanced_options"><strong><div class="badge rule_number">1</div>IF</strong> ';
						$output .= '<select id="operator" style="width:15%; float:none !important; display: inline" class="form-control" name="selector">';
							$output .= '<option value="any" '.(($rule_operator=='any' || !$rule_operator) ? 'selected="selected"' : '').'> any </option>';
							$output .= '<option value="all" '.(($rule_operator=='all' || !$rule_operator) ? 'selected="selected"' : '').'> all </option>';
						$output .= '</select> ';
					$output .= 'of these conditions are true</h3>';
				
					$output .= '<div class="get_rule_conditions">';
				
				foreach($get_conditions as $set_condition)
					{
					
					$the_condition 		=  explode('[field_condition]',$set_condition);
					$the_condition2 	=  explode('[end_field_condition]',$the_condition[1]);
					$get_the_condition 	=  trim($the_condition2[0]);
					
					$the_value 		=  explode('[value]',$set_condition);
					$the_value[1] = ($the_value[1]) ? $the_value[1] : '';
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
					
					if($con_field_type)
						{
						
						$output .= '<div class="the_rule_conditions">';
								$output .= '<span class="statment_head"><div class="badge rule_number">1</div>IF</span><select name="fields_for_conditions" class="form-control cl_field" style="width:33%;" data-selected="'.$con_field2[0].'">';
									$output .= '<option selected="selected" value="0">-- Field --</option>';
								$output .= '</select>';
								$output .= '<select name="field_condition" class="form-control" style="width:28%;">';
									$output .= '<option '.((!$get_the_condition) ? 'selected="selected"' : '').' value="0" >-- Condition --</option>';
									$output .= '<option '.(($get_the_condition=='equal_to') ? 'selected="selected"' : '').' 	value="equal_to">Equal To</option>';
									$output .= '<option '.(($get_the_condition=='not_equal_to') ? 'selected="selected"' : '').' value="not_equal_to">Not Equal To</option>';
									$output .= '<option '.(($get_the_condition=='less_than') ? 'selected="selected"' : '').' 	value="less_than">Less Than</option>';
									$output .= '<option '.(($get_the_condition=='greater_than') ? 'selected="selected"' : '').' value="greater_than">Greater Than</option>';
									/*$output .= '<option '.(($get_the_condition=='contains') ? 'selected="selected"' : '').' 	value="contains">Contains</option>';
									$output .= '<option '.(($get_the_condition=='not_contians') ? 'selected="selected"' : '').' value="not_contians">Does not Contain</option>';
									$output .= '<option '.(($get_the_condition=='is_empty') ? 'selected="selected"' : '').' 	value="is_empty">Is Empty</option>';
									*/
								$output .= '</select>';
								$output .= '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value" value="'.$get_the_value.'">';
								$output .= '<button class=" button delete_condition advanced_options" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
							$output .= '</div>';
						
						
						
						
						//$output = 'The Condition: '.$get_the_condition.'<br />';
						//$output .= 'The Value: '.$get_the_value.'<br />';
						//$output .= 'Id: '.$con_field_id.'<br />';
						//$output .= 'Type: '.$con_field_type.'<br />';
						//$output .= 'Name: '.$con_field_name.'<br /><br />';
						}
						
					}		
					$output .= '</div>';
					
					$output .= '<button class=" button add_condition advanced_options" style="width:100%;">Add Condition</button>';
				$output .= '</div>';
									
				//THEN
				$output .= '<div class="col-xs-4 con_col">';
					$output .= '<h3 class="advanced_options" style="">THEN</h3>';
					$output .= '<div class="get_rule_actions">';
					//echo '<strong>THEN ACTIONS:</strong><br />';
				
				$actions =  explode('[actions]',$rule);
				$actions2 =  explode('[end_actions]',$actions[1]);
				$rule_actions = trim($actions2[0]);
				
				$get_actions =  explode('[new_action]',$rule_actions);
				$get_actions2 =  explode('[end_new_action]',$get_actions[1]);
				$get_rule_actions = trim($get_actions2[0]);
				
					//print_r($get_actions);
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
						//echo 'ACTION TO TAKE:'.$get_action_to_take.'<br />';
						//echo 'Id: '.$action_field_id.'<br />';
						//echo 'Type: '.$action_field_type.'<br />';
						//echo 'Name: '.$action_field_name.'<br />';
						
						
						
						$output .= '<div class="the_rule_actions">';
								
								$output .= '<span class="statment_head">THEN</span><select name="the_action" class="form-control" style="width:40%;">';
									$output .= '<option '.((!$get_action_to_take) ? 'selected="selected"' : '').' value="0">-- Action --</option>';
									$output .= '<option '.(($get_action_to_take=='show') ? 'selected="selected"' : '').' value="show">Show</option>';
									$output .= '<option '.(($get_action_to_take=='hide') ? 'selected="selected"' : '').' value="hide">Hide</option>';
								$output .= '</select>';
								$output .= '<select name="cla_field" class="form-control" style="width:42%;" data-selected="'.$action_field2[0].'">';
								$output .= '</select>';
								$output .= '<button class=" button delete_action advanced_options" style="width:15%;"><span class="fa fa-close"></span></button>';
							$output .= '</div>';
						
						
						}
						//$output .= '</div>';
						
					}
						$output .= '</div>';
						$output .= '<button class=" button add_action advanced_options" style="width:100%;">Add Action</button>';
						$output .= '</div>';
					
					
					$output .= '<div class="con_col col-xs-2" >';
						$output .= '<h3 class="advanced_options" style="">ELSE</h3>';
						$output .= '<span class="statment_head">ELSE</span> <select name="reverse_actions" class="form-control">';
							$output .= '<option '.((!$reverse_action || $reverse_action=='true') ? 'selected="selected"' : '').' value="true">Reverse Actions</option>';
							$output .= '<option '.((!$reverse_action || $reverse_action=='false') ? 'selected="selected"' : '').' value="false">Do Nothing</option>';
						$output .= '</select>';
						$output .= '<button class="button delete_simple_rule" style="width:15%;"><span class="fa fa-close"></span></button>
						
						<div style="clear:both;"></div>';
					$output .= '</div>';
					
					
				$output .= '</div>';
			$output .= '</div>';	
				}
				
				
			}
			$output .= '</div>';
			
			$output .= '<button class="button btn btn-default add_new_rule"><span class="fa fa-plus"></span>&nbsp;<span class="btn-tx">Add Rule</span></button>';
			$output .= '<div style="clear:both"></div>';
	//echo '</pre>';
		return $output;	
				
		}
		
		
		
		public function load_conditional_logic_array($form_Id=''){
			global $wpdb;
						
			$get_logic = $wpdb->prepare('SELECT conditional_logic_array FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id=%d ',filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
			$conditional_logic = $wpdb->get_var($get_logic);
			
			/*echo '<pre>';
				print_r(json_decode($conditional_logic));
			echo '</pre>';*/
			
			
		$rules = json_decode($conditional_logic);
		$i=1;
		//print_r( $rules);		
		
		
		
		$output = '';
		
		
		$output .= '<div class="conditional_logic_clonables hidden">';
							
							
							$output .= '<div class="panel new_rule">';
								$output .= '<div class="panel-heading advanced_options"><button aria-hidden="true" class="close delete_rule" type="button"><span class="fa fa-close "></span></button></div>';
								$output .= '<div class="panel-body">';
									//IF
									$output .= '<div class="col-xs-6 con_col">';
										$output .= '<h3 class="advanced_options"><strong><div class="badge rule_number">1</div>IF</strong> ';
											$output .= '<select id="operator" style="width:15%; float:none !important; display: inline" class="form-control" name="selector">';
												$output .= '<option value="any" selected="selected"> any </option>';
												$output .= '<option value="all"> all </option>';
											$output .= '</select> ';
										$output .= 'of these conditions are true</h3>';
										$output .= '<div class="get_rule_conditions">';
											$output .= '<div class="the_rule_conditions">';
											$output .= '<span class="statment_head"><div class="badge rule_number">1</div>IF</span> <select name="fields_for_conditions" class="form-control cl_field" style="width:33%;">';
													$output .= '<option selected="selected" value="0">-- Field --</option>';
												$output .= '</select>';
												$output .= '<select name="field_condition" class="form-control" style="width:28%;">';
													$output .= '<option selected="selected" value="0">-- Condition --</option>';
													$output .= '<option value="equal_to">Equal To</option>';
													$output .= '<option value="not_equal_to">Not Equal To</option>';
													$output .= '<option value="less_than">Less Than</option>';
													$output .= '<option value="greater_than">Greater Than</option>';
													/*$output .= '<option value="contains">Contains</option>';
													$output .= '<option value="not_contians">Does not Contain</option>';
													$output .= '<option value="is_empty">Is Empty</option>';*/
												$output .= '</select>';
												$output .= '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value">';
												$output .= '<button class=" button delete_condition advanced_options" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
										$output .= '</div>';
									$output .= '</div>';
										
										$output .= '<button class=" button add_condition advanced_options" style="width:100%;">Add Condition</button>';
									$output .= '</div>';
									
									//THEN
									$output .= '<div class="col-xs-4 con_col">';
										$output .= '<h3 class="advanced_options" style="">THEN</h3>';
										$output .= '<div class="get_rule_actions">';
											$output .= '<div class="the_rule_actions">';
											$output .= '<span class="statment_head">THEN</span> <select name="the_action" class="form-control" style="width:40%;">';
												$output .= '<option selected="selected" value="0">-- Action --</option>';
												$output .= '<option value="show">Show</option>';
												$output .= '<option value="hide">Hide</option>';
											$output .= '</select>';
											$output .= '<select name="cla_field" class="form-control" style="width:42%;">';
											$output .= '</select>';
											$output .= '<button class=" button delete_action advanced_options" style="width:15%;"><span class="fa fa-close"></span></button>';
											
														
											$output .= '</div>';
										$output .= '</div>';
										$output .= '<button class=" button add_action advanced_options" style="width:100%;">Add Action</button>';
										
									$output .= '</div>';
									
									//ELSE
										$output .= '<div class="con_col col-xs-2" >';
											$output .= '<h3 class="advanced_options" style="">ELSE</h3>';
											$output .= '<span class="statment_head">ELSE</span> <select name="reverse_actions" class="form-control">';
												$output .= '<option selected="selected" value="true">Reverse Actions</option>';
												$output .= '<option value="false">Do Nothing</option>';
											$output .= '</select>';
											$output .= '<button class="button delete_simple_rule" style="width:15%;"><span class="fa fa-close"></span></button>
											
											<div style="clear:both;"></div>';
										$output .= '</div>';
								$output .= '</div>';
							$output .= '</div>';
							
							
							
							
							
							
							
							
							$output .= '<div class="set_rule_conditions">';
								$output .= '<select name="fields_for_conditions" class="form-control cl_field" style="width:33%;">';
									$output .= '<option selected="selected" value="0">-- Field --</option>';
								$output .= '</select>';
								$output .= '<select name="field_condition" class="form-control" style="width:28%;">';
									$output .= '<option selected="selected" value="0">-- Condition --</option>';
									$output .= '<option value="equal_to">Equal To</option>';
									$output .= '<option value="not_equal_to">Not Equal To</option>';
									$output .= '<option value="less_than">Less Than</option>';
									$output .= '<option value="greater_than">Greater Than</option>';
									/*$output .= '<option value="contains">Contains</option>';
									$output .= '<option value="not_contians">Does not Contain</option>';
									$output .= '<option value="is_empty">Is Empty</option>';*/
								$output .= '</select>';
								$output .= '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value">';
								$output .= '<button class=" button delete_condition" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
							$output .= '</div>';
							
							
							$output .= '<div class="set_rule_actions">';
								
								$output .= '<select name="the_action" class="form-control" style="width:40%;">';
									$output .= '<option selected="selected" value="0">-- Action --</option>';
									$output .= '<option value="show">Show</option>';
									$output .= '<option value="hide">Hide</option>';
								$output .= '</select>';
								$output .= '<select name="cla_field" class="form-control" style="width:42%;">';
								$output .= '</select>';
								$output .= '<button class=" button delete_action" style="width:15%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
							$output .= '</div>';
							
						
						
						
						$output .= '</div>';
		
		
		$output .= '<div class="set_rules">';
				
			if(!empty($rules))
				{	
				foreach($rules as $rule)
					{
					if($rule)
						{
						$output .= '<div class="panel new_rule">';
							$output .= '<div class="panel-heading advanced_options"><button aria-hidden="true" data-dismiss="modal" class="close delete_rule" type="button"><span class="fa fa-close "></span></button></div>';
							$output .= '<div class="panel-body">';
						
								
							$rule_operator = $rule->operator;
							$reverse_action = $rule->reverse_actions;
						
							$output .= '<div class="col-xs-6 con_col">';
								$output .= '<h3 class="advanced_options"><strong><div class="badge rule_number">1</div>IF</strong> ';
									$output .= '<select id="operator" style="width:15%; float:none !important; display: inline" class="form-control" name="selector">';
										$output .= '<option value="any" '.(($rule_operator=='any' || !$rule_operator) ? 'selected="selected"' : '').'> any </option>';
										$output .= '<option value="all" '.(($rule_operator=='all' || !$rule_operator) ? 'selected="selected"' : '').'> all </option>';
									$output .= '</select> ';
								$output .= 'of these conditions are true</h3>';
							
								$output .= '<div class="get_rule_conditions">';
						
						foreach($rule->conditions as $condition)
							{
							$get_the_condition 	=  $condition->condition;
							$get_the_value 		=  $condition->condition_value;
							$selection_value 	=  $condition->selected_value;
							
							if($get_the_condition)
								{
								
								$output .= '<div class="the_rule_conditions">';
										$output .= '<span class="statment_head"><div class="badge rule_number">1</div>IF</span><select name="fields_for_conditions" class="form-control cl_field" style="width:33%;" data-selected="'.$selection_value.'">';
											$output .= '<option selected="selected" value="0">-- Field --</option>';
											
										$output .= '</select>';
										$output .= '<select name="field_condition" class="form-control" style="width:28%;">';
											$output .= '<option '.((!$get_the_condition) ? 'selected="selected"' : '').' value="0" >-- Condition --</option>';
											$output .= '<option '.(($get_the_condition=='equal_to') ? 'selected="selected"' : '').' 	value="equal_to">Equal To</option>';
											$output .= '<option '.(($get_the_condition=='not_equal_to') ? 'selected="selected"' : '').' value="not_equal_to">Not Equal To</option>';
											$output .= '<option '.(($get_the_condition=='less_than') ? 'selected="selected"' : '').' 	value="less_than">Less Than</option>';
											$output .= '<option '.(($get_the_condition=='greater_than') ? 'selected="selected"' : '').' value="greater_than">Greater Than</option>';
											/*$output .= '<option '.(($get_the_condition=='contains') ? 'selected="selected"' : '').' 	value="contains">Contains</option>';
											$output .= '<option '.(($get_the_condition=='not_contians') ? 'selected="selected"' : '').' value="not_contians">Does not Contain</option>';
											$output .= '<option '.(($get_the_condition=='is_empty') ? 'selected="selected"' : '').' 	value="is_empty">Is Empty</option>';
											*/
										$output .= '</select>';
										$output .= '<input type="text" name="conditional_value" class="form-control" style="width:28%;" placeholder="enter value" value="'.$get_the_value.'">';
										$output .= '<button class=" button delete_condition advanced_options" style="width:11%;"><span class="fa fa-close"></span></button><div style="clear:both;"></div>';
									$output .= '</div>';
								
								}
								
							}		
							$output .= '</div>';
							
							$output .= '<button class=" button add_condition advanced_options" style="width:100%;">Add Condition</button>';
						$output .= '</div>';
											
						//THEN
						$output .= '<div class="col-xs-4 con_col">';
							$output .= '<h3 class="advanced_options" style="">THEN</h3>';
							$output .= '<div class="get_rule_actions">';
							
						foreach($rule->actions as $action)
							{
							
							$get_action_to_take = $action->do_action;
							$selection_value = $action->selected_value;
							
							if($get_action_to_take)
								{						
								$output .= '<div class="the_rule_actions">';
										
										$output .= '<span class="statment_head">THEN</span><select name="the_action" class="form-control" style="width:40%;">';
											$output .= '<option '.((!$get_action_to_take) ? 'selected="selected"' : '').' value="0">-- Action --</option>';
											$output .= '<option '.(($get_action_to_take=='show') ? 'selected="selected"' : '').' value="show">Show</option>';
											$output .= '<option '.(($get_action_to_take=='hide') ? 'selected="selected"' : '').' value="hide">Hide</option>';
										$output .= '</select>';
										$output .= '<select name="cla_field" class="form-control" style="width:42%;" data-selected="'.$selection_value.'">';
										$output .= '</select>';
										$output .= '<button class=" button delete_action advanced_options" style="width:15%;"><span class="fa fa-close"></span></button>';
									$output .= '</div>';
								}
								
							}
								$output .= '</div>';
								$output .= '<button class=" button add_action advanced_options" style="width:100%;">Add Action</button>';
								$output .= '</div>';
							
							
							$output .= '<div class="con_col col-xs-2" >';
								$output .= '<h3 class="advanced_options" style="">ELSE</h3>';
								$output .= '<span class="statment_head">ELSE</span> <select name="reverse_actions" class="form-control">';
									$output .= '<option '.((!$reverse_action || $reverse_action=='true') ? 'selected="selected"' : '').' value="true">Reverse Actions</option>';
									$output .= '<option '.((!$reverse_action || $reverse_action=='false') ? 'selected="selected"' : '').' value="false">Do Nothing</option>';
								$output .= '</select>';
								$output .= '<button class="button delete_simple_rule" style="width:15%;"><span class="fa fa-close"></span></button>
								
								<div style="clear:both;"></div>';
							$output .= '</div>';
							
							
						$output .= '</div>';
					$output .= '</div>';	
						}
						
						
					}
				}
			$output .= '</div>';
			
			$output .= '<button class="button btn btn-default add_new_rule"><span class="fa fa-plus"></span>&nbsp;<span class="btn-tx">Add Rule</span></button>';
			$output .= '<div style="clear:both"></div>';
	//echo '</pre>';
		return $output;	
				
		}
		
		
		
		
		public function get_email_setup(){
			global $wpdb;
			if($_POST['form_Id'])
				{
				$get_id = 'Id';
				if($_POST['status']=='draft')
					$get_id = 'draft_Id';
					
				$form_Id = sanitize_text_field($_POST['form_Id']);
			
				$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE '.$get_id.'='.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT),'');
				$form = $wpdb->get_row($get_form);
				}
	//ADMIN EMAIL SETUP
					$preferences = get_option('nex-forms-preferences');
							$output .= '<div role="tabpanel" class="tab-pane active" id="admin-email">';
							
							
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">From Address</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_autoresponder_from_address" id="nex_autoresponder_from_address"  placeholder="Enter From Address" value="'.(($form->from_address) ? str_replace('\\','',$form->from_address) : $preferences['email_preferences']['pref_email_from_address']).'">';
									$output .= '</div>';
								$output .= '</div>';
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">From Name</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_autoresponder_from_name" id="nex_autoresponder_from_name"  placeholder="Enter From Name"  value="'.(($form->from_name) ? str_replace('\\','',$form->from_name) : $preferences['email_preferences']['pref_email_from_name']).'">';
									$output .= '</div>';
								$output .= '</div>';
								
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">Recipients</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_autoresponder_recipients" id="nex_autoresponder_recipients"  placeholder="Example: email@domian.com, email2@domian.com" value="'.(($form->mail_to) ? $form->mail_to : $preferences['email_preferences']['pref_email_recipients']).'">';
									$output .= '</div>';
								$output .= '</div>';
								
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">BCC</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_admin_bcc_recipients" id="nex_admin_bcc_recipients"  placeholder="Example: email@domian.com, email2@domian.com" value="'.(($form->bcc) ? $form->bcc : '').'" >';
									$output .= '</div>';
								$output .= '</div>';
								
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">Subject</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_autoresponder_confirmation_mail_subject" id="nex_autoresponder_confirmation_mail_subject"  placeholder="Enter Email Subject" value="'.(($form->confirmation_mail_subject) ? str_replace('\\','',$form->confirmation_mail_subject) : $preferences['email_preferences']['pref_email_subject']).'">';
									$output .= '</div>';
								$output .= '</div>';
								
								$output .= '<div class="row">';
									$output .= '<div class="col-xs-3">';
										$output .= '<small>Placeholders/Tags</small>';
										$output .= '<select name="email_field_tags" multiple="multiple"></select>';
									$output .= '</div>';
									$output .= '<div class="col-xs-9">';
										$output .= '<small>Admin Mail Body</small>';
										$output .= '<textarea style="width:100% !important;" placeholder="Enter Email Body. Use text or HTML" class="form-control" name="nex_autoresponder_admin_mail_body" id="nex_autoresponder_admin_mail_body">'.(($form->admin_email_body) ? str_replace('\\','',$form->admin_email_body) : $preferences['email_preferences']['pref_email_body']).'</textarea>';  //wp_editor( 'test', 'nex_autoresponder_admin_mail_body', $settings = array() );//
									$output .= '</div>';
								$output .= '</div>';
								
							
							$output .= '</div>';
							
					//USER EMAIL SETUP			
							$output .= '<div role="tabpanel" class="tab-pane" id="user-email">';
									
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">Recipients (map email field)</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<select class="form-control" data-selected="'.$form->user_email_field.'" id="nex_autoresponder_user_email_field" name="posible_email_fields"><option value="">Dont send confirmation mail to user</option></select>';
									$output .= '</div>';
								$output .= '</div>';
								
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">BCC</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_autoresponder_bcc_recipients" id="nex_autoresponder_bcc_recipients"  placeholder="Example: email@domian.com, email2@domian.com" value="'.(($form->bcc_user_mail) ? $form->bcc_user_mail : '').'" >';
									$output .= '</div>';
								$output .= '</div>';
								
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">Subject</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" name="nex_autoresponder_user_confirmation_mail_subject" id="nex_autoresponder_user_confirmation_mail_subject"  placeholder="Enter Email Subject" value="'.(($form->user_confirmation_mail_subject) ? str_replace('\\','',$form->user_confirmation_mail_subject) :  $preferences['email_preferences']['pref_user_email_subject']).'">';
									$output .= '</div>';
								$output .= '</div>';
																	
								$output .= '<div class="row">';
									$output .= '<div class="col-xs-3">';
										$output .= '<small>Placeholders/Tags</small>';
										$output .= '<select name="user_email_field_tags" multiple="multiple"></select>';
									$output .= '</div>';
									$output .= '<div class="col-xs-9">';
										$output .= '<small>Autoresponder Mail Body</small>';
										$output .= '<textarea style="width:100% !important;" placeholder="Enter Email Body. Use text or HTML" class="form-control" name="nex_autoresponder_confirmation_mail_body" id="nex_autoresponder_confirmation_mail_body">'.(($form->confirmation_mail_body) ? str_replace('\\','',$form->confirmation_mail_body) :  $preferences['email_preferences']['pref_user_email_body']).'</textarea>';  //wp_editor( 'test', 'nex_autoresponder_admin_mail_body', $settings = array() );//
									$output .= '</div>';
								$output .= '</div>';
								
							$output .= '</div>';
							
							
					
						
			
			echo $output;
			die();	
		}
		
		
		public function get_pdf_setup(){
			global $wpdb;
			if($_POST['form_Id'])
				{
				$get_id = 'Id';
				if($_POST['status']=='draft')
					$get_id = 'draft_Id';
					
				$form_Id = sanitize_text_field($_POST['form_Id']);
			
				$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE '.$get_id.'='.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT),'');
				$form = $wpdb->get_row($get_form);
				}
			//PDF SETUP
					$preferences = get_option('nex-forms-preferences');
							
								
								
			
			if ( is_plugin_active( 'nex-forms-export-to-pdf7/main.php' ) ) {
					
					$pdf_attach = explode(',',$form->attach_pdf_to_email);
					$output .= '<div class="row">';
									$output .= '<div class="col-sm-4">PDF Email Attachements</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<label for="pdf_admin_attach"><input '.(in_array('admin',$pdf_attach) ? 'checked="checked"': '').' name="pdf_admin_attach" value="1" id="pdf_admin_attach" type="checkbox"> Attach this PDF to Admin Notifications Emails<em></em></label>';
										$output .= '<label for="pdf_user_attach"><input '.(in_array('user',$pdf_attach) ? 'checked="checked"': '').' name="pdf_user_attach" value="1" id="pdf_user_attach" type="checkbox"> Attach this PDF to Autoresponder User Emails<em></em></label>';
									$output .= '</div>';
								$output .= '</div>';
					$output .= '<div class="row">';
									$output .= '<div class="col-xs-3">';
										$output .= '<small>Placeholders/Tags</small>';
										$output .= '<select name="pdf_field_tags" multiple="multiple"></select>';
									$output .= '</div>';
									$output .= '<div class="col-xs-9">';
										$output .= '<small>PDF Layout</small>';
										$output .= '<textarea style="width:100% !important;" placeholder="Enter your PDF body content" class="form-control" name="nex_pdf_html" id="nex_pdf_html">'.(($form->pdf_html) ? str_replace('\\','',$form->pdf_html) : $preferences['email_preferences']['pdf_html']).'</textarea>';  //wp_editor( 'test', 'nex_autoresponder_admin_mail_body', $settings = array() );//
									$output .= '</div>';
								$output .= '</div>';
					
			}
			else
				{
				$output .= '<div class="alert alert-success">You need the "<strong><em>PDF Creator for NEX-forms</em></strong>" Add-on to create your own PDF\'s from form data and also have the ability to send these PDF\'s via your admin and usert emails! <br>&nbsp;<a class="btn btn-success btn-large form-control" target="_blank" href="https://codecanyon.net/item/export-to-pdf-for-nexforms/11220942?ref=Basix">Buy Now</a></div>';
				}
			
			echo $output;
			die();	
		}
		
		
		public function get_hidden_fields($form_Id=''){
			global $wpdb;
			
				if($form_Id)
					{
					$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id= %d ',filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
					$form = $wpdb->get_row($get_form);
					}
				//HIDDEN FIELDS SETUP	
					
					$output = '';
						
					$output .= 	'<div class="hidden_fields_setup">';
							$output .= '
							
								<div class="hidden_field_clone hidden">
									<div class="input-group input-group-sm">
										<div class="input-group-addon">Name</div><input class="form-control field_name hidden_field_name" type="text" placeholder="Enter field name" value="">
										<div class="input-group-addon the_hidden_field_value">
											<select name="set_hidden_field_value">
																	<optgroup label="Dynamic Variables">
																		<option value="0" selected="selected">Value</option>
																		<option value="{{FORM_TITLE}}">Form Title</option>
																		<option value="{{C_PAGE}}">Current Page</option>
																		<option value="{{DATE_TIME}}">Date and Time</option>
																		<option value="{{WP_USER}}">Current User Name</option>
																		<option value="{{WP_USER_EMAIL}}">Current User Email</option>
																		<option value="{{WP_USER_URL}}">Current User URL</option>
																		<option value="{{WP_USER_IP}}">Current User IP</option>
																	</optgroup>
																	
																	<optgroup label="Server Variables">
																		<option value="{{DOCUMENT_ROOT}}">DOCUMENT_ROOT</option>
																		<option value="{{HTTP_REFERER}}">HTTP_REFERER</option>
																		<option value="{{REMOTE_ADDR}}">REMOTE_ADDR</option>
																		<option value="{{REQUEST_URI}}">REQUEST_URI</option>
																		<option value="{{HTTP_USER_AGENT}}">HTTP_USER_AGENT</option>											
																	</optgroup>
																</select>
										</div><input class="form-control field_value hidden_field_value" type="text" placeholder="Enter field value" value="">
										<div class="input-group-addon remove_hidden_field">
											<span class="fa fa-close"></span>
										</div>
									</div>
								</div>
							
							<div class="hidden_fields">
							';
						
						$hidden_options = '';
						
						if($form_Id)
							{
							
							if($form->hidden_fields)
								{
								$hidden_fields_raw = explode('[end]',$form->hidden_fields);
			
								foreach($hidden_fields_raw as $hidden_field)
									{
									$hidden_field = explode('[split]',$hidden_field);
									if($hidden_field[0])
										{
										$output .= '<div class="hidden_field"><div class="input-group input-group-sm">';
												$output .= '<div class="input-group-addon">Name</div><input type="text" class="form-control field_name hidden_field_name" value="'.$hidden_field[0].'" placeholder="Enter field name">';
												$output .= '<div class="input-group-addon the_hidden_field_value">
																<select name="set_hidden_field_value">
																	<optgroup label="Dynamic Variables">
																		<option value="0" selected="selected">Value</option>
																		<option value="{{FORM_TITLE}}">Form Title</option>
																		<option value="{{C_PAGE}}">Current Page</option>
																		<option value="{{DATE_TIME}}">Date and Time</option>																		
																		<option value="{{WP_USER}}">Current User Name</option>
																		<option value="{{WP_USER_EMAIL}}">Current User Email</option>
																		<option value="{{WP_USER_URL}}">Current User URL</option>
																		<option value="{{WP_USER_IP}}">Current User IP</option>
																	</optgroup>
																	
																	<optgroup label="Server Variables">
																		<option value="{{DOCUMENT_ROOT}}">DOCUMENT_ROOT</option>
																		<option value="{{HTTP_REFERER}}">HTTP_REFERER</option>
																		<option value="{{REMOTE_ADDR}}">REMOTE_ADDR</option>
																		<option value="{{REQUEST_URI}}">REQUEST_URI</option>
																		<option value="{{HTTP_USER_AGENT}}">HTTP_USER_AGENT</option>											
																	</optgroup>
																</select>
												</div><input type="text" class="form-control field_value hidden_field_value" value="'.$hidden_field[1].'" placeholder="Enter field value">';
												$output .= '<div class="input-group-addon remove_hidden_field"><span class="fa fa-close"></span></div>';
												
												$hidden_options .= '<option value="'.trim($hidden_field[0]).'">'.$hidden_field[0].'</option>';
												
										$output .= '</div></div>';
										}
									}
								}
							}
							$output .= '<div class="hidden_form_fields hidden">'.$hidden_options.'</div>
							';
							
						$output .= '</div>
							';					
								
				$output .= '<button class="button btn btn-default add_hidden_field"><span class="fa fa-plus"></span>&nbsp;<span class="btn-tx">Add hidden Field</span></div></button>';
			$output .= '<div style="clear:both"></div>';
			return $output;
			
			
		}
		
		
		
		
		public function get_form_hidden_fields($form_Id=''){
			global $wpdb;
			
				if($form_Id)
					{
					$get_form = $wpdb->prepare('SELECT form_hidden_fields FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id= %d ',filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
					$hidden_fields = $wpdb->get_var($get_form);
					}
				//HIDDEN FIELDS SETUP	
					
					$output = '';
						
					$output .= 	'<div class="hidden_fields_setup">';
							$output .= '
							
								<div class="hidden_field_clone hidden">
									<div class="input-group input-group-sm">
										<div class="input-group-addon">Name</div><input class="form-control field_name hidden_field_name" type="text" placeholder="Enter field name" value="">
										<div class="input-group-addon the_hidden_field_value">
											<select name="set_hidden_field_value">
																	<optgroup label="Dynamic Variables">
																		<option value="0" selected="selected">Value</option>
																		<option value="{{FORM_TITLE}}">Form Title</option>
																		<option value="{{C_PAGE}}">Current Page</option>
																		<option value="{{DATE_TIME}}">Date and Time</option>
																		<option value="{{WP_USER}}">Current User Name</option>
																		<option value="{{WP_USER_EMAIL}}">Current User Email</option>
																		<option value="{{WP_USER_URL}}">Current User URL</option>
																		<option value="{{WP_USER_IP}}">Current User IP</option>
																	</optgroup>
																	
																	<optgroup label="Server Variables">
																		<option value="{{DOCUMENT_ROOT}}">DOCUMENT_ROOT</option>
																		<option value="{{HTTP_REFERER}}">HTTP_REFERER</option>
																		<option value="{{REMOTE_ADDR}}">REMOTE_ADDR</option>
																		<option value="{{REQUEST_URI}}">REQUEST_URI</option>
																		<option value="{{HTTP_USER_AGENT}}">HTTP_USER_AGENT</option>											
																	</optgroup>
																</select>
										</div><input class="form-control field_value hidden_field_value" type="text" placeholder="Enter field value" value="">
										<div class="input-group-addon remove_hidden_field">
											<span class="fa fa-close"></span>
										</div>
									</div>
								</div>';
							
							$output .= '<div class="hidden_fields">
							';
						
						$hidden_options = '';
						
						if($form_Id)
							{
							
							if($hidden_fields)
								{
								$hidden_fields_array = json_decode($hidden_fields);
			
								foreach($hidden_fields_array as $hidden_field)
									{
									
									//if($hidden_field[0])
										//{
										$output .= '<div class="hidden_field"><div class="input-group input-group-sm">';
												$output .= '<div class="input-group-addon">Name</div><input type="text" class="form-control field_name hidden_field_name" value="'.$hidden_field->field_name.'" placeholder="Enter field name">';
												$output .= '<div class="input-group-addon the_hidden_field_value">
																<select name="set_hidden_field_value">
																	<optgroup label="Dynamic Variables">
																		<option value="0" selected="selected">Value</option>
																		<option value="{{FORM_TITLE}}">Form Title</option>
																		<option value="{{C_PAGE}}">Current Page</option>
																		<option value="{{DATE_TIME}}">Date and Time</option>																		
																		<option value="{{WP_USER}}">Current User Name</option>
																		<option value="{{WP_USER_EMAIL}}">Current User Email</option>
																		<option value="{{WP_USER_URL}}">Current User URL</option>
																		<option value="{{WP_USER_IP}}">Current User IP</option>
																	</optgroup>
																	
																	<optgroup label="Server Variables">
																		<option value="{{DOCUMENT_ROOT}}">DOCUMENT_ROOT</option>
																		<option value="{{HTTP_REFERER}}">HTTP_REFERER</option>
																		<option value="{{REMOTE_ADDR}}">REMOTE_ADDR</option>
																		<option value="{{REQUEST_URI}}">REQUEST_URI</option>
																		<option value="{{HTTP_USER_AGENT}}">HTTP_USER_AGENT</option>											
																	</optgroup>
																</select>
												</div><input type="text" class="form-control field_value hidden_field_value" value="'.$hidden_field->field_value.'" placeholder="Enter field value">';
												$output .= '<div class="input-group-addon remove_hidden_field"><span class="fa fa-close"></span></div>';
												
												$hidden_options .= '<option value="'.trim($hidden_field->field_name).'">'.$hidden_field->field_name.'</option>';
												
										$output .= '</div></div>';
										//}
									}
								}
							}
							$output .= '<div class="hidden_form_fields hidden">'.$hidden_options.'</div>
							';
							
						$output .= '</div>
							';					
								
				$output .= '<button class="button btn btn-default add_hidden_field"><span class="fa fa-plus"></span>&nbsp;<span class="btn-tx">Add hidden Field</span></div></button>';
				$output .= '<div style="clear:both"></div>';
			return $output;
			
			
		}
		
		
		
		public function get_options_setup(){
			global $wpdb;
			if($_POST['form_Id'])
				{
				$get_id = 'Id';
				if($_POST['status']=='draft')
					$get_id = 'draft_Id';
					
				$form_Id = sanitize_text_field($_POST['form_Id']);	
				
				$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE '.$get_id.'='.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT),'');
				$form = $wpdb->get_row($get_form);
				}
			$preferences = get_option('nex-forms-preferences');	
	//FORM ATTR
		
		$form_type = $form->form_type;
		
		if($_POST['form_type'])
			$form_type = $_POST['form_type'];
		
		$output .= '<div class="form_attr hidden">';
			$output .= '<div class="form_type">';
				$output .= ($form_type) ? $form_type : 'normal';
			$output .= '</div>';
			$output .= '<div class="form_title">';
				$output .= $form->title;
			$output .= '</div>';			
		$output .= '</div>';
	//ON SUBMIT SETUP
	
							$output .= 	'<div class="on_submit_setup">';
								$output .= '<div role="toolbar" class="btn-toolbar">';
	/*** From Address ***/	
									$output .= '<div role="group" class="btn-group post_action">';
										$output .= '<small>Post Action</small>';
										$output .= '<button class="btn btn-default ajax '.((!$form->post_action || $form->post_action=='ajax') ? 'active' : '' ).'" type="button" title="Use AJAX with no page refreshing" data-value="ajax"><span class="btn-tx">AJAX</span></button>';
										$output .= '<button class="btn btn-default custom '.(($form->post_action=='custom') ? 'active' : '' ).'" type="button" title="Post Form to custom URL" data-value="custom"><span class="btn-tx">Custom</span></button>';
									$output .= '</div>';
									
									$output .= '<div role="group" class="btn-group on_form_submission '.(($form->post_action=='custom') ? 'hidden' : '' ).'">';
										$output .= '<small>After Submit</small>';
										$output .= '<button class="btn btn-default message '.((!$form->on_form_submission || $form->on_form_submission=='message') ? 'active' : '' ).'" type="button" title="Show on-screen message" data-value="message"><span class="btn-tx">Show Message</span></button>';
										$output .= '<button class="btn btn-default redirect '.(($form->on_form_submission=='redirect') ? 'active' : '' ).'" type="button" title="Redirect to a URL after submit" data-value="redirect"><span class="btn-tx">Redirect</span></button>';
									$output .= '</div>';
									
									$output .= '<div role="group" class="btn-group post_method '.(($form->post_action=='custom') ? '' : 'hidden' ).'">';
										$output .= '<small>Submmision method</small>';
										$output .= '<button class="btn btn-default post '.((!$form->post_type || $form->post_type=='POST') ? 'active' : '' ).'" type="button" title="Use POST" data-value="POST"><span class="btn-tx">POST</span></button>';
										$output .= '<button class="btn btn-default get '.(($form->post_type=='GET') ? 'active' : '' ).'" type="button" title="USE GET" data-value="GET"><span class="btn-tx">GET</span></button>';
									$output .= '</div>';
									
								$output .= '</div>';
								
								
								
								//On screen confirmation message
								$output .= '<div class="ajax_settings '.(($form->post_action=='custom') ? 'hidden' : '' ).'"><div class="on_screen_message_settings '.(($form->on_form_submission=='message' || !$form->on_form_submission) ? '' : 'hidden' ).'"><small>On-screen confirmation message</small><textarea class="form-control" name="on_screen_confirmation_message" id="nex_autoresponder_on_screen_confirmation_message">'.(($form->on_screen_confirmation_message) ? str_replace('\\','',$form->on_screen_confirmation_message) : $preferences['other_preferences']['pref_other_on_screen_message'] ).'</textarea></div>';
								
								$output .= '<div class="row redirect_settings '.(($form->on_form_submission=='redirect') ? '' : 'hidden' ).'">';
									$output .= '<div class="col-sm-4">Redirect to</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" value="'.$form->confirmation_page.'" placeholder="Enter URL" name="confirmation_page" id="nex_autoresponder_confirmation_page" data-tag-class="label-info">';
									$output .= '</div>';
								$output .= '</div>';
								
								
								
								
							$output .= '</div>';
							$output .= '<div class="row custom_url_settings '.(($form->post_action=='custom') ? '' : 'hidden' ).'">';
									$output .= '<div class="col-sm-4">Submit form to</div>';
									$output .= '<div class="col-sm-8">';
										$output .= '<input type="text" class="form-control" value="'.$form->custom_url.'" name="custum_url" placeholder="Enter Custom URL" id="on_form_submission_custum_url" data-tag-class="label-info">';
									$output .= '</div>';
								$output .= '</div>';
	
			echo $output;
			die();	
		}
		public function load_form_entries(){
			global $wpdb;
			
			$args 		= str_replace('\\','',$_POST['args']);
			$headings 	= array('Form Name'=>'nex_forms_Id','Page'=>'page','IP Address'=>'ip','User'=>'user_Id','Date Submitted'=>'date_time');
			
			$form_Id = sanitize_text_field($_POST['form_Id']);
			$post_additional_params = sanitize_text_field($_POST['additional_params']);
			$plugin_alias = sanitize_text_field($_POST['plugin_alias']);
			$orderby = sanitize_text_field($_POST['orderby']);
			$current_page = sanitize_text_field($_POST['current_page']);
				
			
			$additional_params = json_decode(str_replace('\\','',$post_additional_params),true);
			
			if(is_array($additional_params))
				{
				foreach($additional_params as $column=>$val)
					$where_str .= ' AND '.$column.'="'.$val.'"';
				}
			
			if($form_Id)
				$where_str .= ' AND nex_forms_Id='.filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT);
			
			
			$sql = $wpdb->prepare('SELECT * FROM '. $wpdb->prefix . 'wap_nex_forms_entries WHERE Id <> "" 
											'.(($tree) ? ' AND parent_Id="0"' : '').' 
											'.(($plugin_alias) ? ' AND (plugin="'.filter_var($plugin_alias,FILTER_SANITIZE_STRING).'" || plugin="shared")' : '').' 
											'.$where_str.'   
											ORDER BY 
											'.((isset($orderby) && !empty($orderby)) ? filter_var($orderby,FILTER_SANITIZE_STRING).' 
											'.filter_var($orderby,FILTER_SANITIZE_STRING) : 'Id DESC').' 
											LIMIT '.((isset($current_page)) ? filter_var($current_page,FILTER_SANITIZE_NUMBER_INT)*10 : '0'  ).',10 ','');
			$results 	= $wpdb->get_results($sql);
			
			
			$output .= '<table class="table table-striped">';
			
			$output .= '<tr><th class="entry_Id">ID</th>';
			
			$order = sanitize_text_field($_POST['order']);
			
			foreach($headings as $heading=>$val)	
						{
						$output .= '<th class="manage-column sortable column-'.$val.'"><a class="'.(($order) ? $order : 'asc').'"><span data-col-order="'.(($order) ? $order : 'asc').'" data-col-name="'.$val.'" class="sortable-column">'.$heading.'</span></a></th>';
						}
			$output .= '<th>&nbsp;</th></tr>';
			if($results)
				{			
				foreach($results as $data)
					{	
					$output .= '<tr>';
					$output .= '<td class="manage-column column-">'.$data->Id.'</td>';
					$k=1;
					foreach($headings as $heading)	
						{
						
						$heading = NEXForms_Functions::format_name($heading);
						$heading = str_replace('_id','_Id',$heading);
						
						if($heading=='user_Id')
							{
							$val = NEXForms_Database_Actions::get_username($data->$heading);	
							}
						else
							{
							$val = (strstr($heading,'Id')) ? NEXForms_Database_Actions::get_title($data->$heading,'wap_'.str_replace('_Id','',$heading)) : $data->$heading;
							
							
							$val = str_replace('\\', '', NEXForms_Functions::view_excerpt($val,25));
							}
						
						$output .= '<td class="manage-column column-'.$heading.'">'.(($k==1) ? '<strong>'.$val.'</strong>' : $val).'';
						$k++;
						}
					
					$output .= '<td width="16%" align="right" class="view_export_del">';
					
					if ( is_plugin_active( 'nex-forms-export-to-pdf7/main.php' ) )
						$output .= '<a target="_blank" title="PDF [new window]" href="'.WP_PLUGIN_URL . '/nex-forms-export-to-pdf/examples/main.php?entry_ID='.$data->Id.'" class="nf-button"><span class="fa fa-file-pdf-o"></span> PDF</div></a>&nbsp;';
					else
						$output .= '<a target="_blank" title="Get export to PDF add-on" href="http://codecanyon.net/item/export-to-pdf-for-nexforms/11220942?ref=Basix" class="nf-button buy">PDF</a>&nbsp;';
					
					$output .= '<a class="nf-button view_form_entry" data-target="#viewFormEntry" data-toggle="modal"  data-id="'.$data->Id.'">View</a>
					<a data-original-title="Delete" title="" data-placement="top" data-toggle="tooltip" class="do_delete_entry nf-button" id="'.$data->Id.'">&nbsp;
					<span class="fa fa-trash"></span>&nbsp;</a>
					
					</td>';
					$output .= '</tr>';	
					
					}
				}
			else
				{
				$output .= '<tr>';	
				$output .= '<td></td><td class="manage-column" colspan="'.(count($headings)).'">No items found</td>';
				$output .= '</tr>';
				}
			
			$output .= '</table>';
				
			echo $output;
			die();

		
		}
		
		public function populate_form_entry(){
			global $wpdb;
			
			$edit_entry = 0;
			
			if($_POST['edit_entry'])
				$edit_entry = sanitize_text_field($_POST['edit_entry']);
			
			
			$form_entry_Id = sanitize_text_field($_POST['form_entry_Id']);
			
			$get_form_entry = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms_entries WHERE Id='.filter_var($form_entry_Id,FILTER_SANITIZE_NUMBER_INT),'');
			$form_entry = $wpdb->get_row($get_form_entry);
			
			$form_data = json_decode($form_entry->form_data);
			
			$database = new NEXForms_Database_Actions();
			
			if($database->checkout())
				{			
				 $output .= '<form id="" class="" name="save_form_entry" action="'.admin_url('admin-ajax.php').'" method="post" enctype="multipart/form-data">';
					 $output .= '<div class="entry_wrapper">';
						 $output .= '<div class="additional_entry_details hidden">';
							$output .= '<div class="entry_id">#<strong>'.$form_entry->Id.'</strong></div>';
							$output .= '<div class="the_form">Form: <strong>'.NEXForms_Database_Actions::get_title($form_entry->nex_forms_Id,'wap_nex_forms').'</strong></div>';
							$output .= '<div class="page">Page: <strong>'.$form_entry->page.'</strong></div>';
							$output .= '<div class="date_time">Date: <strong>'.$form_entry->date_time.'</strong></div>';
							$output .= '<div class="user_ip">User IP: <strong>'.$form_entry->ip.'</strong></div>';
							if($form_entry->user_Id)
								$output .= '<div class="user_id">Username: <strong>'.NEXForms_Database_Actions::get_username($form_entry->user_Id).'</strong></div>';
							/*$output .= '<table class="highlight">';
								$output .= '<tbody>';
									$output .= '<tr>';
										$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>Entry ID</strong></td>';
										$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>Form</strong></td>';
										$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>Page</strong></td>';
										$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>Date &amp; Time</strong></td>';
										$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>User IP</strong></td>';
										if($form_entry->user_Id)
											$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>Username</strong></td>';
									$output .= '</tr>';	
									$output .= '<tr>';
										$output .= '<td valign="top" style="vertical-align:top !important;">'.$form_entry->Id.'</td>';
										$output .= '<td valign="top" style="vertical-align:top !important;">'.NEXForms_Database_Actions::get_title($form_entry->nex_forms_Id,'wap_nex_forms').'</td>';
										$output .= '<td valign="top" style="vertical-align:top !important;">'.$form_entry->page.'</td>';
										$output .= '<td valign="top" style="vertical-align:top !important;">'.$form_entry->date_time.'</td>';
										$output .= '<td valign="top" style="vertical-align:top !important;">'.$form_entry->ip.'</td>';
											if($form_entry->user_Id)
												$output .= '<td valign="top" style="vertical-align:top !important;">'.NEXForms_Database_Actions::get_username($form_entry->user_Id).'</td>';
									$output .= '</tr>';	
								$output .= '</tbody>';
							$output .= '</table>';*/
						 $output .= '</div>';
						 $output .= '<input type="hidden" name="action" value="do_form_entry_save">';
						 $output .= '<input type="hidden" name="form_entry_id" value="'.$form_entry_Id.'">';
						 $output .= '<table class="highlight" id="form_entry_table">';
							$output .= '<thead>';
								$output .= '<tr>';
									$output .= '<th>Field Name</th>';
									$output .= '<th>Field Value</th>';
								$output .= '</tr>';
							$output .= '</thead>';
							$output .= '<tbody class="form_entry_data_records">';
							
							$img_ext_array = array('jpg','jpeg','png','tiff','gif','psd');
							$file_ext_array = array('doc','docx','mpg','mpeg','mp3','mp4','odt','odp','ods','pdf','ppt','pptx','txt','xls','xlsx');
				
							foreach($form_data as $data)
								{
								if($data->field_name!='math_result' && $data->field_name!='paypal_invoice'){
									$output .= '<tr>';
										$output .= '<td valign="top" style="vertical-align:top !important; width:200px;"><strong>';
											$output .= NEXForms_Functions::unformat_name($data->field_name);
										$output .= '</strong></td>';
										$output .= '<td valign="top" style="vertical-align:top !important;">';
											if(is_array($data->field_value))
												{
												 foreach($data->field_value as $key=>$val)
													{
													if($edit_entry)
														$output .= '<input name="'.$data->field_name.'[]" type="checkbox" id="'.$val.'" value="'.$val.'" checked="checked" />
																	<label for="'.$val.'">'.$val.'</label><br />';
													else	
														$output .= '<span class="text-success fa fa-check"></span>&nbsp;&nbsp;'.$val.'<br />';
													}
												}
											else
												{	
												if(strstr($data->field_value,',') && !strstr($data->field_value,'data:image'))
													{
													$is_array = explode(',',$data->field_value);
													foreach($is_array as $item)
														{
														if(in_array(NEXForms_Functions::get_ext($item),$img_ext_array))
															$output .= '<div class="col-xs-6" style="margin-bottom:15px;"><img class="materialboxed" width="100%" src="'.$item.'"></div>
	';													else if(in_array(NEXForms_Functions::get_ext($item),$file_ext_array))
															$output .= '<div class="col-xs-6" style="margin-bottom:15px;"><a class="file_ext_data" href="'.$item.'" target="_blank">'.$item.'</a></div>';
														else
															$output .= $item;
														}
													$output .= '<input type="hidden" name="'.$data->field_name.'" value="'.$data->field_value.'">';
													}
												else if(strstr($data->field_value,'data:image'))
													$output .= '<img src="'.$data->field_value.'"><input type="hidden" name="'.$data->field_name.'" value="'.$data->field_value.'">';
												else if(in_array(NEXForms_Functions::get_ext($data->field_value),$img_ext_array))
													$output .= '<div class="col-xs-6"><img class="materialboxed" width="100%" src="'.$data->field_value.'" style="margin-bottom:15px;"></div><input type="hidden" name="'.$data->field_name.'" value="'.$data->field_value.'">';
												else
													{
													if($edit_entry)
														{
														if(strlen($data->field_value)>50)
															{
															$output .= '<div class="input-field">
																		  <textarea class="materialize-textarea" name="'.$data->field_name.'" id="'.$data->field_name.'">'.$data->field_value.'</textarea>
																		</div>';
															}
														else
															{
															$output .= '<div class="input-field">
																		  <input name="'.$data->field_name.'" id="'.$data->field_name.'" type="text" value="'.$data->field_value.'">
																		</div>';
															}
														}
													else
														$output .= $data->field_value;
													
													}
												}
										$output .= '</td>';
										
									$output .= '</tr>';
									}
								}
							$output .= '</tbody>';
						$output .= '</table>';
					$output .= '</div>';
					$output .= '<input type="submit" value="submit" class="hidden">';
				$output .= '</form>';
				}
			else
				{
				$output .= '<div class="alert alert-danger" style="margin:20px;">Sorry, you need to register this plugin to view entries. Go to global settings above and follow registration procedure.</div>';	
				}
			echo $output;
			
			die();	
		}
	
	public function load_pagination($table='',$form_Id='',$echo=false,$additional_params=array(), $search_params=array(), $search_term=''){

			if($_POST['form_Id'])
				$form_Id = sanitize_text_field($_POST['form_Id']);
			
			if($_POST['table'])
				$table = sanitize_text_field($_POST['table']);
			
			if($_POST['echo'])
				$echo = sanitize_text_field($_POST['echo']);
			
			if($_POST['search_params'])
				$search_params = sanitize_text_field($_POST['search_params']);
			
			if($_POST['additional_params'])
				$additional_params = sanitize_text_field($_POST['additional_params']);
			
			if($_POST['search_term'])
				$search_term = sanitize_text_field($_POST['search_term']);
			
			$total_records = NEXForms_Database_Actions::get_total_records($table,$additional_params,$form_Id,$search_params, $search_term, $echo);
			
			$total_pages = ((is_float($total_records/10)) ? (floor($total_records/10))+1 : $total_records/10);
			
			$output .= '<span class="displaying-num"><span class="entry-count">'.$total_records.'</span> item'.(($total_records==1) ? '' : 's').'</span>';
			if($total_pages>1)
				{				
				$output .= '<span class="pagination-links">';
				$output .= '<a class="first-page iz-first-page btn waves-effect waves-light"><span class="fa fa-angle-double-left"></span></a>';
				$output .= '<a title="Go to the next page" class="iz-prev-page btn waves-effect waves-light prev-page"><span class="fa fa-angle-left"></span></a>&nbsp;';
				$output .= '<span class="paging-input"> ';
				$output .= '<span class="current-page">'.($_POST['page']+1).'</span> of <span class="total-pages">'.$total_pages.'</span>&nbsp;</span>';
				$output .= '<a title="Go to the next page" class="iz-next-page btn waves-effect waves-light next-page"><span class="fa fa-angle-right"></span></a>';
				$output .= '<a title="Go to the last page" class="iz-last-page btn waves-effect waves-light last-page"><span class="fa fa-angle-double-right"></span></a></span>';
				}
			if($echo)
				{
				echo $output;
				die();
				}
			else
				return $output;
		}
	
	public function get_total_records($table,$additional_params=array(),$nex_forms_id='', $search_params=array(),$search_term='',$echo=true){
			global $wpdb;
			
			$where_str = '';
			
			if(is_array($additional_params))
				{
				foreach($additional_params as $clause)
					{
					$like = '';
					if($clause['operator'] == 'LIKE' || $clause['operator'] == 'NOT LIKE')
						$like = '%';
					$where_str .= ' AND `'.$clause['column'].'` '.(($clause['operator']) ? $clause['operator'] : '=').'  "'.$like.$clause['value'].$like.'"';
					}
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
				
			if($nex_forms_id)
				$where_str .= ' AND nex_forms_Id='.$nex_forms_id;
			
			$set_alias = isset($_POST['plugin_alias']) ? $_POST['plugin_alias'] : '';
			$tree = '';
			$sql = 'SELECT count(*) FROM '.$wpdb->prefix . filter_var($table,FILTER_SANITIZE_STRING).' WHERE Id<>"" '. (($tree) ? ' AND parent_Id=0' : '').' '. ((filter_var($set_alias,FILTER_SANITIZE_STRING)) ? ' AND plugin="'.$set_alias.'"' : '').' '.$where_str;
			
			//echo $sql;
			return $wpdb->get_var($sql);
			
			if($echo)
				{
				echo $wpdb->get_var($sql);
				die();
				}
			else
				return $wpdb->get_var($sql);
			
		}
	
	
	public function save_mc_key() {
		$api_key = sanitize_text_field($_POST['mc_api']);
		update_option('nex_forms_mailchimp_api_key',filter_var($api_key,FILTER_SANITIZE_STRING));
		
		die();
	}
	public function save_gr_key() {
		$api_key = sanitize_text_field($_POST['gr_api']);
		update_option('nex_forms_get_response_api_key',filter_var($api_key,FILTER_SANITIZE_STRING));
		
		die();
	}
	
	public function save_email_config() {
		
		$email_method = sanitize_text_field($_POST['email_method']);
		$smtp_host = sanitize_text_field($_POST['smtp_host']);
		$mail_port = sanitize_text_field($_POST['mail_port']);
		$email_smtp_secure = sanitize_text_field($_POST['email_smtp_secure']);
		$smtp_auth = sanitize_text_field($_POST['smtp_auth']);
		$set_smtp_user = sanitize_text_field($_POST['set_smtp_user']);
		$set_smtp_pass = sanitize_text_field($_POST['set_smtp_pass']);
		$email_content = sanitize_text_field($_POST['email_content']);
		
		update_option('nex-forms-email-config',array
			(
			'email_method'			=> filter_var($email_method,FILTER_SANITIZE_STRING),
			'smtp_host' 			=> filter_var($smtp_host,FILTER_SANITIZE_STRING),
			'mail_port' 			=> filter_var($mail_port,FILTER_SANITIZE_NUMBER_INT),
			'email_smtp_secure' 	=> filter_var($email_smtp_secure,FILTER_SANITIZE_STRING),
			'smtp_auth' 			=> filter_var($smtp_auth,FILTER_SANITIZE_NUMBER_INT),
			'set_smtp_user' 		=> filter_var($set_smtp_user,FILTER_SANITIZE_STRING),
			'set_smtp_pass' 		=> filter_var($set_smtp_pass,FILTER_SANITIZE_STRING),
			'email_content' 		=> filter_var($email_content,FILTER_SANITIZE_STRING)
			)
		
		);
		die();
	}
	
	public function save_script_config() {

		if(!array_key_exists('inc-jquery',$_POST))
			$_POST['inc-jquery'] = '2';
		if(!array_key_exists('inc-jquery-ui-core',$_POST))
			$_POST['inc-jquery-ui-core'] = '2';
		if(!array_key_exists('inc-jquery-ui-autocomplete',$_POST))
			$_POST['inc-jquery-ui-autocomplete'] = '2';
		if(!array_key_exists('inc-jquery-ui-slider',$_POST))
			$_POST['inc-jquery-ui-slider'] = '2';
		if(!array_key_exists('inc-jquery-form',$_POST))
			$_POST['inc-jquery-form'] = '2';
		if(!array_key_exists('inc-onload',$_POST))
			$_POST['inc-onload'] = '2';
		if(!array_key_exists('enable-print-scripts',$_POST))
			$_POST['enable-print-scripts'] = '2';
		if(!array_key_exists('inc-moment',$_POST))
			$_POST['inc-moment'] = '2';
		if(!array_key_exists('inc-locals',$_POST))
			$_POST['inc-locals'] = '2';
		if(!array_key_exists('inc-datetime',$_POST))
			$_POST['inc-datetime'] = '2';
		if(!array_key_exists('inc-math',$_POST))
			$_POST['inc-math'] = '2';
		if(!array_key_exists('inc-colorpick',$_POST))
			$_POST['inc-colorpick'] = '2';
		if(!array_key_exists('inc-wow',$_POST))
			$_POST['inc-wow'] = '2';
		if(!array_key_exists('inc-raty',$_POST))
			$_POST['inc-raty'] = '2';
		if(!array_key_exists('inc-sig',$_POST))
			$_POST['inc-sig'] = '2';
		
		
		
		$inc_jquery = sanitize_text_field($_POST['inc-jquery']);
		$inc_jquery_ui_core = sanitize_text_field($_POST['inc-jquery-ui-core']);
		$inc_jquery_ui_autocomplete = sanitize_text_field($_POST['inc-jquery-ui-autocomplete']);
		$inc_jquery_ui_slider = sanitize_text_field($_POST['inc-jquery-ui-slider']);
		$inc_bootstrap = sanitize_text_field($_POST['inc-bootstrap']);
		$inc_jquery_form = sanitize_text_field($_POST['inc-jquery-form']);
		$inc_onload = sanitize_text_field($_POST['inc-onload']);
		$enable_print_scripts = sanitize_text_field($_POST['enable-print-scripts']);
		
		$inc_moment = sanitize_text_field($_POST['inc-moment']);
		$inc_locals = sanitize_text_field($_POST['inc-locals']);
		$inc_datetime = sanitize_text_field($_POST['inc-datetime']);
		$inc_math = sanitize_text_field($_POST['inc-math']);
		$inc_colorpick = sanitize_text_field($_POST['inc-colorpick']);
		$inc_wow = sanitize_text_field($_POST['inc-wow']);
		$inc_raty = sanitize_text_field($_POST['inc-raty']);
		$inc_sig = sanitize_text_field($_POST['inc-sig']);
		
		
		update_option('nex-forms-script-config',array
			(
			'inc-jquery' 					=> filter_var($inc_jquery,FILTER_SANITIZE_NUMBER_INT),
			'inc-jquery-ui-core' 			=> filter_var($inc_jquery_ui_core,FILTER_SANITIZE_NUMBER_INT),
			'inc-jquery-ui-autocomplete' 	=> filter_var($inc_jquery_ui_autocomplete,FILTER_SANITIZE_NUMBER_INT),
			'inc-jquery-ui-slider' 			=> filter_var($inc_jquery_ui_slider,FILTER_SANITIZE_NUMBER_INT),
			'inc-jquery-form' 				=> filter_var($inc_jquery_form,FILTER_SANITIZE_NUMBER_INT),
			'inc-bootstrap' 				=> filter_var($inc_bootstrap,FILTER_SANITIZE_NUMBER_INT),
			'inc-onload' 					=> filter_var($inc_onload,FILTER_SANITIZE_NUMBER_INT),
			'enable-print-scripts' 			=> filter_var($enable_print_scripts,FILTER_SANITIZE_NUMBER_INT),
			'inc-moment' 					=> filter_var($inc_moment,FILTER_SANITIZE_NUMBER_INT),
			'inc-locals' 					=> filter_var($inc_locals,FILTER_SANITIZE_NUMBER_INT),
			'inc-datetime' 					=> filter_var($inc_datetime,FILTER_SANITIZE_NUMBER_INT),
			'inc-math' 						=> filter_var($inc_math,FILTER_SANITIZE_NUMBER_INT),
			'inc-colorpick' 				=> filter_var($inc_colorpick,FILTER_SANITIZE_NUMBER_INT),
			'inc-wow' 						=> filter_var($inc_wow,FILTER_SANITIZE_NUMBER_INT),
			'inc-raty' 						=> filter_var($inc_raty,FILTER_SANITIZE_NUMBER_INT),
			'inc-sig' 						=> filter_var($inc_sig,FILTER_SANITIZE_NUMBER_INT)
			)
		);
		die();
	}
	
	
	
	public function save_style_config() {

		if(!array_key_exists('incstyle-jquery',$_POST))
			$_POST['incstyle-jquery'] = '0';
		if(!array_key_exists('incstyle-font-awesome',$_POST))
			$_POST['incstyle-font-awesome'] = '0';
		if(!array_key_exists('incstyle-bootstrap',$_POST))
			$_POST['incstyle-bootstrap'] = '0';
		if(!array_key_exists('incstyle-jquery',$_POST))
			$_POST['incstyle-custom'] = '0';
		if(!array_key_exists('incstyle-animations',$_POST))
			$_POST['incstyle-animations'] = '0';
		if(!array_key_exists('enable-print-styles',$_POST))
			$_POST['enable-print-styles'] = '0';
		
		
		$incstyle_jquery = sanitize_text_field($_POST['incstyle-jquery']);
		$incstyle_font_awesome = sanitize_text_field($_POST['incstyle-font-awesome']);
		$incstyle_bootstrap = sanitize_text_field($_POST['incstyle-bootstrap']);
		$incstyle_custom = sanitize_text_field($_POST['incstyle-custom']);
		$enable_print_styles = sanitize_text_field($_POST['enable-print-styles']);
		$incstyle_animations = sanitize_text_field($_POST['incstyle-animations']);
		
		update_option('nex-forms-style-config',array
			(
			'incstyle-jquery' 		=> filter_var($incstyle_jquery,FILTER_SANITIZE_NUMBER_INT),
			'incstyle-font-awesome' => filter_var($incstyle_font_awesome,FILTER_SANITIZE_NUMBER_INT),
			'incstyle-bootstrap' 	=> filter_var($incstyle_bootstrap,FILTER_SANITIZE_NUMBER_INT),
			'incstyle-custom' 		=> filter_var($incstyle_custom,FILTER_SANITIZE_NUMBER_INT),
			'incstyle-animations' 	=> filter_var($incstyle_animations,FILTER_SANITIZE_NUMBER_INT),
			'enable-print-styles' 	=> filter_var($enable_print_styles,FILTER_SANITIZE_NUMBER_INT)
			)
		);
		die();
	}
	public function save_other_config() {
		
		if(!get_option('nex-forms-other-config'))
		{
		update_option('nex-forms-other-config',array(
				'enable-tinymce'=>'1',
				'enable-widget'=>'1',
				'enable-color-adapt'=>'1',
				'set-wp-user-level'=>'administrator',	
			));
		}
		if(!array_key_exists('enable-tinymce',$_POST))
			$_POST['enable-tinymce'] = '0';
		if(!array_key_exists('enable-widget',$_POST))
			$_POST['enable-widget'] = '0';
		if(!array_key_exists('enable-color-adapt',$_POST))
			$_POST['enable-color-adapt'] = '0';
		if(!array_key_exists('set-wp-user-level',$_POST))
			$_POST['set-wp-user-level'] = 'administrator';
		
		
		$enable_tinymce = sanitize_text_field($_POST['enable-tinymce']);
		$enable_widget = sanitize_text_field($_POST['enable-widget']);
		$enable_color_adapt = sanitize_text_field($_POST['enable-color-adapt']);
		$set_wp_user_level = sanitize_text_field($_POST['set-wp-user-level']);
		
		update_option('nex-forms-other-config',array
			(
			'enable-tinymce' 			=> filter_var($enable_tinymce,FILTER_SANITIZE_NUMBER_INT),
			'enable-widget' 			=> filter_var($enable_widget,FILTER_SANITIZE_NUMBER_INT),
			'enable-color-adapt' 		=> filter_var($enable_color_adapt,FILTER_SANITIZE_NUMBER_INT),
			'set-wp-user-level' 		=> filter_var($set_wp_user_level,FILTER_SANITIZE_STRING)
			)
		);
		die();
	}
	
	function deactivate_license(){

		$api_params = array( 'client_deactivate_license' => 1,'key'=>get_option('7103891'));
		$response = wp_remote_post( 'http://basixonline.net/activate-license', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
		
	}

	
	
	public function do_form_import() {
		
		global $wpdb;
		
		$nf_functions = new NEXForms_Functions();
		
		
		
		foreach($_FILES as $key=>$file)
			{
			$uploadedfile = $_FILES[$key];
			$upload_overrides = array( 'test_form' => false );
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			//
			if ( $movefile ) {
				
					if($movefile['file'])
						{
						$set_file_name = str_replace(ABSPATH,'',$movefile['file']);
						$_POST['image_path'] = $movefile['url'];
						$_POST['image_name'] = $file['name'];
						$_POST['image_size'] = $file['size'];

						$url = $movefile['url'];
						
						$curl = curl_init();
						curl_setopt($curl, CURLOPT_URL, $url);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($curl, CURLOPT_HEADER, false);
						
						$data = curl_exec($curl);
						
						$file_content = file_get_contents($url);
						
						if(!$file_content)
							$file_content = $data;
						
						//$file_content = mysql_real_escape_string($file_content);
						
						//$file_content_utf8 = mb_convert_encoding($file_content, 'UTF-8',mb_detect_encoding($file_content, 'UTF-8, ISO-8859-1', true));
						
						//echo $file_content;
						//$import_form  = 'INSERT INTO `'.$wpdb->prefix.'wap_nex_forms`';
						//$import_form .= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $file_content);
						
						
						//echo $import_form;
						//$do_form_import = $wpdb->prepare($import_form);
						//$wpdb->query($import_form);
						//echo $wpdb->insert_id;
						//echo file_get_contents($url);
						
						$get_form_data = json_decode($file_content,true);
						
						//print_r($get_form_data);
						
						$import_record = $wpdb->insert($wpdb->prefix.'wap_nex_forms',$get_form_data);
						echo $wpdb->insert_id;	
						
						curl_close($curl);
						}
				} 
			}
		die();
	}
	
	
	public function create_custom_layout(){
		
		$fields_col = sanitize_text_field($_POST['set_layout']['fields_col']);
		$form_canvas_col = sanitize_text_field($_POST['set_layout']['form_canvas_col']);
		$field_settings_col = sanitize_text_field($_POST['set_layout']['field_settings_col']);
		
		$old_layout_name = sanitize_text_field($_POST['set_layout']['old_layout_name']);
		
		$new_layout_name = sanitize_text_field($_POST['set_layout']['layout_name']);
		
		
		$set_custom_layout = array
			(
			'fields_col' 			=> $fields_col,
			'form_canvas_col'		=> $form_canvas_col,
			'field_settings_col' 	=> $field_settings_col
			);
		
		
		
		$custom_layout = get_option('nex-forms-custom-layouts'); 
		
		unset($custom_layout[$old_layout_name]);
		
		$custom_layout[$new_layout_name] = $set_custom_layout;	
		
		update_option('nex-forms-custom-layouts',$custom_layout);
		echo $custom_layout[$old_layout_name];
		die();	
	}
	public function delete_custom_layout(){
		$custom_layout = get_option('nex-forms-custom-layouts'); 
		
		$layout_name = sanitize_text_field($_POST['layout_name']);
		
		unset($custom_layout[layout_name]);
		update_option('nex-forms-custom-layouts',$custom_layout);
	}
	public function load_custom_layout(){
		$custom_layout = get_option('nex-forms-custom-layouts'); 
		
		$output .= '<div class="fields_col">';
			$output .= '<div class="top">'.$custom_layout[$_POST['set_layout']]['fields_col']['top'].'</div>';
			$output .= '<div class="left">'.$custom_layout[$_POST['set_layout']]['fields_col']['left'].'</div>';
			$output .= '<div class="width">'.$custom_layout[$_POST['set_layout']]['fields_col']['width'].'</div>';
			$output .= '<div class="height">'.$custom_layout[$_POST['set_layout']]['fields_col']['height'].'</div>';
		$output .= '</div>';
		
		$output .= '<div class="form_canvas_col">';
			$output .= '<div class="top">'.$custom_layout[$_POST['set_layout']]['form_canvas_col']['top'].'</div>';
			$output .= '<div class="left">'.$custom_layout[$_POST['set_layout']]['form_canvas_col']['left'].'</div>';
			$output .= '<div class="width">'.$custom_layout[$_POST['set_layout']]['form_canvas_col']['width'].'</div>';
			$output .= '<div class="height">'.$custom_layout[$_POST['set_layout']]['form_canvas_col']['height'].'</div>';
		$output .= '</div>';
		
		$output .= '<div class="field_settings_col">';
			$output .= '<div class="top">'.$custom_layout[$_POST['set_layout']]['field_settings_col']['top'].'</div>';
			$output .= '<div class="left">'.$custom_layout[$_POST['set_layout']]['field_settings_col']['left'].'</div>';
			$output .= '<div class="width">'.$custom_layout[$_POST['set_layout']]['field_settings_col']['width'].'</div>';
			$output .= '<div class="height">'.$custom_layout[$_POST['set_layout']]['field_settings_col']['height'].'</div>';
		$output .= '</div>';
		
		echo $output;
		die();
	}
		
	
	public function nf_send_test_email(){
			
			
			$email_config = get_option('nex-forms-email-config');
			
			$email_address = sanitize_email($_POST['email_address']);
			
			$from_address 	= filter_var($email_address,FILTER_SANITIZE_EMAIL);
			$from_name 		= 'You';
			$subject 		= 'NEX-Forms Test Mail';
			$plain_body		= 'This is a test message in PLAIN TEXT. If you received this your email settings are working correctly :)
			
You are using '.$email_config['email_method'].' as your emailing method';
			$html_body		= 'This is a test message in <strong>HTML</strong>. If you received this your email settings are working correctly :)<br /><br />You are using <strong>'.$email_config['email_method'].'</strong> as your emailing method';
			
			if($email_config['email_method']=='api')
				{
					$api_params = array( 
						'from_address' => $from_address,
						'from_name' => $from_name,
						'subject' => $subject,
						'mail_to' => $from_address,
						'admin_message' => ($email_config['email_content']=='pt') ? $plain_body : $html_body,
						'user_email' => 0,
						'is_html'=> ($email_config['email_content']=='pt') ? 0 : 1
					);
					$response = wp_remote_post( 'http://basixonline.net/mail-api/', array('timeout'   => 30,'sslverify' => false,'body'  => $api_params) );
					//echo $response['body'];
				}
			
			else if($email_config['email_method']=='smtp' || $email_config['email_method']=='php_mailer')
				{
				date_default_timezone_set('Etc/UTC');
				include_once(ABSPATH . WPINC . '/class-phpmailer.php'); 
				//Create a new PHPMailer instance
				$mail = new PHPMailer;
			
				$mail->CharSet = "UTF-8";
				
				if($email_config['email_content']=='pt')
					$mail->IsHTML(false);
				 
				//Tell PHPMailer to use SMTP
				if($email_config['email_method']=='smtp')
					{
					$mail->isSMTP();
					
					if($email_config['email_smtp_secure']!='0')
						$mail->SMTPSecure  = $email_config['email_smtp_secure']; //Secure conection
					
					if($email_config['smtp_auth']=='1')
						{
						$mail->SMTPAuth = true;
						//Username to use for SMTP authentication
						$mail->Username = $email_config['set_smtp_user'];
						//Password to use for SMTP authentication
						$mail->Password = $email_config['set_smtp_pass'];
						}
					else
						{
						$mail->SMTPAuth = false;
						}
					
					
					
					
					//encoding
					
					//Whether to use SMTP authentication
					
					}
				//}
				//Set who the message is to be sent from
				//Set an alternative reply-to address
			//Set the hostname of the mail server
					$mail->Host = $email_config['smtp_host'];
					//Set the SMTP port number - likely to be 25, 465 or 587
					$mail->Port = ($email_config['email_port']) ? $email_config['email_port'] : 587;
					
				$mail->setFrom($from_address, $from_name);
				$mail->addCC($from_address, $from_name);
				//Set the subject line
				$mail->Subject = $subject;
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				if($email_config['email_content']=='html')	
					$mail->msgHTML($html_body);
				else
					$mail->Body = $plain_body;
				if (!$mail->send()) {
				    echo "Mailer Error: " . $mail->ErrorInfo;
				} else {
				   echo "Message sent!";
					//echo print_r($mail);
				}
			}
		
/**************************************************/
/** NORMAL PHP ************************************/
/**************************************************/
	else if($email_config['email_method']=='php')
		{

		$headers .= 'Content-Type: '.(($email_config['email_content']=='html') ? 'text/html' : 'text/plain').'; charset=UTF-8\n\n'. "\r\n";
		$headers .= 'From: '.$from_name.' <'.$from_address.'>' . "\r\n";
		
		if($email_config['email_content']=='html')	
			$set_body = $html_body;
		else
			$set_body = $plain_body;
		
		$email_address = sanitize_email($_POST['email_address']);
		
		mail(filter_var($email_address,FILTER_SANITIZE_EMAIL),$subject,$set_body,$headers);
		}

/**************************************************/
/** WORDPRESS MAIL ********************************/
/**************************************************/	
	else if($email_config['email_method']=='wp_mailer')
		{

		$headers .= 'Content-Type: '.(($email_config['email_content']=='html') ? 'text/html' : 'text/plain').'; charset=UTF-8'. "\r\n";
		$headers .= 'From: '.$from_name.' <'.$from_address.'>' . "\r\n";
		
		if($email_config['email_content']=='html')	
			$set_body = $html_body;
		else
			$set_body = $plain_body;
		$email_address = sanitize_email($_POST['email_address']);
		wp_mail(filter_var($email_address,FILTER_SANITIZE_EMAIL),$subject,$set_body,$headers);				
		}
					
	die();
	}
	
	}
}



class NEXForms_widget extends WP_Widget{
	public $name = 'NEX-Forms';
	public $widget_desc = 'Add NEX-Forms to your sidebars.';
	
	public $control_options = array('title' => '','form_id' => '', 'make_sticky'=>'no', 'paddel_text'=>'Contact Us', 'paddel_color'=>'btn-primary', 'position'=>'right', 'open_trigger'=>'normal','type'=>'button' , 'text'=>'Open Form', 'button_color'=>'btn-primary');
	function __construct(){
		$widget_options = array('classname' => __CLASS__,'description' => $this->widget_desc);
		parent::__construct( __CLASS__, $this->name,$widget_options , $this->control_options);
	}
	function widget($args, $instance){
		echo '<div class="widget">';
		NEXForms_ui_output(
			array(
				'id'=>$instance['form_id'],
				'make_sticky'=>$instance['make_sticky'],
				'paddel_text'=>$instance['paddel_text'],
				'paddel_color'=>$instance['paddel_color'],
				'position'=>$instance['position'],
				'open_trigger'=>$instance['open_trigger'],
				'type'=>$instance['type'],
				'text'=>$instance['text'],
				'button_color'=>$instance['button_color']
				
				),true,'');
		echo '</div>';
	}
	public function form( $instance ){
		$placeholders = array();
		foreach ( $this->control_options as $key => $val )
			{
			$placeholders[ $key .'.id' ] = $this->get_field_id( $key);
			$placeholders[ $key .'.name' ] = $this->get_field_name($key );
			if ( isset($instance[ $key ] ) )
				$placeholders[ $key .'.value' ] = esc_attr( $instance[$key] );
			else
				$placeholders[ $key .'.value' ] = $this->control_options[ $key ];
			}
		global $wpdb;
		$get_forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_form=1 ORDER BY Id DESC');
		$current_form = NEXForms_widget_controls::parse('[+form_id.value+]', $placeholders);
		
		$tpl  = '<input id="[+title.id+]" name="[+title.name+]" value="'.NEXForms_Database_Actions::get_title(NEXForms_widget_controls::parse('[+form_id.value+]', $placeholders),'wap_nex_forms').'" class="widefat" style="width:96%;display:none;" />';
		
		if($get_forms)
			{
			$tpl  .= '<h3>Select Form</h3>';
			$tpl .= '<select id="[+form_id.id+]" name="[+form_id.name+] " style="width:100%;">';
				$tpl .= '<option value="0">-- Select form --</option>';
				foreach($get_forms as $form)
					$tpl .= '<option value="'.$form->Id.'" '.(($form->Id==$current_form) ? 'selected="selected"' : '' ).'>'.$form->title.'</option>';
			$tpl .= '</select></p>';
			}
		else
			$tpl .=  '<p>No forms have been created yet.<br /><br /><a href="'.get_option('siteurl').'/wp-admin/admin.php?page=WA-x_forms-main">Click here</a> or click on "X Forms" on the left-hand menu where you will be able to create a form that would be avialable here to select as a widget.</p>';
		
		
		$tpl  .= '<hr />';
		$tpl  .= '<h3>Sticky Mode Options</h3>';
		$tpl  .= '<p><label for="[+make_sticky.id+]"><strong>Make Sticky?</strong></label><br /><small><em>Choose <strong>no</strong> to display in sidebar.<br /> Choose <strong>yes</strong> to display form in sticky mode and select prefered settings.</em></small><br /><input id="1[+make_sticky.id+]" name="[+make_sticky.name+]" value="no" '.((NEXForms_widget_controls::parse('[+make_sticky.value+]', $placeholders))=='no' ? 'checked="checked"' : '').' type="radio" class="widefat"  /> <label for="1[+make_sticky.id+]">No</label><br /><input id="2[+make_sticky.id+]" name="[+make_sticky.name+]" value="yes" '.((NEXForms_widget_controls::parse('[+make_sticky.value+]', $placeholders))=='yes' ? 'checked="checked"' : '').' type="radio" class="widefat"  /> <label for="2[+make_sticky.id+]">Yes</label></p>';
		
		$tpl  .= '<p><label for="[+paddel_text.id+]"><strong>Paddel Text </strong></label><input type="text" id="[+paddel_text.id+]" name="[+paddel_text.name+]" value="'.NEXForms_widget_controls::parse('[+paddel_text.value+]', $placeholders).'" class="widefat" /><p>';
		
		$tpl  .= '<p><label for="[+paddel_color.id+]"><strong>Paddel Color</strong></label><br />';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #428bca; border-radius:4px; border:1px solid #357ebd; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+paddel_color.id+]" name="[+paddel_color.name+]" '.((NEXForms_widget_controls::parse('[+paddel_color.value+]', $placeholders))=='btn-primary' ? 'checked="checked"' : '').' value="btn-primary"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #5bc0de; border-radius:4px; border:1px solid #46b8da; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+paddel_color.id+]" name="[+paddel_color.name+]" '.((NEXForms_widget_controls::parse('[+paddel_color.value+]', $placeholders))=='btn-info' ? 'checked="checked"' : '').' value="btn-info"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #5cb85c; border-radius:4px; border:1px solid #4cae4c; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+paddel_color.id+]" name="[+paddel_color.name+]" '.((NEXForms_widget_controls::parse('[+paddel_color.value+]', $placeholders))=='btn-success' ? 'checked="checked"' : '').' value="btn-success"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #f0ad4e; border-radius:4px; border:1px solid #eea236; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+paddel_color.id+]" name="[+paddel_color.name+]" '.((NEXForms_widget_controls::parse('[+paddel_color.value+]', $placeholders))=='btn-warning' ? 'checked="checked"' : '').' value="btn-warning"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #d9534f; border-radius:4px; border:1px solid #d43f3a; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+paddel_color.id+]" name="[+paddel_color.name+]" '.((NEXForms_widget_controls::parse('[+paddel_color.value+]', $placeholders))=='btn-danger' ? 'checked="checked"' : '').' value="btn-danger"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #ffffff; border-radius:4px; border:1px solid #cccccc; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+paddel_color.id+]" name="[+paddel_color.name+]" '.((NEXForms_widget_controls::parse('[+paddel_color.value+]', $placeholders))=='btn-default' ? 'checked="checked"' : '').' value="btn-default"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp</p>';

		$tpl  .= '<p><label for="[+position.id+]"><strong>Position</strong></label><br />';
		$tpl  .= '<input id="1[+position.id+]" name="[+position.name+]" '.((NEXForms_widget_controls::parse('[+position.value+]', $placeholders))=='top' ? 'checked="checked"' : '').' value="top"  type="radio" class="widefat"  /> <label for="1[+position.id+]">Top</label><br />';
		$tpl  .= '<input id="2[+position.id+]" name="[+position.name+]" '.((NEXForms_widget_controls::parse('[+position.value+]', $placeholders))=='right' ? 'checked="checked"' : '').' value="right"  type="radio" class="widefat"  /> <label for="2[+position.id+]">Right</label><br />';
		$tpl  .= '<input id="3[+position.id+]" name="[+position.name+]" '.((NEXForms_widget_controls::parse('[+position.value+]', $placeholders))=='bottom' ? 'checked="checked"' : '').' value="bottom"  type="radio" class="widefat"  /> <label for="3[+position.id+]">Bottom</label><br />';
		$tpl  .= '<input id="4[+position.id+]" name="[+position.name+]" '.((NEXForms_widget_controls::parse('[+position.value+]', $placeholders))=='left' ? 'checked="checked"' : '').' value="left"  type="radio" class="widefat"  /> <label for="4[+position.id+]">Left</label></p>';
		
		
		
		$tpl  .= '<hr />';
		$tpl  .= '<h3>Popup Form Options</h3>';
		$tpl  .= '<p><label for="[+open_trigger.id+]"><strong>Popup Form?</strong></label><br /><input id="1[+open_trigger.id+]" name="[+open_trigger.name+]" value="normal" '.((NEXForms_widget_controls::parse('[+open_trigger.value+]', $placeholders))=='normal' ? 'checked="checked"' : '').' type="radio" class="widefat"  /> <label for="1[+open_trigger.id+]">No</label><br /><input id="2[+open_trigger.id+]" name="[+open_trigger.name+]" value="popup" '.((NEXForms_widget_controls::parse('[+open_trigger.value+]', $placeholders))=='popup' ? 'checked="checked"' : '').' type="radio" class="widefat"  /> <label for="2[+open_trigger.id+]">Yes</label></p>';
		
		$tpl  .= '<p><label for="[+type.id+]"><strong>Popover Trigge</strong>r</label><br /><input id="1[+type.id+]" name="[+type.name+]" value="button" '.((NEXForms_widget_controls::parse('[+type.value+]', $placeholders))=='button' ? 'checked="checked"' : '').' type="radio" class="widefat"  /> <label for="1[+type.id+]">Button</label><br /><input id="2[+type.id+]" name="[+type.name+]" value="link" '.((NEXForms_widget_controls::parse('[+type.value+]', $placeholders))=='link' ? 'checked="checked"' : '').' type="radio" class="widefat"  /> <label for="2[+type.id+]">Link</label></p>';
		
		$tpl  .= '<p><label for="[+button_color.id+]">Button Color</label><br />';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #428bca; border-radius:4px; border:1px solid #357ebd; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+button_color.id+]" name="[+button_color.name+]" '.((NEXForms_widget_controls::parse('[+button_color.value+]', $placeholders))=='btn-primary' ? 'checked="checked"' : '').' value="btn-primary"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #5bc0de; border-radius:4px; border:1px solid #46b8da; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+button_color.id+]" name="[+button_color.name+]" '.((NEXForms_widget_controls::parse('[+button_color.value+]', $placeholders))=='btn-info' ? 'checked="checked"' : '').' value="btn-info"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #5cb85c; border-radius:4px; border:1px solid #4cae4c; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+button_color.id+]" name="[+button_color.name+]" '.((NEXForms_widget_controls::parse('[+button_color.value+]', $placeholders))=='btn-success' ? 'checked="checked"' : '').' value="btn-success"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #f0ad4e; border-radius:4px; border:1px solid #eea236; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+button_color.id+]" name="[+button_color.name+]" '.((NEXForms_widget_controls::parse('[+button_color.value+]', $placeholders))=='btn-warning' ? 'checked="checked"' : '').' value="btn-warning"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #d9534f; border-radius:4px; border:1px solid #d43f3a; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+button_color.id+]" name="[+button_color.name+]" '.((NEXForms_widget_controls::parse('[+button_color.value+]', $placeholders))=='btn-danger' ? 'checked="checked"' : '').' value="btn-danger"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp';
		$tpl  .= '<label style="margin-right: 5px;background: none repeat scroll 0 0 #ffffff; border-radius:4px; border:1px solid #cccccc; display: block;float: left; height: 23px; width: 30px;">&nbsp;&nbsp;<input id="[+button_color.id+]" name="[+button_color.name+]" '.((NEXForms_widget_controls::parse('[+button_color.value+]', $placeholders))=='btn-default' ? 'checked="checked"' : '').' value="btn-default"  type="radio" class="widefat"  />&nbsp;&nbsp;</label>&nbsp;&nbsp</p>';
		
		$tpl  .= '<p><label for="[+text.id+]"><strong>Button/link Text </strong></label><input type="text" id="[+text.id+]" name="[+text.name+]" value="'.NEXForms_widget_controls::parse('[+text.value+]', $placeholders).'" class="widefat" /><p>';

		print NEXForms_widget_controls::parse($tpl, $placeholders);
	}
	static function register_this_widget(){
		register_widget(__CLASS__);
	}
}
   
class NEXForms_widget_controls {
	static function parse($tpl, $hash){
   	   foreach ($hash as $key => $value)
			$tpl = str_replace('[+'.$key.'+]', $value, $tpl);
	   return $tpl;
	}
}


?>