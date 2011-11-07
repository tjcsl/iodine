<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<!-- prevents errors due to caching; remove soon-ish -->
<!-- <meta http-equiv="Pragma" content="no-cache"/> -->
<!-- <meta http-equiv="Expires" content="-1"/> -->
<!-- <meta http-equiv="CACHE-CONTROL" content="NO-CACHE"/> -->


<title>TJHSST Intranet2[<if $title != "" >]: [<$title>][</if>]</title>
<link type="text/css" rel="stylesheet" href="[<$I2_CSS>]" />

<script type="text/javascript">
//Set some variables so that any script can use them.
var i2root="[<$I2_ROOT>]";
var username="[<$I2_USER->username>]";
var name="[<$I2_USER->name>]";
var fullname="[<$I2_USER->fullname>]";
var userid=[<$I2_USER->iodineUIDNumber>];
</script>
<script type="text/javascript" src="[<$I2_JS>]" ></script>
<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
<!--[if lt IE 7]>
<script type="text/javascript">
IE7_PNG_SUFFIX = ".png";
</script>
<script src="[<$I2_ROOT>]www/js/ie7/ie7-standard-p.js" type="text/javascript"></script>
<![endif]-->
</head>
<body>
<script type="text/javascript">
	window.addEventListener("load", page_init, false);
</script>
<div style="height:100%; width:100%; position: fixed; top:0; left: 0; visibility: hidden; z-index:3">
<div id="chat_area" style="float:right; height:100%">
</div>
</div>
<div id="logo" class="logo"><a href="[<$I2_ROOT>]"><span id="logotext">Intranet 2</span></a></div>
<div class="header">
 <div class="title"> [<if $I2_USER->borntoday()>]Happy Birthday[<else>]Welcome[</if>], [<if $I2_USER->uid == 12937>]compressed-air-abuser[<else>][<$I2_USER->firstornick>][</if>]!</div>
 <div class="blurb">Today is [<$smarty.now|date_format:"%B %e, %Y">]. 
 [<if $date != "none">]
 	[<if $I2_USER->grade=="staff">]
		<a href="[<$I2_ROOT>]eighth/vcp_attendance">View All Rosters</a>.
	 	[<if !empty($hosting)>]The next 8th period is [<$date>], and you are currently sponsoring 
 		[<foreach from=$hosting item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_attendance/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>[</foreach>].[</if>]
		<form action="[<$I2_ROOT>]eighth/vcp_schedule" method="post" name="scheduleform" id="form" style="margin: 5px 0px;">
			<input type="hidden" name="op" value="search" />
			Name/Student ID: <input type="text" name="name_id" id="query"/>
			<input type="submit" value="View Eighth Period Schedule" />
			<input type="submit" value="Search Student Directory" onclick="document.getElementById('form').action='[<$I2_ROOT>]StudentDirectory/search/';document.getElementById('query').name='studentdirectory_query';"/>
		</form>
	[<elseif $I2_USER->grade=="TJStar">]
	[<elseif $I2_USER->grade=="graduate">]
		<!--Whoohoo! An Alumn who reads the source code! You're pretty cool! --!>
	 	[<if !empty($hosting)>]The next 8th period is [<$date>], and you are currently sponsoring 
 		[<foreach from=$hosting item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_attendance/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>[</foreach>].[</if>]
	[<else>]
	 	The next 8th period is [<$date>], and you are currently signed up for 
 		[<foreach from=$activities item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$I2_USER->uid>]/bids/[<$activity->bid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>[</foreach>][<if empty($hosting)>].[<else>] and are sponsoring 
 		[<foreach from=$hosting item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_attendance/view/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_friendly>][<if $activity->cancelled>] - CANCELLED[</if>]</a>[</foreach>].
		[</if>]
	[</if>]
 [</if>]
 </div>
</div>
<div class="date">[<include file='core/menu.tpl'>]</div>
