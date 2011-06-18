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
	var disp = document.getElementById('popupdivcontent');
	disp.innerHTML=event_strings[$id];
	var holder= document.getElementById('calholder');
	holder.style.top='-122px';
}
function hideevent() {
	var disp = document.getElementById('popupdiv');
	disp.style.display='none';
	var holder= document.getElementById('calholder');
	holder.style.top='0px';
}
</script>
[<if $I2_USER->iodineUIDNumber != 9999>]
	<a href="[<$I2_ROOT>]calendar/add">Add Event</a>
[</if>]
[<*[<$extraline>]*>]
<div id='popupdiv'>
<div id='popupdivcloser' onclick="hideevent()" style="float:right">X</div>
<div id='popupdivcontent'></div>
</div>
<div id='calholder' class='calholder'>
	<div class="calhead">
		<div>Sunday</div>
		<div>Monday</div>
		<div>Tuesday</div>
		<div>Wednesday</div>
		<div>Thursday</div>
		<div>Friday</div>
		<div>Saturday</div>
	</div>
[<foreach from=$weeks item=week name=weekloop>]
	<div class="calweek">
	[<foreach from=$week item=day name=dayloop>]
		[<if $day.monthodd>]
			<div class="odd">
		[<else>]
			<div class="even">
		[</if>]
			<div class='calday'>[<$day.day>]</div>
			<div class='calevents'>
				[<foreach from=$day.info item=event >]
					<a onclick="showevent('[<$event.id>]')">[<$event.title>]</a><br />
				[</foreach>]
			</div>
		</div>
	[</foreach>]
	</div>
[</foreach>]
</div>
[<*
<div id='dayholder' class='dayholder'>
	[<section name=hours loop=24 start=0>]
	<div class='hourholder' style='width:100%'>
		[<$smarty.section.hours.iteration>]
	</div>
	[</section>]
</div>*>]
