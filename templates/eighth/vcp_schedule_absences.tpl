[<include file="eighth/header.tpl">]
Absence information for <a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]">[<$user->fullname_comma>] ([<$user->studentid>])</a>: [<$count>] absence[<if $count != 1 >]s[</if>]<br /><br />
[<if $user->iodineUIDNumber == $I2_USER->iodineUIDNumber>]
To clear an absence print this page. Have the teacher listed sign next to the activity to indicate that you were present. Absences in activities with a substitute teacher or in a stickie cannot be cleared. Bring the signed page to the 8th period office within two weeks of an absence to clear it.
<br />
<br />
[</if>]
<table cellspacing="0" cellpadding="0" style="padding: 0; spacing: 0; border: none">
	<tr>
		<th style="padding: 0px 10px;">Date</th>
		<th style="padding: 0px 10px;">Block</th>
		<th style="padding: 0px 10px;">Activity</th>
		<th style="padding: 0px 10px;">Activity ID</th>
		<th style="padding: 0px 10px;">Sponsor(s)</th>
	</tr>
[<foreach from=$absences item="activity">]
	<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">]">
		<td style="padding: 0px 10px; text-align: center;">[<$activity->block->date|date_format:"%B %e, %Y">]</td>
		<td style="padding: 0px 10px; text-align: center;">[<$activity->block->block>] Block</td>
		<td style="padding: 0px 10px; text-align: center;">[<$activity->name_r>]</td>
		<td style="padding: 0px 10px; text-align: center;">[<$activity->aid>]</td>
		<td style="padding: 0px 10px; text-align: center;">[<$activity->block_sponsors_comma_short>]</td>
		[<if $admin>]<td style="padding: 0px 10px; text-align: center;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/remove_absence/uid/[<$user->uid>]/bid/[<$activity->bid>]">Remove</a></td>[</if>]
	</tr>
[</foreach>]
</table>
