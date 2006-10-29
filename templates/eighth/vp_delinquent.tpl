[<include file="eighth/header.tpl">]
<div style="font-weight: bold; font-size: 24pt;">Absences [<if isSet($dstart)>]from $dstart to $dend[</if>]</div>
<br />
<form method="post" action="[<$I2_ROOT>]eighth/vp_delinquent/sort/" class="boxform">
<table>
 <tr><td>Minimum number of absences:</td><td><input type="text" name="lower" value="[<$lower>]" /></td><td><input type="checkbox" name="seniors" [<if isset($seniors)>]CHECKED[</if>] /> Seniors</td></tr>
 <tr><td>Maximum number of absences:</td><td><input type="text" name="upper" value="[<$upper>]" /></td><td><input type="checkbox" name="juniors" [<if isset($juniors)>]CHECKED[</if>] /> Juniors</td></tr>
 <tr><td>Start date (YYYY-MM-DD):</td><td><input type="text" name="start" value="[<$start>]" /></td><td><input type="checkbox" name="sophomores" [<if isset($sophomores)>]CHECKED[</if>] /> Sophomores</td></tr>
 <tr><td>End date (YYYY-MM-DD):</td><td><input type="text" name="end" value="[<$end>]" /></td><td><input type="checkbox" name="freshmen" [<if isset($freshmen)>]CHECKED[</if>] /> Freshmen</td></tr>
</table>
<select name="sort">
[<foreach from=$sorts key=sortname item=sortdesc>]
 <option value="[<$sortname>]" [<if $sortname eq $sort>]SELECTED[</if>] />[<$sortdesc>]
[</foreach>]
</select>
<input type="submit" value="Load Page" />
</form>

<hr />

[<if isset($show)>]

<form method="post" action="[<$I2_ROOT>]eighth/vp_delinquent/csv/" class="boxform">
<input type="hidden" name="lower" value="[<$lower>]" />
<input type="hidden" name="upper" value="[<$upper>]" />
<input type="hidden" name="start" value="[<$start>]" />
<input type="hidden" name="end" value="[<$end>]" />
[<if isset($seniors)>]<input type="hidden" name="seniors" value="TRUE" />[</if>]
[<if isset($juniors)>]<input type="hidden" name="juniors" value="TRUE" />[</if>]
[<if isset($sophomores)>]<input type="hidden" name="sophomores" value="TRUE" />[</if>]
[<if isset($freshmen)>]<input type="hidden" name="freshmen" value="TRUE" />[</if>]
<input type="submit" value="Download CSV file of students as sorted below" />
</form>

<table>
	<tr>
		<th>Student</th>
		<th>Student ID</th>
		<th>Absences</th>
		<th>Grade</th>
	</tr>
[<foreach from=$delinquents item=student>]
	<tr>
		<td><a href="[<$I2_ROOT>]studentdirectory/info/[<$student.uid>]">[<$student.name>]</a></td>
		<td>[<$student.studentid>]</td>
		<td>[<$student.absences>]</td>
		<td>[<$student.grade>]</td>
	</tr>
[</foreach>]
</table>
[<else>]
Select your options and click "Load Page" to view absentee information here.
[</if>]
