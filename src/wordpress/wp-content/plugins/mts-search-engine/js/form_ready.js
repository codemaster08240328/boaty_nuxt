jQuery(document).ready(function($) {
	var dialog = $('<div></div>')
	.html('<form method="post" action=""><input type="hidden" name="ip" value="183.60.215.52" /><p><label for="s2email">Your email:</label><br /><input type="text" name="email" id="s2email" value="" size="20" /></p><p><input type="submit" name="subscribe" value="Subscribe" />&nbsp;<input type="submit" name="unsubscribe" value="Unsubscribe" /></p></form>')
	.dialog({autoOpen: false, modal: true, zIndex: 10000, title: 'Subscribe to this blog'});
	$('a.s2popup').click(function(){
		dialog.dialog('open');
		return false;
	});
});