<div name="Placeholder"></div>
</div>
<script language="javascript" type="text/javascript">
	var m_ibox = null;
	var drag_box = null;
	var box_after = null;
	var interval = 0;
	var boxes = document.getElementById('intraboxes').childNodes;
	for(var i = 0; i < boxes.length - 1; i++) {
		init(boxes[i]);
	}
	function doIntraboxDown(e) {
		e = fixE(e);
		var target = null;
		if(e.target) {
			target = e.target;
		}
		else if(e.srcElement) {
			target = e.srcElement;
		}
		if(!target.href) {
			drag_box = this;
			drag_box.onDragStart();
			this.lastMouseX = e.clientX;
			this.lastMouseY = e.clientY;
			document.onmousemove = doIntraboxMove;
			document.onmouseup = doIntraboxPlace;
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
		make_intrabox().style.left = new_left + "px";
		make_intrabox().style.top = new_top + "px";
		drag_box.lastMouseX = x;
		drag_box.lastMouseY = y;
		drag_box.onDrag(new_left, new_top);
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
		if(typeof e == "undefined")
			e = window.event;
		if(typeof e.layerX == "undefined")
			e.layerX = e.offsetX;
		if(typeof e.layerY == "undefined")
			e.layerY = e.offsetY;
		return e;
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
		http.open('get', '[<$I2_ROOT>]ajax/intrabox/[<$I2_UID>]/'+info);
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
			m_i.style.display = "block";
			m_i.style.opacity = 0.8;
			m_i.style.filter = "alpha(opacity=80)";
			m_i.innerHTML = this.innerHTML;
			m_i.style.border = this.style.border;
			this.innerHTML = "";
			this.style.borderWidth = "2px";
			this.style.borderStyle = "solid";
			this.style.width = (parseInt(this.style.width) - 4) + "px";
			m_i.className = this.className;
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
			if(box != null && this.nextSibling != box) {
				box_after = box;
				box.parentNode.insertBefore(this, box);
				this.parentNode.style.display = "none";
				this.parentNode.style.display = "";
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
				this.style.border = make_intrabox().style.border;
				this.style.width = (parseInt(this.style.width) + 4) + "px";
				this.innerHTML = make_intrabox().innerHTML;
			}
			else {
				if(this.href) {
					make_intrabox().style.display = "none";
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
</script>
