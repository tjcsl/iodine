<!doctype html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="Description" content="The TJ Intranet allows students at the Thomas Jefferson High School for Science and Technology to sign up for activities, access their school files and perform other tasks." />

	<!-- zoom in mobile browsers -->
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=1">
	<title>TJHSST Intranet2: Login</title>

	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&amp;subset=latin,latin-ext,cyrillic-ext,greek-ext,cyrillic,vietnamese,greek" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-ui-light.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-login-default.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-login-light.css" />
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login-schedule.css" />
	<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/schedule.js"></script>
	<script type="text/javascript">
	week_base_url = "news";
	day_base_url = "news";
	week_text = "Back"; //"[<if $has_custom_day>]View [<$schedule.date>][<else>]View Today[</if>]";
	</script>
	<script type="text/javascript">
	//Set some variables so that any script can use them.
	var i2root="[<$I2_ROOT>]";
	[<*
	function parse_bgs() {
		try {
			bid = window.location.search.split('bid=')[1];
		} catch(e) {}
		if(window.location.search.indexOf('bid=')==-1) {
			try {
				bid = (function(){for(i in j=(c=document.cookie).split(';')) if((k=j[i].split('='))[0]=='background'&&!!k[1]) for(l=0;l<(m=document.getElementsByTagName('optgroup')[1].children).length;l++) if(unescape(m[l].value)==unescape(k[1])) return m[l].id.split('bg')[1];})();
			} catch(e) {}
		}
		if(typeof bid != 'undefined' && typeof document.getElementById('bg'+bid) != 'undefined') {
			document.getElementById('bg0').setAttribute('selected', false);
			document.getElementById('bg'+bid).setAttribute('selected', true);
		} else {
			document.getElementById('bg0').setAttribute('selected', true);
		}
	}
	*>]
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
<body style="background-image: url('[<$I2_ROOT>][<$bg|escape>]')" onLoad="document.getElementById('login_username').focus()" class="login">
	<div class="pane" id="mainPane">
		<a id="logo" href="">Intranet</a>

		[<if isset($err)>]
		<div class="login_msg" id="login_failed">
			[<$err>]<br />
		</div>
		[<elseif $failed eq 1>]
		<div class="login_msg" id="login_failed">
			Your login[<if $uname>] as [<$uname|escape>][</if>] failed.  Maybe your password is incorrect?<br />
		</div>
		[<elseif $failed eq 2>]
		<div class="login_msg" id="login_failed">
			Your password and username were correct, but you don't appear to exist in our database.  If this is a mistake, please contact the intranetmaster about it.
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
		<br />

		<form name="login_form" action="[<$I2_SELF|escape>]" method="post">
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

		<div style="text-align:center;">
			<div id="verisign_box" class="box" title="Click to Verify - This site chose VeriSign SSL for secure confidential communications.">
				<script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=iodine.tjhsst.edu&amp;size=S&amp;use_flash=NO&amp;use_transparent=YES&amp;lang=en"></script><br/>
			</div>
		</div>
	</div>
	<div class="pane" id="subPane">
		<p id='sched_tools'>
			<button id="week_b" onclick="window.location=day_base_url+'?day=[<$schedule.yday>]'" style=''>←</button>
			[<if $has_custom_day>]
				<button id="week_today" onclick="window.location=day_base_url+'?day=0'">Today</button>
			[<elseif isset($has_custom_day_tom) and $has_custom_day_tom>]
				<button id="week_today" onclick="window.location=day_base_url+'?day=0&tomorrow'">Tomorrow</button>
			[</if>]
				<button id="week_click" onclick="week_click();">Full Week</button>

			<button id="week_f" onclick="window.location=day_base_url+'?day=[<$schedule.nday>]'">→</button>
		</p>
		<div id="schedule">

			<h2 id="schedule_header">[<$schedule.header>]</h2>
			<p class='desc[<if isset($schedule.schedday)>] schedule-[<$schedule.schedday>][</if>]'>[<$schedule.description>]</p>
			<div style='height: 160px;float: center'>[<$schedule.schedule>]</div>

		</div>

		<div id="schedule_week">
			<p>One moment please..</p>
			<p>If the page doesn't load, <a href='http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar'>click to view the main TJ calendar.</a></p>
		</div>
		<br /><br />
		<ul id="links">
			<li><a href="http://www.tjhsst.edu" target="_blank">TJHSST</a></li>
			<li><a href="https://webmail.tjhsst.edu" target="_blank">Mail</a></li>
			<li><a href="http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar" target="_blank">Calendar</a></li>
			<!--<li><a href="http://www.tjhsst.edu/studentlife/publications/tjtoday" target="_blank">tjTODAY</a></li>-->
		</ul>
		<br />
		[<*
		<form action="[<$I2_SELF|escape>]" method="get">
			<select name="background" onchange="this.form.bid.value=this.options[this.selectedIndex].id.split('bg')[1];this.form.submit()">
				<optgroup label="Change your background image:">
					<option value="random" id="bg0">Surprise me!</option>
				</optgroup>
				<optgroup>
					[<assign var=num value=0>]
					[<foreach from=$backgrounds item=b>]
						[<assign var=num value=$num+1>]
						[<assign var=name value="."|explode:$b>]
						<option value="[<$b|escape>]" id="bg[<$num>]">[<$name[0]|escape>]</option>
					[</foreach>]

				</optgroup>
			</select>
			<input type="hidden" name="bid" value="0">
			<noscript><input type="submit" value="Change Background" /></noscript>
		</form>
		<script type="text/javascript">
		parse_bgs();
		</script>
		*>]
	</div>

	[<* This doesn't work on recent versions of chrome because
	     it is not in the web store; so why advertise it? *>]
	<!--script type="text/javascript">
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

	</script-->

