<div style="font-style: italic; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('italic')">Italic</div>
<div style="font-weight: bold; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('bold')">Bold</div>
<div style="text-decoration: underline; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('underline')">Underline</div>
<div style="text-decoration: line-through; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('strikethrough')">Strikethrough</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('inserthorizontalrule')">Horizontal Rule</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('superscript')">Superscript</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('subscript')">Subscript</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="dohref()">Hyperlink</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('unlink')">Unlink text</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doimg()">Image</div><br /><br />
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('forecolor')">Text Color</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('backcolor')">Back Color</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('hilitecolor')">Highlight Color</div><br /><br />
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyleft')">Justify Left</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyright')">Justify Right</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifycenter')">Justify Center</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyfull')">Justify Full</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('insertorderedlist')">Ordered List</div>
<div style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('insertunorderedlist')">Unordered List</div>
<div id="htmlswitcher" style="border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="edithtml()">Edit HTML</div><br /><br />
<textarea id="RichHTML" style="visibility: hidden; width: 99%; height: 0px"></textarea>
<iframe id="RichForm" width="99%" height="200" src="javascript:"""></iframe><br />
<script type="text/javascript">
var formfield = null;
var form = "form";
window.onload = function() {
	formfield = document.getElementById('RichForm').contentWindow.document;
	document.getElementById('RichForm').contentWindow.document.designMode='on';
	var subhead = formfield.getElementsByTagName("head")[0];
	var css = formfield.createElement("link");
	css.setAttribute("rel", "stylesheet");
	css.setAttribute("type", "text/css");
	css.setAttribute("href", "[<$I2_CSS>]");
	subhead.appendChild(css);
}
/*function initrichform() {
	formfield = document.getElementById('RichForm').contentWindow.document;
	document.getElementById('RichForm').contentWindow.document.designMode='on';
	var subhead = formfield.getElementsByTagName("head")[0];
	var css = formfield.createElement("link");
	css.setAttribute("rel", "stylesheet");
	css.setAttribute("type", "text/css");
	css.setAttribute("href", "[<$I2_CSS>]");
	subhead.appendChild(css);
}*/
function doonsubmit() {
	if(form == "form")
		document.getElementById("text").value=formfield.body.innerHTML;
	else
		document.getElementById("text").value=document.getElementById("RichHTML").value;
}
function doit(action) {
//	if(!formfield) initrichform();
	formfield.execCommand(action,false,null);
}
function dohref() {
//	if(!formfield) initrichform();
	var url = prompt("Input link url:","http://");
	if(url != null && url != "")
		formfield.execCommand("CreateLink",false,url);
}
function doimg() {
//	if(!formfield) initrichform();
	var url = prompt("Input image url:","http://");
	if(url != null && url != "")
		formfield.execCommand("InsertImage",false,url);
}
function docolor(where) {
//	if(!formfield) initrichform();
	var color = prompt("Input color in hex (e.g. 00bcff):","");
	if(color != null && color != "")
		formfield.execCommand(where,false,color);
}
function edithtml() {
//	if(!formfield) initrichform();
	document.getElementById('RichHTML').style.visibility="visible";
	document.getElementById('RichHTML').style.height="120px";
	document.getElementById('RichHTML').value = formfield.body.innerHTML;
	document.getElementById('RichForm').style.display="none";
	form="html";
	document.getElementById('htmlswitcher').childNodes[0].data="Hide HTML";
	document.getElementById('htmlswitcher').onclick=hidehtml;
	//formfield.body.innerHTML = prompt("Input any raw html:",formfield.body.innerHTML);
}
function hidehtml() {
//	if(!formfield) initrichform();
	document.getElementById('RichHTML').style.visibility="hidden";
	document.getElementById('RichHTML').style.height="0px";
	formfield.body.innerHTML = document.getElementById('RichHTML').value;
	document.getElementById('RichForm').style.display="block";
	form="form";
	document.getElementById('htmlswitcher').childNodes[0].data="Show HTML";
	document.getElementById('htmlswitcher').onclick=edithtml;
}
</script>
