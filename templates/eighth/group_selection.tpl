[<include file="eighth/header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select id="group_list" size="10" onchange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/gid/' + this.options[this.selectedIndex].value">
[<foreach from=$groups item='group'>]
	<option value="[<$group->gid>]" [<if ($lastgid == $group->gid)>]selected="selected"[</if>]>[<$group->name|replace:'eighth_':''>]</option>
[</foreach>]
</select>
[<if isset($display_modify)>]<br /><a href="#" onclick="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/gid/' + document.getElementById('group_list').options[document.getElementById('group_list').selectedIndex].value">Modify</a><br />[</if>]
[<if isset($add)>]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	<input type="text" name="name" /><br />
	<input type="submit" value="Add" />
</form>
[</if>]
