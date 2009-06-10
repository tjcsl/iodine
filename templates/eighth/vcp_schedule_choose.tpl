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
<form name="activity_select_form" action="[<$I2_ROOT>]eighth/vcp_schedule/change/uid/[<$uid>]/bids/[<$bids>][<if $start_date != NULL>]/start_date/[<$start_date>][</if>]" method="post">
	<select name="aid" id="select_activity" size="15" style="width:100%; margin-top:3px;" onchange="changeDescription(this.options[this.selectedIndex].value)">
	[<foreach from=$activities item="activity" key="key">]
		[<assign var=capacity value=$activity->capacity>]
		[<math equation="(x * 100)/(y)" x=$activity->member_count y=$capacity assign=percent>]
		<option value="[<$activity->aid>]" [<if $key == 0>]selected [</if>]
		[<if $activity->cancelled >] style="color: #FF0000; font-weight: bold;"
		[<elseif $activity->restricted >] style="color: #FF6600; font-weight: bold;"
		[<elseif $capacity != -1 && $activity->member_count >= $capacity>] style="color: #0000FF; font-weight: bold;"
		[<elseif $capacity != -1 && $percent >= 90 >] style="color: #00878D; font-weight: bold;" 
		[</if>]>[<$activity->aid>]: [<$activity->name_comment_r|escape:html>]</option>
	[</foreach>] 
	</select><br />
	<input type="text" name="aid" id="aid_box" maxlength="4" size="4" />
	<input type="submit" name="submit" value="Change" />
	[<if empty($manybids)>]
		<input type="submit" name="submit" value="View Roster" />
	[</if>]
</form>
[<foreach from=$activities item="activity">]
	[<assign var=capacity value=$activity->capacity>]
	[<assign var=members value=$activity->member_count>]
	<div id="desc_[<$activity->aid>]" style="display: none; border: solid thin; padding: 5px; margin-top:2px; margin-bottom:3px">
	[<$activity->name|escape:html>]
	[<if $activity->comment>]
		<br /><b>[<$activity->comment|escape:html>]</b>
	[</if>]
	[<if $activity->description>]
		<br /><br /><b>Description:</b> [<$activity->description|escape:html>]
	[</if>]
	[<if $activity->block_sponsors_comma_short>]
		<br /><br /><b>Sponsor:</b> [<$activity->block_sponsors_comma_short>]
	[</if>]
	<br /><br />[<$members>] student[<if $members == 1>] is[<else>]s are[</if>] signed up [<if $capacity != -1>]out of [<$capacity>] allowed [</if>]for this activity.<br />
	[<if $activity->cancelled>]
		<br /><span class="bold" style="color: #FF0000;">CANCELLED</span>
	[</if>]
	[<if $capacity != -1 && $activity->member_count >= $capacity>]
		<br /><span class="bold" style="color: #0000FF;">CAPACITY FULL</span>
	[</if>]
	[<if $activity->restricted>]
		<br /><span class="bold" style="color: #FF6600;">RESTRICTED</span>
	[</if>]
</div>
[</foreach>]
<script language="javascript" type="text/javascript">
	var select_activity = document.getElementById("select_activity");
	changeDescription(select_activity.options[select_activity.selectedIndex].value);
	document.getElementById('aid_box').focus();
	[<if count($activities) == 0>]
		document.getElementById('aid_box').value = "";
	[<else>]
		document.getElementById('aid_box').value = "[<$activities[0]->aid>]";
	[</if>]
	document.getElementById('aid_box').select();
</script>
