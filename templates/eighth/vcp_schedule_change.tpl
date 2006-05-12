[<if $eighth_admin>]
The following activities need to be forced to work:
[<else>]
You were unsuccessful in registering for the following activities:
[</if>]
<ul>
[<foreach from=$status item=activity>]
	<li>[<$activity.activity->name_r>] on [<$activity.activity->block->date>]</li>
[</foreach>]
</ul>
[<if $eighth_admin>]
<br/><a href="[<$I2_ROOT>]eighth/vcp_schedule/change/aid/[<$aid>]/uid/[<$uid>]/bids/[<$bids>]/force/1">FORCE</a>
[</if>]
