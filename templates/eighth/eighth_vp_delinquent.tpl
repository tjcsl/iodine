[<include file="eighth/eighth_header.tpl">]
<table>
	<tr>
		<th>Student</th>
		<th>Student ID</th>
		<th>Absences</th>
		<th>Grade</th>
	</tr>
[<foreach from=$students item="student" key="key">]
	<tr>
		<td>[<$student->name_comma>]</td>
		<td>[<$student->uid>]</td>
		<td>[<$absences.$key>]</td>
		<td>[<$student->grade>]</td>
	</tr>
[</foreach>]
</table>
