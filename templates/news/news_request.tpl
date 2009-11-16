[<if $usermail>]
<form method="post" class="boxform" action="[<$I2_ROOT>]news/request">
[</if>]
[<if isSet($mailed)>]
	<br />
	[<if $mailed>]
		Your request has been submitted.
	[<else>]
		There was a problem submitting your request. Please contact the Intranet Developers for assistance.	
	[</if>]
[<else>]
	Do you want to post an informational news article or announcement on Intranet? This page allows you to easily request news posts on Iodine.<br />
	[<if $usermail>]
		We will follow up with you at <strong>[<$usermail>]</strong> if needed.  If this e-mail address is incorrect, please update your preferences, or feel free to send an e-mail directly to <a href="mailto:[<$iodinemail>]">[<$iodinemail>]</a>.  If you send us an e-mail directly, don't forget to tell us who you are!<br />
		Title:<br /><input type="text" name="submit_title" style="width:98%" /><br />Contents:<br />
		<textarea name="submit_box" style="width:98%;height:150px"></textarea><br />
		<input type="submit" value="Submit" name="submit_form" />
		</form>
	[<else>]
		<br />
		Oops...it looks like you didn't enter an e-mail address in your preferences.<br />
		To use the request box, it is required that you specify an e-mail address in your preferences so we can contact you about your request if needed.  However, if you would still like to send us a request without sharing your e-mail with all of TJ, feel free to send an e-mail directly to <a id="mailtolink" href="mailto:[<$iodinemail>]">[<$iodinemail>]</a>.  Don't forget to tell us who you are!
[</if>][</if>]
