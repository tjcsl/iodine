<strong>[<$event->title>]</strong><br />
[<$event->description>]<br />
Registration for this event costs $[<$event->amount>].<br />

<form method="post" action="[<$I2_ROOT>]events/signup/[<$event->eid>]" class="boxform">

[<if count($event->verifiers) > 1>]
 [<*FIXME FIXME FIXME*>]
 Pick your math teacher:

 <select name="event_verifier">
 [<foreach from=$event->verifiers item=verifier>]
  <option value="[<$verifier->uid>]">[<$verifier->name_comma>]</option>
 [</foreach>]
 </select>
[<else>]
<input type="hidden" name="event_verifier" value="[<$event->verifiers[0]->uid>]" />
[</if>]

<input type="submit" name="event_sign_up" value="Sign up" />
</form>
