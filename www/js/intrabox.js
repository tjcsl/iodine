var m_ibox = null;
var drag_box = null;
var box_after = null;
var interval = 0;
var scroll = 0;
var box_index = -1, to_box_index = -1;
if(!document.getElementByID && document.all) {
	document.getElementById = function(id) {return document.all[id]; };
}
var boxes = document.getElementById("intraboxes").childNodes;
for(var i = 0; i < boxes.length - 1; i++) {
	if(boxes[i].id) {
		var boxid = boxes[i].id.replace(/intrabox_/, "");
		init(document.getElementById("boxheader_" + boxid));
	}
}
function doIntraboxDown(e) {
	e = fixE(e);
	var target = (e.target) ? e.target : e.srcElement;
	//ignore links and inputs
	if(target.href || target.type) {
		return true;
	}
	drag_box = this;
	this.lastMouseX = e.clientX;
	this.lastMouseY = e.clientY;
	document.onmousemove = function() {
				drag_box.onDragStart();
				document.onmousemove = doIntraboxMove;
				}
	document.onmouseup = doIntraboxPlace;
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
	if(wpos < 30) { //If the top of the ibox is dragged near the top of the screen, scroll up.
		make_intrabox().style.top = (parseInt(make_intrabox().style.top)-1)+"px";
		window.scrollBy(0,-1);
		if(scroll==0) {
			scroll = 1;
			while(scroll) document.fireEvent("onmousemove");
		}
	}
	else if(wpos + parseInt(make_intrabox().style.height) > (document.all?document.documentElement.clientHeight:window.innerHeight)) { //If part of the ibox goes below the screen, scroll down.
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
function doIntraboxMinimize(boxid) {
	var content = document.getElementById("boxcontent_" + boxid);
	if(content.style.display == "none") {
		content.style.display = "block";
	}
	else {
		content.style.display = "none";
	}
	sendReq("minimize/" + boxid);
}
function make_intrabox() {
	if(!m_ibox) {
		m_ibox = document.createElement("div");
		m_ibox.style.display = "none";
		m_ibox.style.position = "absolute";
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
			if(boxes[i] == this.parentNode) {
				offset_height = boxes[i].offsetHeight;
			}
			boxes[i].pagePosLeft = getLeft(boxes[i]);
			boxes[i].pagePosTop = getTop(boxes[i]) - offset_height;
		}
		var m_i = make_intrabox();
		m_i.style.left = getLeft(this.parentNode) + "px";
		m_i.style.top = getTop(this.parentNode) + "px";
		m_i.style.height = this.parentNode.offsetHeight;
		m_i.style.width = this.parentNode.offsetWidth;
		m_i.style.opacity = 0.8;
		m_i.style.filter = "alpha(opacity=80)";
		m_i.innerHTML = this.parentNode.innerHTML;
		m_i.style.border = this.parentNode.style.border;
		m_i.style.borderWidth = this.parentNode.style.borderWidth;
		m_i.style.borderStyle = this.parentNode.style.borderStyle;
		m_i.style.borderColor = this.parentNode.style.borderColor;
		m_i.className = this.parentNode.className;
		m_i.style.display="block";
		this.parentNode.style.visibility="hidden";
		this.parentNode.dragged = false;
	};
	box.onDrag = function(new_left, new_top) {
		var box = null;
		var max = 100000000;
		for(var i = 0; i < boxes.length; i++) {
			if(boxes[i] == this.parentNode) {
				if(box_index == -1) {
					box_index = i;
				}
				continue;
			}
			var dist = Math.sqrt(Math.pow(new_left - boxes[i].pagePosLeft, 2) + Math.pow(new_top - boxes[i].pagePosTop, 2));
			if(isNaN(dist))
				continue;
			if(dist < max) {
				max = dist;
				box = boxes[i];
				to_box_index = i;
			}
		}
		if(box != null && this.nextSibling != box) {
			box.parentNode.insertBefore(this.parentNode, box);
			this.parentNode.style.display = "none";
			this.parentNode.style.display = "";
		}
		this.dragged = true;
	};
	box.onDragEnd = function() {
		if(this.dragged) {
			sendReq("move/" + this.parentNode.id.replace(/intrabox_/, "") + "/" + (to_box_index - box_index - 1));
/*OK, this code rotates the box indexes. It looks like, without this code,
 *if you tried to move more than one box between page reloads, it would break.
 *But, somehow, it works anyway, and this is what's breaking IE, and commenting
 *it out makes drag-and-drop work in IE and doesn't appear to do any harm in
 *Mozilla, so for now, it stays commented out.
			var add = box_index < to_box_index-1 ? 1 : -1;
			for (var i = box_index; i != to_box_index-1; i+=add) {
				boxes[i] = boxes[i+add];
			}
			boxes[to_box_index-1] = this.parentNode;*/
			box_index = -1;
			var dist = 15;
			var left = parseInt(make_intrabox().style.left);
			var top = parseInt(make_intrabox().style.top);
			var left_increment = (left - getLeft(this.parentNode)) / dist;
			var top_increment = (top - getTop(this.parentNode)) / dist;
			var box = this.parentNode;
			clearInterval(interval);
			interval = setInterval(function() {
				if(dist < 1) {
					clearInterval(interval);
					make_intrabox().style.display = "none";
					return;
				}
				dist--;
				left -= left_increment;
				top -= top_increment;
				make_intrabox().style.left = left + "px";
				make_intrabox().style.top = top + "px";
			}, 10);
			this.parentNode.style.visibility = "visible"
		}
		else {
			if(this.href) {
				make_intrabox().style.display = "none";
				this.parentNode.style.visibility = "visible";
				if(this.target) {
					window.open(this.href, this.target);
				}
				else {
					document.location = this.href;
				}
			}
		}
	};
}
