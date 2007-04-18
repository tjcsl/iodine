[<* calendar.tpl, written by Joshua Cranmer
Last update: 04/15/07
USAGE: include file='utils/calendar.tpl' post_var='var'
'var' should be replaced with the expected variable of the form.
'var'_mon, 'var'_year, 'var'_day, 'var'_hour, 'var'_min, 'var'_sec are all reserved by this template.
*>]
<input type="hidden" name="[<$post_var>]" />
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
  <option value="11">Nev</option>
  <option value="12">Dec</option>
</select>
<select id="[<$post_var>]_day">
  <option value="0">Day</option>
  <option>1</option>
  <option>2</option>
  <option>3</option>
  <option>4</option>
  <option>5</option>
  <option>6</option>
  <option>7</option>
  <option>8</option>
  <option>9</option>
  <option>10</option>
  <option>11</option>
  <option>12</option>
  <option>13</option>
  <option>14</option>
  <option>15</option>
  <option>16</option>
  <option>17</option>
  <option>18</option>
  <option>19</option>
  <option>20</option>
  <option>21</option>
  <option>22</option>
  <option>23</option>
  <option>24</option>
  <option>25</option>
  <option>26</option>
  <option>27</option>
  <option>28</option>
  <option>29</option>
  <option>30</option>
  <option>31</option>
</select>
<select id="[<$post_var>]_year">
  <option value="0">Year</option>
</select>
<script type="text/javascript">
[<* This code will select the last year, the current year, and next year for year *>]
var sel = document.getElementById("[<$post_var>]_year");
var now = new Date();
if (now.getMonth() < 6) {
	var year = document.createElement("option");
	year.value = now.getFullYear()-1;
	year.text = year.value;
	sel.add(year, null);
}
year = document.createElement("option");
year.value = now.getFullYear();
year.text = year.value;
sel.add(year, null);
if (now.getMonth() >= 6) {
	year = document.createElement("option");
	year.value = now.getFullYear()+1;
	year.text = year.value;
	sel.add(year, null);
}

[<* Now we add the code to validate and submission information. *>]
var form = sel.form;
function submit_form_[<$post_var>]() {
	var supra = form.elements.namedItem("[<$post_var>]");
	var month = form.elements.namedItem("[<$post_var>]_mon").value;
	var year = form.elements.namedItem("[<$post_var>]_year").value;
	var day = form.elements.namedItem("[<$post_var>]_day").value;
	if (!validate_[<$post_var>](month, year, day)) {
		alert("The date is not a legal date.");
		return false;
	}
	month = month < 10 ? '0'+month : month;
	day = day < 10 ? '0'+day : day;
	supra.value = year+'-'+month+'-'+day;
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
</script>
