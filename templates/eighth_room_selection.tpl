[<include file="eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="room_list" size="10" onChange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:'view'>]/rid/' + this.options[this.selectedIndex].value">
[<foreach from=$rooms item='room'>]
	<option value="[<$room.rid>]">[<$room.name>]</option>
[</foreach>]
</select>
[<if $add >]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Name: <input type="text" name="name"><br />
	Capacity: <input type="text" name="capacity" size="4"><br />
	<input type="submit" value="Add">
</form>
[</if>]
