[<include file="eighth/header.tpl">]
<div style="font-weight: bold; font-size: 24pt;">Absences [<if isSet($dstart)>]from $dstart to $dend[</if>]</div>
<div style="float: right">
	<form action="[<$I2_ROOT>]eighth/vp_delinquent/sort/" method="get">
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
				<input type="checkbox" name="seniors" [<if isset($seniors)>]checked="checked"[</if>] /> Seniors<br />
				<input type="checkbox" name="juniors" [<if isset($juniors)>]checked="checked"[</if>] /> Juniors<br />
				<input type="checkbox" name="sophomores" [<if isset($sophomores)>]checked="checked"[</if>] /> Sophomores<br />
				<input type="checkbox" name="freshmen" [<if isset($freshmen)>]checked="checked"[</if>] /> Freshmen<br />
		</fieldset>
		<fieldset>
			<legend>Sorts</legend>
			<select name="sort" [<if isset($show)>]onchange="sortDelinquents(this)"[</if>]>
			[<foreach from=$sorts key=sortname item=sortdesc>]
				<option value="[<$sortname>]" [<if $sortname eq $sort>]selected="selected"[</if>]>[<$sortdesc>]</option>
			[</foreach>]
			</select>
		</fieldset>
		<input type="submit" />
	</form>
</div>

[<if isset($show)>]

<script type="text/javascript" src="[<$I2_ROOT>]www/js/sort.js"></script>
<script type="text/javascript">
<!--
function sortDelinquents(select) {
	var form = document.getElementById("exportcsv");
	form.elements.namedItem("sort").value = select.value;
	var ascend = select.selectedIndex % 2 == 0 ? 1 : -1;
	var index;
	switch (select.selectedIndex) {
	case 0: case 1: index = 0; break; // Alphabetically
	case 2: case 3: index = 3; break; // By grade
	case 4: case 5: index = 2; break; // By absences
	default: return; // Silently break
	}
	var table = document.getElementById("delinquents");
	if (index == 0) {
		sortTable(table, index, ascend,
			function(cell) {return cell.firstChild.firstChild.nodeValue;});
	} else {
		sortTable(table, index, ascend);
	}
}
// -->
</script>
<form method="post" action="[<$I2_ROOT>]eighth/vp_delinquent/csv/" class="boxform" id="exportcsv">
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

<table id="delinquents">
<thead>
	<tr>
		<th>Student</th>
		<th>Student ID</th>
		<th>Absences</th>
		<th>Grade</th>
	</tr>
</thead><tbody>
[<foreach from=$delinquents item=student>]
	<tr>
		<td><a href="[<$I2_ROOT>]studentdirectory/info/[<$student.uid>]">[<$student.name>]</a></td>
		<td>[<$student.studentid>]</td>
		<td>[<$student.absences>]</td>
		<td>[<$student.grade>]</td>
	</tr>
[</foreach>]
</tbody>
</table>
[<else>]
Select your options and click "Load Page" to view absentee information here.
[</if>]
