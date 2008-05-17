[<include file="eighth/header.tpl">]
<script language="javascript" type="text/javascript" src="[<$I2_ROOT>]www/js/eighth_sch_activity.js"></script>
<div style="width: 100%; text-align: center; font-weight: bold; font-size: 18pt; margin-top: 10px;">
<select name="activity_list" style="font-size: 18pt; text-align: left;" onChange="location.href='[<$I2_ROOT>]eighth/sch_activity/view/aid/' + this.options[this.selectedIndex].value">
[<foreach from=$activities item="activity">]
	<option value="[<$activity->aid>]" [<if $act->aid == $activity->aid>]style="font-size: 18pt; font-weight: bold;" SELECTED[<else>]style="font-size: 10pt; "[</if>]>[<$activity->aid>]: [<$activity->name_r>]</option>
[</foreach>]
</select>
<form name="aidform" action="[<$I2_ROOT>]eighth/sch_activity/view/" method="POST">
AID: <input type="text" name="aid"/>
<script language="javascript" type="text/javascript">
	document.aidform.aid.focus();
</script>
</form>
</div>

<form name="activities" action="[<$I2_ROOT>]eighth/sch_activity/modify/aid/[<$act->aid>]" method="post">
<div id="eighth_room_pane">
	<select id="eighth_room_list" name="rooms" size="10" multiple="multiple" onChange="do_action('add_room', 0, this.options[this.selectedIndex]); void(this.selectedIndex=-1);">
[<foreach from=$rooms item='room'>]
		<option value="[<$room.rid>]">[<$room.name>]</option>
[</foreach>]
	</select><br />
	<span style="text-decoration: underline; cursor: pointer;" onclick="void(this.parentNode.style.display='none');">Close</span>
</div>
<div id="eighth_sponsor_pane">
	<select id="eighth_sponsor_list" name="sponsors" size="10" multiple="multiple" onChange="do_action('add_sponsor', 0, this.options[this.selectedIndex]); void(this.selectedIndex=-1);">
[<foreach from=$sponsors item='sponsor'>]
	<option value="[<$sponsor.sid>]">[<$sponsor.name_comma>]</option>
[</foreach>]
	</select><br />
	<span style="text-decoration: underline; cursor: pointer;" onclick="void(this.parentNode.style.display='none');">Close</span>
</div>
<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px; width: 100%;">
	<tr>
		<td colspan="2">
			<input type="submit" value="Save" />
		</td>
		<td colspan="4">
		</td>
	</tr>
	<tr>
		<td style="width:  20px; text-align: left;"><input type="checkbox" name="selectall" onclick="CA();" /></td>
		<th style="padding: 5px; text-align: left; width: 120px;">Select All</th>
		<th style="padding: 5px; text-align: left;">Room(s)</th>
		<td>&nbsp;</td>
		<th style="padding: 5px; text-align: left;">Sponsor(s)</th>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td style="width: 150px;"></td>
	</tr>
