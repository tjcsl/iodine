[<if $message>]<strong>[<$message>]</strong><br /><br />[</if>]
The serial number can be found on the back.  It is etched in, and consists of all the numbers up to and excluding the first letter.  This should be 10 numbers long.<br />
The ID number can be found by turning on the calculator.  Press [2nd], then [+] for the memory menu.  Press [Enter] or [1] for "About".  This is a 14-digit string.  Please enter it without dashes or spaces.<br />
<form method="POST" action="[<$I2_ROOT>]findcalc" class="boxform">
Search by:
<select name="calc_form" style="width:150px">
<option value="sn">serial number</option>
<option value="id">digital ID number</option>
</select><br /><br />
<input type="text" name="number" style="width:150px" value="" />
<input type="submit" value="Search" style="width:75px" name="submit" />
</form><br />
[<if $calcs>]
FOUND:<br />
[<$username.fname>] [<$username.mname>] [<$username.lname>]: [<$calcs.calcsn>] ([<$calcs.calcid>])<br />
[</if>]
