<input type="button" style="font-style: italic; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('italic')" value="Italic" />
<input type="button" style="font-weight: bold; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('bold')" value="Bold" />
<input type="button" style="text-decoration: underline; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('underline')" value="Underline" />
<input type="button" style="text-decoration: line-through; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('strikethrough')" value="Strikethrough" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('inserthorizontalrule')" value="Horizontal Rule" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('superscript')" value="Superscript" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('subscript')" value="Subscript" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="dohref()" value="Hyperlink" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('unlink')" value="Unlink text" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doimg()" value="Image" /><br /><br />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('forecolor')" value="Text Color" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('backcolor')" value="Back Color" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('hilitecolor')" value="Highlight Color" /><br /><br />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyleft')" value="Justify Left" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyright')" value="Justify Right" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifycenter')" value="Justify Center" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyfull')" value="Justify Full"/>
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('insertorderedlist')" value="Ordered List" />
<input type="button" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('insertunorderedlist')" value="Unordered List" />
<input type="button" id="htmlswitcher" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="edithtml()" value="Edit HTML" /><br /><br />
<textarea id="RichHTML" style="visibility: hidden; width: 99%; height: 0px"></textarea>
<iframe id="RichForm" width="99%" height="200" src="[<$I2_ROOT>]richedit"></iframe><br />
<script type="text/javascript">
var formfield = null;
var form = "form";
window.onload = function() {
	formfield = document.getElementById('RichForm').contentWindow.document;
	document.getElementById('RichForm').contentWindow.document.designMode='on';
	// Need the next line for ie support :(
	formfield = document.getElementById('RichForm').contentWindow.document;
	// End of ie redundancy
	page_init(); // Allow the theme-specific stuff to run.
}
function doonsubmit() {
	if(form == "form")
		document.getElementById("text").value=formfield.body.innerHTML;
	else
		document.getElementById("text").value=document.getElementById("RichHTML").value;
}
function doit(action) {
	if(!document.all) { // W3C Compliant browsers
		formfield.execCommand(action,false,null);
	} else { // IE
		var selection = formfield.selection.createRange();
		selection.execCommand(action);
		selection.select();
		document.getElementById('RichForm').contentWindow.focus();
	}
}
function dohref() {
	var url = prompt("Input link url:","http://");
	if(url != null && url != "")
		formfield.execCommand("CreateLink",false,url);
}
function doimg() {
	var url = prompt("Input image url:","http://");
	if(url != null && url != "") {
		if(document.all) // IE fix
			document.getElementById('RichForm').contentWindow.focus();
		formfield.execCommand("InsertImage",false,url);
	}
}
function docolor(where) {
	var color = prompt("Input color in hex (e.g. 00bcff):","");
	if(color != null && color != "")
		formfield.execCommand(where,false,color);
}
function edithtml() {
	document.getElementById('RichHTML').style.visibility="visible";
	document.getElementById('RichHTML').style.height="120px";
	document.getElementById('RichHTML').value = formfield.body.innerHTML;
	document.getElementById('RichForm').style.display="none";
	form="html";
	document.getElementById('htmlswitcher').value="Hide HTML";
	document.getElementById('htmlswitcher').onclick=hidehtml;
}
function hidehtml() {
	document.getElementById('RichHTML').style.visibility="hidden";
	document.getElementById('RichHTML').style.height="0px";
	formfield.body.innerHTML = document.getElementById('RichHTML').value;
	document.getElementById('RichForm').style.display="block";
	form="form";
	document.getElementById('htmlswitcher').value="Show HTML";
	document.getElementById('htmlswitcher').onclick=edithtml;
}
</script>
