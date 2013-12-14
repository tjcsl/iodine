[<include file="eighth/header.tpl">]
<style type="text/css">
table.vp-room-viewbyroom {
    border: 0px; margin: 0px; padding: 0px; width: 100%;
}

table.vp-room-viewbyroom th {
    padding: 0px 5px; text-align: left;
}

table.vp-room-viewbyroom td {
    padding: 0px 5px;
}
</style>
<form method="get">
<select name="sort">
    <option value=0[<if $sort==0>] selected[</if>]>Block ID</option>
    <option value=1[<if $sort==1>] selected[</if>]>Activity ID</option>
    <option value=3[<if $sort==3>] selected[</if>]>Room ID</option>
    <option value=5[<if $sort==5>] selected[</if>]>Activity Capacity</option>
    <option value=6[<if $sort==6>] selected[</if>]>Activity Name</option>
    <option value=9[<if $sort==9>] selected[</if>]>Sponsor</option>
</select>
<input type="submit" value="Sort" />
</form>
<table class="vp-room-viewbyroom">
    <tr>
	<th>Room</th>
	<th>Block</th>
        <th>Activity</th>
        <th>Teacher</th>
        <th>Students</th>
        <th>Capacity</th>
    </tr>
[<foreach from=$util item="activities" key="roomname">]
    [<foreach from=$activities item="actblk">]
    <tr>
        <th>[<$actblk.roomname>] ([<$actblk.roomid>])</th>
        <td>[<$actblk.date>] [<$actblk.block>] Block ([<$actblk.bid>])</td>
        <td>[<$actblk.actname>] ([<$actblk.aid>])</td>
        <td>[<$actblk.sponsorname>]</td>
        <td>[<$actblk.actsignups>]</td>
        <td>[<$actblk.actcapacity>]</td>
    </tr>
    [</foreach>]
[<foreachelse>]
    <tr><td colspan=7>There are no activities in this room during the specified period.</td></tr>
[</foreach>]
</table>
