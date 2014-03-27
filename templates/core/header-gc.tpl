<!doctype html>
<html>
<head>
<script>_start = +new Date()</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- Automatically zooms on mobile devices in certain (mobile-optimized) themes. -->
<!-- This list of themes should really not be hard-coded, but it is for now.  Sue me. -->

<meta name="viewport" content="width=device-width,initial-scale=1" />

<!-- prevents errors due to caching-->
<!-- <meta http-equiv="Pragma" content="no-cache"/> -->
<!-- <meta http-equiv="Expires" content="-1"/> -->
<!-- <meta http-equiv="CACHE-CONTROL" content="NO-CACHE"/> -->


<title>====TJ INTRANET[<if $title != "" >]: [<$title>][</if>]====</title>
    <script type="text/javascript">
var titlesi = 0;
setInterval(function() {
    var m = "TJ INTRANET[<if $title != "" >]: [<$title>][</if>]",
    titles = [
        "===="+m+"====",
        ">==="+m+"===<",
        "=>=="+m+"==<=",
        ">=>="+m+"=<=<",
        "=>=>"+m+"<=<=",
        "==>="+m+"=<==",
        "===>"+m+"<==="
    ];
    document.title = titles[titlesi++];
    if(titlesi >= titles.length) titlesi = 0;
}, 250);
noads = function() {
	document.cookie = "noads=true; expires="+new Date(+new Date()+(1000*60*60*48)).toGMTString();
	console.log("No more ads for you!");
	$(".ad1, .ad2").remove();
}
if(document.cookie.indexOf('noads=true') == -1) {
console.log("Ads incoming");
    setTimeout(function() {
        setInterval(function() {
            var t = Math.floor(Math.random() * ($(window).height() - 200));
            var l = Math.floor(Math.random() * ($(window).width() - 200));
            $(".ad1").css({'top': t, 'left': l}).click(function() { $(this).remove(); noads(); });
        }, 2500);
    }, 10000)
    setTimeout(function() {
        setInterval(function() {
            var t = Math.floor(Math.random() * ($(window).height() - 200));
            var l = Math.floor(Math.random() * ($(window).width() - 200));
            $(".ad2").css({'top': t, 'left': l}).click(function() { $(this).remove(); noads(); });
        }, 2500);
    }, 13700);
} else console.log("No ads for you!");
    </script>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&amp;subset=latin,latin-ext,cyrillic-ext,greek-ext,cyrillic,vietnamese,greek" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/debug.css" />
<link type="text/css" rel="stylesheet" href="[<$I2_ROOT>]www/gc/core.css" />
<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js">/* woo hoo jquery */</script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/common.js"></script>
<script type="text/javascript" src="[<$I2_JS>]" ></script>
<script type="text/javascript">
//Set some variables so that any script can use them.
var i2root="[<$I2_ROOT>]";
var username="[<$I2_USER->username>]";
var name="[<$I2_USER->name>]";
var fullname="[<$I2_USER->fullname>]";
var userid=[<$I2_USER->iodineUIDNumber>];
prep_init = function() {
	common_init(); // common js
	page_init(); // theme js
}
if(!!window.addEventListener) {
	window.addEventListener("load", prep_init, false);
} else {
	window.onload = prep_init;
}
</script>
<style>
body {
        color: white;
    }
    .date {
        float: right;
    }
    .date a {
        color: white;
    }
    .ad1, .ad2 {
        position: fixed;
        top: -999px;
        left: -999px;
        width: 300px;
        height: 300px;
        cursor: pointer;
    }
    .ad1 {
        background-image: url('[<$I2_ROOT>]www/gc/winner1.gif');
    }
    .ad2 {
        background-image: url('[<$I2_ROOT>]www/gc/winner2.gif');
    }
</style>
<link rel="icon" type="image/gif" href="[<$I2_ROOT>]www/gc/iewin.gif" />
<link rel="shortcut icon" type="image/gif" href="[<$I2_ROOT>]www/gc/iewin.gif" />
<!--[if lt IE 7]>
<script type="text/javascript">
IE7_PNG_SUFFIX = ".png";
</script>
<script src="[<$I2_ROOT>]www/js/ie7/ie7-standard-p.js" type="text/javascript"></script>
<![endif]-->
</head>
<body class="i3 i3-light" background="[<$I2_ROOT>]www/gc/stars.bmp">
<div class="ad1"></div>
<div class="ad2"></div>
<table border=2 width="100%" height="100%">
<tr>
<td rowspan=2 width=256 style="background-image:url('[<$I2_ROOT>]www/gc/fire.gif');background-repeat:no-repeat;background-position:center center" class="header_td">
<div class="header_div">
<marquee direction=up behaviour=alternate>
<img src="[<$I2_ROOT>]www/gc/l_i.gif" />
<img src="[<$I2_ROOT>]www/gc/l_n.gif" />
<img src="[<$I2_ROOT>]www/gc/l_t.gif" />
<img src="[<$I2_ROOT>]www/gc/l_r.gif" />
<img src="[<$I2_ROOT>]www/gc/l_a.gif" />
<img src="[<$I2_ROOT>]www/gc/l_n.gif" />
<img src="[<$I2_ROOT>]www/gc/l_e.gif" />
<img src="[<$I2_ROOT>]www/gc/l_t.gif" />
</marquee>
</div>
<div class="header_placer">

