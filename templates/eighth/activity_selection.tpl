[<if !isset($act)>][<include file="eighth/header.tpl">][</if>]
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="activity_list" size="10" onChange="location.href='[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">]/[<if isSet($bid)>]bid/[<$bid>]/[</if>][<$field|default:"aid">]/' + this.options[this.selectedIndex].value">
[<foreach from=$activities item='activity'>]
	<option value="[<$activity->aid>]"[<if isset($act) && ($act->aid == $activity->aid)>] SELECTED[</if>]>[<$activity->aid>]: [<$activity->name_r>][<if $activity->comment_short>] - [<$activity->comment_short>][</if>]</option>
[</foreach>]
</select><br/>
<form name="activity_selection_form" action="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:"view">][<if isSet($bid)>]/bid/[<$bid>][</if>]" method="GET">
	Activity ID: <input type="text" name="aid">
</form>
<script language="javascript" type="text/javascript">
	document.activity_selection_form.aid.focus();
</script>
[<if isset($add) >]
<br />
<br />
<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
	Name: <input type="text" name="name" /><br />
	<input type="submit" value="Add" />
</form>
[</if>]
