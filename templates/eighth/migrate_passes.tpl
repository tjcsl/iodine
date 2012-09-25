[<include file="eighth/header.tpl">]
<a href="#add">Jump to bottom</a><br />

<form method='get' target=''>
Start Date: <input type='text' name='start_date' value='[<$start_date>]'/><br/>
</form>
<table style="border: 0px; margin: 0px; padding: 0px;">
	<tr>
		<th style="text-align: left; padding: 0px 5px;">Date</th>
		<th style="text-align: left; padding: 0px 5px;">Block</th>
		<td>&nbsp;</td>
	</tr>
[<foreach from=$blocks item='block'>]
	<tr>
		<td style="padding: 0px 5px;">[<$block.date|date_format:"%A, %B %e, %Y">]</td>
		<td style="padding: 0px 5px;">[<$block.block>] block</td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/migrate_passes/migrate/bid/[<$block.bid>]">Migrate Passes</a></td>
	</tr>
[</foreach>]
</table>
<br />
