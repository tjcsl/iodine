[<if $failed>]
Your login as [<$failedname>] failed.  Maybe your password is incorrect?
[</if>]

<form action='[<$I2_SELF>]' method='post'>
	Username: <input name='login_username' type='text' value='[<$failedname>]'/><br />
	Password: <input name='login_password' type='password'/><br />
	<input type='submit' value='Submit'/>
</form>
