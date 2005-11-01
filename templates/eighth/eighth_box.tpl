You have been absent <a href="">[<$absent>] time[<if $absent != 1 >]s[</if>]</a>.
[<if isset($activities) && count($activities) > 0 >]
	<table style="width: 100%; border: 0px; padding: 0px; margin: 0px">
		<tr>
			<th style="width: 50%;">Activity</th>
			<th style="width: 25%;">Room(s)</th>
			<th style="width: 25%;">Block</th>
		</tr>
		[<foreach from=$activities item="activity">]
			<tr>
				<td style="text-align: left;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$I2_UID>]/bid/[<$activity->bid>]">[<$activity->name_r>]</a></td>
				<td style="text-align: center;">[<$activity->block_rooms_comma>]</td>
				<td style="text-align: center;">[<$activity->block->block>] block</td>
			</tr>
		[</foreach>]
	</table>
[<else>]
	<br />There are currently no activities.<br />
[</if>]
<span style="font-style: italic;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$I2_UID>]">Full Schedule</a> | <a href="[<$I2_ROOT>]eighth">Special Activities</a> | <a href="[<$I2_ROOT>]eighth/vcp_schedule/print/uid/[<$I2_UID>]">Printer Friendly</a></span>
