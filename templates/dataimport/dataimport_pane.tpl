[<if isSet($userdata)>]
	[<*
	[<foreach from=$userdata item=row>]
		[<foreach from=$row key=key item=value>]
			[<$key>] = [<$value>]
		[</foreach>]
		<br />
	[</foreach>]
	*>]
[<else>]
	<form action="[<$I2_ROOT>]dataimport/userdata" method="post">
		<table>
		<tr><td>User Data file:</td><td> <input type="text" name="userfile"/></td></tr>
		</table>
		<input type="submit" value="Load User Data"/><br/>
	</form>
	<br /><br />
	<form action="[<$I2_ROOT>]dataimport/teacherdata" method="post">
		<table>
		<tr><td>Teacher Data file:</td><td> <input type="text" name="teacherfile"/></td></tr>
		<tr><td>Teacher LDAP server: </td><td><input type="text" name="teacherserver"/></td></tr>
		<tr><td>Teacher LDAP username: </td><td><input type="text" name="teacherdn"/></td></tr>
		<tr><td>Teacher LDAP password: </td><td><input type="password" name="teacherpass"/></td></tr>
		<tr><td>Obsolete</b> Staff file:</td><td><input type="text" name="stafffile"/></td></tr>
		</table>
		<input type="submit" value="Load Teacher Data"/><br/>
	</form>
	<br /><br />
	<form action="[<$I2_ROOT>]dataimport/teachersponsors" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Make all teachers into Eighth-Period sponsors"/><br/>
	</form>
[<if $intranet_pass>]
	<form action="[<$I2_ROOT>]dataimport/eighthdata" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Load 8th-period data"/><br/>
	</form>
	<form action="[<$I2_ROOT>]dataimport/studentinfo" method="post">
		<input type="hidden" name="doit" value="1"/>
		<input type="submit" value="Expand student information using old Intranet data"/><br/>
	</form>
	<br /><br />
	[</if>]
	[<if !$admin_pass>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
			LDAP Admin Password: <input type="password" name="admin_pass"/><br/>
			<input type="submit" value="Set LDAP Password"/><br/>
		</form>
		<br/>
	[<else>]
		LDAP Admin Password <a href="[<$I2_ROOT>]dataimport/unset_pass">SET</a><br/>
	[</if>]
	[<if !$intranet_pass>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
		<table>
			<tr><td>Intranet MySQL server:</td><td> <input type="text" name="intranet_server"/></td></tr>
			<tr><td>Intranet MySQL database: </td><td><input type="text" name="intranet_db"/></td></tr>
			<tr><td>Intranet MySQL user: </td><td><input type="text" name="intranet_user"/></td></tr>
			<tr><td>Intranet MySQL password: </td><td><input type="password" name="intranet_pass"/></td></tr>
		</table>
			<input type="submit" value="Set Intranet 1 Server"/><br/>
		</form>
	[<else>]
		Intranet MySQL Information <a href="[<$I2_ROOT>]dataimport/unset_intranet">SET</a>
	[</if>]
[</if>]
