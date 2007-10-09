function doNewsShade(nid) {
	var content = document.getElementById("newsitem_" + nid);
	if(content.style.display == "none") {
		content.style.display = "block";
	} else {
		content.style.display = "none";
	}
	var shadelink = document.getElementById("shadelink_" + nid);
	if(shadelink.innerHTML == "Expand") {
		shadelink.innerHTML = "Collapse";
	} else {
		shadelink.innerHTML= "Expand";
	}
	newsSendReq("shade/" + nid);
	return false;
}
function newsSendReq(info) {
	http.open('GET', news_root + info);
	http.onreadystatechange = newsHandleResponse;
	http.send(null);
}
function newsHandleResponse() {
	if(http.readyState == 4) {
		var response = http.responseText;
	}
}
