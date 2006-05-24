[<if !isset($act)>][<include file="eighth/header.tpl">][</if>]
<select name="activity_list" size="20" onChange="location.href='[<$I2_ROOT>]eighth/sch_activity/view/aid/' + this.options[this.selectedIndex].value">
[<foreach from=$activities item="activity">]
	<option value="[<$activity->aid>]"[<if $activity->cancelled>] style="font-weight: bold; color: #FF0000;"[<elseif $activity->scheduled>] style="font-weight: bold; color: #FF6600;"[</if>][<if isset($act) && ($act->aid == $activity->aid)>] SELECTED[</if>]>[<$activity->aid>]: [<$activity->name_r>]</option>
[</foreach>]
</select>
<form name="sch_activity_choose_form" action="[<$I2_ROOT>]eighth/sch_activity/view" method="POST">
Activity ID: <input type="text" name="aid"/>
</form>
<script language="javascript" type="text/javascript">
	document.sch_activity_choose_form.aid.focus();
</script>
