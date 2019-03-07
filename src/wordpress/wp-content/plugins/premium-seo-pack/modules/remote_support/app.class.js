/*
Document   :  404 Monitor
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
pspRemoteSupport = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loaded_page = 0;
    var token = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".psp-main");

			triggers();
		});
	})();
	
	function remote_register_and_login( that )
	{
		pspFreamwork.to_ajax_loader( "Loading...", jQuery("div.psp" ) );
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspRemoteSupportRequest',
			'sub_actions'	: 'remote_register_and_login',
			'params'		: that.serialize(),
			'debug_level'	: debug_level
		}, function(response) {
			
			if( response.status == 'valid' ){
				token = response.token;
				$("#psp-token").val(token);
				$("#psp-boxid-login").fadeOut(100);
				$("#psp-boxid-register").fadeOut(100);
				
				var box_info_message = $("#psp-boxid-logininfo .psp-message");
				box_info_message.removeClass("psp-info");
				box_info_message.addClass("psp-success");
				
				box_info_message.html('You have successfully login into <a href="http://support.aa-team.com"></a>. Now you can open a ticket for our AA-Team support team.');
				
				$("#psp-boxid-ticket").fadeIn(100);
			}else{
				var status_block = that.find(".psp-message");
				status_block.html( "<strong>" + ( response.error_code ) + ": </strong>" + response.msg );
				
				status_block.fadeIn('fast'); 
			}
			
			pspFreamwork.to_ajax_loader_close();
		}, 'json'); 
	}
	
	function remote_login( that )
	{
		pspFreamwork.to_ajax_loader( "Loading...", jQuery("div.psp" ) );
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspRemoteSupportRequest',
			'sub_actions'	: 'remote_login',
			'params'		: that.serialize(),
			'debug_level'	: debug_level
		}, function(response) {
			
			if( response.status == 'valid' ){
				token = response.token;
				$("#psp-token").val(token);
				$("#psp-boxid-login").fadeOut(100);
				$("#psp-boxid-register").fadeOut(100);
				
				var box_info_message = $("#psp-boxid-logininfo .psp-message");
				box_info_message.removeClass("psp-info");
				box_info_message.addClass("psp-success");
				
				box_info_message.html('You have successfully login into <a href="http://support.aa-team.com"></a>. Now you can open a ticket for our AA-Team support team.');
				
				$("#psp-boxid-ticket").fadeIn(100);
			}else{
				var status_block = that.find(".psp-message");
				status_block.html( "<strong>" + ( response.error_code ) + ": </strong>" + response.msg );
				
				status_block.fadeIn('fast'); 
			}
			
			pspFreamwork.to_ajax_loader_close();
		}, 'json'); 
	}
	
	function open_ticket( that )
	{
		pspFreamwork.to_ajax_loader( "Loading...", jQuery("div.psp" ) );
		
		$("#psp-wp_password").val( $("#psp-password").val() );
		$("#psp-access_key").val( $("#psp-key").val() );
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspRemoteSupportRequest',
			'sub_actions'	: 'open_ticket',
			'params'		: that.serialize(),
			'token'			: $("#psp-token").val(),
			'debug_level'	: debug_level
		}, function(response) {
			
			if( response.status == 'valid' ){
				that.find(".psp-message").html( "The ticket has been open. New ticket ID: <strong>" + response.new_ticket_id + "</strong>" );
				that.find(".psp-message").show();
			}
			 
			pspFreamwork.to_ajax_loader_close();
			
		}, 'json'); 
	}
	
	function access_details( that )
	{
		pspFreamwork.to_ajax_loader( "Loading...", jQuery("div.psp" ) );
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspRemoteSupportRequest',
			'sub_actions'	: 'access_details',
			'params'		: that.serialize(),
			'debug_level'	: debug_level
		}, function(response) {
			
			pspFreamwork.to_ajax_loader_close();
		}, 'json'); 
	}
	
	function checkAuth( token )
	{
		pspFreamwork.to_ajax_loader( "Loading...", jQuery("div.psp" ) ); 
		
		jQuery.post(ajaxurl, {
			'action' 		: 'pspRemoteSupportRequest',
			'sub_actions'	: 'check_auth',
			'params'		: {
				'token': token
			},
			'debug_level'	: debug_level
		}, function(response) {
			// if has a valid token
			if( response.status == 'valid' ){
				$("#psp-boxid-ticket").show();
				$("#psp-boxid-logininfo").hide();
			}
			
			// show the auth box
			else{
				$("#psp-boxid-ticket").hide();
				$("#psp-boxid-logininfo .psp-message").html( 'In order to contact the AA-Team support team you need to login into: support.aa-team.com' );
				$("#psp-boxid-login").show();
				$("#psp-boxid-register").show();
			}
			pspFreamwork.to_ajax_loader_close();
		}, 'json'); 
	}

	function triggers()
	{
		maincontainer.on('submit', '#psp-form-login', function(e){
			e.preventDefault();

			remote_login( $(this) );
		});
		
		maincontainer.on('submit', '#psp-form-register', function(e){
			e.preventDefault();

			remote_register_and_login( $(this) );
		});
		
		maincontainer.on('submit', '#psp_access_details', function(e){
			e.preventDefault();

			access_details( $(this) );
		});
		
		maincontainer.on('change', '#psp-create_wp_credential', function(e){
			e.preventDefault();

			var that = $(this);
			
			if( that.val() == 'yes' ){
				$(".psp-wp-credential").show();
			}else{
				$(".psp-wp-credential").hide();
			}
		});
		
		maincontainer.on('change', '#psp-allow_file_remote', function(e){
			e.preventDefault();

			var that = $(this);
			
			if( that.val() == 'yes' ){
				$(".psp-file-access-credential").show();
			}else{
				$(".psp-file-access-credential").hide();
			}
		});
		
		maincontainer.on('submit', '#psp_add_ticket', function(e){
			e.preventDefault();

			open_ticket( $(this) );
		});
	}

	// external usage
	return {
		'checkAuth': checkAuth,
		'token' : token
    }
})(jQuery);
