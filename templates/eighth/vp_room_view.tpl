[<include file="eighth/header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$block->date|date_format:"%B %e, %Y">] - [<$block->block>] block</span><br /><br />
<table style="border: 0px; margin: 0px; padding: 0px; width: 100%;">
	<tr>
		[<if isSet($inc.room)>]<th style="padding: 0px 5px; text-align: left;">Location</th>[</if>]
		[<if isSet($inc.aid)>]<th style="padding: 0px 5px; text-align: left;">Activity ID</th>[</if>]
		[<if isSet($inc.name)>]<th style="padding: 0px 5px; text-align: left;">Activity Name</th>[</if>]
		[<if isSet($inc.teacher)>]<th style="padding: 0px 5px; text-align: left;">Teacher</th>[</if>]
		[<if isSet($inc.comments)>]<th style="padding: 0px 5px; text-align: left;">Comments</th>[</if>]
		[<if isSet($inc.students)>]<th style="padding: 0px 5px; text-align: left;">Students</th>[</if>]
		[<if isSet($inc.capacity)>]<th style="padding: 0px 5px; text-align: left;">Capacity</th>[</if>]
	</tr>
[<foreach from=$utilizations item="utilization">]
	<tr>
		[<if isSet($inc.room)>]<td style="padding: 0px 5px;">[<$utilization.room->name>]</td>[</if>]
		[<if isSet($inc.aid)>]<td style="padding: 0px 5px;">[<$utilization.activity->aid>]</td>[</if>]
		[<if isSet($inc.name)>]<td style="padding: 0px 5px;"><span[<if $utilization.activity->cancelled >] style="color: #FF0000; font-weight: bold;"[<elseif $utilization.activity->restricted >] style="color: #FF6600; font-weight: bold;"[<elseif $utilization.activity->capacity != -1 && $utilization.activity->member_count >= $utilization.activity->capacity>] style="color: #0000FF; font-weight: bold;"[</if>]> [<$utilization.activity->name_full_r>] [<if $utilization.activity->comment>] ([<$utilization.activity->comment>])[</if>]</span></td>[</if>]
		[<if isSet($inc.teacher)>]<td style="padding: 0px 5px;">[<$utilization.activity->block_sponsors_comma>]</td>[</if>]
		[<if isSet($inc.comments)>]<td style="padding: 0px 5px;">[<$utilization.activity->comment>]</td>[</if>]
		[<if isSet($inc.students)>]<td style="padding: 0px 5px;">[<$utilization.students>]</td>[</if>]
		[<if isSet($inc.capacity)>]<td style="padding: 0px 5px;">[<if $utilization.room->capacity == -1>]UNLIMITED[<else>][<$utilization.room->capacity>][</if>]</td>[</if>]
	</tr>
[</foreach>]
</table>
<div style="float: right; margin: 10px;">
	<a href="[<$I2_ROOT>]eighth/vp_room/format/bid/[<$block->bid>]"><img src="[<$I2_ROOT>]www/pics/eighth/printer.png" alt="Print" title="Print" /></a>
</div>
