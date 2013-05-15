[<* included in bellschedule/schedule.tpl and login/login.tpl *>]
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/schedule.css" />
<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/schedule.js"></script>
<p id='sched_tools'>
	<button id="week_b[<$box>]" onclick="day_click[<$box>]('[<$day-1>]')" style=''>←</button>
	[<if $has_custom_day>]
		[<if $tomorrow>]
			<button id="week_today[<$box>]" onclick="day_click[<$box>]('1')">Tomorrow</button>
		[<else>]
			<button id="week_today[<$box>]" onclick="day_click[<$box>]('0')">Today</button>
		[</if>]
	[</if>]
	[<if !$is_intrabox>]
		<button id="week_click" onclick="week_click();">Week</button>
	[</if>]
	<button id="week_f[<$box>]" onclick="day_click[<$box>]('[<$day+1>]')">→</button>
</p>
<div id="schedule[<$box>]">

	<h2 id="schedule_header">[<$header>]</h2>
	<p class='desc[<if isset($schedday)>] schedule-[<$schedday>][</if>]'>[<$schedule.description>]</p>
	<div style='height: 160px;float: center'>[<$schedule.schedule>]</div>

</div>
<div id="schedule_week">
	<p>One moment please..</p>
	<p>If the page doesn't load, <a href='http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar'>click to view the main TJ calendar.</a></p>
</div>
