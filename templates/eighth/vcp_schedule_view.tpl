
<script language="javascript" type="text/javascript">
	<!--
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
	// -->
</script>

[<if $I2_USER->is_group_member('admin_eighth')>]
	[<include file="eighth/header.tpl">]
	<h2>[<$user->name_comma>] ([<$user->tjhsstStudentId>], [<$user->grade>][<if $user->grade != 'staff' >]th[</if>])</h2>
	<b>Counselor: [<$user->counselor>], TA: </b><br />
	<span style="color: #FF0000; font-weight: bold;">Comments: [<if isSet($comments) && $comments != "">][<$comments>][<else>]none[</if>]</span><br />
	<a href="[<$I2_ROOT>]eighth/vcp_schedule/absences/uid/[<$user->uid>]">[<$absence_count>] absence[<if $absence_count != 1>]s[</if>]</a><br />
	<a href="[<$I2_ROOT>]eighth/view/comments/uid/[<$user->uid>]">Edit Comments</a> - <a href="[<$I2_ROOT>]eighth/view/student/uid/[<$user->uid>]">Edit Student</a><br />
[</if>]
	<form name="activities" action="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$user->uid>]" method="post">
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
	<tr>
	<td style="text-align: left;"><input type="button" value="&lt; Back Two Weeks" onclick="location.href='[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date-3600*24*14|date_format:"%Y-%m-%d">]'" /></td>
	<td style="text-align: center;"><input type="submit" value="Change Selected" /></td>
	<td style="text-align: right;"><input type="button" value="Forward Two Weeks &gt;" onclick="location.href='[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date+3600*24*14|date_format:"%Y-%m-%d">]'" /></td>
	</tr>
	</table>
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
		<tr>
			<th>Select All<br /><input type="checkbox" name="selectall" onclick="CA();" /></th>
			<td>&nbsp;</td>
			<th>Day of Week</th>
			<th>Date</th>
			<th>Block</th>
			<th>Attended</th>
			<th>Activity</th>
			<th>Teacher</th>
			<th>Room</th>
		</tr>
[<foreach from=$activities item="activity">]
		<tr class="[<cycle values="c1,c2">]">
			<td style="text-align: center;">[<if !$activity->block->locked || $eighth_admin>]<input type="checkbox" name="bids[]" value="[<$activity->bid>]" onclick="CCA(this);" />[<else>]&nbsp;[</if>]</td>
			<td style="text-align: center;">[<if !$activity->block->locked || $eighth_admin>]<a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$user->uid>]/bids/[<$activity->bid>]">Change</a>[<else>]LOCKED[</if>]</td>
			<td style="text-align: center;">[<$activity->block->date|date_format:"%a">]</td>
			<td style="text-align: center;">[<$activity->block->date|date_format>]</td>
			<td style="text-align: center;">[<$activity->block->block>]</td>
			<td style="text-align: center;">[<if in_array(array($activity->aid, $activity->bid), $absences)>]No[<elseif $activity->attendancetaken>]Yes[<else>]---[</if>]</td>
			<td style="text-align: center;">[<if $activity->aid == $defaultaid>]HAS NOT SELECTED AN ACTIVITY[<else>]<a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_r>] ([<$activity->aid>])</a>[</if>]</td>
			<td style="text-align: center;">[<$activity->block_sponsors_comma_short>]</td>
			<td style="text-align: center;">[<if $activity->aid != $defaultaid>][<$activity->block_rooms_comma>][<else>]236WK[</if>]</td>
		</tr>
[</foreach>]
	</table>
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
	<tr>
	<td style="text-align: left;"><input type="button" value="&lt; Back Two Weeks" onclick="location.href='[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date-3600*24*14|date_format:"%Y-%m-%d">]'" /></td>
	<td style="text-align: center;"><input type="submit" value="Change Selected" /></td>
	<td style="text-align: right;"><input type="button" value="Forward Two Weeks &gt;" onclick="location.href='[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date+3600*24*14|date_format:"%Y-%m-%d">]'" /></td>
	</tr>
	</table>
</form>
<script language="javascript" type="text/javascript">
	<!--
		var frm = document.activities;
	// -->
</script>
