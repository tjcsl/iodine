[<php>]
$this->assign('first_year', User::get_gradyear(12));
[</php>]
<form name="search_pane_form" action="[<$I2_ROOT>]search/results/[<$search_destination>]" method="post">
Grade:<br />
[<if empty($choose_grades) || !empty($grade_9)>]<input type="checkbox" name="graduationYear[]" value="[<$first_year+3>]"/>Freshman<br />[</if>]
[<if empty($choose_grades) || !empty($grade_10)>]<input type="checkbox" name="graduationYear[]" value="[<$first_year+2>]"/>Sophomore<br />[</if>]
[<if empty($choose_grades) || !empty($grade_11)>]<input type="checkbox" name="graduationYear[]" value="[<$first_year+1>]"/>Junior<br />[</if>]
[<if empty($choose_grades) || !empty($grade_12)>]<input type="checkbox" name="graduationYear[]" value="[<$first_year>]"/>Senior<br />[</if>]
<br />
Name/ID: <input type="text" name="name"/><br /><br />
<input type="submit" value="[<$action_name|default:'Search'>]"/>
</form>
<script language="javascript" type="text/javascript">
	document.search_pane_form.name.focus();
</script>
