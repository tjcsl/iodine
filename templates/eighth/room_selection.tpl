[<include file="eighth/header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="room_list" size="10" onchange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:'view'>]/rid/' + this.options[this.selectedIndex].value">
[<foreach from=$rooms item='room'>]
	<option value="[<$room.rid>]" [<if isset($rid) && $rid==$room.rid>]selected="selected"[</if>]>[<$room.name>]</option>
[</foreach>]
</select>
[<if isset($add)>]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Name: <input type="text" name="name" /><br />
	Capacity: <input type="text" name="capacity" size="4" /><br />
	<input type="submit" value="Add" />
</form>
[</if>]
