[<if isset($ask_override)>]
 <div style="color: red;">Assigning parking spot [<$override_spot>] to [<$override_name>]: parking spot already taken by <a href="[<$I2_ROOT>]parking/admin#[<$override_otherid>]">[<$override_othername>]</a>.
  <form action="[<$I2_ROOT>]parking/admin[<if isset($sort)>]?sort=[<$sort>][</if>]" method="post" class="boxform">
   <input type="hidden" name="parking_admin_form" value="person_assign" />
   <input type="hidden" name="person_id" value="[<$override_id>]" />
   <input type="hidden" name="spot" value="[<$override_spot>]" />
   <input type="hidden" name="override" value="true" />
   You can override this protection and force the parking spot to this person for joint parking spots:
   <input type="submit" value="Override" />
  </form>
 </div>
[</if>]

<div style="float: right; margin-right: 20px;"><a href="[<$I2_ROOT>]parking/print_apps">Print</a></div>

<h3>Parking Application Data Interface</h3><br />

Using this interface, you can:
<ul>
<li>change the deadline for applications.</li>
<li>sort the fields. The first dropdown box will have first priority, and etc. There is a random sort: for example, if you have 0 skips as the last sort, it will give you these people in alphabetical order. By choosing "random within last category", you can randomize the people that have 0 skips so that you can assign spots without preference to anything else. (Try it with and without to understand what it does)</li>
[<*<li>assign or remove parking spots. You must enter a number and then press Enter or click Set for that box. The form will save the space and then return you to the spot you were working on (saved sort preferences). To remove an assigned parking spot, enter a blank box or a 0 and set. If you assign an already assigned number, the interface will stop you to alert you that you are doing so. There is an option to override the protection if needed (for joint applicants).</li>*>]
</ul>
Some features to come include searching by field.<br /><br />
<form action="[<$I2_ROOT>]parking/admin[<if isset($sort)>]?sort=[<$sort>][</if>]" method="post" class="boxform">
 <input type="hidden" name="parking_admin_form" value="changestartdate" />
 <input type="text" name="startdate" value="[<$startdate>]" />
 <input type="submit" value="Update starting date" />
</form><br />
<form action="[<$I2_ROOT>]parking/admin[<if isset($sort)>]?sort=[<$sort>][</if>]" method="post" class="boxform">
 <input type="hidden" name="parking_admin_form" value="changedeadline" />
 <input type="text" name="deadline" value="[<$deadline>]" />
 <input type="submit" value="Update deadline" />
</form><br /><br />
[<*<form action="[<$I2_ROOT>]parking/admin[<if isset($sort)>]?sort=[<$sort>][</if>]" method="post" class="boxform">
 *<input type="hidden" name="parking_admin_form" value="sort" />
 *Priority 1:
 *<select name="sort1">
 * [<foreach from=$options key=name item=option>]
 *  <option value="[<$name>]" [<if $sort_selected.1 == $name>]selected="selected"[</if>]>[<$option.1>]</option>
 * [</foreach>]
 *</select><br />
 *Priority 2:
 *<select name="sort2">
 * [<foreach from=$options key=name item=option>]
 *  <option value="[<$name>]" [<if $sort_selected.2 == $name>]selected="selected"[</if>]>[<$option.1>]</option>
 * [</foreach>]
 *</select><br />
 *Priority 3:
 *<select name="sort3">
 * [<foreach from=$options key=name item=option>]
 *  <option value="[<$name>]" [<if $sort_selected.3 == $name>]selected="selected"[</if>]>[<$option.1>]</option>
 * [</foreach>]
 *</select><br />
 *Priority 4:
 *<select name="sort4">
 * [<foreach from=$options key=name item=option>]
 *  <option value="[<$name>]" [<if $sort_selected.4 == $name>]selected="selected"[</if>]>[<$option.1>]</option>
 * [</foreach>]
 *</select><br />
 *Priority 5:
 *<select name="sort5">
 * [<foreach from=$options key=name item=option>]
 *  <option value="[<$name>]" [<if $sort_selected.5 == $name>]selected="selected"[</if>]>[<$option.1>]</option>
 * [</foreach>]
 *</select><br />
 *<input type="submit" value="Sort">
*</form>*>]

