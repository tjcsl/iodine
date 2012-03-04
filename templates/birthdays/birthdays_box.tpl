<table id="birthdays">
	[<if count($today) >]
		<tr>
			<th style="width:50%;">Today</th>
			<th>Grade</th>
			<th>Age</th>
		</tr>
	[<foreach from=$today item=person>]
		<tr>
			<td><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a></td>
			<td>[<$person.grade>]</td>
			<td>[<$person.age>]</td>
		</tr>
	[</foreach>]
	[<else>]
		<tr><td colspan="3">No birthdays today.</td></tr>
	[</if>]
	[<if count($tomorrow) >]
		<tr>
			<th style="width:50%;">Tomorrow</th>
			<th>Grade</th>
			<th>Age</th>
		</tr>
		[<foreach from=$tomorrow item=person>]
			<tr>
				<td><a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a></td>
				<td>[<$person.grade>]</td>
				<td>[<$person.age>]</td>
			</tr>
		[</foreach>]
	[<else>]
		<tr><td colspan="3">No birthdays tomorrow.</td></tr>
	[</if>]
</table>
<b><a href="[<$I2_ROOT>]birthdays/">More birthdays</a></b>
