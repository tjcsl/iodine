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
		User Data file: <input type="text" name="userfile"/><br/>
		<input type="submit" value="Load User Data"/><br/>
	</form>
	[<if !$admin_pass>]
		<form action="[<$I2_ROOT>]dataimport" method="post">
			LDAP Admin Password: <input type="password" name="admin_pass"/><br/>
			<input type="submit" value="Set LDAP Password"/><br/>
		</form>
	[<else>]
		LDAP Admin Password <a href="[<$I2_ROOT>]dataimport/unset_pass">SET</a>
	[</if>]
[</if>]
