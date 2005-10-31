[<include file="eighth_header.tpl">]
<form action="[<$I2_ROOT>]eighth/sch_activity/modify/aid/[<$aid>]" method="post">
	<input type="submit" value="Modify"><input type="button" value="Select All" onClick=";"><br /><br />
<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px">
	<tr>
		<td>&nbsp;</td>
		<th>Block</th>
		<th>Room(s)</th>
		<th>Sponsor(s)</th>
	</tr>
[<foreach from=$activities item="activity">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td><input type="checkbox" id="[<$activity.block.bid>]_checkbox" name="modify[]" value="[<$activity.block.bid>]" [<if $activity.scheduled >]disabled[</if>]/></td>
		<td style="padding: 5px;">[<$activity.block.date|date_format:"%a">] [<$activity.block.block>], [<$activity.block.date|date_format:"%m/%d/%y">]</td>
		<td style="padding: 5px;">
[<if $activity.scheduled >]
			<div>
				<a href="#" onClick="void(document.getElementById('[<$activity.block.bid>]_checkbox').checked=true);void(this.style.display='none');void(document.getElementById('[<$activity.block.bid>]_rooms').style.display='block');void(document.getElementById('[<$activity.block.bid>]_sponsors').style.display='block');" style="display: block; color: #FF0000;">Reschedule</a>
			</div>
			<div id='[<$activity.block.bid>]_rooms' style="display: none;">
[</if>]
				<select name="room_list[[<$activity.block.bid>]][]" size="3" multiple>
					<option value="" style="color: #FF0000; font-weight: bold;">CANCELLED</option>
[<foreach from=$rooms item='room'>]
					<option value="[<$room.rid>]"[<if in_array($room.rid, explode(",", $activity.rooms))>] selected[</if>]>[<$room.name>]</option>
[</foreach>]
				</select>
[<if $activity.scheduled >]
			</div>
[</if>]
		</td>
		<td style="padding: 5px;">
[<if $activity.scheduled >]
			<div id='[<$activity.block.bid>]_sponsors' style="display: none;">
[</if>]
				<select name="sponsor_list[[<$activity.block.bid>]][]" size="3" multiple>
					<option value="" style="color: #FF0000; font-weight: bold;">CANCELLED</option>
[<foreach from=$sponsors item='sponsor'>]
					<option value="[<$sponsor.sid>]"[<if in_array($sponsor.sid, explode(",", $activity.sponsors))>] selected[</if>]>[<$sponsor.name_comma>]</option>
[</foreach>]
				</select>
[<if $activity.scheduled >]
			</div>
[</if>]
		</td>
	</tr>
[</foreach>]
</table><br />
<input type="submit" value="Modify">
</form>
