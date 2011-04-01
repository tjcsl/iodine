<script type="text/javascript" src="[<$I2_ROOT>]www/js/eighth_box.js"></script>
You have been absent <a href="[<$I2_ROOT>]eighth/vcp_schedule/absences/uid/[<$I2_UID>]">[<$absent>] time[<if $absent != 1 >]s[</if>]</a>.[<if $absent > 2 >]</span>[</if>]
[<if isset($activities) && count($activities) > 0 >]
	<table style="width: 100%; border: 0px; padding: 0px; margin: 0px" cellspacing="0">
		<tr>
			<th style="width: 50%;">Activity</th>
			<th style="width: 25%;">Room(s)</th>
			<th style="width: 25%;">Block</th>
		</tr>
		[<foreach from=$activities item="activity">]
			<tr[<if $activity->cancelled>] class="activity_cancelled"[</if>]>
				<td style="text-align: left;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$I2_UID>]/bids/[<$activity->bid>]/aid/[<$activity->aid>]/" title="[<$activity->comment>]" [<if $activity->aid!=999>]onclick="return eighth_box_options(this)"[</if>]>[<$activity->name_full_r>]</a></td>
				<td style="text-align: center;">[<if $activity->cancelled>]CANCELLED[<else>][<$activity->block_rooms_comma>][</if>]</td>
				<td style="text-align: center;">[<$activity->block->block>] block</td>
			</tr>
		[</foreach>]
	</table>
[<else>]
	<br />There are currently no activities.<br />
[</if>]
<span style="font-style: italic;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$I2_UID>]">Full Schedule</a> | 
[<*<a href="[<$I2_ROOT>]eighth">Special Activities</a> |*>]
<a href="javascript://null()" onclick="alert('An error occured. We\'re working on it. Please try again later.');">Dismiss All Absences</a> |
<a href="[<$I2_ROOT>]info/eighth">General Info</a></span>
