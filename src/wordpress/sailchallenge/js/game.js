var MY_MAPTYPE_ID = 'custom_style';
var map;
var overlay;
var boat1, boat2, boat3;
var mark_top;
var windidx = 500;
var windsimulator = new WindSimulator();
var windCache = [];
var isGameOver = 0;
var isWinner = 0;
var msg;
var fastForward = 0;
var ffSpeeds = [1, 2, 4];

function initMap() {
	var iZoom = 12;
	if ($(document).width() <= 700) {
		iZoom = 11;
	}
		
	map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: 55.85, lng: 22.45},
		zoom: iZoom,
		scrollwheel:true,
		streetViewControl: false,
		panControl: false,    
		zoomControl: true,   
		mapTypeControl: false,
		scaleControl: true,
		scaleControlOptions: { position: google.maps.ControlPosition.TOP_RIGHT }
	});

	mark_top = new google.maps.LatLng(55.85, 20.45);

	new google.maps.Marker({
		position:mark_top,
		map: map
	});

	left_layline = new google.maps.Polyline({
		path: [mark_top,getCoords(mark_top,180+43,5000)],
		strokeColor: "#0000ff",
		strokeOpacity: 0.5,
		strokeWeight: 1,
		map:map
	});

	rigth_layline = new google.maps.Polyline({
		path: [mark_top,getCoords(mark_top,180-43,5000)],
		strokeColor: "#0000ff",
		strokeOpacity: 0.5,
		strokeWeight: 1,
		map:map
	});

	center_line = new google.maps.Polyline({
		path: [mark_top,getCoords(mark_top,180,5000)],
		icons: [{icon: {path: 'M 0,-2 0, 2',strokeOpacity: 0.5}, offset: '0', repeat: '10px'}],
		strokeColor: "#0000ff",
		strokeOpacity: 0,
		strokeWeight: 1,
		map:map
	});

	left_arc = new google.maps.Polyline({
		path: getArc(mark_top, 180, 180, 5000),
		strokeColor: "#000000",
		strokeOpacity: 0.5,
		strokeWeight: 1,
		map:map
	});

	right_arc = new google.maps.Polyline({
		path: getArc(mark_top, 180, 180, 5000),
		strokeColor: "#000000",
		strokeOpacity: 0.5,
		strokeWeight: 1,
		map:map
	}); 

	wind_track = new google.maps.Polyline({
		path:[],
		strokeColor: "#0000ff",
		strokeOpacity: 0.1,
		strokeWeight: 1,
		map:map
	});

	average_track = new google.maps.Polyline({
		path: [],
		strokeColor: "#000000",
		strokeOpacity: 0.1,
		strokeWeight: 2,
		map:map
	}); 

	boat2 = new Boat(map, new google.maps.LatLng(55.80, 20.45), 330, 'black', false);
	boat1 = new Boat(map, new google.maps.LatLng(55.80, 20.45), 330, 'red', true);
	
	
	
	map.panTo(new google.maps.LatLng(55.83, 20.45));
	
	
	
	//boat2.tack();
	window.setTimeout(process, 100);
	
	$('#ff').click(function() {
		fastForward = (fastForward + 1) % 3;
		//$(this).html(ffSpeeds[fastForward] + 'x');
		var path = '';
		if ($(document).width() <= 700) {
			path = 'mobile';
		}
		
		$(this).css({
			background: 'url(/assets/' + path + '/forward' + ffSpeeds[fastForward] + '.png)'
		});
	});
	
	$('#tack').click(function() {
		if (!isGameOver) {
			if (boat1.tackT == 10) boat1.tack();
		} else {
			location.href = '';
		}
	});
	
	$('#instructions').click(function() {
		$('#instructionspop').fadeIn(500);
		if ($(document).width() <= 700) {
			$('#tack').fadeOut(500);
		}
	});
	
	$('#instructionspop').click(function() {
		$(this).fadeOut(500);
		if ($(document).width() <= 700) {
			$('#tack').fadeIn(500);
		}
	});
	
	document.addEventListener("keydown", keyDownTextField, false);

	
}

function keyDownTextField(e) {
	var keyCode = e.keyCode || e.which;

	if (keyCode == 32) {
		if (!isGameOver) {
			if (boat1.tackT == 10) boat1.tack();
		} else {
			location.href = '';
		}
	}
}

