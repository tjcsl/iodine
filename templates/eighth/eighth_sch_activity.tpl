[<include file="eighth/eighth_header.tpl">]
<form action="[<$I2_ROOT>]eighth/sch_activity/modify/aid/[<$aid>]" method="post">
	<input type="submit" value="Modify" /><input type="button" value="Select All" onclick=";" /><br /><br />
<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px; width: 100%;">
	<tr>
		<td style="width:  20px; text-align: left;">&nbsp;</td>
		<th style="padding: 5px; text-align: left; width: 120px;">Block</th>
		<th style="padding: 5px; text-align: left;">Room(s)</th>
		<th style="padding: 5px; text-align: left;">Sponsor(s)</th>
		<td</td>
	</tr>
[<foreach from=$activities item="activity">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td style="text-align: center;"><input type="checkbox" name="modify[]" value="[<$activity.block.bid>]"[<if $activity.scheduled>] id="check_[<$activity.block.bid>]"[</if>] /></td>
		<td style="padding: 5px 5px 5px 0px;">
			[<$activity.block.date|date_format:"%a">] [<$activity.block.block>], [<$activity.block.date|date_format:"%m/%d/%y">]
[<if $activity.scheduled>]
			<script language="javascript" type="text/javascript">
				var comment_bid = -1;
				function show_comment_dialog(e, bid) {
					comment_bid = bid;
					var dialog = document.createElement("div");
					dialog.style.position = "absolute";
					dialog.style.left = e.clientX + "px";
					dialog.style.top = e.clientY + "px";
					dialog.style.width = "300px";
					dialog.style.height = "150px";
					dialog.style.zIndex = 100;
					dialog.style.backgroundColor = "#FFFFCC";
					dialog.style.color = "#000000";
					dialog.style.border = "2px solid #000000";
					dialog.style.padding = "5px";
					var title = document.createElement("div");
					title.innerHTML = "Add a comment:";
					title.style.fontFamily = "sans-serif";
					title.style.fontSize = "12pt";
					title.style.fontWeight = "bold";
					dialog.appendChild(title);
					var comment_area = document.createElement("textarea");
					comment_area.id = "comment_area";
					comment_area.style.width = "288px";
					comment_area.style.height = "85px";
					comment_area.style.backgroundColor = "#FFFFCC";
					comment_area.style.color = "#000000";
					comment_area.style.border = "1px solid #CCCCCC";
					comment_area.style.padding = "5px";
					comment_area.style.fontFamily = "sans-serif";
					comment_area.value = document.getElementById("comment_" + bid).value;
					dialog.appendChild(comment_area);
					var set_button = document.createElement("div");
					set_button.style.border = "2px outset #000000";
					set_button.style.position = "absolute";
					set_button.style.left = "90px";
					set_button.style.bottom = "5px";
					set_button.style.width = "120px";
					set_button.style.height = "20px";
					set_button.style.backgroundColor = "#FFFFFF";
					set_button.style.color = "#000000";
					set_button.innerHTML = "Add Comment";
					set_button.style.textAlign = "center";
					set_button.style.fontWeight = "bold"
					set_button.style.fontSize = "16px";
					set_button.style.MozUserSelect = "none";
					set_button.style.cursor = "default";
					set_button.onmousedown = function() {
						add_comment();
						dialog.style.display = "none";
					};
					dialog.appendChild(set_button);
					document.body.appendChild(dialog);
				}
				function add_comment() {
					if(comment_bid != -1) {
						var comment_field = document.getElementById("comment_" + comment_bid);
						var new_comment = document.getElementById("comment_area").value;
						if(comment_field.value != new_comment) {
							var check = document.getElementById("check_" + comment_bid);
							check.checked = "true";
							comment_field.value = new_comment;
						}
						comment_bid = -1;
					}
				}
				function do_action(action, bid) {
					var reschedule_id = document.getElementById("reschedule_" + bid);
					var unschedule_id = document.getElementById("unschedule_" + bid);
					var cancel_id = document.getElementById("cancel_" + bid);
					var room_id = document.getElementById("room_" + bid);
					var sponsor_id = document.getElementById("sponsor_" + bid);
					var check_id = document.getElementById("check_" + bid);
					var status_id = document.getElementById("status_" + bid);
					var activity_status_id = document.getElementById("activity_status_" + bid);
					if(action == "reschedule") {
						if(reschedule_id.innerHTML == "Reschedule") {
							reschedule_id.innerHTML = "Reset";
							unschedule_id.style.visibility = "visible";
							cancel_id.style.visibility = "visible";
							room_id.style.display = "inline";
							sponsor_id.style.display = "inline";
							room_id.style.visibility = "visible";
							sponsor_id.style.visibility = "visible";
							check_id.checked = true;
							status_id.style.display = "none";
							activity_status_id.innerHTML = "SCHEDULED";
						}
						else {
							reschedule_id.innerHTML = "Reschedule";
							room_id.style.display = "none";
							sponsor_id.style.display = "none";
							check_id.checked = false;
							status_id.innerHTML = "Scheduled";
							status_id.style.display = "block";
							activity_status_id.value = "SCHEDULED";
						}
					}
					else if(action == "unschedule") {
						reschedule_id.innerHTML = "Reschedule";
						reschedule_id.style.visibility = "visible";
						unschedule_id.style.visibility = "hidden";
						cancel_id.style.visibility = "hidden";
						check_id.checked = true;
						room_id.style.display = "none"
						sponsor_id.style.display = "none";
						status_id.innerHTML = "Unscheduled";
						status_id.style.display = "block";
						activity_status_id.value = "UNSCHEDULED";
					}
					else {
						if(cancel_id.innerHTML == "Cancel") {
							cancel_id.innerHTML = "Uncancel";
							reschedule_id.style.visibility = "hidden";
							check_id.checked = !check_id.checked;
							room_id.style.display = "none"
							sponsor_id.style.display = "none";
							status_id.innerHTML = "Cancelled";
							status_id.style.display = "block";
							activity_status_id.value = "CANCELLED";
						}
						else {
							cancel_id.innerHTML = "Cancel";
							status_id.innerHTML = "Scheduled";
							reschedule_id.style.visibility = "visible";
							check_id.checked = !check_id.checked;
							activity_status_id.value = "SCHEDULED";
						}
					}
				}
			</script>
			<br /><a id="reschedule_[<$activity.block.bid>]" onclick="do_action('reschedule', '[<$activity.block.bid>]');" href="#" style="visibility: [<if $activity.cancelled>]hidden[<else>]visible[</if>]">Reschedule</a>
			<br /><a id="unschedule_[<$activity.block.bid>]" onclick="do_action('unschedule', '[<$activity.block.bid>]');" href="#" style="visibility: visible">Unschedule</a>
			<br /><a id="cancel_[<$activity.block.bid>]" onclick="do_action('cancel', '[<$activity.block.bid>]');" href="#" style="visibility: visible">[<if $activity.cancelled>]Uncancel[<else>]Cancel[</if>]</a>
[</if>]
		</td>
		<td style="padding: 5px;">
[<if $activity.scheduled>]
			<div id="status_[<$activity.block.bid>]" style="display: block; color: #FF0000; font-weight: bold; text-align: center; font-size: 14pt;">[<if $activity.cancelled>]Cancelled[<else>]Scheduled[</if>]</div>
[</if>]
			<div id="room_[<$activity.block.bid>]" style="visibility: [<if $activity.scheduled>]hidden; display: none[<else>]visible[</if>];">
				<select id="room_list_[<$activity.block.bid>]" name="room_list[[<$activity.block.bid>]][]" size="3" style="width: 100%;" multiple>
[<foreach from=$rooms item='room'>]
					<option value="[<$room.rid>]"[<if in_array($room.rid, explode(",", $activity.rooms))>] selected[</if>]>[<$room.name>]</option>
[</foreach>]
				</select>
			</div>
		</td>
		<td style="padding: 5px;">
			<div id="sponsor_[<$activity.block.bid>]" style="visibility: [<if $activity.scheduled>]hidden; display: none[<else>]visible[</if>];">
				<select id="sponsor_list_[<$activity.block.bid>]" name="sponsor_list[[<$activity.block.bid>]][]" size="3" style="width: 100%;" multiple>
[<foreach from=$sponsors item='sponsor'>]
				<option value="[<$sponsor.sid>]"[<if in_array($sponsor.sid, explode(",", $activity.sponsors))>] selected[</if>]>[<$sponsor.name_comma>]</option>
[</foreach>]
				</select>
			</div>
			<input type="hidden" id="activity_status_[<$activity.block.bid>]" name="activity_status[[<$activity.block.bid>]]" value="SCHEDULED" />
		</td>
		<td><img src="[<$I2_ROOT>]www/pics/eighth/notepad.gif" alt="Add Comment" title="Add Comment" onMouseDown="show_comment_dialog(event, [<$activity.block.bid>])"><input type="hidden" id= "comment_[<$activity.block.bid>]" name="comments[[<$activity.block.bid>]]" value="[<$activity.comment>]" /></td>
	</tr>
[</foreach>]
</table><br />
<input type="submit" value="Modify" />
</form>
