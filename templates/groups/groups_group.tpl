<a href="[<$I2_ROOT>]groups">Groups Home</a><br />
<br />
Group: [<$group>]<br />
<br />
[<if count($members) > 0>]
This group has the following members:<br />
[<foreach from=$members item=person>]
  [<$person.name>][<if isset($person.admin) && ($admin == "all" || $admin == "master")>] ([<$person.admin>])[</if>]<br />
[</foreach>]
[<else>]
This group has no members.<br />
[</if>]
[<if $admin == "all" || $admin == "master">]
<br />
You may add and remove people from this group.<br />
To add a person, enter their user id here:<br />
<form method="post" action="[<$I2_ROOT>]groups/pane/[<$group>]" class="boxform">
<input type="hidden" name="group_form" value="add" />
<input type="text" name="uid" value="" /><input type="submit" value="Add" name="submit" /><br />
</form>
To remove a person, enter their user id here:<br />
<form method="post" action="[<$I2_ROOT>]groups/pane/[<$group>]" class="boxform">
<input type="hidden" name="group_form" value="remove" />
<input type="text" name="uid" value="" /><input type="submit" value="Remove" name="submit" /><br />
</form>
To make a current member a group admin, enter their user id here:<br />
<form method="post" action="[<$I2_ROOT>]groups/pane/[<$group>]" class="boxform">
<input type="hidden" name="group_form" value="make_admin" />
<input type="text" name="uid" value="" /><input type="submit" value="Make Admin" name="submit" /><br />
</form>
[</if>]
[<if $admin == "master">]
To remove admin permissions of a current admin, enter their user id here:<br />
<form method="post" action="[<$I2_ROOT>]groups/pane/[<$group>]" class="boxform">
<input type="hidden" name="group_form" value="remove_admin" />
<input type="text" name="uid" value="" /><input type="submit" value="Remove Admin" name="submit" /><br />
</form>
[</if>]
