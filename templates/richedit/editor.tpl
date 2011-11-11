<button type="button" class="toolbarbtn" style="font-weight:bold;/*padding-left:0.7em;padding-right:0.7em;*/" onclick="doit('bold')" title="Bold">B</button>
<button type="button" class="toolbarbtn" style="font-style:italic;" onclick="doit('italic')" title="Italic">I</button>
<button type="button" class="toolbarbtn" style="text-decoration:underline;" onclick="doit('underline')" title="Underline">U</button>
<button type="button" class="toolbarbtn" style="text-decoration:line-through;" onclick="doit('strikethrough')" title="Strikethrough">St</button>
<button type="button" class="toolbarbtn" onclick="doit('inserthorizontalrule')" title="Insert horizontal rule">HR</button>
<button type="button" class="toolbarbtn" onclick="doit('superscript')" title="Superscript">x<sup>y<sup></button>
<button type="button" class="toolbarbtn" onclick="doit('subscript')" title="Subscript">x<sub>y</sub></button>
<button type="button" class="toolbarbtn" style="text-decoration: underline; color:blue" onclick="dohref()" title="Set hyperlink">link</button>
<button type="button" class="toolbarbtn" onclick="doit('unlink')" title="Remove hyperlink">Unlink</button>
<button type="button" class="toolbarbtn" onclick="doimg()" title="Embed image">Image</button>
<button type="button" class="toolbarbtn" style="color:red" onclick="docolor('forecolor')" title="Foreground/text color">T</button>
<button type="button" class="toolbarbtn" onclick="docolor('backcolor')" title="Background color"><div style="width:11px; height:11px; border: 1px solid black; padding:2px; background-color:red"></div></button>
<button type="button" class="toolbarbtn" onclick="docolor('hilitecolor')" title="Highlight color"><div style="width:16px; height:16px; border: padding:2px; background-color:yellow">T</div></button>
<button type="button" class="toolbarbtn" onclick="doit('justifyleft')" title="Align left"><img src="[<$I2_ROOT>]www/pics/richedit/left-align.gif"/></button>
<button type="button" class="toolbarbtn" onclick="doit('justifyright')" title="Align right"><img src="[<$I2_ROOT>]www/pics/richedit/right-align.gif"/></button>
<button type="button" class="toolbarbtn" onclick="doit('justifycenter')" title="Align center"><img src="[<$I2_ROOT>]www/pics/richedit/center-align.gif"/></button>
<button type="button" class="toolbarbtn" onclick="doit('justifyfull')" title="Align justified"><img src="[<$I2_ROOT>]www/pics/richedit/justified-align.gif"/></button>
<button type="button" class="toolbarbtn" onclick="doit('insertorderedlist')" title="Ordered list"><img src="[<$I2_ROOT>]www/pics/richedit/numbered-list.gif"/></button>
<button type="button" class="toolbarbtn" onclick="doit('insertunorderedlist')" title="Unordered list"><img src="[<$I2_ROOT>]www/pics/richedit/bulleted-list.gif"/></button>
<button type="button" class="toolbarbtn" style="font-family:monospace;" onclick="edithtml()" id="htmlswitcher" title="Edit HTML">&lt;html&gt;</button>
<textarea id="RichHTML" style="display:none; width:99%; height:0px;" rows="10" cols="400"></textarea>
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
	document.getElementById('RichHTML').style.display="block";
	document.getElementById('RichHTML').style.height="120px";
	document.getElementById('RichHTML').value = formfield.body.innerHTML;
	document.getElementById('RichForm').style.display="none";
	form="html";
	document.getElementById('htmlswitcher').value="Hide HTML";
	document.getElementById('htmlswitcher').onclick=hidehtml;
}
function hidehtml() {
	document.getElementById('RichHTML').style.display="none";
	document.getElementById('RichHTML').style.height="0px";
	formfield.body.innerHTML = document.getElementById('RichHTML').value;
	document.getElementById('RichForm').style.display="block";
	form="form";
	document.getElementById('htmlswitcher').value="Show HTML";
	document.getElementById('htmlswitcher').onclick=edithtml;
}
</script>
