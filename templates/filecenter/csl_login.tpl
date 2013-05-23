[<if isset($csl_failed_login)>]
I'm sorry, but the Intranet was unable to access your files. Please type your <b>Computer Systems Lab</b> user and password to allow Intranet to access these files.
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
