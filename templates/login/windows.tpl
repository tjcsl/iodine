<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title>Welcome to Microsoft Intranet</title>
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
		@font-face {
			font-family:"Segoe UI";
			font-weight:bold;
			font-style:italic;
			src:local("Segoe UI Bold Italic"), local("SegoeUI-BoldItalic"), url([<$I2_ROOT>]www/fonts/segoeui/segoeuiz.ttf);
		}
		@font-face {
			font-family:"Segoe UI";
			font-weight:bold;
			src:local("Segoe UI Bold"), local("SegoeUI-Bold"), url([<$I2_ROOT>]www/fonts/segoeui/segoeuib.ttf);
		}
		@font-face {
			font-family:"Segoe UI";
			font-style:italic;
			src:local("Segoe UI Italic"), local("SegoeUI-Italic"), url([<$I2_ROOT>]www/fonts/segoeui/segoeuii.ttf);
		}
		@font-face {
			font-family:"Segoe UI";
			src:local("Segoe UI"), local("SegoeUI"), url([<$I2_ROOT>]www/fonts/segoeui/segoeui.ttf);
		}
		
		* {
			font-family:"Segoe UI", Arial, Helvetica, sans-serif;
		}
		body {
			background-color:#1D5F7A;
			color:white;
			font-family:"Segoe UI", Arial, Helvetica, sans-serif;
			text-align:center;
		}
		.login.box {
			position:absolute;
			top:20%;
			left:0px;
			right:0px;
			text-align:center;
		}
		.login.box table {
			margin:auto;
		}
		input {
			font-family:"Segoe UI", Arial, Helvetica, sans-serif;
			
			border-style:solid;
			border-width:1px;
			border-color:#8EAFBC;
			-webkit-border-radius:3px;
			   -moz-border-radius:3px;
			        border-radius:3px;
			
			outline-style:solid;
			outline-width:1px;
			outline-color:#2C628B;
			padding:2px;
		}
		.login.box button {
			width:29px;
			height:29px;
			border-style:none;
			border-width:0px;
			background-color:transparent;
			background-image:url([<$I2_ROOT>]www/pics/styles/msoffice/go_btn.png);
			color:transparent;
			
			outline-style:none;
			outline-width:1px;
		}
		.login.box button:hover, .login.box button:focus {
			background-position:-29px 0px;
		}
		.login.box button:active {
			background-position:-58px 0px;
		}
		.login.box button:disabled {
			background-position:-87 0px;
		}
		.logo {
			position:absolute;
			bottom:6px;
			left:0px;
			right:0px;
			font-size:20pt;
			text-align:center;
			vertical-align:middle;
		}

		.logo img {
			vertical-align:top;
		}
		#verisign_box {
			display:none;
		}
	</style>
</head>
<body>
<div class="logo">
	<img src="[<$I2_ROOT>]www/pics/styles/msoffice/win_logo.png" alt="" />
	Intranet
</div>
<div class="login box">
	<img src="[<$I2_ROOT>]www/pics/styles/msoffice/win_user_icon.png" alt="" />
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
	<!--Please type your username and password to login to the Intranet.-->
	[</if>]
	<form name="login_form" action="[<$I2_SELF>]" method="post">
		[<$posts>]
		<table>
			<tr>
				<td rowspan="2">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
				<td>
					<noscript>Username:</noscript>
					<script type="text/javascript">
						if(typeof document.createElement("input").placeholder == "undefined") {
							document.write("Username:");
						}
					</script>
					<input name="login_username" type="text" size="25" value="[<$uname|escape>]" placeholder="Username" tabindex="1" />
				</td>
				<td rowspan="2">
					<button type="submit" tabindex="3">&rarr;</button>
				</td>
			</tr>
			<tr>
				<td>
					<noscript>Password:</noscript>
					<script type="text/javascript">
						if(typeof document.createElement("input").placeholder == "undefined") {
							document.write("Password:");
						}
					</script>
					<input name="login_password" type="password" size="25" placeholder="Password" tabindex="2" />
			</tr>
		</table>
	</form>
<!--	<div style="text-align: right">
		<a href="http://www.tjhsst.edu/">TJHSST</a> &ndash; <a href="https://webmail.tjhsst.edu/">Mail</a> &ndash; <a href="http://postman.tjhsst.edu/">Postman (Calendar)</a>
	</div>-->
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
		//document.getElementsByTagName("body")[0].appendChild(chromeLink);
	}
	
	
	document.login_form.login_username.focus();
	//-->
</script>
