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
	[<if $I2_USER->uid != $user->uid>]<h2>Schedule for [<$user->name>]</h2>[</if>]
[</if>]
	<input type="button" value="Back" onclick="window.back()" />
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
		<tr>
			<th>Day of Week</th>
			<th>Date</th>
			<th>Block</th>
			<th>Attended</th>
			<th>Activity</th>
			<th>Teacher</th>
		</tr>
[<foreach from=$activities item="activity">]
		<tr class="[<cycle values="c1,c2">]">
			<td style="text-align: center;">[<$activity->block->date|date_format:"%a">]</td>
			<td style="text-align: center;">[<$activity->block->date|date_format:"%B %e, %Y">]</td>
			<td style="text-align: center;">[<$activity->block->block>]</td>
			<td style="text-align: center;">[<if in_array(array($activity->aid, $activity->bid), $absences)>]No[<elseif $activity->attendancetaken>]Yes[<else>]---[</if>]</td>
			<td style="text-align: center;">[<if $activity->aid == $defaultaid>]HAS NOT SELECTED AN ACTIVITY[<else>]<a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_full_r>] ([<$activity->aid>])</a>[</if>]</td>
			<td style="text-align: center;">[<$activity->block_sponsors_comma_short>]</td>
		</tr>
[</foreach>]
	</table>
	<input type="button" value="Back" onclick="window.back()" />
</form>
