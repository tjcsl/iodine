[<if $is_admin>]You are a parking admin. <a href="[<$I2_ROOT>]parking/admin">Review parking applications</a><br /><br />[</if>]

<br /><b>Note: The deadline for this form is [<$deadline>].</b><br /><br />

<form action="[<$I2_ROOT>]parking/apply" method="post" class="boxform">
<input type="hidden" name="parking_apply_form" value="apply" />
<table border=0 cellpadding=3>
 [<if $submitdate>]
   <tr>
     <td align="right"><b>Your application was originally submitted on:</b></td>
     <td>[<$submitdate>]</td>
   </tr>
 [</if>]
 <tr>
  <td align="right">Name:</td>
  <td>[<$name>]</td>
 </tr>

 <tr>
  <td align="right">Grade:</td>
  <td>[<$grade>]</td>
 </tr>

 <tr>
  <td align="right">8th Period Skips:</td>
  <td>[<$skips>]</td>
 </tr>

 <tr>
  <td align="right">Click if in mentorship:</td>
  <td><input type="checkbox" name="mship" [<if $mship>]checked="checked" [</if>]/></td>
 </tr>

 [<if $submitdate>]
 [<foreach from=$potential_partners item=pot_part>]
  [<if empty($otherdriver) || $otherdriver->uid != $pot_part->uid>]
   <tr>
     <td>&nbsp;</td><td>[<$pot_part->name>] has requested you as a parking partner. <a href="[<$I2_ROOT>]parking/partner/select/[<$pot_part->uid>]">Approve request</a></td>
   </tr>
  [</if>]
 [</foreach>]
 <tr>
  <td align="right">Partner (for joint application):</td>
  <td>
   [<if !empty($otherdriver)>]
    [<if $otherdriver_od == $I2_USER->uid>]
     You are partnered with [<$otherdriver->name>].
    [<else>]
     You have requested [<$otherdriver->name>] as your parking partner.
    [</if>]
    <a href="[<$I2_ROOT>]parking/partner">Change</a> or <a href="[<$I2_ROOT>]parking/partner/remove">remove</a> your parking partner
   [<else>]
    You do not have a parking partner. <a href="[<$I2_ROOT>]parking/partner">Request a partner</a>
   [</if>]
  </td>
 </tr>
 [<else>]
 <tr>
 <td colspan=2><strong>For a joint application, fill out the rest of the application, click submit below, and then select your partner here.</strong></td>
 </tr>
 [</if>]

</table>

For each car you may be parking in this spot, please enter the following data:

<table border=0 cellpadding=3>
 <tr><td>License Plate</td><td>Car Make</td><td>Car Model</td><td>Car Year</td></tr>

 <tr>
  <td><input type="text" name="car[0][plate]" value="[<$cars.0.plate>]" />
  <td><input type="text" name="car[0][make]" value="[<$cars.0.make>]" />
  <td><input type="text" name="car[0][model]" value="[<$cars.0.model>]" />
  <td><input type="text" name="car[0][year]" value="[<$cars.0.year>]" />
 </tr>
 <tr>
  <td><input type="text" name="car[1][plate]" value="[<$cars.1.plate>]" />
  <td><input type="text" name="car[1][make]" value="[<$cars.1.make>]" />
  <td><input type="text" name="car[1][model]" value="[<$cars.1.model>]" />
  <td><input type="text" name="car[1][year]" value="[<$cars.1.year>]" />
 </tr>
 <tr>
  <td><input type="text" name="car[2][plate]" value="[<$cars.2.plate>]" />
  <td><input type="text" name="car[2][make]" value="[<$cars.2.make>]" />
  <td><input type="text" name="car[2][model]" value="[<$cars.2.model>]" />
  <td><input type="text" name="car[2][year]" value="[<$cars.2.year>]" />
 </tr>
 <tr>
  <td><input type="text" name="car[3][plate]" value="[<$cars.3.plate>]" />
  <td><input type="text" name="car[3][make]" value="[<$cars.3.make>]" />
  <td><input type="text" name="car[3][model]" value="[<$cars.3.model>]" />
  <td><input type="text" name="car[3][year]" value="[<$cars.3.year>]" />
 </tr>

</table>

<p>
Email Address:
<input type="text" name="email" value="[<$email>]" />
<br />
(For notification of parking-lot closures, etc.)
</p>
<p>
<strong>Your application will not be considered unless you enter an email address.</strong>
</p>
<hr />
<p>
Note that submission is not final; you can change your values until the deadline.
</p>
<br />

<input type="hidden" name="go_to_form" value="yes" />
<center><input type="submit" value="Submit" /></center>
</form>


<p>
If you wish to withdraw your application, click below:
</p>

<center><form action="[<$I2_ROOT>]parking/apply" method="post" class="boxform">
<input type="hidden" name="go_to_form" value="yes" />
<input type="hidden" name="parking_apply_form" value="withdraw" />
<input type="submit" value="Withdraw" />
</form></center>
