<script language="javascript" type="text/javascript">
	var olddesc = null;
	function changeDescription(aid) {
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
	<select name="aid" size="10" onchange="changeDescription(this.options[this.selectedIndex].value)">
[<foreach from=$activities item="activity">]                                    
        <option value="[<$activity->aid>]"[<if $activity->cancelled >] style="color: #FF0000; font-weight: bold;"[<elseif $activity->restricted >] style="color: #FF6600; font-weight: bold;"[</if>]>[<$activity->aid>]: [<$activity->name_r>]</option>                                                                         
[</foreach>] 
	</select><br />
	<input type="text" name="aid" id="aid_box" maxlength="4" size="4" /><input type="submit" value="Change" /><br />
</form>
[<foreach from=$activities item="activity">]
<div id="desc_[<$activity->aid>]" style="display: none; border: solid thin; padding: 5px; margin: 5px; width: 300px;">
	<span class="bold">Description:</span><br />
	[<$activity->description>]<br />
	[<if $activity->cancelled>]<br /><span class="bold" style="color: #FF0000;">CANCELLED</span>[</if>]
	[<if $activity->restricted>]<br /><span class="bold" style="color: #FF6600;">RESTRICTED</span>[</if>]
</div>
[</foreach>]
