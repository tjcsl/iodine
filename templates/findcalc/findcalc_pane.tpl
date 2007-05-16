[<if isset($message)>]<strong>[<$message>]</strong><br /><br />[</if>]
The serial number can be found on the back.  It is etched in, and consists of all the numbers up to and excluding the first letter.  This should be 10 numbers long.<br />
<br />
The ID number can be found by turning on the calculator.  Press [2nd], then [+] for the memory menu.  Press [Enter] or [1] for "About".  This is a 14-digit string.  Please enter it without dashes or spaces.<br />
<br />
<form method="post" action="[<$I2_ROOT>]findcalc" class="boxform">
Search by:
<select name="calc_form" style="width:150px">
<option value="sn">serial number</option>
<option value="id">digital ID number</option>
</select><br /><br />
<input type="text" name="number" style="width:150px" value="" />
<input type="submit" value="Search" style="width:75px" name="submit" />
</form><br />
[<if isset($results)>]
<strong>Results:</strong><br />
[<foreach from=$results item=match>]
<a href="[<$I2_ROOT>]studentdirectory/info/[<$match.uid>]">[<$match.name>]</a> [<$match.sn>] ([<$match.id>])<br />
[</foreach>]
[</if>]
