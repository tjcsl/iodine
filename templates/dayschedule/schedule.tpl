[<if isset($schedule.error)>]
	[<$schedule.error>]
[<else>]
	<table class='schedule-tbl'>
	[<foreach from=$schedule item=s>]
		<tr class='schedule-day' data-type="[<$s[0]>]" data-start="[<$s[1]>]" data-end="[<$s[2]>]">
			<th class='type'>
				[<$s[0]>][<if $s[1] != "" and $s[2] != "">]:
			</th>
			<td class='times'>
				[<$s[1]>] - [<$s[2]>]
			</td>
			[<else>]
			</th>
			[</if>]
		</tr>
	[</foreach>]
	</table>
[</if>]
