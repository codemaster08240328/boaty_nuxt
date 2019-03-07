<?php

/**

 * Plugin Name: Sailchecker UI

 * Plugin URI:

 * Description: 

 * Author: Ivan Acog

 * Version: 1.0.0

 * Author URI: http://ivanacog.wordpress.com

 */



define('PLUGPATH', dirname(__FILE__) . '/');



require_once 'core/core.php';

require_once 'admin/admin.php';

require_once 'admin/admin-menu.php';



add_action('wp_enqueue_scripts', 'sc_ui_styles');

function sc_ui_styles() {

	wp_register_style('sailchecker-ui-css', plugins_url('css/sc-ui-styles.css', __FILE__));

	wp_enqueue_style('sailchecker-ui-css');

    wp_enqueue_script('jquery');


	wp_register_script('sailchecker-ui-js', plugins_url('js/sc-ui-scripts.js', __FILE__));

	wp_enqueue_script('sailchecker-ui-js');

}



add_action('init', 'sc_ui_init');

function sc_ui_init() {

	/**

	 *

	 * To send message to the info@sailchecker and to the visitor

	 */

	if (isset($_POST) && $_SERVER['REQUEST_METHOD'] === 'POST') {

		// Email and message are required...

		if (!empty($_POST['email']) && !empty($_POST['message'])) {

			// ini_set('SMTP','localhost');

			// ini_set('smtp_port',25);



			$vs_mail['name'] 	= (isset($_POST['name'])) ? $_POST['name'] : "SailChecker.com Visitor";

			$vs_mail['email'] 	= $_POST['email'];

			$vs_mail['phone'] 	= (isset($_POST['phone'])) ? $_POST['phone'] : "None";;

			$vs_mail['message'] = 'Name:'.$vs_mail['name']. '<br /> Phone: ' . $vs_mail['phone'] . '<br /> Message: ' .$_POST['message'];



			$headers = 	'MIME-Version: 1.0' . "\r\n" .

						'Content-type: text/html; charset=UTF-8' . "\r\n" .

						'From: ' . $vs_mail['email'] . "\r\n" .

						'Reply-To: ' . $vs_mail['email'] . "\r\n" .

						'X-Mailer: PHP/' . phpversion();

/* FIX EMAIL ERROR */

				
			$sc_mail['email'] 	= 'info@sailchecker.com';

			$sc_mail['noreply']	= 'noreply@sailchecker.com'; 


			$success = mail($sc_mail['email'], get_option('sc-enquire-ui-enquiry-subject'), $vs_mail['message'], $headers);



			// Check if email was successfully sent     

			if ($success) {				
				 
				// Auto respond
				$headers = 'MIME-version: 1.0\n' .
						   'Content-type: text/html; charset= iso-8859-1\n' . 
						   'From: ' . $sc_mail['email'] . "\r\n" .
						   'X-Mailer: PHP/' . phpversion();     

/* EDITED BY KEN ALLOW TO SEND HTML */ 
				$new_message= '<html><head></head><body>';
				$new_message.= '<div style="float:left;min-height:1px;margin-left:120px;width:620px;margin-bottom:30px;"><a href="http://sailchecker.com/sandbox/" style="color:rgb(0,136,204);text-decoration:none" target="_blank"><img src="http://sailchecker.com/wp-content/uploads/2014/02/Perspective-sailchecker-logo-coloured.png" alt="SailChecker" style="min-height:auto;max-width:100%;vertical-align:middle;border:0px;width:200px!important;text-align:center!important"></a></div>';
				$new_message.= '<div style="float:left;min-height:1px;margin-left:120px;width:620px">';
				$new_message.= '<p>Dear '.$vs_mail['name'].',</p>';
				$new_message.= get_option('sc-enquire-ui-response-message');
				$new_message.= '</div></body></html>';	       
                $success = mail($vs_mail['email'], get_option('sc-enquire-ui-response-subject'), $new_message, $headers);  
			}                    



			if ($success) {

				$response['status'] 	= 'ok';

				$response['message']	= 'Your enquiry was sent successfully. Expect a response from us within 24 hours.';

			} else {

				$response['status'] 	= 'failed';

				$response['message']	= 'We have encountered an error sending your enquiry. Please try again later.';

			}

			echo json_encode($response);

			exit;

		}

	}

}



