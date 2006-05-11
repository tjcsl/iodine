<script type="text/javascript" src="[<$I2_ROOT>]www/js/eighth_help.js"></script>
<div id="eighth_help_blocker"></div>
<div id="eighth_help_pane" onMouseDown="register_press(event)">
	<div id="eighth_help_close" onClick="hide_help(max_height)">Close</div>
	<span class="bold">Help:</span><br /><br />
	[<if isset($help)>][<$help>][</if>]
</div>
[<if $eighth_admin>]
<form action="[<$I2_ROOT>]eighth/vcp_schedule" method="post" name="scheduleform">
	<input type="hidden" name="op" value="search" />
	<table id="eighth_header_table">
		<tr>
			<td rowspan="3">
				<a href="[<$I2_ROOT>]eighth"><img src="[<$I2_ROOT>]www/pics/eighth/eighth.png" id="eighth_header_logo" alt="Eighth Period Office Online" title="Return to 8th Period Office Menu" /></a>
			</td>
			<td colspan="2" style="text-align: right;">
				<b>[<$smarty.now|date_format:"%B %e, %Y, %l:%M %p">]</b>
			</td>
			<td colspan="2" style="text-align: right">
				<span id="eighth_help_text" onclick="show_help(0)">Help</span>
			</td>
		</tr>
		<tr>
			<td style="width: 90px; text-align: right;">First name:</td>
			<td style="width: 120px"><input type="text" name="fname" style="width: 100%;" /></td>
			<td style="width: 90px; text-align: right;">Student ID:</td>
			<td style="width: 120px">
				<input type="text" name="uid" style="width: 100%;" />
			</td>
		</tr>
		<tr>
			<td style="width: 90px; text-align: right;">Last name:</td>
			<td style="width: 120px">
				<input type="text" name="lname" style="width: 100%;" />
			</td>
			<td style="width: 90px">&nbsp;</td>
			<td style="width: 120px">
				<input type="submit" value="View Schedule" style="width: 100%;" tabindex="4" />
			</td>
		</tr>
	</table>
</form>
<div style="text-align: right; font: 12pt bold;">
	[<if $last_undo>]<a href="[<$I2_ROOT>]eighth/undoit/undo/[<$argstr>]">Undo [<$last_undo>]</a><br/>[</if>]
	[<if $last_redo>]<a href="[<$I2_ROOT>]eighth/undoit/redo/[<$argstr>]">Redo [<$last_redo>]</a><br/>[</if>]
	[<if $last_redo || $last_undo>]<a href="[<$I2_ROOT>]eighth/undoit/clear/[<$argstr>]">Clear undo stack</a>[</if>]
</div>
<script language="javascript" type="text/javascript">
	document.scheduleform.uid.focus();
</script>
[</if>]
