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
<form action="[<$I2_ROOT>]eighth/vcp_schedule/change/uid/[<$uid>]/bids/[<$bids>]" method="post">
	<select name="aid" size="10" onchange="changeDescription(this.options[this.selectedIndex].value)">
[<foreach from=$activities item="activity">]
	[<assign var=capacity value=$activity->capacity>]
        <option value="[<$activity->aid>]"[<if $activity->cancelled >] style="color: #FF0000; font-weight: bold;"[<elseif $activity->restricted >] style="color: #FF6600; font-weight: bold;"[<elseif $capacity != -1 && $activity->member_count >= $capacity>] style="color: #0000FF; font-weight: bold;"[</if>]>[<$activity->aid>]: [<$activity->name_r>][<if $activity->comment_notsoshort>] ([<$activity->comment_notsoshort>])[</if>]</option>
[</foreach>] 
	</select><br />
	<input type="text" name="aid" id="aid_box" maxlength="4" size="4" /><input type="submit" value="Change" /><br />
</form>
[<foreach from=$activities item="activity">]
	[<assign var=capacity value=$activity->capacity>]
<div id="desc_[<$activity->aid>]" style="display: none; border: solid thin; padding: 5px; margin: 5px; width: 300px;">
	<span class="bold">Description:</span><br />
	[<$activity->description>]<br />
	[<if $activity->comment>]<br /><b>[<$activity->comment>]</b><br /><br />[</if>]
	[<$activity->member_count>] student(s) are signed up [<if $capacity != -1>]out of [<$capacity>] allowed [</if>]for this activity.<br />
	[<if $activity->cancelled>]<br /><span class="bold" style="color: #FF0000;">CANCELLED</span>[</if>]
	[<if $capacity != -1 && $activity->member_count >= $capacity>]<br /><span class="bold" style="color: #0000FF;">CAPACITY FULL</span>[</if>]
	[<if $activity->restricted>]<br /><span class="bold" style="color: #FF6600;">RESTRICTED</span>[</if>]
</div>
[</foreach>]
