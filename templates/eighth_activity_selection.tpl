[<include file="eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="activity_list" size="10" onChange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$filed|default:"aid">]/' + this.options[this.selectedIndex].value">
[<foreach from=$activities item='activity'>]
<option value="[<$activity->aid>]">[<$activity->aid>]: [<$activity->name_r>]</option>
[</foreach>]
</select>
[<if $add >]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Name: <input type="text" name="name"><br />
	<input type="submit" value="Add">
</form>
[</if>]
