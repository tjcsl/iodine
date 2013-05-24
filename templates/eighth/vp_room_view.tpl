[<include file="eighth/header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$block->date|date_format:"%B %e, %Y">] - [<$block->block>] block</span><br /><br />
<table style="border: 0px; margin: 0px; padding: 0px; width: 100%;">
	<tr>
		[<if isset($inc.room)>]<th style="padding: 0px 5px; text-align: left;">Location</th>[</if>]
		[<if isset($inc.aid)>]<th style="padding: 0px 5px; text-align: left;">Activity ID</th>[</if>]
		[<if isset($inc.name)>]<th style="padding: 0px 5px; text-align: left;">Activity Name</th>[</if>]
		[<if isset($inc.teacher)>]<th style="padding: 0px 5px; text-align: left;">Teacher</th>[</if>]
		[<if isset($inc.students)>]<th style="padding: 0px 5px; text-align: left;">Students</th>[</if>]
		[<if isset($inc.capacity)>]<th style="padding: 0px 5px; text-align: left;">Capacity</th>[</if>]
	</tr>
[<foreach from=$utilizations item="utilization">]
	<tr>
		[<assign var=activity value=$utilization.activity>]
		[<assign var=room value=$utilization.room>]
		[<if isset($inc.room)>]<td style="padding: 0px 5px;">[<$room->name>]</td>[</if>]
		[<if isset($inc.aid)>]<td style="padding: 0px 5px;">[<$activity->aid>]</td>[</if>]
		[<if isset($inc.name)>]<td style="padding: 0px 5px;">
			<span
				[<if $activity->cancelled >] style="color: #FF0000; font-weight: bold;"
				[<elseif $activity->restricted >] style="color: #FF6600; font-weight: bold;"
				[<elseif $activity->capacity != -1 && $activity->member_count >= $activity->capacity>] style="color: #0000FF; font-weight: bold;"
				[<elseif $activity->capacity != -1 && $activity->percent_full >= 90 >] style="color: #00878D; font-weight: bold;"[</if>]>
				[<$activity->name_r>]
				[<if $activity->comment>] ([<$activity->comment>])[</if>]
			</span></td>[</if>]
		[<if isset($inc.teacher)>]<td style="padding: 0px 5px;">[<$activity->block_sponsors_comma>]</td>[</if>]
		[<if isset($inc.students)>]<td style="padding: 0px 5px;">[<$utilization.students>]</td>[</if>]
		[<if isset($inc.capacity)>]<td style="padding: 0px 5px;">[<if $room->capacity == -1>]UNLIMITED[<else>][<$room->capacity>][</if>]</td>[</if>]
	</tr>
[</foreach>]
</table>
<div style="float: right; margin: 10px;">
	<a href="[<$I2_ROOT>]eighth/vp_room/format/bid/[<$block->bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" /></a>
</div>
