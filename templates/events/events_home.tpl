<strong>Events you are currently signed up for:</strong><br />
<ul>
[<foreach from=$signed_up item=event>]
 <li><a href="[<$I2_ROOT>]events/view/[<$event->eid>]">[<$event->title>]</a></li>
[</foreach>]
</ul>
[<*<br />
<strong>Events you may sign up for:</strong><br />
<ul>
[<foreach from=$may_sign_up item=event>]
 <li><a href="[<$I2_ROOT>]events/view/[<$event->eid>]">[<$event->title>]</a></li>
[</foreach>]
</ul>*>]
<br />
[<if count($verifier_events) > 0>]
<strong>Events you may verify payments for:</strong><br />
<ul>
[<foreach from=$verifier_events item=event>]
 <li><a href="[<$I2_ROOT>]events/verify/[<$event->eid>]">[<$event->title>]</a></li>
[</foreach>]
</ul>
[</if>]
