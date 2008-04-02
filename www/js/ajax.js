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
function sendReq(http, info) {
	http.open('GET', '/ajax/' + info);
	http.send(null);
}

