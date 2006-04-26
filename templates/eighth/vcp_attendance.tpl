[<include file="eighth/header.tpl">]
<span style="font-family: courier;">
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$activity->block->date|date_format>], [<$activity->block->block>] block<br />
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$activity->block_rooms_comma>]<br />
Sponsor(s):&nbsp;[<$activity->block_sponsors_comma>]<br />
<br />
<form action="[<$I2_ROOT>]eighth/vcp_attendance/update/bid/[<$activity->bid>]/aid/[<$activity->aid>]" method="post">
	<input type="button" value="Select All" onclick=";" /> <input type="submit" value="Update" /><br />
	<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
		<tr>
			<th>Absent</th>
			<th style="padding: 0px 5px;">Student</th>
			<th style="padding: 0px 5px;">Grade</th>
		</tr>
[<php>]
	$this->_tpl_vars['users'] = User::sort_users($this->_tpl_vars['activity']->members);
[</php>]
[<foreach from=$users item="user">]
		<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
			<td style="padding: 0px 5px;"><input type="checkbox" name="absentees[]" value="[<$user->uid>]"[<if in_array($user->uid, $absentees) >] checked="checked"[</if>] /></td>
			<td style="padding: 0px 5px;">[<$user->name_comma>] ([<$user->tjhsstStudentId>])</td>
			<td style="padding: 0px 5px;">[<$user->grade>]</td>
		</tr>
[</foreach>]
	</table><br />
	<input type="submit" value="Update" />
</form>
<div style="float: right; margin: 10px;">
	<a href="[<$I2_ROOT>]eighth/vcp_attendance/format/aid/[<$activity->aid>]/bid/[<$activity->bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" /></a>
</div>
