if (moz) {
	extendElementModel();
	extendEventObject();
	emulateEventHandlers(["mousemove", "mousedown", "mouseup"]);
}

var divs = Array();
divs['error'] = 
{ 
	width : getCookie('error_width', default_width),
	height : getCookie('error_height', '')
};

divs['debug'] = 
{
	width : getCookie('debug_width', default_width),
	height : getCookie('debug_height', '')
};

function swap(id) {
	var div = document.getElementById(id);
	if (/ minimized/.test(div.className)) {
		maximize(div);
		setCookie(id + "_open", "true", "/");
	} else {
		minimize(div);
		setCookie(id + "_open", "false", "/");
	}
}

function minimize(div) {
	divs[div.id].width = div.style.width;
	divs[div.id].height = div.style.height;
	div.style.width = "";
	div.style.height = "";
	div.className = div.className.replace(/ resizeMe/, "");
	div.className += " minimized";
}

function maximize(div) {
	div.style.width = divs[div.id].width;
	div.style.height = divs[div.id].height;
	div.className = div.className.replace(/ minimized/, "");
	div.className += " resizeMe";
}

if (eval(getCookie('error_open', 'true'))) {
	var error_div = document.getElementById("error");
	if (error_div) {
		maximize(error_div);
	}
}
if (eval(getCookie('debug_open', 'false'))) {
	var debug_div = document.getElementById("debug");
	if (debug_div) {
		maximize(debug_div);
	}
}

