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
<a style="width:261px;height:80px;vertical-align:middle;text-align:center;background-color:#000;position:absolute;z-index:5555;top:16px;left:2px;background-image:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAAUCAIAAADJMG6kAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2hpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDowOTgwMTE3NDA3MjA2ODExOTEwOUFFNjFDRTVBQUE3MCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpCOEEzMTFFNjA0RDcxMUUxODZBNERCRTYwOEVCMDhCQyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpCOEEzMTFFNTA0RDcxMUUxODZBNERCRTYwOEVCMDhCQyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1LjEgTWFjaW50b3NoIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RTBBODQ1RUExMjIwNjgxMTkxMDlBRTYxQ0U1QUFBNzAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDk4MDExNzQwNzIwNjgxMTkxMDlBRTYxQ0U1QUFBNzAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6uFKqXAAAD50lEQVR42uyXO04cQRCGWTIiuAF7AYQ5AfgEcAQIECE4IGZXIiOBEBHwOAHcwNwAxAWWG0BEiD/NJ5fK3TPrBRFY1vzBqqe2Xl2v7p6b69GjR48e/yoG+WNpaWl7e5vF6emplI2Njczw8PDw8vIi59bW1nA45PP29nYymQTPtwb89bNBbbJVtjCEoJTQMGwQDuAneq6urvzM1lGuYLYOM3+hE/4stdEgM2e7SsGMXR2YNOjSNhMQRt17A3Tp7vuf0AltB5G16QGj0SjzR8JyIFCeeZQtDKkKzXiVNetA+AnDwcFB+E/ashI+QzzTkSJkENljEImXzOGAQWdt0HWA3y5t0zEfK6yurq6+vr5eX1+bMYjj8fj+/p4Fv6yJkaW6uLgIZWdn5+7ujvXl5WUuSTQotb+/n51ALbLLy8tqkycLjn/DTzQTrMJj+PGTxdnZGZsMcUKwubmJ/z8asODTuASgq1a6v4+Pj3jC9iMrM4LtZ215FwsLC51iZswazCZzJgGZjyQLRCyfzIwGqz7H0X+px6CQhrqOiuaIoKjNKosEKI4ema0PW0eKmQ792f/MUNT+LBUdxGJCYvrm5qaI9Xyev9agPduVDwOXZ4JryqcwRqpbZXP+aZFsa9QgBpE4OjqK8AH5MYdd02k78kt7RRZZ8Bl/5UkdSmwpcua0LdJce9IVjQKYPjk5OT8/76zrmH0sYm9FRdelF0Ssymz4nF+5OYqiqzWIXEHqQaF948asSv9VW+Fk7XnsKx82FmxxnLxX6Kro0FbYFSsrK7mu54v8MPhYMATr4VicnDUxioIpTDkzJamID53I3xvE+Wa7MENRSKsFEbXOcawQhdlnq8MdhcaO37W1tefnZ1s52/Wo4LD5qzbaoj7zwdPTEzHc3d1tCbSHOFslRuyttS/wsmiZOO6ibXFxMBh4DciytmprRcetDuQhbu/jT32iECPoxBoG01k47NDImTZwBCjmifc26XnIODponWlX4wb12ImKRuHFxUVLoOMGOqWc49CLOjKf05MfB2khi8UpcY85nmvNgHoZ1TSR0qv19fWYqiysuLwXiPopG4Ixeb72eUKUDw8P9/b23t7eyv+8VMYNnLXhKMZfXLfZP0QvxXGXbJ2V9TGgFWUNRExDwf4LVd6RCbEXjOxnHtPeiGOI10dLiFtSYVTBj946uqJc3zraHyzxjmiNnVfp4CReXSdnq5Usy4a7XkZ1gjFkLeeHSe7u4rmU3cihMcE+g+Pl5XvvSwJ9fHxcR3lQv9zcUn5Vt0LO/EqeHZ+T9dVuMarBx3GRSLMej/VW63GtDP5P7KJHjx49evwn+CXAAJmTFR0xj6zSAAAAAElFTkSuQmCC);background-position:center center;background-repeat:no-repeat;" href="http://americancensorship.org" target="_blank"></a>

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
