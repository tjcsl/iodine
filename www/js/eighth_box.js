var rowIndEighth = null;

function eighth_box_options(anchor) {
	var url = anchor.href;
	var bid = url.substring(url.indexOf("bids")+5,url.indexOf("/",url.indexOf("bids")+5));
	var aid = url.substring(url.indexOf("aid")+4,url.indexOf("/",url.indexOf("aid")+4));
	var uid = url.substring(url.indexOf("uid")+4,url.indexOf("/",url.indexOf("uid")+4));
	var eighthroot = url.substr(0,url.indexOf("vcp_schedule"));
	var row = anchor.parentNode.parentNode;
	var table = row.parentNode;
	var cell;
	if (rowIndEighth != null) {
		table.deleteRow(rowIndEighth);
	}
	if (rowIndEighth == null || rowIndEighth != row.rowIndex+1) {
		row = table.insertRow(row.rowIndex+1);
		cell = row.insertCell(0);
		cell.id = "eighth_box_options";
		cell.colSpan = "3";
		cell.innerHTML = 
		"&nbsp;&nbsp; <a href=\"" + eighthroot + "vcp_schedule/roster/bid/" + bid + "/aid/" + aid + "\">View roster for current activity</a><br />" +
		"&nbsp;&nbsp; <a href=\"" + eighthroot + "vcp_schedule/choose/uid/" + uid + "/bids/" + bid + "\">Change activity/View all rosters</a><br />";
		rowIndEighth = row.rowIndex;
	} else {
		rowIndEighth = null;
	}
	return false;
}
