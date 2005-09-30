[<if isset($stdout)>]
	Results of command [<$csl_command>]:<br/>
	Output: [<$stdout>]<br/>
[</if>]
[<if isset($stderr)>]
	Error: [<$stderr>]<br/>
[</if>]
<form action="[<$I2_SELF>]" method="post">
[<if isset($csl_user)>]
	Logged in as [<$csl_user>]<br/>
	<input type="text" name="csl_cmd"/><br/>
[<else>]			
	<table>
		<tr><th>Username:</th><td> <input type="text" name="csl_user"/></td></tr>
		<tr><th>Password:</th><td> <input type="password" name="csl_pass"/></td></tr>
	</table>	
[</if>]
<input type="submit" value="[<if isset($csl_user)>]Run Command[<else>]Log in[</if>]"/>
</form>

