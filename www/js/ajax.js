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
   // FIXME: should account for a different www_root
   // At least this takes care of development copies on the iodine server,
   // but the ajax requests should work using any value value of www_root.
	url = window.location.href.split("/");
   path = "/"
   if(url[4] == "i2") {
      path = "/" + url[3] + "/" + url[4] + "/"
   }
   http.open('GET', path + 'ajax/' + info);
	http.send(null);
}
