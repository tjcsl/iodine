var max_height = 200;

function show_help(height) {
	var help_pane = document.getElementById("eighth_help_pane");
	var	blocker_pane = document.getElementById("eighth_help_blocker");
	if(height == 0) {
		help_pane.style.display = "block";
		help_pane.style.padding = "10px";
		blocker_pane.style.display = "block";
	}
	help_pane.style.height = height + "px";
	help_pane.style.width = 2 * height + "px";
	help_pane.style.top = 20 + height / 2 + "px";
	help_pane.style.left = height / 2 + "px";
	help_pane.style.opacity = height / max_height;
	help_pane.style.filter = "alpha(opacity=" + (100 * height / max_height) + ")";
	blocker_pane.style.opacity = height / (2 * max_height);
	blocker_pane.style.filter = "alpha(opacity=" + (50 * height / max_height) + ")";
	if(height < max_height) {
		setTimeout("show_help(" + (height + 20) + ")", 5);
	}
	else {
	}
}

function hide_help(height) {
	var help_pane = document.getElementById("eighth_help_pane");
	var	blocker_pane = document.getElementById("eighth_help_blocker");
	help_pane.style.height = height + "px";
	help_pane.style.width = 2 * height + "px";
	help_pane.style.top = 20 + height / 2 + "px";
	help_pane.style.left = height / 2 + "px";
	help_pane.style.opacity = height / max_height;
	help_pane.style.filter = "alpha(opacity=" + (100 * height / max_height) + ")";
	blocker_pane.style.opacity = height / (2 * max_height);
	blocker_pane.style.filter = "alpha(opacity=" + (50 * height / max_height) + ")";
	if(height > 0) {
		setTimeout("hide_help(" + (height - 20) + ")", 5);
	}
	else {
		help_pane.style.padding = "0px";
		help_pane.style.display = "none";
		blocker_pane.style.display = "none";
	}
}

function move_help(e) {
	if(!e) var e = window.event;
	alert("hello");
}
