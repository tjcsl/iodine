
[<if count($today) > 0 >]

<table>
<tbody>
	<tr>
		<th>Today</th>
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
</tbody>
</table>

[<else>]

No birthdays today.

[</if>]
[<if count($tomorrow) > 0 >]

<table>
<tbody>
	<tr>
		<th>Tomorrow</th>
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
</tbody>
</table>

[<else>]

No birthdays tomorrow.

[</if>]
<div><a href="[<$I2_ROOT>]birthdays/">More birthdays</a></div>
