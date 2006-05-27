<script type="text/javascript">
var load_page = "[<$I2_ROOT>]scratchpad/load/";
var save_page = "[<$I2_ROOT>]scratchpad/save/";
var chflag=0;

window.onload = function(){
	// load the text dynamically
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200) {
			var temp = http.responseText.replace(/\ue000/g,"+").split("&");
			for(var i=1;i<=temp.length;i++) {
				document.getElementById("text"+i).value = temp[i-1].replace(/\ue001/g,"&");
			}
		}
	};
	http.open('GET', load_page, true);
	http.send(null);
}

window.onunload = function(){
	if(!chflag) return; //Don't bother saving null changes.
	var info = document.getElementById("text1").value;
	//alert(escape(info));
	http.open('POST', save_page,false);
	http.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	http.onreadystatechange = handleResponse;
	http.send('text='+info.replace(/\+/g,"\ue000").replace(/\&/g,"\ue001"));
}
</script>
<textarea id="text1" onclick="chflag=1;" style="width:98%;height:150px">[<$text>]</textarea><br>
[<*<a href="[<$I2_ROOT>]scratchpad/help/">What Is This Box For?</a>*>]
