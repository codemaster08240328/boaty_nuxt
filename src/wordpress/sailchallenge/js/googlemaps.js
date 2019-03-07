$(document).ready(function()
{ 
	var script = document.createElement("script");   
	script.type = "text/javascript";   
	
	script.src = "http://maps.googleapis.com/maps/api/js?key=AIzaSyAvlKchA46j0y9l-PDK6An7K1W6tonYw4g&callback=googleMapsLoaded&libraries=visualization";   
	document.body.appendChild(script); 
}); 
 
function googleMapsLoaded()
{
	$.getScript("http://www.sailracer.net/js/usgsoverlay.js", function() 
	{
		if (typeof(readyWithGoogle)=="function")
		{
			window.setTimeout("readyWithGoogle()",500);
		}
	});
}
