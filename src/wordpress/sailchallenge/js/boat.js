var Boat = (function() {
	var point_;
	var color_;
	var overlay_;
	var obj_;
	var line;
	var tmpWindSpeed;
	var windidx;
	var tack_;
	var tackT;
	var displayInfo_;

	function Boat(map, point, angle, color, displayInfo) {
		this.point_ = point;
		this.img_ = 'assets/' + color + '.png';
		this.tack_ = 1;
		this.tackT = 10;
		this.tmpWindSpeed = 9.7;
		this.windidx = 0;
		this.displayInfo_ = displayInfo;
		
		obj_ = this;
		
		this.line = new google.maps.Polyline({
			path: [obj_.point_],
			strokeColor: LINE_COLOR[color],
			strokeOpacity: 0.3,
			strokeWeight: 2,
			map:map
		});

		this.overlay_ = new USGSOverlay(map, point, angle, this.img_, this.displayInfo_);
	}
	
	Boat.prototype.move = function(d, dist, speed, heading) {
		dist *= 0.75; // reduce boat speed by 25%
		
		if (this.tackT < 10) {
			var tackDelay = Math.min(5 + Math.floor(windsimulator.kn / 5), 9) / 10;
			
			dist *= tackDelay;
			speed *= tackDelay;
			++this.tackT;
		}
		
		var boatdirection = d + (this.tack_ * 43);
		
		var wr = d-heading;
		if (wr > 180) wr = wr - 360;
		if (wr > 43)
		{
			if (this.tack_==-1) var boatdirection = heading;
		}
		else if (wr < -43)
		{
			if (this.tack_==1) var boatdirection = heading;	
		}
		
		if (boatdirection > 360) boatdirection = boatdirection - 360;
		if (boatdirection < 0) boatdirection = boatdirection + 360;
		
		this.point_ = getCoords(this.point_, boatdirection, dist);
		this.overlay_.move(this.point_, boatdirection, speed);
		//console.log(this.point_);
		
		var path = this.line.getPath();
		path.push(this.point_);
	}
	
	Boat.prototype.moveAI = function(d, dist, speed, avg) {
		var heading = Math.round(google.maps.geometry.spherical.computeHeading(this.point_, mark_top));
		
		var shift = d - heading;
		if (shift < -180) shift = 360 + shift;
		if (shift > 180) shift = shift - 360;
		
		if (Math.abs(shift)<43*0.5)
		{
			// in between 80% laylines
			var wshift = d - avg["avg"];
			if (wshift < -180) wshift = 360 + wshift;
			if (wshift > 180) wshift = wshift - 360;
		
			if (wshift < 0)
			{
				if (this.tack_ == -1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
					
				}
			}
			else
			{
				if (this.tack_ == 1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
					
				}
			}
		}
		else
		{
			if (shift<0)
			{
				if (this.tack_ == -1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
					
				}
			}
			else
			{
				if (this.tack_ == 1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
					
				}
			}
		}
		
		this.move(d, dist, speed, heading);
	}
	
	Boat.prototype.moveAI2 = function(d, dist, speed, heading) {
		var angle = 180 + d;
		console.log(windsimulator.destAngle);
		if (this.tack_ == 1) {
			if ((angle > 182 && windsimulator.destAngle < 5 && windsimulator.destAngle > -5) || (windsimulator.destAngle >= 5)) {
				this.tack();
			}
		} else {
			if ((angle < 178 && windsimulator.destAngle > -5 && windsimulator.destAngle < 5) || windsimulator.destAngle <= -5) {
				this.tack();
			}
		}
		
		this.move(d, dist, speed, heading);
		
		/*if (windsimulator.destAngle > 0) {
			if (this.tack_ == 1) {
				this.tack();
			}
		} else {
			if (this.tack_ == -1) {
				this.tack();
			}
		}*/
		
		//this.move(d, dist, speed, heading);
		
		/*var heading = Math.round(google.maps.geometry.spherical.computeHeading(this.point_, mark_top));
		
		var shift = d - heading;
		if (shift < -180) shift = 360 + shift;
		if (shift > 180) shift = shift - 360;
		
		if (Math.abs(shift) < 43 * 0.5) {
			
			// in between 80% laylines
			var wshift = d - avg;
			if (wshift < -180) wshift = 360 + wshift;
			if (wshift > 180) wshift = wshift - 360;
		
			if (wshift < 0)
			{
				if (this.tack_ == -1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
				}
			}
			else
			{
				if (this.tack_ == 1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
				}
			}
			
			this.move(d, dist, speed, heading);
		}
		else
		{
			
			if (shift < 0)
			{
				if (this.tack_ == -1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
				}
			}
			else
			{
				if (this.tack_ == 1)
				{
					if (this.tackT == 10)
					{
						this.tack();
					}
				}
			}
			
			this.move(d, dist, speed, heading);
		}*/
	}
	
	/*Boat.prototype.update = function(callback, windidx) {
		var winddirection = windsimulator.getAngle();
		
		var knots = Math.round(kn[windidx] * 10) / 10;
		$("#windkn").text(knots + " kn"); 
		
		var distance = knots / 2;
		var heading = Math.round(google.maps.geometry.spherical.computeHeading(this.point_, mark_top));
		this.move(winddirection + (Math.floor(Math.random() * 4) + 1), distance, knots / 2.2, heading);
		callback(180 + winddirection);
		
		// update laylines
		if (left_layline)
		{
			var path = left_layline.getPath();
			path.setAt(1, getCoords(mark_top,winddirection + 180 + 43, 5000));
		}
		if (rigth_layline)
		{
			var path = rigth_layline.getPath();
			path.setAt(1, getCoords(mark_top, winddirection + 180 - 43, 5000));
		}
		if (center_line)
		{
			var boatdist = google.maps.geometry.spherical.computeDistanceBetween(this.point_, mark_top);
			var path = center_line.getPath();
			path.setAt(1, getCoords(mark_top, winddirection + 180, boatdist + 300));
		}

		
		if (map.getBounds() && map.getBounds().contains(this.point_)==false) 
		{
			map.panTo(this.point_);
		}

		distance = google.maps.geometry.spherical.computeDistanceBetween(this.point_, mark_top);
		
		if ((this.point_.lat() > mark_top.lat()) || distance < 125) return true;
		
		return false;
	}*/
	
	Boat.prototype.tack = function() {
		this.tack_ *= -1;
		this.tackT = 0;
	}

	return Boat;
})();