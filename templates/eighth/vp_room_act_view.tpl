[<include file="eighth/header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$block->date|date_format:"%B %e, %Y">] - [<$block->block>] block</span><br /><br />
<table style="border: 0px; margin: 0px; padding: 0px; width: 100%;">
	<tr>
		<th>Block ID</th>
		<th>Room ID</th>
	</tr>
	[<foreach from=$utilizations item="blocks" key="roomname">]
	[<foreach from=$blocks item="block">]
	<tr>
		<td>
		[<$block->room->name>]
		</td>
	</tr>
	[</foreach>]
	[</foreach>]
	<!--/foreach-->
[</foreach>]
</table>
<div style="float: right; margin: 10px;">
	<a href="[<$I2_ROOT>]eighth/vp_room/format/bid/[<$block->bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" /></a>
</div>
