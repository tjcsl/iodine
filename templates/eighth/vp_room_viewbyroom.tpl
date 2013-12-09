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
<table class="vp-room-viewbyroom">
    <tr>
        <th>Room Name</th>
        <th>Block ID</th>
        <th>Activity ID</th>
        <th>Activity Name</th>
        <th>Teacher</th>
        <th>Students</th>
        <th>Capacity</th>
    </tr>
[<foreach from=$util item="activities" key="roomname">]
    [<foreach from=$activities item="actblk">]
    <tr>
        <th>[<$actblk.roomname>] ([<$actblk.roomid>])</th>
        <td>[<$actblk.bid>]</td>
        <td>[<$actblk.aid>]</td>
        <td>[<$actblk.actname>]</td>
        <td>[<$actblk.sponsorname>]</td>
        <td>[<$actblk.actsignups>]</td>
        <td>[<$actblk.actcapacity>]</td>
    </tr>
    [</foreach>]
[<foreachelse>]
    <tr><td colspan=7>There are no activities in this room during the specified period.</td></tr>
[</foreach>]
</table>