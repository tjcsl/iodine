[<include file="eighth/header.tpl">]
<script type="text/javascript">
function sponsorSelect(sid) {
	location.href="[<$I2_ROOT>]eighth/[<$method>]/[<$op|default:'view'>]/sid/" + sid;
}
</script>
<!--[<$sponsors|@print_r>]
-->
<span style="font-weight: bold; font-size: 125%;">[<$title|default:"">]</span><br />
<select name="sponsor_list" id="spons_box" size="10" onchange="sponsorSelect(this.options[this.selectedIndex].value)">
[<*if it is called sponsor_box then adblock might filter it*>]
[<foreach from=$sponsors item='sponsor'>]
	<option value="[<$sponsor['sid']>]">[<$sponsor.name_comma>]</option>[</foreach>]
</select>
[<if isset($add)>]
	<br /><br />
	<form action="[<$I2_ROOT>]eighth/[<$method>]/add" method="post">
		First Name: <input type="text" name="fname" /><br />
		Last Name: <input type="text" name="lname" /><br />
		<input type="submit" value="Add" />
	</form>
[</if>]
