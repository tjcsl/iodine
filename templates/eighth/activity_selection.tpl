[<if !isset($act)>][<include file="eighth/header.tpl">][</if>]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="activity_list" id="activity_list" size="10" onchange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<if isset($bid)>]bid/[<$bid>]/[</if>][<$field|default:"aid">]/' + this.options[this.selectedIndex].value">
[<foreach from=$activities item='activity'>]
	<option value="[<$activity->aid>]"[<if isset($act) && ($act->aid == $activity->aid)>] selected="selected"[</if>]>[<$activity->aid>]: [<$activity->name_r>][<if $activity->comment_short>] - [<$activity->comment_short>][</if>]</option>
[</foreach>]
</select>
<br/>
<!-- begin search box code -->
Search: <input type="search" results="0" placeholder=" Search for an activity" onchange="filterList(value);" oninput="filterList(value);" onsearch="filterList(value);" style="margin-top:3px; width:256px;"/>
<script type="text/javascript">
	var activities = [];
	function filterList(txt) {
		txt = txt.toLowerCase();
		txt = txt.split(" or ");
		
		var currentList = document.getElementById("activity_list");
		currentList.innerHTML = "";
		
//              var listItems = savedList.getElementsByTagName("option");
		var listItems = savedList.options;
		for (var i = 0; i < listItems.length; i++) {
			for (var j = 0; j < txt.length; j++) {
				if (listItems[i].innerHTML.toLowerCase().indexOf(txt[j]) != -1) {
					currentList.appendChild(listItems[i].cloneNode(true));
					break;
				}
			}
		}
		
//              currentList.innerHTML = newList.innerHTML;
	}
	var savedList = document.getElementById("activity_list").cloneNode(true);

</script>
<!-- end search box code -->
<br/>
<form name="activity_selection_form" action="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">][<if isset($bid)>]/bid/[<$bid>][</if>]" method="get">
	Activity ID: <input type="text" name="[<$field|default:"aid">]" />
</form>
<script language="javascript" type="text/javascript">
	document.activity_selection_form.[<$field|default:"aid">].focus();
</script>
[<if isset($add) >]
<br />
<br />
<span style="font-weight: bold; font-size: 125%;">[<$add_title|default:"">]</span><br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Name: <input type="text" name="name" /><br />
	Activity ID: <select name="aid">
		<option value="auto">Automatically select a new ID number</option>
		[<foreach from=$add_aids item=aid>]
		<option value="[<$aid>]">[<$aid>]</option>
		[</foreach>]
	</select>
	<input type="submit" value="Add" />
</form>
[</if>]
