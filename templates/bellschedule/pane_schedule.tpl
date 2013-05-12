
	<!--link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login-schedule.css" /-->
	<style>
	#schedule_week {
		display: none;
	}
	.desc-modified {
		color: red;
	}
	</style>
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js"></script>
	<script type="text/javascript" src="[<$I2_ROOT>]www/js/logins/schedule.js"></script>
	<script type="text/javascript">
	week_base_url = i2root+"bellschedule";
	week_text = "[<if $has_custom_day>]View [<$schedule.date>][<else>]View Today[</if>]";
	</script>
<p id='sched_tools'>
	<button id="week_b" onclick="window.location='?day=[<$schedule.yday>]'" style=''>&lt;==</button>
	[<if $has_custom_day>]
		<button id="week_today" onclick="window.location='?day=0'">View Today</button>
	[</if>]
		<button id="week_click" onclick="week_click();">View Week</button>

	<button id="week_f" onclick="window.location='?day=[<$schedule.nday>]'">==&gt;</button>
</p>
<div id="schedule">

	<h2>[<$schedule.header>]</h2>
	<p class='desc[<if isset($schedule.modified)>] desc-modified[</if>]'>[<$schedule.description>]</p>
	<div style='height: 160px;float: center'>[<$schedule.schedule>]</div>

</div>

<div id="schedule_week">
	<p>One moment please..</p>
	<p>If the page doesn't load, <a href='http://postman.tjhsst.edu'>click to view the main TJ calendar.</a></p>
</div>