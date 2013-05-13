<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<meta name="Description" content="TJHSST Intranet2: What you need, when you need it." />
	<title>TJHSST Intranet2: Login</title>
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login.css" />
	<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<script type="text/javascript">
	//Set some variables so that any script can use them.
	var i2root="[<$I2_ROOT>]";
	</script>
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
<div class="login box">
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
	[<else>]
	Please type your username and password to login to the Intranet.
	[</if>]
	<form name="login_form" action="[<$I2_SELF>]" method="post">
		[<$posts>]
		<table id="login_box">
			<tr>
				<td>Username:</td>
				<td><input name="login_username" type="text" size="25" value="[<$uname|escape>]" /></td>
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
	<div style="text-align: right">
		<a href="http://www.tjhsst.edu/">TJHSST</a> &ndash; <a href="https://webmail.tjhsst.edu/">Mail</a> &ndash; <a href="http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar">Calendar</a>
	</div>
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

<!-- Chrome app notification -->
<script type="text/javascript">
	<!--
	if (!!chrome && !!chrome.app && !chrome.app.isInstalled) {
		var chromeLink = document.createElement("a");
		chromeLink.href = "[<$I2_ROOT>]www/chrome/iodine_chrome_app.crx";
		chromeLink.type = "application/x-chrome-extension";

		var chromeBox = document.createElement("div");
		chromeBox.className = "box";
		chromeBox.style.position = "absolute";
		chromeBox.style.top = "8px";
		chromeBox.style.right = "8px";
		chromeBox.style.padding = "4px";
		
		chromeBox.innerHTML = "<img src=\"[<$I2_ROOT>]www/pics/chrome_icon_32.png\" style=\"float:left; margin-right:4px;\" alt=\"Google Chrome logo\"/>Install the TJ Intranet app for Chrome";

		chromeLink.appendChild(chromeBox);
		document.getElementsByTagName("body")[0].appendChild(chromeLink);
	}
	
	
	document.login_form.login_username.focus();
	//-->
</script>
