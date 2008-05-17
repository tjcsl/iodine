var comment_bid = -1;
var action_bid = -1;
function show_comment_dialog(e, bid) {
	comment_bid = bid;
	var dialog = document.createElement("div");
	dialog.style.position = "absolute";
	dialog.style.left = (e.pageX - 314) + "px";
	dialog.style.top = e.pageY + "px";
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
	cancel_button.style.fontSize = "16px";
	cancel_button.style.MozUserSelect = "none";
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
function do_action(action, bid, data, e) {
	if(bid && !data) {
		var unschedule_id = document.getElementById("unschedule_" + bid);
		var cancel_id = document.getElementById("cancel_" + bid);
		var room_id = document.getElementById("room_" + bid);
		var sponsor_id = document.getElementById("sponsor_" + bid);
		var check_id = document.getElementById("check_" + bid);
		var status_id = document.getElementById("status_" + bid);
		var activity_status_id = document.getElementById("activity_status_" + bid);
	}
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
    else if(action == "cancel") {
		if(cancel_id.innerHTML == "Cancel") {
			cancel_id.innerHTML = "Uncancel";
			activity_status_id.value = "CANCELLED";
		}
		else {
			cancel_id.innerHTML = "Cancel";
			activity_status_id.value = "SCHEDULED";
		}
    }
	else if(action == "view_rooms") {
		var room_pane = document.getElementById("eighth_room_pane");
		room_pane.style.display = "block";
		room_pane.style.left = (e.pageX - room_pane.offsetParent.offsetLeft) + "px";
		room_pane.style.top = (e.pageY - room_pane.offsetParent.offsetTop) + "px";
		action_bid = bid;
	}
	else if(action == "view_sponsors") {
		var sponsor_pane = document.getElementById("eighth_sponsor_pane");
		sponsor_pane.style.display = "block";
		sponsor_pane.style.left = (e.pageX - sponsor_pane.offsetParent.offsetLeft) + "px";
		sponsor_pane.style.top = (e.pageY - sponsor_pane.offsetParent.offsetTop) + "px";
		action_bid = bid;
	}
	else if(action == "add_room") {
		var rooms = document.getElementById("room_list_" + action_bid);
		var room_list = document.getElementById("div_room_list_" + action_bid);
		var list_of_rooms = rooms.value.split(",");
		list_of_rooms.push(data.value);
		rooms.value = list_of_rooms.join(",");
		room_list.innerHTML += data.innerHTML + " <a href=\"#" + action_bid + "\" onclick=\"do_action('remove_room', " + action_bid + ", " + data.value + ", event)\">Remove</a><br />";
		var room_pane = document.getElementById("eighth_room_pane");
		room_pane.style.display = "none";
		action_bid = -1;
	}
	else if(action == "add_sponsor") {
		var sponsors = document.getElementById("sponsor_list_" + action_bid);
		var sponsor_list = document.getElementById("div_sponsor_list_" + action_bid);
		var list_of_sponsors = sponsors.value.split(",");
		list_of_sponsors.push(data.value);
		sponsors.value = list_of_sponsors.join(",");
		sponsor_list.innerHTML += data.innerHTML + " <a href=\"#" + action_bid + "\" onclick=\"do_action('remove_sponsor', " + action_bid + ", " + data.value + ", event)\">Remove</a><br />";
		var sponsor_pane = document.getElementById("eighth_sponsor_pane");
		sponsor_pane.style.display = "none";
		action_bid = -1;
	}
	else if(action == "remove_room") {
		var rooms = document.getElementById("room_list_" + bid);
		var room_list = document.getElementById("div_room_list_" + bid);
		var list_of_rooms = rooms.value.split(",");
		for(var i = 0; i < list_of_rooms.length; i++) {
			if(list_of_rooms[i] == data) {
				list_of_rooms.splice(i,1);
				break;
			}
		}
		var remove_btn = e.srcElement;
		remove_btn.parentNode.removeChild(remove_btn.nextSibling);
		remove_btn.parentNode.removeChild(remove_btn.previousSibling);
		remove_btn.parentNode.removeChild(remove_btn);
		rooms.value = list_of_rooms.join(",");
	}
	else if(action == "remove_sponsor") {
		var sponsors = document.getElementById("sponsor_list_" + bid);
		var sponsor_list = document.getElementById("div_sponsor_list_" + bid);
		var list_of_sponsors = sponsors.value.split(",");
		for(var i = 0; i < list_of_sponsors.length; i++) {
			if(list_of_sponsors[i] == data) {
				list_of_sponsors.splice(i,1);
				break;
			}
		}
		var remove_btn = e.srcElement;
		remove_btn.parentNode.removeChild(remove_btn.nextSibling);
		remove_btn.parentNode.removeChild(remove_btn.previousSibling);
		remove_btn.parentNode.removeChild(remove_btn);
		sponsors.value = list_of_sponsors.join(",");
	}
	else if(action == "set_default_rooms") {
		var rooms = document.getElementById("room_list_" + bid);
		var room_list = document.getElementById("div_room_list_" + bid);
		rooms.value = data[0].join(",");
		for(room in data[1]) {
			room_list.innerHTML += data[1][room] + " <a href=\"#\" onclick=\"do_action('remove_room', " + bid + ", " + data[0][room] + ", event)\">Remove</a><br />";
		}
	}
	else if(action == "set_default_sponsors") {
		var sponsors = document.getElementById("sponsor_list_" + bid);
		var sponsor_list = document.getElementById("div_sponsor_list_" + bid);
		sponsors.value = data[0].join(",");
		for(sponsor in data[1]) {
			sponsor_list.innerHTML += data[1][sponsor] + " <a href=\"#\" onclick=\"do_action('remove_sponsor', " + bid + ", " + data[0][sponsor] + ", event)\">Remove</a><br />";
		}
	}
	else if(action == "propagate") {
		for(block in unscheduled_blocks) {
			var ubid = unscheduled_blocks[block];
			var rooms_from = document.getElementById("room_list_" + bid);
			var room_list_from = document.getElementById("div_room_list_" + bid);
			var sponsors_from = document.getElementById("sponsor_list_" + bid);
			var sponsor_list_from = document.getElementById("div_sponsor_list_" + bid);
			var rooms_to = document.getElementById("room_list_" + ubid);
			var room_list_to = document.getElementById("div_room_list_" + ubid);
			var sponsors_to = document.getElementById("sponsor_list_" + ubid);
			var sponsor_list_to = document.getElementById("div_sponsor_list_" + ubid);
			if(rooms_to.value == "") {
				rooms_to.value = rooms_from.value;
				room_list_to.innerHTML = room_list_from.innerHTML;
			}
			if(sponsors_to.value == "") {
				sponsors_to.value = sponsors_from.value;
				sponsor_list_to.innerHTML = sponsor_list_from.innerHTML;
			}
		}
	}
	return false;
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
