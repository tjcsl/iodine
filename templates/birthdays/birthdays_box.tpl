<p style="margin-top:6px;"><b>Today's birthdays</b><br />

[<if count($today) > 0 >]

<table id="birthdays">
<tbody>
	<tr>
		<th id="name">Name</th>
		<th id="grade">Grade</th>
		<th id="age">Age</th>
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
</p>
<p><b>Tomorrow's birthdays</b><br />
[<if count($tomorrow) > 0 >]

<table id="birthdays_tom">
<tbody>
	<tr>
		<th id="name">Name</th>
		<th id="grade">Grade</th>
		<th id="age">Age</th>
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
</p>
<div id="morebirthdays"><a href="[<$I2_ROOT>]birthdays/">More birthdays</a></div>
