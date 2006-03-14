<script type="text/javascript">
var load_page = "[<$I2_ROOT>]scratchpad/load/";
var save_page = "[<$I2_ROOT>]scratchpad/save/";

window.onload = function(){
	// load the text dynamically
	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
		http_request = new XMLHttpRequest();
		http_request.overrideMimeType('text/xml');
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsof.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		return false;
	}
	http_request.onreadystatechange = function() {
		if(http_request.readyState == 4 && http_request.status == 200) {
			info = http_request.responseText;
			if(document.getElementById) {
				document.getElementById("scratchtext").value = info;
			}
			else {
				document.scratchtext.value = info;
			}
		}
	};
	http_request.open('GET', load_page, true);
	http_request.send(null);
}

window.onunload = function(){
	var info;
	if(document.getElementById) {
		info = document.getElementById("scratchtext").value;
	}
	else {
		info = document.scratchtext.value;
	}
	if(info == "") return;
	http.open('GET', save_page+escape(info));
	http.onreadystatechange = handleResponse;
	http.send(null);
}
</script>
<textarea id="scratchtext" width=20 height=15></textarea>
