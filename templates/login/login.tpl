<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
<br /><br /><br />
<table class="middle">
	<tr>
		<td class="box">
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
			<script language="javascript" type="text/javascript">
				document.login_form.login_username.focus();
			</script>
			<div style="text-align: right">
				<a href="http://www.tjhsst.edu/">TJHSST</a> &ndash; <a href="https://webmail.tjhsst.edu/">Mail</a> &ndash; <a href="http://postman.tjhsst.edu/">Postman (Calendar)</a>
			</div>
		</td>
	</tr>
</table>
<div id="verisign_box" class="box" style="position: fixed; bottom: 10px; right: 10px; padding: 0px;">
<table width="100" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose VeriSign SSL for secure confidential communications.">
<tr>
<td width="100" align="center" valign="top"><script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=iodine.tjhsst.edu&amp;size=S&amp;use_flash=NO&amp;use_transparent=YES&amp;lang=en"></script><br />
</table>
</div>

<!-- Chrome app notification -->
<script type="text/javascript">
	<!--
	if (chrome && chrome.app && !chrome.app.isInstalled) {
		var chromeLink = document.createElement("a");
		chromeLink.href = "[<$I2_ROOT>]www/chrome/iodine_chrome_app.crx";
		chromeLink.type = "application/x-chrome-extension";

		var chromeBox = document.createElement("div");
		chromeBox.style.position = "fixed";
		chromeBox.style.top = "8px";
		chromeBox.style.right = "8px";
		chromeBox.style.padding = "4px";
		chromeBox.style.border = "1px solid #343433";
		chromeBox.style.backgroundColor = "#F8F8F7";
		chromeBox.style.opacity = "0.85";
		
		chromeBox.innerHTML = "<img src=\"[<$I2_ROOT>]www/pics/chrome_icon_32.png\" style=\"float:left; margin-right:4px;\" alt=\"\"/>Install the TJ Intranet app for Chrome";

		chromeLink.appendChild(chromeBox);
		document.getElementsByTagName("body")[0].appendChild(chromeLink);
	}
	//-->
</script>

<a style="width:261px;height:80px;vertical-align:middle;text-align:center;background-color:#000;position:absolute;z-index:5555;top:16px;left:2px;background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAUCAIAAADJMG6kAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2hpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDowOTgwMTE3NDA3MjA2ODExOTEwOUFFNjFDRTVBQUE3MCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCOEEzMTFFNjA0RDcxMUUxODZBNERCRTYwOEVCMDhCQyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCOEEzMTFFNTA0RDcxMUUxODZBNERCRTYwOEVCMDhCQyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1LjEgTWFjaW50b3NoIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RTBBODQ1RUExMjIwNjgxMTkxMDlBRTYxQ0U1QUFBNzAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDk4MDExNzQwNzIwNjgxMTkxMDlBRTYxQ0U1QUFBNzAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6uFKqXAAAD50lEQVR42uyXO04cQRCGWTIiuAF7AYQ5AfgEcAQIECE4IGZXIiOBEBHwOAHcwNwAxAWWG0BEiD/NJ5fK3TPrBRFY1vzBqqe2Xl2v7p6b69GjR48e/yoG+WNpaWl7e5vF6emplI2Njczw8PDw8vIi59bW1nA45PP29nYymQTPtwb89bNBbbJVtjCEoJTQMGwQDuAneq6urvzM1lGuYLYOM3+hE/4stdEgM2e7SsGMXR2YNOjSNhMQRt17A3Tp7vuf0AltB5G16QGj0SjzR8JyIFCeeZQtDKkKzXiVNetA+AnDwcFB+E/ashI+QzzTkSJkENljEImXzOGAQWdt0HWA3y5t0zEfK6yurq6+vr5eX1+bMYjj8fj+/p4Fv6yJkaW6uLgIZWdn5+7ujvXl5WUuSTQotb+/n51ALbLLy8tqkycLjn/DTzQTrMJj+PGTxdnZGZsMcUKwubmJ/z8asODTuASgq1a6v4+Pj3jC9iMrM4LtZ215FwsLC51iZswazCZzJgGZjyQLRCyfzIwGqz7H0X+px6CQhrqOiuaIoKjNKosEKI4ema0PW0eKmQ792f/MUNT+LBUdxGJCYvrm5qaI9Xyev9agPduVDwOXZ4JryqcwRqpbZXP+aZFsa9QgBpE4OjqK8AH5MYdd02k78kt7RRZZ8Bl/5UkdSmwpcua0LdJce9IVjQKYPjk5OT8/76zrmH0sYm9FRdelF0Ssymz4nF+5OYqiqzWIXEHqQaF948asSv9VW+Fk7XnsKx82FmxxnLxX6Kro0FbYFSsrK7mu54v8MPhYMATr4VicnDUxioIpTDkzJamID53I3xvE+Wa7MENRSKsFEbXOcawQhdlnq8MdhcaO37W1tefnZ1s52/Wo4LD5qzbaoj7zwdPTEzHc3d1tCbSHOFslRuyttS/wsmiZOO6ibXFxMBh4DciytmprRcetDuQhbu/jT32iECPoxBoG01k47NDImTZwBCjmifc26XnIODponWlX4wb12ImKRuHFxUVLoOMGOqWc49CLOjKf05MfB2khi8UpcY85nmvNgHoZ1TSR0qv19fWYqiysuLwXiPopG4Ixeb72eUKUDw8P9/b23t7eyv+8VMYNnLXhKMZfXLfZP0QvxXGXbJ2V9TGgFWUNRExDwf4LVd6RCbEXjOxnHtPeiGOI10dLiFtSYVTBj946uqJc3zraHyzxjmiNnVfp4CReXSdnq5Usy4a7XkZ1gjFkLeeHSe7u4rmU3cihMcE+g+Pl5XvvSwJ9fHxcR3lQv9zcUn5Vt0LO/EqeHZ+T9dVuMarBx3GRSLMej/VW63GtDP5P7KJHjx49evwn+CXAAJmTFR0xj6zSAAAAAElFTkSuQmCC);background-position:center center;background-repeat:no-repeat;" href="http://americancensorship.org" target="_blank"></a>
