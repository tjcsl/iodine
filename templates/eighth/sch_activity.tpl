[<include file="eighth/header.tpl">]
			<script language="javascript" type="text/javascript">
				var comment_bid = -1;
				function show_comment_dialog(e, bid) {
					comment_bid = bid;
					var dialog = document.createElement("div");
					dialog.style.position = "absolute";
					dialog.style.left = (e.clientX - 314) + "px";
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
					set_button.style.left = "53px";
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
					var cancel_button = document.createElement("div");
					cancel_button.style.border = "2px outset #000000";
					cancel_button.style.position = "absolute";
					cancel_button.style.left = "183px";
					cancel_button.style.bottom = "5px";
					cancel_button.style.width = "64px";
					cancel_button.style.height = "20px";
					cancel_button.style.backgroundColor = "#FFFFFF";
					cancel_button.style.color = "#000000";
					cancel_button.innerHTML = "Cancel";
					cancel_button.style.textAlign = "center";
					cancel_button.style.fontWeight = "bold"
					cancel_button.style.fontSize = "16px";
					cancel_button.style.MozUserSelect = "none";
					cancel_button.style.cursor = "default";
					cancel_button.onmousedown = function() {
						dialog.style.display = "none";
					};
					dialog.appendChild(cancel_button);
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
					var unschedule_id = document.getElementById("unschedule_" + bid);
					var cancel_id = document.getElementById("cancel_" + bid);
					var room_id = document.getElementById("room_" + bid);
					var sponsor_id = document.getElementById("sponsor_" + bid);
					var check_id = document.getElementById("check_" + bid);
					var status_id = document.getElementById("status_" + bid);
					var activity_status_id = document.getElementById("activity_status_" + bid);
					if(action == "unschedule") {
						if(unschedule_id.innerHTML == "Unschedule") {
							cancel_id.style.visibility = "hidden";
							check_id.checked = true;
							activity_status_id.value = "UNSCHEDULED";
							unschedule_id.innerHTML = "Reschedule";
						}
						else {
							cancel_id.style.visibility = "visible";
							check_id.checked = false;
							activity_status_id.value = "SCHEDULED";
							unschedule_id.innerHTML = "Unschedule";
						}
					}
					else {
						if(cancel_id.innerHTML == "Cancel") {
							cancel_id.innerHTML = "Uncancel";
							check_id.checked = !check_id.checked;
							activity_status_id.value = "CANCELLED";
						}
						else {
							cancel_id.innerHTML = "Cancel";
							check_id.checked = !check_id.checked;
							activity_status_id.value = "SCHEDULED";
						}
					}
				}
				function CA() {
					var trk=0;
					for (var i=0;i<frm.elements.length;i++)	{
						var e=frm.elements[i];
						if ((e.name != 'selectall') && (e.type=='checkbox')) {
							trk++;
							e.checked=frm.selectall.checked;
						}
					}
				}
				function CCA(CB){
					var TB=TO=0;
					for (var i=0;i<frm.elements.length;i++) {
						var e=frm.elements[i];
						if ((e.name != 'selectall') && (e.type=='checkbox')) {
							TB++;
							if (e.checked) TO++;
						}
					}
					frm.selectall.checked=(TO==TB)?true:false;
				}
			</script>
<div style="width: 100%; text-align: center; margin-top: 10px;"><font style="font: 18pt bold;">[<$activity_name>]</font></div><br />
<form name="activities" action="[<$I2_ROOT>]eighth/sch_activity/modify/aid/[<$aid>]" method="post">
	<input type="submit" value="Save" /><br /><br />
<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px; width: 100%;">
	<tr>
		<td style="width:  20px; text-align: left;"><input type="checkbox" name="selectall" onclick="CA();" /></td>
		<th style="padding: 5px; text-align: left; width: 120px;">Select All</th>
		<th style="padding: 5px; text-align: left;">Room(s)</th>
		<th style="padding: 5px; text-align: left;">Sponsor(s)</th>
		<td>&nbsp;</td>
		<td style="width: 120px;"></td>
	</tr>
[<foreach from=$activities item="activity">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td style="text-align: center;"><input type="checkbox" name="modify[]" value="[<$activity.block.bid>]" id="check_[<$activity.block.bid>]" onclick="CCA(this);" [<if $activity.scheduled>]checked="checked"[</if>]/></td>
		<td style="padding: 5px 5px 5px 0px;[<if !$activity.scheduled>] font-weight: bold;[</if>]">
			[<$activity.block.date|date_format:"%a">] [<$activity.block.block>], [<$activity.block.date|date_format:"%m/%d/%y">]
[<if $activity.scheduled>]
			<br /><a id="unschedule_[<$activity.block.bid>]" onclick="do_action('unschedule', '[<$activity.block.bid>]');" href="#" style="visibility: visible">Unschedule</a>
			<br /><a id="cancel_[<$activity.block.bid>]" onclick="do_action('cancel', '[<$activity.block.bid>]');" href="#" style="visibility: visible">[<if $activity.cancelled>]Uncancel[<else>]Cancel[</if>]</a>
[</if>]
		</td>
		<td style="padding: 5px;">
			<div id="room_[<$activity.block.bid>]">
				<select id="room_list_[<$activity.block.bid>]" name="room_list[[<$activity.block.bid>]][]" size="3" style="width: 100%;" multiple>
[<foreach from=$rooms item='room'>]
					<option value="[<$room.rid>]"[<if in_array($room.rid, explode(",", $activity.rooms))>] selected[</if>]>[<$room.name>]</option>
[</foreach>]
				</select>
			</div>
		</td>
		<td style="padding: 5px;">
			<div id="sponsor_[<$activity.block.bid>]">
				<select id="sponsor_list_[<$activity.block.bid>]" name="sponsor_list[[<$activity.block.bid>]][]" size="3" style="width: 100%;" multiple>
[<foreach from=$sponsors item='sponsor'>]
				<option value="[<$sponsor.sid>]"[<if in_array($sponsor.sid, explode(",", $activity.sponsors))>] selected[</if>]>[<$sponsor.name_comma>]</option>
[</foreach>]
				</select>
			</div>
			<input type="hidden" id="activity_status_[<$activity.block.bid>]" name="activity_status[[<$activity.block.bid>]]" value="SCHEDULED" />
		</td>
		<td style="padding: 5px;">
			<textarea name="comments[[<$activity.block.bid>]]" id="comment_[<$activity.block.bid>]" readonly="readonly" style="border: none; background: none; width: 100%; font-family: sans-serif;">[<if isset($activity.comment) >][<$activity.comment>][</if>]</textarea>
		</td>
		<td style="text-align: center;"><img src="[<$I2_ROOT>]www/pics/eighth/notepad.gif" alt="Add Comment" title="Add Comment" onMouseDown="show_comment_dialog(event, [<$activity.block.bid>])" style="cursor: pointer; margin-bottom: 5px;"><a href="#" style="font-size: 10pt; border: 1px dashed #999999; padding: 2px;">&uarr;&nbsp;Propagate&nbsp;&darr;</a>
		</td>
	</tr>
[</foreach>]
</table><br />
<input type="submit" value="Save" />
</form>
<script language="javascript" type="text/javascript">
	var frm = document.activities;
</script>
