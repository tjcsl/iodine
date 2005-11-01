[<include file="eighth_header.tpl">]
<form action="[<$I2_ROOT>]eighth/amr_room/modify/rid/[<$room->rid>]" method="post">
	Name: <input type="text" name="name" value="[<$room->name>]"><br />
	Capacity: <input type="text" name="capacity" value="[<$room->capacity>]" size="4"><br />
	<input type="submit" value="Modify"><br />
	<a href="[<$I2_ROOT>]eighth/amr_room/remove/rid/[<$room->rid>]"><input type="button" value="Remove"></a>
</form>
