<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="Description" content="The TJ Intranet allows students at the Thomas Jefferson High School for Science and Technology to sign up for activities, access files, and perform other tasks." />
	<meta name="keywords" content="TJHSST, TJ Intranet, Intranet, Intranet2, Thomas Jefferson High School" />
	<meta name="robots" content="index, follow" />
	<meta name="author" content="The Intranet Development Team" />
	<link rel="image_src" href="[<$I2_ROOT>]www/pics/styles/i3/logo-light.png" />
	<link rel="author" href="http://www.tjhsst.edu/admin/livedoc/index.php/Iodine#Intranet_Credits" />
	<link rel="canonical" href="[<$I2_ROOT>]" />
	<!-- zoom in mobile browsers -->
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=1">
	<title>TJHSST Intranet: Login</title>
	
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&amp;subset=latin,latin-ext,cyrillic-ext,greek-ext,cyrillic,vietnamese,greek" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-ui-light.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-login-default.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-login-light.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login-schedule.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/schedule.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/debug.css" />
    <link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/dayschedule-app.css" />
	<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/login.js"></script>
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/common.js"></script>
    <script type="text/javascript" src="[<$I2_ROOT>]www/js/dayschedule-app.js"></script>
	<script type="text/javascript">
	//Set some variables so that any script can use them.
	var i2root="[<$I2_ROOT>]";
	prep_init = function() {
		common_init();
		init_dayschedule();
		document.getElementById('login_username').focus()
	}
	if(!!window.addEventListener) {
		window.addEventListener("load", prep_init, false);
	} else {
		window.onload = prep_init;
	}
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
	<style type="text/css">
	body {
		background-image: url('[<$I2_ROOT>][<$bg|escape>]')
	}
	</style>
</head>
<body class="login">
[<include file='downtime.tpl'>]
[<$emerg>]
	<div class="pane" id="mainPane">
		<a id="logo" href="[<$I2_ROOT>]" title="TJHSST Intranet">Intranet</a>

		[<if isset($err)>]
		<div class="login_msg" id="login_failed">
			[<$err>]<br />
		</div>
		[<elseif $failed eq 1>]
		<div class="login_msg" id="login_failed">
			Your login[<if $uname>] as [<$uname|escape>][</if>] failed.  Maybe your password is incorrect?<br />
			[<if $smarty.now|date_format:"%B" eq "September">]
			<br />
			Note: If your credentials are not working on all TJ services, you may have to reset your password for the new school year. Log into a school Windows computer on the LOCAL domain and follow the instructions to set a new password.
			[</if>]
		</div>
		[<elseif $failed eq 2>]
		<div class="login_msg" id="login_failed">
			Your password and username were correct, but you don't appear to exist in our database.  If this is a mistake, please contact the intranetmaster about it.
		</div>
		[<elseif $failed eq 3>]
		<div class="login_msg" id="login_failed">
			Your login[<if $uname>] as [<$uname|escape>][</if>] failed. Maybe your username is incorrect?
		</div>
		[<elseif $failed eq 4>]
		<div class="login_msg" id="login_failed">
			Your TJ email address[<if $uname>] ([<$uname|escape>])[</if>] is not your username! Remove the @tjhsst.edu suffix.
		</div>
		[<elseif $failed eq 5>]
		<div class="login_msg" id="login_failed">
			Your account is not yet active for use on Intranet. Patience, young freshman.
		</div>
		[<elseif $failed>]
		<div class="login_msg" id="login_failed">
			An unidentified error has occurred.  Please contact the Intranetmaster and tell him you received this error message.
		</div>
		[<else>]
		<div class="login_msg" id="login_msg">
			Please type your username and password to log in.
		</div>
		[</if>]

		<br />

		<form name="login_form" action="[<$I2_SELF|escape>][<$querystring>]" method="post">
			[<$posts>]
			<label for="login_username">Username</label>
			<br />
			<input name="login_username" id="login_username" type="text" size="25" value="[<$uname|escape>]"/>
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

		<div style="text-align:center">
        [<*
		    <!-- Begin DigiCert site seal HTML and JavaScript -->
            <div id="verisign_box" data-language="en_US">
            </div>
            <script type="text/javascript" async="async">
            var __dcid = __dcid || [];__dcid.push(["verisign_box", "7", "m", "black", "5VDyXkwz"]);(function(){var cid=document.createElement("script");cid.async=true;cid.src="//seal.digicert.com/seals/cascade/seal.min.js";var s = document.getElementsByTagName("script");var ls = s[(s.length - 1)];ls.parentNode.insertBefore(cid, ls.nextSibling);}());
            </script>
            <!-- End DigiCert site seal HTML and JavaScript -->
        *>]
            <div id="verisign_box" data-language="en_US">
            </div>
            <script type="text/javascript">
            var __dcid = __dcid || [];__dcid.push(["verisign_box", "7", "m", "black", "5VDyXkwz"]);
            </script>
            <script type="text/javascript" async="" src="//seal.digicert.com/seals/cascade/seal.min.js"></script>
        </div>
	</div>
	<div class="pane" id="subPane">
		[<include file='dayschedule/login.tpl'>]
		<br />
        <div class="dayschedule-app">
            <b>New: Bell Schedule app for Android</b>
            <em><a href="https://www.tjhsst.edu/~2016jwoglom/uploads/TJDaySchedule/latest.apk">Download now</a></em>
            <span>(enable "Unknown Sources")</span>
        </div>
        <br />
		<ul id="links">
			<li><a href="http://www.tjhsst.edu" target="_blank">TJHSST</a></li>
			<li><a href="https://webmail.tjhsst.edu" target="_blank">Mail</a></li>
			<li><a href="http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar" target="_blank">Calendar</a></li>
			<!--<li><a href="http://www.tjhsst.edu/studentlife/publications/tjtoday" target="_blank">tjTODAY</a></li>-->
		</ul>
		<br />
	</div>

