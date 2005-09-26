You have been absent <a href="">[<$absent>] time[<if $absent != 1 >]s[</if>]</a>.
<table style="width: 100%; border: 0px; padding: 0px; margin: 0px">
	<tr>
		<th style="width: 50%;">Activity</th>
		<th style="width: 25%;">Room(s)</th>
		<th style="width: 25%;">Block</th>
	</tr>
[<if isset($activities) && count($activities) > 0 >]
	[<foreach from=$activities item="activity">]
		<tr>
			<td style="text-align: left;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/single/uid/[<$I2_UID>]">[<$activity->name>]</a></td>
			<td style="text-align: center;">[<$activity->block_rooms_comma>]</td>
			<td style="text-align: center;">[<$activity->block->block>] block</td>
		</tr>
	[</foreach>]
[</if>]
</table>
<span style="font-style: italic;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$I2_UID>]">Full Schedule</a> | <a href="">Special Activities</a> | <a href="">Printer Friendly</a></span>
