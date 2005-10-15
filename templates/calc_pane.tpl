[<if $message>][<$message>]<br />[</if>]
Enter your calculator serial number below.<br />
<form method="POST" action="[<$I2_ROOT>]calc" class="boxform">
<input type="hidden" name="calc_form" value="" />
<input type="text" name="add" value="" />
<input type="submit" value="Add" name="submit" />
</form>
<form method="POST" action="[<$I2_ROOT>]calc" class="boxform">
<input type="hidden" name="calc_form" value="" />
<select name="delete" size="3">
[<foreach from=$calcs item=id>]
<option value="[<$id.calcid>]">[<$id.calcid>]</option>
[</foreach>]
</select>
<input type="submit" value="Delete" name="submit" />
</form>
