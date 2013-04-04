[<* calendar.tpl, originally written by Joshua Cranmer
USAGE: include file='utils/calendar.tpl' post_var='var'
'var' should be replaced with the expected variable of the form.
'var'_mon, 'var'_year, and 'var'_day are all reserved by this template.
*>]
<input type="hidden" name="[<$post_var>]" value="3000-01-01 00:00:00"/>
<input type="checkbox" name="[<$post_var>]_allow" checked="checked" onchange="disable(this);"/>
[<if $smarty.now|date_format:'%m' < 6 >]
[<assign var="first_year" value="-1">]
[<assign var="last_year" value="+0">]
[<else>]
[<assign var="first_year" value="+0">]
[<assign var="last_year" value="+1">]
[</if>]
[<html_select_date month_empty="Month" month_format="%b" month_extra="id=`$post_var`_mon onchange=submit_form_`$post_var`()"
 day_empty="Day" day_format="%d" day_extra="id=`$post_var`_day onchange=submit_form_`$post_var`()" 
 year_empty="Year" year_extra="id=`$post_var`_year onchange=submit_form_`$post_var`()" start_year=$first_year end_year=$last_year time="0000-00-00">]
<span id="alerter" style="color: #ff0000"></span>
<script type="text/javascript">
[<* This code will select the last year, the current year, and next year for year *>]
function submit_form_[<$post_var>]() {
	var form = document.getElementById("[<$post_var>]_year").form;
	var supra = form.elements.namedItem("[<$post_var>]");
	var month = form.elements.namedItem("[<$post_var>]_mon").value;
	var year = form.elements.namedItem("[<$post_var>]_year").value;
	var day = form.elements.namedItem("[<$post_var>]_day").value;
	if (form.elements.namedItem("[<$post_var>]_mon").disabled) {
		supra.value = '3000-01-01';
	} else {
		if (!validate_[<$post_var>](month, year, day)) {
			//alert("The date is not a legal date.");
			document.getElementById("alerter").innerHTML = "The date is not a legal date.";
			return false;
		}
		document.getElementById("alerter").innerHTML = "";
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
	// remove leading zeros
	if (day > days[month.replace(/^0+/, '')])
		return false;
	return true;
}

function disable(checkbox) {
	var form = checkbox.form;
	var month = form.elements.namedItem("[<$post_var>]_mon");
	var year = form.elements.namedItem("[<$post_var>]_year");
	var day = form.elements.namedItem("[<$post_var>]_day");
	month.disabled = !checkbox.checked;
	year.disabled = !checkbox.checked;
	day.disabled = !checkbox.checked;
	submit_form_[<$post_var>]();
}
</script>
