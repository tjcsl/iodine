[<if $failed>]
Your login as [<$uname>] failed.  Maybe your password is incorrect?
[</if>]
[<if $loggedin>]
Logged in as [<$uname>].  Please wait to be redirected...
<meta http-equiv='refresh' content='1'/>
[</if>]
<form action='[<$I2_SELF>]' method='post'>
	Username: <input name='login_username' type='text' value='[<$uname>]'/><br />
	Password: <input name='login_password' type='password'/><br />
	<input type='submit' value='Submit'/>
</form>
