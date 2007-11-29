<a href="[<$I2_ROOT>]newimport">Back to data import home</a><br /><br />

This may take a <strong>really, really</strong> long time.

<form enctype="multipart/form-data" action="[<$I2_ROOT>]newimport/students_doit" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />

<fieldset style="width: 300px">
<legend>Database Cleaning</legend>
Is this the initial data import of the year?<br ?>
<input type="radio" name="startyear" value="1" /> Yes<br />
<input type="radio" name="startyear" value="0" checked="CHECKED" /> No<br />
</fieldset>

<fieldset style="width: 300px">
<legend>Students</legend>
Select the student data file (INTRANET.***):<br />
<input type="file" name="studentfile" /><br />
</fieldset>

<fieldset style="width: 300px">
<legend>Schedules</legend>
Select the student schedule file (SCS.***):<br />
<input type="file" name="schedulefile" /><br /><br />
Select the course info file (CLS.***):<br />
<input type="file" name="coursefile" /><br />
</fieldset>

<fieldset style="width: 300px">
<legend>Do It</legend>
<input type="checkbox" name="doit" value="1" /> Yes, I really want to do this.<br />
<input type="submit" name="submit" value="Do Import" />
</fieldset>

</form>
