[<if isSet($mailed)>]
	<br />
	[<if $mailed>]
		Your suggestion has been submitted. Thank you for your input.
	[<else>]
		There was a problem submitting your suggestion. Please contact the Intranet Developers for assistance.	
	[</if>]
[<else>]
	How do you think the Intranet can be improved?<br />
	[<if $usermail>]
		We will follow up with you at <strong>[<$usermail>]</strong> if needed.  If this e-mail address is incorrect, please update your preferences, or feel free to send an e-mail directly to <a href="mailto:intranet@tjhsst.edu">intranet@tjhsst.edu</a>.  If you send us an e-mail directly, don't forget to tell us who you are!<br />
		<form method="post" class="boxform" action="[<$I2_ROOT>]suggestion">
		<textarea name="submit_box" style="width:98%;height:150px"></textarea><br />
		<input type="submit" value="Submit" name="submit_form" />
		</form>
	[<else>]
		<br />
		Oops...it looks like you didn't enter an e-mail address in your preferences.<br />
		To use the suggestion box, it is required that you specify an e-mail address in your preferences so we can contact you about your suggestion if needed.  However, if you would still like to send us a suggestion without sharing your e-mail with all of TJ, feel free to send an e-mail directly to <a href="mailto:intranet@tjhsst.edu">intranet@tjhsst.edu</a>.  Don't forget to tell us who you are!
[</if>][</if>]
