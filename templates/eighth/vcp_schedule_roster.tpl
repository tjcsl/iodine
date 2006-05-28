Roster for <b>[<$activity->name>]</b><br />
Room: [<$activity->block_rooms_comma>]<br />

[<if $activity->comment>]
<br />
<em>[<$activity->comment>]</em>
<br />
[</if>]

<br />
<table cellspacing="0" cellpadding="0">
	<tr>
		<th>Name</th>
		<th>Grade</th>
[<* Insert more personal information *>]
	</tr>
[<foreach from=$activity->members_obj item="member">]
	<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">];">
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]studentdirectory/info/[<$member->uid>]">[<$member->name_comma>]</a></td>
		<td style="padding: 0px 5px;">[<$member->grade>]</td>
	</tr>
[</foreach>]
</table><br />
Total: [<$activity->member_count>]
