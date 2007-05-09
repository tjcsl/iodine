[<* calendar.tpl, written by Joshua Cranmer
Last update: 04/15/07
USAGE: include file='utils/calendar.tpl' post_var='var'
'var' should be replaced with the expected variable of the form.
'var'_mon, 'var'_year, 'var'_day, 'var'_hour, 'var'_min, 'var'_sec are all reserved by this template.
*>]
<input type="hidden" name="[<$post_var>]" value="3000-01-01 00:00:00"/>
<input type="checkbox" name="[<$post_var>]_allow" checked="checked" onchange="disable(this);"/>
<select id="[<$post_var>]_mon">
  <option value="0">Month</option>
  <option value="1">Jan</option>
  <option value="2">Feb</option>
  <option value="3">Mar</option>
  <option value="4">Apr</option>
  <option value="5">May</option>
  <option value="6">Jun</option>
  <option value="7">Jul</option>
  <option value="8">Aug</option>
  <option value="9">Sep</option>
  <option value="10">Oct</option>
  <option value="11">Nov</option>
  <option value="12">Dec</option>
</select>
<select id="[<$post_var>]_day">
  <option value="0">Day</option>
[<php>]
  for ($i=1;$i<=31;$i++) {
    echo "<option value=\"$i\">$i</option>\n";
  }
[</php>]
</select>
[<php>]
	$today = getdate();
	if ($today['mon'] < 6) {
		$year2 = $today['year'];
		$year1 = $year2-1;
	} else {
		$year1 = $today['year'];
		$year2 = $year1+1;
	}
	$this->assign('y1',$year1);
	$this->assign('y2',$year2);
[</php>]
<select id="[<$post_var>]_year">
  <option value="0">Year</option>
  <option value="[<$y1>]">[<$y1>]</option>
  <option value="[<$y2>]">[<$y2>]</option>
</select>
<script type="text/javascript">
[<* This code will select the last year, the current year, and next year for year *>]
var form = document.getElementById("[<$post_var>]_year").form;
function submit_form_[<$post_var>]() {
	var supra = form.elements.namedItem("[<$post_var>]");
	var month = form.elements.namedItem("[<$post_var>]_mon").value;
	var year = form.elements.namedItem("[<$post_var>]_year").value;
	var day = form.elements.namedItem("[<$post_var>]_day").value;
	if (form.elements.namedItem("[<$post_var>]_mon").disabled) {
		supra.value = '3000-01-01';
	} else {
		if (!validate_[<$post_var>](month, year, day)) {
			alert("The date is not a legal date.");
			return false;
		}
		month = month < 10 ? '0'+month : month;
		day = day < 10 ? '0'+day : day;
		supra.value = year+'-'+month+'-'+day;
	}
}
function validate_[<$post_var>](month, year, day) {
	var leap = false;
	if (year % 400 == 0)
		leap = true;
	else if (year % 100 == 0)
		leap = false;
	else if (year % 4 == 0)
		leap = true;
	var days = [-1, 31, leap+28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
	if (month == 0 || year == 0 || day == 0)
		return false;
	if (day > days[month])
		return false;
	return true;
}
form.onsubmit = submit_form_[<$post_var>];

function disable(checkbox) {
	var form = checkbox.form;
	var month = form.elements.namedItem("[<$post_var>]_mon");
	var year = form.elements.namedItem("[<$post_var>]_year");
	var day = form.elements.namedItem("[<$post_var>]_day");
	month.disabled = !checkbox.checked;
	year.disabled = !checkbox.checked;
	day.disabled = !checkbox.checked;
}
</script>
