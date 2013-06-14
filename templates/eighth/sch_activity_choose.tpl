[<if !isset($act)>][<include file="eighth/header.tpl">][</if>]
<select name="activity_list" id="activity_list" size="20" onchange="location.href='[<$I2_ROOT>]eighth/sch_activity/view/aid/' + this.options[this.selectedIndex].value">
[<foreach from=$activities item="activity">]
	<option value="[<$activity->aid>]"[<if $activity->cancelled>] style="font-weight: bold; color: #FF0000;"[<elseif $activity->scheduled>] style="font-weight: bold; color: #FF6600;"[</if>][<if isset($act) && ($act->aid == $activity->aid)>] selected="selected"[</if>]>[<$activity->aid>]: [<$activity->name_r>]</option>
[</foreach>]
</select>
<br />
<input type="search" results="0" placeholder="Search for an activity" id="search_box" style="width: 30em;" />
<form name="sch_activity_choose_form" action="[<$I2_ROOT>]eighth/sch_activity/view" method="post">
Activity ID: <input type="text" name="aid"/>
</form>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/eighth_search.js"></script>
<script language="javascript" type="text/javascript">
	document.sch_activity_choose_form.aid.focus();
	makeSearchable(document.getElementById("activity_list"), document.getElementById("search_box")); 
</script>
