<p id='sched_tools'>
	<button id="week_b" onclick="day_click('[<$day-1>]')" style=''>←</button>
	[<if $has_custom_day>]
		[<if $tomorrow>]
			<button id="week_today" onclick="day_click('1')">Tomorrow</button>
		[<else>]
			<button id="week_today" onclick="day_click('0')">Today</button>
		[</if>]
	[</if>]
	<button id="week_click" onclick="week_click();">Week</button>
	<button id="week_f" onclick="day_click('[<$day+1>]')">→</button>
</p>
<div id="schedule">

	<h2 id="schedule_header">[<$header>]</h2>
	<p class='desc[<if isset($schedday)>] schedule-[<$schedday>][</if>]'>[<$schedule.description>]</p>
	<div style='height: 160px;float: center'>[<$schedule.schedule>]</div>

</div>

<div id="schedule_week">
	<p>One moment please..</p>
	<p>If the page doesn't load, <a href='http://www.calendarwiz.com/calendars/calendar.php?crd=tjhsstcalendar'>click to view the main TJ calendar.</a></p>
</div>
