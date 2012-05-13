<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<meta name="Description" content="TJHSST Intranet2: What you need, when you need it." />
	
	<!-- zoom in mobile browsers -->
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>TJHSST Intranet2: Login</title>
	
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&subset=latin,latin-ext,cyrillic-ext,greek-ext,cyrillic,vietnamese,greek" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-ui-light.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-login-default.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-login-light.css" />
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
	<div class="pane" id="mainPane">
		<a id="logo" href="">Intranet</a>
		<br />
		<br />
		<br />
		<br />
		<br />
		
		[<if $failed eq 1>]
		<div id="login_failed">
			Your login as [<$uname|escape>] failed.  Maybe your password is incorrect?<br />
		</div>
		[<elseif $failed eq 2>]
		<div id="login_failed">
			Your password and username were correct, but you don't appear to exist in our database.  If this is a mistake, please contact the intranetmaster about it.
		</div>
		[<elseif $failed>]
		<div id="login_failed">
			An unidentified error has occurred.  Please contact the Intramaster and tell him you received this error message.
		</div>
		[<else>]
		Please type your username and password to log in.
		[</if>]
		
		<br />
		<br />
		
		<form name="login_form" action="[<$I2_SELF>]" method="post">
			[<$posts>]
			<label for="login_username">Username</label>
			<br />
			<input name="login_username" id="login_username" type="text" size="25" value="[<$uname|escape>]" />
			<br />
			<br />
			<label for="login_password">Password</label>
			<input name="login_password" id="login_password" type="password" size="25" />
			<br />
			<br />
			<button type="submit">Login</button>
		</form>
		
		<br />
		<br />
		
		<div style="text-align:center;">
			<div id="verisign_box" class="box">
				<table width="100" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose VeriSign SSL for secure confidential communications.">
					<tr>
						<td width="100" align="center" valign="top">
							<script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=iodine.tjhsst.edu&amp;size=S&amp;use_flash=NO&amp;use_transparent=YES&amp;lang=en"></script><br/>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="pane" id="subPane">
		<div id="schedule">
			<h2>Today's Schedule</h2>
			<p>[<$schedule.description>]</p>
			<table>
				[<foreach from=$schedule.schedule item=block>]
				<tr>
					<td>[<$block.pd>]</td>
					<td>[<$block.starttime>]-[<$block.endtime>]</td>
				</tr>
				[</foreach>]
			</table>
		</div>
		<br />
		<ul id="links">
			<li><a href="http://www.tjhsst.edu" target="_blank">TJHSST</a></li>
			<li><a href="https://webmail.tjhsst.edu" target="_blank">Mail</a></li>
			<li><a href="http://postman.tjhsst.edu" target="_blank">Postman (Calendar)</a></li>
		</ul>
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
			chromeBox.style.padding = "4px";
			
			chromeBox.innerHTML = "<img src=\"[<$I2_ROOT>]www/pics/chrome_icon_42.png\" style=\"float:left; margin-right:4px;\" alt=\"Google Chrome logo\"/>Install the TJ Intranet app for Chrome";

			chromeLink.appendChild(chromeBox);
			document.getElementById("mainPane").appendChild(chromeLink);
		}
		
		
		document.login_form.login_username.focus();
		//-->
	</script>
