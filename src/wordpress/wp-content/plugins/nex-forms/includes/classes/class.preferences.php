<?php
//PREFERENCES
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'wp_ajax_save_field_pref', array('NF5_Preferences','save_field_pref'));
add_action( 'wp_ajax_save_validation_pref', array('NF5_Preferences','save_validation_pref'));
add_action( 'wp_ajax_save_email_pref', array('NF5_Preferences','save_email_pref'));
add_action( 'wp_ajax_save_other_pref', array('NF5_Preferences','save_other_pref'));
if(!class_exists('NF5_Preferences'))
	{
	class NF5_Preferences
		{
		//PREFERENCES	
		public function save_field_pref() {
			$preferences = get_option('nex-forms-preferences'); 
			$preferences['field_preferences'] = $_POST;
			update_option('nex-forms-preferences',$preferences);
			die();
		}
		public function save_validation_pref() {
			$preferences = get_option('nex-forms-preferences'); 
			$preferences['validation_preferences'] = $_POST;
			update_option('nex-forms-preferences',$preferences);
			die();
		}
		public function save_email_pref() {
			$preferences = get_option('nex-forms-preferences'); 
			$preferences['email_preferences'] = $_POST;
			update_option('nex-forms-preferences',$preferences);
			die();
		}
		public function save_other_pref() {
			$preferences = get_option('nex-forms-preferences'); 
			$preferences['other_preferences'] = $_POST;
			update_option('nex-forms-preferences',$preferences);
			die();
		}
		public function get_preferences()
			{
			//PREFERENCES
			$preferences = get_option('nex-forms-preferences');
			$output = '';
			$output .= '<div class="modal fade in admin-modal pref" id="preferences" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="z-index:1000000000 !important;">
							<div class="modal-dialog preview-modal">
								
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="myModalLabel">Preferences (defaults)</h4>
									  </div>
									<div class="modal-body">
										
										<div role="tabpanel">

											  <!-- Nav tabs -->
											  <ul class="nav nav-tabs" role="tablist">
												<li role="presentation" class="active"><a href="#field-preferences" role="tab" data-toggle="tab">Fields</a></li>
												<li role="presentation"><a href="#validation-preferences" role="tab" data-toggle="tab">Validation</a></li>
												<li role="presentation"><a href="#email-preferences" role="tab" data-toggle="tab">Emails</a></li>
												<li role="presentation"><a href="#other-preferences" role="tab" data-toggle="tab">Other</a></li>
											  </ul>
											
											  <!-- Tab panes -->
											  <div class="tab-content panel">
												
												<div role="tabpanel" class="tab-pane active" id="field-preferences">
													<form name="field-pref" id="field-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
														<div class="alert alert-success" style="display:none;">Field Preferences Saved <div class="close fa fa-close"></div></div>
															
															<h5>Field Labels</h5>
															
															
															<div class="row">
																<div class="col-sm-4">Label Position</div>
																<div class="col-sm-8">
																	<label for="pref_label_align_top">
																		<input type="radio" name="pref_label_align" '.((!$preferences['field_preferences']['pref_label_align'] || $preferences['field_preferences']['pref_label_align']=='top') ? 'checked="checked"' : '').' id="pref_label_align_top" value="top"> Top
																	</label>
																	<label for="pref_label_align_left">
																		<input type="radio" name="pref_label_align" id="pref_label_align_left" value="left" '.(($preferences['field_preferences']['pref_label_align']=='left') ? 'checked="checked"' : '').'> Left
																	</label>
																	<label for="pref_label_align_right">
																		<input type="radio" name="pref_label_align" id="pref_label_align_right" value="right" '.(($preferences['field_preferences']['pref_label_align']=='right') ? 'checked="checked"' : '').'> Right
																	</label>
																	<label for="pref_label_align_hidden">
																		<input type="radio" name="pref_label_align" id="pref_label_align_hidden" value="hidden" '.(($preferences['field_preferences']['pref_label_align']=='hidden') ? 'checked="checked"' : '').'> Hidden
																	</label>
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Label Text Alignment</div>
																<div class="col-sm-8">
																	<label for="pref_label_text_align_left">
																		<input type="radio" name="pref_label_text_align" id="pref_label_text_align_left" value="align_left" '.((!$preferences['field_preferences']['pref_label_text_align'] || $preferences['field_preferences']['pref_label_text_align']=='align_left') ? 'checked="checked"' : '').'> Left
																	</label>
																	<label for="pref_label_text_align_right">
																		<input type="radio" name="pref_label_text_align" id="pref_label_text_align_right" value="align_right" '.(($preferences['field_preferences']['pref_label_text_align']=='align_right') ? 'checked="checked"' : '').'> Right
																	</label>
																	<label for="pref_label_text_align_center">
																		<input type="radio" name="pref_label_text_align" id="pref_label_text_align_center" value="align_center" '.(($preferences['field_preferences']['pref_label_text_align']=='align_center') ? 'checked="checked"' : '').'> Center
																	</label>
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Label Size</div>
																<div class="col-sm-8">
																	<label for="pref_label_size_sm">
																		<input type="radio" name="pref_label_size" id="pref_label_size_sm" value="text-sm" '.(($preferences['field_preferences']['pref_label_size']=='text-sm') ? 'checked="checked"' : '').'> Small
																	</label>
																	<label for="pref_label_size_normal">
																		<input type="radio" name="pref_label_size" id="pref_label_size_normal" value="" '.((!$preferences['field_preferences']['pref_label_size'] || $preferences['field_preferences']['pref_label_size']=='') ? 'checked="checked"' : '').'> Normal
																	</label>
																	<label for="pref_label_size_lg">
																		<input type="radio" name="pref_label_size"  id="pref_label_size_lg" value="text-lg" '.(($preferences['field_preferences']['pref_label_size']=='text-lg') ? 'checked="checked"' : '').'> Large
																	</label>
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Show Sublabel</div>
																<div class="col-sm-8">
																	<label for="pref_sub_label_yes">
																		<input type="radio" name="pref_sub_label" id="pref_sub_label_yes" value="Sub Label" '.(($preferences['field_preferences']['pref_sub_label']=='Sub Label') ? 'checked="checked"' : '').'> Yes
																	</label>
																	<label for="pref_sub_label_no">
																		<input type="radio" name="pref_sub_label"  id="pref_sub_label_no" value="" '.((!$preferences['field_preferences']['pref_sub_label'] || $preferences['field_preferences']['pref_sub_label']=='') ? 'checked="checked"' : '').'> No
																	</label>
																</div>
															</div>
															
															
															
															<h5>Field Inputs</h5>

															<div class="row">
																<div class="col-sm-4">Input Text Alignment</div>
																<div class="col-sm-8">
																	<label for="pref_input_text_align_left">
																		<input type="radio" name="pref_input_text_align" id="pref_input_text_align_left" value="align_left" '.((!$preferences['field_preferences']['pref_input_text_align'] || $preferences['field_preferences']['pref_input_text_align']=='align_left') ? 'checked="checked"' : '').'> Left
																	</label>
																	<label for="pref_input_text_align_right">
																		<input type="radio" name="pref_input_text_align" id="pref_input_text_align_right" value="align_right" '.(($preferences['field_preferences']['pref_input_text_align']=='align_right') ? 'checked="checked"' : '').'> Right
																	</label>
																	<label for="pref_input_text_align_center">
																		<input type="radio" name="pref_input_text_align" id="pref_input_text_align_center" value="align_center" '.(($preferences['field_preferences']['pref_input_text_align']=='align_center') ? 'checked="checked"' : '').'> Center
																	</label>
																</div>
															</div>
															
															<div class="row">
																<div class="col-sm-4">Input Size</div>
																<div class="col-sm-8">
																	<label for="pref_input_size_sm">
																		<input type="radio" name="pref_input_size" id="pref_input_size_sm" value="input-sm" '.(($preferences['field_preferences']['pref_input_size']=='input-sm') ? 'checked="checked"' : '').'> Small
																	</label>
																	<label for="pref_input_size_normal">
																		<input type="radio" name="pref_input_size" id="pref_input_size_normal" value="" '.((!$preferences['field_preferences']['pref_input_size'] || $preferences['field_preferences']['pref_input_size']=='') ? 'checked="checked"' : '').'> Normal
																	</label>
																	<label for="pref_input_size_lg">
																		<input type="radio" name="pref_input_size"  id="pref_input_size_lg" value="input-lg" '.(($preferences['field_preferences']['pref_input_size']=='input-lg') ? 'checked="checked"' : '').'> Large
																	</label>
																</div>
															</div>
															
															
														
														<div class="modal-footer">
															<button class="btn btn-default">&nbsp;&nbsp;&nbsp;Save Field Preferences&nbsp;&nbsp;&nbsp;</button>
														</div>
															
													</form>
												
												</div>
												
												
												<div role="tabpanel" class="tab-pane" id="validation-preferences">
													<form name="validation-pref" id="validation-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
															
															<div class="alert alert-success" style="display:none;">Validation Preferences Saved <div class="close fa fa-close"></div></div>
														

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
															
															<div class="modal-footer">
																<button class="btn btn-default">&nbsp;&nbsp;&nbsp;Save Validation Preferences&nbsp;&nbsp;&nbsp;</button>
															</div>
															
													</form>
												
												</div>
												
												<div role="tabpanel" class="tab-pane" id="email-preferences">
													<form name="emails-pref" id="emails-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
														<div class="alert alert-success" style="display:none;">Email Preferences Saved <div class="close fa fa-close"></div></div>
															
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
															
															
															
														
														<div class="modal-footer">
															<button class="btn btn-default">&nbsp;&nbsp;&nbsp;Save Email Preferences&nbsp;&nbsp;&nbsp;</button>
														</div>
															
													</form>
												
												</div>
												
												
												<div role="tabpanel" class="tab-pane" id="other-preferences">
													<form name="other-pref" id="other-pref" action="'.admin_url('admin-ajax.php').'" method="post">	
														<div class="alert alert-success" style="display:none;">Other Preferences Saved <div class="close fa fa-close"></div></div>
															
															
															
															<div class="row">
																<div class="col-sm-4">On-screen confirmation message</div>
																<div class="col-sm-8">
																	<textarea name="pref_other_on_screen_message" class="materialize-textarea">'.(($preferences['other_preferences']['pref_other_on_screen_message']) ? $preferences['other_preferences']['pref_other_on_screen_message'] : 'Thank you for connecting with us. We will respond to you shortly.').'</textarea>
																</div>
															</div>
															
															
															
														
														<div class="modal-footer">
															<button class="btn btn-default">&nbsp;&nbsp;&nbsp;Save Other Preferences&nbsp;&nbsp;&nbsp;</button>
														</div>
															
													</form>
												
												</div>
												
												
												
											</div>
										</div>
									</div>
								</div>
							</div> 
						</div>  
						';
					return $output;
				
			}
		}
	}



?>