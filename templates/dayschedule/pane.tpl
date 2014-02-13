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
[</if>]
[<if $iframe>]
<style>.dayschedule div.day-name:hover { visibility: visible; } .dayschedule .day-name div.view-week,.dayschedule .day-name:hover div.view-week { display: none; margin-top: auto; }</style>
<body class='login'>
[</if>]
<script type="text/javascript" src="[<$I2_ROOT>]www/js/dayschedule.js"></script>
<script type='text/javascript'>
var currentdate = '[<$date>]';
var dayschedule_type = '[<if isset($type)>][<$type>][<else>]page[</if>]';
</script>
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
	<div class='day-type [<$summaryid>]' title='[<$summaryid>]'>
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
