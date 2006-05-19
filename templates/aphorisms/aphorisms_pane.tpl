[<if $updated>]<b>Aphorism Updated</b>[</if>]

<form action="[<$I2_ROOT>]aphorisms" method="POST">
	<input type="hidden" name="posting" value="1"/><br />
	<table>
	<tr><td>College:</td><td><input type="text" name="college" value="[<$aphorism.college|escape>]"/></td></tr>
	<tr><td>College Plans:</td><td><textarea name="collegeplans" rows="1" cols="50">[<$aphorism.collegeplans|escape>]</textarea></td></tr>
	<tr><td>National Merit Semifinalist:</td><td><input type="checkbox" name="nationalmeritsemifinalist"[<if $aphorism.nationalmeritsemifinalist>]checked="checked"[</if>]/>
	<tr><td>National Merit Finalist:</td><td><input type="checkbox" name="nationalmeritfinalist"[<if $aphorism.nationalmeritfinalist>]checked="checked"[</if>]/>
	<tr><td>National Achievement:</td><td><input type="checkbox" name="nationalachievement"[<if $aphorism.nationalachievement>]checked="checked"[</if>]/>
	<tr><td>Hispanic Achievement:</td><td><input type="checkbox" name="hispanicachievement"[<if $aphorism.hispanicachievement>]checked="checked"[</if>]/>
	<tr><td>Honor #1:</td><td><textarea name="honor1" rows="3" cols="50">[<$aphorism.honor1|escape>]</textarea></td></tr>
	<tr><td>Honor #2:</td><td><textarea name="honor2" rows="3" cols="50">[<$aphorism.honor2|escape>]</textarea></td></tr>
	<tr><td>Honor #3:</td><td><textarea name="honor3" rows="3" cols="50">[<$aphorism.honor3|escape>]</textarea></td></tr>
	<tr><td>Aphorism:</td><td><textarea name="aphorism" rows="5" cols="50">[<$aphorism.aphorism|escape>]</textarea></td></tr>
	</table>
	<input type="submit" value="Submit"/>
</form>
