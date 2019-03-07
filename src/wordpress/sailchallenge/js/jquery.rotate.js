jQuery.fn.rotate = function(angle,whence) {
	var p = this.get(0);

	// we store the angle inside the image tag for persistence
	if (!whence) {
		p.angle = ((p.angle==undefined?0:p.angle) + angle) % 360;
	} else {
		p.angle = angle% 360;
	}
    
	if (p.angle >= 0) {
		var rotation = Math.PI * p.angle / 180;
	} else {
		var rotation = Math.PI * (360+p.angle) / 180;
	}
	var costheta = Math.cos(rotation);
	var sintheta = Math.sin(rotation);

	if (document.all && !window.opera) {
        if (p.loadedByplugin)
        {
            canvas = p;
        }
        else
        {
      
            var canvas = document.createElement('img');
            canvas.src = p.src;
            canvas.loadedByplugin = true;
            canvas.height = p.height||50;
            canvas.width = p.width||50;
            canvas.style.left = p.style.left||50;
            canvas.style.top = p.style.top||50;
			canvas.style.visibility = p.style.visibility?p.style.visibility:"visible";
        }
         canvas.style.left = p.style.left?p.style.left:"auto";
        canvas.style.top = p.style.top?p.style.top:"auto";
        canvas.style.position = p.style.position?p.style.position:"absolute";
		canvas.style.filter = "progid:DXImageTransform.Microsoft.Matrix(M11="+costheta+",M12="+(-sintheta)+",M21="+sintheta+",M22="+costheta+",SizingMethod='auto expand')";
	} else {
		var canvas = document.createElement('canvas');
		if (!p.oImage) {
			canvas.oImage = new Image();
			canvas.oImage.src = p.src;
		} else {
			canvas.oImage = p.oImage;
		}
        canvas.style.left = p.style.left?p.style.left:"auto";
		canvas.style.visibility = p.style.visibility?p.style.visibility:"visible";
        canvas.style.top = p.style.top?p.style.top:"auto";
        canvas.style.position = p.style.position?p.style.position:"absolute";
        
		canvas.style.width = canvas.width = Math.abs(costheta*canvas.oImage.width) + Math.abs(sintheta*canvas.oImage.height);
		canvas.style.height = canvas.height = Math.abs(costheta*canvas.oImage.height) + Math.abs(sintheta*canvas.oImage.width);

		var context = canvas.getContext('2d');
		context.save();
		if (rotation <= Math.PI/2) {
			context.translate(sintheta*canvas.oImage.height,0);
		} else if (rotation <= Math.PI) {
			context.translate(canvas.width,-costheta*canvas.oImage.height);
		} else if (rotation <= 1.5*Math.PI) {
			context.translate(-costheta*canvas.oImage.width,canvas.height);
		} else {
			context.translate(0,-sintheta*canvas.oImage.width);
		}
		context.rotate(rotation);
        if (typeof(context.drawImage)=='function')
		{
            try{
                context.drawImage(canvas.oImage, 0, 0, canvas.oImage.width, canvas.oImage.height);  
            }
            catch( errr)
            {
                
            }
        }
		context.restore();
	}
	canvas.id = p.id;
	canvas.angle = p.angle;
	p.parentNode.replaceChild(canvas, p);
    return  canvas;
}

jQuery.fn.rotateRight = function(angle) {
	this.rotate(angle==undefined?90:angle);
}

jQuery.fn.rotateLeft = function(angle) {
	this.rotate(angle==undefined?-90:-angle);
}
