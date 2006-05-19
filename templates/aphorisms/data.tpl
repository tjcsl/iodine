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
[<foreach from=$users item=user>]
	[<assign var=uid value=$user->uid>]
	<tr>
		<td>[<$user->name>]</td
		<td>[<$data.$uid.college>]</td>
		<td>[<$data.$uid.collegeplans>]</td>
		<td>[<if $data.$uid.nationalmeritsemifinalist>]Yes[<else>]No[</if>]</td>
		<td>[<if $data.$uid.nationalmeritfinalist>]Yes[<else>]No[</if>]</td>
		<td>[<if $data.$uid.nationalachievement>]Yes[<else>]No[</if>]</td>
		<td>[<if $data.$uid.hispanicachievement>]Yes[<else>]No[</if>]</td>
		<td>[<$data.$uid.honor1>]</td>
		<td>[<$data.$uid.honor2>]</td>
		<td>[<$data.$uid.honor3>]</td>
		<td>[<$data.$uid.aphorism>]</td>
	</tr>
[</foreach>]
</table>
