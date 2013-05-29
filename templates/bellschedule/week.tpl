<span style='display:none'>::START::</span>
<table class='weeksched'>
	<tr class='h' style='min-height: 40px;max-height: 40px;line-height: 25px'>
	[<foreach $schedules as $schedule>]
		<td style='font-size: 16px;font-weight: bold'>Schedule for<br />[<$schedule.dayformat>]</td>
	[</foreach>]
	</tr><tr>
	[<foreach $schedules as $schedule>]
		<td class='desc schedule-[<$schedule.index>]'>[<$schedule.description>]	</td>
	[</foreach>]
	</tr><tr>
	[<foreach $schedules as $schedule>]
		<td>[<$schedule.schedule>]</td>
	[</foreach>]
</tr></table>
<p><span style='max-width: 500px'>Schedules are subject to change.</span></p>
<span style='display:none'>::END::</span>
