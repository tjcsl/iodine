<strong>[<$event->title>]</strong><br />
You are authorized to verify the required $[<$event->amount>] payment for the following people:
<ul>
[<foreach from=$users item=user>]
 <li><a href="[<$I2_ROOT>]studentdirectory/info/[<$user->uid>]">[<$user->name>]</a> (<a href="[<$I2_ROOT>]events/verify/[<$event->eid>]/[<$user->uid>]">confirm that this person has paid</a>)</li>
[</foreach>]
</ul>