&nbsp; <img src="[<$I2_ROOT>]www/gc/l_i.gif" style="visibility: hidden"/> &nbsp;
</div>
</td>
<td class="nav_td">
<div class="date">
[<include file='core/menu.tpl'>]
</div>
</td>
</tr>
<tr>
<td>
<div class="header" style="background-image:url('[<$I2_ROOT>]www/gc/flame.gif');background-repeat:repeat-x;background-position:center bottom">
 <marquee class="title" style="color:red;font-size:32px"> [<if $I2_USER->borntoday()>]Happy Birthday[<else>]Welcome[</if>], [<$I2_USER->firstornick>]!</marquee>
 <div class="blurb"><span class='hid'>Today is [<$smarty.now|date_format:"%B %e, %Y">]. 
 [<if $date != "none">]
 	[<if $I2_USER->grade=="staff">]
		<a href="[<$I2_ROOT>]eighth/vcp_attendance">View All Rosters</a>.
	 	[<if !empty($hosting)>]The next 8th period is [<$date>], and </span><span class='show'><span class='c'>y</span>ou are currently sponsoring 
 		[<foreach from=$hosting item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_attendance/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>[</foreach>].</span>[</if>]
	[<elseif $I2_USER->grade=="TJStar">]
	[<elseif $I2_USER->grade=="graduate">]
		<!--Whoohoo! An Alumn who reads the source code! You're pretty cool! --!>
	 	[<if !empty($hosting)>]The next 8th period is [<$date>], and </span><span class='show'><span class='c'>y</span>ou are currently sponsoring 
 		[<foreach from=$hosting item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_attendance/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>[</foreach>].</span>[</if>]
	[<else>]
		The next 8th period is [<$date>][<if !empty($activities) || !empty($hosting)>], and </span><span class='show'><span class='c'>y</span>ou are currently[</if>]

		[<if !empty($activities)>] signed up for
			[<foreach from=$activities item="activity" name="activities">]
				[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
					and
				[<elseif not $smarty.foreach.activities.first>]
					,
				[</if>]
				<a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$I2_USER->uid>]/bids/[<$activity->bid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>
			[</foreach>]</span>[<if empty($hosting)>].[</if>]
		[</if>]

		[<if !empty($hosting)>]
			[<if !empty($activities)>] and are [</if>]
		sponsoring

 		[<foreach from=$hosting item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_attendance/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>
		[</foreach>].
		[</if>]
	[</if>]
 [</if>]
 </div>
<!-- <div id="searchcontainer">-->
 	[<if $I2_USER->grade == "staff">]
 		<form action="[<$I2_ROOT>]eighth/vcp_schedule" method="post" name="scheduleform" id="form" style="margin: 1px 0px;">
 			<input type="hidden" name="op" value="search" />
 			<label for="query">Name/Student ID:</label>
 			<input type="search" name="name_id" id="query" results="0"/>
 			<button type="submit">View Eighth Schedule</button>
 			<button type="submit" onclick="document.getElementById('form').action='[<$I2_ROOT>]StudentDirectory/search/';document.getElementById('query').name='q';document.getElementById('form').method='get';">Search Directory</button>
 		</form>
 	[<elseif $I2_USER->grade == "TJStar">]
 	[<else>]
		<form action="[<$I2_ROOT>]studentdirectory/search/" method="get" id="form" style="margin: 1px 0px;">
			<input type="hidden" name="op" value="search" />
			<div>
				<input type="search" name="q" id="query" results="0" placeholder="Search the directory" />
			</div>
			<button type="submit" id="studentsearchbtn">Search</button>
		</form>
 	[</if>]
<!-- </div>-->
</div>
</td>
</tr>
<tr>
<td valign="top" width=256 bgcolor=green class="ibox_td">
<!-- IBOXES -->
