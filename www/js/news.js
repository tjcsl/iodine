function doNewsShade(nid) {
	var content = document.getElementById("newsitem_" + nid).parentElement;
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
	http.open('GET', news_root + info); // To whoever wrote this line, you are missing an argument.  Sincerely, Zachary Yaro
	http.onreadystatechange = newsHandleResponse;
	http.send(null);
}
function newsHandleResponse() {
	if(http.readyState == 4) {
		var response = http.responseText;
	}
}

function newsLike(nid) {
	var likeXHR;
	if (window.XMLHttpRequest) { // try a normal XHR
		likeXHR = new XMLHttpRequest();
	} else if (window.ActiveXObject) { // for IE version 6 and older
		likeXHR = new ActiveXObject("Microsoft.XMLHTTP");
	} else { // this should never happen
		alert("You cannot like news posts because your browser does not support XMLHttpRequests.  Please try a different browser.");
	}
	likeXHR.open("GET", news_root + "like/" + nid, true);
	likeXHR.onreadystatechange = function() {
		if (likeXHR.readyState == 4) {
			if (likeXHR.status == 200) {
				var likeBtnElem = document.getElementById("likebtn" + nid);
				var likeCountElem = document.getElementById("likecount" + nid);
				var likeCount = parseInt(likeCountElem.innerHTML.substring(0, likeCountElem.innerHTML.indexOf(" ")));
				if (likeBtnElem.innerHTML.indexOf("Un") == -1) {
					likeBtnElem.innerHTML = "Unlike";
					likeBtnElem.className = "newslikebtn newslikebtnliked";
					likeCount++;
				} else {
					likeBtnElem.innerHTML = "Like";
					likeBtnElem.className = "newslikebtn newslikebtnunliked";
					likeCount--;
				}
				likeCountElem.innerHTML = likeCount + " " + (likeCount == 1 ? "person" : "people") + " liked this";
			}
		}
	}
	likeXHR.send(null);
}
