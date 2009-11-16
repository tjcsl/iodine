<script type="text/javascript">
formfield = null;
window.onload=function() {
	formfield = document.getElementById('RichForm').contentWindow.document;
	formfield.designMode='on';
}
function doonsubmit() {
	document.getElementById("text").value=formfield.body.innerHTML;
}
function doit(action) {
	formfield.execCommand(action,false,null);
}
function dohref() {
	var url = prompt("Input link url:","http://");
	if(url != null && url != "")
		formfield.execCommand("CreateLink",false,url);
}
function doimg() {
	var url = prompt("Input image url:","http://");
	if(url != null && url != "")
		formfield.execCommand("InsertImage",false,url);
}
function docolor(where) {
	var color = prompt("Input color in hex (e.g. 00bcff):","");
	if(color != null && color != "")
		formfield.execCommand(where,false,color);
}
</script>
<div style="font-style: italic; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('italic')">Italic</div>
<div style="font-weight: bold; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('bold')">Bold</div>
<div style="text-decoration: underline; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('underline')">Underline</div>
<div style="text-decoration: line-through; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('strikethrough')">Strikethrough</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="dohref()">Hyperlink</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doimg()">Image</div><br /><br />
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('forecolor')">Text Color</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('backcolor')">Back Color</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('hilitecolor')">Highlight Color</div><br /><br />
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyleft')">Justify Left</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyright')">Justify Right</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifycenter')">Justify Center</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyfull')">Justify Full</div>
<iframe id="RichForm" width="90%" height="120"></iframe><br />
