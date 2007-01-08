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
			<td rowspan="2">
				<a href="[<$I2_ROOT>]eighth"><img src="[<$I2_ROOT>]www/pics/eighth/eighth.png" id="eighth_header_logo" alt="Eighth Period Office Online" title="Return to 8th Period Office Menu" /></a>
			</td>
			<td style="text-align: right; vertical-align: middle;">
				<b>[<$smarty.now|date_format:"%B %e, %Y, %l:%M %p">]</b>
			</td>
			<td style="text-align: right">
				<span class="bold" id="eighth_help_text" onclick="show_help(0)">Help</span>
[<if isset($print_url)>]
				<a href="[<$I2_ROOT>]eighth/[<$method>]/format/[<$print_url>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" style="vertical-align: middle; margin-left: 30px;" /></a>
[</if>]
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">
				Name/ID: <input type="text" name="name_id" />
			</td>
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
	document.scheduleform.lname.focus();
</script>
[<if isSet($startdate)>]<b>Start date: [<$startdate|date_format>]</b><br />[</if>]
[</if>]
