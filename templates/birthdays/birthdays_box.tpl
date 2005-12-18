[<if count($birthdays) > 0 >]

<table id="birthdays">
<tbody>
	<tr>
		<th id="name">Name</th>
		<th id="grade">Grade</th>
		<th id="age">Age</th>
	</tr>
[<foreach from=$birthdays item=person>]
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

<div id="morebirthdays"><a href="[<$I2_ROOT>]birthdays/">More birthdays</a></div>
