[<if isSet($csl_failed_login)>]
Your login failed.  Please check your username/password.
[</if>]
<form method="post" action="[<$I2_ROOT>]filecenter/cslauth">
	<table>
		<tr>
			<th>Username:</th>
			<td><input type="text" name="user"/></td>
		</tr>
		<tr>
			<th>Password:</th>
			<td><input type="password" name="password"/></td>
		</tr>
	</table>
	
	<input type="submit" value="Log in"/>
</form>
