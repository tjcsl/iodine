[<if isset($is_senior)>]
  <a href="[<$I2_ROOT>]seniors/submit">Click here to [<if isset($has_submitted)>]change[<else>]add[</if>] your college destination info</a><br /><br />
[</if>]

[<$num_submitted>] seniors have submitted college plans.<br />

[<if count($seniors) > 0>]
<table cellspacing="0">
	<tr>
		<th><a href="[<$I2_ROOT>]seniors/sort/name/[<if $sort == 'name' && $sortnormal>]1[</if>]">Name</a></th>
		<th><a href="[<$I2_ROOT>]seniors/sort/college/[<if $sort == 'college' && $sortnormal>]1[</if>]">College</a></th>
		<th><a href="[<$I2_ROOT>]seniors/sort/major/[<if $sort == 'major' && $sortnormal>]1[</if>]">Major</a></th>
	</tr>
	[<foreach from=$seniors item=senior>]
	<tr class="[<cycle values="c1,c2">]">
		<td class="pane"><a href="[<$I2_ROOT>]studentdirectory/info/[<$senior.user->uid>]">[<$senior.user->name>]</a></td>
		<td class="pane">[<$senior.dest>][<if ! $senior.dest_sure>] (unsure)[</if>]</td>
		<td class="pane">[<$senior.major>][<if ! $senior.major_sure>] (unsure)[</if>]</td>
	</tr>
	[</foreach>]
</table>
[</if>]
