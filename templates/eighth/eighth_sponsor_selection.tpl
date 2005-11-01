[<include file="eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="sponsor_list" size="10" onChange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:'view'>]/sid/' + this.options[this.selectedIndex].value">
[<foreach from=$sponsors item='sponsor'>]
<option value="[<$sponsor.sid>]">[<$sponsor.name_comma>]</option>[</foreach>]
</select>
[<if $add >]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	First Name: <input type="text" name="fname"><br />
	Last Name: <input type="text" name="lname"><br />
	<input type="submit" value="Add">
</form>
[</if>]