[<foreach from=$block_activities item="activity">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td class="eighth_sch_activity_checkcell"><a name="[<$activity.block.bid>]"></a><input type="checkbox" name="modify[]" value="[<$activity.block.bid>]" id="check_[<$activity.block.bid>]" onclick="CCA(this);" [<if $activity.scheduled>]checked="checked"[</if>]/></td>
		<td class="eighth_sch_activity_datecell[<if !$activity.scheduled>]_unscheduled[</if>]">
			[<$activity.block.date|date_format:"%a">] [<$activity.block.block>], [<$activity.block.date|date_format:"%m/%d/%y">]
[<if $activity.scheduled>]
			<br /><a id="unschedule_[<$activity.block.bid>]" onclick="return do_action('unschedule', '[<$activity.block.bid>]');" href="#[<$activity.block.bid>]" class="eighth_sch_activity_unschedule">Unschedule</a>&nbsp;&nbsp;<a id="cancel_[<$activity.block.bid>]" onclick="return do_action('cancel', '[<$activity.block.bid>]');" href="#[<$activity.block.bid>]" class="eighth_sch_activity_cancel">[<if $activity.cancelled>]Uncancel[<else>]Cancel[</if>]</a>
[</if>]
		</td>
		<td class="eighth_sch_activity_listcell">
			<div id="div_room_list_[<$activity.block.bid>]" class="eighth_room_list">
[<if $activity.scheduled>]
	[<foreach from=$activity.rooms_obj item=room>]
				[<$room->name>] <a href="#[<$activity.block.bid>]" onclick="return do_action('remove_room', '[<$activity.block.bid>]', '[<$room->rid>]', event)">Remove</a><br />
	[</foreach>]
[</if>]
			</div>
			<input type="hidden" name="room_list[[<$activity.block.bid>]]" value="[<if $activity.scheduled>][<$activity.rooms>][</if>]" id="room_list_[<$activity.block.bid>]" />
		</td>
		<td style="text-align: left;">
			<a href="#[<$activity.block.bid>]" onclick="return do_action('view_rooms', '[<$activity.block.bid>]', new Array([<$activity.rooms_array>]), event);">Add Room</a><br />
			<a href="#[<$activity.block.bid>]" onclick="return do_action('set_default_rooms', '[<$activity.block.bid>]', new Array(new Array([<$activity.rooms_array>]), new Array([<$activity.rooms_name_array>])));">Set to Default Room(s)</a>
		</td>
		<td class="eighth_sch_activity_listcell">
			<div id="div_sponsor_list_[<$activity.block.bid>]" class="eighth_sponsor_list">
[<if $activity.scheduled>]
	[<foreach from=$activity.sponsors_obj item=sponsor>]
				[<$sponsor->name_comma>] <a href="#[<$activity.block.bid>]" onclick="return do_action('remove_sponsor', '[<$activity.block.bid>]', '[<$sponsor->sid>]', event)">Remove</a><br />
	[</foreach>]
[</if>]
			</div>
			<input type="hidden" name="sponsor_list[[<$activity.block.bid>]]" value="[<if $activity.scheduled>][<$activity.sponsors>][</if>]" id="sponsor_list_[<$activity.block.bid>]" />
			<input type="hidden" id="activity_status_[<$activity.block.bid>]" name="activity_status[[<$activity.block.bid>]]" value="[<if $activity.scheduled && $activity.cancelled>]CANCELLED[<else>]SCHEDULED[</if>]" />
		</td>
		<td style="text-align: left;">
			<a href="#[<$activity.block.bid>]" onclick="return do_action('view_sponsors', '[<$activity.block.bid>]', new Array([<$activity.sponsors_array>]), event);">Add Sponsor</a><br />
			<a href="#[<$activity.block.bid>]" onclick="return do_action('set_default_sponsors', '[<$activity.block.bid>]', new Array(new Array([<$activity.sponsors_array>]), new Array([<$activity.sponsors_name_array>])));">Set to Default Sponsor(s)</a>
		</td>
		<td style="padding: 5px;">
			<textarea name="comments[[<$activity.block.bid>]]" id="comment_[<$activity.block.bid>]" readonly="readonly" class="eighth_sch_activity_commentcell" rows="1">[<if isset($activity.comment) >][<$activity.comment|escape:"html">][</if>]</textarea>
		</td>
		<td style="text-align: center;"><img src="[<$I2_ROOT>]www/pics/eighth/notepad.gif" alt="Add Comment" title="Add Comment" onmousedown="show_comment_dialog(event, [<$activity.block.bid>])" class="eighth_sch_activity_comment"><a href="#[<$activity.block.bid>]" class="eighth_sch_activity_propagate" onclick="return do_action('propagate', [<$activity.block.bid>]);">&uarr;&nbsp;Propagate&nbsp;&darr;</a>
		</td>
		<td>
			<input type="submit" value="Save" />
		</td>
	</tr>
[</foreach>]
	<tr>
		<td colspan="2">
			<input type="submit" value="Save" />
		</td>
		<td colspan="4">
		</td>
	</tr>
</table><br />
</form>
<script language="javascript" type="text/javascript">
	var frm = document.activities;
	var unscheduled_blocks = new Array([<$unscheduled_blocks>]);
</script>
