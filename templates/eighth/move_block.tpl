[<include file="eighth/header.tpl">]
<a href="#add">Jump to bottom</a><br />
<table style="border: 0px; margin: 0px; padding: 0px;">
	<tr>
		<th style="text-align: left; padding: 0px 5px;">Date</th>
		<th style="text-align: left; padding: 0px 5px;">Block</th>
		<th style="text-align: left; padding: 0px 5px;">Block ID</th>
		<td>&nbsp;</td>
	</tr>
[<foreach from=$blocks item='block'>]
	<tr>
		<td style="padding: 0px 5px;">[<$block.date|date_format:"%A, %B %e, %Y">]</td>
		<td style="padding: 0px 5px;">[<$block.block>] block</td>
		<td style="padding: 0px 5px;">[<$block.bid>]</td>
		<td style="padding: 0px 5px;"><a href="javascript:null()" onclick="document.getElementById('block_number').value='[<$block.bid>]';document.getElementById('move_date').value='[<$block.date>]';document.getElementById('move_block').value='[<$block.block>]';">Select</a></td>
	</tr>
[</foreach>]
</table>
<br />
<a id="add"></a>
<form action="[<$I2_ROOT>]eighth/move_block/move" method="post">
	Block <input id='block_number' type="text" name="block_number"/>
	To <input id='move_date' type="text" name="move_date"/>,
	block <input id='move_block' type="text" name="move_block"/>
	<input type="submit" value="Move" />
</form>
