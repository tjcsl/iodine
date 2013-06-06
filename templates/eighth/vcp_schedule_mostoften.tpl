[<if $I2_USER->is_group_member('admin_eighth')>]
	[<include file="eighth/header.tpl">]
	<h2>[<$user->name_comma>] ([<if isset($user->tjhsstStudentId)>][<$user->tjhsstStudentId>], [</if>][<$user->grade>][<if $user->grade != 'staff' >]th[</if>])</h2>
	<div style="float: right; margin: 10px;">
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/absences/uid/[<$user->uid>]" style="font-weight: bold; font-size: 14pt;">[<$absence_count>] absence[<if $absence_count != 1>]s[</if>]</a><br />
		<a href="[<$I2_ROOT>]eighth/view/student/uid/[<$user->uid>]">Edit Student</a><br />
	</div>
	<b>Counselor: [<$counselor_name>]</b><br />
	<span style="color: #FF0000; font-weight: bold;">Comments: [<if isset($comments) && $comments != "">][<$comments>][<else>]none[</if>]</span><br />
	<a href="[<$I2_ROOT>]eighth/view/comments/uid/[<$user->uid>]">Edit Comments</a><br />
	<br /><br />
[<else>]
	[<if $I2_USER->uid != $user->uid>]<h2>Most Often Activity Signups for [<$user->name>]</h2>[</if>]
[</if>]
	<input type="button" value="Back" onclick="history.back()" />
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
		<tr>
			<th>Number of Signups</th>
			<th>Activity ID</th>
			<th>Activity</th>
			<th>Teacher</th>
		</tr>
[<foreach from=$mostoften item="activity">]
		<tr class="[<cycle values="c1,c2">]">
			<td style="text-align: center;">[<$activity['num']>]</td>
			<td style="text-align: center;">[<$activity['act']->aid>]</td>
			<td style="text-align: center;">[<if $activity['act']->aid == $defaultaid>]HAS NOT SELECTED AN ACTIVITY[<else>]<a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity['act']->bid>]/aid/[<$activity['act']->aid>]">[<$activity['act']->name_full_r>] ([<$activity['act']->aid>])</a>[</if>]</td>
			<td style="text-align: center;">[<$activity['act']->block_sponsors_comma_short>]</td>
		</tr>
[<foreachelse>]
		<tr class="c1">
			<td colspan=4 style="text-align: center">There is no information to be displayed at this time.</td>
		</tr>
[</foreach>]
	</table>
	<input type="button" value="Back" onclick="history.back()" />
</form>
