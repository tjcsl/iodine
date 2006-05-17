<form action="[<$I2_ROOT>]search/results/[<$search_destination>]" method="POST">
Grade:<br />
<input type="checkbox" name="graduationYear[]" value="[<$first_year+3>]"/>Freshman<br />
<input type="checkbox" name="graduationYear[]" value="[<$first_year+2>]"/>Sophomore<br />
<input type="checkbox" name="graduationYear[]" value="[<$first_year+1>]"/>Junior<br />
<input type="checkbox" name="graduationYear[]" value="[<$first_year>]"/>Senior<br />
<br />
Name/ID: <input type="text" name="name"/><br /><br />
<input type="submit" value="[<$action_name|default:'Search'>]"/>
</form>
