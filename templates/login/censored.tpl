<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>TJHSST Intranet2: Login</title>
	<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<script type="text/javascript">
	//Set some variables so that any script can use them.
	var i2root="[<$I2_ROOT>]";
	</script>
<!--[if lt IE 7]>
<script type="text/javascript">
IE7_PNG_SUFFIX = ".png";
</script>
<script src="[<$I2_ROOT>]www/js/ie7/ie7-standard-p.js" type="text/javascript"></script>
<![endif]-->
	<style type="text/css">
		body {
			background-color:black;
			color:#EFEFEF;
			font-family:Arial, Helvetica, Roboto, sans-serif;
			font-size:10pt;
		}
		a {
			color:#8080FF;
		}
		#censor {
			width:720px;
			margin-top:10%;
			margin-left:auto;
			margin-right:auto;
			color:red;
			text-align:center;
			font-family:serif;
		}
		#login {
			position:fixed;
			bottom:16px;
			right:16px;
		}
		#verisign_box {
			position:fixed;
			bottom:0px;
			left:6px;
		}
		input {
			background-color:black;
			color:#EFEFEF;
			border-color:#EFEFEF;
			border-style:solid;
			border-width:1px;
		}
		h1 {
			font-size:400%;
			margin:0em auto;
		}
		h2 {
			font-size:200%;
		}
		p {
			font-family:Arial, Helvetica, Roboto, sans-serif;
			font-size:10pt;
			color:#EFEFEF;
		}
	</style>
</head>
<body>
<div id="censor">
	<img src="[<$I2_ROOT>]www/pics/censor_stamp.png" alt="Censored"/>
	<br/>
	<h1>CONTENT BLOCKED</h1>
	<!--<h2>Parts of this site have been removed</h2>-->
	<br/><br/>
	<p>If you do not want to see more pages like this, <a href="http://www.sopastrike.com/strike" target="_blank">tell your representatives and senators to stop SOPA and PIPA</a>.
</div>
<div id="login">
	[<if $failed eq 1>]
	<div id="login_failed">
		Your login as [<$uname|escape>] failed.  Maybe your password is incorrect?<br />
	</div>
	[<elseif $failed eq 2>]
	<div id="login_failed">
		Your password and username were correct, but you don't appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.
	</div>
	[<elseif $failed>]
	<div id="login_failed">
		An unidentified error has occurred. Please contact the intranetmaster and tell him you received this error message immediately.
	</div>
	[</if>]
	<form name="login_form" action="[<$I2_SELF>]" method="post">
		[<$posts>]
		Username:
		<input name="login_username" type="text" size="25" value="[<$uname|escape>]"/>
		Password:
		<input name="login_password" type="password" size="25"/>
		<button type="submit">Login</button>
	</form>
</div>
<div id="verisign_box" class="box">
	<table width="100" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose VeriSign SSL for secure confidential communications.">
		<tr>
			<td width="100" align="center" valign="top">
				<script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=iodine.tjhsst.edu&amp;size=S&amp;use_flash=NO&amp;use_transparent=YES&amp;lang=en"></script><br/>
			</td>
		</tr>
	</table>
</div>
