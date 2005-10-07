<script language="javascript" type="text/css">
	var olddesc = null;
	function changeDescription(aid) {
		var desc = document.getElementById('desc_' + aid);
		if(olddesc) {
			olddesc.style.display = 'none';
		}
		desc.style.display = 'block';
		olddesc = desc;
	}
</script>
<form action="[<$I2_ROOT>]eighth/vcp_schedule/change" method="post">
	<select name="aid" onChange="changeDescription(this.options[this.selectedIndex].value]);">
[<foreach from=$activities item="activity">]
		<option value="[<$activity->aid>]">[<$activity->aid>]: [<$activity->name_r>]</option>
[</foreach>]
	</select>
	<input type="text" name="aid" /><input type="submit" value="Change" />
</form>
[<foreach from=$activities item="activity">]
	<div id="desc_[<$activity->aid>]" style="display: none">[<$activity->description>]</div>
[</foreach>]
