<table>
<caption>Today's Birthdays</caption>
<summary>Name, grade, and age of students born today</summary>
<tbody>
	<tr>
		<th>Name</th>
		<th>Grade</th>
		<th>Age</th>
	</tr>
[<foreach from=$birthdays item=person>]
	<tr>
		<th>[<$person[0]>]</th>
		<th>[<$person[1]>]</th>
		<th>[<$person[2]>]</th>
	</tr>
[</foreach>]
</tbody>
</table>
