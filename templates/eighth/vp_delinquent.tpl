[<include file="eighth/header.tpl">]
<div style="font-weight: bold; font-size: 24pt;">Absences [<if isSet($dstart)>]from $dstart to $dend[</if>]</div>
<table>
	<tr>
		<th>Student</th>
		<th>Student ID</th>
		<th>Absences</th>
		<th>Grade</th>
	</tr>
[<foreach from=$students item="student" key="key">]
	<tr>
		<td><a href="[<$I2_ROOT>]studentdirectory/info/[<$student->uid>]">[<$student->name_comma>]</a></td>
		<td>[<$student->tjhsstStudentId>]</td>
		<td>[<$absences.$key>]</td>
		<td>[<$student->grade>]</td>
	</tr>
[</foreach>]
</table>
