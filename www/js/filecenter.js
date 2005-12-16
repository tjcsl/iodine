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
		if (type == 'file') {
			cell = row.insertCell(-1);
			cell.id = "options";
			cell.colSpan = "4";
			cell.innerHTML = 
			"<a href='" + url + "'>Download file</a><br/>" + 
			"<a href='" + url + "?download=zip'>Download file as ZIP</a><br/>" + 
			"<a href=\"javascript:rename('" + file + "')\">Rename file</a><br/>" + 
			"<a href='#'>Delete file</a>";
		} else {
			cell = row.insertCell(-1);
			cell.id = "options";
			cell.colSpan = "4";
			cell.innerHTML = 
			"<a href='" + url + "/'>Open directory</a><br/>" + 
			"<a href='" + url + "/?download'>Download directory as ZIP</a><br/>" + 
			"<a href=\"javascript:rename('" + file + "')\">Rename directory</a><br/>" +
			"<a href='#'>Delete directory</a>";
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
