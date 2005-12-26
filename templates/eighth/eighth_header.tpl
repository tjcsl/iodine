<script language="javascript" type="text/javascript">
var max_height = 200;
function show_help(height) {
	var help_pane = document.getElementById("help_pane");
	var	blocker_pane = document.getElementById("blocker");
	if(height == 0) {
		help_pane.style.display = "block";
		help_pane.style.padding = "10px";
		blocker_pane.style.display = "block";
	}
	help_pane.style.height = height + "px";
	help_pane.style.width = 2 * height + "px";
	help_pane.style.top = 20 + height / 2 + "px";
	help_pane.style.left = height / 2 + "px";
	help_pane.style.opacity = height / max_height;
	help_pane.style.filter = "alpha(opacity=" + (100 * height / max_height) + ")";
	blocker_pane.style.opacity = height / (2 * max_height);
	blocker_pane.style.filter = "alpha(opacity=" + (50 * height / max_height) + ")";
	if(height < max_height) {
		setTimeout("show_help(" + (height + 20) + ")", 5);
	}
	else {
	}
}
function hide_help(height) {
	var help_pane = document.getElementById("help_pane");
	var	blocker_pane = document.getElementById("blocker");
	help_pane.style.height = height + "px";
	help_pane.style.width = 2 * height + "px";
	help_pane.style.top = 20 + height / 2 + "px";
	help_pane.style.left = height / 2 + "px";
	help_pane.style.opacity = height / max_height;
	help_pane.style.filter = "alpha(opacity=" + (100 * height / max_height) + ")";
	blocker_pane.style.opacity = height / (2 * max_height);
	blocker_pane.style.filter = "alpha(opacity=" + (50 * height / max_height) + ")";
	if(height > 0) {
		setTimeout("hide_help(" + (height - 20) + ")", 5);
	}
	else {
		help_pane.style.padding = "0px";
		help_pane.style.display = "none";
		blocker_pane.style.display = "none";
	}
}
function move_help(e) {
	if(!e) var e = window.event;
	alert("hello");
}
</script>
<div id="blocker" style="display: none; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px; background-color: #666666;"></div>
<div id="help_pane" onmousedown="register_press(event)" >
<div id="help_close" onclick="hide_help(max_height)">Close</div>
<span class="bold">Help:</span><br /><br />
	[<$help>]
</div>
<table style="border: 0px; padding: 0px; margin: 0px;">
<tr>
<td>
<a href="[<$I2_ROOT>]eighth"><img src="[<$I2_ROOT>]www/pics/eighth/eighth.png" style="border: 0; width: 300; height: 80" alt="Eighth Period Office Online"/></a>
</td>
<td style="width: 10"></td>
<td style="valign: top">
<table style="border: 0px; padding: 0px; margin: 0px; width: 100%">
<tr>
<td>
<b>[<$smarty.now|date_format:"%B %e, %Y, %l:%M %p">]</b>
</td>
<td style="text-align: right">
<span id="help_text" onclick="show_help(0)">Help</span>&nbsp;&nbsp;&nbsp;
</td>
</tr>
</table>
<form action="[<$I2_ROOT>]eighth/vcp_schedule" method="post" name="scheduleform">
<input type="hidden" name="op" value="search" />
<table style="border: 0px; padding: 0px; margin: 0px">
<tr>
<td style="width: 80">First name:</td>
<td style="width: 120"><input type="text" name="fname" style="width: 115px;" /></td>
<td style="width: 80">Student ID:</td>
<td style="width: 120"><input type="text" name="uid" style="width: 115px;" /></td>
</tr> 
<tr>
<td style="width: 80">Last name:</td>
<td style="width: 120"><input type="text" name="lname" style="width: 115px;" /></td>
<td style="width: 80">&nbsp;</td>
<td style="width: 120"><input type="submit" value="View Schedule" style="width: 115px;" tabindex="4" /></td>
</tr>
</table>
</form>
<script language="javascript" type="text/javascript">
document.scheduleform.uid.focus();
</script>
</td>
</tr>
</table>

