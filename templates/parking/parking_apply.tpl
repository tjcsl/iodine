[<if $is_admin>]You are a parking admin. <a href="[<$I2_ROOT>]parking/admin">Review parking applications</a><br /><br />[</if>]

<br /><b>Note: The deadline for this form is [<$deadline>].</b><br /><br />

<form action="[<$I2_ROOT>]parking" method="post" class="boxform">
<input type="hidden" name="parking_apply_form" value="apply" />
<table border=0 cellpadding=3>
 <tr>
  [<if $submitdate>]
   <td align="right"><b>Your application was originally submitted on:</b></td>
   <td>[<$submitdate>]</td>
  [<else>]
   <td align="right"
  [</if>]
 </tr>
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
  <td><input type="checkbox" name="mship" [<if $mship>]checked [</if>]/>
 </tr>

 <tr>
  <td align="right">Name of partner if you are submitting a joint application:</td>
  <td><input type="text" name="otherdriver" value="[<$otherdriver>]" />
 </tr>

</table>

For each car you may be parking in this spot, please enter the following data:

<table border=0 cellpadding=3>
 <tr><td>License Plate</td><td>Car Make</td><td>Car Model</td><td>Car Color</td><td>Car Year</td></tr>

 <tr>
  <td><input type="text" name="car[0][plate]" value="[<$cars.0.plate>]" />
  <td><input type="text" name="car[0][make]" value="[<$cars.0.make>]" />
  <td><input type="text" name="car[0][model]" value="[<$cars.0.model>]" />
  <td><input type="text" name="car[0][color]" value="[<$cars.0.color>]" />
  <td><input type="text" name="car[0][year]" value="[<$cars.0.year>]" />
 </tr>
 <tr>
  <td><input type="text" name="car[1][plate]" value="[<$cars.1.plate>]" />
  <td><input type="text" name="car[1][make]" value="[<$cars.1.make>]" />
  <td><input type="text" name="car[1][model]" value="[<$cars.1.model>]" />
  <td><input type="text" name="car[1][color]" value="[<$cars.1.color>]" />
  <td><input type="text" name="car[1][year]" value="[<$cars.1.year>]" />
 </tr>
 <tr>
  <td><input type="text" name="car[2][plate]" value="[<$cars.2.plate>]" />
  <td><input type="text" name="car[2][make]" value="[<$cars.2.make>]" />
  <td><input type="text" name="car[2][model]" value="[<$cars.2.model>]" />
  <td><input type="text" name="car[2][color]" value="[<$cars.2.color>]" />
  <td><input type="text" name="car[2][year]" value="[<$cars.2.year>]" />
 </tr>
 <tr>
  <td><input type="text" name="car[3][plate]" value="[<$cars.3.plate>]" />
  <td><input type="text" name="car[3][make]" value="[<$cars.3.make>]" />
  <td><input type="text" name="car[3][model]" value="[<$cars.3.model>]" />
  <td><input type="text" name="car[3][color]" value="[<$cars.3.color>]" />
  <td><input type="text" name="car[3][year]" value="[<$cars.3.year>]" />
 </tr>

</table>

<p>
Note that submission is not final; you can change your values until the deadline.
And don't lie; you'll get caught and it will only hurt your chances.
</p>
<br />

<input type="hidden" name="go_to_form" value="yes" />
<center><input type="submit" value="Submit" /></center>
</form>


<p>
If you wish to withdraw your application, click below:
</p>

<center><form action="[<$I2_ROOT>]parking" method="post" class="boxform">
<input type="hidden" name="go_to_form" value="yes" />
<input type="hidden" name="parking_apply_form" value="withdraw" />
<input type="submit" value="Withdraw" />
</form></center>
