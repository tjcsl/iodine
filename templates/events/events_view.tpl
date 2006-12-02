<strong>[<$event->title>]</strong><br />
[<$event->description>]<br />
Registration for this event costs $[<$event->amount>].<br />

[<if count($event->verifiers) > 1>]
 The following people can verify payment for the event:
 <ul>
 [<foreach from=$event->verifiers item=verifier>]
  <li><a href="[<$I2_ROOT>]studentdirectory/info/[<$verifier->uid>]">[<$verifier->name_comma>]</a></li>
 [</foreach>]
 </ul>
[<else>]
<a href="[<$I2_ROOT>]studentdirectory/info/[<$event->verifiers[0]->uid>]">[<$event->verifiers[0]->name_comma>]</a> can verify payment for this event.
[</if>]
<br />

[<if !$event->user_signed_up()>]
<a href="[<$I2_ROOT>]events/signup/[<$event->eid>]">Sign up for this event</a>
[</if>]
