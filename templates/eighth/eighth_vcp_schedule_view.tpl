
[<if $user->is_group_member('admin_eighth')>]
[<include file="eighth/eighth_header.tpl">]
	<h2>[<$user->name_comma>] ([<$user->uid>], [<$user->grade>]th)</h2>
	<b>Counselor: [<$user->counselor>], TA: </b><br />
	<span style="color: #FF0000; font-weight: bold;">Comments: [<if $user->comments == "" >]none[<else>][<$user->comments>][</if>]</span><br />
	[<php>] $this->_tpl_vars['count'] = count($this->_tpl_vars['absences']) [</php>]
<a href="[<$I2_ROOT>]eighth/vcp_schedule/absences/uid/[<$user->uid>]">[<$count>] absence[<if $count != 1>]s[</if>]</a><br />
	<a href="[<$I2_ROOT>]eighth/view/comments/uid/[<$user->uid>]">Edit Comments</a> - <a href="[<$I2_ROOT>]eighth/view/student/uid/[<$user->uid>]">Edit Student</a><br />
[</if>]
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

[<if $user->is_group_member('admin_eighth')>]
	[<include file="eighth/eighth_header.tpl">]
	<h2>[<$user->name_comma>] ([<$user->uid>], [<$user->grade>]th)</h2>
	<b>Counselor: [<$user->counselor>], TA: </b><br />
	<span style="color: #FF0000; font-weight: bold;">Comments: [<if $user->comments == "" >]none[<else>][<$user->comments>][</if>]</span><br />
	<a href="[<$I2_ROOT>]eighth/vcp_schedule/absences/uid/[<$user->uid>]">[<$absence_count>] absence[<if $absence_count != 1>]s[</if>]</a><br />
	<a href="[<$I2_ROOT>]eighth/view/comments/uid/[<$user->uid>]">Edit Comments</a> - <a href="[<$I2_ROOT>]eighth/view/student/uid/[<$user->uid>]">Edit Student</a><br />
[</if>]
	<form name="activities" action="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$user->uid>]" method="post">
	<input type="submit" value="Change Selected" />
	<div style="display: inline; margin-left: 100px;">
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date-3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="&lt; Back Two Weeks" /></a>
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date+3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="Forward Two Weeks &gt;" /></a>
	</div>
	<br /><br />
	<table cellspacing="0" cellpadding="0" style="margin: 0px; padding: 0px; border: 0px; width: 100%;">
		<tr>
			<th><input type="checkbox" name="selectall" onclick="CA();" /></th>
			<td>&nbsp;</td>
			<th>Day of Week</th>
			<th>Block</th>
			<th>Date</th>
			<th>Attended</th>
			<th>Activity</th>
			<th>Teacher</th>
			<th>Room</th>
		</tr>
[<foreach from=$activities item="activity">]
		<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">];">
			<td style="text-align: center;"><input type="checkbox" name="change[[<$activity->bid>]]" value="1" onclick="CCA(this);" /></td>
			<td style="text-align: center;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/choose/uid/[<$user->uid>]/bid/[<$activity->bid>]">Change</a></td>
			<td style="text-align: center;">[<$activity->block->date|date_format:"%a">]</td>
			<td style="text-align: center;">[<$activity->block->block>]</td>
			<td style="text-align: center;">[<$activity->block->date|date_format>]</td>
			<td style="text-align: center;">[<if in_array(array($activity->aid, $activity->bid), $absences)>]No[<elseif $activity->attendancetaken>]Yes[<else>]---[</if>]</td>
			<td style="text-align: center;"><a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_r>] ([<$activity->aid>])</a></td>
			<td style="text-align: center;">[<$activity->block_sponsors_comma_short>]</td>
			<td style="text-align: center;">[<$activity->block_rooms_comma>]</td>
		</tr>
[</foreach>]
	</table>
	<br />
	<input type="submit" value="Change Selected" />
	<div style="display: inline; margin-left: 100px;">
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date-3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="&lt; Back Two Weeks" /></a>
		<a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$user->uid>]/start_date/[<$start_date+3600*24*14|date_format:"%Y-%m-%d">]"><input type="button" value="Forward Two Weeks &gt;" /></a>
	</div>
</form>
<script language="javascript" type="text/javascript">
	<!--
		var frm = document.activities;
	// -->
</script>
