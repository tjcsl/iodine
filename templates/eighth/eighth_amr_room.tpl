[<include file="eighth/eighth_header.tpl">]
<form action="[<$I2_ROOT>]eighth/amr_room/modify/rid/[<$room->rid>]" method="post">
	Name: <input type="text" name="name" value="[<$room->name>]" /><br />
	Capacity: <input type="text" name="capacity" value="[<$room->capacity>]" size="4" /><br />
	<input type="radio" name="modify_or_remove" value="modify" checked />Modify<br />
	<input type="radio" name="modify_or_remove" value="remove" />Remove<br />
	<input type="submit" value="Modify/Remove Room" /><br />
</form>
