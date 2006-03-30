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
 <div class="title"> Welcome, [<$first_name>]!</div>
 <div class="blurb">Today is [<$smarty.now|date_format:"%B %e, %Y">]. 
 [<if $date != "none">]
 	The next 8th period is [<$date>], and you are currently signed up for 
 	[<foreach from=$activities item="activity" name="activities">]
 		[<if $smarty.foreach.activities.last and not $smarty.foreach.activities.first>]
			and
		[<elseif not $smarty.foreach.activities.first>]
			,
		[</if>]
		[<$activity->name_r>]
	[</foreach>]
 [<else>]
 	There aren't any currently scheduled 8th period activities
 [</if>]
 .</div><br />
 <span id="top_news">
 News: [<foreach from=$news_posts item=story name=titles>]
 <a href="[<$I2_ROOT>]news#newspost[<$story->id>]">[<$story->title>]</a>
 [<if not $smarty.foreach.titles.last>]<span class="bold">&middot;</span>[</if>]
 [</foreach>]
 </span>
</div>
<div class="date">[<include file='core/menu.tpl'>]</div>
