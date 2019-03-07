var USGSOverlay = (function() {
	var point_;
	var image_;
	var map_;
	var div_;
	var angle_;
	var timeout_;
	var speed_;
	var img_;
	var divSpeed_;
	var divDirection_;
	var displayInfo_;
	var obj;

	USGSOverlay.prototype = new google.maps.OverlayView();
	
	function USGSOverlay(map, point, angle, image, displayInfo) {
		this.point_ = point;
		this.speed_ = 0;
		this.image_ = image;
		this.map_ = map;
		this.angle_ = angle || 0;
		this.timeout_ = null;
		this.div_ = null;
		this.img_ = null;
		this.divSpeed_ = null;
		this.divDirection_ = null;
		this.displayInfo_ = displayInfo;
		this.setMap(map);
		obj = this;
	}
	
	USGSOverlay.prototype.onAdd = function() {

		var div = document.createElement('div');
		div.style.borderStyle = 'none';
		div.style.borderWidth = '0px';
		div.style.position = 'absolute';
		
		/*var div = document.createElement('DIV');
		div.style.borderStyle = "none";
		div.style.borderWidth = "0px";
		div.style.overflow = "hidden";
		div.style.textOverflow= "ellipsis";
		div.style.whiteSpace = "nowrap";
		div.style.position = "absolute";
		div.style.width = "100px";
		div.style.zIndex = "200";
		div.style.textAlign = "left";
		div.style.display = "none";//this.hideFlag?"none":"";*/
		
		// Create the img element and attach it to the div.
		var img = document.createElement('img');
		img.src = this.image_;
		img.style.width = '32px';
		img.style.height = '28px';
		img.style.position = 'absolute';
		div.appendChild(img);
		
		var divSpeed = document.createElement('DIV');
		divSpeed.style.borderStyle = "none";
		divSpeed.style.borderWidth = "0px";
		divSpeed.style.overflow = "hidden";
		divSpeed.style.textOverflow= "ellipsis";
		divSpeed.style.whiteSpace = "nowrap";
		divSpeed.style.width = "200px";
		divSpeed.style.zIndex = "200";
		divSpeed.style.textAlign = "left";
		divSpeed.style.position = "relative";
		divSpeed.style.left = "32px";
		divSpeed.innerHTML = (this.speed_ * 1.9438612860586).toFixed(1) + "kn";
		divSpeed.style.display = this.displayInfo_ ? "block" : "none";
		div.appendChild(divSpeed);
		
		var divDirection = document.createElement('DIV');
		divDirection.style.borderStyle = "none";
		divDirection.style.borderWidth = "0px";
		divDirection.style.overflow = "hidden";
		divDirection.style.textOverflow= "ellipsis";
		divDirection.style.whiteSpace = "nowrap";
		divDirection.style.width = "200px";
		divDirection.style.zIndex = "200";
		divDirection.style.textAlign = "left";
		divDirection.style.position = "relative";
		divDirection.style.left = "32px";
		divDirection.innerHTML = this.angle_ + "&ordm; ";
		divDirection.style.display = this.displayInfo_ ? "block" : "none";
		div.appendChild(divDirection);

		
		this.divSpeed_ = divSpeed;
		this.divDirection_ = divDirection;
		this.img_ = img;
		this.div_ = div;

		// Add the element to the "overlayLayer" pane.
		var panes = this.getPanes();
		panes.overlayLayer.appendChild(div);
	}

	USGSOverlay.prototype.draw = function() {

		// We use the south-west and north-east
		// coordinates of the overlay to peg it to the correct position and size.
		// To do this, we need to retrieve the projection from the overlay.
		clearTimeout(this.timeout_);
		var overlayProjection = this.getProjection();
		var div = this.div_;
		var img = this.img_;
		var angle = this.angle_;
		var p = this.point_;
		var divDirection_ = this.divDirection_;
		var divSpeed_ = this.divSpeed_;
		var speed = this.speed_;
		this.timeout_ = window.setTimeout(function() {
			
			//if (typeof this.getProjection() != 'undefined') {
				// Retrieve the south-west and north-east coordinates of this overlay
				// in LatLngs and convert them to pixel coordinates.
				// We'll use these coordinates to resize the div.
				var point = overlayProjection.fromLatLngToDivPixel(p);
				
				/*var sw = overlayProjection.fromLatLngToDivPixel(this.point_.getSouthWest());
				var ne = overlayProjection.fromLatLngToDivPixel(this.point_.getNorthEast());

				// Resize the image's div to fit the indicated dimensions.
				var div = this.div_;
				div.style.left = sw.x + 'px';
				div.style.top = ne.y + 'px';
				div.style.width = (ne.x - sw.x) + 'px';
				div.style.height = (sw.y - ne.y) + 'px';*/
				var lObjCor = getCorrection(angle);
				divDirection_.innerHTML = lObjCor.angle.toFixed(0) + "&ordm;";
				divSpeed_.innerHTML = (speed * 1.9438612860586).toFixed(1) + " kn";
				
				var x = point.x;
				var y = point.y;
				var width = 32;
				
				x -= width / 2;
				y -= width / 2;
				
				//var div = obj.div_;
				div.style.left = x + 'px';
				div.style.top = y + 'px';
				div.style.width = '32px';
				div.style.height = '28px';
				div.style.zIndex = 99999;

				//var angle = obj.angle_;

				img.style.webkitTransform = "rotate("+angle+"deg)";
				img.style.MozTransform = "rotate("+angle+"deg)";
				img.style.msTransform = "rotate("+angle+"deg)";
				img.style.OTransform = "rotate("+angle+"deg)";
				img.style.transform = "rotate("+angle+"deg)";
			//}
		}, 10);
	}
	
	USGSOverlay.prototype.move = function(point, angle, speed) {
		this.point_ = point;
		this.angle_ = angle;
		this.speed_ = speed;
		//window.setTimeout(100, function() {  });
		this.draw();
	}

	
	// The onRemove() method will be called automatically from the API if
	// we ever set the overlay's map property to 'null'.
	USGSOverlay.prototype.onRemove = function() {
		this.div_.parentNode.removeChild(this.div_);
		this.div_ = null;
	}

	
	return USGSOverlay;
})();