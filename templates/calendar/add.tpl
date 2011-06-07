<!-- interface coded by Zachary "Gamer_Z." Yaro...yeah, I know it sucks -->
<script type="text/javascript">
	function formatDate() {
		var year = document.getElementById("year").value;
		var month = document.getElementById("month").value;
		var day = document.getElementById("date").value;
		var starttime = document.getElementById("starttimetxt").value;
		starttime = starttime.replace(/:/g, "-");
		var endtime = document.getElementById("endtimetxt").value;
		endtime = endtime.replace(/:/g, "-");

		document.getElementById("starttime").value = year + "-" + month + "-" + day + " " + starttime;
		document.getElementById("endtime").value = year + "-" + month + "-" + day + " " + endtime;
	}
</script>
<form action="[<$I2_SELF>]" method="post">
	<input type="hidden" name="action" value="add"/>
	<label for="title" style="font-size:larger;">Event title:</label>
	<input type="text" name="title" id="title" style="font-size:larger;"/>
	<br/>
	<br/>
	Date:
	<select id="month" onchange="formatDate();">
		<option value="01">January</option>
		<option value="02">February</option>
		<option value="03">March</option>
		<option value="04">April</option>
		<option value="05">May</option>
		<option value="06">June</option>
		<option value="07">July</option>
		<option value="08">August</option>
		<option value="09">September</option>
		<option value="10">October</option>
		<option value="11">November</option>
		<option value="12">December</option>
	</select>
	<select id="date" onchange="formatDate();">
		<option value="01">1</option>
		<option value="02">2</option>
		<option value="03">3</option>
		<option value="04">4</option>
		<option value="05">5</option>
		<option value="06">6</option>
		<option value="07">7</option>
		<option value="08">8</option>
		<option value="09">9</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
		<option value="13">13</option>
		<option value="14">14</option>
		<option value="15">15</option>
		<option value="16">16</option>
		<option value="17">17</option>
		<option value="18">18</option>
		<option value="19">19</option>
		<option value="20">20</option>
		<option value="21">21</option>
		<option value="22">22</option>
		<option value="23">23</option>
		<option value="24">24</option>
		<option value="25">25</option>
		<option value="26">26</option>
		<option value="27">27</option>
		<option value="28">28</option>
		<option value="29">29</option>
		<option value="30">30</option>
		<option value="31">31</option>
	</select>
	<select id="year" onchange="formatDate();">
		<option value="2011">2011</option>
		<option value="2012">2012</option>
	</select>
	<br/>
	Start time:
	<input type="text" value="00:00:00" id="starttimetxt" onchange="formatDate();" onkeyup="formatDate();"/>
	&nbsp;&nbsp;&nbsp;
	End time:
	<input type="text" value="23:59:59" id="endtimetxt" onchange="formatDate();" onkeyup="formatDate();"/>
	<input type="hidden" name="starttime" id="starttime" value="2011-01-01 00-00-00">
	<input type="hidden" name="endtime" id="endtime" value="2011-01-01 23-59-59">
	<br/>
	<br/>
	Groups:
	<select id="groups" class="groups_list" name="add_groups[]">
		[<foreach from=$groups item=group>]
			<option value="[<$group->gid>]">[<$group->name>]</option>
		[</foreach>]
	</select>
	<br/>
	<br/>
	Event description:
	<br/>
	<textarea name="text" style="width:80%;"></textarea>
	<br/>
	<br/>
	<input type="submit" value="Add event"/>
</form>
