[<if isset($schedule.error)>]
	[<$schedule.error>]
[<else>]
	<table class='schedule-tbl'>
	[<foreach from=$schedule item=s>]
		<!--<span class='period'>
			<span class='name'>
				[<$s[0]>]: 
			</span>
			<span class='times'>
				[<$s[1]>] - [<$s[2]>]
			</span>
		</span><br />-->
		<tr>
			<th>
				[<$s[0]>]
			</th>
			<td>
				[<$s[1]>] - [<$s[2]>]
			</td>
		</tr>
	[</foreach>]
	</table>
[</if>]