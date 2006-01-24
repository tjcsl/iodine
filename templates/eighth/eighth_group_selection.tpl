[<include file="eighth/eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="group_list" size="10" onchange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/gid/' + this.options[this.selectedIndex].value">
[<foreach from=$groups item='group'>]
	<option value="[<$group->gid>]">[<$group->name|replace:'eighth_':''>]</option>
[</foreach>]
</select>
[<if $add >]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	<input type="text" name="name" /><br />
	<input type="submit" value="Add" />
</form>
[</if>]
