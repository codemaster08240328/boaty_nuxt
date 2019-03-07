var subscribeunlock_suffix = "";
var subscribeunlock_soft_mode = "";
var subscribeunlock_busy = false;
function subscribeunlock_submit(suffix, soft_mode) {
	if (subscribeunlock_busy == true) return;
	subscribeunlock_busy = true;
	subscribeunlock_suffix = suffix;
	subscribeunlock_soft_mode = soft_mode;
	jQuery("#submit"+suffix).attr("disabled","disabled");
	jQuery("#loading"+suffix).fadeIn(300);
	jQuery("#message"+suffix).slideUp("slow");
	var tmp_email = jQuery("#email"+suffix).val();
	tmp_email = tmp_email.replace("@", "+");
	var tmp_name = jQuery("#name"+suffix).val();
	if (tmp_name) tmp_name = tmp_name.replace("@", "+");
	else tmp_name = "";
	jQuery.ajax({
		url: subscribeunlock_action, 
		data: {
			subscribeunlock_email: tmp_email,
			subscribeunlock_name: tmp_name,
			subscribeunlock_suffix: suffix,
			action: "subscribeunlock_submit"
		},
		dataType: "jsonp",
		success: function(return_data) {
			var data = return_data.html;
			jQuery("#submit"+subscribeunlock_suffix).removeAttr("disabled");
			jQuery("#loading"+subscribeunlock_suffix).fadeOut(300);
			if(data.match("subscribeunlock_confirmation_info") != null) {
				subscribeunlock_ga_track("opt-in-locker", "subscribe");
				subscribeunlock_write_cookie("subscribeunlock", subscribeunlock_cookie_value, 180);
				if (subscribeunlock_soft_mode == "on") {
					jQuery(".subscribeunlock_container").fadeOut(500, function() {
						jQuery(".subscribeunlock_content").removeClass("subscribeunlock_invisible");
					});
				} else {
					jQuery("#subscribeunlock_signup_form"+subscribeunlock_suffix).fadeOut(500, function() {
						jQuery("#subscribeunlock_confirmation_container"+subscribeunlock_suffix).html(data);
						jQuery("#subscribeunlock_confirmation_container"+subscribeunlock_suffix).fadeIn(500, function() {
							location.reload();
						});
					});
				}
			} else {
				jQuery("#message"+subscribeunlock_suffix).html(data);
				jQuery("#message"+subscribeunlock_suffix).slideDown("slow");
			}
			subscribeunlock_busy = false;
		}
	});
}

function subscribeunlock_read_cookie(key) {
	var pairs = document.cookie.split("; ");
	for (var i = 0, pair; pair = pairs[i] && pairs[i].split("="); i++) {
		if (pair[0] === key) return pair[1] || "";
	}
	return null;
}

function subscribeunlock_write_cookie(key, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	} else var expires = "";
	document.cookie = key+"="+value+expires+"; path=/";
}

function subscribeunlock_ga_track(type, action) {
	if (subscribeunlock_ga_tracking != "on") return;
	try {
		var title = document.title;
		if (title.length > 0) {
			if (typeof _gaq == 'object') {
				_gaq.push(['_trackEvent', type, action, title, 1, false]);
			} else if (typeof _trackEvent == 'function') { 
				_trackEvent(type, action, title, 1, false);
			} else if (typeof __gaTracker == 'function') { 
				__gaTracker('send', 'event', type, action, title);
			} else if (typeof ga == 'function') {
				ga('send', 'event', type, action, title);
			}
		}
	} catch(error) {
	
	}
}

var subscribeunlock_cookie = subscribeunlock_read_cookie("subscribeunlock");