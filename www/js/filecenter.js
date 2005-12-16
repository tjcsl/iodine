var rowIndex = null;

function options(anchor, type) {
	var file = anchor.innerHTML;
	var url = escape(file);
	var row = anchor.parentNode.parentNode;
	var table = row.parentNode;
	if (rowIndex != null) {
		table.deleteRow(rowIndex-1);
	}
	if (rowIndex == null || rowIndex != row.rowIndex+1) {
		row = table.insertRow(row.rowIndex);
		if (type == 'file') {
			row.innerHTML = 
			"<td>&nbsp;</td><td id='options' colspan='4'>" +
			"<a href='" + url + "'>Download file</a><br/>" + 
			"<a href='" + url + "?download=zip'>Download file as ZIP</a><br/>" + 
			"<a href=\"javascript:rename('" + file + "')\">Rename file</a><br/>" + 
			"<a href='#'>Delete file</a></td>";
		} else {
			row.innerHTML = 
			"<td>&nbsp;</td><td id='options' colspan='4'>" +
			"<a href='" + url + "/'>Open directory</a><br/>" + 
			"<a href='" + url + "/?download'>Download directory as ZIP</a><br/>" + 
			"<a href=\"javascript:rename('" + file + "')\">Rename directory</a><br/>" +
			"<a href='#'>Delete directory</a></td>";	
		}
		rowIndex = row.rowIndex;
	} else {
		rowIndex = null;
	}
}

function rename(file) {
	var name = prompt("Rename", file);
	if (name == null) {
		return;
	}
	window.location = window.location + "?rename=" + escape(file) + "&to=" + escape(name);
}
