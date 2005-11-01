Room: [<$activity->block_rooms_comma>]<br />
<br />
<table cellspacing="0" cellpadding="0">
	<tr>
		<th>Name</th>
		<th>Grade</th>
[<* Insert more personal information *>]
	</tr>
[<foreach from=$activity->members item="member">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">];">
		<td style="padding: 0px 5px;">[<$member->name_comma>]</td>
		<td style="padding: 0px 5px;">[<$member->grade>]</td>
[<* Pull the extra data *>]
	</tr>
[</foreach>]
</table><br />
Total: [<$num_members>]
