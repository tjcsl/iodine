[<if isSet($header) && $header!='FALSE'>]
[<include file="eighth/header.tpl">]
[</if>]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<table cellspacing="0" style="border: 0px; margin: 0px; padding: 0px;">
[<foreach from=$blocks item='block'>]
	<tr class="[<cycle values="c1,c2">]"[<if isSet($bid) && $block.bid==$bid>]style="font-weight: bold;"[</if>]>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$field|default:"bid">]/[<$block.bid>]">[<$block.date|date_format:"%A">]</a></td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$field|default:"bid">]/[<$block.bid>]">[<$block.date|date_format>]</a></td>
		<td style="padding: 0px 5px;"><a href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<$field|default:"bid">]/[<$block.bid>]">[<$block.block>] block</a></td>
	</tr>
[</foreach>]
</table>
[<if isset($add)>]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Date: <input type="text" name="date" /><br />
	Block: <select name="block">
		<option value="A">A</option>
		<option value="B">B</option>
	</select><br />
	<input type="submit" value="Add" />
</form>
[</if>]
