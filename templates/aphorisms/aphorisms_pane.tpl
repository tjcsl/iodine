[<if $admin_aphorisms>]
<a href="[<$I2_ROOT>]aphorisms/choose">Select a Student</a><br />
<a href="[<$I2_ROOT>]aphorisms/data">Data Dump</a><br />
<a href="[<$I2_ROOT>]aphorisms/csv">CSV File</a><br /><br />
Aphorisms for [<$username>]<br /><br />
[</if>]

[<if $updated>]<b>Aphorism Updated</b>[</if>]

<form action="[<$I2_ROOT>]aphorisms" method="post">
	<input type="hidden" name="posting" value="1"/><br />
	<table>
	<tr><td style="width:180px;">College you plan to attend:</td><td><input type="text" size="50" name="college" value="[<$aphorism.college|escape>]"/></td></tr>
[<*	<tr><td>National Merit Semifinalist:</td><td><input type="checkbox" name="nationalmeritsemifinalist"[<if $aphorism.nationalmeritsemifinalist>]checked="checked"[</if>]/></td></tr>
	<tr><td>National Merit Finalist:</td><td><input type="checkbox" name="nationalmeritfinalist"[<if $aphorism.nationalmeritfinalist>]checked="checked"[</if>]/></td></tr>
	<tr><td>National Achievement:</td><td><input type="checkbox" name="nationalachievement"[<if $aphorism.nationalachievement>]checked="checked"[</if>]/></td></tr>
	<tr><td>Hispanic Achievement:</td><td><input type="checkbox" name="hispanicachievement"[<if $aphorism.hispanicachievement>]checked="checked"[</if>]/></td></tr>*>]
	<tr><td></td><td>Please list your three most important honors/scholarships/accomplishments, with grade level.</td></tr>
	<tr><td></td><td>(Example: "Title (10-12), Title (10)") Be selective! Space is limited.</td></tr>
	<tr><td>Honor #1:</td><td><input type="text" name="honor1" size="70" value="[<$aphorism.honor1|escape>]"/></td></tr>
	<tr><td>Honor #2:</td><td><input type="text" name="honor2" size="70" value="[<$aphorism.honor2|escape>]"/></td></tr>
	<tr><td>Honor #3:</td><td><input type="text" name="honor3" size="70" value="[<$aphorism.honor3|escape>]"/></td></tr>
	<tr><td></td><td>Seniors, this is your chance to write a eulogy to your high school career. Be witty, creative, sentimental - whatever - but keep it clean. And in English. It's your 200 characters (not including spaces).</td></tr>
	<tr><td>Aphorism:</td><td><textarea name="aphorism" rows="5" cols="50">[<$aphorism.aphorism|escape>]</textarea></td></tr>
	</table>
	<input type="submit" value="Submit"/>
</form>
