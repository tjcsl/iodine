[<if $student>]
<span style="color: red;"><strong>You are a student; students who elected not to show their schedules will not appear on this list.</strong></span><br /><br />
[</if>]
<form action="[<$I2_ROOT>]fieldtrip" method="post">
	<fieldset>
		<legend>Student ids or usernames:</legend>
		<textarea name="students" rows="8" cols="80"></textarea><br />
	</fieldset>
	<fieldset>
		<legend>Periods</legend>
		[<foreach from=$periods item=period>]
			Period [<$period>] <input type="checkbox" name="periods[]" value="[<$period>]" /><br />
		[</foreach>]
	</fieldset>
	<fieldset>
		<legend>Quarter</legend>
		1 <input type="radio" name="quarter" value="1" checked="1" />
		2 <input type="radio" name="quarter" value="2" />
		3 <input type="radio" name="quarter" value="3" />
		4 <input type="radio" name="quarter" value="4" />
	</fieldset>
	<input type="submit" name="fieldtrip_submit" value="Submit" />
</form>
