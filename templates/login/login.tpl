<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<meta name="Description" content="TJHSST Intranet2: What you need, when you need it." />
	<title>TJHSST Intranet2: Login</title>
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login.css" />
	<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
	[<if isset($bgjs)>]
	<script type="text/javascript" src="[<$I2_ROOT>][<$bgjs>]"></script>
	[</if>]
<!--[if lt IE 7]>
<script type="text/javascript">
IE7_PNG_SUFFIX = ".png";
</script>
<script src="[<$I2_ROOT>]www/js/ie7/ie7-standard-p.js" type="text/javascript"></script>
<![endif]-->
</head>
<body background="[<$I2_ROOT>][<$bg>]">
<div class="logo"></div>
<br /><br /><br />
<table class="middle">
	<tr>
		<td class="box">
[<if $failed eq 1>]
			<div id="login_failed">
				Your login as [<$uname>] failed.  Maybe your password is incorrect?<br />
			</div>
[<elseif $failed eq 2>]
			<div id="login_failed">
				Your password and username were correct, but you don't appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.
			</div>
[<elseif $failed>]
			<div id="login_failed">
				An unidentified error has occurred. Please contact the intranetmaster and tell him you received this error message immediately.
			</div>
[<else>]
			Please type your username and password to login to the Intranet.
[</if>]
			<form name="login_form" action="[<$I2_SELF>]" method="post">
				[<$posts>]
				<table id="login_box">
					<tr>
						<td>Username:</td>
					   	<td><input name="login_username" type="text" size="25" value="[<$uname>]" /></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><input name="login_password" type="password" size="25" /></td>
					</tr>
					<tr>
						<td align="right" colspan="2"><input type="submit" value="Login" /></td>
					</tr>
				</table>
			</form>
			<script language="javascript" type="text/javascript">
				document.login_form.login_username.focus();
			</script>
			<div style="text-align: right">
				<a href="http://www.tjhsst.edu/">TJHSST</a> &ndash; <a href="https://webmail.tjhsst.edu/">Mail</a> &ndash; <a href="http://postman.tjhsst.edu/">Postman (Calendar)</a>
			</div>
		</td>
	</tr>
</table>
