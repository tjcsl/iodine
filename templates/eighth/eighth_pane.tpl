[<* A '*' next to an item means it's not quite done yet *>]

[<include file="eighth/eighth_header.tpl">]
<table style="border: 0px; padding: 0px; margin: 10px; width: 100%">
[<* <tr><td colspan="2" style="color: #FF0000; font-weight: bold; font-family: courier;">** I'm updating the database.&nbsp;&nbsp;Please don't do anything. --Andrew **</td></tr> *>]

<tr>
<td style="width: 50%; valign: top">
<b>Student Options</b>

<ol>
[<* <li><a href="vcp.schedule.phtml"><b>View, change, or print a student's schedule</b></a> *>]
<li><a href="[<$I2_ROOT>]eighth/reg_group"><b>Register a group of students for an activity</b></a>
<li><a href="[<$I2_ROOT>]eighth/amr_group">Add, modify, or remove a special group of students</a>
<li>*<a href="[<$I2_ROOT>]eighth/alt_permissions">Add students to a restricted activity</a>
<li><a href="[<$I2_ROOT>]eighth/people_switch">Switch all the students in one activity into another</a>
</ol>

</td>
<td style="width: 50%; valign: top">
<b>Attendance Options</b>
<ol>
<li><a href="[<$I2_ROOT>]eighth/res_student"><b>Reschedule students by student ID for a single activity</b></a>
<li><a href="[<$I2_ROOT>]eighth/vcp_attendance"><b>View, change, or print attendance data</b></a>
<li><a href="[<$I2_ROOT>]eighth/ent_attendance"><b>Enter TA absences by student ID</b></a>

<li>*<a href="[<$I2_ROOT>]eighth/vp_delinquent">View or print a list of delinquent students</a>
</ol>
</td>
</tr>
<tr>
<td style="width: 50%; valign: top">
<b>Activity Scheduling Options</b>
<ol>

<li><a href="[<$I2_ROOT>]eighth/amr_activity">Add, modify, or remove an activity</a>
<li><a href="[<$I2_ROOT>]eighth/amr_room">Add, modify, or remove a room</a>
<li><a href="[<$I2_ROOT>]eighth/amr_sponsor">Add, modify, or remove an activity sponsor</a>
<li><a href="[<$I2_ROOT>]eighth/sch_activity"><b>Schedule an activity for eighth period</b></A>
<li><a href="[<$I2_ROOT>]eighth/vp_roster"><b>View or print a class roster</b></a>
<li><a href="[<$I2_ROOT>]eighth/vp_room"><b>View or print the utilization of a room</b></a>

[<* <li><a href="adlist.phtml">Get a plain text list of students in administrative study hall</a> *>]
<li><a href="[<$I2_ROOT>]eighth/cancel_activity">Cancel/set comments/advertise for an activity</a>
<li><a href="[<$I2_ROOT>]eighth/room_sanity">Room assignment sanity check</a>
<li><a href="[<$I2_ROOT>]eighth/vp_sponsor">View or print sponsor schedule</a>
</ol>
</td>
<td style="width: 50%; valign: top">

<b>Special Options</b>
<ol>
<li><a href="[<$I2_ROOT>]eighth/fin_schedules"><b>Finalize student schedules</b></a>
<li>*<a href="[<$I2_ROOT>]eighth/prn_attendance"><b>Print activity rosters</b></a>
<li>*<a href="[<$I2_ROOT>]eighth/chg_start"><b>Change starting date</b></a>
<li><a href="[<$I2_ROOT>]eighth/ar_block">Add or remove 8th period block from system</a>

<li><a href="[<$I2_ROOT>]eighth/rep_schedules">Repair broken schedules</a>
[<* <li><a href="set.printer.phtml">Set printer</a> *>]
</ol>
</td>
</tr>
<tr>
[<*
<td style="width: 50%; valign: top">
<b>New Session Options</b>
<ol>
<li><a href="chg.section.phtml">Change section ID mapping</a>
<li><a href="prn.catalog.phtml">Print catalog</a>
<li><a href="reg.bubble.phtml">Register from bubble sheets</a>
</ol>
</td>
*>]
<td style="width: 50%; valign: top">
</td>

<td style="width: 50%; valign: top">
<b>Other Options</b>
<ol>
<li><a href="[<$I2_ROOT>]logout">Logout</a>
</ol>
</td>
</tr>
</table>
