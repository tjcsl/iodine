You were unsuccessful in registering for the following activities:
<ul>
[<foreach from=$status item=activity>]
	<li>[<$activity.activity->name_r>] on [<$activity.activity->block->date>]</li>
[</foreach>]
</ul>
