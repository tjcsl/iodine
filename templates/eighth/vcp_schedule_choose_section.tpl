<div class="activityHead">[<$sTitle>]</div>
[<foreach from=$activities item=activity key=key>]
	<div onclick="activityRowClicked(this)" class="[<$activity->aid>]_AID [<if $key%2==1>]odd[<else>]even[</if>] [<$activity->displayClass($selected_aid)>] activityRow">
		<div class="alIndicator"></div>
		[<$activity->aid>]: [<$activity->name_comment_r|escape:html>]
	</div>
	<div class="[<if $key%2==1>]odd[<else>]even[</if>] [<$activity->displayClass($selected_aid)>] activityInfo">
		<div class="activityInfoInner">
			<a href="[<$I2_ROOT>]eighth/vcp_schedule/favorite/uid/[<$activity->aid>]/bids/[<$bids>]">[<if $activity->favorite>]Unfavorite this activity[<else>]Favorite this activity[</if>]</a>
			<br /><b>[<$activity->name|escape:html>]</b>
			[<if $activity->comment>]
				([<$activity->comment|escape:html>])
			[</if>]
			[<if $activity->description>]
				<br ><b>Description:</b> [<$activity->description|escape:html>]
			[</if>]
			[<if $activity->block_sponsors_comma_short>]
				<br /><b>Sponsor:</b> [<$activity->block_sponsors_comma_short>]
			[</if>]
			[<if $activity->block_rooms_comma>]
				<br /><b>Room:</b> [<$activity->block_rooms_comma>]
			[</if>]
			<br />[<$activity->member_count>] student[<if $activity->member_count == 1>] is[<else>]s are[</if>] signed up [<if $activity->capacity != -1>]out of [<$activity->capacity>] allowed [</if>]for this activity.<br />

			[<if $activity->cancelled>]
				<span class="bold" style="color: #FF0000;">CANCELLED</span>&nbsp;<br />
			[</if>]
			[<if $activity->capacity != -1 && $activity->member_count >= $activity->capacity>]
				<span class="bold" style="color: #0000FF;">CAPACITY FULL</span>&nbsp;<br />
			[</if>]
			[<if $activity->restricted>]
				<span class="bold" style="color: #FF6600;">RESTRICTED</span>&nbsp;<br />
			[</if>]

			<input type="submit" name="submit" value="Change">
			[<if empty($manybids)>]
				<input type="submit" name="submit" value="View Roster" />
			[</if>]

		</div>
	</div>
[</foreach>]