add_action('wp_footer', 'sc_ui_scripts');

function sc_ui_scripts() {

	echo '<script type="text/javascript" src="' . plugins_url('js/sc-ui-scripts.js', __FILE__) .'"></script>';

}



add_shortcode('sc_enquire_ui', 'sc_enquire_ui_shortcode');

function sc_enquire_ui_shortcode($atts, $content) {

	extract(shortcode_atts(array( 

    ), $atts));



	// Put the dialog and modal html to the footer

    add_action('wp_footer', 'insert_sc_inquire_ui_modal');



    return 

	    '<div class="sc-ui">

		    <div class="sc-inquiry-ui">

		    

		    	<div style="position:relative">

		    		<textarea id="inquiryInputMessageShortCut" placeholder="" ></textarea>
					<div class="typewriter"></div>
		    	</div>

		    	<div>

		    		<button class="sc-button" data-toggle="dialog" data-target="#sc-dialog-ui-inquire">Enquire</button>

		    	</div>

		    </div>

	    </div>';

}



function insert_sc_inquire_ui_modal() {

	echo 

		'<div class="sc-dialog-ui" id="sc-dialog-ui-inquire" data-target="#sc-dialog-ui-inquire">

			<div class="sc-dialog-ui-caption-bar">

				<div class="sc-dialog-ui-caption">

					Let\'s just check if we have everything we need! 

				</div>

				<div class="sc-dialog-ui-control-button">

					<button class="sc-dialog-ui-close" data-close="dialog">&times;</button>

				</div>

			</div>

			<div class="sc-dialog-ui-body">

				<div style="padding: 10px 15px; margin-bottom: 10px; line-height: 1.3em; background-color: #99CCFF; border: 1px solid #0099CC;">

					Having Trouble? Call Us.<br />

					<strong>US :</strong> +1-844-335-1306<br />

					<strong>UK :</strong> GBR:  +44 (0)8000 988 188<br /> 

					<strong>UK :</strong> GRE:  +30 (0)800 848 1290<br />

					<strong>UK :</strong> AUS:  +61 73-067-8907<br />

				</div>

				<div id="sc-dialog-ui-inquire-message">



				</div>

				<div>

					<div>

						<input type="text" placeholder="Full Name" id="inquiryInputName" />

					</div>

					<div>

						<input type="text" placeholder="email address *" id="inquiryInputEmail" />

					</div>

					<div>

						<input type="text" placeholder="Phonem Number" id="inquiryInputPhone" />

					</div>

					<div>

						<label for="inquiryInputMessage">Message</label>

						<div>

							<textarea id="inquiryInputMessage" placeholder="Enter your message here."></textarea>

						</div>

					</div>

				</div>

				<div>

					<button class="sc-button-send" onclick="scInquirySend(this);" id="inquiryButtonSend">Send</button>

				</div>

			</div>

		</div>';



	echo 

		'<script type="text/javascript">

			(function($) {

				$("#sc-dialog-ui-inquire").dialog({

					onopen: function() {

						$("#sc-dialog-ui-inquire-message").hide();



						var message = $("#inquiryInputMessageShortCut").val();

						$("#inquiryInputMessage").val(message);

					}

				});

			})(jQuery);

		</script>';

}



add_shortcode('sc_enquire_page_ui', 'sc_enquire_page_ui_shortcode');

function sc_enquire_page_ui_shortcode($atts, $content) {

	return 

		'<div>

		</div>';

}



add_shortcode('sc_aweber_ui', 'sc_aweber_ui_shortcode');

