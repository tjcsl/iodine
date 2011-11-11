<button type="button" style="height:25px; font-style: italic; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('italic')">I</button>
<button type="button" style="height:25px; font-weight: bold; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('bold')">B</button>
<button type="button" style="height:25px; text-decoration: underline; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('underline')">U</button>
<button type="button" style="height:25px; text-decoration: line-through; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('strikethrough')">St</button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('inserthorizontalrule')">HR</button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('superscript')">x<sup>y<sup></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('subscript')">x<sub>y</sub></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left; text-decoration: underline; color:blue" onclick="dohref()">link</button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('unlink')">Unlink</button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doimg()">Image</button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left; color: red" onclick="docolor('forecolor')">T</button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('backcolor')"><div style="width:11px; height:11px; border: 1px solid black; padding:2px; background-color:red"></div></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="docolor('hilitecolor')"><div style="width:16px; height:16px; border: padding:2px; background-color:yellow">T</div></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyleft')"><img src="[<$I2_ROOT>]www/pics/richedit/left-align.gif"/></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyright')"><img src="[<$I2_ROOT>]www/pics/richedit/right-align.gif"/></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifycenter')"><img src="[<$I2_ROOT>]www/pics/richedit/center-align.gif"/></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('justifyfull')"><img src="[<$I2_ROOT>]www/pics/richedit/justified-align.gif"/></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('insertorderedlist')"><img src="[<$I2_ROOT>]www/pics/richedit/numbered-list.gif"/></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="doit('insertunorderedlist')"><img src="[<$I2_ROOT>]www/pics/richedit/bulleted-list.gif"/></button>
<button type="button" style="height:25px; border: 1px solid; padding-left: 2px; padding-right: 2px; float: left" onclick="edithtml()" id="htmlswitcher">&lt;html&gt;</button>
<textarea id="RichHTML" style="visibility: hidden; width: 99%; height: 0px; padding:0px; margin:0px; border-width:0px" rows="10" cols="400"></textarea>
<iframe id="RichForm" width="99%" height="200" src="[<$I2_ROOT>]richedit"></iframe><br />
<script type="text/javascript">
var formfield = null;
var form = "form";
window.addEventListener("load", function() {
	formfield = document.getElementById('RichForm').contentWindow.document;
	document.getElementById('RichForm').contentWindow.document.designMode='on';
	// Need the next line for IE support :(
	formfield = document.getElementById('RichForm').contentWindow.document;
	// End of IE redundancy
}, false);
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
