[<include file="eighth/eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
[<foreach from=$blocks item='block'>]
	<tr style="background-color: [<cycle values="#CCCCCC,#FFFFFF">];">
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$field|default:"bid">]/[<$block.bid>]">[<$block.date|date_format:"%A">]</a></td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$field|default:"bid">]/[<$block.bid>]">[<$block.date|date_format>]</a></td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$field|default:"bid">]/[<$block.bid>]">[<$block.block>] block</a></td>
	</tr>
[</foreach>]
</table>
[<if $add >]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Date: <input type="text" name="date"><br />
	Block: <select name="block">
		<option value="A">A</option>
		<option value="B">B</option>
	</select><br />
	<input type="submit" value="Add">
</form>
[</if>]
