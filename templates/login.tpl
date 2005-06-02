[<if $failed>]
Your login as [<$uname>] failed.  Maybe your password is incorrect?
[</if>]
<form action='[<$I2_SELF>]' method='post'>
	Username: <input name='login_username' type='text' value='[<$uname>]'/><br />
	Password: <input name='login_password' type='password'/><br />
	<input type='submit' value='Submit'/>
</form>
