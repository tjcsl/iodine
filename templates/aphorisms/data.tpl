<table>
	<tr>
		<th>Student</th>
		<th>College</th>
		<th>College Plans</th>
		<th>National Merit Semi-finalist</th>
		<th>National Merit Finalist</th>
		<th>National Achievement</th>
		<th>Hispanic Achievement</th>
		<th>First Honor</th>
		<th>Second Honor</th>
		<th>Third Honor</th>
		<th>Aphorism</th>
	</tr>
[<foreach from=$data item=$dataitem>]
<tr>
	<td>[<$dataitem.uid>]</td>
	<td>[<$dataitem.college>]</td>
	<td>[<$dataitem.collegeplans>]</td>
	<td>[<$dataitem.nationalmeritsemifinalist>]</td>
	<td>[<$dataitem.nationalmeritfinalist>]</td>
	<td>[<$dataitem.nationalachievement>]</td>
	<td>[<$dataitem.hispanicachievement>]</td>
	<td>[<$dataitem.honor1>]</td>
	<td>[<$dataitem.honor2>]</td>
	<td>[<$dataitem.honor3>]</td>
	<td>[<$dataitem.aphorism>]</td>
</tr>
[</foreach>]
</table>
