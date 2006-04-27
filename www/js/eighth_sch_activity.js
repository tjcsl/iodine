var comment_bid = -1;
function show_comment_dialog(e, bid) {
	comment_bid = bid;
	var dialog = document.createElement("div");
	dialog.style.position = "absolute";
	dialog.style.left = (e.clientX - 314) + "px";
	dialog.style.top = e.clientY + "px";
	dialog.style.width = "300px";
	dialog.style.height = "150px";
	dialog.style.zIndex = 100;
	dialog.style.backgroundColor = "#FFFFCC";
	dialog.style.color = "#000000";
	dialog.style.border = "2px solid #000000";
	dialog.style.padding = "5px";
	var title = document.createElement("div");
	title.innerHTML = "Add a comment:";
	title.style.fontFamily = "sans-serif";
	title.style.fontSize = "12pt";
	title.style.fontWeight = "bold";
	dialog.appendChild(title);
	var comment_area = document.createElement("textarea");
	comment_area.id = "comment_area";
	comment_area.style.width = "288px";
	comment_area.style.height = "85px";
	comment_area.style.backgroundColor = "#FFFFCC";
	comment_area.style.color = "#000000";
	comment_area.style.border = "1px solid #CCCCCC";
	comment_area.style.padding = "5px";
	comment_area.style.fontFamily = "sans-serif";
	comment_area.value = document.getElementById("comment_" + bid).value;
	dialog.appendChild(comment_area);
	var set_button = document.createElement("div");
	set_button.style.border = "2px outset #000000";
	set_button.style.position = "absolute";
	set_button.style.left = "53px";
	set_button.style.bottom = "5px";
	set_button.style.width = "120px";
	set_button.style.height = "20px";
	set_button.style.backgroundColor = "#FFFFFF";
	set_button.style.color = "#000000";
	set_button.innerHTML = "Add Comment";
	set_button.style.textAlign = "center";
	set_button.style.fontWeight = "bold"
	set_button.style.fontSize = "16px";
	set_button.style.MozUserSelect = "none";
	set_button.style.cursor = "default";
	set_button.onmousedown = function() {
		add_comment();
		dialog.style.display = "none";
	};
	dialog.appendChild(set_button);
	var cancel_button = document.createElement("div");
	cancel_button.style.border = "2px outset #000000";
	cancel_button.style.position = "absolute";
	cancel_button.style.left = "183px";
	cancel_button.style.bottom = "5px";
	cancel_button.style.width = "64px";
	cancel_button.style.height = "20px";
	cancel_button.style.backgroundColor = "#FFFFFF";
	cancel_button.style.color = "#000000";
	cancel_button.innerHTML = "Cancel";
	cancel_button.style.textAlign = "center";
	cancel_button.style.fontWeight = "bold"
	cancel_button.style.fontSize = "16px";                                                                                                                                                                             cancel_button.style.MozUserSelect = "none";
	cancel_button.style.cursor = "default";
	cancel_button.onmousedown = function() {
		dialog.style.display = "none";
	};
	dialog.appendChild(cancel_button);
	document.body.appendChild(dialog);
}
function add_comment() {
	if(comment_bid != -1) {
		var comment_field = document.getElementById("comment_" + comment_bid);
		var new_comment = document.getElementById("comment_area").value;
		if(comment_field.value != new_comment) {
			var check = document.getElementById("check_" + comment_bid);
			check.checked = "true";
			comment_field.value = new_comment;
		}
		comment_bid = -1;
	}
}
function do_action(action, bid) {
	var unschedule_id = document.getElementById("unschedule_" + bid);
	var cancel_id = document.getElementById("cancel_" + bid);
	var room_id = document.getElementById("room_" + bid);
	var sponsor_id = document.getElementById("sponsor_" + bid);
	var check_id = document.getElementById("check_" + bid);
	var status_id = document.getElementById("status_" + bid);
	var activity_status_id = document.getElementById("activity_status_" + bid);
	if(action == "unschedule") {
		if(unschedule_id.innerHTML == "Unschedule") {
			cancel_id.style.visibility = "hidden";
			check_id.checked = true;
			activity_status_id.value = "UNSCHEDULED";
			unschedule_id.innerHTML = "Reschedule";
		}
		else {
			cancel_id.style.visibility = "visible";
			check_id.checked = false;
			activity_status_id.value = "SCHEDULED";
		    unschedule_id.innerHTML = "Unschedule";
		}
    }
    else {
		if(cancel_id.innerHTML == "Cancel") {
			cancel_id.innerHTML = "Uncancel";
			check_id.checked = !check_id.checked;
			activity_status_id.value = "CANCELLED";
		}
		else {
			cancel_id.innerHTML = "Cancel";
			check_id.checked = !check_id.checked;
			activity_status_id.value = "SCHEDULED";
		}
    }
}
function CA() {
    var trk = 0;
    for (var i = 0; i < frm.elements.length; i++) {
		var e = frm.elements[i];
		if ((e.name != 'selectall') && (e.type == 'checkbox')) {
			trk++;
			e.checked = frm.selectall.checked;
		}
    }
}
function CCA(CB){
    var TB = TO = 0;
    for (var i = 0; i < frm.elements.length; i++) {
		var e = frm.elements[i];
		if ((e.name != 'selectall') && (e.type == 'checkbox')) {
			TB++;
			if(e.checked) TO++;
		}
	}
	frm.selectall.checked=(TO == TB) ? true : false;
}
