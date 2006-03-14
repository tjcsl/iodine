<script type="text/javascript">
var save_page = "[<$I2_ROOT>]savepad/";
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
<textarea id="scratchtext" width=20 height=15>[<$text>]</textarea>
