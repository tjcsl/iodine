[<*
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/dayschedule.css" />
[<if $alljs>]
<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
<script type="text/javascript">var i2root = "[<$I2_ROOT>]";</script>
[</if>]
[<if $allcss>]
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&amp;subset=latin,latin-ext,cyrillic-ext,greek-ext,cyrillic,vietnamese,greek" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/i3-ui-light.css" />
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login-schedule.css" />
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/schedule.css" />
<style>body{background:none;}</style>
[</if>]
[<if $afd>]
<script type="text/javascript">
$(function() {
$(".schedule-day.now").addClass("blink").css("color","white");
$(".day-name").html("<marquee behavior='alternate' direction=right>"+$(".day-name").html()+"</marquee>");
$(".day-type").html("<marquee>"+$('.day-type').html()+"</marquee>");
$(".day-right,.day-left").each(function(){$(this).html("<marquee height=50 direction=up><span>"+$(this).html()+"</span></marquee>")});
blink=setInterval(function(){$("blink,.blink").each(function(){$(this).css('visibility',($(this).css('visibility')=='hidden'?'visible':'hidden'))})},400);
});</script>
[</if>]
[<if $iframe>]
<style>.dayschedule div.day-name:hover { visibility: visible; } .dayschedule .day-name div.view-week,.dayschedule .day-name:hover div.view-week { display: none; margin-top: auto; } td { vertical-align: top; }</style>
<body class='login'>
[</if>]
<script type="text/javascript" src="[<$I2_ROOT>]www/js/dayschedule.js"></script>
<script type='text/javascript'>
var currentdate = '[<$date>]';
var dayschedule_type = '[<if isset($type)>][<$type>][<else>]page[</if>]';
var dayschedule_summary = '[<$summaryid>]';
</script>
[<if $summaryid eq "snowday">]
    <script type='text/javascript'> $.getScript('[<$I2_ROOT>]www/js/logins/special/snow.js')</script>
    [<if isset($type) and $type eq "box">]
        <style type="text/css">#snowcontainer { position: fixed; }</style>
    [</if>]
[</if>]
<div class='dayschedule[<if isset($type)>] [<$type>][</if>]'>
	<div class='day-left' onclick='day_jump(-1)' title='Go back one day'>
	&#9668;
	</div>
	<div class='day-right' onclick='day_jump(1)' title='Go forward one day'>
	&#9658;
	</div>
	<div class='day-name'>
		<span[<if $dayname|count_characters > 15>] class='small'[</if>]>[<$dayname>]</span>
		<div class='view-week' onclick='load_week()'>
			Week
			<span class='day-up'>
			&#9660;
			</span>
			View
		</div>
	</div>
	<div class='day-type [<$summaryid>][<if $summary|lower|truncate:8:"" eq "blue day">] blue[<else if $summary|lower|truncate:7:"" eq "red day">] red[</if>]' title='[<$summaryid>]'>
		[<$summary>]
	</div>
	<div class='day-schedule'>
		[<include file='dayschedule/schedule.tpl' schedule=$schedule>]
	</div>
	<div class='day-remaining'>

	</div>
</div>
<script type="text/javascript">
init_dayschedule();
</script>
*>]
<iframe src="https://ion.tjhsst.edu/schedule/embed?iodine"
        seamless=seamless allowtransparency=true width="310" height="275" style="margin: -4px -39px;border: 0 !important"></iframe>
