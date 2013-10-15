<style type="text/css">
.dayschedule {
	width: 300px;
	text-align: center;
}
.dayschedule .day-name {
	font-size: 24px;
}
.dayschedule .day-type {
	font-size: 16px;
}
</style>
<div class='dayschedule'>
	<div class='day-name'>
		[<$dayname>]
	</div>
	<div class='day-type'>
		[<$daytype>]
	</div>
	<div class='day-schedule'>
		[<include file='dayschedule/schedule.tpl' schedule=$schedule>]
	</div>


</div>
