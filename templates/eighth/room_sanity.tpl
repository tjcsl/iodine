[<include file="eighth/header.tpl">]
[<if count($conflicts) > 0 >]
	<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px">
	[<foreach from=$conflicts item="conflict" key="room">]
		<tr style="background-color: #EEEEFF;">
			<th>Conflicts for room: [<$room>]</th>
		</tr>
		[<foreach from=$conflicts.$room item="activity">]
			<tr>
				<td style="padding-left: 5px;"><a href="[<$I2_ROOT>]eighth/sch_activity/view/aid/[<$activity.aid>]">[<$activity.name>] - [<$activity.aid>]</a></td>
			</tr>
		[</foreach>]
		[<if isset($sponsorconflicts.$room)>]
			<tr>
			[<foreach from=$sponsorconflicts.$room item="arr">]
				[<foreach from=$arr key="sponsorid" item="subarr">]
				<th>[<$subarr.sponsor->name>] must also be in the following rooms:</th>
					[<foreach from=$subarr.rooms item="otherroom">]
						<td>[<$otherroom>]</td>
					[</foreach>]
				[</foreach>]
			[</foreach>]
			</tr>
		[</if>]
	[</foreach>]
	</table>
[<else>]
	<span style="color: #FF0000; font-weight: bold;">No Conflicts</span>
[</if>]
