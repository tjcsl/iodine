<a href="[<$I2_ROOT>]seniors/">Back to Senior Destinations</a><br /><br />

If the college you plan to attend or the subject you plan to major in is not listed below, please notify <a href="mailto:intranet@tjhsst.edu">intranet@tjhsst.edu</a> and include the name of the missing item.<br /><br />

<form action="[<$I2_ROOT>]seniors/submit/" method="post">
	<input type="hidden" name="seniors_form" value="yeah" />
	Please choose your:
	<fieldset>
		<legend>College</legend>
		<select name="ceeb">
			[<foreach from=$colleges item=college>]
			<option value="[<$college.CEEB>]" [<if isset($sel_ceeb) && $sel_ceeb == $college.CEEB>]selected="selected"[</if>] />[<$college.CollegeName>]
			[</foreach>]
		</select><br />
		Are you sure? <input type="checkbox" name="dest_sure" [<if isset($dest_sure) && $dest_sure>]checked="checked"[</if>] />
	</fieldset>
	<fieldset>
		<legend>Major</legend>
		<select name="major">
			[<foreach from=$majors item=major>]
			<option value="[<$major.MajorID>]" [<if isset($sel_major) && $sel_major == $major.MajorID>]selected="selected"[</if>] />[<$major.Major>]
			[</foreach>]
		</select><br />
		Are you sure? <input type="checkbox" name="major_sure"[<if isset($major_sure) && $major_sure>]checked="checked"[</if>] />
	</fieldset>
<input type="submit" value="Submit" />
</form>
