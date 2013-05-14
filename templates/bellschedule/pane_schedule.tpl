
	<!--link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login-schedule.css" /-->
	<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/bellschedule_pane.css" />
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/schedule.js"></script>
	<script type="text/javascript">
	week_base_url = i2root+"bellschedule";
	week_text = "Back";
	</script>
<p id='sched_tools'>
	<button id="week_b" onclick="day_click('[<$yday>]')" style=''>←</button>
	[<if $has_custom_day>]
		<button id="week_today" onclick="day_click(0)">Today</button>
	[</if>]
		<button id="week_click" onclick="week_click();">Week</button>

	<button id="week_f" onclick="day_click('[<$nday>]')">→</button>
</p>
<div id="schedule">

	<h2>[<$header>]</h2>
	<p class='desc[<if isset($schedule.modified)>] desc-modified[</if>]'>[<$schedule.description>]</p>
	<div style='height: 160px;float: center'>[<$schedule.schedule>]</div>

</div>

<div id="schedule_week">
	<p>One moment please..</p>
	<p>If the page doesn't load, <a href='http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar'>click to view the main TJ calendar.</a></p>
</div>
