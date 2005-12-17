var m_ibox = null;
var drag_box = null;
var box_after = null;
var interval = 0;
var scroll = 0;
var boxes = document.getElementById('intraboxes').childNodes;
for(var i = 0; i < boxes.length - 1; i++) {
	init(boxes[i]);
}
function doIntraboxDown(e) {
	e = fixE(e);
	var target = (e.target) ? e.target : e.srcElement;
	//ignore links and inputs
	if(!target.href && !target.type) {
		drag_box = this;
		this.lastMouseX = e.clientX;
		this.lastMouseY = e.clientY;
		document.onmousemove = function(){
					drag_box.onDragStart();
					document.onmousemove = doIntraboxMove;
					}
		document.onmouseup = doIntraboxPlace;
	} else {
		return true;
	}
	return false;
}
function doIntraboxMove(e) {
	e = fixE(e);
	var x = e.clientX;
	var y = e.clientY;
	var top = parseInt(make_intrabox().style.top);
	var left = parseInt(make_intrabox().style.left);
	var new_left = left + x - drag_box.lastMouseX;
	var new_top = top + y - drag_box.lastMouseY;
	var wpos = parseInt(make_intrabox().style.top) - (document.all?document.documentElement.scrollTop:window.pageYOffset);
	make_intrabox().style.left = new_left + "px";
	make_intrabox().style.top = new_top + "px";
	drag_box.lastMouseX = x;
	drag_box.lastMouseY = y;
	drag_box.onDrag(new_left, new_top);
	if(wpos < 30) {
		make_intrabox().style.top = (parseInt(make_intrabox().style.top)-1)+"px";
		window.scrollBy(0,-1);
		if(scroll==0) {
			scroll = 1;
			while(scroll) document.fireEvent("onmousemove");
		}
	}
	else if(wpos + parseInt(make_intrabox().style.height) > (document.all?document.documentElement.clientHeight:window.innerHeight)) {
		make_intrabox().style.top = (parseInt(make_intrabox().style.top)+1)+"px";
		window.scrollBy(0,1);
		if(scroll==0) {
			scroll = 1;
			while(scroll) document.fireEvent("onmousemove");
		}
	}
	else scroll = 0;
	return false;
}
function doIntraboxPlace() {
	document.onmousemove = null;
	document.onmouseup = null;
	drag_box.onDragEnd();
	drag_box = null;
}
function make_intrabox() {
	if(!m_ibox) {
		m_ibox = document.createElement("div");
		m_ibox.style.display = "none";
		m_ibox.style.position = "absolute";
		m_ibox.style.cursor = "move";
		m_ibox.style.paddingBottom = "0px";
		document.body.appendChild(m_ibox);
	}
	return m_ibox;
}
function fixE(e) {
	return (typeof e == "undefined") ? window.event : e;
}
function getLeft(element) {
	var offset = 0;
	var elem = element;
	while(elem != null) {
		offset += elem["offsetLeft"];
		elem = elem.offsetParent;
	}
	return offset;
}
function getTop(element) {
	var offset = 0;
	var elem = element;
	while(elem != null) {
		offset += elem["offsetTop"];
		elem = elem.offsetParent;
	}
	return offset;
}
function createRequestObject() {
	var ro = null;
	if(window.ActiveXObject) {
		ro = new ActiveXObject("Msxml2.XMLHTTP");
		if(!ro) {
			ro = new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	else if(window.XMLHttpRequest) {
		ro = new XMLHttpRequest();
	}
	return ro;
}
var http = createRequestObject();
function sendReq(info) {
	http.open('GET', ajax_page+info);
	http.onreadystatechange = handleResponse;
	http.send(null);
}
function handleResponse() {
	if(http.readyState == 4) {
		var response = http.responseText;
	}
}
function init(box) {
	box.onmousedown = doIntraboxDown;
	if(isNaN(parseInt(make_intrabox().style.left)))
		make_intrabox().style.left = "0px";
	if(isNaN(parseInt(make_intrabox().style.top)))
		make_intrabox().style.top = "0px";
	box.onDragStart = function() {
		clearInterval(interval);
		var offset_height = 0;
		for(var i = 0; i < boxes.length; i++) {
			if(boxes[i] == this) {
				offset_height = boxes[i].offsetHeight;
			}
			boxes[i].pagePosLeft = getLeft(boxes[i]);
			boxes[i].pagePosTop = getTop(boxes[i]) - offset_height;
		}
		var m_i = make_intrabox();
		m_i.style.left = getLeft(this) + "px";
		m_i.style.top = getTop(this) + "px";
		m_i.style.height = this.offsetHeight;
		m_i.style.width = this.offsetWidth;
		m_i.style.opacity = 0.8;
		m_i.style.filter = "alpha(opacity=80)";
		m_i.innerHTML = this.innerHTML;
		m_i.style.border = this.style.border;
		m_i.style.borderWidth = this.style.borderWidth;
		m_i.style.borderStyle = this.style.borderStyle;
		m_i.style.borderColor = this.style.borderColor;
		m_i.className = this.className;
		m_i.style.display="block";
		this.style.visibility="hidden";
		this.dragged = false;
	};
	box.onDrag = function(new_left, new_top) {
		var box = null;
		var max = 100000000;
		for(var i = 0; i < boxes.length; i++) {
			if(boxes[i] == this)
				continue;
			var dist = Math.sqrt(Math.pow(new_left - boxes[i].pagePosLeft, 2) + Math.pow(new_top - boxes[i].pagePosTop, 2));
			if(isNaN(dist))
				continue;
			if(dist < max) {
				max = dist;
				box = boxes[i];
			}
		}
		if(box != null) {
			box_after = box;
			if (this.nextSibling != box) {
				box.parentNode.insertBefore(this, box);
			}
			//this.parentNode.style.display = "none";
			//this.parentNode.style.display = "";
		}
		this.dragged = true;
	};
	box.onDragEnd = function() {
		if(this.dragged) {
			sendReq(this.id + "," + box_after.id);
			var dist = 15;
			var left = parseInt(make_intrabox().style.left);
			var top = parseInt(make_intrabox().style.top);
			var left_increment = (left - getLeft(this)) / dist;
			var top_increment = (top - getTop(this)) / dist;
			var box = this;
			clearInterval(interval);
			interval = setInterval(function() {
				if(dist < 1) {
					clearInterval(interval);
					box.style.visibility="visible";
					make_intrabox().style.display = "none";
					return;
				}
				dist--;
				left -= left_increment;
				top -= top_increment;
				make_intrabox().style.left = left + "px";
				make_intrabox().style.top = top + "px";
			}, 10);
		}
		else {
			if(this.href) {
				make_intrabox().style.display = "none";
				(this.target) ? window.open(this.href, this.target) : document.location = this.href;
			}
		}
	};
}