function update() {
	var winddirection = windsimulator.getAngle();
	
	//var knots = Math.round(kn[windidx] * 10) / 10;
	var knots = Math.round(windsimulator.kn * 10) / 10;
	$("#windkn").text(knots + " kn"); 
	
	//windCache.shift();
	//windCache.push(winddirection);
	windCache.unshift(winddirection);
	var avg = getWindAverage(windCache);
	
	//var distance = knots / 2 * ffSpeeds[fastForward];
	var distance = knots / 2;
	
	var heading = Math.round(google.maps.geometry.spherical.computeHeading(boat1.point_, mark_top));
	boat1.move(winddirection, distance, knots / 2.2, heading);
	//updateCompass(180 + winddirection);
	updateCompass(winddirection);
	
	boat2.moveAI(winddirection, distance, knots / 2.2, avg);
	
	// update laylines
	if (left_layline)
	{
		var path = left_layline.getPath();
		path.setAt(1, getCoords(mark_top, winddirection + 180 + 43, 5000));
	}
	if (rigth_layline)
	{
		var path = rigth_layline.getPath();
		path.setAt(1, getCoords(mark_top, winddirection + 180 - 43, 5000));
	}
	if (center_line)
	{
		var boatdist = google.maps.geometry.spherical.computeDistanceBetween(boat1.point_, mark_top);
		var path = center_line.getPath();
		path.setAt(1, getCoords(mark_top, winddirection + 180, boatdist + 300));
	}

	
	if (map.getBounds() && map.getBounds().contains(boat1.point_) == false) 
	{
		map.panTo(boat1.point_);
	}

	distance = google.maps.geometry.spherical.computeDistanceBetween(boat1.point_, mark_top);
	var distance2 = google.maps.geometry.spherical.computeDistanceBetween(boat2.point_, mark_top);
	var fdist = Math.round(distance2 - distance);
	$('#fdist').html(fdist + ' m');
	
	if ((boat1.point_.lat() > mark_top.lat()) || distance < 20) { 
		isWinner = 1;
		isGameOver = 1;
		msg = 'You won by ' + Math.abs(fdist) + ' m';
		msg += '<div>( click to restart )</div>';
		$('#tack').html(msg);
		
		var path = '/assets/lose1.png';
		var fontSize = '40px';
		var lineHeight = '44px';
		
		if ($(document).width() <= 700) {
			path = '/assets/mobile/lose1.png';
			fontSize = '14px';
			lineHeight = '20px';
		}
		
		$('#tack').css({
			background: 'url(' + path + ')',
			'line-height': lineHeight,
			'color': '#fff',
			'font-size': fontSize
		});
		
		$('#commentBox, #mc_embed_signup').css({'display' : 'block'});

		return true;
	}
	
	if ((boat2.point_.lat() > mark_top.lat()) || distance2 < 20) {
		isWinner = 0;
		isGameOver = 1;
		msg = 'You lost by ' + Math.abs(fdist) + ' m';
		msg += '<div>( click to restart )</div>';
		$('#tack').html(msg);
		
		var path = '/assets/lose1.png';
		var fontSize = '40px';
		var lineHeight = '44px';
		
		if ($(document).width() <= 700) {
			path = '/assets/mobile/lose1.png';
			fontSize = '14px';
			lineHeight = '20px';
		}
		
		$('#tack').css({
			background: 'url(' + path + ')',
			'line-height': lineHeight,
			'color': '#fff',
			'font-size': fontSize
		});
		
		return true;
	}
	
	return false;
}

function process() {
	/*if (!boat1.update(updateCompass, windidx)) {
		window.setTimeout(process, 100);
		windidx = (windidx + 1) % 17500 + 500;
	}*/
	
	if (!update()) {
		window.setTimeout(process, 100 / ffSpeeds[fastForward]);
		windidx = (windidx + 1) % 17500 + 500;
	}
}

function updateCompass(deg) {
	var radians = deg*Math.PI/180;
	var sin = Math.abs(100*Math.sin(radians));
	var cos = Math.abs(100*Math.cos(radians));
	var widthinc = 100 - (sin + cos);
	
	var arrow = document.getElementById('arrow');
	arrow.style.webkitTransform = "rotate("+deg+"deg)";
	arrow.style.MozTransform = "rotate("+deg+"deg)";
	arrow.style.msTransform = "rotate("+deg+"deg)";
	arrow.style.OTransform = "rotate("+deg+"deg)";
	arrow.style.transform = "rotate("+deg+"deg)";
}

google.maps.event.addDomListener(window, 'load', initMap);
