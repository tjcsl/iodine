[<* included in bellschedule/schedule.tpl and login/login.tpl *>]
<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/schedule.js"></script>
<p id='sched_tools'>
	<button id="week_b[<$box>]" onclick="schedule_click[<$box>]('[<$day-1>]')" style=''>←</button>
	[<if $has_custom_day>]
		[<if $tomorrow>]
			<button id="week_today[<$box>]" onclick="schedule_click[<$box>]('1')">Tomorrow</button>
		[<else>]
			<button id="week_today[<$box>]" onclick="schedule_click[<$box>]('0')">Today</button>
		[</if>]
	[<else>] [<* keep a space here so that the buttons always stay in the same place *>]
		[<if $is_intrabox>]
			<button style="visibility: hidden">[<if $tomorrow>]Tomorrow[<else>]Today[</if>]</button>
		[</if>]
	[</if>]
	[<if !$is_intrabox>]
		<button id="week_click" onclick="week_click('0');">Week</button>
		<button id="week_thiswk" onclick="schedule_reset()" style='display: none'>This Week</button>
	[</if>]
	<button id="week_f[<$box>]" onclick="schedule_click[<$box>]('[<$day+1>]')">→</button>
</p>
<div id="schedule[<$box>]">

	<h2 id="schedule_header">[<$header>]</h2>
	<p class='desc[<if isset($schedday)>] schedule-[<$schedday>][</if>]'>[<$schedule.description>]</p>
	<div style='height: 180px;float: center'>[<$schedule.schedule>]</div>

</div>
<div id="schedule_week[<$box>]">
	<p>One moment please..</p>
	<p>If the page doesn't load, <a href='http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar'>click to view the main TJ calendar.</a></p>
</div>
