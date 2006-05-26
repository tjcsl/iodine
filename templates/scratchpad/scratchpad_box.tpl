<script type="text/javascript">
var load_page = "[<$I2_ROOT>]scratchpad/load/";
var save_page = "[<$I2_ROOT>]scratchpad/save/";
var chflag=0;

window.onload = function(){
	// load the text dynamically
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200) {
			if(document.getElementById) {
				document.getElementById("scratchtext").value = unescape(http.responseText).replace(/\\n/g,"\n");
			}
			else {
				document.scratchtext.value = unescape(http.responseText).replace(/\\n/g,"\n");
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
	if(!chflag) return; //Don't bother saving null changes.
//	alert(escape(info));
	http.open('GET', save_page+escape(info.replace(/\n/g,"\\n")));
	http.onreadystatechange = handleResponse;
	http.send(null);
}
</script>
<textarea id="scratchtext" onclick="chflag=1;" style="width:98%;height:150px">[<$text>]</textarea><br>
[<*<a href="[<$I2_ROOT>]scratchpad/help/">What Is This Box For?</a>*>]
