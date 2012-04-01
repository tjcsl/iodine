var clippy = {};

/**
 * Initializes Clippy's elements and events
 */
clippy.init = function() {
	// get Clippy's position
	clippy.x = window.innerWidth - 300;
	clippy.y = Math.round(window.innerHeight * 0.45);
	if(!!localStorage) {
		if(!!localStorage.x ) {
			clippy.x = localStorage.x;
		}
		if(!!localStorage.y) {
			clippy.y = localStorage.y;
		}
	}
	
	// initialize the element that will be Clippy
	clippy.elem = document.createElement("div");
	clippy.elem.id = "clippy";
	clippy.elem.onselectstart = function(e) {
		if(!!e && !!e.preventDefault) {
			e.preventDefault();
		}
		return false;
	};
	clippy.elem.ondrag = function(e) {
		if(!!e && !!e.preventDefault) {
			e.preventDefault();
		}
		return false;
	};
	
	// initialize the speech buble
	clippy.bubbleElem = document.createElement("table");
	clippy.bubbleElem.id = "clippyBubble";
	clippy.bubbleElem.cellSpacing = 0;
	clippy.bubbleElem.cellPadding = 0;
	
	var bubbleTopRow = document.createElement("tr");
	var bubbleMidRow = document.createElement("tr");
	var bubbleBottomRow = document.createElement("tr");
	var bubbleArrowRow = document.createElement("tr");
	
	var bubbleTopLeft = document.createElement("td");
	bubbleTopLeft.className = "topLeft";
	bubbleTopLeft.innerHTML = "&nbsp;";
	bubbleTopRow.appendChild(bubbleTopLeft);
	var bubbleTopCenter = document.createElement("td");
	bubbleTopCenter.className = "topCenter";
	bubbleTopCenter.innerHTML = "&nbsp;";
	bubbleTopRow.appendChild(bubbleTopCenter);
	var bubbleTopRight = document.createElement("td");
	bubbleTopRight.className = "topRight";
	bubbleTopRight.innerHTML = "&nbsp;";
	bubbleTopRow.appendChild(bubbleTopRight);
	var bubbleMidLeft = document.createElement("td");
	bubbleMidLeft.className = "midLeft";
	bubbleMidLeft.innerHTML = "&nbsp;";
	bubbleMidRow.appendChild(bubbleMidLeft);
	clippy.bubbleText = document.createElement("td");
	clippy.bubbleText.id = "clippyBubbleText";
	bubbleMidRow.appendChild(clippy.bubbleText);
	var bubbleMidRight = document.createElement("td");
	bubbleMidRight.className = "midRight";
	bubbleMidRight.innerHTML = "&nbsp;";
	bubbleMidRow.appendChild(bubbleMidRight);
	var bubbleBottomLeft = document.createElement("td");
	bubbleBottomLeft.className = "bottomLeft";
	bubbleBottomLeft.innerHTML = "&nbsp;";
	bubbleBottomRow.appendChild(bubbleBottomLeft);
	var bubbleBottomCenter = document.createElement("td");
	bubbleBottomCenter.className = "bottomCenter";
	bubbleBottomCenter.innerHTML = "&nbsp;";
	bubbleBottomRow.appendChild(bubbleBottomCenter);
	var bubbleBottomRight = document.createElement("td");
	bubbleBottomRight.className = "bottomRight";
	bubbleBottomRight.innerHTML = "&nbsp;";
	bubbleBottomRow.appendChild(bubbleBottomRight);
	
	clippy.bubbleArrowCell = document.createElement("td");
	clippy.bubbleArrowCell.colSpan = 3;
	clippy.bubbleArrowCell.id = "clippyBubbleArrow";
	clippy.bubbleArrowCell.className = "left";
	var bubbleArrowElem = document.createElement("div");
	bubbleArrowElem.innerHTML = "&nbsp;";
	clippy.bubbleArrowCell.appendChild(bubbleArrowElem);
	bubbleArrowRow.style.overflow = "visible";
	bubbleArrowRow.appendChild(clippy.bubbleArrowCell);
	
	clippy.bubbleElem.appendChild(bubbleTopRow);
	clippy.bubbleElem.appendChild(bubbleMidRow);
	clippy.bubbleElem.appendChild(bubbleBottomRow);
	clippy.bubbleElem.appendChild(bubbleArrowRow);
	
	
	document.body.appendChild(clippy.elem);
	document.body.appendChild(clippy.bubbleElem);
	
	
	clippy.displayMessage("Hello, and welcome to my study.  My name is Mitchell.  You're always welcome to enjoy the things that I've found.  I'm lonely, and I collect things.");
	
	
	clippy.elem.onmousedown = clippy.startDrag;
	clippy.elem.onmouseup = clippy.stopDrag;
	
	window.addEventListener("resize", clippy.reposition, false);
	//window.onresize = clippy.reposition;
};

