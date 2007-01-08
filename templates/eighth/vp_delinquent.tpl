[<include file="eighth/header.tpl">]
<div style="font-weight: bold; font-size: 24pt;">Absences [<if isSet($dstart)>]from $dstart to $dend[</if>]</div>
<div style="float: right">
	<form action="[<$I2_ROOT>]eighth/vp_delinquent/sort/" method="GET">
		<fieldset>
			<legend>Absences</legend>
			Lower Limit: <input type="text" name="lower" value="[<$lower>]" /><br />
			Upper Limit: <input type="text" name="upper" value="[<$upper>]" /><br />
		</fieldset>
		<fieldset>
			<legend>Dates</legend>
			Start: <input type="text" name="start" value="[<$start>]" /><br />
			End: <input type="text" name="end" value="[<$end>]" /><br />
		</fieldset>
		<fieldset>
			<legend>Grades</legend>
				<input type="checkbox" name="seniors" [<if isset($seniors)>]CHECKED[</if>] /> Seniors<br />
				<input type="checkbox" name="juniors" [<if isset($juniors)>]CHECKED[</if>] /> Juniors<br />
				<input type="checkbox" name="sophomores" [<if isset($sophomores)>]CHECKED[</if>] /> Sophomores<br />
				<input type="checkbox" name="freshmen" [<if isset($freshmen)>]CHECKED[</if>] /> Freshmen<br />
		</fieldset>
		<fieldset>
			<legend>Sorts</legend>
			<select name="sort">
			[<foreach from=$sorts key=sortname item=sortdesc>]
				<option value="[<$sortname>]" [<if $sortname eq $sort>]SELECTED[</if>] />[<$sortdesc>]
			[</foreach>]
			</select>
		</fieldset>
		<input type="submit">
	</form>
</div>

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
<input type="hidden" name="sort" value="[<$sort>]" />
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
