[<include file="eighth_header.tpl">]

<h2>[<$user->name_comma>] ([<$user->uid>], [<$user->grade>]th)</h2>
<b>Counselor: [<$user->counselor>], TA: </b><br />
<span style="color: #FF0000; font-weight: bold;">Comments: [<if empty($user->comments) >]none[<else>][<$user->comments>][</if>]</span><br />
<form action="[<$I2_ROOT>]eighth/vcp_schedule/change" method="post">
	<input type="submit" value="Change Selected">
	<div style="display: inline; margin-left: 100px;">
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date-3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="< Back Two Weeks"></a>
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date+3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="Forward Two Weeks >"></a>
	</div>
	<br /><br />
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
		<tr>
			<th><input type="checkbox" name="selectall"></td>
			<td>&nbsp;</td>
			<th>Day of Week</th>
			<th>Block</th>
			<th>Date</th>
			<th>Attended</th>
			<th>Activity</th>
			<th>Teacher</th>
			<th>Room</th>
		</tr>
[<foreach from=$activities item="activity">]
		<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">];">
			<td style="text-align: center;"><input type="checkbox" name="change[[<$activity->bid>]]" value="1"></td>
			<td style="text-align: center;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/change/bid/[<$activity->bid>]">Change</a></td>
			<td style="text-align: center;">[<$activity->block->date|date_format:"%a">]</td>
			<td style="text-align: center;">[<$activity->block->block>]</td>
			<td style="text-align: center;">[<$activity->block->date|date_format>]</td>
			<td style="text-align: center;">[<* figure out attended *>]</td>
			<td style="text-align: center;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_r>] ([<$activity->aid>])</a></td>
			<td style="text-align: center;">[<$activity->block_sponsors_comma>]</td>
			<td style="text-align: center;">[<$activity->block_rooms_comma>]</td>
		</tr>
[</foreach>]
	</table>
	<br />
	<input type="submit" value="Change Selected">
	<div style="display: inline; margin-left: 100px;">
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date-3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="< Back Two Weeks"></a>
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date+3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="Forward Two Weeks >"></a>
	</div>
</form>