/**
 * Displays a message in a speech bubble
 * @param {string} text - The text to display
 */
clippy.displayMessage = function(text) {
	clippy.bubbleText.innerHTML = text;
	clippy.bubbleElem.style.visibility = "visible";
	clippy.reposition();
};

/**
 * Begin dragging Clippy
 * @param {event} e - The mouse event
 */
clippy.startDrag = function(e) {
	document.body.style.WebkitUserSelect = "none";
	   document.body.style.MozUserSelect = "none";
	      document.body.style.userSelect = "none";
	
	clippy.mouseXOffset = getMouseX(e) - clippy.x;
	clippy.mouseYOffset = getMouseY(e) - clippy.y;
	
	clippy.bubbleElem.style.visibility = "hidden";
	
	document.body.onmousemove = clippy.move;
	document.body.onmouseleave = clippy.stopDrag;
	document.body.onmouseup = clippy.stopDrag;
};

/**
 * Moves clippy with the mouse
 * @param {event} e - The mouse event
 */
clippy.move = function(e) {
	clippy.x = getMouseX(e) - clippy.mouseXOffset;
	clippy.y = getMouseY(e) - clippy.mouseYOffset;
	clippy.elem.style.left = clippy.x + "px";
	clippy.elem.style.top = clippy.y + "px";
};

/**
 * Stop dragging clippy and move it back inside the window if necessary
 * @param {event} e - The mouse event
 */
clippy.stopDrag = function(e) {
	document.body.style.WebkitUserSelect = null;
	   document.body.style.MozUserSelect = null;
	      document.body.style.userSelect = null;
	
	document.body.onmousemove = null; // remove drag event
	document.body.onmouseleave = null;
	document.body.onmouseup = null;
	
	clippy.bubbleElem.style.visibility = "visible";
	clippy.reposition();
};

/**
 * Repositions Clippy within the screen
 */
clippy.reposition = function() {
	if(clippy.x + clippy.elem.offsetWidth > window.innerWidth) {
		clippy.x = window.innerWidth - clippy.elem.offsetWidth;
	}
	if(clippy.y + clippy.elem.offsetHeight > window.innerHeight) {
		clippy.y = window.innerHeight - clippy.elem.offsetHeight;
	}
	if(clippy.x < 0) {
		clippy.x = 0;
	}
	if(clippy.y < 0) {
		clippy.y = 0;
	}
	
	clippy.elem.style.left = clippy.x + "px";
	clippy.elem.style.top = clippy.y + "px";
	
	if(window.innerWidth - clippy.x > 280) {
		clippy.bubbleElem.style.left = (clippy.x + 10) + "px";
		clippy.bubbleArrowCell.className = "left";
	} else {
		clippy.bubbleElem.style.left = (clippy.x + clippy.elem.offsetWidth - clippy.bubbleElem.offsetWidth - 10) + "px";
		clippy.bubbleArrowCell.className = "right";
	}
	clippy.bubbleElem.style.top = (clippy.y - clippy.bubbleElem.offsetHeight) + "px";
};

/**
 * Hides Clippy
 */
clippy.hide = function() {
	clippy.bubbleElem.style.visibility = "hidden";
	
	clippy.elem.style.WebkitTransform = "scale(0)";
	   clippy.elem.style.MozTransform = "scale(0)";
	    clippy.elem.style.MSTransform = "scale(0)";
	     clippy.elem.style.OTransform = "scale(0)";
	      clippy.elem.style.transform = "scale(0)";
};

/**
 * Shows Clippy after being hidden
 */
clippy.show = function() {
	clippy.bubbleElem.style.visibility = "visible";
	
	clippy.elem.style.WebkitTransform = null;
	   clippy.elem.style.MozTransform = null;
	    clippy.elem.style.MSTransform = null;
	     clippy.elem.style.OTransform = null;
	      clippy.elem.style.transform = null;
};

function getMouseX(event) {
	if (!event) {
		event = window.event;
	}
	if (event.pageX) {
		return event.pageX;
	} else {
		return event.clientX + document.body.scrollLeft;
	}
}
function getMouseY(event) {
	if (!event) {
		event = window.event;
	}
	if (event.pageY) {
		return event.pageY;
	} else {
		return event.clientY + document.body.scrollTop;
	}
}
