[<include file="eighth/header.tpl">]
<script language="javascript" type="text/javascript" src="[<$I2_ROOT>]www/js/eighth_sch_activity.js"></script>
<div style="width: 100%; text-align: center; font-weight: bold; font-size: 18pt; margin-top: 10px;">[<$act->name_r>]</div>
[<include file="eighth/include_list_open.tpl">]
[<include file="eighth/sch_activity_choose.tpl">]
[<include file="eighth/include_list_close.tpl">]
<form name="activities" action="[<$I2_ROOT>]eighth/sch_activity/modify/aid/[<$act->aid>]" method="post">
<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px;">
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
		<th style="padding: 5px; text-align: left;">Sponsor(s)</th>
		<td>&nbsp;</td>
		<td style="width: 120px;"></td>
	</tr>
[<foreach from=$block_activities item="activity">]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">]">
		<td class="eighth_sch_activity_checkcell"><input type="checkbox" name="modify[]" value="[<$activity.block.bid>]" id="check_[<$activity.block.bid>]" onclick="CCA(this);" [<if $activity.scheduled>]checked="checked"[</if>]/></td>
		<td class="eighth_sch_activity_datecell[<if !$activity.scheduled>]_unscheduled[</if>]">
			[<$activity.block.date|date_format:"%a">] [<$activity.block.block>], [<$activity.block.date|date_format:"%m/%d/%y">]
[<if $activity.scheduled>]
			<br /><a id="unschedule_[<$activity.block.bid>]" onclick="do_action('unschedule', '[<$activity.block.bid>]');" href="#" class="eighth_sch_activity_unschedule">Unschedule</a>
			<br /><a id="cancel_[<$activity.block.bid>]" onclick="do_action('cancel', '[<$activity.block.bid>]');" href="#" class="eighth_sch_activity_cancel">[<if $activity.cancelled>]Uncancel[<else>]Cancel[</if>]</a>
[</if>]
		</td>
		<td class="eighth_sch_activity_listcell">
			<div id="room_[<$activity.block.bid>]">
				<select id="room_list_[<$activity.block.bid>]" name="room_list[[<$activity.block.bid>]][]" size="3" class="eighth_sch_activity_list" multiple="multiple">
[<foreach from=$rooms item='room'>]
					<option value="[<$room.rid>]"[<if in_array($room.rid, explode(",", $activity.rooms))>] selected[</if>]>[<$room.name>]</option>
[</foreach>]
				</select>
			</div>
		</td>
		<td class="eighth_sch_activity_listcell">
			<div id="sponsor_[<$activity.block.bid>]">
				<select id="sponsor_list_[<$activity.block.bid>]" name="sponsor_list[[<$activity.block.bid>]][]" size="3" style="eighth_sch_activity_list" multiple"multiple">
[<foreach from=$sponsors item='sponsor'>]
				<option value="[<$sponsor.sid>]"[<if in_array($sponsor.sid, explode(",", $activity.sponsors))>] selected[</if>]>[<$sponsor.name_comma>]</option>
[</foreach>]
				</select>
			</div>
			<input type="hidden" id="activity_status_[<$activity.block.bid>]" name="activity_status[[<$activity.block.bid>]]" value="SCHEDULED" />
		</td>
		<td style="padding: 5px;">
			<textarea name="comments[[<$activity.block.bid>]]" id="comment_[<$activity.block.bid>]" readonly="readonly" class="eighth_sch_activity_commentcell">[<if isset($activity.comment) >][<$activity.comment>][</if>]</textarea>
		</td>
		<td style="text-align: center;"><img src="[<$I2_ROOT>]www/pics/eighth/notepad.gif" alt="Add Comment" title="Add Comment" onMouseDown="show_comment_dialog(event, [<$activity.block.bid>])" class="eighth_sch_activity_comment"><br /><a href="#" class="eighth_sch_activity_propagate">&uarr;&nbsp;Propagate&nbsp;&darr;</a>
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
</script>
