/////////////////////////////////////////////////////////////////////////
// Generic Resize by Erik Arvidsson                                    //
//                                                                     //
// You may use this script as long as this disclaimer is remained.     //
// See www.dtek.chalmers.se/~d96erik/dhtml/ for mor info               //
//                                                                     //
// How to use this script!                                             //
// Link the script in the HEAD and create a container (DIV, preferable //
// absolute positioned) and add the class="resizeMe" to it.            //
/////////////////////////////////////////////////////////////////////////


var regex = /resizeMe/;

var theobject = null; //This gets a value as soon as a resize start

function resizeObject() {
	this.el        = null; //pointer to the object
	this.dir    = "";      //type of current resize (n, s, e, w, ne, nw, se, sw)
	this.grabx = null;     //Some useful values
	this.graby = null;
	this.width = null;
	this.height = null;
	this.left = null;
	this.top = null;
}
	

//Find out what kind of resize! Return a string inlcluding the directions
function getDirection(el) {
	var xPos, yPos, offset, dir;
	dir = "";

	//Position relative to element
	xPos = window.event.offsetX - el.scrollLeft;
	yPos = window.event.offsetY - el.scrollTop;

	offset = 7; //The distance from the edge in pixels

	if (yPos<offset) dir += "n";
	else if (yPos > el.offsetHeight-offset) dir += "s";
	if (xPos<offset) dir += "w";
	else if (xPos > el.offsetWidth-offset) dir += "e";

	return dir;
}

function doDown() {
	var el = event.srcElement;//getReal(event.srcElement, "className", regex);

	if (el == null || !regex.test(el.className)) {
		theobject = null;
		return;
	}		

	dir = getDirection(el);
	if (dir == "") return;

	theobject = new resizeObject();
		
	theobject.el = el;
	theobject.dir = dir;

	//Absolute position of mouse on screen
	theobject.grabx = window.event.clientX;
	theobject.graby = window.event.clientY;
	
	//Current size of object
	theobject.width = el.offsetWidth - 14;
	theobject.height = el.offsetHeight - 14;
	
	if (ie) {
		theobject.left = el.runtimeStyle.screenLeft;
		theobject.top = el.runtimeStyle.screenTop;
	}

	window.event.returnValue = false;
	window.event.cancelBubble = true;
}

function doUp() {
	if (theobject != null) {
		theobject = null;
	}
}

function doMove() {
	var el, xPos, yPos, str, xMin, yMin;
	xMin = 8; //The smallest width possible
	yMin = 8; //             height
	
	el = event.srcElement;//getReal(event.srcElement, "className", regex);
	if (theobject == null && regex.test(el.className)) {
		str = getDirection(el);
		//Fix the cursor	
		if (str == "") str = "default";
		else str += "-resize";
		el.style.cursor = str;
	}

	//Dragging starts here
	if(theobject != null) {
		var width = oldwidth = theobject.el.offsetWidth - 14;
		var height = oldheight = theobject.el.offsetHeight - 14;

		if (dir.indexOf("e") != -1) {
			width = Math.max(xMin, theobject.width + window.event.clientX - theobject.grabx);
		}
		if (dir.indexOf("s") != -1) {
			height = Math.max(yMin, theobject.height + window.event.clientY - theobject.graby);
		}
		if (dir.indexOf("w") != -1) {
			width = Math.max(xMin, theobject.width - window.event.clientX + theobject.grabx);
		}
		if (dir.indexOf("n") != -1) {
			height = Math.max(yMin, theobject.height - window.event.clientY + theobject.graby);
		}
		
		theobject.el.style.width = width + "px";
		theobject.el.style.height = height + "px";
		
		if (ie) {
			theobject.el.runtimeStyle.width = width + "px";
			theobject.el.runtimeStyle.height = height + "px";
			if (theobject.el.runtimeStyle.autoLeft) {
				theobject.el.runtimeStyle.screenLeft += oldwidth - width;
			}
			theobject.el.runtimeStyle.screenTop += oldheight - height;
		}
	
		window.event.returnValue = false;
		window.event.cancelBubble = true;
	
		setCookie(theobject.el.id + "_width", theobject.el.style.width, "/");
		setCookie(theobject.el.id + "_height", theobject.el.style.height, "/");
	}
}


/*function getReal(el, type, testExp) {
	temp = el;
	while ((temp != null) && (temp.tagName != "BODY")) {
		if (testExp.test(eval("temp." + type))) {
			el = temp;
			return el;
		}
		temp = temp.parentElement;
	}
	window.status = el.id + " (" + el.className + ")" +
			" event.offsetY=" + event.offsetY + " event.clientY=" + event.clientY + 
			" event.scrollTop=" + event.scrollTop +
			" el.offsetHeight=" + el.offsetHeight + " el.scrollTop=" + el.scrollTop;
	return el;
}*/

// The purpose for using onload is to prevent spurious events before the page is finished loading
document.onload = function() {
	document.onmousedown = doDown;
	document.onmouseup   = doUp;
	document.onmousemove = doMove;
}
