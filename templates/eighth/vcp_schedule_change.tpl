[<include file="eighth/header.tpl">]
[<if $eighth_admin>]
The following activities need to be forced to work:<br /><br />
[<else>]
You were unsuccessful in registering for the following activities:<br /><br />
[</if>]
[<if isset($act_status.past)>]Activity is in the past<br />[</if>]
[<if isset($act_status.presign)>]This activity is popular, and signups for it are not yet open.  Try signing up within 48 hours of the activity date.<br />[</if>]
[<if isset($act_status.capacity)>]Activity is full<br />[</if>]
[<if isset($act_status.sticky)>][<if $eighth_admin>]Student is[<else>]You are[</if>] stuck to another activity<br />[</if>]
[<if isset($act_status.permissions)>]Student is not allowed into the activity<br />[</if>]
[<if isset($act_status.oneaday)>]Student is already signed up for another block of this one-a-day activity<br />[</if>]
[<if isset($act_status.cancelled)>]Activity is cancelled<br />[</if>]
<ul>
[<foreach from=$status item=activity>]
	<li>[<$activity.activity->name_r>] on [<$activity.activity->block->date>]</li>
[</foreach>]
<br />Reason: <b>[<$forcereason>]</b>
</ul>
[<if $eighth_admin>]
<br/>
<form action="[<$I2_ROOT>]eighth/vcp_schedule/change/aid/[<$aid>]/uid/[<$uid>][<if $start_date != NULL>]/start_date/[<$start_date>][</if>]/bids/[<$bids>]/force/1" method="post" name="forceform">
	<input type="submit" name="force" value="FORCE" />
</form>
<script language="javascript" type="text/javascript">
	document.forceform.force.focus();
</script>
[</if>]
