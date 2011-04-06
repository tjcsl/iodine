<script type='text/javascript'>
var event_strings = new Array();
[<foreach from=$weeks item=week>]
[<foreach from=$week item=day name=dayloop>]
[<foreach from=$day.info item=event >]
event_strings["[<$event.id>]"]="[<$event.text|escape:'javascript'>]";
[</foreach>]
[</foreach>]
[</foreach>]
function showevent($id) {
	var disp = document.getElementById('popupdiv');
	disp.style.display='block';
	disp.innerHTML=event_strings[$id];
}
</script>
<a href="[<$I2_ROOT>]calendar/add">Add Event</a>
<div class='calholder'>
	<div style='width:100%'>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-left:1px solid black;border-bottom:1px solid black;'>Sunday</div>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-left:1px solid black;border-bottom:1px solid black;'>Monday</div>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-left:1px solid black;border-bottom:1px solid black;'>Tuesday</div>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-left:1px solid black;border-bottom:1px solid black;'>Wednesday</div>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-left:1px solid black;border-bottom:1px solid black;'>Thursday</div>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-left:1px solid black;border-bottom:1px solid black;'>Friday</div>
		<div style='text-align:center;width:14%;padding-bottom:5px;padding-top:5px;float:left;border-top: 1px solid black;background-color:rgb(200,200,200);border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'>Saturday</div>
	</div>
[<foreach from=$weeks item=week name=weekloop>]
	<div style='width:100%'>
	[<foreach from=$week item=day name=dayloop>]
		[<if $smarty.foreach.dayloop.last>]
			<div style='width:14%;height:120px;float:left;background-color:rgb([<$day.monthodd>],[<$day.monthodd>],[<$day.monthodd>]);border-right:1px solid black;border-left:1px solid black;border-bottom:1px solid black;'>
		[<else>]
			<div style='width:14%;height:120px;float:left;background-color:rgb([<$day.monthodd>],[<$day.monthodd>],[<$day.monthodd>]);border-left:1px solid black;border-bottom:1px solid black;'>
		[</if>]
			<div style='float:right;border-left:1px solid black;border-bottom:1px solid black;border-bottom-left-radius:5px;-moz-border-radius-bottomleft:5px;width:25px;height:20px;text-align:center;font-size:13pt'>[<$day.day>]</div>
			<div style='margin-top:3px;margin-left:6px;margin-right:6px'>
				[<foreach from=$day.info item=event >]
					<a onclick="showevent('[<$event.id>]')">[<$event.title>]</a><br />
				[</foreach>]
			</div>
		</div>
	[</foreach>]
	</div>
[</foreach>]
</div>
<div id='popupdiv'></div>
