<script language="javascript" type="text/javascript">
	var olddesc = null;
	function changeDescription(aid) {
		alert("aid: " + aid);
		var desc = document.getElementById('desc_' + aid);
		if(olddesc) {
			olddesc.style.display = 'none';
		}
		desc.style.display = 'block';
		olddesc = desc;
		document.getElementById('aid_box').value = aid;
	}
</script>
<form action="[<$I2_ROOT>]eighth/vcp_schedule/change/uid/[<$uid>]/bid/[<$bid>]" method="post">
	<select name="aid" size="10" onChange="javascript:changeDescription(this.options[this.selectedIndex].value]);">
[<foreach from=$activities item="activity">]
		<option value="[<$activity->aid>]">[<$activity->aid>]: [<$activity->name_r>]</option>
[</foreach>]
	</select>
	<input type="text" name="aid" id="aid_box" /><input type="submit" value="Change" />
</form>
[<foreach from=$activities item="activity">]
	<div id="desc_[<$activity->aid>]" style="display: none">[<$activity->description>]</div>
[</foreach>]
