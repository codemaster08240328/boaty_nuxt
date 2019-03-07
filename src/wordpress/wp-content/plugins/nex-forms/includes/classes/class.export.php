<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('NF5_Export_Forms'))
	{
	class NF5_Export_Forms
		{
		/**
		* Constructor
		*/
		public function __construct(){
			
			$functions = new NEXForms_Functions();
			$export_form = isset($_REQUEST['export_form']) ? $_REQUEST['export_form'] : '';
			
			$db_actions = new NEXForms_Database_Actions();
			if($export_form)
				{
				$form_export = $this->generate_form();
				
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private", false);
				header("content-type:application/csv;charset=UTF-8");
				header("Content-Disposition: attachment; filename=\"".$functions->format_column_name($db_actions->get_title2(filter_var($_REQUEST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT),'wap_nex_forms')).".txt\";" );
				header("Content-Transfer-Encoding: base64");
				//echo "\xEF\xBB\xBF";
				echo $form_export;
				exit;
				}
			
		}	
		/**
		* Converting data to HTML
		*/
		public function generate_form(){
			global $wpdb;
			
				$form_data = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'wap_nex_forms WHERE Id = '.filter_var($_REQUEST['nex_forms_Id'],FILTER_SANITIZE_NUMBER_INT).'');
				//$content = str_replace('\\','',$form_data->form_fields);
				$content = '';
				$fields 	= $wpdb->get_results("SHOW FIELDS FROM " . $wpdb->prefix ."wap_nex_forms");
				$field_array = array();
				$count_fields = count($fields);
				$i = 0;
				
				$insert_array = array();
				$content .= '(';
				foreach($fields as $field)
					{
					if($field->Field!='date_sent')
						{
						$content .= '`'.$field->Field.'`'.(($i<$count_fields-2) ? ',' : '').'';
						 $my_fields[$field->Field]=$field->Field;
						 $i++;
						}
					}
				$content .= ') VALUES (';
				
				$j = 0;
				
				
				foreach($my_fields as $key=>$value)
					{
					$insert_array['Id'] =  'NULL';
					if($key!='date_sent' || $key!='Id')
						{
						/*if($my_field=='Id')
							$content .= 'NULL,';
						else
							$content .= '\''.str_replace('\\','',str_replace('\'','',$form_data->$my_field)).'\''.(($j<$count_fields-2) ? ',' : '').'';
						*/	
						
						$set_value = str_replace('\\','',$form_data->$value);
						$set_value = str_replace('\'','',$set_value);
						
						$insert_array[$key] =  $set_value; 
						
						
						$j++;
						}
					}
				
				
				$content .= ')';
				
				return json_encode($insert_array);
			}
		}
	}
$formExport = new NF5_Export_Forms();