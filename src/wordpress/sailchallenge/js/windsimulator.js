var WindSimulator = (function() {
	var speed;
	var angle;
	var destAngle;
	var direction;
	var sign;
	var delay;
	var triggerChange;
	var ranges;
	var angleDiff;
	var knots;
	var kn;
	var knCounter;
	var knSign;
	
	function WindSimulator() {
		this.triggerChange = 0;
		this.angle = 0.0;
		this.destAngle = 0.0;
		this.delay = this.getRandomInt(5, 30);
		this.direction = 1;
		this.sign = [-1, 1];
		this.angleDiff = 0;
		this.ranges = [
			3.0, 4.0, 5.0, 6.0, 7.0, 10.0, 11.0, 10.0, 9.0, 15.0,
			20.0, 15.0, 11.0, 13.0, 11.0
		];
		
		this.knots = [
			4.0, 5.0, 5.0, 6.0, 7.0, 10.0, 11.0, 10.0, 9.0, 15.0,
			20.0, 15.0, 11.0, 13.0, 25.0
		];
		
		this.kn = this.getkn();
		this.knCounter = 0;
		this.knSign = 1;
	}
	
	WindSimulator.prototype.getAngle = function() {
		if (this.triggerChange < this.delay) {
			++this.triggerChange;
			//if (this.triggerChange % 2 == 0)
				//this.kn += this.sign[this.getRandomInt(0, 1)] * 0.1;
		} else {
			if (Math.round(this.angle, 2) == Math.round(this.destAngle, 2)) {
				this.destAngle = this.getDestAngle();
				this.triggerChange = 0;
				this.kn = this.getkn();
				this.knSign = this.sign[this.getRandomInt(0, 1)];
			}
	
			if (this.angle != this.destAngle) {
				this.delay = this.getRandomInt(10, 20);
				this.angle += (this.destAngle < this.angle) ? -0.1 : 0.1;
				
				if (this.knCounter++ % 5 == 0) this.kn += this.knSign * 0.1;
				if (this.kn <= 0) this.knSign = 1;
				
				if (Math.round(this.angle, 2) == Math.round(this.destAngle, 2)) {
					
					this.delay = this.getRandomInt(5, 30);
					this.destAngle = this.getDestAngle();
					this.triggerChange = 0;
				}
			}
		}
		
		return this.angle;
	}
	
	WindSimulator.prototype.getDirection = function() {
		this.direction = this.sign[this.getRandomInt(0, 1)];
		return this.direction;
	}
	
	WindSimulator.prototype.getDestAngle = function() {
		var len = this.ranges.length - 1;
		return this.ranges[this.getRandomInt(0, len)] * this.getDirection();
	}
	
	WindSimulator.prototype.getkn = function() {
		var len = this.knots.length - 1;
		return this.knots[this.getRandomInt(0, len)];
	}
	
	WindSimulator.prototype.getRandomInt = function (min, max) {
		return Math.floor(Math.random() * (max - min + 1)) + min;
	}
	
	return WindSimulator;
})();