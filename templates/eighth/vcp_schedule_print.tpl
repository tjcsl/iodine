	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
		<tr>
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
			<td style="text-align: center;">[<$activity->block->date|date_format:"%a">]</td>
			<td style="text-align: center;">[<$activity->block->block>]</td>
			<td style="text-align: center;">[<$activity->block->date|date_format>]</td>
			<td style="text-align: center;">[<if in_array(array($activity->aid, $activity->bid), $absences)>]No[<elseif $activity->attendancetaken>]Yes[<else>]---[</if>]</td>
			<td style="text-align: center;">[<$activity->name_r>] ([<$activity->aid>])</a></td>
			<td style="text-align: center;">[<$activity->block_sponsors_comma_short>]</td>
			<td style="text-align: center;">[<$activity->block_rooms_comma>]</td>
		</tr>
[</foreach>]
	</table>

