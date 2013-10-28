<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/dayschedule.css" />
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
		[<$dayname>]
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
