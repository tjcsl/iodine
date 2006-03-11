<script type="text/javascript">
var save_page = '[<$I2_ROOT>]savepad/';

window.onunload=function(){
	var info = document.scratchForm.elements[0].value;
//	alert("Saving "+info);
	if(info == "") return;
	http.open('GET', save_page+escape(info));
	http.onreadystatechange = handleResponse;
	http.send(null);
}
</script>
<form name="scratchForm"><textarea width=20 height=15 >[<$text>]</textarea></form>
