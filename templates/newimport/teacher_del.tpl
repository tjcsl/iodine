<form action="[<$I2_ROOT>]newimport/teacher_delete_doit" method="POST">
You are about to delete [<$user->name>] from the database. Are you sure you want to do this?<br />
<input type="hidden" name="uid" value="[<$user->iodineUid>]" />
<input type="submit" name="DOIT" value="Yes, delete this teacher" />
</form>
