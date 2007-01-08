[<include file="eighth/header.tpl">]
Select a block to lock/unlock:
<table cellspacing="0" style="border: 0px; padding: 0px; margin: 0px;">
	<tr>
		<td>&nbsp;</td>
		<th style="text-align: left; padding: 5px;">Date</th>
		<th style="text-align: left; padding: 5px;">Block</th>
	</tr>
[<foreach from=$blocks item="block">]
	<tr style="background-color: [<cycle values="#EEEEFF,#FFFFFF">]">
		<td style="padding: 5px;"><a href="[<$I2_ROOT>]eighth/fin_schedules/[<if $block.locked>]unlock/bid/[<$block.bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/check-on.gif">[<else>]lock/bid/[<$block.bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/check-off.gif">[</if>]</a></td>
		<td style="padding: 0px 5px;">[<$block.date|date_format:"%A, %B %e, %Y">]</td>
		<td style="padding: 0px 5px;">[<$block.block>] block</td>
	</tr>
[</foreach>]
</table>
