var rowIndex = null;

function options(anchor, type) {
	var file = anchor.innerHTML;
	var url = escape(file);
	var row = anchor.parentNode.parentNode;
	var table = row.parentNode;
	var cell;
	if (rowIndex != null) {
		table.deleteRow(rowIndex-1);
	}
	if (rowIndex == null || rowIndex != row.rowIndex+1) {
		row = table.insertRow(row.rowIndex);
		row.insertCell(-1).innerHTML = "&nbsp;";
		cell = row.insertCell(-1);
		cell.id = "options";
		cell.colSpan = "4";
		if (type == 'file') {
			cell.innerHTML = 
			"<a href=\"" + url + "\">Download file</a><br />" + 
			"<a href=\"" + url + "?download=zip\">Download file as ZIP</a><br />" + 
			"<a href=\"javascript:rename('" + file + "')\">Rename file</a><br />" + 
			"<a href=\"javascript:rmf('" + file + "')\">Delete file</a><br />";
		} else if (type == 'dir') {
			cell.innerHTML = 
			"<a href=\"" + url + "/\">Open directory</a><br />" + 
			"<a href=\"" + url + "/?download\">Download directory as ZIP</a><br />" + 
			"<a href=\"javascript:rename('" + file + "')\">Rename directory</a><br />" +
			"<a href=\"javascript:rmd('" + file + "')\">Delete directory</a><br />";
		} else if (type == 'cur') {
			cell.innerHTML = 
			"<a href=\"?download\">Download directory as ZIP</a><br />";
		}
		rowIndex = row.rowIndex;
	} else {
		rowIndex = null;
	}
	return false;
}

function rename(file) {
	var name = prompt("Rename", file);
	if (name == null) {
		return;
	}
	window.location = window.location + "?rename=" + escape(file) + "&to=" + escape(name);
}

function rmf(file) {
	var name = confirm("Are you sure you want to delete \"" + file + "\"?");
	if (name) {
		window.location = window.location + "?rmf=" + escape(file);
	}
}

function rmd(file) {
	var name = confirm("Are you sure you want to delete \"" + file + "\"?  (Note: Intranet filecenter currently only supports deleting of empty directories; delete the contents of this directory first.)");
	if (name) {
		window.location = window.location + "?rmd=" + escape(file);
	}
}
