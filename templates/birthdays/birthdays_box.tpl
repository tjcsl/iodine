[<if count($birthdays) > 0 >]

<table>
<tbody>
	<tr>
		<th>Name</th>
		<th>Grade</th>
		<th>Age</th>
	</tr>
[<foreach from=$birthdays item=person>]
	<tr>
		<td>[<$person[0]>]</td>
		<td>[<$person[1]>]</td>
		<td>[<$person[2]>]</td>
	</tr>
[</foreach>]
</tbody>
</table>

[<else>]

No birthdays today.

[</if>]
