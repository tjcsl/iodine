<style type='text/css'>
@media (max-width: 650px) {
  #boxcontent>table>tbody>tr>td {
    display: block;
    width: 100%;
  }	
}
</style>
[<include file="eighth/header.tpl">]
<table><tr><td style="vertical-align: top;">
[<if $is_admin >]
[<include file="eighth/include_list_open.tpl">]
[<include file="eighth/activity_selection.tpl" op='view' bid=$block->bid field='aid'>]
[<include file="eighth/block_selection.tpl" header="FALSE" title='' method='vcp_attendance' op='view' field='bid' bid=$block->bid>]
[<include file="eighth/include_list_close.tpl">]
<br />
[</if>]
<div id="eighth_passes">
	<h2>Passes</h2>
	<p>The following students were issued passes by the 8th period office.  When they arrive, please click the green button next to their name.</p>
	[<if $is_admin>]<p><a style="padding: 0px 5px; color: white; background-color: green;" href="[<$I2_ROOT>]eighth/vcp_schedule/acceptallpasses/bid/[<$act->bid>]/aid/[<$act->aid>]">Accept All Passes</a></p>[</if>]
	<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
		<tr><th>Name</th><th>Accept</th></tr>
		[<foreach from=$act->passes_obj item=user>]
			<tr class="c[<cycle values="1,2">]">
				<td style="padding: 0px 5px;">[<$user->name_comma>] ([<$user->iodineUidNumber>])</td>
				<td style="padding: 0px 5px; color: white; background-color: green;"><a style="color: white;" href="[<$I2_ROOT>]eighth/vcp_schedule/callin/bid/[<$act->bid>]/aid/[<$act->aid>]/name_id/[<$user->iodineUidNumber>]">Accept</a></td>
			</tr>
		[</foreach>]
	</table>
</div>
<!--
<div id="eighth_call_ins">
	<h2>Call-ins</h2>
	<p>Enter the Name or Student ID# of a student to call-in. Please note that call-ins cannot override Stickies.</p>
	<form name="vcp_callin_form" id="vcp_callin_form" action="[<$I2_ROOT>]eighth/vcp_schedule/callin/bid/[<$act->bid>]/aid/[<$act->aid>]" method="post">
		<input type="search" name="name_id" id="query" results="0"/>
		<input type="submit" value="Call in Student"/>
	</form>
</div>
-->
</td><td style="vertical-align: top;">
<div style="font-family: monospace;">
Activity:&nbsp;[<$act->name>], ID [<$act->aid>]<br />
Date:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<$act->block->date|date_format>], [<$act->block->block>] block<br />
Room(s):&nbsp;&nbsp;&nbsp;&nbsp;[<$act->block_rooms_comma>]<br />
Sponsor(s):&nbsp;[<$act->block_sponsors_comma>]<br />
Signups:&nbsp;[<sizeof($act->members_obj)>]&nbsp;/&nbsp;[<$act->capacity>]<br />
[<if $is_admin || $is_sponsor>]
[<if $act->attendancetaken == 1>]<span id="eighth_attendance_status" style="color:green;font-weight:bold;font-size:2.0em">Attendance Taken</span>
[<else>]<span id="eighth_attendance_status" style="color:red;font-weight:bold;font-size:2.0em">Attendance NOT Taken</span>[</if>]
[</if>]

</div>
<br />
<form name="vcp_attendance_form" id="vcp_attendance_form" action="[<$I2_ROOT>]eighth/vcp_attendance/update/bid/[<$act->bid>]/aid/[<$act->aid>]" method="post">
	<input type="button" value="Select All" onclick="CA();" name="selectall" /> [<if $is_admin>]<input type="submit" value="Update" />[</if>]<br />
	<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
		<tr>
			<th>Present</th>
			<th style="padding: 0px 5px;">Student</th>
			<th style="padding: 0px 5px;">Grade</th>
		</tr>
[<foreach from=$act->members_obj item=user>]
		<tr class="c[<cycle values="1,2">]">
			<td style="padding: 0px 5px;"><input type="checkbox" name="attendies[]" value="[<$user->uid>]"[<if ($act->attendancetaken == 1) && !in_array($user->uid, $absentees) >] checked="checked"[</if>] onchange="CCA(this);" /></td>
			<td style="padding: 0px 5px;">[<$user->name_comma>] ([<$user->iodineUidNumber>])</td>
			<td style="padding: 0px 5px;">[<$user->grade>]</td>
		</tr>
[<foreachelse>]
		<p>There is no information to be displayed at this time.</p>
[</foreach>]
	</table><br />
	[<if $is_admin || $is_sponsor>]<input type="submit" value="Update" />[</if>]
</form>
</td></tr></table>
<script language="javascript" type="text/javascript">
	var frm = document.vcp_attendance_form;
	function CA() {
		var trk = 0;
		for (var i = 0; i < frm.elements.length; i++) {
			var e = frm.elements[i];
			if ((e.name != 'selectall') && (e.type == 'checkbox')) {
				trk++;
				e.checked = (frm.selectall.value == "Select All" ? true : false);
			}
		}
		if(frm.selectall.value == "Select All") {
			frm.selectall.value = "Deselect All";
		}
		else {
			frm.selectall.value = "Select All";
		}
	}
	function CCA(CB){
		var TB = TO = 0;
		for (var i = 0; i < frm.elements.length; i++) {
			var e = frm.elements[i];
			if ((e.name != 'selectall') && (e.type == 'checkbox')) {
				TB++;
				if(e.checked) TO++;
			}
		}
		frm.selectall.value = (TO == TB ? "Deselect All" : "Select All");
	}
	CCA();
</script>
