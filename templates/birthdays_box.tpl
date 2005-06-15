<table>
<caption>Today's Birthdays</caption>
<summary>Name, grade, and age of students born today</summary>
<tbody>
	<tr>
		<th>Name</th>
		<th>Grade</th>
		<th>Age</th>
	</tr>
{section name=person loop=$birthdays}
	<tr>
		<th>$birthdays[person][0]</th>
		<th>$birthdays[person][1]</th>
		<th>$birthdays[person][2]</th>
	</tr>
{/section}
</tbody>
</table>
