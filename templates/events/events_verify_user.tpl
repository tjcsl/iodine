By clicking the button below, you are confirming that <strong>[<$user->name>]</strong> has paid <strong>$[<$event->amount>]</strong> to register for <strong>[<$event->title>]</strong>.
<form method="post" action="[<$I2_ROOT>]events/verify/[<$event->eid>]/[<$user->uid>]" class="boxform">
<input type="submit" name="events_verify_user" value="Confirm" />
</form>
