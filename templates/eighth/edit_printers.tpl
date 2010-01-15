[<include file="eighth/header.tpl">]

<span style="font-weight: bold; font-size: 125%;">Set Current Printer</span><br />
<form name="printer_choose_form" method="post" action="[<$I2_ROOT>]eighth/edit_printers/choose">
<select name="printer" size="10" onchange="location.href='[<$I2_ROOT>]eighth/edit_printers/choose/printer/' + this.options[this.selectedIndex].value">
[<foreach from=$printers item='printer'>]
	<option value="[<$printer.id>]" [<if $printer.is_selected>]selected="SELECTED"[</if>]>[<$printer.name>]</option>
[</foreach>]
</select><br />
<input type="submit" value="Set Printer" />
</form><br /><br />

<span style="font-weight: bold; font-size: 125%;">Delete a Printer</span><br />
<form name="printer_delete_form" method="post" action="[<$I2_ROOT>]eighth/edit_printers/delete">
<select name="printer" size="10">
[<foreach from=$printers item='printer'>]
	<option value="[<$printer.id>]">[<$printer.name>]</option>
[</foreach>]
</select><br />
<input type="submit" value="Delete Printer" />
</form><br /><br />

<span style="font-weight: bold; font-size: 125%;">Add a printer</span><br />
<form action="[<$I2_ROOT>]eighth/edit_printers/add" method="post">
	IP Address: <input type="text" name="ip" /><br />
	Name: <input type="text" name="name" /><br />
	<input type="submit" value="Add Printer" />
</form>