Note: For joint applications, the number of 8th period skips is the sum of the two people's skips.
<br /><br />
<table>
 <tr>
  <th><a href="[<$I2_ROOT>]parking/admin?sort=[<if isset($sort) && $sort=='spot'>]spot_reverse[<else>]spot[</if>]">Spot</th>
  <th><a href="[<$I2_ROOT>]parking/admin?sort=[<if isset($sort) && $sort=='name'>]name_reverse[<else>]name[</if>]">Name</a></th>
  <th><a href="[<$I2_ROOT>]parking/admin?sort=[<if isset($sort) && $sort=='year'>]year_reverse[<else>]year[</if>]">Yr</th>
  <th><a href="[<$I2_ROOT>]parking/admin?sort=[<if isset($sort) && $sort=='mentor'>]mentor_reverse[<else>]mentor[</if>]">Mntr</th>
  <th><a href="[<$I2_ROOT>]parking/admin?sort=[<if isset($sort) && $sort=='skips'>]skips_reverse[<else>]skips[</if>]">8th</th>
  <th><a href="[<$I2_ROOT>]parking/admin?sort=[<if isset($sort) && $sort=='email'>]email_reverse[<else>]email[</if>]">Email</th>
  <th>Plate</th>
  <th>Make</th>
  <th>Model</th>
  <th>Year</th>
 </tr>
 [<foreach from=$people item=person>]
  [<foreach from=$person.cars item=car>]
   <tr>
    [<if $car.index == 0>]
     <td rowspan="[<$person.numcars>]" style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">
      <a name="[<$person.id>]" />[<if $person.assigned == "">]---[<else>][<$person.assigned>][</if>]<br />
      <form action="[<$I2_ROOT>]parking/admin[<if isset($sort)>]?sort=[<$sort>][</if>]" method="post" class="boxform"><input type="hidden" name="parking_admin_form" value="person_assign" /><input type="hidden" name="person_id" value="[<$person.id>]" /><input type="text" name="spot" value="[<$person.assigned>]" style="width: 40px; font-size: 10px;" /><input type="submit" value="Set" style="cursor: pointer; font-size: 10px;" /></form>
     </td>
     <td rowspan="[<$person.numcars>]" style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.name>]</td>
     <td rowspan="[<$person.numcars>]" style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.grade>]</td>
     <td rowspan="[<$person.numcars>]" style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.mentor>]</td>
     <td rowspan="[<$person.numcars>]" style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.skips>]</td>
     <td rowspan="[<$person.numcars>]" style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.email>]</td>
     [</if>]
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$car.plate>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$car.make>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$car.model>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$car.year>]</td>
   </tr>
  [<foreachelse>]
   <tr>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">
     <a name="[<$person.id>]" />[<if $person.assigned == "">]---[<else>][<$person.assigned>][</if>]
     <form action="[<$I2_ROOT>]parking/admin[<if isset($sort)>]?sort=[<$sort>][</if>]" method="post" class="boxform"><input type="hidden" name="parking_admin_form" value="person_assign" /><input type="hidden" name="person_id" value="[<$person.id>]" /><input type="text" name="spot" value="[<$person.assigned>]" style="width: 40px; font-size: 10px;" /><input type="submit" value="Set" style="cursor: pointer; font-size: 10px;" /></form>
    </td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.name>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.grade>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.mentor>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">[<$person.skips>]</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">&nbsp;</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">&nbsp;</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">&nbsp;</td>
    <td style="padding: 4px; border-right: 1px solid gray; border-bottom: 1px solid gray;[<if $person.isTeacher>] color: red;[</if>]">&nbsp;</td>

   </tr>
  [</foreach>]
 [</foreach>]
</table>
