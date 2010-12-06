<form action="testing" method="post">
Time:<input type="text" name="time" value="0000-00-00 00:00:00"/><br />
Type:<input type="text" name="type" /><br />
<input type="submit" />
</form>
[<$message>]

<table>
[<foreach from=$tests item=test>]
<tr><form action="testing" method="post">
	<td><input type="hidden" name="update" value="[<$test.id>]" /><input type="text" name="time" value="[<$test.time|date_format:"%Y-%m-%d %T">]" /></td>
	<td><input type="text" name="type" value="[<$test.type>]" /></td><td><input type="submit" value="Update" /></td>
</form></tr>
[</foreach>]
</table>
