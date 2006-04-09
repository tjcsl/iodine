[<include file="eighth/header.tpl">]
[<if count($conflicts) > 0 >]
	<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px">
	[<foreach from=$conflicts item="conflict" key="room">]
		<tr style="background-color: #CCCCCC;">
			<th>Conflicts for room: [<$room>]</th>
		</tr>
		[<foreach from=$conflicts.$room item="activity">]
			<tr>
				<td style="padding-left: 5px;"><a href="[<$I2_ROOT>]eighth/sch_activity/view/aid/[<$activity.aid>]">[<$activity.name>] - [<$activity.aid>]</a></td>
			</tr>
		[</foreach>]
	[</foreach>]
	</table>
[<else>]
	<span style="color: #FF0000; font-weight: bold;">No Conflicts</span>
[</if>]
