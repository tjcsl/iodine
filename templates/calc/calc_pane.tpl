[<if $message>]<strong>[<$message>]</strong><br /><br />[</if>]
Welcome to Calculator Registration!  By registering your calculator, a teacher or administrator will be able to identify your calculator should you ever lose it.<br /><br />
Please make every effort to accurately provide both pieces of information requested.  It is easier to certify that you are the owner of a particular calculator this way.<br />
The instructions provided here are known to work with the TI-83 and TI-84 calculators.  Please enter your calculator information.  If you have more than one calculator, only enter information for one calculator at a time.<br />
<form method="post" action="[<$I2_ROOT>]calc" class="boxform">
<input type="hidden" name="calc_form" value="add" />
<table border="0"><tr><td>
Serial number: (the digits up to, but excluding, the first letter) on the back of your calculator.<br />
<input type="text" name="sn" maxlength="10" style="width:150px" value="" />
</td></tr>
<tr><td>
ID number: Turn on your calculator.  Press [2nd], then [+] for the memory menu.  Press [1] for "About".  Enter the 14-digit string, without the dashes.<br />
<input type="text" name="id" maxlength="14" style="width:150px" value="" />
</td></tr>
<tr><td>
<input type="submit" value="Add" style="width:75px" name="submit" />
</td></tr></table>
</form><br />
If you do not own one of the calculators listed below, click on it, then click "Delete"
<form method="post" action="[<$I2_ROOT>]calc" class="boxform">
<input type="hidden" name="calc_form" value="delete" />
<table border="0"><tr><td>
Serial number (ID number)
</td></tr><tr><td>
<select name="sn" size="3" style="width:300px">
[<foreach from=$calcs item=id>]
<option value="[<$id.calcsn>]">[<$id.calcsn>] ([<$id.calcid>])</option>
[</foreach>]
</select>
</td></tr><tr><td>
<input type="submit" value="Delete" style="width:75px" name="submit" />
</td></tr></table>
</form>
