Sections of <b>[<$classes.0.class->name>]</b>: <br /><br />

<table>
	<tr>
		<th>Period</th>
		<th>Teacher</th>
	</tr>
[<foreach from=$classes item="class">]
	<tr>
		<td><a href="[<$I2_ROOT>]studentdirectory/class/[<$class.class->sectionid>]">[<$class.class->period>]</a></td>
		<td><a href="[<$I2_ROOT>]studentdirectory/info/[<$class.class->teacher->uid>]">[<$class.class->teacher->name_comma>]</a></td>
	</tr>
[</foreach>]
</table>