function sc_aweber_ui_shortcode($atts, $content) {

	return 

		'<div class="sc-ui">

			<div class="sc-aweber-ui">

				<div id="sc-aweber-ui-error">

				</div>

				<form method="post" onsubmit="verifyAweberSubscribeForm(this, event);" class="sc-aweber-ui-form" action="http://www.aweber.com/scripts/addlead.pl"  >

				    <div style="display: none;">

				        <input type="hidden" name="meta_web_form_id" value="1935179809" />

				        <input type="hidden" name="meta_split_id" value="" />

				        <input type="hidden" name="listname" value="sailchecker" />

				        <input type="hidden" name="redirect" value="http://www.aweber.com/thankyou-coi.htm?m=text" id="redirect_d3e7527816b18789c9b078400b5a55b8" />



				        <input type="hidden" name="meta_adtracking" value="Sailcheck_Webform_Footer" />

				        <input type="hidden" name="meta_message" value="1" />

				        <input type="hidden" name="meta_required" value="name,email" />



				        <input type="hidden" name="meta_tooltip" value="" />

				    </div>

				    <div class="sc-aweber-ui-controls-wrapper">

				        <div class="sc-aweber-ui-controls">

				            <div class="sc-aweber-ui-element">

				                <label for="sc-aweber-ui-name">Name: </label>

				                <div>

				                    <input id="sc-aweber-ui-name" type="text" name="name" class="text" value=""  tabindex="500" />

				                </div>

				            </div>

				            <div class="sc-aweber-ui-element">

				                <label for="sc-aweber-ui-email">Email: </label>

				                <div>

				                    <input class="text" id="sc-aweber-ui-email" type="text" name="email" value="" tabindex="501"  />

				                </div>

				            </div>

				            <div class="sc-aweber-ui-element">

				                <input name="submit" class="sc-button" type="submit" value="Submit" tabindex="502" />

				            </div>

				        </div>

				    </div>

				</form>

			</div>

		</div>';

}



add_shortcode('sc_subscribe_ui_pop', 'sc_subscribe_ui_pop_shortcode');

function sc_subscribe_ui_pop_shortcode($atts, $content) {  

	extract(shortcode_atts(array(

		'time' => 30000

    ), $atts));



	// Put the dialog and modal html to the footer

    add_action('wp_footer', 'insert_sc_subscribe_ui_modal');



    return

    	'<script type="text/javascript">

    		(function($) {

    			setTimeout(function() {

    				var diag = $("#sc-dialog-ui-subscribe").data("base.dialog");

    				if (diag) diag.open();

    			}, ' . $time . ');

    		})(jQuery);

    	</script>';

}



function insert_sc_subscribe_ui_modal() {

	echo

		'<div class="sc-dialog-ui" id="sc-dialog-ui-subscribe" data-target="#sc-dialog-ui-subscribe">

			<div class="sc-dialog-ui-caption-bar">

				<div class="sc-dialog-ui-caption">

					Subscribe...

				</div>

				<div class="sc-dialog-ui-control-button">

					<button class="sc-dialog-ui-close" data-close="dialog">&times;</button>

				</div>

			</div>

			<div class="sc-dialog-ui-body">' .

				do_shortcode('[sc_aweber_ui]') .

			'</div>

		</div>';



	echo 

		'<script type="text/javascript">

			(function($) {

				$("#sc-dialog-ui-subscribe").dialog();

			})(jQuery);

		</script>';

}





add_shortcode('boatbooker_small', 'boatbooker_widget_shortcode');

function boatbooker_widget_shortcode($atts, $content) {

	extract(shortcode_atts(array(

    ), $atts));



	return

		'<script type="text/javascript" src="//app-static.boatbooker.net/JS/widget.js"></script>

		<script type="text/javascript">

			BoatBooker.createWidget({

				widget: 	"smallsearchbox",

				width: 		400,

				height:		400,

				urlparams: 	{

					sespid:	"cd3a6941-babd-4876-9070-1cd57ebc62a8",

					lang:	"en"

				}

			});

		</script>';

}