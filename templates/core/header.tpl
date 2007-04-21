<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title>TJHSST Intranet2[<if $title != "" >]: [<$title>][</if>]</title>
<link type="text/css" rel="stylesheet" href="[<$I2_CSS>]" />
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
<div class="logo"><a href="[<$I2_ROOT>]"><span id="logotext">Intranet 2</span></a></div>
<div class="header">
 <div class="title"> [<if $I2_USER->borntoday()>]Happy Birthday[<else>]Welcome[</if>], [<$I2_USER->firstornick>]!</div>
 <div class="blurb">Today is [<$smarty.now|date_format:"%B %e, %Y">]. 
 [<if $date != "none">]
 	[<if $I2_USER->is_group_member('grade_staff')>]
		<a href="[<$I2_ROOT>]eighth/vcp_attendance">View Eighth-period Rosters</a>
		<form action="[<$I2_ROOT>]eighth/vcp_schedule" method="post" name="scheduleform" style="margin: 5px 0px;">
			<input type="hidden" name="op" value="search" />
			Name/Student ID: <input type="text" name="name_id" />
			<input type="submit" value="View Eighth Period Schedule" />
		</form>
	[<else>]
	 	The next 8th period is [<$date>], and you are currently signed up for 
 		[<foreach from=$activities item="activity" name="activities">]
 			[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
				and
			[<elseif not $smarty.foreach.activities.first>]
				,
			[</if>]
			<a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$I2_USER->uid>]/bids/[<$activity->bid>]">[<$activity->name_friendly>]</a>[</foreach>].
	[</if>]
 [</if>]
 </div>
</div>
<div class="date">[<include file='core/menu.tpl'>]</div>
