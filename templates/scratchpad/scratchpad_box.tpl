<script type="text/javascript">
var load_page = "[<$I2_ROOT>]scratchpad/load/";
var save_page = "[<$I2_ROOT>]scratchpad/save/";

window.onload = function(){
	// load the text dynamically
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200) {
			if(document.getElementById) {
				document.getElementById("scratchtext").value = http.responseText;
			}
			else {
				document.scratchtext.value = http.responseText;
			}
		}
	};
	http.open('GET', load_page, true);
	http.send(null);
}

window.onunload = function(){
	var info;
	if(document.getElementById) {
		info = document.getElementById("scratchtext").value;
	}
	else {
		info = document.scratchtext.value;
	}
	if(info == "") return; //Don't bother saving an empty pad.
	http.open('GET', save_page+escape(info));
	http.onreadystatechange = handleResponse;
	http.send(null);
}
</script>
<textarea id="scratchtext" style="width:98%;height:150px">[<$text>]</textarea><br>
[<*<a href="[<$I2_ROOT>]scratchpad/help/">What Is This Box For?</a>*>]
